<?php
/**
 * 決済サービスタイプ：UPOP、コマンド名：返金の要求Dtoクラス<br>
 *
 * @author Veritrans, Inc.
 *
 */
class UpopRefundRequestDto extends AbstractPaymentRequestDto {
    /**
     * 決済サービスタイプ<br>
     */
    private $SERVICE_TYPE = "upop";

    /**
     * 決済サービスコマンド<br>
     */
    private $SERVICE_COMMAND = "Refund";

    /**
     * 取引金額<br>
     */
    private $amount;

    /**
     * 取引ID
     */
    private $orderId;

    /**
     * ログ用文字列(マスク済み)<br>
     */
    private $maskedLog;

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
     * 取引金額を取得する<br>
     * @return 取引金額<br>
     */
    public function getAmount() {
        return $this -> amount;
    }

    /**
     * 取引金額を設定する<br>
     * @param  amount 取引金額<br>
     */
    public function setAmount($amount) {
        $this -> amount = $amount;
    }

    /**
     * 決済サービスタイプを取得する<br>
     * @return 決済サービスタイプ<br>
     */
    public function getServiceType() {
        return $this -> SERVICE_TYPE;
    }

    /**
     * 決済サービスコマンドを取得する<br>
     * @return 決済サービスコマンド<br>
     */
    public function getServiceCommand() {
        return $this -> SERVICE_COMMAND;
    }

    /**
     * ログ用文字列(マスク済み)を設定する<br>
     * @param  maskedLog ログ用文字列(マスク済み)<br>
     */
    public function _setMaskedLog($maskedLog) {
        $this -> maskedLog = $maskedLog;
    }

    /**
     * ログ用文字列(マスク済み)を取得する<br>
     * @return ログ用文字列(マスク済み)<br>
     */
    public function toString() {
        return (string)$this -> maskedLog;
    }

}
?>