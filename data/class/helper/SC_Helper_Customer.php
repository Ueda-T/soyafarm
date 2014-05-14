<?php
/**
 * 会員情報の登録・編集・検索ヘルパークラス.
 *
 *
 * @package Helper
 * @author IQUEVE CO.,LTD.
 * @version $Id: SC_Helper_Customer.php 109 2012-04-24 03:54:19Z hira $
 */
class SC_Helper_Customer {

    /**
     * 会員情報の登録・編集処理を行う.
     *
     * @param array $array 登録するデータの配列（SC_FormParamのgetDbArrayの戻り値）
     * @param array $customer_id nullの場合はinsert, 存在する場合はupdate
     * @access public
     * @return integer 登録編集したユーザーのcustomer_id
     */
    function sfEditCustomerData($array, $customer_id = null) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();

	$s = $array["tel"];
	if ($s && strlen($s)) {
	    $array["tel"] = preg_replace('/-/', '', $s);
	}

	$s = $array["fax"];
	if ($s && strlen($s)) {
	    $array["fax"] = preg_replace('/-/', '', $s);
	}

	$s = trim($array["zip"]);
	if ($s && strlen($s)) {
	    $s = preg_replace('/-/', '', $s);
	    $array["zip"] = substr($s, 0, 3) . "-" . substr($s, 3, 4);
	}

	// 住所カナ
	$array["addr_kana"] = SC_Helper_DB_Ex::sfGetAddrKanaByZipcode
	    (preg_replace('/-/', '', $array["zip"]));

	// 更新日
        $array["update_date"] = "now()";

        // salt値の生成(insert時)または取得(update時)。
        if (is_numeric($customer_id)) {
            $salt = $objQuery->get('salt', "dtb_customer", "customer_id = ? ", array($customer_id));

            // 旧バージョン(2.11未満)からの移行を考慮
            if (empty($salt)) {
		$old_version_flag = true;
	    }
        } else {
            $salt = SC_Utils_Ex::sfGetRandomString(10);
            $array['salt'] = $salt;
        }

        //-- パスワードの更新がある場合は暗号化
        if ($array['password'] == DEFAULT_PASSWORD or $array['password'] == "") {
            //更新しない
            unset($array['password']);
        } else {
            // 旧バージョン(2.11未満)からの移行を考慮
            if ($old_version_flag) {
                $is_password_updated = true;
                $salt = SC_Utils_Ex::sfGetRandomString(10);
                $array['salt'] = $salt;
            }

            $array['password'] = SC_Utils_Ex::sfGetHashString($array['password'], $salt);
        }

        //-- 秘密の質問の更新がある場合は暗号化
        if ($array["reminder_answer"] == DEFAULT_PASSWORD or $array["reminder_answer"] == "") {
            //更新しない
            unset($array["reminder_answer"]);

            // 旧バージョン(2.11未満)からの移行を考慮
            if ($old_version_flag && $is_password_updated) {
                // パスワードが更新される場合は、平文になっている秘密の質問を暗号化する
                $reminder_answer = $objQuery->get('reminder_answer', "dtb_customer", "customer_id = ? ", array($customer_id));
                //$array["reminder_answer"] = SC_Utils_Ex::sfGetHashString($reminder_answer, $salt);
                if ($reminder_answer != "") {
                    $array["reminder_answer"] = SC_Utils_Ex::sfGetHashString($reminder_answer, $salt);
                }
            }
        } else {
            // 旧バージョン(2.11未満)からの移行を考慮
            if ($old_version_flag && !$is_password_updated) {
                // パスワードが更新されない場合は、平文のままにする
                unset($array['salt']);
            } else {
                $array["reminder_answer"] = SC_Utils_Ex::sfGetHashString($array["reminder_answer"], $salt);
            }
        }

	unset($array['house_no']);

        //-- 編集登録実行
        if (is_numeric($customer_id)){
            // 編集

            unset($array["lastlogin_date"]);
            // 送信フラグを未送信へセット
            $array["send_flg"] = INOS_SEND_FLG_OFF;
            // 2014.1.15 takao
            // 作成日をunset
            unset($array['create_date']);

            $objQuery->update("dtb_customer", $array, "customer_id = ? ", array($customer_id));
        } else {
            // 新規登録

            // 顧客ID
            $customer_id = $objQuery->nextVal('dtb_customer_customer_id');
            $array['customer_id'] = $customer_id;
            // 作成日
            if (is_null($array["create_date"])){
                $array["create_date"] = "now()";
            }
	    // 作成者
            if (is_null($array["creator_id"])){
                $array["creator_id"] = $customer_id;
                $array["updator_id"] = $customer_id;
            }

            $objQuery->insert("dtb_customer", $array);
        }

