<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * カテゴリ一覧 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Products_CategoryList.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Products_CategoryList extends LC_Page_Ex {

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
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のAction
     * @return void
     */
    function action() {
        // カテゴリIDの正当性チェック
        $this->lfCheckCategoryId();

        // カテゴリー情報を取得する。
        $objFormParam = $this->lfInitParam($_REQUEST);
        $arrCategoryData = $this->lfGetCategories($objFormParam->getValue('category_id'), true, $this);
        $this->arrCategory = $arrCategoryData['arrCategory'];
        $this->arrChildren = $arrCategoryData['arrChildren'];
        $this->tpl_subtitle = $this->arrCategory['category_name'];
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /* カテゴリIDの正当性チェック */
    function lfCheckCategoryId($category_id) {
        if ($category_id && !SC_Helper_DB_Ex::sfIsRecord('dtb_category', 'category_id', (array)$category_id, 'del_flg = 0')){
            return 0;
        }
        return $category_id;
    }

    /**
     * 選択されたカテゴリーとその子カテゴリーの情報を取得し、
     * ページオブジェクトに格納する。
     *
     * @param string $category_id カテゴリーID
     * @param boolean $count_check 有効な商品がないカテゴリーを除くかどうか
     * @param object &$objPage ページオブジェクト
     * @return void
     */
    function lfGetCategories($category_id, $count_check = false, &$objPage) {
        // カテゴリーの正しいIDを取得する。
        $category_id = $this->lfCheckCategoryId($category_id);
        if ($category_id == 0) {
            SC_Utils_Ex::sfDispSiteError(CATEGORY_NOT_FOUND);
        }

        $arrCategory = null;    // 選択されたカテゴリー
        $arrChildren = array(); // 子カテゴリー

        $arrAll = SC_Helper_DB_Ex::sfGetCatTree($category_id, $count_check);
        foreach ($arrAll as $category) {
            // 選択されたカテゴリーの場合
            if ($category['category_id'] == $category_id) {
                $arrCategory = $category;
                continue;
            }

            // 関係のないカテゴリーはスキップする。
            if ($category['parent_category_id'] != $category_id) {
                continue;
            }

            // 子カテゴリーの場合は、孫カテゴリーが存在するかどうかを調べる。
            $arrGrandchildrenID = SC_Utils_Ex::sfGetUnderChildrenArray($arrAll, 'parent_category_id', 'category_id', $category['category_id']);
            $category['has_children'] = count($arrGrandchildrenID) > 0;
            $arrChildren[] = $category;
        }

        if (!isset($arrCategory)) {
            SC_Utils_Ex::sfDispSiteError(CATEGORY_NOT_FOUND);
        }

        // 子カテゴリーの商品数を合計する。
        $children_product_count = 0;
        foreach ($arrChildren as $category) {
            $children_product_count += $category['product_count'];
        }

        // 選択されたカテゴリーに直属の商品がある場合は、子カテゴリーの先頭に追加する。
        if ($arrCategory['product_count'] > $children_product_count) {
            $arrCategory['product_count'] -= $children_product_count; // 子カテゴリーの商品数を除く。
            $arrCategory['has_children'] = false; // 商品一覧ページに遷移させるため。
            array_unshift($arrChildren, $arrCategory);
        }

        return array('arrChildren'=>$arrChildren, 'arrCategory'=>$arrCategory);
    }

    /**
     * ユーザ入力値の処理
     *
     * @return object
     */
    function lfInitParam($arrRequest) {
        $objFormParam = new SC_FormParam_Ex();
        $objFormParam->addParam("カテゴリID", "category_id", INT_LEN, 'n', array('NUM_CHECK',"MAX_LENGTH_CHECK"));
        // 値の取得
        $objFormParam->setParam($arrRequest);
        // 入力値の変換
        $objFormParam->convParam();
        return $objFormParam;        
    }

}
?>
