<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';
// }}}

/**
 * ページクラス(拡張).
 *
 * (イノス)通販システムの出荷実績をインポートする
 *
 * @package Page
 * @author IQUEVE CO.,LTD.
 * @version $Id: LC_Page_Admin_InosImport_Ex.php 338 2012-06-01 07:28:57Z nao $
 */
class LC_Page_Admin_InosImportShukka_Ex extends LC_Page_Admin_Ex
{
    // {{{ properties

    /** 専用エラーチェックフィールド情報 */
    var $arrCustomCheckItem;

    /** エラーメッセージ **/
    var $arrRowErr;

    /** 正常メッセージ **/
    var $arrRowResult;

    /** 送信フラグ(0:未送信、1:送信済み */
    const SEND_FLAG_OFF = '0';
    const SEND_FLAG_ON = '1';

    /** 配送メールテンプレートID */
    const DELEVERY_MAIL_TEMPLATE = '6';

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
        $this->tpl_mainpage = 'order/inos_import_shukka.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'inos_import_shukka';
        $this->tpl_maintitle = '受注関連';
        $this->tpl_subtitle = '通販システム出荷実績インポート';

        $this->arrCustomCheckItem = array();
        $this->arrRowErr = array();
        $this->arrRowResult = array();

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
                "csv_id"                 => 99992,
                "col"                    => "order_id",
                "disp_name"              => "受注ID",
                "rank"                   => 1,
                "status"                 => 1,
                "rw_flg"                 => 1,
                "mb_convert_kana_option" => "n",
                "size_const_type"        => 11,
                "error_check_types"      => "EXIST_CHECK,MAX_LENGTH_CHECK",
            ),
            array(
                "no"                     => 2,
                "csv_id"                 => 99992,
                "col"                    => "customer_id",
                "disp_name"              => "顧客ID",
                "rank"                   => 2,
                "status"                 => 1,
                "rw_flg"                 => 1,
                "mb_convert_kana_option" => "n",
                "size_const_type"        => "INT_LEN",
                "error_check_types"      => "EXIST_CHECK,NUM_CHECK,MAX_LENGTH_CHECK",
            ),
            array(
                "no"                     => 3,
                "csv_id"                 => 99992,
                "col"                    => "query_number",
                "disp_name"              => "お問合せ番号",
                "rank"                   => 3,
                "status"                 => 1,
                "rw_flg"                 => 3,
                "mb_convert_kana_option" => "a",
                "size_const_type"        => "14",
                "error_check_types"      => "EXIST_CHECK,MAX_LENGTH_CHECK",
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
        switch($this->getMode()) {
        case 'send_mail':
			$this->lfInitParamSend($objFormParam);
			$objFormParam->setParam($_POST);
			break;
        default:
			$this->lfInitParam($objFormParam, $this->arrCSVFrame);
		}

        $objFormParam->setHtmlDispNameArray();
        $this->arrTitle = $objFormParam->getHtmlDispNameArray();

        switch($this->getMode()) {
			// CSVインポート
        case 'csv_upload':
            $this->doUploadCsv($objFormParam, $objUpFile);
            break;
			// 選択データメール送信
        case 'send_mail':
            $this->doSendMail($objFormParam);
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
     * @param stirng $key 受注ID,顧客ID
     * @return void
     */
    function addRowResult($line_count, $message, $key) {
		$fmt = '<input type="checkbox" name="shukka_jisseki[]" value="%s" checked="checked" />%s';
		$msg = $line_count . "行目：" . $message;

        $this->arrRowResult[] = sprintf($fmt, $key, $msg);
        //$this->arrRowResult[] = $line_count . "行目：" . $message;
    }

    /**
     * 出荷実績送信結果のメッセージをプロパティへ追加する
     *
     * @param stirng $message メッセージ
     * @return void
     */
    function addRowResultMail($message) {

        $this->arrRowResult[] = $message;
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
        //$all_line_checked = false;

        while (!feof($fp)) {
            $arrCSV = fgetcsv($fp, CSV_LINE_MAX);

            // 全行入力チェック後に、ファイルポインターを先頭に戻す
			/*
            if (feof($fp) && !$all_line_checked) {
                rewind($fp);
                $line_count = 0;
                $all_line_checked = true;
                continue;
            }
			 */

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
            $arrCSVErr = $this->lfCheckError($objFormParam, $errChk);

            // 入力エラーチェック
            if (count($arrCSVErr) > 0) {
                foreach ($arrCSVErr as $err) {
                    $this->addRowErr($line_count, $err);
                }
				if (!$errChk) {
					$errFlag = true;
					continue;
				}
                //break;
            }

            //if ($all_line_checked) {
				$this->lfRegistInosShipmentTemp($objQuery, $line_count,
												$objFormParam);
  
				$arrParam = $objFormParam->getHashArray();

				$this->addRowResult($line_count, sprintf
									("受注ID：%s / Web顧客ID：%s / お問合せ番号：%s",
									 $arrParam['order_id'],
									 $arrParam['customer_id'],
									 $arrParam['query_number']),
									sprintf("%s,%s,%s", 
									 $arrParam['order_id'],
									 $arrParam['customer_id'],
									 $arrParam['query_number'])
								 );
            //}
        }
        fclose($fp);

        // 実行結果画面を表示
        $this->tpl_mainpage = 'order/inos_import_shukka_confirm.tpl';

        if ($errFlag) {
            $objQuery->rollback();
            $this->arrRowResult = array();
            return;
        }

        $objQuery->commit();

        return;
    }

    /**
     * 出荷実績メール送信を実行します.
     *
     * @return void
     */
    function doSendMail(&$objFormParam) {

		$arrShukka = $objFormParam->getValue('shukka_jisseki');

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();

		$where = 'order_id = ? and customer_id = ?';

		for($i = 0; $i < count($arrShukka); $i++) {
			$arrKey = explode(",", $arrShukka[$i]);


			// 出荷実績更新
			$sqlval = array();
			$sqlval['update_date'] = $this->lfGetDbFormatTimeWithLine($i);
			$sqlval['send_flag'] = self::SEND_FLAG_ON;
			$wval = array();
			$wval[] = $arrKey[0]; // 受注ID
			$wval[] = $arrKey[1]; // 顧客ID

			$objQuery->update("dtb_shukka_jisseki", $sqlval, $where, $wval);

			// 受注情報更新
			$sqlval['status'] = ORDER_DELIV; // 発送済み
			unset($sqlval['send_flag']);
			$wval = array();
			$wval[] = $arrKey[0]; // 受注ID

			$objQuery->update("dtb_order", $sqlval, "order_id = ?", $wval);

			// 配送先情報更新
			$sqlval['shipping_num'] = $arrKey[2]; // 配送番号
			unset($sqlval['status']);
			$wval = array();
			$wval[] = $arrKey[0]; // 受注ID

			$objQuery->update("dtb_shipping", $sqlval, "order_id = ?", $wval);

			$this->addRowResultMail(sprintf("受注ID：%s / Web顧客ID：%s / お問合せ番号：%s",
										 $arrKey[0],
										 $arrKey[1],
										 $arrKey[2])
								 );

            $objMail = new SC_Helper_Mail_Ex();
			$objSendMail = $objMail->sfSendOrderMail($arrKey[0]
													, self::DELEVERY_MAIL_TEMPLATE);

		}

        $objQuery->commit();

        // 実行結果画面を表示
        $this->tpl_mainpage = 'order/inos_import_shukka_complete.tpl';

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
                'CHECK_ORDER',
                'CHECK_CUSTOMER',
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
     * パラメータ情報の初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitParamSend(&$objFormParam) {
        // POSTされる値
        $objFormParam->addParam("出荷実績", "shukka_jisseki", STEXT_LEN, 'a', array("MAX_LENGTH_CHECK"));
	}
    /**
     * 入力チェックを行う.
     *
     * @return void
     */
    function lfCheckError(&$objFormParam, &$errorFlg) {

        $errorFlg = false;

        // 入力データを渡す。
        $arrRet = $objFormParam->getHashArray();
        $objErr = new SC_CheckError_Ex($arrRet);
        $objErr->arrErr = $objFormParam->checkError(false);

        $objQuery = SC_Query_Ex::getSingletonInstance();

		// 出荷実績登録済み
        $table = 'dtb_shukka_jisseki';
        $where = 'order_id = ? and customer_id = ?';
        $orderId = $objFormParam->getValue('order_id');
        $customerId = $objFormParam->getValue('customer_id');
        $shukkaCnt = $objQuery->count($table, $where, array($orderId, $customerId));
        if ($shukkaCnt > 0) {
			$objErr->arrErr['order_id'] = sprintf("【受注ID：%s、顧客ID：%s】既に出荷実績インポートを実施されています", $orderId, $customerId);
			$errorFlg = true;
		}

		// 受注ID未登録
        $table = 'dtb_order';
        $where = 'order_id = ? and customer_id = ?';
        $orderCnt = $objQuery->count($table, $where, array($orderId, $customerId));
        if ($orderCnt == 0) {
			$objErr->arrErr['order_id'] = "受注情報が存在しません";
			$errorFlg = false;
		}

		// Web顧客ID未登録
        $table = 'dtb_customer';
        $where = 'customer_id = ?';
        $customerCnt = $objQuery->count($table, $where, array($customerId));
        if ($customerCnt == 0) {
			$objErr->arrErr['customer_id'] = "顧客IDが存在しません";
			$errorFlg = false;
		}



        // 専用タグチェックの実行
		/*
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
		 */
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

    /**
     * 通販システム出荷実績登録を行う.
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param string|integer $line 処理中の行数
     * @return void
     */
    function lfRegistInosShipmentTemp($objQuery, $line = "", &$objFormParam) {

        $objProduct = new SC_Product_Ex();
        // 登録データ対象取得
        $arrList = $objFormParam->getHashArray();

        // 登録時間を生成
        // DBのnow()だとcommitした際、すべて同一の時間になってしまう
        $arrList['update_date'] = $this->lfGetDbFormatTimeWithLine($line);

        // カラムに存在しているもののうち、Form投入設定されていないデータは
        // 上書きしない
        $sqlval = SC_Utils_Ex::sfArrayIntersectKeys
	    ($arrList, $objQuery->listTableFields('dtb_shukka_jisseki'));

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

		// 出荷実績登録済み確認
		$table = 'dtb_shukka_jisseki';
		$where = 'order_id = ? and customer_id = ?';

        $objQuery = SC_Query_Ex::getSingletonInstance();
		$checkCnt = $objQuery->count($table, $where, array($arrList["order_id"]
														, $arrList["customer_id"]));

		$sqlval['send_flag'] = self::SEND_FLAG_OFF;
		if ($checkCnt) {
			// 更新
			$wval = array();
			$wval[] = $sqlval['order_id'];
			$wval[] = $sqlval['customer_id'];
			unset($sqlval['order_id']);
			unset($sqlval['customer_id']);

			$objQuery->update("dtb_shukka_jisseki", $sqlval, $where, $wval);
		} else {
			// 新規登録
			$sqlval['create_date'] = $arrList['update_date'];
			$objQuery->insert("dtb_shukka_jisseki", $sqlval);
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
            case 'order_id' :
            case 'customer_id' :
            case 'query_number' :
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
