<?php

require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * 問い合わせ(完了ページ) のページクラス.
 */
class LC_Page_Contact_Complete extends LC_Page_Ex {
    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainno = 'contact';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrSubject = $masterData->getMasterData('mtb_contact_subject');
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
	$this->arrForm = $_SESSION['contact'];
	if (empty($_SESSION['contact'])) {
	    SC_Response_Ex::sendRedirect('index.php');
	    exit;
	}
	unset($_SESSION['contact']);
    }
}
?>
