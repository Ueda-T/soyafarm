<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

// {{{ requires
require_once CLASS_REALDIR . 'helper/SC_Helper_Purchase.php';

/** ベリトランス3Gモジュールの参照 */
require_once MODULE_REALDIR . 'mdl_sbivt3g' .DIRECTORY_SEPARATOR. 'define.php';

/**
 * 商品購入関連のヘルパークラス(拡張).
 *
 * LC_Helper_Purchase をカスタマイズする場合はこのクラスを編集する.
 *
 * @package Helper
 * @author Kentaro Ohkouchi
 * @version $Id: SC_Helper_Purchase_Ex.php 210 2013-12-19 12:10:49Z takao $
 */
class SC_Helper_Purchase_Ex extends SC_Helper_Purchase {

    /**
     * 受注をキャンセルし, カートをロールバックして, 受注一時IDを返す.
     * オーバーライド by Veritrans3G
     * 2.11.0に存在しない
     *
     * @param integer $order_id 受注ID
     * @param integer $orderStatus 受注ステータス
     * @param boolean $is_delete 受注データを論理削除する場合 true
     * @return string 受注一時ID
     * @see SC_Helper_Purchase::rollbackOrder()
     */
    function rollbackOrder($order_id, $orderStatus = ORDER_CANCEL, $is_delete = false) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $in_transaction = $objQuery->inTransaction();
        if (!$in_transaction) {
            $objQuery->begin();
        }

        $this->cancelOrder($order_id, $orderStatus, $is_delete);
        $arrOrderTemp = $this->getOrderTempByOrderId($order_id);
        $_SESSION = array_merge($_SESSION, unserialize($arrOrderTemp['session']));

        $objSiteSession = new SC_SiteSession_Ex();
        $objCartSession = new SC_CartSession_Ex();
        $objCustomer = new SC_Customer_Ex();

        // 新たに受注一時情報を保存する
        $objSiteSession->unsetUniqId();
        $uniqid = $objSiteSession->getUniqId();
        $arrOrderTemp['del_flg'] = 0;
        $this->saveOrderTemp($uniqid, $arrOrderTemp, $objCustomer);
        $this->verifyChangeCart($uniqid, $objCartSession);
        $objSiteSession->setRegistFlag();

