<?php
/**
 * 検索条件:カード検索パラメータクラス<br>
 *
 * @author Veritrans, Inc.
 */
class CardSearchParameter {

    /**
     * 詳細オーダー決済状態<br>
     */
    private $detailOrderType;

    /**
     * 仕向先カード会社<br>
     */
    private $requestedCorporationId;
    /**
     * 支払い種別<br>
     */
    private $requestedPaymentMethodType;

    /**
     * 3d-xid<br>
     */
    private $dddTransactionId;

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
     * 仕向先カード会社を取得する<br>
     *
     * @return $this->仕向先カード会社<br>
     */
    public function getRequestedCorporationId() {
        return $this->requestedCorporationId;
    }

    /**
     * 仕向先カード会社を設定する<br>
     *
     * @param requestedCorporationId 仕向先カード会社<br>
     */
    public function setRequestedCorporationId($requestedCorporationId) {
        $this->requestedCorporationId = $requestedCorporationId;
    }

    /**
     * 支払い種別を取得する<br>
     *
     * @return $this->支払い種別<br>
     */
    public function getRequestedPaymentMethodType() {
        return $this->requestedPaymentMethodType;
    }

    /**
     * 支払い種別を設定する<br>
     *
     * @param requestedPaymentMethodType 支払い種別<br>
     */
    public function setRequestedPaymentMethodType($requestedPaymentMethodType) {
        $this->requestedPaymentMethodType = $requestedPaymentMethodType;
    }

    /**
     * 3d-xidを取得する<br>
     *
     * @return $this->3d-xid<br>
     */
    public function getDddTransactionId() {
        return $this->dddTransactionId;
    }

    /**
     * 3d-xidを設定する<br>
     *
     * @param dddTransactionId 3d-xid<br>
     */
    public function setDddTransactionId($dddTransactionId) {
        $this->dddTransactionId = $dddTransactionId;
    }

}
?>