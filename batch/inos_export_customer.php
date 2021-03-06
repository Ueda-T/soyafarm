<?php

/**
 * INOSシステム連携 顧客エクスポート処理ファイル
 *
 * @package File
 * @author IQUEVE CO.,LTD.
 * @version $Id:$
 */

// {{{ requires
require_once realpath(dirname(__FILE__) . '/../html/require.php');
require_once CLASS_EX_REALDIR
    . 'page_extends/admin/customer/LC_Page_Admin_Customer_InosExportCustomer_Ex.php';

// }}}
// {{{ generate page

$objPage = new LC_Page_Admin_Customer_InosExportCustomer_Ex();
register_shutdown_function(array($objPage, 'destroy'));
$objPage->init();
$objPage->process();
?>
