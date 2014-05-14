<?php
/**
 * 検索条件:Alipay検索パラメータクラス<br>
 *
 * @author Veritrans, Inc.
 *
 */
class AlipaySearchParameter {

    /**
     * 詳細オーダー決済状態<br>
     */
    private $detailOrderType;
    
	/**
	 * 決済センターとの取引ID<br>
	 */
	private $centerTradeId;

    /** 
     * 支払日時（From, To）<br>
     * 支払日時をYYYYMMDDhhmm形式で指定します。<br>
     */
	private $paymentTime;

    /** 
     * 清算日時（From, To）<br>
     * 清算日時をYYYYMMDDhhmm形式で指定します。<br>
     */
	private $settlementTime;

    /**
     * 詳細オーダー決済状態を取得する<br>
     * 
     * @return 詳細オーダー決済状態<br>
     */
    public function getDetailOrderType() {
        return $this -> detailOrderType;
    }

    /**
     * 詳細オーダー決済状態を設定する<br>
     * 
     * @param detailOrderType
     *            詳細オーダー決済状態<br>
     */
    public function setDetailOrderType($detailOrderType) {
        $this -> detailOrderType = $detailOrderType;
    }
    
	/**
	 * 決済センターとの取引IDを取得する<br>
	 *
	 * @return 決済センターとの取引ID<br>
	 */
	public function getCenterTradeId() {
		return $this -> centerTradeId;
	}

	/**
	 * 決済センターとの取引IDを設定する<br>
	 *
	 * @param centerTradeId 決済センターとの取引ID<br>
	 */
	public function setCenterTradeId($centerTradeId) {
		$this -> centerTradeId = $centerTradeId;
	}

	/**
	 * 支払い日時（From, To)を取得する<br>
	 *
	 * @return 支払い日時（From, To)<br>
	 */
	public function getPaymentTime() {
		return $this -> paymentTime;
	}

	/**
	 * 支払い日時（From, To)を設定する<br>
	 *
	 * @param paymentTime 支払い日時（From, To)<br>
	 */
	public function setPaymentTime($paymentTime) {
		$this -> paymentTime = $paymentTime;
	}

	/**
	 * 決済日時（From, To)を取得する<br>
	 *
	 * @return 決済日時（From, To)<br>
	 */
	public function getSettlementTime() {
		return $this -> settlementTime;
	}

	/**
	 * 決済日時（From, To)を設定する<br>
	 *
	 * @param settlementTime 決済日時（From, To)<br>
	 */
	public function setSettlementTime($settlementTime) {
		$this -> settlementTime = $settlementTime;
	}
}
?>