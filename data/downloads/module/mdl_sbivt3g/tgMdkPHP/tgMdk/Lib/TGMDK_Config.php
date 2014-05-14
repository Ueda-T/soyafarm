<?php
if (realpath($_SERVER["SCRIPT_FILENAME"]) == realpath(__FILE__)) die('Permission denied.');

if (!defined('MDK_LIB_DIR')) require_once('../3GPSMDK.php');

/**
 *
 * 設定情報管理クラス
 *
 * 設定ファイルの読み取り、設定ファイルを使用する各クラスへのインターフェイスを提供する
 *
 * @category    Veritrans
 * @package     Lib
 * @copyright   VeriTrans Inc.
 */
class TGMDK_Config
{
    // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    // >>>> インスタンス変数、スタティック（クラス）変数
    // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    /** 当クラスのインスタンスを保持する変数 */
    private static $instance = null;    //  このクラスへの静的なアクセス
    /** プロパティの情報を保持する変数 */
    private static $m_config = null;    // プロパティ連想配列（2次元連想配列）
    /** 例外を配列で保持する変数 */
    private $valids;                    //  Validateエラーの内容を保持する配列
    /** 呼び出すプロパティファイルパスを格納する変数 */
    private $conf_file_name = null;

    // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    // >>>> 定数 - セクション
    // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    /** プロパティから取得するためのセクション情報 － Service */
    const SERVICE_SECTION           = "Service";                // FOR [Service]
    /** プロパティから取得するためのセクション情報 － Cipher */
    const CIPHER_SECTION            = "Cipher";                 // FOR [TGMDK_Cipher]
    /** プロパティから取得するためのセクション情報 － Connection */
    const CONNECTION_SECTION        = "Connection";             // FOR [TGMDK_Connection]
    /** プロパティから取得するためのセクション情報 － Environment */
    const ENVIRONMENT_SECTION       = "Environment";            // FOR [TGMDK_NVQuery]
    /** プロパティから取得するためのセクション情報 － RequestDto */
    const REQUEST_DTO_SECTION       = "RequestDto";             // FOR [RequestDto]
    /** プロパティから取得するためのセクション情報 － ResponseDto */
    const RESPONSE_DTO_SECTION      = "ResponseDto";            // FOR [ResponseDto]
    /** プロパティから取得するためのセクション情報 － Url */
    const URL_SECTION               = "Url";                    // FOR [Url]


    // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    // >>>> 定数 - パラメータキー
    // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

    //  [Service] - 3gpsmdk.properties
    /** プロパティから取得するためのパラメータキー ： ダミーリクエスト */
    const DUMMY_REQUEST                = "DUMMY_REQUEST";                // ダミーモード（テスト時のみ指定）
    /** プロパティから取得するためのパラメータキー ： エラーモード */
    const MDK_ERROR_MODE               = "MDK_ERROR_MODE";               // MDK固有エラーモード(テスト時のみ指定）
    //  [Service] - internal.properties
    /** プロパティから取得するためのパラメータキー ： MDKバージョン */
    const MDK_VERSION                  = "MDK_VERSION";                  //  MDKバージョン
    //  [Service] - dto.properties
    /** プロパティから取得するためのパラメータキー ： MDK DTO バージョン */
    const MDK_DTO_VERSION              = "MDK_DTO_VERSION";              //  MDK DTOバージョン

    //  [Cipher] - 3gpsmdk.properties
    /** プロパティから取得するためのパラメータキー ： エンドポイント暗号化のサーバ公開鍵 */
    const TRUST_CERT_FILE              = "TRUST_CERT_FILE";              // ENDPOINT暗号化用 公開鍵ファイル名
    /** プロパティから取得するためのパラメータキー ： エンドポイント暗号化のクライアント秘密鍵 */
    const PRIVATE_CERT_FILE            = "PRIVATE_CERT_FILE";            // Endpoint暗号化用 マーチャント鍵ペアファイル名
    /** プロパティから取得するためのパラメータキー ： エンドポイント暗号化のクライアント公開鍵 */
    const PRIVATE_CERT_PUBLIC_FILE     = "PRIVATE_CERT_PUBLIC_FILE";     // Endpoint暗号化用 マーチャント鍵公開ファイル名

