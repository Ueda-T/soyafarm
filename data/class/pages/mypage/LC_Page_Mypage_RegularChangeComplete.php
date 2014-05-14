<?php
require_once CLASS_EX_REALDIR . 'page_extends/mypage/LC_Page_AbstractMypage_Ex.php';

/*
*/
class LC_Page_Mypage_RegularChangeComplete extends LC_Page_AbstractMypage_Ex {
    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mypageno = 'regular';
        if (SC_Display_Ex::detectDevice() === DEVICE_TYPE_MOBILE){
            $this->tpl_subtitle = 'MYページ';
        } else {
            $this->tpl_subtitle = '定期購入一覧詳細';
        }
	$this->tpl_mainpage = 'mypage/regular_change_complete.tpl';
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
    }
}
?>
