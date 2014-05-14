<?php

// {{{ requires
require_once CLASS_REALDIR . 'pages/mypage/LC_Page_Mypage.php';

/**
 * MyPage のページクラス(拡張).
 *
 * LC_Page_MyPage をカスタマイズする場合はこのクラスを編集する.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Mypage_Ex.php 100 2012-04-13 11:20:57Z takao $
 */
class LC_Page_Mypage_Ex extends LC_Page_Mypage {

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