    /** プロパティから取得するためのパラメータキー ： エンドポイント暗号化のクライアント秘密鍵のパスワード */
    const PRIVATE_CERT_PASSWORD        = "PRIVATE_CERT_PASSWORD";        // ENDPOINT暗号化用 秘密鍵パスワード
    //  [Cipher] - internal.properties
    /** プロパティから取得するためのパラメータキー ： メッセージダイジェストのアルゴリズム */
    const MESSAGE_DIGEST_TYPE          = "MESSAGE_DIGEST_TYPE";          // メッセージダイジェストのアルゴリズム

    //  [Connection] - 3gpsmdk.properties
//    2013/11/21 3GPSMDK.properties変更の為コメントアウト
//    /** プロパティから取得するためのパラメータキー ： 通信プロトコル */
//    const PROTOCOL                     = "PROTOCOL";                     // 送信プロトコル
//    /** プロパティから取得するためのパラメータキー ： SOAP(WS-Securityの場合)送信時のURL */
//    const TARGET_HOST_SOAP_WS_SECURITY = "TARGET_HOST_SOAP_WS_SECURITY"; // HOSTサーバURL SOAP(WS-Security)
//    /** プロパティから取得するためのパラメータキー ： SOAP(WS-Securityなしの場合)送信時のURL */
//    const TARGET_HOST_SOAP_NO_SECURITY = "TARGET_HOST_SOAP_NO_SECURITY"; // HOSTサーバURL SOAP(Securityなし)
//    /** プロパティから取得するためのパラメータキー ： GET送信時のURL */
//    const TARGET_HOST_GET              = "TARGET_HOST_GET";              // HOSTサーバURL GET
//    /** プロパティから取得するためのパラメータキー ： POST送信時のURL */
//    const TARGET_HOST_POST             = "TARGET_HOST_POST";             // HOSTサーバURL POST
//    /** プロパティから取得するためのパラメータキー ： POST送信時のURL */
//    const TARGET_HOST_POST_NO_SECURITY = "TARGET_HOST_POST_NO_SECURITY"; // HOSTサーバURL POST(Securityなし)
//    /** プロパティから取得するためのパラメータキー ： 三者間用URL(UTF-8版) */
//    const TRIPARTITE_URL               = "TRIPARTITE_URL";               // ３者間通信用URL(UTF-8版)
//    /** プロパティから取得するためのパラメータキー ： 三者間用URL(Shift_JIS版) */
//    const TRIPARTITE_SJIS_URL          = "TRIPARTITE_SJIS_URL";          // ３者間通信用URL(Shift_JIS版)
    /** プロパティから取得するためのパラメータキー ： GWのURL */
    const HOST_URL                     = "HOST_URL";                     // GWのURL
    /** プロパティから取得するためのパラメータキー ： プロキシサーバドメイン */
    const PROXY_SERVER                 = "PROXY_SERVER";                 // PROXYサーバ
    /** プロパティから取得するためのパラメータキー ： プロキシサーバポート */
    const PROXY_PORT                   = "PROXY_PORT";                   // PROXYポート
    /** プロパティから取得するためのパラメータキー ： プロキシサーバ ユーザ名 */
    const PROXY_USERNAME               = "PROXY_USERNAME";               // PROXY認証ユーザ
    /** プロパティから取得するためのパラメータキー ： プロキシサーバ パスワード */
    const PROXY_PASSWORD               = "PROXY_PASSWORD";               // PROXY認証パスワード
    /** プロパティから取得するためのパラメータキー ： プロキシサーバURL */
    const PROXY_SERVER_URL             = "PROXY_SERVER_URL";             // PROXYサーバのURL
    /** プロパティから取得するためのパラメータキー ： マーチャントCCID */
    const MERCHANT_CC_ID               = "MERCHANT_CC_ID";               // マーチャントCCID
    /** プロパティから取得するためのパラメータキー ： マーチャントパスワード */
    const MERCHANT_SECRET_KEY          = "MERCHANT_SECRET_KEY";          // マーチャントパスワード
    /** プロパティから取得するためのパラメータキー ： SSL用サーバ公開鍵 */
    const CA_CERT_FILE                 = "CA_CERT_FILE";                 // 認証局　CA証明書ファイル名
    /** プロパティから取得するためのパラメータキー ： SSL用クライアント秘密鍵 */
    const CLIENT_CERT_FILE             = "CLIENT_CERT_FILE";             // SSL Client Auth 証明書ファイル
    /** プロパティから取得するためのパラメータキー ： SSL用クライアント秘密鍵のパスワード */
    const CLIENT_CERT_PASSWORD         = "CLIENT_CERT_PASSWORD";         // SSL Client Auth 証明書パスワード
    /** プロパティから取得するためのパラメータキー ： サーバ鍵のエイリアス名 */
    const BODY_ENCRYPT_SVR_ALIAS_NAME  = "BODY_ENCRYPT_SVR_ALIAS_NAME";  // サーバキーエイリアス名
    /** プロパティから取得するためのパラメータキー ： クライアント鍵のエイリアス名 */
    const BODY_ENCRYPT_MDK_ALIAS_NAME  = "BODY_ENCRYPT_MDK_ALIAS_NAME";  // マーチャントキーエイリアス名
    //  [Connection] - internal.properties
    /** プロパティから取得するためのパラメータキー ： タイムアウト時間 */
    const CONNECTION_TIMEOUT           = "CONNECTION_TIMEOUT";           // コネクションタイムアウト
    /** プロパティから取得するためのパラメータキー ： DTOに設定する文字エンコード */
    const DTO_ENCODE                   = "DTO_ENCODE";
    //  [RequestDto] - internal.properties
    /** プロパティから取得するためのパラメータキー ： 共通項目 */
    const COMMON_ITEM                  = "COMMON_ITEM";                  //  RequestDto共通項目バージョン
    //  [ResponseDto] - internal.properties
    /** プロパティから取得するためのパラメータキー ： マスク項目 */
    const MASK_ITEM                    = "MASK_ITEM";                    //  ResponseDto伏字対象項目
    // [Url] - internal.properties
    /** プロパティから取得するためのパラメータキー : 決済のバージョン */
    const ADD_URL_PAYMENT_VERSION      = "ADD_URL_PAYMENT_VERSION";
    /** プロパティから取得するためのパラメータキー : VTIDのバージョン */
    const ADD_URL_VTID_VERSION         = "ADD_URL_VTID_VERSION";
    /** プロパティから取得するためのパラメータキー : 決済のURL付加情報 */
    const ADD_URL_PAYMENT              = "ADD_URL_PAYMENT";
    /** プロパティから取得するためのパラメータキー : VTIDのURL付加情報 */
    const ADD_URL_VTID                 = "ADD_URL_VTID";
    /** プロパティから取得するためのパラメータキー : VTIDのサービスタイプ */
    const PAYNOWID_SERVICE_TYPE        = "PAYNOWID_SERVICE_TYPE";
    
