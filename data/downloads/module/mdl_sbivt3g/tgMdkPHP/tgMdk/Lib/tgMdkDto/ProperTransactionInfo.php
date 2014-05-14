<?php
/**
 * 検索結果:固有トランザクション情報のクラス<br>
 *
 * @author Veritrans, Inc.
 */
class ProperTransactionInfo {

    /**
     * トランザクション種別<br>
     */
    private $txnKind;
    /**
     * 取引対象タイプ<br>
     */
    private $emTxnType;
    /**
     * 取引日時<br>
     */
    private $centerProcDatetime;
    /**
     * 取引対象タイプ<br>
     */
    private $cvsTxnType;
    /**
     * 対象取引タイプ<br>
     */
    private $peTxnType;
    /**
     * 受付番号<br>
     */
    private $receiptNo;
    /**
     * 取引日時<br>
     */
    private $startDatetime;
    /**
     * カードトランザクションタイプ<br>
     */
    private $cardTransactionType;
    /**
     * ゲートウェイ要求日時<br>
     */
    private $gatewayRequestDate;
    /**
     * ゲートウェイ応答日時<br>
     */
    private $gatewayResponseDate;
    /**
     * センター要求日時<br>
     */
    private $centerRequestDate;
    /**
     * センター応答日時<br>
     */
    private $centerResponseDate;
    /**
     * センター要求番号<br>
     */
    private $centerRequestNumber;
    /**
     * センターリファレンス番号<br>
     */
    private $centerReferenceNumber;
    /**
     * 要求商品コード<br>
     */
    private $reqItemCode;
    /**
     * 応答商品コード<br>
     */
    private $resItemCode;
    /**
     * 要求リターン参照番号<br>
     */
    private $reqReturnReferenceNumber;
    /**
     * 応答データ<br>
     */
    private $responsedata;
    /**
     * ペンディング<br>
     */
    private $pending;
    /**
     * ループバック<br>
     */
    private $loopback;
    /**
     * 接続先カード接続センター<br>
     */
    private $connectedCenterId;
    /**
     * 要求カード番号<br>
     */
    private $reqCardNumber;
    /**
     * 要求カード有効期限<br>
     */
    private $reqCardExpire;
    /**
     * 要求カードオプションタイプ<br>
     */
    private $reqAmount;
    /**
     * 要求取引金額<br>
     */
    private $reqCardOptionType;
    /**
     * 要求マーチャントトランザクション番号<br>
     */
    private $reqMerchantTransaction;
    /**
     * 要求承認番号<br>
     */
    private $reqAuthCode;
    /**
     * 要求仕向先コード<br>
     */
    private $reqAcquirerCode;
    /**
     * 要求カードセンター<br>
     */
    private $reqCardCenter;
    /**
     * 要求JPO情報<br>
     */
    private $reqJpoInformation;
    /**
     * 要求売上日<br>
     */
    private $reqSalesDay;
    /**
     * 要求取消日<br>
     */
    private $reqCancelDay;
    /**
     * 要求同時売上<br>
     */
    private $reqWithCapture;
    /**
     * 要求同時直接<br>
     */
    private $reqWithDirect;
    /**
     * 要求3Dメッセージバージョン<br>
     */
    private $req3dMessageVersion;
    /**
     * 要求3DトランザクションID<br>
     */
    private $req3dTransactionId;
    /**
     * 要求3Dトランザクションステータス<br>
     */
    private $req3dTransactionStatus;
    /**
     * 要求3D CAVVアルゴリズム<br>
     */
    private $req3dCavvAlgorithm;
    /**
     * 要求3D CAVV<br>
     */
    private $req3dCavv;
    /**
     * 要求3D ECI<br>
     */
    private $req3dEci;
    /**
     * 要求セキュリティコード<br>
     */
    private $reqSecurityCode;
    /**
     * 要求認証番号<br>
     */
    private $reqAuthFlag;
    /**
     * 要求誕生日<br>
     */
    private $reqBirthday;
    /**
     * 要求電話番号<br>
     */
    private $reqTel;
    /**
     * 要求カナ名前（名）<br>
     */
    private $reqFirstKanaName;
    /**
     * 要求カナ名前（姓）<br>
     */
    private $reqLastKanaName;
    /**
     * 応答マーチャントトランザクション番号<br>
     */
    private $resMerchantTransaction;
    /**
     * 応答リターン参照番号<br>
     */
    private $resReturnReferenceNumber;
    /**
     * 応答承認番号<br>
     */
    private $resAuthCode;
    /**
     * アクションコード<br>
     */
    private $resActionCode;
    /**
     * 応答センターエラーコード<br>
     */
    private $resCenterErrorCode;
    /**
     * 応答与信期間<br>
     */
    private $resAuthTerm;
    /**
     * 要求新規返品<br>
     */
    private $reqWithNew;
    /**
     * 取引金額<br>
     */
    private $amount;
    /**
     * 決済完了フラグ<br>
     */
    private $txnFixed;

    /**
     * 対象取引タイプ<br>
     */
    private $ppTxnType;
    /**
     * 取引識別子<br>
     */
    private $centerTxnId;
    /**
     * 払戻し手数料<br>
     */
    private $feeAmount;
    /**
     * 外貨換算レート<br>
     */
    private $exchangeRate;
    /**
     * 純返金金額<br>
     */
    private $netRefundAmount;

    /**
     * MPIトランザクションタイプ<br>
     */
    private $mpiTransactionType;

    /**
     * リダイレクションURI<br>
     */
    private $reqRedirectionUri;

    /**
     * カード会社ID<br>
     */
    private $corporationId;

    /**
     * ブランドID<br>
     */
    private $brandId;

    /**
     * 仕向先バイナリ<br>
     */
    private $acquirerBinary;

    /**
     * ディレクトリサービスログインID<br>
     */
    private $dsLoginId;

    /**
     * CRRSステータス<br>
     */
    private $crresStatus;

    /**
     * VERESステータス<br>
     */
    private $veresStatus;

    /**
     * PARESステータス<br>
     */
    private $paresStatus;

    /**
     * PARESサイン<br>
     */
    private $paresSign;

    /**
     * PARES ECI<br>
     */
    private $paresEci;

    /**
     * 本人認証レスポンスコード<br>
     */
    private $authResponseCode;

    /**
     * 検証レスポンスコード<br>
     */
    private $verifyResponseCode;

    /**
     * 応答3Dメッセージバージョン<br>
     */
    private $res3dMessageVersion;

    /**
     * 本人認証要求日時<br>
     */
    private $authRequestDatetime;
    /**
     * 本人認証応答日時<br>
     */
    private $authResponseDatetime;

    /**
     * 検証要求日時<br>
     */
    private $verifyRequestDatetime;
    /**
     * 検証応答日時<br>
     */
    private $verifyResponseDatetime;

    /**
     * 要求通貨単位<br>
     */
    private $reqCurrencyUnit;

    /**
     * 応答3DトランザクションID<br>
     */
    private $res3dTransactionId;

    /**
     * 応答3Dトランザクションステータス<br>
     */
    private $res3dTransactionStatus;

    /**
     * 応答3D CAVVアルゴリズム<br>
     */
    private $res3dCavvAlgorithm;

    /**
     * 応答3D CAVV <br>
     */
    private $res3dCavv;

    /**
     * 応答3D ECI<br>
     */
    private $res3dEci;

