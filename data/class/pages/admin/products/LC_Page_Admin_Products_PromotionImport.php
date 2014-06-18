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
	/*
        parent::init();
        $this->tpl_mainpage = 'products/promotion_import.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'promotion_import';
        $this->tpl_maintitle = 'プロモーションマスタ管理';
        $this->tpl_subtitle = 'プロモーションマスタ登録CSV';
	 */
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
        //$this->sendResponse();
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
            new SC_UploadFile_Ex(CSV_TEMP_REALDIR, CSV_SAVE_REALDIR);
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
		(INOS_DATA_TYPE_RECV_PROMOTION, $count, $r);
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
        $this->arrRowResult[] = "プロモーション取込結果：". $line_max
                              . "件の取込が完了しました。";
    }
    /**
     * CSVアップロードを実行します.
     *
     * @return void
     */
    function doUploadCsv(&$objFormParam, &$objUpFile) {
	// ファイル情報取得
        $arrFile = SC_Utils_Ex::sfGetDirFile(INOS_DIR_RECV_PROMOTION);
	if (!$arrFile[0]) {
	    $this->arrRowErr[] = "プロモーションデータの取込ファイルがセットされておりません";
	    return;
	}

	/*
        // ファイルアップロードのチェック
        $objUpFile->makeTempFile('csv_file');
        $arrErr = $objUpFile->checkExists();
        if (count($arrErr) > 0) {
            $this->arrErr = $arrErr;
            return;
        }
        // 一時ファイル名の取得
        $filepath = $objUpFile->getTempFilePath('csv_file');
	 */

	$objQuery =& SC_Query_Ex::getSingletonInstance();
	$objQuery->begin();

	$all_count = 0;
	// 取込ファイル数処理を行う
	for ($i = 0; $i < count($arrFile); $i++) {
	    // 取込ファイルパス
	    $filepath = INOS_DIR_RECV_PROMOTION . $arrFile[$i];

	    // CSVファイルの文字コード変換
	    $enc_filepath =
		SC_Utils_Ex::sfEncodeFile
		    ($filepath, CHAR_CODE, CSV_SAVE_REALDIR, 'cp932');

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

		// プロモーションコード取得
		$this->setPromotionData;

		// プロモーションマスタ登録
		$this->lfRegistPromotion($line_count, $objFormParam, $arrKey);
		// プロモーションイベントマスタ登録
		$this->lfRegistPromotionMedia($line_count, $objFormParam, $arrKey);
		// プロモーション受注区分マスタ登録
		//$this->lfRegistOrderKbn($line_count, $objFormParam, $arrKey);
		// プロモーション購入商品マスタ登録
		$this->lfRegistOrderProduct
		    ($line_count, $objFormParam, $arrKey);
		// プロモーション値引商品マスタ登録
		$this->lfRegistDiscountProduct
		    ($line_count, $objFormParam, $arrKey);
		// プロモーション同梱商品マスタ登録
		$this->lfRegistIncludeProduct
		    ($line_count, $objFormParam, $arrKey);
		// 広告媒体情報登録
		$this->lfRegistMedia($line_count, $objFormParam, $arrKey);

		// 企画情報登録
		$this->lfRegistPlanning($line_count, $objFormParam, $arrKey);

		// ログ出力
		if (($line_count % 100) == 0) {
		    GC_Utils_Ex::gfPrintLog("[" . $arrFile[$i] . "]" . $line_count. "件の登録が完了しました。");
		}
	    }
	    $msg = sprintf("[%s]%d", $arrFile[$i], ($line_count - 1));
	    // 取込完了のメッセージを表示
	    //$this->addRowCompleteMsg($msg);
	    $all_count += ($line_count - 1);
	    fclose($fp);
        }
        // 取込完了のメッセージを表示
        //$this->addRowCompleteMsg("合計：" . $all_count);
        $this->addRowCompleteMsg($all_count);

        // 実行結果画面を表示
        //$this->tpl_mainpage = 'products/promotion_import_complete.tpl';


        if ($errFlag) {
            $objQuery->rollback();
	    // 取込ファイルを移動
	    SC_Utils_Ex::sfImportFileMove(INOS_DIR_RECV_PROMOTION, $arrFile
					, INOS_NG_DIR);
            return array(INOS_ERROR_FLG_EXIST_ERROR, 0);
        }

        $objQuery->commit();

	// 取込ファイルを移動
	SC_Utils_Ex::sfImportFileMove(INOS_DIR_RECV_PROMOTION, $arrFile
				    , INOS_OK_DIR);

        return array(INOS_ERROR_FLG_EXIST_NORMAL, $all_count);
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
     * @param array $arrKey プロモーションKEY情報
     * @return void
     */
    function lfRegistPromotion($line = "", &$objFormParam, &$arrKey) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // 登録データ対象取得
        $arrList = $objFormParam->getHashArray();

	$arrKey = array();

        // プロモーションマスタ登録情報を生成する。
        // プロモーションマスタテーブルのカラムに存在しているものだけ採用する。
        $sqlval =
            SC_Utils_Ex::sfArrayIntersectKeys($arrList, $this->arrPromotionCol);

	$discountFlg = false;
	$includeFlg = false;
	// 値引きか同梱品チェック
	if ($arrList["discount_products"]) {
	    $arrProducts = explode(",", $arrList["discount_products"]);
	    for ($i = 0; $i < count($arrProducts); $i++) {
		$arrRate = explode("-", $arrProducts[$i]);
		// 値引き有り
		if ($arrRate[1] > 0) {
		    $discountFlg = true;
		    break;
		}
	    }
	    //$sqlval["promotion_kbn"] = PROMOTION_KBN_DISCOUNT;
	}
	if ($arrList["include_products"]) {
	    // 同梱品有り
	    $includeFlg = true;
	    //$sqlval["promotion_kbn"] = PROMOTION_KBN_INCLUDE;
	}
	// 有効区分
	$sqlval["valid_kbn"] = PROMOTION_VALID_KBN_ON;
	// 数量集計区分
	$sqlval["quantity_kbn"] = PROMOTION_QUANTITY_KBN_DETAIL;
	// コース区分
	$sqlval["course_kbn"] = PROMOTION_COURSE_KBN_ALL;
	// 送料区分
	$sqlval["deliv_fee_kbn"] = PROMOTION_DELIV_FEE_KBN_NO_FREE;
        // 属性情報
        $sqlval['updator_id']  = $_SESSION['member_id'];
        $sqlval['update_date'] = 'NOW()';

	// プロモーションコード取得
	$sql =<<<EOF
