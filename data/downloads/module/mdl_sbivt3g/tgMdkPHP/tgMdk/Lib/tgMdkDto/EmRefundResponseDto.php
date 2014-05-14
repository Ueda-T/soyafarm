<?php
/**
 * 決済サービスタイプ：電子マネー、コマンド名：返金の応答Dtoクラス
 *
 * @author Veritrans, Inc.
 *
 */
class EmRefundResponseDto extends MdkBaseDto
{

    /** 
     * 決済サービスタイプ<br>
     */
    private $serviceType;

    /** 
     * 処理結果コード<br>
     * 半角英数字<br>
     * 32 文字以内<br>
     * 返金請求処理後、応答電文に含まれる値。<br>
     * 以下の処理結果のいずれかが格納される<br>
     * ・success：正常終了<br>
     * ・failure：異常終了<br>
     * ・pending：保留状態<br>
     */
    private $mstatus;

    /** 
     * 詳細結果コード<br>
     * 半角英数字<br>
     * 16 文字以内<br>
     * 結果コード<br>
     * 例) E001000100000000<br>
     */
    private $vResultCode;

    /** 
     * エラーメッセージ<br>
     * 文字列<br>
     * 1024 バイト以内<br>
     * エラーメッセージ<br>
     */
    private $merrMsg;

    /** 
     * 電文ID<br>
     */
    private $marchTxn;

    /** 
     * 取引ID<br>
     * 半角英数字<br>
     * 100 文字以内<br>
     * マーチャント側で返金取引を一意に表す注文管理ID<br>
     */
    private $orderId;

    /** 
     * 取引毎に付くID<br>
     */
    private $custTxn;

    /** 
     * 受付番号<br>
     * 半角英数字<br>
     * 32 文字以内<br>
     * 決済センターへ正常に返金請求が完了した際に決済センターで採番される受付番号<br>
     */
    private $receiptNo;

    /** 
     * 請求番号<br>
     * 半角英数字<br>
     * 25文字以内<br>
     * 3GPSGWが発番する請求に対するIDが格納されます。<br>
     */
    private $invoiceId;

    /** 
     * 決済アプリ起動URL<br>
     * 半角英数字<br>
     * 384 文字以内<br>
     * 支払/受取アプリ起動URL<br>
     * Edy<br>
     * 未使用： Mobile Edy<br>
     * Edy Viewerの起動URL： Cyber Edy<br>
     * Suica<br>
     * 未使用： メール決済<br>
     * モバイルSuica アプリURL： アプリ決済<br>
     */
    private $appUrl;

    /** 
     * MDKバージョン<br>
     * 半角英数字<br>
     * 5 桁<br>
     * 電文のバージョン番号。<br>
     */
    private $txnVersion;

    /** 
     * 結果XML(マスク済み)<br>
     * 半角英数字<br>
     */
    private  $resultXml;

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
        return $this->serviceType;
    }

    /**
     * 決済サービスタイプを設定する<br>
     * @param  serviceType 決済サービスタイプ<br>
     */
    public function setServiceType($serviceType) {
        $this->serviceType = $serviceType;
    }

    /**
     * 処理結果コードを取得する<br>
     * @return 処理結果コード<br>
     */
    public function getMstatus() {
        return $this->mstatus;
    }

    /**
     * 処理結果コードを設定する<br>
     * @param  mstatus 処理結果コード<br>
     */
    public function setMstatus($mstatus) {
        $this->mstatus = $mstatus;
    }

    /**
     * 詳細結果コードを取得する<br>
     * @return 詳細結果コード<br>
     */
    public function getVResultCode() {
        return $this->vResultCode;
    }

    /**
     * 詳細結果コードを設定する<br>
     * @param  vResultCode 詳細結果コード<br>
     */
    public function setVResultCode($vResultCode) {
        $this->vResultCode = $vResultCode;
    }

    /**
     * エラーメッセージを取得する<br>
     * @return エラーメッセージ<br>
     */
    public function getMerrMsg() {
        return $this->merrMsg;
    }

    /**
     * エラーメッセージを設定する<br>
     * @param  merrMsg エラーメッセージ<br>
     */
    public function setMerrMsg($merrMsg) {
        $this->merrMsg = $merrMsg;
    }

    /**
     * 電文IDを取得する<br>
     * @return 電文ID<br>
     */
    public function getMarchTxn() {
        return $this->marchTxn;
    }

    /**
     * 電文IDを設定する<br>
     * @param  marchTxn 電文ID<br>
     */
    public function setMarchTxn($marchTxn) {
        $this->marchTxn = $marchTxn;
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
     * 取引毎に付くIDを取得する<br>
     * @return 取引毎に付くID<br>
     */
    public function getCustTxn() {
        return $this->custTxn;
    }

    /**
     * 取引毎に付くIDを設定する<br>
     * @param  custTxn 取引毎に付くID<br>
     */
    public function setCustTxn($custTxn) {
        $this->custTxn = $custTxn;
    }

    /**
     * 受付番号を取得する<br>
     * @return 受付番号<br>
     */
    public function getReceiptNo() {
        return $this->receiptNo;
    }

    /**
     * 受付番号を設定する<br>
     * @param  receiptNo 受付番号<br>
     */
    public function setReceiptNo($receiptNo) {
        $this->receiptNo = $receiptNo;
    }

    /**
     * 請求番号を取得する<br>
     * @return 請求番号<br>
     */
    public function getInvoiceId() {
        return $this->invoiceId;
    }

    /**
     * 請求番号を設定する<br>
     * @param  invoiceId 請求番号<br>
     */
    public function setInvoiceId($invoiceId) {
        $this->invoiceId = $invoiceId;
    }

    /**
     * 決済アプリ起動URLを取得する<br>
     * @return 決済アプリ起動URL<br>
     */
    public function getAppUrl() {
        return $this->appUrl;
    }

    /**
     * 決済アプリ起動URLを設定する<br>
     * @param  appUrl 決済アプリ起動URL<br>
     */
    public function setAppUrl($appUrl) {
        $this->appUrl = $appUrl;
    }

    /**
     * MDKバージョンを取得する<br>
     * @return MDKバージョン<br>
     */
    public function getTxnVersion() {
        return $this->txnVersion;
    }

    /**
     * MDKバージョンを設定する<br>
     * @param  txnVersion MDKバージョン<br>
     */
    public function setTxnVersion($txnVersion) {
        $this->txnVersion = $txnVersion;
    }

    /**
     * 結果XML(マスク済み)を設定する<br>
     * @param  resultXml 結果XML(マスク済み)<br>
     */
    public function _setResultXml($resultXml) {
        $this->resultXml = $resultXml;
    }

    /**
     * 結果XML(マスク済み)を取得する<br>
     * @return 結果XML(マスク済み)<br>
     */
    public function __toString() {
        return (string)$this->resultXml;
    }


    /**
     * レスポンスのXMLからTradURLを取得します<br>
     *
     * @return レスポンスのXMLに含まれていた広告用（Trad）URL<br>
     *         エレメントが無いか、エレメントに内容が無ければnullを返す<br>
     */
    public function getTradUrl() {
        $processor = new TGMDK_ResElementProcessor($this->__toString());
        return $processor->get_trad_url();
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
