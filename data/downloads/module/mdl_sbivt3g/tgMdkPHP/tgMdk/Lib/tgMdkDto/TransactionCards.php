<?php
/**
 * 検索結果:Saisonトランザクションカード情報電文リストのクラス
 *
 * @author Veritrans, Inc.
 */
class TransactionCards {

    /**
     * Saisonトランザクションカード情報<br>
     */
    private $transactionCard;

    /**
     * コンストラクタ。
     */
    public function __construct() {
        $this->transactionCard = Array(new MdkBaseDto());
    }

    /**
     * Saisonトランザクションカード情報を取得する<br>
     *
     * @return Saisonトランザクションカード情報<br>
     */
    public function getTransactionCard() {
        return $this->transactionCard;
    }

    /**
     * Saisonトランザクションカード情報を設定する<br>
     *
     * @param transactionCard Saisonトランザクションカード情報<br>
     */
    public function setTransactionCard($transactionCard) {
        $this->transactionCard = $transactionCard;
    }

}
?>
