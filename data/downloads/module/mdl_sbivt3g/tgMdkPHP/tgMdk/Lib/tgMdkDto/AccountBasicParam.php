<?php


/**
 * 会員基本情報のクラス
 *
 * @author Created automatically by DtoCreator
 *
 */
class AccountBasicParam {

    /**
     * 入会年月日<br>
     * 半角数字<br/>
     * 最大桁数：8<br/>
     * YYYYMMDD形式<br/>
     */
    private $createDate;

    /**
     * 退会年月日<br>
     * 半角数字<br/>
     * 最大桁数：8<br/>
     * YYYYMMDD形式<br/>
     */
    private $deleteDate;

    /**
     * 強制退会フラグ<br>
     * 半角数字<br/>
     * 最大桁数：1<br/>
     * 継続課金中であっても強制的に退会するか否か指定するフラグ<br/>
     * "1"：強制的に終了する。（以降課金は継続課金は発生しない。）<br/>
     * "0"：継続課金中の場合、退会できない。（未設定時は"0"として扱う）<br/>
     */
    private $forceDeleteDate;



    /**
     * 入会年月日を設定する<br>
     * @param createDate 入会年月日<br>
     */
    public function setCreateDate($createDate) {
        $this->createDate = $createDate;
    }

    /**
     * 入会年月日を取得する<br>
     * @return 入会年月日<br>
     */
    public function getCreateDate() {
        return $this->createDate;
    }

    /**
     * 退会年月日を設定する<br>
     * @param deleteDate 退会年月日<br>
     */
    public function setDeleteDate($deleteDate) {
        $this->deleteDate = $deleteDate;
    }

    /**
     * 退会年月日を取得する<br>
     * @return 退会年月日<br>
     */
    public function getDeleteDate() {
        return $this->deleteDate;
    }

    /**
     * 強制退会フラグを設定する<br>
     * @param forceDeleteDate 強制退会フラグ<br>
     */
    public function setForceDeleteDate($forceDeleteDate) {
        $this->forceDeleteDate = $forceDeleteDate;
    }

    /**
     * 強制退会フラグを取得する<br>
     * @return 強制退会フラグ<br>
     */
    public function getForceDeleteDate() {
        return $this->forceDeleteDate;
    }



}
?>