<?php

// {{{ requires
//require_once CLASS_EX_REALDIR . 'page_extends/admin/products/LC_Page_Admin_Products_Ex.php';
require_once CLASS_EX_REALDIR . 'page_extends/admin/products/LC_Page_Admin_Products_Product_Ex.php';
/**
 * 簡易商品登録 のページクラス
 *
 * @package Page
 * @author  IQUEVE
 */
class LC_Page_Admin_Products_ProductLight extends LC_Page_Admin_Products_Product_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/product_light.tpl';
        $this->tpl_subnavi = 'products/subnavi.tpl';
        $this->tpl_complete = 'products/complete_light.tpl';
        $this->tpl_confirm = 'products/confirm_light.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'product_light';
        $this->tpl_subtitle = '簡易商品登録';
    }

}
?>
