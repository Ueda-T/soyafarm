<?php
require_once("../require.php");
require_once(MODULE_REALDIR . 'mdl_smbc/inc/include.php');
require_once(MODULE_REALDIR . 'mdl_smbc/class/SC_SMBC.php');
require_once(MODULE_REALDIR . 'mdl_smbc/class/SC_SMBC_Recv.php');

class LC_Mdl_SMBC_Overtime_Recv {

    function process() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $objRecv = new SC_SMBC_Recv();
            $objSmbc = new SC_SMBC();

            $arrPost = $_POST;

            // 通知内容をログ出力
            $objSmbc->printLog("overtime_recv");
            $objSmbc->printLog($arrPost);

            // エラーチェック。エラー無:true エラー有:false
            if ($this->lfErrCheck($objRecv, $objSmbc, $arrPost) == true) {
                // 受注ステータスをキャンセルに更新
                $objPurchase = new SC_Helper_Purchase_Ex();
                $objPurchase->sfUpdateOrderStatus(intval($arrPost['shoporder_no']), ORDER_CANCEL);

                // 更新内容をログ出力
                $objSmbc->printLog("shoporder_no : " . $arrPost['shoporder_no'] . " status:" . ORDER_CANCEL);
            }

            $objRecv->sendRespons();
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
        $res_flg = true;

        // 受注マスタに請求番号が無い場合、請求金額が異なっている場合は処理を終了
        if ($objRecv->checkOrder($arrPost) == false) {
            $res_flg = false;
        }

        // 契約コード・収納企業コードをチェック
        if ($objRecv->checkModuleMaster($arrPost, "dtb_order") == false) {
            $res_flg = false;
        }

        // 既に入金済みの受注の場合、更新を行わない
        if ($objRecv->checkOrderPreEnd($arrPost) == false) {
            $res_flg = false;
        }

        return $res_flg;
    }
    function doValidToken() {
        // nothing.
    }
}
$object = new LC_Mdl_SMBC_Overtime_Recv();
$object->process();
?>