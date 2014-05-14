<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * ログアウト のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_Logout.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Admin_Logout extends LC_Page_Admin_Ex {

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
        $this->action();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $this->lfDoLogout();
        // ログイン画面に遷移
        SC_Response_Ex::sendRedirectFromUrlPath(
            ADMIN_DIR . DIR_INDEX_PATH);
    }

    /**
     * ログアウト処理
     *
     * @return void
     */
    function lfDoLogout() {
        $objSess = new SC_Session_Ex();
        $objSess->logout();
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
