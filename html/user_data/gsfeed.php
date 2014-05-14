<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

// {{{ requires
require_once("../require.php");
require_once(CLASS_EX_REALDIR . "page_extends/LC_Page_Ex.php");
require_once(MODULE_REALDIR . "mdl_gshopping/include.php");
require_once(MODULE_REALDIR . "mdl_gshopping/class/LC_Page_Mdl_Gshopping_Config.php");

/**
 * RSS(商品) のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: gsfeed.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Gshopping_Products extends LC_Page_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = MODULE_REALDIR . MDL_GSHOPPING_CODE . "/templates/product.tpl";
        $this->title = "商品一覧情報";

        // デフォルトのソートするフィールド名
        $this->sortby = 'create_date';
        // デフォルトの昇順、降順を指定
        $this->direction = 'DESC';
        // 1ページに表示する件数
        $this->show = 10;
        // デフォルトのページ番号
        $this->page = null;

        // 指定できる条件
        $this->allow_order_field = array("product_id", "name", "price01_min", "create_date", "update_date");
        $this->show_limit = 300;
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->arrInfo = SC_Helper_DB_Ex::sfGetBasisData();
        $this->arrConfig = LC_Page_Mdl_Gshopping_Config::getConfig();
        if( $this->arrConfig['work_flg'] == 1 ){
            $this->setConditions();
            $this->arrProduct = $this->lfGetProductsAllclass();
            $objView = new SC_SiteView_Ex(false);
            $objView->assignobj($this);
            header("Content-type: application/xml");
            $objView->display($this->tpl_mainpage);
        }
    }

    function setConditions(){

        // 1ページの件数
        if ( is_numeric($_GET['show']) && $_GET['show'] <= $this->show_limit ){
            $this->show = intval($_GET['show']);
        }

        // ページ番号
        if ( is_numeric($_GET['page'])){
            $this->page = intval($_GET['page']);
        } else {
            $this->page = null;
        }

        // 並べ替えのフィールド
        if( in_array(strtolower($_GET["sortby"]), $this->allow_order_field) ){
            $this->sortby = $_GET["sortby"];
        }

        // 並べ替え順序
        if( in_array(strtoupper($_GET["direction"]), array("DESC", "ASC")) ){
            $this->direction = $_GET["direction"];
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
     * 商品情報を取得する.
     *
     * @return array $arrProduct 取得結果を配列で返す
     */
    function lfGetProductsAllclass() {
        $sql_category["pgsql"] = ", ARRAY_TO_STRING(ARRAY(SELECT category_name FROM dtb_product_categories JOIN dtb_category USING(category_id) WHERE A.product_id = product_id), ',') as category_name";
        $sql_category["mysql"] = ",(SELECT GROUP_CONCAT(category_name SEPARATOR ',') FROM dtb_product_categories JOIN dtb_category ON dtb_product_categories.category_id = dtb_category.category_id WHERE A.product_id = product_id) as category_name";

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $col = <<< __EOS__
            product_id
            ,name
            ,comment3
            ,main_list_comment
            ,main_list_image
            ,del_flg
            ,create_date
            ,update_date
            ,(SELECT min(price01) FROM dtb_products_class WHERE A.product_id = product_id) AS price01_min
            ,(SELECT max(price01) FROM dtb_products_class WHERE A.product_id = product_id) AS price01_max
            ,(SELECT max(stock) FROM dtb_products_class WHERE A.product_id = product_id) AS stock
__EOS__;
        $col .= $sql_category[DB_TYPE];
        $from = 'dtb_products AS A';
        $objQuery->setWhere('del_flg = 0 AND status = 1');
        $objQuery->setOrder($this->sortby . " " . $this->direction);
        if (!is_null($this->page)) {
            $offset = ($this->page - 1) * $this->show;
            $objQuery->setLimitOffset($this->show, ($offset < 0) ? 0 : $offset);
        }
        return $objQuery->select($col, $from, $where);
    }
}


// }}}
// {{{ generate page

$objPage = new LC_Page_Gshopping_Products();
register_shutdown_function(array($objPage, "destroy"));
$objPage->init();
$objPage->process();
