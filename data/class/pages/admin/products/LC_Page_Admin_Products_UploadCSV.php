<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';
require_once CLASS_EX_REALDIR . 'helper_extends/SC_Helper_CSV_Ex.php';

/**
 * 商品登録CSVのページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Admin_Products_UploadCSV.php 15532 2007-08-31 14:39:46Z nanasess $
 *
 * FIXME 同一商品IDで商品規格違いを登録できない。(更新は可能)
 */
class LC_Page_Admin_Products_UploadCSV extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /** TAGエラーチェックフィールド情報 */
    var $arrTagCheckItem;

    /** 商品テーブルカラム情報 (登録処理用) **/
    var $arrProductColumn;

    /** 商品規格テーブルカラム情報 (登録処理用) **/
    var $arrProductClassColumn;

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
        $this->tpl_mainpage = 'products/upload_csv.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'upload_csv';
        $this->tpl_maintitle = '商品管理';
        $this->tpl_subtitle = '商品登録CSV';
        $this->csv_id = '1';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrDISP = $masterData->getMasterData("mtb_disp");
        $this->arrSTATUS = $masterData->getMasterData("mtb_status");
        $this->arrDELIVERYDATE = $masterData->getMasterData("mtb_delivery_date");
        $this->arrProductType = $masterData->getMasterData("mtb_product_type");
        $this->arrMaker = SC_Helper_DB_Ex::sfGetIDValueList("dtb_maker", "maker_id", 'name');
        $this->arrPayments = SC_Helper_DB_Ex::sfGetIDValueList("dtb_payment", "payment_id", "payment_method");
        $this->arrInfo = SC_Helper_DB_Ex::sfGetBasisData();
        $this->arrAllowedTag = $masterData->getMasterData("mtb_allowed_tag");
        $this->arrTagCheckItem = array();

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
        if(!$objCSV->sfIsImportCSVFrame($arrCSVFrame) ) {
            // 無効なフォーマットなので初期状態に強制変更
            $arrCSVFrame = $objCSV->sfGetCsvOutput($this->csv_id, '', array(), 'no');
            $this->tpl_is_format_default = true;
        }
        // CSV構造は更新可能なフォーマットかのフラグ取得
        $this->tpl_is_update = $objCSV->sfIsUpdateCSVFrame($arrCSVFrame);

        // CSVファイルアップロード情報の初期化
        $objUpFile = new SC_UploadFile_Ex(IMAGE_TEMP_REALDIR, IMAGE_SAVE_REALDIR);
        $this->lfInitFile($objUpFile);

        // パラメーター情報の初期化
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam, $arrCSVFrame);

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
        $enc_filepath = SC_Utils_Ex::sfEncodeFile
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
                $this->lfRegistProduct($objQuery, $line_count, $objFormParam);
                $arrParam = $objFormParam->getHashArray();

                $this->addRowResult($line_count, "商品ID：".$arrParam['product_id'] . " / 商品名：" . $arrParam['name']);
            }
        }

        // 実行結果画面を表示
        $this->tpl_mainpage = 'products/upload_csv_complete.tpl';

        fclose($fp);

        if ($errFlag) {
            $objQuery->rollback();
            return;
        }

        $objQuery->commit();

        // 商品件数カウント関数の実行
        $this->objDb->sfCountCategory($objQuery);
        $this->objDb->sfCountMaker($objQuery);
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
            // HTML_TAG_CHECKは別途実行なので除去し、別保存しておく
            if(strpos(strtoupper($item['error_check_types']), 'HTML_TAG_CHECK') !== FALSE) {
                $this->arrTagCheckItem[] = $item;
                $error_check_types = str_replace('HTML_TAG_CHECK', '', $item['error_check_types']);
            }else{
                $error_check_types = $item['error_check_types'];
            }
            $arrErrorCheckTypes = explode(',', $error_check_types);
            foreach($arrErrorCheckTypes as $key => $val) {
                if(trim($val) == "") {
                    unset($arrErrorCheckTypes[$key]);
                }else{
                    $arrErrorCheckTypes[$key] = trim($val);
                }
            }
            // パラメーター登録
            $objFormParam->addParam(
                    $item['disp_name']
                    , $col
                    , constant($item['size_const_type'])
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
        $arrRet =  $objFormParam->getHashArray();
        $objErr = new SC_CheckError_Ex($arrRet);
        $objErr->arrErr = $objFormParam->checkError(false);
        // HTMLタグチェックの実行
        foreach($this->arrTagCheckItem as $item) {
            $objErr->doFunc(array( $item['disp_name'], $item['col'], $this->arrAllowedTag), array("HTML_TAG_CHECK"));
        }
        // このフォーム特有の複雑系のエラーチェックを行う
        if(count($objErr->arrErr) == 0) {
            $objErr->arrErr = $this->lfCheckErrorDetail($arrRet, $objErr->arrErr);
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
        $this->arrProductColumn = $objQuery->listTableFields('dtb_products');
        $this->arrProductClassColumn = $objQuery->listTableFields('dtb_products_class');
    }

    /**
     * 商品登録を行う.
     *
     * FIXME: 商品登録の実処理自体は、LC_Page_Admin_Products_Productと共通化して欲しい。
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param string|integer $line 処理中の行数
     * @return void
     */
    function lfRegistProduct($objQuery, $line = "", &$objFormParam) {
        $objProduct = new SC_Product_Ex();
        // 登録データ対象取得
        $arrList = $objFormParam->getHashArray();
        // 登録時間を生成(DBのnow()だとcommitした際、すべて同一の時間になってしまう)
        $arrList['update_date'] = $this->lfGetDbFormatTimeWithLine($line);

        // 商品登録情報を生成する。
        // 商品テーブルのカラムに存在しているもののうち、Form投入設定されていないデータは上書きしない。
        $sqlval = SC_Utils_Ex::sfArrayIntersectKeys($arrList, $this->arrProductColumn);

        // 必須入力では無い項目だが、空文字では問題のある特殊なカラム値の初期値設定
        $sqlval = $this->lfSetProductDefaultData($sqlval);

        if($sqlval['product_id'] != "") {
            // 同じidが存在すればupdate存在しなければinsert
            $where = "product_id = ?";
            $product_count = $objQuery->count("dtb_products", $where, array($sqlval['product_id']));
            if($product_count > 0){
                $objQuery->update("dtb_products", $sqlval, $where, array($sqlval['product_id']));
            }else{
                $sqlval['create_date'] = $arrList['update_date'];
                // INSERTの実行
                $objQuery->insert("dtb_products", $sqlval);
                // シーケンスの調整
                $seq_count = $objQuery->currVal('dtb_products_product_id');
                if($seq_count < $sqlval['product_id']){
                    $objQuery->setVal('dtb_products_product_id', $sqlval['product_id'] + 1);
                }
            }
            $product_id = $sqlval['product_id'];
        } else {
            // 新規登録
            $sqlval['product_id'] = $objQuery->nextVal('dtb_products_product_id');
            $product_id = $sqlval['product_id'];
            $sqlval['create_date'] = $arrList['update_date'];
            // INSERTの実行
            $objQuery->insert("dtb_products", $sqlval);
        }

        // カテゴリー登録
        if($arrList['category_ids'] != "") {
            $arrCategory_id = explode(',', $arrList['category_ids']);
            $this->objDb->updateProductCategories($arrCategory_id, $product_id);
        }
        // ステータス登録
        if($arrList['product_statuses'] != "") {
            $arrStatus_id = explode(',', $arrList['product_statuses']);
            $objProduct->setProductStatus($product_id, $arrStatus_id);
        }

        // 商品規格情報を登録する
        $this->lfRegistProductClass($objQuery, $arrList, $product_id, $arrList['product_class_id']);

        // 関連商品登録
        //$this->lfRegistReccomendProducts($objQuery, $arrList, $product_id);
    }

    /**
     * 商品規格登録を行う.
     *
     * FIXME: 商品規格登録の実処理自体は、LC_Page_Admin_Products_Productと共通化して欲しい。
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param array $arrList 商品規格情報配列
     * @param integer $product_id 商品ID
     * @param integer $product_class_id 商品規格ID
     * @return void
     */
    function lfRegistProductClass($objQuery, $arrList, $product_id, $product_class_id) {
        $objProduct = new SC_Product_Ex();
        // 商品規格登録情報を生成する。
        // 商品規格テーブルのカラムに存在しているもののうち、Form投入設定されていないデータは上書きしない。
        $sqlval = SC_Utils_Ex::sfArrayIntersectKeys($arrList, $this->arrProductClassColumn);
        // 必須入力では無い項目だが、空文字では問題のある特殊なカラム値の初期値設定
        $sqlval = $this->lfSetProductClassDefaultData($sqlval);

        if($product_class_id == "") {
            // 新規登録
            $sqlval['product_id'] = $product_id;
            $sqlval['product_class_id'] = $objQuery->nextVal('dtb_products_class_product_class_id');
            $sqlval['create_date'] = $arrList['update_date'];
            // INSERTの実行
            $objQuery->insert("dtb_products_class", $sqlval);
            $product_class_id = $sqlval['product_class_id'];
        } else {
            // UPDATEの実行
            $where = "product_class_id = ?";
            $objQuery->update("dtb_products_class", $sqlval, $where, array($product_class_id));
        }
        // 支払い方法登録
        if($arrList['product_payment_ids'] != "") {
            $arrPayment_id = explode(',', $arrList['product_payment_ids']);
            $objProduct->setPaymentOptions($product_class_id, $arrPayment_id);
        }
    }

    /**
     * 関連商品登録を行う.
     *
     * FIXME: 商品規格登録の実処理自体は、LC_Page_Admin_Products_Productと共通化して欲しい。
     *        DELETE/INSERT ではなく UPDATEへの変更も・・・
     *
     * @param SC_Query $objQuery SC_Queryインスタンス
     * @param array $arrList 商品規格情報配列
     * @param integer $product_id 商品ID
     * @return void
     */
    function lfRegistReccomendProducts($objQuery, $arrList, $product_id) {
        $objQuery->delete("dtb_recommend_products", "product_id = ?", array($product_id));
        for($i = 1; $i <= RECOMMEND_PRODUCT_MAX; $i++) {
            $keyname = "recommend_product_id" . $i;
            $comment_key = "recommend_comment" . $i;
            if($arrList[$keyname] != "") {
                $arrProduct = $objQuery->select("product_id", "dtb_products", "product_id = ?", array($arrList[$keyname]));
                if($arrProduct[0]['product_id'] != "") {
                    $arrval['product_id'] = $product_id;
                    $arrval['recommend_product_id'] = $arrProduct[0]['product_id'];
                    $arrval['comment'] = $arrList[$comment_key];
                    $arrval['update_date'] = $arrList['update_date'];
                    $arrval['create_date'] = $arrList['update_date'];
                    $arrval['creator_id'] = $_SESSION['member_id'];
                    $arrval['rank'] = RECOMMEND_PRODUCT_MAX - $i + 1;
                    $objQuery->insert("dtb_recommend_products", $arrval);
                }
            }
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
                /* 更新のみなので不要
                case 'status':
                    $arrCSVFrame[$key]['default'] = DEFAULT_PRODUCT_DISP;
                    break;
                case 'del_flg':
                    $arrCSVFrame[$key]['default'] = '0';
                    break;
                case 'point_rate':
                    $arrCSVFrame[$key]['default'] = $this->arrInfo['point_rate'];
                    break;
                case 'product_type_id':
                    $arrCSVFrame[$key]['default'] = DEFAULT_PRODUCT_DOWN;
                    break;
                case 'product_payment_ids':
                    $arrCSVFrame[$key]['default'] = implode(',',array_keys($this->arrPayments));
                    break;
                case 'stock_unlimited':
                    $arrCSVFrame[$key]['default'] = UNLIMITED_FLG_LIMITED;
                 */
                default:
                    break;
            }
        }
        return $arrCSVFrame;
    }

    /**
     * 商品データ登録前に特殊な値の持ち方をする部分のデータ部分の初期値補正を行う
     *
     * @param array $sqlval 商品登録情報配列
     * @return $sqlval 登録情報配列
     */
    function lfSetProductDefaultData(&$sqlval) {
        //新規登録時のみ設定する項目
        if( $sqlval['product_id'] == "") {
            if($sqlval['status'] == "") {
                $sqlval['status'] = DEFAULT_PRODUCT_DISP;
            }
        }
        //共通で空欄時に上書きする項目
        if($sqlval['del_flg'] == ""){
            $sqlval['del_flg'] = '0'; //有効
        }
        if($sqlval['creator_id'] == "") {
            $sqlval['creator_id'] = $_SESSION['member_id'];
        }
        return $sqlval;
    }

    /**
     * 商品規格データ登録前に特殊な値の持ち方をする部分のデータ部分の初期値補正を行う
     *
     * @param array $sqlval 商品登録情報配列
     * @return $sqlval 登録情報配列
     */
    function lfSetProductClassDefaultData(&$sqlval) {
        //新規登録時のみ設定する項目
        if($sqlval['product_class_id'] == "") {
            if($sqlval['point_rate'] == "") {
                $sqlval['point_rate'] = $this->arrInfo['point_rate'];
            }
            if($sqlval['product_type_id'] == "") {
                $sqlval['product_type_id'] = DEFAULT_PRODUCT_DOWN;
            }
            // TODO: 在庫数、無制限フラグの扱いについて仕様がぶれているので要調整
            if($sqlval['stock'] == "" and $sqlval['stock_unlimited'] != UNLIMITED_FLG_UNLIMITED) {
                //在庫数設定がされておらず、かつ無制限フラグが設定されていない場合、強制無制限
                $sqlval['stock_unlimited'] = UNLIMITED_FLG_UNLIMITED;
            }elseif($sqlval['stock'] != "" and $sqlval['stock_unlimited'] != UNLIMITED_FLG_UNLIMITED) {
                //在庫数設定時は在庫無制限フラグをクリア
                $sqlval['stock_unlimited'] = UNLIMITED_FLG_LIMITED;
            }elseif($sqlval['stock'] != "" and $sqlval['stock_unlimited'] == UNLIMITED_FLG_UNLIMITED) {
                //在庫無制限フラグ設定時は在庫数をクリア
                $sqlval['stock'] = '';
            }
        }else{
            //更新時のみ設定する項目
            if(array_key_exists('stock_unlimited', $sqlval) and $sqlval['stock_unlimited'] == UNLIMITED_FLG_UNLIMITED) {
                $sqlval['stock'] = '';
            }
        }
        //共通で設定する項目
        if($sqlval['del_flg'] == ""){
            $sqlval['del_flg'] = '0'; //有効
        }
        if($sqlval['creator_id'] == "") {
            $sqlval['creator_id'] = $_SESSION['member_id'];
        }
        return $sqlval;
    }

    /**
     * このフォーム特有の複雑な入力チェックを行う.
     *
     * @param array 確認対象データ
     * @param array エラー配列
     * @return array エラー配列
     */
    function lfCheckErrorDetail($item, $arrErr) {
        // 規格IDの存在チェック
        if(!$this->lfIsDbRecord('dtb_products_class', 'product_class_id', $item)) {
            $arrErr['product_class_id'] = "※ 指定の商品規格IDは、登録されていません。";
        }
        // 商品ID、規格IDの組合せチェック
        if(array_search('product_class_id', $this->arrFormKeyList) !== FALSE
                and $item['product_class_id'] != "") {
            if($item['product_id'] == "") {
                $arrErr['product_class_id'] = "※ 商品規格ID指定時には商品IDの指定が必須です。";
            }else{
                if(!$this->objDb->sfIsRecord('dtb_products_class', 'product_id, product_class_id'
                        , array($item['product_id'], $item['product_class_id']))) {
                    $arrErr['product_class_id'] = "※ 指定の商品IDと商品規格IDの組合せは正しくありません。";
                }
            }
        }
        // 規格組合せIDの存在チェック
//        if(!$this->lfIsDbRecord('dtb_class_combination', 'class_combination_id', $item)) {
//      SC_Utils::sfIsRecord が del_flg が無いと使えない為、個別処理
        if(array_search('class_combination_id', $this->arrFormKeyList) !== FALSE
                and $item['class_combination_id'] != "" ) {
            $objQuery =& SC_Query_Ex::getSingletonInstance();
            $ret = $objQuery->get('class_combination_id', 'dtb_class_combination', 'class_combination_id = ?', array($item['class_combination_id']));
            if($ret == "") {
                $arrErr['class_combination_id'] = "※ 指定の規格組合せIDは、登録されていません。";
            }
        }
        // 表示ステータスの存在チェック
        if(!$this->lfIsArrayRecord($this->arrDISP, 'status', $item)) {
            $arrErr['status'] = "※ 指定の表示ステータスは、登録されていません。";
        }
        // メーカーIDの存在チェック
        if(!$this->lfIsArrayRecord($this->arrMaker, 'maker_id', $item)) {
            $arrErr['maker_id'] = "※ 指定のメーカーIDは、登録されていません。";
        }
        // 発送日目安IDの存在チェック
        if(!$this->lfIsArrayRecord($this->arrDELIVERYDATE, 'deliv_date_id', $item)) {
            $arrErr['deliv_date_id'] = "※ 指定の発送日目安IDは、登録されていません。";
        }
        // 発送日目安IDの存在チェック
        if(!$this->lfIsArrayRecord($this->arrProductType, 'product_type_id', $item)) {
            $arrErr['product_type_id'] = "※ 指定の商品種別IDは、登録されていません。";
        }
        // 関連商品IDの存在チェック
        for($i = 1; $i <= RECOMMEND_PRODUCT_MAX; $i++) {
            if(array_search('recommend_product_id' . $i, $this->arrFormKeyList) !== FALSE
                    and $item['recommend_product_id' . $i] != ""
                    and !$this->objDb->sfIsRecord('dtb_products', 'product_id', (array)$item['recommend_product_id' . $i]) ) {
                $arrErr['recommend_product_id' . $i] = "※ 指定の関連商品ID($i)は、登録されていません。";
            }
        }
        // カテゴリIDの存在チェック
        if(!$this->lfIsDbRecordMulti('dtb_category', 'category_id', 'category_ids', $item, ',')) {
            $arrErr['category_ids'] = "※ 指定のカテゴリIDは、登録されていません。";
        }
        // ステータスIDの存在チェック
        if(!$this->lfIsArrayRecordMulti($this->arrSTATUS, 'product_statuses', $item, ',')) {
            $arrErr['product_statuses'] = "※ 指定のステータスIDは、登録されていません。";
        }
        // 支払い方法IDの存在チェック
        if(!$this->lfIsArrayRecordMulti($this->arrPayments, 'product_payment_ids', $item, ',')) {
            $arrErr['product_payment_ids'] = "※ 指定の支払い方法IDは、登録されていません。";
        }
        // 削除フラグのチェック
        if(array_search('del_flg', $this->arrFormKeyList) !== FALSE
                and $item['del_flg'] != "") {
            if(!($item['del_flg'] == "0" or $item['del_flg'] == "1")) {
                $arrErr['del_flg'] = "※ 削除フラグは「0」(有効)、「1」(削除)のみが有効な値です。";
            }
        }
/*
    TODO: 在庫数の扱いが2.4仕様ではぶれているのでどうするか・・
        // 在庫数/在庫無制限フラグの有効性に関するチェック
        if($item['stock'] == "") {
            if(array_search('stock_unlimited', $this->arrFormKeyList) === FALSE) {
                $arrErr['stock'] = "※ 在庫数は必須です（無制限フラグ項目がある場合のみ空欄許可）。";
            }else if($item['stock_unlimited'] != UNLIMITED_FLG_UNLIMITED) {
                $arrErr['stock'] = "※ 在庫数または在庫無制限フラグのいずれかの入力が必須です。";
            }
        }
*/
        // ダウンロード商品チェック
        if(array_search('product_type_id', $this->arrFormKeyList) !== FALSE
                 and $item['product_type_id'] == PRODUCT_TYPE_NORMAL) {
            //実商品の場合
            if( $item['down_filename'] != "") {
                $arrErr['down_filename'] = "※ 実商品の場合はダウンロードファイル名は入力できません。";
            }
            if( $item['down_realfilename'] != "") {
                $arrErr['down_realfilename'] = "※ 実商品の場合はダウンロード商品用ファイルアップロードは入力できません。";
            }
        }elseif(array_search('product_type_id', $this->arrFormKeyList) !== FALSE
                and $item['product_type_id'] == PRODUCT_TYPE_DOWNLOAD) {
            //ダウンロード商品の場合
            if( $item['down_filename'] == "") {
                $arrErr['down_filename'] = "※ ダウンロード商品の場合はダウンロードファイル名は必須です。";
            }
            if( $item['down_realfilename'] == "") {
                $arrErr['down_realfilename'] = "※ ダウンロード商品の場合はダウンロード商品用ファイルアップロードは必須です。";
            }
        }
        return $arrErr;
    }

    // TODO: ここから下のルーチンは汎用ルーチンとして移動が望ましい

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

    /**
     * 指定されたキーと複数値の有効性の配列内確認
     *
     * @param string $arr チェック対象配列
     * @param string $keyname フォームキー名
     * @param array  $item 入力データ配列
     * @param string $delimiter 分割文字
     * @return boolean true:有効なデータがある false:有効ではない
     */
    function lfIsArrayRecordMulti($arr, $keyname, $item, $delimiter = ',') {
        if(array_search($keyname, $this->arrFormKeyList) === FALSE) {
            return true;
        }
        if($item[$keyname] == "") {
            return true;
        }
        $arrItems = explode($delimiter, $item[$keyname]);
        //空項目のチェック 1つでも空指定があったら不正とする。
        if(array_search("", $arrItems) !== FALSE) {
            return false;
        }
        //キー項目への存在チェック
        foreach($arrItems as $item) {
            if(!array_key_exists($item, $arr)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 指定されたキーと複数値の有効性のDB確認
     *
     * @param string $table テーブル名
     * @param string $tblkey テーブルキー名
     * @param string $keyname フォームキー名
     * @param array  $item 入力データ配列
     * @param string $delimiter 分割文字
     * @return boolean true:有効なデータがある false:有効ではない
     */
    function lfIsDbRecordMulti($table, $tblkey, $keyname, $item, $delimiter = ',') {
        if(array_search($keyname, $this->arrFormKeyList) === FALSE) {
            return true;
        }
        if($item[$keyname] == "") {
            return true;
        }
        $arrItems = explode($delimiter, $item[$keyname]);
        //空項目のチェック 1つでも空指定があったら不正とする。
        if(array_search("", $arrItems) !== FALSE) {
            return false;
        }
        $count = count($arrItems);
        $where = $tblkey ." IN (" . implode(",", array_fill(0, $count, "?")) . ")";

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $db_count = $objQuery->count($table, $where, $arrItems);
        if($count != $db_count) {
            return false;
        }
        return true;
    }

    /**
     * 指定されたキーと値の有効性のDB確認
     *
     * @param string $table テーブル名
     * @param string $keyname キー名
     * @param array  $item 入力データ配列
     * @return boolean true:有効なデータがある false:有効ではない
     */
    function lfIsDbRecord($table, $keyname, $item) {
        if(array_search($keyname, $this->arrFormKeyList) !== FALSE  //入力対象である
                and $item[$keyname] != ""   // 空ではない
                and !$this->objDb->sfIsRecord($table, $keyname, (array)$item[$keyname]) //DBに存在するか
                ) {
            return false;
        }
        return true;
    }

    /**
     * 指定されたキーと値の有効性の配列内確認
     *
     * @param string $arr チェック対象配列
     * @param string $keyname キー名
     * @param array  $item 入力データ配列
     * @return boolean true:有効なデータがある false:有効ではない
     */
    function lfIsArrayRecord($arr, $keyname, $item) {
        if(array_search($keyname, $this->arrFormKeyList) !== FALSE //入力対象である
                and $item[$keyname] != "" // 空ではない
                and !array_key_exists($item[$keyname], $arr) //配列に存在するか
                ) {
            return false;
        }
        return true;
    }
}
