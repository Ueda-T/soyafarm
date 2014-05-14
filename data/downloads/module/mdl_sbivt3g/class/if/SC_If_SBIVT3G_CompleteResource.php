<?php
/**
 * SC_If_SBIVT3G_CompleteResource.php - SC_If_SBIVT3G_CompleteResource クラスを定義
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: SC_If_SBIVT3G_CompleteResource.php 47 2011-08-04 14:10:00Z hira $
 * @link        http://www.veritrans.co.jp/3gps
*/

/**
 *
 * 注文完了用リソースクラス
 *
 * @category    Veritrans
 * @package     Lib
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version    Release: @package_version@
 * @link        http://www.veritrans.co.jp/3gps
 * @access  public
 * @author  K.Hiranuma
 */
class SC_If_SBIVT3G_CompleteResource {

    // {{{ properties

    /** 完了画面用記述リソース */
    var $arrCompDispRC;

    /** 注文完了メール用(dtb_order.memo02)記述リソース */
    var $arrCompMailRC;

    // }}}
    // {{{ functions

    /**
     * コンストラクタ
     *
     * @access public
     * @return void
     */
    function SC_If_SBIVT3G_CompleteResource() {
        $this->__counstruct();
    }

    /**
     * コンストラクタ
     *
     * @access public
     * @return void
     */
    function __counstruct() {
        $this->init();
    }

    /**
     * 初期化処理
     *
     * @access public
     * @return void
     */
    function init() {
        // プロパティ初期化
        $this->arrCompDispRC = array();
        $this->arrCompMailRC = array();
    }

    /**
     * 注文完了時の記述リソース(画面orメール)のタイトルを設定
     *
     * @access protected
     * @param array $arrRC $arrCompDispRC or $arrCompMailRC
     * @param string $title データのタイトル
     * @return void
     */
    function setCompRCTitle(&$arrRC, $title) {
        $arrRC['title'] = array(
            'name' => $title,
            'value' => true,
        );
    }

    /**
     * 注文完了画面記述用のリソースタイトルを設定
     *
     * @access protected
     * @param string $name データのタイトル
     * @return void
     */
    function setCompDispRCTitle($title) {
        $this->setCompRCTitle($this->arrCompDispRC, $title);
    }

    /**
     * 注文完了メール記述用のリソースタイトルを設定
     *
     * @access protected
     * @param string $title データのタイトル
     * @return void
     */
    function setCompMailRCTitle($title) {
        $this->setCompRCTitle($this->arrCompMailRC, $title);
    }

    /**
     * リソースタイトルを画面・メールの双方に設定
     *
     * @access protected
     * @param string $title 表示するデータのタイトル
     * @return void
     */
    function setCompBothRCTitle($title) {
        $this->setCompRCTitle($this->arrCompDispRC, $title);
        $this->setCompRCTitle($this->arrCompMailRC, $title);
    }

    /**
     * 注文完了時の記述リソース(画面orメール)を設定
     *
     * @access protected
     * @param array $arrRC $arrCompDispRC or $arrCompMailRC
     * @param string $name データのラベル(空白可)
     * @param string $value データの値
     * @return void
     */
    function setCompRC(&$arrRC, $name, $value) {
        $idx = count($arrRC);
        $idx = ($idx == 0)? 1 : $idx;
        $arrRC[$idx] = array(
            'name' => $name,
            'value' => $value,
        );
    }

    /**
     * 注文完了画面記述用のリソースを設定
     *
     * @access protected
     * @param string $name データのラベル(空白可)
     * @param string $value データの値
     * @return void
     */
    function setCompDispRC($name, $value) {
        $this->setCompRC($this->arrCompDispRC, $name, $value);
    }

    /**
     * 注文完了メール記述用のリソースを設定
     *
     * @access protected
     * @param string $name データのラベル(空白可)
     * @param string $value データの値
     * @return void
     */
    function setCompMailRC($name, $value) {
        $this->setCompRC($this->arrCompMailRC, $name, $value);
    }

    /**
     * リソースを画面・メールの双方に設定
     *
     * @access protected
     * @param string $name データのラベル(空白可)
     * @param string $value データの値
     * @return void
     */
    function setCompBothRC($name, $value) {
        $this->setCompRC($this->arrCompDispRC, $name, $value);
        $this->setCompRC($this->arrCompMailRC, $name, $value);
    }

    /**
     * 注文完了画面記述用のリソースをセッションへ
     *
     * @access protected
     * @return void
     */
    function pushCompDispRC() {
        // セッションに格納
        $_SESSION[MDL_SBIVT3G_COMPLETE_RC] = $this->arrCompDispRC;
    }

    /**
     * 注文完了メール記述用のリソースを返す(memo02へ渡す時に利用)
     *
     * @access protected
     * @return string
     */
    function getCompMailRC() {
        // シリアライズして返す
        return serialize( $this->arrCompMailRC);
    }
}
?>
