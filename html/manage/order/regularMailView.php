<?php
require_once '../require.php';
require_once CLASS_REALDIR . 'pages/admin/order/LC_Page_Admin_Order_RegularMailView.php';

$objPage = new LC_Page_Admin_Order_RegularMailView();
register_shutdown_function(array($objPage, 'destroy'));
$objPage->init();
$objPage->process();
?>
