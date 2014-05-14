<?php
if (realpath($_SERVER["SCRIPT_FILENAME"]) == realpath(__FILE__)) die('Permission denied.');

if (!defined('MDK_LIB_DIR')) require_once('../3GPSMDK.php');
/*
 * マーチャントが設定した要求DTOよりJSON文字列を作成するクラス
 */
class TGMDK_JSONQuery {
    
    /** N=V要素結合文字 */
    const PARAM_UNITE_CHAR = '&';
    /** N=V要素結合文字エスケープ文字列 */
    const PARAM_UNITE_CHAR_ESCAPE = '\&';
    /** ダブルクオート */
    const DQUOTE_CHAR = "\"";
    /** ダブルクオートエスケープ文字列 */
    const DQUOTE_CHAR_ESCAPE = "\\\"";
    /** N、V結合文字 */
    const NV_UNITE_CHAR = "=";
    /** BaseDto名 */
    const BASE_DTO_NAME = "MdkBaseDto";
    /** サービス固有要素前置詞 */
    const EXPARAM_PREPOSIT = "exparam";
    /** NAME部階層区切り文字 */
    const N_SEP = ".";
    /** サービスタイプのフィールド名 */
    const FIELD_NAME_SERVICE_TYPE = "SERVICE_TYPE";
    /** サービスコマンドのフィールド名 */
    const FIELD_NAME_SERVICE_COMMAND = "SERVICE_COMMAND";
    /** サーチパラメータのフィールド名 */
    const FIELD_NAME_SEARCH_PARAM = "SearchParameter";
    /** サーチの範囲指定フィールド名 */
    const FIELD_NAME_SEARCH_RANGE = "SearchRange";
    
    
    /** TGMDK_Configファイルの読み込み */
    private $conf;

    /** JSON文字 */
    private $jsonParame;

    /** マスクされたJSON文字 */
    private $maskedJsonParam;
    /** サービスタイプ */
    private $serviceType;
    /** サービスコマンド */
    private $serviceCommand;

    /**
     * JSON文字列を取得する
     */
    public function getJsonParam() {
        return $this->jsonParame;
    }
    
    /**
     * マスク化したJSON文字列を取得する
     */
    public function getMaskedJsonParam() {
        return $this->maskedJsonParam;
    }
    
    /**
     * サービスタイプを取得する
     */
    public function getServiceType() {
        return $this->serviceType;
    }

    /**
     * サービスコマンドを取得する
     */
    public function getServiceCommand() {
        return $this->serviceCommand;
    }
    
    /**
     * コンストラクタ。
     * コンフィグファイルからデータを取得して当クラスを使用できる状態にする。
     * @access public
     */
    public function __construct() {
        $myConf = TGMDK_Config::getInstance();
        $this->conf = $myConf->getRequestDtoParameters();
        $this->conf = array_merge($this->conf, $myConf->getResponseDtoParameters());
        $this->conf = array_merge($this->conf, $myConf->getEnvironmentParameters());
    }

    /**
     * リクエストDTOをJSON文字列に変換する
     * @param type $requestDto リクエストDTO
     */
    public function createJsonParameter($requestDto, $className) {
        $arrayData = (array)$requestDto;
        $maskedArrayData = (array)$requestDto;
        if (is_array($arrayData)) {
            foreach ($arrayData as $key => $value) {
                $orgKey = (string)$key;
                $workKey = str_replace($className, "", $orgKey);
                $replacekey = preg_replace('/\W/', '', $workKey);
                if (is_object($value) or is_array($value)) {
                    $arrayData[$replacekey] = $this->createJsonSubObject($value, ucfirst($replacekey), false);
                    $maskedArrayData[$replacekey] = $this->createJsonSubObject($value, ucfirst($replacekey), true);
                } else {
                    if (!is_null($value)) {
                        if (strpos($replacekey, self::FIELD_NAME_SERVICE_TYPE) !== FALSE) {
                            $this->serviceType = $value;
                        } else if (strpos($replacekey, self::FIELD_NAME_SERVICE_COMMAND) !== FALSE) {
                            $this->serviceCommand = $value;
                        } else {
                            $param = $this->encConv($this->valueEscape($value));
                            $arrayData[$replacekey] = $param;
                            $maskedArrayData[$replacekey] = TGMDK_Util::maskValue($replacekey, $param);
                        }
                    }
                }
                if ($orgKey != $replacekey) {
                    unset($arrayData[$orgKey]);
                    unset($maskedArrayData[$orgKey]);
                }
            }
        }
        $arrayData = array_diff($arrayData, array(""));
        $maskedArrayData = array_diff($maskedArrayData, array(""));
        // JSON文字列
        $this->jsonParame = json_encode($arrayData);
        // マスク化したJSON文字列
        $this->maskedJsonParam = $this->unicode_encode(json_encode($maskedArrayData));
    }