SELECT 
    M.promotion_cd,
    P.promotion_kbn
FROM 
    dtb_promotion P
INNER JOIN dtb_promotion_media M
    ON P.promotion_cd = M.promotion_cd
WHERE M.media_code = ?
EOF;
	$arrChk = $objQuery->getAll($sql, array($arrList["media_code"]));
	if (is_array($arrChk)) {
	    for ($i = 0; $i < count($arrChk); $i++) {
		$arrKey[$arrChk[$i]["promotion_kbn"]] = $arrChk[$i]["promotion_cd"];
	    }
	}

        // 同じプロモーションコードがある場合は上書き、
        // なければ追加する。
	// 値引き
	if ($discountFlg) {
	    if (isset($arrKey[PROMOTION_KBN_DISCOUNT])) {
		$where = "promotion_cd = ?";
		// 更新
		$objQuery->update("dtb_promotion", $sqlval,
				  $where, array($arrKey[PROMOTION_KBN_DISCOUNT]));
	    } else {
		$promotion_cd = $objQuery->nextVal("dtb_promotion_promotion_cd");
		$arrKey[PROMOTION_KBN_DISCOUNT] = $promotion_cd;
		$sqlval['promotion_cd']  = $promotion_cd;
		$sqlval['promotion_kbn'] = PROMOTION_KBN_DISCOUNT;

		// 追加
		$sqlval['creator_id']  = $_SESSION['member_id'];
		$sqlval['create_date'] = 'NOW()';

		// INSERTの実行
		$objQuery->insert("dtb_promotion", $sqlval);

		unset($sqlval['creator_id']);
		unset($sqlval['create_date']);

	    }
	} else {
	    if (isset($arrKey[PROMOTION_KBN_DISCOUNT])) {
		// 不要情報のため削除
		$this->allPromotionDelete($arrKey[PROMOTION_KBN_INCLUDE]);
		unset($arrKey[PROMOTION_KBN_INCLUDE]);
	    }
	}

	// 同梱品
	if ($includeFlg) {
	    if (isset($arrKey[PROMOTION_KBN_INCLUDE])) {
		$where = "promotion_cd = ?";
		// 更新
		$objQuery->update("dtb_promotion", $sqlval,
				  $where, array($arrKey[PROMOTION_KBN_INCLUDE]));
	    } else {
		$promotion_cd = $objQuery->nextVal("dtb_promotion_promotion_cd");
		$arrKey[PROMOTION_KBN_INCLUDE] = $promotion_cd;
		$sqlval['promotion_cd']  = $promotion_cd;
		$sqlval['promotion_kbn'] = PROMOTION_KBN_INCLUDE;

		// 追加
		$sqlval['creator_id']  = $_SESSION['member_id'];
		$sqlval['create_date'] = 'NOW()';

		// INSERTの実行
		$objQuery->insert("dtb_promotion", $sqlval);

		unset($sqlval['creator_id']);
		unset($sqlval['create_date']);

	    }
	} else {
	    if (isset($arrKey[PROMOTION_KBN_INCLUDE])) {
		// 不要情報のため削除
		$this->allPromotionDelete($arrKey[PROMOTION_KBN_INCLUDE]);
		unset($arrKey[PROMOTION_KBN_INCLUDE]);
	    }
	}

    }

    /**
     * プロモーション関連テーブルの削除を行う.
     *
     * @param string|integer $promotionCd プロモーションコード
     * @return void
     */
    function allPromotionDelete($promotionCd) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

	$where = "promotion_cd = ?";
	// プロモーションマスタ削除
	$objQuery->delete("dtb_promotion", $where, array($promotionCd));

	// プロモーションイベントマスタ削除
	$objQuery->delete("dtb_promotion_media", $where, array($promotionCd));

	// プロモーション受注区分マスタ削除
	$objQuery->delete("dtb_promotion_order_kbn", $where, array($promotionCd));

	// プロモーション購入商品マスタ削除
	$objQuery->delete("dtb_promotion_order_product", $where, array($promotionCd));

	// プロモーション割引商品マスタ削除
	$objQuery->delete("dtb_promotion_discount_product", $where, array($promotionCd));

	// プロモーション同梱商品マスタ削除
	$objQuery->delete("dtb_promotion_include_product", $where, array($promotionCd));

    }

    /**
     * プロモーションイベントマスタ登録を行う.
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param string|integer $line 処理中の行数
     * @param array $arrKey プロモーションKEY情報
     * @return void
     */
    function lfRegistPromotionMedia($line = "", &$objFormParam, $arrKey) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // 登録データ対象取得
        $arrList = $objFormParam->getHashArray();

        // イベントCDがない場合はここまで
        if (empty($arrList['media_code'])) {
            return;
        }

	$table = "dtb_promotion_media";
	$where = "promotion_cd = ?";
	// プロモーションコード分登録する
	foreach ($arrKey as $key => $val) {
	    $sqlval = array();
	    // 登録確認
	    $chkCnt = $objQuery->count($table, $where, array($val));

	    // 属性情報
	    $sqlval['updator_id']  = $_SESSION['member_id'];
	    $sqlval['update_date'] = 'NOW()';
	    $sqlval['del_flg']  = $arrList['del_flg'];
	    if ($chkCnt > 0) {
		// 更新
		$objQuery->update($table, $sqlval, $where, array($val));
	    } else {
		// 追加
		// プロモーションコード
		$sqlval['promotion_cd'] = $val;
		// 広告媒体コード
		$sqlval['media_code'] = $arrList["media_code"];

		// 属性情報
		$sqlval['creator_id']  = $_SESSION['member_id'];
		$sqlval['create_date'] = 'NOW()';
		// INSERTの実行
		$objQuery->insert($table, $sqlval);
	    }
	}
    }

    /**
     * プロモーション受注区分マスタ登録を行う.
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param string|integer $line 処理中の行数
     * @return void
     */
    /* データが渡ってこないためコメントアウト
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
     */

    /**
     * プロモーション購入商品マスタ登録を行う.
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param string|integer $line 処理中の行数
     * @param array $arrKey プロモーションKEY情報
     * @return void
     */
    function lfRegistOrderProduct($line = "", &$objFormParam, $arrKey) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // 登録データ対象取得
        $arrList = $objFormParam->getHashArray();

	if (!empty($arrList['discount_products'])) {
	    $arrProducts = explode(',', $arrList['discount_products']);
	}

	$table = "dtb_promotion_order_product";
	$where = "promotion_cd = ?";
	foreach ($arrKey as $key => $val) {
	    $objQuery->delete($table, $where, array($val));
	    if (empty($arrList['discount_products'])) {
		continue;
	    }

	    // プロモーションコード
	    $sqlval['promotion_cd'] = $val;

	    for ($i = 0; $i < count($arrProducts); $i++) {
		// 購入商品CD
		$arrChk = explode("-", $arrProducts[$i]);
		$sqlval['product_cd'] = $arrChk[0];

		// 属性情報
		$sqlval['updator_id']  = $_SESSION['member_id'];
		$sqlval['update_date'] = 'NOW()';
		$sqlval['creator_id']  = $_SESSION['member_id'];
		$sqlval['create_date'] = 'NOW()';
		$sqlval['del_flg'] = $arrList['del_flg'];
		// INSERTの実行
		$objQuery->insert($table, $sqlval);
	    }
	}
    }

    /**
     * プロモーション値引商品マスタ登録を行う.
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param string|integer $line 処理中の行数
     * @param array $arrKey プロモーションKEY情報
     * @return void
     */
    function lfRegistDiscountProduct($line = "", &$objFormParam, $arrKey) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // 登録データ対象取得
        $arrList = $objFormParam->getHashArray();

	if (!isset($arrKey[PROMOTION_KBN_DISCOUNT])) {
	    // 値引商品CDがない場合はここまで
	    return;
	}

	$table = "dtb_promotion_discount_product";
	$where = "promotion_cd = ?";
	// プロモーション値引き商品マスタ削除
	$objQuery->delete($table, $where, array($arrKey[PROMOTION_KBN_DISCOUNT]));

        // プロモーションコード
        $sqlval['promotion_cd'] = $arrKey[PROMOTION_KBN_DISCOUNT];
        $sqlval['del_flg'] = $arrList['del_flg'];

        // 値引商品CDはカンマ区切りで複数登録されているため、
        // ループして登録処理を行う
        $discount_products = explode(',', $arrList['discount_products']);
	$length = count($discount_products);

        for ($i = 0; $i < count($discount_products); $i++) {
            // 値引商品CD-割引率-税込単価 形式になっているので、さらに分解
            $discount_product = explode('-', $discount_products[$i]);
            // 商品コード
            $sqlval['product_cd'] = $discount_product[0];
            // 割引率
            $sqlval['cut_rate'] = $discount_product[1];
            // 販売価格
            $sqlval['sales_price'] = $discount_product[2];

            // 属性情報
            $sqlval['updator_id']  = $_SESSION['member_id'];
            $sqlval['update_date'] = 'NOW()';
	    $sqlval['creator_id']  = $_SESSION['member_id'];
	    $sqlval['create_date'] = 'NOW()';
	    // INSERTの実行
	    $objQuery->insert($table, $sqlval);
        }
    }

    /**
     * プロモーション同梱商品マスタ登録を行う.
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param string|integer $line 処理中の行数
     * @param array $arrKey プロモーションKEY情報
     * @return void
     */
    function lfRegistIncludeProduct($line = "", &$objFormParam, $arrKey) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // 登録データ対象取得
        $arrList = $objFormParam->getHashArray();

	if (!isset($arrKey[PROMOTION_KBN_INCLUDE])) {
	    // 同梱商品CDがない場合はここまで
	    return;
	}

	$table = "dtb_promotion_include_product";
	$where = "promotion_cd = ?";
	// プロモーション同梱商品マスタ削除
	$objQuery->delete($table, $where, array($arrKey[PROMOTION_KBN_INCLUDE]));

        // プロモーションコード
        $sqlval['promotion_cd'] = $arrKey[PROMOTION_KBN_INCLUDE];
        $sqlval['del_flg'] = $arrList['del_flg'];

        // 同梱商品CDはカンマ区切りで複数登録されているため、
        // ループして登録処理を行う
        $include_products = explode(',', $arrList['include_products']);

        for ($i = 0; $i < count($include_products); $i++) {
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
	    $objQuery->insert($table, $sqlval);
	}
    }

    /**
     * 広告媒体情報登録を行う.
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param string|integer $line 処理中の行数
     * @param array $arrKey プロモーションKEY情報
     * @return void
     */
    function lfRegistMedia($line = "", &$objFormParam, $arrKey) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // 登録データ対象取得
        $arrList = $objFormParam->getHashArray();

        // イベントCDがない場合はここまで
        if (empty($arrList['media_code'])) {
            return;
        }

	$table = "dtb_media";
	$where = "media_code = ?";

	// 登録確認
	$chkCnt = $objQuery->count($table, $where, array($arrList['media_code']));

	$sqlval = array();
	// 媒体名
	$sqlval['media_name']  = $arrList['promotion_name'];
	// 属性情報
	$sqlval['update_id']  = $_SESSION['member_id'];
	$sqlval['update_date'] = 'NOW()';
	$sqlval['del_flg']  = $arrList['del_flg'];
	if ($chkCnt > 0) {
	    // 更新
	    $objQuery->update($table, $sqlval, $where, array($arrList['media_code']));
	} else {
	    // 追加
	    $media_id = $objQuery->nextVal("dtb_media_media_id");
	    // 広告ID
	    $sqlval['media_id'] = $media_id;
	    // 広告媒体コード
	    $sqlval['media_code'] = $arrList["media_code"];

	    // 属性情報
	    $sqlval['creator_id']  = $_SESSION['member_id'];
	    $sqlval['create_date'] = 'NOW()';
	    // INSERTの実行
	    $objQuery->insert($table, $sqlval);
	}
    }

    /**
     * 企画情報登録を行う.
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param string|integer $line 処理中の行数
     * @param array $arrKey プロモーションKEY情報
     * @return void
     */
    function lfRegistPlanning($line = "", &$objFormParam, $arrKey) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // 登録データ対象取得
        $arrList = $objFormParam->getHashArray();

        // イベントCDがない場合はここまで
        if (empty($arrList['media_code'])) {
            return;
        }

	$table = "dtb_planning";
	$where = "media_code = ?";

	// 登録確認
	$chkCnt = $objQuery->count($table, $where, array($arrList['media_code']));

	$sqlval = array();
	// 企画名
	$sqlval['planning_name']  = $arrList['promotion_name'];
	// 企画タイプ
	$sqlval['planning_type']  = PLANNING_TYPE_CAMPAIGN;
	// 広告媒体コード
	$sqlval['media_code']  = $arrList['media_code'];
	// キャンペーンコード
	$sqlval['campaign_code']  = $arrList['campaign_code'];
	// 開始日
	$sqlval['start_date']  = $arrList['valid_from'];
	// 属性情報
	$sqlval['update_id']  = $_SESSION['member_id'];
	$sqlval['update_date'] = 'NOW()';
	$sqlval['del_flg']  = $arrList['del_flg'];
	if ($chkCnt > 0) {
	    // 更新
	    $objQuery->update($table, $sqlval, $where, array($arrList['media_code']));
	} else {
	    // 追加
	    $planning_id = $objQuery->nextVal("dtb_planning_planning_id");
	    // 広告ID
	    $sqlval['planning_id'] = $planning_id;

	    // 属性情報
	    $sqlval['creator_id']  = $_SESSION['member_id'];
	    $sqlval['create_date'] = 'NOW()';
	    // INSERTの実行
	    $objQuery->insert($table, $sqlval);
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

    /**
     * 結果情報を返す
     *
     * @param $arrErr  エラー内容
     * @param $arrRes  結果内容
     * @return void
     */
    function getResPromotionImport(&$arrErr, &$arrRes) {

	// エラー内容
	if (is_array($arrErr) && is_array($this->arrRowErr)) {
	    $arrErr = array_merge($arrErr, $this->arrRowErr);
	} else if (is_array($this->arrRowErr)) {
	    $arrErr = $this->arrRowErr;
	}
	// 結果内容
	if (is_array($arrRes) && is_array($this->arrRowResult)) {
	    $arrRes = array_merge($arrRes, $this->arrRowResult);
	} else if (is_array($this->arrRowResult)) {
	    $arrRes = $this->arrRowResult;
	}
    }

}
