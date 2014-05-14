<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * メールテンプレート設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_Mail_Template.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Admin_Mail_Template extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'mail/template.tpl';
        /* メニュー移動 (メルマガ管理 → 顧客関連)
        $this->tpl_mainno = 'mail';
         */
        $this->tpl_mainno   = 'customer';
        $this->tpl_subnavi  = 'customer/subnavi.tpl';
        $this->tpl_subno    = 'template';
        $this->tpl_maintitle = 'メルマガ管理';
        $this->tpl_subtitle = 'テンプレート設定';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrMagazineType =
            $masterData->getMasterData("mtb_magazine_type");
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
        case 'delete':
            if ( SC_Utils_Ex::sfIsInt($_GET['id'])===true ){
                $this->lfDeleteMailTemplate($_GET['id']);
                $this->objDisplay->reload(null, true);
            }
            break;
        default:
            break;
        }
        $this->arrTemplates = $objMailHelper->sfGetMailmagaTemplate();
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
     * メールテンプレートの削除
     * @param integer 削除したいテンプレートのID
     * @return void
     */
    function lfDeleteMailTemplate($template_id){
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->update("dtb_mailmaga_template",
                          array('del_flg' =>1),
                          "template_id = ?",
                          array($template_id));
    }

}
?>
