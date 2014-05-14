<?php
// {{{ requires
require_once '../require.php';
require_once CLASS_REALDIR . 'pages/products/LC_Page_Products_Catalogue.php';

// }}}
// {{{ generate page

$objPage = new LC_Page_Products_Catalogue();
register_shutdown_function(array($objPage, "destroy"));
$objPage->init();
$objPage->process();
?>
