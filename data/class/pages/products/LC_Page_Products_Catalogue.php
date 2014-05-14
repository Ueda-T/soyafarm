<?php
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

class LC_Page_Products_Catalogue extends LC_Page_Ex
{
    var $tpl_mainpage = "products/catalogue.tpl";

    function doValidToken($is_admin = false) {
	// Nothing to do.
    }

    function init() {
	parent::init();
	$this->tpl_mainpage = "products/catalogue.tpl";
	$this->tpl_title = "商品番号から注文";
    }

    function process() {
        $this->action();
        $this->sendResponse();
    }

    function action() {
	// キャンペーンコードが指定されている場合はセッションに保持
	if ($_REQUEST[CAMPAIGN_PARAM_STR]) {
	    $_SESSION["CAMPAIGN_CODE"] = $_REQUEST[CAMPAIGN_PARAM_STR];
	}

	global $CLICK_ANALYZER_STATIC;

	// CLICK ANALYZER用埋め込み
	$this->tpl_clickAnalyzer = "";
	if (isset($CLICK_ANALYZER_STATIC["catalogue"])) {
	    $this->tpl_clickAnalyzer = $CLICK_ANALYZER_STATIC["catalogue"];
	}

	$objCartSess = new SC_CartSession_Ex();
	$redirect = false;
	$this->tpl_err_msg = "";

	for ($i = 1; $i <= 5; ++$i) {
	    $this->arrForm[sprintf("how%d", $i)] = intval(0);
	}

	if (empty($_POST)) {
	    return;
	}

	$product_cnt = 0;
	for ($i = 0; $i < 5; ++$i) {
	    $elem = sprintf("quantity%d", $i + 1);
	    $quantity = 1;
	    if (isset($_POST[$elem]) && $_POST[$elem]) {
		$quantity = $_POST[$elem];
	    }
	    $elem = sprintf("how%d", $i + 1);
	    $how = $this->arrForm[$elem] = $_POST[$elem];
	    $elem = sprintf("goods%d", $i + 1);
	    $goods = $this->arrForm[$elem] = $_POST[$elem];
	    if (empty($goods)) {
		continue;
	    }
	    $product_cnt++;
	    
	    // 商品番号を商品規格IDに変換する
	    $id = $this->goodsNo2productClassId($goods);
	    if (empty($id)) {
		$this->arrErr[$elem] = "ご指定の商品番号に該当する商品はございません。";
		continue;
	    }

	    // 該当の商品が定期可かチェック
	    $objProduct = new SC_Product_Ex();
	    $teiki_flg = $objProduct->checkTeikiFlg($id);
	    if ($teiki_flg === false
		&& $how == REGULAR_PURCHASE_FLG_ON) {

		$this->arrErr[$elem] = "定期購入未対象の商品です。単回を選択してください。";
		continue;
	    }

	    $objCartSess->addProduct($id, $quantity, $how);
	    $redirect = true;
	}

	if ($product_cnt < 1) {
	    $this->tpl_err_msg = "※商品番号を入力してください。";
	}

	if ($redirect == true && count($this->arrErr) == 0) {
	    SC_Response_Ex::sendRedirect(CART_URLPATH);
	    exit;
	}
    }

    function goodsNo2productClassId($goodsNo) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
	$sql =<<<__EOS
SELECT
    product_class_id
FROM
    dtb_products_class AS pc
INNER JOIN
    dtb_products AS p
    ON p.product_id = pc.product_id
    AND p.del_flg = 0
    AND p.status = 1
    AND date_format(now(), '%Y%m%d') <=
        date_format(ifnull(p.sale_end_date, now()), '%Y%m%d')
    AND date_format(now(), '%Y%m%d') >=
        date_format(ifnull(p.disp_start_date, now()), '%Y%m%d')
WHERE
    pc.product_code = "$goodsNo"
AND pc.del_flg = 0
AND ifnull(pc.stock, 1) > 0
__EOS;

        $r = $objQuery->getAll($sql);
	if ($r[0]['product_class_id']) {
	    return $r[0]['product_class_id'];
	}

	return null;
    }
}
?>
