<?php
/**
 * 決済サービスタイプ：UPOP、コマンド名：与信の要求Dtoクラス<br>
 *
 * @author Veritrans, Inc.
 *
 */
class UpopAuthorizeRequestDto extends AbstractPaymentRequestDto {

    /**
     * 決済サービスタイプ<br>
     */
    private $SERVICE_TYPE = "upop";

    /**
     * 決済サービスコマンド<br>
     */
    private $SERVICE_COMMAND = "Authorize";

    /**
     * 取引ID<br>
     */
    private $orderId;

    /**
     * 決済金額<br>
     */
    private $amount;

    /**
     * 決済完了後に、店舗側へ遷移を戻すためのURLを指定します
     */
    private $termUrl;

    /**
     * 消費者のIP address
     */
    private $customerIp;

    /**
     * 売上フラグ<br>
     */
    private $withCapture;

    /**
     * 売上フラグを取得する<br>
     * @return 売上フラグ<br>
     */
    public function getWithCapture() {
        return $this -> withCapture;
    }

    /**
     * 売上フラグを設定する<br>
     * @param  withCapture 売上フラグ<br>
     */
    public function setWithCapture($withCapture) {
        $this -> withCapture = $withCapture;
    }

    /**
     *  Verify 用URLを取得する<br>
     * @return Verify 用URL<br>
     */
    public function getTermUrl() {
        return $this -> termUrl;
    }

    /**
     *  Verify 用URLを設定する<br>
     * @param  Verify 用URL<br>
     */
    public function setTermUrl($termUrl) {
        $this -> termUrl = $termUrl;
    }

    /**
     *  消費者ＩＰを取得する<br>
     * @return V消費者ＩＰ<br>
     */
    public function getCustomerIp() {
        return $this -> customerIp;
    }

    /**
     *  消費者ＩＰを設定する<br>
     * @param   消費者ＩＰ<br>
     */
    public function setCustomerIp($customerIp) {
        $this -> customerIp = $customerIp;
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
     * ログ用文字列(マスク済み)<br>
     */
    private $maskedLog;

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
    public function __toString() {
        return (string)$this -> maskedLog;
    }

}
?>
