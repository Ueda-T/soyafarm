<?php
/**
 * SC_Helper_SBIVT3G_Admin.php - SC_Helper_SBIVT3G_Admin クラスを定義
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: SC_Helper_SBIVT3G_Admin.php 188 2012-08-07 06:57:51Z hira $
 * @link        http://www.veritrans.co.jp/3gps
*/

/**
 * SBIVT3Gモジュール管理者ヘルパークラス
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
class SC_Helper_SBIVT3G_Admin {

    // {{{ properties

    /** 店舗別設定ヘルパー */
    var $objSetting;

    /**  受注情報メンテナンス用データ */
    var $objIF;

    // }}}
    // {{{ functions

    /**
     * コンストラクタ
     *
     * @access public
     * @return void
     */
    function SC_Helper_SBIVT3G_Admin() {
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
     * @return SC_Helper_SBIVT3G_Admin シングルトン・インスタンス
     */
    function getSingletonInstance() {
        $myName = '_SC_Helper_SBIVT3G_Admin_instance';
        if (isset($GLOBALS[$myName]) == false
                || get_class($GLOBALS[$myName]) != "SC_Helper_SBIVT3G_Admin") {
            $GLOBALS[$myName] =& new SC_Helper_SBIVT3G_Admin();
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
    }

    /**
     * 受注情報メンテナンス用データクラスを設定
     *
     * @access public
     * @param SC_If_SBIVT3G_OrderDataMainte
     * @return void
     */
    function setIF($objIF) {
        $this->objIF = $objIF;
    }

    /**
     * 受注情報メンテナンス用データクラスを返す
     *
     * @access public
     * @return SC_If_SBIVT3G_OrderDataMainte
     */
    function getIF() {
        return $this->objIF;
    }

    /**
     * 管理画面->受注編集での受注情報パラメータ初期化
     *
     * @access public
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     * @see LC_Page_Admin_Order_Edit_Ex::lfInitParam()
     */
    function initOrderParam(&$objFormParam) {
        // 追加
        $objFormParam->addParam('決済ステータス', 'memo01',
            INT_LEN,
            'n',
            array()
        );
        $objFormParam->addParam('決済メール用情報', 'memo02',
            MTEXT_LEN,
            'n',
            array()
        );
        $objFormParam->addParam('決済変更ログ', 'memo03',
            MTEXT_LEN,
            'n',
            array()
        );
        $objFormParam->addParam('最終受注番号', 'memo04',
            MTEXT_LEN,
            'n',
            array()
        );
        $objFormParam->addParam('再決済用情報', 'memo05',
            MTEXT_LEN,
            'n',
            array()
        );
        $objFormParam->addParam('再取引用カード番号', 'memo06',
            MTEXT_LEN,
            'n',
            array()
        );
        $objFormParam->addParam('返金受注番号', 'memo07',
            MTEXT_LEN,
            'n',
            array()
        );
        $objFormParam->addParam('返金URL', 'memo08',
            MTEXT_LEN,
            'n',
            array()
        );
        // クレジットカード決済
        $objFormParam->addParam('カード番号',
            'newCardNo',
            MDL_SBIVT3G_CARD_NO_MAXLEN,
            'n',
            array('MAX_LENGTH_CHECK')
        );
        $objFormParam->addParam('有効期限(月)',
            'newExpiryMon',
            2,
            'n',
            array('NUM_COUNT_CHECK', 'NUM_CHECK')
        );
        $objFormParam->addParam('有効期限(年)',
            'newExpiryYear',
            2,
            'n',
            array('NUM_COUNT_CHECK', 'NUM_CHECK')
        );
        $objFormParam->addParam('再取引用注文番号',
            'newReTradeOId',
            STEXT_LEN,
            'n',
            array('MAX_LENGTH_CHECK')
        );
        $objFormParam->addParam('支払方法',
            'newPaymentType',
            2,
            'n',
            array('MAX_LENGTH_CHECK', 'NUM_CHECK')
        );
        $objFormParam->addParam('支払回数',
            'newPaymentCount',
            3,
            'n',
            array('MAX_LENGTH_CHECK')
        );
        $objFormParam->addParam('売上フラグ',
            'newCaptureFlg',
            1,
            'n',
            array('MAX_LENGTH_CHECK'),
            $this->objSetting->arrSettings['C_captureFlg']
        );
        $objFormParam->addParam('cardInput', 'cardInput',
            '', '', array(), '', 'false');
        $objFormParam->addParam('canDownPriceCapture', 'canDownPriceCapture',
            '', '', array(), '', 'false');
        $objFormParam->addParam('doDownPriceCapture', 'doDownPriceCapture',
            '', '', array(), '', 'false');
        $objFormParam->addParam('canDownPriceCancel', 'canDownPriceCancel',
            '', '', array(), '', 'false');
        $objFormParam->addParam('doDownPriceCancel', 'doDownPriceCancel',
            '', '', array(), '', 'false');

        // コンビニ決済
        $objFormParam->addParam('コンビニ', 'newConveni',
            MDL_SBIVT3G_SERVICE_OPTION_MAXLEN,
            'n',
            array('MAX_LENGTH_CHECK')
        );
        $objFormParam->addParam('cvsInput', 'cvsInput',
            '', '', array(), '', false);
    }

    /**
     * 管理画面->受注編集でのカード入力情報チェック処理
     *
     * @access public
     * @param array $arrValues 受注情報配列
     * @return array エラー配列
     */
    function checkCardInput($arrValues) {

        $arrErr = array();

        // 未設定時(初表示時)はチェックせず終了
        if (strcmp($arrValues['cardInput'], '') == 0) {
            $arrErr['cardInput'] = '※カード情報を入力して下さい。<br/>';
            return $arrErr;
        }

        // 減額処理の方法
        if (strcmp($arrValues['canDownPriceCapture'], '') != 0) {
            if (strcmp($arrValues['doDownPriceCapture'], '') == 0) {
                $arrErr['doDownPriceCapture'] =
                    '※必ずどちらかを選択して下さい。<br/>';
                return $arrErr;
            }
            // 減額売上請求の場合
            if (strcmp($arrValues['doDownPriceCapture'], '1') == 0) {
                // 必ず売上が選択されていること
                if (strcmp($arrValues['memo01'], MDL_SBIVT3G_STATUS_CAPTURE)
                    != 0) {

                    $arrErr['memo01'] = '※必ず“売上”を選択して下さい。<br/>';
                }
                return $arrErr;
            }
        } else if (strcmp($arrValues['canDownPriceCancel'], '') != 0) {
            if (strcmp($arrValues['doDownPriceCancel'], '') == 0) {
                $arrErr['doDownPriceCancel'] = 
                    '※必ずどちらかを選択して下さい。<br/>';
                return $arrErr;
            }
            // 部分返金の場合
            if (strcmp($arrValues['doDownPriceCancel'], '1') == 0) {
                // 必ず取消が選択されていること
                if (strcmp($arrValues['memo01'], MDL_SBIVT3G_STATUS_CANCEL)
                    != 0) {

                    $arrErr['memo01'] = '※必ず“取消”を選択して下さい。<br/>';
                }
                return $arrErr;
            }
        }

        if (strlen($arrValues['newReTradeOId']) == 0) {
            // カード番号
            if (preg_match('/^[0-9\-]+$/', $arrValues['newCardNo']) == false) {
                $arrErr['newCardNo'] =
                    '※半角数字とハイフンで入力して下さい。<br/>';
                
            }
            if (preg_match('/-{2,}/', $arrValues['newCardNo']) == true
                || strlen(str_replace('-', '', $arrValues['newCardNo']))
                    < MDL_SBIVT3G_CARD_NO_MINLEN) {
                $arrErr['newCardNo'] = '※入力書式が不正です。<br/>';
            }
            if (strlen($arrValues['newCardNo']) == 0) {
                $arrErr['newCardNo'] = '※再取引用注文番号を'
                    . '選択しない場合は必ず入力して下さい。<br/>';
            }

            // 有効期限
            $bol = GC_Utils_SBIVT3g::isValidExpiry(
                $arrValues['newExpiryMon'],
                $arrValues['newExpiryYear']
            );
            if ($bol == false ) {
                $arrErr['newExpiryMon'] = '※有効期間をご確認下さい。<br/>';
                $arrErr['newExpiryYear'] = ' ';
            }
            $isCheck = checkdate($arrValues['newExpiryMon'], 1,
                '20' + $arrValues['newExpiryYear']);
            if (strlen($arrValues['newReTradeOId']) == 0 && $isCheck == false) {
                $arrErr['newExpiryMon'] = '※不正な年月です。<br/>';
                $arrErr['newExpiryYear'] = ' ';
            }
            if (strlen($arrValues['newExpiryMon']) == 0
                || strlen($arrValues['newExpiryYear']) == 0) {
                $arrErr['newExpiryMon'] = '※再取引用注文番号を'
                    . '選択しない場合は必ず入力して下さい。<br/>';
                $arrErr['newExpiryYear'] = ' ';
            }
        }

        // 支払い方法・回数
        if ($arrValues['newPaymentType'] == MDL_SBIVT3G_PTYPE_SPLIT) {
            if ($arrValues['newPaymentCount'] == '') {
                $arrErr['newPaymentCount'] = 
                    '※お支払い回数を選択して下さい。<br/>';
            }
        } else if ($arrValues['newPaymentType'] == MDL_SBIVT3G_PTYPE_BULK
        || $arrValues['newPaymentType'] == MDL_SBIVT3G_PTYPE_REVO) {
            if ($arrValues['newPaymentCount'] != '') {
                $arrErr['newPaymentCount'] = 
                    '※お支払い回数の選択は不要です。<br/>';
            }
        }
        if (strlen($arrValues['newPaymentType']) == 0) {
            $arrErr['newPaymentType'] = '※必ず入力して下さい。<br/>';
        }
        return $arrErr;
    }

    /**
     * 管理画面->受注編集でのコンビニ入力情報チェック処理
     *
     * @access public
     * @param array $arrValues 受注情報配列
     * @return array エラー配列
     */
    function checkCvsInput($arrValues) {

        $arrErr = array();

        // 未設定時(初表示時)はチェックせず終了
        if (strcmp($arrValues['cvsInput'], '') == 0) {
            $arrErr['cvsInput'] = '※コンビニを選択して下さい。<br/>';
            return $arrErr;
        }

        if (strlen($arrValues['newConveni']) == 0) {
            $arrErr['newConveni'] = '※必ず入力して下さい。<br/>';
        }
        return $arrErr;
    }

    /**
     * 管理画面->受注編集での受注情報の入力チェック処理
     *
     * @access public
     * @param array $arrValues 受注情報配列
     * @return array エラー配列
     * @see LC_Page_Admin_Order_Edit_Ex::lfCheckError()
     */
    function checkOrderRecord($arrValues) {

        $objRule =& SC_Helper_SBIVT3G_PaymentRule::getSingletonInstance();
        $arrErr = array();
        $objIF = new SC_If_SBIVT3G_OrderDataMainte($arrValues);
        $this->setIF($objIF);

        $srcPayStatus = $objIF->getSrcPayStatus();
        $srcInnerPayment = $objIF->srcInnerPayment;
        $dstInnerPayment = $objIF->dstInnerPayment;

        if ($objIF->isChangePayment() == true) {
            // 支払い方法の変更は有効か？
            $srcJudge = $objRule->isRemovablePayment($srcInnerPayment,
                $srcPayStatus);
            $dstJudge = $objRule->isRenewablePayment($dstInnerPayment);
            if ($srcJudge == false) {
                $arrErr['payment_id'] =
                    '※現在のお支払い方法からは更新できません<br/>';
            } else if ($dstJudge == false) {
                $arrErr['payment_id'] =
                    //'※そのお支払い方法へは更新できません<br/>';
                    '※そのお支払方法は選択できません<br/>';
            }
            if (strcmp($dstInnerPayment, '') != 0
            && $arrValues['payment_total'] == 0) {
                $arrErr['payment_id'] = '※そのお支払い方法ではお支払い'
                    . '合計0円への変更はできません。<br />';
            }

            if ($objIF->isDstCredit() == true) {
                // カード情報エラーチェック
                $arrCardErr = $this->checkCardInput($arrValues);
                $arrErr = array_merge($arrErr, $arrCardErr);

            } else if ($objIF->isDstCvs() == true) {
                // コンビニ情報エラーチェック
                $arrCvsErr = $this->checkCvsInput($arrValues);
                $arrErr = array_merge($arrErr, $arrCvsErr);
            }
        } else if ($objIF->isChangeTotal() == true) {
            if (strcmp($dstInnerPayment, '') != 0) {
                $srcJudge = $objRule->isRemovablePayment($srcInnerPayment,
                    $srcPayStatus);
                $dstJudge = $objRule->isRenewablePayment($dstInnerPayment);
                if ($srcJudge == false) {
                    $arrErr['payment_id'] =
                        '※現在のお支払い方法からは更新できません<br/>';
                } else if ($dstJudge == false) {
                    $arrErr['payment_id'] = '※お支払い合計が変更されています'
                        . '(現在のお支払い方法では金額の変更はできません。)'
                        . '<br />';
                } else if ($arrValues['payment_total'] == 0) {
                    $arrErr['payment_id'] = '※現在のお支払い方法ではお支払い'
                        . '合計0円への変更はできません。<br />';
                }
            }
            if ($objIF->isDstCredit() == true) {
                // カード情報エラーチェック
                $arrCardErr = $this->checkCardInput($arrValues);
                $arrErr = array_merge($arrErr, $arrCardErr);

            } else if ($objIF->isDstCvs() == true) {
                // コンビニ情報エラーチェック
                $arrCvsErr = $this->checkCvsInput($arrValues);
                $arrErr = array_merge($arrErr, $arrCvsErr);
            }
        }
        return $arrErr;
    }

    /**
     * 管理画面->受注編集での受注情報の更新前処理
     *
     * @access public
     * @param integer $order_id 受注ID
     * @param SC_Helper_Purchase $objPurchase SC_Helper_Purchase インスタンス
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param string $message 通知メッセージ
     * @return boolean 成功：失敗
     * @see LC_Page_Admin_Order_Edit_Ex::doRegister()
     */
    function preDoRegister($order_id, &$objPurchase, &$objFormParam, &$message) {
        $logger =& TGMDK_Logger::getInstance();
        $objRule =& SC_Helper_SBIVT3G_PaymentRule::getSingletonInstance();


        $objIF = $this->getIF();
        if (get_class($objIF) != 'SC_If_SBIVT3G_OrderDataMainte') {
            $arrDbArray = $objFormParam->getDbArray();
            $objIF = new SC_If_SBIVT3G_OrderDataMainte($arrDbArray);
            $this->setIF($objIF);
        }

        // 現状の受注情報を取得
        $srcPaymentId = $objIF->getSrcPaymentId();
        $srcPayStatus = $objIF->getSrcPayStatus();
        $srcInnerPayment = $objIF->srcInnerPayment;
        $dstPayStatus = $objIF->getdstPayStatus();
        $dstInnerPayment = $objIF->dstInnerPayment;

        // 決済ステータスの更新を整理
        $dstPayStatus = $objFormParam->getValue('memo01');
        if (strcmp($dstPayStatus, '') == 0) {
            $dstPayStatus = $srcPayStatus;
            $objFormParam->setValue('memo01', $srcPayStatus);
        }

        // 決済ログ準備
        $paymentLog = $objFormParam->getValue('memo03');
        if (strcmp($paymentLog, '') != 0 && substr($paymentLog, -1) != LF) {
            $paymentLog .= LF;
        }

        // 条件判別
        $doNothing     = 0;
        $doDownCapture = 1;
        $doDownCancel  = 2;
        $doPaymentChg  = 3;
        $doPayStatChg  = 4;
        $doIs = $doNothing;
        if ($objIF->isDoDownPriceCapture() == true) {
            $doIs = $doDownCapture;
        } else if ($objIF->isDoDownPriceCancel() == true) {
            $doIs = $doDownCancel;
        } else if ($objIF->isChangePayment() == true
        || $objIF->isChangeTotal() == true) {
            $doIs = $doPaymentChg;
        } else if ($objIF->isChangePayStatus() == true) {
            $doIs = $doPayStatChg;
        }

        // ケースごとの処理
        switch ($doIs) {
        case $doPaymentChg : // お支払い方法変更(または新規入力)

            // 実行タイムアウト再設定
            set_time_limit(MDL_SBIVT3G_STANDARD_EXECUTE_LIMIT);

            // 現行決済情報の取消・返金
            $logger->info('現行決済の消去');
            $bol = $objRule->removePayment($srcInnerPayment,
                $srcPayStatus, $objIF->arrSrcOrder, $arrBeforeRes);
            // エラーハンドラ復元
            GC_Utils_SBIVT3G::restoreErrorHandler();

            // 失敗なら終了
            if ($bol == false) {
                $logger->fatal('現行決済消去の失敗');
                $message = $arrBeforeRes['message'];
                $message .= ' 旧決済の取消に失敗しました';
                $objFormParam->setValue('payment_id', $srcPaymentId);
                return false;
            }
            // 成功なら中間更新
            if (isset($arrBeforeRes['orderId']) == true) {
                $paymentLog .= GC_Utils_SBIVT3G::putPaymentLogString(
                    $srcInnerPayment,
                    $arrBeforeRes,
                    '成功' . $arrBeforeRes['other']
                );
                $objFormParam->setValue('memo01', $arrBeforeRes['payStatus']);
                $objFormParam->setValue('memo03', $paymentLog);
                $objFormParam->setValue('memo04', $arrBeforeRes['orderId']);
                $objFormParam->setValue('memo05', serialize($arrBeforeRes));
                if ($objFormParam->getValue('memo01')
                    == MDL_SBIVT3G_STATUS_REFUND
                || ($objFormParam->getValue('memo01')
                    == MDL_SBIVT3G_STATUS_CANCEL
                    && $srcInnerPayment == MDL_SBIVT3G_INNER_ID_CUP)) {
                    // 返金なら返金処理した取引IDを保持
                    $objFormParam->setValue('memo07', $arrBeforeRes['orderId']);
                    // 更にWaon決済なら返金用URLを保持
                    if ($srcInnerPayment == MDL_SBIVT3G_INNER_ID_WAON_MOBILE_APP
                    || $srcInnerPayment == MDL_SBIVT3G_INNER_ID_WAON_PC_APP) {
                        $objFormParam->setValue('memo08',
                            $arrBeforeRes['appUrl']);
                    }
                }
                $objQuery =& SC_Query_Ex::getSingletonInstance();
                $objQuery->update('dtb_order',
                    array('memo01' => $objFormParam->getValue('memo01'),
                        'memo03' => $objFormParam->getValue('memo03'),
                        'memo04' => $objFormParam->getValue('memo04'),
                        'memo05' => $objFormParam->getValue('memo05'),
                        'memo07' => $objFormParam->getValue('memo07'),
                        'memo08' => $objFormParam->getValue('memo08'),
                        'update_date' => GC_Utils_SBIVT3G::getNowExpression()),
                    'order_id = ?', array($order_id)
                );
            }

            // 実行タイムアウト再設定
            set_time_limit(MDL_SBIVT3G_STANDARD_EXECUTE_LIMIT);

            // 新規で決済情報を実行
            $logger->info('新規決済の実行');
            $arrForm = $objFormParam->getDbArray();
            $bol = $objRule->renewPayment($dstInnerPayment, $arrForm,
                $arrAfterRes);
            // エラーハンドラ復元
            GC_Utils_SBIVT3G::restoreErrorHandler();

            if ($bol == false) {
                $logger->fatal('新規決済の失敗');
                $objFormParam->setValue('payment_id', $srcPaymentId);
                $message = $arrAfterRes['message'];
                $message .= ' 新決済の登録に失敗しました';
                return false;
            }
            if (isset($arrAfterRes['orderId']) == true) {
                // レコードの更新
                $objFormParam->setValue('memo01', $arrAfterRes['payStatus']);
                $paymentLog .= GC_Utils_SBIVT3G::putPaymentLogString(
                    $dstInnerPayment,
                    $arrAfterRes,
                    '成功' . $arrAfterRes['other']
                );
                $objFormParam->setValue('memo02',
                    (isset($arrAfterRes['memo02']))?
                    $arrAfterRes['memo02'] : ''
                );
                $objFormParam->setValue('status', $arrAfterRes['status']);
                $objFormParam->setValue('memo03', $paymentLog);
                $objFormParam->setValue('memo04', $arrAfterRes['orderId']);
                $objFormParam->setValue('memo05', serialize($arrAfterRes));
                $objFormParam->setValue('memo06',
                    (isset($arrAfterRes['reqCardNo']))?
                    $arrAfterRes['reqCardNo'] : ''
                );
                $objFormParam->setValue('status', $arrAfterRes['status']);
            } else {
                // SBI以外の実行なので決済ステータスを初期化
                $objFormParam->setValue('memo01', '');
                $objFormParam->setValue('memo02', '');
                $objFormParam->setValue('memo04', '');
                $objFormParam->setValue('memo05', '');
                $objFormParam->setValue('memo06', '');
            }
            break;

        case $doPayStatChg : // 同一決済のステータス更新
        case $doDownCapture : // クレジットカード決済 減額売上請求
        case $doDownCancel : // クレジットカード決済 部分取消

            // 実行タイムアウト再設定
            set_time_limit(MDL_SBIVT3G_STANDARD_EXECUTE_LIMIT);

            $logger->info('決済ステータスの更新');
            $arrForm = $objFormParam->getDbArray();
            $arrForm['memo01'] = $srcPayStatus;
            if ($doIs ==  $doDownCancel) {
                // 部分取消の金額は差分を指定する
                $arrForm['payment_total'] = $objIF->getSrcPaymentTotal()
                    - $objIF->getDstPaymentTotal();
            }
            $bol = $objRule->modifyPayStatus($dstInnerPayment,
                $dstPayStatus, $arrForm, $arrModStatRes);
            // エラーハンドラ復元
            GC_Utils_SBIVT3G::restoreErrorHandler();

            if ($bol == false) {
                $logger->fatal('決済ステータス更新の失敗');
                $message = $arrModStatRes['message'];
                $objFormParam->setValue('memo01', '');
                $message .=  ' 決済ステータスの更新に失敗しました';
                return false;
            }
            // レコードの更新
            $paymentLog .= GC_Utils_SBIVT3G::putPaymentLogString(
                $dstInnerPayment,
                $arrModStatRes,
                '成功' . $arrModStatRes['other']
            );
            $objFormParam->setValue('memo03', $paymentLog);
            $objFormParam->setValue('memo04', $arrModStatRes['orderId']);
            $objFormParam->setValue('memo05', serialize($arrModStatRes));
            if ($objFormParam->getValue('memo01') == MDL_SBIVT3G_STATUS_REFUND
            || ($objFormParam->getValue('memo01') == MDL_SBIVT3G_STATUS_CANCEL
                && $srcInnerPayment == MDL_SBIVT3G_INNER_ID_CUP)) {
                // 返金なら返金処理した取引IDを保持
                $objFormParam->setValue('memo07', $arrModStatRes['orderId']);
                // 更にWaon決済なら返金用URLを保持
                if ($srcInnerPayment == MDL_SBIVT3G_INNER_ID_WAON_MOBILE_APP
                || $srcInnerPayment == MDL_SBIVT3G_INNER_ID_WAON_PC_APP) {
                    $objFormParam->setValue('memo08', $arrModStatRes['appUrl']);
                }
            }
            if ($doIs ==  $doDownCancel) {
                // 部分取消の場合はステータス更新しない
                $objFormParam->setValue('memo01', $srcPayStatus);
            }
            break;

        default : // 何もしない
            break;
        }
        return true;
    }

    /**
     * ステータス更新を行う
     *
     * @access public
     * @param array $arrForm  受注情報フォーム
     * @param string $message メッセージ
     * @return void
     * @see LC_Page_Admin_Order_Edit_Ex::action()
     */
    function refreshStatus(&$arrForm, &$message) {
        $logger =& TGMDK_Logger::getInstance();
        $objRule =& SC_Helper_SBIVT3G_PaymentRule::getSingletonInstance();

        // 実行タイムアウト再設定
        set_time_limit(MDL_SBIVT3G_STANDARD_EXECUTE_LIMIT);

        // 検索
        $orderId = $arrForm['memo04']['value'];
        $rtn = $objRule->searchOrderInfo($orderId, $objOrderInfo);

        // エラーハンドラ復元
        GC_Utils_SBIVT3G::restoreErrorHandler();

        // 結果判定
        if ($rtn < 0) {
            $message = '決済ステータス取得に失敗しました';
            return;
        } else if ($rtn == 0) {
            $message = '該当する決済は存在しませんでした';
            return;
        }

        // ステータスをモジュールのものに読み替える
        $status = $objRule->extractPayStatus($objOrderInfo);
        if (strcmp($status, '') == 0) {
            $message = '現在の決済ステータスは不明です';
            return;
        }

        // 同じであれば更新不要
        if (strcmp($status, $arrForm['memo01']['value']) == 0) {
            $message = '決済ステータスに差異はありません';
            return;
        }

        // 更新処理
        $paymentLog = $arrForm['memo03']['value'];
        if (strcmp($paymentLog, '') != 0 && substr($paymentLog, -1) != LF) {
            $paymentLog .= LF;
        }
        $paymentLog .= GC_Utils_SBIVT3G::putPaymentLogString(
            GC_Utils_SBIVT3G::getInnerPayment($arrForm['payment_id']['value']),
            array('orderId' => $orderId, 'payStatus' => $status),
            '最新状態に更新'
        );
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->update('dtb_order',
            array(
                'memo01' => $status,
                'memo03' => $paymentLog,
                'update_date' => GC_Utils_SBIVT3G::getNowExpression(),
            ),
            'order_id = ?', array($arrForm['order_id']['value'])
        );
        $arrForm['memo01']['value'] = $status;
        $arrForm['memo03']['value'] = $paymentLog;

        $message = 'ステータス更新に成功しました';
    }
}
