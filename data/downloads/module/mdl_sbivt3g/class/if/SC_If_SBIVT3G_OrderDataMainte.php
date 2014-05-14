<?php
/**
 * SC_If_SBIVT3G_OrderDataMainte.php - SC_If_SBIVT3G_OrderDataMainte クラスを定義
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: SC_If_SBIVT3G_OrderDataMainte.php 166 2011-12-19 12:40:12Z hira $
 * @link        http://www.veritrans.co.jp/3gps
*/

/**
 *
 * 受注情報メンテナンス用データクラス
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
class SC_If_SBIVT3G_OrderDataMainte {

    // {{{ properties

    /** 現状受注データ */
    var $arrSrcOrder;

    /** 変更先受注データ */
    var $arrDstOrder;

    /** 現状内部決済ID */
    var $srcInnerPayment;

    /** 変更先内部決済ID */
    var $dstInnerPayment;

    /** 変更可能な決済ステータス配列 */
    var $arrEnableStatus;

    /** 3G決済ステータス配列 */
    var $arrPayStatus;

    /** カード支払方法配列 */
    var $arrPaymentType;

    /** カード支払回数配列 */
    var $arrPaymentCount;

    /** カード決済フラグ配列 */
    var $arrCardCaptures;

    /** カード再取引用番号配列 */
    var $arrReTradeOId;

    /** コンビニ店舗配列 */
    var $arrCvsShop;

    // }}}
    // {{{ functions

    /**
     * コンストラクタ
     *
     * @access public
     * @param array $arrOrder 変更先受注データ FormParamのgetDbArray()の値を想定
     * @param $arrSrc, $arrOrder) {
     * @return void
     */
    function SC_If_SBIVT3G_OrderDataMainte($arrOrder) {
        $this->__construct($arrOrder);
    }

    /**
     * コンストラクタ
     *
     * @access public
     * @param array $arrOrder 変更先受注データ FormParamのgetDbArray()の値を想定
     * @return void
     */
    function __construct($arrOrder) {
        $this->init($arrOrder);
    }

    /**
     * 初期化処理
     *
     * @access public
     * @param array $arrOrder 変更先受注データ FormParamのgetDbArray()の値を想定
     * @return void
     */
    function init($arrOrder) {

        // 引数を判定(getDbArray()の取得値か？)
        if (is_array($arrOrder['order_id']) == true
        || is_array($arrOrder['customer_id']) == true
        || is_array($arrOrder['payment_id']) == true
        || is_array($arrOrder['payment_total']) == true) {

            $arrTmp['order_id']      = 0;
            $arrTmp['customer_id']   = 0;
            $arrTmp['payment_id']    = 0;
            $arrTmp['payment_total'] = 0;
            // 設定し直す
            if (isset($arrOrder['order_id']['value']) == true) {
                $arrTmp['order_id'] = (int) $arrOrder['order_id']['value'];
            }
            if (isset($arrOrder['customer_id']['value']) == true) {
                $arrTmp['customer_id'] =
                    (int) $arrOrder['customer_id']['value'];
            }
            if (isset($arrOrder['payment_id']['value']) == true) {
                $arrTmp['payment_id'] =
                    (int) $arrOrder['payment_id']['value'];
            }
            if (isset($arrOrder['payment_total']['value']) == true) {
                $arrTmp['payment_total'] =
                    (int) $arrOrder['payment_total']['value'];
            }

            // 非必須項目
            if (isset($arrOrder['memo01']['value']) == true) {
                $arrTmp['memo01'] = $arrOrder['memo01']['value'];
            }
            if (isset($arrOrder['doDownPriceCapture']['value']) == true) {
                $arrTmp['doDownPriceCapture']
                    = $arrOrder['doDownPriceCapture']['value'];
            }
            if (isset($arrOrder['doDownPriceCancel']['value']) == true) {
                $arrTmp['doDownPriceCancel']
                    = $arrOrder['doDownPriceCancel']['value'];
            }

            $arrOrder = $arrTmp;
        }

        // プロパティ初期化
        $this->arrDstOrder = $arrOrder;

        // 現状の受注データを取得
        $objPurchase = new SC_Helper_Purchase_Ex();
        $this->arrSrcOrder = $objPurchase->getOrder(
            $this->arrDstOrder['order_id']);

        // 内部決済ID
        $srcPayment = $this->arrSrcOrder['payment_id'];
        $this->srcInnerPayment = GC_Utils_SBIVT3G::getInnerPayment($srcPayment);
        $dstPayment = $this->arrDstOrder['payment_id'];
        $this->dstInnerPayment = GC_Utils_SBIVT3G::getInnerPayment($dstPayment);

        // 変更可能な決済ステータス
        $srcPayStatus = $this->arrSrcOrder['memo01'];
        $objRule =& SC_Helper_SBIVT3G_PaymentRule::getSingletonInstance();
        $this->arrEnableStatus =
            $objRule->getEnableStatusByPaymentId($srcPayment, $srcPayStatus);

        // 設定ヘルパー参照
        $objSetting =& SC_Helper_SBIVT3G_Setting::getSingletonInstance();

        // 3G決済ステータス
        $this->arrPayStatus    = $objSetting->getPayStatus();
        // カード支払方法
        $this->arrPaymentType  = $objSetting->getPaymentType();
        // カード支払回数
        $this->arrPaymentCount = $objSetting->getPaymentCount();
        // 売上フラグ
        $this->arrCardCaptures = $objSetting->getCardCaptures();
        // カード再取引用番号
        $this->arrReTradeOId   = $this->getReTradeOId();
        // コンビニ店舗
        $this->arrCvsShop      = $objSetting->getCvsShop();
    }

    /**
     * 支払方法の変更判定
     *
     * @access public
     * @return boolean 
     */
    function isChangePayment() {
        if (strcmp($this->srcInnerPayment, $this->dstInnerPayment) != 0) {
            return true;
        }
        return false;
    }

    /**
     * 決済金額の変更判定
     *
     * @access public
     * @return boolean 
     */
    function isChangeTotal() {
        if (isset($this->arrSrcOrder['payment_total']) == true
        && isset($this->arrDstOrder['payment_total']) == true
        && $this->arrSrcOrder['payment_total']
            != $this->arrDstOrder['payment_total']) {
            return true;
        }
        return false;
    }

    /**
     * 決済ステータスの変更判定
     *
     * @access public
     * @return boolean 
     */
    function isChangePayStatus() {
        // 変更先が空白時は変更なしとする
        if (isset($this->arrSrcOrder['memo01']) == false
        || isset($this->arrDstOrder['memo01']) == false
        || strcmp($this->arrDstOrder['memo01'], '') == 0
        || $this->arrSrcOrder['memo01'] == $this->arrDstOrder['memo01']) {
            return false;
        }
        return true;
    }

    /**
     * 変更先クレジット決済判定
     *
     * @access public
     * @return boolean 
     */
    function isDstCredit() {
        if ($this->dstInnerPayment == MDL_SBIVT3G_INNER_ID_CREDIT) {
            return true;
        }
        return false;
    }

    /**
     * 現状がクレジット決済かの判定
     *
     * @access public
     * @return boolean 
     */
    function isSrcCredit() {
        if ($this->srcInnerPayment == MDL_SBIVT3G_INNER_ID_CREDIT) {
            return true;
        }
        return false;
    }

    /**
     * 変更先コンビニ決済判定
     *
     * @access public
     * @return boolean 
     */
    function isDstCvs() {
        if ($this->dstInnerPayment == MDL_SBIVT3G_INNER_ID_CVS) {
            return true;
        }
        return false;
    }

    /**
     * 現状がSBIベリトランス決済かの判定
     *
     * @access public
     * @return boolean 
     */
    function isSrcSbiPayment() {
        if (isset($this->srcInnerPayment) == true
        && strcmp($this->srcInnerPayment, '') != 0) {
            return true;
        }
        return false;
    }

    /**
     * 変更先がSBIベリトランス決済かの判定
     *
     * @access public
     * @return boolean 
     */
    function isDstSbiPayment() {
        if (isset($this->dstInnerPayment) == true
        && strcmp($this->dstInnerPayment, '') != 0) {
            return true;
        }
        return false;
    }

    /**
     * 減額売上請求可能かの判定
     *
     * @access public
     * @return boolean 
     */
    function canDownPriceCapture() {
        if ($this->isSrcCredit() == true
        && strcmp($this->getSrcPayStatus(), MDL_SBIVT3G_STATUS_AUTH) == 0
        && isset($this->arrSrcOrder['payment_total']) == true
        && isset($this->arrDstOrder['payment_total']) == true
        && $this->arrSrcOrder['payment_total']
            > $this->arrDstOrder['payment_total']
        && $this->arrDstOrder['payment_total'] > 0) {

            return true;
        }
        return false;
    }

    /**
     * 減額売上請求を実施するかの判定
     *
     * @access public
     * @return boolean 
     */
    function isDoDownPriceCapture() {
        if ($this->canDownPriceCapture() == true
        && strcmp($this->arrDstOrder['doDownPriceCapture'], '1') == 0) {
            return true;
        }
        return false;
    }

    /**
     * 部分返金可能かの判定
     *
     * @access public
     * @return boolean 
     */
    function canDownPriceCancel() {
        if ($this->isSrcCredit() == true
        && strcmp($this->getSrcPayStatus(), MDL_SBIVT3G_STATUS_CAPTURE) == 0
        && isset($this->arrSrcOrder['payment_total']) == true
        && isset($this->arrDstOrder['payment_total']) == true
        && $this->arrSrcOrder['payment_total']
            > $this->arrDstOrder['payment_total']) {

            return true;
        }
        return false;
    }

    /**
     * 部分返金を実施するかの判定
     *
     * @access public
     * @return boolean 
     */
    function isDoDownPriceCancel() {
        if ($this->canDownPriceCancel() == true
        && strcmp($this->arrDstOrder['doDownPriceCancel'], '1') == 0) {
            return true;
        }
        return false;
    }

    /**
     * 現状の支払方法の取得
     *
     * @access public
     * @return integer 
     */
    function getSrcPaymentId() {
        if (isset($this->arrSrcOrder['payment_id']) == true) {
            return $this->arrSrcOrder['payment_id'];
        }
        return null;
    }

    /**
     * 変更先の支払方法の取得
     *
     * @access public
     * @return integer 
     */
    function getDstPaymentId() {
        if (isset($this->arrDstOrder['payment_id']) == true) {
            return $this->arrDstOrder['payment_id'];
        }
        return null;
    }

    /**
     * 現状の決済ステータスの取得
     *
     * @access public
     * @return string 
     */
    function getSrcPayStatus() {
        if (isset($this->arrSrcOrder['memo01']) == true) {
            return $this->arrSrcOrder['memo01'];
        }
        return null;
    }

    /**
     * 変更先の決済ステータスの取得
     *
     * @access public
     * @return string 
     */
    function getDstPayStatus() {
        if (isset($this->arrDstOrder['memo01']) == true) {
            return $this->arrDstOrder['memo01'];
        }
        return null;
    }

    /**
     * 現状の決済金額の取得
     *
     * @access public
     * @return integer 
     */
    function getSrcPaymentTotal() {
        if (isset($this->arrSrcOrder['payment_total']) == true) {
            return $this->arrSrcOrder['payment_total'];
        }
        return null;
    }

    /**
     * 変更先の決済金額の取得
     *
     * @access public
     * @return integer 
     */
    function getDstPaymentTotal() {
        if (isset($this->arrDstOrder['payment_total']) == true) {
            return $this->arrDstOrder['payment_total'];
        }
        return null;
    }

    /**
     * 現状の決済ステータス名を取得
     *
     * @access public
     * @return array 決済ステータス配列
     */
    function getSrcPayStatusName() {
        if (isset($this->arrPayStatus[$this->arrSrcOrder['memo01']]) == true) {
            return $this->arrPayStatus[$this->arrSrcOrder['memo01']];
        }
        return '未決済';
    }

    /**
     * 対象顧客IDの再取引カード用番号配列を生成
     *
     * @access public
     * @return array 再取引カード用番号配列
     */
    function getReTradeOId() {
        // 取得
        $customerId = $this->arrDstOrder['customer_id'];
        $arrTmp = GC_Utils_SBIVT3G::getReTradeCard($customerId);

        // パースする
        $arrReTradeOId = array();
        foreach ($arrTmp as $key => $rec) {
            $arrReTradeOId[$key] = sprintf(
                '注文番号:%d - カード番号:%s',
                $rec['orderId'],
                $rec['maskedNo']);
        }

        // もし現状受注がクレジットであれば
        if ($this->isSrcCredit() == true) {
            // 自身の取引IDを追加する
            $arrAdd[$this->arrSrcOrder['memo04']] =
                'この受注のカード番号で再取引';
            $arrReTradeOId = array_merge($arrAdd, $arrReTradeOId);
        }
        return $arrReTradeOId;
    }
}
?>
