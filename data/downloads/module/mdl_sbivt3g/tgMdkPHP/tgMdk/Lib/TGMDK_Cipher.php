<?php
if (realpath($_SERVER["SCRIPT_FILENAME"]) == realpath(__FILE__)) die('Permission denied.');

if (!defined('MDK_LIB_DIR')) require_once('../3GPSMDK.php');

/**
 *
 * パラメータ暗号化クラス
 *
 * @category    Veritrans
 * @package     Lib
 * @copyright   VeriTrans Inc.
 * @access  public
 * @author  VeriTrans Inc.
 */
class TGMDK_Cipher {

    /** キーストアファイルパス */
    private $storeFile = null;

    /** キーストアファイル操作時のパスワード */
    private $storePass = null;

    /** トラストストアファイルパス */
    private $trustFile = null;

    /**
     * コンストラクタ。
     * コンフィグファイルからデータを取得して当クラスを使用できる状態にする。
     */
    public function __construct() {
        // エラーハンドラ設定
        set_error_handler("error_handler");

        $conf = TGMDK_Config::getInstance();
        $array = $conf->getCipherParameters();

        if (isset($array[TGMDK_Config::PRIVATE_CERT_FILE])) {
            $this->storeFile = $array[TGMDK_Config::PRIVATE_CERT_FILE];
        }
        if (isset($array[TGMDK_Config::PRIVATE_CERT_PASSWORD])) {
            $this->storePass = $array[TGMDK_Config::PRIVATE_CERT_PASSWORD];
        }
        if (isset($array[TGMDK_Config::TRUST_CERT_FILE])) {
            $this->trustFile = $array[TGMDK_Config::TRUST_CERT_FILE];
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
     * 暗号化メソッド。
     *
     * @access public
     * @param String $pal 暗号化する文字列
     * @return String 暗号化された文字列
     */
    public function encryption($pal) {
        try {
            // パラメータチェック
            if (is_null($pal) || empty($pal)) {
                // エラーハンドラの破棄
                restore_error_handler();

                return "";
            }
            // private key 取得
            $private_str = fopen($this->storeFile, "r");
            $private_cert = fread($private_str, 8192);
            fclose($private_str);
            $private_key = openssl_pkey_get_private(Array($private_cert, $this->storePass));

            // public key 取得
            $public_str = fopen($this->trustFile, "r");
            $public_cert = fread($public_str, 8192);
            fclose($public_str);
            $public_key = openssl_pkey_get_public($public_cert);

            // 各鍵が正常に生成されたことを確認する
            if ($private_key == FALSE or $public_key == FALSE) {
                throw new TGMDK_Exception(TGMDK_Exception::MA04_MESSAGE_ENCRYPT_ERROR);
            }

            // パラメータからメッセージダイジェストを生成
            $digest = hash("sha256", $pal);

            // 署名を計算
            openssl_sign($digest, $signature, $private_key);

            // 署名をBase64化
            $signature_base64 = $this->base64Enc($signature);

            // パラメータに署名を追加
            $pal = $pal . "&signedDigest=" . $signature_base64;

            // GZipで圧縮
            $zipPal = gzencode($pal);

            // 共通鍵の生成
            $key = $this->getRandomString();

            // 共通鍵(AES)で電文を暗号化
            $enc = $this->encrypt($zipPal, $key);

            // 共通鍵を公開鍵で暗号化
            openssl_public_encrypt($key, $crypted, $public_key);

            // GZipで圧縮
            $zip2 = gzencode(($crypted . $enc));

            // Base64エンコード
            $rtnStr = $this->base64Enc($zip2);

            // 最後処理
            // キーの開放
            openssl_free_key($private_key);
            openssl_free_key($public_key);

            // エラーハンドラの破棄
            restore_error_handler();

            return $rtnStr;
        } catch (Exception $e) {
            // エラーハンドラの破棄
            restore_error_handler();

            throw new TGMDK_Exception($e, TGMDK_Exception::MA04_MESSAGE_ENCRYPT_ERROR);
        }
    }

    /**
     * 復号化メソッド。
     *
     * @param String $pal 復号化する文字列
     * @return String 復号化された文字列
     */
    public function decryption($pal) {
        try {
            // パラメータチェック
            if (is_null($pal) || empty($pal)) {
                // エラーハンドラの破棄
                restore_error_handler();

                return "";
            }

            // private key 取得
            $private_str = fopen($this->storeFile, "r");
            $private_cert = fread($private_str, 8192);
            fclose($private_str);
            $private_key = openssl_pkey_get_private(Array($private_cert, $this->storePass));

            // public key 取得
            $public_str = fopen($this->trustFile, "r");
            $public_cert = fread($public_str, 8192);
            fclose($public_str);
            $public_key = openssl_pkey_get_public($public_cert);

            // 各鍵が正常に生成されたことを確認する
            if ($private_key == FALSE or $public_key == FALSE) {
                throw new TGMDK_Exception(TGMDK_Exception::MA06_MESSAGE_DECRYPT_ERROR);
            }

            // Base64デコード
            $dec = $this->base64Dec($pal);

            // GZipを解凍
            $key_and_enc = gzinflate(substr($dec, 10));

            // RSA(1024bit)を使用した鍵の長さを示す
            $rsa_key_length = 1024 / 8;

            // 共通鍵を取り出す
            $enckey = substr($key_and_enc, 0, $rsa_key_length);

            // 共通鍵を秘密鍵で復号化
            $resultFlg = openssl_private_decrypt($enckey, $decrypted, $private_key);

            // パラメータ部分を取り出す
            $encParam = substr($key_and_enc, ($rsa_key_length - strlen($key_and_enc)));

            // 共通鍵(AES)で電文を暗号化
            $zip_dec = $this->decrypt($encParam, $decrypted);

            // GZipを解凍
            $signed_digest_in_param = gzinflate(substr($zip_dec, 10));

            // ダイジェストパラメータのキー文字列
            $signedDigestString = "&signedDigest=";

            // ダイジェスト文字列の開始位置を取得
            $pos = strpos($signed_digest_in_param, $signedDigestString);

            // パラメータ部分のみ取得
            $rtnStr = substr($signed_digest_in_param, 0, $pos);

            // ダイジェスト文字列のみ取得
            $digest_start_pos = $pos + strlen($signedDigestString);
            $digest_end_pos   = strlen($signed_digest_in_param);
            $signature = substr($signed_digest_in_param, ($digest_start_pos - $digest_end_pos));

            // パラメータからメッセージダイジェストを生成
            $digest = hash("sha256", $rtnStr);

            // ダイジェスト検証
            $verify = openssl_verify($digest, $this->base64Dec($signature), $public_key);
            if ($verify != 1) {
                throw new Exception("データが改竄されています");
            }

            // 最後処理
            // キーの開放
            openssl_free_key($private_key);
            openssl_free_key($public_key);

            // エラーハンドラの破棄
            restore_error_handler();

            return $rtnStr;
        } catch (Exception $e) {
            // エラーハンドラの破棄
            restore_error_handler();

            throw new TGMDK_Exception($e, TGMDK_Exception::MA06_MESSAGE_DECRYPT_ERROR);
        }
    }

    /**
     * Bese64エンコードを行う。
     * エンコード後の文字列を"/"を"-"に、"+"を"_"に、"="を"*"に置換して返却する。
     *
     * @access public
     * @param String $data エンコードする文字列
     * @return String エンコードを行った結果の文字列
     */
    public function base64Enc($data) {
        $data = base64_encode($data);
        $data = str_replace("/", "-", $data);
        $data = str_replace("+", "_", $data);
        $data = str_replace("=", "*", $data);

        return $data;
    }

    /**
     * Bese64デコードを行う。
     * base64Encの逆。
     * デコード前の文字列を"-"を"/"に、"_"を"+"に、"*"を"="に置換してからデコードする。
     *
     * @access public
     * @param String $data デコードする文字列
     * @return String デコードを行った結果の文字列
     */
    public function base64Dec($data) {
        $data = str_replace("*", "=", $data);
        $data = str_replace("_", "+", $data);
        $data = str_replace("-", "/", $data);
        $data = base64_decode($data);

        return $data;
    }

    /**
     * 共通鍵暗号化メソッド。
     *
     * @access private
     * @param String $data 暗号化する文字列
     * @param String $key 共通鍵
     * @return String 共通鍵で暗号化した文字列
     */
    private function encrypt($data, $key) {
        // ブロックサイズを取得
        $block_size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);

        // ブロック処理
        $data = $this->pkcs5_pad($data, $block_size);

        // 暗号化モジュールをオープン
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128 , '', MCRYPT_MODE_CBC, '');

        // IV生成
        mt_srand();
        $iv_length = mcrypt_enc_get_iv_size($td);
        $iv = mcrypt_create_iv($iv_length, MCRYPT_RAND);

        // 初期化
        mcrypt_generic_init($td, $key, $iv);

        // 暗号化
        $data = mcrypt_generic($td, $data);

        // 暗号化ハンドラを終了
        mcrypt_generic_deinit($td);

        // 暗号化モジュールをクローズ
        mcrypt_module_close($td);

        // 暗号化したデータの頭にIVを連結
        $data = $iv . $data;

        return $data;
    }

