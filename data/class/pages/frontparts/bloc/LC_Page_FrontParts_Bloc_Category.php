<?php

// {{{ requires
require_once CLASS_REALDIR . 'pages/frontparts/bloc/LC_Page_FrontParts_Bloc.php';

/**
 * カテゴリ のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_FrontParts_Bloc_Category.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_FrontParts_Bloc_Category extends LC_Page_FrontParts_Bloc {

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
        // モバイル判定
        switch ( SC_Display_Ex::detectDevice() ) {
            case DEVICE_TYPE_MOBILE:
                // メインカテゴリーの取得
                $this->arrCat = $this->lfGetMainCat(true);
                break;
            default:
                // 選択中のカテゴリID
                $this->tpl_category_id = $this->lfGetSelectedCategoryId($_GET);
                // カテゴリツリーの取得
                $this->arrTree = $this->lfGetCatTree($this->tpl_category_id, true);
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
     * 選択中のカテゴリIDを取得する.
     *
     * @param array $arrRequest リクエスト配列
     * @return array $arrCategoryId 選択中のカテゴリID
     */
    function lfGetSelectedCategoryId($arrRequest) {
            // 商品ID取得
        $product_id = '';
        if ( isset($arrRequest['product_id']) && $arrRequest['product_id'] != '' && is_numeric($arrRequest['product_id']) ) {
            $product_id = $arrRequest['product_id'];
        }
        // カテゴリID取得
        $category_id = '';
        if ( isset($arrRequest['category_id']) && $arrRequest['category_id'] != '' && is_numeric($arrRequest['category_id']) ) {
            $category_id = $arrRequest['category_id'];
        }
        // 選択中のカテゴリIDを判定する
        $objDb = new SC_Helper_DB_Ex();
        $arrCategoryId = $objDb->sfGetCategoryId($product_id, $category_id);
        if (empty($arrCategoryId)) {
            $arrCategoryId = array(0);
        }
        return $arrCategoryId;
    }

    /**
     * カテゴリツリーの取得.
     *
     * @param array $arrParentCategoryId 親カテゴリの配列
     * @param boolean $count_check 登録商品数をチェックする場合はtrue
     * @return array $arrRet カテゴリーツリーの配列を返す
     */
    function lfGetCatTree($arrParentCategoryId, $count_check = false) {
        $objQuery = new SC_Query_Ex();
        $objDb = new SC_Helper_DB_Ex();

        // 登録商品数のチェック
        if($count_check) {
            $where = 'del_flg = 0 AND product_count > 0';
        } else {
            $where = 'del_flg = 0';
        }

        $sql =<<<EOF
SELECT
    *
FROM
    dtb_category
    left join dtb_category_total_count using (category_id)
WHERE
    $where
ORDER BY rank DESC
EOF;
        $arrRet = $objQuery->getAll($sql);

        foreach ($arrParentCategoryId as $category_id) {
            $arrParentID = $objDb->sfGetParents(
                'dtb_category',
                'parent_category_id',
                'category_id',
                $category_id
            );
            $arrBrothersID = SC_Utils_Ex::sfGetBrothersArray(
                $arrRet,
                'parent_category_id',
                'category_id',
                $arrParentID
            );
            $arrChildrenID = SC_Utils_Ex::sfGetUnderChildrenArray(
                $arrRet,
                'parent_category_id',
                'category_id',
                $category_id
            );
            $this->root_parent_id[] = $arrParentID[0];
            $arrDispID = array_merge($arrBrothersID, $arrChildrenID);
            foreach($arrRet as $key => $array) {
                foreach($arrDispID as $val) {
                    if($array['category_id'] == $val) {
                        $arrRet[$key]['display'] = 1;
                        break;
                    }
                }
            }
        }
        return $arrRet;
    }

    /**
     * メインカテゴリーの取得.
     *
     * @param boolean $count_check 登録商品数をチェックする場合はtrue
     * @return array $arrMainCat メインカテゴリーの配列を返す
     */
    function lfGetMainCat($count_check = false) {
        $objQuery = new SC_Query_Ex();

        $col = '*';
        $from = 'dtb_category left join dtb_category_total_count using (category_id)';

        // メインカテゴリーとその直下のカテゴリーを取得する。
        $where = 'level <= 2 AND del_flg = 0';

        // 登録商品数のチェック
        if($count_check) {
            $where .= ' AND product_count > 0';
        }

        $sql =<<<EOF
SELECT
    *
FROM
    dtb_category
    left join dtb_category_total_count using (category_id)
WHERE
    $where
ORDER BY rank DESC
EOF;

        $arrRet = $objQuery->getAll($sql);

        // メインカテゴリーを抽出する。
        $arrMainCat = array();
        foreach ($arrRet as $cat) {
            if ($cat['level'] != 1) {
                continue;
            }
            // 子カテゴリーを持つかどうかを調べる。
            $arrChildrenID = SC_Utils_Ex::sfGetUnderChildrenArray(
                $arrRet,
                'parent_category_id',
                'category_id',
                $cat['category_id']
            );
            $cat['has_children'] = count($arrChildrenID) > 0;
            $arrMainCat[] = $cat;
        }
        return $arrMainCat;
    }
}
?>