    /**
     *  コンストラクタ。
     *
     * @param string $configFileName 読み込むコンフィグファイル名
     * @throws TGMDK_Exception コンフィグファイルの読み取り失敗で例外発生
     */
    private function __construct($configFileName=null) {
        $this->resetErrors();
        $err = array();

        //  コンフィグファイルの指定がある場合は、デフォルトコンフィグファイル名を変更する
        if ($configFileName == null) {
            $this->conf_file_name = MDK_DIR . DS . "3GPSMDK.properties";
        } else {
            $this->conf_file_name = $configFileName;
        }

        $intconf_filename = MDK_LIB_DIR . DS . "mdkinternal.properties";
        $dtoconf_filename = MDK_DTO_DIR . DS . "dto.properties";
        $log4php_filename = MDK_DIR     . DS . "log4php.properties";

        if (is_readable($this->conf_file_name)) {
            self::$m_config = @parse_ini_file($this->conf_file_name, true);

        } else {
            $ex = new TGMDK_Exception(TGMDK_Exception::MA05_CONF_FILE_DOES_NOT_EXIST,
                "Mdk properties", $this->conf_file_name);
            $msg = $ex->getMessage();
            if (TGMDK_ErrorMessage::UNKNOWN == $msg) {
                // エラーメッセージファイルが読めない可能性もあるため
                // 代替の英語メッセージをセットしておく
                $msg = "Mdk properties file does not exist at " . $this->conf_file_name;
            }
            array_push($err, $msg . "\n");

        }

        if (is_readable($intconf_filename)) {
            $tempMap = @parse_ini_file($intconf_filename, true);
            self::$m_config = @array_merge_recursive(self::$m_config, $tempMap);

        } else {
            $ex = new TGMDK_Exception(TGMDK_Exception::MA05_CONF_FILE_DOES_NOT_EXIST,
                "Mdk internal properties", $intconf_filename);
            $msg = $ex->getMessage();
            if (TGMDK_ErrorMessage::UNKNOWN == $msg) {
                // エラーメッセージファイルが読めない可能性もあるため
                // 代替の英語メッセージをセットしておく
                $msg = "Mdk internal properties file does not exist at " . $intconf_filename;
            }
            array_push($err, $msg . "\n");

        }

        if (is_readable($dtoconf_filename)) {
            $dtoMap = @parse_ini_file($dtoconf_filename, true);
            self::$m_config = @array_merge_recursive(self::$m_config, $dtoMap);

        } else {
            $ex = new TGMDK_Exception(TGMDK_Exception::MA05_CONF_FILE_DOES_NOT_EXIST,
                "Mdk DTO properties", $dtoconf_filename);
            $msg = $ex->getMessage();
            if (TGMDK_ErrorMessage::UNKNOWN == $msg) {
                // エラーメッセージファイルが読めない可能性もあるため
                // 代替の英語メッセージをセットしておく
                $msg = "Mdk DTO properties file does not exist at " . $dtoconf_filename;
            }
            array_push($err, $msg . "\n");

        }

        if (is_readable($log4php_filename) == false) {
            $ex = new TGMDK_Exception(TGMDK_Exception::MA05_CONF_FILE_DOES_NOT_EXIST,
                "log4php properties", $log4php_filename);
            $msg = $ex->getMessage();
            if (TGMDK_ErrorMessage::UNKNOWN == $msg) {
                // エラーメッセージファイルが読めない可能性もあるため
                // 代替の英語メッセージをセットしておく
                $msg = "Mdk properties file does not exist at " . $log4php_filename;
            }
            array_push($err, $msg . "\n");

        }

        /* ====================================================== */
        /* 動的マーチャント処理                                   */
        /* TGMDK_MerchantSettingContextが設定されている場合の処理 */
        /* ====================================================== */
        // マーチャントID
        $merchant_ccid       = TGMDK_MerchantSettingContext::get_merchant_ccid();       // マーチャントCCID
        $merchant_secret_key = TGMDK_MerchantSettingContext::get_merchant_secret_key(); // マーチャントパスワード
        $timeout             = TGMDK_MerchantSettingContext::get_timeout();             // コネクションタイムアウト
        $dummy_request       = TGMDK_MerchantSettingContext::get_dummy_request();       // ダミーリクエスト

        // マーチャントCCIDのチェック＆上書き
        if (isset($merchant_ccid))
            self::$m_config[self::CONNECTION_SECTION][self::MERCHANT_CC_ID]      = $merchant_ccid;
        // マーチャントパスワードのチェック＆上書き
        if (isset($merchant_secret_key))
            self::$m_config[self::CONNECTION_SECTION][self::MERCHANT_SECRET_KEY] = $merchant_secret_key;
        // コネクションタイムアウトのチェック＆上書き
        if (isset($timeout))
            self::$m_config[self::CONNECTION_SECTION][self::CONNECTION_TIMEOUT]  = $timeout;
        // ダミーリクエストのチェック＆上書き
        if (isset($dummy_request))
            self::$m_config[self::SERVICE_SECTION][self::DUMMY_REQUEST]          = $dummy_request;

        //  エラーメッセージの有無を判断してエラーを出力
        if (!empty($err)) {
            throw new TGMDK_Exception(TGMDK_Exception::MB01_NOT_FOUND_CONFIG_FILE, implode(PHP_EOL, $err));
            //throw new Exception(implode(PHP_EOL, $err));
        }
    }

