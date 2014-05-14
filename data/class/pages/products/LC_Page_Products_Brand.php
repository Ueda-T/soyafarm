<?php
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/*
 * ブランド一覧 のページクラス.
 */
class LC_Page_Products_Brand extends LC_Page_Ex {
    /** テンプレートクラス名1 */
    var $tpl_class_name1 = array();
    /** テンプレートクラス名2 */
    var $tpl_class_name2 = array();
    /** JavaScript テンプレート */
    var $tpl_javascript;
    var $mode;
    var $tpl_subtitle = '';
    /** ランダム文字列 **/
    var $tpl_rnd = '';
    /**
     * 検索結果が０件の時に表示する文言
     * mtb_search_link に該当する場合のみセットされる
     */
    var $guidance = '';

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrSTATUS = $masterData->getMasterData("mtb_status");
        $this->arrSTATUS_IMAGE = $masterData->getMasterData("mtb_status_image");
        $this->arrDELIVERYDATE = $masterData->getMasterData("mtb_delivery_date");
        $this->arrPRODUCTLISTMAX = $masterData->getMasterData("mtb_product_list_max");
        $this->arrSEARCH_LINK = $masterData->getMasterData("mtb_search_link", array('word', 'guidance'));
	// モバイル用
	$this->tpl_notitle = true;
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
	global $CLICK_ANALYZER_BRAND;

        $this->arrForm = $_REQUEST;

        //modeの取得
        $this->mode = $this->getMode();

	// キャンペーンコードが指定されている場合はセッションに保持
	if ($_GET[CAMPAIGN_PARAM_STR]) {
	    $_SESSION["CAMPAIGN_CODE"] = $_GET[CAMPAIGN_PARAM_STR];
	}

	// コードからID変換
	if ($this->arrForm["brand_code"]) {
	    $sql = "SELECT brand_id FROM dtb_brand WHERE brand_code = ?";
	    $this->arrForm["brand_id"] = 
		$objQuery->getOne($sql, array($this->arrForm["brand_code"]));
	}

        $this->arrSearchData = array
	    ('brand_id' => intval($this->arrForm['brand_id']));

	$this->tpl_child_brand = false;
	// ブランド情報取得
	$arrBrand = $this->lfGetBrand($this->arrForm["brand_id"]);
	if (empty($arrBrand) || count($arrBrand) == 0) {
	    SC_Response_Ex::sendRedirect(NOTFOUND_URLPATH);
	    exit;
	}

	// CLICK ANALYZER用埋め込み
	$this->tpl_clickAnalyzer = "";
	if (isset($CLICK_ANALYZER_BRAND[$this->arrForm["brand_id"]])) {
	    $this->tpl_clickAnalyzer = $CLICK_ANALYZER_BRAND[$this->arrForm["brand_id"]];
    }

    // 計測タグセット
    $this->lfSetTag($this,$this->arrForm["brand_code"]);

	$arrProductId = array();
	if (is_array($arrBrand)) {
	    if ($arrBrand[0]["parent_id"]) {
		// 親がある場合、親ブランド情報取得
		$arrParentBrand = $this->lfGetBrand($arrBrand[0]["parent_id"]);
	    } else {
		// 子ブランド情報取得
		$arrChildBrand = $this->lfGetBrand($arrBrand[0]["brand_id"], true);
		if (!SC_Utils_Ex::isBlank($arrChildBrand)) {
		    $this->tpl_child_brand = true;
		    // ブランドに属する商品情報取得
		    foreach ($arrChildBrand as $val) {
			$brand_id = $val["brand_id"];
			$arrChildBrandProduct[$brand_id] = $this->lfGetBrandProduct($brand_id);
			foreach ($arrChildBrandProduct[$brand_id] as $data) {
			    $productClassId = $data["product_class_id"];
			    // 商品企画情報取得
			    $arrChildBrandProductClass[$brand_id][$productClassId] = 
				$objProduct->getProductsClass($productClassId);
			    // 商品ステータス取得用に商品IDをセット
			    $arrProductId[] = $data["product_id"];
			}
		    }
		}
	    }
	    // 自分に属するブランド商品情報取得
	    $brandId = $arrBrand[0]["brand_id"];
	    $arrBrandProduct[$brandId] = $this->lfGetBrandProduct($brandId);
	    foreach ($arrBrandProduct[$brandId] as $data) {
		$productClassId = $data["product_class_id"];
		// 商品企画情報取得
		$arrBrandProductClass[$productClassId] = 
		    $objProduct->getProductsClass($productClassId);
		// 商品ステータス取得用に商品IDをセット
		$arrProductId[] = $data["product_id"];
	    }
	}

	// ブランド情報をテンプレート用変数に代入
	if ($this->tpl_child_brand) {
	    $this->arrBrandProduct = $arrChildBrandProduct;
	    $this->arrBrandProductClass = $arrChildBrandProductClass;
	    $this->arrChildBrand = $arrChildBrand;
	} else {
	    $this->arrBrandProduct = $arrBrandProduct;
	    $this->arrBrandProductClass = $arrBrandProductClass;
	    $this->arrChildBrand = $arrBrand;
	}

