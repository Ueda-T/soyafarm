<?php
if (realpath($_SERVER["SCRIPT_FILENAME"]) == realpath(__FILE__)) die('Permission denied.');

if (!defined('MDK_LIB_DIR')) require_once('../3GPSMDK.php');



/**
 *
 * SOAPによるSSL通信管理クラス
 *
 * SSL通信に関わる処理を取りまとめる。Proxyを経由した通信にも対応
 *
 * @category    Veritrans
 * @package     Lib
 * @copyright   VeriTrans Inc.
 * @access  public
 * @author  VeriTrans Inc.
 */
class TGMDK_ConnectionSoap {

    /** プロキシを使用するかを示すフラグ */
    private $useProxy                 = FALSE;
    /** プロキシのホスト */
    private $proxy_host               = null;
    /** プロキシのポート */
    private $proxy_port               = null;
    /** プロキシのユーザ */
    private $proxy_username           = null;
    /** プロキシのユーザのパスワード */
    private $proxy_password           = null;

    /** 接続ターゲットのプロトコル */
    private $target_protocol          = null;
    /** 接続ターゲットのホスト */
    private $target_url               = null;
    /** 接続ターゲットのスキーマ */
    private $target_scheme            = null;
    /** 接続ターゲットのホスト */
    private $target_host              = null;
    /** 接続ターゲットのポート */
    private $target_port              = null;
    /** 接続ターゲットURL(コンテキストルート以降) */
    private $target_path              = null;

    /** コネクションタイムアウト */
    private $connection_timeout       = 0;

    /** サーバ公開鍵ファイルパス */
    private $ca_cert_file             = null;
    /** クライアント秘密鍵ファイルパス */
    private $client_cert_file         = null;
    /** 秘密鍵を扱うためのパスフレーズ */
    private $client_cert_password     = null;

    /** エンドポイント暗号サーバ公開鍵 */
    private $trust_cert_file = null;
    /** エンドポイント暗号クライアント秘密鍵ファイルパス */
    private $private_cert_file = null;
    /** エンドポイント暗号クライアント公開鍵ファイルパス */
    private $private_cert_public_file = null;
    /** サーバ鍵のエイリアス名 */
    private $body_encrypt_svr_alias_name = null;

    /**
     * コンストラクタ。
     * コンフィグファイルからデータを取得して当クラスを使用できる状態にする。
     * パラメータ$use_default_confがFALSEの場合はデフォルト値を設定しない。
     *
     * @param boolean $use_default_conf デフォルトのConfigファイルからデータを取得するかを指定するフラグ
     */
    public function __construct($use_default_conf = TRUE) {

        // エラーハンドラ設定
        set_error_handler("error_handler");

        if ($use_default_conf) {
            $this->set_default_config();
        }
    }

    /**
     * デストラクタ。
     * エラーハンドラを破棄する。
     */
    public function  __destruct() {
        // エラーハンドラの破棄
        restore_error_handler();
    }

