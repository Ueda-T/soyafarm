<?php

  // {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * メルマガプレビュー のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_Mail_Preview.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Admin_Mail_Preview extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'mail/preview.tpl';
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

        $objMailHelper = new SC_Helper_Mail_Ex();

        switch ($this->getMode()) {
        case 'template':
            if (SC_Utils_Ex::sfIsInt($_GET['template_id'])){
                $arrMail = $objMailHelper->sfGetMailmagaTemplate($_GET['template_id']);
                $this->mail = $arrMail[0];
            }
            break;
        case 'history';
            if (SC_Utils_Ex::sfIsInt($_GET['send_id'])){
                $arrMail = $objMailHelper->sfGetSendHistory($_GET['send_id']);
                $this->mail = $arrMail[0];
            }
            break;
        case 'presend';
            $this->mail['body'] = $_POST['body'];
        default:
        }

        $this->setTemplate($this->tpl_mainpage);
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