    /**
     * 永久不滅ウォレット残高<br>
     */
    private $aqAqfWalletBalance;

    /**
     * 永久不滅ポイント残高<br>
     */
    private $aqAqfPointBalance;

    /**
     * 交換後利用可能バリュー<br>
     */
    private $aqAvailableValue;

    /**
     * SaisonトランザクションAPIリスト<br>
     */
    private $transactionApis;

    /**
     * Saisonトランザクションカードリスト<br>
     */
    private $transactionCards;

    /**
    * UPOP取引対象タイプ<br>
    */
    private $upopTxnType;

    /**
    * 清算日付<br>
    */
    private $resUpopSettleDate;

    /**
    * 清算金額<br>
    */
    private $resUpopSettleAmount;

    /**
    * 清算通貨<br>
    */
    private $resUpopSettleCurrency;

    /**
    * 外貨換算日付<br>
    */
    private $resUpopExchangeDate;

    /**
    * 外貨換算レート<br>
    */
    private $resUpopExchangeRate;

    /**
     * 決済センターとの取引ID<br>
     */
    private $centerTradeId;

    /**
     * 対象取引タイプ<br>
     */
    private $alipayTxnType;

    /**
     * 清算金額<br>
     */
    private $settleAmount;

    /**
     * 清算通貨<br>
     */
    private $settleCurrency;

    /**
     * 支払い日付<br>
     */
    private $paymentTime;

    /**
     * 清算日付<br>
     */
    private $settlementTime;

    /**
     * キャリア結果コード<br>
     */
    private $crResultCode;

    /**
     * 詳細コマンドタイプ<br>
     */
    private $detailCommandType;

    /**
     * キャリアへの要求日時<br>
     */
    private $crRequestDatetime;

    /**
     * キャリアからの返戻日時<br>
     */
    private $crResponseDatetime;

    /**
     * 取引対象タイプ<br>
     */
     private $oricoTxnType;

    /**
     * 審査結果コード<br>
     */
    private $orderStateCode;

    /**
     * 承認番号<br>
     */
    private $approvalNo;

    /**
     * 申込日<br>
     */
    private $requestDate;

    /**
     * ローン元金<br>
     */
    private $loanPrincipal;

    /**
     * 支払回数<br>
     */
    private $paymentCount;

    /**
     *
     * コンストラクタ。
     */
    public function __construct() {
        $this->centerMessages = new MdkBaseDto();
        $this->transactionApis = new MdkBaseDto();
        $this->transactionCards = new MdkBaseDto();
    }

   /**
     * トランザクション種別を取得する<br>
     * @return トランザクション種別<br>
     */
    public function getTxnKind() {
        return $this->txnKind;
    }

      /**
     * トランザクション種別を設定する<br>
     * @param トランザクション種別<br>
     */
    public function setTxnKind($txnKind) {
        $this->txnKind = $txnKind;
    }

    /**
     * 取引対象タイプを取得する<br>
     *
     * @return 取引対象タイプ<br>
     */
    public function getEmTxnType() {
        return $this->emTxnType;
    }

    /**
     * 取引対象タイプを設定する<br>
     *
     * @param emTxnType 取引対象タイプ<br>
     */
    public function setEmTxnType($emTxnType) {
        $this->emTxnType = $emTxnType;
    }

    /**
     * 取引日時を取得する<br>
     *
     * @return 取引日時<br>
     */
    public function getCenterProcDatetime() {
        return $this->centerProcDatetime;
    }

    /**
     * 取引日時を設定する<br>
     *
     * @param centerProcDatetime 取引日時<br>
     */
    public function setCenterProcDatetime($centerProcDatetime) {
        $this->centerProcDatetime = $centerProcDatetime;
    }

    /**
     * 取引対象タイプを取得する<br>
     *
     * @return 取引対象タイプ<br>
     */
    public function getCvsTxnType() {
        return $this->cvsTxnType;
    }

    /**
     * 取引対象タイプを設定する<br>
     *
     * @param cvsTxnType 取引対象タイプ<br>
     */
    public function setCvsTxnType($cvsTxnType) {
        $this->cvsTxnType = $cvsTxnType;
    }

    /**
     * 対象取引タイプを取得する<br>
     *
     * @return 対象取引タイプ<br>
     */
    public function getPeTxnType() {
        return $this->peTxnType;
    }

    /**
     * 対象取引タイプを設定する<br>
     *
     * @param peTxnType 対象取引タイプ<br>
     */
    public function setPeTxnType($peTxnType) {
        $this->peTxnType = $peTxnType;
    }

    /**
     * 受付番号を取得する<br>
     *
     * @return 受付番号<br>
     */
    public function getReceiptNo() {
        return $this->receiptNo;
    }

    /**
     * 受付番号を設定する<br>
     *
     * @param receiptNo 受付番号<br>
     */
    public function setReceiptNo($receiptNo) {
        $this->receiptNo = $receiptNo;
    }

    /**
     * 取引日時を取得する<br>
     *
     * @return 取引日時<br>
     */
    public function getStartDatetime() {
        return $this->startDatetime;
    }

    /**
     * 取引日時を設定する<br>
     *
     * @param startDatetime 取引日時<br>
     */
    public function setStartDatetime($startDatetime) {
        $this->startDatetime = $startDatetime;
    }

    /**
     * カードトランザクションタイプを取得する<br>
     *
     * @return カードトランザクションタイプ<br>
     */
    public function getCardTransactionType() {
        return $this->cardTransactionType;
    }

    /**
     * カードトランザクションタイプを設定する<br>
     *
     * @param cardTransactionType カードトランザクションタイプ<br>
     */
    public function setCardTransactionType($cardTransactionType) {
        $this->cardTransactionType = $cardTransactionType;
    }

    /**
     * ゲートウェイ要求日時を取得する<br>
     *
     * @return ゲートウェイ要求日時<br>
     */
    public function getGatewayRequestDate() {
        return $this->gatewayRequestDate;
    }

    /**
     * ゲートウェイ要求日時を設定する<br>
     *
     * @param gatewayRequestDate ゲートウェイ要求日時<br>
     */
    public function setGatewayRequestDate($gatewayRequestDate) {
        $this->gatewayRequestDate = $gatewayRequestDate;
    }

    /**
     * ゲートウェイ応答日時を取得する<br>
     *
     * @return ゲートウェイ応答日時<br>
     */
    public function getGatewayResponseDate() {
        return $this->gatewayResponseDate;
    }

    /**
     * ゲートウェイ応答日時を設定する<br>
     *
     * @param gatewayResponseDate ゲートウェイ応答日時<br>
     */
    public function setGatewayResponseDate($gatewayResponseDate) {
        $this->gatewayResponseDate = $gatewayResponseDate;
    }

    /**
     * センター要求日時を取得する<br>
     *
     * @return センター要求日時<br>
     */
    public function getCenterRequestDate() {
        return $this->centerRequestDate;
    }

    /**
     * センター要求日時を設定する<br>
     *
     * @param centerRequestDate センター要求日時<br>
     */
    public function setCenterRequestDate($centerRequestDate) {
        $this->centerRequestDate = $centerRequestDate;
    }

    /**
     * センター応答日時を取得する<br>
     *
     * @return センター応答日時<br>
     */
    public function getCenterResponseDate() {
        return $this->centerResponseDate;
    }

