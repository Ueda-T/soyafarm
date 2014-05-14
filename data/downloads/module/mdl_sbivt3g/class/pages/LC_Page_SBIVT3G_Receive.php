<?php
/**
 * LC_Page_SBIVT3G_Receive.php - LC_Page_SBIVT3G_Receive クラスを定義
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: LC_Page_SBIVT3G_Receive.php 199 2013-08-23 01:18:56Z kaji $
 * @link        http://www.veritrans.co.jp/3gps
*/


require_once MDL_SBIVT3G_PAGE_PATH . 'LC_Page_SBIVT3G.php';

/**
 * 3Gモジュール 入金通知処理クラス
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
class LC_Page_SBIVT3G_Receive extends LC_Page_SBIVT3G {

    // {{{ properties

    /** エラー格納配列 */
    var $arrErrors;

    /** キャリア用 エラー格納配列 */
    var $arrCarrierErrors;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @access public
     * @return void
     */
    function init() {
        // エラー処理初期化
        restore_error_handler();

        // 出力を抑制する
        ob_start();

        // 親処理実行
        parent::init();

        $this->arrErrors = array();
        $this->arrCarrierErrors = array();
    }

    /**
     * Page のプロセス.
     *
     * @access public
     * @return void
     */
    function process() {
        // sendResponse()は実行しない
        $this->action();
    }

    /**
     * デストラクタ.
     *
     * @access public
     * @return void
     */
    function destroy() {
        // 親処理実行
        parent::destroy();

        // 出力があればログへ逃す
        $buffer = ob_get_contents();
        if (strcmp($buffer, '') != 0) {
            $logger =& TGMDK_Logger::getInstance();
            $logger->info('入金通知 - 標準出力:' .LF. $buffer);
        }

        // バッファを開放
        ob_end_clean();
    }

    /**
     * POST アクセスの妥当性を検証する.
     *
     * @access protected
     * @param boolean $is_admin 管理画面でエラー表示をする場合 true
     * @return void
     * @see LC_Page::doValidToken()
     */
    function doValidToken($is_admin = false) {
        // チェックなし
        return; 
    }

    /**
     * Page のアクション.
     *
     * @access public
     * @return void
     */
    function action() {

        $logger =& TGMDK_Logger::getInstance();
        $logger->info('入金通知処理 開始');

        // 初期化
        $arrRecords = array();
        $numberOfNotify = null;
        $pushTime  = null;
        $pushId  = null;
        $fixed  = null;

        // POST値をパースする
        foreach ($_POST as $_key => $_value) {
            $key   = htmlspecialchars($_key);
            $value = htmlspecialchars($_value);

            //// ヘッダ情報
            // 通知件数
            if (strcmp($key, 'numberOfNotify') == 0) {
                $numberOfNotify = $value;
            }
            // 送信時刻
            if (strcmp($key, 'pushTime') == 0) {
                $pushTime = $value;
            }
            // 識別ID
            if (strcmp($key, 'pushId') == 0) {
                $pushId = $value;
            }
            // 速報・確報フラグ
            if (strcmp($key, 'fixed') == 0) {
                $fixed = $value;
            }

            //// 明細情報
            // フィールド名と連番を取得する
            if (preg_match('/^([^0-9]+)([0-9]{4})$/', $key, $m) == false) {
                continue;
            }
            $arrRecords[$m[2]][$m[1]] = $value; 
        }

        // ここまでで正常な通信かを確認
        if ($numberOfNotify == null || $pushTime == null || $pushId == null) {
            $logger->warn('例外データ受信 終了');
            return;
        }
        $headerInfo = LF
            . ' > 通知件数: ' . $numberOfNotify . LF
            . ' > 送信時刻: ' . $pushTime . LF
            . ' > 識別ID: ' .  $pushId . LF
            . ' > 速報・確報フラグ: ' .  $fixed . LF
            ;
        $logger->info($headerInfo);

        // パースした項目を判定(おそらくレスポンス毎で同一決済と思われるが...)
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $cnt = 0;
        foreach ($arrRecords as $record) {
            // 対象明細をロギング 
            $cnt++;
            if (isset($record['openId'])) {
                $record['openId'] = '*****';
            }
            $logger->info('対象明細['.$cnt.'件目]:' . print_r($record, true));

            if ($this->isCvs($record) == true) {
                // コンビニ決済処理
                $objQuery->begin();
                if ($this->doCvs($record) == false) {
                    $objQuery->rollback();
                    continue;
                }
                $objQuery->commit();

            } else if ($this->isEm($record) == true) {
                // 電子マネー決済処理
                $objQuery->begin();
                if ($this->doEm($record) == false) {
                    $objQuery->rollback();
                    continue;
                }
                $objQuery->commit();

            } else if ($this->isPayeasy($record) == true) {
                // 銀行決済処理
                $objQuery->begin();
                if ($this->doPayeasy($record) == false) {
                    $objQuery->rollback();
                    continue;
                }
                $objQuery->commit();

            } else if ($this->isCup($record) == true) {
                // 銀聯ネット決済処理
                $objQuery->begin();
                switch ($record['txnType']) {
                case 'cancel_authorize' : // 取消
                case 'refund' : // 返金
                    // 取消・返金通知
                    $bol = $this->doCupOfRefund($record, $fixed);
                    break;
                case 'capture' : // 売上
                case 'verify_capture' : 
                default :
                    // 売上(申込)
                    $bol = $this->doCup($record, $fixed);
                    break;
                }
                if ($bol == false) {
                    $objQuery->rollback();
                    continue;
                }
                $objQuery->commit();

            } else if ($this->isPaypal($record) == true) {
                // PayPal決済処理
                $objQuery->begin();
                switch ($record['txnType']) {
                case 'Refund' : // 返金
                    // 返金通知
                    $bol = $this->doPayPalOfRefund($record, $fixed);
                    break;
                case 'Capture' : // 売上
                default :
                    // 売上(申込)
                    $bol = $this->doPayPal($record, $fixed);
                    break;
                }
                if ($bol == false) {
                    $objQuery->rollback();
                    continue;
                }
                $objQuery->commit();

            } else if ($this->isCarrier($record) == true) {
                // キャリア決済処理

                // トランザクションタイプが決済申込完了の場合のみ処理する
                if (strcmp($record['txnType'], 'Authorize') != 0) {
                    continue;
                }

                // 決済申込完了取消通知(WGU3：決済後エラー)に対応する
                if (strcmp($record['vResultCode'], 'WGU3') == 0) {
                    $objQuery->begin();

                    if ($this->doCarrierCancel($record) == false) {
                        $objQuery->rollback();
                        continue;
                    }

                    $objQuery->commit();
                    continue;
                } else if (strcmp($record['vResultCode'], 'W001') != 0 &&
                           strcmp($record['vResultCode'], 'W003') != 0) {
                    // 詳細結果コードが W001 or W003 の場合のみ処理する
                    continue;
                }

                $objQuery->begin();

                if ($this->doCarrier($record) == false) {
                    $objQuery->rollback();
                    continue;
                }

                $objQuery->commit();
            }
        }

        // エラーがあればメール通知を行う 
        if (count($this->arrErrors) > 0) {
            $this->sendErrorMail($this->arrErrors, $pushTime, $pushId);
        }

        // キャリアのエラーがあればメール通知を行う 
        if (count($this->arrCarrierErrors) > 0) {
            $this->sendErrorMailForCarrier
                ($this->arrCarrierErrors, $pushTime, $pushId);
        }

        $logger->info('入金通知処理 終了');
    }

    /**
     * エラー報知メールを店舗管理者へ送信
     *
     * @access protected
     * @param array $arrErrors エラーレコード
     * @param string $pushTime 3Gからの送信時刻
     * @param string $pushId 識別ID
     * @return void
     */
    function sendErrorMail($arrErrors, $pushTime, $pushId) {
        $logger =& TGMDK_Logger::getInstance();

        // 基本情報取得
        $objDB = new SC_Helper_DB_Ex();
        $arrBasis = $objDB->sfGetBasisData();

        // エラーコードを展開
        $errors = implode(LF, $arrErrors);

        // 本文
        $myName = MDL_SBIVT3G_MODULE_NAME;
        $pushDateTime = sprintf('%04d/%02d/%02d %02d:%02d',
            substr($pushTime,  0, 4),
            substr($pushTime,  4, 2),
            substr($pushTime,  6, 2),
            substr($pushTime,  8, 2),
            substr($pushTime, 10, 2)
        );
        $content = <<<EOD
「$myName - 入金通知」で以下のエラーが発生しました。
受信日時 : $pushDateTime
  識別ID : $pushId

＜エラーレコード＞
$errors

該当の受注情報をご確認下さい。
※支払方法や決済金額の変更を行った場合、3G 取引IDは受注番号と合致しない可能性
があります。

EOD;
        $logger->debug('エラー報知メール本文 >' .LF. $content);

        // メール送信クラス生成
        $objMail = new SC_SendMail_Ex();
        $objMail->setItem(
            '', // to 別途指定
            $myName . '- 入金通知エラー報知メール', // subject
            $content, // body
            $arrBasis['email03'], // fromaddress
            $myName . '- 自動送信', // from_name
            '', // reply_to
            $arrBasis['email04'], // return_path
            $arrBasis['email04']  // errors_to
        );
        $objMail->setTo($arrBasis['email01'], $arrBasis['shop_name']);
        $objMail->sendMail();

        $logger->info('入金通知エラー報知メール送信');
    }

    /**
     * キャリア用 エラー報知メールを店舗管理者へ送信
     *
     * @access protected
     * @param array $arrCarrierErrors エラーレコード
     * @param string $pushTime 3Gからの送信時刻
     * @param string $pushId 識別ID
     * @return void
     */
    function sendErrorMailForCarrier($arrCarrierErrors, $pushTime, $pushId) {
        $logger =& TGMDK_Logger::getInstance();

        // 基本情報取得
        $objDB = new SC_Helper_DB_Ex();
        $arrBasis = $objDB->sfGetBasisData();

        // エラーコードを展開
        $errors = implode(LF, $arrCarrierErrors);

        // 本文
        $myName = MDL_SBIVT3G_MODULE_NAME;
        $pushDateTime = sprintf('%04d/%02d/%02d %02d:%02d',
            substr($pushTime,  0, 4),
            substr($pushTime,  4, 2),
            substr($pushTime,  6, 2),
            substr($pushTime,  8, 2),
            substr($pushTime, 10, 2)
        );
        $content = <<<EOD
「$myName - 結果通知」で以下のエラーが発生しました。
受信日時 : $pushDateTime
  識別ID : $pushId

＜エラーレコード＞
$errors

該当の受注情報をご確認下さい。

EOD;
        $logger->debug('キャリア用エラー報知メール本文 >' .LF. $content);

        // メール送信クラス生成
        $objMail = new SC_SendMail_Ex();
        $objMail->setItem(
            '', // to 別途指定
            $myName . '- 結果通知エラー報知メール', // subject
            $content, // body
            $arrBasis['email03'], // fromaddress
            $myName . '- 自動送信', // from_name
            '', // reply_to
            $arrBasis['email04'], // return_path
            $arrBasis['email04']  // errors_to
        );
        $objMail->setTo($arrBasis['email01'], $arrBasis['shop_name']);
        $objMail->sendMail();

        $logger->info('キャリア結果通知エラー報知メール送信');
    }

    /**
     * コンビニ決済レコードか判定
     *
     * @access protected
     * @param array $arrRecord 明細レコード
     * @return boolean 正否
     */
    function isCvs($arrRecord) {
        $arrFormat = array(
            'orderId',     // 取引ID
            'cvsType',     // CVSタイプ
            'receiptNo',   // 受付番号
            'receiptDate', // 完了日時
            'rcvAmount',   // 入金金額
            'dummy',       // ダミー決済フラグ
        );

        // 各フィールドが存在するか？
        foreach ($arrFormat as $field) {
            if (isset($arrRecord[$field]) == false) {
                return false;
            }
        }
        return true;
    }

    /**
     * 電子マネー決済レコードか判定
     *
     * @access protected
     * @param array $arrRecord 明細レコード
     * @return boolean 正否
     */
    function isEm($arrRecord) {
        $arrFormat = array(
            'orderId',     // 取引ID
            'emType',      // EMタイプ
            'receiptDate', // 完了日時
            'dummy',       // ダミー決済フラグ
        );

        // 各フィールドが存在するか？
        foreach ($arrFormat as $field) {
            if (isset($arrRecord[$field]) == false) {
                return false;
            }
        }
        return true;
    }

    /**
     * 銀行決済レコードか判定
     *
     * @access protected
     * @param array $arrRecord 明細レコード
     * @return boolean 正否
     */
    function isPayeasy($arrRecord) {
        $arrFormat = array(
            'orderId',     // 取引ID
            'kikanNo',     // 収納機関コード
            'kigyoNo',     // 収納企業コード
            'rcvDate',     // 収納日時
            'customerNo',  // お客様番号
            'confNo',      // 確認番号
            'rcvAmount',   // 入金金額
            'dummy',       // ダミー決済フラグ
        );

        // 各フィールドが存在するか？
        foreach ($arrFormat as $field) {
            if (isset($arrRecord[$field]) == false) {
                return false;
            }
        }
        return true;
    }

    /**
     * 銀聯ネット決済レコードか判定
     *
     * @access protected
     * @param array $arrRecord 明細レコード
     * @return boolean 正否
     */
    function isCup($arrRecord) {
        $arrFormat = array(
            'orderId',          // 取引ID
            'txnType',          // トランザクションタイプ
            'amount',           // 取引金額
            'issBin',           // イシュア銀行コード
            'settleDate',       // 清算日付
            'settleRate',       // 清算レート
            'traceNum',         // システム追跡番号
            'systemDatetimeCn', // 認証サービス日時
            'dummy',            // ダミー決済フラグ
        );

        // 各フィールドが存在するか？
        foreach ($arrFormat as $field) {
            if (isset($arrRecord[$field]) == false) {
                return false;
            }
        }
        return true;
    }

    /**
     * PayPal決済レコードか判定
     *
     * @access protected
     * @param array $arrRecord 明細レコード
     * @return boolean 正否
     */
    function isPayPal($arrRecord) {
        $arrFormat = array(
            'orderId',          // 取引ID
            'txnType',          // トランザクションタイプ
            'receivedDatetime', // 受付日時
            'amount',           // 金額
            'payerId',          // お客様番号
            'centerTxnId',      // 取引識別子
            'dummy',            // ダミー決済フラグ
        );

        // 各フィールドが存在するか？
        foreach ($arrFormat as $field) {
            if (isset($arrRecord[$field]) == false) {
                return false;
            }
        }
        return true;
    }

    /**
     * キャリア決済レコードか判定
     *
     * @access protected
     * @param array $arrRecord 明細レコード
     * @return boolean 正否
     */
    function isCarrier($arrRecord) {
        $arrFormat = array(
            'orderId',          // 取引ID
            'txnType',          // トランザクションタイプ
            'txnTime',          // 処理日時
            'vResultCode',      // 詳細結果コード
            'mstatus',          // 処理ステータス
            'dummy',            // ダミー決済フラグ
        );

        // 各フィールドが存在するか？
        foreach ($arrFormat as $field) {
            if (isset($arrRecord[$field]) == false) {
                return false;
            }
        }
        return true;
    }

    /**
     * 「申込」象の受注番号を取得
     *
     * @access protected
     * @param string $paymentOrderId 決済を実施した取引ID
     * @param bool $forUpdate FOR UPDTE句を追加するかどうか
     * @return string 受注番号
     */
    function getOrderIdOfRequestStatus($paymentOrderId, $forUpdate = false) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql = <<<EOD
