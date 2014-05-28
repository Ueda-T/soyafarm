<?php
require_once("../require.php");
require_once(CLASS_REALDIR . "pages/LC_Page.php");
require_once(MODULE_REALDIR . 'mdl_smbc/inc/include.php');
require_once(MODULE_REALDIR . 'mdl_smbc/class/SC_SMBC.php');
require_once(MODULE_REALDIR . 'mdl_smbc/class/SC_SMBC_Recv.php');

class LC_Mdl_SMBC_Payment_Recv {

    function process() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $objRecv = new SC_SMBC_Recv();
            $objSmbc = new SC_SMBC();

            $arrPost = $_POST;

            // 通知内容をログ出力
            $objSmbc->printLog("payment_recv");
            $objSmbc->printLog($arrPost);

            // ネットバンク決済の場合は決済完了通知購入完了処理も行う
            if ($objRecv->isNetbank($arrPost['shoporder_no']) == true) {
                require_once(HTML_REALDIR . "smbc/order_recv.php");
            }

            // エラーチェック。エラー無:true エラー有:false
            if ($this->lfErrCheck($objRecv, $objSmbc, $arrPost) == true) {

                // 受注ステータスを入金済みに更新
                $objPurchase = new SC_Helper_Purchase_Ex();
                $objPurchase->sfUpdateOrderStatus(intval($arrPost['shoporder_no']), ORDER_PRE_END);
                $this->lfRegistPayment($arrPost);

                if (in_array($arrPost['rescd'], array(MDL_SMBC_RES_OVER,MDL_SMBC_RES_SHORT,MDL_SMBC_RES_REQUEST,MDL_SMBC_RES_DUPLICATION))) {
                    $objQuery =& SC_Query_Ex::getSingletonInstance();

                    $arrOrder = $objQuery->select("*","dtb_order", "order_id = ?", array(intval($arrPost['shoporder_no'])));
                    $arrModuleMaster = $objSmbc->getModuleMasterData($arrOrder[0]['payment_id']);

                    $arrInfo = SC_Helper_DB_Ex::sfGetBasisData();
                    $to = $arrInfo['email01'];

                    $tmp_subject = "【決済ステーション】　入金結果警告通知";
                    $body  = $arrInfo['company_name']."様　（収納企業コード：".$arrModuleMaster['syuno_co_cd']."）\n";
                    $body .= "\n";
                    $body .= "いつもSMBCファイナンスサービスの決済ステーションをご利用いただきまして\n";
                    $body .= "ありがとうございます。\n";
                    $body .= "入金結果の処理で、警告が発生しました。（警告発生日　".substr($arrPost['kessai_date'],0,4)."/".substr($arrPost['kessai_date'],4,2)."/".substr($arrPost['kessai_date'],6,2)."/".substr($arrPost['kessai_time'],0,2)."/".substr($arrPost['kessai_time'],2,2).")\n";
                    $body .= "\n";
                    $body .= "　ショップ名　　：".$arrInfo['shop_name']."\n";
                    $body .= "\n";
                    $body .= "　お支払方法　　：".$arrOrder[0]['payment_method']."\n";
                    $body .= "\n";
                    $body .= "\n";
                    $body .= "\n";
                    $body .= "決済ステーション管理画面の「決済情報照会」画面から警告の内容を確認して下さい。\n";
                    $body .= "(「決済情報照会」画面の検索項目「状態更新日時」を".substr($arrPost['kessai_date'],0,4)."/".substr($arrPost['kessai_date'],4,2)."/".substr($arrPost['kessai_date'],6,2)."/".substr($arrPost['kessai_time'],0,2)."/".substr($arrPost['kessai_time'],2,2)."\n";
                    $body .= "として検索して下さい。)\n";
                    $body .= "\n";
                    $body .= "このメールはSMBCファイナンスサービスの決済ステーションをご利用のお客様へ\n";
                    $body .= "お届けしております。\n";
                    $body .= "本メールの送信元アドレスは送信専用となります。本メールに返信しないようお願い致します。\n";

                    // 入金結果警告通知メール
                    $mailHelper = new SC_Helper_Mail_Ex();
                    $mailHelper->sfSendMail($to, $tmp_subject, $body);
                }

                // 更新内容をログ出力
                $objSmbc->printLog("shoporder_no : " . $arrPost['shoporder_no'] . " status:" . ORDER_PRE_END);
            }

