<?php
require_once '../require.php';
require_once CLASS_EX_REALDIR . 'page_extends/links/LC_Page_Links_Ex.php';

// }}}
// {{{ generate page

$objPage = new LC_Page_Links_Ex();
register_shutdown_function(array($objPage, "destroy"));
$objPage->init();
$objPage->process();
?>
