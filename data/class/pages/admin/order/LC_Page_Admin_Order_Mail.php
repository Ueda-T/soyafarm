<?php
// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/order/LC_Page_Admin_Order_Ex.php';

/**
 * 受注メール管理 のページクラス.
 *
 * @package Page
 * @author IQUEVE CO.,LTD.
 * @version $Id: LC_Page_Admin_Order_Mail.php 112 2012-04-24 06:49:02Z hira $
 */
class LC_Page_Admin_Order_Mail extends LC_Page_Admin_Order_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'order/mail.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'index';
        $this->tpl_maintitle = '受注管理';
        $this->tpl_subtitle = '受注管理';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrMAILTEMPLATE = $masterData->getMasterData("mtb_mail_template");
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

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        // パラメーター管理クラス
        $objFormParam = new SC_FormParam_Ex();
        // パラメーター情報の初期化
        $this->lfInitParam($objFormParam);

        // 2011.04.27
        $objPurchase = new SC_Helper_Purchase_Ex();

        // POST値の取得
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();
        $this->tpl_order_id = $objFormParam->getValue('order_id');

        // 検索パラメーターの引き継ぎ
        $this->arrSearchHidden = $objFormParam->getSearchArray();
        switch($this->getMode()) {
            case 'pre_edit':
                break;
            case 'return':
                break;
            case 'send':
                $objFormParam->values["shipping_addr"] = $_SESSION["shipping_addr"];
                $sendStatus = $this->doSend($objFormParam);
                if($sendStatus === true){
                    SC_Response_Ex::sendRedirect(ADMIN_ORDER_URLPATH);
                    exit;
                }else{
                    $this->arrErr = $sendStatus;
                }
            case 'confirm':
                $objFormParam->getValue('shipping_addr');

                // 2011.04.27 お届け先選択値を取得
                $objFormParam->values["shipping_addr"] = $_POST["shipping_addr"];
                $_SESSION["shipping_addr"] = $_POST["shipping_addr"];

                $status = $this->confirm($objFormParam);
                
                if($status === true){
                    $this->arrHidden = $objFormParam->getHashArray();
                    return ;
                }else{
                    $this->arrErr = $status;
                }
                break;
            case 'change':
                $objFormParam =  $this->changeData($objFormParam);
                break;
        }

        if(SC_Utils_Ex::sfIsInt($objFormParam->getValue('order_id'))) {
            $this->arrMailHistory = $this->getMailHistory($objFormParam->getValue('order_id'));
        }

        $this->arrForm = $objFormParam->getFormParamList();
    }

    /**
     * 指定された注文番号のメール履歴を取得する。
     * @var int order_id
     */
    function getMailHistory($order_id){
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $col = "send_date, subject, template_id, send_id";
        $where = "order_id = ?";
        $objQuery->setOrder("send_date DESC");
        return $objQuery->select($col, "dtb_mail_history", $where, array($order_id));
    }

    /**
     *
     * メールを送る。
     * @param SC_FormParam $objFormParam
     */
    function doSend(&$objFormParam){
        $arrErr = $objFormParam->checkerror();

        // メールの送信
        if (count($arrErr) == 0) {
            // 注文受付メール
            $objMail = new SC_Helper_Mail_Ex();
            if($objFormParam->values["shipping_addr"] == ""){
                $objSendMail = $objMail->sfSendOrderMail(
                $objFormParam->getValue('order_id'),
                $objFormParam->getValue('template_id'),
                $objFormParam->getValue('subject'),
                $objFormParam->getValue('header'),
                $objFormParam->getValue('footer'),
                true,
                "");
                return true;
            } else {
                $objSendMail = $objMail->sfSendOrderMail(
                $objFormParam->getValue('order_id'),
                $objFormParam->getValue('template_id'),
                $objFormParam->getValue('subject'),
                $objFormParam->getValue('header'),
                $objFormParam->getValue('footer'),
                true,
                $objFormParam->values["shipping_addr"]);
                // TODO $SC_SendMail から送信がちゃんと出来たか確認できたら素敵。
                
                return true;
            }
        }
        return $arrErr;
    }

    /**
     * 確認画面を表示する為の準備
     * @param SC_FormParam $objFormParam
     */
    function confirm(&$objFormParam){
        $arrErr = $objFormParam->checkerror();
        
        // メールの送信
        if (count($arrErr) == 0) {
            // 注文受付メール(送信なし)
            $objMail = new SC_Helper_Mail_Ex();
            if($objFormParam->values["shipping_addr"] == ""){
                $objSendMail = $objMail->sfSendOrderMail(
                $objFormParam->getValue('order_id'),
                $objFormParam->getValue('template_id'),
                $objFormParam->getValue('subject'),
                $objFormParam->getValue('header'),
                $objFormParam->getValue('footer'),
                false,
                "");
            } else {
                $objSendMail = $objMail->sfSendOrderMail(
                $objFormParam->getValue('order_id'),
                $objFormParam->getValue('template_id'),
                $objFormParam->getValue('subject'),
                $objFormParam->getValue('header'),
                $objFormParam->getValue('footer'),
                false,
                $objFormParam->values["shipping_addr"]);
            }

            $this->tpl_subject = $objFormParam->getValue('subject');
            // エンコードが掛かっているならデフォルトへ戻す
            if (strcasecmp(MAIL_ENCODING, CHAR_CODE) != 0) {
                $this->tpl_body = mb_convert_encoding( $objSendMail->body, CHAR_CODE, MAIL_ENCODING);
            } else {
                $this->tpl_body = $objSendMail->body;
            }
            $this->tpl_to = $objSendMail->tpl_to;
            $this->tpl_mainpage = 'order/mail_confirm.tpl';
            
            return true;
        }
        return $arrErr;
    }

    /**
     *
     * テンプレートの文言をフォームに入れる。
     * @param SC_FormParam $objFormParam
     */
    function changeData(&$objFormParam){
        if(SC_Utils_Ex::sfIsInt($objFormParam->getValue('template_id'))) {
            $objQuery =& SC_Query_Ex::getSingletonInstance();
            $where = "template_id = ?";
            $mailTemplates = $objQuery->select("subject, header, footer", "dtb_mailtemplate", $where, array($objFormParam->getValue('template_id')));
            if(!is_null($mailTemplates )){
                foreach(array('subject','header','footer') as $key){
                    $objFormParam->setValue($key,$mailTemplates[$key]);
                }
            }
            $objFormParam->setParam($mailTemplates[0]);
        }else{
            foreach(array('subject','header','footer') as $key){
                $objFormParam->setValue($key,"");
            }
        }
        return $objFormParam;
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * パラメーター情報の初期化
     * @param SC_FormParam $objFormParam
     */
    function lfInitParam(&$objFormParam) {
        // 検索条件のパラメーターを初期化
        parent::lfInitParam($objFormParam);

        $objFormParam->addParam("オーダーID", "order_id", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("テンプレート", "template_id", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("メールタイトル", 'subject', STEXT_LEN, 'KVa',  array("EXIST_CHECK", "MAX_LENGTH_CHECK", "SPTAB_CHECK"));
        $objFormParam->addParam("ヘッダー", 'header', LTEXT_LEN, 'KVa', array("MAX_LENGTH_CHECK", "SPTAB_CHECK"));
        $objFormParam->addParam("フッター", 'footer', LTEXT_LEN, 'KVa', array("MAX_LENGTH_CHECK", "SPTAB_CHECK"));
    }
}
?>
