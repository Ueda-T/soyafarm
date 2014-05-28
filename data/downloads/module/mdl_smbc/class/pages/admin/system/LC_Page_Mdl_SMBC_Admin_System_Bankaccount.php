<?php
// {{{ requires
require_once(CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php');
require_once(MODULE_REALDIR . 'mdl_smbc/inc/include.php');
require_once(MDL_SMBC_CLASS_PATH . 'SC_Mdl_SMBC.php');
require_once(MDL_SMBC_CLASS_PATH . 'SC_SMBC.php');
/**
 * クレジット請求管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Mdl_SMBC_Admin_System_Bankaccount extends LC_Page_Admin_Ex {

    // エラー内容を格納する配列
    var $arrErr;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        $this->skip_load_page_layout = true;
        parent::init();
        $this->tpl_mainpage = MDL_SMBC_TEMPLATE_PATH . 'admin/system/bankaccount.tpl';
        $this->tpl_mainno = 'system';
        $this->tpl_subno = 'bankaccount';
        $this->tpl_maintitle = 'システム設定';
        $this->tpl_subtitle = '口座情報管理';

    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $this->objSmbc = new SC_SMBC();

        $this->objDb = new SC_Helper_DB_Ex();

        // CSVファイルアップロード情報の初期化
        $objUpFile = new SC_UploadFile_Ex(IMAGE_TEMP_REALDIR, IMAGE_SAVE_REALDIR);
        $this->initUploadFile($objUpFile);

        // パラメーター情報の初期化
        $objFormParam = new SC_FormParam_Ex();
        $mode = $this->getMode();
        $this->initParam($objFormParam, $mode);

        switch ($mode) {
            case 'upload':
                $this->arrErr = $this->checkUploadFile('bankaccount_file');
                if ($this->isError($this->arrErr) === false) {
                    $this->doUploadCsv($objFormParam, $objUpFile);
                }
                break;
            case 'download':
                $this->doDownloadCsv($objFormParam);
                break;
            case 'del':
                $objQuery =& SC_Query_Ex::getSingletonInstance();
                $this->doDelete($objQuery);
                break;
            default:
                break;
        }

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $this->all_bankaccount = $objQuery->count('dtb_mdl_smbc_bankaccount', 'change_flg <> ?', array('9'));
        $this->bankaccount = $objQuery->count('dtb_mdl_smbc_bankaccount', 'bill_name IS NULL AND change_flg = ?', array('0'));

        $change_count = $objQuery->count('dtb_mdl_smbc_bankaccount', 'change_flg IN (?,?)', array('1','2'));
        $this->change_flg = ceil($change_count / 10000);

    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
    }

    /**
     * CSVアップロードを実行します.
     *
     * @return void
     */
    function doUploadCsv(&$objFormParam, &$objUpFile) {
        // ファイルアップロードのチェック
        $this->arrErr['bankaccount_file'] = $objUpFile->makeTempFile('bankaccount_file');
        if (strlen($this->arrErr['bankaccount_file']) >= 1) {
            return;
        }
        $arrErr = $objUpFile->checkExists();
        if (count($arrErr) > 0) {
            $this->arrErr = $arrErr;
            return;
        }
        // 一時ファイル名の取得
        $filepath = $objUpFile->getTempFilePath('bankaccount_file');
        // CSVファイルの文字コード変換
        $enc_filepath = SC_Utils_Ex::sfEncodeFile($filepath, CHAR_CODE, CSV_TEMP_REALDIR);

        // CSVファイルのオープン
        $fp = fopen($enc_filepath, 'r');
        // 失敗した場合はエラー表示
        if (!$fp) {
            SC_Utils_Ex::sfDispError('');
        }

        // 行数
        $line_count = 0;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();

        //$this->doDelete($objQuery);

        $errFlag = false;
        $all_line_checked = false;

        while (!feof($fp)) {
            $arrCSV = $this->fgetcsv_reg($fp, CSV_LINE_MAX);

            // 全行入力チェック後に、ファイルポインターを先頭に戻す
            if (feof($fp) && !$all_line_checked) {
                rewind($fp);
                $line_count = 0;
                $all_line_checked = true;
                continue;
            }

            // 行カウント
            $line_count++;
            // ヘッダ行はスキップ
            if ($line_count == 1 && strlen($arrCSV[0]) != 4 ) {
                continue;
            }
            // 空行はスキップ
            if (empty($arrCSV)) {
                continue;
            }
            // 列数が異なる場合はエラー
            if (count($arrCSV) != 12) {
                $this->arrErr['bankaccount_file'] = "※ アップロードファイルの中身が正しくありません。<br />";
                $errFlag = true;
                break;
            }
            // マスクされている場合はエラー
            if (strpos($arrCSV[8],'*')) {
                $this->arrErr['bankaccount_file'] = "※ アップロードファイルの中身が正しくありません。<br />";
                $errFlag = true;
                break;
            }
            // ダウンロードしたファイルをそのままアップロードしないように
            if ($arrCSV[11] != '0') {
                $this->arrErr['bankaccount_file'] = "※ アップロードファイルの中身が正しくありません。<br />";
                $errFlag = true;
                break;
            }

            // シーケンス配列を格納する。
            $objFormParam->setParam($arrCSV, true);
            $arrRet = $objFormParam->getHashArray();
            $objFormParam->setParam($arrRet);
            // 入力値の変換
            $objFormParam->convParam();
            // <br>なしでエラー取得する。
            $arrCSVErr = $this->lfCheckError($objFormParam);

            // 入力エラーチェック
            if (count($arrCSVErr) > 0) {
                $this->arrErr['bankaccount_file'] = "※ アップロードファイルの中身が正しくありません。<br />";
                $errFlag = true;
                break;
            }

            if ($all_line_checked) {
                $this->lfRegistBankaccount($objQuery, $objFormParam);
            }
            SC_Utils_Ex::extendTimeOut();
        }

        fclose($fp);

        if ($errFlag) {
            $objQuery->rollback();
            return;
        }

        $objQuery->commit();
        $this->tpl_onload = "alert('顧客固定割当情報を更新しました。');";

    }

    /**
     * CSVダウンロードを実行します.
     *
     * @return void
     */
    function doDownloadCsv(&$objFormParam) {

        $arrForm = $objFormParam->getHashArray();
        if($arrForm['download_csv'] < 1){
            $arrForm['download_csv'] = 1;
        }

        $sql  = "SELECT ";
        $sql .="bank_code, branch_code, account_type, account_number, account_name, ";
        $sql .="shop_cd, syuno_co_cd, kyoten_cd, ";
        $sql .="bill_no, bill_name, ";
        $sql .="0 AS kbn, "; // 顧客固定割当時顧客情報登録区分
        $sql .="change_flg "; // 処理区分
        $sql .="FROM dtb_mdl_smbc_bankaccount ";
        $sql .="WHERE change_flg IN (?,?) ";
        $sql .="ORDER BY change_flg DESC, bank_code, branch_code, account_number ";
        $sql .="LIMIT 10000 OFFSET ". 10000 * ($arrForm['download_csv'] - 1);

        $objCSV = new SC_Helper_CSV_Ex();
        $objCSV->sfDownloadCsvFromSql($sql, array('1','2'), 'smbc_'.$arrForm['download_csv'], null, true);
        SC_Response_Ex::actionExit();
    }

    function doDelete($objQuery){
        // DB初期化
        $sqlval = array();
        $sqlval['account_name'] = '';
        $sqlval['shop_cd'] = '';
        $sqlval['syuno_co_cd'] = '';
        $sqlval['kyoten_cd'] = '';
        $sqlval['bill_no'] = '';
        $sqlval['bill_name'] = '';
        $sqlval['change_flg'] = '9';

        $objQuery->update('dtb_mdl_smbc_customer', array('account_number' => ''));
        $objQuery->update('dtb_mdl_smbc_bankaccount', $sqlval);
    }

    /**
     * パラメーター初期化.
     *
     * @param SC_FormParam_Ex $objFormParam
     * @param string $mode モード
     * @return void
     */
    function initParam(&$objFormParam, $mode) {
        $objFormParam->addParam('金融機関コード', 'bank_code', 4, '', array('NUM_CHECK','EXIST_CHECK'));
        $objFormParam->addParam('金融機関支店コード', 'branch_code', 3, '', array('NUM_CHECK','EXIST_CHECK'));
        $objFormParam->addParam('預金種目', 'account_type', 1, '', array('NUM_CHECK','EXIST_CHECK'));
        $objFormParam->addParam('口座番号', 'account_number', 7, '', array('NUM_CHECK','EXIST_CHECK'));
        $objFormParam->addParam('口座名義人', 'account_name', STEXT_LEN);
        $objFormParam->addParam('契約コード', 'shop_cd', MDL_SMBC_SHOP_CD_LEN, '', array('NUM_CHECK','EXIST_CHECK'));
        $objFormParam->addParam('収納企業コード', 'syuno_co_cd', MDL_SMBC_SYUNO_CO_CD_LEN, '', array('NUM_CHECK','EXIST_CHECK'));
        $objFormParam->addParam('拠点コード', 'kyoten_cd', MDL_SMBC_SYUNO_CO_CD_LEN, '', array('NUM_CHECK'));
        $objFormParam->addParam('顧客番号', 'bill_no', 14, '', array('NUM_CHECK'));
        $objFormParam->addParam('顧客名', 'bill_name', STEXT_LEN);
        $objFormParam->addParam('顧客固定割当時顧客情報登録区分', 'toroku_kbn', 1, '', array('NUM_CHECK','EXIST_CHECK'));
        $objFormParam->addParam('処理区分', 'shori_kbn', 1, '', array('NUM_CHECK','EXIST_CHECK'));

        $objFormParam->addParam('mode', 'mode', INT_LEN, '', array('ALPHA_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('download_csv', 'download_csv', INT_LEN, '', array('NUM_CHECK'));
    }
    /**
     * ファイルパラメーター初期化.
     *
     * @param SC_UploadFile_Ex $objUpFile SC_UploadFileのインスタンス.
     * @param string $key 登録するキー.
     * @return void
     */
    function initUploadFile(&$objUpFile) {
        $objUpFile->addFile('アップロードファイル', 'bankaccount_file', array('csv'), FILE_SIZE, true, 0, 0, false);
    }

    /**
     * ファイルが指定されている事をチェックします.
     *
     * @param string $file ファイル
     * @param string $file_key ファイルキー
     * @return array エラー情報を格納した連想配列.
     */
    function checkUploadFile($file_key) {
        $objErr = new SC_CheckError_Ex();
        // 拡張子チェック
        $objErr->doFunc(array('アップロードファイル', $file_key, array('csv')), array('FILE_EXT_CHECK'));
        // ファイルサイズチェック
        $objErr->doFunc(array('アップロードファイル', $file_key, FILE_SIZE), array('FILE_SIZE_CHECK'));

        return $objErr->arrErr;
    }

    /**
     * エラー情報が格納されているか判定します.
     *
     * @param array $arrErr エラー情報を格納した連想配列.
     * @return boolean.
     */
    function isError($error) {
        if (is_array($error) && count($error) > 0) {
            return true;
        }
        return false;
    }

    /**
     * 登録を行う.
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param string|integer $line 処理中の行数
     * @return void
     */
    function lfRegistBankaccount($objQuery, &$objFormParam) {

        $arrList = $objFormParam->getHashArray();

        // 更新・追加
        $sqlval = array();
        $sqlval['account_name']  = $arrList['account_name'];
        $sqlval['shop_cd']       = $arrList['shop_cd'];
        $sqlval['syuno_co_cd']   = $arrList['syuno_co_cd'];
        $sqlval['kyoten_cd']     = $arrList['kyoten_cd'];
        if(strlen($arrList['bill_no']) && strlen($arrList['bill_name'])){
            // 存在チェック
            $exists = $objQuery->exists('dtb_mdl_smbc_customer', "customer_id = ?", array((int)$arrList['bill_no']));
            $sql = array();
            $sql['branch_code']    = $arrList['branch_code'];
            $sql['account_number'] = $arrList['account_number'];

            if($exists){
                // update
                $objQuery->update('dtb_mdl_smbc_customer', $sql, "customer_id = ?", array((int)$arrList['bill_no']));
            }else{
                $sql['customer_id'] = (int)$arrList['bill_no'];

                // insert
                $objQuery->insert('dtb_mdl_smbc_customer', $sql);
            }
            $sqlval['bill_no']   = str_pad((int)$arrList['bill_no'], 14, '0', STR_PAD_LEFT);
            $sqlval['bill_name'] = mb_convert_kana($arrList['bill_name'], "KVAN");
            $sqlval['bill_name'] = mb_strcut($sqlval['bill_name'], 0, MDL_SMBC_CUSTOMER_NAME_MAX_LEN);

        }else{
            $objQuery->update('dtb_mdl_smbc_customer', array('account_number'=> ''), "account_number = ?", array($arrList['account_number']));
            $sqlval['bill_no']   = '';
            $sqlval['bill_name'] = '';
        }
        $sqlval['change_flg']   = '0';

        // 存在チェック
        $where = "bank_code = ? AND branch_code = ? AND account_type = ? AND account_number = ?";
        $whereVal = array($arrList['bank_code'], $arrList['branch_code'], $arrList['account_type'], $arrList['account_number']);
        $exists = $objQuery->exists('dtb_mdl_smbc_bankaccount', $where, $whereVal);

        if($exists){
            // update
            $objQuery->update('dtb_mdl_smbc_bankaccount', $sqlval, $where, $whereVal);
        }else{
            $sqlval['bank_code']      = $arrList['bank_code'];
            $sqlval['branch_code']    = $arrList['branch_code'];
            $sqlval['account_type']   = $arrList['account_type'];
            $sqlval['account_number'] = $arrList['account_number'];
            // 未使用口座を優先的に使用するために過去日付をセットする
            $sqlval['update_date']    = '2000-01-01 00:00:00';

            // insert
            $objQuery->insert('dtb_mdl_smbc_bankaccount', $sqlval);
        }
    }

    /**
     * 入力チェックを行う.
     *
     * @return void
     */
    function lfCheckError(&$objFormParam) {
        // 入力データを渡す。
        $arrRet =  $objFormParam->getHashArray();
        $objErr = new SC_CheckError_Ex($arrRet);
        $objErr->arrErr = $objFormParam->checkError(false);
        // HTMLタグチェックの実行
        foreach ($this->arrTagCheckItem as $item) {
            $objErr->doFunc(array($item['disp_name'], $item['col'], $this->arrAllowedTag), array('HTML_TAG_CHECK'));
        }
        return $objErr->arrErr;
    }

    /**
     * 指定された行番号をmicrotimeに付与してDB保存用の時間を生成する。
     * トランザクション内のCURRENT_TIMESTAMPは全てcommit()時の時間に統一されてしまう為。
     *
     * @param string $line_no 行番号
     * @return string $time DB保存用の時間文字列
     */
    function lfGetDbFormatTimeWithLine($line_no = '') {
        $time = date('Y-m-d H:i:s');
        // 秒以下を生成
        if ($line_no != '') {
            $microtime = sprintf('%06d', $line_no);
            $time .= ".$microtime";
        }
        return $time;
    }

    function fgetcsv_reg (&$handle, $length = null, $d = ',', $e = '"') {
        $d = preg_quote($d);
        $e = preg_quote($e);
        $_line = "";
        while (($eof != true)and(!feof($handle))) {
            $_line .= (empty($length) ? fgets($handle) : fgets($handle, $length));
            $itemcnt = preg_match_all('/'.$e.'/', $_line, $dummy);
            if ($itemcnt % 2 == 0) $eof = true;
        }
        $_csv_line = preg_replace('/(?:\\r\\n|[\\r\\n])?$/', $d, trim($_line));
        $_csv_pattern = '/('.$e.'[^'.$e.']*(?:'.$e.$e.'[^'.$e.']*)*'.$e.'|[^'.$d.']*)'.$d.'/';
        preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
        $_csv_data = $_csv_matches[1];
        for($_csv_i=0;$_csv_i<count($_csv_data);$_csv_i++){
            $_csv_data[$_csv_i]=preg_replace('/^'.$e.'(.*)'.$e.'$/s','$1',$_csv_data[$_csv_i]);
            $_csv_data[$_csv_i]=str_replace($e.$e, $e, $_csv_data[$_csv_i]);
        }
        return empty($_line) ? false : $_csv_data;
    }

}
?>
