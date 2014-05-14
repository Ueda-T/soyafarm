<?php
require_once '../require.php';
require_once CLASS_REALDIR . 'pages/LC_Page_GetProdNameByCd.php';

$objPage = new LC_Page_GetProdNameByCd();
register_shutdown_function(array($objPage, "destroy"));
$objPage->init();
$objPage->process();
?>
