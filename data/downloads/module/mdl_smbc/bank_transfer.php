<?php
require_once(MODULE_REALDIR . 'mdl_smbc/inc/include.php');
require_once(MDL_SMBC_CLASS_PATH . "pages/LC_Page_Mdl_SMBC_BankTransfer.php");

$objPage = new LC_Page_Mdl_SMBC_BankTransfer();
$objPage->init();
$objPage->process();
?>
