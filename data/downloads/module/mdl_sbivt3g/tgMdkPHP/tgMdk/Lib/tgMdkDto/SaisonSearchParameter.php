<?php
/**
 * 検索条件:セゾン検索パラメータクラス<br>
 *
 * @author Veritrans, Inc.
 */
class SaisonSearchParameter {

    /**
     * 詳細オーダー決済状態<br>
     */
    private $detailOrderType;

    /**
     * 合計決済金額（From, To）<br>
     */
    private $totalAmount;

    /**
     * ウォレット決済金額（From, To）<br>
     */
    private $walletAmount;

    /**
     * カード決済金額（From, To）<br>
     */
    private $cardAmount;


    /**
     * 詳細オーダー決済状態を取得する<br>
     *
     * @return 詳細オーダー決済状態<br>
     */
    public function getDetailOrderType() {
        return $this->detailOrderType;
    }

    /**
     * 詳細オーダー決済状態を設定する<br>
     *
     * @param detailOrderType 詳細オーダー決済状態<br>
     */
    public function setDetailOrderType($detailOrderType) {
        $this->detailOrderType = $detailOrderType;
    }

    /**
     * 合計決済金額（From, To）を取得する<br>
     *
     * @return 合計決済金額（From, To）<br>
     */
    public function getTotalAmount() {
        return $this->totalAmount;
    }

    /**
     * 合計決済金額（From, To）を設定する<br>
     *
     * @param totalAmount 合計決済金額（From, To）<br>
     */
    public function setTotalAmount($totalAmount) {
        $this->totalAmount = $totalAmount;
    }

    /**
     * ウォレット決済金額（From, To）を取得する<br>
     *
     * @return ウォレット決済金額（From, To）<br>
     */
    public function getWalletAmount() {
        return $this->walletAmount;
    }

    /**
     * ウォレット決済金額（From, To）を設定する<br>
     *
     * @param walletAmount ウォレット決済金額（From, To）<br>
     */
    public function setWalletAmount($walletAmount) {
        $this->walletAmount = $walletAmount;
    }

    /**
     * カード決済金額（From, To）を取得する<br>
     *
     * @return カード決済金額（From, To）<br>
     */
    public function getCardAmount() {
        return $this->cardAmount;
    }

    /**
     * カード決済金額（From, To）を設定する<br>
     *
     * @param cardAmount カード決済金額（From, To）<br>
     */
    public function setCardAmount($cardAmount) {
        $this->cardAmount = $cardAmount;
    }

}
?>
