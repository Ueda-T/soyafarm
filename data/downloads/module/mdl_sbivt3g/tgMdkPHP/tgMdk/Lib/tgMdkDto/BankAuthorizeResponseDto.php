<?php
/**
 * 決済サービスタイプ：銀行決済、コマンド名：決済の応答Dtoクラス
 *
 * @author Veritrans, Inc.
 *
 */
class BankAuthorizeResponseDto extends MdkBaseDto
{

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
     * 電文ID<br>
     */
    private $marchTxn;

    /** 
     * 取引ID<br>
     */
    private $orderId;

    /** 
     * 取引毎に付くID<br>
     */
    private $custTxn;

    /** 
     * リクエストID<br>
     */
    private $requestId;

    /** 
     * 収納機関番号<br>
     */
    private $shunoKikanNo;

    /** 
     * お客様番号<br>
     */
    private $customerNo;

    /** 
     * 確認番号<br>
     */
    private $confirmNo;

    /** 
     * 支払パターン<br>
     */
    private $billPattern;

    /** 
     * 支払暗号文字列<br>
     */
    private $bill;

    /** 
     * URL<br>
     */
    private $url;

    /** 
     * 画面情報<br>
     */
    private $view;

    /** 
     * MDK バージョン<br>
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
     * リクエストIDを取得する<br>
     * @return リクエストID<br>
     */
    public function getRequestId() {
        return $this->requestId;
    }

    /**
     * リクエストIDを設定する<br>
     * @param  requestId リクエストID<br>
     */
    public function setRequestId($requestId) {
        $this->requestId = $requestId;
    }

    /**
     * 収納機関番号を取得する<br>
     * @return 収納機関番号<br>
     */
    public function getShunoKikanNo() {
        return $this->shunoKikanNo;
    }

    /**
     * 収納機関番号を設定する<br>
     * @param  shunoKikanNo 収納機関番号<br>
     */
    public function setShunoKikanNo($shunoKikanNo) {
        $this->shunoKikanNo = $shunoKikanNo;
    }

    /**
     * お客様番号を取得する<br>
     * @return お客様番号<br>
     */
    public function getCustomerNo() {
        return $this->customerNo;
    }

    /**
     * お客様番号を設定する<br>
     * @param  customerNo お客様番号<br>
     */
    public function setCustomerNo($customerNo) {
        $this->customerNo = $customerNo;
    }

    /**
     * 確認番号を取得する<br>
     * @return 確認番号<br>
     */
    public function getConfirmNo() {
        return $this->confirmNo;
    }

    /**
     * 確認番号を設定する<br>
     * @param  confirmNo 確認番号<br>
     */
    public function setConfirmNo($confirmNo) {
        $this->confirmNo = $confirmNo;
    }

    /**
     * 支払パターンを取得する<br>
     * @return 支払パターン<br>
     */
    public function getBillPattern() {
        return $this->billPattern;
    }

    /**
     * 支払パターンを設定する<br>
     * @param  billPattern 支払パターン<br>
     */
    public function setBillPattern($billPattern) {
        $this->billPattern = $billPattern;
    }

    /**
     * 支払暗号文字列を取得する<br>
     * @return 支払暗号文字列<br>
     */
    public function getBill() {
        return $this->bill;
    }

    /**
     * 支払暗号文字列を設定する<br>
     * @param  bill 支払暗号文字列<br>
     */
    public function setBill($bill) {
        $this->bill = $bill;
    }

    /**
     * URLを取得する<br>
     * @return URL<br>
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * URLを設定する<br>
     * @param  url URL<br>
     */
    public function setUrl($url) {
        $this->url = $url;
    }

    /**
     * 画面情報を取得する<br>
     * @return 画面情報<br>
     */
    public function getView() {
        return $this->view;
    }

    /**
     * 画面情報を設定する<br>
     * @param  view 画面情報<br>
     */
    public function setView($view) {
        $this->view = $view;
    }

    /**
     * MDK バージョンを取得する<br>
     * @return MDK バージョン<br>
     */
    public function getTxnVersion() {
        return $this->txnVersion;
    }

    /**
     * MDK バージョンを設定する<br>
     * @param  txnVersion MDK バージョン<br>
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
