<?php
/**
 * 検索結果:決済オーダー情報のクラス
 *
 * @author Veritrans, Inc.
 */
class ProperOrderInfo {

    /**
     * 決済方式<br>
     */
    private $settlementMethod;
    /**
     * 決済タイプ<br>
     */
    private $settlementType;

    /**
     * 決済金額<br>
     */
    private $amount;

    /**
     * 決済期限<br>
     */
    private $settlementLimit;

    /**
     * 転送メール送信要否<br>
     */
    private $forwardMailFlag;

    /**
     * マーチャントメールアドレス<br>
     */
    private $merchantMailAddr;

    /**
     * 取消返金通知メールアドレス<br>
     */
    private $cancelMailAddr;
    /**
     * 依頼メール付加情報<br>
     */
    private $requestMailAddInfo;

    /**
     * 完了メール付加情報<br>
     */
    private $completeMailAddInfo;
    /**
     * ショップ名<br>
     */
    private $shopName;
    /**
     * 完了メール送信要否<br>
     */
    private $completeMailFlag;
    /**
     * 内容確認画面付加情報<br>
     */
    private $confirmScreenAddInfo;

    /**
     * 完了画面付加情報<br>
     */
    private $completeScreenAddInfo;
    /**
     * 画面タイトル<br>
     */
    private $screenTitle;

    /**
     * 決済完了戻り先種別<br>
     */
    private $completeReturnKind;
    /**
     * 決済完了戻り先URL<br>
     */
    private $completeReturnUrl;

    /**
     * 決済完了通知URL<br>
     */
    private $completeNoticeUrl;

    /**
     * 販売区分<br>
     */
    private $salesType;

    /**
     * 備考<br>
     */
    private $free;

    /**
     * 返金オーダーID<br>
     */
    private $refundOrderCtlId;

    /**
     * 決済アプリ起動URL<br>
     */
    private $appUrl;

    /**
     * オーダー種別<br>
     */
    private $orderKind;

    /**
     * 完了日時<br>
     */
    private $completeDatetime;
    /**
     * 決済サービスオプション<br>
     */
    private $cvsType;

    /**
     * 氏名１<br>
     */
    private $name1;
    /**
     * 氏名２<br>
     */
    private $name2;

    /**
     * カナ<br>
     */
    private $kana;

    /**
     * 電話番号<br>
     */
    private $telNo;

    /**
     * メールアドレス<br>
     */
    private $mailAddr;

    /**
     * 備考１<br>
     */
    private $free1;

    /**
     * 備考２<br>
     */
    private $free2;

    /**
     * 支払期限<br>
     */
    private $payLimit;
    /**
     * 受付番号<br>
     */
    private $receiptNo;
    /**
     * 入金受付日<br>
     */
    private $paidDatetime;

    /**
     * 収納日時<br>
     */
    private $receivedDatetime;

    /**
     * 電文ID<br>
     */
    private $startTxn;

    /**
     * 3Dメッセージバージョン<br>
     */
    private $dddMessageVersion;

    /**
     * 要求通貨単位<br>
     */
    private $requestCurrencyUnit;

    /**
     * カード有効期限<br>
     */
    private $cardExpire;

    /**
     * 広告URL<br>
     */
    private $tradUrl;

    /**
     * 請求番号<br>
     */
    private $invoiceId;

    /**
     * 顧客番号<br>
     */
    private $payerId;

    /**
     * 支払日時<br>
     */
    private $paymentDatetime;

    /**
     * マーチャントリダイレクトURI<br>
     */
    private $merchantRedirectUri;

    /**
     * 決済金額<br>
     */
    private $totalAmount;

    /**
     * ウォレット決済金額<br>
     */
    private $walletAmount;

    /**
     * カード決済金額<br>
     */
    private $cardAmount;

    /**
     * カード取引ID<br>
     */
    private $cardOrderId;

    /**
     * キャリアサービスタイプ<br>
     */
    private $crServiceType;

    /**
     * 与信同時売上フラグ<br>
     */
    private $withCapture;

    /**
     * 課金タイプ<br>
     */
    private $accountingType;

    /**
     * 商品情報<br>
     */
    private $itemInfo;

    /**
     * 商品ID<br>
     */
    private $itemId;

    /**
     * 商品タイプ<br>
     */
    private $itemType;

    /**
     * 端末種別<br>
     */
    private $terminalKind;

    /**
     * 決済申込日時<br>
     */
    private $authorizeDatetime;

    /**
     * 売上日時<br>
     */
    private $captureDatetime;

    /**
     * 取消日時<br>
     */
    private $cancelDatetime;

    /**
     * 初回課金日付<br>
     */
    private $mpFirstDate;

    /**
     * 継続課金日<br>
     */
    private $mpDay;

    /**
     * 継続状態フラグ（月額課金状態フラグ）<br>
     */
    private $mpStatus;

    /**
     * 継続課金オーダーID<br>
     */
    private $mpOrderId;

    /**
     * 継続課金状態タイプ<br>
     */
    private $mpTxnStatusType;

    /**
     * 継続課金売上日時<br>
     */
    private $mpCaptureDatetime;

    /**
     * 継続課金取消日時<br>
     */
    private $mpCancelDatetime;

    /**
     * 継続課金終了日時<br>
     */
    private $mpTerminateDatetime;

    /**
     * キャリアオーダーID<br>
     */
    private $crOrderId;

    /**
     * 本人認証（３Ｄセキュア）<br>
     */
    private $d3Flag;

    /**
     * フレッツエリア<br>
     */
    private $fletsArea;

    /**
     * マーチャントリダイレクションURL<br>
     */
    private $merchantRedirectionUrl;

    /**
     * 注文番号<br>
     */
    private $oricoOrderNo;

    /**
     * 会員番号(加盟店)<br>
     */
    private $userNo;

