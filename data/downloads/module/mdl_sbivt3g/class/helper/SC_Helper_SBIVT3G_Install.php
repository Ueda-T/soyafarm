<?php
/*
 * LC_Page_SBIVT3G.php - LC_Mdl_SBIVT3Gクラスを定義
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: SC_Helper_SBIVT3G_Install.php 210 2013-12-19 12:10:49Z takao $
 * @link        http://www.veritrans.co.jp/3gps
 */

/**
 *
 * 当該モジュール 設定情報クラス.
 *
 * @category  Veritrans
 * @package   Lib
 * @copyright 2011 SBI VeriTrans Co., Ltd.
 * @license   http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version   Release: @package_version@
 * @link      http://www.veritrans.co.jp/3gps
 * @access    public
 * @author    
 */
class SC_Helper_SBIVT3G_Install {

    // {{{ properties
    var $arrPaymentTypes;
    // }}}
    // {{{ functions

    /**
     * コンストラクタ
     *
     * @access public
     * @return void
     */
    function SC_Helper_SBIVT3G_Install() {
        $this->__counstruct();
    }

    /**
     * コンストラクタ
     *
     * @access public
     * @return void
     */
    function __counstruct() {
        $this->init();
    }

    /**
     * シングルトンパターン
     *
     * @access public
     * @return SC_Helper_SBIVT3G_Install シングルトン・インスタンス
     */
    function getSingletonInstance() {
        $myName = '_SC_Helper_SBIVT3G_Install_instance';
        if (isset($GLOBALS[$myName]) == false
                || get_class($GLOBALS[$myName]) != "SC_Helper_SBIVT3G_Install") {
            $GLOBALS[$myName] =& new SC_Helper_SBIVT3G_Install();
        }
        return $GLOBALS[$myName];
    }

    /**
     * 初期化処理
     *
     * @access public
     * @return void
     */
    function init() {
        // 支払い方法の初期化
        $this->arrPaymentTypes = $this->getPaymentTypes();
    }

    /**
     * ログ出力
     *
     * @access public
     * @param  string $msg ログ文字列
     * @return void
     */
    function printLog($msg) {
        $path = DATA_REALDIR . 'logs/mdl_sbivt3g.log';
        GC_Utils_Ex::gfPrintLog($msg, $path);
    }