    /**
     * センター応答日時を設定する<br>
     *
     * @param centerResponseDate センター応答日時<br>
     */
    public function setCenterResponseDate($centerResponseDate) {
        $this->centerResponseDate = $centerResponseDate;
    }

    /**
     * センター要求番号を取得する<br>
     *
     * @return センター要求番号<br>
     */
    public function getCenterRequestNumber() {
        return $this->centerRequestNumber;
    }

    /**
     * センター要求番号を設定する<br>
     *
     * @param centerRequestNumber センター要求番号<br>
     */
    public function setCenterRequestNumber($centerRequestNumber) {
        $this->centerRequestNumber = $centerRequestNumber;
    }

    /**
     * センターリファレンス番号を取得する<br>
     *
     * @return センターリファレンス番号<br>
     */
    public function getCenterReferenceNumber() {
        return $this->centerReferenceNumber;
    }

    /**
     * センターリファレンス番号を設定する<br>
     *
     * @param centerReferenceNumber センターリファレンス番号<br>
     */
    public function setCenterReferenceNumber($centerReferenceNumber) {
        $this->centerReferenceNumber = $centerReferenceNumber;
    }

    /**
     * 要求商品コードを取得する<br>
     *
     * @return 要求商品コード<br>
     */
    public function getReqItemCode() {
        return $this->reqItemCode;
    }

    /**
     * 要求商品コードを設定する<br>
     *
     * @param reqItemCode 要求商品コード<br>
     */
    public function setReqItemCode($reqItemCode) {
        $this->reqItemCode = $reqItemCode;
    }

    /**
     * 応答商品コードを取得する<br>
     *
     * @return 応答商品コード<br>
     */
    public function getResItemCode() {
        return $this->resItemCode;
    }

    /**
     * 応答商品コードを設定する<br>
     *
     * @param resItemCode 応答商品コード<br>
     */
    public function setResItemCode($resItemCode) {
        $this->resItemCode = $resItemCode;
    }

    /**
     * 要求リターン参照番号を取得する<br>
     *
     * @return 要求リターン参照番号<br>
     */
    public function getReqReturnReferenceNumber() {
        return $this->reqReturnReferenceNumber;
    }

    /**
     * 要求リターン参照番号を設定する<br>
     *
     * @param reqReturnReferenceNumber 要求リターン参照番号<br>
     */
    public function setReqReturnReferenceNumber($reqReturnReferenceNumber) {
        $this->reqReturnReferenceNumber = $reqReturnReferenceNumber;
    }

    /**
     * 応答データを取得する<br>
     *
     * @return 応答データ<br>
     */
    public function getResponsedata() {
        return $this->responsedata;
    }

    /**
     * 応答データを設定する<br>
     *
     * @param responsedata 応答データ<br>
     */
    public function setResponsedata($responsedata) {
        $this->responsedata = $responsedata;
    }

    /**
     * ペンディングを取得する<br>
     *
     * @return ペンディング<br>
     */
    public function getPending() {
        return $this->pending;
    }

    /**
     * ペンディングを設定する<br>
     *
     * @param pending ペンディング<br>
     */
    public function setPending($pending) {
        $this->pending = $pending;
    }

    /**
     * ループバックを取得する<br>
     *
     * @return ループバック<br>
     */
    public function getLoopback() {
        return $this->loopback;
    }

    /**
     * ループバックを設定する<br>
     *
     * @param loopback ループバック<br>
     */
    public function setLoopback($loopback) {
        $this->loopback = $loopback;
    }

    /**
     * 接続先カード接続センターを取得する<br>
     *
     * @return 接続先カード接続センター<br>
     */
    public function getConnectedCenterId() {
        return $this->connectedCenterId;
    }

    /**
     * 接続先カード接続センターを設定する<br>
     *
     * @param connectedCenterId 接続先カード接続センター<br>
     */
    public function setConnectedCenterId($connectedCenterId) {
        $this->connectedCenterId = $connectedCenterId;
    }

    /**
     * 要求カード番号を取得する<br>
     *
     * @return 要求カード番号<br>
     */
    public function getReqCardNumber() {
        return $this->reqCardNumber;
    }

    /**
     * 要求カード番号を設定する<br>
     *
     * @param reqCardNumber 要求カード番号<br>
     */
    public function setReqCardNumber($reqCardNumber) {
        $this->reqCardNumber = $reqCardNumber;
    }

    /**
     * 要求カード有効期限を取得する<br>
     *
     * @return 要求カード有効期限<br>
     */
    public function getReqCardExpire() {
        return $this->reqCardExpire;
    }

    /**
     * 要求カード有効期限を設定する<br>
     *
     * @param reqCardExpire 要求カード有効期限<br>
     */
    public function setReqCardExpire($reqCardExpire) {
        $this->reqCardExpire = $reqCardExpire;
    }

    /**
     * 要求カードオプションタイプを取得する<br>
     *
     * @return 要求カードオプションタイプ<br>
     */
    public function getReqAmount() {
        return $this->reqAmount;
    }

    /**
     * 要求カードオプションタイプを設定する<br>
     *
     * @param reqAmount 要求カードオプションタイプ<br>
     */
    public function setReqAmount($reqAmount) {
        $this->reqAmount = $reqAmount;
    }

    /**
     * 要求取引金額を取得する<br>
     *
     * @return 要求取引金額<br>
     */
    public function getReqCardOptionType() {
        return $this->reqCardOptionType;
    }

    /**
     * 要求取引金額を設定する<br>
     *
     * @param reqCardOptionType 要求取引金額<br>
     */
    public function setReqCardOptionType($reqCardOptionType) {
        $this->reqCardOptionType = $reqCardOptionType;
    }

    /**
     * 要求マーチャントトランザクション番号を取得する<br>
     *
     * @return 要求マーチャントトランザクション番号<br>
     */
    public function getReqMerchantTransaction() {
        return $this->reqMerchantTransaction;
    }

    /**
     * 要求マーチャントトランザクション番号を設定する<br>
     *
     * @param reqMerchantTransaction 要求マーチャントトランザクション番号<br>
     */
    public function setReqMerchantTransaction($reqMerchantTransaction) {
        $this->reqMerchantTransaction = $reqMerchantTransaction;
    }

    /**
     * 要求承認番号を取得する<br>
     *
     * @return 要求承認番号<br>
     */
    public function getReqAuthCode() {
        return $this->reqAuthCode;
    }

    /**
     * 要求承認番号を設定する<br>
     *
     * @param reqAuthCode 要求承認番号<br>
     */
    public function setReqAuthCode($reqAuthCode) {
        $this->reqAuthCode = $reqAuthCode;
    }

    /**
     * 要求仕向先コードを取得する<br>
     *
     * @return 要求仕向先コード<br>
     */
    public function getReqAcquirerCode() {
        return $this->reqAcquirerCode;
    }

    /**
     * 要求仕向先コードを設定する<br>
     *
     * @param reqAcquirerCode 要求仕向先コード<br>
     */
    public function setReqAcquirerCode($reqAcquirerCode) {
        $this->reqAcquirerCode = $reqAcquirerCode;
    }

    /**
     * 要求カードセンターを取得する<br>
     *
     * @return 要求カードセンター<br>
     */
    public function getReqCardCenter() {
        return $this->reqCardCenter;
    }

