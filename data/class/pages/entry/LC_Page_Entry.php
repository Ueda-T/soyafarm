<?php
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * 会員登録のページクラス.
 *
 * @package Page
 * @author IQUEVE CO.,LTD.
 * @version $Id:LC_Page_Entry.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_Entry extends LC_Page_Ex {

    /**
     * Page を初期化する.
     * @return void
     */
    function init() {
        parent::init();

        // 生年月日選択肢の取得
        $objDate = new SC_Date_Ex(BIRTH_YEAR, date('Y',strtotime('now')));
        $this->arrYear = $objDate->getYear('', START_BIRTH_YEAR, '');
        $this->arrMonth = $objDate->getMonth(true);
        $this->arrDay = $objDate->getDay(true);

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');

        // お届け間隔
        $this->arrCourseCd = $masterData->getMasterData("mtb_course_cd");
        $this->arrTodokeKbn = $masterData->getMasterData("mtb_todoke_kbn");

        // お届け曜日
        $this->arrTodokeWeekNo = $masterData->getMasterData("mtb_todoke_week");

        // XXX 既存のマスタとIDが一致しないため、独自で設定
        $this->arrTodokeWeek = array(1 => '日',
                                     2 => '月',
                                     3 => '火',
                                     4 => '水',
                                     5 => '木',
                                     6 => '金',
                                     7 => '土');

        $this->httpCacheControl('nocache');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    function setAddrLimit($objFormParam) {
	$prefBytes = mb_strlen($this->arrPref[$this->arrForm['pref']]) * 2;
	$objFormParam->setLength("addr01", ADDRESS_LEN - $prefBytes);
    }

    /**
     * Page のプロセス
     * @return void
     */
    function action() {
	global $CLICK_ANALYZER_STATIC;

	// CLICK ANALYZER用埋め込み
	$this->tpl_clickAnalyzer = "";
	if (isset($CLICK_ANALYZER_STATIC["entry"])) {
	    $this->tpl_clickAnalyzer = $CLICK_ANALYZER_STATIC["entry"];
	}

        $objFormParam = new SC_FormParam_Ex();
        $objCartSess = new SC_CartSession_Ex();
        $objSiteSess = new SC_SiteSession_Ex();
        $objPurchase = new SC_Helper_Purchase_Ex();

        $this->cartKey = $objCartSess->getKey();

        // カート内商品のチェック
        $this->tpl_message = $objCartSess->checkProducts($this->cartKey);
        if (!SC_Utils_Ex::isBlank($this->tpl_message)) {
            SC_Response_Ex::sendRedirect(CART_URLPATH);
            exit;
        }

	// カート内情報取得
        $this->arrCartItems = $objCartSess->getCartList($this->cartKey);
        if (SC_Utils_Ex::isBlank($this->arrCartItems)) {
            SC_Response_Ex::sendRedirect(CART_URLPATH);
            exit;
        }

        SC_Helper_Customer_Ex::sfCustomerEntryParam($objFormParam);
        $this->lfShippingParam($objFormParam);

	// 
	$_POST['kana01'] = mb_convert_kana($_POST['kana01'], 'ahks');
	$_POST['kana02'] = mb_convert_kana($_POST['kana02'], 'ahks');

	// 番地なし補正
	if ($_POST['house_no'] != "") {
	    $_POST['addr02'] = "番地なし";
	}
	if ($_POST['shipping_house_no'] != "") {
	    $_POST['shipping_addr02'] = "番地なし";
	}

	// メールマガジンとDMは必ず「受け取る」を指定
	$_POST["mailmaga_flg"] = "1";
	$_POST["dm_flg"] = "1";
        $objFormParam->setParam($_POST);
        $this->arrForm = $objFormParam->getHashArray();

        // mobile用（戻るボタンでの遷移かどうかを判定）
        if (!empty($this->arrForm['return'])) {
            $_POST['mode'] = 'return';
        }

        // 新規登録以外でログインした場合ログイン済み処理に遷移させる
        $objCustomer = new SC_Customer_Ex();
        if ($objCustomer->isLoginSuccess(true) && !$_SESSION["new_customer_id"]) {
            // 購入ページへ
            SC_Response_Ex::sendRedirect(SHOPPING_URL);
            exit;
        }

        switch ($this->getMode()) {
        case 'confirm':
	    // 選択されている都道府県に応じて住所1の制限文字数を調整する
	    $this->setAddrLimit($objFormParam);
	    // 入力内容を結合する
	    $this->setCombiData($objFormParam);

	    $this->arrErr = SC_Helper_Customer_Ex::sfCustomerEntryErrorCheck($objFormParam);

	    // お届け先エラーチェック
	    $this->arrErr = $this->lfShippingErrorCheck($objFormParam, $this->arrErr);

            // 入力エラーなし
            if(empty($this->arrErr)) {
                //パスワード表示
                $this->passlen = SC_Utils_Ex::sfPassLen(strlen($this->arrForm['password']));
                $this->tpl_mainpage = 'entry/confirm.tpl';
                $this->tpl_title = '会員登録(確認ページ)';
            }
            break;
        case 'complete':
	    // 選択されている都道府県に応じて住所1の制限文字数を調整する
	    $this->setAddrLimit($objFormParam);
            //-- 会員登録と完了画面
            $this->arrErr = SC_Helper_Customer_Ex::sfCustomerEntryErrorCheck($objFormParam);
            if(empty($this->arrErr)) {

		// 戻る画面対応
		$_SESSION["new_customer"] = $this->arrForm;

                $uniqid = $this->lfRegistCustomerData($this->lfMakeSqlVal($objFormParam));

		// お支払情報へ遷移
                SC_Response_Ex::sendRedirect(SHOPPING_PAYMENT_URLPATH);
		exit;

		/*
                $this->tpl_mainpage = 'entry/complete.tpl';
                $this->tpl_title    = '会員登録(完了ページ)';
                $this->lfSendMail($uniqid, $this->arrForm);

                // 仮会員が無効の場合
                if(CUSTOMER_CONFIRM_MAIL == false) {
                    // ログイン状態にする
                    $objCustomer = new SC_Customer_Ex();
                    $objCustomer->setLogin($this->arrForm['email']);
                }

                // 完了ページに移動させる。
                SC_Response_Ex::sendRedirect('complete.php', array('ci' => SC_Helper_Customer_Ex::sfGetCustomerId($uniqid)));
		 */
            }
            break;
        default:
	    // 会員情報入力後に戻ってきた場合
	    if ($_SESSION["new_customer_id"]) {
		$this->arrForm = $_SESSION["new_customer"];
	    }
	    /*
	    if (empty($this->arrForm['sex'])) {
		$this->arrForm['sex'] = 2;
	    }
	     */
            break;
        }

	// 規約関係
	$arrKiyaku = $this->lfGetKiyakuData();
	$this->max = count($arrKiyaku);
	$offset = '';
	// mobile時はGETでページ指定
	if (SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE) {
	    $this->offset = $this->lfSetOffset($_GET['offset']);
	}
	$this->tpl_kiyaku_text
	    = $this->lfMakeKiyakuText($arrKiyaku, $this->max, $this->offset);

        // 同梱品情報取得
        $this->tpl_include_product_flg = false;
        $this->arrIncludeProduct = $objPurchase->getIncludeProducts();
        if (is_array($this->arrIncludeProduct)) {
            $this->tpl_include_product_flg = true;
        }

    }

    /**
     * 会員情報の登録
     *
     * @access private
     * @return uniqid
     */
    function lfRegistCustomerData($sqlval) {

	$objSiteSess = new SC_SiteSession_Ex();
	$objCartSess = new SC_CartSession_Ex();
	$objCustomer = new SC_Customer_Ex();
	$objPurchase = new SC_Helper_Purchase_Ex();

	$objQuery =& SC_Query_Ex::getSingletonInstance();

	// カート内容保持
	$uniqid = $objSiteSess->getUniqId();
	$objPurchase->verifyChangeCart($this->tpl_uniqid, $objCartSess);
	$this->cartKey = $objCartSess->getKey();

	$otherFlg = $sqlval["other_addr_flg"];
	// お届け先用配列を分ける
	if ($otherFlg) {

	    $s = $sqlval["shipping_tel"];
	    if ($s && strlen($s)) {
		$sqlval["shipping_tel"] = preg_replace('/-/', '', $s);
	    }

	    $s = trim($sqlval["shipping_zip"]);
	    if ($s && strlen($s)) {
		$s = preg_replace('/-/', '', $s);
		$sqlval["shipping_zip"] = substr($s, 0, 3) . "-" . substr($s, 3, 4);
	    }

	    $arrOther["name"] = $sqlval["shipping_name"];
	    $arrOther["kana"] = $sqlval["shipping_kana"];
	    $arrOther["zip"] = $sqlval["shipping_zip"];
	    $arrOther["pref"] = $sqlval["shipping_pref"];
	    $arrOther["addr01"] = $sqlval["shipping_addr01"];
	    $arrOther["addr02"] = $sqlval["shipping_addr02"];
	    $arrOther["tel"] = $sqlval["shipping_tel"];
	}
	// お届け先用配列削除
	unset($sqlval["other_addr_flg"]);
	unset($sqlval["shipping_name"]);
	unset($sqlval["shipping_name01"]);
	unset($sqlval["shipping_name02"]);
	unset($sqlval["shipping_kana"]);
	unset($sqlval["shipping_kana01"]);
	unset($sqlval["shipping_kana02"]);
	unset($sqlval["shipping_zip"]);
	unset($sqlval["shipping_pref"]);
	unset($sqlval["shipping_addr01"]);
	unset($sqlval["shipping_addr02"]);
	unset($sqlval["shipping_house_no"]);
	unset($sqlval["shipping_tel"]);
	// 名前用不要項目削除
	unset($sqlval["name01"]);
	unset($sqlval["name02"]);
	unset($sqlval["kana01"]);
	unset($sqlval["kana02"]);

	// ハイフン対応
	$s = $sqlval["tel"];
	if ($s && strlen($s)) {
	    $sqlval["tel"] = preg_replace('/-/', '', $s);
	}

	$s = trim($sqlval["zip"]);
	if ($s && strlen($s)) {
	    $s = preg_replace('/-/', '', $s);
	    $sqlval["zip"] = substr($s, 0, 3) . "-" . substr($s, 3, 4);
	}

	$customerId = "";
	if ($_SESSION["new_customer_id"]) {
	    $customerId = $_SESSION["new_customer_id"];
	    $sqlval["creator_id"] = $customerId;
	    $sqlval["updator_id"] = $customerId;
	}
	$sqlval["create_date"] = "now()";
	$sqlval["update_date"] = "now()";

	// 顧客テーブル登録
        SC_Helper_Customer_Ex::sfEditCustomerData($sqlval, $customerId);

	// 顧客ID取得
	$customerId = SC_Helper_Customer_Ex::sfGetCustomerId($sqlval["secret_key"]);
	$_SESSION["new_customer_id"] = $customerId;
	$_SESSION["new_secret_key"] = $sqlval["secret_key"];
	$sqlval["customer_id"] = $customerId;

	// お届け先が別の場合
	if ($otherFlg) {
	    unset($array['shipping_house_no']);
	    $arrOther["customer_id"] = $customerId;
	    $arrOther["create_date"] = "now()";
	    $arrOther["creator_id"] = $customerId;
	    $arrOther["update_date"] = "now()";
	    $arrOther["updator_id"] = $customerId;
	    if (!$_SESSION["new_other_deliv_id"]) {
		$arrOther['other_deliv_id'] =
		    $objQuery->nextVal('dtb_other_deliv_other_deliv_id');
		$_SESSION["new_other_deliv_id"] = $arrOther['other_deliv_id'];
		$objQuery->insert("dtb_other_deliv", $arrOther);
	    } else {
		$objQuery->update("dtb_other_deliv", $arrOther, "other_deliv_id = ?"
				, array($_SESSION["new_other_deliv_id"]));
	    }
	    $objPurchase->copyFromOrder($sqlval, $sqlval, "order", "");
	    $objPurchase->copyFromOrder($sqlval, $arrOther, "shipping", "");
	} else {
	    $objPurchase->copyFromOrder($sqlval, $sqlval, "order", "");
	    $objPurchase->copyFromOrder($sqlval, $sqlval);
	}
	$sqlval["order_email"] = $sqlval["email"];
	$sqlval["order_birth"] = $sqlval["birth"];
	$objPurchase->saveShippingTemp($sqlval);
	$objCustomer->setValue("customer_id", $customerId);
	$objCustomer->updateSession();
	$objPurchase->saveOrderTemp($uniqid, $sqlval, $objCustomer);

        return $sqlval["secret_key"];
    }

    /**
     * 会員登録に必要なSQLパラメーターの配列を生成する.
     *
     * フォームに入力された情報を元に, SQLパラメーターの配列を生成する.
     * モバイル端末の場合は, email を email_mobile にコピーし,
     * mobile_phone_id に携帯端末IDを格納する.
     *
     * @param mixed $objFormParam
     * @access private
     * @return $arrResults
     */
    function lfMakeSqlVal(&$objFormParam) {
        $arrForm                = $objFormParam->getHashArray();
        $arrResults             = $objFormParam->getDbArray();

        // 生年月日の作成
        $arrResults['birth']    = SC_Utils_Ex::sfGetTimestamp($arrForm['year'], $arrForm['month'], $arrForm['day']);

        // 仮会員 1 本会員 2
        $arrResults['status']   = (CUSTOMER_CONFIRM_MAIL == true) ? "1" : "2";

        /*
         * secret_keyは、テーブルで重複許可されていない場合があるので、
         * 本会員登録では利用されないがセットしておく。
         */
	if ($_SESSION["new_secret_key"]) {
	    $arrResults["secret_key"] = $_SESSION["new_secret_key"];
	} else {
	    $arrResults["secret_key"] = SC_Helper_Customer_Ex::sfGetUniqSecretKey();
	}

        // 入会時ポイント
        $CONF = SC_Helper_DB_Ex::sfGetBasisData();
        $arrResults['point'] = $CONF["welcome_point"];

        if (SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE) {
            // 携帯メールアドレス
            $arrResults['email_mobile']     = $arrResults['email'];
            // PHONE_IDを取り出す
            $arrResults['mobile_phone_id']  =  SC_MobileUserAgent_Ex::getId();
        }
        return $arrResults;
    }

    /**
     * 会員登録完了メール送信する
     *
     * @access private
     * @return void
     */
    function lfSendMail($uniqid, $arrForm){
        $CONF           = SC_Helper_DB_Ex::sfGetBasisData();

        $objMailText    = new SC_SiteView_Ex();
        $objMailText->assign('CONF', $CONF);
        $objMailText->assign("name", $arrForm['name']);
        $objMailText->assign("name01", $arrForm['name01']);
        $objMailText->assign("name02", $arrForm['name02']);
        $objMailText->assign('uniqid', $uniqid);
        $objMailText->assignobj($this);

        $objHelperMail  = new SC_Helper_Mail_Ex();

        // 仮会員が有効の場合
        if(CUSTOMER_CONFIRM_MAIL == true) {
            $subject        = $objHelperMail->sfMakeSubject('会員登録のご確認');
            $toCustomerMail = $objMailText->fetch("mail_templates/customer_mail.tpl");
        } else {
            $subject        = $objHelperMail->sfMakeSubject('会員登録のご完了');
            $toCustomerMail = $objMailText->fetch("mail_templates/customer_regist_mail.tpl");
        }

        $objMail = new SC_SendMail();
        $objMail->setItem(
            ''                    // 宛先
            , $subject              // サブジェクト
            , $toCustomerMail       // 本文
            , $CONF["email03"]      // 配送元アドレス
            , $CONF["shop_name"]    // 配送元 名前
            , $CONF["email03"]      // reply_to
            , $CONF["email04"]      // return_path
            , $CONF["email04"]      // Errors_to
            , $CONF["email01"]      // Bcc
        );
        // 宛先の設定
        $objMail->setTo($arrForm['email'],
                        $arrForm["name"] ." 様");

        $objMail->sendMail();
    }

    /**
     * kiyaku.php からの遷移の妥当性をチェックする
     *
     * 以下の内容をチェックし, 妥当であれば true を返す.
     * 1. 規約ページからの遷移かどうか
     * 2. PC及びスマートフォンかどうか
     * 3. $post に何も含まれていないかどうか
     *
     * @access protected
     * @param array $post $_POST のデータ
     * @param string $referer $_SERVER['HTTP_REFERER'] のデータ
     * @return boolean kiyaku.php からの妥当な遷移であれば true
     */
    function lfCheckReferer(&$post, $referer){

        if (SC_Display_Ex::detectDevice() !== DEVICE_TYPE_MOBILE
            && empty($post)
            && (preg_match('/kiyaku.php/', basename($referer)) === 0)) {
            return false;
            }
        return true;
    }

    /**
     * 以下規約関係
     */

    /**
     * 規約文の作成
     *
     * @param mixed $arrKiyaku
     * @param mixed $max
     * @param mixed $offset
     * @access public
     * @return string 規約の内容をテキストエリアで表示するように整形したデータ
     */
    function lfMakeKiyakuText($arrKiyaku, $max, $offset) {
        $this->tpl_kiyaku_text = "";
        for ($i = 0; $i < $max; $i++) {
            if ($offset !== null && ($offset - 1) <> $i) continue;
            $tpl_kiyaku_text.=$arrKiyaku[$i]['kiyaku_title'] . "\n\n";
            $tpl_kiyaku_text.=$arrKiyaku[$i]['kiyaku_text'] . "\n\n";
        }
        return $tpl_kiyaku_text;
    }

    /**
     * 規約内容の取得
     *
     * @access private
     * @return array $arrKiyaku 規約の配列
     */
    function lfGetKiyakuData() {

        $objQuery   = SC_Query_Ex::getSingletonInstance();

        $sql =<<<EOF
SELECT
    kiyaku_title,
    kiyaku_text
FROM
    dtb_kiyaku
WHERE
    del_flg <> 1
ORDER BY rank DESC
EOF;

        return $objQuery->getAll($sql);
    }

    /**
     *
     * 携帯の場合getで来る次ページのidを適切に処理する
     *
     * @param mixed $offset
     * @access private
     * @return int
     */
    function lfSetOffset($offset) {
       return is_numeric($offset) === true ? intval($offset) : 1;
    }

    /**
     * 入力内容をDBのカラムに合わせてセットする
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @access public
     * @return void
     */
    function setCombiData($objFormParam) {

	$name01 = $objFormParam->getValue("name01");
	$name02 = $objFormParam->getValue("name02");
	$kana01 = $objFormParam->getValue("kana01");
	$kana02 = $objFormParam->getValue("kana02");
	$name = sprintf("%s　%s", $name01, $name02);
	$kana = sprintf("%s　%s", $kana01, $kana02);
	$prefBytes = mb_strlen($this->arrPref[$this->arrForm['pref']]) * 2;
	$objFormParam->setValue("name", $name);
	$objFormParam->setValue("kana", $kana);
	$this->arrForm["name"] = $name;
	$this->arrForm["kana"] = $kana;
	// お届け先指定の場合
	if ($objFormParam->getValue("other_addr_flg")) {
	    $name01 = $objFormParam->getValue("shipping_name01");
	    $name02 = $objFormParam->getValue("shipping_name02");
	    $kana01 = $objFormParam->getValue("shipping_kana01");
	    $kana02 = $objFormParam->getValue("shipping_kana02");
	    $name = sprintf("%s　%s", $name01, $name02);
	    $kana = sprintf("%s　%s", $kana01, $kana02);
	    $objFormParam->setValue("shipping_name", $name);
	    $objFormParam->setValue("shipping_kana", $kana);
	    $this->arrForm["shipping_name"] = $name;
	    $this->arrForm["shipping_kana"] = $kana;
	}
    }

    /**
     * お届け先フォーム初期化
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @access public
     * @return void
     */
    function lfShippingParam(&$objFormParam) {
	// 顧客情報
        $objFormParam->addParam("お名前(姓)", 'name01', STEXT_LEN, 'aKV', array("EXIST_CHECK", "NO_TAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "CHECK_WQ_SQ_C"));
        $objFormParam->addParam("お名前(名)", 'name02', STEXT_LEN, 'aKV', array("EXIST_CHECK", "NO_TAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "CHECK_WQ_SQ_C"));
        $objFormParam->addParam("お名前(フリガナ)(姓)", 'kana01', STEXT_LEN, 'hks', array("EXIST_CHECK", "NO_TAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "HANKANASP_CHECK", "CHECK_WQ_SQ_C"));
        $objFormParam->addParam("お名前(フリガナ)(名)", 'kana02', STEXT_LEN, 'hks', array("EXIST_CHECK", "NO_TAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "HANKANASP_CHECK", "CHECK_WQ_SQ_C"));
	// お届先情報
        $objFormParam->addParam("お届け先チェック", 'other_addr_flg', INT_LEN, 'n', array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam("お名前", 'shipping_name', STEXT_LEN*2, 'aKV', array("NO_TAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $objFormParam->addParam("お名前(姓)", 'shipping_name01', STEXT_LEN, 'aKV', array("NO_TAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "CHECK_WQ_SQ_C"));
        $objFormParam->addParam("お名前(名)", 'shipping_name02', STEXT_LEN, 'aKV', array("NO_TAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "CHECK_WQ_SQ_C"));
        $objFormParam->addParam("お名前(フリガナ)", 'shipping_kana', STEXT_LEN*2, 'hks', array("NO_TAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "HANKANASP_CHECK"));
        $objFormParam->addParam("お名前(フリガナ)(姓)", 'shipping_kana01', STEXT_LEN, 'hks', array("NO_TAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "HANKANASP_CHECK"));
        $objFormParam->addParam("お名前(フリガナ)(名)", 'shipping_kana02', STEXT_LEN, 'hks', array("NO_TAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "HANKANASP_CHECK"));
        $objFormParam->addParam("郵便番号", "shipping_zip", ZIP_LEN, 'n', array("ZIP_CHECK"));
        $objFormParam->addParam("都道府県", 'shipping_pref', INT_LEN, 'n', array("NUM_CHECK"));
        $objFormParam->addParam("市区町村", "shipping_addr01", ADDRESS_LEN, 'aKV', array("SPTAB_CHECK", "MAX_BYTES_SJIS", "CHECK_WQ_SQ_C"));
        $objFormParam->addParam("番地・ビル名", "shipping_addr02", ADDRESS_LEN, 'aKV', array("SPTAB_CHECK", "MAX_BYTES_SJIS", "CHECK_WQ_SQ_C"));
        $objFormParam->addParam("電話番号", 'shipping_tel', TEL_ITEM_LEN*3, 'n', array("SPTAB_CHECK", "IS_TELEPHONE", "MAX_LENGTH_CHECK"));
	$objFormParam->addParam('番地なし', "shipping_house_no");
    }

    /**
     * お届け先フォーム初期化
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param array エラー情報
     * @access public
     * @return array エラー情報
     */
    function lfShippingErrorCheck(&$objFormParam, $arrErr) {
        $objFormParam->convParam();
        $arrParams = $objFormParam->getHashArray();

        $objErr = new SC_CheckError_Ex($arrParams);
        $objErr->arrErr = $arrErr;

	// 別お届け先指定時必須チェック追加
	if ($objFormParam->getValue("other_addr_flg")) {
	    $objErr->doFunc(array("お名前", "shipping_name"), array("EXIST_CHECK"));
	    $objErr->doFunc(array("お名前(姓)", "shipping_name01"), array("EXIST_CHECK"));
	    $objErr->doFunc(array("お名前(名)", "shipping_name02"), array("EXIST_CHECK"));
	    $objErr->doFunc(array("お名前(フリガナ)", "shipping_kana"), array("EXIST_CHECK"));
	    $objErr->doFunc(array("お名前(フリガナ)(姓)", "shipping_kana01"), array("EXIST_CHECK"));
	    $objErr->doFunc(array("お名前(フリガナ)(名)", "shipping_kana02"), array("EXIST_CHECK"));
	    $objErr->doFunc(array("郵便番号", "shipping_zip"), array("EXIST_CHECK", "ZIP_CHECK"));
	    $objErr->doFunc(array("都道府県", "shipping_pref"), array("EXIST_CHECK"));
	    $objErr->doFunc(array("市区町村", "shipping_addr01"), array("EXIST_CHECK"));
	    $objErr->doFunc(array("番地・ビル名", "shipping_addr02"), array("EXIST_CHECK"));
	    $objErr->doFunc(array("電話番号", "shipping_tel"), array("EXIST_CHECK", "IS_TELEPHONE"));
	}

	return $objErr->arrErr;
    }

}
?>
