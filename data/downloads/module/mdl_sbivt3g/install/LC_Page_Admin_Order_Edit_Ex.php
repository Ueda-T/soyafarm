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
require_once CLASS_REALDIR . 'pages/admin/order/LC_Page_Admin_Order_Edit.php';

/** ベリトランス3Gモジュールの参照 */
require_once MODULE_REALDIR . 'mdl_sbivt3g' .DIRECTORY_SEPARATOR. 'define.php';

/**
 * 受注修正 のページクラス(拡張).
 *
 * LC_Page_Admin_Order_Edit をカスタマイズする場合はこのクラスを編集する.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_Order_Edit_Ex.php 175 2012-07-25 05:44:03Z hira $
 */
class LC_Page_Admin_Order_Edit_Ex extends LC_Page_Admin_Order_Edit {

    // {{{ properties
    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        parent::process();
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**************************************************************************/

    /**
     * Page のアクション.
     * オーバーライド by Veritrans3G
     *
     * @return void
     */
    function action() {
        parent::action();

        // ステータス更新を試みる
        if ($this->getMode() == 'sbivt3gRefresh') {
            $objSbivtAdmin =& SC_Helper_SBIVT3G_Admin::getSingletonInstance();
            $objSbivtAdmin->refreshStatus($this->arrForm, $message);
            if (strlen($message, '') == 0) {
                $this->tpl_onload = "window.alert('" . $message . "');";
            }
        }

        // テンプレート用にデータクラス設定
        $objIF = new SC_If_SBIVT3G_OrderDataMainte($this->arrForm);
        $this->objSbivt = $objIF;

        // 決済情報整形
        if (strcmp($objIF->arrSrcOrder['memo02'], '') != 0) {
            $arrOther = unserialize($objIF->arrSrcOrder['memo02']);
            $this->arrForm['payment_type'] = $arrOther['title']['name'];
            $this->arrForm['payment_info'] = $arrOther;
        }
    }

    /**
     * パラメータ情報の初期化を行う.
     * オーバーライド by Veritrans3G
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        // 親処理実行
        parent::lfInitParam($objFormParam);

        // SBIVT限定の項目追加
        $objSbivtAdmin =& SC_Helper_SBIVT3G_Admin::getSingletonInstance();
        $objSbivtAdmin->initOrderParam($objFormParam);
    }

    /**
     * 入力内容のチェックを行う.
     * オーバーライド by Veritrans3G
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return array エラーメッセージの配列
     */
    function lfCheckError(&$objFormParam) {

        // 親処理実行
        $arrErr = parent::lfCheckError($objFormParam);

        // SBIVT限定のチェック
        $objSbivtAdmin =& SC_Helper_SBIVT3G_Admin::getSingletonInstance();
        $arrValues = $objFormParam->getHashArray();
        $arrSbiErr = $objSbivtAdmin->checkOrderRecord($arrValues);
        $arrErr = array_merge($arrSbiErr, $arrErr);

        return $arrErr;
    }

    /**
     * DB更新処理
     * オーバーライド by Veritrans3G
     *
     * @param integer $order_id 受注ID
     * @param SC_Helper_Purchase $objPurchase SC_Helper_Purchase インスタンス
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param string $message 通知メッセージ
     * @param array $arrValuesBefore 更新前の受注情報(2.11.2～)
     * @return integer $order_id 受注ID
     *
     * エラー発生時は負数を返す。
     */
    function doRegister($order_id, &$objPurchase, &$objFormParam, &$message, $arrValuesBefore) {

        // SBIVT限定の前処理実行
        $objSbivtAdmin =& SC_Helper_SBIVT3G_Admin::getSingletonInstance();
        $bol = $objSbivtAdmin->preDoRegister(
            $order_id, $objPurchase, $objFormParam, $message);
        if ($bol == false) {
            return -1;
        }

        // 本処理実行
        if (func_num_args() == 4) {
            // 2.11.1までの処理
            $rtn = parent::doRegister($order_id, $objPurchase,
                $objFormParam, $message);
        } else {
            // 2.11.2の処理
            $rtn = parent::doRegister($order_id, $objPurchase,
                $objFormParam, $message, $arrValuesBefore);
        }
        return $rtn;
    }
}
?>
