<?php

/*  [名称] SC_Customer
 *  [概要] 会員管理クラス
 */
class SC_Customer {

    /** 会員情報 */
    var $customer_data;

    function getCustomerDataFromEmailPass( $pass, $email, $mobile = false ) {
        // 2013/12/17 del okayama 小文字変換させない
        // 小文字に変換
        //$email = strtolower($email);
        $sql_mobile = $mobile ? ' OR email_mobile = binary ?' : '';
        $arrValues = array($email);
        if ($mobile) {
            $arrValues[] = $email;
        }
        // 本登録された会員のみ
        $sql =<<<EOF
SELECT * 
FROM
    dtb_customer
WHERE
    ( email = binary ? $sql_mobile )
    AND del_flg = 0
    AND status = 2
EOF;
        $objQuery = new SC_Query_Ex();
        $result = $objQuery->getAll($sql, $arrValues);
        if (empty($result)) {
            return false;
        } else {
            $data = $result[0];
        }

        // パスワードが合っていれば会員情報をcustomer_dataにセットしてtrueを返す
        if ( SC_Utils_Ex::sfIsMatchHashPassword($pass, $data['password'], $data['salt']) ) {
            // 貸倒顧客判定を行う、貸倒顧客の場合は、ログインさせない
            //   0: 通常顧客、1: 貸倒顧客
            if ($data['kashidaore_kbn'] == "1") {
                return false;
            }

            $this->customer_data = $data;

            // ログイン成功時、所持ポイントを更新 #205にて廃止
            // $this->editPoint();
            
            $this->startSession();
            return true;
        }
        return false;
    }

   /**
     * 所持ポイントを更新する
     *
     * @return boolean false:該当なし
     */
    function editPoint() {

        // ポイント取得
        $point = $this->customer_data['point'];
        $pointValidDate = $this->customer_data['point_valid_date'];
        // お誕生日ポイント取得
        $birthPoint = $this->customer_data['birth_point'];
        $birthPointValidDate = $this->customer_data['birth_point_valid_date'];

        // ポイントの有効期限チェック
        if (strtotime(date('Y-m-d')) > strtotime($pointValidDate)) {
            $point = 0;
        }
        // お誕生日ポイントの有効期限チェック
        if (strtotime(date('Y-m-d')) > strtotime($birthPointValidDate)) {
            $birthPoint = 0;
        }

        // 更新
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sqlval = array();
        $sqlval['point'] = $point;
        $sqlval['birth_point'] = $birthPoint;
        $where = 'customer_id = ?';
        $objQuery->update('dtb_customer',$sqlval, $where, array($this->customer_data['customer_id']));
        $objQuery->commit();

        // ログイン情報設定
        $this->customer_data['point'] = $point;
        $this->customer_data['birth_point'] = $birthPoint;
        return true;
    }

    /**
     * 会員の登録住所を取得する.
     *
     * 配列の1番目に会員登録住所, 追加登録住所が存在する場合は2番目以降に
     * 設定される.
     *
     * @param integer $customer_id 顧客ID
     * @return array 会員登録住所, 追加登録住所の配列
     */
    function getCustomerAddress($customer_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<EOF
SELECT
    *
FROM
    (SELECT
        NULL AS other_deliv_id,
        customer_id,
        name01,
        name02,
        name,
        kana01,
        kana02,
        zip01,
        zip02,
        zip,
        pref,
        addr01,
        addr02,
        email,
        email_mobile,
        tel01,
        tel02,
        tel03,
        fax01,
        fax02,
        fax03
    FROM
        dtb_customer
    WHERE
        customer_id = $customer_id
        UNION ALL
            SELECT
                other_deliv_id,
                customer_id,
                name01, name02,
                name,
                kana01, kana02,
                zip01, zip02,
                zip,
                pref,
                addr01, addr02,
                NULL AS email, NULL AS email_mobile,
                tel01, tel02, tel03,
                NULL AS fax01, NULL AS fax02, NULL AS fax03
            FROM
                dtb_other_deliv
            WHERE
                customer_id = $customer_id
    ) AS addrs
ORDER BY other_deliv_id IS NULL DESC, other_deliv_id DESC
EOF;
        return $objQuery->getAll($sql);
    }

    /**
     * 携帯端末IDが一致する会員が存在するかどうかをチェックする。
     * FIXME
     * @return boolean 該当する会員が存在する場合は true、それ以外の場合
     *                 は false を返す。
     */
    function checkMobilePhoneId() {
        //docomo用にデータを取り出す。
        if(SC_MobileUserAgent_Ex::getCarrier() == 'docomo'){
            if($_SESSION['mobile']['phone_id'] == "" && strlen($_SESSION['mobile']['phone_id']) == 0)
                $_SESSION['mobile']['phone_id'] = SC_MobileUserAgent_Ex::getId();
        }
        if (!isset($_SESSION['mobile']['phone_id']) || $_SESSION['mobile']['phone_id'] === false) {
            return false;
        }

        // 携帯端末IDが一致し、本登録された会員を検索する。
        $objQuery = new SC_Query_Ex();
        $result = $objQuery->count(
            "dtb_customer",
            "mobile_phone_id = ? AND del_flg = 0 AND status = 2",
            array($_SESSION['mobile']['phone_id'])
        );
        return $result > 0;
    }

