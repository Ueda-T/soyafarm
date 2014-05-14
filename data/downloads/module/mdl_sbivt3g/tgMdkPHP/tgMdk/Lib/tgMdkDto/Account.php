<?php


/**
 * 会員情報のクラス
 *
 * @author Created automatically by DtoCreator
 *
 */
class Account {

    /**
     * 会員ID<br>
     * 半角英数字<br/>
     * 最大桁数：100<br/>
     * 以下の文字も使用できます。<br/>
     * “.”(ドット)、“-”(ハイフン)、“_”(アンダースコア)、“@”(アットマーク) <br/>
     */
    private $accountId;

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
     * 会員基本情報を格納するオブジェクト<br/>
     */
    private $accountBasic;

    /**
     * 連絡先情報<br>
     * オブジェクト配列<br/>
     * 連絡先情報を格納するオブジェクト<br/>
     * <br/>
     */
    private $accountInfo;

    /**
     * カード情報<br>
     * オブジェクト配列<br/>
     */
    private $cardInfo;

    /**
     * 会員課金情報<br>
     * オブジェクト配列<br/>
     */
    private $recurringCharge;



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
     * @param accountBasic 会員基本情報<br>
     */
    public function setAccountBasic($accountBasic) {
        $this->accountBasic = $accountBasic;
    }

    /**
     * 会員基本情報を取得する<br>
     * @return 会員基本情報<br>
     */
    public function getAccountBasic() {
        return $this->accountBasic;
    }

    /**
     * 連絡先情報を設定する<br>
     * @param accountInfo 連絡先情報<br>
     */
    public function setAccountInfo($accountInfo) {
        $this->accountInfo = $accountInfo;
    }

    /**
     * 連絡先情報を取得する<br>
     * @return 連絡先情報<br>
     */
    public function getAccountInfo() {
        return $this->accountInfo;
    }

    /**
     * カード情報を設定する<br>
     * @param cardInfo カード情報<br>
     */
    public function setCardInfo($cardInfo) {
        $this->cardInfo = $cardInfo;
    }

    /**
     * カード情報を取得する<br>
     * @return カード情報<br>
     */
    public function getCardInfo() {
        return $this->cardInfo;
    }

    /**
     * 会員課金情報を設定する<br>
     * @param recurringCharge 会員課金情報<br>
     */
    public function setRecurringCharge($recurringCharge) {
        $this->recurringCharge = $recurringCharge;
    }

    /**
     * 会員課金情報を取得する<br>
     * @return 会員課金情報<br>
     */
    public function getRecurringCharge() {
        return $this->recurringCharge;
    }



}
?>