<?php
/**
 * 決済サービスタイプ：UPOP、コマンド名：取消の応答Dtoクラス<br>
 *
 * @author Veritrans, Inc.
 *
 */
class UpopCancelResponseDto extends MdkBaseDto {
    /**
     * 決済サービスタイプ<br>
     */
    private $serviceType;

    /**
     * 処理結果コード<br>
     */
    private $mstatus;

    /**
     * 詳細結果コード<br>
     */
    private $vResultCode;

    /**
     * エラーメッセージ<br>
     */
    private $merrMsg;

    /**
     * 取引ID<br>
     */
    private $orderId;

    /**
     * 取引毎に付くID<br>
     */
    private $custTxn;

    /**
     * 電文ＩＤ<br>
     */
    private $marchTxn;

    /**
     * MDK version
     */
    private $txnVersion;

    /**
     *YYYYMMDDHHmmss
     * 日本時間
     */
    private $txnDatetimeJp;

    /**
     * YYYYMMDDHHmmss
     * 中国時間
     */
    private $txnDatetimeCn;

    /**
     * 売上金額
     */
    private $capturedAmount;

    /**
     *返金後の金額
     */
    private $remainingAmount;

    /**
     * 清算金額
     */
    private $settleAmount;

    /**
     *清算日付
     *MMDD
     */
    private $settleDate;

    /**
     *清算通貨
     */
    private $settleCurrency;

    /**
     *清算レート
     */
    private $settleRate;

    /**
     * PayNowIDオブジェクト<br>
     * オブジェクト<br>
     * PayNowID用項目を格納するオブジェクト<br>
     */
     private $payNowIdResponse;

    /**
     * 決済サービスタイプを取得する<br>
     * @return 決済サービスタイプ<br>
     */
    public function getServiceType() {
        return $this -> serviceType;
    }

    /**
     * 決済サービスタイプを設定する<br>
     * @param  serviceType 決済サービスタイプ<br>
     */
    public function setServiceType($serviceType) {
        $this -> serviceType = $serviceType;
    }

    /**
     * 処理結果コードを取得する<br>
     * @return 処理結果コード<br>
     */
    public function getMstatus() {
        return $this -> mstatus;
    }

    /**
     * 処理結果コードを設定する<br>
     * @param  mstatus 処理結果コード<br>
     */
    public function setMstatus($mstatus) {
        $this -> mstatus = $mstatus;
    }

    /**
     * 詳細結果コードを取得する<br>
     * @return 詳細結果コード<br>
     */
    public function getVResultCode() {
        return $this -> vResultCode;
    }

    /**
     * 決済時刻（日本）を取得する<br>
     * @return 決済時刻（日本）<br>
     */
    public function getTxnDatetimeJp() {
        return $this -> txnDatetimeJp;
    }

    /**
     * 決済時刻（日本）を設定する<br>
     * @param  txnDatetimeJp 決済時刻（日本）<br>
     */
    public function setTxnDatetimeJp($txnDatetimeJp) {
        $this -> txnDatetimeJp = $txnDatetimeJp;
    }

    /**
     * 決済時刻（中国）を取得する<br>
     * @return 決済時刻（中国）<br>
     */
    public function getTxnDatetimeCn() {
        return $this -> txnDatetimeCn;
    }

    /**
     * 決済時刻（中国）を設定する<br>
     * @param  txnDatetimeCn 決済時刻（中国）<br>
     */
    public function setTxnDatetimeCn($txnDatetimeCn) {
        $this -> txnDatetimeCn = $txnDatetimeCn;
    }

    /**
     * 元売上金額を取得する<br>
     * @return 元売上金額<br>
     */
    public function getCapturedAmount() {
        return $this -> capturedAmount;
    }

    /**
     * 元売上金額を設定する<br>
     * @param 元売上金額<br>
     */
    public function setCapturedAmount($capturedAmount) {
        $this -> capturedAmount = $capturedAmount;
    }

    /**
     * 返金後の金額を取得する<br>
     * @return 返金後の金額<br>
     */
    public function getRemainingAmount() {
        return $this -> remainingAmount;
    }

    /**
     * 返金後金額を設定する<br>
     * @param 返金後金額<br>
     */
    public function setRemainingAmount($remainingAmount) {
        $this -> remainingAmount = $remainingAmount;
    }

