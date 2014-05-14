<?php
/**
 * LC_SBIVT3G_CheckError.php - LC_SBIVT3G_CheckError クラスを定義
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: LC_SBIVT3G_CheckError.php 61 2011-08-23 11:20:05Z hira $
 * @link        http://www.veritrans.co.jp/3gps
*/

/**
 * エラーチェッククラス
 *
 * @category    Veritrans
 * @package     Lib
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     Release: @package_version@
 * @link        http://www.veritrans.co.jp/3gps
 * @access      public
 * @author      
 */
 class LC_SBIVT3G_CheckError extends SC_CheckError_Ex {
    
    /**
     * バイト数制限のチェック
     * 最大バイト数を超える場合はエラーを返す
     *
     * @param array
     */
     function MAX_BYTE_LENGTH_CHECK($value) {

        if(isset($this->arrErr[$value[1]])){
            return;
        }
        $this->createParam($value);
        // バイト数取得
        $chk_val = mb_convert_encoding($this->arrParam[$value[1]], "EUC-JP", "UTF-8");
        if(strlen($chk_val) > $value[2] ) {
            $this->arrErr[$value[1]] = "※ " . $value[0] . "には" . $value[2] . "Byte以内の文字列を入力してください。<br />";
        }
    }

    /**
     * 半角カタカタのチェックを行う
     * 半角カナが含まれている場合はエラーを返す
     *
     * @param array $value
     */
    function HANKAKU_KANA_CHECK($value){
        if(isset($this->arrErr[$value[1]])){
            return;
        }
        $this->createParam($value);
        if(strlen($this->arrParam[$value[1]]) > 0 &&
            preg_match("/(?:\xEF\xBD[\xA1-\xBF]|\xEF\xBE[\x80-\x9F])/",$this->arrParam[$value[1]])) {
                $this->arrErr[$value[1]] = "※ " . $value[0] . "に半角カタカナを含めないでください。<br />";
        }
   }

    /**
     * 英数字とアンダーバー・ハイフンの判定
     * @param array $value value[0] = 項目名 value[1] = 判定対象文字列
     */
    function ALNUM_PLUS_CHECK( $value ) {
        if(isset($this->arrErr[$value[1]])) {
            return;
        }
        $this->createParam($value);
        if( strlen($this->arrParam[$value[1]]) > 0
        && !preg_match('/^[[:alnum:]\-_]+$/', $this->arrParam[$value[1]])) {
            $this->arrErr[$value[1]] = "※ " . $value[0]
                . "は英数字、ハイフン、アンダーバーで入力してください。<br />";
        }
    }
}
?>