    /**
     * 携帯端末IDを使用して会員を検索し、パスワードの照合を行う。
     * パスワードが合っている場合は会員情報を取得する。
     *
     * @param string $pass パスワード
     * @return boolean 該当する会員が存在し、パスワードが合っている場合は true、
     *                 それ以外の場合は false を返す。
     */
    function getCustomerDataFromMobilePhoneIdPass($pass) {
        //docomo用にデータを取り出す。
        if(SC_MobileUserAgent_Ex::getCarrier() == 'docomo'){
            if($_SESSION['mobile']['phone_id'] == "" && strlen($_SESSION['mobile']['phone_id']) == 0)
                $_SESSION['mobile']['phone_id'] = SC_MobileUserAgent_Ex::getId();
        }
        if (!isset($_SESSION['mobile']['phone_id'])
            || $_SESSION['mobile']['phone_id'] === false) {

            return false;
        }

        // 携帯端末IDが一致し、本登録された会員を検索する。
        $phone_id = $_SESSION['mobile']['phone_id'];
        $sql =<<<EOF
SELECT
    *
FROM
    dtb_customer
WHERE
    mobile_phone_id = $phone_id
    AND del_flg = 0
    AND status = 2
EOF;
        $objQuery = new SC_Query_Ex();
        @list($data) = $objQuery->getAll($sql);

        // パスワードが合っている場合は、会員情報をcustomer_dataに格納してtrueを返す。
        if ( SC_Utils_Ex::sfIsMatchHashPassword(
            $pass, $data['password'], $data['salt']) ) {

            $this->customer_data = $data;
            $this->startSession();
            return true;
        }
        return false;
    }

    function updateLastLoginDate()
    {
        $objQuery = new SC_Query_Ex();

        $id = $this->customer_data['customer_id'];

        $sql =<<<EOF
UPDATE dtb_customer
   SET lastlogin_date = now()
 WHERE customer_id = $id
EOF;
	
        $objQuery->query($sql);
    }

    /**
     * 携帯端末IDを登録する。
     *
     * @return void
     */
    function updateMobilePhoneId() {
        if (!isset($_SESSION['mobile']['phone_id'])
            || $_SESSION['mobile']['phone_id'] === false) {

            return;
        }

        if ($this->customer_data['mobile_phone_id']
            == $_SESSION['mobile']['phone_id']) {

            return;
        }

        $objQuery = new SC_Query_Ex();
        $sqlval = array('mobile_phone_id' => $_SESSION['mobile']['phone_id']);
        $where = 'customer_id = ? AND del_flg = 0 AND status = 2';
        $objQuery->update('dtb_customer', $sqlval, $where, array($this->customer_data['customer_id']));

        $this->customer_data['mobile_phone_id'] = $_SESSION['mobile']['phone_id'];
    }

    // パスワードを確認せずにログイン
    function setLogin($email) {
        // 本登録された会員のみ
        $sql =<<<EOF
SELECT
    *
FROM
    dtb_customer
WHERE
    (email = '$email' OR email_mobile = '$email')
    AND del_flg = 0
    AND status = 2
EOF;
        $objQuery = new SC_Query_Ex();
        $result = $objQuery->getAll($sql);
        $data = isset($result[0]) ? $result[0] : "";
        $this->customer_data = $data;
        $this->startSession();
    }

    // セッション情報を最新の情報に更新する
    function updateSession() {
        $customer_id = $this->getValue('customer_id');
        $objQuery = new SC_Query_Ex();
        $sql =<<<EOF
SELECT
    *
FROM
    dtb_customer
WHERE
    customer_id = $customer_id
    AND del_flg = 0
EOF;
        $arrRet = $objQuery->getAll($sql);
        $this->customer_data = isset($arrRet[0]) ? $arrRet[0] : "";
        $_SESSION['customer'] = $this->customer_data;
    }

    // ログイン情報をセッションに登録し、ログに書き込む
    function startSession() {
        $_SESSION['customer'] = $this->customer_data;
        // セッション情報の保存
        GC_Utils_Ex::gfPrintLog("access : user=".$this->customer_data['customer_id'] ."\t"."ip=". $this->getRemoteHost(), CUSTOMER_LOG_REALFILE );
    }

    // ログアウト　$_SESSION['customer']を解放し、ログに書き込む
    function EndSession() {
        // $_SESSION['customer']の解放
        unset($_SESSION['customer']);
        // 新規作成された顧客IDの解放
        unset($_SESSION['new_customer_id']);
		unset($_SESSION["new_secret_key"]);
        // トランザクショントークンの破棄
        SC_Helper_Session_Ex::destroyToken();
        $objSiteSess = new SC_SiteSession_Ex();
        $objSiteSess->unsetUniqId();
        // ログに記録する
        GC_Utils_Ex::gfPrintLog("logout : user=".$this->customer_data['customer_id'] ."\t"."ip=". $this->getRemoteHost(), CUSTOMER_LOG_REALFILE );
    }

