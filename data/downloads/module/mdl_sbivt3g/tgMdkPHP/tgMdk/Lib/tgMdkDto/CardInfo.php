<?php


/**
 * カード情報のクラス
 *
 * @author Created automatically by DtoCreator
 *
 */
class CardInfo {

    /**
     * カードID<br>
     * 半角数字<br/>
     * 最大桁数：24<br/>
     */
    private $cardId;

    /**
     * マスクカード番号<br>
     * 半角英数字<br/>
     * 最大桁数：19<br/>
     * カード番号の先頭６桁と下４桁<br/>
     * 桁数はマスク前の桁数を維持<br/>
     */
    private $cardNumber;

    /**
     * 有効期限<br>
     * 半角英数字<br/>
     * 最大桁数：5<br/>
     * MM/YY形式<br/>
     * マスクはしていない<br/>
     */
    private $cardExpire;

    /**
     * 標準カードフラグ<br>
     * 半角英数字<br/>
     * 最大桁数：1<br/>
     * "1"：決済時、カードを明示的に指定しない場合に使用されるカードである。<br/>
     * "0"：決済時、カードを明示的に指定しない場合に使用されるカードでない。<br/>
     */
    private $defaultCard;



    /**
     * カードIDを設定する<br>
     * @param cardId カードID<br>
     */
    public function setCardId($cardId) {
        $this->cardId = $cardId;
    }

    /**
     * カードIDを取得する<br>
     * @return カードID<br>
     */
    public function getCardId() {
        return $this->cardId;
    }

    /**
     * マスクカード番号を設定する<br>
     * @param cardNumber マスクカード番号<br>
     */
    public function setCardNumber($cardNumber) {
        $this->cardNumber = $cardNumber;
    }

    /**
     * マスクカード番号を取得する<br>
     * @return マスクカード番号<br>
     */
    public function getCardNumber() {
        return $this->cardNumber;
    }

    /**
     * 有効期限を設定する<br>
     * @param cardExpire 有効期限<br>
     */
    public function setCardExpire($cardExpire) {
        $this->cardExpire = $cardExpire;
    }

    /**
     * 有効期限を取得する<br>
     * @return 有効期限<br>
     */
    public function getCardExpire() {
        return $this->cardExpire;
    }

    /**
     * 標準カードフラグを設定する<br>
     * @param defaultCard 標準カードフラグ<br>
     */
    public function setDefaultCard($defaultCard) {
        $this->defaultCard = $defaultCard;
    }

    /**
     * 標準カードフラグを取得する<br>
     * @return 標準カードフラグ<br>
     */
    public function getDefaultCard() {
        return $this->defaultCard;
    }



}
?>