    /**
     * インストールするファイルを配列で返す
     *
     * @access public
     * @return $baseFiles
     */
    function getInstallFiles() {
        $classExRealDir = realpath(CLASS_EX_REALDIR). '/';
        $dataRealDir    = realpath(DATA_REALDIR). '/';

        // インストールファイル
        $base = MDL_SBIVT3G_INSTALL_FILE_PATH;
        $baseFiles = array(
            // ▼2013.11.22 add start 2.13.0対応
            'LC_Page_Ex.php' => array(
                'src' => $base . 'LC_Page_Ex.php',
                'dst' => $classExRealDir . 'page_extends/LC_Page_Ex.php',
            ),
            // ▲2013.11.22 add end
            // ▼2013.12.19 add start 2.13.0対応
            'SC_Helper_Payment_Ex.php' => array(
                'src' => $base . 'SC_Helper_Payment_Ex.php',
                'dst' => $classExRealDir. 'helper_extends/SC_Helper_Payment_Ex.php',
            ),
            // ▲2013.12.19 add end
            'SC_Helper_Purchase_Ex.php' => array(
                'src' => $base . 'SC_Helper_Purchase_Ex.php',
                'dst' => $classExRealDir. 'helper_extends/SC_Helper_Purchase_Ex.php',
            ),
            'LC_Page_Shopping_Complete_Ex.php' => array(
                'src' => $base . 'LC_Page_Shopping_Complete_Ex.php',
                'dst' => $classExRealDir . 'page_extends/shopping/LC_Page_Shopping_Complete_Ex.php',
            ),
            'LC_Page_Admin_Order_Edit_Ex.php' => array(
                'src' => $base . 'LC_Page_Admin_Order_Edit_Ex.php',
                'dst' => $classExRealDir . 'page_extends/admin/order/LC_Page_Admin_Order_Edit_Ex.php',
            ),
            'LC_Page_Admin_Order_Disp_Ex.php' => array(
                'src' => $base . 'LC_Page_Admin_Order_Disp_Ex.php',
                'dst' => $classExRealDir . 'page_extends/admin/order/LC_Page_Admin_Order_Disp_Ex.php',
            ),
            'sbivt3g_status.php' => array(
                'src' => $base . 'html_admin_order/sbivt3g_status.php',
                'dst' => HTML_REALDIR . ADMIN_DIR . 'order/sbivt3g_status.php',
            ),
            'res.php' => array(
                'src' => $base . 'html_sbivt3g/res.php',
                'dst' => HTML_REALDIR . MDL_SBIVT3G_RECEIVE_URLPATH,
            ),
            'htaccess' => array(
                'src' => $base . 'html_sbivt3g/htaccess.txt',
                'dst' => HTML_REALDIR . MDL_SBIVT3G_RECEIVE_DIR . '.htaccess',
            ),
            // 2012/07/24 MDKバージョンアップに伴い追加
            'tradv2.js' => array(
                'src' => MDL_SBIVT3G_TRAD_JS_PATH,
                'dst' => HTML_REALDIR . MDL_SBIVT3G_TRAD_JS_SET_PATH,
            ),
            'edit.tpl' => array(
                'src' => $base . 'templates_admin_order/edit.tpl',
                'dst' => $dataRealDir . 'Smarty/templates/admin/order/edit.tpl',
            ),
            'disp.tpl' => array(
                'src' => $base . 'templates_admin_order/disp.tpl',
                'dst' => $dataRealDir . 'Smarty/templates/admin/order/disp.tpl',
            ),
            'subnavi.tpl' => array(
                'src' => $base . 'templates_admin_order/subnavi.tpl',
                'dst' => $dataRealDir . 'Smarty/templates/admin/order/subnavi.tpl',
            ),
            // 2011/10/03 凍結
            //'mobile_complete.tpl' => array(
            //    'src' => 'templates_mobile_shopping/complete.tpl',
            //    'dst' => $dataRealDir . 'Smarty/templates/'. MOBILE_TEMPLATE_NAME . '/shopping/complete.tpl',
            //),
        );

        //// 2013/11/20 更新
        // バージョンごとにファイルを変更
        if (GC_Utils_SBIVT3G::compareVersion('2.12.6') <= 0) {
            // 2.12.0～2.12.6用テンプレート
            $dir = 'less_than_2_12_6';
            $baseFiles['edit.tpl']['src'] =
                 $base . 'templates_admin_order/'.$dir.'/edit.tpl';
            $baseFiles['disp.tpl']['src'] =
                $base . 'templates_admin_order/'.$dir.'/disp.tpl';
            $baseFiles['subnavi.tpl']['src'] =
                $base . 'templates_admin_order/'.$dir.'/subnavi.tpl';

            // 2013.11.22 add
            unset($baseFiles['LC_Page_Ex.php']);
            // 2013.12.19 add
            unset($baseFiles['SC_Helper_Payment_Ex.php']);
        }
        if (GC_Utils_SBIVT3G::compareVersion('2.11.5') <= 0) {
            // 2.11.4, 2.11.5用テンプレート
            $dir = 'less_than_2_11_5';
            $baseFiles['edit.tpl']['src'] =
                 $base . 'templates_admin_order/'.$dir.'/edit.tpl';
            $baseFiles['disp.tpl']['src'] =
                $base . 'templates_admin_order/'.$dir.'/disp.tpl';

            // 2013.11.22 add
            unset($baseFiles['LC_Page_Ex.php']);
            // 2013.12.19 add
            unset($baseFiles['SC_Helper_Payment_Ex.php']);
        }
        if (GC_Utils_SBIVT3G::compareVersion('2.11.2') <= 0) {
            // 2.11.2用テンプレート
            $dir = 'less_than_2_11_2';
            $baseFiles['subnavi.tpl']['src'] =
                $base . 'templates_admin_order/'.$dir.'/subnavi.tpl';
            $baseFiles['disp.tpl']['src'] =
                $base . 'templates_admin_order/'.$dir.'/disp.tpl';

            // 2013.11.22 add
            unset($baseFiles['LC_Page_Ex.php']);
            // 2013.12.19 add
            unset($baseFiles['SC_Helper_Payment_Ex.php']);
        }
        if (GC_Utils_SBIVT3G::compareVersion('2.11.1') <= 0) {
            $dir = 'less_than_2_11_1';
            // 2.11.0、2.11.1用テンプレート
            $baseFiles['edit.tpl']['src'] =
                $base . 'templates_admin_order/'.$dir.'/edit.tpl';
            $baseFiles['subnavi.tpl']['src'] =
                $base . 'templates_admin_order/'.$dir.'/subnavi.tpl';

            // disp.tplは不要
            unset($baseFiles['LC_Page_Admin_Order_Disp_Ex.php']);
            unset($baseFiles['disp.tpl']);

            // 2013.11.22 add
            unset($baseFiles['LC_Page_Ex.php']);
            // 2013.12.19 add
            unset($baseFiles['SC_Helper_Payment_Ex.php']);
        }
        
        return $baseFiles;
    }