    /**
     * 共通鍵復号化メソッド。
     *
     * @access private
     * @param String $data 復号化する文字列
     * @param String $key 共通鍵
     * @return String 共通鍵で復号化した文字列
     */
    private function decrypt($data, $key) {
        // ブロックサイズを取得
        $block_size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);

        // 復号化モジュールをオープン
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128 , '', MCRYPT_MODE_CBC, '');

        // IV生成
        $iv_length = mcrypt_enc_get_iv_size($td);
        // $dataからIVを抜き出す
        $iv = substr($data, 0, $iv_length);

        // $dataからIVを取り除いた部分を抜き出す
        $data = substr($data, ($iv_length - strlen($data)));

        // 初期化
        mcrypt_generic_init($td, $key, $iv);

        // 復号化
        $data = mdecrypt_generic($td, $data);

        // 復号化ハンドラを終了
        mcrypt_generic_deinit($td);

        // 復号化モジュールをクローズ
        mcrypt_module_close($td);

        // ブロック削除処理
        $data = $this->pkcs5_unpad($data);

        return $data;
    }

    /**
     * PKCS#5でパディングする。
     *
     * @access private
     * @param String $text ブロックを追加する文字列
     * @param int $blocksize ブロック数
     * @return String ブロックが追加された文字列
     */
    private function pkcs5_pad ($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    /**
     * PKCS#5のパディングを除去する。
     * pkcs5_padの逆処理。
     *
     * @access private
     * @param String $text ブロックを削除する前の文字列
     * @return String ブロックを削除した文字列
     */
    private function pkcs5_unpad($text) {
        $pad = ord($text{strlen($text)-1});
        if ($pad > strlen($text)) return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false;
        return substr($text, 0, -1 * $pad);
    }

    /**
     * ランダムな文字列を生成する。
     *
     * @access private
     * @param int $nLengthRequired 必要な文字列長。省略すると 8 文字
     * @return String ランダムな文字列
     */
    private function getRandomString($nLengthRequired = 16) {
        $sCharList = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_";
        mt_srand();
        $sRes = "";
        for($i = 0; $i < $nLengthRequired; $i++)
            $sRes .= $sCharList{mt_rand(0, strlen($sCharList) - 1)};
        return $sRes;
    }

}
