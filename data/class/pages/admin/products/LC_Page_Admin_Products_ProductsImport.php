<?php
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';
require_once CLASS_EX_REALDIR . 'helper_extends/SC_Helper_CSV_Ex.php';

/*
 *
 */
class LC_Page_Admin_Products_ProductsImport extends LC_Page_Admin_Ex
{
    // 商品情報カラム
    var $arrProductsCol;
    // 商品規格情報カラム
    var $arrProductsClassCol;

    var $arrFormKeyList;
    var $arrRowErr;
    var $arrRowResult;

    // 商品ID
    var $productId;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/products_import.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'products_import';
        $this->tpl_maintitle = '商品マスタ管理';
        $this->tpl_subtitle = '商品マスタ登録CSV';
        $this->csv_id = '7';

        set_time_limit(0);
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
        $objCsv = new SC_Helper_CSV_Ex();
        $arrCsvFrame = $objCsv->sfGetCsvOutput($this->csv_id);

        // CSV構造がインポート可能かのチェック
        if (!$objCsv->sfIsImportCsvFrame($arrCsvFrame)) {
            // 無効なフォーマットなので初期状態に強制変更
            $arrCsvFrame = $objCsv->sfGetCsvOutput
		($this->csv_id, '', array(), 'no');
            $this->tpl_is_format_default = true;
        }
        // CSV構造は更新可能なフォーマットかのフラグ取得
        $this->tpl_is_update = $objCsv->sfIsUpdateCsvFrame($arrCsvFrame);

        // CSVファイルアップロード情報の初期化
        $objUpFile = new SC_UploadFile_Ex
	    (IMAGE_TEMP_REALDIR, IMAGE_SAVE_REALDIR);
        $this->lfInitFile($objUpFile);