    /**
     * MDKライブラリファイルの補完リスト
     *
     * @access public
     * @return $baseFiles
     */
    function getCompletionFiles() {

        $arrMdkFiles = array(
            '3GPSMDK.properties' => array(
                'src' => '3GPSMDK.txt',
                'dst' => MDL_SBIVT3G_TGMDK_BASE.'3GPSMDK.properties',
            ),
            'log4php.properties' => array(
                'src' => 'log4php.txt',
                'dst' => MDL_SBIVT3G_TGMDK_BASE.'log4php.properties',
            ),
            'dto.properties' => array(
                'src' => 'dto.txt',
                'dst' => MDL_SBIVT3G_TGMDK_BASE.'Lib'.DS.'tgMdkDto'.DS.'dto.properties',
            ),
            'errormessage.properties' => array(
                'src' => 'errormessage.txt',
                'dst' => MDL_SBIVT3G_TGMDK_BASE.'Lib'.DS.'errormessage.properties',
            ),
            'mdkinternal.properties' => array(
                'src' => 'mdkinternal.txt',
                'dst' => MDL_SBIVT3G_TGMDK_BASE.'Lib'.DS.'mdkinternal.properties',
            ),
            'cert.pem' => array(
                'src' => 'cert.txt',
                'dst' => MDL_SBIVT3G_TGMDK_INSTALLED_PATH.'resources'.DS.'cert.pem',
            ),
            'GWSoapSecurityCommandRcvService.xml' => array(
                'src' => 'GWSoapSecurityCommandRcvService.txt',
                'dst' => MDL_SBIVT3G_TGMDK_BASE.'Lib'.DS.'GWSoapSecurityCommandRcvService.xml',
            ),
        );
        return $arrMdkFiles;
    }

