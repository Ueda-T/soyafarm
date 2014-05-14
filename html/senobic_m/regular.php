<?php
require_once '../require.php';
require_once CLASS_REALDIR . 'pages/senobic/LC_Page_Senobic.php';

$objPage = new LC_Page_Senobic();
register_shutdown_function(array($objPage, "destroy"));
$objPage->init();
$objPage->process();
?>