            $objRecv->sendRespons();
        }
    }
    
    
    function lfRegistPayment($arrPost) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        
        $sqlval = array();
        if (in_array($arrPost['rescd'], array(MDL_SMBC_RES_OVER, MDL_SMBC_RES_SHORT, MDL_SMBC_RES_OK))) {
            $sqlval['order_id'] = $arrPost['shoporder_no'];
            switch($arrPost['rescd']) {
                case MDL_SMBC_RES_OK:
                    $sqlval['payment_status'] = MDL_SMBC_PAYMENT_STATUS_OK;
                case MDL_SMBC_RES_OVER:
                    $sqlval['payment_status'] = MDL_SMBC_PAYMENT_STATUS_OVER;
                case MDL_SMBC_RES_SHORT:
                    $sqlval['payment_status'] = MDL_SMBC_PAYMENT_STATUS_SHORT;
            }
            
            $sqlval['payment_date'] = $arrPost['nyukin_date'];
            $sqlval['payment_amount'] = $arrPost['nyukin_kingaku'];
            
            $cnt = $objQuery->count('dtb_mdl_smbc_payment', 'order_id = ?', array($arrPost['shoporder_no']));
            
            if ($cnt > 0) {
                unset($sqlval['order_id']);
                $objQuery->update('dtb_mdl_smbc_payment', $sqlval, 'order_id = ?', array($arrPost['shoporder_no']));
            } else {
                $objQuery->insert('dtb_mdl_smbc_payment', $sqlval);
            }
        }
    }

    /**
     * エラーチェック。エラー無:true エラー有:false
     *
     * @param object $objRecv 通知共通クラス
     * @param object $objSmbc
     * @param array $arrPost POSTされたデータ
     * @return boolean $res_flg エラー:false エラー無し:true
     */
    function lfErrCheck ($objRecv, $objSmbc, $arrPost) {

        if (in_array($arrPost['rescd'], array(MDL_SMBC_RES_OVER,MDL_SMBC_RES_SHORT,MDL_SMBC_RES_REQUEST,MDL_SMBC_RES_DUPLICATION))) {
            $res_flg = false;
            $objQuery =& SC_Query_Ex::getSingletonInstance();

            $arrOrder = $objQuery->select("*","dtb_order", "order_id = ?", array(intval($arrPost['shoporder_no'])));
            $arrModuleMaster = $objSmbc->getModuleMasterData($arrOrder[0]['payment_id'], "recv");

            if ($arrPost['shop_cd'] == $arrModuleMaster['shop_cd'] &&
                $arrPost['syuno_co_cd'] == $arrModuleMaster['syuno_co_cd'] &&
                $arrOrder[0]['del_flg'] == 0 &&
                (
                    $arrPost['rescd'] == MDL_SMBC_RES_OVER && $arrModuleMaster['over_deposit'] == 1 ||
                    $arrPost['rescd'] == MDL_SMBC_RES_SHORT && $arrModuleMaster['short_deposit'] == 1 ||
                    $arrPost['rescd'] == MDL_SMBC_RES_REQUEST && $arrModuleMaster['request_deposit'] == 1 ||
                    $arrPost['rescd'] == MDL_SMBC_RES_DUPLICATION && $arrModuleMaster['request_deposit'] == 1
                ) ) {
                $res_flg = true;
            }
        }else{
            $res_flg = true;

            // 結果コードをチェック
            if ($objRecv->checkRescd($arrPost['rescd']) == false) {
                $res_flg = false;
            }

            // 受注マスタに請求番号が無い場合、請求金額が異なっている場合は処理を終了
            if ($objRecv->checkOrder($arrPost) == false) {
                $res_flg = false;
            }

            // 契約コード・収納企業コードをチェック
            if ($objRecv->checkModuleMaster($arrPost, "dtb_order") == false) {
                $res_flg = false;
            }
        }
        return $res_flg;
    }
    function doValidToken() {
        // nothing.
    }
}
$object = new LC_Mdl_SMBC_Payment_Recv();
$object->process();
?>