	$this->arrBrand = $arrBrand[0];
	$this->arrParentBrand = $arrParentBrand[0];

	// 商品ステータス取得
	$this->productStatus = $objProduct->getProductStatus($arrProductId
							     , SC_Display_Ex::detectDevice());

	// 画面制御関連セット
	$objProduct->setProductsClassByProductIds($arrProductId);

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
	$this->tpl_stock_find = $objProduct->stock_find;
	$this->tpl_product_class_id = $objProduct->product_class_id;
	$this->tpl_product_type = $objProduct->product_type;
	// 在庫切れ時の表示文言を取得
	$this->tpl_stock_status_name = $objProduct->stock_status_name;
	// 定期フラグ
	$this->tpl_teiki_flg = $objProduct->teiki_flg;
	// 数量
	$this->tpl_arrQuantity = $objProduct->arrQuantity;

	    $this->tpl_javascript .= 'var productsClassCategories = ' . SC_Utils_Ex::jsonEncode($objProduct->classCategories) . ';';
	    //onloadスクリプトを設定
	    foreach ($arrProductId as $arrProduct) {
		$js_fnOnLoad .= "fnSetClassCategories(document.product_form{$arrProduct});";
	    }

        switch($this->getMode()){
	    /*
	      case "json":
	      $this->arrProducts = $this->setStatusDataTo($this->arrProducts, $this->arrSTATUS, $this->arrSTATUS_IMAGE);
	      $this->arrProducts = $objProduct->setPriceTaxTo($this->arrProducts);
	      echo SC_Utils_Ex::jsonEncode($this->arrProducts);
	      exit;
	      break;
	    */

	default:
	    // 検索結果が一件の場合、商品詳細ページヘ遷移
 	    if (count($arrProductId) == 1) {
 		$id = $arrProductId[0];
 		SC_Response_Ex::sendRedirect
 		    (sprintf("detail.php?product_id=%d", $id));
 		exit;
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
		$js_fnOnLoad .= $this->lfSetSelectedData($this->arrBrandProduct, $this->arrForm, $arrErr, $target_product_id);
	    } else {
		// カート「戻るボタン」用に保持
		$netURL = new Net_URL();
		//該当メソッドが無いため、$_SESSIONに直接セット
		$_SESSION['cart_referer_url'] = $netURL->getURL();
	    }

	    $this->tpl_javascript   .= 'function fnOnLoad(){' . $js_fnOnLoad . '}';
	    $this->tpl_onload       .= 'fnOnLoad(); ';
	    break;

	}

	$this->tpl_rnd          = SC_Utils_Ex::sfGetRandomString(3);

        // 商品一覧パンくずリストを作成
        if(SC_Display_Ex::detectDevice() == DEVICE_TYPE_PC) {
	    $TopicPath = '<a href="' . ROOT_URLPATH . 'index.php">' . TPL_PC_HOME_NAME . '</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;';
	} else {
	    $TopicPath = '<a href="' . ROOT_URLPATH . 'index.php">HOME</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;';
	}
	if (!SC_Utils_Ex::isBlank($arrParentBrand)) {
	    // 子ブランドの場合
	    $TopicPath .= '<a href="./brand.php?brand_id=' .$arrParentBrand[0]["brand_id"]. '">'. $arrParentBrand[0]["brand_name"] . '</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;';
	}
	$TopicPath .= $arrBrand[0]["brand_name"];

        $this->TopicPath = $TopicPath;
        // パンくずリスト作成終わり

	// ページタイトル設定
	$this->tpl_subtitle = $arrBrand[0]["brand_name"];

        // ログイン判定 
        if($objCustomer->isLoginSuccess(true)) {
            $this->tpl_customer_kbn = $objCustomer->getValue('customer_kbn');
        } else {
            $this->tpl_customer_kbn = null;
        }
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

    /**
     * DBからブランドマスタデータを取得する
     * 
     * @param integer $brand_id ブランドID
     * @param bool $child_flg 子ブランド取得時 true
     * @return array ブランドマスタデータ
     */
    function lfGetBrand($brand_id, $child_flg = false) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

		// ブランドコードがない場合処理しない
		if (SC_Utils_Ex::isBlank($brand_id)) {
			return false;
		}

		// 子ブランド取得時、検索条件セット
		if ($child_flg) {
			$col = "parent_id";
		} else {
			$col = "brand_id ";
		}

