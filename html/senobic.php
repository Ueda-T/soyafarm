<?php
require_once './require.php';
require_once CLASS_REALDIR . 'pages/products/LC_Page_Products_Senobic.php';

$objPage = new LC_Page_Products_Senobic();
register_shutdown_function(array($objPage, "destroy"));
$objPage->init();
$objPage->process();
?>
