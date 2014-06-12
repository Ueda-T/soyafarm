<?php
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * 商品一覧 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Products_List.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Products_List extends LC_Page_Ex {
    /** テンプレートクラス名1 */
    var $tpl_class_name1 = array();

    /** テンプレートクラス名2 */
    var $tpl_class_name2 = array();

    /** JavaScript テンプレート */
    var $tpl_javascript;

    var $orderby;

    var $mode;

    /** 検索条件(内部データ) */
    var $arrSearchData = array();

    /** 検索条件(表示用) */
    var $arrSearch = array();

    var $tpl_subtitle = '';

    /** ランダム文字列 **/
    var $tpl_rnd = '';

    /**
     * 検索結果が０件の時に表示する文言
     * mtb_search_link に該当する場合のみセットされる
     */
    var $guidance = '';

    function doValidToken($is_admin = false) {
	// Nothing to do.
    }

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrSTATUS = $masterData->getMasterData("mtb_status");
        $this->arrSTATUS_IMAGE = 
            $masterData->getMasterData("mtb_status_image");
        $this->arrDELIVERYDATE =
            $masterData->getMasterData("mtb_delivery_date");
        $this->arrPRODUCTLISTMAX = 
            $masterData->getMasterData("mtb_product_list_max");
        $this->arrSEARCH_LINK =
            $masterData->getMasterData
            ("mtb_search_link", array('word', 'guidance'));
        $this->tpl_page_category = 'products';
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
     * Page のAction.
     *
     * @return void
     */
    function action() {
        $objQuery   =& SC_Query_Ex::getSingletonInstance();
        $objProduct = new SC_Product_Ex();
        $objCustomer = new SC_Customer_Ex();
        $this->arrForm = $_REQUEST;
        $this->mode = $this->getMode();
	global $CLICK_ANALYZER_CATEGORY;
	global $CLICK_ANALYZER_STATIC;

	// キャンペーンコードが指定されている場合はセッションに保持
	if ($_REQUEST[CAMPAIGN_PARAM_STR]) {
	    $_SESSION["CAMPAIGN_CODE"] = $_REQUEST[CAMPAIGN_PARAM_STR];
	}

	// コードからID変換
	if ($this->arrForm["category_code"]) {
	    $sql = "SELECT category_id FROM dtb_category WHERE category_code = ?";
	    $this->arrForm["category_id"] = 
		$objQuery->getOne($sql, array($this->arrForm["category_code"]));
	}
	if ($this->arrForm["brand_code"]) {
	    $sql = "SELECT brand_id FROM dtb_brand WHERE brand_code = ?";
	    $this->arrForm["brand_id"] = 
		$objQuery->getOne($sql, array($this->arrForm["brand_code"]));
	}

        //表示条件の取得
        $this->arrSearchData = array
	    ('category_id' => $this->lfGetCategoryId
	     (intval($this->arrForm['category_id'])),
	     'brand_id' => intval($this->arrForm['brand_id']),
	     'maker_id' => intval($this->arrForm['maker_id']),
	     'name' => $this->arrForm['name']);
        $this->orderby = $this->arrForm['orderby'];

        //ページング設定
        $this->tpl_pageno = $this->arrForm['pageno'];
        $this->disp_number = $this->lfGetDisplayNum
	    ($this->arrForm['disp_number']);

        // 画面に表示するサブタイトルの設定
        $this->tpl_subtitle = $this->lfGetPageTitle
	    ($this->mode, $this->arrSearchData['category_id']);

	// カテゴリ情報取得
	$this->tpl_arrCategory =  SC_Helper_DB_Ex::sfGetCat
	    (intval($this->arrForm['category_id']));

        // 画面に表示する検索条件を設定
        $this->arrSearch = $this->lfGetSearchConditionDisp
	    ($this->arrSearchData);

        // 商品一覧データの取得
        $arrSearchCondition = 
            $this->lfGetSearchCondition($this->arrSearchData);

        $this->tpl_linemax = $this->lfGetProductAllNum($arrSearchCondition);
        $urlParam = "category_id={$this->arrSearchData['category_id']}&pageno=#page#";
        $this->objNavi =
            new SC_PageNavi_Ex(
                $this->tpl_pageno,
                $this->tpl_linemax,
                $this->disp_number,
                'fnNaviPage',
                NAVI_PMAX,
                $urlParam,
                SC_Display_Ex::detectDevice() !== DEVICE_TYPE_MOBILE
            );

        $this->arrProducts = $this->lfGetProductsList
	    ($arrSearchCondition, $this->disp_number,
	     $this->objNavi->start_row, $this->tpl_linemax, $objProduct);

        // 検索結果が０件の場合のみ、検索リンクマスタを参照し
        // 該当ワードがあれば、その表示文言を採用する
        if ($this->tpl_linemax == 0) {
            foreach ($this->arrSEARCH_LINK as $word => $guidance) {
                if (stristr($this->arrForm['name'], $word) !== FALSE) {
                    $this->guidance = $guidance;
                    break;
                }
            }
        }

        switch ($this->mode) {
	case "json":
        $this->arrProducts = $this->setStatusDataTo
            ($this->arrProducts, $this->arrSTATUS, $this->arrSTATUS_IMAGE);
	    $this->arrProducts = $objProduct->setPriceTaxTo($this->arrProducts);
	    echo SC_Utils_Ex::jsonEncode($this->arrProducts);
	    exit;

	default:
	    // 検索結果が一件の場合、商品詳細ページヘ遷移
	    if ($this->tpl_linemax == 1) {
		$id = $this->arrProducts[0]["product_id"];
		SC_Response_Ex::sendRedirect
		    (sprintf("detail.php?product_id=%d", $id));
		exit;
	    }

	    //商品一覧の表示処理
	    $strnavi = $this->objNavi->strnavi;
	    // 表示文字列
	    $this->tpl_strnavi = empty($strnavi) ? "&nbsp;" : $strnavi;
	    // 規格1クラス名
	    $this->tpl_class_name1 = $objProduct->className1;
	    // 規格2クラス名
	    $this->tpl_class_name2 = $objProduct->className2;
	    // 規格1
	    $this->arrClassCat1 = $objProduct->classCats1;
	    // 規格1が設定されている
	    $this->tpl_classcat_find1 = $objProduct->classCat1_find;
	    // 規格2が設定されている
	    $this->tpl_classcat_find2 = $objProduct->classCat2_find;
	    //
	    $this->tpl_stock_find = $objProduct->stock_find;
	    $this->tpl_product_class_id = $objProduct->product_class_id;
	    $this->tpl_product_type = $objProduct->product_type;
	    // 在庫切れ時の表示文言を取得
	    $this->tpl_stock_status_name = $objProduct->stock_status_name;
	    // 定期フラグ
	    $this->tpl_teiki_flg = $objProduct->teiki_flg;
	    // 数量
	    $this->tpl_arrQuantity = $objProduct->arrQuantity;
	    // 商品ステータスを取得
	    $this->productStatus = $this->arrProducts['productStatus'];
	    unset($this->arrProducts['productStatus']);
	    $this->tpl_javascript .= 'var productsClassCategories = ' . SC_Utils_Ex::jsonEncode($objProduct->classCategories) . ';';
	    //onloadスクリプトを設定
	    foreach ($this->arrProducts as $arrProduct) {
		$js_fnOnLoad .= "fnSetClassCategories(document.product_form{$arrProduct['product_id']});";
	    }

	    //カート処理
	    $target_product_id = intval($this->arrForm['product_id']);
	    if ( $target_product_id > 0) {
		// 商品IDの正当性チェック
		if (!SC_Utils_Ex::sfIsInt($this->arrForm['product_id'])
		    || !SC_Helper_DB_Ex::sfIsRecord("dtb_products", "product_id", $this->arrForm['product_id'], "del_flg = 0 AND status = 1")) {
		    SC_Utils_Ex::sfDispSiteError(PRODUCT_NOT_FOUND);
		}

		// 定期購入フラグ 取得
		if (isset($_REQUEST['regular_flg'])) {
		    $regular_flg = $_REQUEST['regular_flg'];
		    if (SC_Utils_Ex::isBlank($regular_flg)) {
			$regular_flg = REGULAR_PURCHASE_FLG_OFF;
		    }
		} else {
		    $regular_flg = REGULAR_PURCHASE_FLG_OFF;
		}

		// 入力内容のチェック
		$arrErr = $this->lfCheckError($target_product_id, $this->arrForm, $this->tpl_classcat_find1, $this->tpl_classcat_find2);
		if (empty($arrErr)) {
		    $this->lfAddCart($this->arrForm, $_SERVER['HTTP_REFERER'], $regular_flg);
		    SC_Response_Ex::sendRedirect(CART_URLPATH);
		    exit;
		}
		$js_fnOnLoad .= $this->lfSetSelectedData($this->arrProducts, $this->arrForm, $arrErr, $target_product_id);
	    } else {
		// カート「戻るボタン」用に保持
		$netURL = new Net_URL();
		//該当メソッドが無いため、$_SESSIONに直接セット
		$_SESSION['cart_referer_url'] = $netURL->getURL();
	    }

	    $this->tpl_javascript .= 'function fnOnLoad(){' . $js_fnOnLoad . '}';
	    $this->tpl_onload .= 'fnOnLoad(); ';
	    break;

	}

	$this->tpl_rnd = SC_Utils_Ex::sfGetRandomString(3);

    // 商品一覧パンくずリストを取得
    $objDb = new SC_Helper_DB_Ex();

        if(SC_Display_Ex::detectDevice() == DEVICE_TYPE_PC) {
	    $TopicPath = '<a href="' . ROOT_URLPATH . 'index.php">' . TPL_PC_HOME_NAME . '</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;';
	} else {
	    $TopicPath = '<a href="' . ROOT_URLPATH . 'index.php">HOME</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;';
	}

    // カテゴリのみ指定された場合
    if($this->arrForm['category_id'] && !$this->arrForm['brand_id']){
        $arrCatId = $objDb->sfGetParents("dtb_category", "parent_category_id", "category_id", $this->arrForm['category_id']);

        foreach($arrCatId as $key => $val){
            $arrCatName = $objDb->sfGetCat($val);
            if($val != $this->arrForm['category_id']){
                $TopicPath .= '<a href="./list.php?category_id=' .$val. '">'. $arrCatName['name'] . '</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;';
            } else {
                $TopicPath .= $arrCatName['name'];
            }
        }

    }
    // ブランドのみ指定された場合
    else if ($this->arrForm['brand_id'] && !$this->arrForm['category_id']) {
        $arrBrandId = $objDb->sfGetParents("dtb_brand", "parent_id", "brand_id", $this->arrForm['brand_id']);

        foreach($arrBrandId as $key => $val){
            $arrBrandName = $objDb->sfGetBrand($val);
            if($val != $this->arrForm['brand_id']){
                $TopicPath .= '<a href="./brand.php?brand_code='
                    .$arrBrandName["code"]. '">'. $arrBrandName['name']
                    . '</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;';

            } else {
                $TopicPath .= $arrBrandName['name'];
            }
        }

    }
    // カテゴリ、ブランド共に指定された場合
    else if ($this->arrForm['category_id'] && $this->arrForm['brand_id']) {
        $TopicPath .= "検索結果";
    }
    else {
        if ($this->arrForm['name']) {
            $TopicPath .= $this->arrForm['name'] . " の検索結果";
        } else {
            $TopicPath .= "検索結果";
        }
    }

        $this->TopicPath = $TopicPath;
        // パンくずリスト取得終わり

        //　表示商品数を取得する
        $product_count = count($this->arrProducts);
        // 開始商品番号取得
        $this->tpl_from_no = $this->objNavi->start_row + 1;
        // 終了商品番号
        $this->tpl_to_no = $this->tpl_from_no + $product_count - 1;

	// CLICK ANALYZER用埋め込み
	$this->tpl_clickAnalyzer = "";

	if ($product_count) {
	    if (isset($CLICK_ANALYZER_CATEGORY[$this->arrForm["category_id"]])) {
		$this->tpl_clickAnalyzer = $CLICK_ANALYZER_CATEGORY[$this->arrForm["category_id"]];
	    }
	} else {
	    if (isset($CLICK_ANALYZER_STATIC["search"])) {
		$this->tpl_clickAnalyzer = $CLICK_ANALYZER_STATIC["search"];
	    }
	}

        // ログイン判定 
        if($objCustomer->isLoginSuccess(true)) {
            $this->tpl_customer_kbn = $objCustomer->getValue('customer_kbn');
        } else {
            $this->tpl_customer_kbn = null;
        }
    }

    /**
     * カテゴリIDの取得
     *
     * @return integer カテゴリID
     */
    function lfGetCategoryId($category_id) {

        // 指定なしの場合、0 を返す
        if (empty($category_id)) return 0;

        // 正当性チェック
        if (!SC_Utils_Ex::sfIsInt($category_id)
            || SC_Utils_Ex::sfIsZeroFilling($category_id)
            || !SC_Helper_DB_Ex::sfIsRecord('dtb_category', 'category_id', (array)$category_id, 'del_flg = 0')
            ) {
	    SC_Response_Ex::sendRedirect(NOTFOUND_URLPATH);
	    // SC_Utils_Ex::sfDispSiteError(CATEGORY_NOT_FOUND);
	    exit;
        }

        // 指定されたカテゴリIDを元に正しいカテゴリIDを取得する。
        $arrCategory_id = SC_Helper_DB_Ex::sfGetCategoryId('', $category_id);

        if (empty($arrCategory_id)) {
	    SC_Response_Ex::sendRedirect(NOTFOUND_URLPATH);
            // SC_Utils_Ex::sfDispSiteError(CATEGORY_NOT_FOUND);
	    exit;
        }

        return $arrCategory_id[0];
    }

    /* 商品一覧の表示 */
    function lfGetProductsList($searchCondition, $disp_number, $startno, $linemax, &$objProduct) {

        $arrval_order = array();

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // 表示順序
        switch ($this->orderby) {
            // 販売価格が安い順
            case 'price':
                $objProduct->setProductsOrder('price01', 'dtb_products_class', 'ASC');
                break;

            // 新着順
            case 'date':
                $objProduct->setProductsOrder('create_date', 'dtb_products', 'DESC');
                break;

            default:
                if (strlen($searchCondition["where_category"]) >= 1) {
                    $dtb_product_categories = "(SELECT * FROM dtb_product_categories WHERE " . $searchCondition["where_category"] . ")";
                    $arrval_order = array_merge($searchCondition['arrvalCategory'], $searchCondition['arrvalCategory']);
                } else {
                    $dtb_product_categories = 'dtb_product_categories';
                }

                $order = <<< __EOS__
                    (
                        SELECT
                             T3.rank
                        FROM
                            $dtb_product_categories T2
                            JOIN dtb_category T3
                                USING (category_id)
                        WHERE T2.product_id = alldtl.product_id
                        ORDER BY T3.rank DESC, T2.rank DESC
                        LIMIT 1
                    ) DESC
                    ,(
                        SELECT
                            T2.rank
                        FROM
                            $dtb_product_categories T2
                            JOIN dtb_category T3
                                USING (category_id)
                        WHERE T2.product_id = alldtl.product_id
                        ORDER BY T3.rank DESC, T2.rank DESC
                        LIMIT 1
                    ) DESC
                    ,product_id
__EOS__;
                    $objQuery->setOrder($order);
                break;
        }
        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setLimitOffset($disp_number, $startno);
        $objQuery->setWhere($searchCondition['where']);

         // 表示すべきIDとそのIDの並び順を一気に取得
        $arrProduct_id = $objProduct->findProductIdsOrder($objQuery, array_merge($searchCondition['arrval'], $arrval_order));

        // 取得した表示すべきIDだけを指定して情報を取得。
        $where = "";
        if (is_array($arrProduct_id) && !empty($arrProduct_id)) {
            $where = 'product_id IN (' . implode(',', $arrProduct_id) . ')';
        } else {
            // 一致させない
            $where = '0<>0';
        }

        $where .= ' AND del_flg = 0'; // 商品規格の削除フラグ
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->setWhere($where);
        $arrProducts = $objProduct->lists($objQuery, $arrProduct_id);

        //取得している並び順で並び替え
        $arrProducts2 = array();
        foreach($arrProducts as $item) {
            $arrProducts2[ $item['product_id'] ] = $item;
        }
        $arrProducts = array();
        foreach($arrProduct_id as $product_id) {
            $arrProducts[] = $arrProducts2[$product_id];
        }

        // 規格を設定
        $objProduct->setProductsClassByProductIds($arrProduct_id);
        
        $arrProducts += array('productStatus' => $objProduct->getProductStatus($arrProduct_id, SC_Display_Ex::detectDevice()));
        return $arrProducts;
    }

    /* 入力内容のチェック */
    function lfCheckError($product_id, &$arrForm, $tpl_classcat_find1, $tpl_classcat_find2) {

        // 入力データを渡す。
        $objErr = new SC_CheckError_Ex($arrForm);

        // 複数項目チェック
        if ($tpl_classcat_find1[$product_id]) {
            $objErr->doFunc(array("規格1", 'classcategory_id1', INT_LEN), array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        }
        if ($tpl_classcat_find2[$product_id]) {
            $objErr->doFunc(array("規格2", 'classcategory_id2', INT_LEN), array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        }

        $objErr->doFunc(array("商品規格ID", 'product_class_id', INT_LEN), array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("数量", 'quantity', INT_LEN), array("EXIST_CHECK", "ZERO_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));

        return $objErr->arrErr;
    }

    /**
     * パラメーターの読み込み
     *
     * @return void
     */
    function lfGetDisplayNum($display_number) {
        // 表示件数
        return (SC_Utils_Ex::sfIsInt($display_number))
            ? $display_number
            : current(array_keys($this->arrPRODUCTLISTMAX));
    }

    /**
     * ページタイトルの設定
     *
     * @return str
     */
    function lfGetPageTitle($mode, $category_id = 0){
        if ($mode == 'search') {
            return "検索結果";
        } elseif ($category_id == 0) {
            return "全商品";
        } else {
            $arrCat = SC_Helper_DB_Ex::sfGetCat($category_id);
            return $arrCat['name'];
        }
    }

    /**
     * 表示用検索条件の設定
     *
     * @return array
     */
    function lfGetSearchConditionDisp($arrSearchData){
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrSearch = array('category' => '指定なし',
			   'brand' => '指定なし',
			   'maker' => '指定なし',
			   'name' => '指定なし');

        // カテゴリー検索条件
        if ($arrSearchData['category_id'] > 0) {
            $arrSearch['category'] = $objQuery->get
		('category_name', 'dtb_category', 'category_id = ?',
		 array($arrSearchData['category_id']));
        }

        // ブランド検索条件
        if ($arrSearchData['brand_id'] > 0) {
            $arrSearch['brand'] = $objQuery->get
		('brand_name', 'dtb_brand', 'brand_id = ?',
		 array($arrSearchData['brand_id']));
        }

        // メーカー検索条件
        if (strlen($arrSearchData['maker_id']) > 0) {
            $arrSearch['maker'] = $objQuery->get
		('name', 'dtb_maker', 'maker_id = ?',
		 array($arrSearchData['maker_id']));
        }

        // 商品名検索条件
        if (strlen($arrSearchData['name']) > 0) {
            $arrSearch['name'] = $arrSearchData['name'];
        }

        return $arrSearch;
    }

    /**
     * 該当件数の取得
     *
     * @return int
     */
    function lfGetProductAllNum($searchCondition){
        // 検索結果対象となる商品の数を取得
        $objQuery   =& SC_Query_Ex::getSingletonInstance();
        $objQuery->setWhere($searchCondition['where']);
        $objProduct = new SC_Product_Ex();
        return $objProduct->findProductCount($objQuery, $searchCondition['arrval']);
    }

    /**
     * 検索条件のwhere文とかを取得
     *
     * @return array
     */
    function lfGetSearchCondition($arrSearchData){
        $searchCondition = array(
            'where' => "",
            'arrval' => array(),
            "where_category" => "",
            'arrvalCategory' => array(),
            "where_brand" => "",
            'arrvalBrand' => array()
        );

        // カテゴリからのWHERE文字列取得
        if ($arrSearchData["category_id"] != 0) {
            list($searchCondition["where_category"],
		 $searchCondition['arrvalCategory'])
		= SC_Helper_DB_Ex::sfGetCatWhere($arrSearchData["category_id"]);
        }

        // ブランドからのWHERE文字列取得
        if ($arrSearchData["brand_id"] != 0) {
            list($searchCondition["where_brand"],
		 $searchCondition['arrvalBrand'])
		= SC_Helper_DB_Ex::sfGetBrandWhere($arrSearchData["brand_id"]);
        }

        // ▼対象商品IDの抽出
        // 商品検索条件の作成（未削除、表示）
        $searchCondition['where'] = "alldtl.del_flg = 0 AND alldtl.status = 1 AND alldtl.not_search_flg = 1";

        $searchCondition['where'] .=<<<EOF
   AND (DATE_FORMAT(alldtl.disp_start_date, '%Y%m%d') <= DATE_FORMAT(now(), '%Y%m%d')
       OR alldtl.disp_start_date IS NULL)
EOF;

        // 在庫無し商品の非表示
        if (NOSTOCK_HIDDEN === true) {
            $searchCondition['where'] .= ' AND (stock >= 1 OR stock_unlimited = 1)';
        }

        if (strlen($searchCondition["where_category"]) >= 1) {
            $searchCondition['where'] .= " AND T2.".$searchCondition["where_category"];
            $searchCondition['arrval'] = array_merge($searchCondition['arrval'], $searchCondition['arrvalCategory']);
        }

        if (strlen($searchCondition["where_brand"]) >= 1) {
	    $searchCondition['where'] .= " AND BP.".$searchCondition["where_brand"];
	    $searchCondition['arrval'] = array_merge($searchCondition['arrval'], $searchCondition['arrvalBrand']);
        }

        // 商品名をwhere文に
        $name = $arrSearchData['name'];
        $name = str_replace(",", "", $name);
        // 全角スペースを半角スペースに変換
        $name = str_replace('　', ' ', $name);
        // スペースでキーワードを分割
        $names = preg_split("/ +/", $name);

        // 分割したキーワードを一つずつwhere文に追加
        foreach ($names as $val) {
            if ( strlen($val) > 0 ) {

                // 商品名、検索ワード、商品コードで検索
                $searchCondition['where']    .= " AND (( alldtl.name ILIKE ? OR alldtl.comment3 ILIKE ? OR T1.product_code ILIKE ?) ";
                $searchCondition['where']    .= "  OR ( alldtl.name ILIKE ? OR alldtl.comment3 ILIKE ? OR T1.product_code ILIKE ?)) ";

                // 全角カタカナ対応
                $val_zen_kana = mb_convert_kana($val, "KV");
                $searchCondition['arrval'][]  = "%$val_zen_kana%";
                $searchCondition['arrval'][]  = "%$val_zen_kana%";
                $searchCondition['arrval'][]  = "%$val_zen_kana%";
                // 半角カタカナ対応
                $val_han_kana = mb_convert_kana($val, "k");
                $searchCondition['arrval'][]  = "%$val_han_kana%";
                $searchCondition['arrval'][]  = "%$val_han_kana%";
                $searchCondition['arrval'][]  = "%$val_han_kana%";
            }
        }

        // メーカーらのWHERE文字列取得
        if ($arrSearchData['maker_id']) {
            $searchCondition['where']   .= " AND alldtl.maker_id = ? ";
            $searchCondition['arrval'][] = $arrSearchData['maker_id'];
        }
        return $searchCondition;
    }

    /**
     * カートに入れる商品情報にエラーがあったら戻す
     *
     * @return str
     */
    function lfSetSelectedData(&$arrProducts, $arrForm, $arrErr, $product_id){
        $js_fnOnLoad = "";
        foreach (array_keys($arrProducts) as $key) {
            if ($arrProducts[$key]['product_id'] == $product_id) {

                $arrProducts[$key]['product_class_id']  = $arrForm['product_class_id'];
                $arrProducts[$key]['classcategory_id1'] = $arrForm['classcategory_id1'];
                $arrProducts[$key]['classcategory_id2'] = $arrForm['classcategory_id2'];
                $arrProducts[$key]['quantity']          = $arrForm['quantity'];
                $arrProducts[$key]['arrErr']            = $arrErr;
                $classcategory_id2 = SC_Utils_Ex::jsonEncode($arrForm['classcategory_id2']);
                $js_fnOnLoad .= "fnSetClassCategories(document.product_form{$arrProducts[$key]['product_id']}, {$classcategory_id2});";
            }
        }
        return $js_fnOnLoad;
    }

    /**
     * カートに商品を追加
     *
     * @return void
     */
    function lfAddCart($arrForm, $referer, $regular_flg = '0'){
        $product_class_id = $arrForm['product_class_id'];
        $objCartSess = new SC_CartSession_Ex();
        $objCartSess->addProduct(
            $product_class_id,
            $arrForm['quantity'],
            $regular_flg
        );
    }

    /**
     * 商品情報配列にステータス情報を追加する
     *
     * @param Array $arrProducts 商品一覧情報
     * @param Array $arrStatus	ステータス配列
     * @param Array $arrStatusImage スタータス画像配列
     * @return Array $arrProducts 商品一覧情報
     */
    function setStatusDataTo($arrProducts, $arrStatus, $arrStatusImage){

        foreach ($arrProducts['productStatus'] as $product_id => $arrValues) {
            for ($i = 0; $i < count($arrValues); $i++){
                $product_status_id = $arrValues[$i];
                if (!empty($product_status_id)) {
                    $arrProductStatus = array('status_cd' => $product_status_id,
                                              'status_name' => $arrStatus[$product_status_id],
                                              'status_image' =>$arrStatusImage[$product_status_id]);
                    $arrProducts['productStatus'][$product_id][$i] = $arrProductStatus;
                }
            }
        }
        return $arrProducts;
    }
}
?>
