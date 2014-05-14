<?php

/**
 * 通販システム出荷実績インポート処理ファイル
 *
 * @package File
 * @author IQUEVE CO.,LTD.
 * @version $Id: inos_import.php 68 2012-02-16 18:43:17Z hira $
 */

// {{{ requires
require_once '../require.php';
require_once CLASS_EX_REALDIR . 'page_extends/admin/order/LC_Page_Admin_InosImportShukka_Ex.php';

// }}}
// {{{ generate page

$objPage = new LC_Page_Admin_InosImportShukka_Ex();
register_shutdown_function(array($objPage, 'destroy'));
$objPage->init();
$objPage->process();
?>
