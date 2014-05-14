<?php
/**
 * 検索条件:コンビニ検索パラメータクラス<br>
 *
 * @author Veritrans, Inc.
 */
class CvsSearchParameter {

    /**
     * 詳細オーダー決済状態<br>
     */
    private $detailOrderType;

    /**
     * コンビニタイプ<br>
     */
    private $cvsType;

    /**
     * 支払期限（From, To）<br>
     */
    private $payLimit;

    /**
     * 入金受付日（From, To）<br>
     */
    private $paidDatetime;

    /**
     * 詳細オーダー決済状態を取得する<br>
     *
     * @return $this->詳細オーダー決済状態<br>
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
     * コンビニタイプを取得する<br>
     *
     * @return $this->コンビニタイプ<br>
     */
    public function getCvsType() {
        return $this->cvsType;
    }

    /**
     * コンビニタイプを設定する<br>
     *
     * @param cvsType コンビニタイプ<br>
     */
    public function setCvsType($cvsType) {
        $this->cvsType = $cvsType;
    }

    /**
     * 支払期限（From, To）を取得する<br>
     *
     * @return $this->支払期限（From, To）<br>
     */
    public function getPayLimit() {
        return $this->payLimit;
    }

    /**
     * 支払期限（From, To）を設定する<br>
     *
     * @param payLimit 支払期限（From, To）<br>
     */
    public function setPayLimit($payLimit) {
        $this->payLimit = $payLimit;
    }

    /**
     * 入金受付日（From, To）を取得する<br>
     *
     * @return $this->入金受付日（From, To）<br>
     */
    public function getPaidDatetime() {
        return $this->paidDatetime;
    }

    /**
     * 入金受付日（From, To）を設定する<br>
     *
     * @param paidDatetime 入金受付日（From, To）<br>
     */
    public function setPaidDatetime($paidDatetime) {
        $this->paidDatetime = $paidDatetime;
    }

}
?>