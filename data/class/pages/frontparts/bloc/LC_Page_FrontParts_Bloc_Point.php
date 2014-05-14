<?php

// {{{ requires
require_once CLASS_REALDIR . 'pages/frontparts/bloc/LC_Page_FrontParts_Bloc.php';

/**
 * カート のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_FrontParts_Bloc_Point.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_FrontParts_Bloc_Point extends LC_Page_FrontParts_Bloc {

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
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $objCustomer = new SC_Customer_Ex();

        $this->tpl_login     = false;

        // ログインしている時は、ポイント情報をセット
        if($objCustomer->isLoginSuccess(true)) {
            $this->tpl_login     = true;
            $this->CustomerName = $objCustomer->getvalue('name');
            $this->CustomerPoint = $objCustomer->getvalue('point');
            $this->CustomerPointValidDate = $objCustomer->getvalue('point_valid_date');
            $this->CustomerBirthPoint = $objCustomer->getvalue('birth_point');
            $this->CustomerBirthPointValidDate = $objCustomer->getvalue('birth_point_valid_date');
            $this->CustomerId = $objCustomer->getvalue('customer_id');
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
}
?>
