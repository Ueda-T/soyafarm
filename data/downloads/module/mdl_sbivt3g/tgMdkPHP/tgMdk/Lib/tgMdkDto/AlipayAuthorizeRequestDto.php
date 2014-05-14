<?php
/**
 * 決済サービスタイプ：Alipay、コマンド名：与信の要求Dtoクラス<br>
 *
 * @author Veritrans, Inc.
 *
 */
class AlipayAuthorizeRequestDto extends AbstractPaymentRequestDto {

    /** 
     * 決済サービスタイプ<br>
     * 半角英数字<br>
     * 必須項目、固定値<br>
     */
	private $SERVICE_TYPE = "alipay";

    /** 
     * 決済サービスコマンド:与信<br>
     * 半角英数字<br>
     * 必須項目、固定値<br>
     */
	private $SERVICE_COMMAND = "Authorize";

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
     * 決済金額を指定します。<br>
     */
	private $amount;

    /** 
     * 取引通貨種類<br>
     * 半角文字<br>
     * 3 桁以内<br>
     */
	private $currency;

    /** 
     * 決済が成功した後のECサイト側への戻り先のURL<br>
     * 半角英数字<br>
     * 256文字以内<br>
     * 半角英数字のほかに、URLとして使用できる文字を使用できます。（"."など）<br>
     */
	private $successUrl;

    /** 
     * 決済が失敗した後のECサイト側への戻り先URL<br>
     * 半角英数字<br>
     * 256文字以内<br>
     * 半角英数字のほかに、URLとして使用できる文字を使用できます。（"."など）<br>
     */
	private $errorUrl;

    /** 
     * 商品名<br>
     * 文字列<br>
     * 100桁以内<br>
     * 商品名を設定します。<br>
     */
	private $commodityName;

    /** 
     * 商品詳細<br>
     * 文字列<br>
     * 200桁以内<br>
     * 商品の説明を設定します。<br>
     * （任意指定）
     */
	private $commodityDescription;
    
    /**
     * 売上フラグ<br>
     * 半角文字<br>
     * 5 文字以内<br>
     * 売上フラグを指定します。（任意指定）<br>
     * "true"： 与信・売上<br>
     */
	private $withCapture = "true";

	/**
	 * ログ用文字列(マスク済み)<br>
	 */
	private $maskedLog;
    
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
	 * 売上フラグを取得する<br>
	 * @return 売上フラグ<br>
	 */
	public function getWithCapture() {
		return $this -> withCapture;
	}

	/**
	 * 売上フラグを設定する<br>
	 * @param  withCapture 売上フラグ<br>
	 */
	public function setWithCapture($withCapture) {
		$this -> withCapture = $withCapture;
	}

	/**
	 * 取引通貨種類を取得する<br>
	 * @return 取引通貨種類<br>
	 */
	public function getCurrency() {
		return $this -> currency;
	}

	/**
	 * 取引通貨種類を設定する<br>
	 * @param  currency 取引通貨種類<br>
	 */
	public function setCurrency($currency) {
		$this -> currency = $currency;
	}

	/**
	 * 決済が成功した後のECサイト側への戻り先のURLを取得する<br>
	 * @return 決済完了後に、店舗側へ遷移を戻すためのURL<br>
	 */
	public function getSuccessUrl() {
		return $this -> successUrl;
	}

	/**
	 * 決済が成功した後のECサイト側への戻り先のURLを設定する<br>
	 * @param  決済完了後に、店舗側へ遷移を戻すためのURL<br>
	 */
	public function setSuccessUrl($successUrl) {
		$this -> successUrl = $successUrl;
	}

	/**
	 * 決済が失敗した後のECサイト側への戻り先URLを取得する<br>
	 * @return 決済が失敗した後のECサイト側への戻り先URL<br>
	 */
	public function getErrorUrl() {
		return $this -> errorUrl;
	}

	/**
	 * 決済が失敗した後のECサイト側への戻り先URLを設定する<br>
	 * @param  決済が失敗した後のECサイト側への戻り先URL<br>
	 */
	public function setErrorUrl($errorUrl) {
		$this -> errorUrl = $errorUrl;
	}

	/**
	 *  商品名を取得する<br>
	 * @return 商品名<br>
	 */
	public function getCommodityName() {
		return $this -> commodityName;
	}

	/**
	 *  商品名を設定する<br>
	 * @param   商品名<br>
	 */
	public function setCommodityName($commodityName) {
		$this -> commodityName = $commodityName;
	}

	/**
	 *  商品詳細を取得する<br>
	 * @return 商品詳細<br>
	 */
	public function getCommodityDescription() {
		return $this -> commodityDescription;
	}

	/**
	 *  商品詳細を設定する<br>
	 * @param   商品詳細<br>
	 */
	public function setCommodityDescription($commodityDescription) {
		$this -> commodityDescription = $commodityDescription;
	}

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
	public function __toString() {
		return (string)$this -> maskedLog;
	}
}
?>