    /**
     * 清算金額を取得する<br>
     * @return 清算金額<br>
     */
    public function getSettleAmount() {
        return $this -> settleAmount;
    }

    /**
     * 清算金額を設定する<br>
     * @param 清算金額<br>
     */
    public function setSettleAmount($settleAmount) {
        $this -> settleAmount = $settleAmount;
    }

    /**
     * 清算金額を取得する<br>
     * @return 清算金額<br>
     */
    public function getSettleDate() {
        return $this -> settleDate;
    }

    /**
     * 清算金額を設定する<br>
     * @param 清算金額<br>
     */
    public function setSettleDate($settleDate) {
        $this -> settleDate = $settleDate;
    }

    /**
     * 清算通貨を取得する<br>
     * @return 清算通貨<br>
     */
    public function getSettleCurrency() {
        return $this -> settleCurrency;
    }

    /**
     * 清算通貨を設定する<br>
     * @param 清算通貨<br>
     */
    public function setSettleCurrency($settleCurrency) {
        $this -> settleCurrency = $settleCurrency;
    }

    /**
     * 清算レートを取得する<br>
     * @return 清算レート<br>
     */
    public function getSettleRate() {
        return $this -> settleRate;
    }

    /**
     * 清算レートを設定する<br>
     * @param 清算レート<br>
     */
    public function setSettleRate($settleRate) {
        $this -> settleRate = $settleRate;
    }

    /**
     * 詳細結果コードを設定する<br>
     * @param  vResultCode 詳細結果コード<br>
     */
    public function setVResultCode($vResultCode) {
        $this -> vResultCode = $vResultCode;
    }

    /**
     * エラーメッセージを取得する<br>
     * @return エラーメッセージ<br>
     */
    public function getMerrMsg() {
        return $this -> merrMsg;
    }

    /**
     * エラーメッセージを設定する<br>
     * @param  merrMsg エラーメッセージ<br>
     */
    public function setMerrMsg($merrMsg) {
        $this -> merrMsg = $merrMsg;
    }

    /**
     * 取引IDを取得する<br>
     * @return 取引ID<br>
     */
    public function getOrderId() {
        return $this -> orderId;
    }

    /**
     * 取引IDを設定する<br>
     * @param  orderId 取引ID<br>
     */
    public function setOrderId($orderId) {
        $this -> orderId = $orderId;
    }

    /**
     * 取引毎に付くIDを取得する<br>
     * @return 取引毎に付くID<br>
     */
    public function getCustTxn() {
        return $this -> custTxn;
    }

    /**
     * 取引毎に付くIDを設定する<br>
     * @param  custId 取引毎に付くID<br>
     */
    public function setCustTxn($custTxn) {
        $this -> custTxn = $custTxn;
    }

    /**
     * 電文IDを取得する<br>
     * @return 電文IDくID<br>
     */
    public function getMarchTxn() {
        return $this -> marchTxn;
    }

    /**
     * 電文IDを設定する<br>
     * @param 電文IDくID<br>
     */
    public function setMarchTxn($marchTxn) {
        $this -> marchTxn = $marchTxn;
    }

    /**
     * MDKバージョンを取得する<br>
     * @return MDKバージョン<br>
     */
    public function getTxnVersion() {
        return $this -> txnVersion;
    }

    /**
     * MDKバージョンを設定する<br>
     * @param  txnVersion MDKバージョン<br>
     */
    public function setTxnVersion($txnVersion) {
        $this -> txnVersion = $txnVersion;
    }

    private $resultXml;
    /**
     * 結果XML(マスク済み)を設定する<br>
     * @param  resultXml 結果XML(マスク済み)<br>
     */
    public function _setResultXml($resultXml) {
        $this -> resultXml = $resultXml;
    }

    /**
     * 結果XML(マスク済み)を取得する<br>
     * @return 結果XML(マスク済み)<br>
     */
    public function toString() {
        return (string)$this -> resultXml;
    }

    /**
     * PayNowIDオブジェクトを設定する<br>
     * @param PayNowIDオブジェクト<br>
     */
    public function setPayNowIdResponse($payNowIdResponse) {
        $this -> payNowIdResponse = $payNowIdResponse;
    }
       
    /**
    * PayNowIDオブジェクトを取得する<br>
    * @return PayNowIDオブジェクト<br>
    */
    public function getPayNowIdResponse() {
        return $this -> payNowIdResponse;
    }

}
?>
