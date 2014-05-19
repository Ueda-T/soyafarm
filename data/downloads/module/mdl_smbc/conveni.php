<?php
require_once(MODULE_REALDIR . 'mdl_smbc/inc/include.php');
require_once(MDL_SMBC_CLASS_PATH . "pages/LC_Page_Mdl_SMBC_Conveni.php");

$objPage = new LC_Page_Mdl_SMBC_Conveni();
register_shutdown_function(array($objPage, "destroy"));
$objPage->init();
$objPage->process();
?>