    /**
     * 決済方法を配列で返す
     *
     * @access public
     * @return array
     */
    function getPaymentTypes() {
        $arrPaymentTypes = array(
            // クレジット決済
            MDL_SBIVT3G_INNER_ID_CREDIT => array(
                'module_path' => MDL_SBIVT3G_HTML_PATH . 'credit.php',
                'memo01' => MDL_SBIVT3G_INNER_ID_CREDIT,
                'payment_method' => PAYMENT_NAME_CREDIT,
                'upper_rule' => PAYMENT_UPPER_RULE_CREDIT,
                'rule' => PAYMENT_RULE_CREDIT,
            ),
            // コンビニ決済
            MDL_SBIVT3G_INNER_ID_CVS => array(
                'module_path' => MDL_SBIVT3G_HTML_PATH . 'cvs.php',
                'memo01' => MDL_SBIVT3G_INNER_ID_CVS,
                'payment_method' => PAYMENT_NAME_CONVENI,
                'upper_rule' => PAYMENT_UPPER_RULE_CONVENI,
                'rule' => PAYMENT_RULE_CONVENI,
            ),
            // 銀行決済(Pay-easy ATM)
            MDL_SBIVT3G_INNER_ID_PAYEASY_ATM => array(
                'module_path' => MDL_SBIVT3G_HTML_PATH . 'payeasy_atm.php',
                'memo01' => MDL_SBIVT3G_INNER_ID_PAYEASY_ATM,
                'payment_method' => PAYMENT_NAME_ATM,
                'upper_rule' => PAYMENT_UPPER_RULE_PAYEASY,
                'rule' => PAYMENT_RULE_PAYEASY,
            ),
            // 銀行決済(Pay-easy ネットバンキング)
            MDL_SBIVT3G_INNER_ID_PAYEASY_NET => array(
                'module_path' => MDL_SBIVT3G_HTML_PATH . 'payeasy_net.php',
                'memo01' => MDL_SBIVT3G_INNER_ID_PAYEASY_NET,
                'payment_method' => PAYMENT_NAME_NETBANK,
                'upper_rule' => PAYMENT_UPPER_RULE_PAYEASY,
                'rule' => PAYMENT_RULE_PAYEASY,
            ),
            // モバイルEdy
            MDL_SBIVT3G_INNER_ID_EDY_MOBILE_MAIL => array(
                'module_path' => MDL_SBIVT3G_HTML_PATH . 'edy_mail_mobile.php',
                'memo01' => MDL_SBIVT3G_INNER_ID_EDY_MOBILE_MAIL,
                'payment_method' => PAYMENT_NAME_EDY_MOBILE,
                'upper_rule' => PAYMENT_UPPER_RULE_EDY,
                'rule' => PAYMENT_RULE_EDY,
            ),
            // サイバーEdy
            MDL_SBIVT3G_INNER_ID_EDY_PC_APP => array(
                'module_path' => MDL_SBIVT3G_HTML_PATH . 'edy_app_pc.php',
                'memo01' => MDL_SBIVT3G_INNER_ID_EDY_PC_APP,
                'payment_method' => PAYMENT_NAME_EDY_CYBER,
                'upper_rule' => PAYMENT_UPPER_RULE_EDY,
                'rule' => PAYMENT_RULE_EDY,
            ),
            // モバイルSuica(メール型)
            MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_MAIL => array(
                'module_path' => MDL_SBIVT3G_HTML_PATH . 'suica_mail_mobile.php',
                'memo01' => MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_MAIL,
                'payment_method' => PAYMENT_NAME_SUICA_MOBILE_MAIL,
                'upper_rule' => PAYMENT_UPPER_RULE_SUICA,
                'rule' => PAYMENT_RULE_SUICA,
            ),
            // モバイルSuica(アプリ型)
            MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_APP => array(
                'module_path' => MDL_SBIVT3G_HTML_PATH . 'suica_app_mobile.php',
                'memo01' => MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_APP,
                'payment_method' => PAYMENT_NAME_SUICA_MOBILE_APP,
                'upper_rule' => PAYMENT_UPPER_RULE_SUICA,
                'rule' => PAYMENT_RULE_SUICA
            ),
            // Suicaインターネットサービス(メール型)
            MDL_SBIVT3G_INNER_ID_SUICA_PC_MAIL => array(
                'module_path' => MDL_SBIVT3G_HTML_PATH . 'suica_mail_pc.php',
                'memo01' => MDL_SBIVT3G_INNER_ID_SUICA_PC_MAIL,
                'payment_method' => PAYMENT_NAME_SUICA_PC_MAIL,
                'upper_rule' => PAYMENT_UPPER_RULE_SUICA,
                'rule' => PAYMENT_RULE_SUICA,
            ),
            // Suicaインターネットサービス(アプリ型)
            MDL_SBIVT3G_INNER_ID_SUICA_PC_APP => array(
                'module_path' => MDL_SBIVT3G_HTML_PATH . 'suica_app_pc.php',
                'memo01' => MDL_SBIVT3G_INNER_ID_SUICA_PC_APP,
                'payment_method' => PAYMENT_NAME_SUICA_PC_APP,
                'upper_rule' => PAYMENT_UPPER_RULE_SUICA,
                'rule' => PAYMENT_RULE_SUICA,
            ),
            // Waon(モバイル版)
            MDL_SBIVT3G_INNER_ID_WAON_MOBILE_APP => array(
                'module_path' => MDL_SBIVT3G_HTML_PATH . 'waon_app_mobile.php',
                'memo01' => MDL_SBIVT3G_INNER_ID_WAON_MOBILE_APP,
                'payment_method' => PAYMENT_NAME_WAON_MOBILE,
                'upper_rule' => PAYMENT_UPPER_RULE_WAON,
                'rule' => PAYMENT_RULE_WAON,
            ),
            // Waon(PC版)
            MDL_SBIVT3G_INNER_ID_WAON_PC_APP => array(
                'module_path' => MDL_SBIVT3G_HTML_PATH . 'waon_app_pc.php',
                'memo01' => MDL_SBIVT3G_INNER_ID_WAON_PC_APP,
                'payment_method' => PAYMENT_NAME_WAON_PC,
                'upper_rule' => PAYMENT_UPPER_RULE_WAON,
                'rule' => PAYMENT_RULE_WAON,
            ),
            // PayPal決済
            MDL_SBIVT3G_INNER_ID_PAYPAL => array(
                'module_path' => MDL_SBIVT3G_HTML_PATH . 'paypal.php',
                'memo01' => MDL_SBIVT3G_INNER_ID_PAYPAL,
                'payment_method' => PAYMENT_NAME_PAYPAL,
                'upper_rule' => PAYMENT_UPPER_RULE_PAYPAL,
                'rule' => PAYMENT_RULE_PAYPAL,
            ),
            // キャリア決済
            MDL_SBIVT3G_INNER_ID_CARRIER => array(
                'module_path' => MDL_SBIVT3G_HTML_PATH . 'carrier.php',
                'memo01' => MDL_SBIVT3G_INNER_ID_CARRIER,
                'payment_method' => PAYMENT_NAME_CARRIER,
                'upper_rule' => PAYMENT_UPPER_RULE_CARRIER,
                'rule' => PAYMENT_RULE_CARRIER,
            ),
        );
        return $arrPaymentTypes;
    }

