<?php
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';
require_once CLASS_EX_REALDIR . 'helper_extends/SC_Helper_CSV_Ex.php';

/**
 * 商品管理 のページクラス.
 */
class LC_Page_Admin_Products extends LC_Page_Admin_Ex {
    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/index.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'index';
        $this->tpl_pager = 'pager.tpl';
        $this->tpl_maintitle = '商品管理';
        $this->tpl_subtitle = '商品マスター';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPageMax = $masterData->getMasterData("mtb_page_max");
        $this->arrDISP = $masterData->getMasterData("mtb_disp");
        $this->arrSTATUS = $masterData->getMasterData("mtb_status");
        $this->arrBRAND = array(0 => 'ブランド０', 1 => 'ブランド１');
        $this->arrDELIV_FEE = array(0 => 'なし', 1 => 'あり');

        $objDate = new SC_Date();
        // 登録・更新検索開始年
        $objDate->setStartYear(RELEASE_YEAR);
        $objDate->setEndYear(DATE("Y"));
        $this->arrStartYear = $objDate->getYear();
        $this->arrStartMonth = $objDate->getMonth();
        $this->arrStartDay = $objDate->getDay();
        // 登録・更新検索終了年
        $objDate->setStartYear(RELEASE_YEAR);
        $objDate->setEndYear(DATE("Y"));
        $this->arrEndYear = $objDate->getYear();
        $this->arrEndMonth = $objDate->getMonth();
        $this->arrEndDay = $objDate->getDay();

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
        $objDb = new SC_Helper_DB_Ex();
        $objFormParam = new SC_FormParam();
        $objProduct = new SC_Product();
        $objQuery =& SC_Query::getSingletonInstance();

        // パラメーター情報の初期化
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $this->arrHidden = $objFormParam->getSearchArray();
        $this->arrForm = $objFormParam->getFormParamList();

        switch ($this->getMode()) {
        case 'delete':
            // 商品、子テーブル(商品規格)、顧客お気に入り商品の削除
            $this->doDelete("product_id = ?", array($objFormParam->getValue('product_id')));
            // 件数カウントバッチ実行
            $objDb->sfCountCategory($objQuery);
            $objDb->sfCountMaker($objQuery);

            // 削除後に検索結果を表示するため breakしない
            // 検索パラメーター生成後に処理実行するため breakしない
        case 'csv':
	case 'csv_production':
        case 'delete_all':
        case 'search':
            $objFormParam->convParam();
            $objFormParam->trimParam();
            $this->arrErr = $this->lfCheckError($objFormParam);
            if (count($this->arrErr) == 0) {
		list($where, $arrval) = 
		    $this->buildQuery($objFormParam, $objDb);

                /* -----------------------------------------------
                 * 処理を実行
                 * ----------------------------------------------- */
                switch ($this->getMode()) {
                    // CSVを送信する。
                    case 'csv':
			$alias = "p";
			list($where, $arrval) = $this->buildQuery
			    ($objFormParam, $objDb, $alias);
			$this->doOutputCsv($where, $arrval, $alias);
                        break;

                    case 'csv_production':
			$alias = "p";
			list($where, $arrval) = $this->buildQuery
			    ($objFormParam, $objDb, $alias);
			$this->doOutputProductionCsv($where, $arrval, $alias);
                        break;

                    // 全件削除(ADMIN_MODE)
                    case 'delete_all':
                        $this->doDelete($where, $arrval);
                        break;

                    // 検索実行
                    default:
                        // 行数の取得
                        $this->tpl_linemax = $this->getNumberOfLines($where, $arrval);
                        // ページ送りの処理
                        $page_max = SC_Utils_Ex::sfGetSearchPageMax($objFormParam->getValue('search_page_max'));
                        // ページ送りの取得
                        $objNavi = new SC_PageNavi_Ex
			    ($this->arrHidden['search_pageno'],
			     $this->tpl_linemax, $page_max,
			     'fnNaviSearchPage', NAVI_PMAX);

                        $this->arrPagenavi = $objNavi->arrPagenavi;

                        // 検索結果の取得
                        $this->arrProducts = $this->findProducts
			    ($where, $arrval, $page_max, $objNavi->start_row,
			     $objProduct);

                        // 各商品ごとのカテゴリIDを取得
                        if (count($this->arrProducts) > 0) {
                            foreach ($this->arrProducts as $key => $val) {
                                $this->arrProducts[$key]['categories'] = $objDb->sfGetCategoryId($val["product_id"], 0, true);
                                $objDb->g_category_on = false;
                            }
                        }
                }
            }
            break;
        }

