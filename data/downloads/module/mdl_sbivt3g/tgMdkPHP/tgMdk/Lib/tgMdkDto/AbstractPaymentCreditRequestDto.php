<?php

/**
 * カード決済、MPIのみ継承する親クラス
 * AbstractPaymentCreditRequestDto
 * @author Veritrans, Inc.
 *
 */
class AbstractPaymentCreditRequestDto extends AbstractPaymentRequestDto {

    /**
     * カードIDを設定する<br>
     * @param cardId カードID<br>
     */
    public function setCardId($cardId) {
        $this->existCardParam();
        $this->payNowIdParam->getAccountParam()->getCardParam()->setCardId($cardId);
    }

    /**
     * カードIDを取得する<br>
     * @return カードID<br>
     */
    public function getCardId() {
        $this->existCardParam();
        return $this->payNowIdParam->getAccountParam()->getCardParam()->getCardId();
    }

    /**
     * 標準カードフラグを設定する<br>
     * @param defaultCard 標準カードフラグ<br>
     */
    public function setDefaultCard($defaultCard) {
        $this->existCardParam();
        $this->payNowIdParam->getAccountParam()->getCardParam()->setDefaultCard($defaultCard);
    }

    /**
     * 標準カードフラグを取得する<br>
     * @return 標準カードフラグ<br>
     */
    public function getDefaultCard() {
        $this->existCardParam();
        return $this->payNowIdParam->getAccountParam()->getCardParam()->getDefaultCard();
    }
    
    /**
     * 課金グループIDを設定する<br>
     * @param groupId 課金グループID<br>
     */
    public function setGroupId($groupId) {
        $this->existRecurringChargeParam();
        $this->payNowIdParam->getAccountParam()->getRecurringChargeParam()->setGroupId($groupId);
    }

    /**
     * 課金グループIDを取得する<br>
     * @return 課金グループID<br>
     */
    public function getGroupId() {
        $this->existRecurringChargeParam();
        return $this->payNowIdParam->getAccountParam()->getRecurringChargeParam()->getGroupId();
    }
    
    /**
     * 課金開始日を設定する<br>
     * @param startDate 課金開始日<br>
     */
    public function setStartDate($startDate) {
        $this->existRecurringChargeParam();
        $this->payNowIdParam->getAccountParam()->getRecurringChargeParam()->setStartDate($startDate);
    }
    
    /**
     * 課金開始日を取得する<br>
     * @return 課金開始日<br>
     */
    public function getStartDate() {
        $this->existRecurringChargeParam();
        return $this->payNowIdParam->getAccountParam()->getRecurringChargeParam()->getStartDate();
    }
    
    /**
     * 課金終了日を設定する<br>
     * @param endDate 課金終了日<br>
     */
    public function setEndDate($endDate) {
        $this->existRecurringChargeParam();
        $this->payNowIdParam->getAccountParam()->getRecurringChargeParam()->setEndDate($endDate);
    }
    
    /**
     * 課金終了日を取得する<br>
     * @return 課金終了日<br>
     */
    public function getEndDate() {
        $this->existRecurringChargeParam();
        return $this->payNowIdParam->getAccountParam()->getRecurringChargeParam()->getEndDate();
    }
    
    /**
     * 都度/初回課金金額を設定する<br>
     * @param oneTimeAmount 都度/初回課金金額<br>
     */
    public function setOneTimeAmount($oneTimeAmount) {
        $this->existRecurringChargeParam();
        $this->payNowIdParam->getAccountParam()->getRecurringChargeParam()->setOneTimeAmount($oneTimeAmount);
    }
    
    /**
     * 都度/初回課金金額を取得する<br>
     * @return 都度/初回課金金額<br>
     */
    public function getOneTimeAmount() {
        $this->existRecurringChargeParam();
        return $this->payNowIdParam->getAccountParam()->getRecurringChargeParam()->getOneTimeAmount();
    }

    /**
     * 継続課金金額を設定する<br>
     * @param recurringAmount 継続課金金額<br>
     */
    public function setRecurringAmount($recurringAmount) {
        $this->existRecurringChargeParam();
        $this->payNowIdParam->getAccountParam()->getRecurringChargeParam()->setAmount($recurringAmount);
    }
    
    /**
     * 継続課金金額を取得する<br>
     * @return 継続課金金額<br>
     */
    public function getRecurringAmount() {
        $this->existRecurringChargeParam();
        return $this->payNowIdParam->getAccountParam()->getRecurringChargeParam()->getAmount();
    }
    
    /**
     * 洗替実施フラグを設定する<br>
     * @param updater 洗替実施フラグ<br>
     */
    public function setUpdater($updater) {
        $this->existCardParam();
        $this->payNowIdParam->getAccountParam()->getCardParam()->setUpdater($updater);
    }

    /**
     * 洗替実施フラグを取得する<br>
     * @return 洗替実施フラグ<br>
     */
    public function getUpdater() {
        $this->existCardParam();
        return $this->payNowIdParam->getAccountParam()->getCardParam()->getUpdater();
    }
    
    /**
     * タンキングフラグを設定する<br>
     * @param tanking タンキングフラグ<br>
     */
    public function setTanking($tanking) {
        $this->existPayNowIdParam();
        $this->payNowIdParam->setTanking($tanking);
    }
    
    /**
     * タンキングフラグを取得する<br>
     * @return タンキングフラグ<br>
     */
    public function getTanking() {
        $this->existPayNowIdParam();
        return $this->payNowIdParam->getTanking();
    }
}

?>
