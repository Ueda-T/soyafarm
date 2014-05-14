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
require_once (CLASS_REALDIR .
              'pages/admin/products/LC_Page_Admin_Products_Media.php');

/**
 * 広告媒体マスタ登録 のページクラス(拡張).
 *
 * LC_Page_Admin_Products_Media をカスタマイズする場合はこのクラスを編集する.
 *
 * @package Page
 * @author IQUEVE Co.,Ltd.
 * @version $Id: LC_Page_Admin_Products_Media_Ex.php 82 2013-12-11 07:36:56Z kaji $
 */
class LC_Page_Admin_Products_Media_Ex extends LC_Page_Admin_Products_Media
{
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
}
?>