        // カテゴリの読込
        list($this->arrCatKey, $this->arrCatVal) = $objDb->sfGetLevelCatList(false);
        $this->arrCatList = $this->lfGetIDName($this->arrCatKey, $this->arrCatVal);
        $this->arrLastCatList = array();
        foreach ($this->arrCatList as $key => $value) {
            $array = explode(">", $value);
            $this->arrLastCatList[$key] = $array[count($array) - 1];
        }

        $this->arrBrandList = $objDb->sfGetBrandList("&nbsp;&nbsp;");
    }

    function doOutputCsv($where, $arrval, $alias) {
	$sql =<<<__EOS
select
    $alias.product_id
   ,$alias.name
   ,$alias.sales_name
   ,$alias.status
   ,$alias.brand_id
   ,$alias.comment3
   ,$alias.main_list_comment
   ,$alias.main_list_image
   ,$alias.main_comment
   ,$alias.main_image
   ,$alias.main_large_image
   ,$alias.pc_comment1
   ,$alias.pc_comment2
   ,$alias.pc_comment3
   ,$alias.pc_comment4
   ,$alias.pc_button4
   ,$alias.pc_comment5
   ,$alias.pc_button5
   ,$alias.sp_comment1
   ,$alias.sp_comment2
   ,$alias.sp_comment3
   ,$alias.sp_comment4
   ,$alias.sp_button4
   ,$alias.sp_comment5
   ,$alias.sp_button5
   ,$alias.mb_comment1
   ,$alias.mb_comment2
   ,$alias.mb_comment3
   ,$alias.mb_comment4
   ,$alias.mb_comment5
   ,$alias.capacity
   ,$alias.sale_start_date
   ,$alias.sale_end_date
   ,$alias.guide_image
   ,$alias.guide_image_teiki
   ,$alias.metatag
   ,$alias.deliv_kbn1
   ,$alias.deliv_kbn2
   ,$alias.drop_shipment
   ,$alias.component_flg
   ,$alias.employee_sale_cd
   ,$alias.not_search_flg
   ,$alias.mail_deliv_id
   ,$alias.disp_start_date
   ,$alias.del_flg
   ,$alias.creator_id
   ,$alias.create_date
   ,$alias.updator_id
   ,$alias.update_date
   ,$alias.deliv_date_id
   ,class.product_class_id
   ,class.class_combination_id
   ,class.product_type_id
   ,class.product_code
   ,class.stock
   ,class.stock_unlimited
   ,class.sale_limit
   ,class.sale_minimum_number
   ,class.price01
   ,class.price02
   ,class.deliv_judgment
   ,class.point_rate
   ,class.sample_flg
   ,class.present_flg
   ,class.sell_flg
   ,class.teiki_flg
   ,class.stock_status_name
   ,class.course_cd
   ,class.creator_id as class_creator_id
   ,class.create_date as class_create_date
   ,class.updator_id as class_updator_id
   ,class.update_date as class_update_date
from
    dtb_products $alias
inner join
    dtb_products_class class
    on class.product_id = $alias.product_id
    and class.del_flg = 0
where
    $where
__EOS;

	$header = array
	    ("商品ID",
	     "商品名",
	     "販売名",
	     "表示ステータス",
	     "ブランドID",
	     "コメント3",
	     "メイン一覧コメント",
	     "メイン一覧画像",
	     "メインコメント",
	     "メイン画像",
	     "メイン拡大画像",
	     "PCコメント1",
	     "PCコメント2",
	     "PCコメント3",
	     "PCコメント4",
	     "PCボタン4",
	     "PCコメント5",
	     "PCボタン5",
	     "スマホコメント1",
	     "スマホコメント2",
	     "スマホコメント3",
	     "スマホコメント4",
	     "スマホボタン4",
	     "スマホコメント5",
	     "スマホボタン5",
	     "モバイルコメント1",
	     "モバイルコメント2",
	     "モバイルコメント3",
	     "モバイルコメント4",
	     "モバイルコメント5",
	     "容量",
	     "販売開始日",
	     "販売終了日",
	     "案内画像",
	     "案内画像_定期",
	     "メタタグ",
	     "配送区分１",
	     "配送区分２",
	     "産直区分",
	     "成分表示フラグ",
	     "社員購入グループコード",
	     "検索除外フラグ",
	     "メール便業者ID",
	     "掲載開始日",
	     "削除フラグ",
	     "作成者ID",
	     "作成日時",
	     "更新者ID",
	     "更新日時",
	     "発送日目安",
	     "商品規格ID",
	     "規格組み合わせ情報ID",
	     "商品区分",
	     "商品コード",
	     "在庫数",
	     "在庫制限",
	     "購入制限",
	     "購入最低数",
	     "価格",
	     "社員価格",
	     "配送形態算出係数",
	     "ポイント付与率",
	     "サンプルフラグ",
	     "プレゼントフラグ",
	     "販売対象フラグ",
	     "定期購入可否フラグ",
	     "在庫切れ時の表示文言",
	     "コースCD",
	     "作成者ID",
	     "作成日時",
	     "更新者ID",
	     "更新日時"
	     );

        $objCsv = new SC_Helper_CSV_Ex();
 	$objCsv->sfDownloadCsvFromSql($sql, $arrval, "products", $header, true);
 	exit;
    }

    function doOutputProductionCsv($where, $arrval, $alias) {
	$pc_device = DEVICE_TYPE_PC;
	$sp_device = DEVICE_TYPE_SMARTPHONE;
	$mb_device = DEVICE_TYPE_MOBILE;

	$sql =<<<__EOS
select
    class.product_code
   ,$alias.name
   ,$alias.disp_name
   ,$alias.sales_name
   ,$alias.status
   ,$alias.brand_id
   ,$alias.comment3
   ,$alias.main_list_comment
   ,$alias.main_list_image
   ,$alias.main_comment
   ,$alias.main_image
   ,$alias.main_large_image
   ,$alias.pc_comment1
   ,$alias.pc_comment2
   ,$alias.pc_comment3
   ,$alias.pc_comment4
   ,$alias.pc_button4
   ,$alias.pc_comment5
   ,$alias.pc_button5
   ,$alias.sp_comment1
   ,$alias.sp_comment2
   ,$alias.sp_comment3
   ,$alias.sp_comment4
   ,$alias.sp_button4
   ,$alias.sp_comment5
   ,$alias.sp_button5
   ,$alias.mb_comment1
   ,$alias.mb_comment2
   ,$alias.mb_comment3
   ,$alias.mb_comment4
   ,$alias.mb_comment5
   ,$alias.capacity
   ,$alias.guide_image
   ,$alias.guide_image_teiki
   ,$alias.metatag
   ,$alias.component_flg
   ,$alias.employee_sale_cd
   ,$alias.not_search_flg
   ,$alias.disp_start_date
   ,$alias.deliv_date_id
   ,class.sale_limit
   ,class.sale_minimum_number
   ,class.teiki_flg
   ,class.stock_status_name
   ,(SELECT ARRAY_TO_STRING(ARRAY(SELECT product_status_id FROM dtb_product_status
     WHERE dtb_product_status.product_id = $alias.product_id 
     and dtb_product_status.device_type_id = $pc_device
     ORDER BY dtb_product_status.product_status_id), ',')) AS pc_product_status
   ,(SELECT ARRAY_TO_STRING(ARRAY(SELECT product_status_id FROM dtb_product_status
     WHERE dtb_product_status.product_id = $alias.product_id 
     and dtb_product_status.device_type_id = $sp_device
     ORDER BY dtb_product_status.product_status_id), ',')) AS sp_product_status
   ,(SELECT ARRAY_TO_STRING(ARRAY(SELECT product_status_id FROM dtb_product_status
     WHERE dtb_product_status.product_id = $alias.product_id 
     and dtb_product_status.device_type_id = $mb_device
     ORDER BY dtb_product_status.product_status_id), ',')) AS mb_product_status
from
    dtb_products $alias
inner join
    dtb_products_class class
    on class.product_id = $alias.product_id
    and class.del_flg = 0
where
    $where
__EOS;

	$header = array
	    ("商品コード",
	     "商品名",
	     "表示用商品名",
	     "販売名",
	     "表示ステータス",
	     "ブランドID",
	     "コメント3",
	     "メイン一覧コメント",
	     "メイン一覧画像",
	     "メインコメント",
	     "メイン画像",
	     "メイン拡大画像",
	     "PCコメント1",
	     "PCコメント2",
	     "PCコメント3",
	     "PCコメント4",
	     "PCボタン4",
	     "PCコメント5",
	     "PCボタン5",
	     "スマホコメント1",
	     "スマホコメント2",
	     "スマホコメント3",
	     "スマホコメント4",
	     "スマホボタン4",
	     "スマホコメント5",
	     "スマホボタン5",
	     "モバイルコメント1",
	     "モバイルコメント2",
	     "モバイルコメント3",
	     "モバイルコメント4",
	     "モバイルコメント5",
	     "容量",
	     "案内画像",
	     "案内画像_定期",
	     "メタタグ",
	     "成分表示フラグ",
	     "社員購入グループコード",
	     "検索除外フラグ",
	     "掲載開始日",
	     "発送日目安",
	     "購入制限",
	     "購入最低数",
	     "定期購入可否フラグ",
	     "在庫切れ時の表示文言",
	     "PC用アイコン",
	     "スマホ用アイコン",
	     "モバイル用アイコン"
	     );

        $objCsv = new SC_Helper_CSV_Ex();
 	$objCsv->sfDownloadCsvFromSql($sql, $arrval, "products", $header, true);
 	exit;
    }
    /**
     * パラメーター情報の初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        // POSTされる値
        $objFormParam->addParam("商品ID", "product_id", INT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("カテゴリID", "category_id", STEXT_LEN, 'n', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("ページ送り番号","search_pageno", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("表示件数", "search_page_max", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        // 検索条件
        $objFormParam->addParam("商品ID", "search_product_id", INT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("商品コード", "search_product_code", STEXT_LEN, '', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("商品名", "search_name", STEXT_LEN, '', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("カテゴリ", "search_category_id", STEXT_LEN, 'n', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("ブランド", "search_brand_id", STEXT_LEN, 'n', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("種別", "search_status", INT_LEN, 'n', array("MAX_LENGTH_CHECK"));
        // 登録・更新日
        $objFormParam->addParam("開始年", "search_startyear", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("開始月", "search_startmonth", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("開始日", "search_startday", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("終了年", "search_endyear", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("終了月", "search_endmonth", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("終了日", "search_endday", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("ステータス", "search_product_flag", INT_LEN, 'n', array("MAX_LENGTH_CHECK"));
    }

    /**
     * 入力内容のチェックを行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfCheckError(&$objFormParam) {
        $objErr = new SC_CheckError_Ex($objFormParam->getHashArray());
        $objErr->arrErr = $objFormParam->checkError();

        $objErr->doFunc(array("開始日", "終了日", "search_startyear", "search_startmonth", "search_startday", "search_endyear", "search_endmonth", "search_endday"), array("CHECK_SET_TERM"));

        return $objErr->arrErr;
    }

    // カテゴリIDをキー、カテゴリ名を値にする配列を返す。
    function lfGetIDName($arrCatKey, $arrCatVal) {
        $max = count($arrCatKey);
        for ($cnt = 0; $cnt < $max; $cnt++) {
            $key = isset($arrCatKey[$cnt]) ? $arrCatKey[$cnt] : "";
            $val = isset($arrCatVal[$cnt]) ? $arrCatVal[$cnt] : "";
            $arrRet[$key] = $val;
        }
        return $arrRet;
    }

    /**
     * 商品、子テーブル(商品規格)、お気に入り商品の削除
     *
     * @param string $where 削除対象の WHERE 句
     * @param array $arrParam 削除対象の値
     * @return void
     */
    function doDelete($where, $arrParam = array()) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sqlval['del_flg'] = 1;
        $sqlval['update_date'] = 'now()';
        $objQuery->begin();
        $objQuery->update('dtb_products_class', $sqlval, "product_id IN (SELECT product_id FROM dtb_products WHERE $where)", $arrParam);
        $objQuery->delete('dtb_customer_favorite_products', "product_id IN (SELECT product_id FROM dtb_products WHERE $where)", $arrParam);
        $objQuery->update('dtb_products', $sqlval, $where, $arrParam);
        $objQuery->commit();
    }

    /**
     * クエリを構築する.
     *
     * 検索条件のキーに応じた WHERE 句と, クエリパラメーターを構築する.
     * クエリパラメーターは, SC_FormParam の入力値から取得する.
     *
     * 構築内容は, 引数の $where 及び $arrValues にそれぞれ追加される.
     *
     * @param string $key 検索条件のキー
     * @param string $where 構築する WHERE 句
     * @param array $arrValues 構築するクエリパラメーター
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param SC_FormParam $objDb SC_Helper_DB_Ex インスタンス
     * @return void
     */
    function buildQuery(&$objFormParam, &$objDb, $alias = "") {
	$arrParam = $objFormParam->getHashArray();
	$alias .= strlen($alias) ? "." : "";
	$where = $alias . "del_flg = 0";
	$arrValues = array();

	foreach ($arrParam as $key => $val) {
	    if($val == "") {
		continue;
	    }

	    switch ($key) {
		// 商品ID
	    case 'search_product_id':
		$where .= " AND " . $alias . "product_id = ?";
		$arrValues[] = sprintf('%d', $objFormParam->getValue($key));
		break;
		// 種別
	    case 'search_status':
		$tmp_where = "";
		foreach($objFormParam->getValue($key) as $element) {
		    if($element != "") {
			if(SC_Utils_Ex::isBlank($tmp_where)) {
			    $tmp_where .= " AND (" . $alias . "status = ?";
			} else {
			    $tmp_where .= " OR " . $alias . "status = ?";
			}
			$arrValues[] = $element;
		    }
		}

		if(!SC_Utils_Ex::isBlank($tmp_where)) {
		    $tmp_where .= ")";
		    $where .= " $tmp_where ";
		}
		break;
		// 商品コード
	    case 'search_product_code':
		$where .= " AND " . $alias . "product_id IN (SELECT product_id FROM dtb_products_class WHERE product_code ILIKE ? GROUP BY product_id)";
		$arrValues[] = sprintf('%%%s%%', $objFormParam->getValue($key));
		break;
		// 商品名
	    case 'search_name':
		$where .= " AND " . $alias . "name LIKE ?";
		$arrValues[] = sprintf('%%%s%%', $objFormParam->getValue($key));
		break;
		// カテゴリ
	    case 'search_category_id':
		list($tmp_where, $tmp_Values) = $objDb->sfGetCatWhere($objFormParam->getValue($key));
		if($tmp_where != "") {
		    $where.= " AND " . $alias . "product_id IN (SELECT product_id FROM dtb_product_categories WHERE " . $tmp_where . ")";
		    $arrValues = array_merge((array)$arrValues, (array)$tmp_Values);
		}
		break;
		// ブランド
	    case 'search_brand_id':
		$where .= " AND " . $alias . "brand_id = ?";
		$arrValues[] = $objFormParam->getValue($key);
		break;
	    default:
		break;
	    }
	}

	return array($where, $arrValues);
    }

    /**
     * 検索結果の行数を取得する.
     *
     * @param string $where 検索条件の WHERE 句
     * @param array $arrValues 検索条件のパラメーター
     * @return integer 検索結果の行数
     */
    function getNumberOfLines($where, $arrValues) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->count('dtb_products', $where, $arrValues);
    }

    /**
     * 商品を検索する.
     *
     * @param string $where 検索条件の WHERE 句
     * @param array $arrValues 検索条件のパラメーター
     * @param integer $limit 表示件数
     * @param integer $offset 開始件数
     * @param string $order 検索結果の並び順
     * @param SC_Product $objProduct SC_Product インスタンス
     * @return array 商品の検索結果
     */
    function findProducts($where, $arrValues, $limit, $offset, &$objProduct) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $table = $objProduct->alldtlSQL("del_flg = 0");
	$sql =<<<__EOS
select
    product_id
   ,name
   ,brand_name
   ,main_list_image
   ,status
   ,product_code_min
   ,product_code_max
   ,price01_min
   ,price01_max
   ,price02_min
   ,price02_max
   ,stock_min
   ,stock_max
   ,stock_unlimited_min
   ,stock_unlimited_max
   ,update_date
from
    $table
where
    $where
order by
    product_code_min
limit $offset, $limit
__EOS;

        return $objQuery->getAll($sql, $arrValues);
    }
}
?>
