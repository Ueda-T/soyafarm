<?php
require_once '../require.php';
require_once CLASS_EX_REALDIR . 'page_extends/flow/LC_Page_Flow_Ex.php';

// }}}
// {{{ generate page

$objPage = new LC_Page_Flow_Ex();
register_shutdown_function(array($objPage, "destroy"));
$objPage->init();
$objPage->process();
?>
