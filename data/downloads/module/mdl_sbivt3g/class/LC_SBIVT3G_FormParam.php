<?php
/**
 * LC_SBIVT3G_FormParam.php - LC_SBIVT3G_FormParam クラスを定義
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: LC_SBIVT3G_FormParam.php 61 2011-08-23 11:20:05Z hira $
 * @link        http://www.veritrans.co.jp/3gps
*/

/**
 * パラメータ管理クラス
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
class LC_SBIVT3G_FormParam extends SC_FormParam_Ex {

    /**
     * エラーチェックを行う。
     *
     * @param boolean $br
     * @param string $keyname
     * @return array エラー情報
     */
    function checkError($br = true, $keyname = "") {
        $arrRet = $this->getHashArray($keyname);
        $objErr = new LC_SBIVT3G_CheckError($arrRet);
        $cnt = 0;
        foreach($this->keyname as $val) {
            foreach($this->arrCheck[$cnt] as $func) {
                if (!isset($this->param[$cnt])) $this->param[$cnt] = "";
                switch($func) {
                case 'EXIST_CHECK':
                case 'NUM_CHECK':
                case 'EMAIL_CHECK':
                case 'EMAIL_CHAR_CHECK':
                case 'ALNUM_CHECK':
                case 'ALNUM_PLUS_CHECK':
                case 'GRAPH_CHECK':
                case 'KANA_CHECK':
                case 'HANKAKU_KANA_CHECK':
                case 'URL_CHECK':
                case 'SPTAB_CHECK':
                case 'ZERO_CHECK':
                case 'ALPHA_CHECK':
                case 'ZERO_START':
                case 'FIND_FILE':
                case 'NO_SPTAB':
                case 'DIR_CHECK':
                case 'DOMAIN_CHECK':
                case 'FILE_NAME_CHECK':
                case 'MOBILE_EMAIL_CHECK':
                    if(!is_array($this->param[$cnt])) {
                        $objErr->doFunc(array($this->disp_name[$cnt], $val), array($func));
                    } else {
                        $max = count($this->param[$cnt]);
                        for($i = 0; $i < $max; $i++) {
                            $objSubErr = new SC_CheckError($this->param[$cnt]);
                            $objSubErr->doFunc(array($this->disp_name[$cnt], $i), array($func));
                            if(count($objSubErr->arrErr) > 0) {
                                foreach($objSubErr->arrErr as $mess) {
                                    if($mess != "") {
                                        $objErr->arrErr[$val] = $mess;
                                    }
                                }
                            }
                        }
                    }
                    break;
                case 'MAX_CHECK':
                case 'MIN_CHECK':
                case 'MAX_LENGTH_CHECK':
                case 'MIN_LENGTH_CHECK':
                case 'MAX_BYTE_LENGTH_CHECK':
                case 'NUM_COUNT_CHECK':
                case 'KIGO_CHECK':
                    if(!is_array($this->param[$cnt])) {
                        $objErr->doFunc(array($this->disp_name[$cnt], $val, $this->length[$cnt]), array($func));
                    } else {
                        $max = count($this->param[$cnt]);
                        for($i = 0; $i < $max; $i++) {
                            $objSubErr = new SC_CheckError($this->param[$cnt]);
                            $objSubErr->doFunc(array($this->disp_name[$cnt], $i, $this->length[$cnt]), array($func));
                            if(count($objSubErr->arrErr) > 0) {
                                foreach($objSubErr->arrErr as $mess) {
                                    if($mess != "") {
                                        $objErr->arrErr[$val] = $mess;
                                    }
                                }
                            }
                        }
                    }
                    break;
                // 小文字に変換
                case 'CHANGE_LOWER':
                    $this->param[$cnt] = strtolower($this->param[$cnt]);
                    break;
                // 大文字に変換
                case 'CHANGE_UPPER':
                    $this->param[$cnt] = strtoupper($this->param[$cnt]);
                    break;
                // ファイルの存在チェック
                case 'FILE_EXISTS':
                    if($this->param[$cnt] != "" && !file_exists($this->check_dir . $this->param[$cnt])) {
                        $objErr->arrErr[$val] = "※ " . $this->disp_name[$cnt] . "のファイルが存在しません。<br>";
                    }
                    break;
                default:
                    $objErr->arrErr[$val] = "※※　エラーチェック形式($func)には対応していません　※※ <br>";
                    break;
                }
            }

            if (isset($objErr->arrErr[$val]) && !$br) {
                $objErr->arrErr[$val] = ereg_replace("<br>$", "", $objErr->arrErr[$val]);
            }
            $cnt++;
        }
        return $objErr->arrErr;
    }

}
?>
