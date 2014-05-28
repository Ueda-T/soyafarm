<?php
// {{{ requires PC
require_once("../require.php");
require_once(MODULE_REALDIR . "mdl_smbc/class/pages/LC_Page_Mdl_SMBC_Shopping_Regular_Complete.php");

// }}}
// {{{ generate page

$objPage = new LC_Page_Mdl_SMBC_Shopping_Regular_Complete();
$objPage->init();
$objPage->process();
?>