    /**
     * 当クラスをConfigファイルを使用して値を設定する。
     *
     * @access private
     */
    private function set_default_config() {
        $conf = TGMDK_Config::getInstance();
        $array = $conf->getTransactionParameters();

        if (array_key_exists(TGMDK_Config::PROXY_SERVER_URL, $array)) {
            $proxy_url = $array[TGMDK_Config::PROXY_SERVER_URL]; // ProxyサーバURL

            // ProxyのURLが指定されていない場合はProxyを使用しない
            if (!empty($proxy_url)) {
                $proxy_url_array = parse_url($proxy_url);

                $connection_conf_data["PROXY_HOST"] = $proxy_url_array["host"]; // ProxyサーバHOST
                $connection_conf_data["PROXY_PORT"] = $proxy_url_array["port"]; // ProxyサーバPORT

                // Proxy使用フラグ
                if (!empty($connection_conf_data["PROXY_HOST"]) && !empty($connection_conf_data["PROXY_PORT"])) {
                    $connection_conf_data["USE_ROXY"] = TRUE;
                } else {
                    $connection_conf_data["USE_ROXY"] = FALSE;
                }

                if (array_key_exists(TGMDK_Config::PROXY_USERNAME, $array) and array_key_exists(TGMDK_Config::PROXY_PASSWORD, $array)) {
                    $connection_conf_data["PROXY_USERNAME"] = $array[TGMDK_Config::PROXY_USERNAME]; // Proxyユーザ
                    $connection_conf_data["PROXY_PASSWORD"] = $array[TGMDK_Config::PROXY_PASSWORD]; // Proxyユーザパスワード
                }
            }
        }

        $connection_conf_data["TARGET_PROTOCOL"] = strtoupper($array[TGMDK_Config::PROTOCOL]); // 通信プロトコル
        $connection_conf_data["TARGET_URL"] = $array["TARGET_HOST_" . $connection_conf_data["TARGET_PROTOCOL"]]; // 通信先URL

        // 接続先の情報と送信するデータ内容をログ出力 2010.02.19 add Lincrea
        TGMDK_Logger::getInstance()->debug("connect 3gw url  ==> " . $connection_conf_data["TARGET_URL"] );

        $url_array = parse_url($connection_conf_data["TARGET_URL"]);
        $connection_conf_data["TARGET_SCHEME"] = $url_array["scheme"]; // 通信スキーマ
        $connection_conf_data["TARGET_HOST"]   = $url_array["host"];   // 通信先サーバHOST
        $connection_conf_data["TARGET_PORT"]   = $url_array["port"];   // 通信先サーバPORT
        $connection_conf_data["TARGET_PATH"]   = $url_array["path"];   // 通信先サーバのパス

        $connection_conf_data["CONNECTION_TIMEOUT"] = $array[TGMDK_Config::CONNECTION_TIMEOUT]; // コネクションタイムアウト

        $connection_conf_data["CA_CERT_FILE"] = $array[TGMDK_Config::CA_CERT_FILE]; // SSL公開鍵ファイルパス
        if (isset($array[TGMDK_Config::CLIENT_CERT_FILE])) {
            $connection_conf_data["CLIENT_CERT_FILE"] = $array[TGMDK_Config::CLIENT_CERT_FILE]; // SSL秘密鍵ファイルパス
        }
        if (isset($array[TGMDK_Config::CLIENT_CERT_PASSWORD])) {
            $connection_conf_data["CLIENT_CERT_PASSWORD"] = $array[TGMDK_Config::CLIENT_CERT_PASSWORD]; // SSL秘密鍵を扱うためのパスフレーズ
        }

        if (isset($array[TGMDK_Config::PRIVATE_CERT_FILE])) {
            $connection_conf_data["PRIVATE_CERT_FILE"] = $array[TGMDK_Config::PRIVATE_CERT_FILE]; // エンドポイント暗号のクライアント秘密鍵
        }
        if (isset($array[TGMDK_Config::PRIVATE_CERT_PUBLIC_FILE])) {
            $connection_conf_data["PRIVATE_CERT_PUBLIC_FILE"] = $array[TGMDK_Config::PRIVATE_CERT_PUBLIC_FILE]; // エンドポイント暗号のクライアント公開鍵
        }
        if (isset($array[TGMDK_Config::TRUST_CERT_FILE])) {
            $connection_conf_data["TRUST_CERT_FILE"] = $array[TGMDK_Config::TRUST_CERT_FILE]; // エンドポイント暗号のサーバ公開鍵
        }

        if (isset($array[TGMDK_Config::BODY_ENCRYPT_SVR_ALIAS_NAME])) {
            $connection_conf_data["BODY_ENCRYPT_SVR_ALIAS_NAME"] = $array[TGMDK_Config::BODY_ENCRYPT_SVR_ALIAS_NAME]; // サーバ鍵のエイリアス名
        }

        // グローバル変数に値を設定する
        $this->set_config($connection_conf_data);
    }

