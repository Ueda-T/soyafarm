<?php
/**
 * SC_Helper_SBIVT3G_PaymentRule.php - SC_Helper_SBIVT3G_PaymentRule クラスを定義
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: SC_Helper_SBIVT3G_PaymentRule.php 193 2013-07-31 01:24:57Z kaji $
 * @link        http://www.veritrans.co.jp/3gps
*/

/**
 * SBIVT3Gモジュール決済ルールヘルパークラス
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
class SC_Helper_SBIVT3G_PaymentRule {

    // {{{ properties

    /** 店舗別設定ヘルパー */
    var $objSetting;

    /** 支払方法変更ルール配列 */
    var $arrPayStatusRule;

    /** 取消・返金ルール配列 */
    var $arrDeleteRule;

    /** 再決済ルール配列 */
    var $arrRenewRule;

    // }}}
    // {{{ functions

    /**
     * コンストラクタ
     *
     * @access public
     * @return void
     */
    function SC_Helper_SBIVT3G_PaymentRule() {
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
     * シングルトンパターン
     *
     * @access public
     * @return SC_Helper_SBIVT3G_PaymentRule シングルトン・インスタンス
     */
    function getSingletonInstance() {
        $myName = '_SC_Helper_SBIVT3G_PaymentRule_instance';
        if (isset($GLOBALS[$myName]) == false ||
            get_class($GLOBALS[$myName]) != "SC_Helper_SBIVT3G_PaymentRule") {
            $GLOBALS[$myName] =& new SC_Helper_SBIVT3G_PaymentRule();
        }
        return $GLOBALS[$myName];
    }

    /**
     * 初期化処理
     *
     * @access public
     * @return void
     */
    function init() {
        // 店舗別設定ヘルパー
        $this->objSetting = SC_Helper_SBIVT3G_Setting::getSingletonInstance();
        // 変更可能決済ステータス配列
        $this->arrPayStatusRule = $this->genPayStatusRule();
        // 取消・返金ルール配列
        $this->arrDeleteRule    = $this->genDeleteRule();
        // 再決済ルール配列
        $this->arrRenewRule     = $this->genRenewRule();
    }

    /**
     * 決済ステータスの変更範囲を定義
     *
     * @access protected
     * @return array 変更可能決済ステータス配列
     */
    function genPayStatusRule() {

        $auth = MDL_SBIVT3G_STATUS_AUTH;    // 与信
        $capt = MDL_SBIVT3G_STATUS_CAPTURE; // 売上
        $cncl = MDL_SBIVT3G_STATUS_CANCEL;  // 取消
        $req  = MDL_SBIVT3G_STATUS_REQUEST; // 申込
        $depo = MDL_SBIVT3G_STATUS_DEPOSIT; // 入金
        $refn = MDL_SBIVT3G_STATUS_REFUND;  // 返金

        $arrPayStatusRule = array(
            // クレジット
            MDL_SBIVT3G_INNER_ID_CREDIT => array(
                $auth => array($capt=>'cardCapture', $cncl=>'cardCancel'),
                $capt => array($cncl=>'cardCancel'),
            ),
            // コンビニ
            MDL_SBIVT3G_INNER_ID_CVS => array(
                $req  => array($cncl=>'cvsCancel'),
            ),
            // ATM
            MDL_SBIVT3G_INNER_ID_PAYEASY_ATM => array(),
            // ネットバンキング
            MDL_SBIVT3G_INNER_ID_PAYEASY_NET => array(),
            // モバイルEdy
            MDL_SBIVT3G_INNER_ID_EDY_MOBILE_MAIL => array(
                $depo => array($refn=>'edyRefund'),
            ),
            // サイバーEdy
            MDL_SBIVT3G_INNER_ID_EDY_PC_APP => array(
                $depo => array($refn=>'edyRefund'),
            ),
            // Waon(モバイル版)
            MDL_SBIVT3G_INNER_ID_WAON_MOBILE_APP => array(
                $depo => array($refn=>'waonRefund'),
            ),
            // Waon(PC版)
            MDL_SBIVT3G_INNER_ID_WAON_PC_APP => array(
                $depo => array($refn=>'waonRefund'),
            ),
            // モバイルSuica(メール型)
            MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_MAIL => array(
                $req  => array($cncl=>'suicaCancel'),
                $depo => array($refn=>'suicaRefund'),
            ),
            // モバイルSuica(アプリ型)
            MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_APP => array(
                $req  => array($cncl=>'suicaCancel'),
                $depo => array($refn=>'suicaRefund'),
            ),
            // SuicaIS(メール型)
            MDL_SBIVT3G_INNER_ID_SUICA_PC_MAIL => array(
                $req  => array($cncl=>'suicaCancel'),
                $depo => array($refn=>'suicaRefund'),
            ),
            // SuicaIS(アプリ型)
            MDL_SBIVT3G_INNER_ID_SUICA_PC_APP => array(
                $req  => array($cncl=>'suicaCancel'),
                $depo => array($refn=>'suicaRefund'),
            ),
            // 銀聯ネット決済
            MDL_SBIVT3G_INNER_ID_CUP => array(
                $req  => array($cncl=>'cupCancel'),
                $depo => array($refn=>'cupRefund'),
            ),
            // PayPal決済
            MDL_SBIVT3G_INNER_ID_PAYPAL => array(
                $auth => array($capt=>'paypalCapture', $cncl=>'paypalCancel'),
                $capt => array($refn=>'paypalRefund'),
            ),
            // キャリア決済
            MDL_SBIVT3G_INNER_ID_CARRIER => array(
                $auth => array($capt=>'carrierCapture', $cncl=>'carrierCancel'),
                $capt => array($cncl=>'carrierCancel'),
            ),
        );
        return $arrPayStatusRule;
    }

    /**
     * 各決済の取消・返金ルールを定義
     *
     * @access protected
     * @return array 削除可能ルール配列
     */
    function genDeleteRule() {

        $auth = MDL_SBIVT3G_STATUS_AUTH;    // 与信
        $capt = MDL_SBIVT3G_STATUS_CAPTURE; // 売上
        $cncl = MDL_SBIVT3G_STATUS_CANCEL;  // 取消
        $req  = MDL_SBIVT3G_STATUS_REQUEST; // 申込
        $depo = MDL_SBIVT3G_STATUS_DEPOSIT; // 入金
        $refn = MDL_SBIVT3G_STATUS_REFUND;  // 返金
        $expr = MDL_SBIVT3G_STATUS_EXPIRED; // 期限切れ

        $arrDeleteRule = array(
            // クレジット
            MDL_SBIVT3G_INNER_ID_CREDIT => array(
                $auth => $cncl,
                $capt => $cncl,
                $cncl => '', // 無条件変更
                $expr => '', // 無条件変更
            ),
            // コンビニ
            MDL_SBIVT3G_INNER_ID_CVS => array(
                $req  => $cncl,
                $cncl => '', // 無条件変更
                $expr => '', // 無条件変更
            ),
            // ATM
            MDL_SBIVT3G_INNER_ID_PAYEASY_ATM => array(
                $expr => '', // 無条件変更
            ),
            // ネットバンキング
            MDL_SBIVT3G_INNER_ID_PAYEASY_NET => array(
                $expr => '', // 無条件変更
            ),
            // モバイルEdy
            MDL_SBIVT3G_INNER_ID_EDY_MOBILE_MAIL => array(
                $depo => $refn,
                $refn => '', // 無条件変更
                $expr => '', // 無条件変更
            ),
            // サイバーEdy
            MDL_SBIVT3G_INNER_ID_EDY_PC_APP => array(
                $depo => $refn,
                $refn => '', // 無条件変更
                $expr => '', // 無条件変更
            ),
            // Waon(モバイル版)
            MDL_SBIVT3G_INNER_ID_WAON_MOBILE_APP => array(
                $depo => $refn,
                $refn => '', // 無条件変更
                $expr => '', // 無条件変更
            ),
            // Waon(PC版)
            MDL_SBIVT3G_INNER_ID_WAON_PC_APP => array(
                $depo => $refn,
                $refn => '', // 無条件変更
                $expr => '', // 無条件変更
            ),
            // モバイルSuica(メール型)
            MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_MAIL => array(
                $req  => $cncl,
                $depo => $refn,
                $cncl => '', // 無条件変更
                $refn => '', // 無条件変更
                $expr => '', // 無条件変更
            ),
            // モバイルSuica(アプリ型)
            MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_APP => array(
                $req  => $cncl,
                $depo => $refn,
                $cncl => '', // 無条件変更
                $refn => '', // 無条件変更
                $expr => '', // 無条件変更
            ),
            // SuicaIS(メール型)
            MDL_SBIVT3G_INNER_ID_SUICA_PC_MAIL => array(
                $req  => $cncl,
                $depo => $refn,
                $cncl => '', // 無条件変更
                $refn => '', // 無条件変更
                $expr => '', // 無条件変更
            ),
            // SuicaIS(アプリ型)
            MDL_SBIVT3G_INNER_ID_SUICA_PC_APP => array(
                $req  => $cncl,
                $depo => $refn,
                $cncl => '', // 無条件変更
                $refn => '', // 無条件変更
                $expr => '', // 無条件変更
            ),
            // 銀聯ネット決済
            MDL_SBIVT3G_INNER_ID_CUP => array(
                $req  => $cncl,
                $depo => $refn,
                $cncl => '', // 無条件変更
                $refn => '', // 無条件変更
                $expr => '', // 無条件変更
            ),
            // PayPal決済
            MDL_SBIVT3G_INNER_ID_PAYPAL => array(
                $auth => $cncl,
                $depo => $refn,
                $cncl => '', // 無条件変更
                $refn => '', // 無条件変更
                $expr => '', // 無条件変更
            ),
            // キャリア決済
            MDL_SBIVT3G_INNER_ID_CARRIER => array(
                $auth => $cncl,
                $capt => $cncl,
                $cncl => '', // 無条件変更
                $expr => '', // 無条件変更
            ),
            // その他(SBIVT以外の決済)
            '' => array(
                '' => '',
            ),
        );
        return $arrDeleteRule;
    }

    /**
     * 各決済の再決済ルールを定義
     *
     * @access protected
     * @return array 再決済可能ルール配列
     */
    function genRenewRule() {

        $arrRenewRule = array(
            // クレジット
            MDL_SBIVT3G_INNER_ID_CREDIT => 'cardRenewExecute',
            // コンビニ
            MDL_SBIVT3G_INNER_ID_CVS => 'cvsRenewExecute',
            // ATM
            MDL_SBIVT3G_INNER_ID_PAYEASY_ATM => 'atmRenewExecute',
            // その他(SBIVT以外の決済)
            '' => '',
        );
        return $arrRenewRule;
    }

    /**
     * 対応する決済ステータス選択肢を取得(支払方法ID版)
     *
     * @access public
     * @param string $payment   支払い方法ID
     * @param string $payStatus 決済ステータス
     * @return array 有効決済ステータス配列
     */
    function getEnableStatusByPaymentId($payment, $payStatus) {

        $innerPayment = GC_Utils_SBIVT3G::getInnerPayment($payment);
        return $this->getEnableStatus($innerPayment, $payStatus);
    }

    /**
     * 対応する決済ステータス選択肢を取得
     *
     * @access public
     * @param string $innerPayment 内部決済ID
     * @param string $payStatus    決済ステータス
     * @return array 有効決済ステータス配列
     */
    function getEnableStatus($innerPayment, $payStatus) {

        $arrReturn = array();

        $arrStatus = $this->objSetting->getPayStatus();
        
        if (isset($this->arrPayStatusRule[$innerPayment][$payStatus]) == true) {
            // 存在すれば配列を生成
            $arrRule = $this->arrPayStatusRule[$innerPayment][$payStatus];
            foreach ($arrRule as $status => $method) {
                // status => 文字列に生成
                $arrReturn[$status] = $arrStatus[$status];
            }
        }
        return $arrReturn;
    }

    /**
     * 決済ステータスを更新
     *
     * @access public
     * @param string  $innerPayment 内部決済ID
     * @param string  $payStatus 決済ステータス
     * @param array $arrOrder 受注情報
     * @param array $arrRes 結果配列
     * @return boolean 成功：失敗
     * @see LC_Page_Admin_Order_Edit_Ex::doRegister()
     */
    function modifyPayStatus($innerPayment, $payStatus, $arrOrder, &$arrRes) {

        $arrRes = array();

        // メソッドを取得する
        $inner  = $innerPayment;
        $srcPay = $arrOrder['memo01'];
        $dstPay = $payStatus;
        if (isset($this->arrPayStatusRule[$inner][$srcPay][$dstPay]) == false) {
            // 処理しない
            return true;
        }
        $method = $this->arrPayStatusRule[$inner][$srcPay][$dstPay];
        if (strcmp($method, '') == 0) {
            // 処理しない
            return true;
        }

        // 定義されたメソッドを実行
        $bol = $this->$method($inner, $arrOrder, $arrRes);
        return $bol;
    }

    /**
     * 支払い方法、決済ステータスからキャンセル可否を判定
     *
     * @access public
     * @param string $innerPayment 内部決済ID
     * @param string $payStatus    決済ステータス
     * @return integer 結果
     */
    function isRemovablePayment($innerPayment, $payStatus) {

        if (isset($this->arrDeleteRule[$innerPayment][$payStatus]) == true) {
            return true;
        }
        return false;
    }

    /**
     * 現在の決済情報を取消・返金する
     *
     * @access public
     * @param string  $innerPayment 内部決済ID
     * @param string  $payStatus 決済ステータス
     * @param array $arrOrder 受注情報
     * @param array $arrRes 結果配列
     * @return boolean 成功：失敗
     * @see LC_Page_Admin_Order_Edit_Ex::doRegister()
     */
    function removePayment($innerPayment, $payStatus,
            $arrOrder, &$arrRes) {

        $arrRes = array();

        // 変更メソッドを取得
        if (isset($this->arrDeleteRule[$innerPayment][$payStatus]) == false) {
            // 処理しない
            return true;
        }
        $payStatus = $this->arrDeleteRule[$innerPayment][$payStatus];

        // ステータス更新
        $bol = $this->modifyPayStatus($innerPayment, $payStatus,
            $arrOrder, $arrRes);

        return $bol;
    }

    /**
     * 支払い方法から変更可否を判定
     *
     * @access public
     * @param string $innerPayment 内部決済ID
     * @return integer 結果
     */
    function isRenewablePayment($innerPayment) {

        if (isset($this->arrRenewRule[$innerPayment]) == true) {
            return true;
        }
        return false;
    }

    /**
     * 新たな決済情報を実行
     *
     * @access public
     * @param string  $innerPayment 内部決済ID
     * @param array $arrForm 入力情報
     * @param array $arrRes 結果配列
     * @return boolean 成功：失敗
     * @see LC_Page_Admin_Order_Edit_Ex::doRegister()
     */
    function renewPayment($innerPayment, $arrForm, &$arrRes) {

        $arrRes = array();

        // メソッドを取得する
        if (isset($this->arrRenewRule[$innerPayment]) == false) {
            // 処理しない
            return true;
        }
        $method = $this->arrRenewRule[$innerPayment];
        if (strcmp($method, '') == 0) {
            // 処理しない
            return true;
        }

        // 定義されたメソッドを実行
        $bol = $this->$method($arrForm, $arrRes);
        return $bol;
    }

    /**
     * クレジットカード決済処理(再決済)
     *
     * @access public
     * @param array $arrForm 入力情報配列
     * @param array $arrRes レスポンス情報配列
     * @return boolean 成功/失敗
     */
    function cardRenewExecute($arrForm, &$arrRes) {
        $logger =& TGMDK_Logger::getInstance();

        // 再取引？
        $isReTrade = false;
        if (isset($arrForm['newReTradeOId']) == true
        && strcmp($arrForm['newReTradeOId'], '') != 0) {
            $isReTrade = true;
        }

        // 新規で受注番号採番
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $newOrderId = $objQuery->nextVal('dtb_order_order_id');

        // 要求電文パラメータ値の指定
        if ($isReTrade == true) {
            // 再取引用リクエストオブジェクト
            $objRequest = new CardReAuthorizeRequestDto();
        } else {
            // 通常クレジット用リクエストオブジェクト
            $objRequest = new CardAuthorizeRequestDto();
        }

        // 受注番号(ゼロパディング)
        $objRequest->setOrderId(GC_Utils_SBIVT3G::getMdkOrderId($newOrderId));

        // 決済金額
        $objRequest->setAmount(sprintf('%d',$arrForm['payment_total']));

        if ($isReTrade == true) { // 再取引
            // 元取引ID
            $objRequest->setOriginalOrderId($arrForm['newReTradeOId']);
        } else { // それ以外
            // クレジットカード番号
            $cardNo = str_replace('-', '', $arrForm['newCardNo']);
            $objRequest->setCardNumber($cardNo);

            // カード有効期間
            $cardExpire = $arrForm['newExpiryMon'] . '/' . $arrForm['newExpiryYear'];
            $objRequest->setCardExpire($cardExpire);

        }
        // 支払い方法＋支払回数
        $jpo = $arrForm['newPaymentType'] . $arrForm['newPaymentCount'];
        $objRequest->setJpo($jpo);

        // 決済種別
        if ($arrForm['newCaptureFlg'] == true) {
            $objRequest->setWithCapture('true');
        } else {
            $objRequest->setWithCapture('false');
        }

        // 実行
        $logger->info("管理者クレジットカード決済[与信]通信実行");
        $objTransaction = new TGMDK_Transaction();
        $objResponse = $objTransaction->execute($objRequest);

        // 処理判定
        if (isset($objResponse) == false) {
            // システムエラー
            $arrRes['message'] = 'システムエラー';
            return false;
        }
        if ($objResponse->getMStatus() != MLD_SBIVT3G_MSTATUS_OK) {
            // 処理失敗
            $arrRes['message'] = sprintf('[%s] %s',
                $objResponse->getVResultCode(),
                $objResponse->getMerrMsg());
            return false;
        }
        // 結果取得
        $arrRes['status'] = ORDER_NEW;
        $arrRes['orderId'] = $objResponse->getOrderId();
        $arrRes['reqCardNo'] = $objResponse->getReqCardNumber();
        $reqJpo = $objResponse->getReqJpoInformation();
        $arrRes['paymentType'] = substr($reqJpo, 0, 2);
        $arrRes['paymentCount'] = substr($reqJpo, 2);
        if ($arrForm['newCaptureFlg'] == true) {
            $arrRes['payStatus'] = MDL_SBIVT3G_STATUS_CAPTURE;
        } else {
            $arrRes['payStatus'] = MDL_SBIVT3G_STATUS_AUTH;
        }

        $arrPaymentType = $this->objSetting->getPaymentType();
        $arrPaymentCount = $this->objSetting->getPaymentCount();

        // 完了記述情報
        $objIF = new SC_If_SBIVT3G_CompleteResource();
        $objIF->setCompMailRCTitle(PAYMENT_NAME_CREDIT);
        $objIF->setCompMailRC('お支払い方法',
            $arrPaymentType[$arrRes['paymentType']]);
        if ($arrRes['paymentType'] == MDL_SBIVT3G_PTYPE_SPLIT) {
            $objIF->setCompMailRC('お支払回数',
                $arrPaymentCount[$arrRes['paymentCount']]);
        }
        $arrRes['memo02'] = $objIF->getCompMailRC();

        // ログ用の情報
        $arrRes['other'] = ' ' . $arrPaymentType[$arrRes['paymentType']];
        if ($arrRes['paymentType'] == MDL_SBIVT3G_PTYPE_SPLIT) {
            $arrRes['other'] .= '/' . $arrPaymentCount[$arrRes['paymentCount']];
        }
        $logger->debug(print_r($arrRes, true));
        return true;
    }

    /**
     * コンビニ決済処理(再決済)
     *
     * @access public
     * @param array $arrForm 入力情報配列
     * @param array $arrRes レスポンス情報配列
     * @return boolean 成功/失敗
     */
    function cvsRenewExecute($arrForm, &$arrRes) {
        $logger =& TGMDK_Logger::getInstance();

        $arrRes = array('message' => '');

        // 新規で受注番号採番
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $newOrderId = $objQuery->nextVal('dtb_order_order_id');

        // 要求電文パラメータ値の指定
        $objRequest = new CvsAuthorizeRequestDto();
        $objRequest->setServiceOptionType($arrForm['newConveni']);
        $objRequest->setOrderId(GC_Utils_SBIVT3G::getMdkOrderId($newOrderId));
        $objRequest->setAmount(sprintf('%d',$arrForm['payment_total']));
        $objRequest->setName1(mb_convert_kana($arrForm['order_kana01']));
        $objRequest->setName2(mb_convert_kana($arrForm['order_kana02']));
        $telno = $arrForm['order_tel01']
            . '-' . $arrForm['order_tel02']
            . '-' . $arrForm['order_tel03'];
        $objRequest->setTelNo($telno);
        $limitDate = GC_Utils_SBIVT3G::getAddDateFormat(
            $this->objSetting->get('V_limitDays'), 'Y/m/d');
        $objRequest->setPayLimit($limitDate);
        $objRequest->setPaymentType('0');
        if ($arrForm['newConveni'] != MDL_SBIVT3G_CVS_TYPE_SEVEN) {
            $objRequest->setFree1($this->objSetting->get('V_shopName'));
        }
        if ($arrForm['newConveni'] != MDL_SBIVT3G_CVS_TYPE_SEVEN &&
            $arrForm['newConveni'] != MDL_SBIVT3G_CVS_TYPE_LAWSON &&
            $arrForm['newConveni'] != MDL_SBIVT3G_CVS_TYPE_ECON) {
            $objRequest->setFree2($this->objSetting->get('V_note'));
        }

        // 実行
        $logger->info("管理者コンビニ決済[申込]通信実行");
        $objTransaction = new TGMDK_Transaction();
        $objResponse = $objTransaction->execute($objRequest);

        // 処理判定
        if (isset($objResponse) == false) {
            // システムエラー
            $arrRes['message'] = 'システムエラー';
            return false;
        }
        if ($objResponse->getMStatus() != MLD_SBIVT3G_MSTATUS_OK) {
            // 処理失敗
            $arrRes['message'] = sprintf('[%s] %s',
                $objResponse->getVResultCode(),
                $objResponse->getMerrMsg());
            $logger->debug(print_r($objRequest, true));
            return false;
        }
        // 結果取得
        $arrRes['status'] = ORDER_PAY_WAIT;
        $arrRes['orderId'] = $objResponse->getOrderId();
        $arrRes['serviceOptionType'] = $arrForm['newConveni'];
        $arrRes['limitDate'] = $limitDate;
        $arrRes['receiptNo'] = $objResponse->getReceiptNo();
        $arrRes['telNo'] = $objRequest->getTelNo();
        $arrRes['haraikomiUrl'] = $objResponse->getHaraikomiUrl();
        $arrRes['payStatus'] = MDL_SBIVT3G_STATUS_REQUEST;

        $arrCvsShop = $this->objSetting->getCvsShop();

        // 完了記述情報
        $objIF = new SC_If_SBIVT3G_CompleteResource();
        $objIF->setCompMailRCTitle(PAYMENT_NAME_CONVENI);
        $objIF->setCompMailRC('お支払い先ストア',
            $arrCvsShop[$arrRes['serviceOptionType']]);
        // 受付番号設定
        $arrNo = GC_Utils_SBIVT3G::translateRecpNo(
            $arrRes['serviceOptionType'],
            $arrRes['receiptNo'],
            $arrRes['telNo'],
            true
        );
        foreach ($arrNo as $k => $v) {
            $objIF->setCompMailRC($k, $v);
        }
        // セブンイレブンであれば設定
        if (strcmp($arrRes['serviceOptionType'],
            MDL_SBIVT3G_CVS_TYPE_SEVEN) == 0) {
            $objIF->setCompMailRC('払込票URL', $arrRes['haraikomiUrl']);
        }
        $objIF->setCompMailRC('お支払い期限', $arrRes['limitDate']);
        // 説明取得
        $objIF->setCompMailRC('', GC_Utils_SBIVT3G::getExplain(
            $arrRes['serviceOptionType']
        ));
        // memo02:メールでの記述情報
        $arrRes['memo02'] = $objIF->getCompMailRC();

        // ログ用の情報
        $other .= ' 店舗['. $arrCvsShop[$arrRes['serviceOptionType']] .']';
        $other .= GC_Utils_SBIVT3G::translateRecpNo(
            $arrRes['serviceOptionType'],
            $arrRes['receiptNo'],
            $arrRes['telNo']
        );
        $other .= ' 支払期限['. $arrRes['limitDate'] .']';
        $arrRes['other'] = $other;

        $logger->debug(print_r($arrRes, true));
        return true;
    }

    /**
     * Pay-easy(ATM)決済処理(再決済)
     *
     * @access public
     * @param array $arrForm 入力情報配列
     * @param array $arrRes レスポンス情報配列
     * @return boolean 成功/失敗
     */
    function atmRenewExecute($arrForm, &$arrRes) {
        $logger =& TGMDK_Logger::getInstance();

        // 新規で受注番号採番
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $newOrderId = $objQuery->nextVal('dtb_order_order_id');

        // 要求電文パラメータ値の指定
        $objRequest = new BankAuthorizeRequestDto();
        $objRequest->setServiceOptionType(MDL_SBIVT3G_BANK_TYPE_ATM);
        $objRequest->setOrderId(GC_Utils_SBIVT3G::getMdkOrderId($newOrderId));
        $objRequest->setAmount(sprintf('%d',$arrForm['payment_total']));
        $objRequest->setName1(mb_convert_kana($arrForm['order_kana01']));
        $objRequest->setName2(mb_convert_kana($arrForm['order_kana02']));
        $objRequest->setKana1(mb_convert_kana($arrForm['order_kana01']));
        $objRequest->setKana2(mb_convert_kana($arrForm['order_kana02']));
        $limitDate = GC_Utils_SBIVT3G::getAddDateFormat(
            $this->objSetting->get('B_limitDays'), 'Ymd');
        $objRequest->setPayLimit($limitDate);
        $objRequest->setContents($this->objSetting->get('B_note'));
        $objRequest->setContentsKana($this->objSetting->get('B_noteKana'));

        // 実行
        $logger->info("管理者銀行決済(ATM)[申込]通信実行");
        $objTransaction = new TGMDK_Transaction();
        $objResponse = $objTransaction->execute($objRequest);

        // 処理判定
        if (isset($objResponse) == false) {
            // システムエラー
            $arrRes['message'] = 'システムエラーが発生しました。';
            return false;
        }
        if ($objResponse->getMStatus() != MLD_SBIVT3G_MSTATUS_OK) {
            // 処理失敗
            $arrRes['message'] = sprintf('[%s] %s',
                $objResponse->getVResultCode(),
                $objResponse->getMerrMsg());
            return false;
        }
        // 結果取得
        $arrRes['status'] = ORDER_PAY_WAIT;
        $arrRes['orderId'] = $objResponse->getOrderId();
        $arrRes['orgNo'] = $objResponse->getShunoKikanNo();
        $arrRes['customerNo'] = $objResponse->getCustomerNo();
        $arrRes['confirmNo'] = $objResponse->getConfirmNo();
        $arrRes['limitDate'] = GC_Utils_SBIVT3G::getAddDateFormat(
            $this->objSetting->get('B_limitDays'), 'Y/m/d');
        $arrRes['payStatus'] = MDL_SBIVT3G_STATUS_REQUEST;

        // 完了記述情報
        $objIF = new SC_If_SBIVT3G_CompleteResource();
        $objIF->setCompMailRCTitle(PAYMENT_NAME_ATM);
        $objIF->setCompMailRC('収納機関番号', $arrRes['orgNo']);
        $objIF->setCompMailRC('お客様番号', $arrRes['customerNo']);
        $objIF->setCompMailRC('確認番号', $arrRes['confirmNo']);
        $objIF->setCompMailRC('支払期限', $arrRes['limitDate']);
        // 説明取得
        $objIF->setCompMailRC('', GC_Utils_SBIVT3G::getExplain(
            MDL_SBIVT3G_BANK_TYPE_ATM
        ));
        // memo02:メールでの記述情報
        $arrRes['memo02'] = $objIF->getCompMailRC();

        // ログ用の情報
        $other = ' 収納機関番号['. $arrRes['orgNo'] .']';
        $other .= ' お客様番号['. $arrRes['customerNo'] .']';
        $other .= ' 確認番号['. $arrRes['confirmNo'] .']';
        $other .= ' 支払期限['. $arrRes['limitDate'] .']';
        $arrRes['other'] = $other;

        $logger->debug(print_r($arrRes, true));
        return true;
    }

    /**
     * クレジットカード売上請求処理
     *
     * @access public
     * @param string $innerPayment 内部決済ID
     * @param string $arrOrder 受注情報
     * @param array $arrRes レスポンス情報配列
     * @return boolean 成功/失敗
     */
    function cardCapture($innerPayment, $arrOrder, &$arrRes) {
        $logger =& TGMDK_Logger::getInstance();

        $arrRes = array('message' => '');

        // リクエスト生成
        $objRequest = new CardCaptureRequestDto();
        $objRequest->setOrderId($arrOrder['memo04']);
        $objRequest->setAmount(sprintf('%d',$arrOrder['payment_total']));

        // 通信実行
        $logger->info("管理者クレジットカード決済[売上]通信実行");
        $objTransaction = new TGMDK_Transaction();
        $objResponse = $objTransaction->execute($objRequest);

        // レスポンス検証
        if (isset($objResponse) == false) {
            // システムエラー
            $arrRes['message'] = 'システムエラーが発生しました。';
            return false;
        }

        // 処理判定
        if ($objResponse->getMStatus() != MLD_SBIVT3G_MSTATUS_OK) {
            // 処理失敗
            $arrRes['message'] = sprintf('[%s] %s',
                $objResponse->getVResultCode(),
                $objResponse->getMerrMsg());
            return false;
        }
        $arrRes['orderId']   = $objResponse->getOrderId();
        $arrRes['payStatus'] = MDL_SBIVT3G_STATUS_CAPTURE;

        // ログ用の情報
        $arrRes['other'] = sprintf(' 売上金額[%s]',
            number_format($objResponse->getReqAmount()));
        $logger->debug(print_r($arrRes, true));
        return true;
    }

    /**
     * クレジットカードキャンセル処理
     *
     * @access public
     * @param string $innerPayment 内部決済ID
     * @param string $arrOrder 受注情報
     * @param array $arrRes レスポンス情報配列
     * @return boolean 成功/失敗
     */
    function cardCancel($innerPayment, $arrOrder, &$arrRes) {
        $logger =& TGMDK_Logger::getInstance();

        $arrRes = array('message' => '');

        // リクエスト生成
        $objRequest = new CardCancelRequestDto();
        $objRequest->setOrderId($arrOrder['memo04']);
        $objRequest->setAmount(sprintf('%d',$arrOrder['payment_total']));

        // 通信実行
        $logger->info("管理者クレジットカード決済[取消]通信実行");
        $objTransaction = new TGMDK_Transaction();
        $objResponse = $objTransaction->execute($objRequest);

        // レスポンス検証
        if (isset($objResponse) == false) {
            // システムエラー
            $arrRes['message'] = 'システムエラーが発生しました。';
            return false;
        }

        // 処理判定
        if ($objResponse->getMStatus() != MLD_SBIVT3G_MSTATUS_OK) {
            // 処理失敗
            $arrRes['message'] = sprintf('[%s] %s',
                $objResponse->getVResultCode(),
                $objResponse->getMerrMsg());
            return false;
        }
        $arrRes['orderId']   = $objResponse->getOrderId();
        $arrRes['payStatus'] = MDL_SBIVT3G_STATUS_CANCEL;

        // ログ用の情報
        $arrRes['other'] = sprintf(' キャンセル金額[%s]',
            number_format($objResponse->getReqAmount()));
        $logger->debug(print_r($arrRes, true));

        return true;
    }

    /**
     * コンビニ決済キャンセル処理
     *
     * @access public
     * @param string $innerPayment 内部決済ID
     * @param string $arrOrder 受注情報
     * @param array $arrRes レスポンス情報配列
     * @return boolean 成功/失敗
     */
    function cvsCancel($innerPayment, $arrOrder, &$arrRes) {
        $logger =& TGMDK_Logger::getInstance();

        $arrRes = array('message' => '');

        // リクエスト生成
        $objRequest = new CvsCancelRequestDto();
        $objRequest->setOrderId($arrOrder['memo04']);
        $objRequest->setServiceOptionType($serviceOptionType);

        // 通信実行
        $logger->info("管理者コンビニ決済[取消]通信実行");
        $objTransaction = new TGMDK_Transaction();
        $objResponse = $objTransaction->execute($objRequest);

        // レスポンス検証
        if (isset($objResponse) == false) {
            // システムエラー
            $arrRes['message'] = 'システムエラーが発生しました。';
            return false;
        }

        // 処理判定
        if ($objResponse->getMStatus() != MLD_SBIVT3G_MSTATUS_OK) {
            // 処理失敗
            $arrRes['message'] = sprintf('[%s] %s',
                $objResponse->getVResultCode(),
                $objResponse->getMerrMsg());
            return false;
        }
        $arrRes['orderId']   = $objResponse->getOrderId();
        $arrRes['payStatus'] = MDL_SBIVT3G_STATUS_CANCEL;

        $logger->debug(print_r($arrRes, true));
        return true;
    }

    /**
     * Edy決済返金処理
     *
     * @access public
     * @param string $innerPayment 内部決済ID
     * @param string $arrOrder 受注情報
     * @param array $arrRes レスポンス情報配列
     * @return boolean 成功/失敗
     */
    function edyRefund($innerPayment, $arrOrder, &$arrRes) {
        $logger =& TGMDK_Logger::getInstance();

        $arrRes = array('message' => '');

        switch ($innerPayment) {
        case MDL_SBIVT3G_INNER_ID_EDY_MOBILE_MAIL :  // モバイルEdy
            $option = MDL_SBIVT3G_EM_TYPE_MOBILE_EDY;
            break;
        case MDL_SBIVT3G_INNER_ID_EDY_PC_APP : // サイバーEdy
            $option = MDL_SBIVT3G_EM_TYPE_CYBER_EDY;
            break;
        default :
            return false;
        }

        // 新規で受注番号採番
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $newOrderId = $objQuery->nextVal('dtb_order_order_id');

        // リクエスト生成
        $objRequest = new EmRefundRequestDto();
        $objRequest->setOrderId(GC_Utils_SBIVT3G::getMdkOrderId($newOrderId));
        $objRequest->setRefundOrderId($arrOrder['memo04']);
        $objRequest->setServiceOptionType($option);
        $objRequest->setOrderKind('refund');
        $objRequest->setAmount(sprintf('%d', $arrOrder['payment_total']));

        // 通信実行
        $logger->info("管理者電子マネー(Edy)決済[返金]通信実行");
        $objTransaction = new TGMDK_Transaction();
        $objResponse = $objTransaction->execute($objRequest);

        // レスポンス検証
        if (isset($objResponse) == false) {
            // システムエラー
            $arrRes['message'] = 'システムエラーが発生しました。';
            return false;
        }

        // 処理判定
        if ($objResponse->getMStatus() != MLD_SBIVT3G_MSTATUS_OK) {
            // 処理失敗
            $arrRes['message'] = sprintf('[%s] %s',
                $objResponse->getVResultCode(),
                $objResponse->getMerrMsg());
            return false;
        }
        $arrRes['orderId'] = $objResponse->getOrderId();
        $arrRes['appUrl'] = $objResponse->getAppUrl();
        $arrRes['payStatus'] = MDL_SBIVT3G_STATUS_REFUND;

        $logger->debug(print_r($arrRes, true));
        return true;
    }

    /**
     * Suica決済キャンセル処理
     *
     * @access public
     * @param string $innerPayment 内部決済ID
     * @param string $arrOrder 受注情報
     * @param array $arrRes レスポンス情報配列
     * @return boolean 成功/失敗
     */
    function suicaCancel($innerPayment, $arrOrder, &$arrRes) {
        $logger =& TGMDK_Logger::getInstance();

        $arrRes = array('message' => '');

        // 復元
        $arrReData = unserialize($arrOrder['memo05']);

        switch ($innerPayment) {
        case MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_MAIL : // モバイルSuica(メール型)
            $option = MDL_SBIVT3G_EM_TYPE_MOBILE_SUICA_MAIL;
            $mailAddr = $arrReData['mailAddr'];
            break;
        case MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_APP :  // モバイルSuica(アプリ型)
            $option = MDL_SBIVT3G_EM_TYPE_MOBILE_SUICA_APP;
            $mailAddr = '';
            break;
        case MDL_SBIVT3G_INNER_ID_SUICA_PC_MAIL :     // SuicaIS(メール型)
            $option = MDL_SBIVT3G_EM_TYPE_PC_SUICA_MAIL;
            $mailAddr = $arrReData['mailAddr'];
            break;
        case MDL_SBIVT3G_INNER_ID_SUICA_PC_APP :         // SuicaIS(アプリ型)
            $option = MDL_SBIVT3G_EM_TYPE_PC_SUICA_APP;
            $mailAddr = '';
            break;
        default :
            return false;
        }

        // リクエスト生成
        $objRequest = new EmCancelRequestDto();
        $objRequest->setOrderId($arrOrder['memo04']);
        $objRequest->setServiceOptionType($option);
        $objRequest->setOrderKind('authorize');
        if (strcmp($mailAddr, '') != 0) {
            $objRequest->setCancelMailAddr($mailAddr);
        }

        // 通信実行
        $logger->info("管理者電子マネー(Suica)決済[取消]通信実行");
        $objTransaction = new TGMDK_Transaction();
        $objResponse = $objTransaction->execute($objRequest);

        // レスポンス検証
        if (isset($objResponse) == false) {
            // システムエラー
            $arrRes['message'] = 'システムエラーが発生しました。';
            return false;
        }

        // 処理判定
        if ($objResponse->getMStatus() != MLD_SBIVT3G_MSTATUS_OK) {
            // 処理失敗
            $arrRes['message'] = sprintf('[%s] %s',
                $objResponse->getVResultCode(),
                $objResponse->getMerrMsg());
            return false;
        }
        $arrRes['orderId']   = $objResponse->getOrderId();
        $arrRes['payStatus'] = MDL_SBIVT3G_STATUS_CANCEL;

        $logger->debug(print_r($arrRes, true));
        return true;
    }

    /**
     * suica決済返金処理
     *
     * @access public
     * @param string $innerPayment 内部決済ID
     * @param string $arrOrder 受注情報
     * @param array $arrRes レスポンス情報配列
     * @return boolean 成功/失敗
     */
    function suicaRefund($innerPayment, $arrOrder, &$arrRes) {
        $logger =& TGMDK_Logger::getInstance();

        $arrRes = array('message' => '');

        // 復元
        $arrReData = unserialize($arrOrder['memo05']);

        switch ($innerPayment) {
        case MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_MAIL : // モバイルSuica(メール型)
            $option = MDL_SBIVT3G_EM_TYPE_MOBILE_SUICA_MAIL;
            $mailAddr = $arrReData['mailAddr'];
            break;
        case MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_APP :  // モバイルSuica(アプリ型)
            $option = MDL_SBIVT3G_EM_TYPE_MOBILE_SUICA_APP;
            $mailAddr = '';
            break;
        case MDL_SBIVT3G_INNER_ID_SUICA_PC_MAIL :     // SuicaIS(メール型)
            $option = MDL_SBIVT3G_EM_TYPE_PC_SUICA_MAIL;
            $mailAddr = $arrReData['mailAddr'];
            break;
        case MDL_SBIVT3G_INNER_ID_SUICA_PC_APP :         // SuicaIS(アプリ型)
            $option = MDL_SBIVT3G_EM_TYPE_PC_SUICA_APP;
            $mailAddr = '';
            break;
        default :
            return false;
        }

        // 新規で受注番号採番
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $newOrderId = $objQuery->nextVal('dtb_order_order_id');

        // リクエスト生成
        $objRequest = new EmRefundRequestDto();
        $objRequest->setOrderId(GC_Utils_SBIVT3G::getMdkOrderId($newOrderId));
        $objRequest->setRefundOrderId($arrOrder['memo04']);
        $objRequest->setServiceOptionType($option);
        $objRequest->setOrderKind('refund');
        $objRequest->setAmount(sprintf('%d', $arrOrder['payment_total']));
        $limitDate = GC_Utils_SBIVT3G::getAddDateFormat(
            $this->objSetting->get('ES_limitDays'), 'Ymd235959');
        $objRequest->setSettlementLimit($limitDate);

        if (strcmp($mailAddr, '') != 0) {
            $objRequest->setCancelMailAddr($mailAddr);
            $objRequest->setMailAddr($mailAddr);
            $fwdFlg = sprintf('%d', $this->objSetting->get('ES_bccMailFlg'));
            $objRequest->setForwardMailFlag($fwdFlg);
            if ($fwdFlg == '1') {
                $objRequest->setMerchantMailAddr(
                    $this->objSetting->get('ES_bccMailAddr'));
            }
            $objRequest->setRequestMailAddInfo(
                $this->objSetting->get('ES_reqMailInfo'));
            $objRequest->setRequestMailFlag('1');
        }

        $objRequest->setConfirmScreenAddInfo(
            $this->objSetting->get('ES_cnfDispInfo'));
        $objRequest->setCompleteScreenAddInfo(
            $this->objSetting->get('ES_cmpDispInfo'));
        $objRequest->setScreenTitle($this->objSetting->get('ES_shopName'));

        // 通信実行
        $logger->info("管理者電子マネー(Suica)決済[返金]通信実行");
        $objTransaction = new TGMDK_Transaction();
        $objResponse = $objTransaction->execute($objRequest);

        // レスポンス検証
        if (isset($objResponse) == false) {
            // システムエラー
            $arrRes['message'] = 'システムエラーが発生しました。';
            return false;
        }

        // 処理判定
        if ($objResponse->getMStatus() != MLD_SBIVT3G_MSTATUS_OK) {
            // 処理失敗
            $arrRes['message'] = sprintf('[%s] %s',
                $objResponse->getVResultCode(),
                $objResponse->getMerrMsg());
            return false;
        }
        $arrRes['orderId'] = $objResponse->getOrderId();
        $arrRes['appUrl'] = $objResponse->getAppUrl();
        $arrRes['payStatus'] = MDL_SBIVT3G_STATUS_REFUND;

        $logger->debug(print_r($arrRes, true));
        return true;
    }

    /**
     * Waon決済返金処理
     *
     * @access public
     * @param string $innerPayment 内部決済ID
     * @param string $arrOrder 受注情報
     * @param array $arrRes レスポンス情報配列
     * @return boolean 成功/失敗
     */
    function waonRefund($innerPayment, $arrOrder, &$arrRes) {
        $logger =& TGMDK_Logger::getInstance();

        $arrRes = array('message' => '');

        switch ($innerPayment) {
        case MDL_SBIVT3G_INNER_ID_WAON_MOBILE_APP :     // Waon(モバイル版)
            $option = MDL_SBIVT3G_EM_TYPE_MOBILE_WAON_APP;
            break;
        case MDL_SBIVT3G_INNER_ID_WAON_PC_APP :         // Waon(PC版)
            $option = MDL_SBIVT3G_EM_TYPE_PC_WAON_APP;
            break;
        default :
            return false;
        }

        // 新規で受注番号採番
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $newOrderId = $objQuery->nextVal('dtb_order_order_id');

        // リクエスト生成
        $objRequest = new EmRefundRequestDto();
        $objRequest->setOrderId(GC_Utils_SBIVT3G::getMdkOrderId($newOrderId));
        $objRequest->setRefundOrderId($arrOrder['memo04']);
        $objRequest->setServiceOptionType($option);
        $objRequest->setOrderKind('refund');
        $objRequest->setAmount(sprintf('%d', $arrOrder['payment_total']));
        $objRequest->setMailAddr($arrOrder['order_email']);

        // PC限定項目
        if ($option == MDL_SBIVT3G_EM_TYPE_PC_WAON_APP) {
            $objRequest->setSuccessUrl(HTTP_URL);
            $objRequest->setFailureUrl(HTTP_URL);
            $objRequest->setCancelUrl(HTTP_URL);
        }

        // 通信実行
        $logger->info("管理者電子マネー(Waon)決済[返金]通信実行");
        $objTransaction = new TGMDK_Transaction();
        $objResponse = $objTransaction->execute($objRequest);

        // レスポンス検証
        if (isset($objResponse) == false) {
            // システムエラー
            $arrRes['message'] = 'システムエラーが発生しました。';
            return false;
        }

        // 処理判定
        if ($objResponse->getMStatus() != MLD_SBIVT3G_MSTATUS_OK) {
            // 処理失敗
            $arrRes['message'] = sprintf('[%s] %s',
                $objResponse->getVResultCode(),
                $objResponse->getMerrMsg());
            return false;
        }
        $arrRes['orderId'] = $objResponse->getOrderId();
        $arrRes['appUrl'] = $objResponse->getAppUrl();
        $arrRes['payStatus'] = MDL_SBIVT3G_STATUS_REFUND;

        $logger->debug(print_r($arrRes, true));
        return true;
    }

    /**
     * 銀聯ネット決済キャンセル処理
     *
     * @access public
     * @param string $innerPayment 内部決済ID
     * @param string $arrOrder 受注情報
     * @param array $arrRes レスポンス情報配列
     * @return boolean 成功/失敗
     */
    function cupCancel($innerPayment, $arrOrder, &$arrRes) {
        $logger =& TGMDK_Logger::getInstance();

        $arrRes = array('message' => '');

        // リクエスト生成
        $objRequest = new CupCancelRequestDto();
        $objRequest->setOrderId($arrOrder['memo04']);

        // 通信実行
        $logger->info("管理者銀聯ネット決済[取消]通信実行");
        $objTransaction = new TGMDK_Transaction();
        $objResponse = $objTransaction->execute($objRequest);

        // レスポンス検証
        if (isset($objResponse) == false) {
            // システムエラー
            $arrRes['message'] = 'システムエラーが発生しました。';
            return false;
        }

        // 処理判定
        if ($objResponse->getMStatus() != MLD_SBIVT3G_MSTATUS_OK) {
            // 処理失敗
            $arrRes['message'] = sprintf('[%s] %s',
                $objResponse->getVResultCode(),
                $objResponse->getMerrMsg());
            return false;
        }
        $arrRes['orderId']   = $objResponse->getOrderId();
        $arrRes['payStatus'] = MDL_SBIVT3G_STATUS_CANCEL;

        $logger->debug(print_r($arrRes, true));
        return true;
    }

    /**
     * 銀聯ネット決済返金処理
     *
     * @access public
     * @param string $innerPayment 内部決済ID
     * @param string $arrOrder 受注情報
     * @param array $arrRes レスポンス情報配列
     * @return boolean 成功/失敗
     */
    function cupRefund($innerPayment, $arrOrder, &$arrRes) {
        $logger =& TGMDK_Logger::getInstance();

        $arrRes = array('message' => '');

        // リクエスト生成
        $objRequest = new CupRefundRequestDto();
        $objRequest->setOrderId($arrOrder['memo04']);
        $objRequest->setAmount(sprintf('%d',$arrOrder['payment_total']));

        // 通信実行
        $logger->info("管理者銀聯ネット決済[返金]通信実行");
        $objTransaction = new TGMDK_Transaction();
        $objResponse = $objTransaction->execute($objRequest);

        // レスポンス検証
        if (isset($objResponse) == false) {
            // システムエラー
            $arrRes['message'] = 'システムエラーが発生しました。';
            return false;
        }

        // 処理判定
        if ($objResponse->getMStatus() != MLD_SBIVT3G_MSTATUS_OK) {
            // 処理失敗
            $arrRes['message'] = sprintf('[%s] %s',
                $objResponse->getVResultCode(),
                $objResponse->getMerrMsg());
            return false;
        }
        $arrRes['orderId']   = $objResponse->getOrderId();
        $arrRes['payStatus'] = MDL_SBIVT3G_STATUS_REFUND;

        $logger->debug(print_r($arrRes, true));
        return true;
    }

    /**
     * PayPal決済売上請求処理
     *
     * @access public
     * @param string $innerPayment 内部決済ID
     * @param string $arrOrder 受注情報
     * @param array $arrRes レスポンス情報配列
     * @return boolean 成功/失敗
     */
    function paypalCapture($innerPayment, $arrOrder, &$arrRes) {
        $logger =& TGMDK_Logger::getInstance();

        $arrRes = array('message' => '');

        // リクエスト生成
        $objRequest = new PayPalCaptureRequestDto();
        $objRequest->setAction('capture');
        $objRequest->setOrderId($arrOrder['memo04']);
        $objRequest->setAmount(sprintf('%d',$arrOrder['payment_total']));

        // 通信実行
        $logger->info("管理者PayPal決済[売上]通信実行");
        $objTransaction = new TGMDK_Transaction();
        $objResponse = $objTransaction->execute($objRequest);

        // レスポンス検証
        if (isset($objResponse) == false) {
            // システムエラー
            $arrRes['message'] = 'システムエラーが発生しました。';
            return false;
        }

        // 処理判定
        if ($objResponse->getMStatus() != MLD_SBIVT3G_MSTATUS_OK) {
            // 処理失敗
            $arrRes['message'] = sprintf('[%s] %s',
                $objResponse->getVResultCode(),
                $objResponse->getMerrMsg());
            return false;
        }
        $arrRes['orderId']   = $objResponse->getOrderId();
        $arrRes['payStatus'] = MDL_SBIVT3G_STATUS_CAPTURE;

        $logger->debug(print_r($arrRes, true));
        return true;
    }

    /**
     * PayPal決済キャンセル処理
     *
     * @access public
     * @param string $innerPayment 内部決済ID
     * @param string $arrOrder 受注情報
     * @param array $arrRes レスポンス情報配列
     * @return boolean 成功/失敗
     */
    function paypalCancel($innerPayment, $arrOrder, &$arrRes) {
        $logger =& TGMDK_Logger::getInstance();

        $arrRes = array('message' => '');

        // リクエスト生成
        $objRequest = new PayPalCancelRequestDto();
        $objRequest->setOrderId($arrOrder['memo04']);

        // 通信実行
        $logger->info("管理者PayPal決済[取消]通信実行");
        $objTransaction = new TGMDK_Transaction();
        $objResponse = $objTransaction->execute($objRequest);

        // レスポンス検証
        if (isset($objResponse) == false) {
            // システムエラー
            $arrRes['message'] = 'システムエラーが発生しました。';
            return false;
        }

        // 処理判定
        if ($objResponse->getMStatus() != MLD_SBIVT3G_MSTATUS_OK) {
            // 処理失敗
            $arrRes['message'] = sprintf('[%s] %s',
                $objResponse->getVResultCode(),
                $objResponse->getMerrMsg());
            return false;
        }
        $arrRes['orderId']   = $objResponse->getOrderId();
        $arrRes['payStatus'] = MDL_SBIVT3G_STATUS_CANCEL;

        $logger->debug(print_r($arrRes, true));
        return true;
    }

    /**
     * PayPal決済返金処理
     *
     * @access public
     * @param string $innerPayment 内部決済ID
     * @param string $arrOrder 受注情報
     * @param array $arrRes レスポンス情報配列
     * @return boolean 成功/失敗
     */
    function paypalRefund($innerPayment, $arrOrder, &$arrRes) {
        $logger =& TGMDK_Logger::getInstance();

        $arrRes = array('message' => '');

        // リクエスト生成
        $objRequest = new PayPalRefundRequestDto();
        $objRequest->setOrderId($arrOrder['memo04']);
        $objRequest->setAmount(sprintf('%d',$arrOrder['payment_total']));

        // 通信実行
        $logger->info("管理者PayPal決済[返金]通信実行");
        $objTransaction = new TGMDK_Transaction();
        $objResponse = $objTransaction->execute($objRequest);

        // レスポンス検証
        if (isset($objResponse) == false) {
            // システムエラー
            $arrRes['message'] = 'システムエラーが発生しました。';
            return false;
        }

        // 処理判定
        if ($objResponse->getMStatus() != MLD_SBIVT3G_MSTATUS_OK) {
            // 処理失敗
            $arrRes['message'] = sprintf('[%s] %s',
                $objResponse->getVResultCode(),
                $objResponse->getMerrMsg());
            return false;
        }
        $arrRes['orderId']   = $objResponse->getOrderId();
        $arrRes['payStatus'] = MDL_SBIVT3G_STATUS_REFUND;

        $logger->debug(print_r($arrRes, true));
        return true;
    }

    /**
     * キャリア決済 売上処理
     *
     * @access public
     * @param string $innerPayment 内部決済ID
     * @param string $arrOrder 受注情報
     * @param array $arrRes レスポンス情報配列
     * @return boolean 成功/失敗
     */
    function carrierCapture($innerPayment, $arrOrder, &$arrRes)
    {
        $logger =& TGMDK_Logger::getInstance();

        $arrRes = array('message' => '');

        // 取引IDを取得
        $orderId = $arrOrder['memo04'];
        // サービスオプションタイプを取得
        $saveResData = unserialize($arrOrder['memo05']);
        $serviceOptionType = $saveResData['serviceOptionType'];

        // リクエスト生成
        $objRequest = new CarrierCaptureRequestDto();
        $objRequest->setOrderId($orderId);
        $objRequest->setServiceOptionType($serviceOptionType);

        // 通信実行
        $logger->info("管理者キャリア決済[売上]通信実行");
        $objTransaction = new TGMDK_Transaction();
        $objResponse = $objTransaction->execute($objRequest);

        // レスポンス検証
        if (isset($objResponse) == false) {
            // システムエラー
            $arrRes['message'] = 'システムエラーが発生しました。';
            return false;
        }

        // 処理判定
        if ($objResponse->getMStatus() != MLD_SBIVT3G_MSTATUS_OK) {
            // 処理失敗
            $arrRes['message'] = sprintf('[%s] %s',
                                         $objResponse->getVResultCode(),
                                         $objResponse->getMerrMsg());
            return false;
        }
        $arrRes['orderId']           = $objResponse->getOrderId();
        $arrRes['serviceOptionType'] = $serviceOptionType;
        $arrRes['payStatus']         = MDL_SBIVT3G_STATUS_CAPTURE;

        // ログ用の情報
        $arrRes['other']  = ' サービスオプションタイプ[';
        $arrRes['other'] .= $serviceOptionType . ']';

        $logger->debug(print_r($arrRes, true));

        return true;
    }

    /**
     * キャリア決済 キャンセル処理
     *
     * @access public
     * @param string $innerPayment 内部決済ID
     * @param string $arrOrder 受注情報
     * @param array $arrRes レスポンス情報配列
     * @return boolean 成功/失敗
     */
    function carrierCancel($innerPayment, $arrOrder, &$arrRes)
    {
        $logger =& TGMDK_Logger::getInstance();

        $arrRes = array('message' => '');

        // 取引IDを取得
        $orderId = $arrOrder['memo04'];
        // サービスオプションタイプを取得
        $saveResData = unserialize($arrOrder['memo05']);
        $serviceOptionType = $saveResData['serviceOptionType'];

        // リクエスト生成
        $objRequest = new CarrierCancelRequestDto();
        $objRequest->setOrderId($orderId);
        $objRequest->setServiceOptionType($serviceOptionType);

        // 通信実行
        $logger->info("管理者キャリア決済[取消]通信実行");
        $objTransaction = new TGMDK_Transaction();
        $objResponse = $objTransaction->execute($objRequest);

        // レスポンス検証
        if (isset($objResponse) == false) {
            // システムエラー
            $arrRes['message'] = 'システムエラーが発生しました。';
            return false;
        }

        // 処理判定
        if ($objResponse->getMStatus() != MLD_SBIVT3G_MSTATUS_OK) {
            // 処理失敗
            $arrRes['message'] = sprintf('[%s] %s',
                                         $objResponse->getVResultCode(),
                                         $objResponse->getMerrMsg());
            return false;
        }
        $arrRes['orderId']           = $objResponse->getOrderId();
        $arrRes['serviceOptionType'] = $serviceOptionType;
        $arrRes['payStatus']         = MDL_SBIVT3G_STATUS_CANCEL;

        // ログ用の情報
        $arrRes['other']  = ' サービスオプションタイプ[';
        $arrRes['other'] .= $serviceOptionType . ']';

        $logger->debug(print_r($arrRes, true));

        return true;
    }

    /**
     * 取引IDに合致する受注のオブジェクトを取得
     *
     * @access public
     * @param string $orderId  3Gの取引ID
     * @param OrderInfo $objOrderInfo 検索結果:オーダー情報のクラス
     * @return integer -1:エラー 0:結果ゼロ 1:正常取得
     * @see tgMdk/LibtgMdkDto/OrderInfo.php
     */
    function searchOrderInfo($orderId, &$objOrderInfo) {
        $logger =& TGMDK_Logger::getInstance();
        $arrSettings =& $this->objSetting->arrSettings;

        // 条件設定
        $objCommonParam = new CommonSearchParameter();
        $objCommonParam->setOrderId($orderId);
        $objSearchParam = new SearchParameters();
        $objSearchParam->setCommon($objCommonParam);
        $objRequest = new SearchRequestDto();
        $objRequest->setSearchParameters($objSearchParam);
        $objRequest->setNewerFlag('true');
        $objRequest->setContainDummyFlag($arrSettings['dummyModeFlg']);

        // 通信
        $objTransaction = new TGMDK_Transaction();
        $objResponse = $objTransaction->execute($objRequest);

        // 判定
        if ($objResponse->getMstatus() == MLD_SBIVT3G_MSTATUS_NG) {
            $logger->debug($objRequest);
            $logger->debug($objResponse);
            return -1; // エラー
        }

        // 取得
        $objOrderInfos = $objResponse->getOrderInfos();
        $arrOrderInfos = $objOrderInfos->getOrderInfo();
        if (count($arrOrderInfos) == 0) {
            $logger->debug($objOrderInfos);
            return 0; // 該当なし
        }

        $objOrderInfo = $arrOrderInfos[0];
        return 1; // 検索成功
    }

    /**
     * 指定された受注オブジェクトの決済ステータスを取得
     *
     * @access public
     * @param OrderInfo $objOrderInfo 検索結果:オーダー情報のクラス
     * @return string 決済ステータス
     * @see tgMdk/LibtgMdkDto/OrderInfo.php
     */
    function extractPayStatus($objOrderInfo) {
        $logger =& TGMDK_Logger::getInstance();
        $arrSettings =& $this->objSetting->arrSettings;

        // 決済タイプ(支払い方法)を取得
        $serviceType = $objOrderInfo->getServiceTypeCd();

        // 取引状態が期限切れならすぐに期限切れに変更
        $orderStatus = $objOrderInfo->getOrderStatus();
        if (strcmp($orderStatus, 'expired') == 0) {
            return MDL_SBIVT3G_STATUS_EXPIRED;
        }

        // トランザクションタイプを取得
        $txnType =  $objOrderInfo->getSuccessDetailTxnType();
        $logger->info('TxnType:' . $txnType);
        if (strcmp($txnType, '') == 0) {
            return ''; // 該当なし
        }

        // ステータスを抽出
        $status = '';
        switch ($serviceType) {
        case 'card':   //  カード決済
        case 'mpi':    //  MPIホスティング
            switch ($txnType) {
            case 'a': // 与信
            case 'ax': // 与信（期限切れ）
            case 'ap': // 与信（保留）
                $status = MDL_SBIVT3G_STATUS_AUTH;
                break;
            case 'ac': // 与信売上
            case 'acp': // 与信売上（保留）
            case 'pa': // 売上
            case 'rpae': // 売上→取消(部分取消)
            case 'race': // 与信売上→取消(部分取消)
                $status = MDL_SBIVT3G_STATUS_CAPTURE;
                break;
            case 'va' : case 'rad' : case 'rae': // 与信→取消
            case 'vap' : case 'rap': // 与信→取消（保留）
            case 'vac' : case 'racd' : // 与信売上→取消
            case 'vacp' : case 'racp': // 与信売上→取消（保留）
            case 'vpa' : case 'rpad' : // 売上→取消
            case 'vpap' : case 'rpap': // 売上→取消（保留）
                $status = MDL_SBIVT3G_STATUS_CANCEL;
                break;
            case 'rn': // 新規返品
            case 'rnp': // 新規返品（保留）
            default :
                break;
            }
            break;
        case 'cvs':    //  コンビニ決済
            switch ($txnType) {
            case 'authorize': // 決済請求
                $status = MDL_SBIVT3G_STATUS_REQUEST;
                break;
            case 'cancel_authorize': // 決済請求取消
            case 'cancel_capture': // 決済完了（入金取消）
                $status = MDL_SBIVT3G_STATUS_CANCEL;
                break;
            case 'capture': // 決済完了（入金済）
            case 'fix_capture': // 決済完了（入金確定）
                $status = MDL_SBIVT3G_STATUS_DEPOSIT;
                break;
            default :
                break;
            }
            break;
        case 'em':     //  電子マネー決済
            switch ($txnType) {
            case 'authorize': // 決済請求
                $status = MDL_SBIVT3G_STATUS_REQUEST;
                break;
            case 'refund': // 返金請求
            case 'fix_refund': // 返金完了
                $status = MDL_SBIVT3G_STATUS_REFUND;
                break;
            case 'cancel_authorize': // 決済請求取消
                $status = MDL_SBIVT3G_STATUS_CANCEL;
                break;
            case 'capture': // 決済完了
                $status = MDL_SBIVT3G_STATUS_DEPOSIT;
                break;
            case 'present': // プレゼント請求
            case 'refund_new': // 新規返金請求
            case 'cancel_refund': // 返金請求取消
            case 'cancel_refund_new': // 新規返金請求取消
            case 'cancel_present': // プレゼント請求取消
            case 'part_refund': // 一部返金完了
            case 'fix_refund_new': // 新規返金完了
            case 'fix_present': // プレゼント完了
            default :
                break;
            }
            break;
        case 'bank':   // 銀行決済
            switch ($txnType) {
            case 'authorize': // 決済請求
                $status = MDL_SBIVT3G_STATUS_REQUEST;
                break;
            case 'capture': // 収納情報通知
                $status = MDL_SBIVT3G_STATUS_DEPOSIT;
                break;
            case 'paid_confirm': // 入金確認
            case 'bank_select': // 金融機関選択
            default :
                break;
            }
            break;
        case 'cup':    //  銀聯ネット決済
            switch ($txnType) {
            case 'authorize': // 決済請求
                $status = MDL_SBIVT3G_STATUS_REQUEST;
                break;
            case 'capture': // 決済結果通知（決済請求）
                $status = MDL_SBIVT3G_STATUS_DEPOSIT;
                break;
            case 'cancel_authorize': // 決済請求取消
                $status = MDL_SBIVT3G_STATUS_CANCEL;
                break;
            case 'refund': // 返金
                $status = MDL_SBIVT3G_STATUS_REFUND;
                break;
            case 'initial_authorize': // 決済請求通知
            case 'verify': // 解析
            default :
                break;
            }
            break;
        case 'paypal': //  PayPal決済
            switch ($txnType) {
            case 'do_authorize': // 決済請求(Authorize：Do)
                $status = MDL_SBIVT3G_STATUS_AUTH;
                break;
            case 'do_capture': // 売上
                $status = MDL_SBIVT3G_STATUS_CAPTURE;
                break;
            case 'cancel_authorize': // 取消
                $status = MDL_SBIVT3G_STATUS_CANCEL;
                break;
            case 'refund': // 返金
                $status = MDL_SBIVT3G_STATUS_REFUND;
                break;
            case 'set_authorize': // 決済請求(Authorize：Set)
            case 'get_authorize': // 決済請求(Authorize：Get)
            case 'set_capture': // 決済請求(Capture：Set)
            case 'get_capture': // 決済請求(Capture：Get)
            case 'do_capture': // 決済請求(Capture：Do)
            case 'reauthorize': // 再与信
            default :
                break;
            }
            break;
        case 'carrier':    // キャリア決済
            switch ($txnType) {
            case 'Auth':                // 与信
                // 与信
                $status = MDL_SBIVT3G_STATUS_AUTH;
                break;
            case 'PostAuth':            // 売上
            case 'AuthCapture':         // 与信売上
                // 売上
                $status = MDL_SBIVT3G_STATUS_CAPTURE;
                break;
            case 'VoidAuth':            // 取消（与信）
            case 'VoidPostAuth':        // 取消（売上）
            case 'VoidAuthCapture':     // 取消（与信売上）
                // 取消
                $status = MDL_SBIVT3G_STATUS_CANCEL;
                break;
            case 'Init':                // 決済申込
            case 'Deregistration':      // 抹消
            case 'Terminate':           // 継続終了
            default:
                break;
            }
            break;
        default :
            break;
        }
        return $status;
    }
}
