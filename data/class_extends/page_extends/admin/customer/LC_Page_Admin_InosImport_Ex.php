<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';
// }}}

/**
 * ページクラス(拡張).
 *
 * (イノス)通販システムの顧客情報をインポートする
 *
 * @package Page
 * @author IQUEVE CO.,LTD.
 * @version $Id: LC_Page_Admin_InosImport_Ex.php 338 2012-06-01 07:28:57Z nao $
 */
class LC_Page_Admin_InosImport_Ex extends LC_Page_Admin_Ex
{
    // {{{ properties

    /** 専用エラーチェックフィールド情報 */
    var $arrCustomCheckItem;

    /** エラーメッセージ **/
    var $arrRowErr;

    /** 正常メッセージ **/
    var $arrRowResult;

    /** 氏名の分割用正規表現パターン */
    const NAME_SEPARATE_PTN = '/^([^ 　]+)(?: +|　+)?([^ 　].*)$/u';

    var $arrPref;
    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        $masterData = new SC_DB_MasterData_Ex();

        parent::init();
        $this->tpl_mainpage = 'customer/inos_import.tpl';
        $this->tpl_mainno = 'customer';
        $this->tpl_subno = 'inos_import';
        $this->tpl_maintitle = '顧客管理';
        $this->tpl_subtitle = '通販システム顧客情報インポート';

        $this->arrCustomCheckItem = array();
        $this->arrRowErr = array();
        $this->arrRowResult = array();
        $this->arrPref = $masterData->getMasterData('mtb_pref');

        // CSVレイアウトの初期化
        $this->arrCSVFrame = $this->getCsvFrame();

