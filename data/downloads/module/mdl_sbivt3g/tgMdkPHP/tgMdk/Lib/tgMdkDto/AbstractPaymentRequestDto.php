<?php
/**
 * 決済の親クラス
 *
 * @author Veritrans, Inc.
 */
class AbstractPaymentRequestDto extends MdkBaseDto {

    /** PayNowIDオブジェクト */
    protected $payNowIdParam;
    
    /**
     * 会員IDを取得する。<br>
     * @return 会員ID<br>
     */
    public function getAccountId() {
        $this->existAccountParam();
        return $this->payNowIdParam->getAccountParam()->getAccountId();
    }
    
    /**
     * 会員IDを設定する。<br>
     * @param $accountId 会員ID<br>
     */
    public function setAccountId($accountId) {
        $this->existAccountParam();
        $this->payNowIdParam->getAccountParam()->setAccountId($accountId);
    }
    
    /**
     * 入会年月日を設定する<br>
     * @param createDate 入会年月日<br>
     */
    public function setCreateDate($createDate) {
        $this->existAccountBasicParam();
        $this->payNowIdParam->getAccountParam()->getAccountBasicParam()->setCreateDate($createDate);
    }

    /**
     * 入会年月日を取得する<br>
     * @return 入会年月日<br>
     */
    public function getCreateDate() {
        $this->existAccountBasicParam();
        return $this->payNowIdParam->getAccountParam()->getAccountBasicParam()->getCreateDate();
    }

    /**
     * 取引メモ１を設定する<br>
     * @param memo1 取引メモ１<br>
     */
    public function setMemo1($memo1) {
        $this->existPayNowIdParam();
        $this->payNowIdParam->setMemo1($memo1);
    }
    
    /**
     * 取引メモ１を取得する<br>
     * @return 取引メモ１<br>
     */
    public function getMemo1() {
        $this->existPayNowIdParam();
        return $this->payNowIdParam->getMemo1();
    }
    
    /**
     * 取引メモ２を設定する<br>
     * @param memo2 取引メモ２<br>
     */
    public function setMemo2($memo2) {
        $this->existPayNowIdParam();
        $this->payNowIdParam->setMemo2($memo2);
    }
    
    /**
     * 取引メモ２を取得する<br>
     * @return 取引メモ２<br>
     */
    public function getMemo2() {
        $this->existPayNowIdParam();
        return $this->payNowIdParam->getMemo2();
    }
    
    /**
     * 取引メモ３を設定する<br>
     * @param memo3 取引メモ３<br>
     */
    public function setMemo3($memo3) {
        $this->existPayNowIdParam();
        $this->payNowIdParam->setMemo3($memo3);
    }
    
    /**
     * 取引メモ３を取得する<br>
     * @return 取引メモ３<br>
     */
    public function getMemo3() {
        $this->existPayNowIdParam();
        return $this->payNowIdParam->getMemo3();
    }
    
    /**
     * キー情報を設定する<br>
     * @param freeKey キー情報<br>
     */
    public function setFreeKey($freeKey) {
        $this->existPayNowIdParam();
        $this->payNowIdParam->setFreeKey($freeKey);
    }
    
    /**
     * キー情報を取得する<br>
     * @return キー情報<br>
     */
    public function getFreeKey() {
        $this->existPayNowIdParam();
        return $this->payNowIdParam->getFreeKey();
    }
    
    /**
     * PayNowIdPramが設定されているか判定する。<br>
     * 設定されていない場合はインスタンスを生成する。<br>
     */
    protected function existPayNowIdParam() {
        if (is_null($this->payNowIdParam)) {
            $this->payNowIdParam = new PayNowIdParam();
        }
    }
    
    
    /**
     * AccountParamがPayNowIdParamに設定されているか判定する。<br>
     * 設定されていない場合はインスタンスを生成し、PayNowIdParamに設定する。<br>
     */
    protected function existAccountParam() {
        $this->existPayNowIdParam();
        if (is_null($this->payNowIdParam->getAccountParam())) {
            $accountParam = new AccountParam();
            $this->payNowIdParam->setAccountParam($accountParam);
        }
    }
    
    /**
     * AccountBasicParamがPayNowIdParamに設定されているか判定する。<br>
     * 設定されていない場合はインスタンスを生成し、PayNowIdParamに設定する。<br>
     */
    protected function existAccountBasicParam() {
        $this->existAccountParam();
        if (is_null($this->payNowIdParam->getAccountParam()->getAccountBasicParam())) {
            $accountBasicParam = new AccountBasicParam();
            $this->payNowIdParam->getAccountParam()->setAccountBasicParam($accountBasicParam);
        }
    }
    
    /**
     * CardParamがPayNowIdParamに設定されているか判定する。<br>
     * 設定されていない場合はインスタンスを生成し、PayNowIdParamに設定する。<br>
     */
    protected function existCardParam() {
        $this->existAccountParam();
        if (is_null($this->payNowIdParam->getAccountParam()->getCardParam())) {
            $cardParam = new CardParam();
            $this->payNowIdParam->getAccountParam()->setCardParam($cardParam);
        }
    }
    
    /**
     * RecurringChargeParamがPayNowIdParamに設定されているか判定する。<br>
     * 設定されていない場合はインスタンスを生成し、PayNowIdParamに設定する。<br>
     */
    protected function existRecurringChargeParam() {
        $this->existAccountParam();
        if (is_null($this->payNowIdParam->getAccountParam()->getRecurringChargeParam())) {
            $recurringChargeParam = new RecurringChargeParam();
            $this->payNowIdParam->getAccountParam()->setRecurringChargeParam($recurringChargeParam);
        }
    }
}

?>
