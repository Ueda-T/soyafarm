<?php

// rtrim は PHP バージョン依存対策
define('HTML_REALDIR', rtrim(realpath(rtrim(realpath(dirname(__FILE__)), '/\\') . '/../'), '/\\') . '/');
define('ADMIN_FUNCTION', true);

require_once HTML_REALDIR . 'define.php';
require_once HTML_REALDIR . 'handle_error.php';

require_once HTML_REALDIR . HTML2DATA_DIR . 'require_base.php';

ob_start();
?>
