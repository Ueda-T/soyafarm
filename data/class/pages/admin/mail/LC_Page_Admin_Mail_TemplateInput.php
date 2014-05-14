<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * テンプレート設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_Mail_TemplateInput.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Admin_Mail_TemplateInput extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'mail/template_input.tpl';
        /* メニュー移動 (メルマガ管理 → 顧客関連)
        $this->tpl_mainno = 'mail';
        $this->tpl_maintitle = 'メルマガ管理';
         */
        $this->tpl_mainno   = 'customer';
        $this->tpl_subnavi  = 'customer/subnavi.tpl';
        $this->tpl_subtitle = 'テンプレート設定';
        $this->tpl_subno = 'template';
        $this->mode = 'regist';
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrMagazineType = $masterData->getMasterData("mtb_magazine_type");
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
        case 'edit':
            // 編集
            if ( SC_Utils_Ex::sfIsInt($_GET['template_id'])===true ){
                $arrMail = $objMailHelper->sfGetMailmagaTemplate($_GET['template_id']);
                $this->arrForm = $arrMail[0];
            }
            break;
        case 'regist':
            // 新規登録
            $objFormParam = new SC_FormParam_Ex();

            $this->lfInitParam($objFormParam);
            $objFormParam->setParam($_POST);
            $this->arrErr = $objFormParam->checkError();
            $this->arrForm = $objFormParam->getHashArray();

            if (SC_Utils_Ex::isBlank($this->arrErr)) {
                // エラーが無いときは登録・編集
                $this->lfRegistData( $objFormParam, $objFormParam->getValue('template_id'));
                // 自分を再読込して、完了画面へ遷移
                $this->objDisplay->reload(array('mode' => 'complete'));
            } else {
                $this->arrForm['template_id'] = $objFormParam->getValue('template_id');
            }
            break;
        case 'complete':
            // 完了画面表示
            $this->tpl_mainpage = 'mail/template_complete.tpl';
            break;
        default:
            break;
        }
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
     * メルマガテンプレートデータの登録・更新を行う
     * 
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param integer template_id 更新時は指定
     * @return void
     */
    function lfRegistData( &$objFormParam, $template_id = null ){

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sqlval = $objFormParam->getDbArray();

        $sqlval['creator_id'] = $_SESSION['member_id'];
        $sqlval['update_date'] = "now()";

        if ( SC_Utils_Ex::sfIsInt($template_id) ){
            // 更新時
            $objQuery->update("dtb_mailmaga_template",
                              $sqlval,
                              "template_id = ?",
                              array($template_id));
        } else {
            // 新規登録時
            $sqlval['create_date'] = "now()";
            $sqlval['template_id'] = $objQuery->nextVal('dtb_mailmaga_template_template_id');
            $objQuery->insert("dtb_mailmaga_template", $sqlval);
        }
    }

    /**
     * お問い合わせ入力時のパラメーター情報の初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam("メール形式", 'mail_method', INT_LEN, 'n', array("EXIST_CHECK","ALNUM_CHECK"));
        $objFormParam->addParam('Subject', 'subject', STEXT_LEN, 'KVa', array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam("本文", 'body', LLTEXT_LEN, 'KVCa', array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam("テンプレートID", "template_id", INT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"), "", false);
    }

}
?>
