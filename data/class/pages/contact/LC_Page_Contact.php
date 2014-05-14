<?php
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * お問い合わせ のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Contact.php 124 2012-06-11 10:18:16Z takao $
 */
class LC_Page_Contact extends LC_Page_Ex {
    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();

        $this->tpl_page_category = 'contact';
        $this->httpCacheControl('nocache');

        $masterData = new SC_DB_MasterData_Ex();
        // 「件名」選択肢
        $this->arrSubject =
            $masterData->getMasterData('mtb_contact_subject');
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
	global $CLICK_ANALYZER_STATIC;

	$this->httpCacheControl('nocache');

	// CLICK ANALYZER用埋め込み
	$this->tpl_clickAnalyzer = "";
	if (isset($CLICK_ANALYZER_STATIC["contact"])) {
	    $this->tpl_clickAnalyzer = $CLICK_ANALYZER_STATIC["contact"];
	}

        $objDb = new SC_Helper_DB_Ex();
        $objFormParam = new SC_FormParam_Ex();
	$this->lfInitParam($objFormParam);
	$objFormParam->setParam($_POST);

        $this->arrData = isset($_SESSION['customer']) ? $_SESSION['customer'] : "";

        // 表示用氏名
        $this->tpl_customer_name = $this->arrData['name'];

        switch ($this->getMode()) {
        case 'confirm':
	    if (!isset($_SESSION['contact'])) {
		SC_Response_Ex::sendRedirect('index.php');
		exit;
	    }

            // エラーチェック
            $objFormParam->convParam();
	    $this->arrErr = $this->lfCheckError($objFormParam);
            $this->arrForm = $objFormParam->getFormParamList();
            if (SC_Utils_Ex::isBlank($this->arrErr)) {
                $this->tpl_mainpage = 'contact/confirm.tpl';
		// 
		$this->token = sha1(uniqid(mt_rand(), true));
		$_SESSION['token'][] = $this->token;
            }
            break;

        case 'complete':
            $this->arrErr = $objFormParam->checkError();
            $this->arrForm = $objFormParam->getFormParamList();

	    $key = array_search($_POST['token'], $_SESSION['token']);
            if (SC_Utils_Ex::isBlank($this->arrErr) && $key !== false) {
                $this->lfSendMail($this);
		// 使用済みトークンの破棄
		unset($_SESSION['token'][$key]);
                // 完了ページへ移動する
		$_SESSION['contact'] = $this->arrForm;
                SC_Response_Ex::sendRedirect('complete.php');
                exit;
            } else {
                SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
                exit;
            }
            break;

        case 'return':
            $this->arrForm = $objFormParam->getFormParamList();
            break;

        default:
	    $_SESSION['contact'] = array();
            break;
        }
    }

    /**
     * お問い合わせ入力時のパラメーター情報の初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {

        $objFormParam->addParam("件名", 'subject', INT_LEN, 'n', array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("お名前", 'name', STEXT_LEN*2, 'KVa', array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('メールアドレス', 'email', null, 'KVa',array("EXIST_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK"));
        $objFormParam->addParam("内容", 'contents', MLTEXT_LEN, 'KVa', array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("電話番号", 'tel', TEL_ITEM_LEN*3, 'n', array("EXIST_CHECK", "IS_TELEPHONE", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("現在時刻", 'now');
    }

    /**
     * 入力内容のチェックを行なう.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return array 入力チェック結果の配列
     */
    function lfCheckError(&$objFormParam) {
        // 入力データを渡す。
        $arrForm =  $objFormParam->getHashArray();
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->arrErr = $objFormParam->checkError();
        return $objErr->arrErr;
    }

    /**
     * メールの送信を行う。
     *
     * @param $objPage
     * @return void
     */
    function lfSendMail(&$objPage){
        $CONF = SC_Helper_DB_Ex::sfGetBasisData();
        $objPage->tpl_shopname = MAIL_TITLE_SHOP_NAME;
        $objPage->tpl_infoemail = $CONF['email02'];

        $fromMail_name = $objPage->arrForm['name']['value'] ." 様";
        $fromMail_address = $objPage->arrForm['email']['value'];
        $helperMail = new SC_Helper_Mail_Ex();

        $helperMail->sfSendTemplateMail(
            $objPage->arrForm['email']['value'],            // to
            $objPage->arrForm['name']['value'] .' 様',    // to_name
            5,                                              // template_id
            $objPage,                                       // objPage
            $CONF['email03'],                               // from_address
            MAIL_TITLE_SHOP_NAME,                             // from_name
            $CONF['email02'],                               // reply_to
            $CONF['email02']                                // bcc
        );
    }
}
?>
