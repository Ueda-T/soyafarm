<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * プレビュー のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Preview.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Preview extends LC_Page_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_SiteView_Ex();
        $objSess = new SC_Session_Ex();

        SC_Utils_Ex::sfIsSuccess($objSess);

        if (isset($_SESSION['preview']) && $_SESSION['preview'] === 'ON') {
            // プレビュー用のレイアウトデザインを取得
            $objLayout = new SC_Helper_PageLayout_Ex();
            $objLayout->sfGetPageLayout($this, true);

            // 画面の表示
            $objView->assignobj($this);
            $objView->display(SITE_FRAME);

            return;
        }
        SC_Utils_Ex::sfDispSiteError(PAGE_ERROR);
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
