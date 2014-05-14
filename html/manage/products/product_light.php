<?php
// {{{ requires
require_once '../require.php';
require_once CLASS_EX_REALDIR . 'page_extends/admin/products/LC_Page_Admin_Products_ProductLight_Ex.php';

// }}}
// {{{ generate page

$objPage = new LC_Page_Admin_Products_ProductLight_Ex();
register_shutdown_function(array($objPage, 'destroy'));
$objPage->init();
$objPage->process();
?>