    /**
     *
     * TGMDK_Config クラスをインスタンス化する
     *
     * @param string $configFileName コンフィグファイル
     * @return 静的なコンフィグクラスのインスタンス
     * @throws TGMDK_Exception コンフィグファイルの読み取り失敗で例外発生
     */
    public static function getInstance($configFileName=null) {
        if (self::$instance == NULL) {
            self::$instance = new TGMDK_Config($configFileName);
        }
        return self::$instance;
    }

    /**
     * コンフィグファイルの各パラメータの検証を行う
     *
     * @param boolean $validOnly エラー発生時、TRUEは例外をスローしない、FALSEは例外をスローする
     * @return boolean パラメータの検証に成功: TRUE、それ以外: FALSE
     */
    public function validate($validOnly=FALSE) {
//        $protocol = self::$m_config[self::CONNECTION_SECTION][self::PROTOCOL];

        /***********************************************************************
             Service セクションのキーと値の確認
         ***********************************************************************/
        //  DUMMY_REQUEST
        $this->presence_of(self::SERVICE_SECTION, self::DUMMY_REQUEST);
        $this->numeric_of(self::SERVICE_SECTION, self::DUMMY_REQUEST);

        //  MDK_ERROR_MODE
        $this->presence_of(self::SERVICE_SECTION, self::MDK_ERROR_MODE);
        $this->numeric_of(self::SERVICE_SECTION, self::MDK_ERROR_MODE);

        //  MDK_VERSION
        $this->presence_of(self::SERVICE_SECTION, self::MDK_VERSION);

        //  MDK_DTO_VERSION
        $this->presence_of(self::SERVICE_SECTION, self::MDK_DTO_VERSION);

        /***********************************************************************
             Cipher セクションのキーと値の確認
         ***********************************************************************/
        // エンドポイント暗号化を行う場合のみチェック
        /* 2012/01/30 プロトコル間違いにしても、ここのエラーがでるため、クライアント
           証明についてはノーチェックで。
        if ($protocol != "POST_NO_SECURITY" and $protocol != "SOAP_NO_SECURITY") {
            //  TRUST_CERT_FILE
            $this->presence_of(self::CIPHER_SECTION, self::TRUST_CERT_FILE);
            $this->exist_of(self::CIPHER_SECTION, self::TRUST_CERT_FILE);

            //  PRIVATE_CERT_FILE
            $this->presence_of(self::CIPHER_SECTION, self::PRIVATE_CERT_FILE);
            $this->exist_of(self::CIPHER_SECTION, self::PRIVATE_CERT_FILE);

            //  MESSAGE_DIGEST_TYPE
            $this->presence_of(self::CIPHER_SECTION, self::MESSAGE_DIGEST_TYPE);

            //  BODY_ENCRYPT_SVR_ALIAS_NAME
            $this->presence_of(self::CIPHER_SECTION, self::BODY_ENCRYPT_SVR_ALIAS_NAME);

            if ($protocol == "SOAP_WS_SECURITY") {
            // WS-Securityの場合のみのチェック
                //  PRIVATE_CERT_PUBLIC_FILE
                $this->presence_of(self::CIPHER_SECTION, self::PRIVATE_CERT_PUBLIC_FILE);
                $this->exist_of(self::CIPHER_SECTION, self::PRIVATE_CERT_PUBLIC_FILE);
            } else {
                //  PRIVATE_CERT_PASSWORD
                $this->presence_of(self::CIPHER_SECTION, self::PRIVATE_CERT_PASSWORD);

                //  BODY_ENCRYPT_MDK_ALIAS_NAME
                $this->presence_of(self::CIPHER_SECTION, self::BODY_ENCRYPT_MDK_ALIAS_NAME);
            }
        }
        */

        /***********************************************************************
             Connection セクションのキーと値の確認
         ***********************************************************************/
        //  PROTOCOL(SOAP_NO_SECURITY or GET or POST or POST_NO_SECURITY)
        // 2013/11/22 comment out
        // $this->presence_of(self::CONNECTION_SECTION, self::PROTOCOL);
        // $this->format_of(self::CONNECTION_SECTION, self::PROTOCOL,
        //    '/^post_no_security$/i');
        // 2012/01/30 allow only post_no_security
        //    '/^soap_ws_security$|^soap_no_security$|^get$|^post$|^post_no_security$/i');

        // 2012/01/30 start comment out not used property see #27
        //  TARGET_HOST_SOAP_WS_SECURITY
        //$this->presence_of(self::CONNECTION_SECTION, self::TARGET_HOST_SOAP_WS_SECURITY);
        //$this->format_of(self::CONNECTION_SECTION, self::TARGET_HOST_SOAP_WS_SECURITY,
        //    '/^https:\/\/+[a-z0-9]+\.[a-z0-9]/i');

        //  TARGET_HOST_SOAP_NO_SECURITY
        //$this->presence_of(self::CONNECTION_SECTION, self::TARGET_HOST_SOAP_NO_SECURITY);
        //$this->format_of(self::CONNECTION_SECTION, self::TARGET_HOST_SOAP_NO_SECURITY,
        //    '/^https:\/\/+[a-z0-9]+\.[a-z0-9]/i');

        //  TARGET_HOST_GET
        //$this->presence_of(self::CONNECTION_SECTION, self::TARGET_HOST_GET);
        //$this->format_of(self::CONNECTION_SECTION, self::TARGET_HOST_GET,
        //    '/^https:\/\/+[a-z0-9]+\.[a-z0-9]/i');

        //  TARGET_HOST_POST
        //$this->presence_of(self::CONNECTION_SECTION, self::TARGET_HOST_POST);
        //$this->format_of(self::CONNECTION_SECTION, self::TARGET_HOST_POST,
        //    '/^https:\/\/+[a-z0-9]+\.[a-z0-9]/i');

        //  TARGET_HOST_POST_NO_SECURITY
        // 2013/11/22 comment out
        // if ($protocol == "POST_NO_SECURITY") {
        //    $this->presence_of(self::CONNECTION_SECTION, self::TARGET_HOST_POST_NO_SECURITY);
        //    $this->format_of(self::CONNECTION_SECTION, self::TARGET_HOST_POST_NO_SECURITY,
        //        '/^https:\/\/+[a-z0-9]+\.[a-z0-9]/i');
        // }

        //  TRIPARTITE_URL
        //$this->presence_of(self::CONNECTION_SECTION, self::TRIPARTITE_URL);
        //$this->format_of(self::CONNECTION_SECTION, self::TRIPARTITE_URL,
        //    '/^https:\/\/+[a-z0-9]+\.[a-z0-9]/i');

        //  TRIPARTITE_SJIS_URL
        //$this->presence_of(self::CONNECTION_SECTION, self::TRIPARTITE_SJIS_URL);
        //$this->format_of(self::CONNECTION_SECTION, self::TRIPARTITE_SJIS_URL,
        //    '/^https:\/\/+[a-z0-9]+\.[a-z0-9]/i');
        // 2012/01/30 end comment out not used property see #27

        //  MERCHANT_CC_ID
        $this->presence_of(self::CONNECTION_SECTION, self::MERCHANT_CC_ID);

        //  MERCHANT_SECRET_KEY
        $this->presence_of(self::CONNECTION_SECTION, self::MERCHANT_SECRET_KEY);

        //  CA_CERT_FILE
        $this->presence_of(self::CONNECTION_SECTION, self::CA_CERT_FILE);
        $this->exist_of(self::CONNECTION_SECTION, self::CA_CERT_FILE);

        // 定義があり値がある場合のみチェックを行う
        if (isset(self::$m_config[self::CONNECTION_SECTION][self::CLIENT_CERT_FILE])) {
            //  CLIENT_CERT_FILE
            $this->presence_of(self::CONNECTION_SECTION, self::CLIENT_CERT_FILE);
            $this->exist_of(self::CONNECTION_SECTION, self::CLIENT_CERT_FILE);

            //  CLIENT_CERT_PASSWORD
            $this->presence_of(self::CONNECTION_SECTION, self::CLIENT_CERT_PASSWORD);
        }

        //  CONNECTION_TIMEOUT
        $this->presence_of(self::CONNECTION_SECTION, self::CONNECTION_TIMEOUT);

        /***********************************************************************
             RequestDto セクションのキーと値の確認
         ***********************************************************************/
        //  COMMON_ITEM
        $this->presence_of(self::REQUEST_DTO_SECTION, self::COMMON_ITEM);

        /***********************************************************************
             ResponseDto セクションのキーと値の確認
         ***********************************************************************/
        //  MASK_ITEM
        $this->presence_of(self::RESPONSE_DTO_SECTION, self::MASK_ITEM);


        // メソッドの戻り値を設定 >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
        if ($validOnly == TRUE) {
            // 検証のみ
            if (empty($this->valids)) {
                //  エラーなし
                return TRUE;
            } else {
                //  エラーあり
                return FALSE;
            }
        } else {
            //  例外を発生
            if (empty($this->valids)) {
                //  エラーなし
                return TRUE;
            } else {
                //  エラーあり（例外発生）
                //  とりあえず最初のエラーを返す(resetは配列の先頭要素を返す)
                //  全部取得する場合は、getErrors()で配列を取得
                throw reset($this->valids);
            }
        }
    }

