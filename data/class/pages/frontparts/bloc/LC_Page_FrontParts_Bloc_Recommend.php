<?php

// {{{ requires
require_once CLASS_REALDIR . 'pages/frontparts/bloc/LC_Page_FrontParts_Bloc.php';

/**
 * Recommend のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_FrontParts_Bloc_Recommend.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_FrontParts_Bloc_Recommend extends LC_Page_FrontParts_Bloc {

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

        // 基本情報を渡す
        $objSiteInfo = SC_Helper_DB_Ex::sfGetBasisData();
        $this->arrInfo = $objSiteInfo->data;

        //おすすめ商品表示
        $this->arrBestProducts = $this->lfGetRanking();
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
     * おすすめ商品検索.
     *
     * @return array $arrBestProducts 検索結果配列
     */
    function lfGetRanking(){
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objProduct = new SC_Product_Ex();

        // おすすめ商品取得
        $limit = RECOMMEND_NUM;
        $sql =<<<EOF
SELECT
    best_id,
    best_id,
    category_id,
    rank,
    product_id,
    title,
    comment,
    create_date,
    update_date
FROM
    dtb_best_products
WHERE
    del_flg = 0
ORDER BY rank
LIMIT $limit
EOF;
        $arrBestProducts = $objQuery->getAll($sql);

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        if (count($arrBestProducts) > 0) {
            // 商品一覧を取得
            // where条件生成&セット
            $arrBestProductIds = array();
            $where = 'product_id IN (';
            foreach ($arrBestProducts as $key => $val) {
                $arrBestProductIds[] = $val['product_id'];
            }
            $where .= implode(', ', $arrBestProductIds);
            $where .= ')';
            $objQuery->setWhere($where);
            // 取得
            $arrTmp = $objProduct->lists($objQuery);
            foreach ($arrTmp as $key => $arrRow) {
                $arrProductList[$arrRow['product_id']] = $arrRow;
            }
            // おすすめ商品情報にマージ
            foreach (array_keys($arrBestProducts) as $key) {
                $arrRow =& $arrBestProducts[$key];
                if (isset($arrProductList[$arrRow['product_id']])) {
                    $arrRow = array_merge($arrRow, $arrProductList[$arrRow['product_id']]);
                } else {
                    // 削除済み商品は除外
                    unset($arrBestProducts[$key]);
                }
            }
        }
        return $arrBestProducts;
    }
}
?>