    /**
     * TGMDK_Configファイルからデータを取得しないでパラメータによって当クラスを使用できる状態にする。
     *
     * @access pubilc
     * @param Array $connection_conf_data TGMDK_Config同様のデータを格納した配列。
     */
    public function set_config($connection_conf_data) {
        if (!empty($connection_conf_data["USE_ROXY"])) {
            $this->useProxy = $connection_conf_data["USE_ROXY"]; // Proxy使用フラグ
        }

        if ($this->useProxy) {
            $this->proxy_host = $connection_conf_data["PROXY_HOST"]; // ProxyサーバHOST
            $this->proxy_port = $connection_conf_data["PROXY_PORT"]; // ProxyサーバPORT
            if (array_key_exists(TGMDK_Config::PROXY_USERNAME, $connection_conf_data) and array_key_exists(TGMDK_Config::PROXY_PASSWORD, $connection_conf_data)) {
                $this->proxy_username = $connection_conf_data["PROXY_USERNAME"]; // Proxyユーザ
                $this->proxy_password = $connection_conf_data["PROXY_PASSWORD"]; // Proxyユーザパスワード
            }
        }

        $this->target_protocol = strtoupper($connection_conf_data["TARGET_PROTOCOL"]); // 通信先プロトコル
        $this->target_url      = $connection_conf_data["TARGET_URL"];                  // 通信先サーバURL
        $this->target_scheme   = $connection_conf_data["TARGET_SCHEME"];               // 通信スキーマ
        $this->target_host     = $connection_conf_data["TARGET_HOST"];                 // 通信先サーバHOST
        $this->target_port     = $connection_conf_data["TARGET_PORT"];                 // 通信先サーバPORT
        $this->target_path     = $connection_conf_data["TARGET_PATH"];                 // 通信先サーバのPATH

        if (!empty($connection_conf_data["CONNECTION_TIMEOUT"])) {
            $this->connection_timeout = $connection_conf_data["CONNECTION_TIMEOUT"]; // コネクションタイムアウト
        }

        $this->ca_cert_file = realpath($connection_conf_data["CA_CERT_FILE"]); // 公開鍵ファイルパス
        if (isset($connection_conf_data[TGMDK_Config::CLIENT_CERT_FILE])) {
            $this->client_cert_file = realpath($connection_conf_data["CLIENT_CERT_FILE"]); // 秘密鍵ファイルパス
        }
        if (isset($connection_conf_data[TGMDK_Config::CLIENT_CERT_PASSWORD])) {
            $this->client_cert_password = $connection_conf_data["CLIENT_CERT_PASSWORD"]; // 秘密鍵を扱うためのパスフレーズ
        }

        if (isset($connection_conf_data[TGMDK_Config::PRIVATE_CERT_FILE])) {
            $this->private_cert_file = $connection_conf_data["PRIVATE_CERT_FILE"];
        }
        if (isset($connection_conf_data[TGMDK_Config::PRIVATE_CERT_PUBLIC_FILE])) {
            $this->private_cert_public_file = $connection_conf_data["PRIVATE_CERT_PUBLIC_FILE"];
        }
        if (isset($connection_conf_data[TGMDK_Config::TRUST_CERT_FILE])) {
            $this->trust_cert_file = $connection_conf_data["TRUST_CERT_FILE"];
        }

        if (isset($connection_conf_data[TGMDK_Config::BODY_ENCRYPT_SVR_ALIAS_NAME])) {
            $this->body_encrypt_svr_alias_name = $connection_conf_data["BODY_ENCRYPT_SVR_ALIAS_NAME"];
        }
    }