    // 2013/11 未使用の為、コメントアウト
    //  正規表現(PCRE)に一致する場合は TRUE、それ以外は FALSE
    //  例： 'https:' で始まるか？ → /^https:/
//    private function format_of($section, $key, $pattern) {
//        if (0 < preg_match($pattern, self::$m_config[$section][$key])) {
//            return TRUE;
//        }
//
//        //  書式のエラー
//        $this->addError(new TGMDK_Exception(TGMDK_Exception::MA02_CONFIG_NOT_CORRECT,
//                $section, $key, self::$m_config[$section][$key]));
//
//        return FALSE;
//    }
    //  数値を表している場合 TRUE、それ以外は FALSE
    private function numeric_of($section, $key) {
        if (is_numeric(self::$m_config[$section][$key])) {
            return TRUE;
        }

        //  書式のエラー
        $this->addError(new TGMDK_Exception(TGMDK_Exception::MA02_CONFIG_NOT_CORRECT,
                $section, $key, self::$m_config[$section][$key]));
        return FALSE;
    }

    //  ファイルもしくはディレクトリが存在する場合は TRUE、それ以外は FALSE
    private function exist_of($section, $key) {
        if (file_exists(self::$m_config[$section][$key])) {
            return TRUE;
        }

        //  Does not exist エラー
        $this->addError(new TGMDK_Exception(TGMDK_Exception::MA03_FILE_DOES_NOT_EXIST,
                $section, $key, self::$m_config[$section][$key]));
        return FALSE;
    }

