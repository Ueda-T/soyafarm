<?php
/**
 * res.php - 入金通知処理の呼び出しファイル
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: res.php 44 2011-08-03 09:54:45Z hira $
 * @link        http://www.veritrans.co.jp/3gps
*/

// {{{ requires
require_once '../require.php';
require_once realpath(MODULE_REALDIR . 'mdl_sbivt3g/define.php');
require_once MDL_SBIVT3G_PAGE_PATH . 'LC_Page_SBIVT3G_Receive.php';

// }}}
// {{{ generate page

$objPage = new LC_Page_SBIVT3G_Receive();
register_shutdown_function(array($objPage, 'destroy'));
$objPage->init();
$objPage->process();

?>
