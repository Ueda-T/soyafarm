<?php

/**
 * INOSシステム連携 受注インポート処理ファイル
 *
 * @package File
 * @author IQUEVE CO.,LTD.
 * @version $Id:$
 */

// {{{ requires
require_once '../require.php';
require_once CLASS_EX_REALDIR
    . 'page_extends/admin/order/LC_Page_Admin_Order_InosImportOrder_Ex.php';

// }}}
// {{{ generate page

$objPage = new LC_Page_Admin_Order_InosImportOrder_Ex();
register_shutdown_function(array($objPage, 'destroy'));
$objPage->init();
$objPage->process();
?>
