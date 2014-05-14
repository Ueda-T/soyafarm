<?php
/**
 * carrier.php - キャリア決済処理の呼び出しファイル
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: carrier.php 193 2013-07-31 01:24:57Z kaji $
 * @link        http://www.veritrans.co.jp/3gps
*/

// {{{ requires
require_once realpath(dirname(__FILE__) . '/../define.php');
require_once MDL_SBIVT3G_PAGE_PATH . 'LC_Page_SBIVT3G_Carrier.php';

// }}}
// {{{ generate page

$objPage = new LC_Page_SBIVT3G_Carrier();
register_shutdown_function(array($objPage, 'destroy'));
$objPage->init();
$objPage->process();

?>
