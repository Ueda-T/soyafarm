<?php

/*
 * 通販システム定期情報インポート処理ファイル
 */

require_once '../require.php';
require_once CLASS_EX_REALDIR . 'page_extends/admin/order/LC_Page_Admin_InosImportTeiki_Ex.php';

$objPage = new LC_Page_Admin_InosImportTeiki_Ex();
register_shutdown_function(array($objPage, 'destroy'));
$objPage->init();
$objPage->process();
?>
