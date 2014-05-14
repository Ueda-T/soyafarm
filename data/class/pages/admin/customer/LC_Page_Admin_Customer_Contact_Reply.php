<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * お問い合わせ返信のページクラス.
 */
class LC_Page_Admin_Customer_Contact_Reply extends LC_Page_Admin_Ex {

	// }}}
	// {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'customer/contact_reply.tpl';
        $this->tpl_mainno = 'customer';
        $this->tpl_subnavi = 'customer/subnavi.tpl';
        $this->tpl_subno = 'contact_reply';
        $this->tpl_subtitle = 'お問い合わせ返信';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrMailTemplate = $masterData->getMasterData("mtb_mail_template");
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

        // 認証判定
        $objSess = new SC_Session();
        SC_Utils_Ex::sfIsSuccess($objSess);

        // contact_idの取得
        //GET値に"contact_id"があり、数字であるならば
        if(isset($_GET['contact_id']) && SC_Utils_Ex::sfIsInt($_GET['contact_id'])) {
            //tpl用に値を格納
            $this->contact_id = $_GET['contact_id'];
        } elseif(isset($_POST['contact_id']) && SC_Utils_Ex::sfIsInt($_POST['contact_id'])) {
            //POST値に"contact_id"があり、数字であるならば
            //tpl用に値を格納
            $this->contact_id = $_POST['contact_id'];
        }

        $objQuery = new SC_Query();

        // 取得した"contact_id"と一致するお問合せ内容の取得
        $this->contact_data = $objQuery->select("*", "dtb_contact", "contact_id=?", array($this->contact_id));

        // modeの判定
        if(isset($_POST['mode'])) {
            //モードが"send"ならば
            if($_POST['mode']=="send") {
                //POST値を格納
                $this->arrForm = $_POST;

                // 入力値のチェック
                // 文字列変換
                /*
                 *  文字列の変換
                 *  K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
                 *  C :  「全角ひら仮名」を「全角かた仮名」に変換
                 *  V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します
                 *  n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
                 *  a :  「全角」英数字を「半角」に
                 */
                //入力の変換方法を指定
                $arrConvList['contact_id'] = "n";
                $arrConvList['title']      = "KV";
                $arrConvList['content']    = "KV";
                foreach ($arrConvList as $key => $val) {
                    //指定した方法で文字列を変換
                    $this->arrForm[$key] = mb_convert_kana($this->arrForm[$key] ,$val);
                }
                // 値チェック
                // 値が存在しているか、最大文字数を超えていないかチェック
                $objErr = new SC_CheckError($this->arrForm);
                $objErr->doFunc(array("タイトル", 'title', STEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
                $objErr->doFunc(array("本文", 'content', LTEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
                $this->arrErr = $objErr->arrErr;
                if(!$this->arrErr) {
                    //エラーがないならば
                    // メール送信
                    $objDB = new SC_Helper_DB();
                    // $objSiteInfo["email01"]:"商品注文受付メールアドレス"
                    // $objSiteInfo["email02"]:"問い合わせ受付メールアドレス"
                    // $objSiteInfo["email03"]:"メール送信元メールアドレス"
                    // $objSiteInfo["email04"]:"送信エラー受付メールアドレス"
                    //店舗基本情報を取得
                    $arrInfo = $objDB->sfGetBasisData();
                    $objSendMail = new SC_SendMail_Ex();
                    //送信メールの準備
                    $to          = $this->contact_data[0]['email'];//送信相手のアドレス
                    $subject     = $_POST['title'];//タイトル
                    $body        = $_POST['content'];//本文
                    $fromaddress = $arrInfo['email03'];//送信者のアドレス
                    $from_name   = $arrInfo['shop_name'];//店名
                    $reply_to    = $arrInfo['email02'];//送信メールの返信先のアドレス
                    $return_path = $arrInfo['email04'];//エラーメールの送信先のアドレス
                    $errors_to   = $arrInfo['email04'];//エラーメールの送信先のアドレス
                    $bcc         = $arrInfo['email02'];//bccの送信アドレス
                    //値をセット
                    $objSendMail->setItem($to, $subject, $body, $fromaddress, $from_name, $reply_to, $return_path, $errors_to, $bcc);//
                    //宛先をセット
                    $objSendMail->setTo($to, $this->contact_data[0]["name01"] . $this->contact_data[0]["name02"] . " 様");//
                    //メールを送信
                    $objSendMail->sendMail();//

                    // データベースにメールを登録するための配列
                    $sqlval['contact_id']  = $this->contact_id;//返信元のメールID
                    $sqlval['email']       = $this->contact_data[0]['email'];//送信相手のアドレス
                    $sqlval['title']       = $_POST['title'];//タイトル
                    $sqlval['content']     = $_POST['content'];//本文
                    $sqlval['create_date'] = 'NOW()';//送信日時
                    $objQuery->begin();
                    $objQuery->insert("dtb_contact_reply", $sqlval);//メールの登録
                    $objQuery->commit();
                }
            } elseif($_POST['mode']=="template" && SC_Utils_Ex::sfIsInt($_POST['template_id'])) {
                //"mode"変数が"template"であり、"template_id"が数字ならば
                $this->mailTemplateId = $_POST['template_id'];
            }
        }

        // メールテンプレート内容取得
        $objQuery->setOrder();
        //"template_id"と一致するメールテンプレートを取得
        $this->mail_template = $objQuery->select("subject, header, footer", "dtb_mailtemplate", "template_id=?", array($this->mailTemplateId));

        // GETから（初めてのページ表示時）またはテンプレート変更時は$arrFormと$arrErrを用意
        if(isset($_GET['contact_id']) || $_POST['mode']=="template") {
            $this->arrForm['title']   = $this->mail_template[0]['subject'];//タイトル
            $this->arrForm['content'] = $this->contact_data[0]['name01'] . $this->contact_data[0]['name01'] . '様' . $this->mail_template[0]['header'] . '' . $this->mail_template[0]['footer'];//本文
            $this->arrErr['title'] = '';//エラーなし
            $this->arrErr['content'] = '';//エラーなし
        }

        // 返信一覧の取得
        //送信日時が近い順
        $objQuery->setorder("create_date DESC");
        //"contact_id"と一致する返信メールを取得
        $this->arrReply = $objQuery->select("*", "dtb_contact_reply", "contact_id = ?", array($this->contact_id));
        //返信メールの数を取得
        $this->replyCount = $objQuery->count("dtb_contact_reply","contact_id = ?",array($this->contact_id));
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

}
?>