        if (!$in_transaction) {
            $objQuery->commit();
        }
        return $uniqid;
    }

    /**
     * 受注をキャンセルする.
     * オーバーライド by Veritrans3G
     * 2.11.0に存在しない
     *
     * @param integer $order_id 受注ID
     * @param integer $orderStatus 受注ステータス
     * @param boolean $is_delete 受注データを論理削除する場合 true
     * @return void
     * @see SC_Helper_Purchase::cancelOrder()
     */
    function cancelOrder($order_id, $orderStatus = ORDER_CANCEL, $is_delete = false) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $in_transaction = $objQuery->inTransaction();
        if (!$in_transaction) {
            $objQuery->begin();
        }

        $arrParams['status'] = $orderStatus;
        if ($is_delete) {
            $arrParams['del_flg'] = 1;
        }

        $this->registerOrder($order_id, $arrParams);

        $arrOrderDetail = $this->getOrderDetail($order_id);
        foreach ($arrOrderDetail as $arrDetail) {
            $objQuery->update('dtb_products_class', array(),
                              "product_class_id = ?", array($arrDetail['product_class_id']),
                              array('stock' => 'stock + ?'), array($arrDetail['quantity']));
        }
        if (!$in_transaction) {
            $objQuery->commit();
        }
    }

    /**
     * 受注IDをキーにして受注一時情報を取得する.
     * オーバーライド by Veritrans3G
     * 2.11.0に存在しない
     *
     * @param integer $order_id 受注ID
     * @return array 受注一時情報の配列
     * @see SC_Helper_Purchase::getOrderTempByOrderId()
     */
    function getOrderTempByOrderId($order_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->getRow("*", "dtb_order_temp", "order_id = ?",
                                 array($order_id));
    }

    /**
     * ポイント使用するかの判定
     * オーバーライド by Veritrans3G
     * ->決済処理中の段階でポイントが減算されてしまうのを防ぐ
     *
     * $status が null の場合は false を返す.
     *
     * @param integer $status 対応状況
     * @return boolean 使用するか(顧客テーブルから減算するか)
     */
    function isUsePoint($status) {
        if ($status == null) {
            return false;
        }
        switch ($status) {
            case ORDER_CANCEL:      // キャンセル
            case ORDER_PENDING:     // 決済処理中
                return false;
            default:
                break;
        }

        return true;
    }

    /**
     * 受注.対応状況の更新
     *
     * 必ず呼び出し元でトランザクションブロックを開いておくこと。
     *
     * オーバーライド by Veritrans3G
     * ->ポイント変動発生時のバグを修正(2.11.2では修正済み)
     *
     * @param integer $orderId 注文番号
     * @param integer|null $newStatus 対応状況 (null=変更無し)
     * @param integer|null $newAddPoint 加算ポイント (null=変更無し)
     * @param integer|null $newUsePoint 使用ポイント (null=変更無し)
     * @param array $sqlval 更新後の値をリファレンスさせるためのパラメータ
     * @return void
     */
    function sfUpdateOrderStatus($orderId, $newStatus = null, $newAddPoint = null, $newUsePoint = null, &$sqlval) {

        // もし2.11.2以上なら親処理を実行
        if (GC_Utils_SBIVT3G::compareVersion('2.11.2') >= 0) {
            return parent::sfUpdateOrderStatus($orderId, $newStatus,
                $newAddPoint, $newUsePoint, $sqlval);
        }

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $arrOrderOld = $objQuery->getRow('status, add_point, use_point, customer_id', 'dtb_order', 'order_id = ?', array($orderId));

        // 対応状況が変更無しの場合、DB値を引き継ぐ
        if (is_null($newStatus)) {
            $newStatus = $arrOrderOld['status'];
        }

        // 使用ポイント、DB値を引き継ぐ
        if (is_null($newUsePoint)) {
            $newUsePoint = $arrOrderOld['use_point'];
        }

        // 加算ポイント、DB値を引き継ぐ
        if (is_null($newAddPoint)) {
            $newAddPoint = $arrOrderOld['add_point'];
        }

        if (USE_POINT !== false) {
            // 顧客.ポイントの加減値
            $addCustomerPoint = 0;

            // ▼使用ポイント
            // 変更前の対応状況が利用対象の場合、変更前の使用ポイント分を戻す
            if ($this->isUsePoint($arrOrderOld['status'])) {
                $addCustomerPoint += $arrOrderOld['use_point'];
            }

            // 変更後の対応状況が利用対象の場合、変更後の使用ポイント分を引く
            if ($this->isUsePoint($newStatus)) {
                $addCustomerPoint -= $newUsePoint;
            }

            // ▲使用ポイント

            // ▼加算ポイント
            // 変更前の対応状況が加算対象の場合、変更前の加算ポイント分を戻す
            if ($this->isAddPoint($arrOrderOld['status'])) {
                $addCustomerPoint -= $arrOrderOld['add_point'];
            }

            // 変更後の対応状況が加算対象の場合、変更後の加算ポイント分を足す
            if ($this->isAddPoint($newStatus)) {
                $addCustomerPoint += $newAddPoint;
            }
            // ▲加算ポイント

            if ($addCustomerPoint != 0) {
                // ▼顧客テーブルの更新
                //$sqlval = array();
                $localSqlval = array();
                $where = '';
                $arrVal = array();
                $arrRawSql = array();
                $arrRawSqlVal = array();

                //$sqlval['update_date'] = 'Now()';
                $localSqlval['update_date'] = 'Now()';
                $arrRawSql['point'] = 'point + ?';
                $arrRawSqlVal[] = $addCustomerPoint;
                $where .= 'customer_id = ?';
                $arrVal[] = $arrOrderOld['customer_id'];

                //$objQuery->update('dtb_customer', $sqlval, $where, $arrVal, $arrRawSql, $arrRawSqlVal);
                $objQuery->update('dtb_customer', $localSqlval, $where, $arrVal, $arrRawSql, $arrRawSqlVal);
                // ▲顧客テーブルの更新

                // 顧客.ポイントをマイナスした場合、
                if ($addCustomerPoint < 0) {
                    $sql = 'SELECT point FROM dtb_customer WHERE customer_id = ?';
                    $point = $objQuery->getOne($sql, array($arrOrderOld['customer_id']));
                    // 変更後の顧客.ポイントがマイナスの場合、
                    if ($point < 0) {
                        // ロールバック
                        $objQuery->rollback();
                        // エラー
                        SC_Utils_Ex::sfDispSiteError(LACK_POINT);
                    }
                }
            }
        }

        // ▼受注テーブルの更新
        if (empty($sqlval)) {
            $sqlval = array();
        }

        if (USE_POINT !== false) {
            $sqlval['add_point'] = $newAddPoint;
            $sqlval['use_point'] = $newUsePoint;
        }
        // ステータスが発送済みに変更の場合、発送日を更新
        if ($arrOrderOld['status'] != ORDER_DELIV && $newStatus == ORDER_DELIV) {
            $sqlval['commit_date'] = 'Now()';
        }
        // ステータスが入金済みに変更の場合、入金日を更新
        elseif ($arrOrderOld['status'] != ORDER_PRE_END && $newStatus == ORDER_PRE_END) {
            $sqlval['payment_date'] = 'Now()';
        }

        $sqlval['status'] = $newStatus;
        $sqlval['update_date'] = 'Now()';

        $dest = $objQuery->extractOnlyColsOf('dtb_order', $sqlval);
        $objQuery->update('dtb_order', $dest, 'order_id = ?', array($orderId));
        // ▲受注テーブルの更新

        // 2013.12.19  2.13.0以上で実行
        if (GC_Utils_SBIVT3G::compareVersion('2.13.0') >= 0) {
            //会員情報の最終購入日、購入合計を更新
            if ($arrOrderOld['customer_id'] > 0 and $arrOrderOld['status'] != $newStatus) {
                SC_Customer_Ex::updateOrderSummary($arrOrderOld['customer_id']);
            }
        }
    }

    /**
     * 購入金額に応じた支払方法を取得する.
     * オーバーライド by Veritrans3G
     * ->支払方法ごとのデバイス判別を行う
     *
     * @param integer $total 購入金額
     * @param integer $deliv_id 配送業者ID
     * @return array 購入金額に応じた支払方法の配列
     */
    function getPaymentsByPrice($total, $deliv_id) {

        // 親処理実行
        $arrPayments = parent::getPaymentsByPrice($total, $deliv_id);

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // ベリトランスの支払方法を取得
        $arrIds = array();
        foreach ($arrPayments as $payment) {
            $arrIds[] = $payment['payment_id'];
        }
        $where = sprintf('memo01 IS NOT NULL AND payment_id IN (%s)',
            implode(',', array_pad(array(), count($arrIds), '?'))
        );
        $sbiPayments = $objQuery->select('payment_id, memo01',
            'dtb_payment', $where, $arrIds);
        $arrInnerIds = array();
        foreach ($sbiPayments as $record) {
            $arrInnerIds[$record['payment_id']] = $record['memo01']; 
        }

        // 有効な支払方法のみ残す
        $arrValidPayments = array();
        $objMobile = new Net_UserAgent_Mobile();
        foreach ($arrPayments as $payment) {
            $valid = true;
            switch ($arrInnerIds[$payment['payment_id']]) {

            // PC環境に限るもの
            case MDL_SBIVT3G_INNER_ID_CUP :
            case MDL_SBIVT3G_INNER_ID_PAYPAL :
                if ($objMobile->isMobile() == true
                || $objMobile->isSmartphone() == true){
                    $valid = false;
                }
                break;

            // IEの特定バージョンに限るもの
            case MDL_SBIVT3G_INNER_ID_EDY_PC_APP :
            case MDL_SBIVT3G_INNER_ID_SUICA_PC_APP :
            case MDL_SBIVT3G_INNER_ID_WAON_PC_APP :
                if (GC_Utils_SBIVT3G::isValidBrowserForPasori() == false) {
                    $valid = false;
                }
                break;

            // 携帯電話＋特定のスマートフォン
            case MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_APP :
            case MDL_SBIVT3G_INNER_ID_WAON_MOBILE_APP :
                if ($objMobile->isMobile() == false
                && GC_Utils_SBIVT3G::isValidSphoneForEMoney() == false){
                    $valid = false;
                }
                break;
            default : // それ以外
                break;
            }
            if ($valid == true) {
                $arrValidPayments[] = $payment;
            }
        }
        return $arrValidPayments;
    }
}
?>
