<?php
/**
 * edy_mail_mobile.php - モバイルEdy(メール型)決済処理の呼び出しファイル
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: edy_mail_mobile.php 43 2011-08-03 09:35:33Z hira $
 * @link        http://www.veritrans.co.jp/3gps
*/

// {{{ requires
require_once realpath(dirname(__FILE__) . '/../define.php');
require_once MDL_SBIVT3G_PAGE_PATH . 'LC_Page_SBIVT3G_EdyMail.php';

// }}}
// {{{ generate page

$objPage = new LC_Page_SBIVT3G_EdyMail();
register_shutdown_function(array($objPage, 'destroy'));
$objPage->init();
$objPage->process();

?>