    //  パラメータに値が設定されている場合は TRUE、 それ以外は FALSE
    private function presence_of($section, $key) {
        $var = self::$m_config[$section][$key];
        //if (!empty(self::$m_config[$section][$key])) {
        if (!is_null($var) && $var !== '') {
            return TRUE;
        }

        //  設定なしエラー
        $this->addError(new TGMDK_Exception(TGMDK_Exception::MA01_CONFIG_MISSING,
                $section, $key, self::$m_config[$section][$key]));
        return FALSE;
    }

    //  Error原因を保存する配列をリセットする
    private function resetErrors() {
        unset ($this->valids);
        $this->valids = array();
    }

    //  Error原因を配列に保存する
    private function addError($cause) {
        array_push($this->valids, $cause);
    }

    /**
     *
     *  validate時に発生した例外を返却する
     *
     * @return mixed validate時に発生した例外の配列
     */
    public function getErrors() {
        return $this->valids;
    }

    /**
     * サービスに必要なパラメータを連想配列で返す
     *
     * @return mixed パラメータ（連想配列）
     */
    public function getServiceParameters() {
        $retMap = array();
        $retMap = array_merge($retMap, $this->getServiceSection());
        return $retMap;
    }

    /**
     * 電文暗号化に必要なパラメータを連想配列で返す
     *
     * @return mixed パラメータ（連想配列）
     */
    public function getCipherParameters() {
        $retMap = array();
        $retMap = array_merge($retMap, $this->getCipherSection());
        return $retMap;
    }