    /*
     * バックアップ作成
     *
     * @access public
     * @param  string $backupDir  対象ディレクトリ
     * @param  array $arrFiles ファイルリスト
     * @param  array $arrFailed 失敗ファイルリスト
     * @return bool 成功/失敗
     */
    function doBackUp($backupDir, $arrFiles, &$arrFailed) {
        $arrFailed = array();
        $isOk = true;

        $this->printLog("ファイルバックアップ開始");
        foreach ($arrFiles as $arrFile) {
            $srcPath = $arrFile['dst'];

            // 絶対パスを差し替えてバックアップ先パスを作成
            $htmlPath = realpath(HTML_REALDIR);
            $dataPath = realpath(DATA_REALDIR);
            $replaceDir = '';
            if (strncmp($srcPath, $htmlPath, strlen($htmlPath)) == 0) {
                $replaceDir = dirname($htmlPath);
            } else if (strncmp($srcPath, $dataPath, strlen($dataPath)) == 0) {
                $replaceDir = dirname($dataPath);
            } else {
                // どちらでもないものは除外(存在しないが)
                continue;
            }
            $dstPath = str_replace($replaceDir, $backupDir, $srcPath);

            if (file_exists($srcPath) == false) {
                // 存在しないファイルは対象外
                $this->printLog("バックアップ対象外 $srcPath");
                continue;
            }

            // 同一ファイルであればスキップ
            if(sha1_file($srcPath) == sha1_file($dstPath)) {
                $this->printLog("同一のためバックアップ見送り $srcPath");
                continue;
            }

            // ディレクトリ作成
            $dstDir = dirname($dstPath);
            if (!is_writable($dstDir) && !mkdir($dstDir, 0777, true)) {
                $isOk = false;
                $arrFailed[] = $dstPath;
                $this->printLog("バックアップディレクトリ作成失敗 $dstDir");
                continue;
            }

            // コピー
            if (copy($srcPath, $dstPath) == false) {
                $isOk = false;
                $arrFailed[] = $srcPath;
                $this->printLog("バックアップ失敗 $srcPath -> $dstPath");
                continue;
            }
            $this->printLog("バックアップ作成 $srcPath -> $dst_path");
        }
        $this->printLog("ファイルバックアップ終了");
        return $isOk;
    }

