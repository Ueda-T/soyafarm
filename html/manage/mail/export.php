<?php
require_once '../require.php';
require_once CLASS_EX_REALDIR . 'page_extends/admin/mail/LC_Page_Admin_Mail_Export_Ex.php';

$objPage = new LC_Page_Admin_Mail_Export_Ex();
register_shutdown_function(array($objPage, 'destroy'));
$objPage->init();
$objPage->process();
?>
