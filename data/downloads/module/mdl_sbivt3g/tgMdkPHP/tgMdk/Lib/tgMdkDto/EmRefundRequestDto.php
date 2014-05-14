<?php
/**
 * 決済サービスタイプ：電子マネー、コマンド名：返金の要求Dtoクラス
 *
 * @author Veritrans, Inc.
 *
 */
class EmRefundRequestDto extends AbstractPaymentRequestDto
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
    private $SERVICE_COMMAND = "Refund";

    /** 
     * 決済サービスオプション<br>
     * 半角英数字<br>
     * 決済サービスのオプションを指定します<br>
     * 例） モバイルEdyの場合： "edy-mobile"<br>
     * Edy<br>
     * "edy-mobile"<br>
     * 　：モバイルEdy<br>
     * "edy-pc"<br>
     * 　：パソリ（pc）<br>
     * Suica<br>
     * "suica-mobile-mail"<br>
     * 　：モバイル-メール<br>
     * 　　決済<br>
     * "suica-mobile-app"<br>
     * 　：モバイル-アプリ決済<br>
     * "suica-pc-mail"<br>
     * 　：ネット-メール決済<br>
     * "suica-pc-app"<br>
     * 　：ネット-アプリ決済<br>
     */
    private $serviceOptionType;

    /** 
     * 取引ID<br>
     * 半角英数字<br>
     * 100 文字以内<br>
     * マーチャント側で取引を一意に表す注文管理IDを指定します。<br>
     * 申込処理ごとに一意である必要があります。<br>
     * 半角英数字、“-”（ハイフン）、“_”（アンダースコア）も使用可能です。<br>
     * ※Suicaに限り40桁を上限とする。<br>
     */
    private $orderId;

    /** 
     * 金額<br>
     * 半角数字<br>
     * 5 桁以内<br>
     * 返金金額となります。決済金額以下を指定する必要があります。<br>
     * 例）1800<br>
     * Edy<br>
     * 1 以上<br>
     *  ～ 50,000 以下<br>
     * Suica<br>
     * 1 以上<br>
     *  ～ 20,000 以下<br>
     */
    private $amount;

    /** 
     * オーダー種別<br>
     * 半角英数字<br>
     * 10 文字以内<br>
     * 返金請求オーダーの種別を指定します。<br>
     * Edy<br>
     * refund：返金<br>
     * Suica<br>
     * refund：返金<br>
     * refund_new：新規返金<br>
     */
    private $orderKind;

    /** 
     * 返金対象取引ID<br>
     * 半角英数字<br>
     * 100 文字以内<br>
     * 返金を依頼する決済請求の取引IDを指定します。<br>
     */
    private $refundOrderId;

    /** 
     * 決済期限<br>
     * 半角数字<br>
     * 14桁固定<br>
     * 返金・新規返金の受取期限となります。<br>
     * YYYYMMDDhhmmssの形式<br>
     * 例）20060901235901<br>
     * Edy<br>
     * 未使用（30日固定）<br>
     * Suica<br>
     * 60日以内<br>
     */
    private $settlementLimit;

    /** 
     * メールアドレス<br>
     * 半角英数字<br>
     * 256 文字以内<br>
     * 返金・新規返金依頼メールを送信する消費者の携帯電話メールアドレスとなります。<br>
     * Edy<br>
     * 未使用<br>
     * Suica<br>
     * 必須： メール決済<br>
     * 未使用： アプリ決済<br>
     */
    private $mailAddr;

    /** 
     * 転送メール送信要否<br>
     * 半角数字<br>
     * 1 桁固定<br>
     * 返金・新規返金依頼メールのコピーメール又はBCCメールをマーチャントメールアドレス（merchantMailAddr）に送信するか否かを設定します。<br>
     * 0：送信不要<br>
     * 1：送信要<br>
     * Edy<br>
     * 未使用<br>
     * Suica<br>
     * 任意： メール決済<br>
     * 未使用： アプリ決済<br>
     */
    private $forwardMailFlag;

    /** 
     * マーチャントメールアドレス<br>
     * 半角英数字<br>
     * 256 文字以内<br>
     * 返金・新規返金依頼メールのコピーメール又はBCC メール先マーチャントメールアドレス。<br>
     * 以下の文字も使用できます。<br>
     * “.”(ドット)、“-”(ハイフン)、“_”(アンダースコア)、“@”(アットマーク)<br>
     * Edy<br>
     * 未使用<br>
     * Suica<br>
     * 任意： メール決済<br>
     * 未使用： アプリ決済<br>
     */
    private $merchantMailAddr;

    /** 
     * 取消通知メールアドレス<br>
     * 半角英数字<br>
     * 256 文字以内<br>
     * 返金・新規返金を利用者に通知するためのメールアドレスを指定します。<br>
     */
    private $cancelMailAddr;

    /** 
     * 依頼メール付加情報<br>
     * 文字列<br>
     * 256 バイト以内<br>
     * 返金・新規返金依頼メールに追加される文字列（返金情報等）です。<br>
     * Edy<br>
     * 未使用<br>
     * Suica<br>
     * 任意： メール決済<br>
     * 未使用： アプリ決済<br>
     */
    private $requestMailAddInfo;

    /** 
     * 依頼メール送信要否<br>
     * 半角数字<br>
     * 1 桁固定<br>
     * Suicaポケット発行メールの送信要否を設定します。<br>
     * 0： 送信不要<br>
     * 1： 送信要<br>
     * Edy<br>
     * 未使用<br>
     * Suica<br>
     * 任意： メール決済<br>
     * 未使用： アプリ決済<br>
     */
    private $requestMailFlag;

    /** 
     * 内容確認画面付加情報<br>
     * 文字列<br>
     * 256 バイト以内<br>
     * 内容確認画面に表示する付加情報を設定します。<br>
     * モバイルSuicaで決済内容確認画面に表示される文字列<br>
     * Edy<br>
     * 未使用<br>
     * Suica<br>
     * 任意<br>
     */
    private $confirmScreenAddInfo;

    /** 
     * 完了画面付加情報<br>
     * 文字列<br>
     * 256 バイト以内<br>
     * 返金・新規返金完了画面に表示する付加情報を設定します。<br>
     * モバイルSuicaで決済完了画面に表示される文字列<br>
     * Edy<br>
     * 未使用<br>
     * Suica<br>
     * 任意<br>
     */
    private $completeScreenAddInfo;

    /** 
     * 画面タイトル<br>
     * 文字列<br>
     * 256 バイト以内<br>
     * モバイルSuicaで返金・新規返金完了画面・返金・新規返金確認画面等で「商品・サービス名」に表示されます。<br>
     * Edy<br>
     * 未使用<br>
     * Suica<br>
     * 任意<br>
     * CR（復帰）、LF（改行）は使用不可<br>
     */
    private $screenTitle;

    /** 
     * 備考<br>
     * 文字列<br>
     * 256 バイト以内<br>
     * 備考(商品詳細など)<br>
     * Edy<br>
     * 未使用<br>
     * Suica<br>
     * 任意<br>
     * CR（復帰）、LF（改行）は使用不可<br>
     */
    private $free;

    /** 
     * Edy個別ギフト名称<br>
     * 文字列<br>
     * 32 バイト以内<br>
     * Edyギフト画面で表示されるギフト名称の後に、個別ギフト名称を指定します。<br>
     * Edy<br>
     * 任意<br>
     * 未指定の場合、GWで決められた名称を表示します。<br>
     * Suica<br>
     * 未使用<br>
     */
    private $edyGiftName;

    /** 
     * 成功時URL<br>
     * 半角英数字<br>
     * 128 バイト<br>
     * PaSoRi決済時、決済が成功した場合に遷移されるURL<br>
     * Edy<br>
     * 未使用<br>
     * Suica<br>
     * 未使用<br>
     */
    private $successUrl;

    /** 
     * 失敗時URL<br>
     * 半角英数字<br>
     * 128 バイト<br>
     * PaSoRi決済時、決済が失敗した場合に遷移されるURL<br>
     * Edy<br>
     * 未使用<br>
     * Suica<br>
     * 未使用<br>
     */
    private $failureUrl;

    /** 
     * キャンセルURL<br>
     * 半角英数字<br>
     * 128 バイト<br>
     * PaSoRi決済時、確認画面等でキャンセルボタンが押された場合に遷移されるURL<br>
     * Edy<br>
     * 未使用<br>
     * Suica<br>
     * 未使用<br>
     */
    private $cancelUrl;

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
     * 金額を取得する<br>
     * @return 金額<br>
     */
    public function getAmount() {
        return $this->amount;
    }

    /**
     * 金額を設定する<br>
     * @param  amount 金額<br>
     */
    public function setAmount($amount) {
        $this->amount = $amount;
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
     * 返金対象取引IDを取得する<br>
     * @return 返金対象取引ID<br>
     */
    public function getRefundOrderId() {
        return $this->refundOrderId;
    }

    /**
     * 返金対象取引IDを設定する<br>
     * @param  refundOrderId 返金対象取引ID<br>
     */
    public function setRefundOrderId($refundOrderId) {
        $this->refundOrderId = $refundOrderId;
    }

    /**
     * 決済期限を取得する<br>
     * @return 決済期限<br>
     */
    public function getSettlementLimit() {
        return $this->settlementLimit;
    }

    /**
     * 決済期限を設定する<br>
     * @param  settlementLimit 決済期限<br>
     */
    public function setSettlementLimit($settlementLimit) {
        $this->settlementLimit = $settlementLimit;
    }

    /**
     * メールアドレスを取得する<br>
     * @return メールアドレス<br>
     */
    public function getMailAddr() {
        return $this->mailAddr;
    }

    /**
     * メールアドレスを設定する<br>
     * @param  mailAddr メールアドレス<br>
     */
    public function setMailAddr($mailAddr) {
        $this->mailAddr = $mailAddr;
    }

    /**
     * 転送メール送信要否を取得する<br>
     * @return 転送メール送信要否<br>
     */
    public function getForwardMailFlag() {
        return $this->forwardMailFlag;
    }

    /**
     * 転送メール送信要否を設定する<br>
     * @param  forwardMailFlag 転送メール送信要否<br>
     */
    public function setForwardMailFlag($forwardMailFlag) {
        $this->forwardMailFlag = $forwardMailFlag;
    }

    /**
     * マーチャントメールアドレスを取得する<br>
     * @return マーチャントメールアドレス<br>
     */
    public function getMerchantMailAddr() {
        return $this->merchantMailAddr;
    }

    /**
     * マーチャントメールアドレスを設定する<br>
     * @param  merchantMailAddr マーチャントメールアドレス<br>
     */
    public function setMerchantMailAddr($merchantMailAddr) {
        $this->merchantMailAddr = $merchantMailAddr;
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
     * 依頼メール付加情報を取得する<br>
     * @return 依頼メール付加情報<br>
     */
    public function getRequestMailAddInfo() {
        return $this->requestMailAddInfo;
    }

    /**
     * 依頼メール付加情報を設定する<br>
     * @param  requestMailAddInfo 依頼メール付加情報<br>
     */
    public function setRequestMailAddInfo($requestMailAddInfo) {
        $this->requestMailAddInfo = $requestMailAddInfo;
    }

    /**
     * 依頼メール送信要否を取得する<br>
     * @return 依頼メール送信要否<br>
     */
    public function getRequestMailFlag() {
        return $this->requestMailFlag;
    }

    /**
     * 依頼メール送信要否を設定する<br>
     * @param  requestMailFlag 依頼メール送信要否<br>
     */
    public function setRequestMailFlag($requestMailFlag) {
        $this->requestMailFlag = $requestMailFlag;
    }

    /**
     * 内容確認画面付加情報を取得する<br>
     * @return 内容確認画面付加情報<br>
     */
    public function getConfirmScreenAddInfo() {
        return $this->confirmScreenAddInfo;
    }

    /**
     * 内容確認画面付加情報を設定する<br>
     * @param  confirmScreenAddInfo 内容確認画面付加情報<br>
     */
    public function setConfirmScreenAddInfo($confirmScreenAddInfo) {
        $this->confirmScreenAddInfo = $confirmScreenAddInfo;
    }

    /**
     * 完了画面付加情報を取得する<br>
     * @return 完了画面付加情報<br>
     */
    public function getCompleteScreenAddInfo() {
        return $this->completeScreenAddInfo;
    }

    /**
     * 完了画面付加情報を設定する<br>
     * @param  completeScreenAddInfo 完了画面付加情報<br>
     */
    public function setCompleteScreenAddInfo($completeScreenAddInfo) {
        $this->completeScreenAddInfo = $completeScreenAddInfo;
    }

    /**
     * 画面タイトルを取得する<br>
     * @return 画面タイトル<br>
     */
    public function getScreenTitle() {
        return $this->screenTitle;
    }

    /**
     * 画面タイトルを設定する<br>
     * @param  screenTitle 画面タイトル<br>
     */
    public function setScreenTitle($screenTitle) {
        $this->screenTitle = $screenTitle;
    }

    /**
     * 備考を取得する<br>
     * @return 備考<br>
     */
    public function getFree() {
        return $this->free;
    }

    /**
     * 備考を設定する<br>
     * @param  free 備考<br>
     */
    public function setFree($free) {
        $this->free = $free;
    }

    /**
     * Edy個別ギフト名称を取得する<br>
     * @return Edy個別ギフト名称<br>
     */
    public function getEdyGiftName() {
        return $this->edyGiftName;
    }

    /**
     * Edy個別ギフト名称を設定する<br>
     * @param  edyGiftName Edy個別ギフト名称<br>
     */
    public function setEdyGiftName($edyGiftName) {
        $this->edyGiftName = $edyGiftName;
    }

    /**
     * 成功時URLを取得する<br>
     * @return 成功時URL<br>
     */
    public function getSuccessUrl() {
        return $this->successUrl;
    }

    /**
     * 成功時URLを設定する<br>
     * @param  successUrl 成功時URL<br>
     */
    public function setSuccessUrl($successUrl) {
        $this->successUrl = $successUrl;
    }

    /**
     * 失敗時URLを取得する<br>
     * @return 失敗時URL<br>
     */
    public function getFailureUrl() {
        return $this->failureUrl;
    }

    /**
     * 失敗時URLを設定する<br>
     * @param  failureUrl 失敗時URL<br>
     */
    public function setFailureUrl($failureUrl) {
        $this->failureUrl = $failureUrl;
    }

    /**
     * キャンセルURLを取得する<br>
     * @return キャンセルURL<br>
     */
    public function getCancelUrl() {
        return $this->cancelUrl;
    }

    /**
     * キャンセルURLを設定する<br>
     * @param  cancelUrl キャンセルURL<br>
     */
    public function setCancelUrl($cancelUrl) {
        $this->cancelUrl = $cancelUrl;
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
