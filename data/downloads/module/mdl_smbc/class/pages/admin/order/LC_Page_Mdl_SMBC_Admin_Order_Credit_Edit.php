<?php
// {{{ requires
require_once(CLASS_REALDIR . "pages/LC_Page.php");
require_once(MODULE_REALDIR . 'mdl_smbc/inc/include.php');
require_once(MDL_SMBC_CLASS_PATH . 'SC_Mdl_SMBC.php');
require_once(MDL_SMBC_CLASS_PATH . 'SC_SMBC.php');
/**
 * 受注修正 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Mdl_SMBC_Admin_Order_Credit_Edit extends LC_Page {

    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = MDL_SMBC_TEMPLATE_PATH . 'admin/order/edit.tpl';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrORDERSTATUS = $masterData->getMasterData("mtb_order_status");
        $this->arrDeviceType = $masterData->getMasterData('mtb_device_type');

        $objDate = new SC_Date_Ex(RELEASE_YEAR);
        $this->arrYearShippingDate = $objDate->getYear('', date('Y'), '');
        $this->arrMonthShippingDate = $objDate->getMonth(true);
        $this->arrDayShippingDate = $objDate->getDay(true);

        // 支払い方法の取得
        $this->arrPayment = SC_Helper_DB_Ex::sfGetIDValueList("dtb_payment", "payment_id", "payment_method");

        // 配送業者の取得
        $this->arrDeliv = SC_Helper_DB_Ex::sfGetIDValueList("dtb_deliv", "deliv_id", 'name');

        $this->httpCacheControl('nocache');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->objSmbc = new SC_SMBC();
        $objView = new SC_AdminView();
        $objSess = new SC_Session();

        $objPurchase = new SC_Helper_Purchase_Ex();
        $objFormParam = new SC_FormParam_Ex();

        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_REQUEST);
        $objFormParam->convParam();
        $order_id = $objFormParam->getValue('order_id');
        $this->tpl_order_id = $order_id;

        // DBから受注情報を読み込む
        $this->setOrderToFormParam($objFormParam, $order_id);

        if(!empty($_POST['mode'])){
            // POST情報で上書き
            $objFormParam->setParam($_POST);
            // 入力値の変換
            $objFormParam->convParam();
            $this->arrErr = $this->lfCheckError($objFormParam);

            if($_POST['mode'] == 'edit' && count($this->arrErr) == 0){

                if($this->credit_status == "1"){
                    $version = MDL_SMBC_YOSHIN_PART_CANCEL_LINK_CREDIT_VERSION;
                }elseif($this->credit_status == "2"){
                    $version = MDL_SMBC_SALES_PART_CANCEL_LINK_CREDIT_VERSION;
                }
                // 決済ステーション連携
                $this->lfSendMode($this->tpl_order_id, $version);
            }
        }

        $this->arrForm = $objFormParam->getFormParamList();
        $this->arrDelivTime = $objPurchase->getDelivTime($objFormParam->getValue('deliv_id'));

        $this->tpl_onload .= $this->getAnchorKey($objFormParam);
        $this->arrInfo = SC_Helper_DB_Ex::sfGetBasisData();

        $objView->assignObj($this);
        $objView->display($this->tpl_mainpage);
    }


    function lfInitParam(&$objFormParam) {
        // 検索条件のパラメータを初期化

        // お客様情報
        $objFormParam->addParam("顧客名1", "order_name01");
        $objFormParam->addParam("顧客名2", "order_name02");
        $objFormParam->addParam("顧客名カナ1", "order_kana01");
        $objFormParam->addParam("顧客名カナ2", "order_kana02");
        $objFormParam->addParam("メールアドレス", "order_email");
        $objFormParam->addParam("郵便番号1", "order_zip01");
        $objFormParam->addParam("郵便番号2", "order_zip02");
        $objFormParam->addParam("都道府県", "order_pref");
        $objFormParam->addParam("住所1", "order_addr01");
        $objFormParam->addParam("住所2", "order_addr02");
        $objFormParam->addParam("電話番号1", "order_tel01");
        $objFormParam->addParam("電話番号2", "order_tel02");
        $objFormParam->addParam("電話番号3", "order_tel03");

        // お届け先情報
        $objFormParam->addParam("配送数", "shipping_quantity", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"), 1);
        $objFormParam->addParam("配送ID", "shipping_id", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"), 0);
        $objFormParam->addParam("お名前1", "shipping_name01", STEXT_LEN, 'KVa', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("お名前2", "shipping_name02", STEXT_LEN, 'KVa', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("お名前(フリガナ・姓)", "shipping_kana01", STEXT_LEN, 'KVCa', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("お名前(フリガナ・名)", "shipping_kana02", STEXT_LEN, 'KVCa', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("郵便番号1", "shipping_zip01", ZIP01_LEN, 'n', array("NUM_CHECK", "NUM_COUNT_CHECK"));
        $objFormParam->addParam("郵便番号2", "shipping_zip02", ZIP02_LEN, 'n', array("NUM_CHECK", "NUM_COUNT_CHECK"));
        $objFormParam->addParam("都道府県", "shipping_pref", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("住所1", "shipping_addr01", MTEXT_LEN, 'KVa', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("住所2", "shipping_addr02", MTEXT_LEN, 'KVa', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("電話番号1", "shipping_tel01", TEL_ITEM_LEN, 'n', array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $objFormParam->addParam("電話番号2", "shipping_tel02", TEL_ITEM_LEN, 'n', array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $objFormParam->addParam("電話番号3", "shipping_tel03", TEL_ITEM_LEN, 'n', array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $objFormParam->addParam("お届け時間ID", "time_id", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("お届け日(年)", "shipping_date_year", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("お届け日(月)", "shipping_date_month", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("お届け日(日)", "shipping_date_day", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("お届け日", "shipping_date", STEXT_LEN, 'KVa', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("配送商品数量", "shipping_product_quantity", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("商品規格ID", "shipment_product_class_id", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("商品コード", "shipment_product_code");
        $objFormParam->addParam("商品名", "shipment_product_name");
        $objFormParam->addParam("規格名1", "shipment_classcategory_name1");
        $objFormParam->addParam("規格名2", "shipment_classcategory_name2");
        $objFormParam->addParam("単価", "shipment_price", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
        $objFormParam->addParam("数量", "shipment_quantity", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"), '0');



        // 受注商品情報
        $objFormParam->addParam("減額金額", "add_discount", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
        $objFormParam->addParam("値引", "discount");
        $objFormParam->addParam("元値引", "org_discount");
        $objFormParam->addParam("送料", "deliv_fee");
        $objFormParam->addParam("手数料", "charge");

        // ポイント機能ON時のみ
        if( USE_POINT === true ){
            $objFormParam->addParam("利用ポイント", "use_point");
        }

        $objFormParam->addParam("お支払い方法", "payment_id");
        $objFormParam->addParam("お届け時間ID", "deliv_id");
        $objFormParam->addParam("対応状況", "status");
        $objFormParam->addParam("お届け日", "deliv_date");
        $objFormParam->addParam("お支払方法名称", "payment_method");
        $objFormParam->addParam("お届け時間", "deliv_time");

        // 受注詳細情報
        $objFormParam->addParam("単価", "price");
        $objFormParam->addParam("個数", "quantity");
        $objFormParam->addParam("商品ID", "product_id");
        $objFormParam->addParam("ポイント付与率", "point_rate");
        $objFormParam->addParam("商品コード", "product_code");
        $objFormParam->addParam("商品名", "product_name");
        $objFormParam->addParam("規格1", "classcategory_id1");
        $objFormParam->addParam("規格2", "classcategory_id2");
        $objFormParam->addParam("規格名1", "classcategory_name1");
        $objFormParam->addParam("規格名2", "classcategory_name2");
        $objFormParam->addParam("メモ", "note", MTEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
        // DB読込用
        $objFormParam->addParam("小計", "subtotal");
        $objFormParam->addParam("合計", "total");
        $objFormParam->addParam("支払い合計", "payment_total");
        $objFormParam->addParam("加算ポイント", "add_point");
        $objFormParam->addParam("お誕生日ポイント", "birth_point");
        $objFormParam->addParam("消費税合計", "tax");
        $objFormParam->addParam("最終保持ポイント", "total_point");
        $objFormParam->addParam("顧客ID", "customer_id");
        $objFormParam->addParam("現在のポイント", "point");
        $objFormParam->addParam("注文番号", "order_id");
        $objFormParam->addParam("受注日", "create_date");
        $objFormParam->addParam("発送日", "commit_date");
        $objFormParam->addParam("入金日", "payment_date");
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
        $arrOrderDetail = $objPurchase->getOrderDetail($order_id, false);
        $objFormParam->setParam(SC_Utils_Ex::sfSwapArray($arrOrderDetail));

        $arrShippings = $objPurchase->getShippings($order_id);
        // お届け日の処理
        foreach (array_keys($arrShippings) as $key) {
            $shipping =& $arrShippings[$key];
            if (!SC_Utils_Ex::isBlank($shipping["shipping_date"])) {
                $ts = strtotime($shipping["shipping_date"]);
                $arrShippings[$key]['shipping_date_year'] = date('Y', $ts);
                $arrShippings[$key]['shipping_date_month'] = date('n', $ts);
                $arrShippings[$key]['shipping_date_day'] = date('j', $ts);
            }
        }
        $objFormParam->setValue('shipping_quantity', count($arrShippings));
        $objFormParam->setParam(SC_Utils_Ex::sfSwapArray($arrShippings));

        /*
         * 配送商品を設定
         *
         * $arrShipmentItem['shipment_(key)'][$shipping_index][$item_index] = 値
         * $arrProductQuantity[$shipping_index] = 配送先ごとの配送商品数量
         */
        $arrProductQuantity = array();
        $arrShipmentItem = array();
        foreach ($arrShippings as $shipping_index => $arrShipping) {
            $arrProductQuantity[$shipping_index] = count($arrShipping['shipment_item']);
            foreach ($arrShipping['shipment_item'] as $item_index => $arrItem) {
                foreach ($arrItem as $item_key => $item_val) {
                    $arrShipmentItem['shipment_' . $item_key][$shipping_index][$item_index] = $item_val;
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
        $objFormParam->setValue('org_discount', $arrOrder['discount']);
        $objFormParam->setParam($arrOrder);

        // XXX ポイントを設定
        list($db_point, $rollback_point) = SC_Helper_DB_Ex::sfGetRollbackPoint($order_id, $arrOrder['use_point'], $arrOrder['add_point']);
        $objFormParam->setValue('total_point', $rollback_point);
        $objFormParam->setValue('point', $db_point);

        if (!SC_Utils_Ex::isBlank($objFormParam->getValue('customer_id'))) {
            $this->setCustomerTo($objFormParam->getValue('customer_id'),
                                 $objFormParam);
        }

        // メモ項目のデフォルト分を表示
        if (SC_Utils_Ex::isBlank($objFormParam->getValue('note')) == true) {
            $objFormParam->setValue('note', "記入例)YYYY年MM月DD日 ○○　○○様\n<請求金額変更理由>のため、請求金額x,xxx円をy,yyy円へ変更");
        }

        // 元の請求金額
        $this->org_payment_total = $arrOrder["payment_total"];

        // 請求ステータス取得
        $objQuery = new SC_Query();
        $where = "order_id = ?";
        $arrSMBC = $objQuery->select("credit_status", "dtb_mdl_smbc_order", $where, array($order_id));
        $this->credit_status = $arrSMBC[0]["credit_status"];
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
     * 顧客情報をフォームに設定する.
     *
     * @param integer $customer_id 顧客ID
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function setCustomerTo($customer_id, &$objFormParam) {
        $arrCustomer = SC_Helper_Customer_Ex::sfGetCustomerDataFromId($customer_id);
        foreach ($arrCustomer as $key => $val) {
            $objFormParam->setValue('order_' . $key, $val);
        }
        $objFormParam->setValue('customer_id', $customer_id);
        $objFormParam->setValue('customer_point', $arrCustomer['point']);
    }

    /**
     * アンカーキーを取得する.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return アンカーキーの文字列
     */
    function getAnchorKey(&$objFormParam) {
        $ancor_key = $objFormParam->getValue('anchor_key');
        if (!SC_Utils_Ex::isBlank($ancor_key)) {
            return "location.hash='#" . htmlentities(urlencode($ancor_key), ENT_QUOTES) . "'";
        }
        return '';
    }

    /**
     * 入力内容のチェックを行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return array エラーメッセージの配列
     */
     function lfCheckError(&$objFormParam) {
        $arrErr = $objFormParam->checkError();

        if (!SC_Utils_Ex::isBlank($objErr->arrErr)) {
            return $arrErr;
        }

        $arrValues = $objFormParam->getHashArray();

        // 商品の種類数
        $max = count($arrValues['quantity']);
        $subtotal = 0;
        $totalpoint = 0;
        $totaltax = 0;
        for($i = 0; $i < $max; $i++) {
            // 小計の計算
            $subtotal += SC_Helper_DB_Ex::sfCalcIncTax($arrValues['price'][$i]) * $arrValues['quantity'][$i];
            // 小計の計算
            $totaltax += SC_Helper_DB_Ex::sfTax($arrValues['price'][$i]) * $arrValues['quantity'][$i];
            // 加算ポイントの計算
            $totalpoint += SC_Utils_Ex::sfPrePoint($arrValues['price'][$i], $arrValues['point_rate'][$i]) * $arrValues['quantity'][$i];
        }

        // 消費税
        $arrValues['tax'] = $totaltax;
        // 小計
        $arrValues['subtotal'] = $subtotal;
        // 合計
        $arrValues['total'] = $subtotal + $arrValues['deliv_fee'] + $arrValues['charge'] - ($arrValues['add_discount'] + $arrValues['discount']);
        // お支払い合計
        $arrValues['payment_total'] = $arrValues['total'] - ($arrValues['use_point'] * POINT_VALUE);

        // 加算ポイント
        $arrValues['add_point'] = SC_Helper_DB_Ex::sfGetAddPoint($totalpoint, $arrValues['use_point']);

        // 最終保持ポイント
        $arrValues['total_point'] = $objFormParam->getValue('point') - $arrValues['use_point'] + $arrValues['add_point'];

        if($arrValues['payment_total'] <= 0) {
            $arrErr['add_discount'] = 'お支払い合計額が1円以上になるように調整して下さい。<br />';
        }

        $objFormParam->setParam($arrValues);
        return $arrErr;
    }

    /**
     * 決済ステーションへデータを送る
     *
     * @param void
     * @return void
     */
    function lfSendMode($order_id, $version = null) {
        $objQuery = new SC_Query();

        // 送信用データの配列初期化
        $this->objSmbc->addArrParam("version", 3);
        $this->objSmbc->addArrParam("bill_method", 2);
        $this->objSmbc->addArrParam("kessai_id", 4);
        $this->objSmbc->addArrParam("shop_cd", 7);
        $this->objSmbc->addArrParam("syuno_co_cd", 8);
        $this->objSmbc->addArrParam("shop_pwd", 20);
        $this->objSmbc->addArrParam("shoporder_no", 17);
        $this->objSmbc->addArrParam("seikyuu_kingaku", 13);

        // 連携データを作成
        // バージョン
        $this->arrParam['version'] = $version;
        // 決済手段区分
        $this->arrParam['bill_method'] = MDL_SMBC_CREDIT_BILL_METHOD;
        // 決済種類コード
        $this->arrParam['kessai_id'] = MDL_SMBC_CREDIT_KESSAI_ID;

        // 請求番号
        $this->arrParam['shoporder_no'] = str_pad($_POST['order_id'], 17, "0", STR_PAD_LEFT);

        // 請求金額
        $this->arrParam['seikyuu_kingaku'] = $this->org_payment_total;

        // 取消後金額
        $this->arrParam['torikeshi_kingaku'] = $_POST['payment_total'];

        // モジュールマスタからデータを取得
        $payment_id = $objQuery->select("payment_id", "dtb_order", "order_id = ?", array($_POST['order_id']));
        $arrModule = $this->objSmbc->getModuleMasterData($payment_id[0]['payment_id']);

        // 契約コード
        $this->arrParam['shop_cd'] = $arrModule['shop_cd'];

        // 収納企業コード
        $this->arrParam['syuno_co_cd'] = $arrModule['syuno_co_cd'];

        // ショップパスワード
        $this->arrParam['shop_pwd'] = $arrModule['shop_pwd'];

        // 接続先
        if($arrModule['connect_url'] == "real"){
            // 本番用
            $this->connect_url = MDL_SMBC_CREDIT_KAKUTEI_LINK_URL_REAL;
        }else{
            // テスト用
            $this->connect_url = MDL_SMBC_CREDIT_KAKUTEI_LINK_URL_TEST;
        }
        // 送信データを設定する
        $this->objSmbc->setParam($this->arrParam);

        // 決済ステーションへ送信
        $arrResponse = $this->objSmbc->sendParam($this->connect_url);

        // 連携結果を取得
        $res_mode = $this->objSmbc->getMode($arrResponse);

        if($res_mode == 'complete') {
            // EC-CUBE更新
            $sqlval['update_date'] = 'Now()';
            $sqlval['total'] = $_POST['total'];
            $sqlval['payment_total'] = $_POST['payment_total'];
            $sqlval['discount'] = $_POST['discount'] + $_POST['add_discount'];
            $sqlval['note'] = $_POST['note'];

            // 受注テーブルの更新
            $objQuery->update("dtb_order", $sqlval, "order_id = ?", array($order_id));
            $this->tpl_onload = "window.alert('受注履歴を編集しました。');parent.window.opener.document.form1.submit();";

        }elseif($res_mode == 'error'){
            $this->lfDispError($arrResponse);
        }
    }
    /**
     * 決済ステーションから受け取ったエラー情報を、表示用データにする.
     *
     * @param array $arrResponse 決済ステーションからのレスポンスボディ
     * @return void
     */
    function lfDispError($arrResponse) {
        // 結果内容
        $this->arrError['res'] = mb_convert_encoding($arrResponse['res'], "UTF-8", "auto");
        // 結果コード
        $this->arrError['rescd'] = $arrResponse['rescd'];
    }

}
?>