    /**
     * 商品名１<br>
     */
    private $itemName1;

    /**
     * 数量１<br>
     */
    private $itemCount1;

    /**
     * 商品価格１(税込)<br>
     */
    private $itemAmount1;

    /**
     * 商品名２<br>
     */
    private $itemName2;

    /**
     * 数量２<br>
     */
    private $itemCount2;

    /**
     * 商品価格２(税込)<br>
     */
    private $itemAmount2;

    /**
     * 商品名３<br>
     */
    private $itemName3;

    /**
     * 数量３<br>
     */
    private $itemCount3;

    /**
     * 商品価格３(税込)<br>
     */
    private $itemAmount3;

    /**
     * 商品名４<br>
     */
    private $itemName4;

    /**
     * 数量４<br>
     */
    private $itemCount4;

    /**
     * 商品価格４(税込)<br>
     */
    private $itemAmount4;

    /**
     * 商品名５<br>
     */
    private $itemName5;

    /**
     * 数量５<br>
     */
    private $itemCount5;

    /**
     * 商品価格５(税込)<br>
     */
    private $itemAmount5;

    /**
     * 商品価格合計(税込)<br>
     */
    private $totalItemAmount;

    /**
     * 送料合計(税込)<br>
     */
    private $totalCarriage;

    /**
     * 頭金<br>
     */
    private $deposit;

    /**
     * 配送先郵便番号<br>
     */
    private $shippingZipCode;

    /**
     * 取扱契約番号<br>
     */
    private $handlingContractNo;

    /**
     * 契約書有無区分<br>
     */
    private $contractDocumentKbn;

    /**
     * WEB申込商品ID
     */
    private $webDescriptionId;

    /**
     * 決済固有トランザクション情報<br>
     */
    private $properTransactionInfo;

    /**
     * コンストラクタ。
     */
    public function __construct() {
        $this->properTransactionInfo = new MdkBaseDto();
    }

    /**
     * 決済方式を取得する<br>
     *
     * @return 決済方式<br>
     */
    public function getSettlementMethod() {
        return $this->settlementMethod;
    }

    /**
     * 決済方式を設定する<br>
     *
     * @param settlementMethod 決済方式<br>
     */
    public function setSettlementMethod($settlementMethod) {
        $this->settlementMethod = $settlementMethod;
    }

    /**
     * 決済タイプを取得する<br>
     *
     * @return 決済タイプ<br>
     */
    public function getSettlementType() {
        return $this->settlementType;
    }

    /**
     * 決済タイプを設定する<br>
     *
     * @param settlementType 決済タイプ<br>
     */
    public function setSettlementType($settlementType) {
        $this->settlementType = $settlementType;
    }

    /**
     * 決済金額を取得する<br>
     *
     * @return 決済金額<br>
     */
    public function getAmount() {
        return $this->amount;
    }

    /**
     * 決済金額を設定する<br>
     *
     * @param amount 決済金額<br>
     */
    public function setAmount($amount) {
        $this->amount = $amount;
    }

    /**
     * 決済期限を取得する<br>
     *
     * @return 決済期限<br>
     */
    public function getSettlementLimit() {
        return $this->settlementLimit;
    }

    /**
     * 決済期限を設定する<br>
     *
     * @param settlementLimit 決済期限<br>
     */
    public function setSettlementLimit($settlementLimit) {
        $this->settlementLimit = $settlementLimit;
    }

    /**
     * 転送メール送信要否を取得する<br>
     *
     * @return 転送メール送信要否<br>
     */
    public function getForwardMailFlag() {
        return $this->forwardMailFlag;
    }

    /**
     * 転送メール送信要否を設定する<br>
     *
     * @param forwardMailFlag 転送メール送信要否<br>
     */
    public function setForwardMailFlag($forwardMailFlag) {
        $this->forwardMailFlag = $forwardMailFlag;
    }

    /**
     * マーチャントメールアドレスを取得する<br>
     *
     * @return マーチャントメールアドレス<br>
     */
    public function getMerchantMailAddr() {
        return $this->merchantMailAddr;
    }

    /**
     * マーチャントメールアドレスを設定する<br>
     *
     * @param merchantMailAddr マーチャントメールアドレス<br>
     */
    public function setMerchantMailAddr($merchantMailAddr) {
        $this->merchantMailAddr = $merchantMailAddr;
    }

    /**
     * 取消返金通知メールアドレスを取得する<br>
     *
     * @return 取消返金通知メールアドレス<br>
     */
    public function getCancelMailAddr() {
        return $this->cancelMailAddr;
    }

    /**
     * 取消返金通知メールアドレスを設定する<br>
     *
     * @param cancelMailAddr 取消返金通知メールアドレス<br>
     */
    public function setCancelMailAddr($cancelMailAddr) {
        $this->cancelMailAddr = $cancelMailAddr;
    }

    /**
     * 依頼メール付加情報を取得する<br>
     *
     * @return 依頼メール付加情報<br>
     */
    public function getRequestMailAddInfo() {
        return $this->requestMailAddInfo;
    }

    /**
     * 依頼メール付加情報を設定する<br>
     *
     * @param requestMailAddInfo 依頼メール付加情報<br>
     */
    public function setRequestMailAddInfo($requestMailAddInfo) {
        $this->requestMailAddInfo = $requestMailAddInfo;
    }

    /**
     * 完了メール付加情報を取得する<br>
     *
     * @return 完了メール付加情報<br>
     */
    public function getCompleteMailAddInfo() {
        return $this->completeMailAddInfo;
    }

    /**
     * 完了メール付加情報を設定する<br>
     *
     * @param completeMailAddInfo 完了メール付加情報<br>
     */
    public function setCompleteMailAddInfo($completeMailAddInfo) {
        $this->completeMailAddInfo = $completeMailAddInfo;
    }