        $objQuery->commit();

        return $customer_id;
    }

    /**
     * 注文番号、利用ポイント、加算ポイントから最終ポイントを取得する.
     *
     * @param integer $order_id 注文番号
     * @param integer $use_point 利用ポイント
     * @param integer $add_point 加算ポイント
     * @return array 最終ポイントの配列
     */
    function sfGetCustomerPoint($order_id, $use_point, $add_point) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<EOF
SELECT
    customer_id
FROM
    dtb_order
WHERE
    order_id = $order_id
EOF;
        $arrRet = $objQuery->getAll($sql);

        $customer_id = $arrRet[0]['customer_id'];
        if ($customer_id != "" && $customer_id >= 1) {
            if (USE_POINT !== false) {
                $arrRet = $objQuery->select('point', "dtb_customer", "customer_id = ?", array($customer_id));
                $point = $arrRet[0]['point'];
                $total_point = $arrRet[0]['point'] - $use_point + $add_point;
            } else {
                $total_point = 0;
                $point = 0;
            }
        } else {
            $total_point = "";
            $point = "";
        }
        return array($point, $total_point);
    }

    /**
     * emailアドレスから、登録済み会員や退会済み会員をチェックする
     *
     * XXX SC_CheckError からしか呼び出されず, 本クラスの中で SC_CheckError を呼び出している
     *
     * @param string $email  メールアドレス
     * @return integer  0:登録可能     1:登録済み   2:再登録制限期間内削除ユーザー  3:自分のアドレス
     */
    function sfCheckRegisterUserFromEmail($email) {
        $objCustomer = new SC_Customer_Ex();
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // ログインしている場合、すでに登録している自分のemailの場合
        if ($objCustomer->isLoginSuccess(true)
            && SC_Helper_Customer_Ex::sfCustomerEmailDuplicationCheck($objCustomer->getValue('customer_id'), $email)) {
            // 自分のアドレス
            return 3;
        }

        $sql =<<<EOF
SELECT
    email,
    update_date,
    del_flg
FROM
    dtb_customer
WHERE
    (email = '$email' OR email_mobile = '$email')
ORDER BY del_flg
EOF;
        $arrRet = $objQuery->getAll($sql);

        // 会員である場合
        if (count($arrRet) > 0 && $arrRet[0]['del_flg'] != '1') {
            // 登録済み
            return 1;
        }

        // 登録可能
        return 0;
    }

    /**
     * ログイン時メールアドレス重複チェック.
     *
     * 会員の保持する email, mobile_email が, 引数 $email と一致するかチェックする
     *
     * @param integer $customer_id チェック対象顧客の顧客ID
     * @param string $email チェック対象のメールアドレス
     * @return boolean メールアドレスが重複する場合 true
     */
    function sfCustomerEmailDuplicationCheck($customer_id, $email) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $arrResults = $objQuery->getRow('email, email_mobile',
                                        'dtb_customer', 'customer_id = ?',
                                        array($customer_id));
        $return =
               strlen($arrResults['email']) >= 1 && $email === $arrResults['email']
            || strlen($arrResults['email_mobile']) >= 1 &&  $email === $arrResults['email_mobile']
        ;
        return $return;
    }

    /**
     * customer_idから会員情報を取得する
     *
     * @param mixed $customer_id
     * @param mixed $mask_flg
     * @access public
     * @return array 会員情報の配列を返す
     */
    function sfGetCustomerData($customer_id, $mask_flg = true) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // 会員情報DB取得
        $sql =<<<EOF
SELECT
    *
FROM
    dtb_customer
WHERE
    customer_id = $customer_id
