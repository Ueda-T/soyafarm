<?php
/**
 * LC_Page_SBIVT3G_Status.php - LC_Page_SBIVT3G_Status クラスを定義
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: LC_Page_SBIVT3G_Status.php 181 2012-07-27 05:45:17Z hira $
 * @link        http://www.veritrans.co.jp/3gps
*/

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * 3Gモジュール 管理画面 Veritrans 3G 専用ステータス管理ページクラス
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
class LC_Page_SBIVT3G_Status extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = MDL_SBIVT3G_TPL_PATH
            . 'admin/order/sbivt3g_status.tpl';
        $this->tpl_subnavi = 'order/subnavi.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'sbivb3g_status';
        $this->tpl_subtitle = 'ベリトランス3G専用ステータス管理';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrORDERSTATUS =
            $masterData->getMasterData("mtb_order_status");
        $this->arrORDERSTATUS_COLOR =
            $masterData->getMasterData("mtb_order_status_color");

        // 抽出用決済ステータス取得
        $this->arrSbivtStatus = SC_Helper_SBIVT3G_Setting::getPayStatus();

        // 受注詳細のポップアップURI(edit.php or disp.php)
        $this->tpl_dispUri = './edit.php';
        if (is_readable(HTML_REALDIR . ADMIN_DIR . 'order/disp.php') == true) {
            $this->tpl_dispUri = './disp.php';
        }
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $objDb = new SC_Helper_DB_Ex();

        // パラメータ管理クラス
        $objFormParam = new SC_FormParam_Ex();
        // パラメータ情報の初期化
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        // 入力値の変換
        $objFormParam->convParam();

        $this->arrForm = $objFormParam->getHashArray();

        //支払方法の取得(veritrans限定)
        $where  = 'memo01 LIKE ?';
        $arrVal = array(MDL_SBIVT3G_MODULE_CODE.'%');
        $this->arrPayment = $objDb->sfGetIDValueList("dtb_payment",
            "payment_id", "payment_method", $where, $arrVal);

        // 変更可能な支払方法を取得
        $objRule =& SC_Helper_SBIVT3G_PaymentRule::getSingletonInstance();
        $this->arrSbivtEnableStatus = $objRule->getEnableStatusByPaymentId(
            $this->arrForm['payment'],
            $this->arrForm['sbivt_status']);

        switch ($this->getMode()){
            case 'update':
                // 更新
                $this->lfStatusMove(
                    $this->arrForm['payment'],
                    $this->arrForm['sbivt_status'],
                    $this->arrForm['change_sbivt_status'],
                    $this->arrForm['move']);
                break;

            case 'search':
                break;

            default:
                // デフォルト値設定
                $objFormParam->setValue('payment', '');
                $objFormParam->setValue('sbivt_status', '');
                break;
        }

        //検索結果の表示
        $this->lfStatusDisp($this->arrForm['payment'],
            $this->arrForm['sbivt_status'],
            $this->arrForm['search_pageno']);
    }

    /**
     * パラメータ情報の初期化
     *
     * @access protected
     * @param $objFormParam SC_FormParam 入力値 
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam('ページ番号',
            'search_pageno',
            INT_LEN,
            'n',
            array('MAX_LENGTH_CHECK', 'NUM_CHECK')
        );
        $objFormParam->addParam('支払方法',
            'payment',
            INT_LEN,
            'n',
            array('MAX_LENGTH_CHECK', 'NUM_CHECK')
        );
        $objFormParam->addParam('3G決済ステータス',
            'sbivt_status',
            INT_LEN,
            'n',
            array('MAX_LENGTH_CHECK', 'NUM_CHECK')
        );
        $objFormParam->addParam('変更後3G決済ステータス',
            'change_sbivt_status',
            INT_LEN,
            'n',
            array('MAX_LENGTH_CHECK', 'NUM_CHECK')
        );
        $objFormParam->addParam('対象', 'move');
    }

    /**
     * ステータス一覧の表示
     *
     * @access protected
     * @param integer $payment 支払方法ID
     * @param string $sbivt_status 3G決済ステータス
     * @param integer $pageno 現在ページ
     * @return array 結果配列
     */
    function lfStatusDisp($payment, $sbivt_status, $pageno){
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $select ="*";
        $from = "dtb_order";
        $where = " del_flg = 0 AND payment_id = ? ";
        $arrval= array($payment);
        $order = "order_id DESC";
        if (strcmp($sbivt_status, '') != 0) {
            $where .= " AND memo01 = ? ";
            $arrval[] = $sbivt_status;
        }

        $linemax = $objQuery->count($from, $where, $arrval);
        $this->tpl_linemax = $linemax;

        // ページ送りの処理
        $page_max = ORDER_STATUS_MAX;

        // ページ送りの取得
        $objNavi = new SC_PageNavi_Ex($pageno, $linemax, $page_max,
            'fnNaviSearchOnlyPage', NAVI_PMAX);
        $this->tpl_strnavi = $objNavi->strnavi;      // 表示文字列
        $startno = $objNavi->start_row;

        $this->tpl_pageno = $pageno;

        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setLimitOffset($page_max, $startno);

        //表示順序
        $objQuery->setOrder($order);

        //検索結果の取得
        $this->arrStatus = $objQuery->select($select, $from, $where, $arrval);
    }

    /**
     * ステータス情報の更新
     *
     * @access protected
     * @param integer $paymentId 支払方法ID
     * @param string $sbivtStatus 変更前3G決済ステータス
     * @param string $dstSbivtStatus 変更後3G決済ステータス
     * @param array  $arrMove 選択対象
     * @return boolean 処理の成功/失敗
     */
    function lfStatusMove($paymentId, $sbivtStatus, $dstSbivtStatus, $arrMove) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objRule =& SC_Helper_SBIVT3G_PaymentRule::getSingletonInstance();

        // エラーチェック
        $alert = "window.alert('%s');";
        if (is_array($arrMove) == false || count($arrMove) < 1) {
            $this->tpl_onload = sprintf($alert, '選択項目をご確認下さい');
            return false;
        }
        if (isset($this->arrSbivtEnableStatus[$dstSbivtStatus]) == false) {
            $this->tpl_onload = sprintf($alert,
                '選択したステータス移動は禁止されています');
            return false;
        }

        // 現行の内部決済IDを取得
        $innerPayment = GC_Utils_SBIVT3G::getInnerPayment($paymentId);

        $count = 0;
        foreach ($arrMove as $orderId) {
            $objQuery->begin();

            // 現状の受注情報を取得
            $srcPaymentId = '';
            $srcSbivtStatus = '';
            $srcPaymentLog = '';
            if (strcmp($orderId, '') != 0) {
                $arrOrder = $objPurchase->getOrder($orderId);
                $srcPaymentId = $arrOrder['payment_id'];
                $srcSbivtStatus = $arrOrder['memo01'];
                $srcPaymentLog = $arrOrder['memo03'];
            }

            $this->tpl_log .= sprintf('受注番号[%d] ', $orderId);

            // 逐次可否をチェック
            if (strcmp($paymentId, $srcPaymentId) != 0) {
                $this->tpl_log .= '<span class="attention">';
                $this->tpl_log .= '失敗 選択した受注の支払方法が合致しません';
                $this->tpl_log .= '</span>' . LF;
                $objQuery->rollback();
                continue;
            }
            if (strcmp($sbivtStatus, $srcSbivtStatus) != 0) {
                $this->tpl_log .= '<span class="attention">';
                $this->tpl_log .= '失敗 選択した受注の決済ステータスが合致';
                $this->tpl_log .= 'しません</span>' . LF;
                $objQuery->rollback();
                continue;
            }

            // 1回の通信ごとに実行タイムアウトを再設定
            set_time_limit(MDL_SBIVT3G_STANDARD_EXECUTE_LIMIT);

            // 逐次更新
            $arrRes = array();
            $bol = $objRule->modifyPayStatus($innerPayment,
                $dstSbivtStatus,
                $arrOrder,
                $arrRes);

            if ($bol == false) {
                $objQuery->rollback();
                $this->tpl_log .= '<span class="attention">';
                $this->tpl_log .= '失敗 ' . $arrRes['message'];
                $this->tpl_log .= '</span>' . LF;
                continue;
            }

            // 成功ならレコードの更新
            $dstPaymentLog = $srcPaymentLog;
            $dstPaymentLog .= ((substr($srcPaymentLog, -1) != LF)? LF : '');
            $dstPaymentLog .= GC_Utils_SBIVT3G::putPaymentLogString(
                $innerPayment,
                $arrRes,
                '成功' . $arrRes['other']
            );
            $arrModifies = array(
                'memo01' => $arrRes['payStatus'],
                'memo03' => $dstPaymentLog,
                'memo04' => $arrRes['orderId'],
                'memo05' => serialize($arrRes),
                'update_date' => GC_Utils_SBIVT3G::getNowExpression(),
            );
            if ($dstSbivtStatus == MDL_SBIVT3G_STATUS_REFUND
            || ($dstSbivtStatus == MDL_SBIVT3G_STATUS_CANCEL
                && $innerPayment == MDL_SBIVT3G_INNER_ID_CUP)) {
                // 返金なら返金処理した取引IDを保持
                $arrModifies['memo07']  = $arrRes['orderId'];
                // 更にWaon決済なら返金用URLを保持
                if ($innerPayment == MDL_SBIVT3G_INNER_ID_WAON_MOBILE_APP
                || $innerPayment == MDL_SBIVT3G_INNER_ID_WAON_PC_APP) {
                    $arrModifies['memo08']  = $arrRes['appUrl'];
                }
            }
            $objQuery->update('dtb_order',
                $arrModifies,
                ' order_id = ? ', array($orderId)
            );
            $objQuery->commit();
            $this->tpl_log .= '成功' . LF;
            $count++;
        }

        $this->tpl_onload = sprintf($alert,
            sprintf('選択項目%d件を%sへ移動しました',
                $count,
                $this->arrSbivtEnableStatus[$dstSbivtStatus]
            )
        );

        return true;
    }
}
?>
