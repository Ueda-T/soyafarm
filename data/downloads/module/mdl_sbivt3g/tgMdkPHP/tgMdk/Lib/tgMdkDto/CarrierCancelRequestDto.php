<?php
/**
 * 決済サービスタイプ：キャリア、コマンド名：取消の要求Dtoクラス<br>
 *
 * @author Veritrans, Inc.
 *
 */
class CarrierCancelRequestDto extends AbstractPaymentRequestDto
{

    /**
     * 決済サービスタイプ<br>
     * 半角英数字<br>
     * 必須項目、固定値<br>
     */
    private $SERVICE_TYPE = "carrier";

    /**
     * 決済サービスコマンド<br>
     * 半角英数字<br>
     * 必須項目、固定値<br>
     */
    private $SERVICE_COMMAND = "Cancel";

    /**
     * 取引ID<br>
     * 半角英数字<br/>
     * 最大桁数：100<br/>
     * キャンセル対象の取引IDを指定する。<br/>
     */
    private $orderId;

    /**
     * サービスオプションタイプ<br>
     * 半角英数字<br/>
     * - "docomo":ドコモケータイ払い<br/>
     * - "au":auかんたん決済<br/>
     * - "sb_ktai":ソフトバンクまとめて支払い（B）<br/>
     * - "flets":フレッツまとめて支払い<br/>
     * 以下は指定できない。<br/>
     * - "sb_matomete":ソフトバンクまとめて支払い（A）<br/>
     * - "s_bikkuri":S!まとめて支払い<br/>
     */
    private $serviceOptionType;

    /**
     * 課金取消月<br>
     * 半角英数字<br/>
     * 最大桁数：6<br/>
     * docomoとauとfletsで、継続課金（月額）の取消時のみ指定可能。<br/>
     * - YYYYMM形式<br/>
     */
    private $cancelMonth;


    /** 
     * ログ用文字列(マスク済み)<br>
     * 半角英数字<br>
     */
    private $maskedLog;


    /**
     * 決済サービスタイプを取得する<br>
     * @return 決済サービスタイプ<br>
     */
    public function getServiceType() {
        return $this->SERVICE_TYPE;
    }

    /**
     * 決済サービスコマンドを取得する<br>
     * @return 決済サービスコマンド<br>
     */
    public function getServiceCommand() {
        return $this->SERVICE_COMMAND;
    }

    /**
     * 取引IDを取得する<br>
     * @return 取引ID<br>
     */
    public function getOrderId() {
        return $this->orderId;
    }

    /**
     * 取引IDを設定する<br>
     * @param orderId 取引ID<br>
     */
    public function setOrderId($orderId) {
        $this->orderId = $orderId;
    }

    /**
     * サービスオプションタイプを取得する<br>
     * @return サービスオプションタイプ<br>
     */
    public function getServiceOptionType() {
        return $this->serviceOptionType;
    }

    /**
     * サービスオプションタイプを設定する<br>
     * @param serviceOptionType サービスオプションタイプ<br>
     */
    public function setServiceOptionType($serviceOptionType) {
        $this->serviceOptionType = $serviceOptionType;
    }

    /**
     * 課金取消月を取得する<br>
     * @return 課金取消月<br>
     */
    public function getCancelMonth() {
        return $this->cancelMonth;
    }

    /**
     * 課金取消月を設定する<br>
     * @param cancelMonth 課金取消月<br>
     */
    public function setCancelMonth($cancelMonth) {
        $this->cancelMonth = $cancelMonth;
    }


    /**
     * ログ用文字列(マスク済み)を設定する<br>
     * @param  maskedLog ログ用文字列(マスク済み)<br>
     */
    public function _setMaskedLog($maskedLog) {
        $this->maskedLog = $maskedLog;
    }

    /**
     * ログ用文字列(マスク済み)を取得する<br>
     * @return ログ用文字列(マスク済み)<br>
     */
    public function __toString() {
        return (string)$this->maskedLog;
    }

}
?>