EOF;
        $ret = $objQuery->getAll($sql);
        $arrForm = $ret[0];

        // 確認項目に複製
        $arrForm['email02'] = $arrForm['email'];
        $arrForm['email_mobile02'] = $arrForm['email_mobile'];

        // 誕生日を年月日に分ける
        if (isset($arrForm['birth'])){
            $birth = explode(" ", $arrForm['birth']);
            list($arrForm['year'], $arrForm['month'], $arrForm['day']) = explode("-",$birth[0]);
        }

        // ポイント有効期限を年月日に分ける
        if (isset($arrForm['point_valid_date'])){
            $birth = explode(" ", $arrForm['point_valid_date']);
            list($arrForm['pv_year'], $arrForm['pv_month'], $arrForm['pv_day']) = explode("-",$birth[0]);
        }

        // 誕生日ポイント有効期限を年月日に分ける
        if (isset($arrForm['birth_point_valid_date'])){
            $birth = explode(" ", $arrForm['birth_point_valid_date']);
            list($arrForm['bpv_year'], $arrForm['bpv_month'], $arrForm['bpv_day']) = explode("-",$birth[0]);
        }

        if ($mask_flg) {
            $arrForm['password'] = DEFAULT_PASSWORD;
            $arrForm['password02'] = DEFAULT_PASSWORD;
            if (isset($arrForm['reminder'])) {
                $arrForm['reminder_answer'] = DEFAULT_PASSWORD;
            }
        }

        // 最終ログイン日時
        // 登録日時
        // 最終更新日時
        // の「-」を「/」に変換して、秒を削る
        if (isset($arrForm['lastlogin_date'])) {
            $arrForm['lastlogin_date'] =
                SC_Utils::sfDispDBDate($arrForm['lastlogin_date']);
        }
        if (isset($arrForm['create_date'])) {
            $arrForm['create_date'] =
                SC_Utils::sfDispDBDate($arrForm['create_date']);
        }
        if (isset($arrForm['update_date'])) {
            $arrForm['update_date'] =
                SC_Utils::sfDispDBDate($arrForm['update_date']);
        }

        return $arrForm;
    }

    /**
     * 顧客ID指定またはwhere条件指定での会員情報取得(単一行データ)
     *
     * TODO: sfGetCustomerDataと統合したい
     *
     * @param integer $customer_id 顧客ID (指定無しでも構わないが、Where条件を入れる事)
     * @param string $add_where 追加WHERE条件
     * @param array $arrAddVal 追加WHEREパラメーター
     * @access public
     * @return array 対象会員データ
     */
    function sfGetCustomerDataFromId($customer_id, $add_where = '', $arrAddVal = array()) {
        $objQuery   =& SC_Query_Ex::getSingletonInstance();
        if($where == '') {
            $where = 'customer_id = ?';
            $arrData = $objQuery->getRow("*", "dtb_customer", $where, array($customer_id));
        }else{
            if(SC_Utils_Ex::sfIsInt($customer_id)) {
                $where .= ' AND customer_id = ?';
                $arrAddVal[] = $customer_id;
            }
            $arrData = $objQuery->getRow("*", "dtb_customer", $where, $arrAddVal);
        }
        return $arrData;
    }

    /**
     * 重複しない会員登録キーを発行する。
     *
     * @access public
     * @return string 会員登録キーの文字列
     */
    function sfGetUniqSecretKey() {
        $objQuery   =& SC_Query_Ex::getSingletonInstance();
        $count      = 1;
        while ($count != 0) {
            $uniqid = SC_Utils_Ex::sfGetUniqRandomId('r');
            $count  = $objQuery->count("dtb_customer", "secret_key = ?", array($uniqid));
        }
        return $uniqid;
    }

    /**
     * 会員登録キーから顧客IDを取得する.
     *
     * @param string $uniqid 会員登録キー
     * @param boolean $check_status 本会員のみを対象とするか
     * @access public
     * @return integer 顧客ID
     */
    function sfGetCustomerId($uniqid, $check_status = false) {
        $objQuery   =& SC_Query_Ex::getSingletonInstance();
        $where      = "secret_key = ?";

        if ($check_status) {
            $where .= ' AND status = 1 AND del_flg = 0';
        }

        return $objQuery->get("customer_id", "dtb_customer", $where, array($uniqid));
    }

    /**
     * 会員登録時フォーム初期化
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param boolean $isA dmin true:管理者画面 false:顧客向け
     * @access public
     * @return void
     */
    function sfCustomerEntryParam (&$objFormParam, $isAdmin = false) {
        SC_Helper_Customer_Ex::sfCustomerCommonParam($objFormParam);
        SC_Helper_Customer_Ex::sfCustomerRegisterParam($objFormParam, $isAdmin);
	$objFormParam->addParam('番地なし', "house_no");
        $objFormParam->addParam("規約", "agree", INT_LEN, 'n', array("EXIST_CHECK"), "", false);

        if ($isAdmin) {
            $objFormParam->addParam("顧客ID", "customer_id", INT_LEN, 'n', array("NUM_CHECK"));
            $objFormParam->addParam("ポイント", 'point', INT_LEN, 'n', array("NUM_CHECK"));
        }

        if (SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE) {
            // 登録確認画面の「戻る」ボタンのためのパラメーター
            $objFormParam->addParam("戻る", "return", '', '', array(), '', false);
        }
    }

    /**
     * 会員情報変更フォーム初期化
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @access public
     * @return void
     */
    function sfCustomerMypageParam (&$objFormParam) {
        SC_Helper_Customer_Ex::sfCustomerCommonParam($objFormParam);
        SC_Helper_Customer_Ex::sfCustomerRegisterParam($objFormParam, false, true);

	$objFormParam->addParam('番地なし', "house_no");

        if (SC_Display_Ex::detectDevice() !== DEVICE_TYPE_MOBILE){
            $objFormParam->addParam('携帯メールアドレス', "email_mobile", INOS_CUSTOMER_EMAIL_LEN, 'a', array("NO_SPTAB", "EMAIL_CHECK", "SPTAB_CHECK" ,"EMAIL_CHAR_CHECK", "MOBILE_EMAIL_CHECK", "MAX_LENGTH_CHECK"));
            $objFormParam->addParam('携帯メールアドレス(確認)', "email_mobile02", null, 'a', array("NO_SPTAB", "EMAIL_CHECK","SPTAB_CHECK" , "EMAIL_CHAR_CHECK", "MOBILE_EMAIL_CHECK"), "", false);
        } else {
            //$objFormParam->addParam('携帯メールアドレス', "email_mobile", null, 'a', array("EXIST_CHECK", "NO_SPTAB", "EMAIL_CHECK", "SPTAB_CHECK" ,"EMAIL_CHAR_CHECK", "MOBILE_EMAIL_CHECK"));
            $objFormParam->addParam('メールアドレス', 'email', INOS_CUSTOMER_EMAIL_LEN, 'a', array("EXIST_CHECK", "NO_SPTAB", "EMAIL_CHECK", "SPTAB_CHECK" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        }
    }

    /**
     * お届け先フォーム初期化
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @access public
     * @return void
     */
    function sfCustomerOtherDelivParam (&$objFormParam) {
        SC_Helper_Customer_Ex::sfCustomerCommonParam($objFormParam);
        $objFormParam->addParam("", 'other_deliv_id');
    }

    /**
     * 会員共通
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @access public
     * @return void
     */
    function sfCustomerCommonParam(&$objFormParam) {
        $objFormParam->addParam("お名前", 'name', STEXT_LEN*2, 'aKV', array("EXIST_CHECK", "NO_TAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "CHECK_WQ_SQ_C"));
        $objFormParam->addParam("お名前(フリガナ)", 'kana', STEXT_LEN*2, 'ahks', array("EXIST_CHECK", "NO_TAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "HANKANASP_CHECK", "CHECK_WQ_SQ_C"));
        $objFormParam->addParam("郵便番号", "zip", ZIP_LEN, 'n', array("EXIST_CHECK", "ZIP_CHECK"));
        $objFormParam->addParam("都道府県", 'pref', INT_LEN, 'n', array("EXIST_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("市区町村", "addr01", ADDRESS_LEN, 'aKV', array("EXIST_CHECK", "SPTAB_CHECK", "MAX_BYTES_SJIS", "CHECK_WQ_SQ_C"));
        $objFormParam->addParam("番地・ビル名", "addr02", ADDRESS_LEN, 'aKV', array("EXIST_CHECK", "SPTAB_CHECK", "MAX_BYTES_SJIS", "CHECK_WQ_SQ_C"));
        $objFormParam->addParam("電話番号", 'tel', TEL_ITEM_LEN*3, 'n', array("EXIST_CHECK", "SPTAB_CHECK", "IS_TELEPHONE", "MAX_LENGTH_CHECK"));

        $objFormParam->addParam("最終ログイン日時", 'lastlogin_date');
        $objFormParam->addParam("登録日時", 'create_date');
        $objFormParam->addParam("最終更新日時", 'update_date');
	$objFormParam->addParam("顧客番号", 'customer_id', 0, null, null, null, false);
    }

    /**
     * 会員登録共通
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param boolean $isAdmin true:管理者画面 false:会員向け
     * @param boolean $is_mypage マイページの場合 true
     * @return void
     */
    function sfCustomerRegisterParam (&$objFormParam, $isAdmin = false, $is_mypage = false) {
        $objFormParam->addParam("パスワード", 'password', STEXT_LEN, 'a', array("EXIST_CHECK", "SPTAB_CHECK", "GRAPH_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("性別", 'sex', INT_LEN, 'n', array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("年", 'year', 4, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"), "", false);
        $objFormParam->addParam("月", 'month', 2, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"), "", false);
        $objFormParam->addParam("日", 'day', 2, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"), "", false);
        $objFormParam->addParam("メールマガジン", "mailmaga_flg", INT_LEN, 'n', array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        if (!$is_mypage) {
            $objFormParam->addParam("DM", "dm_flg", INT_LEN, 'n', array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        }
        if ($isAdmin) {
            $objFormParam->addParam
                ("基幹顧客番号", 'customer_cd', 11, 'a',
                 array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
            $objFormParam->addParam
                ("顧客区分", 'customer_kbn', INT_LEN, 'n',
                 array("NUM_CHECK", "MAX_LENGTH_CHECK"));
            $objFormParam->addParam
                ("償却顧客区分", 'kashidaore_kbn', INT_LEN, 'n',
                 array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        }

        if (SC_Display_Ex::detectDevice() !== DEVICE_TYPE_MOBILE){
            $objFormParam->addParam("FAX番号", 'fax', TEL_ITEM_LEN*3, 'n', array("SPTAB_CHECK"));
            if(!$isAdmin) {
                $objFormParam->addParam("パスワード(確認)", 'password02', STEXT_LEN, 'a', array("EXIST_CHECK", "SPTAB_CHECK" ,"GRAPH_CHECK"), "", false);
                $objFormParam->addParam('メールアドレス(確認)', "email02", null, 'a', array("NO_SPTAB", "EXIST_CHECK", "EMAIL_CHECK","SPTAB_CHECK" , "EMAIL_CHAR_CHECK"), "", false);
                $objFormParam->addParam('メールアドレス', 'email', INOS_CUSTOMER_EMAIL_LEN, 'a', array("NO_SPTAB", "EXIST_CHECK", "EMAIL_CHECK", "SPTAB_CHECK" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));

		//                $objFormParam->addParam("アンケート", "questionnaire", INT_LEN, 'n', array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
		//                $objFormParam->addParam("アンケートその他", 'questionnaire_other', LTEXT_LEN, 'KVa', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));

            } else {
                $objFormParam->addParam('メールアドレス', 'email', INOS_CUSTOMER_EMAIL_LEN, 'a', array("NO_SPTAB", "EMAIL_CHECK", "SPTAB_CHECK" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
	        }
        } else {
            if (!$is_mypage) {
                $objFormParam->addParam('メールアドレス', 'email', INOS_CUSTOMER_EMAIL_LEN, 'a', array("EXIST_CHECK", "EMAIL_CHECK", "NO_SPTAB" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
            }
        }
    }

    function sfCustomerOtherDelivErrorCheck(&$objFormParam) {
        $objErr = SC_Helper_Customer_Ex::sfCustomerCommonErrorCheck($objFormParam);
        return $objErr->arrErr;
    }

    function sfCheckBytesAddress1(&$objFormParam, $prefBytes) {
	$objFormParam->getValue('addr02');
    }

    /**
     * 会員登録エラーチェック
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @access public
     * @return array エラーの配列
     */
    function sfCustomerEntryErrorCheck(&$objFormParam) {
        $objErr = SC_Helper_Customer_Ex::sfCustomerCommonErrorCheck($objFormParam);

        $objErr = SC_Helper_Customer_Ex::sfCustomerRegisterErrorCheck($objErr);

        return $objErr->arrErr;
    }

    /**
     * 会員情報変更エラーチェック
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param boolean $isAdmin 管理画面チェック時:true
     * @access public
     * @return array エラーの配列
     */
    function sfCustomerMypageErrorCheck(&$objFormParam, $isAdmin = false) {

	//        $objFormParam->toLower('email_mobile');
	//        $objFormParam->toLower('email_mobile02');

        $objErr = SC_Helper_Customer_Ex::sfCustomerCommonErrorCheck($objFormParam);
        $objErr = SC_Helper_Customer_Ex::sfCustomerRegisterErrorCheck($objErr, $isAdmin);

        if (isset($objErr->arrErr['password'])
             && $objFormParam->getValue('password') == DEFAULT_PASSWORD) {
            unset($objErr->arrErr['password']);
            unset($objErr->arrErr['password02']);
        }
        if (isset($objErr->arrErr['reminder_answer'])
                && $objFormParam->getValue('reminder_answer') == DEFAULT_PASSWORD) {
            unset($objErr->arrErr['reminder_answer']);
        }
        return $objErr->arrErr;
    }

    /**
     * 会員エラーチェック共通
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @access private
     * @return array エラー情報の配列
     */
    function sfCustomerCommonErrorCheck(&$objFormParam) {
        $objFormParam->convParam();
        $arrParams = $objFormParam->getHashArray();

        // 入力データを渡す。
        $objErr = new SC_CheckError_Ex($arrParams);
        $objErr->arrErr = $objFormParam->checkError();

        $objErr->doFunc(array("電話番号", "tel"),array("IS_TELEPHONE"));

        return $objErr;
    }

    /**
     * 会員登録編集共通の相関チェック
     *
     * @param SC_CheckError $objErr SC_CheckError インスタンス
     * @param boolean $isAdmin 管理画面チェック時:true
     * @return SC_CheckError $objErr エラー情報
     */
    function sfCustomerRegisterErrorCheck(&$objErr, $isAdmin = false) {
        $objErr->doFunc(array("生年月日", 'year', 'month', 'day'), array("CHECK_BIRTHDAY"));

        if (SC_Display_Ex::detectDevice() !== DEVICE_TYPE_MOBILE) {
            if (!$isAdmin) {
                $objErr->doFunc(array('パスワード', 'パスワード(確認)', 'password', "password02") ,array("EQUAL_CHECK"));
                $objErr->doFunc(array('メールアドレス', 'メールアドレス(確認)', 'email', "email02") ,array("EQUAL_CHECK"));
            }
            $objErr->doFunc(array("FAX番号", "fax") ,array("TEL_CHECK2"));
        }

        if (!$isAdmin) {
            // 現会員の判定 → 現会員もしくは仮登録中は、メアド一意が前提になってるので同じメアドで登録不可
            $objErr->doFunc(array("メールアドレス", 'email'), array("CHECK_REGIST_CUSTOMER_EMAIL"));
            $objErr->doFunc(array("携帯メールアドレス", 'email_mobile'), array("CHECK_REGIST_CUSTOMER_EMAIL", "MOBILE_EMAIL_CHECK"));
        }
        return $objErr;
    }

    /**
     * 顧客検索パラメーター（管理画面用）
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @access public
     * @return void
     */
    function sfSetSearchParam(&$objFormParam) {
        $objFormParam->addParam('顧客ID', 'search_customer_id', ID_MAX_LEN, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam("顧客コード(基幹)", "search_customer_cd", INOS_CUSTOMER_CD_LEN, 'n',array("MAX_LENGTH_CHECK", "ALNUM_CHECK"));
        $objFormParam->addParam('お名前', 'search_name', STEXT_LEN, 'KVa', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam('お名前(フリガナ)', 'search_kana', STEXT_LEN, 'hks', array("SPTAB_CHECK", "MAX_LENGTH_CHECK", "KANABLANK_CHECK"));
        $objFormParam->addParam('都道府県', 'search_pref', INT_LEN, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('誕生日(開始年)', 'search_b_start_year', 4, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('誕生日(開始月)', 'search_b_start_month', 2, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('誕生日(開始日)', 'search_b_start_day', 2, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));

        $objFormParam->addParam('誕生日(終了年)', 'search_b_end_year', 4, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('誕生日(終了月)', 'search_b_end_month', 2, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('誕生日(終了日)', 'search_b_end_day', 2, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('誕生月', 'search_birth_month', 2, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('メールアドレス', 'search_email', INOS_CUSTOMER_EMAIL_LEN, 'a', array("SPTAB_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam('携帯メールアドレス', 'search_email_mobile', INOS_CUSTOMER_EMAIL_LEN, 'a', array("SPTAB_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam('電話番号', 'search_tel', TEL_LEN, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('購入金額(開始)', 'search_buy_total_from', PRICE_LEN, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('購入金額(終了)', 'search_buy_total_to', PRICE_LEN, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('購入回数(開始)', 'search_buy_times_from', INT_LEN, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('購入回数(終了)', 'search_buy_times_to', INT_LEN, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('登録・更新日(開始年)', 'search_start_year', 4, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('登録・更新日(開始月)', 'search_start_month', 2, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('登録・更新日(開始日)', 'search_start_day', 2, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('登録・更新日(終了年)', 'search_end_year', 4, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('登録・更新日(終了月)', 'search_end_month', 2, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('登録・更新日(終了日)', 'search_end_day', 2, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('表示件数', 'search_page_max', INT_LEN, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"), 1, false);
        $objFormParam->addParam('ページ番号', 'search_pageno', INT_LEN, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"), 1, false);
        $objFormParam->addParam('最終購入日(開始年)', 'search_buy_start_year', 4, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('最終購入日(開始月)', 'search_buy_start_month', 2, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('最終購入日(開始日)', 'search_buy_start_day', 2, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('最終購入日(終了年)', 'search_buy_end_year', 4, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('最終購入日(終了月)', 'search_buy_end_month', 2, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('最終購入日(終了日)', 'search_buy_end_day', 2, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('購入商品コード', 'search_buy_product_code', STEXT_LEN, 'KVa', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam('購入商品名', 'search_buy_product_name', STEXT_LEN, '', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam('退会者をふくめる', 'search_withdrawal');
        $objFormParam->addParam('カテゴリ', 'search_category_id', INT_LEN, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('性別', 'search_sex', INT_LEN, 'n', array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam('会員状態', 'search_status', INT_LEN, 'n', array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam('顧客区分', 'search_customer_kbn', INT_LEN, 'n', array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam('貸倒区分', 'search_kashidaore_kbn', INT_LEN, 'n', array("MAX_LENGTH_CHECK"));
    }

    /**
     * 顧客検索パラメーター　エラーチェック（管理画面用）
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @access public
     * @return array エラー配列
     */
    function sfCheckErrorSearchParam(&$objFormParam) {
        // パラメーターの基本チェック
        $arrErr = $objFormParam->checkError();
        // 拡張エラーチェック初期化
        $objErr = new SC_CheckError_Ex($objFormParam->getHashArray());
        // 拡張エラーチェック
        $objErr->doFunc(array("誕生日(開始日)", "search_b_start_year", "search_b_start_month", "search_b_start_day"), array("CHECK_DATE"));
        $objErr->doFunc(array("誕生日(終了日)", "search_b_end_year", "search_b_end_month", "search_b_end_day"), array("CHECK_DATE"));

        $objErr->doFunc(array("誕生日(開始日)","誕生日(終了日)", "search_b_start_year", "search_b_start_month", "search_b_start_day", "search_b_end_year", "search_b_end_month", "search_b_end_day"), array("CHECK_SET_TERM"));
        $objErr->doFunc(array("登録・更新日(開始日)", "search_start_year", "search_start_month", "search_start_day",), array("CHECK_DATE"));
        $objErr->doFunc(array("登録・更新日(終了日)", "search_end_year", "search_end_month", "search_end_day"), array("CHECK_DATE"));
        $objErr->doFunc(array("登録・更新日(開始日)","登録・更新日(終了日)", "search_start_year", "search_start_month", "search_start_day", "search_end_year", "search_end_month", "search_end_day"), array("CHECK_SET_TERM"));
        $objErr->doFunc(array("最終購入日(開始日)", "search_buy_start_year", "search_buy_start_month", "search_buy_start_day",), array("CHECK_DATE"));
        $objErr->doFunc(array("最終購入(終了日)", "search_buy_end_year", "search_buy_end_month", "search_buy_end_day"), array("CHECK_DATE"));
        //購入金額(from) ＞ 購入金額(to) の場合はエラーとする
        $objErr->doFunc(array("最終購入日(開始日)","登録・更新日(終了日)", "search_buy_start_year", "search_buy_start_month", "search_buy_start_day", "search_buy_end_year", "search_buy_end_month", "search_buy_end_day"), array("CHECK_SET_TERM"));

        if ((SC_Utils_Ex::sfIsInt($array["search_buy_total_from"])
             && SC_Utils_Ex::sfIsInt($array["search_buy_total_to"]))
             && ($array["search_buy_total_from"] > $array["buy_total_to"])) {
            $objErr->arrErr["search_buy_total_from"] .= "※ 購入金額の指定範囲が不正です。";
        }

        if ((SC_Utils_Ex::sfIsInt($array["search_buy_times_from"])
             && SC_Utils_Ex::sfIsInt($array["search_buy_times_to"]))
             && ($array["search_buy_times_from"] > $array["search_buy_times_to"])) {
            $objErr->arrErr["search_buy_times_from"] .= "※ 購入回数の指定範囲が不正です。";
        }
        if(!SC_Utils_Ex::isBlank($objErr->arrErr)) {
            $arrErr = array_merge($arrErr, $objErr->arrErr);
        }
        return $arrErr;
    }

    /**
     * 顧客一覧検索をする処理（ページング処理付き、管理画面用共通処理）
     *
     * @param array $arrParam 検索パラメーター連想配列
     * @param string $limitMode ページングを利用するか判定用フラグ
     * @return array( integer 全体件数, mixed 顧客データ一覧配列, mixed SC_PageNaviオブジェクト)
     */
    function sfGetSearchData($arrParam, $limitMode = '') {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objSelect = new SC_CustomerList_Ex($arrParam, 'customer');
        $page_max = SC_Utils_Ex::sfGetSearchPageMax($arrParam['search_page_max']);
        $disp_pageno = $arrParam['search_pageno'];
        if($disp_pageno == 0) {
            $disp_pageno = 1;
        }
        $offset = intval($page_max) * (intval($disp_pageno) - 1);
        if ($limitMode == '') {
        	$objSelect->setLimitOffset($page_max, $offset);
        }
        $arrData = $objQuery->getAll($objSelect->getList(), $objSelect->arrVal);

        // 該当全体件数の取得
        $linemax = $objQuery->getOne($objSelect->getListCount(), $objSelect->arrVal);

        // ページ送りの取得
        $objNavi = new SC_PageNavi_Ex($arrParam['search_pageno'],
                                    $linemax,
                                    $page_max,
                                    'fnNaviSearchOnlyPage',
                                    NAVI_PMAX);
        return array($linemax, $arrData, $objNavi);
    }

    /**
     * 郵便番号から都道府県名を取得.
     *
     * @param  string $zip 郵便番号 XXX-XXXX or XXXXXXX
     * @return string $prefName 都道府県名
     */
    function sfGetPrefNameByZip($zip) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // ハイフン付きの場合
        if (strlen($zip) == 8) {
	        $zip = preg_replace('/-/', '', $zip);
        }
        // SQL生成
        $sql = <<<EOS

SELECT ZIP.state
  FROM mtb_zip ZIP
 WHERE ZIP.zipcode = '$zip'

EOS;
        // 郵便番号から都道府県名を取得
        $prefName = $objQuery->getOne($sql);
        return $prefName;
    }

    /**
     * 都道府県名から都道府県コードを取得.
     *
     * @param  string  $prefName 都道府県名
     * @return integer $prefCd   都道府県コード
     */
    function sfGetPrefCdByPrefName($prefName) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // SQL生成
        $sql = <<<EOS

SELECT PRE.id
  FROM mtb_pref PRE
 WHERE PRE.name = '$prefName'

EOS;
        // 都道府県名から都道府県コードを取得
        $prefCd = $objQuery->getOne($sql);
        return $prefCd;
    }

    /**
     * 都道府名を対象文字列から消去.
     *
     * @param  string $targetStr 対象の文字列
     * @param  string $prefName  都道府県名
     * @return string $stripStr  都道府県が取り除かれた文字列
     */
    function sfStripPrefName($targetStr, $prefName) {

        $stripStr = '';

        // 文字列から都道府県名を消去
        $stripStr = preg_replace('/'. $prefName. '/',
                                 '', $targetStr);
        return $stripStr;
    }

    /**
     * 基幹顧客番号の指定で会員情報取得(単一行データ)
     *
     * @param integer $customer_cd 基幹顧客番号
     * @access public
     * @return array 対象会員データ
     */
    function sfGetCustomerDataFromCd($customer_cd = "") {

        if (empty($customer_cd)) {
            return;
        }

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $where = 'customer_cd = ?';

        $arrData = $objQuery->getRow
            ("*", "dtb_customer", $where, array($customer_cd));

        return $arrData;
    }

    /**
     * 顧客CDとWEB顧客CDを指定して
     * 顧客マスタの存在チェックを行う
     *
     * @param integer $customer_cd
     * @param integer $customer_id
     * @return boolean true:レコードあり false:レコードなし
     */
    function checkExistsCustomer($customer_cd, $customer_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<__EOS
SELECT
    COUNT(*)
FROM
    dtb_customer
WHERE
    customer_cd = '{$customer_cd}'
AND customer_id = {$customer_id}
AND del_flg = 0
__EOS;

        $count = $objQuery->getOne($sql);
        if ($count < 1) {
            return false;
        } else {
            return true;
        }
    }

}
