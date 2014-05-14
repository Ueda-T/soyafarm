<?php
require_once '../require.php';
require_once CLASS_REALDIR . 'pages/admin/customer/LC_Page_Admin_Customer_InosImportCustomer.php';

$objPage = new LC_Page_Admin_Customer_InosImportCustomer();
register_shutdown_function(array($objPage, 'destroy'));
$objPage->init();
$objPage->process();
?>
