<?php

/**
 * PayNowIDリクエストDTO親クラス<br>
 *
 * @author Veritrans, Inc.
 */
class AbstractPayNowIdRequestDto extends MdkBaseDto {
    
    /** サービスタイプ */
    private $SERVICE_TYPE;
    /** サービスコマンド */
    private $SERVICE_COMMAND;
    /** ログ用文字列 */
    private  $maskedLog;
    /** PayNowIDオブジェクト */
    protected $payNowIdParam;
    
    /**
     * コンストラクタ<br>
     * @param $serviceType サービスタイプ<br>
     * @param $serviceCommand サービスコマンド<br>
     */
    function __construct($serviceType, $serviceCommand) {
        $this->SERVICE_TYPE = $serviceType;
        $this->SERVICE_COMMAND = $serviceCommand;
        $this->payNowIdParam = new PayNowIdParam();
    }
    
    /**
     * 決済サービスタイプを取得する<br>
     * @return 決済サービスタイプ<br>
     */
    public function getServiceType() {
        return $this->SERVICE_TYPE;
    }
    
    /**
     * 決済サービスコマンドを取得する<br>
     * @return 決済サービスコマンド<br>
     */
    public function getServiceCommand() {
        return $this->SERVICE_COMMAND;
    }
    
    /**
     * ログ用文字列(マスク済み)を設定する<br>
     * @param  maskedLog ログ用文字列(マスク済み)<br>
     */
    public function _setMaskedLog($maskedLog) {
        $this->maskedLog = $maskedLog;
    }
    
    /**
     * ログ用文字列(マスク済み)を取得する<br>
     * @return ログ用文字列(マスク済み)<br>
     */
    public function __toString() {
        return (string)$this->maskedLog;
    }
    
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
     * PayNowIDを取得する。<br>
     * @return PayNowID<br>
     */
    public function getPayNowId() {
        $this->existAccountParam();
        return $this->payNowIdParam->getAccountParam()->getPayNowId();
    }
    
    /**
     * PayNowIDを設定する。<br>
     * @param $payNowId PayNowID<br>
     */
    public function setPayNowId($payNowId) {
        $this->existAccountParam();
        $this->payNowIdParam->getAccountParam()->setPayNowId($payNowId);
    }
    
    /**
     * リクエスト項目を取得する。<br>
     * @return リクエスト項目<br>
     */
    public function getTransData() {
        $this->existAccountParam();
        return $this->payNowIdParam->getAccountParam()->getTransData();
    }
    