    /**
     * 要求カードセンターを設定する<br>
     *
     * @param reqCardCenter 要求カードセンター<br>
     */
    public function setReqCardCenter($reqCardCenter) {
        $this->reqCardCenter = $reqCardCenter;
    }

    /**
     * 要求JPO情報を取得する<br>
     *
     * @return 要求JPO情報<br>
     */
    public function getReqJpoInformation() {
        return $this->reqJpoInformation;
    }

    /**
     * 要求JPO情報を設定する<br>
     *
     * @param reqJpoInformation 要求JPO情報<br>
     */
    public function setReqJpoInformation($reqJpoInformation) {
        $this->reqJpoInformation = $reqJpoInformation;
    }

    /**
     * 要求売上日を取得する<br>
     *
     * @return 要求売上日<br>
     */
    public function getReqSalesDay() {
        return $this->reqSalesDay;
    }

    /**
     * 要求売上日を設定する<br>
     *
     * @param reqSalesDay 要求売上日<br>
     */
    public function setReqSalesDay($reqSalesDay) {
        $this->reqSalesDay = $reqSalesDay;
    }

    /**
     * 要求取消日を取得する<br>
     *
     * @return 要求取消日<br>
     */
    public function getReqCancelDay() {
        return $this->reqCancelDay;
    }

    /**
     * 要求取消日を設定する<br>
     *
     * @param reqCancelDay 要求取消日<br>
     */
    public function setReqCancelDay($reqCancelDay) {
        $this->reqCancelDay = $reqCancelDay;
    }

    /**
     * 要求同時売上を取得する<br>
     *
     * @return 要求同時売上<br>
     */
    public function getReqWithCapture() {
        return $this->reqWithCapture;
    }

    /**
     * 要求同時売上を設定する<br>
     *
     * @param reqWithCapture 要求同時売上<br>
     */
    public function setReqWithCapture($reqWithCapture) {
        $this->reqWithCapture = $reqWithCapture;
    }

    /**
     * 要求同時直接を取得する<br>
     *
     * @return 要求同時直接<br>
     */
    public function getReqWithDirect() {
        return $this->reqWithDirect;
    }

    /**
     * 要求同時直接を設定する<br>
     *
     * @param reqWithDirect 要求同時直接<br>
     */
    public function setReqWithDirect($reqWithDirect) {
        $this->reqWithDirect = $reqWithDirect;
    }

    /**
     * 要求3Dメッセージバージョンを取得する<br>
     *
     * @return 要求3Dメッセージバージョン<br>
     */
    public function getReq3dMessageVersion() {
        return $this->req3dMessageVersion;
    }

    /**
     * 要求3Dメッセージバージョンを設定する<br>
     *
     * @param req3dMessageVersion 要求3Dメッセージバージョン<br>
     */
    public function setReq3dMessageVersion($req3dMessageVersion) {
        $this->req3dMessageVersion = $req3dMessageVersion;
    }

    /**
     * 要求3DトランザクションIDを取得する<br>
     *
     * @return 要求3DトランザクションID<br>
     */
    public function getReq3dTransactionId() {
        return $this->req3dTransactionId;
    }

    /**
     * 要求3DトランザクションIDを設定する<br>
     *
     * @param req3dTransactionId 要求3DトランザクションID<br>
     */
    public function setReq3dTransactionId($req3dTransactionId) {
        $this->req3dTransactionId = $req3dTransactionId;
    }

    /**
     * 要求3Dトランザクションステータスを取得する<br>
     *
     * @return 要求3Dトランザクションステータス<br>
     */
    public function getReq3dTransactionStatus() {
        return $this->req3dTransactionStatus;
    }

    /**
     * 要求3Dトランザクションステータスを設定する<br>
     *
     * @param setReq3dTransactionStatus 要求3Dトランザクションステータス<br>
     */
    public function setReq3dTransactionStatus($req3dTransactionStatus) {
        $this->req3dTransactionStatus = $req3dTransactionStatus;
    }

    /**
     * 要求3D CAVVアルゴリズムを取得する<br>
     *
     * @return 要求3D CAVVアルゴリズム<br>
     */
    public function getReq3dCavvAlgorithm() {
        return $this->req3dCavvAlgorithm;
    }

    /**
     * 要求3D CAVVアルゴリズムを設定する<br>
     *
     * @param req3dCavvAlgorithm 要求3D CAVVアルゴリズム<br>
     */
    public function setReq3dCavvAlgorithm($req3dCavvAlgorithm) {
        $this->req3dCavvAlgorithm = $req3dCavvAlgorithm;
    }

    /**
     * 要求3D CAVVを取得する<br>
     *
     * @return 要求3D CAVV<br>
     */
    public function getReq3dCavv() {
        return $this->req3dCavv;
    }

    /**
     * 要求3D CAVVを設定する<br>
     *
     * @param req3dCavv 要求3D CAVV<br>
     */
    public function setReq3dCavv($req3dCavv) {
        $this->req3dCavv = $req3dCavv;
    }

    /**
     * 要求3D ECIを取得する<br>
     *
     * @return 要求3D ECI<br>
     */
    public function getReq3dEci() {
        return $this->req3dEci;
    }

    /**
     * 要求3D ECIを設定する<br>
     *
     * @param req3dEci 要求3D ECI<br>
     */
    public function setReq3dEci($req3dEci) {
        $this->req3dEci = $req3dEci;
    }

    /**
     * 要求セキュリティコードを取得する<br>
     *
     * @return 要求セキュリティコード<br>
     */
    public function getReqSecurityCode() {
        return $this->reqSecurityCode;
    }

    /**
     * 要求セキュリティコードを設定する<br>
     *
     * @param reqSecurityCode 要求セキュリティコード<br>
     */
    public function setReqSecurityCode($reqSecurityCode) {
        $this->reqSecurityCode = $reqSecurityCode;
    }

    /**
     * 要求認証番号を取得する<br>
     *
     * @return 要求認証番号<br>
     */
    public function getReqAuthFlag() {
        return $this->reqAuthFlag;
    }

    /**
     * 要求認証番号を設定する<br>
     *
     * @param reqAuthFlag 要求認証番号<br>
     */
    public function setReqAuthFlag($reqAuthFlag) {
        $this->reqAuthFlag = $reqAuthFlag;
    }

    /**
     * 要求誕生日を取得する<br>
     *
     * @return 要求誕生日<br>
     */
    public function getReqBirthday() {
        return $this->reqBirthday;
    }

    /**
     * 要求誕生日を設定する<br>
     *
     * @param reqBirthday 要求誕生日<br>
     */
    public function setReqBirthday($reqBirthday) {
        $this->reqBirthday = $reqBirthday;
    }

    /**
     * 要求電話番号を取得する<br>
     *
     * @return 要求電話番号<br>
     */
    public function getReqTel() {
        return $this->reqTel;
    }

    /**
     * 要求電話番号を設定する<br>
     *
     * @param reqTel 要求電話番号<br>
     */
    public function setReqTel($reqTel) {
        $this->reqTel = $reqTel;
    }

    /**
     * 要求カナ名前（名）を取得する<br>
     *
     * @return 要求カナ名前（名）<br>
     */
    public function getReqFirstKanaName() {
        return $this->reqFirstKanaName;
    }

