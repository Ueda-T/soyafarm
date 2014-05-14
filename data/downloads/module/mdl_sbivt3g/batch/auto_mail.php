<?php
/**
 * auto_mail.php - お支払い期限前・期限切れメール自動配信プログラム
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: auto_mail.php 101 2011-09-02 04:30:56Z hira $
 * @link        http://www.veritrans.co.jp/3gps
*/

// {{{ requires
require_once dirname(__FILE__) . '/../../../../../html/require.php';
require_once realpath(MODULE_REALDIR . 'mdl_sbivt3g/define.php');
require_once MDL_SBIVT3G_PAGE_PATH . 'LC_Page_SBIVT3G_AutoMail.php';

// }}}
// {{{ generate page

$objPage = new LC_Page_SBIVT3G_AutoMail();
register_shutdown_function(array($objPage, 'destroy'));
$objPage->init();
$objPage->process();

?>
