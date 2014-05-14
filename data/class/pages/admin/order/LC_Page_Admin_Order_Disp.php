<?php
require_once CLASS_REALDIR . 'pages/admin/order/LC_Page_Admin_Order_Edit.php';

/**
 * 受注情報表示 のページクラス.
 */
class LC_Page_Admin_Order_Disp extends LC_Page_Admin_Order_Ex {
    var $arrShippingKeys = array(
        'shipping_id',
        'shipping_name',
        'shipping_kana',
        'shipping_tel',
        'shipping_fax',
        'shipping_pref',
        'shipping_zip',
        'shipping_addr01',
        'shipping_addr02',
        'shipping_date_year',
        'shipping_date_month',
        'shipping_date_day',
        'time_id',
        'fee',
        'shipping_num',
        'box_flg',
    );

    var $arrShipmentItemKeys = array(
        'shipment_product_class_id',
        'shipment_product_code',
        'shipment_product_name',
        'shipment_classcategory_name1',
        'shipment_classcategory_name2',
        'shipment_price',
        'shipment_quantity',
    );

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'order/disp.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subnavi = '';
        $this->tpl_subno = '';
        $this->tpl_subtitle = '受注情報表示';

        $master = new SC_DB_MasterData_Ex();
        $this->arrPref = $master->getMasterData('mtb_pref');
        $this->arrORDERSTATUS = $master->getMasterData("mtb_order_status");
        $this->arrDeviceType = $master->getMasterData('mtb_device_type');
        $this->arrBoxFlg = $master->getMasterData("mtb_box_flg");

        // 支払い方法の取得
        $this->arrPayment = SC_Helper_DB_Ex::sfGetIDValueList(
                "dtb_payment", "payment_id", "payment_method");

        // 配送業者の取得
        $this->arrDeliv = SC_Helper_DB_Ex::sfGetIDValueList(
            "dtb_deliv", "deliv_id", 'name');  

        $this->httpCacheControl('nocache');
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
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objFormParam = new SC_FormParam_Ex();
        
