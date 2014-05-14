<?php


/**
 * カード情報のクラス
 *
 * @author Created automatically by DtoCreator
 *
 */
class CardParam {

    /**
     * カードID<br>
     * 半角英数字<br/>
     * 最大桁数：24<br/>
     * PayNowIDで管理しているカードを決済で利用する場合に指定する。<br/>
     */
    private $cardId;

    /**
     * 標準カードフラグ<br>
     * 半角数字<br/>
     * 最大桁数：1<br/>
     * 今後決済時、カード情報をリクエストに含めなかった際に、<br/>
     * このカード情報を使用するか否かを設定する。<br/>
     * <br/>
     * "1"：今回のカードを今後決済時使用するカードに設定する。（未設定時は"1"として扱う）<br/>
     * "0"：この課金でのみ利用する　<br/>
     */
    private $defaultCard;

    /**
     * 標準カードID<br>
     * 半角英数字<br/>
     * 最大桁数：24<br/>
     * "Member"：未設定<br/>
     * "ChargeMaster"：未設定<br/>
     * "ReccaringMaster"：未設定<br/>
     * "Card"：未設定<br/>
     * カード削除リクエスト時のみ設定可能。<br/>
     * <br/>
     * 標準カードとして設定しているカード情報を削除する際に、<br/>
     * 新しく標準カードとして扱うカードを指定可能。<br/>
     */
    private $defaultCardId;

    /**
     * 洗替実施フラグ<br>
     * 半角数字<br/>
     * 最大桁数：1<br/>
     * このカードを洗替対象外にする際に設定する。<br/>
     * ※洗替を実施するマーチャントの場合のみ設定可能。<br/>
     * <br/>
     * "1"：洗替する。（未設定時は"1"として扱う）<br/>
     * "0"：洗替しない。<br/>
     */
    private $updater;

    /**
     * カード番号<br>
     * 半角数字<br/>
     * 最大桁数：19<br/>
     * クレジットカード番号を指定します。<br/>
     * 例） クレジットカード番号は19桁まで処理が可能。<br/>
     * （ハイフンを含んでも含まなくても同様に処理が可能）<br/>
     * <br/>
     * サービスコマンド「update」且つサービスオプションタイプ「card」の場合を除き、<br/>
     * カードID指定時は入力不可<br/>
     * また、決済コマンド時は入力不可<br/>
     */
    private $cardNumber;

    /**
     * 有効期限<br>
     * 半角数字<br/>
     * 最大桁数：5<br/>
     * クレジットカードの有効期限を指定します。<br/>
     * MM/YY （月 + "/" + 年）の形式<br/>
     * 例） "11/09"<br/>
     * <br/>
     * サービスコマンド「update」且つサービスオプションタイプ「card」の場合を除き、<br/>
     * カードID指定時は入力不可<br/>
     * また、決済コマンド時は入力不可<br/>
     */
    private $cardExpire;



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

    /**
     * 標準カードIDを設定する<br>
     * @param defaultCardId 標準カードID<br>
     */
    public function setDefaultCardId($defaultCardId) {
        $this->defaultCardId = $defaultCardId;
    }

    /**
     * 標準カードIDを取得する<br>
     * @return 標準カードID<br>
     */
    public function getDefaultCardId() {
        return $this->defaultCardId;
    }

    /**
     * 洗替実施フラグを設定する<br>
     * @param updater 洗替実施フラグ<br>
     */
    public function setUpdater($updater) {
        $this->updater = $updater;
    }

    /**
     * 洗替実施フラグを取得する<br>
     * @return 洗替実施フラグ<br>
     */
    public function getUpdater() {
        return $this->updater;
    }

    /**
     * カード番号を設定する<br>
     * @param cardNumber カード番号<br>
     */
    public function setCardNumber($cardNumber) {
        $this->cardNumber = $cardNumber;
    }

    /**
     * カード番号を取得する<br>
     * @return カード番号<br>
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



}
?>