<?php

// {{{ requires
require_once '../require.php';
require_once CLASS_EX_REALDIR . 'page_extends/mypage/LC_Page_Mypage_Regular_Ex.php';

// }}}
// {{{ generate page

$objPage = new LC_Page_Mypage_Regular_Ex();
register_shutdown_function(array($objPage, "destroy"));
$objPage->init();
$objPage->process();
?>
