<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * おすすめ商品管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_Contents_Recommend.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Admin_Contents_Recommend extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'contents/recommend.tpl';
        $this->tpl_mainno   = 'products';
        $this->tpl_subnavi  = 'products/subnavi.tpl';
        $this->tpl_subno    = 'recommend';
        $this->tpl_maintitle = 'コンテンツ管理';
        $this->tpl_subtitle = 'おすすめ商品管理';
        //最大登録数の表示
        $this->tpl_disp_max = RECOMMEND_NUM;
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
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();

        switch ($this->getMode()) {
        case 'regist': // 商品を登録する。
            $this->arrErr = $this->lfCheckError($objFormParam);
            $arrPost = $objFormParam->getHashArray();
            // 登録処理にエラーがあった場合は商品選択の時と同じ処理を行う。
            if (SC_Utils_Ex::isBlank($this->arrErr)) {
                $member_id = $_SESSION['member_id'];
                $this->insertRecommendProduct($arrPost,$member_id);
                $arrItems = $this->getRecommendProducts();
            }else{
                $arrItems = $this->setProducts($arrPost, $arrItems);
                $this->checkRank = $arrPost['rank'];
            }
            $this->tpl_onload = "window.alert('編集が完了しました');";
            break;
        case 'delete': // 商品を削除する。
            $this->arrErr = $this->lfCheckError($objFormParam);
            $arrPost = $objFormParam->getHashArray();
            if (SC_Utils_Ex::isBlank($this->arrErr)) {
                $this->deleteProduct($arrPost);
                $arrItems = $this->getRecommendProducts();
            }
            $this->tpl_onload = "window.alert('削除しました');";
            break;
        case 'set_item': // 商品を選択する。
            $this->arrErr = $this->lfCheckError($objFormParam);
            $arrPost = $objFormParam->getHashArray();
            if (SC_Utils_Ex::isBlank($this->arrErr['rank']) 
                && SC_Utils_Ex::isBlank($this->arrErr['product_id'])) {

                $arrItems =
                    $this->setProducts($arrPost, $this->getRecommendProducts());
                $this->checkRank = $arrPost['rank'];
            }
            break;
        default:
            $arrItems = $this->getRecommendProducts();
            break;
        }

        $this->category_id = intval($arrPost['category_id']);
        $this->arrItems = $arrItems;

        // カテゴリ取得
        $objDb = new SC_Helper_DB_Ex();
        $this->arrCatList = $objDb->sfGetCategoryList("level = 1");

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
     * パラメーターの初期化を行う
     * @param Object $objFormParam
     */
    function lfInitParam(&$objFormParam){
        $objFormParam->addParam("商品ID", "product_id", INT_LEN, 'n', array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("カテゴリID", "category_id", INT_LEN, 'n', array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("ランク", 'rank', INT_LEN, 'n', array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("コメント", 'comment', LTEXT_LEN, 'KVa', array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
    }

    /**
     * 入力されたパラメーターのエラーチェックを行う。
     * @param Object $objFormParam
     * @return Array エラー内容
     */
    function lfCheckError(&$objFormParam){
        $objErr = new SC_CheckError_Ex($objFormParam->getHashArray());
        $objErr->arrErr = $objFormParam->checkError();
        return $objErr->arrErr;
    }

    /**
     * 既に登録されている内容を取得する
     * @return Array $arrReturnProducts データベースに登録されているおすすめ商品の配列
     */
    function getRecommendProducts(){
        $objQuery = $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<EOF
SELECT
    dtb_products.name,
    dtb_products.main_list_image,
    dtb_best_products.*
FROM
    dtb_best_products
    INNER JOIN dtb_products USING (product_id)
WHERE
    dtb_best_products.del_flg = 0
ORDER BY rank
EOF;
        $arrProducts = $objQuery->getAll($sql);

        $arrReturnProducts = array();
        foreach( $arrProducts as $data ){
            $arrReturnProducts[$data['rank']] = $data;
        }
        return $arrReturnProducts;
    }

    /**
     * おすすめ商品の新規登録を行う。
     * @param Array $arrPost POSTの値を格納した配列
     * @param Integer $member_id 登録した管理者を示すID
     */
    function insertRecommendProduct($arrPost,$member_id){
        $objQuery = $objQuery =& SC_Query_Ex::getSingletonInstance();
        // 古いおすすめ商品のデータを削除する。
        $this->deleteProduct($arrPost);

        $sqlval = array();
        $sqlval['product_id'] = $arrPost['product_id'];
        $sqlval['category_id'] = $arrPost['category_id'];
        $sqlval['rank'] = $arrPost['rank'];
        $sqlval['comment'] = $arrPost['comment'];
        $sqlval['creator_id'] = $member_id;
        $sqlval['create_date'] = "NOW()";
        $sqlval['update_date'] = "NOW()";
        $sqlval['best_id'] = $objQuery->nextVal('dtb_best_products_best_id');
        $objQuery->insert("dtb_best_products", $sqlval);
    }

    /**
     * データを削除する
     * @param Array $arrPost POSTの値を格納した配列
     */
    function deleteProduct($arrPost){
        $objQuery = $objQuery =& SC_Query_Ex::getSingletonInstance();

        $rank = $arrPost['rank'];
        $category_id = $arrPost['category_id'];

        $sql =<<<EOF
DELETE
FROM
    dtb_best_products
WHERE
    category_id = $category_id
    AND rank = $rank
EOF;

        $objQuery->query($sql);
    }

    /**
     * 商品情報を取得する
     * @param Integer $product_id 商品ID
     * @return Array $arrProduct 商品のデータを格納した配列
     */
    function getProduct($product_id){
        $objQuery = $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<EOF
SELECT
    product_id,
    main_list_image,
    name
FROM
    dtb_products
WHERE
    product_id = $product_id
    AND del_flg = 0
EOF;

        list($arrProduct) = $objQuery->getAll($sql);

        return $arrProduct;
    }

    /**
     * 商品のデータを表示用に処理する
     * @param Array $arrPost POSTのデータを格納した配列
     * @param Array $arrItems フロントに表示される商品の情報を格納した配列
     */
    function setProducts($arrPost,$arrItems){
        $arrProduct = $this->getProduct($arrPost['product_id']);
        if (count($arrProduct) > 0) {
            $rank = $arrPost['rank'];
            foreach( $arrProduct as $key => $val){
                $arrItems[$rank][$key] = $val;
            }
            $arrItems[$rank]['rank'] = $rank;
        }
        return $arrItems;
    }

}
?>
