<?php
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';
require_once CLASS_EX_REALDIR . 'helper_extends/SC_Helper_CSV_Ex.php';

/**
 * プロモーションマスタ登録CSVのページクラス.
 *
 * @package Page
 * @author IQUEVE Co.,Ltd.
 * @version $Id:LC_Page_Admin_Products_UploadCSV.php 15532 2007-08-31 14:39:46Z nanasess $
 *
 */
class LC_Page_Admin_Products_PromotionImport extends LC_Page_Admin_Ex
{
    /** プロモーションマスタ群 カラム情報 (登録処理用) **/
    var $arrPromotionCol;
    var $arrMediaCol;               // プロモーションイベント
    var $arrOrderKbnCol;            // プロモーション受注区分
    var $arrOrderProductCol;        // プロモーション購入商品
    var $arrDiscountProductCol;     // プロモーション値引商品
    var $arrIncludeProductCol;      // プロモーション同梱商品

    /** 登録フォームカラム情報 **/
    var $arrFormKeyList;

    var $arrRowErr;

    var $arrRowResult;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/promotion_import.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'promotion_import';
        $this->tpl_maintitle = 'プロモーションマスタ管理';
        $this->tpl_subtitle = 'プロモーションマスタ登録CSV';
        $this->csv_id = '6';

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
        $this->objDb = new SC_Helper_DB_Ex();

        // CSV管理ヘルパー
        $objCSV = new SC_Helper_CSV_Ex();
        // CSV構造読み込み
        $arrCSVFrame = $objCSV->sfGetCsvOutput($this->csv_id);

        // CSV構造がインポート可能かのチェック
        if (!$objCSV->sfIsImportCSVFrame($arrCSVFrame)) {
            // 無効なフォーマットなので初期状態に強制変更
            $arrCSVFrame =
                $objCSV->sfGetCsvOutput($this->csv_id, '', array(), 'no');
            $this->tpl_is_format_default = true;
        }
        // CSV構造は更新可能なフォーマットかのフラグ取得
        $this->tpl_is_update = $objCSV->sfIsUpdateCSVFrame($arrCSVFrame);

        // CSVファイルアップロード情報の初期化
        $objUpFile =
            new SC_UploadFile_Ex(IMAGE_TEMP_REALDIR, IMAGE_SAVE_REALDIR);
        $this->lfInitFile($objUpFile);

        // パラメーター情報の初期化
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam, $arrCSVFrame);

        $objFormParam->setHtmlDispNameArray();
        $this->arrTitle = $objFormParam->getHtmlDispNameArray();

        switch ($this->getMode()) {
        case 'csv_upload':
            list($r, $count) = $this->doUploadCsv
		($objFormParam, $objUpFile);
	    SC_Helper_DB_Ex::sfInsertBatchHistory
		(INOS_DATA_TYPE_REVC_PROMOTION, $count, $r);
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
            return;
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

            // プロモーションマスタ登録
            $this->lfRegistPromotion($line_count, $objFormParam);
            // プロモーションイベントマスタ登録
            $this->lfRegistMedia($line_count, $objFormParam);
            // プロモーション受注区分マスタ登録
            $this->lfRegistOrderKbn($line_count, $objFormParam);
            // プロモーション購入商品マスタ登録
            $this->lfRegistOrderProduct
                ($line_count, $objFormParam);
            // プロモーション値引商品マスタ登録
            $this->lfRegistDiscountProduct
                ($line_count, $objFormParam);
            // プロモーション同梱商品マスタ登録
            $this->lfRegistIncludeProduct
                ($line_count, $objFormParam);
            // ログ出力
            if (($line_count % 100) == 0) {
                GC_Utils_Ex::gfPrintLog($line_count. "件の登録が完了しました。");
            }
        }
        // 取込完了のメッセージを表示
        $this->addRowCompleteMsg($line_count - 1);

        // 実行結果画面を表示
        $this->tpl_mainpage = 'products/promotion_import_complete.tpl';

        fclose($fp);

        if ($errFlag) {
            $objQuery->rollback();
            return array(INOS_ERROR_FLG_EXIST_ERROR, 0);
        }

        $objQuery->commit();

        return array(INOS_ERROR_FLG_EXIST_NORMAL, $line_count - 1);
    }

    /**
     * ファイル情報の初期化を行う.
     *
     * @return void
     */
    function lfInitFile(&$objUpFile) {
        $objUpFile->addFile("CSVファイル", 'csv_file',
                            array('csv'), CSV_SIZE, true, 0, 0, false);
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
        foreach ($arrCSVFrame as $item) {
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
                $error_check_types = str_replace('HTML_TAG_CHECK', '', $item['error_check_types']);
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

        $this->arrPromotionCol = $objQuery->listTableFields('dtb_promotion');
        $this->arrMediaCol = $objQuery->listTableFields('dtb_promotion_media');
        $this->arrOrderKbnCol =
            $objQuery->listTableFields('dtb_promotion_order_kbn');
        $this->arrOrderProductCol =
            $objQuery->listTableFields('dtb_promotion_order_product');
        $this->arrDiscountProductCol =
            $objQuery->listTableFields('dtb_promotion_discount_product');
        $this->arrIncludeProductCol =
            $objQuery->listTableFields('dtb_promotion_include_product');
    }

    /**
     * プロモーションマスタ登録を行う.
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param string|integer $line 処理中の行数
     * @return void
     */
    function lfRegistPromotion($line = "", &$objFormParam) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // 登録データ対象取得
        $arrList = $objFormParam->getHashArray();

        // プロモーションマスタ登録情報を生成する。
        // プロモーションマスタテーブルのカラムに存在しているものだけ採用する。
        $sqlval =
            SC_Utils_Ex::sfArrayIntersectKeys($arrList, $this->arrPromotionCol);

        // 同じプロモーションコードがある場合は上書き、
        // なければ追加する。
        $where = "promotion_cd = ?";
        $promotion_count = $objQuery->count
            ("dtb_promotion", $where, array($sqlval['promotion_cd']));

        // 属性情報
        $sqlval['updator_id']  = $_SESSION['member_id'];
        $sqlval['update_date'] = 'NOW()';

        if ($promotion_count > 0) {
            // 更新
            $objQuery->update("dtb_promotion", $sqlval,
                              $where, array($sqlval['promotion_cd']));
        } else {
            // 追加
            $sqlval['creator_id']  = $_SESSION['member_id'];
            $sqlval['create_date'] = 'NOW()';
            // INSERTの実行
            $objQuery->insert("dtb_promotion", $sqlval);
        }
    }

    function deleteMedia($strCd) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
	$sql =<<<__EOS
