<?php
/**
 * sbivt3g_status.php - 3Gモジュール 管理画面 Veritrans 3G 専用ステータス管理の呼び出しファイル
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: sbivt3g_status.php 175 2012-07-25 05:44:03Z hira $
 * @link        http://www.veritrans.co.jp/3gps
*/

// {{{ requires
require_once '../require.php';

require_once realpath(MODULE_REALDIR . 'mdl_sbivt3g/define.php');

require_once MDL_SBIVT3G_PAGE_PATH . 'LC_Page_SBIVT3G_Status.php';

// }}}
// {{{ generate page

$objPage = new LC_Page_SBIVT3G_Status();
register_shutdown_function(array($objPage, 'destroy'));
$objPage->init();
$objPage->process();
?>