        $sql =<<< __EOS
SELECT bd.brand_id
     , bd.brand_name
     , bd.brand_code
     , bd.rank
     , DATE_FORMAT(bd.disp_start_date, '%Y/%m/%d') AS disp_start_date
     , DATE_FORMAT(bd.disp_end_date, '%Y/%m/%d') AS disp_end_date
     , bd.category_id
     , cg.category_code
     , cg.category_name
     , bd.product_disp_num
     , bd.img_disp_num
     , bd.metatag
     , bd.parent_id
     , pb.brand_code AS parent_brand_code
     , pb.brand_name AS parent_brand_name
     , bd.pc_comment
     , bd.pc_free_space1
     , bd.pc_free_space2
     , bd.pc_free_space3
     , bd.pc_free_space4
     , bd.pc_free_space5
     , bd.sp_comment
     , bd.sp_free_space1
     , bd.sp_free_space2
     , bd.sp_free_space3
     , bd.sp_free_space4
     , bd.sp_free_space5
     , bd.mb_comment
     , bd.mb_free_space1
     , bd.mb_free_space2
     , bd.mb_free_space3
     , bd.mb_free_space4
     , bd.mb_free_space5
  FROM dtb_brand bd
  LEFT JOIN dtb_category cg
    ON bd.category_id = cg.category_id
   AND cg.del_flg = 0
  LEFT JOIN dtb_brand pb
    ON bd.parent_id = pb.brand_id
   AND pb.del_flg = 0
 WHERE bd.del_flg = 0
   AND bd.{$col} = ?
   AND (DATE_FORMAT(bd.disp_start_date, '%Y%m%d') <= DATE_FORMAT(now(), '%Y%m%d')
       OR bd.disp_start_date IS NULL)
   AND (DATE_FORMAT(bd.disp_end_date, '%Y%m%d') >= DATE_FORMAT(now(), '%Y%m%d')
       OR bd.disp_end_date IS NULL)
__EOS;

       $results = $objQuery->getAll($sql, array($brand_id));

        return $results;
    }

    /**
     * DBからブランドマスタに属する商品を取得する
     * 
     * @param integer $brand_id ブランドID
     * @param bool $child_flg 子ブランド取得時 true
     * @return array ブランドマスタデータ
     */
    function lfGetBrandProduct($brand_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

		// ブランドコードがない場合処理しない
		if (SC_Utils_Ex::isBlank($brand_id)) {
			return false;
		}

		$sql =<<<EOF
SELECT
    P.*,
    PC.*,
    BP.brand_id,
    BP.product_code
FROM
    dtb_products P
INNER JOIN (
    SELECT product_code,
           product_id,
           product_class_id,
           MIN(product_code) AS product_code_min,
           MAX(product_code) AS product_code_max,
           MIN(price01) AS price01_min,
           MAX(price01) AS price01_max,
           MIN(price02) AS price02_min,
           MAX(price02) AS price02_max,
           MAX(deliv_judgment) AS deliv_judgment,
           MIN(stock) AS stock_min,
           MAX(stock) AS stock_max,
           MIN(stock_unlimited) AS stock_unlimited_min,
           MAX(stock_unlimited) AS stock_unlimited_max,
           MAX(sale_limit) AS sale_limit_max,
           MIN(sale_limit) AS sale_limit_min,
           MAX(sale_minimum_number) AS sale_minimum_number_max,
           MIN(sale_minimum_number) AS sale_minimum_number_min,
           MAX(point_rate) AS point_rate,
           MAX(deliv_fee) AS deliv_fee,
           MAX(deliv_fee_quantity) AS deliv_fee_quantity,
           MIN(teiki_flg) AS teiki_flg,
           COUNT(*) as class_count
     FROM dtb_products_class
     WHERE del_flg = 0
     AND sell_flg = 1
     GROUP BY product_code
) PC
		ON P.product_id = PC.product_id
INNER JOIN
	dtb_brand_products BP
		ON PC.product_code = BP.product_code
		AND BP.del_flg = 0
WHERE
	BP.brand_id = ?
   AND (DATE_FORMAT(P.disp_start_date, '%Y%m%d') <= DATE_FORMAT(now(), '%Y%m%d')
       OR P.disp_start_date IS NULL)
AND P.status = 1
ORDER BY BP.rank
EOF;


        $results = $objQuery->getAll($sql, array($brand_id));

        return $results;
    }

    /**
     * 各種計測タグをセット
     *
     */
    function lfSetTag(&$thisPage, $brand_code) {
        global $BLADE_TAG_BRAND;
        global $MARKETONE_TAG_BRAND;

        $objTagView = new SC_SiteView_Ex();

        // タグテンプレートパス取得
        $tag_tpl_dir = "";

        // 端末判定
        $device = SC_Display_Ex::detectDevice();
        switch ($device){
        case DEVICE_TYPE_SMARTPHONE:
                // スマートフォン
                $tag_tpl_dir = SMARTPHONE_TAG_TEMPLATE_REALDIR;
                break;
            case DEVICE_TYPE_PC:
                // PC 
                $tag_tpl_dir = TAG_TEMPLATE_REALDIR;
                break;
        }

        // 各種タグテンプレートをセット
        if (!empty($tag_tpl_dir)) {
            // BLADEタグ
            if (isset($BLADE_TAG_BRAND[$brand_code]) &&
                $BLADE_TAG_BRAND[$brand_code] === true) {
                $thisPage->tpl_tag_blade =
                    $objTagView->fetch($tag_tpl_dir . "blade.tpl");
            }

            // MarketOneタグ
            if (isset($MARKETONE_TAG_BRAND[$brand_code]) &&
                $MARKETONE_TAG_BRAND[$brand_code] === true) {
                $thisPage->tpl_tag_market_one =
                    $objTagView->fetch($tag_tpl_dir . "market_one.tpl");
            }
        }
    }
}
?>