    /*
     * ファイルを上書き配置
     *
     * @access public
     * @param array $arrFiles
     * @return boolean 成功/失敗
     */
    function doInstall($arrFiles) {
        $arrFailed = array();
        $isOk = true;

        $this->printLog("ファイルインストール開始");

        foreach($arrFiles as $name => $file) {
            $dstPath = $file['dst'];
            $srcPath = $file['src'];

            // ファイルが既にあって、同一ファイルであればスキップ
            if(file_exists($dstPath) == true
            && sha1_file($srcPath) == sha1_file($dstPath)) {
                $this->printLog("同一ファイルのためコピー見送り $srcPath");
                continue;
            }

            // ディレクトリ作成
            $dstDir = dirname($dstPath);
            if (!is_writable($dstDir) && !mkdir($dstDir, 0777, true)) {
                $isOk = false;
                $arrFailed[] = $dstPath;
                $this->printLog("インストールディレクトリ作成失敗 $dstDir");
                continue;
            }

            // 上書きを試みる
            if (copy($srcPath, $dstPath) == false) {
                $this->printLog("インストール失敗 $srcPath -> $dstPath");
                if ($name == 'htaccess') {
                    // 見送り
                    $this->printLog("$srcPath インストールを見送ります");
                    continue;
                }
                $isOk = false;
                $arrFailed[] = $dstPath;
                continue;
            }

            // 2012/07/24 さくらインターネット等でのセーフモード対策
            if (strncmp(HTML_REALDIR, $dstPath, strlen(HTML_REALDIR)) == 0) {
                @chmod($dstPath, 0644);
            }

            $this->printLog("インストール成功 $srcPath -> $dstPath");
        }
        $this->printLog("ファイルインストール終了");
        return $arrFailed;
    }


    /**
     * .propertiesのファイルがなければ差し替え
     *
     * @access public
     * @param  string &$error エラーメッセージ
     * @param  string $dst    .propertiesファイル
     * @param  string $extra  予備ファイル
     * @return boolean
     */
    function setCompletionProperties(&$error, $dst, $extra){

        $error = '';

        // 有無を確認
        if (@file_exists($dst) == false) {
            // まずディレクトリの有無を確認
            if (@is_dir(dirname($dst)) == false) {
                // 無ければ生成
                $this->printLog('ディレクトリ生成['.dirname($dst).']');
                if (@mkdir(dirname($dst), 0777, true) == false) {
                    $error = dirname($dst) . 'の生成に失敗しました。' . LF;
                    $this->printLog($error);
                    return false;
                }
            }
            // 書き込み権限付与
            @chmod(dirname($dst), 0777);
            // 予備ファイルを探す
            if (@is_readable($extra) == false) {
                $error = $extra . 'が見つかりません。' . LF;
                $this->printLog($error);
                return false;
            }
            // 予備ファイルをコピー
            $this->printLog('ファイルコピー['.$extra.']->['.$dst.']');
            if (@copy($extra, $dst) == false) {
                $error = $dst . 'の生成に失敗しました。' . LF;
                $this->printLog($error);
                return false;
            }
        }
        // 書き込み権限付与
        @chmod($dst, 0666);
        return true;
    }

    /**
     * .propertiesのファイルチェック
     *
     * @access public
     * @param  string &$error エラーメッセージ
     * @param  string $path    .propertiesファイル
     * @return boolean
     */
    function checkProperties(&$error, $path) {

        $error = '';

        // 書き込み権限確認
        if (@is_writable($path) == false) {
            $error = $path . 'のファイルを書き込み可能に変更してください' . LF; 
            $this->printLog($error);
            return false;
        }
        return true;
    }