SELECT order_id
FROM dtb_order
WHERE memo04 = ?
ORDER BY order_id ASC LIMIT 1
EOD;
        if ($forUpdate) {
            $sql .= ' FOR UPDATE';
        }
        $res = $objQuery->getOne($sql, array($paymentOrderId));
        return $res;
    }

    /**
     * 「返金」対象の受注番号を取得
     *
     * @access protected
     * @param string $paymentOrderId 決済を実施した取引ID
     * @return string 受注番号
     */
    function getOrderIdOfRefundStatus($paymentOrderId) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql = <<<EOD
SELECT order_id
FROM dtb_order
WHERE memo07 = ?
ORDER BY order_id ASC LIMIT 1
EOD;
        $res = $objQuery->getOne($sql, array($paymentOrderId, $innerPayment));
        return $res;
    }

    /**
     * エラーメッセージ作成
     *
     * @access protected
     * @param string $payment 決済方法
     * @param integer $paymentOrderId 取引ID
     * @param integer $paymentTotal   決済金額
     * @param integer $errorno        エラー番号
     * @return string 受注番号
     */
    function putError($payment, $paymentOrderId, $paymentTotal, $errorno = 1) {
        // エラー文言
        switch ($errorno) {
        case 1 :
        default :
            $errorString = '該当受注番号の不在';
            break;
        }

        $error = <<<EOD
 3G 取引ID : $paymentOrderId
  支払方法 : $payment
  決済金額 : $paymentTotal
エラー原因 : $errorString

EOD;
        $this->arrErrors[] = $error;
    }

    /**
     * キャリア用エラーメッセージ作成
     *
     * @access protected
     * @param string $payment 決済方法
     * @param integer $paymentOrderId 取引ID
     * @param integer $errorno        エラー番号
     * @return string 受注番号
     */
    function putCarrierError($payment, $paymentOrderId, $errorno = '') {
        // エラー文言
        switch ($errorno) {
        case 'WGU3':
            $errorString = GC_Utils_SBIVT3G::getCarrierErrorMessage($errorno);
            break;

        default:
            $errorString = '該当受注番号の不在';
            break;
        }

        $error = <<<EOD
 3G 取引ID : $paymentOrderId
  支払方法 : $payment
エラー原因 : $errorString

EOD;
        $this->arrCarrierErrors[] = $error;
    }

    /**
     * コンビニ決済レコードを処理
     *
     * @access protected
     * @param array $arrRecord 明細レコード
     * @return boolean 正否
     */
    function doCvs($arrRecord) {
        $logger =& TGMDK_Logger::getInstance();
        $logger->info('入金通知 - コンビニ決済処理 開始');

        // 対象の受注番号を取得
        $orderId = $this->getOrderIdOfRequestStatus($arrRecord['orderId']);
        if ($orderId == null) {
            $logger->warn('該当する受注番号なし');
            $this->putError('コンビニ決済', $arrRecord['orderId'],
                number_format($arrRecord['rcvAmount']));
            return false;
        }

        // 対象受注を取得
        $objPurchase = new SC_Helper_Purchase_Ex();
        $arrOrder = $objPurchase->getOrder($orderId);
        $arrRes = unserialize($arrOrder['memo05']);

        // 値をパース
        $payStatus = MDL_SBIVT3G_STATUS_DEPOSIT;
        $status = ORDER_PRE_END;
        $paymentDate = $this->toDate($arrRecord['receiptDate']);
        $other = sprintf(' 入金金額[%s]',
            number_format($arrRecord['rcvAmount']));

        // 現在の支払方法検証
        $innerPayment =
            GC_Utils_SBIVT3G::getInnerPayment($arrOrder['payment_id']);
        if (strcmp($innerPayment, MDL_SBIVT3G_INNER_ID_CVS) != 0) {
            $payStatus = $arrOrder['memo01'];
            $status = $arrOrder['status'];
            $paymentDate = $arrOrder['payment_date'];
            $other .= ' 現在の支払方法と異なります(更新中止)';
        }

        // 決済ステータス検証
        if ($arrOrder['memo01'] != MDL_SBIVT3G_STATUS_REQUEST) {
            $payStatus = $arrOrder['memo01'];
            $paymentDate = $arrOrder['payment_date'];
            $other .= ' 3G決済ステータス不整合(更新中止)';
        }

        // 受注ステータス検証
        if ($arrOrder['status'] != ORDER_PAY_WAIT) {
            $status = $arrOrder['status'];
            $other .= ' 対応状況不整合(更新中止)';
        }

        // 決済ログ生成
        $paymentLog = $arrOrder['memo03'];
        $paymentLog .= ((substr($paymentLog, -1) != LF)? LF : '');
        $paymentLog .= GC_Utils_SBIVT3G::putPaymentLogString(
            MDL_SBIVT3G_INNER_ID_CVS,
            array(
                'orderId' => $arrRecord['orderId'],
                'payStatus'=>MDL_SBIVT3G_STATUS_DEPOSIT
            ),
            '入金通知受信' . $other
        );

        // 更新
        $arrModifies = array(
            'status' => $status,
            'memo01' => $payStatus,
            'memo03' => $paymentLog,
            'payment_date'=> $paymentDate,
            'update_date' => GC_Utils_SBIVT3G::getNowExpression(),
        );
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->update('dtb_order',
            $arrModifies,
            ' order_id = ? ', array($orderId)
        );
        $logger->info(sprintf('受注データ更新 受注番号:%d', $orderId));

        $logger->info('入金通知 - コンビニ決済処理 終了');
        return true;
    }

    /**
     * 電子マネー決済レコードを処理
     *
     * @access protected
     * @param array $arrRecord 明細レコード
     * @return boolean 正否
     */
    function doEm($arrRecord) {
        $logger =& TGMDK_Logger::getInstance();
        $logger->info('入金通知 - 電子マネー決済処理 開始');

        // まず「返金」対象の受注番号かを取得
        $refundId = $this->getOrderIdOfRefundStatus($arrRecord['orderId']);
        if ($refundId != null) {
            // 返金通知処理
            return $this->doEmOfRefund($arrRecord, $refundId);
        }

        // 無いなら「申込」対象の受注番号を取得
        $orderId = $this->getOrderIdOfRequestStatus($arrRecord['orderId']);
        if ($orderId == null) {
            $logger->warn('該当する受注番号なし');
            $this->putError('電子マネー決済',$arrRecord['orderId'],'不明');
            return false;
        }

        // 対象受注を取得
        $objPurchase = new SC_Helper_Purchase_Ex();
        $arrOrder = $objPurchase->getOrder($orderId);
        $arrRes = unserialize($arrOrder['memo05']);

        // 値をパース
        $payStatus = MDL_SBIVT3G_STATUS_DEPOSIT;
        $status = ORDER_PRE_END;
        $paymentDate = $this->toDate($arrRecord['receiptDate']);
        $other = '';

        // 現在の支払方法検証
        $innerPayment =
            GC_Utils_SBIVT3G::getInnerPayment($arrOrder['payment_id']);
        $bol = false;
        $fixInnerPay = '';
        switch ($innerPayment) {
        case MDL_SBIVT3G_INNER_ID_EDY_MOBILE_MAIL :
        case MDL_SBIVT3G_INNER_ID_EDY_PC_APP :
            if ($arrRecord['emType'] == 'edy') {
                $fixInnerPay = $innerPayment;
                $bol = true;
            }
            break;
        case MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_MAIL :
        case MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_APP :
        case MDL_SBIVT3G_INNER_ID_SUICA_PC_MAIL :
        case MDL_SBIVT3G_INNER_ID_SUICA_PC_APP :
            if ($arrRecord['emType'] == 'suica') {
                $fixInnerPay = $innerPayment;
                $bol = true;
            }
            break;
        case MDL_SBIVT3G_INNER_ID_WAON_MOBILE_APP :
        case MDL_SBIVT3G_INNER_ID_WAON_PC_APP :
            if ($arrRecord['emType'] == 'waon') {
                $fixInnerPay = $innerPayment;
                $bol = true;
            }
            break;
        default :
            break;
        }
        if ($bol == false) {
            $other .= ' 現在の支払方法と異なります(更新中止)';
            $fixInnerPay = $arrRecord['emType']; // 代替表示
            $payStatus = $arrOrder['memo01'];
            $status = $arrOrder['status'];
            $paymentDate = $arrOrder['payment_date'];
        }

        // 決済ステータス検証
        if ($arrOrder['memo01'] != MDL_SBIVT3G_STATUS_REQUEST) {
            $payStatus = $arrOrder['memo01'];
            $paymentDate = $arrOrder['payment_date'];
            $other .= ' 3G決済ステータス不整合(更新中止)';
        }

        // 受注ステータス検証
        if ($arrOrder['status'] != ORDER_PAY_WAIT) {
            $status = $arrOrder['status'];
            $other .= ' 対応状況不整合(更新中止)';
        }

        // 決済ログ生成
        $paymentLog = $arrOrder['memo03'];
        $paymentLog .= ((substr($paymentLog, -1) != LF)? LF : '');
        $paymentLog .= GC_Utils_SBIVT3G::putPaymentLogString(
            $fixInnerPay,
            array(
                'orderId' => $arrRecord['orderId'],
                'payStatus'=>MDL_SBIVT3G_STATUS_DEPOSIT
            ),
            '入金通知受信' . $other
        );

        // 更新
        $arrModifies = array(
            'status' => $status,
            'memo01' => $payStatus,
            'memo03' => $paymentLog,
            'payment_date'=> $paymentDate,
            'update_date' => GC_Utils_SBIVT3G::getNowExpression(),
        );
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->update('dtb_order',
            $arrModifies,
            ' order_id = ? ', array($orderId)
        );
        $logger->info(sprintf('受注データ更新 受注番号:%d', $orderId));

        $logger->info('入金通知 - 電子マネー決済処理 終了');
        return true;
    }

    /**
     * 電子マネー決済レコードを返金処理
     *
     * @access protected
     * @param array $arrRecord 明細レコード
     * @param string $orderId  対象受注番号
     * @return boolean 正否
     */
    function doEmOfRefund($arrRecord, $orderId) {
        $logger =& TGMDK_Logger::getInstance();
        $logger->info(' 電子マネー決済 返金通知で処理');

        // 対象受注を取得
        $objPurchase = new SC_Helper_Purchase_Ex();
        $arrOrder = $objPurchase->getOrder($orderId);

        // 決済ログ生成
        $paymentLog = $arrOrder['memo03'];
        $paymentLog .= ((substr($paymentLog, -1) != LF)? LF : '');
        $paymentLog .= GC_Utils_SBIVT3G::putPaymentLogString(
            $arrRecord['emType'], // edy suica waon で表示
            array(
                'orderId' => $arrRecord['orderId'],
                'payStatus' => MDL_SBIVT3G_STATUS_REFUND
            ),
            '返金通知受信'
        );

        // 更新
        $arrModifies = array(
            'memo03' => $paymentLog,
            'update_date' => GC_Utils_SBIVT3G::getNowExpression(),
        );
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->update('dtb_order',
            $arrModifies,
            ' order_id = ? ', array($orderId)
        );
        $logger->info(sprintf('受注データ更新 受注番号:%d', $orderId));

        $logger->info('入金通知 - 電子マネー決済処理(返金) 終了');
        return true;
    }

    /**
     * 銀行決済レコードを処理
     *
     * @access protected
     * @param array $arrRecord 明細レコード
     * @return boolean 正否
     */
    function doPayeasy($arrRecord) {
        $logger =& TGMDK_Logger::getInstance();
        $logger->info('入金通知 - 銀行決済処理 開始');

        // 対象の受注番号を取得
        $orderId = $this->getOrderIdOfRequestStatus($arrRecord['orderId']);
        if ($orderId == null) {
            $logger->warn('該当する受注番号なし');
            $this->putError('銀行決済', $arrRecord['orderId'],
                number_format($arrRecord['rcvAmount']));
            return false;
        }

        // 対象受注を取得
        $objPurchase = new SC_Helper_Purchase_Ex();
        $arrOrder = $objPurchase->getOrder($orderId);
        $arrRes = unserialize($arrOrder['memo05']);

        // 値をパース
        $payStatus = MDL_SBIVT3G_STATUS_DEPOSIT;
        $status = ORDER_PRE_END;
        $paymentDate = $this->toDate($arrRecord['rcvDate']);
        $other = sprintf(' 入金金額[%s]',
            number_format($arrRecord['rcvAmount']));

        // 現在の支払方法検証
        $innerPayment =
            GC_Utils_SBIVT3G::getInnerPayment($arrOrder['payment_id']);
        $bol = false;
        $fixInnerPay = '';
        switch ($innerPayment) {
        case MDL_SBIVT3G_INNER_ID_PAYEASY_ATM :
        case MDL_SBIVT3G_INNER_ID_PAYEASY_NET :
            $fixInnerPay = $innerPayment;
            $bol = true;
            break;
        default :
            break;
        }
        if ($bol == false) {
            $other .= ' 現在の支払方法と異なります(更新中止)';
            $fixInnerPay = '銀行'; // 代替表示
            $payStatus = $arrOrder['memo01'];
            $paymentDate = $arrOrder['payment_date'];
            $status = $arrOrder['status'];
        }

        // 決済ステータス検証
        if ($arrOrder['memo01'] != MDL_SBIVT3G_STATUS_REQUEST) {
            $payStatus = $arrOrder['memo01'];
            $paymentDate = $arrOrder['payment_date'];
            $other .= ' 3G決済ステータス不整合(更新中止)';
        }

        // 受注ステータス検証
        if ($arrOrder['status'] != ORDER_PAY_WAIT) {
            $status = $arrOrder['status'];
            $other .= ' 対応状況不整合(更新中止)';
        }

        // 決済ログ生成
        $paymentLog = $arrOrder['memo03'];
        $paymentLog .= ((substr($paymentLog, -1) != LF)? LF : '');
        $paymentLog .= GC_Utils_SBIVT3G::putPaymentLogString(
            $fixInnerPay,
            array(
                'orderId' => $arrRecord['orderId'],
                'payStatus'=>MDL_SBIVT3G_STATUS_DEPOSIT
            ),
            '入金通知受信' . $other
        );

        // 更新
        $arrModifies = array(
            'status' => $status,
            'memo01' => $payStatus,
            'memo03' => $paymentLog,
            'payment_date'=> $paymentDate,
            'update_date' => GC_Utils_SBIVT3G::getNowExpression(),
        );
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->update('dtb_order',
            $arrModifies,
            ' order_id = ? ', array($orderId)
        );
        $logger->info(sprintf('受注データ更新 受注番号:%d', $orderId));

        $logger->info('入金通知 - 銀行決済処理 終了');
        return true;
    }

    /**
     * 銀聯ネット決済レコードを処理
     *
     * @access protected
     * @param array $arrRecord 明細レコード
     * @param integer $fixed 速報・確報フラグ
     * @return boolean 正否
     */
    function doCup($arrRecord, $fixed) {
        $logger =& TGMDK_Logger::getInstance();
        $logger->info('入金通知 - 銀聯ネット決済処理 開始');

        if (strcmp($fixed, '1') == 0) {
            $bolFixed = true; // 確報
        } else {
            $bolFixed = false; // 速報
        }

        // 対象の受注番号を取得
        $orderId = $this->getOrderIdOfRequestStatus($arrRecord['orderId']);
        if ($orderId == null) {
            $logger->warn('該当する受注番号なし');
            $this->putError('銀聯ネット決済', $arrRecord['orderId'],
                number_format($arrRecord['amount']));
            return false;
        }

        // 対象受注を取得
        $objPurchase = new SC_Helper_Purchase_Ex();
        $arrOrder = $objPurchase->getOrder($orderId);
        $arrRes = unserialize($arrOrder['memo05']);

        // 値をパース
        $payStatus = MDL_SBIVT3G_STATUS_DEPOSIT;
        $status = ORDER_PRE_END;

        // MMDDなので判別して日付にする
        $y = date('Y');
        $m = substr($arrRecord['settleDate'], 0, 2);
        $d = substr($arrRecord['settleDate'], 2, 2);
        if (mktime(0,0,0,$m,$d,$y) > time()) {
            $y -= 1;
        }
        $paymentDate = sprintf('%04d/%02d/%02d', $y, $m, $d);

        $other = sprintf(' 入金金額[%s]', number_format($arrRecord['amount']));

        // 現在の支払方法検証
        $innerPayment =
            GC_Utils_SBIVT3G::getInnerPayment($arrOrder['payment_id']);
        if (strcmp($innerPayment, MDL_SBIVT3G_INNER_ID_CUP) != 0) {
            $other .= ' 現在の支払方法と異なります(更新中止)';
            $status = $arrOrder['status'];
            $payStatus = $arrOrder['memo01'];
            $paymentDate = $arrOrder['payment_date'];
        }

        // 決済ステータス検証
        if ($arrOrder['memo01'] != MDL_SBIVT3G_STATUS_REQUEST) {
            $payStatus = $arrOrder['memo01'];
            $paymentDate = $arrOrder['payment_date'];
            $other .= ' 3G決済ステータス不整合(更新中止)';
        }

        // 受注ステータス検証
        if ($arrOrder['status'] != ORDER_PAY_WAIT) {
            $status = $arrOrder['status'];
            $other .= ' 対応状況不整合(更新中止)';
        }

        // 決済ログ生成
        $paymentLog = $arrOrder['memo03'];
        $paymentLog .= ((substr($paymentLog, -1) != LF)? LF : '');
        if ($bolFixed == true) {
            $other = '入金通知受信(確報)' . $other;
        } else {
            $other = '入金通知受信(速報)' . $other;
        }
        $paymentLog .= GC_Utils_SBIVT3G::putPaymentLogString(
            MDL_SBIVT3G_INNER_ID_CUP,
            array(
                'orderId' => $arrRecord['orderId'],
                'payStatus'=>MDL_SBIVT3G_STATUS_DEPOSIT
            ),
            $other
        );

        // 更新
        if ($bolFixed == true) {
            // 確報の更新
            $arrModifies = array(
                'status' => $status,
                'memo01' => $payStatus,
                'memo03' => $paymentLog,
                'payment_date'=> $paymentDate,
                'update_date' => GC_Utils_SBIVT3G::getNowExpression(),
            );
        } else {
            // 速報の更新
            $arrModifies = array(
                'memo03' => $paymentLog,
                'update_date' => GC_Utils_SBIVT3G::getNowExpression(),
            );
        }
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->update('dtb_order',
            $arrModifies,
            ' order_id = ? ', array($orderId)
        );
        $logger->info(sprintf('受注データ更新 受注番号:%d', $orderId));

        $logger->info('入金通知 - 銀聯ネット決済処理 終了');
        return true;
    }

    /**
     * 銀聯ネット決済レコードを取消・返金処理
     *
     * @access protected
     * @param array $arrRecord 明細レコード
     * @param integer $fixed 速報・確報フラグ
     * @return boolean 正否
     */
    function doCupOfRefund($arrRecord, $fixed) {
        $logger =& TGMDK_Logger::getInstance();
        $logger->info('取消・返金通知 - 銀聯ネット決済処理 開始');

        if (strcmp($fixed, '1') == 0) {
            $bolFixed = true; // 確報
        } else {
            $bolFixed = false; // 速報
        }

        // 対象の受注番号を取得
        $orderId = $this->getOrderIdOfRefundStatus($arrRecord['orderId']);
        if ($orderId == null) {
            $logger->warn('該当する受注番号なし');
            $this->putError('銀聯ネット決済', $arrRecord['orderId'],
                number_format($arrRecord['amount']));
            return false;
        }

        // 対象受注を取得
        $objPurchase = new SC_Helper_Purchase_Ex();
        $arrOrder = $objPurchase->getOrder($orderId);

        // 決済ログ生成
        $other = sprintf(' 金額[%s]', number_format($arrRecord['amount']));
        $paymentLog = $arrOrder['memo03'];
        $paymentLog .= ((substr($paymentLog, -1) != LF)? LF : '');
        if (strcmp($arrRecord['txnType'], 'cancel_authorize') == 0) {
            $msg = '取消通知受信';
            $payStatus = MDL_SBIVT3G_STATUS_CANCEL;
        } else {
            $msg = '返金通知受信';
            $payStatus = MDL_SBIVT3G_STATUS_REFUND;
        }
        if ($bolFixed == true) {
            $msg .= '(確報)';
        } else {
            $msg .= '(速報)';
        }
        $paymentLog .= GC_Utils_SBIVT3G::putPaymentLogString(
            MDL_SBIVT3G_INNER_ID_CUP,
            array(
                'orderId' => $arrRecord['orderId'],
                'payStatus' => $payStatus,
            ),
            $msg . $other
        );

        // 更新
        $arrModifies = array(
            'memo03' => $paymentLog,
            'update_date' => GC_Utils_SBIVT3G::getNowExpression(),
        );
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->update('dtb_order',
            $arrModifies,
            ' order_id = ? ', array($orderId)
        );
        $logger->info(sprintf('受注データ更新 受注番号:%d', $orderId));

        $logger->info('取消・返金通知 - 銀聯ネット決済処理 終了');
        return true;
    }

    /**
     * PayPal決済レコードを処理
     *
     * @access protected
     * @param array $arrRecord 明細レコード
     * @param integer $fixed 速報・確報フラグ
     * @return boolean 正否
     */
    function doPayPal($arrRecord, $fixed) {
        $logger =& TGMDK_Logger::getInstance();
        $logger->info('入金通知 - PayPal決済処理 開始');

        if (strcmp($fixed, '1') == 0) {
            $bolFixed = true; // 確報
        } else {
            $bolFixed = false; // 速報
        }

        // 対象の受注番号を取得
        $orderId = $this->getOrderIdOfRequestStatus($arrRecord['orderId']);
        if ($orderId == null) {
            $logger->warn('該当する受注番号なし');
            $this->putError('PayPal決済', $arrRecord['orderId'],
                number_format($arrRecord['amount']));
            return false;
        }

        // 対象受注を取得
        $objPurchase = new SC_Helper_Purchase_Ex();
        $arrOrder = $objPurchase->getOrder($orderId);
        $arrRes = unserialize($arrOrder['memo05']);

        // 値をパース
        $payStatus = MDL_SBIVT3G_STATUS_DEPOSIT;
        $status = ORDER_PRE_END;
        $paymentDate = $this->toDate($arrRecord['receivedDatetime']);
        $other = sprintf(' 入金金額[%s]', number_format($arrRecord['amount']));

        // 現在の支払方法検証
        $innerPayment =
            GC_Utils_SBIVT3G::getInnerPayment($arrOrder['payment_id']);
        if (strcmp($innerPayment, MDL_SBIVT3G_INNER_ID_PAYPAL) != 0) {
            $other .= ' 現在の支払方法と異なります(更新中止)';
            $payStatus = $arrOrder['memo01'];
            $paymentDate = $arrOrder['payment_date'];
            $status = $arrOrder['status'];
        }

        // 決済ログ生成
        $paymentLog = $arrOrder['memo03'];
        $paymentLog .= ((substr($paymentLog, -1) != LF)? LF : '');
        if ($bolFixed == true) {
            $other = '入金通知受信(確報)' . $other;
        } else {
            $other = '入金通知受信(速報)' . $other;
        }
        $paymentLog .= GC_Utils_SBIVT3G::putPaymentLogString(
            MDL_SBIVT3G_INNER_ID_PAYPAL,
            array(
                'orderId' => $arrRecord['orderId'],
                'payStatus'=>MDL_SBIVT3G_STATUS_DEPOSIT
            ),
            $other
        );
        // 更新
        if ($bolFixed == true) {
            // 確報の更新
            $arrModifies = array(
                'memo03' => $paymentLog,
                'payment_date'=> $paymentDate,
                'update_date' => GC_Utils_SBIVT3G::getNowExpression(),
            );
        } else {
            // 速報の更新
            $arrModifies = array(
                'memo03' => $paymentLog,
                'update_date' => GC_Utils_SBIVT3G::getNowExpression(),
            );
        }
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->update('dtb_order',
            $arrModifies,
            ' order_id = ? ', array($orderId)
        );
        $logger->info(sprintf('受注データ更新 受注番号:%d', $orderId));

        $logger->info('入金通知 - PayPal決済処理 終了');
        return true;
    }

    /**
     * PayPal決済レコードを返金処理
     *
     * @access protected
     * @param array $arrRecord 明細レコード
     * @param integer $fixed 速報・確報フラグ
     * @return boolean 正否
     */
    function doPayPalOfRefund($arrRecord, $fixed) {
        $logger =& TGMDK_Logger::getInstance();
        $logger->info('返金通知 - PayPal決済処理 開始');

        if (strcmp($fixed, '1') == 0) {
            $bolFixed = true; // 確報
        } else {
            $bolFixed = false; // 速報
        }

        // 無いなら「申込」対象の受注番号を取得
        $orderId = $this->getOrderIdOfRefundStatus($arrRecord['orderId']);
        if ($orderId == null) {
            $logger->warn('該当する受注番号なし');
            $this->putError('PayPal決済', $arrRecord['orderId'],
                number_format($arrRecord['amount']));
            return false;
        }

        // 対象受注を取得
        $objPurchase = new SC_Helper_Purchase_Ex();
        $arrOrder = $objPurchase->getOrder($orderId);

        // 決済ログ生成
        $other = sprintf(' 金額[%s]', number_format($arrRecord['amount']));
        $paymentLog = $arrOrder['memo03'];
        $paymentLog .= ((substr($paymentLog, -1) != LF)? LF : '');
        if ($bolFixed == true) {
            $msg = '返金通知受信(確報)';
        } else {
            $msg = '返金通知受信(速報)';
        }
        $paymentLog .= GC_Utils_SBIVT3G::putPaymentLogString(
            MDL_SBIVT3G_INNER_ID_PAYPAL,
            array(
                'orderId' => $arrRecord['orderId'],
                'payStatus' => MDL_SBIVT3G_STATUS_REFUND
            ),
            $msg . $other
        );

        // 更新
        $arrModifies = array(
            'memo03' => $paymentLog,
            'update_date' => GC_Utils_SBIVT3G::getNowExpression(),
        );
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->update('dtb_order',
            $arrModifies,
            ' order_id = ? ', array($orderId)
        );
        $logger->info(sprintf('受注データ更新 受注番号:%d', $orderId));

        $logger->info('返金通知 - PayPal決済処理 終了');
        return true;
    }

    /**
     * キャリア決済レコードを処理
     *
     * @access protected
     * @param array $arrRecord 明細レコード
     * @return boolean 正否
     */
    function doCarrier($arrRecord) {
        $logger =& TGMDK_Logger::getInstance();
        $logger->info('結果通知 - キャリア決済処理 開始');

        // 対象の受注番号を取得
        $orderId =
            $this->getOrderIdOfRequestStatus($arrRecord['orderId'], true);
        if ($orderId == null) {
            $logger->warn('該当する受注番号なし');
            $this->putCarrierError('キャリア決済', $arrRecord['orderId']);
            return false;
        }

        // 対象受注を取得
        $objPurchase = new SC_Helper_Purchase_Ex();
        $arrOrder = $objPurchase->getOrder($orderId);

        // 受注のステータスを確認
        // 決済処理中以外はここで終了
        if ($arrOrder['status'] != ORDER_PENDING) {
            $logger->warn('受注ステータスが決済処理中ではないため、' .
                          '更新は行いません。');
            return false;
        }

        // 取引情報を復元
        $arrRes = unserialize($arrOrder['memo05']);
        $logger->info(print_r($arrRes, true));

        // 完了記述情報
        $objIF = new SC_If_SBIVT3G_CompleteResource();
        $objIF->setCompMailRCTitle($arrOrder['payment_method']);

        // 受注ステータスは"新規注文"
        $arrOrder['status'] = ORDER_NEW;

        // memo01:決済状態を保存
        $arrOrder['memo01'] = $arrRes['payStatus'];

        // memo02:メールでの記述情報
        $service = '';
        switch ($arrRes['serviceOptionType']) {
        case MDL_SBIVT3G_CARRIER_TYPE_DOCOMO:
            $service = PAYMENT_NAME_CARRIER_DOCOMO;
            break;
        case MDL_SBIVT3G_CARRIER_TYPE_AU:
            $service = PAYMENT_NAME_CARRIER_AU;
            break;
        case MDL_SBIVT3G_CARRIER_TYPE_SB_KTAI:
            $service = PAYMENT_NAME_CARRIER_SB_KTAI;
            break;
        case MDL_SBIVT3G_CARRIER_TYPE_SB_MATOMETE:
            $service = PAYMENT_NAME_CARRIER_SB_MATOMETE;
            break;
        case MDL_SBIVT3G_CARRIER_TYPE_S_BIKKURI:
            $service = PAYMENT_NAME_CARRIER_S_BIKKURI;
            break;
        default:
            break;
        }
        $objIF->setCompMailRC('お支払い方法', $service);
        $arrOrder['memo02'] = $objIF->getCompMailRC();

        // memo03:ログ情報
        $other  = '結果通知 成功';
        $other .= ' サービスオプションタイプ[';
        $other .= $arrRes['serviceOptionType'] . ']';
        $other .= ' 端末種別[' . $arrRes['terminalKind'] . ']';
        $arrOrder['memo03'] = GC_Utils_SBIVT3G::putPaymentLogString
            (MDL_SBIVT3G_INNER_ID_CARRIER, $arrRes, $other);

        // memo04:最終受注ID
        //   すでに登録済みのためここでは何も行わない
        $arrOrder['memo04'];

        // memo05:再決済用情報
        //   すでに登録済みのためここでは何も行わない
        $arrOrder['memo05'];

        // memo06:空白
        //   すでに空白のためここでは何も行わない
        $arrOrder['memo06'];

        // 更新
        $arrModifies = array('status' => $arrOrder['status'],
                             'memo01' => $arrOrder['memo01'],
                             'memo02' => $arrOrder['memo02'],
                             'memo03' => $arrOrder['memo03']);
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->update
            ('dtb_order', $arrModifies, ' order_id = ? ', array($orderId));
        $logger->info(sprintf('受注データ更新 受注番号:%d', $orderId));

        // 受注完了メールを送信する
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objPurchase->sendOrderMail($orderId);

        $logger->info('結果通知 - キャリア決済処理 終了');

        return true;
    }

    /**
     * キャリア決済取消レコードを処理
     *
     * @access protected
     * @param array $arrRecord 明細レコード
     * @return boolean 正否
     */
    function doCarrierCancel($arrRecord) {
        $logger =& TGMDK_Logger::getInstance();
        $logger->info('結果通知 - キャリア決済取消処理 開始');

        // 対象の受注番号を取得
        $orderId =
            $this->getOrderIdOfRequestStatus($arrRecord['orderId'], true);
        if ($orderId == null) {
            $logger->warn('該当する受注番号なし');
            $this->putCarrierError('キャリア決済', $arrRecord['orderId']);
            return false;
        }

        // 対象受注を取得
        $objPurchase = new SC_Helper_Purchase_Ex();
        $arrOrder = $objPurchase->getOrder($orderId);

        // 取引情報を復元
        $arrRes = unserialize($arrOrder['memo05']);
        $logger->info(print_r($arrRes, true));

        // memo03:ログ情報
        $other  = '取消通知';
        $other .= ' サービスオプションタイプ[';
        $other .= $arrRes['serviceOptionType'] . ']';
        $other .= ' 端末種別[' . $arrRes['terminalKind'] . ']';
        $log  = $arrOrder['memo03'];
        $log .= ((substr($log, -1) != LF) ? LF : '');
        $log .= GC_Utils_SBIVT3G::putPaymentLogString
            (MDL_SBIVT3G_INNER_ID_CARRIER, $arrRes, $other);
        $arrOrder['memo03'] = $log;

        // 更新
        $arrModifies = array('memo03' => $arrOrder['memo03']);
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->update
            ('dtb_order', $arrModifies, ' order_id = ? ', array($orderId));
        $logger->info(sprintf('受注データ更新 受注番号:%d', $orderId));

        // 決済後エラーのメール通知を行うためエラー情報を作成する
        $this->putCarrierError
            ('キャリア決済', $arrRecord['orderId'], $arrRecord['vResultCode']);

        $logger->info('結果通知 - キャリア決済取消処理 終了');

        return true;
    }

    /**
     * 日付フォーマットをY/m/d H:i:sに変換
     *
     * @access protected
     * @param array $arrRecord 明細レコード
     * @param integer $fixed 速報・確報フラグ
     * @return boolean 正否
     */
    function toDate($yyyymmddhhmiss) {
        $yyyy = date("Y");
        $mm   = date("m");
        $dd   = date("d");
        $hh   = "00";
        $mi   = "00";
        $ss   = "00";
        if (strlen($yyyymmddhhmiss) >= 4) {
            $yyyy = substr($yyyymmddhhmiss, 0, 4);
        }
        if (strlen($yyyymmddhhmiss) >= 6) {
            $mm = substr($yyyymmddhhmiss, 4, 2);
        }
        if (strlen($yyyymmddhhmiss) >= 8) {
            $dd = substr($yyyymmddhhmiss, 6, 2);
        }
        if (strlen($yyyymmddhhmiss) >= 10) {
            $hh = substr($yyyymmddhhmiss, 8, 2);
        }
        if (strlen($yyyymmddhhmiss) >= 12) {
            $mi = substr($yyyymmddhhmiss, 10, 2);
        }
        if (strlen($yyyymmddhhmiss) >= 14) {
            $ss = substr($yyyymmddhhmiss, 12, 2);
        }
        return sprintf("%04d/%02d/%02d %02d:%02d:%02d",
            $yyyy, $mm, $dd, $hh, $mi, $ss
        );
    }
}

?>