    /**
     * 通信に必要なパラメータを連想配列で返す
     *
     * @return mixed パラメータ（連想配列）
     */
    public function getConnectionParameters() {
        $retMap = array();
        $retMap = array_merge($retMap, $this->getServiceSection());
        $retMap = array_merge($retMap, $this->getConnectionSection());
        return $retMap;
    }

    /**
     * 環境整備に必要なパラメータを連想配列で返す
     *
     * @return mixed パラメータ（連想配列）
     */
    public function getEnvironmentParameters() {
        $retMap = array();
        $tmp = $this->getEnvironmentSection();
        if (!empty($tmp)) {
            $retMap = array_merge($retMap, $tmp);
        }
        return $retMap;
    }

    /**
     * RequestDto に必要なパラメータを連想配列で返す
     *
     * @return mixed パラメータ（連想配列）
     */
    public function getRequestDtoParameters() {
        $retMap = array();
        $retMap = array_merge($retMap, $this->getRequestDtoSection());
        return $retMap;
    }

    /**
     * メイン動作(Transaction)に必要なパラメータを連想配列で返す
     *
     * @return mixed パラメータ（連想配列）
     */
    public function getTransactionParameters() {
        $retMap = array();
        $retMap = array_merge($retMap, $this->getServiceSection());
        $retMap = array_merge($retMap, $this->getConnectionSection());
        $retMap = array_merge($retMap, $this->getCipherSection());
        $retMap = array_merge($retMap, $this->getUrlSection());
        return $retMap;
    }

