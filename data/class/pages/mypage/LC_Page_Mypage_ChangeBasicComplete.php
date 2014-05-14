<?php
require_once CLASS_EX_REALDIR . 'page_extends/mypage/LC_Page_AbstractMypage_Ex.php';

/*
*/
class LC_Page_Mypage_ChangeBasicComplete extends LC_Page_AbstractMypage_Ex {
    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_subtitle = 'メールアドレスとパスワードの変更';
        $this->tpl_mypageno = 'change_basic';
	$this->tpl_mainpage = 'mypage/change_basic_complete.tpl';
        $this->httpCacheControl('nocache');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        parent::process();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    function action() {
		$_SESSION["MYPAGENO"] = $this->tpl_mypageno;

        //セッション情報を最新の状態に更新する
        $objCustomer = new SC_Customer_Ex();
        $objCustomer->updateSession();
    }
}
?>
