<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * 商品番号入力 のページクラス.
 *
 * @package Page
 * @author IQUEVE Co.,Ltd.
 * @version $Id: LC_Page_InputProduct.php 224 2013-12-25 05:44:01Z taizo $
 */
class LC_Page_InputProduct extends LC_Page_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_message = "商品を検索しています。";
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_SiteView_Ex(false);

        // 商品検索文作成
        $product_name = $_GET['search_product_name'];

        // 商品検索
        $arrProductsList = $this->getProducts($product_name);

        if (!empty($arrProductsList)) {
            $html = "";

            for ($i=0;$i<count($arrProductsList);$i++) {
                $row_num = $i+1;

                // テーブルhtml文生成
                $html .= "<tr>";
                $html .= "<td><input type='checkbox' id='chk_".$row_num."' value=''></td>";
                $html .= "<td>".$row_num."</td>";
                $html .= "<td>".$arrProductsList[$i]['product_id']."</td>";
                $html .= "<td>".$arrProductsList[$i]['name']."</td>";
                $html .= "<td><input type='button' onclick=selAdd('";
                    $html .= $arrProductsList[$i]['product_id']."','";
                    $html .= $arrProductsList[$i]['name']."'); value='選択' /></td>";
                $html .= "</tr>";
            }

        // 該当無し
        } else {
            echo "";
        }

        if ($html != "") {
            echo $html;
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
     * エラーチェック.
     *
     * @param string $value
     * @return エラーなし：true エラー：false
     */
    function lfInputNameCheck($value) {
        // 半角英数字と_（アンダーバー）, []以外の文字を使用していたらエラー
        if(strlen($value) > 0 && !preg_match("/^[a-zA-Z0-9_\[\]]+$/", $value)) {
            return false;
        }

        return true;
    }

    /**
     * 商品データ取得.
     *
     * @return void
     */
    function getProducts($product_name) {
        // 初期化
        $where = '';
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // 条件文
        $where = 'del_flg = ?';
        $arrValues[] = 0;
        //product_name指定時
        if ($product_name != "") {
            $where .= ' AND name LIKE ?';
            $arrValues[] = sprintf('%%%s%%', $product_name);
        }

        // 表示順
        $objQuery->setOrder("product_id ASC");

        $arrResults = $objQuery->select('*', 'dtb_products', $where, $arrValues);
        return $arrResults;
    }
}
?>