    /**
     * 要求カナ名前（名）を設定する<br>
     *
     * @param reqFirstKanaName 要求カナ名前（名）<br>
     */
    public function setReqFirstKanaName($reqFirstKanaName) {
        $this->reqFirstKanaName = $reqFirstKanaName;
    }

    /**
     * 要求カナ名前（姓）を取得する<br>
     *
     * @return 要求カナ名前（姓）<br>
     */
    public function getReqLastKanaName() {
        return $this->reqLastKanaName;
    }

    /**
     * 要求カナ名前（姓）を設定する<br>
     *
     * @param reqLastKanaName 要求カナ名前（姓）<br>
     */
    public function setReqLastKanaName($reqLastKanaName) {
        $this->reqLastKanaName = $reqLastKanaName;
    }

    /**
     * 応答マーチャントトランザクション番号を取得する<br>
     *
     * @return 応答マーチャントトランザクション番号<br>
     */
    public function getResMerchantTransaction() {
        return $this->resMerchantTransaction;
    }

    /**
     * 応答マーチャントトランザクション番号を設定する<br>
     *
     * @param resMerchantTransaction 応答マーチャントトランザクション番号<br>
     */
    public function setResMerchantTransaction($resMerchantTransaction) {
        $this->resMerchantTransaction = $resMerchantTransaction;
    }

    /**
     * 応答リターン参照番号を取得する<br>
     *
     * @return 応答リターン参照番号<br>
     */
    public function getResReturnReferenceNumber() {
        return $this->resReturnReferenceNumber;
    }

    /**
     * 応答リターン参照番号を設定する<br>
     *
     * @param resReturnReferenceNumber 応答リターン参照番号<br>
     */
    public function setResReturnReferenceNumber($resReturnReferenceNumber) {
        $this->resReturnReferenceNumber = $resReturnReferenceNumber;
    }

    /**
     * 応答承認番号を取得する<br>
     *
     * @return 応答承認番号<br>
     */
    public function getResAuthCode() {
        return $this->resAuthCode;
    }

    /**
     * 応答承認番号を設定する<br>
     *
     * @param resAuthCode 応答承認番号<br>
     */
    public function setResAuthCode($resAuthCode) {
        $this->resAuthCode = $resAuthCode;
    }

    /**
     * アクションコードを取得する<br>
     *
     * @return アクションコード<br>
     */
    public function getResActionCode() {
        return $this->resActionCode;
    }

    /**
     * アクションコードを設定する<br>
     *
     * @param resActionCode アクションコード<br>
     */
    public function setResActionCode($resActionCode) {
        $this->resActionCode = $resActionCode;
    }

    /**
     * 応答センターエラーコードを取得する<br>
     *
     * @return 応答センターエラーコード<br>
     */
    public function getResCenterErrorCode() {
        return $this->resCenterErrorCode;
    }

    /**
     * 応答センターエラーコードを設定する<br>
     *
     * @param resCenterErrorCode 応答センターエラーコード<br>
     */
    public function setResCenterErrorCode($resCenterErrorCode) {
        $this->resCenterErrorCode = $resCenterErrorCode;
    }

    /**
     * 応答与信期間を取得する<br>
     *
     * @return 応答与信期間<br>
     */
    public function getResAuthTerm() {
        return $this->resAuthTerm;
    }

    /**
     * 応答与信期間を設定する<br>
     *
     * @param resAuthTerm 応答与信期間<br>
     */
    public function setResAuthTerm($resAuthTerm) {
        $this->resAuthTerm = $resAuthTerm;
    }

    /**
     * 要求新規返品を取得する<br>
     *
     * @return 要求新規返品<br>
     */
    public function getReqWithNew() {
        return $this->reqWithNew;
    }

    /**
     * 要求新規返品を設定する<br>
     *
     * @param reqWithNew 要求新規返品<br>
     */
    public function setReqWithNew($reqWithNew) {
        $this->reqWithNew = $reqWithNew;
    }

    /**
     * 取引金額を取得する<br>
     *
     * @return 取引金額<br>
     */
    public function getAmount() {
        return $this->amount;
    }

    /**
     * 取引金額を設定する<br>
     *
     * @param amount 取引金額<br>
     */
    public function setAmount($amount) {
        $this->amount = $amount;
    }

    /**
     * 決済完了フラグを取得する<br>
     *
     * @return 決済完了フラグ<br>
     */
    public function getTxnFixed() {
        return $this->txnFixed;
    }

    /**
     * 決済完了フラグを設定する<br>
     *
     * @param txnFixed 決済完了フラグ<br>
     */
    public function setTxnFixed($txnFixed) {
        $this->txnFixed = $txnFixed;
    }

    /**
     * Paypal対象取引タイプを取得する<br>
     *
     * @return 対象取引タイプ<br>
     */
    public function getPpTxnType() {
        return $this->ppTxnType;
    }

    /**
     * Paypal対象取引タイプを設定する<br>
     *
     * @param ppTxnType 対象取引タイプ<br>
     */
    public function setPpTxnType($ppTxnType) {
        $this->ppTxnType = $ppTxnType;
    }

    /**
     * Paypal取引識別子を取得する<br>
     *
     * @return 取引識別子<br>
     */
    public function getCenterTxnId() {
        return $this->centerTxnId;
    }

    /**
     * Paypal取引識別子を設定する<br>
     *
     * @param centerTxnId 取引識別子<br>
     */
    public function setCenterTxnId($centerTxnId) {
        $this->centerTxnId = $centerTxnId;
    }

    /**
     * 手数料を取得する<br>
     *
     * @return 手数料<br>
     */
    public function getFeeAmount() {
        return $this->feeAmount;
    }

    /**
     * 手数料を設定する<br>
     *
     * @param feeAmount 手数料<br>
     */
    public function setFeeAmount($feeAmount) {
        $this->feeAmount = $feeAmount;
    }

    /**
     * Paypal外貨換算レートを取得する<br>
     *
     * @return 外貨換算レート<br>
     */
    public function getExchangeRate() {
        return $this->exchangeRate;
    }

    /**
     * Paypal外貨換算レートを設定する<br>
     *
     * @param $exchangeRate 外貨換算レート<br>
     */
    public function setExchangeRate($exchangeRate) {
        $this->exchangeRate = $exchangeRate;
    }

    /**
     * Paypal純返金金額を取得する<br>
     *
     * @return 純返金金額<br>
     */
    public function getNetRefundAmount() {
        return $this->netRefundAmount;
    }

    /**
     * Paypal純返金金額を設定する<br>
     *
     * @param netRefundAmount 純返金金額<br>
     */
    public function setNetRefundAmount($netRefundAmount) {
        $this->netRefundAmount = $netRefundAmount;
    }

    /**
     * MPIトランザクションタイプを取得する<br>
     *
     * @return MPIトランザクションタイプ<br>
     */
    public function getMpiTransactionType() {
        return $this->mpiTransactionType;
    }

    /**
     * MPIトランザクションタイプを設定する。<br>
     *
     * @param mpiTransactionType MPIトランザクションタイプ<br>
     */
    public function setMpiTransactionType($mpiTransactionType) {
        $this->mpiTransactionType = $mpiTransactionType;
    }

    /**
     * リダイレクションURIを取得する<br>
     *
     * @return リダイレクションURI<br>
     */
    public function getReqRedirectionUri() {
        return $this->reqRedirectionUri;
    }