    /**
     * 3GPSMDK.propertiesの更新
     *
     * @access public
     * @param  array $arrForm 
     * @return none
     */
    function setMdkProperties($arrForm){

        // 3GPSMDK.properties の読み込み
        $path = MDL_SBIVT3G_PROPERTIES_PATH;
        $arrPropertiesFile = file($path);
        if ($arrPropertiesFile === false) {
            return;
        }

        foreach($arrPropertiesFile as $key => $val) {
            // ダミーモードフラグ
            if(strstr($val,"DUMMY_REQUEST")){
                if($arrForm["dummyModeFlg"] == 1) {
                $arrPropertiesFile[$key] = 
                    "DUMMY_REQUEST                  = " . $arrForm["dummyModeFlg"] . "\r\n";
                } else {
                    $arrPropertiesFile[$key] = 
                    "DUMMY_REQUEST                  = 0" . "\r\n";
                }
            }
            
            // SSL暗号用 CA証明書ファイル名
            if(strstr($val,"CA_CERT_FILE")){
                $arrPropertiesFile[$key] =
                    "CA_CERT_FILE                   = " . realpath(MDL_SBIVT3G_TGMDK_INSTALLED_PATH) . "/resources/cert.pem". "\r\n";
            }
            
            // マーチャントCCID
            if(strstr($val,"MERCHANT_CC_ID")){
                $arrPropertiesFile[$key] =
                    "MERCHANT_CC_ID                 = ". $arrForm["merchantCcId"] ."\r\n";
            }
            
            // マーチャントパスワード
            if(strstr($val,"MERCHANT_SECRET_KEY")){
                $arrPropertiesFile[$key] =
                    "MERCHANT_SECRET_KEY            = ". $arrForm["merchantPass"] ."\r\n";
            }
        }
        $line = join("",$arrPropertiesFile);
        $file = fopen($path, "w+");
        $fwrite = fwrite($file, $line);
        
        fclose($file);
    }

    /**
     * log4php.propertiesの更新
     *
     * @access public
     * @param  none 
     * @return none
     */
    function setLogProperties() {
        // log4php.properties の読み込み
        $path = MDL_SBIVT3G_LOG_PROPERTIES_PATH;
        $arrPropertiesFile = file($path);
        if ($arrPropertiesFile === false) {
            return;
        }
        
        foreach($arrPropertiesFile as $key => $val) {
            if(strstr($val,"log4php.appender.R1.File")){
                $arrPropertiesFile[$key] =
                    "log4php.appender.R1.File=" . realpath(DATA_REALDIR) ."/logs/mdk.log". "\r\n";
            }
        }
        $line = join("",$arrPropertiesFile);
        $file = fopen($path, "w+");
        fwrite($file, $line);
        fclose($file);
    }

    /**
     * 支払い方法テーブルを更新する.
     * 
     * @access public
     * @param  $paymentType
     * @return void
     */
    function updatePaymentTable($paymentType) {
        $moduleCode = MDL_SBIVT3G_MODULE_CODE;

        // 支払い方法設定を取得
        $arrFields = array();
        if (isset($this->arrPaymentTypes[$paymentType]) == true) {
            $arrFields = $this->arrPaymentTypes[$paymentType];
        }

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objSess = new SC_Session_Ex();

        // 登録データ作成
        $arrPaymentInfo = array(
            "fix"            => '3',
            "del_flg"        => '0',
            'memo03'         => '###', 
            "creator_id"     => $objSess->member_id,
            "update_date"    => GC_Utils_SBIVT3G::getNowExpression(),
        );

        $arrPaymentInfo = array_merge($arrPaymentInfo, $arrFields);
        $payment_id = $objQuery->getOne('SELECT payment_id FROM dtb_payment WHERE memo01 = ?', array($paymentType));

        // 手数料のNULLを0にする
        $charge = $objQuery->getOne('SELECT charge FROM dtb_payment WHERE memo01 = ?', array($paymentType));
        $arrPaymentInfo["charge"] = ($charge > 0)? $charge : '0';

        // 2012/7/27 2.12からのdtb_paymentのフィールド名改変に伴う
        if (GC_Utils_SBIVT3G::compareVersion('2.12.0') >= 0
            && isset($arrPaymentInfo['rule']) == true
        ) {
            // rule => rule_max
            $rule_max = $arrPaymentInfo['rule'];
            $arrPaymentInfo['rule_max'] = $rule_max;
            unset($arrPaymentInfo['rule']);
        }

        if($payment_id) {
            $arrPaymentInfo['payment_id'] = $payment_id;
            $objQuery->update("dtb_payment", $arrPaymentInfo, "memo01 = ?", array($paymentType));
        } else {
            $arrPaymentInfo['payment_id'] = $objQuery->nextVal('dtb_payment_payment_id');

            // rankの最大値取得
            $max_rank = $objQuery->getOne("SELECT max(rank) FROM dtb_payment");
            $arrPaymentInfo['rank'] = $max_rank + 1;

            $objQuery->insert("dtb_payment", $arrPaymentInfo);
        }
    }

