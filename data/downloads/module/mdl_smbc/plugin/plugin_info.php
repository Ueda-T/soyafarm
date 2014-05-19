<?php
/*
 * CategoryContents
 * Copyright (C) 2012 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/**
 * プラグイン の情報クラス.
 *
 * @package MdlSmbc
 * @author LOCKON CO.,LTD.
 * @version $Id: $
 */
class plugin_info{
    /** プラグインコード(必須)：プラグインを識別する為キーで、他のプラグインと重複しない一意な値である必要がありま. */
    static $PLUGIN_CODE       = "MdlSmbc";
    /** プラグイン名(必須)：EC-CUBE上で表示されるプラグイン名. */
    static $PLUGIN_NAME       = "SMBCファイナンスサービス　決済依頼メール送信＆口座固定割当";
    /** プラグインバージョン(必須)：プラグインのバージョン. */
    static $PLUGIN_VERSION    = "1.0";
    /** 対応バージョン(必須)：対応するEC-CUBEバージョン. */
    static $COMPLIANT_VERSION = "2.12.0, 2.12.1, 2.12.2";
    /** 作者(必須)：プラグイン作者. */
    static $AUTHOR            = "SMBCファイナンスサービス";
    /** 説明(必須)：プラグインの説明. */
    static $DESCRIPTION       = "お客様宛に決済手続を依頼するメールを送信、お客様はメールで指定されたURLから「決済ステーション」にアクセスして決済手続を行う運用および銀行振込決済において口座を固定する運用が可能となるプラグインです。";
    /** プラグインURL：プラグイン毎に設定出来るURL（説明ページなど） */
    static $PLUGIN_SITE_URL   = "http://www.ec-cube.net/";
    /** プラグイン作者URL：プラグイン毎に設定出来るURL（説明ページなど） */
    static $AUTHOR_SITE_URL   = "http://www.ec-cube.net/";
    /** クラス名(必須)：プラグインのクラス（拡張子は含まない） */
    static $CLASS_NAME       = "MdlSmbc";
    /** フックポイント：フックポイントとコールバック関数を定義します */
    static $HOOK_POINTS       = array(
        array("prefilterTransform", 'prefilterTransform'),
        // 新規受注登録から支払方法選択依頼メール送信ボタン
        array("LC_Page_Admin_Order_Edit_action_after", 'send_data_smbc'),
        // 銀行振込固定化
        array("LC_Page_Admin_Customer_Edit_action_before", 'fixBankAccountSmbc'),
        // 管理画面でログファイルを参照できるようにする
        array("LC_Page_Admin_System_Log_action_after", 'loadLogListWithSmbcLog'),
        );
    /** ライセンス */
    static $LICENSE        = "LGPL";
}
?>