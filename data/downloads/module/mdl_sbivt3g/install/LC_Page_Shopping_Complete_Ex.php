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
require_once CLASS_REALDIR . 'pages/shopping/LC_Page_Shopping_Complete.php';
// }}}

/*
 * 専用のSC_Display_Exクラス
 */
require_once CLASS_EX_REALDIR . 'SC_Display_Ex.php';
class SC_Display_Ex_Custom extends SC_Display_Ex {

    /**
     * LC_Page のパラメーターを, テンプレートに設定し, 出力の準備を行う.
     * オーバーライド by Veritrans3G
     *
     * @param LC_Page $page LC_Page インスタンス
     * @param $is_admin boolean 管理画面を扱う場合 true
     */
    function prepare($page, $is_admin = false) {
        if (!$this->deviceSeted || !is_null($this->view)) {
            $device = ($is_admin) ? DEVICE_TYPE_ADMIN : $this->detectDevice();
            $this->setDevice($device);
        }
        $this->assignobj($page);

        // script_escapeを除去する
        $this->view->_smarty->default_modifiers = array();

        // コンパイル済みファイルにmodifierが残る可能性があるので初期化
        $this->view->_smarty->clear_compiled_tpl($page->tpl_mainpage);

        // 2.12.0から追加された処理
        if (method_exists($this->view, 'setPage') == true) {
            $this->view->setPage($page);
        }
        $this->response->setResposeBody($this->view->getResponse($page->getTemplate()));

        // 次以降のページ表示ではmodifierを復元するため再度初期化
        $this->view->_smarty->clear_compiled_tpl($page->tpl_mainpage);
    }

}

/**
 * ご注文完了 のページクラス(拡張).
 *
 * LC_Page_Shopping_Complete をカスタマイズする場合はこのクラスを編集する.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Shopping_Complete_Ex.php 183 2012-07-27 09:37:45Z hira $
 */
class LC_Page_Shopping_Complete_Ex extends LC_Page_Shopping_Complete {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     * オーバーライド by Veritrans3G
     *
     * @return void
     */
    function init() {
        parent::init();

        // 修正したクラスに差し替える
        $this->objDisplay = new SC_Display_Ex_Custom();
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

    /**
     * Page のレスポンス送信.
     * オーバーライド by Veritrans3G
     *
     * @return void
     */
    function sendResponse() {

        // 完了画面表示リソースを取得
        if (isset($_SESSION['MDL_SBIVT3G_COMPLETE_RC']) == true) {
            $this->arrOther = $_SESSION['MDL_SBIVT3G_COMPLETE_RC'];
            unset($_SESSION['MDL_SBIVT3G_COMPLETE_RC']);
        }

        // 受注番号の消去 2.11.4以降なら既に削除されている
        if (isset($_SESSION['order_id']) == true) {
            unset($_SESSION['order_id']);
        }

        // 親処理実行
        parent::sendResponse();
    }
}
?>