        // パラメータ情報の初期化
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_REQUEST);
        $objFormParam->convParam();

        $order_id = $objFormParam->getValue('order_id');
        if (!SC_Utils_Ex::isBlank($order_id)) {
	    $this->arrSearchHidden = $objFormParam->getSearchArray();
	    $this->setOrderToFormParam($objFormParam, $order_id);
	    $this->arrForm = $objFormParam->getFormParamList();
	    $this->arrAllShipping = $objFormParam->getSwapArray
		(array_merge($this->arrShippingKeys,
			     $this->arrShipmentItemKeys));

	    $this->arrShippingId = array();
	    foreach($this->arrForm["shipping_id"]["value"] as $val){
		array_push($this->arrShippingId, $val);
	    }

	    $this->arrDelivTime = $objPurchase->getDelivTime
		($objFormParam->getValue('deliv_id'));

	    // 適用プロモーションの取得
            $this->arrForm["promotion"]["value"] =
		$this->getPromoString($order_id);

	    $this->setTemplate($this->tpl_mainpage);
	}
    }
    
    /**
     * パラメータ情報の初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        // 検索条件のパラメータを初期化
        parent::lfInitParam($objFormParam);

        // お客様情報
        $objFormParam->addParam("注文者 お名前(姓名)", "order_name");
        $objFormParam->addParam("注文者 お名前(フリガナ・姓名)", "order_kana");
        $objFormParam->addParam("メールアドレス", "order_email");
        $objFormParam->addParam("郵便番号", "order_zip");
        $objFormParam->addParam("都道府県", "order_pref");
        $objFormParam->addParam("住所1", "order_addr01");
        $objFormParam->addParam("住所2", "order_addr02");
        $objFormParam->addParam("電話番号", "order_tel");

        // 受注商品情報
        $objFormParam->addParam("値引き", "discount");
        $objFormParam->addParam("送料", "deliv_fee");
        $objFormParam->addParam("手数料", "charge");

        // ポイント機能ON時のみ
        if (USE_POINT !== false) {
            $objFormParam->addParam("利用ポイント（通常）", "use_point1");
            $objFormParam->addParam("利用ポイント（誕生日）", "use_point2");
        }

        $objFormParam->addParam("配送業者", "deliv_id");
        $objFormParam->addParam("お支払い方法", "payment_id");
        $objFormParam->addParam("対応状況", "status");
        $objFormParam->addParam("お支払方法名称", "payment_method");

        // 受注詳細情報
        $objFormParam->addParam("商品項番", "product_class_id");
        $objFormParam->addParam("商品種別ID", "product_type_id");
        $objFormParam->addParam("単価", "price");
        $objFormParam->addParam("数量", "quantity");
        $objFormParam->addParam("商品ID", "product_id");
        $objFormParam->addParam("商品規格ID", "product_class_id");
        $objFormParam->addParam("ポイント付与率", "point_rate");
        $objFormParam->addParam("商品コード", "product_code");
        $objFormParam->addParam("商品名", "product_name");
        $objFormParam->addParam("規格名1", "classcategory_name1");
        $objFormParam->addParam("規格名2", "classcategory_name2");
        $objFormParam->addParam("メモ", "note");
        $objFormParam->addParam("削除用項番", "delete_no");
        $objFormParam->addParam("コースCD", "course_cd");

        // DB読込用
        $objFormParam->addParam("小計", "subtotal");
        $objFormParam->addParam("合計", "total");
        $objFormParam->addParam("支払い合計", "payment_total");
        $objFormParam->addParam("加算ポイント", "add_point");
        $objFormParam->addParam("誕生日ポイント", "birth_point");
        $objFormParam->addParam("消費税合計", "tax");
        $objFormParam->addParam("最終保持ポイント", "total_point");
        $objFormParam->addParam("顧客ID", "customer_id");
        $objFormParam->addParam("顧客番号", "customer_cd");
        $objFormParam->addParam("顧客ID", "edit_customer_id");
        $objFormParam->addParam("現在のポイント", "customer_point");
        $objFormParam->addParam("受注前ポイント", "point");
        $objFormParam->addParam("注文番号", "order_id");
        $objFormParam->addParam("受注日", "create_date");
        $objFormParam->addParam("発送日", "commit_date");
        $objFormParam->addParam("備考", 'message');
        $objFormParam->addParam("入金日", "payment_date");
        $objFormParam->addParam("アクセス端末", "device_type_id");
        $objFormParam->addParam("顧客区分", "customer_kbn");
	// #141
        $objFormParam->addParam("キャンペーンコード", "campaign_cd");
        $objFormParam->addParam("アンケート", "event_code");
        $objFormParam->addParam("請求書同梱区分", "include_kbn");

        // 複数情報
        $objFormParam->addParam("配送数", "shipping_quantity");
        $objFormParam->addParam("配送ID", "shipping_id");
        $objFormParam->addParam("お名前(姓名)", "shipping_name");
        $objFormParam->addParam("お名前(フリガナ・姓名)", "shipping_kana");
        $objFormParam->addParam("郵便番号", "shipping_zip");
        $objFormParam->addParam("都道府県", "shipping_pref");
        $objFormParam->addParam("住所1", "shipping_addr01");
        $objFormParam->addParam("住所2", "shipping_addr02");
        $objFormParam->addParam("電話番号", "shipping_tel");
        $objFormParam->addParam("お届け時間ID", "time_id");
        $objFormParam->addParam("お届け日(年)", "shipping_date_year");
        $objFormParam->addParam("お届け日(月)", "shipping_date_month");
        $objFormParam->addParam("お届け日(日)", "shipping_date_day");
        $objFormParam->addParam("お届け日", "shipping_date");
        $objFormParam->addParam("配送商品数量", "shipping_product_quantity");
        $objFormParam->addParam("送料", "fee");
        $objFormParam->addParam("配送伝票番号", "shipping_num");
        $objFormParam->addParam("宅配ボックス", "box_flg");

        $objFormParam->addParam("商品規格ID", "shipment_product_class_id");
        $objFormParam->addParam("商品コード", "shipment_product_code");
        $objFormParam->addParam("商品名", "shipment_product_name");
        $objFormParam->addParam("規格名1", "shipment_classcategory_name1");
        $objFormParam->addParam("規格名2", "shipment_classcategory_name2");
        $objFormParam->addParam("単価", "shipment_price");
        $objFormParam->addParam("数量", "shipment_quantity");

        $objFormParam->addParam("商品項番", "no");
        $objFormParam->addParam("追加商品規格ID", "add_product_class_id");
        $objFormParam->addParam("修正商品規格ID", "edit_product_class_id");
        $objFormParam->addParam("アンカーキー", "anchor_key");
    }

    /**
     * 受注データを取得して, SC_FormParam へ設定する.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param integer $order_id 取得元の受注ID
     * @return void
     */
    function setOrderToFormParam(&$objFormParam, $order_id) {
        $objPurchase = new SC_Helper_Purchase_Ex();

        // 受注詳細を設定
        $arrOrderDetail = $objPurchase->getOrderDetailOuter($order_id, false);
        $objFormParam->setParam(SC_Utils_Ex::sfSwapArray($arrOrderDetail));

        $arrShippingsTmp = $objPurchase->getShippings($order_id);
        $arrShippings = array();
        foreach ($arrShippingsTmp as $row) {
            // お届け日の処理
            if (!SC_Utils_Ex::isBlank($row["shipping_date"])) {
                $ts = strtotime($row["shipping_date"]);
                $row['shipping_date_year'] = date('Y', $ts);
                $row['shipping_date_month'] = date('n', $ts);
                $row['shipping_date_day'] = date('j', $ts);
            }
            $arrShippings[$row['shipping_id']] = $row;
        }
        $objFormParam->setValue('shipping_quantity', count($arrShippings));
        $objFormParam->setParam(SC_Utils_Ex::sfSwapArray($arrShippings));

        /*
         * 配送商品を設定
         *
         * $arrShipmentItem['shipment_(key)'][$shipping_id][$item_index] = 値
         * $arrProductQuantity[$shipping_id] = 配送先ごとの配送商品数量
         */
        $arrProductQuantity = array();
        $arrShipmentItem = array();
        foreach ($arrShippings as $shipping_id => $arrShipping) {
            $arrProductQuantity[$shipping_id] = count($arrShipping['shipment_item']);
            foreach ($arrShipping['shipment_item'] as $item_index => $arrItem) {
                foreach ($arrItem as $item_key => $item_val) {
                    $arrShipmentItem['shipment_' . $item_key][$shipping_id][$item_index] = $item_val;
                }
            }
        }
        $objFormParam->setValue('shipping_product_quantity', $arrProductQuantity);
        $objFormParam->setParam($arrShipmentItem);

        /*
         * 受注情報を設定
         * $arrOrderDetail と項目が重複しており, $arrOrderDetail は連想配列の値
         * が渡ってくるため, $arrOrder で上書きする.
         */
        $arrOrder = $objPurchase->getOrder($order_id);
        $objFormParam->setParam($arrOrder);

        // ポイントを設定
        list($db_point, $rollback_point) = SC_Helper_DB_Ex::sfGetRollbackPoint(
            $order_id, $arrOrder['use_point1'], $arrOrder['add_point'], $arrOrder['status']
        );
        $objFormParam->setValue('total_point', $db_point);
        $objFormParam->setValue('point', $rollback_point);

        //if (!in_array($this->getMode(), array('pre_edit','edit')) && !SC_Utils_Ex::isBlank($objFormParam->getValue('customer_id'))) {
        if (!SC_Utils_Ex::isBlank($objFormParam->getValue('customer_id'))) {
            $arrCustomer = SC_Helper_Customer_Ex::sfGetCustomerDataFromId($objFormParam->getValue('customer_id'));
            $objFormParam->setValue('customer_point', $arrCustomer['point']);
            $objFormParam->setValue('birth_point', $arrCustomer['birth_point']);
            $objFormParam->setValue('customer_kbn', $arrCustomer['customer_kbn']);
        }
    }

    /**
     * 適用プロモーションを取得します
     */
    function getPromoString($order_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
	$sql =<<<__EOS
select
    promotion_cd as cd
from
    dtb_order_promotion
where
    order_id = '$order_id'
order by
    promotion_cd
__EOS;
	
        $result = $objQuery->getAll($sql);
	$length = count($result);
	$s = "";

	for ($i = 0; $i < $length; ++$i) {
	    $s .= $result[$i]["cd"];
	    if ($i < ($length - 1)) {
		$s .= ", ";
	    }
	}

	return $s;
    }
}
?>
