<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * パスワード発行 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Forgot.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Forgot extends LC_Page_Ex {

    // {{{ properties

    /** フォームパラメーターの配列 */
    var $objFormParam;

    /** 秘密の質問の答え */
    var $arrReminder;

    /** 変更後パスワード */
    var $temp_password;

    /** エラーメッセージ */
    var $errmsg;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_title = "パスワードを忘れた方";
        $this->tpl_mainpage = 'forgot/index.tpl';
        $this->tpl_mainno = '';
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrReminder = $masterData->getMasterData("mtb_reminder");
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
                $this->arrForm = $objFormParam->getHashArray();
                $this->arrErr = $objFormParam->checkError();

                if (SC_Utils_Ex::isBlank($this->arrErr)) {
                    $this->errmsg = $this->lfCheckForgotMail($this->arrForm, $this->arrReminder);
                    if(SC_Utils_Ex::isBlank($this->errmsg)) {
                        // 完了ページへ移動する
                        $this->tpl_mainpage = 'forgot/complete.tpl';
                    }
                }
                break;
            default:
                break;
        }

        // ポップアップ用テンプレート設定
        if($this->device_type == DEVICE_TYPE_PC) {
            $this->setTemplate($this->tpl_mainpage);
        }
    }

    /**
     * メールアドレス・名前確認
     *
     * @param array $arrForm フォーム入力値
     * @param array $arrReminder リマインダー質問リスト
     * @return string エラー文字列 問題が無ければNULL
     */
    function lfCheckForgotMail(&$arrForm, &$arrReminder) {
        $errmsg = NULL;
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<EOF
SELECT
    customer_id
   ,status
FROM
    dtb_customer
WHERE
    email = BINARY '%s'
    AND replace(replace(name, ' ', ''), '　', '') = '%s'
    AND del_flg = 0
EOF;
        $sql = sprintf($sql,
            $arrForm['email'],
            preg_replace('/(\s|　)/', '', $arrForm['name'])
        );

        $result = $objQuery->getAll($sql);

        if (isset($result[0]['customer_id'])) {
            // 会員状態の確認
            if($result[0]['status'] == '2') {
                // 新しいパスワードを設定する
                $new_password = GC_Utils_Ex::gfMakePassword(8);
                // メールで変更通知をする
                $objDb = new SC_Helper_DB_Ex();
                $CONF = $objDb->sfGetBasisData();
                $this->lfSendMail($CONF, $arrForm['email'], $arrForm['name'], $new_password);

                $sqlval = array();
                $sqlval['password'] = $new_password;
                $sqlval['customer_id'] = $result[0]['customer_id'];

                // パスワード更新
                $this->lfUpdateCustomer($sqlval);

            } else if ($result[0]['status'] == '1') {
                // 仮会員
                $errmsg = 'ご入力のemailアドレスは現在仮登録中です。<br/>登録の際にお送りしたメールのURLにアクセスし、<br/>本会員登録をお願いします。';
            }
        } else {
            $errmsg = 'お名前に間違いがあるか、このメールアドレスは登録されていません。';
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
        $toCustomerMail = $objMailText->fetch("mail_templates/forgot_mail.tpl");
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
        return;
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
   ,update_date  = now()
   ,updator_id   = '{$customer_id}'
WHERE
    customer_id = '{$customer_id}'
__EOS;

        // 実行
        $objQuery->exec($sql);
    }

}
?>