    // ログインに成功しているか判定する。
    function isLoginSuccess($dont_check_email_mobile = false) {
        // ログイン時のメールアドレスとDBのメールアドレスが一致している場合
        if(isset($_SESSION['customer']['customer_id'])
            && SC_Utils_Ex::sfIsInt($_SESSION['customer']['customer_id'])) {

            $objQuery = new SC_Query_Ex();
            $email = $objQuery->get('email', "dtb_customer", "customer_id = ?", array($_SESSION['customer']['customer_id']));
            if($email == $_SESSION['customer']['email']) {
                // モバイルサイトの場合は携帯のメールアドレスが登録されていることもチェックする。
                // ただし $dont_check_email_mobile が true の場合はチェックしない。

                /* XXX 2014.2.3 takao 携帯メールアドレスは使用しないのでチェックしない
                if (SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE
                    && !$dont_check_email_mobile) {

                    $email_mobile = $objQuery->get("email_mobile", "dtb_customer", "customer_id = ?", array($_SESSION['customer']['customer_id']));
                    return isset($email_mobile);
                 }
                 */

                return true;
            }
        }
        return false;
    }

    // パラメーターの取得
    function getValue($keyname) {
        // ポイントはリアルタイム表示
        if ($keyname == 'point') {
            $objQuery =& SC_Query_Ex::getSingletonInstance();
            $point = $objQuery->get('point', 'dtb_customer', 'customer_id = ?', array($_SESSION['customer']['customer_id']));
            if (strlen($point) > 0) {
                $_SESSION['customer']['point'] = $point;
            }
            return $point;
        // お誕生日ポイントはリアルタイム表示
        } elseif ($keyname == 'birth_point') {
            $objQuery =& SC_Query_Ex::getSingletonInstance();
            $birthPoint = $objQuery->get('birth_point', 'dtb_customer', 'customer_id = ?', array($_SESSION['customer']['customer_id']));
            if (strlen($birthPoint) > 0) {
                $_SESSION['customer']['birth_point'] = $birthPoint;
            }
            return $birthPoint;
        } else {
            return isset($_SESSION['customer'][$keyname]) ? $_SESSION['customer'][$keyname] : "";
        }
    }

    // パラメーターのセット
    function setValue($keyname, $val) {
        $_SESSION['customer'][$keyname] = $val;
    }

    // パラメーターがNULLかどうかの判定
    function hasValue($keyname) {
        if (isset($_SESSION['customer'][$keyname])) {
            return !SC_Utils_Ex::isBlank($_SESSION['customer'][$keyname]);
        }
        return false;
    }

    // 誕生日月であるかどうかの判定
    function isBirthMonth() {
        if (isset($_SESSION['customer']['birth'])) {
            $arrRet = preg_split("|[- :/]|", $_SESSION['customer']['birth']);
            $birth_month = intval($arrRet[1]);
            $now_month = intval(date('m'));

            if($birth_month == $now_month) {
                return true;
            }
        }
        return false;
    }

    /**
     * $_SERVER['REMOTE_HOST'] または $_SERVER['REMOTE_ADDR'] を返す.
     *
     * $_SERVER['REMOTE_HOST'] が取得できない場合は $_SERVER['REMOTE_ADDR']
     * を返す.
     *
     * @return string $_SERVER['REMOTE_HOST'] 又は $_SERVER['REMOTE_ADDR']の文字列
     */
    function getRemoteHost() {

        if (!empty($_SERVER['REMOTE_HOST'])) {
            return $_SERVER['REMOTE_HOST'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        } else {
            return "";
        }
    }

    /**
     * 受注関連の顧客情報を更新
     *
     * @param $customer_id 顧客ID
     * @return none
     */
    function updateOrderSummary($customer_id){

        $objQuery = new SC_Query_Ex();

        $arrOrderSummary =  $objQuery->getRow("SUM( payment_total ) as buy_total, COUNT(order_id) as buy_times,MAX( create_date ) as last_buy_date, MIN(create_date) as first_buy_date","dtb_order","customer_id = ? AND del_flg = 0 AND status <> ?",array($customer_id,ORDER_CANCEL));
        $objQuery->update("dtb_customer",$arrOrderSummary,"customer_id = ?",array($customer_id));

    }

    /**
     * 初回購入か判定
     *
     * @param none
     * @return true:初回購入 false:2回目以上
     */
    function checkFirstTimePurchase() {
        $objPurchase = new SC_Helper_Purchase_Ex();

        // 初期値定義
        $result = false;
        $customer_id = "";
        $history_cnt = 0;

        // 顧客IDを取得
        $customer_id = $this->getValue('customer_id');

        // 過去3年の購入回数を取得
        $history_cnt = $objPurchase->getOrderCountLastThreeYears($customer_id);

        // 購入回数がない場合 
        if ($history_cnt == 0) {
            $result = true;
        }
        return $result;
    }
}
?>
