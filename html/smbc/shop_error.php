<?php
// {{{ requires PC
require_once("../require.php");

unset($_SESSION["MOVE_SMBC"]);

//header('Location: ' . HTTP_URL);
header('Location: ' . SHOPPING_CONFIRM_URLPATH);
exit;