        set_time_limit(0);
    }

    /**
     * CSVレイアウトを返す
     *
     * @return array
     */
    function getCsvFrame() {
        $arrFrame = array(
            /*
             */
            array(
                "no"                     => 1,
                "csv_id"                 => 99991,
                "col"                    => "uniquekey",
                "disp_name"              => "システムユニークキー",
                "rank"                   => 1,
                "status"                 => 1,
                "rw_flg"                 => 1,
                "mb_convert_kana_option" => "n",
                "size_const_type"        => 11,
                "error_check_types"      => "MAX_LENGTH_CHECK",
            ),
            array(
                "no"                     => 2,
                "csv_id"                 => 99991,
                "col"                    => "name",
                "disp_name"              => "顧客名(姓名)",
                "rank"                   => 2,
                "status"                 => 1,
                "rw_flg"                 => 3,
                "mb_convert_kana_option" => "aKV",
                "size_const_type"        => "SMTEXT_LEN",
                "error_check_types"      => "EXIST_CHECK,MAX_LENGTH_CHECK",
            ),
            array(
                "no"                     => 3,
                "csv_id"                 => 99991,
                "col"                    => "kana",
                "disp_name"              => "顧客名カナ(姓名)",
                "rank"                   => 3,
                "status"                 => 1,
                "rw_flg"                 => 3,
                "mb_convert_kana_option" => "aCKV",
                "size_const_type"        => "SMTEXT_LEN",
                "error_check_types"      => "EXIST_CHECK,MAX_LENGTH_CHECK,KANABLANK_CHECK",
            ),
            array(
                "no"                     => 4,
                "csv_id"                 => 99991,
                "col"                    => "email",
                "disp_name"              => "顧客メールアドレス",
                "rank"                   => 4,
                "status"                 => 1,
                "rw_flg"                 => 3,
                "mb_convert_kana_option" => "a",
                "size_const_type"        => 256,
                "error_check_types"      => "EXIST_CHECK,NO_SPTAB,EMAIL_CHECK,SPTAB_CHECK,EMAIL_CHAR_CHECK",
            ),
            array(
                "no"                     => 5,
                "csv_id"                 => 99991,
                "col"                    => "tel",
                "disp_name"              => "顧客電話番号",
                "rank"                   => 5,
                "status"                 => 1,
                "rw_flg"                 => 3,
                "mb_convert_kana_option" => "n",
                "size_const_type"        => 18,
                "error_check_types"      => "EXIST_CHECK,SPTAB_CHECK,NUM_CHECK,MAX_LENGTH_CHECK",
            ),
            array(
                "no"                     => 6,
                "csv_id"                 => 99991,
                "col"                    => "fax",
                "disp_name"              => "顧客FAX番号",
                "rank"                   => 6,
                "status"                 => 1,
                "rw_flg"                 => 3,
                "mb_convert_kana_option" => "n",
                "size_const_type"        => 18,
                "error_check_types"      => "SPTAB_CHECK,NUM_CHECK,MAX_LENGTH_CHECK",
            ),
            array(
                "no"                     => 7,
                "csv_id"                 => 99991,
                "col"                    => "zip",
                "disp_name"              => "顧客郵便番号",
                "rank"                   => 7,
                "status"                 => 1,
                "rw_flg"                 => 3,
                "mb_convert_kana_option" => "n",
                "size_const_type"        => 7,
                "error_check_types"      => "EXIST_CHECK,NUM_CHECK,MAX_LENGTH_CHECK,MIN_LENGTH_CHECK",
            ),
            array(
                "no"                     => 9,
                "csv_id"                 => 99991,
                "col"                    => "addr01",
                "disp_name"              => "顧客住所1",
                "rank"                   => 9,
                "status"                 => 1,
                "rw_flg"                 => 3,
                "mb_convert_kana_option" => "n",
                "size_const_type"        => "SMTEXT_LEN",
                "error_check_types"      => "EXIST_CHECK,MAX_LENGTH_CHECK",
            ),
            array(
                "no"                     => 10,
                "csv_id"                 => 99991,
                "col"                    => "addr02",
                "disp_name"              => "顧客住所2",
                "rank"                   => 10,
                "status"                 => 1,
                "rw_flg"                 => 3,
                "mb_convert_kana_option" => "n",
                "size_const_type"        => "SMTEXT_LEN",
                "error_check_types"      => "MAX_LENGTH_CHECK",
            ),
            array(
                "no"                     => 11,
                "csv_id"                 => 99991,
                "col"                    => "customer_id",
                "disp_name"              => "Web顧客ID",
                "rank"                   => 11,
                "status"                 => 1,
                "rw_flg"                 => 3,
                "mb_convert_kana_option" => "n",
                "size_const_type"        => "INT_LEN",
                "error_check_types"      => "NUM_CHECK,MAX_LENGTH_CHECK",
            ),
            array(
                "no"                     => 12,
                "csv_id"                 => 99991,
                "col"                    => "last_buy_date",
                "disp_name"              => "最終購入日",
                "rank"                   => 12,
                "status"                 => 1,
                "rw_flg"                 => 3,
                "mb_convert_kana_option" => "n",
                "size_const_type"        => 10,
                "error_check_types"      => "MAX_LENGTH_CHECK,CHECK_DATE",

            ),
            array(
                "no"                     => 13,
                "csv_id"                 => 99991,
                "col"                    => "point",
                "disp_name"              => "保持ポイント",
                "rank"                   => 13,
                "status"                 => 1,
                "rw_flg"                 => 3,
                "mb_convert_kana_option" => "n",
                "size_const_type"        => "INT_LEN",
                "error_check_types"      => "MAX_LENGTH_CHECK,NUM_CHECK,MAX_LENGTH_CHECK",
            ),
            array(
                "no"                     => 14,
                "csv_id"                 => 99991,
                "col"                    => "point_valid_date",
                "disp_name"              => "保持ポイント有効期限",
                "rank"                   => 14,
                "status"                 => 1,
                "rw_flg"                 => 3,
                "mb_convert_kana_option" => "n",
                "size_const_type"        => 10,
                "error_check_types"      => "MAX_LENGTH_CHECK,CHECK_DATE",

            ),
            array(
                "no"                     => 15,
                "csv_id"                 => 99991,
                "col"                    => "birth_point",
                "disp_name"              => "誕生日ポイント",
                "rank"                   => 15,
                "status"                 => 1,
                "rw_flg"                 => 3,
                "mb_convert_kana_option" => "n",
                "size_const_type"        => "INT_LEN",
                "error_check_types"      => "MAX_LENGTH_CHECK,NUM_CHECK,MAX_LENGTH_CHECK",
            ),
            array(
                "no"                     => 16,
                "csv_id"                 => 99991,
                "col"                    => "birth_point_valid_date",
                "disp_name"              => "誕生日ポイント有効期限",
                "rank"                   => 16,
                "status"                 => 1,
                "rw_flg"                 => 3,
                "mb_convert_kana_option" => "n",
                "size_const_type"        => 10,
                "error_check_types"      => "MAX_LENGTH_CHECK,CHECK_DATE",

            ),
            array(
                "no"                     => 17,
                "csv_id"                 => 99991,
                "col"                    => "purchased01",
                "disp_name"              => "購入履歴判定",
                "rank"                   => 17,
                "status"                 => 1,
                "rw_flg"                 => 3,
                "mb_convert_kana_option" => "n",
                "size_const_type"        => 1,
                "error_check_types"      => "NUM_CHECK,MAX_LENGTH_CHECK",
            ),
            array(
                "no"                     => 18,
                "csv_id"                 => 99991,
                "col"                    => "buy_times",
                "disp_name"              => "購入回数",
                "rank"                   => 18,
                "status"                 => 1,
                "rw_flg"                 => 3,
                "mb_convert_kana_option" => "n",
                "size_const_type"        => "INT_LEN",
                "error_check_types"      => "MAX_LENGTH_CHECK,NUM_CHECK,MAX_LENGTH_CHECK",
            ),
            array(
                "no"                     => 19,
                "csv_id"                 => 99991,
                "col"                    => "mailmaga_flg",
                "disp_name"              => "メルマガ受信区分",
                "rank"                   => 19,
                "status"                 => 1,
                "rw_flg"                 => 3,
                "mb_convert_kana_option" => "n",
                "size_const_type"        => "INT_LEN",
                "error_check_types"      => "MAX_LENGTH_CHECK,NUM_CHECK,MAX_LENGTH_CHECK",
            ),
            array(
                "no"                     => 20,
                "csv_id"                 => 99991,
                "col"                    => "dm_flg",
                "disp_name"              => "DM区分",
                "rank"                   => 20,
                "status"                 => 1,
                "rw_flg"                 => 3,
                "mb_convert_kana_option" => "n",
                "size_const_type"        => "INT_LEN",
                "error_check_types"      => "MAX_LENGTH_CHECK,NUM_CHECK,MAX_LENGTH_CHECK",
            ),
        );
        return $arrFrame;
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
        $this->objDb = new SC_Helper_DB_Ex();

        // CSVファイルアップロード情報の初期化
        $objUpFile = new SC_UploadFile_Ex(DOWN_TEMP_REALDIR, DOWN_SAVE_REALDIR);

        $this->lfInitFile($objUpFile);

        // パラメーター情報の初期化
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam, $this->arrCSVFrame);

        $objFormParam->setHtmlDispNameArray();
        $this->arrTitle = $objFormParam->getHtmlDispNameArray();

        switch($this->getMode()) {
        case 'csv_upload':
            $this->doUploadCsv($objFormParam, $objUpFile);
            break;
        default:
            break;
        }
    }

    /**
     * 登録/編集結果のメッセージをプロパティへ追加する
     *
     * @param integer $line_count 行数
     * @param stirng $message メッセージ
     * @return void
     */
    function addRowResult($line_count, $message) {
        $this->arrRowResult[] = $line_count . "行目：" . $message;
    }

    /**
     * 登録/編集結果のエラーメッセージをプロパティへ追加する
     *
     * @param integer $line_count 行数
     * @param stirng $message メッセージ
     * @return void
     */
    function addRowErr($line_count, $message) {
        $this->arrRowErr[] = $line_count . "行目：" . $message;
    }

    /**
     * CSVアップロードを実行します.
     *
     * @return void
     */
    function doUploadCsv(&$objFormParam, &$objUpFile) {
        // ファイルアップロードのチェック
        $objUpFile->makeTempFile('csv_file');
        $arrErr = $objUpFile->checkExists();

        if (count($arrErr) > 0) {
            $this->arrErr = $arrErr;
            return;
        }
        // 一時ファイル名の取得
        $filepath = $objUpFile->getTempFilePath('csv_file');
        // CSVファイルの文字コード変換
        $enc_filepath = SC_Utils_Ex::sfEncodeFile($filepath, CHAR_CODE, CSV_TEMP_REALDIR, 'SJIS-WIN');
        // CSVファイルのオープン
        $fp = fopen($enc_filepath, 'r');
        // 失敗した場合はエラー表示
        if (!$fp) {
             SC_Utils_Ex::sfDispError("");
        }

        // 登録対象の列数
        $col_max_count = $objFormParam->getCount();
        // 行数
        $line_count = 0;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();

        $errFlag = false;
        $all_line_checked = false;

        while (!feof($fp)) {
            $arrCSV = fgetcsv($fp, CSV_LINE_MAX);

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
            if ($line_count == 1) {
                continue;
            }
            // 空行はスキップ
            if (empty($arrCSV)) {
                continue;
            }

            // 列数が異なる場合はエラー
            $col_count = count($arrCSV);
            if ($col_max_count != $col_count) {
                $this->addRowErr($line_count, "※ 項目数が" . $col_count . "個検出されました。項目数は" . $col_max_count . "個になります。");
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
                foreach ($arrCSVErr as $err) {
                    $this->addRowErr($line_count, $err);
                }
                $errFlag = true;
                break;
            }

            if ($all_line_checked) {
		$this->lfRegistInosCustomer($objQuery, $line_count,
					    $objFormParam);
  
 	        $arrParam = $objFormParam->getHashArray();

		$this->addRowResult($line_count, sprintf
				    ("氏名：%s / 電話番号：%s",
				     $arrParam['name'],
				     $arrParam['tel']));
            }
        }
        fclose($fp);

        // 実行結果画面を表示
        $this->tpl_mainpage = 'customer/inos_import_complete.tpl';

        if ($errFlag) {
            $objQuery->rollback();
            return;
        }

        $objQuery->commit();

        return;
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * ファイル情報の初期化を行う.
     *
     * @return void
     */
    function lfInitFile(&$objUpFile) {
        $objUpFile->addFile("CSVファイル", 'csv_file', array('csv'), CSV_SIZE, true, 0, 0, false);
    }

    /**
     * 入力情報の初期化を行う.
     *
     * @param array CSV構造設定配列
     * @return void
     */
    function lfInitParam(&$objFormParam, &$arrCSVFrame) {
        // 固有の初期値調整
        $arrCSVFrame = $this->lfSetParamDefaultValue($arrCSVFrame);
        // CSV項目毎の処理
        foreach($arrCSVFrame as $item) {
            if($item['status'] == CSV_COLUMN_STATUS_FLG_DISABLE) continue;
            //サブクエリ構造の場合は AS名 を使用
            if(preg_match_all('/\(.+\)\s+as\s+(.+)$/i', $item['col'], $match, PREG_SET_ORDER)) {
                $col = $match[0][1];
            }else{
                $col = $item['col'];
            }

            // 共通処理できないエラーチェック処理を保存
            $arrCustomError = array(
                'CHECK_NAME',
                'CHECK_NAME_KANA',
                'CHECK_DATE',
            );
            $arrTypes = explode(',', strtoupper($item['error_check_types']));
            $arrTypes = array_map('trim', $arrTypes);
            $arrErrorCheckTypes = array();
            foreach ($arrTypes as $chk) {
                if (in_array($chk, $arrCustomError) == true) {
                    $this->arrCustomCheckItem[$chk][] = $item;
                } else {
                    $arrErrorCheckTypes[] = $chk;
                }
            }

            $size = defined($item['size_const_type'])?
                constant($item['size_const_type']) : $item['size_const_type'];

            // パラメーター登録
            $objFormParam->addParam(
                    $item['disp_name']
                    , $col
                    , $size
                    , $item['mb_convert_kana_option']
                    , $arrErrorCheckTypes
                    , $item['default']
                    , ($item['rw_flg'] != CSV_COLUMN_RW_FLG_READ_ONLY) ? true : false
                    );
        }
    }

    /**
     * 入力チェックを行う.
     *
     * @return void
     */
    function lfCheckError(&$objFormParam) {

        // 入力データを渡す。
        $arrRet = $objFormParam->getHashArray();
        $objErr = new SC_CheckError_Ex($arrRet);
        $objErr->arrErr = $objFormParam->checkError(false);

        // Web顧客IDが未設定の場合
        $customerId = $objFormParam->getValue('customer_id');
        if (empty($customerId)) {
            // メールアドレスの重複チェック
            $objErr->doFunc(array("メールアドレス", 'email'), array("CHECK_REGIST_CUSTOMER_EMAIL"));
            if (isset($objErr->arrErr['email'])) {
                $objErr->arrErr['email'] = preg_replace("/<br(\s+\/)?>/i", "", $objErr->arrErr['email']);
            }
        }

        // 専用タグチェックの実行
        foreach($this->arrCustomCheckItem as $chk => $items) {
            foreach ($items as $item) {
                $val = $arrRet[$item['col']];
                switch ($chk) {
                case 'CHECK_NAME' :
                case 'CHECK_NAME_KANA' :
                    // 分割できること
                    $ptn = self::NAME_SEPARATE_PTN;
                    if (preg_match($ptn, $val, $m) == false) {
                        $objErr->arrErr[$item['col']] = sprintf(
                            "※ %sが正しくありません。",
                            $item['disp_name']
                        );
                    }
                    break;
                case 'CHECK_DATE' :
                    // 独自ロジックでチェック
                    $date = explode('/', $val);
                    if (strcmp($val, '') != 0
                        && (count($date) != 3
                        || checkdate($date[1], $date[2], $date[0]) == false)
                    ) {
                        $objErr->arrErr[$item['col']] = sprintf(
                            "※ %sの日付が正しくありません。",
                            $item['disp_name']
                        );
                        break;
                    }
                    break;
                default :
                    break;
                }
            }
        }
        return $objErr->arrErr;
    }

    /**
     * 前後のスペースをトリムする
     *
     * @param string $string 対象文字
     * @return string トリム後の文字
     */
    function lfTrim($string) {
        // 前
        $string = preg_replace('/^[ 　]+/u', '', $string);
        // 後ろ
        $string = preg_replace('/[ 　]+$/u', '', $string);

        return $string;
    }

    function cutPrefStr(&$strAddr)
    {
	foreach ($this->arrPref as $id => $name) {
	    if (preg_match('/^' . $name . '/', $strAddr)) {
		$strAddr = preg_replace('/^' . $name . '/', '', $strAddr);
		break;
	    }
	}

	return $id;
    }

    /**
     * 通販システム顧客情報登録を行う.
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param string|integer $line 処理中の行数
     * @return void
     */
    function lfRegistInosCustomer($objQuery, $line = "", &$objFormParam) {

        $objProduct = new SC_Product_Ex();
        // 登録データ対象取得
        $arrList = $objFormParam->getHashArray();

        // 登録時間を生成
        // DBのnow()だとcommitした際、すべて同一の時間になってしまう
        $arrList['update_date'] = $this->lfGetDbFormatTimeWithLine($line);

        // カラムに存在しているもののうち、Form投入設定されていないデータは
        // 上書きしない
        $sqlval = SC_Utils_Ex::sfArrayIntersectKeys
	    ($arrList, $objQuery->listTableFields('dtb_customer'));

	// 郵便番号を分割する
	$zip = $arrList['zip'];
	if (isset($zip) && strlen($zip) > 0) {
	    $sqlval['zip01'] = substr($zip, 0, 3);
	    $sqlval['zip02'] = substr($zip, 3, 4);
	}

        // FormParamのデフォルト判定がバカなので自力で対応
        foreach ($sqlval as $k => $v) {
            if (strcmp($v, '') != 0) {
                continue;
            }
            foreach ($this->arrCSVFrame as $item) {
                if ($item['col'] == $k) {
                    if (isset($item['default']) == true) {
                        $sqlval[$k] = $item['default'];
                    }
                    break;
                }
            }
        }

	// 住所1から都道府県を削除し、ついでに都道府県コードをえる
	$sqlval['pref'] = $this->cutPrefStr($sqlval['addr01']);

        // 新規登録
	if (empty($arrList['customer_id'])) {
	    $sqlval['secret_key'] = SC_Helper_Customer_Ex::sfGetUniqSecretKey();
            $sqlval['customer_id'] = $objQuery->nextVal('dtb_customer_customer_id');
	    $sqlval['create_date'] = $arrList['update_date'];
	    $sqlval['status'] = 2;
	    $objQuery->insert("dtb_customer", $sqlval);
	    return;
	}

	// 更新
	$wval = array();
	$wval[] = $sqlval['customer_id'];
	unset($sqlval['customer_id']);

        $objQuery->update("dtb_customer", $sqlval, "customer_id = ?", $wval);
    }

    /**
     * 初期値の設定
     *
     * @param array $arrCSVFrame CSV構造配列
     * @return array $arrCSVFrame CSV構造配列
     */
    function lfSetParamDefaultValue(&$arrCSVFrame) {
        foreach($arrCSVFrame as $key => $val) {
            switch($val['col']) {
            case 'uniquekey' :
            case 'name' :
            case 'kana' :
            case 'email' :
            case 'tel' :
            case 'fax' :
            case 'zip' :
            case 'pref' :
            case 'addr01' :
            case 'addr02' :
            case 'customer_id' :
            case 'last_buy_date' :
            case 'point' :
            case 'point_valid_date' :
            case 'birth_point' :
            case 'birth_point_valid_date' :
            case 'purchased01' :
            case 'buy_times' :
            case 'mailmaga_flg' :
            case 'dm_flg' :
            default:
                $arrCSVFrame[$key]['default'] = '';
                break;
            }
        }
        return $arrCSVFrame;
    }

    /**
     * 指定された行番号をmicrotimeに付与してDB保存用の時間を生成する。
     * トランザクション内のnow()は全てcommit()時の時間に統一されてしまう為。
     *
     * @param string $line_no 行番号
     * @return string $time DB保存用の時間文字列
     */
    function lfGetDbFormatTimeWithLine($line_no = '') {
        $time = date("Y-m-d H:i:s");
        // 秒以下を生成
        if($line_no != '') {
            $microtime = sprintf("%06d", $line_no);
            $time .= ".$microtime";
        }
        return $time;
    }
}
?>