        // パラメーター情報の初期化
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam, $arrCsvFrame);

        $objFormParam->setHtmlDispNameArray();
        $this->arrTitle = $objFormParam->getHtmlDispNameArray();

        switch ($this->getMode()) {
        case 'csv_upload':
            list($r, $count) = $this->doUploadCsv($objFormParam, $objUpFile);

            // バッチ処理履歴情報へデータ登録
            SC_Helper_DB_Ex::sfInsertBatchHistory
                (INOS_DATA_TYPE_REVC_PRODUCT, $count, $r);
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
     * 取込完了のメッセージをプロパティへ追加する
     *
     * @param integer $line_max 件数
     * @param stirng $message メッセージ
     * @return void
     */
    function addRowCompleteMsg($line_max) {
        $this->arrRowResult[] = "取込結果：". $line_max
                              . "件の取込が完了しました。";
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
            return array(INOS_ERROR_FLG_EXIST_ERROR, 0);
        }
        // 一時ファイル名の取得
        $filepath = $objUpFile->getTempFilePath('csv_file');
        // CSVファイルの文字コード変換
        $enc_filepath =
            SC_Utils_Ex::sfEncodeFile
            ($filepath, CHAR_CODE, CSV_TEMP_REALDIR, 'cp932');
        // CSVファイルのオープン
        $fp = fopen($enc_filepath, 'r');
        // 失敗した場合はエラー表示
        if (!$fp) {
             SC_Utils_Ex::sfDispError("");
        }

        // 登録先テーブル カラム情報の初期化
        $this->lfInitTableInfo();

        // 登録フォーム カラム情報
        $this->arrFormKeyList = $objFormParam->getKeyList();

        // 登録対象の列数
        $col_max_count = $objFormParam->getCount();
        // 行数
        $line_count = 0;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();

        $errFlag = false;

        while (!feof($fp)) {
            $arrCSV = fgetcsv($fp, CSV_LINE_MAX);

            // 行カウント
            $line_count++;

            // 空行はスキップ
            if (empty($arrCSV)) {
                continue;
            }
            // 列数が異なる場合はエラー
            $col_count = count($arrCSV);
            if ($col_max_count != $col_count) {
                $this->addRowErr($line_count,
                                 "※ 項目数が" . $col_count .
                                 "個検出されました。項目数は" .
                                 $col_max_count . "個になります。");
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
            // 商品情報登録
            $this->lfRegistProducts($line_count,
                                    $objFormParam);
            // 商品規格情報登録
            $this->lfRegistProductsClass($line_count,
                                         $objFormParam);
            // ログ出力
            if (($line_count % 100) == 0) {
                GC_Utils_Ex::gfPrintLog($line_count. "件の登録が完了しました。");
            }
        }
        // 取込完了のメッセージを表示
        $this->addRowCompleteMsg($line_count - 1);

        // 実行結果画面を表示
        $this->tpl_mainpage = 'products/products_import_complete.tpl';

        fclose($fp);

        if ($errFlag) {
            $objQuery->rollback();
            return array(INOS_ERROR_FLG_EXIST_ERROR, 0);
        }

        $objQuery->commit();
        return array(INOS_ERROR_FLG_EXIST_NORMAL, $line_count - 1);
    }

    /*
     * ファイル情報の初期化を行う.
     */
    function lfInitFile(&$objUpFile) {
        $objUpFile->addFile("CSVファイル", 'csv_file',
                            array('csv'), CSV_SIZE, true, 0, 0, false);
    }

    /*
     * 入力情報の初期化を行う
     */
    function lfInitParam(&$objFormParam, &$arrCsvFrame) {
        // 固有の初期値調整
        $arrCsvFrame = $this->lfSetParamDefaultValue($arrCsvFrame);
        // CSV項目毎の処理
        foreach ($arrCsvFrame as $item) {
            //サブクエリ構造の場合は AS名 を使用
            if (preg_match_all('/\(.+\)\s+as\s+(.+)$/i',
                               $item['col'], $match, PREG_SET_ORDER)) {
                $col = $match[0][1];
            } else {
                $col = $item['col'];
            }
            // HTML_TAG_CHECKは別途実行なので除去し、別保存しておく
            if (strpos(strtoupper($item['error_check_types']),
		       'HTML_TAG_CHECK') !== FALSE) {
                $this->arrTagCheckItem[] = $item;
                $error_check_types = str_replace
		    ('HTML_TAG_CHECK', '',
		     $item['error_check_types']);
            } else {
                $error_check_types = $item['error_check_types'];
            }

            $arrErrorCheckTypes = explode(',', $error_check_types);
            foreach ($arrErrorCheckTypes as $key => $val) {
                if (trim($val) == "") {
                    unset($arrErrorCheckTypes[$key]);
                } else {
                    $arrErrorCheckTypes[$key] = trim($val);
                }
            }

            // パラメーター登録
            $objFormParam->addParam
                ($item['disp_name'],
                 $col,
                 constant($item['size_const_type']),
                 $item['mb_convert_kana_option'],
                 $arrErrorCheckTypes,
                 $item['default'],
                 ($item['rw_flg'] != CSV_COLUMN_RW_FLG_READ_ONLY)
                 ? true : false);
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
        // このフォーム特有の複雑系のエラーチェックを行う
        if (count($objErr->arrErr) == 0) {
            $objErr->arrErr =
                $this->lfCheckErrorDetail($arrRet, $objErr->arrErr);
        }
        return $objErr->arrErr;
    }

    /**
     * 保存先テーブル情報の初期化を行う.
     *
     * @return void
     */
    function lfInitTableInfo() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // 商品情報
        $this->arrProductsCol = 
	        $objQuery->listTableFields('dtb_products');
        // 商品規格情報
        $this->arrProductsClassCol = 
	        $objQuery->listTableFields('dtb_products_class');
    }

    /*
     * 商品情報登録を行う
     */
    function lfRegistProducts($line = "", &$objFormParam) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // 登録データ対象取得
        $arrList = $objFormParam->getHashArray();

        // 商品情報を生成する。
        // 商品情報テーブルのカラムに存在しているものだけ採用する。
        $sqlval =
            SC_Utils_Ex::sfArrayIntersectKeys
                ($arrList, $this->arrProductsCol);

        // 同じ商品IDがある場合は上書き、
        // なければ追加する。
        $where = "product_code = ? AND del_flg = 0 ";
        $product_id = $objQuery->get
            ("product_id", "dtb_products_class",
            $where, array($arrList['product_code']));

        if (strlen($product_id) > 0) {
            $where = "product_id = ?";
            $product_count = $objQuery->count
                ("dtb_products", $where, array($product_id));
        } else {
            $product_count = 0;
        }
        // 更新者
        $sqlval['updator_id'] = $_SESSION['member_id'];

        // この時点で値が入ってないものは登録対象外
        foreach ($sqlval as $key => &$val) {
            if (strlen($val) == 0) {
                unset($sqlval[$key]);
            }
        }
        if ($product_count > 0) {
            // 商品ID
            $sqlval["product_id"] = $product_id;
            // 更新
            $where = "product_id = ? ";
            $objQuery->update("dtb_products", $sqlval,
                              $where, array($sqlval['product_id']));
            // 商品IDをメンバ変数にセット
            $this->productId = $sqlval['product_id'];
        // 新規登録
        } else {
            // 作成者
            $sqlval['creator_id'] = $_SESSION['member_id'];
            // 作成日時
            $sqlval['create_date'] = $sqlval['update_date'];
            // 商品ID生成
            $productId = $objQuery->nextVal('dtb_products_product_id');
            $sqlval['product_id'] = $productId;
            // 商品IDをメンバ変数にセット
            $this->productId = $productId;
            // INSERTの実行
            $objQuery->insert("dtb_products", $sqlval);
        }
    }

    /*
     * 商品規格情報登録を行う
     */
    function lfRegistProductsClass($line = "", &$objFormParam) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // 登録データ対象取得
        $arrList = $objFormParam->getHashArray();

        // 商品情報を生成する。
        // 商品情報テーブルのカラムに存在しているものだけ採用する。
        $sqlval =
            SC_Utils_Ex::sfArrayIntersectKeys
                ($arrList, $this->arrProductsClassCol);

        // 同じ商品CDがある場合は上書き、
        // なければ追加する。
        $where = "product_code = ? AND del_flg = 0 ";
        $product_count = $objQuery->count
            ("dtb_products_class", $where, array($sqlval['product_code']));

        // 商品ID
        $sqlval['product_id'] = $this->productId;
        // 更新者
        $sqlval['updator_id'] = $_SESSION['member_id'];

        // この時点で値が入ってないものは登録対象外
        foreach ($sqlval as $key => &$val) {
            if (strlen($val) == 0) {
                unset($sqlval[$key]);
            }
        }
        if ($product_count > 0) {
            // 更新
            $objQuery->update("dtb_products_class", $sqlval,
                              $where, array($sqlval['product_code']));
        // 新規登録
        } else {
            // 商品規格ID生成
            $sqlval["product_class_id"] = 
                $objQuery->nextVal
                ('dtb_products_class_product_class_id');
            // 作成者
            $sqlval['creator_id'] = $_SESSION['member_id'];
            // 作成日時
            $sqlval['create_date'] = $sqlval['update_date'];
            // INSERTの実行
            $objQuery->insert("dtb_products_class", $sqlval);
        }
    }

    /*
     * 初期値の設定
     */
    function lfSetParamDefaultValue(&$arrCsvFrame) {
        foreach($arrCsvFrame as $key => $val) {
            switch($val['col']) {
                case 'del_flg':
                    $arrCsvFrame[$key]['default'] = '0';
                    break;
                default:
                    break;
            }
        }
        return $arrCsvFrame;
    }

    /*
     * このフォーム特有の複雑な入力チェックを行う
     */
    function lfCheckErrorDetail($item, $arrErr) {
        return $arrErr;
    }
}
