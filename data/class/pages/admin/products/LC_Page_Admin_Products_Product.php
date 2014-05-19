<?php
// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/products/LC_Page_Admin_Products_Ex.php';

/**
 * 商品登録 のページクラス
 *
 * @package Page
 * @author IQUEVE Co.,Ltd.
 * @version $Id: LC_Page_Admin_Products_Product.php 133 2012-06-13 05:46:25Z hira $
 */
class LC_Page_Admin_Products_Product extends LC_Page_Admin_Products_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/product.tpl';

        $this->tpl_complete = 'products/complete.tpl';
        $this->tpl_confirm = 'products/confirm.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'index';
        $this->tpl_maintitle = '商品管理';
        $this->tpl_subtitle = '商品登録';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrDISP = $masterData->getMasterData("mtb_disp");
        $this->arrSTATUS = $masterData->getMasterData("mtb_status");

        // #228 画像の取得先を変更
        $objDb = new SC_Helper_DB_Ex();
        $this->arrSTATUS_IMAGE = $objDb->sfGetStatusImageList();
        //$this->arrSTATUS_IMAGE = $masterData->getMasterData("mtb_status_image");
        
        $this->arrDELIVERYDATE =
            $masterData->getMasterData("mtb_delivery_date");
        $this->arrAllowedTag = $masterData->getMasterData("mtb_allowed_tag");
        $this->arrCourseCd = $masterData->getMasterData("mtb_course_cd");
        $this->arrTodokeKbn = $masterData->getMasterData("mtb_todoke_kbn");

        $this->arrSANTYOKU = array(1 => '紀泉', 2 => '産直');
        $this->arrHAISOKBN_1 =
            array(0 => '通常', 1 => 'ワレモノ', 2 => 'なまもの');
        $this->arrHAISOKBN_2 = array(0 => '通常', 1 => '冷蔵', 2 => '冷凍');
        $this->arrMAILDELIV = array(0 => 'ヤマト', 1 => '佐川');
        $this->arrCOMPONENT_FLG = array(1 => '表示', 0 => '非表示');
        $this->arrNOT_SEARCH_FLG = array(1 => '検索対象', 0 => '検索除外');
        $this->arrTEIKI_FLG = array(0 => 'なし', 1 => 'あり');
        $this->arrSAMPLE_FLG = array(0 => '通常', 1 => 'サンプル商品');
        $this->arrPRESENT_FLG = array(0 => '通常', 1 => 'プレゼント商品');
        $this->arrSELL_FLG = array(0 => '販売対象外', 1 => '販売対象');
        $this->arrCART_BTN_FLG = array(0 => '表示しない', 1 => '表示する');

        $objPurchase = new SC_Helper_Purchase_Ex();
        $this->arrEMPLOYEE_CD_NAME = $objPurchase->getEmployeeSaleNameList();
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
        $objFormParam = new SC_FormParam_Ex();

        // アップロードファイル情報の初期化
        $objUpFile =
            new SC_UploadFile_Ex(IMAGE_TEMP_REALDIR, IMAGE_SAVE_REALDIR);
        $this->lfInitFile($objUpFile);
        $objUpFile->setHiddenFileList($_POST);

        // ダウンロード販売ファイル情報の初期化
        $objDownFile =
            new SC_UploadFile_Ex(DOWN_TEMP_REALDIR, DOWN_SAVE_REALDIR);
        $this->lfInitDownFile($objDownFile);
        $objDownFile->setHiddenFileList($_POST);

        // 検索パラメーター引き継ぎ
        $this->arrSearchHidden = $this->lfGetSearchParam($_POST);

        $mode = $this->getMode();
        switch($mode) {
        case 'pre_edit':
            // パラメーター初期化(商品ID)
            $this->lfInitFormParam_PreEdit($objFormParam, $_POST);
            // エラーチェック
            $this->arrErr = $objFormParam->checkError();
            if (count($this->arrErr) > 0) {
                SC_Utils_Ex::sfDispException();
            }

            // 商品ID取得
            $product_id = $objFormParam->getValue('product_id');
            // 商品データ取得
            $arrForm = $this->lfGetFormParam_PreEdit
                ($objUpFile, $objDownFile, $product_id);
            // ページ表示用パラメーター設定
            $this->arrForm = $this->lfSetViewParam_InputPage
                ($objUpFile, $objDownFile, $arrForm);

            // ページonload時のJavaScript設定
            $this->tpl_onload = $this->lfSetOnloadJavaScript_InputPage();
            break;

        case 'edit':
            // パラメーター初期化, 取得
            $this->lfInitFormParam($objFormParam, $_POST);
            $arrForm = $objFormParam->getHashArray();
            // エラーチェック
            $this->arrErr = $this->lfCheckError_Edit
                ($objFormParam, $objUpFile, $objDownFile, $arrForm);
            if (count($this->arrErr) == 0) {
                // 確認画面表示設定
                $this->tpl_mainpage = $this->tpl_confirm;
                $this->arrCatList = $this->lfGetCategoryList_Edit();
                $this->arrForm = $this->lfSetViewParam_ConfirmPage
                    ($objUpFile, $objDownFile, $arrForm);
            } else {
                // 入力画面表示設定
                $this->arrForm = $this->lfSetViewParam_InputPage
                    ($objUpFile, $objDownFile, $arrForm);
                // ページonload時のJavaScript設定
                $this->tpl_onload = $this->lfSetOnloadJavaScript_InputPage();
            }
            break;

        case 'complete':
            // パラメーター初期化, 取得
            $this->lfInitFormParam($objFormParam, $_POST);
            $arrForm = $this->lfGetFormParam_Complete($objFormParam);
            // エラーチェック
            $this->arrErr = $this->lfCheckError_Edit
                ($objFormParam, $objUpFile, $objDownFile, $arrForm);
            if (count($this->arrErr) == 0) {
                // DBへデータ登録
                $product_id =
                    $this->lfRegistProduct($objUpFile, $objDownFile, $arrForm);

                // 件数カウントバッチ実行
                $objQuery =& SC_Query_Ex::getSingletonInstance();
                $objDb = new SC_Helper_DB_Ex();
                $objDb->sfCountCategory($objQuery);
                $objDb->sfCountMaker($objQuery);

                // 一時ファイルを本番ディレクトリに移動する
                $this->lfSaveUploadFiles($objUpFile, $objDownFile, $product_id);

                $this->tpl_mainpage = $this->tpl_complete;
                $this->arrForm['product_id'] = $product_id;
            } else {
                // 入力画面表示設定
                $this->arrForm = $this->lfSetViewParam_InputPage
                    ($objUpFile, $objDownFile, $arrForm);
                // ページonload時のJavaScript設定
                $this->tpl_onload = $this->lfSetOnloadJavaScript_InputPage();
            }
            break;

        // 画像のアップロード
        case 'upload_image':
        case 'delete_image':
            // パラメーター初期化
            $this->lfInitFormParam_UploadImage($objFormParam);
            $this->lfInitFormParam($objFormParam, $_POST);
            $arrForm = $objFormParam->getHashArray();

            switch ($mode) {
            case 'upload_image':
                // ファイルを一時ディレクトリにアップロード
                $this->arrErr[$arrForm['image_key']] =
                    $objUpFile->makeTempFile($arrForm['image_key'],
                                             IMAGE_RENAME);
                if ($this->arrErr[$arrForm['image_key']] == "") {
                    // 縮小画像作成
                    $this->lfSetScaleImage($objUpFile, $arrForm['image_key']);
                }
                break;

            case 'delete_image':
                // ファイル削除
                $this->lfDeleteTempFile($objUpFile, $arrForm['image_key']);
                break;
            }

            // 入力画面表示設定
            $this->arrForm = $this->lfSetViewParam_InputPage
                ($objUpFile, $objDownFile, $arrForm);
            // ページonload時のJavaScript設定
            $anchor_hash = $this->getAnchorHash($arrForm['image_key']);
            $this->tpl_onload =
                $this->lfSetOnloadJavaScript_InputPage($anchor_hash);
            break;

        // ダウンロード商品ファイルアップロード
        case 'upload_down':
        case 'delete_down':
            // パラメーター初期化
            $this->lfInitFormParam_UploadDown($objFormParam);
            $this->lfInitFormParam($objFormParam, $_POST);
            $arrForm = $objFormParam->getHashArray();

            switch($mode) {
            case 'upload_down':
                // ファイルを一時ディレクトリにアップロード
                $this->arrErr[$arrForm['down_key']] =
                    $objDownFile->makeTempDownFile();
                break;

            case 'delete_down':
                // ファイル削除
                $objDownFile->deleteFile($arrForm['down_key']);
                break;
            }

            // 入力画面表示設定
            $this->arrForm = $this->lfSetViewParam_InputPage
                ($objUpFile, $objDownFile, $arrForm);
            // ページonload時のJavaScript設定
            $anchor_hash = $this->getAnchorHash($arrForm['down_key']);
            $this->tpl_onload =
                $this->lfSetOnloadJavaScript_InputPage($anchor_hash);
            break;

        // 関連商品選択
        case 'recommend_select' :
            // パラメーター初期化
            $this->lfInitFormParam_RecommendSelect($objFormParam);
            $this->lfInitFormParam($objFormParam, $_POST);
            $arrForm = $objFormParam->getHashArray();
            // 入力画面表示設定
            $this->arrForm = $this->lfSetViewParam_InputPage
                ($objUpFile, $objDownFile, $arrForm);

            // 選択された関連商品IDがすでに登録している関連商品と重複し
            // ていないかチェック
            $this->lfCheckError_RecommendSelect($this->arrForm, $this->arrErr);

            // ページonload時のJavaScript設定
            $anchor_hash = $this->getAnchorHash($this->arrForm['anchor_key']);
            $this->tpl_onload =
                $this->lfSetOnloadJavaScript_InputPage($anchor_hash);
            break;

        // 確認ページからの戻り
        case 'confirm_return':
            // パラメーター初期化
            $this->lfInitFormParam($objFormParam, $_POST);
            $arrForm = $objFormParam->getHashArray();
            // 入力画面表示設定
            $this->arrForm = $this->lfSetViewParam_InputPage
                ($objUpFile, $objDownFile, $arrForm);
            // ページonload時のJavaScript設定
            $this->tpl_onload = $this->lfSetOnloadJavaScript_InputPage();
            break;

        default:
            // 入力画面表示設定
            $arrForm = array();
            $this->arrForm = $this->lfSetViewParam_InputPage
                ($objUpFile, $objDownFile, $arrForm);
            // ページonload時のJavaScript設定
            $this->tpl_onload = $this->lfSetOnloadJavaScript_InputPage();
            break;
        }

        // 関連商品の読み込み
        $this->arrRecommend = $this->lfGetRecommendProducts($this->arrForm);
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
     * パラメーター情報の初期化
     * - 編集/複製モード
     *
     * @param object $objFormParam SC_FormParamインスタンス
     * @param array $arrPost $_POSTデータ
     * @return void
     */
    function lfInitFormParam_PreEdit(&$objFormParam, $arrPost) {
        $objFormParam->addParam
            ("商品ID", "product_id", INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));

        $objFormParam->setParam($arrPost);
        $objFormParam->convParam();
    }

    /**
     * パラメーター情報の初期化
     *
     * @param object $objFormParam SC_FormParamインスタンス
     * @param array $arrPost $_POSTデータ
     * @return void
     */
    function lfInitFormParam(&$objFormParam, $arrPost) {
        $objFormParam->addParam
            ("商品ID", "product_id", INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("商品名", 'name', PRODUCT_NAME_BYTE_LEN, '',
             array("EXIST_CHECK", "SPTAB_CHECK", "MAX_BYTES_SJIS"));

        $objFormParam->addParam
            ("表示用商品名", 'disp_name', DISP_NAME_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));

        if (!$this->lfGetProductClassFlag($arrPost['has_product_class'])) {
            $objFormParam->addParam
                ("商品コード", "product_code", STEXT_LEN, 'a',
                 array("EXIST_CHECK", "SPTAB_CHECK","MAX_LENGTH_CHECK"));
        }

        $objFormParam->addParam
            ("商品カテゴリ", "category_id", INT_LEN, 'n',
             array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("公開・非公開", 'status', INT_LEN, 'n',
             array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("掲載開始日", "disp_start_date", STEXT_LEN, 'a',
             array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("販売開始日", "sale_start_date", STEXT_LEN, 'a',
             array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("販売終了日", "sale_end_date", STEXT_LEN, 'a',
             array("SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("ブランドID", "brand_id", INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("ブランドコード", "brand_code", STEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("ブランド名", "brand_name", STEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("販売名", 'sales_name', SALES_NAME_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("産直区分", 'drop_shipment', INT_LEN, 'n',
             array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("配送区分1", 'deliv_kbn1', INT_LEN, 'n',
             array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("配送区分2", 'deliv_kbn2', INT_LEN, 'n',
             array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("メール便業者", 'mail_deliv_id', INT_LEN, 'n',
             array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("成分表示フラグ", 'component_flg', INT_LEN, 'n',
             array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("検索除外", 'not_search_flg', INT_LEN, 'n',
             array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("定期購入", 'teiki_flg', INT_LEN, 'n',
             array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("コースCD", 'course_cd', INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("お届け周期", 'todoke_kbn', INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("ポイント付与率", 'point_rate', INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("サンプル区分", 'sample_flg', INT_LEN, 'n',
             array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("プレゼント区分", 'present_flg', INT_LEN, 'n',
             array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("販売対象フラグ", 'sell_flg', INT_LEN, 'n',
             array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("社員購入グループ", 'employee_sale_cd', INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));

        if (!$this->lfGetProductClassFlag($arrPost['has_product_class'])) {
            // 新規登録, 規格なし商品の編集の場合
            $objFormParam->addParam
                ("商品種別", "product_type_id", INT_LEN, 'n',
                 array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK")); 
            $objFormParam->addParam
                (NORMAL_PRICE_TITLE, "price01", PRICE_LEN, 'n',
                 array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
            $objFormParam->addParam
                (SALE_PRICE_TITLE, "price02", PRICE_LEN, 'n',
                 array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
            $objFormParam->addParam
                ("在庫数", 'stock', AMOUNT_LEN, 'n',
                 array("SPTAB_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
            $objFormParam->addParam
                ("在庫無制限", "stock_unlimited", INT_LEN, 'n',
                 array("SPTAB_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        }

        $objFormParam->addParam
            ("完売時の表示文言", 'stock_status_name', STEXT_LEN, 'a',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("購入最低数", "sale_minimum_number", AMOUNT_LEN, 'n',
             array("EXIST_CHECK", "SPTAB_CHECK",
                   "ZERO_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("購入制限", "sale_limit", AMOUNT_LEN, 'n',
             array("EXIST_CHECK", "SPTAB_CHECK",
                   "ZERO_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("メール便計算個数", "deliv_judgment", INT_LEN, 'n',
             array("EXIST_CHECK", "SPTAB_CHECK", "ZERO_AND_OVER_CHECK",
                   "LESS_ONE_CHECK", "NUM_POINT_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("発送日目安", "deliv_date_id", INT_LEN, 'n',
             array("EXIST_CHECK", "NUM_CHECK"));
        $objFormParam->addParam
            ("容量", "capacity", CAPACITY_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("検索ワード", "comment3", LLTEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("METAタグ", "metatag", LLTEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("一覧コメント", "main_list_comment", MLTEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("save_main_list_image", "save_main_list_image", '', "", array());
        $objFormParam->addParam
            ("temp_main_list_image", "temp_main_list_image", '', "", array());
        $objFormParam->addParam
            ("詳細コメント", "main_comment", LLTEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("save_main_image", "save_main_image", '', "", array());
        $objFormParam->addParam
            ("temp_main_image", "temp_main_image", '', "", array());
        $objFormParam->addParam
            ("save_main_large_image", "save_main_large_image", '', "", array());
        $objFormParam->addParam
            ("temp_main_large_image", "temp_main_large_image", '', "", array());
        $objFormParam->addParam
            ("save_guide_image", "save_guide_image", '', "", array());
        $objFormParam->addParam
            ("temp_guide_image", "temp_guide_image", '', "", array());
        $objFormParam->addParam
            ("save_guide_image_teiki",
             "save_guide_image_teiki", '', "", array());
        $objFormParam->addParam
            ("temp_guide_image_teiki",
             "temp_guide_image_teiki", '', "", array());

        $arrTab = array("pc_", "sp_", "mb_");
        foreach ($arrTab as $prefix) {
            $objFormParam->addParam
                ("アイコン", $prefix . "product_status", INT_LEN, 'n',
                 array("NUM_CHECK", "MAX_LENGTH_CHECK"));
            $objFormParam->addParam
                ("コメント１", $prefix . "comment1", LLTEXT_LEN, '',
                 array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
            $objFormParam->addParam
                ("コメント２", $prefix . "comment2", LLTEXT_LEN, '',
                 array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
            $objFormParam->addParam
                ("コメント３", $prefix . "comment3", LLTEXT_LEN, '',
                 array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
            $objFormParam->addParam
                ("コメント４", $prefix . "comment4", LLTEXT_LEN, '',
                 array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));

            if ($prefix != "mb_") {
                $objFormParam->addParam
                    ("カートボタン表示４", $prefix . "button4", INT_LEN, 'n',
                     array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
            }

            $objFormParam->addParam
                ("コメント５", $prefix . "comment5", LLTEXT_LEN, '',
                 array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));

            if ($prefix != "mb_") {
                $objFormParam->addParam
                    ("カートボタン表示５", $prefix . "button5", INT_LEN, 'n',
                     array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
            }
        }

        for ($cnt = 1; $cnt <= RECOMMEND_PRODUCT_MAX; $cnt++) {
            $objFormParam->addParam
                ("関連商品コメント" . $cnt, "recommend_comment" . $cnt,
                 LTEXT_LEN, '', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
            $objFormParam->addParam
                ("関連商品ID" . $cnt, "recommend_id" . $cnt,
                 INT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"));
            $objFormParam->addParam
                ("recommend_delete" . $cnt, "recommend_delete" . $cnt,
                 '', 'n', array());
        }

        $objFormParam->addParam
            ("has_product_class", "has_product_class", INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("product_class_id", "product_class_id", INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("select_tab_index", "select_tab_index", INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));

        $objFormParam->setParam($arrPost);
        $objFormParam->convParam();
    }

    /**
     * パラメーター情報の初期化
     * - 画像ファイルアップロードモード
     *
     * @param object $objFormParam SC_FormParamインスタンス
     * @return void
     */
    function lfInitFormParam_UploadImage(&$objFormParam) {
        $objFormParam->addParam("image_key", "image_key", "", "", array());
    }

    /**
     * パラメーター情報の初期化
     * - ダウンロード商品ファイルアップロードモード
     *
     * @param object $objFormParam SC_FormParamインスタンス
     * @return void
     */
    function lfInitFormParam_UploadDown(&$objFormParam) {
        $objFormParam->addParam("down_key", "down_key", "", "", array());
    }

    /**
     * パラメーター情報の初期化
     * - 関連商品追加モード
     *
     * @param object $objFormParam SC_FormParamインスタンス
     * @return void
     */
    function lfInitFormParam_RecommendSelect(&$objFormParam) {
        $objFormParam->addParam("anchor_key", "anchor_key", "", "", array());
        $objFormParam->addParam("select_recommend_no", "select_recommend_no", INT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"));
    }

    /**
     * アップロードファイルパラメーター情報の初期化
     * - 画像ファイル用
     * 
     * @param object $objUpFile SC_UploadFileインスタンス
     * @return void
     */
    function lfInitFile(&$objUpFile) {
        $objUpFile->addFile
            ("カート案内画像", 'guide_image',
             array('jpg', 'gif', 'png'),
             IMAGE_SIZE, false, GUIDE_IMAGE_WIDTH, GUIDE_IMAGE_HEIGHT);
        $objUpFile->addFile
            ("カート案内画像（定期）", 'guide_image_teiki',
             array('jpg', 'gif', 'png'),
             IMAGE_SIZE, false, GUIDE_IMAGE_WIDTH, GUIDE_IMAGE_HEIGHT);
        $objUpFile->addFile
            ("一覧画像", 'main_list_image',
             array('jpg', 'gif', 'png'),
             IMAGE_SIZE, false, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
        $objUpFile->addFile
            ("詳細画像", 'main_image',
             array('jpg', 'gif', 'png'),
             IMAGE_SIZE, false, NORMAL_IMAGE_WIDTH, NORMAL_IMAGE_HEIGHT);
        $objUpFile->addFile
            ("詳細拡大画像", 'main_large_image',
             array('jpg', 'gif', 'png'),
             IMAGE_SIZE, false, LARGE_IMAGE_WIDTH, LARGE_IMAGE_HEIGHT);
    }

    /**
     * アップロードファイルパラメーター情報の初期化
     * - ダウンロード商品ファイル用
     *
     * @param object $objDownFile SC_UploadFileインスタンス
     * @return void
     */
    function lfInitDownFile(&$objDownFile) {
        $objDownFile->addFile
            ("ダウンロード販売用ファイル", 'down_file',
             explode(",", DOWNLOAD_EXTENSION),DOWN_SIZE, true, 0, 0);
    }

    /**
     * フォーム入力パラメーターのエラーチェック
     * 
     * @param object $objFormParam SC_FormParamインスタンス
     * @param object $objUpFile SC_UploadFileインスタンス
     * @param object $objDownFile SC_UploadFileインスタンス
     * @param array $arrForm フォーム入力パラメーター配列
     * @return array エラー情報を格納した連想配列
     */
    function lfCheckError_Edit(&$objFormParam,
                               &$objUpFile, &$objDownFile, $arrForm) {
        $objErr = new SC_CheckError_Ex($arrForm);
        $arrErr = array();

        // 入力パラメーターチェック
        $arrErr = $objFormParam->checkError();

        // アップロードファイル必須チェック
        $arrErr = array_merge((array)$arrErr, (array)$objUpFile->checkEXISTS());

        // 商品コード重複チェック
        $product_id = $objFormParam->getValue("product_id");
        $product_code = $objFormParam->getValue("product_code");
        if ($this->lfExistsProductCode($product_id, $product_code) > 0) {
            $arrErr['product_code'] = '商品コードが重複しています。<br />';
        }

        // HTMLタグ許可チェック
        // 2011.05.10 一覧メインコメント タグ許可
        $objErr->doFunc
            (array("一覧コメント", "main_list_comment",
                   $this->arrAllowedTag), array("HTML_TAG_CHECK"));
        $objErr->doFunc
            (array("詳細コメント", "main_comment",
                   $this->arrAllowedTag), array("HTML_TAG_CHECK"));

        $arrTab = array("pc_", "sp_", "mb_");
        foreach ($arrTab as $prefix) {
            $objErr->doFunc
                (array($prefix . "コメント１", $prefix . "comment1",
                       $this->arrAllowedTag), array("HTML_TAG_CHECK"));
            $objErr->doFunc
                (array($prefix . "コメント２", $prefix . "comment2",
                       $this->arrAllowedTag), array("HTML_TAG_CHECK"));
            $objErr->doFunc
                (array($prefix . "コメント３", $prefix . "comment3",
                       $this->arrAllowedTag), array("HTML_TAG_CHECK"));
            $objErr->doFunc
                (array($prefix . "コメント４", $prefix . "comment4",
                       $this->arrAllowedTag), array("HTML_TAG_CHECK"));
            $objErr->doFunc
                (array($prefix . "コメント５", $prefix . "comment5",
                       $this->arrAllowedTag), array("HTML_TAG_CHECK"));
        }

        // 規格情報がない商品の場合のチェック
        if ($arrForm['has_product_class'] != true) {
            // 在庫必須チェック(在庫無制限ではない場合)
            if (isset($arrForm['stock_unlimited']) == true &&
                $arrForm['stock_unlimited'] != UNLIMITED_FLG_UNLIMITED) {
                $objErr->doFunc(array("在庫数", 'stock'), array("EXIST_CHECK"));
            }
            // ダウンロード商品ファイル必須チェック(ダウンロード商品の場合)
            if ($arrForm['product_type_id'] == PRODUCT_TYPE_DOWNLOAD) {
                $arrErr = array_merge
                    ((array)$arrErr, (array)$objDownFile->checkEXISTS());
            }
        }
        // 掲載開始日
        if (!$this->checkDateYYYYMMDD($arrForm["disp_start_date"])) {
            $objErr->arrErr["disp_start_date"] = "日付入力エラー<br />";
        }
        // 販売開始日
        if (!$this->checkDateYYYYMMDD($arrForm["sale_start_date"])) {
            $objErr->arrErr["sale_start_date"] = "日付入力エラー<br />";
        }
        // 販売終了日
        if (!$this->checkDateYYYYMMDD($arrForm["sale_end_date"])) {
            $objErr->arrErr["sale_end_date"] = "日付入力エラー<br />";
        }
        // 日付前後チェック
        if (!empty($arrForm["sale_start_date"]) &&
            !empty($arrForm["sale_end_date"])) {
            $sale_start_date =
                str_replace("/", "", $arrForm["sale_start_date"]);
            $sale_end_date = str_replace("/", "", $arrForm["sale_end_date"]);

            if ($sale_start_date > $sale_end_date) {
                $objErr->arrErr["sale_start_date"] =
                    "開始日・終了日を確認してください<br />";
            }
        }
        // 購入最低数・購入制限 数量チェック
        if (empty($arrErr["sale_limit"])) {
            if ($arrForm["sale_minimum_number"] > $arrForm["sale_limit"]) {
                $objErr->arrErr["sale_limit"] =
                    "下限数・上限数を確認してください<br />";
            }
        }
        // 定期購入 コースCDチェック
        // 　ありの場合は、必須
        if ($arrForm["teiki_flg"] == "1") {
            $objErr->doFunc
                (array("定期購入 コースCD", 'course_cd'), array("EXIST_CHECK"));
        }

        // ブランドコードが入力されている場合は、存在チェックを行う
        $brand_code = $arrForm['brand_code'];
        if (!empty($arrForm['brand_code'])) {
            if ($this->lfExistsBrand($brand_code) == 0) {
                $arrErr['brand_code'] = 'ブランドが見つかりません。<br />';
            }
        }

        $arrErr = array_merge((array)$arrErr, (array)$objErr->arrErr);

        return $arrErr;
    }

    /**
     * 関連商品の重複登録チェック、エラーチェック
     *
     * 関連商品の重複があった場合はエラーメッセージを格納し、該当の商品IDをリセットする
     *
     * @param array $arrForm 入力値の配列
     * @param array $arrErr エラーメッセージの配列
     * @return void
     */
    function lfCheckError_RecommendSelect(&$arrForm, &$arrErr) {
        $select_recommend_no = $arrForm['select_recommend_no'];
        $select_recommend_id = $arrForm['recommend_id' . $select_recommend_no];

        foreach (array_keys($arrForm) as $key) {
            if (preg_match('/^recommend_id/', $key)) {
                if ($select_recommend_no ==
                    preg_replace('/^recommend_id/', '', $key)) {
                    continue;
                }

                if ($select_recommend_id == $arrForm[$key]) {
                    // 重複した場合、選択されたデータをリセットする
                    $arrForm['recommend_id' . $select_recommend_no] = '';
                    $arrErr['recommend_comment' . $select_recommend_no] =
                        '※ すでに登録されている関連商品です。<br />';
                    break;
                }
            }
        }
    }

    /**
     * 検索パラメーター引き継ぎ用配列取得
     *
     * @param array $arrPost $_POSTデータ
     * @return array 検索パラメーター配列
     */
    function lfGetSearchParam($arrPost) {
        $arrSearchParam = array();
        $objFormParam = new SC_FormParam_Ex();

        parent::lfInitParam($objFormParam);
        $objFormParam->setParam($arrPost);
        $arrSearchParam = $objFormParam->getSearchArray();

        return $arrSearchParam;
    }

    /**
     * フォームパラメーター取得
     * - 編集/複製モード
     * 
     * @param object $objUpFile SC_UploadFileインスタンス
     * @param object $objDownFile SC_UploadFileインスタンス
     * @param integer $product_id 商品ID
     * @return array フォームパラメーター配列
     */
    function lfGetFormParam_PreEdit(&$objUpFile, &$objDownFile, $product_id) {
        $arrForm = array();

        // DBから商品データ取得
        $arrForm = $this->lfGetProductData_FromDB($product_id);
        // DBデータから画像ファイル名の読込
        $objUpFile->setDBFileList($arrForm);
        // DBデータからダウンロードファイル名の読込
        $objDownFile->setDBDownFile($arrForm);

        return $arrForm;
    }

    /**
     * フォームパラメーター取得
     * - 登録モード
     * 
     * @param object $objFormParam SC_FormParamインスタンス
     * @return array フォームパラメーター配列
     */
    function lfGetFormParam_Complete(&$objFormParam) {
        $arrForm = $objFormParam->getHashArray();
        $arrForm['category_id'] = unserialize($arrForm['category_id']);
        $objFormParam->setValue('category_id', $arrForm['category_id']);

        return $arrForm;
    }

    /**
     * 表示用フォームパラメーター取得
     * - 入力画面
     *
     * @param object $objUpFile SC_UploadFileインスタンス
     * @param object $objDownFile SC_UploadFileインスタンス
     * @param array $arrForm フォーム入力パラメーター配列
     * @return array 表示用フォームパラメーター配列
     */
    function lfSetViewParam_InputPage(&$objUpFile, &$objDownFile, &$arrForm) {
        // カテゴリマスターデータ取得
        $objDb = new SC_Helper_DB_Ex();
        list($this->arrCatVal, $this->arrCatOut) = $objDb->sfGetLevelCatList(false);

        if (isset($arrForm['category_id']) && !is_array($arrForm['category_id'])) {
            $arrForm['category_id'] = unserialize($arrForm['category_id']);
        }
        if ($arrForm['status'] == "") {
            $arrForm['status'] = DEFAULT_PRODUCT_DISP;
        }
	    // 定期購入可否フラグ 0:不可
        if($arrForm['teiki_flg'] == "") {
            $arrForm['teiki_flg'] = 0;
        }
        if($arrForm['product_type_id'] == "") {
            $arrForm['product_type_id'] = DEFAULT_PRODUCT_DOWN;
        }
        // アップロードファイル情報取得(Hidden用)
        $arrHidden = $objUpFile->getHiddenFileList();
        $arrForm['arrHidden'] = array_merge((array)$arrHidden, (array)$objDownFile->getHiddenFileList());

        // 画像ファイル表示用データ取得
        $arrForm['arrFile'] = $objUpFile->getFormFileList(IMAGE_TEMP_URLPATH, IMAGE_SAVE_URLPATH);

        // ダウンロード商品実ファイル名取得
        $arrForm['down_realfilename'] = $objDownFile->getFormDownFile();

        // 基本情報(デフォルトポイントレート用)
        $arrForm['arrInfo'] = SC_Helper_DB_Ex::sfGetBasisData();

        // サブ情報ありなしフラグ
        $arrForm['sub_find'] = $this->hasSubProductData($arrForm);

        //// 2012/06/11 hira #70
        // 選択済みカテゴリ,未選択を作る
        $this->arrSelCat = array();
        $this->arrNonCat = array();
        $arrCat = is_array($arrForm['category_id'])?
            $arrForm['category_id'] : array();

        // 一致する値を抽出
        $arrSel = array_intersect($this->arrCatVal, $arrCat);
        if (count($arrSel) > 0) {
            // option群作成 php >= 5.1 
            $this->arrSelCat = array_combine($arrSel,
                array_intersect_key($this->arrCatOut, $arrSel)
            );
        }
        // 一致しない値を抽出
        $arrNon = array_diff($this->arrCatVal, $arrCat);
        if (count($arrNon) > 0) {
            // option群作成 php >= 5.1 
            $this->arrNonCat = array_combine($arrNon,
                array_intersect_key($this->arrCatOut, $arrNon)
            );
        }

        return $arrForm;
    }

    /**
     * 表示用フォームパラメーター取得
     * - 確認画面
     *
     * @param object $objUpFile SC_UploadFileインスタンス
     * @param object $objDownFile SC_UploadFileインスタンス
     * @param array $arrForm フォーム入力パラメーター配列
     * @return array 表示用フォームパラメーター配列
     */
    function lfSetViewParam_ConfirmPage(&$objUpFile, &$objDownFile, &$arrForm) {
        // カテゴリ表示用
        $arrForm['arrCategoryId'] = $arrForm['category_id'];
        // hidden に渡す値は serialize する
        $arrForm['category_id'] = serialize($arrForm['category_id']);
        // 画像ファイル用データ取得
        $arrForm['arrFile'] = $objUpFile->getFormFileList(IMAGE_TEMP_URLPATH, IMAGE_SAVE_URLPATH);
        // ダウンロード商品実ファイル名取得
        $arrForm['down_realfilename'] = $objDownFile->getFormDownFile();

        return $arrForm;
    }

    /**
     * 縮小した画像をセットする
     * 
     * @param object $objUpFile SC_UploadFileインスタンス
     * @param string $image_key 画像ファイルキー
     * @return void
     */
    function lfSetScaleImage(&$objUpFile, $image_key){
        $subno = str_replace("sub_large_image", "", $image_key);
        switch ($image_key){
        // 2013.12.03 add 案内画像追加
        case "guide_image":
            // カート案内画像
            $this->lfMakeScaleImage($objUpFile, $image_key, "guide_image");
            break;
        case "guide_image_teiki":
            // カート案内画像（定期）
            $this->lfMakeScaleImage($objUpFile, $image_key, "guide_image_teiki");
            break;
        case "main_large_image":
            // 詳細メイン画像
            $this->lfMakeScaleImage($objUpFile, $image_key, "main_image");
        case "main_image":
            // 一覧メイン画像
            $this->lfMakeScaleImage($objUpFile, $image_key, "main_list_image");
            break;
        case "sub_large_image" . $subno:
            // サブメイン画像
            $this->lfMakeScaleImage($objUpFile, $_POST['image_key'], "sub_image" . $subno);
            break;
        default:
            break;
        }
    }

    /**
     * 縮小画像生成
     *
     * @param object $objUpFile SC_UploadFileインスタンス
     * @param string $from_key 元画像ファイルキー
     * @param string $to_key 縮小画像ファイルキー
     * @param boolean $forced
     * @return void
     */
    function lfMakeScaleImage(&$objUpFile, $from_key, $to_key, $forced = false){
        $arrImageKey = array_flip($objUpFile->keyname);
        $from_path = "";

        if($objUpFile->temp_file[$arrImageKey[$from_key]]) {
            $from_path = $objUpFile->temp_dir . $objUpFile->temp_file[$arrImageKey[$from_key]];
        } elseif($objUpFile->save_file[$arrImageKey[$from_key]]){
            $from_path = $objUpFile->save_dir . $objUpFile->save_file[$arrImageKey[$from_key]];
        }

        if(file_exists($from_path)) {
            // 生成先の画像サイズを取得
            $to_w = $objUpFile->width[$arrImageKey[$to_key]];
            $to_h = $objUpFile->height[$arrImageKey[$to_key]];

            if($forced) $objUpFile->save_file[$arrImageKey[$to_key]] = "";

            if(empty($objUpFile->temp_file[$arrImageKey[$to_key]])
                    && empty($objUpFile->save_file[$arrImageKey[$to_key]])) {
                // リネームする際は、自動生成される画像名に一意となるように、Suffixを付ける
                $dst_file = $objUpFile->lfGetTmpImageName(IMAGE_RENAME, "", $objUpFile->temp_file[$arrImageKey[$from_key]]) . $this->lfGetAddSuffix($to_key);
                $path = $objUpFile->makeThumb($from_path, $to_w, $to_h, $dst_file);
                $objUpFile->temp_file[$arrImageKey[$to_key]] = basename($path);
            }
        }
    }

    /**
     * アップロードファイルパラメーター情報から削除
     * 一時ディレクトリに保存されている実ファイルも削除する
     *
     * @param object $objUpFile SC_UploadFileインスタンス
     * @param string $image_key 画像ファイルキー
     * @return void
     */
    function lfDeleteTempFile(&$objUpFile, $image_key) {
        // TODO: SC_UploadFile::deleteFileの画像削除条件見直し要
        $arrTempFile = $objUpFile->temp_file;
        $arrKeyName = $objUpFile->keyname;

        foreach($arrKeyName as $key => $keyname) {
            if($keyname != $image_key) continue;

            if(!empty($arrTempFile[$key])) {
                $temp_file = $arrTempFile[$key];
                $arrTempFile[$key] = '';

                if(!in_array($temp_file, $arrTempFile)) {
                    $objUpFile->deleteFile($image_key);
                } else {
                    $objUpFile->temp_file[$key] = '';
                    $objUpFile->save_file[$key] = '';
                }
            } else {
                $objUpFile->temp_file[$key] = '';
                $objUpFile->save_file[$key] = '';
            }
        }
    }

    /**
     * アップロードファイルを保存する
     * 
     * @param object $objUpFile SC_UploadFileインスタンス
     * @param object $objDownFile SC_UploadFileインスタンス
     * @param integer $product_id 商品ID
     * @return void
     */
    function lfSaveUploadFiles(&$objUpFile, &$objDownFile, $product_id) {
        // TODO: SC_UploadFile::moveTempFileの画像削除条件見直し要
        $objImage = new SC_Image_Ex($objUpFile->temp_dir);
        $arrKeyName = $objUpFile->keyname;
        $arrTempFile = $objUpFile->temp_file;
        $arrSaveFile = $objUpFile->save_file;
        $arrImageKey = array();
        foreach($arrTempFile as $key => $temp_file) {
            if($temp_file) {
                $objImage->moveTempImage($temp_file, $objUpFile->save_dir);
                $arrImageKey[] = $arrKeyName[$key];
                if(!empty($arrSaveFile[$key])
                        && !$this->lfHasSameProductImage($product_id, $arrImageKey, $arrSaveFile[$key])
                        && !in_array($temp_file, $arrSaveFile)) {
                    $objImage->deleteImage($arrSaveFile[$key], $objUpFile->save_dir);
                }
            }
        }
        $objDownFile->moveTempDownFile();
    }

    /**
     * 同名画像ファイル登録の有無を確認する.
     *
     * 画像ファイルの削除可否判定用。
     * 同名ファイルの登録がある場合には画像ファイルの削除を行わない。
     * 戻り値： 同名ファイル有り(true) 同名ファイル無し(false)
     *
     * @param string $product_id 商品ID
     * @param string $arrImageKey 対象としない画像カラム名
     * @param string $image_file_name 画像ファイル名
     * @return boolean
     */
    function lfHasSameProductImage($product_id, $arrImageKey, $image_file_name) {
        if (!SC_Utils_Ex::sfIsInt($product_id)) return false;
        if (!$arrImageKey) return false;
        if (!$image_file_name) return false;

        $arrWhere = array();
        $sqlval = array('0', $product_id);
        foreach ($arrImageKey as $image_key) {
            $arrWhere[] = "{$image_key} = ?";
            $sqlval[] = $image_file_name;
        }
        $where = implode(" OR ", $arrWhere);
        $where = "del_flg = ? AND ((product_id <> ? AND ({$where}))";

        $arrKeyName = $this->objUpFile->keyname;
        foreach ($arrKeyName as $key => $keyname) {
            if (in_array($keyname, $arrImageKey)) continue;
            $where .= " OR {$keyname} = ?";
            $sqlval[] = $image_file_name;
        }
        $where .= ")";

        $objQuery = new SC_Query_Ex();
        $count = $objQuery->count('dtb_products', $where, $sqlval);
        if (!$count) return false;
        return true;
    }

    /**
     * DBから商品データを取得する
     * 
     * @param integer $product_id 商品ID
     * @return array 商品データ配列
     */
    function lfGetProductData_FromDB($product_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrProduct = array();

        // 商品データ取得
        $col =<<< __EOF__
T1.*, T2.*,
DATE_FORMAT(T1.sale_start_date, '%Y/%m/%d') AS sale_start_date,
DATE_FORMAT(T1.sale_end_date, '%Y/%m/%d') AS sale_end_date,
DATE_FORMAT(T1.disp_start_date, '%Y/%m/%d') AS disp_start_date,
bd.brand_id,
bd.brand_code,
bd.brand_name
__EOF__;
        $table = <<< __EOF__
                      dtb_products AS T1
            LEFT JOIN (
                       SELECT product_id AS product_id_sub,
                              product_type_id,
                              product_code,
                              stock,
                              stock_unlimited,
                              sale_limit,
                              sale_minimum_number,
                              price01,
                              price02,
                              deliv_judgment,
                              point_rate,
                              sample_flg,
                              present_flg,
                              sell_flg,
                              teiki_flg,
                              stock_status_name,
                              course_cd
                        FROM dtb_products_class
                       ) AS T2
                     ON T1.product_id = T2.product_id_sub
            LEFT JOIN dtb_brand AS bd
                     ON T1.brand_id = bd.brand_id
                    AND bd.del_flg = 0
__EOF__;
        $where = "product_id = ?";
        $objQuery->setLimit('1');
        $arrProduct = $objQuery->select($col, $table, $where, array($product_id));

        // カテゴリID取得
        $col = "category_id";
        $table = "dtb_product_categories";
        $where = "product_id = ?";
        $objQuery->setOption('');
        $arrProduct[0]['category_id'] = $objQuery->getCol($col, $table, $where, array($product_id));

        // 規格情報ありなしフラグ取得
        $objDb = new SC_Helper_DB_Ex();
        $arrProduct[0]['has_product_class'] = $objDb->sfHasProductClass($product_id);

        // 規格が登録されていなければ規格ID取得
        if ($arrProduct[0]['has_product_class'] == false) {
            $arrProduct[0]['product_class_id'] = SC_Utils_Ex::sfGetProductClassId($product_id,"0","0");
        }

        // 商品ステータス取得
        $objProduct = new SC_Product_Ex();
        $pcProductStatus = $objProduct->getProductStatus
            (array($product_id), DEVICE_TYPE_PC);
        $arrProduct[0]['pc_product_status'] = $pcProductStatus[$product_id];
        $spProductStatus = $objProduct->getProductStatus
            (array($product_id), DEVICE_TYPE_SMARTPHONE);
        $arrProduct[0]['sp_product_status'] = $spProductStatus[$product_id];
        $mbProductStatus = $objProduct->getProductStatus
            (array($product_id), DEVICE_TYPE_MOBILE);
        $arrProduct[0]['mb_product_status'] = $mbProductStatus[$product_id];

        // 関連商品データ取得
        $arrRecommend = $this->lfGetRecommendProductsData_FromDB($product_id);
        $arrProduct[0] = array_merge($arrProduct[0], $arrRecommend);

        return $arrProduct[0];
    }

    /**
     * DBから関連商品データを取得する
     * 
     * @param integer $product_id 商品ID
     * @return array 関連商品データ配列
     */
    function lfGetRecommendProductsData_FromDB($product_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrRecommendProducts = array();

        $col = 'recommend_product_id,';
        $col.= 'comment';
        $table = 'dtb_recommend_products';
        $where = 'product_id = ?';
        $objQuery->setOrder("rank DESC");
        $arrRet = $objQuery->select($col, $table, $where, array($product_id));

        $no = 1;
        foreach ($arrRet as $arrVal) {
            $arrRecommendProducts['recommend_id' . $no] = $arrVal['recommend_product_id'];
            $arrRecommendProducts['recommend_comment' . $no] = $arrVal['comment'];
            $no++;
        }

        return $arrRecommendProducts;
    }

    /**
     * 関連商品データ表示用配列を取得する
     * 
     * @param string $arrForm フォーム入力パラメーター配列
     * @return array 関連商品データ配列
     */
    function lfGetRecommendProducts(&$arrForm) {
        $arrRecommend = array();

        for($i = 1; $i <= RECOMMEND_PRODUCT_MAX; $i++) {
            $keyname = "recommend_id" . $i;
            $delkey = "recommend_delete" . $i;
            $commentkey = "recommend_comment" . $i;

            if (!isset($arrForm[$delkey])) $arrForm[$delkey] = null;

            if((isset($arrForm[$keyname]) && !empty($arrForm[$keyname])) && $arrForm[$delkey] != 1) {
                $objProduct = new SC_Product_Ex();
                $arrRecommend[$i] = $objProduct->getDetail($arrForm[$keyname]);
                $arrRecommend[$i]['product_id'] = $arrForm[$keyname];
                $arrRecommend[$i]['comment'] = $arrForm[$commentkey];
            }
        }
        return $arrRecommend;
    }

    /**
     * 表示用カテゴリマスターデータ配列を取得する
     * - 編集モード
     * 
     * @param void
     * @return array カテゴリマスターデータ配列
     */
    function lfGetCategoryList_Edit() {
        $objDb = new SC_Helper_DB_Ex();
        $arrCategoryList = array();

        list($arrCatVal, $arrCatOut) = $objDb->sfGetLevelCatList(false);
        for ($i = 0; $i < count($arrCatVal); $i++) {
            $arrCategoryList[$arrCatVal[$i]] = $arrCatOut[$i];
        }

        return $arrCategoryList;
    }

    /**
     * 入力フォームパラメーターの規格ありなしフラグを判定
     * 
     * @param string $has_product_class 入力フォームパラメーターの規格ありなしフラグ
     * @return boolean true: 規格あり, false: 規格なし
     */
    function lfGetProductClassFlag($has_product_class) {
        if ($has_product_class == '1') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * ページonload用JavaScriptを取得する
     * - 入力画面
     *
     * @param string $anchor_hash アンカー用ハッシュ文字列(省略可)
     * @return string ページonload用JavaScript
     * 2012/06/05 nagata 964行目　追加
     */
    function lfSetOnloadJavaScript_InputPage($anchor_hash = "") {
        //header('Cache-Control: no-store');
        //return "fnCheckStockLimit('" . DISABLED_RGB . "'); fnMoveSelect('category_id_unselect', 'category_id');" . $anchor_hash;
        return "fnCheckStockLimit('" . DISABLED_RGB . "'); " . $anchor_hash;
    }

    /**
     * DBに商品データを登録する
     * 
     * @param object $objUpFile SC_UploadFileインスタンス
     * @param object $objDownFile SC_UploadFileインスタンス
     * @param array $arrList フォーム入力パラメーター配列
     * @return integer 登録商品ID
     */
    function lfRegistProduct(&$objUpFile, &$objDownFile, $arrList) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objDb = new SC_Helper_DB_Ex();

        // 配列の添字を定義
        $checkArray = array
	    ('name', 'disp_name', 'sales_name', 'status', 'brand_id',
	     'comment3', 'main_list_comment', 'main_comment',
	     'pc_comment1', 'pc_comment2', 'pc_comment3', 'pc_comment4',
	     'pc_button4', 'pc_comment5', 'pc_button5', 'sp_comment1',
	     'sp_comment2', 'sp_comment3', 'sp_comment4', 'sp_button4',
	     'sp_comment5', 'sp_button5', 'mb_comment1', 'mb_comment2',
	     'mb_comment3', 'mb_comment4', 'mb_comment5',
	     'capacity', 'sale_start_date', 'sale_end_date',
	     'metatag', 'deliv_kbn1', 'deliv_kbn2',
	     'drop_shipment', 'component_flg',
	     'employee_sale_cd', 'not_search_flg',
	     'mail_deliv_id', 'disp_start_date',
	     'deliv_date_id', 'sale_limit', 'sale_minimum_number',
	     'deliv_judgment', 'point_rate', 'sample_flg', 'present_flg',
	     'sell_flg', 'teiki_flg', 'stock_status_name', 'course_cd');

        $arrList = SC_Utils_Ex::arrayDefineIndexes($arrList, $checkArray);

        // INSERTする値を作成する。
        $sqlval['name'] = $arrList['name'];
        $sqlval['disp_name'] = $arrList['disp_name'];
        $sqlval['sales_name'] = $arrList['sales_name'];
        $sqlval['status'] = $arrList['status'];
        $sqlval['brand_id'] = $arrList['brand_id'];
        $sqlval['comment3'] = $arrList['comment3'];
        $sqlval['main_list_comment'] = $arrList['main_list_comment'];
        $sqlval['main_comment'] = $arrList['main_comment'];
        $sqlval['pc_comment1'] = $arrList['pc_comment1'];
        $sqlval['pc_comment2'] = $arrList['pc_comment2'];
        $sqlval['pc_comment3'] = $arrList['pc_comment3'];
        $sqlval['pc_comment4'] = $arrList['pc_comment4'];
        $sqlval['pc_button4'] = $arrList['pc_button4'];
        $sqlval['pc_comment5'] = $arrList['pc_comment5'];
        $sqlval['pc_button5'] = $arrList['pc_button5'];
        $sqlval['sp_comment1'] = $arrList['sp_comment1'];
        $sqlval['sp_comment2'] = $arrList['sp_comment2'];
        $sqlval['sp_comment3'] = $arrList['sp_comment3'];
        $sqlval['sp_comment4'] = $arrList['sp_comment4'];
        $sqlval['sp_button4'] = $arrList['sp_button4'];
        $sqlval['sp_comment5'] = $arrList['sp_comment5'];
        $sqlval['sp_button5'] = $arrList['sp_button5'];
        $sqlval['mb_comment1'] = $arrList['mb_comment1'];
        $sqlval['mb_comment2'] = $arrList['mb_comment2'];
        $sqlval['mb_comment3'] = $arrList['mb_comment3'];
        $sqlval['mb_comment4'] = $arrList['mb_comment4'];
        $sqlval['mb_comment5'] = $arrList['mb_comment5'];
        $sqlval['capacity'] = $arrList['capacity'];
        $sqlval['sale_start_date'] = $arrList['sale_start_date'];
        $sqlval['sale_end_date'] = $arrList['sale_end_date'];
        $sqlval['metatag'] = $arrList['metatag'];
        $sqlval['deliv_kbn1'] = $arrList['deliv_kbn1'];
        $sqlval['deliv_kbn2'] = $arrList['deliv_kbn2'];
        $sqlval['drop_shipment'] = $arrList['drop_shipment'];
        $sqlval['component_flg'] = $arrList['component_flg'];
        $sqlval['employee_sale_cd'] = $arrList['employee_sale_cd'];
        $sqlval['not_search_flg'] = $arrList['not_search_flg'];
        $sqlval['mail_deliv_id'] = $arrList['mail_deliv_id'];
        $sqlval['disp_start_date'] = $arrList['disp_start_date'];
        $sqlval['deliv_date_id'] = $arrList['deliv_date_id'];
        $sqlval['updator_id'] = $_SESSION['member_id'];
        $sqlval['update_date'] = "Now()";
        $arrRet = $objUpFile->getDBFileList();
        $sqlval = array_merge($sqlval, $arrRet);

        $objQuery->begin();

        // 新規登録
        if ($arrList['product_id'] == "") {
            $product_id = $objQuery->nextVal("dtb_products_product_id");
            $sqlval['product_id'] = $product_id;

            // INSERTの実行
            $sqlval['creator_id'] = $_SESSION['member_id'];
            $sqlval['create_date'] = "Now()";
            $objQuery->insert("dtb_products", $sqlval);

            $arrList['product_id'] = $product_id;

            // カテゴリを更新
            $objDb->updateProductCategories
                ($arrList['category_id'], $product_id);
        } else {    // 更新
            $product_id = $arrList['product_id'];
            // 削除要求のあった既存ファイルの削除
            $arrRet = $this->lfGetProductData_FromDB($arrList['product_id']);
            // TODO: SC_UploadFile::deleteDBFileの画像削除条件見直し要
            $objImage = new SC_Image_Ex($objUpFile->temp_dir);
            $arrKeyName = $objUpFile->keyname;
            $arrSaveFile = $objUpFile->save_file;
            $arrImageKey = array();

            foreach ($arrKeyName as $key => $keyname) {
                if ($arrRet[$keyname] && !$arrSaveFile[$key]) {
                    $arrImageKey[] = $keyname;
                    $has_same_image = $this->lfHasSameProductImage
                        ($arrList['product_id'],
                         $arrImageKey, $arrRet[$keyname]);
                    if (!$has_same_image) {
                        $objImage->deleteImage
                            ($arrRet[$keyname], $objUpFile->save_dir);
                    }
                }
            }
            $objDownFile->deleteDBDownFile($arrRet);
 
           // UPDATEの実行
            $where = "product_id = ?";
            $objQuery->update("dtb_products",
                              $sqlval, $where, array($product_id));

            // カテゴリを更新
            $objDb->updateProductCategories
                ($arrList['category_id'], $product_id);
        }

        // 商品登録の時は規格を生成する。
        if ($objDb->sfHasProductClass($product_id)) {
            // 規格あり商品（商品規格テーブルのうち、商品登録フォームで
            // 設定するパラメーターのみ更新）
            $this->lfUpdateProductClass($arrList);
        } else {
            // 規格なし商品（商品規格テーブルの更新）
            $this->lfInsertDummyProductClass($arrList);
        }

        // ステータス設定
        $objProduct = new SC_Product_Ex();
        $objProduct->setProductStatus
            ($product_id,
             $arrList['pc_product_status'], DEVICE_TYPE_PC);
        $objProduct->setProductStatus
            ($product_id,
             $arrList['sp_product_status'], DEVICE_TYPE_SMARTPHONE);
        $objProduct->setProductStatus
            ($product_id,
             $arrList['mb_product_status'], DEVICE_TYPE_MOBILE);

        // 関連商品登録
        $this->lfInsertRecommendProducts($objQuery, $arrList, $product_id);

        $objQuery->commit();

        return $product_id;
    }

    /**
     * 規格を設定していない商品を商品規格テーブルに登録
     *
     * @param array $arrList
     * @return void
     */
    function lfInsertDummyProductClass($arrList) {
        $objQuery = new SC_Query_Ex();
        $objDb = new SC_Helper_DB_Ex();

        $product_id = $arrList['product_id'];

        // 配列の添字を定義
        $checkArray = array(
            'product_class_id',
            'product_id',
            'product_type_id',
            'product_code',
            'stock',
            'stock_unlimited',
            'sale_limit',
            'sale_minimum_number',
            'price01',
            'price02',
            'deliv_judgment',
            'point_rate',
            'sample_flg',
            'present_flg',
            'sell_flg',
            'teiki_flg',
            'stock_status_name',
            'course_cd');
        $sqlval = SC_Utils_Ex::sfArrayIntersectKeys($arrList, $checkArray);
        $sqlval = SC_Utils_Ex::arrayDefineIndexes($sqlval, $checkArray);

        $sqlval['stock_unlimited'] =
            $sqlval['stock_unlimited'] ? UNLIMITED_FLG_UNLIMITED
                                       : UNLIMITED_FLG_LIMITED;

        if (strlen($sqlval['deliv_judgment']) == 0 ||
            $sqlval['deliv_judgment'] == null) {
            $sqlval['deliv_judgment'] = DELIV_JUDGMENT_DEFAULT_VALUE;
        }
        $sqlval['updator_id'] = $_SESSION['member_id'];
        $sqlval['update_date'] = 'now()';

        if (strlen($sqlval['product_class_id']) == 0) {
            $sqlval['product_class_id'] =
                $objQuery->nextVal('dtb_products_class_product_class_id');
            $sqlval['creator_id'] = $_SESSION['member_id'];
            $sqlval['create_date'] = 'now()';
            // INSERTの実行
            $objQuery->insert('dtb_products_class', $sqlval);
        } else {
            // UPDATEの実行
            $objQuery->update
                ('dtb_products_class', $sqlval, "product_class_id = ?",
                 array($sqlval['product_class_id']));
        }
    }

    /**
     * 規格を設定している商品の商品規格テーブルを更新
     * (point_rate, sale_limit)
     *
     * @param array $arrList
     * @return void
     */
    function lfUpdateProductClass($arrList) {
        $objQuery = new SC_Query_Ex();
        $sqlval = array();
        
        $sqlval['sale_limit'] = $arrList['sale_limit'];
        $sqlval['sale_minimum_number'] = $arrList['sale_minimum_number'];

        if (strlen($arrList['deliv_judgment']) == 0 ||
            $arrList['deliv_judgment'] == null) {
            $sqlval['deliv_judgment'] = DELIV_JUDGMENT_DEFAULT_VALUE;
        } else {
            $sqlval['deliv_judgment'] = $arrList['deliv_judgment'];
        }

        $sqlval['point_rate'] = $arrList['point_rate'];
        $sqlval['sample_flg'] = $arrList['sample_flg'];
        $sqlval['present_flg'] = $arrList['present_flg'];
        $sqlval['sell_flg'] = $arrList['sell_flg'];
        $sqlval['teiki_flg'] = $arrList['teiki_flg'];
        $sqlval['stock_status_name'] = $arrList['stock_status_name'];
        $sqlval['course_cd'] = $arrList['course_cd'];

        $where = 'product_id = ?';
        $objQuery->update('dtb_products_class',
                          $sqlval, $where, array($arrList['product_id']));
    }

    /**
     * DBに関連商品データを登録する
     * 
     * @param object $objQuery SC_Queryインスタンス
     * @param string $arrList フォーム入力パラメーター配列
     * @param integer $product_id 登録する商品ID
     * @return void
     */
    function lfInsertRecommendProducts(&$objQuery, $arrList, $product_id) {
        // 一旦関連商品をすべて削除する
        $objQuery->delete("dtb_recommend_products", "product_id = ?", array($product_id));
        $sqlval['product_id'] = $product_id;
        $rank = RECOMMEND_PRODUCT_MAX;
        for($i = 1; $i <= RECOMMEND_PRODUCT_MAX; $i++) {
            $keyname = "recommend_id" . $i;
            $commentkey = "recommend_comment" . $i;
            $deletekey = "recommend_delete" . $i;

            if (!isset($arrList[$deletekey])) $arrList[$deletekey] = null;

            if($arrList[$keyname] != "" && $arrList[$deletekey] != '1') {
                $sqlval['recommend_product_id'] = $arrList[$keyname];
                $sqlval['comment'] = $arrList[$commentkey];
                $sqlval['rank'] = $rank;
                $sqlval['creator_id'] = $_SESSION['member_id'];
                $sqlval['create_date'] = "now()";
                $sqlval['update_date'] = "now()";
                $objQuery->insert("dtb_recommend_products", $sqlval);
                $rank--;
            }
        }
    }

    /**
     * リネームする際は、自動生成される画像名に一意となるように、Suffixを付ける
     * 
     * @param string $to_key
     * @return string 
     */
    function lfGetAddSuffix($to_key){
        if( IMAGE_RENAME === true ) return ;

        // 自動生成される画像名
        $dist_name = "";
        switch($to_key) {
        // 2013.12.03 add 案内画像追加
        case "guide_image":
            $dist_name = '_g';
            break;
        case "guide_image_teiki":
            $dist_name = '_gt';
            break;
        case "main_list_image":
            $dist_name = '_s';
            break;
        case "main_image":
            $dist_name = '_m';
            break;
        default:
            $arrRet = explode('sub_image', $to_key);
            $dist_name = '_sub' .$arrRet[1];
            break;
        }
        return $dist_name;
    }

    /**
     * サブ情報の登録があるかを取得する
     * タイトル, コメント, 画像のいずれかに登録があれば「あり」と判定する
     * 
     * @param array $arrSubProductData サブ情報配列
     * @return boolean true: サブ情報あり, false: サブ情報なし
     */
    function hasSubProductData($arrSubProductData) {
        $has_subproduct_data = false;

        for($i = 1; $i <= PRODUCTSUB_MAX; $i++) {
            if(SC_Utils_Ex::isBlank($arrSubProductData['sub_title'.$i]) == false
                    || SC_Utils_Ex::isBlank($arrSubProductData['sub_comment'.$i]) == false
                    || SC_Utils_Ex::isBlank($arrSubProductData['sub_image'.$i]) == false
                    || SC_Utils_Ex::isBlank($arrSubProductData['sub_large_image'.$i]) == false
                    || SC_Utils_Ex::isBlank($arrSubProductData['temp_sub_image'.$i]) == false
                    || SC_Utils_Ex::isBlank($arrSubProductData['temp_sub_large_image'.$i]) == false) {
                $has_subproduct_data = true;
                break;
            }
        }

        return $has_subproduct_data;
    }

    /**
     * アンカーハッシュ文字列を取得する
     * アンカーキーをサニタイジングする
     * 
     * @param string $anchor_key フォーム入力パラメーターで受け取ったアンカーキー
     * @return <type> 
     */
    function getAnchorHash($anchor_key) {
        if($anchor_key != "") {
            return "location.hash='#" . htmlspecialchars($anchor_key) . "'";
        } else {
            return "";
        }
    }

    /**
     * 日付入力チェックを行う
     * 
     * @param string $date
     * @return boolean 
     */
    function checkDateYYYYMMDD($date) {
        if(!empty($date)){
            // 日付形式チェック
            if(!preg_match('/^[0-9]{4}\/[0-9]{1,2}\/[0-9]{1,2}$/', $date)){
                return false;
            }else{
                // 日付妥当性チェック
                list($year, $momth, $day) = preg_split("/\//", $date);
                if(!checkdate($momth, $day, $year)){
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * ブランドデータが存在するかを取得する
     *
     * @param integer $brand_code ブランドコード
     * @return integer 0:なし、1以上:あり
     */
    function lfExistsBrand($brand_code) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<< __EOS
SELECT COUNT(*) AS count
  FROM dtb_brand
 WHERE del_flg = 0
   AND brand_code = '{$brand_code}'
__EOS;

        $results = $objQuery->getAll($sql);

        return $results[0]['count'];
    }
}
?>
