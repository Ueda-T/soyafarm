<?php
/**
 * 決済サービスタイプ：Alipay、コマンド名：返金の要求Dtoクラス<br>
 *
 * @author Veritrans, Inc.
 *
 */
class AlipayRefundRequestDto extends AbstractPaymentRequestDto {

    /** 
     * 決済サービスタイプ<br>
     * 半角英数字<br>
     * 必須項目、固定値<br>
     */
	private $SERVICE_TYPE = "alipay";

    /** 
     * 決済サービスコマンド:返金<br>
     * 半角英数字<br>
     * 必須項目、固定値<br>
     */
	private $SERVICE_COMMAND = "Refund";

    /** 
     * 取引ID<br>
     * 半角英数字<br>
     * 100 文字以内<br>
     * 決済請求、予授権完了時に採番した取引IDを指定します。<br>
     * “.”（ドット）、“-”（ハイフン）、“_”（アンダースコア）も使用できます。<br>
     */
	private $orderId;

    /**
     * 決済金額<br>
     * 半角数字<br>
     * 7 桁以内<br>
     * 返金金額を指定します。<br>
     */
	private $amount;

    /** 
     * 返金理由<br>
     * 文字列<br>
     * 80桁以内<br>
     * 返金の理由を指定します。<br>
     */
	private $reason;

	/**
	 * ログ用文字列(マスク済み)<br>
	 */
	private $maskedLog;

    /**
	 * 決済サービスタイプを取得する<br>
	 * @return 決済サービスタイプ<br>
	 */
	public function getServiceType() {
		return $this -> SERVICE_TYPE;
	}

	/**
	 * 決済サービスコマンドを取得する<br>
	 * @return 決済サービスコマンド<br>
	 */
	public function getServiceCommand() {
		return $this -> SERVICE_COMMAND;
	}
    
	/**
	 * 取引IDを取得する<br>
	 * @return 取引ID<br>
	 */
	public function getOrderId() {
		return $this -> orderId;
	}

	/**
	 * 取引IDを設定する<br>
	 * @param  orderId 取引ID<br>
	 */
	public function setOrderId($orderId) {
		$this -> orderId = $orderId;
	}

	/**
	 * 取引金額を取得する<br>
	 * @return 取引金額<br>
	 */
	public function getAmount() {
		return $this -> amount;
	}

	/**
	 * 取引金額を設定する<br>
	 * @param  amount 取引金額<br>
	 */
	public function setAmount($amount) {
		$this -> amount = $amount;
	}

	/**
	 * 返金理由を取得する<br>
	 * @return 返金理由<br>
	 */
	public function getReason() {
		return $this -> reason;
	}

	/**
	 * 返金理由を設定する<br>
	 * @param  reason 返金理由<br>
	 */
	public function setReason($reason) {
		$this -> reason = $reason;
	}

	/**
	 * ログ用文字列(マスク済み)を設定する<br>
	 * @param  maskedLog ログ用文字列(マスク済み)<br>
	 */
	public function _setMaskedLog($maskedLog) {
		$this -> maskedLog = $maskedLog;
	}

	/**
	 * ログ用文字列(マスク済み)を取得する<br>
	 * @return ログ用文字列(マスク済み)<br>
	 */
	public function toString() {
		return (string)$this -> maskedLog;
	}
}
?>