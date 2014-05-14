<?php

/**
 * メルマガ予約配信バッチファイル
 *
 * @package File
 * @author IQUEVE CO.,LTD.
 * @version $Id: sendmail.php 110 2012-04-24 05:41:18Z hira $
 */

// cron設定時は以下のようにcrontabに記述する
//0,30 * * * * /usr/bin/php (絶対パス)/batch/sendmail.php >/dev/null 2>&1 

// {{{ requires
require_once realpath(dirname(__FILE__) . '/../html/require.php');
require_once CLASS_EX_REALDIR . 'page_extends/admin/mail/LC_Page_Admin_Mail_Sendmail_Ex.php';

// }}}
// {{{ batch process start

$objPage = new LC_Page_Admin_Mail_Sendmail_Ex();
register_shutdown_function(array($objPage, 'destroy'));
$objPage->init();
$objPage->process();
?>