delete from dtb_promotion_media where promotion_cd = "$strCd"
__EOS;

	$objQuery->exec($sql);
    }

    /**
     * プロモーションイベントマスタ登録を行う.
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param string|integer $line 処理中の行数
     * @return void
     */
    function lfRegistMedia($line = "", &$objFormParam) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // 登録データ対象取得
        $arrList = $objFormParam->getHashArray();

	// プロモーションイベントマスタ削除
	$this->deleteMedia($arrList['promotion_cd']);

        // イベントCDがない場合はここまで
        if (empty($arrList['media_cds'])) {
            return;
        }

        // プロモーションコード
        $sqlval['promotion_cd'] = $arrList['promotion_cd'];

        // 広告媒体コードはカンマ区切りで複数登録されているため、
        // ループして登録処理を行う
        $media_cds = explode(',', $arrList['media_cds']);
	$length = count($media_cds);

        for ($i = 0; $i < $length; ++$i) {
            // 広告媒体コード
            $sqlval['media_code'] = $media_cds[$i];

            // 属性情報
            $sqlval['updator_id']  = $_SESSION['member_id'];
            $sqlval['update_date'] = 'NOW()';
	    $sqlval['creator_id']  = $_SESSION['member_id'];
	    $sqlval['create_date'] = 'NOW()';
	    // INSERTの実行
	    $objQuery->insert("dtb_promotion_media", $sqlval);
        }
    }

    function deleteOrderKbn($strCd) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
	$sql =<<<__EOS
delete from dtb_promotion_order_kbn where promotion_cd = "$strCd"
__EOS;

	$objQuery->exec($sql);
    }

    /**
     * プロモーション受注区分マスタ登録を行う.
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param string|integer $line 処理中の行数
     * @return void
     */
    function lfRegistOrderKbn($line = "", &$objFormParam) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // 登録データ対象取得
        $arrList = $objFormParam->getHashArray();

	// プロモーション受注区分削除
	$this->deleteOrderKbn($arrList['promotion_cd']);

        // 受注区分がない場合はここまで
        if (empty($arrList['order_kbns'])) {
            return;
        }

        // プロモーションコード
        $sqlval['promotion_cd'] = $arrList['promotion_cd'];

        // 受注区分はカンマ区切りで複数登録されているため、
        // ループして登録処理を行う
        $order_kbns = explode(',', $arrList['order_kbns']);
	$length = count($order_kbns);

        for ($i = 0; $i < $length; ++$i) {
            // 受注区分
            $sqlval['order_kbn'] = $order_kbns[$i];

            // 属性情報
            $sqlval['updator_id']  = $_SESSION['member_id'];
            $sqlval['update_date'] = 'NOW()';
	    $sqlval['creator_id']  = $_SESSION['member_id'];
	    $sqlval['create_date'] = 'NOW()';
	    // INSERTの実行
	    $objQuery->insert("dtb_promotion_order_kbn", $sqlval);
        }
    }

    function deleteOrderProduct($strCd) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
	$sql =<<<__EOS
