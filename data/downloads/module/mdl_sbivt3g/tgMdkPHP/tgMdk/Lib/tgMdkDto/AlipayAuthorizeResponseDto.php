<?php
/**
 * 決済サービスタイプ：Alipay、コマンド名：与信の応答Dtoクラス<br>
 *
 * @author Veritrans, Inc.
 *
 */
class AlipayAuthorizeResponseDto extends MdkBaseDto {

    /**
     * 決済サービスタイプ<br>
     * 半角英数字<br/>
     * 最大桁数：10<br/>
     * 決済サービスの区分を返却します。<br/>
     * "alipay"： Alipay決済<br/>
     */
	private $serviceType;

    /**
     * 処理結果コード<br>
     * 半角英数字<br/>
     * 最大桁数：32<br/>
     * 処理結果に対するステータスを返却します。<br/>
     * "success"：正常終了<br/>
     * "failure"：異常終了<br/>
     */
	private $mstatus;

    /**
     * 詳細結果コード<br>
     * 半角英数字<br/>
     * 最大桁数：16<br/>
     * 処理結果を詳細に表すコードを返却します。<br/>
     * 4桁ずつ4つのブロックで構成され、各ブロックでサービス毎の処理結果を表します。<br/>
     */
	private $vResultCode;

    /**
     * エラーメッセージ<br>
     * 文字列<br/>
     * 最大桁数：300<br/>
     * 処理結果に対するメッセージを返却します。<br/>
     */
	private $merrMsg;

    /**
     * 取引毎に付くID<br>
     * 半角英数字<br/>
     * 最大桁数：100<br/>
     */
	private $custTxn;

    /**
     * 電文ID<br>
     * 半角英数字<br/>
     * 最大桁数：100<br/>
     */
	private $marchTxn;

    /**
     * 取引ID<br>
     * 半角英数字<br/>
     * 最大桁数：100<br/>
     * 画面表示要求時に指定された取引IDを返却します。<br/>
     */
	private $orderId;
    
    /**
     * MDKバージョン<br>
     * 半角英数字<br/>
     * 最大桁数：5<br/>
     * 電文のバージョン番号を返却します。<br/>
     */
	private $txnVersion;

    /**
     * AlipayへリダイレクトするためのHTML文
     */
	private $entryForm;

    /**
     * 結果XML(マスク済み)<br>
     * 半角英数字<br>
     */
	private $resultXml;
        
    /**
     * PayNowIDオブジェクト<br>
     * オブジェクト<br>
     * PayNowID用項目を格納するオブジェクト<br>
     */
     private $payNowIdResponse;

	/**
	 * 決済サービスタイプを取得する<br>
	 * @return 決済サービスタイプ<br>
	 */
	public function getServiceType() {
		return $this -> serviceType;
	}

	/**
	 * 決済サービスタイプを設定する<br>
	 * @param  serviceType 決済サービスタイプ<br>
	 */
	public function setServiceType($serviceType) {
		$this -> serviceType = $serviceType;
	}

	/**
	 * 処理結果コードを取得する<br>
	 * @return 処理結果コード<br>
	 */
	public function getMstatus() {
		return $this -> mstatus;
	}

	/**
	 * 処理結果コードを設定する<br>
	 * @param  mstatus 処理結果コード<br>
	 */
	public function setMstatus($mstatus) {
		$this -> mstatus = $mstatus;
	}

	/**
	 * 詳細結果コードを取得する<br>
	 * @return 詳細結果コード<br>
	 */
	public function getVResultCode() {
		return $this -> vResultCode;
	}

	/**
	 * 詳細結果コードを設定する<br>
	 * @param  vResultCode 詳細結果コード<br>
	 */
	public function setVResultCode($vResultCode) {
		$this -> vResultCode = $vResultCode;
	}

	/**
	 * エラーメッセージを取得する<br>
	 * @return エラーメッセージ<br>
	 */
	public function getMerrMsg() {
		return $this -> merrMsg;
	}

	/**
	 * エラーメッセージを設定する<br>
	 * @param  merrMsg エラーメッセージ<br>
	 */
	public function setMerrMsg($merrMsg) {
		$this -> merrMsg = $merrMsg;
	}

	/**
	 * 取引毎に付くIDを取得する<br>
	 * @return 取引毎に付くID<br>
	 */
	public function getCustTxn() {
		return $this -> custTxn;
	}

	/**
	 * 取引毎に付くIDを設定する<br>
	 * @param  custTxn 取引毎に付くID<br>
	 */
	public function setCustTxn($custTxn) {
		$this -> custTxn = $custTxn;
	}
    
	/**
	 * 電文IDを取得する<br>
	 * @return 電文ID<br>
	 */
	public function getMarchTxn() {
		return $this -> marchTxn;
	}

	/**
	 * 電文IDを設定する<br>
	 * @param  marchTxn 電文ID<br>
	 */
	public function setMarchTxn($marchTxn) {
		$this -> marchTxn = $marchTxn;
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
	 * MDK バージョンを取得する<br>
	 * @return MDK バージョン<br>
	 */
	public function getTxnVersion() {
		return $this -> txnVersion;
	}

	/**
	 * MDK バージョンを設定する<br>
	 * @param  txnVersion MDK バージョン<br>
	 */
	public function setTxnVersion($txnVersion) {
		$this -> txnVersion = $txnVersion;
	}

	/**
	 * AlipayへリダイレクトするためのHTML文を取得する<br>
	 * @return AlipayへリダイレクトするためのHTML文<br>
	 */
	public function getEntryForm() {
		return $this -> entryForm;
	}

	/**
	 * AlipayへリダイレクトするためのHTML文を設定する<br>
	 * @param  AlipayへリダイレクトするためのHTML文<br>
	 */
	public function setEntryForm($entryForm) {
		$this -> entryForm = $entryForm;
	}

	/**
	 * 結果XML(マスク済み)を設定する<br>
	 * @param  resultXml 結果XML(マスク済み)<br>
	 */
	public function _setResultXml($resultXml) {
		$this -> resultXml = $resultXml;
	}

	/**
	 * 結果XML(マスク済み)を取得する<br>
	 * @return 結果XML(マスク済み)<br>
	 */
	public function _toString() {
		return (string)$this -> resultXml;
	}

        /**
         * PayNowIDオブジェクトを設定する<br>
         * @param PayNowIDオブジェクト<br>
         */
        public function setPayNowIdResponse($payNowIdResponse) {
            $this -> payNowIdResponse = $payNowIdResponse;
        }
        
        /**
         * PayNowIDオブジェクトを取得する<br>
         * @return PayNowIDオブジェクト<br>
         */
        public function getPayNowIdResponse() {
            return $this -> payNowIdResponse;
        }

}
?>
