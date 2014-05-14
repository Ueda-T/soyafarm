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
require_once CLASS_REALDIR . 'pages/admin/order/LC_Page_Admin_Order_Disp.php';

/** ベリトランス3Gモジュールの参照 */
require_once MODULE_REALDIR . 'mdl_sbivt3g' .DIRECTORY_SEPARATOR. 'define.php';

/**
 * 受注情報表示 のページクラス(拡張).
 *
 * LC_Page_Admin_Order_Disp をカスタマイズする場合はこのクラスを編集する.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_Order_Disp_Ex.php 175 2012-07-25 05:44:03Z hira $
 */
class LC_Page_Admin_Order_Disp_Ex extends LC_Page_Admin_Order_Disp {

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
}
?>
