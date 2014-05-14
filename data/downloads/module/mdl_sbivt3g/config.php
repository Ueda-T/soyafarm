<?php
/**
 * config.php - 設定画面処理の呼び出しファイル
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: config.php 19 2011-07-28 09:35:15Z takao $
 * @link        http://www.veritrans.co.jp/3gps
 */

require_once(MODULE_REALDIR . 'mdl_sbivt3g/define.php');

require_once(MDL_SBIVT3G_CLASS_PATH . "pages/LC_Page_SBIVT3G_Config.php");

$objPage = new LC_Page_SBIVT3G_Config();
register_shutdown_function(array($objPage, "destroy"));
$objPage->init();
$objPage->process();
?>
