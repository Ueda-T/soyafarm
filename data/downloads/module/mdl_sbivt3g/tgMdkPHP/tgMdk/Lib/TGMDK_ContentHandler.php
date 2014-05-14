<?php
if (realpath($_SERVER["SCRIPT_FILENAME"]) == realpath(__FILE__)) die('Permission denied.');

if (!defined('MDK_LIB_DIR')) require_once('../3GPSMDK.php');

/**
 *
 * GW処理結果JSONから応答Dtoに変換するクラス<br>
 *
 * 対応する応答Dtoのプロパティ属性<br>
 * ・String<br>
 * ・String[]<br>
 * ・ Dto<br>
 * ・ Dto[]<br>
 * ※上記以外の属性に対するparse処理については動作対象外とする。<br>
 *
 * @category    Veritrans
 * @package     Lib
 * @copyright   VeriTrans Inc.
 * @access  public
 * @author VeriTrans Inc.
 */
class TGMDK_ContentHandler {

    //処理対象ルート
    const ELEM_RESULT_ROOT = "result";
    //決済オプションリザルト
    const ELEM_OPTION_RESULTS = "optionResults";

    /** TGMDK_Configファイルの読み込み */
    private $conf;


    /**
     * コンストラクタ。
     * コンフィグファイルからデータを取得して当クラスを使用できる状態にする。
     * @access public
     */
    public function __construct() {
        $myConf = TGMDK_Config::getInstance();
        $this->conf = $myConf->getEnvironmentParameters();
    }
    
    /**
     * JSONからDtoに変換する
     * @param type $responseArray JSON形式から連想配列に変換したGW処理結果
     * @param type $responseDto レスポンスDTO
     * @param type $className サブオブジェクトのクラス名
     */
    public function parseDto($responseArray, $responseDto) {
        // 全てのメンバをnullを設定する。
        $methods = get_class_methods($responseDto);
        foreach ($methods as $method) {
            if (strpos($method, "get", 0) === 0) {
                $setter = "set". substr($method, 3);
                if (method_exists($responseDto, $setter)) {
                    $responseDto->$setter(null);
                }
            }
        }
        return $this->parse($responseArray, $responseDto);
    }
   
    
    /**
     * JSONからDtoに変換する
     * @param type $responseArray JSON形式から連想配列に変換したGW処理結果
     * @param type $responseDto レスポンスDTO
     * @param type $className サブオブジェクトのクラス名
     */
    public function parse($responseArray, $responseDto) {
        foreach ($responseArray as $key => $value) {
            if (is_array($value)) {
                if ($key === self::ELEM_RESULT_ROOT) {
                    $responseDto = $this->parse($value, $responseDto);
                    continue;
                } else if ($key === self::ELEM_OPTION_RESULTS) {
                    // 決済レスポンスデータのoptionResultsはスキップする。
                    // PayNowIdResponseに設定されているoptionResultsはここに入ってこない
                    continue;
                } else {
                    $className = ucfirst($key);
                }

                $subObject = $this->parseSubObj($value, $className);
                $setterName = "set". ucfirst($key);
                // setterが存在するか
                if (method_exists($responseDto, $setterName)) {
                    $responseDto->$setterName($subObject);
                }
            } else if (is_string($value)) {
                $setterName = "set". ucfirst($key);
                // setterが存在するか
                if (method_exists($responseDto, $setterName)) {
                    $param = $this->encConv($value);
                    $responseDto->$setterName($param);
                }        
            } else if (is_int($value)) {
                $setterName = "set". ucfirst($key);
                // setterが存在するか
                if (method_exists($responseDto, $setterName)) {
                    $responseDto->$setterName($value);
                }   
            } else if (is_bool($value)) {
                $setterName = "set". ucfirst($key);
                // setterが存在するか
                if (method_exists($responseDto, $setterName)) {
                    $responseDto->$setterName($value);
                } 
            }
        }
        return $responseDto;
    }

    /**
     * サブオブジェクトをJSONから変換する
     * @param type $data データ
     * @param type $className クラス名
     * @return サブオブジェクト 
     */
    public function parseSubObj($data, $className) {
        // オブジェクト作成
        $obj = new $className();

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $subClassName = ucfirst($key);
                
                if (class_exists($subClassName)) {
                    $setterName = "set". ucfirst($key);
                    $subObj = $this->parseSubObj($value, $subClassName);
                    if (method_exists($obj, $setterName)) {
                        $obj->$setterName($subObj);
                    }
                } else {
                    if (is_null($temp_val)) {
                        $temp_val = Array();
                    }
                    $arraySub = $this->parseSubObj($value, $className);
                    array_push($temp_val, $arraySub);
                }
            } else if (is_string($value)) {
                $setterName = "set". ucfirst($key);
                // setterが存在するか
                if (method_exists($obj, $setterName)) {
                    $param = $this->encConv($value);
                    $obj->$setterName($param);
                }
            } else if (is_int($value)) {
                $setterName = "set". ucfirst($key);
                // setterが存在するか
                if (method_exists($obj, $setterName)) {
                    $param = $this->encConv($value);
                    $obj->$setterName($param);
                }
            } else if (is_bool($value)) {
                $setterName = "set". ucfirst($key);
                // setterが存在するか
                if (method_exists($obj, $setterName)) {
                    $param = $this->encConv($value);
                    $obj->$setterName($param);
                }
            }
        }
        if (!is_null($temp_val)) {
            $obj = $temp_val;
        }
        return $obj;
    }

    /**
     * レスポンスデータをマスク化する。
     * @param type $responseArray 連想配列にしたGWの処理結果
     * @return type マスク化した連想配列のGW処理結果
     */
    public function maskedResponse($responseArray) {
        
        $maskJsonArray = array();
        foreach ($responseArray as $key => $value) {
            if (is_null($value)) {
                $maskJsonArray[$key] = null;
            } else if (is_array($value)) {
                $maskJsonArray[$key] = $this->maskedResponse($value);
            } else if (is_string($value)) {
                $value = $this->encConv($value);
                $maskJsonArray[$key] = TGMDK_Util::maskValue($key, $value);
            } else if (is_int($value)) {
                $maskJsonArray[$key] = TGMDK_Util::maskValue($key, $value);
            } else if (is_bool($value)) {
                $maskJsonArray[$key] = TGMDK_Util::maskValue($key, $value);
            }
        }
        return $maskJsonArray;
    }
    
    /**
     * MDKで使用されている文字のUTF-8エンコードをマーチャントが指定するエンコードに変更する<br>
     * 指定されていない場合はUTF-8として扱う<br>
     *
     * @access public
     * @param $value 設定値<br>
     * @return エスケープ処理後の設定値
     */
    private function encConv($value) {
        // DTOの文字エンコードを取得
        $dto_enc = $this->conf[TGMDK_Config::DTO_ENCODE];

        // 指定されている場合は指定のエンコードに変換
        if (0 < strlen($dto_enc) && "UTF-8" != strtoupper($dto_enc)) {
        // エンコードが指定されている場合
            return mb_convert_encoding($value, $dto_enc, "UTF-8");
        } else {
            return $value;
        }
    }
}