    /**
     * ショップ名を取得する<br>
     *
     * @return ショップ名<br>
     */
    public function getShopName() {
        return $this->shopName;
    }

    /**
     * ショップ名を設定する<br>
     *
     * @param shopName ショップ名<br>
     */
    public function setShopName($shopName) {
        $this->shopName = $shopName;
    }

    /**
     * 完了メール送信要否を取得する<br>
     *
     * @return 完了メール送信要否<br>
     */
    public function getCompleteMailFlag() {
        return $this->completeMailFlag;
    }

    /**
     * 完了メール送信要否を設定する<br>
     *
     * @param completeMailFlag 完了メール送信要否<br>
     */
    public function setCompleteMailFlag($completeMailFlag) {
        $this->completeMailFlag = $completeMailFlag;
    }

    /**
     * 内容確認画面付加情報を取得する<br>
     *
     * @return 内容確認画面付加情報<br>
     */
    public function getConfirmScreenAddInfo() {
        return $this->confirmScreenAddInfo;
    }

    /**
     * 内容確認画面付加情報を設定する<br>
     *
     * @param confirmScreenAddInfo 内容確認画面付加情報<br>
     */
    public function setConfirmScreenAddInfo($confirmScreenAddInfo) {
        $this->confirmScreenAddInfo = $confirmScreenAddInfo;
    }

    /**
     * 完了画面付加情報を取得する<br>
     *
     * @return 完了画面付加情報<br>
     */
    public function getCompleteScreenAddInfo() {
        return $this->completeScreenAddInfo;
    }

    /**
     * 完了画面付加情報を設定する<br>
     *
     * @param completeScreenAddInfo 完了画面付加情報<br>
     */
    public function setCompleteScreenAddInfo($completeScreenAddInfo) {
        $this->completeScreenAddInfo = $completeScreenAddInfo;
    }

    /**
     * 画面タイトルを取得する<br>
     *
     * @return 画面タイトル<br>
     */
    public function getScreenTitle() {
        return $this->screenTitle;
    }

    /**
     * 画面タイトルを設定する<br>
     *
     * @param screenTitle 画面タイトル<br>
     */
    public function setScreenTitle($screenTitle) {
        $this->screenTitle = $screenTitle;
    }

    /**
     * 決済完了戻り先種別を取得する<br>
     *
     * @return 決済完了戻り先種別<br>
     */
    public function getCompleteReturnKind() {
        return $this->completeReturnKind;
    }

    /**
     * 決済完了戻り先種別を設定する<br>
     *
     * @param completeReturnKind 決済完了戻り先種別<br>
     */
    public function setCompleteReturnKind($completeReturnKind) {
        $this->completeReturnKind = $completeReturnKind;
    }

    /**
     * 決済完了戻り先URLを取得する<br>
     *
     * @return 決済完了戻り先URL<br>
     */
    public function getCompleteReturnUrl() {
        return $this->completeReturnUrl;
    }

    /**
     * 決済完了戻り先URLを設定する<br>
     *
     * @param completeReturnUrl 決済完了戻り先URL<br>
     */
    public function setCompleteReturnUrl($completeReturnUrl) {
        $this->completeReturnUrl = $completeReturnUrl;
    }

    /**
     * 決済完了通知URLを取得する<br>
     *
     * @return 決済完了通知URL<br>
     */
    public function getCompleteNoticeUrl() {
        return $this->completeNoticeUrl;
    }

    /**
     * 決済完了通知URLを設定する<br>
     *
     * @param completeNoticeUrl 決済完了通知URL<br>
     */
    public function setCompleteNoticeUrl($completeNoticeUrl) {
        $this->completeNoticeUrl = $completeNoticeUrl;
    }

    /**
     * 販売区分を取得する<br>
     *
     * @return 販売区分<br>
     */
    public function getSalesType() {
        return $this->salesType;
    }

    /**
     * 販売区分を設定する<br>
     *
     * @param salesType 販売区分<br>
     */
    public function setSalesType($salesType) {
        $this->salesType = $salesType;
    }

    /**
     * 備考を取得する<br>
     *
     * @return 備考<br>
     */
    public function getFree() {
        return $this->free;
    }

    /**
     * 備考を設定する<br>
     *
     * @param free 備考<br>
     */
    public function setFree($free) {
        $this->free = $free;
    }

    /**
     * 返金オーダーIDを取得する<br>
     *
     * @return 返金オーダーID<br>
     */
    public function getRefundOrderCtlId() {
        return $this->refundOrderCtlId;
    }

    /**
     * 返金オーダーIDを設定する<br>
     *
     * @param refundOrderCtlId 返金オーダーID<br>
     */
    public function setRefundOrderCtlId($refundOrderCtlId) {
        $this->refundOrderCtlId = $refundOrderCtlId;
    }

    /**
     * 決済アプリ起動URLを取得する<br>
     *
     * @return 決済アプリ起動URL<br>
     */
    public function getAppUrl() {
        return $this->appUrl;
    }

    /**
     * 決済アプリ起動URLを設定する<br>
     *
     * @param appUrl 決済アプリ起動URL<br>
     */
    public function setAppUrl($appUrl) {
        $this->appUrl = $appUrl;
    }

    /**
     * オーダー種別を取得する<br>
     *
     * @return オーダー種別<br>
     */
    public function getOrderKind() {
        return $this->orderKind;
    }

    /**
     * オーダー種別を設定する<br>
     *
     * @param orderKind オーダー種別<br>
     */
    public function setOrderKind($orderKind) {
        $this->orderKind = $orderKind;
    }

    /**
     * 完了日時を取得する<br>
     *
     * @return 完了日時<br>
     */
    public function getCompleteDatetime() {
        return $this->completeDatetime;
    }

