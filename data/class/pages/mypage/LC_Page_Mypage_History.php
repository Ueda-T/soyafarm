<?php
require_once CLASS_EX_REALDIR . 'page_extends/mypage/LC_Page_AbstractMypage_Ex.php';

/**
 * 購入履歴 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Mypage_History.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Mypage_History extends LC_Page_AbstractMypage_Ex {
    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mypageno     = 'history';
        $this->tpl_subtitle     = "ご注文内容詳細";
        $this->httpCacheControl('nocache');

        $master = new SC_DB_MasterData_Ex();
        $this->arrMAILTEMPLATE = $master->getMasterData("mtb_mail_template");
        $this->arrPref = $master->getMasterData('mtb_pref');
        $this->arrWDAY = $master->getMasterData("mtb_wday");
        $this->arrProductType = $master->getMasterData("mtb_product_type");
        // 宅配BOX配列
        $this->arrBoxFlg = $master->getMasterData("mtb_box_flg");
        // 配送方法配列(箱ID)
        $this->arrDelivBox = $master->getMasterData("mtb_deliv_box");
        $this->arrOrderStatus = $master->getDbMasterDataNatural
	    ("mtb_order_status", array("id", "name", "image_s", "image_l"));
   }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        parent::process();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    function action() {
		$_SESSION["MYPAGENO"] = $this->tpl_mypageno;

        $objCustomer    = new SC_Customer_Ex();
        $objDb          = new SC_Helper_DB_Ex();
        $objPurchase = new SC_Helper_Purchase_Ex();

        if (!SC_Utils_Ex::sfIsInt($_GET['order_id'])) {
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }

        $order_id        = $_GET['order_id'];

        //受注データの取得
        $this->tpl_arrOrderData =
            $objPurchase->getOrder($order_id, $objCustomer->getValue('customer_id'));

        if (empty($this->tpl_arrOrderData)){
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }

        $this->arrShipping =
            $this->lfGetShippingDate($objPurchase, $order_id, $this->arrWDAY);

        $this->isMultiple       = count($this->arrShipping) > 1;
        // 支払い方法の取得
        $this->arrPayment =
            $objDb->sfGetIDValueList("dtb_payment", "payment_id", "payment_method");

        // 受注商品明細の取得
        $this->tpl_arrOrderDetail = $objPurchase->getOrderDetailOuter($order_id);

        $this->tpl_arrOrderDetail = $this->setProductsDetail($this->tpl_arrOrderDetail);

        // 受注メール送信履歴の取得
        $this->tpl_arrMailHistory = $this->lfGetMailHistory($order_id);

        // 明細書同梱区分値セット
        $this->arrIncludeKbn = array(
            INCLUDE_KBN_BESSOU => '商品と別送(ご注文者様の住所に郵送)',
            INCLUDE_KBN_DOUKON => '商品と同封(お支払い明細書が商品に同梱されます)'
        );
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
     * 受注メール送信履歴の取得
     *
     * @param integer $order_id 注文番号
     * @return array 受注メール送信履歴の内容
     */
    function lfGetMailHistory($order_id) {
        $objQuery   =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<EOF
SELECT
    send_date,
    subject,
    template_id,
    send_id
FROM
    dtb_mail_history
WHERE
    order_id = $order_id
ORDER BY send_date DESC
EOF;
        return $objQuery->getAll($sql);
    }

    /**
     * 受注お届け先情報の取得
     *
     * @param $objPurchase object SC_Helper_Purchaseクラス
     * @param $order_id integer 注文番号
     * @param $arrWDAY array 曜日データの配列
     * @return array お届け先情報
     */
    function lfGetShippingDate(&$objPurchase, $order_id, $arrWDAY) {
        $arrShipping = $objPurchase->getShippings($order_id);

        foreach($arrShipping as $shipping_index => $shippingData) {
            // 伝票番号存在時は問い合わせURLをセット
            if (!empty($shippingData['shipping_num'])) {
                // 配送id取得
                $deliv_id = $shippingData['deliv_id'];
                $objQuery =& SC_Query_Ex::getSingletonInstance();
                // 問い合わせURL取得
                $confirm_url = $objQuery->getOne("SELECT confirm_url FROM dtb_deliv WHERE deliv_id = ?", array($deliv_id));
                $arrShipping[$shipping_index]['confirm_url'] = $confirm_url;
            }

            foreach($shippingData as $key => $val) {
                if($key == 'shipping_date' && SC_Utils_Ex::isBlank($val) == false) {
                    // お届け日を整形
                    list($y, $m, $d, $w) =
                        explode(" ", date("Y m d" , strtotime($val)));

                    $arrShipping[$shipping_index]['shipping_date'] =
                        sprintf("%04d年%02d月%02d日", $y, $m, $d, $arrWDAY[$w]);
                }
            }
        }
        return $arrShipping;
    }
    
    /**
     * 購入履歴商品に画像をセット
     *
     * @param $arrOrderDetail 購入履歴の配列
     * @return array 画像をセットした購入履歴の配列
     */
    function setMainListImage($arrOrderDetails) {
        $i = 0;
        foreach ($arrOrderDetails as $arrOrderDetail) {

            $arrProduct = $this->getArrayImage($arrOrderDetail['product_id']);

            $arrOrderDetails[$i]['main_list_image'] = $arrProduct[0]['main_list_image'];

            $i++;
        }
        return $arrOrderDetails;
    }

    /**
     * 購入履歴商品に商品販売可否フラグをセット
     *
     * @param $arrOrderDetail 購入履歴の配列
     * @return array 画像をセットした購入履歴の配列
     */
    function setProductsDetail($arrOrderDetails) {
        $i = 0;

        foreach ($arrOrderDetails as $arrOrderDetail) {

            if (!(empty($arrOrderDetail['product_class_id']))){
                $arrProduct = $this->getArrayProductsDetail($arrOrderDetail['product_class_id']);
                if (!(empty($arrProduct))) {
                    // 販売可否フラグ（1:有効 0:無効）
                    $product_valid_flg = 1;
                    // サンプルフラグ・プレゼントフラグが立っているものは販売不可能
                    if ($arrProduct[0]['sample_flg'] == 1) {
                        $product_valid_flg = 0;
                    }
                    if ($arrProduct[0]['present_flg'] == 1) {
                        $product_valid_flg = 0;
                    }
                    // 販売期間外は販売不可能
                    if (!(empty($arrProduct[0]['sale_start_date'])) && $arrProduct[0]['sale_start_date'] > date("Ymd")) {
                        $product_valid_flg = 0;
                    }
                    if (!(empty($arrProduct[0]['sale_end_date'])) && $arrProduct[0]['sale_end_date'] < date("Ymd")) {
                        $product_valid_flg = 0;
                    }
                    // 販売対象フラグ判定
                    if ($arrProduct[0]['sell_flg'] != SELL_FLG_ON) {
                        $product_valid_flg = 0;
                    }
                    // 公開ステータス判定
                    if ($arrProduct[0]['status'] == DEFAULT_PRODUCT_DISP) {
                        $product_valid_flg = 0;
                    }
                    // 掲載開始日判定
                    if (!(empty($arrProduct[0]['disp_start_date'])) &&
                        $arrProduct[0]['disp_start_date'] > date("Ymd")) {
                        $product_valid_flg = 0;
                    }

                } else {
                    $product_valid_flg = 0;
                }
            } else {
                $product_valid_flg = 0;
            }
            $arrOrderDetails[$i]['product_valid_flg'] = $product_valid_flg;

            $i++;
        }
        return $arrOrderDetails;
    }

    /**
     * 商品詳細情報を取得する
     *
     * @param  $product_id 商品ID
     * @return $arrImage 商品画像情報の配列
     */
    function getArrayProductsDetail ($product_class_id) {
        
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $arrProductsDetail = array();

        $sql =<<<EOF
SELECT
    dtb_products_class.sample_flg,
    dtb_products_class.present_flg,
    dtb_products_class.sell_flg,
    dtb_products.status,
    DATE_FORMAT(dtb_products.disp_start_date, '%Y%m%d') AS disp_start_date,
    DATE_FORMAT(dtb_products.sale_start_date, '%Y%m%d') AS sale_start_date,
    DATE_FORMAT(dtb_products.sale_end_date, '%Y%m%d') AS sale_end_date
FROM
    dtb_products_class INNER JOIN dtb_products
 ON dtb_products_class.product_id = dtb_products.product_id
WHERE
    dtb_products_class.product_class_id = $product_class_id
AND dtb_products.del_flg = 0
EOF;
        return $objQuery->getAll($sql);
    }
}
