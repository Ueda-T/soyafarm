<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * よくあるご質問のページクラス.
 *
 * @package Page
 * @author 
 * @version $Id:LC_Page_Faq.php 15532 2011-09-01  $
 */
class LC_Page_Brand extends LC_Page_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_page_category = 'brand';
        $this->tpl_title = 'ブランド一覧';
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        parent::process();
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

	// CLICK ANALYZER用埋め込み
	$this->tpl_clickAnalyzer = "";
	if (isset($CLICK_ANALYZER_STATIC["brand"])) {
	    $this->tpl_clickAnalyzer = $CLICK_ANALYZER_STATIC["brand"];
	}

        $this->objSiteInfo->data = SC_Helper_DB_Ex::sfGetBasisData();
        $this->objSiteInfo->data['pref'] =
            isset($this->arrPref[$this->objSiteInfo->data['pref']])
            ? $this->arrPref[$this->objSiteInfo->data['pref']] : "";
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
