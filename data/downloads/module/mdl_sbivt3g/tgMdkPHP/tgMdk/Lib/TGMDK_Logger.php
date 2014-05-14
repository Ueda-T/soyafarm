<?php
if (realpath($_SERVER["SCRIPT_FILENAME"]) == realpath(__FILE__)) die('Permission denied.');

if (!defined('MDK_LIB_DIR')) require_once('../3GPSMDK.php');
require_once(LOG4PHP_DIR . DS . 'LoggerManager.php');

/**
 * TGMDK_Logger ログ出力クラス
 *
 * @category    Veritrans
 * @package     Lib
 * @copyright   VeriTrans Inc.
 */
final class TGMDK_Logger {
// Defines
    /** パッケージ名 */
    const PACKAGE_NAME = 'jp.co.veritrans.3GpsMdk';

    // Data members
    /** ロガー保持変数 */
    private $logger;
    /** 当クラスのインスタンスを保持する変数 */
    private static $instance = NULL;


    /**
     * コンストラクタ（getInstanceのみインスタンス化が可能）
     *
     * @access private
     */
    private function __construct() {
//        $this->logger =& LoggerManager::getLogger(self::PACKAGE_NAME);
        $work = LoggerManager::getLogger(self::PACKAGE_NAME);
        $this->logger =& $work;
    }

    /**
     * デストラクタ
     *
     * @access public
     */
    public function __destruct() {
        LoggerManager::shutdown();
    }

    /**
     * 静的なインスタンスを返す
     *
     * @access public
     * @static
     * @return TGMDK_Logger ロガー
     */
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new TGMDK_Logger();
        }
        return self::$instance;
    }

    /**
     * ロガークラスを取得する。
     *
     * @access private
     * @return Logger ロガークラス
     */
    private function getLogger() { return $this->logger; }


    /**
     * ログ出力設定の Debug レベルの有効/無効 判定
     * 
     * @access public
     * @return boolean 有効な場合 true
     */
    public function isDebugEnabled() {
        return $this->getLogger()->isDebugEnabled();
    }

    /**
     * ログ出力設定の Info レベルの有効/無効 判定
     *
     * @access public
     * @return boolean 有効な場合 true
     */
    public function isInfoEnabled() {
        return $this->getLogger()->isInfoEnabled();
    }

    /**
     * ログ出力設定の Warn レベルの有効/無効 判定
     *
     * @access public
     * @return boolean 有効な場合 true
     */
    public function isWarnEnabled() {
        return $this->logger->isEnabledFor(LoggerLevel::getLevelWarn());
    }

    /**
     * ログ出力設定の Error レベルの有効/無効 判定
     *
     * @access public
     * @return boolean 有効な場合 true
     */
    public function isErrorEnabled() {
    //        return $this->getLogger()->isErrorEnabled();
        return $this->logger->isEnabledFor(LoggerLevel::getLevelError());
    }

    /**
     * ログ出力設定の Fatal レベルの有効/無効 判定
     * 
     * @access public
     * @return boolean 有効な場合 true
     */
    public function isFatalEnabled() {
    //        return $this->getLogger()->isFatalEnabled();
        return $this->logger->isEnabledFor(LoggerLevel::getLevelFatal());
    }

    /**
     * 接続しているマシンのIPアドレスを保持する。
     *
     * @access private
     */
    private function push() {
        $remote = getenv("REMOTE_ADDR");
        if (empty($remote)) {
            $remote = "unknown";
        }
        LoggerNDC::push($remote);
    }

    /**
     * LoggerNDCクラスのpopを呼び出す
     *
     * @access private
     */
    private function pop() {
        LoggerNDC::pop();
    }

    /**
     *
     * Debugレベルでログを出力
     *
     * @access public
     * @param mixed $message: ログ出力するメッセージ
     * @param mixed $caller: callerオブジェクト もしくは callerストリングid
     */
    public function debug($message, $caller=NULL) {
        $this->push();
        $this->getLogger()->debug($message, $caller);
        $this->pop();
    }

    /**
     *
     * Infoレベルでログを出力
     *
     * @access public
     * @param mixed $message: ログ出力するメッセージ
     * @param mixed $caller: callerオブジェクト もしくは callerストリングid
     */
    public function info($message, $caller=NULL) {
        $this->push();
        $this->getLogger()->info($message, $caller);
        $this->pop();
    }

    /**
     *
     * Warnレベルでログを出力
     *
     * @access public
     * @param mixed $message: ログ出力するメッセージ
     * @param mixed $caller: callerオブジェクト もしくは callerストリングid
     */
    public function warn($message, $caller=NULL) {
        $this->push();
        $this->getLogger()->warn($message, $caller);
        $this->pop();
    }

    /**
     *
     * Errorレベルでログを出力
     *
     * @access public
     * @param mixed $message: ログ出力するメッセージ
     * @param mixed $caller: callerオブジェクト もしくは callerストリングid
     */
    public function error($message, $caller=NULL) {
        $this->push();
        $this->getLogger()->error($message, $caller);
        $this->pop();
    }

    /**
     *
     * Fatalレベルでログを出力
     *
     * @access public
     * @param mixed $message: ログ出力するメッセージ
     * @param mixed $caller: callerオブジェクト もしくは callerストリングid
     */
    public function fatal($message, $caller=NULL) {
        $this->push();
        $this->getLogger()->fatal($message, $caller);
        $this->pop();
    }
}
