<?php
/**
 * 検索条件:ショッピングクレジット検索パラメータクラス<br>
 */
class OricoscSearchParameter {

    /**
     * 詳細オーダー決済状態<br>
     */
    private $detailOrderType;

    /**
     * 注文番号<br>
     */
    private $oricoOrderNo;

    /**
     * 支払金額合計（From, To）<br>
     */
    private $amount;

    /**
     * 商品価格合計（From, To）<br>
     */
    private $totalItemAmount;


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
     * 注文番号を取得する<br>
     *
     * @return 注文番号<br>
     */
    public function getOricoOrderNo() {
        return $this->oricoOrderNo;
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
     * 支払金額合計（From, To）を取得する<br>
     *
     * @return 支払金額合計（From, To）<br>
     */
    public function getAmount() {
        return $this->amount;
    }

    /**
     * 支払金額合計（From, To）を設定する<br>
     *
     * @param amount 支払金額合計（From, To）<br>
     */
    public function setAmount($amount) {
        $this->amount = $amount;
    }

    /**
     * 商品価格合計（From, To）を取得する<br>
     *
     * @return 商品価格合計（From, To）<br>
     */
    public function getTotalItemAmount() {
        return $this->totalItemAmount;
    }

    /**
     * 商品価格合計（From, To）を設定する<br>
     *
     * @param totalItemAmount 商品価格合計（From, To）<br>
     */
    public function setTotalItemAmount($totalItemAmount) {
        $this->totalItemAmount = $totalItemAmount;
    }

}
?>