    /**
     * 接続メソッド。(WS-Securityなし)
     *
     * @access pubilc
     * @param String $param リクエスト電文BODY
     * @return String レスポンス電文BODY
     */
    public function execute($param) {
        try {
            $context = stream_context_create();
            $res = stream_context_set_option($context, "ssl", "verify_host", true);
            $res = stream_context_set_option($context, "ssl", "verify_peer", true);
            $res = stream_context_set_option($context, "ssl", "cafile"     , $this->ca_cert_file);
            if (!empty($this->client_cert_file)) {
                $res = stream_context_set_option($context, "ssl", "local_cert" , $this->client_cert_file);
                if (!empty($this->client_cert_file)) {
                    $res = stream_context_set_option($context, "ssl", "passphrase" , $this->client_cert_password);
                }
            }

            $road = 0;
            $connect_host = "";
            if($this->useProxy) {
                $road = 1;
                $connect_host = "tcp://" . $this->proxy_host . ":" . $this->proxy_port;
            } else {
                $road = 2;
                $connect_host = "ssl://" . $this->target_host . ":" . $this->target_port;
            }


            try {
                $fp = stream_socket_client($connect_host, $errno, $errstr, $this->connection_timeout, STREAM_CLIENT_CONNECT, $context);
            } catch (Exception $try_error) {
                $message_id = "";
                if ($road == 1) {
                    $message_id = TGMDK_Exception::MF01_PROXY_ERROR;
                } else {
                    $message_id = TGMDK_Exception::MF02_CANNOT_CONNECT_TO_GW;
                }
                throw new TGMDK_Exception($try_error, $message_id);
            }

            // Proxyを使用する場合の処理
            if($this->useProxy) {
                $req = "";
                $req .= "CONNECT " . $this->target_host . ":" . $this->target_port . " HTTP/1.0\r\n";
                $req .= "Host: "   . $this->target_host . ":" . $this->target_port . "\r\n";
                if (!empty($this->proxy_username) and !empty($this->proxy_password)) {
                    $req .= "Proxy-Authorization: basic " . base64_encode($this->proxy_username . ":" . $this->proxy_password) . "\r\n";
                }
                $req .= "\r\n";

                fwrite($fp, $req);
                fflush($fp);

                // Proxyサーバの処理結果を読み込む
                $header_string = "";
                while($header = trim(fgets($fp))) {
                    $header_string .= $header . "\n";
                }
                // PROXYでエラーの場合のエラーハンドリング
                preg_match("/^[^\\s]*\\s\\d{3}\\s/", $header_string, $header_matches);
                if (!is_null($header_matches) && 0 < count($header_matches)) {
                    preg_match("/\\d{3}/", $header_matches[0], $result_matches);
                    $result_code = $result_matches[0];
                    if($result_code <> "200") {
                        throw new TGMDK_Exception(TGMDK_Exception::MF01_PROXY_ERROR);
                    }
                }

                try {
                    // 接続済みのソケットについて暗号化をONにする
                    stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                } catch (Exception $ssl_exception) {
                    throw new TGMDK_Exception($ssl_exception, TGMDK_Exception::MB03_SSLSOCKET_CREATION_FAILED);
                }
            }

            // SOAP電文の作成
            $message = $this->create_soap_xml($param);

            // POST送信
            $req = "";
            $req .= "POST $this->target_path HTTP/1.0\r\n";
            $req .= "Host: $this->target_host:$this->target_port\r\n";
            $req .= "Content-type: text/xml; charset=utf-8\r\n";
            $req .= "Content-length: " . strlen($message) . "\r\n";
            $req .= "SOAPAction: \"$this->target_url\"\r\n";
            $req .= "\r\n";

            fwrite($fp, $req);
            fwrite($fp, $message);
            fflush($fp);

            // 結果のヘッダ情報の読み込み
            $header_string = "";
            while($header = trim(fgets($fp))) {
                $header_string .= $header . "\n";
            }

            // エラーの場合のエラーハンドリング
            preg_match("/^[^\\s]*\\s(\\d{3})\\s/", $header_string, $header_matches);
            if (!is_null($header_matches) && 0 < count($header_matches)) {
                preg_match("/\\d{3}/", $header_matches[0], $result_matches);
                $result_code = $result_matches[0];
                if($result_code <> "200") {
                    throw new TGMDK_Exception(TGMDK_Exception::MF02_CANNOT_CONNECT_TO_GW);
                }
            }


            // 結果のボディ情報の読み込み
            $body_string = "";
            while(!feof($fp)) {
                $body = trim(fgets($fp));
                $body_string .= $body . "\n";
            }
            fclose($fp);

            // 結果ボディ情報から戻りの電文を抜き出す
            $rtnStr = mb_eregi_replace(".*<return>", "", $body_string);
            $rtnStr = mb_eregi_replace("</return>.*$", "", $rtnStr);
            
            $rtnStr = htmlspecialchars_decode($rtnStr);

            // エラーハンドラの破棄
            restore_error_handler();

            return $rtnStr;
        } catch(TGMDK_Exception $e) {
            // エラーハンドラの破棄
            restore_error_handler();

            throw $e;
        } catch(Exception $e) {
            // エラーハンドラの破棄
            restore_error_handler();

            throw new TGMDK_Exception($e, TGMDK_Exception::MF99_SYSTEM_INTERNAL_ERROR);
        }
    }

