<?php
/**
 * 検索結果:SaisonトランザクションAPI情報電文のクラス
 *
 * @author Veritrans, Inc.
 *
 */
class TransactionApi {

    /**
     * API種別<br>
     */
    private $saApiKind;

    /**
     * リクエスト日時<br>
     */
    private $requestDatetime;

    /**
     * リクエストID<br>
     */
    private $requestId;

    /**
     * 対象API取引ID<br>
     */
    private $targetDealId;

    /**
     * 利用ポイント数<br>
     */
    private $usePoint;

    /**
     * 付与ポイント数<br>
     */
    private $grantPoint;

    /**
     * 追加情報（利用年月日）<br>
     */
    private $addInfoUseDate;

    /**
     * 追加情報（店舗名）<br>
     */
    private $addInfoShopName;

    /**
     * 追加情報（ウォレット決済金額）<br>
     */
    private $addInfoWalletAmount;

    /**
     * 追加情報4<br>
     */
    private $addInfo4;

    /**
     * 追加情報5<br>
     */
    private $addInfo5;

    /**
     * レスポンス日時<br>
     */
    private $aqResponseDatetime;

    /**
     * API取引ID<br>
     */
    private $aqDealId;

    /**
     * 処理後ポイント残高<br>
     */
    private $aqPointBalance;



    /**
     * API種別を設定する<br>
     * @param saApiKind API種別<br>
     */
    public function setSaApiKind($saApiKind) {
        $this->saApiKind = $saApiKind;
    }

    /**
     * API種別を取得する<br>
     * @return API種別<br>
     */
    public function getSaApiKind() {
        return $this->saApiKind;
    }

    /**
     * リクエスト日時を設定する<br>
     * @param requestDatetime リクエスト日時<br>
     */
    public function setRequestDatetime($requestDatetime) {
        $this->requestDatetime = $requestDatetime;
    }

    /**
     * リクエスト日時を取得する<br>
     * @return リクエスト日時<br>
     */
    public function getRequestDatetime() {
        return $this->requestDatetime;
    }

    /**
     * リクエストIDを設定する<br>
     * @param requestId リクエストID<br>
     */
    public function setRequestId($requestId) {
        $this->requestId = $requestId;
    }

    /**
     * リクエストIDを取得する<br>
     * @return リクエストID<br>
     */
    public function getRequestId() {
        return $this->requestId;
    }

    /**
     * 対象API取引IDを設定する<br>
     * @param targetDealId 対象API取引ID<br>
     */
    public function setTargetDealId($targetDealId) {
        $this->targetDealId = $targetDealId;
    }

    /**
     * 対象API取引IDを取得する<br>
     * @return 対象API取引ID<br>
     */
    public function getTargetDealId() {
        return $this->targetDealId;
    }

    /**
     * 利用ポイント数を設定する<br>
     * @param usePoint 利用ポイント数<br>
     */
    public function setUsePoint($usePoint) {
        $this->usePoint = $usePoint;
    }

    /**
     * 利用ポイント数を取得する<br>
     * @return 利用ポイント数<br>
     */
    public function getUsePoint() {
        return $this->usePoint;
    }

    /**
     * 付与ポイント数を設定する<br>
     * @param grantPoint 付与ポイント数<br>
     */
    public function setGrantPoint($grantPoint) {
        $this->grantPoint = $grantPoint;
    }

    /**
     * 付与ポイント数を取得する<br>
     * @return 付与ポイント数<br>
     */
    public function getGrantPoint() {
        return $this->grantPoint;
    }

    /**
     * 追加情報（利用年月日）を設定する<br>
     * @param addInfoUseDate 追加情報（利用年月日）<br>
     */
    public function setAddInfoUseDate($addInfoUseDate) {
        $this->addInfoUseDate = $addInfoUseDate;
    }

    /**
     * 追加情報（利用年月日）を取得する<br>
     * @return 追加情報（利用年月日）<br>
     */
    public function getAddInfoUseDate() {
        return $this->addInfoUseDate;
    }

    /**
     * 追加情報（店舗名）を設定する<br>
     * @param addInfoShopName 追加情報（店舗名）<br>
     */
    public function setAddInfoShopName($addInfoShopName) {
        $this->addInfoShopName = $addInfoShopName;
    }

    /**
     * 追加情報（店舗名）を取得する<br>
     * @return 追加情報（店舗名）<br>
     */
    public function getAddInfoShopName() {
        return $this->addInfoShopName;
    }

    /**
     * 追加情報（ウォレット決済金額）を設定する<br>
     * @param addInfoWalletAmount 追加情報（ウォレット決済金額）<br>
     */
    public function setAddInfoWalletAmount($addInfoWalletAmount) {
        $this->addInfoWalletAmount = $addInfoWalletAmount;
    }

    /**
     * 追加情報（ウォレット決済金額）を取得する<br>
     * @return 追加情報（ウォレット決済金額）<br>
     */
    public function getAddInfoWalletAmount() {
        return $this->addInfoWalletAmount;
    }

    /**
     * 追加情報4を設定する<br>
     * @param addInfo4 追加情報4<br>
     */
    public function setAddInfo4($addInfo4) {
        $this->addInfo4 = $addInfo4;
    }

    /**
     * 追加情報4を取得する<br>
     * @return 追加情報4<br>
     */
    public function getAddInfo4() {
        return $this->addInfo4;
    }

    /**
     * 追加情報5を設定する<br>
     * @param addInfo5 追加情報5<br>
     */
    public function setAddInfo5($addInfo5) {
        $this->addInfo5 = $addInfo5;
    }

    /**
     * 追加情報5を取得する<br>
     * @return 追加情報5<br>
     */
    public function getAddInfo5() {
        return $this->addInfo5;
    }

    /**
     * レスポンス日時を設定する<br>
     * @param aqResponseDatetime レスポンス日時<br>
     */
    public function setAqResponseDatetime($aqResponseDatetime) {
        $this->aqResponseDatetime = $aqResponseDatetime;
    }

    /**
     * レスポンス日時を取得する<br>
     * @return レスポンス日時<br>
     */
    public function getAqResponseDatetime() {
        return $this->aqResponseDatetime;
    }

    /**
     * API取引IDを設定する<br>
     * @param aqDealId API取引ID<br>
     */
    public function setAqDealId($aqDealId) {
        $this->aqDealId = $aqDealId;
    }

    /**
     * API取引IDを取得する<br>
     * @return API取引ID<br>
     */
    public function getAqDealId() {
        return $this->aqDealId;
    }

    /**
     * 処理後ポイント残高を設定する<br>
     * @param aqPointBalance 処理後ポイント残高<br>
     */
    public function setAqPointBalance($aqPointBalance) {
        $this->aqPointBalance = $aqPointBalance;
    }

    /**
     * 処理後ポイント残高を取得する<br>
     * @return 処理後ポイント残高<br>
     */
    public function getAqPointBalance() {
        return $this->aqPointBalance;
    }



}
?>