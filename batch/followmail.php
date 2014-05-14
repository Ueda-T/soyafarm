<?php

/**
 * グループ送信(フォローメール)バッチファイル
 *
 * @package File
 * @author IQUEVE CO.,LTD.
 * @version $Id: followmail.php 110 2014-01-23 13:28:18Z nagata $
 */

// {{{ requires
require_once realpath(dirname(__FILE__) . '/../html/require.php');
require_once CLASS_EX_REALDIR . 'page_extends/admin/mail/LC_Page_Admin_Mail_Followmail_Ex.php';

// }}}
// {{{ batch process start

// 引数(指定日付)チェック
if ($argv[1] == null || strlen($argv[1]) == 0) {
    echo "引数が存在しません。処理を中止します。";
    exit;
}

$objPage = new LC_Page_Admin_Mail_Followmail_Ex();
register_shutdown_function(array($objPage, 'destroy'));
$objPage->init();

// 引数(指定日付)をメンバ変数にセット
$objPage->date = $argv[1];
$objPage->process();
?>
