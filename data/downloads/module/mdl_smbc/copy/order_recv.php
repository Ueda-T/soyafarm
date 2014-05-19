<?php
require_once("../require.php");
require_once(CLASS_REALDIR . "pages/LC_Page.php");
require_once(MODULE_REALDIR . 'mdl_smbc/inc/include.php');
require_once(MODULE_REALDIR . 'mdl_smbc/class/SC_SMBC.php');
require_once(MODULE_REALDIR . 'mdl_smbc/class/SC_SMBC_Recv.php');

class LC_Mdl_SMBC_Order_Recv {

    function process() {
        $objSmbc = new SC_SMBC();
        $objSmbc->printLog("start_order_recv");

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $objRecv = new SC_SMBC_Recv();

            $arrPost = $_POST;

            // 通知内容をログ出力
            $objSmbc->printLog("order_recv");
            $objSmbc->printLog($arrPost);

            // エラーチェック。エラー無:true エラー有:false
            if ($this->lfErrCheck($objRecv, $arrPost) == true) {
                $order_id = intval($arrPost['shoporder_no']);

                $arrOrderForCheck = $objSmbc->getOrderTemp($order_id);
                if ($arrOrderForCheck['status'] == ORDER_PENDING || $arrOrderForCheck['status'] == ORDER_NEW) {
                    $objPurchase = new SC_Helper_Purchase_Ex();
                    $objPurchase->sfUpdateOrderStatus($order_id, ORDER_PAY_WAIT);

                    // 更新内容をログ出力
                    $objSmbc->printLog("shoporder_no : " . $arrPost['shoporder_no'] . " status:" . ORDER_PAY_WAIT);

                    $objMobile = new SC_Helper_Mobile_Ex();
                    $mailHelper = new SC_Helper_Mail_Ex();
                    if ($objMobile->gfIsMobileMailAddress($arrOrderForCheck['order_email']) == true) {
                        $mailHelper->sfSendOrderMail($order_id, '2');
                    } else {
                        $mailHelper->sfSendOrderMail($order_id, '1');
                    }
                }
            }

            // ネットバンク決済の場合入金通知側でレスポンスを表示するためここでは表示しない
            if ($objRecv->isNetbank($arrPost['shoporder_no']) == false) {
                $objRecv->sendRespons();
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
    function lfErrCheck ($objRecv, $arrPost) {
        $res_flg = true;

        // 結果コードをチェック
        if ($objRecv->checkRescd($arrPost['rescd']) == false) {
            $res_flg = false;
        }

        // 請求番号がデータ連携（クレジット・コンビニ）の決済の場合処理を終了
        if ($objRecv->isDataLinkPayment($arrPost['shoporder_no']) == true) {
            $res_flg = false;
        }

        // 請求番号が受注マスタに存在しない場合は処理を終了
        if ($objRecv->searchOrderId($arrPost['shoporder_no']) == false) {
            $res_flg = false;
        }

        // 受注一時マスタに請求番号が無い場合、請求金額が異なっている場合は処理を終了
        if ($objRecv->checkOrderTemp($arrPost) == false) {
            $res_flg = false;
        }

        // 契約コード・収納企業コードをチェック
        if ($objRecv->checkModuleMaster($arrPost, "dtb_order_temp") == false) {
            $res_flg = false;
        }

        return $res_flg;
    }

    function doValidToken() {
        // nothing.
    }
}
$object = new LC_Mdl_SMBC_Order_Recv();
$object->process();
?>