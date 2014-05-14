<?php
/**
 * 決済サービスタイプ：電子マネー、コマンド名：取消の要求Dtoクラス
 *
 * @author Veritrans, Inc.
 *
 */
class EmCancelRequestDto extends AbstractPaymentRequestDto
{

    /** 
     * 決済サービスタイプ<br>
     * 半角英数字<br>
     * 必須項目、固定値<br>
     */
    private $SERVICE_TYPE = "em";

    /** 
     * 決済サービスコマンド<br>
     * 半角英数字<br>
     * 必須項目、固定値<br>
     */
    private $SERVICE_COMMAND = "Cancel";

    /** 
     * 決済サービスオプション<br>
     * 半角英数字<br>
     * 配列<br>
     * 10 文字以内<br>
     * 決済サービスのオプションを指定します<br>
     * 例） モバイル-メール決済の場合： "suica-mobile-mail"<br>
     * Edy<br>
     * 未使用<br>
     * Suica<br>
     * "suica-mobile-mail"<br>
     * 　　：モバイル-メール決済<br>
     * "suica-mobile-app"<br>
     * 　　：モバイル-アプリ決済<br>
     * "suica-pc-mail"<br>
     * 　　：ネット-メール決済<br>
     * "suica-pc-app"<br>
     * 　　：ネット-アプリ決済<br>
     */
    private $serviceOptionType;

    /** 
     * 取引ID<br>
     * 半角英数字<br>
     * 100 文字以内<br>
     * 取り消し処理を依頼する、マーチャント側で発番済みの注文管理ID<br>
     * ※Suicaに限り40桁を上限とする。<br>
     * Edy<br>
     * 未使用<br>
     * Suica<br>
     * 必須<br>
     */
    private $orderId;

    /** 
     * オーダー種別<br>
     * 半角英数字<br>
     * 10 文字以内<br>
     * オーダー種別を指定します。<br>
     * Edy<br>
     * 未使用<br>
     * Suica<br>
     * "authorize"： 決済<br>
     * "refund"： 返金<br>
     * "refund_new"： 新規返金<br>
     * "give"： プレゼント請求<br>
     */
    private $orderKind;

    /** 
     * 取消通知メールアドレス<br>
     * 半角英数字<br>
     * 256 文字以内<br>
     * 決済の取消完了を利用者に通知するためのメールアドレスを指定します。<br>
     * Edy<br>
     * 未使用<br>
     * Suica<br>
     * 必須<br>
     */
    private $cancelMailAddr;

    /** 
     * ログ用文字列(マスク済み)<br>
     * 半角英数字<br>
     */
    private  $maskedLog;

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
     * 決済サービスオプションを取得する<br>
     * @return 決済サービスオプション<br>
     */
    public function getServiceOptionType() {
        return $this->serviceOptionType;
    }

    /**
     * 決済サービスオプションを設定する<br>
     * @param  serviceOptionType 決済サービスオプション<br>
     */
    public function setServiceOptionType($serviceOptionType) {
        $this->serviceOptionType = $serviceOptionType;
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
     * @param  orderId 取引ID<br>
     */
    public function setOrderId($orderId) {
        $this->orderId = $orderId;
    }

    /**
     * オーダー種別を取得する<br>
     * @return オーダー種別<br>
     */
    public function getOrderKind() {
        return $this->orderKind;
    }

    /**
     * オーダー種別を設定する<br>
     * @param  orderKind オーダー種別<br>
     */
    public function setOrderKind($orderKind) {
        $this->orderKind = $orderKind;
    }

    /**
     * 取消通知メールアドレスを取得する<br>
     * @return 取消通知メールアドレス<br>
     */
    public function getCancelMailAddr() {
        return $this->cancelMailAddr;
    }

    /**
     * 取消通知メールアドレスを設定する<br>
     * @param  cancelMailAddr 取消通知メールアドレス<br>
     */
    public function setCancelMailAddr($cancelMailAddr) {
        $this->cancelMailAddr = $cancelMailAddr;
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


    /**
     * 拡張パラメータ<br>
     * 並列処理用の拡張パラメータを保持する。
     */
    private $optionParams;

    /**
     * 拡張パラメータリストを取得する<br>
     * @return 拡張パラメータリスト<br>
     */
    public function getOptionParams()
    {
        return $this->optionParams;
    }

    /**
     * 拡張パラメータリストを設定する<br>
     * @param  optionParams 拡張パラメータリスト<br>
     */
    public function setOptionParams($optionParams)
    {
        $this->optionParams = $optionParams;
    }

}
?>
