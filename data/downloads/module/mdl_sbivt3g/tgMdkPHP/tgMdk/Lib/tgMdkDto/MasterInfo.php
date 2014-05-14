<?php
/**
 *
 * @author Veritrans, Inc.
 */
class MasterInfo {

    /**
     * マスタ名<br>
     */
    private $name;

    /**
     * マスタ情報<br>
     */
    private $masters;

    /**
     * コンストラクタ。
     */
    public function __construct() {
        $this->masters = new MdkBaseDto();
    }

    /**
     * マスタ名を取得する<br>
     *
     * @return マスタ名<br>
     */
    public function getName() {
        return $this->name;
    }

    /**
     * マスタ名を設定する<br>
     *
     * @param マスタ名<br>
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**    
     * マスタ情報を取得する<br>
     *
     * @return マスタ情報<br>
     */
    public function getMasters() {
        return $this->masters;
    }

    /**
     * マスタ情報を設定する<br>
     *
     * @param マスタ情報<br>
     */
    public function setMasters($masters) {
        $this->masters = $masters;
    }

}
?>