    /**
     * 完了日時を設定する<br>
     *
     * @param completeDatetime 完了日時<br>
     */
    public function setCompleteDatetime($completeDatetime) {
        $this->completeDatetime = $completeDatetime;
    }

    /**
     * 決済サービスオプションを取得する<br>
     *
     * @return 決済サービスオプション<br>
     */
    public function getCvsType() {
        return $this->cvsType;
    }

    /**
     * 決済サービスオプションを設定する<br>
     *
     * @paracvsType m 決済サービスオプション<br>
     */
    public function setCvsType($cvsType) {
        $this->cvsType = $cvsType;
    }

    /**
     * 氏名１を取得する<br>
     *
     * @return 氏名１<br>
     */
    public function getName1() {
        return $this->name1;
    }

    /**
     * 氏名１を設定する<br>
     *
     * @param name1 氏名１<br>
     */
    public function setName1($name1) {
        $this->name1 = $name1;
    }

    /**
     * 氏名２を取得する<br>
     *
     * @return 氏名２<br>
     */
    public function getName2() {
        return $this->name2;
    }

    /**
     * 氏名２を設定する<br>
     *
     * @param name2 氏名２<br>
     */
    public function setName2($name2) {
        $this->name2 = $name2;
    }

    /**
     * カナを取得する<br>
     *
     * @return カナ<br>
     */
    public function getKana() {
        return $this->kana;
    }

    /**
     * カナを設定する<br>
     *
     * @param kana カナ<br>
     */
    public function setKana($kana) {
        $this->kana = $kana;
    }

    /**
     * 電話番号を取得する<br>
     *
     * @return 電話番号<br>
     */
    public function getTelNo() {
        return $this->telNo;
    }

    /**
     * 電話番号を設定する<br>
     *
     * @param telNo 電話番号<br>
     */
    public function setTelNo($telNo) {
        $this->telNo = $telNo;
    }

    /**
     * メールアドレスを取得する<br>
     *
     * @return メールアドレス<br>
     */
    public function getMailAddr() {
        return $this->mailAddr;
    }

    /**
     * メールアドレスを設定する<br>
     *
     * @param mailAddr メールアドレス<br>
     */
    public function setMailAddr($mailAddr) {
        $this->mailAddr = $mailAddr;
    }

    /**
     * 備考１を取得する<br>
     *
     * @return 備考１<br>
     */
    public function getFree1() {
        return $this->free1;
    }

    /**
     * 備考１を設定する<br>
     *
     * @param free1 備考１<br>
     */
    public function setFree1($free1) {
        $this->free1 = $free1;
    }

    /**
     * 備考２を取得する<br>
     *
     * @return 備考２<br>
     */
    public function getFree2() {
        return $this->free2;
    }

    /**
     * 備考２を設定する<br>
     *
     * @param free2 備考２<br>
     */
    public function setFree2($free2) {
        $this->free2 = $free2;
    }

    /**
     * 支払期限を取得する<br>
     *
     * @return 支払期限<br>
     */
    public function getPayLimit() {
        return $this->payLimit;
    }

