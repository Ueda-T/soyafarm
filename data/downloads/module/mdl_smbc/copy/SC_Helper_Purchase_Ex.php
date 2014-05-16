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

/**
 * 商品購入関連のヘルパークラス(拡張).
 *
 * LC_Helper_Purchase をカスタマイズする場合はこのクラスを編集する.
 *
 * @package Helper
 * @author Kentaro Ohkouchi
 * @version $Id: SC_Helper_Purchase_Ex.php 20764 2011-03-22 06:26:40Z nanasess $
 */
class SC_Helper_Purchase_Ex extends SC_Helper_Purchase {

    /**
     * 受注をキャンセルする.
     *
     * 受注完了後の受注をキャンセルする.
     * この関数は, 主に決済モジュールにて, 受注をキャンセルする場合に使用する.
     *
     * 対応状況を引数 $orderStatus で指定した値に変更する.
     * (デフォルト ORDER_CANCEL)
     * 引数 $is_delete が true の場合は, 受注データを論理削除する.
     * 商品の在庫数は, 受注前の在庫数に戻される.
     *
     * @param integer $order_id 受注ID
     * @param integer $orderStatus 対応状況
     * @param boolean $is_delete 受注データを論理削除する場合 true
     * @return void
     */
    function cancelOrder($order_id, $orderStatus = ORDER_CANCEL, $is_delete = false) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $in_transaction = $objQuery->inTransaction();
        if (!$in_transaction) {
            $objQuery->begin();
        }

        $arrParams = array();
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
     * 受注をキャンセルし, カートをロールバックして, 受注一時IDを返す.
     *
     * 受注完了後の受注をキャンセルし, カートの状態を受注前の状態へ戻す.
     * この関数は, 主に, 決済モジュールに遷移した後, 購入確認画面へ戻る場合に使用する.
     *
     * 対応状況を引数 $orderStatus で指定した値に変更する.
     * (デフォルト ORDER_CANCEL)
     * 引数 $is_delete が true の場合は, 受注データを論理削除する.
     * 商品の在庫数, カートの内容は受注前の状態に戻される.
     *
     * @param integer $order_id 受注ID
     * @param integer $orderStatus 対応状況
     * @param boolean $is_delete 受注データを論理削除する場合 true
     * @return string 受注一時ID
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
     * 受注IDをキーにして受注一時情報を取得する.
     *
     * @param integer $order_id 受注ID
     * @return array 受注一時情報の配列
     */
    function getOrderTempByOrderId($order_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->getRow("*", "dtb_order_temp", "order_id = ?",
                                 array($order_id));
    }

}