    /**
     * リダイレクションURIを設定する<br>
     *
     * @param reqRedirectionUri リダイレクションURI<br>
     */
    public function setReqRedirectionUri($reqRedirectionUri) {
        $this->reqRedirectionUri = $reqRedirectionUri;
    }

    /**
     * カード会社IDを取得する<br>
     *
     * @return カード会社ID<br>
     */
    public function getCorporationId() {
        return $this->corporationId;
    }

    /**
     * カード会社IDを設定する<br>
     *
     * @param corporationId カード会社ID<br>
     */
    public function setCorporationId($corporationId) {
        $this->corporationId = $corporationId;
    }

    /**
     * ブランドIDを取得する<br>
     *
     * @return ブランドID<br>
     */
    public function getBrandId() {
        return $this->brandId;
    }

    /**
     * ブランドIDを設定する<br>
     * @param brandId ブランドID<br>
     */
    public function setBrandId($brandId) {
        $this->brandId = $brandId;
    }

    /**
     * 仕向先バイナリを取得する<br>
     *
     * @return 仕向先バイナリ<br>
     */
    public function getAcquirerBinary() {
        return $this->acquirerBinary;
    }

    /**
     * 仕向先バイナリを取得する<br>
     *
     * @param acquirerBinary 仕向先バイナリ<br>
     */
    public function setAcquirerBinary($acquirerBinary) {
        $this->acquirerBinary = $acquirerBinary;
    }

    /**
     * ディレクトリサービスログインIDを取得する<br>
     *
     * @return ディレクトリサービスログインID<br>
     */
    public function getDsLoginId() {
        return $this->dsLoginId;
    }

    /**
     * ディレクトリサービスログインIDを設定する<br>
     *
     * @param dsLoginId ディレクトリサービスログインID<br>
     */
    public function setDsLoginId($dsLoginId) {
        $this->dsLoginId = $dsLoginId;
    }

    /**
     * CRRSステータスを取得する<br>
     *
     * @return CRRSステータス<br>
     */
    public function getCrresStatus() {
        return $this->crresStatus;
    }

    /**
     * CRRSステータスを設定する<br>
     *
     * @param crresStatus  CRRSステータス<br>
     */
    public function setCrresStatus($crresStatus) {
        $this->crresStatus = $crresStatus;
    }

    /**
     * VERESステータスを取得する<br>
     *
     * @return VERESステータス<br>
     */
    public function getVeresStatus() {
        return $this->veresStatus;
    }

    /**
     *  VERESステータスを設定する<br>
     *
     * @param veresStatus  VERESステータス<br>
     */
    public function setVeresStatus($veresStatus) {
        $this->veresStatus = $veresStatus;
    }

    /**
     * PARESサインを取得する<br>
     *
     * @return PARESサイン<br>
     */
    public function getParesSign() {
        return $this->paresSign;
    }

    /**
     *  PARESサインを設定する<br>
     *
     * @param paresSign  PARESサイン<br>
     */
    public function setParesSign($paresSign) {
         $this->paresSign = $paresSign;
    }

    /**
     * PARESステータスを取得する<br>
     *
     * @return PARESステータス<br>
     */
    public function getParesStatus() {
        return $this->paresStatus;
    }

    /**
     *  PARESステータスを設定する<br>
     * @param paresStatus PARESステータス<br>
     */
    public function setParesStatus($paresStatus) {
        $this->paresStatus = $paresStatus;
    }

    /**
     *  PARES ECIを取得する<br>
     * @return  PARES ECI<br>
     */
    public function getParesEci() {
        return $this->paresEci;
    }

    /**
     * PARES ECIを設定する<br>
     * @param paresEci PARES ECI<br>
     */
    public function setParesEci($paresEci) {
        $this->paresEci = $paresEci;
    }

    /**
     * 本人認証レスポンスコードを取得する<br>
     *
     * @return 本人認証レスポンスコード<br>
     */
    public function getAuthResponseCode() {
        return $this->authResponseCode;
    }

    /**
     * 本人認証レスポンスコードを設定する<br>
     *
     * @param authResponseCode 本人認証レスポンスコード<br>
     */
    public function setAuthResponseCode($authResponseCode) {
        $this->authResponseCode = $authResponseCode;
    }

    /**
     * 検証レスポンスコードを取得する<br>
     *
     * @return 検証レスポンスコード<br>
     */
    public function getVerifyResponseCode() {
        return $this->verifyResponseCode;
    }

    /**
     * 検証レスポンスコードを設定する<br>
     *
     * @param verifyResponseCode 検証レスポンスコード<br>
     */
    public function setVerifyResponseCode($verifyResponseCode) {
        $this->verifyResponseCode = $verifyResponseCode;
    }

    /**
     * 応答3Dメッセージバージョンを取得する<br>
     *
     * @return 応答3Dメッセージバージョン<br>
     */
    public function getRes3dMessageVersion() {
        return $this->res3dMessageVersion;
    }

    /**
     * 応答3Dメッセージバージョンを設定する<br>
     *
     * @param res3dMessageVersion 応答3Dメッセージバージョン<br>
     */
    public function setRes3dMessageVersion($res3dMessageVersion) {
        $this->res3dMessageVersion = $res3dMessageVersion;
    }

    /**
     * 応答3DトランザクションIDを取得する<br>
     *
     * @return 応答3DトランザクションID<br>
     */
    public function getRes3dTransactionId() {
        return $this->res3dTransactionId;
    }

    /**
     * 応答3DトランザクションIDを設定する<br>
     *
     * @param res3dTransactionId 応答3DトランザクションID<br>
     */
    public function setRes3dTransactionId($res3dTransactionId) {
        $this->res3dTransactionId = $res3dTransactionId;
    }

    /**
     * 応答3Dトランザクションステータスを取得する<br>
     *
     * @return 応答3Dトランザクションステータス<br>
     */
    public function getRes3dTransactionStatus() {
        return $this->res3dTransactionStatus;
    }

    /**
     * 応答3Dトランザクションステータスを設定する<br>
     *
     * @param res3dTransactionStatus 応答3Dトランザクションステータス<br>
     */
    public function setRes3dTransactionStatus($res3dTransactionStatus) {
        $this->res3dTransactionStatus = $res3dTransactionStatus;
    }

    /**
     * 応答3D CAVV アルゴリズムを取得する<br>
     *
     * @return 応答3D CAVV アルゴリズム<br>
     */
    public function getRes3dCavvAlgorithm() {
        return $this->res3dCavvAlgorithm;
    }

    /**
     *  応答3D CAVV アルゴリズムを設定する<br>
     *
     * @param res3dCavvAlgorithm  応答3D CAVV アルゴリズム<br>
     */
    public function setRes3dCavvAlgorithm($res3dCavvAlgorithm) {
        $this->res3dCavvAlgorithm = $res3dCavvAlgorithm;
    }

    /**
     * 応答3D CAVVを取得する<br>
     *
     * @return 応答3D CAVV<br>
     */
    public function getRes3dCavv() {
        return $this->res3dCavv;
    }

    /**
     * 応答3D CAVV を設定する<br>
     *
     * @param res3dCavv 応答3D CAVV<br>
     */
    public function setRes3dCavv($res3dCavv) {
        $this->res3dCavv = $res3dCavv;
    }