    /**
     * リクエスト項目を設定する。<br>
     * @param $transData リクエスト項目<br>
     */
    public function setTransData($transData) {
        $this->existAccountParam();
        $this->payNowIdParam->getAccountParam()->setTransData($transData);
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
     * 退会年月日を設定する<br>
     * @param deleteDate 退会年月日<br>
     */
    public function setDeleteDate($deleteDate) {
        $this->existAccountBasicParam();
        $this->payNowIdParam->getAccountParam()->getAccountBasicParam()->setDeleteDate($deleteDate);
    }

    /**
     * 退会年月日を取得する<br>
     * @return 退会年月日<br>
     */
    public function getDeleteDate() {
        $this->existAccountBasicParam();
        return $this->payNowIdParam->getAccountParam()->getAccountBasicParam()->getDeleteDate();
    }

    /**
     * 強制退会フラグを設定する<br>
     * @param forceDeleteDate 強制退会フラグ<br>
     */
    public function setForceDeleteDate($forceDeleteDate) {
        $this->existAccountBasicParam();
        $this->payNowIdParam->getAccountParam()->getAccountBasicParam()->setForceDeleteDate($forceDeleteDate);
    }

    /**
     * 強制退会フラグを取得する<br>
     * @return 強制退会フラグ<br>
     */
    public function getForceDeleteDate() {
        $this->existAccountBasicParam();
        return $this->payNowIdParam->getAccountParam()->getAccountBasicParam()->getForceDeleteDate();
    }
    
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
     * 標準カードIDを設定する<br>
     * @param defaultCardId 標準カードID<br>
     */
    public function setDefaultCardId($defaultCardId) {
        $this->existCardParam();
        $this->payNowIdParam->getAccountParam()->getCardParam()->setDefaultCardId($defaultCardId);
    }

    /**
     * 標準カードIDを取得する<br>
     * @return 標準カードID<br>
     */
    public function getDefaultCardId() {
        $this->existCardParam();
        return $this->payNowIdParam->getAccountParam()->getCardParam()->getDefaultCardId();
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
     * カード番号を設定する<br>
     * @param cardNumber カード番号<br>
     */
    public function setCardNumber($cardNumber) {
        $this->existCardParam();
        $this->payNowIdParam->getAccountParam()->getCardParam()->setCardNumber($cardNumber);
    }

    /**
     * カード番号を取得する<br>
     * @return カード番号<br>
     */
    public function getCardNumber() {
        $this->existCardParam();
        return $this->payNowIdParam->getAccountParam()->getCardParam()->getCardNumber();
    }

    /**
     * 有効期限を設定する<br>
     * @param cardExpire 有効期限<br>
     */
    public function setCardExpire($cardExpire) {
        $this->existCardParam();
        $this->payNowIdParam->getAccountParam()->getCardParam()->setCardExpire($cardExpire);
    }

    /**
     * 有効期限を取得する<br>
     * @return 有効期限<br>
     */
    public function getCardExpire() {
        $this->existCardParam();
        return $this->payNowIdParam->getAccountParam()->getCardParam()->getCardExpire();
    }
    
    /**
     * 課金グループIDを設定する<br>
     * @param groupId 課金グループID<br>
     */
    public function setGroupId($groupId) {
        if ($this->SERVICE_TYPE === PayNowIdConstants::SERVICE_TYPE_ACCOUNT
                || $this->SERVICE_TYPE === PayNowIdConstants::SERVICE_TYPE_RECURRING) {
            $this->existRecurringChargeParam();
            $this->payNowIdParam->getAccountParam()->getRecurringChargeParam()->setGroupId($groupId);
        } else if ($this->SERVICE_TYPE === PayNowIdConstants::SERVICE_TYPE_CHARGE) {
            $this->existChargeParam();
            $this->payNowIdParam->getChargeParam()->setGroupId($groupId);
        }
    }

    /**
     * 課金グループIDを取得する<br>
     * @return 課金グループID<br>
     */
    public function getGroupId() {
        if ($this->SERVICE_TYPE === PayNowIdConstants::SERVICE_TYPE_ACCOUNT
                || $this->SERVICE_TYPE === PayNowIdConstants::SERVICE_TYPE_RECURRING) {
            $this->existRecurringChargeParam();
            return $this->payNowIdParam->getAccountParam()->getRecurringChargeParam()->getGroupId();
        } else if ($this->SERVICE_TYPE === PayNowIdConstants::SERVICE_TYPE_CHARGE) {
            $this->existChargeParam();
            return $this->payNowIdParam->getChargeParam()->getGroupId();
        }
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
     * 次回課金終了フラグを設定する<br>
     * @param finalCharge 次回課金終了フラグ<br>
     */
    public function setFinalCharge($finalCharge) {
        $this->existRecurringChargeParam();
        $this->payNowIdParam->getAccountParam()->getRecurringChargeParam()->setFinalCharge($finalCharge);
    }
    
    /**
     * 次回課金終了フラグを取得する<br>
     * @return 次回課金終了フラグ<br>
     */
    public function getFinalCharge() {
        $this->existRecurringChargeParam();
        return $this->payNowIdParam->getAccountParam()->getRecurringChargeParam()->getFinalCharge();
    }
    
    /**
     * 都度/初回課金金額を設定する<br>
     * @param oneTimeAmount 都度/初回課金金額<br>
     */
    public function setOneTimeAmount($oneTimeAmount) {
        if (PayNowIdConstants::SERVICE_TYPE_ACCOUNT === $this->SERVICE_TYPE
                || PayNowIdConstants::SERVICE_TYPE_RECURRING === $this->SERVICE_TYPE) {
            // 会員課金情報オブジェクトに設定
            $this->existRecurringChargeParam();
            $this->payNowIdParam->getAccountParam()->getRecurringChargeParam()->setOneTimeAmount($oneTimeAmount);
        } else if (PayNowIdConstants::SERVICE_TYPE_CHARGE === $this->SERVICE_TYPE) {
            // 課金グループ情報オブジェクトに設定
            $this->existChargeParam();
            $this->payNowIdParam->getChargeParam()->setOneTimeAmount($oneTimeAmount);
        }
    }
    
    /**
     * 都度/初回課金金額を取得する<br>
     * @return 都度/初回課金金額<br>
     */
    public function getOneTimeAmount() {
        if (PayNowIdConstants::SERVICE_TYPE_ACCOUNT === $this->SERVICE_TYPE
                || PayNowIdConstants::SERVICE_TYPE_RECURRING === $this->SERVICE_TYPE) {
            // 会員課金情報オブジェクトから取得
            $this->existRecurringChargeParam();
            $oneTimeAmount = $this->payNowIdParam->getAccountParam()->getRecurringChargeParam()->getOneTimeAmount();
        } else if (PayNowIdConstants::SERVICE_TYPE_CHARGE === $this->SERVICE_TYPE) {
            // 課金グループ情報オブジェクトから取得
            $this->existChargeParam();
            $oneTimeAmount = $this->payNowIdParam->getChargeParam()->getOneTimeAmount();
        }
        return $oneTimeAmount;
    }

    /**
     * 継続課金金額を設定する<br>
     * @param amount 継続課金金額<br>
     */
    public function setAmount($amount) {
        if (PayNowIdConstants::SERVICE_TYPE_ACCOUNT === $this->SERVICE_TYPE
                || PayNowIdConstants::SERVICE_TYPE_RECURRING === $this->SERVICE_TYPE) {
            // 会員課金情報オブジェクトに設定
            $this->existRecurringChargeParam();
            $this->payNowIdParam->getAccountParam()->getRecurringChargeParam()->setAmount($amount);
        } else if (PayNowIdConstants::SERVICE_TYPE_CHARGE === $this->SERVICE_TYPE) {
            // 課金グループ情報オブジェクトに設定
            $this->existChargeParam();
            $this->payNowIdParam->getChargeParam()->setAmount($amount);
        }
    }
    
    /**
     * 継続課金金額を取得する<br>
     * @return 継続課金金額<br>
     */
    public function getAmount() {
        if (PayNowIdConstants::SERVICE_TYPE_ACCOUNT === $this->SERVICE_TYPE
                || PayNowIdConstants::SERVICE_TYPE_RECURRING === $this->SERVICE_TYPE) {
            // 会員課金情報オブジェクトから取得
            $this->existRecurringChargeParam();
            $amount = $this->payNowIdParam->getAccountParam()->getRecurringChargeParam()->getAmount();
        } else if (PayNowIdConstants::SERVICE_TYPE_CHARGE === $this->SERVICE_TYPE) {
            // 課金グループ情報オブジェクトから取得
            $this->existChargeParam();
            $amount = $this->payNowIdParam->getChargeParam()->getAmount();
        }
        return $amount;
    }
    
    /**
     * 課金グループ名を設定する<br>
     * @param groupName 課金グループ名<br>
     */
    public function setGroupName($groupName) {
        $this->existChargeParam();
        $this->payNowIdParam->getChargeParam()->setGroupName($groupName);
    }
    
    /**
     * 課金グループ名を取得する<br>
     * @return 課金グループ名<br>
     */
    public function getGroupName() {
        $this->existChargeParam();
        return $this->payNowIdParam->getChargeParam()->getGroupName();
    }
    
    /**
     * 課金スタイル区分を設定する<br>
     * @param type 課金スタイル区分<br>
     */
    public function setType($type) {
        $this->existChargeParam();
        $this->payNowIdParam->getChargeParam()->setType($type);
    }
    
    /**
     * 課金スタイル区分を取得する<br>
     * @return 課金スタイル区分<br>
     */
    public function getType() {
        $this->existChargeParam();
        return $this->payNowIdParam->getChargeParam()->getType();
    }
    
    /**
     * 課金日取扱区分を設定する<br>
     * @param chargeType 課金日取扱区分<br>
     */
    public function setChargeType($chargeType) {
        $this->existChargeParam();
        $this->payNowIdParam->getChargeParam()->setChargeType($chargeType);
    }
    
    /**
     * 課金日取扱区分を取得する<br>
     * @return 課金日取扱区分<br>
     */
    public function getChargeType() {
        $this->existChargeParam();
        return $this->payNowIdParam->getChargeParam()->getChargeType();
    }
    
    /**
     * 継続課金スケジュールを設定する<br>
     * @param schedule 継続課金スケジュール<br>
     */
    public function setSchedule($schedule) {
        $this->existChargeParam();
        $this->payNowIdParam->getChargeParam()->setSchedule($schedule);
    }
    
    /**
     * 継続課金スケジュールを取得する<br>
     * @return 継続課金スケジュール<br>
     */
    public function getSchedule() {
        $this->existChargeParam();
        return $this->payNowIdParam->getChargeParam()->getSchedule();
    }
    
    /**
     * タンキングフラグを設定する<br>
     * @param tanking タンキングフラグ<br>
     */
    public function setTanking($tanking) {
        $this->payNowIdParam->setTanking($tanking);
    }
    
    /**
     * タンキングフラグを取得する<br>
     * @return タンキングフラグ<br>
     */
    public function getTanking() {
        return $this->payNowIdParam->getTanking();
    }
    
    /**
     * 取引メモ１を設定する<br>
     * @param memo1 取引メモ１<br>
     */
    public function setMemo1($memo1) {
        $this->payNowIdParam->setMemo1($memo1);
    }
    
    /**
     * 取引メモ１を取得する<br>
     * @return 取引メモ１<br>
     */
    public function getMemo1() {
        return $this->payNowIdParam->getMemo1();
    }
    
    /**
     * 取引メモ２を設定する<br>
     * @param memo2 取引メモ２<br>
     */
    public function setMemo2($memo2) {
        $this->payNowIdParam->setMemo2($memo2);
    }
    
    /**
     * 取引メモ２を取得する<br>
     * @return 取引メモ２<br>
     */
    public function getMemo2() {
        return $this->payNowIdParam->getMemo2();
    }
    
    /**
     * 取引メモ３を設定する<br>
     * @param memo3 取引メモ３<br>
     */
    public function setMemo3($memo3) {
        $this->payNowIdParam->setMemo3($memo3);
    }
    
    /**
     * 取引メモ３を取得する<br>
     * @return 取引メモ３<br>
     */
    public function getMemo3() {
        return $this->payNowIdParam->getMemo3();
    }
    
    /**
     * キー情報を設定する<br>
     * @param freeKey キー情報<br>
     */
    public function setFreeKey($freeKey) {
        $this->payNowIdParam->setFreeKey($freeKey);
    }
    
    /**
     * キー情報を取得する<br>
     * @return キー情報<br>
     */
    public function getFreeKey() {
        return $this->payNowIdParam->getFreeKey();
    }

    /**
     * AccountParamがPayNowIdParamに設定されているか判定する。<br>
     * 設定されていない場合はインスタンスを生成し、PayNowIdParamに設定する。<br>
     */
    private function existAccountParam() {
        if (is_null($this->payNowIdParam->getAccountParam())) {
            $accountParam = new AccountParam();
            $this->payNowIdParam->setAccountParam($accountParam);
        }
    }
    
    /**
     * AccountBasicParamがPayNowIdParamに設定されているか判定する。<br>
     * 設定されていない場合はインスタンスを生成し、PayNowIdParamに設定する。<br>
     */
    private function existAccountBasicParam() {
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
    private function existCardParam() {
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
    private function existRecurringChargeParam() {
        $this->existAccountParam();
        if (is_null($this->payNowIdParam->getAccountParam()->getRecurringChargeParam())) {
            $recurringChargeParam = new RecurringChargeParam();
            $this->payNowIdParam->getAccountParam()->setRecurringChargeParam($recurringChargeParam);
        }
    }
    
    /**
     * ChargeParamがPayNowIdParamに設定されているか判定する。<br>
     * 設定されていない場合はインスタンスを生成し、PayNowIdParamに設定する。<br>
     */
    private function existChargeParam() {
        if (is_null($this->payNowIdParam->getChargeParam())) {
            $chargeParam = new ChargeParam();
            $this->payNowIdParam->setChargeParam($chargeParam);
        }
    }
    
}

?>
