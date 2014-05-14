<?php
/**
 * 決済サービスタイプ：Search 応答Dtoクラス<br>
 *
 * @author Veritrans, Inc.
 *
 */
class SearchResponseDto extends MdkBaseDto{

    /**
     * 決済サービスタイプ<br>
     */
    private $serviceType;

    /**
     * 取引ID<br>
     */
    private $orderId;
    /**
     *
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
     * 最大件数超えフラグ<br>
     */
    private $overMaxCountFlag;

    /**
     * 検索結果件数（オーダー件数）<br>
     */
    private $searchCount;

    /**
     * オーダー情報リスト<br>
     */
    private $orderInfos;

    /**
     * マスタ情報リスト<br>
     */
    private $masterInfos;

    /**
     * 電文ID<br>
     */
    private $marchTxn;

    /**
     * 取引毎に付くID<br>
     */
    private $custTxn;

    /**
     * MDKバージョン<br>
     */
    private $txnVersion;

    /**
     * 結果XML(マスク済み)<br>
     * 半角英数字<br>
     */
    private $resultXml;

    /**
     * コンストラクタ。
     */
    public function __construct() {
        $this->orderInfos = new MdkBaseDto();
        $this->masterInfos = new MdkBaseDto();
    }

    /**
     * 決済サービスタイプを取得する<br>
     * @return $this->決済サービスタイプ<br>
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
     * 取引IDを取得する<br>
     * @return 取引ID<br>
     */
    public function getOrderId() {
        return $this->orderId;
    }

    /**
     * 取引IDを設定する<br>
     * @param  $orderId 取引ID<br>
     */
    public function setOrderId($orderId) {
        $this->orderId = $orderId;
    }

    /**
     * 処理結果コードを取得する<br>
     * @return $this->処理結果コード<br>
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
     * @return $this->詳細結果コード<br>
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
     * @return $this->エラーメッセージ<br>
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
     * 最大件数超えフラグを取得する<br>
     * @return $this->最大件数超えフラグ<br>
     */
    public function getOverMaxCountFlag() {
        return $this->overMaxCountFlag;
    }

    /**
     * 最大件数超えフラグを設定する<br>
     * @param  overMaxCountFlag 最大件数超えフラグ<br>
     */
    public function setOverMaxCountFlag($overMaxCountFlag) {
        $this->overMaxCountFlag = $overMaxCountFlag;
    }

    /**
     * 検索結果件数（オーダー件数）を取得する<br>
     * @return $this->検索結果件数（オーダー件数）<br>
     */
    public function getSearchCount() {
        return $this->searchCount;
    }

    /**
     * 検索結果件数（オーダー件数）を設定する<br>
     * @param  searchCount 検索結果件数（オーダー件数）<br>
     */
    public function setSearchCount($searchCount) {
        $this->searchCount = $searchCount;
    }


    /**
     * 電文IDを取得する<br>
     * @return $this->電文ID<br>
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
     * 取引毎に付くIDを取得する<br>
     * @return $this->取引毎に付くID<br>
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
     * MDKバージョンを取得する<br>
     * @return $this->MDKバージョン<br>
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
     * オーダー情報リストを取得する<br>
     * @return $this->オーダー情報リスト<br>
     */
    public function getOrderInfos() {
        return $this->orderInfos;
    }

    /**
     * オーダー情報リストを設定する<br>
     * @param  orderInfos オーダー情報リスト<br>
     */
    public function setOrderInfos($orderInfos) {
        $this->orderInfos = $orderInfos;
    }

    /**
     * マスタ情報リストを取得する<br>
     * @return マスタ情報リスト<br>
     */
    public function getMasterInfos() {
        return $this->masterInfos;
    }

    /**
     * マスタ情報リストを設定する<br>
     * @param  masterInfos マスタ情報リスト<br>
     */
    public function setMasterInfos($masterInfos) {
        $this->masterInfos = $masterInfos;
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
     * @return $this->結果XML(マスク済み)<br>
     */
    public function __toString() {
        return (string)$this->resultXml;
    }

}
?>
