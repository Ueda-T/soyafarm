<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * アプリケーション管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_OwnersStore.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Admin_OwnersStore extends LC_Page_Admin_Ex {

    var $tpl_subno = 'index';
    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();

        $this->tpl_mainpage = 'ownersstore/index.tpl';
        $this->tpl_subnavi  = 'ownersstore/subnavi.tpl';
        $this->tpl_mainno   = 'ownersstore';
        $this->tpl_subno    = 'index';
        $this->tpl_maintitle = 'オーナーズストア';
        $this->tpl_subtitle = '購入商品一覧';
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
        // nothing.
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
