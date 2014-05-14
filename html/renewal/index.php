<?php
require_once '../require.php';
require_once CLASS_REALDIR . 'pages/renewal/LC_Page_Renewal.php';



$objPage = new LC_Page_Renewal();
register_shutdown_function(array($objPage, "destroy"));
$objPage->init();
$objPage->process();
?>

