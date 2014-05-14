<?php
/**
 * 決済サービスタイプ：電子マネー、コマンド名：決済の要求Dtoクラス
 *
 * @version 1.0.11 Edy Direct対応
 * @author Veritrans, Inc.
 *
 */
class EmAuthorizeRequestDto extends AbstractPaymentRequestDto
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
    private $SERVICE_COMMAND = "Authorize";

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
     * "edy-direct"<br>
     * 　：Edyダイレクト（モバイル）<br>
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
     * 決済金額<br>
     * 半角数字<br>
     * 7 桁以内<br>
     * 支払金額となります。<br>
     * 例）2100<br>
     * Edy<br>
     * 1 以上<br>
     *  ～ 50,000 以下<br>
     * Suica<br>
     * 1 以上<br>
     *  ～ 20,000 以下<br>
     */
    private $amount;

    /** 
     * 決済期限<br>
     * 半角数字<br>
     * 14 桁固定<br>
     * 支払の期限となります。<br>
     * 支払期限を過ぎた決済については、消費者はアプリ上から確認できます。<br>
     * YYYYMMDDhhmmss の形式<br>
     * 例）20060901235959<br>
     * Edy<br>
     * 当日～90日後： Mobile Edyのみ<br>
     * 未使用(10日固定)： pc Edy<br>
     * Suica<br>
     * 60日以内<br>
     */
    private $settlementLimit;

    /** 
     * メールアドレス<br>
     * 半角英数字<br>
     * 256 文字以内<br>
     * 決済依頼メールを送信する消費者の携帯電話メールアドレスとなります。<br>
     * Edy<br>
     * 必須： Mobile Edy<br>
     * 未使用： pc Edy<br>
     * 　パソリによる未使用時は<br>
     * 　固定値 "#@NOMAIL"<br>
     * Suica<br>
     * 必須： メール決済<br>
     * 未使用： アプリ決済<br>
     */
    private $mailAddr;

    /** 
     * 転送メール送信要否<br>
     * 半角数字<br>
     * 1 桁固定<br>
     * 決済依頼メール、決済完了メールのコピーメール又はBCC メールをマーチャントメールアドレス（merchantMailAddr）に送信するか否かを設定します。<br>
     * 0： 送信不要<br>
     * 1： 送信要<br>
     * Edy<br>
     * 任意： Mobile Edy<br>
     * 未使用： pc Edy<br>
     * Suica<br>
     * 任意： メール決済<br>
     * 未使用： アプリ決済<br>
     */
    private $forwardMailFlag;

    /** 
     * マーチャントメールアドレス<br>
     * 半角英数字<br>
     * 256 文字以内<br>
     * 決済依頼メール、決済完了メールのコピーメール又はBCC メール先マーチャントメールアドレス。<br>
     * 以下の文字も使用できます。<br>
     * “.”(ドット)、“-”(ハイフン)、“_”(アンダースコア)、“@”(アットマーク)<br>
     * Edy<br>
     * 任意： Mobile Edy<br>
     * 未使用： pc Edy<br>
     * Suica<br>
     * 任意： メール決済<br>
     * 任意： アプリ決済<br>
     */
    private $merchantMailAddr;

    /** 
     * 依頼メール付加情報<br>
     * 文字列<br>
     * 256 バイト以内<br>
     * 支払依頼メールに追加される文字列（商品情報等）です。<br>
     * Edy<br>
     * 任意： Mobile Edy<br>
     * 未使用： pc Edy<br>
     * Suica<br>
     * 任意： メール決済<br>
     * 未使用： アプリ決済<br>
     */
    private $requestMailAddInfo;

    /** 
     * 完了メール付加情報<br>
     * 文字列<br>
     * 320 バイト以内<br>
     * 決済完了メールに追加される文字列（遷移先URL 等）<br>
     * Edy<br>
     * 任意： Mobile Edy<br>
     * 未使用： pc Edy<br>
     * Suica<br>
     * 任意： メール決済<br>
     * 未使用： アプリ決済<br>
     */
    private $completeMailAddInfo;

    /** 
     * ショップ名<br>
     * 文字列<br>
     * 48 バイト以内<br>
     * Edy で使用する店舗名<br>
     * Edy<br>
     * 任意： Mobile Edy<br>
     * 未使用： pc Edy<br>
     * Suica<br>
     * 未使用<br>
     */
    private $shopName;

    /** 
     * 完了メール送信要否<br>
     * 半角数字<br>
     * 1 桁固定<br>
     * 決済完了時にメールを送信するか否かを設定します。<br>
     * 0： 送信不要　1： 送信要<br>
     * Edy<br>
     * 未使用<br>
     * Suica<br>
     * 任意<br>
     */
    private $completeMailFlag;

    /** 
     * 内容確認画面付加情報<br>
     * 文字列<br>
     * 256 バイト以内<br>
     * 内容確認画面に表示する付加情報を設定します。<br>
     * モバイルSuica で決済内容確認画面に表示される文字列<br>
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
     * 決済完了画面に表示する付加情報を設定します。<br>
     * モバイルSuica で決済完了画面に表示される文字列<br>
     * Edy<br>
     * 未使用<br>
     * Suica<br>
     * 任意<br>
     */
    private $completeScreenAddInfo;

    /** 
     * 画面タイトル<br>
     * 文字列<br>
     * 40 バイト以内<br>
     * モバイルSuica で決済完了画面・決済確認画面等で「商品・サービス名」に表示されます。<br>
     * Edy<br>
     * 未使用<br>
     * Suica<br>
     * 必須<br>
     * CR（復帰）、LF（改行）は使用不可<br>
     */
    private $screenTitle;

    /** 
     * 決済完了戻り先種別<br>
     * 半角数字<br>
     * 1 桁固定<br>
     * モバイルSuica でアプリ終了時に遷移する先の種別<br>
     * 1： ローカルメニュー<br>
     * 2： モバイルSuica アプリを終了し「決済完了戻り先URL」へ遷移<br>
     * Edy<br>
     * 未使用<br>
     * Suica<br>
     * 任意： メール決済<br>
     * 未使用： アプリ決済<br>
     */
    private $completeReturnKind;

    /** 
     * 決済完了戻り先URL<br>
     * 半角英数字<br>
     * 256 文字以内<br>
     * モバイルSuicaでアプリ終了時に遷移する先のURL<br>
     * Edy<br>
     * 未使用<br>
     * Suica<br>
     * 任意<br>
     */
    private $completeReturnUrl;

    /** 
     * 決済完了通知URL<br>
     * 半角英数字<br>
     * 256 文字以内<br>
     * Edy Viewer にて支払いを完了した後に遷移するURL<br>
     * Edy<br>
     * 未使用： Mobile Edy<br>
     * 任意： pc Edy<br>
     * Suica<br>
     * 未使用<br>
     */
    private $completeNoticeUrl;

    /** 
     * 販売区分<br>
     * 半角数字<br>
     * 1 桁固定<br>
     * Edy で任意に登録する販売区分<br>
     * 　1：物販、　2：デジタル<br>
     * Edy<br>
     * 任意<br>
     * Suica<br>
     * 未使用<br>
     */
    private $salesType;

    /** 
     * 備考<br>
     * 文字列<br>
     * 256 バイト<br>
     * 備考(商品詳細など)<br>
     * Edy<br>
     * 任意<br>
     * カンマ、セミコロン、シングルクォーテーション、CR（復帰）、LF（改行）は使用不可<br>
     * Suica<br>
     * 任意<br>
     * CR（復帰）、LF（改行）は使用不可<br>
     */
    private $free;

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
     * 加盟店決済受付時刻<br>
     * 半角数字<br>
     * 14 桁固定<br>
     * 加盟店側で当該決済を受付た時刻<br>
     * YYYYMMDDhhmmss の形式<br>
     * 例）20060901235959<br>
     * Edy<br>
     * 未使用<br>
     * Suica<br>
     * 未使用<br>
     */
    private $memberStoreSettleEntryTime;

    /** 
     * 支払取消期限<br>
     * 半角数字<br>
     * 14 桁固定<br>
     * 決済後、該当の支払を返金（取消）することができる期限。<br>
     * YYYYMMDDhhmmss の形式<br>
     * 例）20060901235959<br>
     * Edy<br>
     * 未使用<br>
     * Suica<br>
     * 未使用<br>
     */
    private $cancelLimit;

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
     * 決済金額を取得する<br>
     * @return 決済金額<br>
     */
    public function getAmount() {
        return $this->amount;
    }

    /**
     * 決済金額を設定する<br>
     * @param  amount 決済金額<br>
     */
    public function setAmount($amount) {
        $this->amount = $amount;
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
     * 完了メール付加情報を取得する<br>
     * @return 完了メール付加情報<br>
     */
    public function getCompleteMailAddInfo() {
        return $this->completeMailAddInfo;
    }

    /**
     * 完了メール付加情報を設定する<br>
     * @param  completeMailAddInfo 完了メール付加情報<br>
     */
    public function setCompleteMailAddInfo($completeMailAddInfo) {
        $this->completeMailAddInfo = $completeMailAddInfo;
    }

    /**
     * ショップ名を取得する<br>
     * @return ショップ名<br>
     */
    public function getShopName() {
        return $this->shopName;
    }

    /**
     * ショップ名を設定する<br>
     * @param  shopName ショップ名<br>
     */
    public function setShopName($shopName) {
        $this->shopName = $shopName;
    }

    /**
     * 完了メール送信要否を取得する<br>
     * @return 完了メール送信要否<br>
     */
    public function getCompleteMailFlag() {
        return $this->completeMailFlag;
    }

    /**
     * 完了メール送信要否を設定する<br>
     * @param  completeMailFlag 完了メール送信要否<br>
     */
    public function setCompleteMailFlag($completeMailFlag) {
        $this->completeMailFlag = $completeMailFlag;
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
     * 決済完了戻り先種別を取得する<br>
     * @return 決済完了戻り先種別<br>
     */
    public function getCompleteReturnKind() {
        return $this->completeReturnKind;
    }

    /**
     * 決済完了戻り先種別を設定する<br>
     * @param  completeReturnKind 決済完了戻り先種別<br>
     */
    public function setCompleteReturnKind($completeReturnKind) {
        $this->completeReturnKind = $completeReturnKind;
    }

    /**
     * 決済完了戻り先URLを取得する<br>
     * @return 決済完了戻り先URL<br>
     */
    public function getCompleteReturnUrl() {
        return $this->completeReturnUrl;
    }

    /**
     * 決済完了戻り先URLを設定する<br>
     * @param  completeReturnUrl 決済完了戻り先URL<br>
     */
    public function setCompleteReturnUrl($completeReturnUrl) {
        $this->completeReturnUrl = $completeReturnUrl;
    }

    /**
     * 決済完了通知URLを取得する<br>
     * @return 決済完了通知URL<br>
     */
    public function getCompleteNoticeUrl() {
        return $this->completeNoticeUrl;
    }

    /**
     * 決済完了通知URLを設定する<br>
     * @param  completeNoticeUrl 決済完了通知URL<br>
     */
    public function setCompleteNoticeUrl($completeNoticeUrl) {
        $this->completeNoticeUrl = $completeNoticeUrl;
    }

    /**
     * 販売区分を取得する<br>
     * @return 販売区分<br>
     */
    public function getSalesType() {
        return $this->salesType;
    }

    /**
     * 販売区分を設定する<br>
     * @param  salesType 販売区分<br>
     */
    public function setSalesType($salesType) {
        $this->salesType = $salesType;
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
     * 加盟店決済受付時刻を取得する<br>
     * @return 加盟店決済受付時刻<br>
     */
    public function getMemberStoreSettleEntryTime() {
        return $this->memberStoreSettleEntryTime;
    }

    /**
     * 加盟店決済受付時刻を設定する<br>
     * @param  memberStoreSettleEntryTime 加盟店決済受付時刻<br>
     */
    public function setMemberStoreSettleEntryTime($memberStoreSettleEntryTime) {
        $this->memberStoreSettleEntryTime = $memberStoreSettleEntryTime;
    }

    /**
     * 支払取消期限を取得する<br>
     * @return 支払取消期限<br>
     */
    public function getCancelLimit() {
        return $this->cancelLimit;
    }

    /**
     * 支払取消期限を設定する<br>
     * @param  cancelLimit 支払取消期限<br>
     */
    public function setCancelLimit($cancelLimit) {
        $this->cancelLimit = $cancelLimit;
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