    /**
     * 応答3D ECI を取得する<br>
     *
     * @return 応答3D ECI<br>
     */
    public function getRes3dEci() {
        return $this->res3dEci;
    }

    /**
     * 応答3D ECIを設定する<br>
     *
     * @param res3dEci 応答3D ECI<br>
     */
    public function setRes3dEci($res3dEci) {
        $this->res3dEci = $res3dEci;
    }

    /**
     * 本人認証要求日時を取得する<br>
     *
     * @return 本人認証要求日時<br>
     */
    public function getAuthRequestDatetime() {
        return $this->authRequestDatetime;
    }

    /**
     * 本人認証要求日時を設定する<br>
     *
     * @param authRequestDatetime 本人認証要求日時<br>
     */
    public function setAuthRequestDatetime($authRequestDatetime) {
        $this->authRequestDatetime = $authRequestDatetime;
    }

    /**
     * 本人認証応答日時を取得する<br>
     *
     * @return 本人認証応答日時<br>
     */
    public function getAuthResponseDatetime() {
        return $this->authResponseDatetime;
    }

    /**
     * 本人認証応答日時を設定する<br>
     *
     * @param authResponseDatetime 本人認証応答日時<br>
     */
    public function setAuthResponseDatetime($authResponseDatetime) {
        $this->authResponseDatetime = $authResponseDatetime;
    }

    /**
     * 検証要求日時を取得する<br>
     *
     * @return 検証要求日時<br>
     */
    public function getVerifyRequestDatetime() {
        return $this->verifyRequestDatetime;
    }

    /**
     * 検証要求日時を設定する<br>
     * @param verifyRequestDatetime 検証要求日時<br>
     */
    public function setVerifyRequestDatetime($verifyRequestDatetime) {
        $this->verifyRequestDatetime = $verifyRequestDatetime;
    }

    /**
     * 検証応答日時を取得する<br>
     *
     * @return 検証応答日時<br>
     */
    public function getVerifyResponseDatetime() {
        return $this->verifyResponseDatetime;
    }

    /**
     * 検証応答日時を設定する<br>
     *
     * @param verifyResponseDatetime 検証応答日時<br>
     */
    public function setVerifyResponseDatetime($verifyResponseDatetime) {
        $this->verifyResponseDatetime = $verifyResponseDatetime;
    }

    /**
     * 要求通貨単位を取得する<br>
     *
     * @return 要求通貨単位<br>
     */
    public function getReqCurrencyUnit() {
        return $this->reqCurrencyUnit;
    }

    /**
     * 要求通貨単位を設定する<br>
     *
     * @param reqCurrencyUnit 要求通貨単位<br>
     */
    public function setReqCurrencyUnit($reqCurrencyUnit) {
        $this->reqCurrencyUnit = $reqCurrencyUnit;
    }

    /**
     * 永久不滅ウォレット残高を設定する<br>
     *
     * @param aqAqfWalletBalance 永久不滅ウォレット残高<br>
     */
    public function setAqAqfWalletBalance($aqAqfWalletBalance) {
        $this->aqAqfWalletBalance = $aqAqfWalletBalance;
    }

    /**
     * 永久不滅ウォレット残高を取得する<br>
     *
     * @return 永久不滅ウォレット残高<br>
     */
    public function getAqAqfWalletBalance() {
        return $this->aqAqfWalletBalance;
    }

    /**
     * 永久不滅ポイント残高を設定する<br>
     *
     * @param aqAqfPointBalance 永久不滅ポイント残高<br>
     */
    public function setAqAqfPointBalance($aqAqfPointBalance) {
        $this->aqAqfPointBalance = $aqAqfPointBalance;
    }

    /**
     * 永久不滅ポイント残高を取得する<br>
     *
     * @return 永久不滅ポイント残高<br>
     */
    public function getAqAqfPointBalance() {
        return $this->aqAqfPointBalance;
    }

    /**
     * 交換後利用可能バリューを設定する<br>
     *
     * @param aqAvailableValue 交換後利用可能バリュー<br>
     */
    public function setAqAvailableValue($aqAvailableValue) {
        $this->aqAvailableValue = $aqAvailableValue;
    }

    /**
     * 交換後利用可能バリューを取得する<br>
     *
     * @return 交換後利用可能バリュー<br>
     */
    public function getAqAvailableValue() {
        return $this->aqAvailableValue;
    }

    /**
     * SaisonトランザクションAPI情報リストを設定する<br>
     *
     * @param transactionApis SaisonトランザクションAPI情報リスト<br>
     */
    public function setTransactionApis($transactionApis) {
        $this->transactionApis = $transactionApis;
    }

    /**
     * SaisonトランザクションAPI情報リストを取得する<br>
     *
     * @return SaisonトランザクションAPI情報リスト<br>
     */
    public function getTransactionApis() {
        return $this->transactionApis;
    }

    /**
     * Saisonトランザクションカード情報リストを設定する<br>
     *
     * @param transactionCards Saisonトランザクションカード情報リスト<br>
     */
    public function setTransactionCards($transactionCards) {
        $this->transactionCards = $transactionCards;
    }

    /**
     * Saisonトランザクションカード情報リストを取得する<br>
     *
     * @return Saisonトランザクションカード情報リスト<br>
     */
    public function getTransactionCards() {
        return $this->transactionCards;
    }

    /**
     * 取引対象タイプを取得する<br>
     * @return upopTxnType 取引対象タイプ<br>
     */
    public function getUpopTxnType() {
        return $this->upopTxnType;
    }

    /**
     * 取引対象タイプを設定する。<br>
     * @param upopTxnType 取引対象タイプ<br>
     */
    public function setUpopTxnType($upopTxnType) {
        $this->upopTxnType = $upopTxnType;
    }

    /**
     * 清算金額を取得する。<br>
     * @return resUpopSettleAmount 清算金額<br>
     */
    public function getResUpopSettleAmount() {
        return $this->resUpopSettleAmount;
    }

    /**
     * 清算金額を設定する。<br>
     * @param resUpopSettleAmount 清算金額<br>
     */
    public function setResUpopSettleAmount($resUpopSettleAmount) {
        $this->resUpopSettleAmount = $resUpopSettleAmount;
    }

    /**
     * 清算日付を取得する。<br>
     * @return resUpopSettleDate 清算日付<br>
     */
    public function getResUpopSettleDate() {
        return $this->resUpopSettleDate;
    }

    /**
     * 清算日付を設定する。<br>
     * @param resUpopSettleDate 清算日付<br>
     */
    public function setResUpopSettleDate($resUpopSettleDate) {
        $this->resUpopSettleDate = $resUpopSettleDate;
    }

    /**
     * 清算通貨を取得する。<br>
     * @return resUpopSettleCurrency 清算通貨<br>
     */
    public  function getResUpopSettleCurrency(){
        return $this->resUpopSettleCurrency;
    }

    /**
     * 清算通貨を設定する。<br>
     * @param resUpopSettleCurrency 清算通貨<br>
     */
    public function setResUpopSettleCurrency($resUpopSettleCurrency){
        $this->resUpopSettleCurrency= $resUpopSettleCurrency;
    }

    /**
     * 外貨換算日付を取得する。<br>
     * @return resUpopExchangeDate 外貨換算日付<br>
     */
    public function getResUpopExchangeDate(){
        return $this->resUpopExchangeDate;
    }

