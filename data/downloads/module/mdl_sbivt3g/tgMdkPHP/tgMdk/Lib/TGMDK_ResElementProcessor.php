<?php
if (realpath($_SERVER["SCRIPT_FILENAME"]) == realpath(__FILE__)) die('Permission denied.');

if (!defined('MDK_LIB_DIR')) require_once('../3GPSMDK.php');

/**
 *
 * レスポンスXMLをパースし、指定のタグの値を取得する<br>
 *
 * @category    Veritrans
 * @package     Lib
 * @copyright   VeriTrans Inc.
 * @access  public
 * @author VeriTrans Inc.
 */
class TGMDK_ResElementProcessor {
    
    /** 連想配列にした結果 */
    private $data_array = Array();

    /**
     * コンストラクタ。
     * JSON文字列を連想配列に設定する。
     * 
     * @access  public
     * @param JSON $json_str JSON文字列
     */
    public function  __construct($json_str) {
        // JSON文字列を変換して連想配列にする。
        $this->data_array = json_decode($json_str);
    }

    /**
     * 当クラスのコンストラクタで解析したXMLから定数のQUERY_TRAD_URLに紐付く値を取得する。
     * 
     * @access  public
     * @return String TradのURL
     */
    public function get_trad_url() {
        return $this->get_list(TGMDK_ResElementConstants::QUERY_TRAD_URL);
    }

    /**
     * 引数のパス文字列に紐付く値を配列で返す。
     *
     * @access  public
     * @param String $path データに紐付くパス文字列
     * @return Array 取得された値の配列
     */
    public function get_list($path) {
           
        // 取得パスを分割する
        $targetKeys = split("/", $path);
        return $this->get_value($this->data_array, $targetKeys, 1);
    }
    
    /**
     * 値を取得する。
     * サブオブジェクトの場合は再帰的に実施する。
     * @param type $value
     * @param type $keys 
     */
    public function get_value($data, $keys, $level) {
        
        $str = "";
        if (is_null($data)) {
            return null;
        }
        foreach ($data as $key => $value) {
            if ($keys[$level] === $key) {
                if ($key === "optionResults") {
                    $str = $this->get_value($value[0], $keys, $level + 1);
                    break;
                } else if (is_object($value)) {
                    $str = $this->get_value($value, $keys, $level + 1);
                    break;
                } else if (is_array($value)) {
                    $str = $this->get_value($value, $keys, $level + 1);
                    break;
                } else if (is_string($value)) {
                    $str = $value;
                    break;
                }
            }
        }
        return $str;
    }
       
    /**
     * 引数のパス文字列に紐付く値の配列の一番目を返す。
     *
     * @access  public
     * @param String $path データに紐付くパス文字列
     * @return String 取得された配列の一番目の文字列
     */
    public function get_first($path) {
        $array = $this->get_list($path);

        // 取得したデータが1件もない場合はnullを返却
        if (is_null($array) || count($array) == 0) {
            return null;
        }

        // 1件以上の場合は先頭を返却する
        return $array[0];
    }
}
