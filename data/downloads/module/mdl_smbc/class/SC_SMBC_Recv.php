<?php
require_once HTML_REALDIR .'require.php';
require_once DATA_REALDIR . 'module/Request.php';
require_once(MODULE_REALDIR . 'mdl_smbc/inc/include.php');
require_once(MDL_SMBC_CLASS_PATH . 'SC_Mdl_SMBC.php');
// レスポンス内容
define('MDL_SMBC_RESPONSE', '<BODY>status_cd="0"<BR></BODY>');

class SC_SMBC_Recv {

    /**
     *  コンストラクタ
     */
    function SC_SMBC_Recv() {
        $this->objSmbc = new SC_SMBC();
        $this->objQuery = new SC_Query();
    }

    /**
     * 結果コードをチェック
     *
     * @param integer $rescd 結果コード
     * @return boolean 結果OK:true 結果NG:false
     */
    function checkRescd($rescd) {
        if ($rescd != MDL_SMBC_RES_OK) {
            return false;
        }
        return true;
    }

    /**
     * 受注マスタの請求番号を確認
     * 同じ請求番号が存在する場合はtrue、存在しない場合はfalse
     *
     * @param integer $order_id 請求番号
     * @return boolean 請求番号が存在する:true 存在しない:false
     */
    function searchOrderId ($order_id){
        $count = $this->objQuery->count("dtb_order", "order_id = ?", array(intval($order_id)));

        if ($count > 0) {
            return true;
        }
        return false;
    }

    /**
     * 受注一時マスタの請求番号と請求金額を確認
     * 受注一時マスタに請求番号が無い場合、請求金額が異なっている場合はfalse
     * 請求番号があり、請求金額が一致している場合はtrueを返す
     *
     * @param array $array POSTデータ
     * @return boolean
     */
    function checkOrderTemp ($array){
        $arrOrderTemp = array();
        $arrOrderTemp =  $this->objQuery->select("payment_total", "dtb_order_temp", "order_id = ?", array(intval($array['shoporder_no'])));

        if (count($arrOrderTemp) > 0 && $array['seikyuu_kingaku'] == $arrOrderTemp[0]['payment_total']) {
            return true;
        }

        return false;
    }

    /**
     * 受注マスタの請求番号と請求金額を確認
     * 受注マスタに請求番号が無い場合、請求金額が異なっている場合はfalse
     * 請求番号があり、請求金額が一致している場合はtrueを返す
     *
     * @param array $array POSTデータ
     * @return boolean
     */
    function checkOrder ($array){
        $arrOrder = array();
        $arrOrder =  $this->objQuery->select("payment_total", "dtb_order", "del_flg = 0 AND order_id = ?", array(intval($array['shoporder_no'])));

        if (count($arrOrder) > 0 && $array['seikyuu_kingaku'] == $arrOrder[0]['payment_total']) {
            return true;
        }

        return false;
    }

    /**
     * 受注ステータスが入金済みの受注の場合false
     *
     * @param array $array POSTデータ
     * @return boolean
     */
    function checkOrderPreEnd ($array){
        $arrOrder = array();
        $arrOrder =  $this->objQuery->select("status", "dtb_order", "del_flg = 0 AND order_id = ?", array(intval($array['shoporder_no'])));

        if ($arrOrder[0]['status'] != ORDER_PRE_END) {
            return true;
        }

        return false;
    }

    /**
     * 契約コード・収納企業コードをチェック
     *
     * @return array $array POSTデータ
     * @return string $table 受注の検索先テーブル
     * @return boolean 契約コード・収納企業コードがこのモジュールのものと一致:true
     */
    function checkModuleMaster($array, $table) {
        $payment_id =  $this->objQuery->select("payment_id", $table, "order_id = ?", array(intval($array['shoporder_no'])));

        $arrModuleMaster = $this->objSmbc->getModuleMasterData($payment_id[0]['payment_id']);

        if ($array['shop_cd'] == $arrModuleMaster['shop_cd'] && $array['syuno_co_cd'] == $arrModuleMaster['syuno_co_cd']) {
            return true;
        }
        return false;
    }

    /**
     * レスポンスを返す
     */
    function sendRespons () {
        $resBody = mb_convert_encoding(MDL_SMBC_RESPONSE, "SJIS-win", "auto");

        print_r($resBody);
    }

    /**
     * 注文番号からネットバンク決済かを調べる
     *
     * @param integer $order_id 注文番号
     * @return boolean ネットバンク決済:true それ以外:false
     */
    function isNetbank ($order_id) {
        $objQuery = new SC_Query();

        $cols = "dtb_payment.memo01";
        $from = "dtb_order_temp INNER JOIN dtb_payment USING(payment_id)";
        $where = "order_id = ?";

        $bill_method = $objQuery->select($cols, $from, $where, array(intval($order_id)));

        if ($bill_method[0]['memo01'] == MDL_SMBC_NETBUNK_BILL_METHOD) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 注文番号からデータ連携の決済かを調べる
     *
     * @param integer $order_id 注文番号
     * @return boolean データ連携決済:true それ以外:false
     */
    function isDataLinkPayment ($order_id) {
        $objQuery = new SC_Query();

        $cols = "dtb_payment.memo01";
        $from = "dtb_order_temp INNER JOIN dtb_payment USING(payment_id)";
        $where = "order_id = ?";

        $bill_method = $objQuery->select($cols, $from, $where, array(intval($order_id)));

        if ($bill_method[0]['memo01'] == MDL_SMBC_CREDIT_BILL_METHOD || $bill_method[0]['memo01'] == MDL_SMBC_CONVENI_NUMBER_BILL_METHOD) {
            return true;
        } else {
            return false;
        }
    }
}

?>
