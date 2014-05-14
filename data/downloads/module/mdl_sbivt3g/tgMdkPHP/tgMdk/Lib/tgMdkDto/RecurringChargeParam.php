<?php


/**
 * 会員課金情報のクラス
 *
 * @author Created automatically by DtoCreator
 *
 */
class RecurringChargeParam {

    /**
     * 課金グループID<br>
     * 半角英数字<br/>
     * 最大桁数：24<br/>
     * 継続課金対象の課金グループID<br/>
     * 事前に登録してあるIDのみ指定可能<br/>
     */
    private $groupId;

    /**
     * 課金開始日<br>
     * 半角数字<br/>
     * 最大桁数：8<br/>
     * 課金を開始する日付（YYYYMMD形式）<br/>
     * <br/>
     * ※初回課金される日付ではない<br/>
     * 　継続課金スケジュール日程の参照を開始する日付<br/>
     */
    private $startDate;

    /**
     * 課金終了日<br>
     * 半角数字<br/>
     * 最大桁数：8<br/>
     * 課金を終了する日付（YYYYMMD形式）<br/>
     * <br/>
     * ※最後に課金される日付ではない<br/>
     * 　継続課金スケジュール日程の参照を終了する最終日<br/>
     */
    private $endDate;

    /**
     * 次回課金終了フラグ<br>
     * 半角数字<br/>
     * 最大桁数：1<br/>
     * 次回課金をもって課金終了する場合に設定したい場合に利用するフラグ<br/>
     * <br/>
     * "1"：次回課金日で終了<br/>
     * "0"：課金終了日で終了 (未設定時は"0"として扱う)<br/>
     */
    private $finalCharge;

    /**
     * 都度／初回課金金額<br>
     * 半角数字<br/>
     * 最大桁数：8<br/>
     * 都度課金時又は継続課金時の初回の課金金額<br/>
     * 都度課金時は、次回課金時の金額を指定する。<br/>
     * 継続課金時は、課金グループにて指定している金額と別の金額で決済したい場合に設定する。<br/>
     */
    private $oneTimeAmount;

    /**
     * 継続課金金額<br>
     * 半角数字<br/>
     * 最大桁数：8<br/>
     * 継続課金時、2回目以降の決済金額<br/>
     * 課金グループにて指定している金額と別の金額で決済したい場合に使用する。<br/>
     * ※1回目の課金に継続課金金額は加算されないので注意<br/>
     */
    private $amount;



    /**
     * 課金グループIDを設定する<br>
     * @param groupId 課金グループID<br>
     */
    public function setGroupId($groupId) {
        $this->groupId = $groupId;
    }

    /**
     * 課金グループIDを取得する<br>
     * @return 課金グループID<br>
     */
    public function getGroupId() {
        return $this->groupId;
    }

    /**
     * 課金開始日を設定する<br>
     * @param startDate 課金開始日<br>
     */
    public function setStartDate($startDate) {
        $this->startDate = $startDate;
    }

    /**
     * 課金開始日を取得する<br>
     * @return 課金開始日<br>
     */
    public function getStartDate() {
        return $this->startDate;
    }

    /**
     * 課金終了日を設定する<br>
     * @param endDate 課金終了日<br>
     */
    public function setEndDate($endDate) {
        $this->endDate = $endDate;
    }

    /**
     * 課金終了日を取得する<br>
     * @return 課金終了日<br>
     */
    public function getEndDate() {
        return $this->endDate;
    }

    /**
     * 次回課金終了フラグを設定する<br>
     * @param finalCharge 次回課金終了フラグ<br>
     */
    public function setFinalCharge($finalCharge) {
        $this->finalCharge = $finalCharge;
    }

    /**
     * 次回課金終了フラグを取得する<br>
     * @return 次回課金終了フラグ<br>
     */
    public function getFinalCharge() {
        return $this->finalCharge;
    }

    /**
     * 都度／初回課金金額を設定する<br>
     * @param oneTimeAmount 都度／初回課金金額<br>
     */
    public function setOneTimeAmount($oneTimeAmount) {
        $this->oneTimeAmount = $oneTimeAmount;
    }

    /**
     * 都度／初回課金金額を取得する<br>
     * @return 都度／初回課金金額<br>
     */
    public function getOneTimeAmount() {
        return $this->oneTimeAmount;
    }

    /**
     * 継続課金金額を設定する<br>
     * @param amount 継続課金金額<br>
     */
    public function setAmount($amount) {
        $this->amount = $amount;
    }

    /**
     * 継続課金金額を取得する<br>
     * @return 継続課金金額<br>
     */
    public function getAmount() {
        return $this->amount;
    }



}
?>