    /**
     * 支払い方法を削除する
     *
     * @access public
     * @param  $paymentType
     * @return void
     */
    function deletePaymentTable($paymentType) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->update(
            "dtb_payment", array('del_flg' => '1'),
            "memo01 = ?", array($paymentType)
        );
    }

    /**
     * 対象メールテンプレートを設定
     *
     * @access protected
     * @param  SC_DB_MasterData $objMasterData
     * @param  string $name テンプレート名
     * @param  string $path テンプレートパス
     * @param  integer $default デフォルトキー(万が一のため)
     * @param  boolean $set true:設定 false:削除
     * @return none
     */
    function setMtbMailTemplate($objMasterData, $name, $path, $default,
        $set = true) {

        // トランザクション開始
        $objMasterData->objQuery =& SC_Query_Ex::getSingletonInstance();
        $objMasterData->objQuery->begin();

        // メール用マスタ取得
        $arrMtbMailTemplate =
            $objMasterData->getDbMasterData('mtb_mail_template');
        $arrMtbMailTplPath =
            $objMasterData->getDbMasterData('mtb_mail_tpl_path');

        // テンプレート名が合致するものを探す
        $arrTplKey = array_keys($arrMtbMailTemplate, $name);

        // 同じキーのテンプレートとファイルパスがあれば一旦消去
        if (count($arrTplKey) > 0) {
            foreach ($arrTplKey as $key) {
                unset($arrMtbMailTemplate[$key]);
            }
            foreach ($arrTplKey as $key) {
                unset($arrMtbMailTplPath[$key]);
            }
        }
        if ($set == true) { // 設定
            // 設定する
            if (isset($arrTplKey[0]) == true) {
                $newKey = $arrTplKey[0];
            } else {
                // 新たに採番
                $neweKey = $default;
                for ($i = 1; $i < 1000; $i++) {
                    if (isset($arrMtbMailTemplate[$i]) == false
                    && isset($arrMtbMailTplPath[$i]) == false) {
                        $newKey = $i;
                        break;
                    }
                }
            }
            $arrMtbMailTemplate[$newKey] = $name;
            $arrMtbMailTplPath[$newKey] = $path;

        } else { // 設定解除
            // 該当のdtb_mailtemplateを消去
            foreach ($arrTplKey as $key) {
                $objMasterData->objQuery->delete(
                    'dtb_mailtemplate', 'template_id = ?', array($key));
            }
        }

        // 更新
        $arrMtb = array(
            'mtb_mail_template' => $arrMtbMailTemplate,
            'mtb_mail_tpl_path' =>  $arrMtbMailTplPath,
        );
        foreach ($arrMtb as $mtb => $arr) {
            $objMasterData->deleteMasterData($mtb, false);
            $objMasterData->registMasterData($mtb,
                array('id', 'name', 'rank'),
                $arr, false);
        }
        $objMasterData->objQuery->commit();
    }
}
?>
