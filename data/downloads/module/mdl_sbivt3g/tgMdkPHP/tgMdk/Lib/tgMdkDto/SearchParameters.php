<?php
/**
 * 検索条件:検索条件パラメータ群を保持するクラス
 *
 * @author Veritrans, Inc.
 */
class SearchParameters {

    /**
     * 共通検索パラメータ<br>
     */
    private $common;

    /**
     * カード検索パラメータ<br>
     */
    private $card;

    /**
     * 電子マネー検索パラメータ<br>
     */
    private $em;

    /**
     * コンビニ検索パラメータ<br>
     */
    private $cvs;

    /**
     * 銀行決済検索パラメータ<br>
     */
    private $bank;

    /**
     * ペイパル検索パラメータ<br>
     */
    private $paypal;

    /**
     * セゾン検索パラメータ<br>
     */
    private $saison;

    /**
     * UPOPパラメータ<br>
     */
    private $upop;

    /**
     * 3Dセキュアカード連携検索パラメータ<br>
     */
    private $mpi;

    /**
     * Alipayパラメータ<br>
     */
    private $alipay;

    /**
     * キャリア検索パラメータ<br>
     */
    private $carrier;

    /**
     * ショッピングクレジット検索パラメータ<br>
     */
    private $oricosc;

    /**
     * 金融機関マスタ検索パラメータ<br>
     */
    private $bankFinancialInstInfo;

    /**
     * 共通検索パラメータを取得する<br>
     *
     * @return 共通検索パラメータ
     */
    public function getCommon() {
        return $this->common;
    }

    /**
     * 共通検索パラメータを設定する<br>
     *
     * @param common 共通検索パラメータ
     */
    public function setCommon($common) {
        $this->common = $common;
    }

    /**
     * カード検索パラメータを取得する<br>
     *
     * @return カード検索パラメータ
     */
    public function getCard() {
        return $this->card;
    }

    /**
     * カード検索パラメータを設定する<br>
     *
     * @param card カード検索パラメータ
     */
    public function setCard($card) {
        $this->card = $card;
    }

    /**
     * 電子マネー検索パラメータを取得する<br>
     *
     * @return 電子マネー検索パラメータ
     */
    public function getEm() {
        return $this->em;
    }

    /**
     * 電子マネー検索パラメータを設定する<br>
     *
     * @param em 電子マネー検索パラメータ
     */
    public function setEm($em) {
        $this->em = $em;
    }

    /**
     * コンビニ検索パラメータを取得する<br>
     *
     * @return コンビニ検索パラメータ
     */
    public function getCvs() {
        return $this->cvs;
    }

    /**
     * コンビニ検索パラメータを設定する<br>
     *
     * @param cvs コンビニ検索パラメータ
     */
    public function setCvs($cvs) {
        $this->cvs = $cvs;
    }

    /**
     * ペイジー検索パラメータを取得する<br>
     *
     * @return ペイジー検索パラメータ
     */
    public function getBank() {
        return $this->bank;
    }

    /**
     * 銀行決済検索パラメータを設定する<br>
     *
     * @param payeasy 銀行決済検索パラメータ
     */
    public function setBank($bank) {
        $this->bank = $bank;
    }

    /**
     * ペイパル検索パラメータを取得する<br>
     *
     * @return ペイパル検索パラメータ
     */
    public function getPaypal() {
        return $this->paypal;
    }

    /**
     * ペイパル検索パラメータを設定する<br>
     *
     * @param paypal ペイパル検索パラメータ
     */
    public function setPaypal($paypal) {
        $this->paypal = $paypal;
    }

    /**
     * セゾン検索パラメータを取得する<br>
     *
     * @return セゾン検索パラメータ
     */
    public function getSaison() {
        return $this->saison;
    }

    /**
     * セゾン検索パラメータを設定する<br>
     *
     * @param saison セゾン検索パラメータ
     */
    public function setSaison($saison) {
        $this->saison = $saison;
    }

    /**
     * upop銀聯検索パラメータを取得する<br>
     *
     * @return upop銀聯検索パラメータ
     */
    public function getUpop() {
        return $this->upop;
    }

    /**
     * upop銀聯検索パラメータを設定する<br>
     *
     * @param upop銀聯検索パラメータ
     */
    public function setUpop($upop) {
        $this->upop = $upop;
    }

    /**
     * 3Dセキュアカード連携検索パラメータを取得する<br>
     *
     * @return 3Dセキュアカード連携検索パラメータ
     */
    public function getMpi() {
        return $this->mpi;
    }

    /**
     * 3Dセキュアカード連携検索パラメータを設定する<br>
     *
     * @param mpi 3Dセキュアカード連携検索パラメータ
     */
    public function setMpi($mpi) {
        $this->mpi = $mpi;
    }

    /**
     * alipay検索パラメータを取得する<br>
     *
     * @return alipay検索パラメータ
     */
    public function getAlipay() {
        return $this->alipay;
    }

    /**
     * alipay検索パラメータを設定する<br>
     *
     * @param alipay検索パラメータ
     */
    public function setAlipay($alipay) {
        $this->alipay = $alipay;
    }

    /**
     * キャリア検索パラメータを取得する<br>
     *
     * @return キャリア検索パラメータ
     */
    public function getCarrier() {
        return $this->carrier;
    }

    /**
     * キャリア検索パラメータを設定する<br>
     *
     * @param carrier キャリア検索パラメータ
     */
    public function setCarrier($carrier) {
        $this->carrier = $carrier;
    }

    /**
     * ショッピングクレジット検索パラメータを取得する<br>
     *
     * @return oricosc検索パラメータ
     */
    public function getOricosc() {
        return $this->oricosc;
    }

    /**
     * ショッピングクレジット検索パラメータを設定する<br>
     *
     * @param oricosc検索パラメータ
     */
    public function setOricosc($oricosc) {
        $this->oricosc = $oricosc;
    }
    
    /**
     * 金融機関マスタ検索パラメータを取得する<br>
     *
     * @return 金融機関マスタ検索パラメータ
     */
    public function getBankFinancialInstInfo() {
        return $this->bankFinancialInstInfo;
    }

    /**
     * 金融機関マスタ検索パラメータを設定する<br>
     *
     * @param mpi 金融機関マスタ検索パラメータ
     */
    public function setBankFinancialInstInfo($bankFinancialInstInfo) {
        $this->bankFinancialInstInfo = $bankFinancialInstInfo;
    }
}
?>