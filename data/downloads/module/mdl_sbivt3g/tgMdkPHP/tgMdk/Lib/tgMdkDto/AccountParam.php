<?php


/**
 * 会員情報のクラス
 *
 * @author Created automatically by DtoCreator
 *
 */
class AccountParam {

    /**
     * 会員ID<br>
     * 半角英数字<br/>
     * 最大桁数：100<br/>
     * 以下の文字も使用できます。<br/>
     * “.”(ドット)、“-”(ハイフン)、“_”(アンダースコア)、“@”(アットマーク) <br/>
     */
    private $accountId;

    /**
     * PayNowID<br>
     * 半角英数字<br/>
     * 最大桁数：100<br/>
     * グローバル情報を元に会員情報を作成する場合に指定する。<br/>
     * 以下の文字も使用できます。<br/>
     * “.”(ドット)、“-”(ハイフン)、“_”(アンダースコア)、“@”(アットマーク) <br/>
     */
    private $payNowId;

    /**
     * リクエスト項目<br>
     * 半角英数字<br/>
     * 最大桁数：256<br/>
     * レスポンスに含める項目を記述する。<br/>
     * “-”(ハイフン)、“_”(アンダースコア)、“,”(カンマ)<br/>
     */
    private $transData;

    /**
     * 会員基本情報<br>
     * オブジェクト<br/>
     * 会員情報を格納するオブジェクト<br/>
     */
    private $accountBasicParam;

    /**
     * カード情報<br>
     * オブジェクト<br/>
     */
    private $cardParam;

    /**
     * 会員課金情報<br>
     * オブジェクト<br/>
     */
    private $recurringChargeParam;



    /**
     * 会員IDを設定する<br>
     * @param accountId 会員ID<br>
     */
    public function setAccountId($accountId) {
        $this->accountId = $accountId;
    }

    /**
     * 会員IDを取得する<br>
     * @return 会員ID<br>
     */
    public function getAccountId() {
        return $this->accountId;
    }

    /**
     * PayNowIDを設定する<br>
     * @param payNowId PayNowID<br>
     */
    public function setPayNowId($payNowId) {
        $this->payNowId = $payNowId;
    }

    /**
     * PayNowIDを取得する<br>
     * @return PayNowID<br>
     */
    public function getPayNowId() {
        return $this->payNowId;
    }

    /**
     * リクエスト項目を設定する<br>
     * @param transData リクエスト項目<br>
     */
    public function setTransData($transData) {
        $this->transData = $transData;
    }

    /**
     * リクエスト項目を取得する<br>
     * @return リクエスト項目<br>
     */
    public function getTransData() {
        return $this->transData;
    }

    /**
     * 会員基本情報を設定する<br>
     * @param accountBasicParam 会員基本情報<br>
     */
    public function setAccountBasicParam($accountBasicParam) {
        $this->accountBasicParam = $accountBasicParam;
    }

    /**
     * 会員基本情報を取得する<br>
     * @return 会員基本情報<br>
     */
    public function getAccountBasicParam() {
        return $this->accountBasicParam;
    }

    /**
     * カード情報を設定する<br>
     * @param cardParam カード情報<br>
     */
    public function setCardParam($cardParam) {
        $this->cardParam = $cardParam;
    }

    /**
     * カード情報を取得する<br>
     * @return カード情報<br>
     */
    public function getCardParam() {
        return $this->cardParam;
    }

    /**
     * 会員課金情報を設定する<br>
     * @param recurringChargeParam 会員課金情報<br>
     */
    public function setRecurringChargeParam($recurringChargeParam) {
        $this->recurringChargeParam = $recurringChargeParam;
    }

    /**
     * 会員課金情報を取得する<br>
     * @return 会員課金情報<br>
     */
    public function getRecurringChargeParam() {
        return $this->recurringChargeParam;
    }



}
?>