    /**
     * ResponseDto に必要なパラメータを連想配列で返す
     *
     * @return mixed パラメータ（連想配列）
     */
    public function getResponseDtoParameters() {
        $retMap = array();
        $retMap = array_merge($retMap, $this->getResponseDtoSection());
        return $retMap;
    }
    
    /**
     * Urlに必要なパラメータの連想配列を返す
     * @return mixed パラメータ（連想配列）
     */
    public function getUrlParameters() {
        $retMap = array();
        $retMap = array_merge($retMap, $this->getUrlSection());
        return $retMap;
    }

    // Service Sectionの連想配列を返す
    private function getServiceSection() {
        return self::$m_config[self::SERVICE_SECTION];
    }

    // Cipher Sectionの連想配列を返す
    private function getCipherSection() {
        return self::$m_config[self::CIPHER_SECTION];
    }

    // Connection Sectionの連想配列を返す
    private function getConnectionSection() {
        return self::$m_config[self::CONNECTION_SECTION];
    }

    // Environment Sectionの連想配列を返す
    private function getEnvironmentSection() {
        return self::$m_config[self::ENVIRONMENT_SECTION];
    }

    // RequestDto Sectionの連想配列を返す
    private function getRequestDtoSection() {
        return self::$m_config[self::REQUEST_DTO_SECTION];
    }

    // ResponseDto Sectionの連想配列を返す
    private function getResponseDtoSection() {
        return self::$m_config[self::RESPONSE_DTO_SECTION];
    }
    
    // Url Sectionの連想配列を返す
    private function getUrlSection() {
        return self::$m_config[self::URL_SECTION];
    }

    /**
     * インスタンス生成時のコンフィグファイル名取得（UNITテスト用）
     *
     * @return string コンフィグファイル名
     */
    public function __getConfigFileName() {
        return $this->conf_file_name;
    }

    /**
     * 静的なインスタンスを破棄する（UNITテスト用）
     */
    public static function  __reset() {
        self::$m_config = NULL;
        self::$instance = NULL;
    }
}
