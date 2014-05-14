<?php
/**
 * マスタ検索結果のクラス<br>
 *
 * @author Veritrans, Inc.
 */
class Masters {

    /**
     * 金融機関マスタ<br>
     */
    private $bankFinancialInstInfo;

    /**
     * コンストラクタ。
     */
    public function __construct() {
        $this->bankFinancialInstInfo = Array(new MdkBaseDto());
    }

    /**
     * 金融機関マスタリストを取得する<br>
     * @return 金融機関マスタリスト<br>
     */
    public function getBankFinancialInstInfo() {
        return $this->bankFinancialInstInfo;
    }

    /**
     * 金融機関マスタリストを設定する<br>
     * @param  bankFinancialInstInfo 金融機関マスタリスト<br>
     */
    public function setBankFinancialInstInfo($bankFinancialInstInfo) {
        $this->bankFinancialInstInfo = $bankFinancialInstInfo;
    }
}
?>