delete from dtb_promotion_order_product where promotion_cd = "$strCd"
__EOS;

	$objQuery->exec($sql);
    }

    /**
     * プロモーション購入商品マスタ登録を行う.
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param string|integer $line 処理中の行数
     * @return void
     */
    function lfRegistOrderProduct($line = "", &$objFormParam) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // 登録データ対象取得
        $arrList = $objFormParam->getHashArray();

	// プロモーション購入商品マスタ削除
	$this->deleteOrderProduct($arrList['promotion_cd']);

        // 購入商品CDがない場合はここまで
        if (empty($arrList['order_products'])) {
            return;
        }

        // プロモーションコード
        $sqlval['promotion_cd'] = $arrList['promotion_cd'];

        // 購入商品コードはカンマ区切りで複数登録されているため、
        // ループして登録処理を行う
        $order_products = explode(',', $arrList['order_products']);
	$length = count($order_products);

        for ($i = 0; $i < $length; ++$i) {
            // 購入商品CD
            $sqlval['product_cd'] = $order_products[$i];

            // 属性情報
            $sqlval['updator_id']  = $_SESSION['member_id'];
            $sqlval['update_date'] = 'NOW()';
	    $sqlval['creator_id']  = $_SESSION['member_id'];
	    $sqlval['create_date'] = 'NOW()';
	    // INSERTの実行
	    $objQuery->insert("dtb_promotion_order_product", $sqlval);
        }
    }

    function deleteDiscountProduct($strCd) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
	$sql =<<<__EOS
delete from dtb_promotion_discount_product where promotion_cd = "$strCd"
__EOS;

	$objQuery->exec($sql);
    }

    /**
     * プロモーション値引商品マスタ登録を行う.
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param string|integer $line 処理中の行数
     * @return void
     */
    function lfRegistDiscountProduct($line = "", &$objFormParam) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // 登録データ対象取得
        $arrList = $objFormParam->getHashArray();

	// プロモーション値引き商品マスタ削除
	$this->deleteDiscountProduct($arrList['promotion_cd']);

        // 値引商品CDがない場合はここまで
        if (empty($arrList['discount_products'])) {
            return;
        }

        // プロモーションコード
        $sqlval['promotion_cd'] = $arrList['promotion_cd'];

        // 値引商品CDはカンマ区切りで複数登録されているため、
        // ループして登録処理を行う
        $discount_products = explode(',', $arrList['discount_products']);
	$length = count($discount_products);

        for ($i = 0; $i < $length; ++$i) {
            // 値引商品CD-税込単価 形式になっているので、さらに分解
            $discount_product = explode('-', $discount_products[$i]);
            // 商品コード
            $sqlval['product_cd'] = $discount_product[0];
            // 販売価格
            $sqlval['sales_price'] = $discount_product[1];

            // 属性情報
            $sqlval['updator_id']  = $_SESSION['member_id'];
            $sqlval['update_date'] = 'NOW()';
	    $sqlval['creator_id']  = $_SESSION['member_id'];
	    $sqlval['create_date'] = 'NOW()';
	    // INSERTの実行
	    $objQuery->insert("dtb_promotion_discount_product", $sqlval);
        }
    }

    function deleteIncludeProduct($strCd) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
	$sql =<<<__EOS
delete from dtb_promotion_include_product where promotion_cd = "$strCd"
__EOS;

	$objQuery->exec($sql);
    }

    /**
     * プロモーション同梱商品マスタ登録を行う.
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param string|integer $line 処理中の行数
     * @return void
     */
    function lfRegistIncludeProduct($line = "", &$objFormParam) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // 登録データ対象取得
        $arrList = $objFormParam->getHashArray();

	// プロモーション同梱商品マスタ削除
	$this->deleteIncludeProduct($arrList['promotion_cd']);

        // 同梱商品CDがない場合はここまで
        if (empty($arrList['include_products'])) {
            return;
        }

        // プロモーションコード
        $sqlval['promotion_cd'] = $arrList['promotion_cd'];

        // 同梱商品CDはカンマ区切りで複数登録されているため、
        // ループして登録処理を行う
        $include_products = explode(',', $arrList['include_products']);
	$length = count($include_products);

        for ($i = 0; $i < $length; ++$i) {
            // 同梱商品CD-数量 形式になっているので、さらに分解
            $include_product = explode('-', $include_products[$i]);
            // 商品コード
            $sqlval['product_cd'] = $include_product[0];
            // 個数
            $sqlval['quantity'] = $include_product[1];

            // 属性情報
            $sqlval['updator_id']  = $_SESSION['member_id'];
            $sqlval['update_date'] = 'NOW()';
	    $sqlval['creator_id']  = $_SESSION['member_id'];
	    $sqlval['create_date'] = 'NOW()';

	    // INSERTの実行
	    $objQuery->insert("dtb_promotion_include_product", $sqlval);
	}
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
                case 'del_flg':
                    $arrCSVFrame[$key]['default'] = '0';
                    break;
                default:
                    break;
            }
        }
        return $arrCSVFrame;
    }

    /**
     * このフォーム特有の複雑な入力チェックを行う.
     *
     * @param array 確認対象データ
     * @param array エラー配列
     * @return array エラー配列
     */
    function lfCheckErrorDetail($item, $arrErr) {
        return $arrErr;
    }
}