    /**
     * 外貨換算日付を設定する。<br>
     * @param resUpopExchangeDate 外貨換算日付<br>
     */
    public function setResUpopExchangeDate($resUpopExchangeDate){
        $this->resUpopExchangeDate= $resUpopExchangeDate;
    }

    /**
     * 外貨換算レートを取得する。<br>
     * @return resUpopExchangeRate 外貨換算レート<br>
     */
    public function getResUpopExchangeRate(){
        return $this->resUpopExchangeRate;
    }

    /**
     * 外貨換算レートを設定する。<br>
     * @param resUpopExchangeRate 外貨換算レート<br>
     */
    public function setResUpopExchangeRate($resUpopExchangeRate){
        $this->resUpopExchangeRate = $resUpopExchangeRate;
    }

    /**
     * 決済センターとの取引IDを取得する。<br>
     * @return centerTradeId 決済センターとの取引ID<br>
     */
    public function getCenterTradeId(){
        return $this->centerTradeId;
    }

    /**
     * 決済センターとの取引IDを設定する。<br>
     * @param centerTradeId 決済センターとの取引ID<br>
     */
    public function setCenterTradeId($centerTradeId){
        $this->centerTradeId = $centerTradeId;
    }

    /**
     * 対象取引タイプ。<br>
     * 
     * @return alipayTxnType 対象取引タイプ<br>
     */
    public function getAlipayTxnType() {
        return $this->alipayTxnType;
    }

    /**
     * 対象取引タイプ。<br>
     * 
     * @param alipayTxnType 対象取引タイプ<br>
     */
    public function setAlipayTxnType($alipayTxnType) {
        $this->alipayTxnType = $alipayTxnType;
    }

    /**
     * 清算金額を取得する。<br>
     * @return settleAmount 清算金額<br>
     */
    public function getSettleAmount(){
        return $this->settleAmount;
    }

    /**
     * 清算金額を設定する。<br>
     * @param settleAmount 清算金額<br>
     */
    public function setSettleAmount($settleAmount){
        $this->settleAmount = $settleAmount;
    }

    /**
     * 清算通貨を取得する。<br>
     * @return settleCurrency 清算通貨<br>
     */
    public function getSettleCurrency(){
        return $this->settleCurrency;
    }

    /**
     * 清算通貨を設定する。<br>
     * @param settleCurrency 清算通貨<br>
     */
    public function setSettleCurrency($settleCurrency){
        $this->settleCurrency = $settleCurrency;
    }

    /**
     * 支払い日付を取得する。<br>
     * @return paymentTime 支払い日付<br>
     */
    public function getPaymentTime(){
        return $this->paymentTime;
    }

    /**
     * 支払い日付を設定する。<br>
     * @param paymentTime 支払い日付<br>
     */
    public function setPaymentTime($paymentTime){
        $this->paymentTime = $paymentTime;
    }

    /**
     * 清算日付を取得する。<br>
     * @return settlementTime 清算日付<br>
     */
    public function getSettlementTime(){
        return $this->settlementTime;
    }

    /**
     * 清算日付を設定する。<br>
     * @param settlementTime 清算日付<br>
     */
    public function setSettlementTime($settlementTime){
        $this->settlementTime = $settlementTime;
    }

    /**
     * キャリア結果コードを取得する。<br>
     * @return crResultCode キャリア結果コード<br>
     */
    public function getCrResultCode(){
        return $this->crResultCode;
    }

    /**
     * キャリア結果コードを設定する。<br>
     * @param crResultCode キャリア結果コード<br>
     */
    public function setCrResultCode($crResultCode){
        $this->crResultCode= $crResultCode;
    }

    /**
     * 詳細コマンドタイプを取得する。<br>
     * @return detailCommandType 詳細コマンドタイプ<br>
     */
    public function getDetailCommandType(){
        return $this->detailCommandType;
    }

    /**
     * 詳細コマンドタイプを設定する。<br>
     * @param detailCommandType 詳細コマンドタイプ<br>
     */
    public function setDetailCommandType($detailCommandType){
        $this->detailCommandType= $detailCommandType;
    }

    /**
     * キャリアへの要求日時を取得する。<br>
     * @return crRequestDatetime キャリアへの要求日時<br>
     */
    public function getCrRequestDatetime(){
        return $this->crRequestDatetime;
    }

    /**
     * キャリアへの要求日時を設定する。<br>
     * @param crRequestDatetime キャリアへの要求日時<br>
     */
    public function setCrRequestDatetime($crRequestDatetime){
        $this->crRequestDatetime= $crRequestDatetime;
    }

    /**
     * キャリアからの返戻日時を取得する。<br>
     * @return crResponseDatetime キャリアからの返戻日時<br>
     */
    public function getCrResponseDatetime(){
        return $this->crResponseDatetime;
    }

    /**
     * キャリアからの返戻日時を設定する。<br>
     * @param crResponseDatetime キャリアからの返戻日時<br>
     */
    public function setCrResponseDatetime($crResponseDatetime){
        $this->crResponseDatetime= $crResponseDatetime;
    }

    /**
     * 取引対象タイプを取得する<br>
     * @return oricoTxnType 取引対象タイプ<br>
     */
    public function getOricoTxnType() {
        return $this->oricoTxnType;
    }

    /**
     * 取引対象タイプを設定する。<br>
     * @param oricoTxnType 取引対象タイプ<br>
     */
    public function setOricoTxnType($oricoTxnType) {
        $this->oricoTxnType = $oricoTxnType;
    }

    /**
     * 審査結果コードを設定する<br>
     *
     * @param orderStateCode 審査結果コード<br>
     */
    public function setOrderStateCode($orderStateCode) {
        $this->orderStateCode = $orderStateCode;
    }

    /**
     * 審査結果コードを取得する<br>
     *
     * @return 審査結果コード<br>
     */
    public function getOrderStateCode() {
        return $this->orderStateCode;
    }

    /**
     * 承認番号を設定する<br>
     *
     * @param approvalNo 承認番号<br>
     */
    public function setApprovalNo($approvalNo) {
        $this->approvalNo = $approvalNo;
    }

    /**
     * 承認番号を取得する<br>
     *
     * @return 承認番号<br>
     */
    public function getApprovalNo() {
        return $this->approvalNo;
    }

    /**
     * 申込日を設定する<br>
     *
     * @param requestDate 申込日<br>
     */
    public function setRequestDate($requestDate) {
        $this->requestDate = $requestDate;
    }

    /**
     * 申込日を取得する<br>
     *
     * @return 申込日<br>
     */
    public function getRequestDate() {
        return $this->requestDate;
    }

    /**
     * ローン元金を設定する<br>
     *
     * @param loanPrincipal ローン元金<br>
     */
    public function setLoanPrincipal($loanPrincipal) {
        $this->loanPrincipal = $loanPrincipal;
    }

    /**
     * ローン元金を取得する<br>
     *
     * @return ローン元金<br>
     */
    public function getLoanPrincipal() {
        return $this->loanPrincipal;
    }

    /**
     * 支払回数を設定する<br>
     *
     * @param paymentCount 支払回数<br>
     */
    public function setPaymentCount($paymentCount) {
        $this->paymentCount = $paymentCount;
    }

    /**
     * 支払回数を取得する<br>
     *
     * @return 支払回数<br>
     */
    public function getPaymentCount() {
        return $this->paymentCount;
    }
}
?>