    /**
     * 支払期限を設定する<br>
     *
     * @param payLimit 支払期限<br>
     */
    public function setPayLimit($payLimit) {
        $this->payLimit = $payLimit;
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
     * 入金受付日を取得する<br>
     *
     * @return 入金受付日<br>
     */
    public function getPaidDatetime() {
        return $this->paidDatetime;
    }

    /**
     * 入金受付日を設定する<br>
     *
     * @param paidDatetime 入金受付日<br>
     */
    public function setPaidDatetime($paidDatetime) {
        $this->paidDatetime = $paidDatetime;
    }

    /**
     * 収納日時を取得する<br>
     *
     * @return 収納日時<br>
     */
    public function getReceivedDatetime() {
        return $this->receivedDatetime;
    }

    /**
     * 収納日時を設定する<br>
     *
     * @param receivedDatetime 収納日時<br>
     */
    public function setReceivedDatetime($receivedDatetime) {
        $this->receivedDatetime = $receivedDatetime;
    }

    /**
     * 電文IDを取得する<br>
     *
     * @return 電文ID<br>
     */
    public function getStartTxn() {
        return $this->startTxn;
    }

    /**
     * 電文IDを設定する<br>
     *
     * @param startTxn 電文ID<br>
     */
    public function setStartTxn($startTxn) {
        $this->startTxn = $startTxn;
    }

    /**
     * 3Dメッセージバージョンを取得する<br>
     *
     * @return 3Dメッセージバージョン<br>
     */
    public function getDddMessageVersion() {
        return $this->dddMessageVersion;
    }

    /**
     * 3Dメッセージバージョンを設定する<br>
     *
     * @param dddMessageVersion 3Dメッセージバージョン<br>
     */
    public function setDddMessageVersion($dddMessageVersion) {
        $this->dddMessageVersion = $dddMessageVersion;
    }

    /**
     * 要求通貨単位を取得する<br>
     *
     * @return 要求通貨単位<br>
     */
    public function getRequestCurrencyUnit() {
        return $this->requestCurrencyUnit;
    }

    /**
     * 要求通貨単位を設定する<br>
     *
     * @param requestCurrencyUnit 要求通貨単位<br>
     */
    public function setRequestCurrencyUnit($requestCurrencyUnit) {
        $this->requestCurrencyUnit = $requestCurrencyUnit;
    }

    /**
     * カード有効期限を取得する<br>
     *
     * @return カード有効期限<br>
     */
    public function getCardExpire() {
        return $this->cardExpire;
    }

    /**
     * カード有効期限を設定する<br>
     *
     * @param cardExpire カード有効期限<br>
     */
    public function setCardExpire($cardExpire) {
        $this->cardExpire = $cardExpire;
    }


    /**
     * 広告URLを取得する<br>
     *
     * @return 広告URL<br>
     */
    public function getTradUrl() {
        return $this->tradUrl;
    }

    /**
     * 広告URLを設定する<br>
     *
     * @param tradUrl 広告URL<br>
     */
    public function setTradUrl($tradUrl) {
        $this->tradUrl = $tradUrl;
    }

    /**
     * 請求番号を取得する<br>
     *
     * @return 請求番号<br>
     */
    public function getInvoiceId() {
        return $this->invoiceId;
    }

    /**
     * 請求番号を設定する<br>
     *
     * @param invoiceId 請求番号<br>
     */
    public function setInvoiceId($invoiceId) {
        $this->invoiceId = $invoiceId;
    }

    /**
     * 顧客番号を取得する<br>
     *
     * @return 顧客番号<br>
     */
    public function getPayerId() {
        return $this->payerId;
    }

    /**
     * 顧客番号を設定する<br>
     *
     * @param payerId 顧客番号<br>
     */
    public function setPayerId($payerId) {
        $this->payerId = $payerId;
    }

    /**
     * 支払日時を取得する<br>
     *
     * @return 支払日時<br>
     */
    public function getPaymentDatetime() {
        return $this->paymentDatetime;
    }

    /**
     * 支払日時を設定する<br>
     *
     * @param paymentDatetime 支払日時<br>
     */
    public function setPaymentDatetime($paymentDatetime) {
        $this->paymentDatetime = $paymentDatetime;
    }

    /**
     * マーチャントリダイレクトURIを設定する<br>
     *
     * @param merchantRedirectUri マーチャントリダイレクトURI<br>
     */
    public function setMerchantRedirectUri($merchantRedirectUri) {
        $this->merchantRedirectUri = $merchantRedirectUri;
    }

    /**
     * マーチャントリダイレクトURIを取得する<br>
     *
     * @return マーチャントリダイレクトURI<br>
     */
    public function getMerchantRedirectUri() {
        return $this->merchantRedirectUri;
    }

    /**
     * 合計決済金額を設定する<br>
     *
     * @param totalAmount 合計決済金額<br>
     */
    public function setTotalAmount($totalAmount) {
        $this->totalAmount = $totalAmount;
    }

    /**
     * 合計決済金額を取得する<br>
     *
     * @return 合計決済金額<br>
     */
    public function getTotalAmount() {
        return $this->totalAmount;
    }

    /**
     * ウォレット決済金額を設定する<br>
     *
     * @param walletAmount ウォレット決済金額<br>
     */
    public function setWalletAmount($walletAmount) {
        $this->walletAmount = $walletAmount;
    }

    /**
     * ウォレット決済金額を取得する<br>
     *
     * @return ウォレット決済金額<br>
     */
    public function getWalletAmount() {
        return $this->walletAmount;
    }

    /**
     * カード決済金額を設定する<br>
     *
     * @param cardAmount カード決済金額<br>
     */
    public function setCardAmount($cardAmount) {
        $this->cardAmount = $cardAmount;
    }

    /**
     * カード決済金額を取得する<br>
     *
     * @return カード決済金額<br>
     */
    public function getCardAmount() {
        return $this->cardAmount;
    }

    /**
     * カード取引IDを設定する<br>
     *
     * @param cardOrderId カード取引ID<br>
     */
    public function setCardOrderId($cardOrderId) {
        $this->cardOrderId = $cardOrderId;
    }

    /**
     * カード取引IDを取得する<br>
     *
     * @return カード取引ID<br>
     */
    public function getCardOrderId() {
        return $this->cardOrderId;
    }

    /**
     * キャリアサービスタイプを設定する<br>
     *
     * @param crServiceType キャリアサービスタイプ<br>
     */
    public function setCrServiceType($crServiceType) {
        $this->crServiceType = $crServiceType;
    }

    /**
     * キャリアサービスタイプを取得する<br>
     *
     * @return キャリアサービスタイプ<br>
     */
    public function getCrServiceType() {
        return $this->crServiceType;
    }

    /**
     * 与信同時売上フラグを設定する<br>
     *
     * @param withCapture 与信同時売上フラグ<br>
     */
    public function setWithCapture($withCapture) {
        $this->withCapture = $withCapture;
    }

    /**
     * 与信同時売上フラグを取得する<br>
     *
     * @return 与信同時売上フラグ<br>
     */
    public function getWithCapture() {
        return $this->withCapture;
    }

    /**
     * 課金フラグを設定する<br>
     *
     * @param accountingType 課金フラグ<br>
     */
    public function setAccountingType($accountingType) {
        $this->accountingType = $accountingType;
    }

    /**
     * 課金フラグを取得する<br>
     *
     * @return 課金フラグ<br>
     */
    public function getAccountingType() {
        return $this->accountingType;
    }

    /**
     * 商品情報を設定する<br>
     *
     * @param itemInfo 商品情報<br>
     */
    public function setItemInfo($itemInfo) {
        $this->itemInfo = $itemInfo;
    }

    /**
     * 商品情報を取得する<br>
     *
     * @return 商品情報<br>
     */
    public function getItemInfo() {
        return $this->itemInfo;
    }

    /**
     * 商品IDを設定する<br>
     *
     * @param itemId 商品ID<br>
     */
    public function setItemId($itemId) {
        $this->itemId = $itemId;
    }

    /**
     * 商品IDを取得する<br>
     *
     * @return 商品ID<br>
     */
    public function getItemId() {
        return $this->itemId;
    }

    /**
     * 商品タイプを設定する<br>
     *
     * @param itemType 商品タイプ<br>
     */
    public function setItemType($itemType) {
        $this->itemType = $itemType;
    }

    /**
     * 商品タイプを取得する<br>
     *
     * @return 商品タイプ<br>
     */
    public function getItemType() {
        return $this->itemType;
    }

    /**
     * 端末種別を設定する<br>
     *
     * @param terminalKind 端末種別<br>
     */
    public function setTerminalKind($terminalKind) {
        $this->terminalKind = $terminalKind;
    }

    /**
     * 端末種別を取得する<br>
     *
     * @return 端末種別<br>
     */
    public function getTerminalKind() {
        return $this->terminalKind;
    }

    /**
     * 決済申込日時を設定する<br>
     *
     * @param authorizeDatetime 決済申込日時<br>
     */
    public function setAuthorizeDatetime($authorizeDatetime) {
        $this->authorizeDatetime = $authorizeDatetime;
    }

    /**
     * 決済申込日時を取得する<br>
     *
     * @return 決済申込日時<br>
     */
    public function getAuthorizeDatetime() {
        return $this->authorizeDatetime;
    }

    /**
     * 売上日時を設定する<br>
     *
     * @param captureDatetime 売上日時<br>
     */
    public function setCaptureDatetime($captureDatetime) {
        $this->captureDatetime = $captureDatetime;
    }

    /**
     * 売上日時を取得する<br>
     *
     * @return 売上日時<br>
     */
    public function getCaptureDatetime() {
        return $this->captureDatetime;
    }

    /**
     * 取消日時を設定する<br>
     *
     * @param cancelDatetime 取消日時<br>
     */
    public function setCancelDatetime($cancelDatetime) {
        $this->cancelDatetime = $cancelDatetime;
    }

    /**
     * 取消日時を取得する<br>
     *
     * @return 取消日時<br>
     */
    public function getCancelDatetime() {
        return $this->cancelDatetime;
    }

    /**
     * 初回課金日付を設定する<br>
     *
     * @param mpFirstDate 初回課金日付<br>
     */
    public function setMpFirstDate($mpFirstDate) {
        $this->mpFirstDate = $mpFirstDate;
    }

    /**
     * 初回課金日付を取得する<br>
     *
     * @return 初回課金日付<br>
     */
    public function getMpFirstDate() {
        return $this->mpFirstDate;
    }

    /**
     * 継続課金日を設定する<br>
     *
     * @param mpDay 継続課金日<br>
     */
    public function setMpDay($mpDay) {
        $this->mpDay = $mpDay;
    }

    /**
     * 継続課金日を取得する<br>
     *
     * @return 継続課金日<br>
     */
    public function getMpDay() {
        return $this->mpDay;
    }

    /**
     * 継続状態フラグ（月額課金状態フラグ）を設定する<br>
     *
     * @param mpStatus 継続状態フラグ（月額課金状態フラグ）<br>
     */
    public function setMpStatus($mpStatus) {
        $this->mpStatus = $mpStatus;
    }

    /**
     * 継続状態フラグ（月額課金状態フラグ）を取得する<br>
     *
     * @return 継続状態フラグ（月額課金状態フラグ）<br>
     */
    public function getMpStatus() {
        return $this->mpStatus;
    }

    /**
     * 継続課金オーダーIDを設定する<br>
     *
     * @param mpOrderId 継続課金オーダーID<br>
     */
    public function setMpOrderId($mpOrderId) {
        $this->mpOrderId = $mpOrderId;
    }

    /**
     * 継続課金オーダーIDを取得する<br>
     *
     * @return 継続課金オーダーID<br>
     */
    public function getMpOrderId() {
        return $this->mpOrderId;
    }

    /**
     * 継続課金状態タイプを設定する<br>
     *
     * @param mpTxnStatusType 継続課金状態タイプ<br>
     */
    public function setMpTxnStatusType($mpTxnStatusType) {
        $this->mpTxnStatusType = $mpTxnStatusType;
    }

    /**
     * 継続課金状態タイプを取得する<br>
     *
     * @return 継続課金状態タイプ<br>
     */
    public function getMpTxnStatusType() {
        return $this->mpTxnStatusType;
    }

    /**
     * 継続課金売上日時を設定する<br>
     *
     * @param mpCaptureDatetime 継続課金売上日時<br>
     */
    public function setMpCaptureDatetime($mpCaptureDatetime) {
        $this->mpCaptureDatetime = $mpCaptureDatetime;
    }

    /**
     * 継続課金売上日時を取得する<br>
     *
     * @return 継続課金売上日時<br>
     */
    public function getMpCaptureDatetime() {
        return $this->mpCaptureDatetime;
    }

    /**
     * 継続課金取消日時を設定する<br>
     *
     * @param mpCancelDatetime 継続課金取消日時<br>
     */
    public function setMpCancelDatetime($mpCancelDatetime) {
        $this->mpCancelDatetime = $mpCancelDatetime;
    }

    /**
     * 継続課金取消日時を取得する<br>
     *
     * @return 継続課金取消日時<br>
     */
    public function getMpCancelDatetime() {
        return $this->mpCancelDatetime;
    }

    /**
     * 継続課金終了日時を設定する<br>
     *
     * @param mpTerminateDatetime 継続課金終了日時<br>
     */
    public function setMpTerminateDatetime($mpTerminateDatetime) {
        $this->mpTerminateDatetime = $mpTerminateDatetime;
    }

    /**
     * 継続課金終了日時を取得する<br>
     *
     * @return 継続課金終了日時<br>
     */
    public function getMpTerminateDatetime() {
        return $this->mpTerminateDatetime;
    }

    /**
     * キャリアオーダーIDを設定する<br>
     *
     * @param crOrderId キャリアオーダーID<br>
     */
    public function setCrOrderId($crOrderId) {
        $this->crOrderId = $crOrderId;
    }

    /**
     * キャリアオーダーIDを取得する<br>
     *
     * @return キャリアオーダーID<br>
     */
    public function getCrOrderId() {
        return $this->crOrderId;
    }

    /**
     * 本人認証（３Ｄセキュア）を設定する<br>
     *
     * @param d3Flag 本人認証（３Ｄセキュア）<br>
     */
    public function setD3Flag($d3Flag) {
        $this->d3Flag = $d3Flag;
    }

    /**
     * 本人認証（３Ｄセキュア）を取得する<br>
     *
     * @return 本人認証（３Ｄセキュア）<br>
     */
    public function getD3Flag() {
        return $this->d3Flag;
    }

    /**
     * フレッツエリアを設定する<br>
     *
     * @param fletsArea フレッツエリア<br>
     */
    public function setFletsArea($fletsArea) {
        $this->fletsArea = $fletsArea;
    }

    /**
     * フレッツエリアを取得する<br>
     *
     * @return フレッツエリア<br>
     */
    public function getFletsArea() {
        return $this->fletsArea;
    }

    /**
     * マーチャントリダイレクションURLを設定する<br>
     *
     * @param merchantRedirectionUrl マーチャントリダイレクションURL<br>
     */
    public function setMerchantRedirectionUrl($merchantRedirectionUrl) {
        $this->merchantRedirectionUrl = $merchantRedirectionUrl;
    }
    
    /**
     * マーチャントリダイレクションURLを取得する<br>
     *
     * @param merchantRedirectionUrl マーチャントリダイレクションURL<br>
     */
    public function getMerchantRedirectionUrl() {
        return $this->merchantRedirectionUrl;
    }

    /**
     * 注文番号を設定する<br>
     *
     * @param oricoOrderNo 注文番号<br>
     */
    public function setOricoOrderNo($oricoOrderNo) {
        $this->oricoOrderNo = $oricoOrderNo;
    }

    /**
     * 注文番号を取得する<br>
     *
     * @return 注文番号<br>
     */
    public function getOricoOrderNo() {
        return $this->oricoOrderNo;
    }

    /**
     * 会員番号(加盟店)を設定する<br>
     *
     * @param userNo 会員番号(加盟店)<br>
     */
    public function setUserNo($userNo) {
        $this->userNo = $userNo;
    }

    /**
     * 会員番号(加盟店)を取得する<br>
     *
     * @return 会員番号(加盟店)<br>
     */
    public function getUserNo() {
        return $this->userNo;
    }

    /**
     * 商品名１を設定する<br>
     *
     * @param itemName1 商品名１<br>
     */
    public function setItemName1($itemName1) {
        $this->itemName1 = $itemName1;
    }

    /**
     * 商品名１を取得する<br>
     *
     * @return 商品名１<br>
     */
    public function getItemName1() {
        return $this->itemName1;
    }

    /**
     * 数量１を設定する<br>
     *
     * @param itemCount1 数量１<br>
     */
    public function setItemCount1($itemCount1) {
        $this->itemCount1 = $itemCount1;
    }

    /**
     * 数量１を取得する<br>
     *
     * @return 数量１<br>
     */
    public function getItemCount1() {
        return $this->itemCount1;
    }

    /**
     * 商品価格１(税込)を設定する<br>
     *
     * @param itemAmount1 商品価格１(税込)<br>
     */
    public function setItemAmount1($itemAmount1) {
        $this->itemAmount1 = $itemAmount1;
    }

    /**
     * 商品価格１(税込)を取得する<br>
     *
     * @return 商品価格１(税込)<br>
     */
    public function getItemAmount1() {
        return $this->itemAmount1;
    }

    /**
     * 商品名２を設定する<br>
     *
     * @param itemName2 商品名２<br>
     */
    public function setItemName2($itemName2) {
        $this->itemName2 = $itemName2;
    }

    /**
     * 商品名２を取得する<br>
     *
     * @return 商品名２<br>
     */
    public function getItemName2() {
        return $this->itemName2;
    }

    /**
     * 数量２を設定する<br>
     *
     * @param itemCount2 数量２<br>
     */
    public function setItemCount2($itemCount2) {
        $this->itemCount2 = $itemCount2;
    }

    /**
     * 数量２を取得する<br>
     *
     * @return 数量２<br>
     */
    public function getItemCount2() {
        return $this->itemCount2;
    }

    /**
     * 商品価格２(税込)を設定する<br>
     *
     * @param itemAmount2 商品価格２(税込)<br>
     */
    public function setItemAmount2($itemAmount2) {
        $this->itemAmount2 = $itemAmount2;
    }

    /**
     * 商品価格２(税込)を取得する<br>
     *
     * @return 商品価格２(税込)<br>
     */
    public function getItemAmount2() {
        return $this->itemAmount2;
    }

    /**
     * 商品名３を設定する<br>
     *
     * @param itemName3 商品名３<br>
     */
    public function setItemName3($itemName3) {
        $this->itemName3 = $itemName3;
    }

    /**
     * 商品名３を取得する<br>
     *
     * @return 商品名３<br>
     */
    public function getItemName3() {
        return $this->itemName3;
    }

    /**
     * 数量３を設定する<br>
     *
     * @param itemCount3 数量３<br>
     */
    public function setItemCount3($itemCount3) {
        $this->itemCount3 = $itemCount3;
    }

    /**
     * 数量３を取得する<br>
     *
     * @return 数量３<br>
     */
    public function getItemCount3() {
        return $this->itemCount3;
    }

    /**
     * 商品価格３(税込)を設定する<br>
     *
     * @param itemAmount3 商品価格３(税込)<br>
     */
    public function setItemAmount3($itemAmount3) {
        $this->itemAmount3 = $itemAmount3;
    }

    /**
     * 商品価格３(税込)を取得する<br>
     *
     * @return 商品価格３(税込)<br>
     */
    public function getItemAmount3() {
        return $this->itemAmount3;
    }

    /**
     * 商品名４を設定する<br>
     *
     * @param itemName4 商品名４<br>
     */
    public function setItemName4($itemName4) {
        $this->itemName4 = $itemName4;
    }

    /**
     * 商品名４を取得する<br>
     *
     * @return 商品名４<br>
     */
    public function getItemName4() {
        return $this->itemName4;
    }

    /**
     * 数量４を設定する<br>
     *
     * @param itemCount4 数量４<br>
     */
    public function setItemCount4($itemCount4) {
        $this->itemCount4 = $itemCount4;
    }

    /**
     * 数量４を取得する<br>
     *
     * @return 数量４<br>
     */
    public function getItemCount4() {
        return $this->itemCount4;
    }

    /**
     * 商品価格４(税込)を設定する<br>
     *
     * @param itemAmount4 商品価格４(税込)<br>
     */
    public function setItemAmount4($itemAmount4) {
        $this->itemAmount4 = $itemAmount4;
    }

    /**
     * 商品価格４(税込)を取得する<br>
     *
     * @return 商品価格４(税込)<br>
     */
    public function getItemAmount4() {
        return $this->itemAmount4;
    }

    /**
     * 商品名５を設定する<br>
     *
     * @param itemName5 商品名５<br>
     */
    public function setItemName5($itemName5) {
        $this->itemName5 = $itemName5;
    }

    /**
     * 商品名５を取得する<br>
     *
     * @return 商品名５<br>
     */
    public function getItemName5() {
        return $this->itemName5;
    }

    /**
     * 数量５を設定する<br>
     *
     * @param itemCount5 数量５<br>
     */
    public function setItemCount5($itemCount5) {
        $this->itemCount5 = $itemCount5;
    }

    /**
     * 数量５を取得する<br>
     *
     * @return 数量５<br>
     */
    public function getItemCount5() {
        return $this->itemCount5;
    }

    /**
     * 商品価格５(税込)を設定する<br>
     *
     * @param itemAmount5 商品価格５(税込)<br>
     */
    public function setItemAmount5($itemAmount5) {
        $this->itemAmount5 = $itemAmount5;
    }

    /**
     * 商品価格５(税込)を取得する<br>
     *
     * @return 商品価格５(税込)<br>
     */
    public function getItemAmount5() {
        return $this->itemAmount5;
    }

    /**
     * 商品価格合計(税込)を設定する<br>
     *
     * @param totalItemAmount 商品価格合計(税込)<br>
     */
    public function setTotalItemAmount($totalItemAmount) {
        $this->totalItemAmount = $totalItemAmount;
    }

    /**
     * 商品価格合計(税込)を取得する<br>
     *
     * @return 商品価格合計(税込)<br>
     */
    public function getTotalItemAmount() {
        return $this->totalItemAmount;
    }

    /**
     * 送料合計(税込)を設定する<br>
     *
     * @param totalCarriage 送料合計(税込)<br>
     */
    public function setTotalCarriage($totalCarriage) {
        $this->totalCarriage = $totalCarriage;
    }

    /**
     * 送料合計(税込)を取得する<br>
     *
     * @return 送料合計(税込)<br>
     */
    public function getTotalCarriage() {
        return $this->totalCarriage;
    }

    /**
     * 頭金を設定する<br>
     *
     * @param deposit 頭金<br>
     */
    public function setDeposit($deposit) {
        $this->deposit = $deposit;
    }

    /**
     * 頭金を取得する<br>
     *
     * @return 頭金<br>
     */
    public function getDeposit() {
        return $this->deposit;
    }

    /**
     * 配送先郵便番号を設定する<br>
     *
     * @param shippingZipCode 配送先郵便番号<br>
     */
    public function setShippingZipCode($shippingZipCode) {
        $this->shippingZipCode = $shippingZipCode;
    }

    /**
     * 配送先郵便番号を取得する<br>
     *
     * @return 配送先郵便番号<br>
     */
    public function getShippingZipCode() {
        return $this->shippingZipCode;
    }

    /**
     * 取扱契約番号を設定する<br>
     *
     * @param handlingContractNo 取扱契約番号<br>
     */
    public function setHandlingContractNo($handlingContractNo) {
        $this->handlingContractNo = $handlingContractNo;
    }

    /**
     * 取扱契約番号を取得する<br>
     *
     * @return 取扱契約番号<br>
     */
    public function getHandlingContractNo() {
        return $this->handlingContractNo;
    }

    /**
     * 契約書有無区分を設定する<br>
     * @param contractDocumentKbn 契約書有無区分<br>
     */
    public function setContractDocumentKbn($contractDocumentKbn) {
        $this->contractDocumentKbn = $contractDocumentKbn;
    }

    /**
     * 契約書有無区分を取得する<br>
     * @return 契約書有無区分<br>
     */
    public function getContractDocumentKbn() {
        return $this->contractDocumentKbn;
    }

    /**
     * WEB申込商品IDを設定する<br>
     * @param webDescriptionId WEB申込商品ID
     */
    public function setWebDescriptionId($webDescriptionId) {
        $this->webDescriptionId = $webDescriptionId;
    }

    /**
     * WEB申込商品IDを取得する<br>
     * @return WEB申込商品ID
     */
    public function getWebDescriptionId() {
        return $this->webDescriptionId;
    }

    /**
     * 固有トランザクション情報を取得する<br>
     *
     * @return 固有トランザクション情報<br>
     */
    public function getProperTransactionInfo() {
        return $this->properTransactionInfo;
    }

    /**
     * 固有トランザクション情報を設定する<br>
     *
     * @param properTransactionInfo 固有トランザクション情報<br>
     */
    public function setProperTransactionInfo($properTransactionInfo) {
        $this->properTransactionInfo = $properTransactionInfo;
    }

}
?>