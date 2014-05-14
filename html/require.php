<?php

// rtrim は PHP バージョン依存対策
define('HTML_REALDIR', rtrim(realpath(rtrim(realpath(dirname(__FILE__)), '/\\') . '/'), '/\\') . '/');

if (!defined('ADMIN_FUNCTION') || ADMIN_FUNCTION !== true) {
    define('FRONT_FUNCTION', true);
}

require_once HTML_REALDIR . 'define.php';

if (defined('SAFE') && SAFE === true) {
    require_once HTML_REALDIR . HTML2DATA_DIR . 'require_safe.php';
} else {
    require_once HTML_REALDIR . 'handle_error.php';
    require_once HTML_REALDIR . HTML2DATA_DIR . 'require_base.php';
}

if (SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE) {
    $objMobile = new SC_Helper_Mobile_Ex();
    $objMobile->sfMobileInit();
    ob_start();
    /* ▽モバイル禁止
    header("Location: " . ROOT_URLPATH . "mobile/index.html\r\n");
    exit();
    △モバイル禁止 */

} else {
    // 絵文字変換 (除去) フィルターを組み込む。
    ob_start(array('SC_MobileEmoji', 'handler'));
}
