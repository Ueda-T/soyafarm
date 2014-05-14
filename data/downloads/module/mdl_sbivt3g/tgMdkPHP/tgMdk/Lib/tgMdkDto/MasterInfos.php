<?php
/**
 * マスタ情報リストのクラス<br>
 *
 * @author Veritrans, Inc.
 */
class MasterInfos {

    /**
     * マスタ情報<br>
     */
    private $masterInfo;

    /**
     * コンストラクタ。
     */
    public function __construct() {
        $this->masterInfo = Array(new MdkBaseDto());
    }

    /**
     * マスタ情報を取得する<br>
     * @return マスタ情報<br>
     */
    public function getMasterInfo() {
        return $this->masterInfo;
    }

    /**
     * マスタ情報を設定する<br>
     * @param  masterInfo マスタ情報<br>
     */
    public function setMasterInfo($masterInfo) {
        $this->masterInfo = $masterInfo;
    }

}
?>