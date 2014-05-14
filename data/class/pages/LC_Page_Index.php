<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * Index のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Index.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Index extends LC_Page_Ex {

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
        $this->tpl_title = '';

	// キャンペーンコードが指定されている場合はセッションに保持
	if ($_GET[CAMPAIGN_PARAM_STR]) {
	    $_SESSION["CAMPAIGN_CODE"] = $_GET[CAMPAIGN_PARAM_STR];
    }

    // タグセット
    $this->lfSetTag($this);

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
     * 各種計測タグをセット
     *
     */
    function lfSetTag(&$thisPage) {

        $objTagView = new SC_SiteView_Ex();

        // タグテンプレートパス取得
        $tag_tpl_dir = "";

        // 端末判定
        $device = SC_Display_Ex::detectDevice();
        switch ($device){
        case DEVICE_TYPE_SMARTPHONE:
                // スマートフォン
                $tag_tpl_dir = SMARTPHONE_TAG_TEMPLATE_REALDIR;
                break;
            case DEVICE_TYPE_PC:
                // PC 
                $tag_tpl_dir = TAG_TEMPLATE_REALDIR;
                break;
        }

        // 各種タグテンプレートをセット
        if (!empty($tag_tpl_dir)) {
            // BLADEタグ
            $thisPage->tpl_tag_blade =
                $objTagView->fetch($tag_tpl_dir . "blade.tpl");
            // MarketOneタグ
            $thisPage->tpl_tag_market_one =
                $objTagView->fetch($tag_tpl_dir . "market_one.tpl");
        }
    }
}
?>
