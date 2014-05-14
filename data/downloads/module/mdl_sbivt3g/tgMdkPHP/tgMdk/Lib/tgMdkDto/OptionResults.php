<?php


/**
 * PayNowID結果オプションのクラス
 *
 * @author Created automatically by DtoCreator
 *
 */
class OptionResults {

    /**
     * オプション<br>
     * 文字列配列<br/>
     */
    private $options;



    /**
     * オプションを設定する<br>
     * @param options オプション<br>
     */
    public function setOptions($options) {
        $this->options = $options;
    }

    /**
     * オプションを取得する<br>
     * @return オプション<br>
     */
    public function getOptions() {
        return $this->options;
    }



}
?>