    /**
    * Unicodeエスケープされた文字列をUTF-8文字列に戻す。
    * @param unknown_type $str
    */
    private function unicode_encode($str) {
        return preg_replace_callback("/\\\\u([0-9a-zA-Z]{4})/", array($this, "encode_callback"), $str);
    }
    
    private function encode_callback($matches) {
        return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UTF-16");
    }
    
    
     /**
     * サブオブジェクトを解析する。
     * @param $subObject サブオブジェクト
     * @param $className クラス名
     * @return サブオブジェクト
     */
    private function createJsonSubObject($subObject, $className, $is_mask) {
        $arrayData = (array)$subObject;
        foreach ($arrayData as $key => $value) {
            $orgKey = (string)$key;
            
            // SearchRangeがついているものは削除
            $wk = str_replace(self::FIELD_NAME_SEARCH_RANGE, "", $orgKey);
            // SearchParameterを後ろにつけてクラスが存在するか判定
            if (!class_exists($className)) {
                $wcn = $className. self::FIELD_NAME_SEARCH_PARAM;
                if (class_exists($wcn)) {
                    $className = $wcn;
                }
            }
            $workKey = str_replace($className, "", $wk);
            $replacekey = preg_replace('/\W/', '', $workKey);
            if (is_object($value) or is_array($value)) {
                $arrayData[$replacekey] = $this->createJsonSubObject($value, ucfirst($replacekey), $is_mask);
            } else {
                if (!is_null($value)) {
                    $param = $this->encConv($this->valueEscape($value));
                    if ($is_mask) {
                        $arrayData[$replacekey] = TGMDK_Util::maskValue($replacekey, $param);
                    } else {
                        $arrayData[$replacekey] = $param;
                    }
                }
            }
            if ($orgKey != $replacekey) {
                unset($arrayData[$orgKey]);
            }
            if (is_null($arrayData[$replacekey])) {
                unset($arrayData[$replacekey]);
            }
        }
        
        // カラ文字除去
        $arrayData = array_filter($arrayData);
        // サブオブジェクトの要素が0の場合nullを返す
        if (count($arrayData) == 0) {
            return null;
        }

        return $arrayData;
    }

    /**
     * 設定値のエスケープ処理を行う<br>
     * @param $value 設定値<br>
     * @return エスケープ処理後の設定値
     */
    private function valueEscape($value){

        $return_string="";
        //["]⇒[\"]
        $return_string = str_replace(self::DQUOTE_CHAR,self::DQUOTE_CHAR_ESCAPE,$value);
        
        return $return_string;
    }

    /**
     * 設定値のエンコードをMDKで使用するUTF-8のエンコードに変更する<br>
     * 指定されていない場合はUTF-8として扱う<br>
     * @param $value 設定値<br>
     * @return エスケープ処理後の設定値
     */
    private function encConv($value){
        // DTOの文字エンコードを取得
        $dto_enc = $this->conf[TGMDK_Config::DTO_ENCODE];

        // 指定されている場合は指定のエンコードからUTF-8に変換
        if (0 < strlen($dto_enc) && "UTF-8" != strtoupper($dto_enc)) {
            return mb_convert_encoding($value, "UTF-8", $dto_enc);
        } else {
            return $value;
        }
    }
}
?>
