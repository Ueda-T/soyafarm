<?php

require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/*
 * パスワード発行 のページクラス.
 */
class LC_Page_Renewal extends LC_Page_Ex {
    /** フォームパラメーターの配列 */
    var $objFormParam;

    /** 変更後パスワード */
    var $temp_password;

    /** エラーメッセージ */
    var $errmsg;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_title = "パスワードの再発行";
        $this->tpl_mainpage = 'renewal/index.tpl';
        $this->tpl_mainno = '';
        $masterData = new SC_DB_MasterData_Ex();
        $this->device_type = SC_Display_Ex::detectDevice();
        $this->httpCacheControl('nocache');
        // デフォルトログインアドレスロード
        $objCookie = new SC_Cookie_Ex(COOKIE_EXPIRE);
        $this->tpl_login_email = $objCookie->getCookie('login_email');        
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

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        // パラメーター管理クラス
        $objFormParam = new SC_FormParam_Ex();

        switch($this->getMode()) {
            case 'mail_check':
                $this->lfInitMailCheckParam($objFormParam, $this->device_type);
                $objFormParam->setParam($_POST);
                $objFormParam->convParam();
                // 2013/12/18 del okayama 小文字変換させない
                //$objFormParam->toLower('email');
                $this->arrForm = $objFormParam->getHashArray();
                $this->arrErr = $objFormParam->checkError();
                if (SC_Utils_Ex::isBlank($this->arrErr)) {
                    $this->errmsg = $this->lfCheckRenewalMail($this->arrForm);
                    if (SC_Utils_Ex::isBlank($this->errmsg)) {
                        // 完了ページへ移動する
                        $this->tpl_mainpage = 'renewal/complete.tpl';
                    }
                }
                break;
            default:
                break;
        }

    }

    /**
     * メールアドレス・名前確認
     *
     * @param array $arrForm フォーム入力値
     * @return string エラー文字列 問題が無ければNULL
     */
    function lfCheckRenewalMail(&$arrForm) {
        $errmsg = '';
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $email = $arrForm['email'];
        // スペース除去
        $name  = preg_replace('/(\s|　)/', '', $arrForm['name']);

        $sql =<<<EOF
SELECT count(*) AS cnt
FROM   dtb_customer
WHERE email = BINARY '$email'
  AND replace(replace(name, ' ', ''), '　', '') = '$name'
  AND del_flg = 0
EOF;
        $n = intval($objQuery->getOne($sql));
        if ($n > 1) {
	    return "ご入力いただいた内容で既に会員登録されています。";
	}

        $sql =<<<EOF
SELECT customer_id
FROM   dtb_customer
WHERE email = BINARY '$email'
  AND replace(replace(name, ' ', ''), '　', '') = '$name'
  AND del_flg = 0
  AND status = 1
EOF;

        $result = $objQuery->getOne($sql);
        if (strlen($result) > 0) {
            // 新しいパスワードを設定する
            $new_password = GC_Utils_Ex::gfMakePassword(8);
            // メールで変更通知をする
            $objDb = new SC_Helper_DB_Ex();
            $CONF = $objDb->sfGetBasisData();

            $this->lfSendMail($CONF, $arrForm['email'], $arrForm['name'], $new_password);

            $sqlval = array();
            $sqlval['password'] = $new_password;
            $sqlval['status'] = CUSTOMER_STATUS_MEMBER;
            $sqlval['customer_id'] = $result;

            // パスワード更新
            $this->lfUpdateCustomer($sqlval);
	    return;
        }

	if ($n)  {
	    $errmsg = 'パスワードの再発行は既に完了しています。';
	} else {
	    $errmsg = 'ご入力内容に該当する登録が確認できません。';
	}
        
        return $errmsg;
    }

    /**
     * メールアドレス確認におけるパラメーター情報の初期化
     *
     * @param array $objFormParam フォームパラメータークラス
     * @param array $device_type デバイスタイプ
     * @return void
     */
    function lfInitMailCheckParam(&$objFormParam, $device_type) {

        $objFormParam->addParam("お名前", 'name', STEXT_LEN, '', array("EXIST_CHECK", "NO_TAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
	/* 判定はPC、スマホ、モバイル共通の為コメントアウト
        if ($device_type === DEVICE_TYPE_MOBILE){
            $objFormParam->addParam('メールアドレス', 'email', null, '', array("EXIST_CHECK", "EMAIL_CHECK", "NO_SPTAB" ,"EMAIL_CHAR_CHECK", "MOBILE_EMAIL_CHECK"));
        } else {
            $objFormParam->addParam('メールアドレス', 'email', null, '', array("NO_SPTAB", "EXIST_CHECK", "EMAIL_CHECK", "SPTAB_CHECK" ,"EMAIL_CHAR_CHECK"));
        }
	 */
        $objFormParam->addParam('メールアドレス', 'email', null, '', array("NO_SPTAB", "EXIST_CHECK", "EMAIL_CHECK", "SPTAB_CHECK" ,"EMAIL_CHAR_CHECK"));
        return;
    }

    /**
     * パスワード変更お知らせメールを送信する.
     *
     * @param array $CONF 店舗基本情報の配列
     * @param string $email 送信先メールアドレス
     * @param string $customer_name 送信先氏名
     * @param string $new_password 変更後の新パスワード
     * @return void
     *
     * FIXME: メールテンプレート編集の方に足すのが望ましい
     */
    function lfSendMail(&$CONF, $email, $customer_name, $new_password){
        // パスワード変更お知らせメール送信
        $objMailText = new SC_SiteView_Ex(false);
        $objMailText->assign('customer_name', $customer_name);
        $objMailText->assign('new_password', $new_password);
        $toCustomerMail = $objMailText->fetch("mail_templates/renewal_mail.tpl");
        $objHelperMail  = new SC_Helper_Mail_Ex();
        // メール送信オブジェクトによる送信処理
        $objMail = new SC_SendMail();
        $objMail->setItem(
            '' //宛先
            , $objHelperMail->sfMakeSubject('パスワードを変更いたしました。')
            , $toCustomerMail //本文
            , $CONF['email03'] //配送元アドレス
            , MAIL_TITLE_SHOP_NAME // 配送元名
            , $CONF['email03'] // reply to
            , $CONF['email04'] //return_path
            , $CONF['email04'] // errors_to
            );
        $objMail->setTo($email, $customer_name ." 様");
        $objMail->sendMail();
    }

    /*
     * 顧客情報の更新を行う
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param array    $arrData  登録データ
     * @return void
     */
    function lfUpdateCustomer($arrData) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $customer_id = $arrData['customer_id'];

        // salt取得
        $salt = $objQuery->get
            ('salt', "dtb_customer", "customer_id = ? ", array($customer_id));

        // パスワードを暗号化
        $password = SC_Utils_Ex::sfGetHashString
            ($arrData['password'], $salt);

        $sql =<<<__EOS
UPDATE
    dtb_customer
SET
    password     = '{$password}'
   ,status       = {$arrData['status']}
   ,update_date  = now()
   ,updator_id   = {$customer_id}
WHERE
    customer_id = {$customer_id}
__EOS;

        GC_Utils::gfFrontLog($sql);

        // 実行
        $objQuery->exec($sql);
    }

}
?>