    /**
     * SOAP電文の作成。(WS-Security無し用)
     *
     * @param String $param 呼び出し先サービスに引き渡すパラメータ
     * @return String 生成されたSOAP電文
     */
    private function create_soap_xml($param) {
        $xml = "";

        $xml .= "<?xml version=\"1.0\" ?>\r\n";
        $xml .= "<S:Envelope xmlns:S = \"http://schemas.xmlsoap.org/soap/envelope/\">\r\n";
        $xml .= "<S:Body>\r\n";
        $xml .= "<ns2:service xmlns:ns2 = \"http://webinterface.service.gw.tercerog.veritrans.jp/\">\r\n";
        $xml .= "<param>$param</param>\r\n";
        $xml .= "</ns2:service>\r\n";
        $xml .= "</S:Body>\r\n";
        $xml .= "</S:Envelope>\r\n";

        return $xml;
    }

    /**
     * 接続メソッド。(WS-Securityあり)
     *
     * @access pubilc
     * @param String $param リクエスト電文BODY
     * @return String レスポンス電文BODY
     */
    public function executeWSSecurity($param) {
        //--------------------------------------------------------------------------------
        //外部リソースの読み込み
        //--------------------------------------------------------------------------------
        //Policyは外部XMLより読み込む
        $policy_file = file_get_contents(MDK_LIB_DIR . DS . "GWSoapSecurityCommandRcvService.xml");
        $policy = new WSPolicy($policy_file);

        //EndPoint暗号鍵の読み込み
        $ServerKey = ws_get_cert_from_file($this->trust_cert_file);       // サーバ公開鍵
        $pubKey = ws_get_cert_from_file($this->private_cert_public_file); // クライアント公開鍵
        $privKey = ws_get_key_from_file($this->private_cert_file);        // クライアント秘密鍵

        //--------------------------------------------------------------------------------
        //SOAPメッセージの生成
        //--------------------------------------------------------------------------------
        //鍵Alias名のヘッダへ設定
        $inputHeader = new WSHeader(
            array(
              "ns" => "http://webinterface.service.gw.tercerog.veritrans.jp/"
                ,"name" => "tgatewayKeyAlias"
                ,"data" => $this->body_encrypt_svr_alias_name
            )
        );

        //ヘッダをメッセージプロパティへ設定
        $message_properties = array(
             "to" => $this->target_url,
             "inputHeaders"=> array($inputHeader)
        );

        //メッセージBodyを生成
        $message_body = $this->create_wss_soap_xml($param);

        //メッセージBodyとメッセージプロパティを基にSoapメッセージを生成
        $message = new WSMessage($message_body, $message_properties);
        //--------------------------------------------------------------------------------
        //セキュリティトークンの生成
        //--------------------------------------------------------------------------------
        //各鍵をトークンオプションへ設定
        $security_token_array = array(
            "certificate"         => $pubKey,
            "privateKey"          => $privKey,
            "receiverCertificate" => $ServerKey,
        );
        $security_token = new WSSecurityToken($security_token_array);

        //--------------------------------------------------------------------------------
        //ソープメッセージの送信
        //--------------------------------------------------------------------------------
        //送信オプションの設定
        $client_options = Array(
            "useSOAP" => "1.1",
            "policy" => $policy,
            "securityToken" => $security_token,
            "CACert"=> $this->ca_cert_file,
            "clientCert"=> $this->client_cert_file,
            "passphrase"=> $this->client_cert_password
        );
        //クライアント接続の確立
        $client = new WSClient($client_options);

        //メッセージの送信
        $reply = $client->request($message);
        $rtnStr = htmlspecialchars_decode($reply->str);

        // 結果ボディ情報から戻りの電文を抜き出す
        $rtnStr = mb_eregi_replace(".*<return>", "", $rtnStr);
        $rtnStr = mb_eregi_replace("</return>.*$", "", $rtnStr);

        return $rtnStr;
    }

    /**
     * SOAP電文の作成。(WS-Security用)
     *
     * @param String $param 呼び出し先サービスに引き渡すパラメータ
     * @return String 生成されたSOAP電文
     */
    private function create_wss_soap_xml($param) {
        $xml = "";

        $xml .= "<ns:service xmlns:ns=\"http://webinterface.service.gw.tercerog.veritrans.jp/\">\r\n";
        $xml .= "<param>$param</param>\r\n";
        $xml .= "</ns:service>\r\n";

        return $xml;
    }

}
