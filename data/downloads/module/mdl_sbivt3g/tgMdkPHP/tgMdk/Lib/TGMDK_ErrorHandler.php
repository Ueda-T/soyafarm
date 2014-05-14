<?php

/**
 * エラーハンドリング処理。
 * @param <type> $errno
 * @param <type> $errstr
 * @param <type> $errfile
 * @param <type> $errline
 */
function error_handler($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
