<?php
require_once (CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php');

/**
 * ブランドマスタ登録 のページクラス
 *
 * @package Page
 * @author IQUEVE Co.,Ltd.
 * @version $Id: LC_Page_Admin_Products_Brand.php 1182 2014-03-10 08:13:39Z moriuchi $
 */
class LC_Page_Admin_Products_Brand extends LC_Page_Admin_Ex
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/brand.tpl';

        $this->tpl_complete = 'products/brand_complete.tpl';
        $this->tpl_confirm = 'products/brand_confirm.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'brand_search';
        $this->tpl_maintitle = 'ブランド管理';
        $this->tpl_subtitle = 'ブランド登録';
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

        // パラメーター初期化, 取得
        $this->lfInitFormParam_UploadImage($objFormParam);
        $this->lfInitFormParam($objFormParam, $_POST);
        $this->arrForm = $objFormParam->getHashArray();

        // 検索パラメーター引き継ぎ
        $this->arrSearchHidden = $objFormParam->getSearchArray();

        $mode = $this->getMode();
        $brand_id = $this->arrForm['brand_id'];

        switch($mode) {
        case 'edit':
            // エラーチェック
            $this->arrErr = $this->lfCheckError($objFormParam);
            if (count($this->arrErr) == 0) {
                // 確認画面表示設定
                $this->tpl_mainpage = $this->tpl_confirm;
            }
            break;

        case 'complete':
            // エラーチェック
            $this->arrErr = $this->lfCheckError($objFormParam);
            if (count($this->arrErr) == 0) {
                // DBへデータ登録情報編集
                $this->lfRegistBrandDataEdit($this->arrForm);
                // DBへデータ登録
                $this->lfRegistBrand($file, $this->arrForm);
                // 完了画面表示設定
                $this->tpl_mainpage = $this->tpl_complete;
            }
            break;

        // 確認ページからの戻り
        case 'confirm_return':
            break;

        default:
            if (!empty($brand_id)) {
                /* 編集モード */
                $this->arrForm = $this->lfGetBrand($brand_id);
            }
            break;
        }

        // オリジナルのブランド商品コードをカンマ区切りで連結した文字列
        $this->arrForm['org_product_cds'] =
            $this->lfGetOrgBrandProductsCode($brand_id);

        // ブランド商品一覧を取得
        $this->arrBrandProducts = $this->lfGetBrandProducts($brand_id);
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
     *
     * @param object $objFormParam SC_FormParamインスタンス
     * @param array $arrPost $_POSTデータ
     * @return void
     */
    function lfInitFormParam(&$objFormParam, $arrPost) {
        $objFormParam->addParam
            ("ブランドID", "brand_id", INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("ブランドコード", 'brand_code', BRAND_CODE_LEN, '',
             array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("ブランド名", 'brand_name', STEXT_LEN, '',
             array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("掲載開始日", 'disp_start_date', STEXT_LEN, 'a',
             array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("掲載終了日", 'disp_end_date', STEXT_LEN, 'a',
             array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("カテゴリID", 'category_id', INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("カテゴリコード", 'category_code', CATEGORY_CODE_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("カテゴリ名", 'category_name', STEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("親ブランドID", 'parent_id', INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("親ブランドコード", 'parent_brand_code', BRAND_CODE_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("親ブランド名", 'parent_brand_name', STEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("並び順", 'rank', INT_LEN, 'n',
             array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));

        $objFormParam->addParam
            ("商品表示件数", 'product_disp_num', INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("画像表示件数", 'img_disp_num', INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));

        $objFormParam->addParam
            ("METAタグ", 'metatag', LLTEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));

        $objFormParam->addParam
            ("選択中タブ", 'select_tab_index', INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));

        // PC情報の初期化
        $this->lfInitFormParamPc($objFormParam);

        // スマホ(SP)情報の初期化
        $this->lfInitFormParamSp($objFormParam);

        // モバイル(MB)情報の初期化
        $this->lfInitFormParamMb($objFormParam);

        // オリジナルブランド商品
        $objFormParam->addParam
            ("ORG商品IDS", 'org_product_cds', 0, '', array("SPTAB_CHECK"));
        // 追加対象ブランド商品
        $objFormParam->addParam
            ("追加商品IDS", 'add_product_cds', 0, '', array("SPTAB_CHECK"));
        // 削除対象ブランド商品
        $objFormParam->addParam
            ("削除商品IDS", 'del_product_cds', 0, '', array("SPTAB_CHECK"));

        // 検索条件
        $objFormParam->addParam
            ("ブランドコード", "search_brand_code", STEXT_LEN, '',
             array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("ブランド名", 'search_brand_name', STEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("ページ送り番号", "search_pageno", INT_LEN, 'n',
             array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam
            ("表示件数", "search_page_max", INT_LEN, 'n',
             array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->setParam($arrPost);
        $objFormParam->convParam();
    }

    /**
     * PCパラメーター情報の初期化
     *
     * @param object $objFormParam SC_FormParamインスタンス
     * @return void
     */
    function lfInitFormParamPc(&$objFormParam) {
        $objFormParam->addParam
            ("PCコメント", 'pc_comment', LLTEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("PCフリースペース１", 'pc_free_space1', LLTEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("PCフリースペース２", 'pc_free_space2', LLTEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("PCフリースペース３", 'pc_free_space3', LLTEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("PCフリースペース４", 'pc_free_space4', LLTEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("PCフリースペース５", 'pc_free_space5', LLTEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    }

    /**
     * スマホ(SP)パラメーター情報の初期化
     *
     * @param object $objFormParam SC_FormParamインスタンス
     * @return void
     */
    function lfInitFormParamSp(&$objFormParam) {
        $objFormParam->addParam
            ("SPコメント", 'sp_comment', LLTEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("SPフリースペース１", 'sp_free_space1', LLTEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("SPフリースペース２", 'sp_free_space2', LLTEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("SPフリースペース３", 'sp_free_space3', LLTEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("SPフリースペース４", 'sp_free_space4', LLTEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("SPフリースペース５", 'sp_free_space5', LLTEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    }

    /**
     * モバイル(mb)パラメーター情報の初期化
     *
     * @param object $objFormParam SC_FormParamインスタンス
     * @return void
     */
    function lfInitFormParamMb(&$objFormParam) {
        $objFormParam->addParam
            ("MBコメント", 'mb_comment', LLTEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("MBフリースペース１", 'mb_free_space1', LLTEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("MBフリースペース２", 'mb_free_space2', LLTEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("MBフリースペース３", 'mb_free_space3', LLTEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("MBフリースペース４", 'mb_free_space4', LLTEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("MBフリースペース５", 'mb_free_space5', LLTEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    }

    /**
     * アンカーハッシュ文字列を取得する
     * アンカーキーをサニタイジングする
     * 
     * @param string $anchor_key フォーム入力パラメータで受け取ったアンカーキー
     * @return <type> 
    function getAnchorHash($anchor_key) {
        if ($anchor_key != "") {
            return "location.hash='#" . htmlspecialchars($anchor_key) . "'";
        } else {
            return "";
        }
    }
     */

    /**
     * フォーム入力パラメーターのエラーチェック
     * 
     * @param object $objFormParam SC_FormParamインスタンス
     * @return array エラー情報を格納した連想配列
     */
    function lfCheckError(&$objFormParam) {
        // 入力パラメーターチェック
        $arrErr = $objFormParam->checkError();
        if (count($arrErr) > 0) {
            return $arrErr;
        }

        // ブランドコードの重複チェック
        $brand_id   = $objFormParam->getValue('brand_id');
        $brand_code = $objFormParam->getValue('brand_code');
        if (!empty($brand_code)) {
            if ($this->lfExistsBrand($brand_id, $brand_code) > 0) {
                $arrErr['brand_code'] =
                    'ブランドコードが重複しています。<br />';
            }
        }

        // 所属カテゴリが入力されている場合は、存在チェックを行う
        $category_code = $objFormParam->getValue('category_code');
        if (!empty($category_code)) {
            if ($this->lfExistsCategory($category_code) == 0) {
                $arrErr['category_code'] =
                    'カテゴリが見つかりません。<br />';
            }
        }

        // 親ブランドが入力されている場合は、存在チェックを行う
        $parent_brand_code = $objFormParam->getValue('parent_brand_code');
        if (!empty($parent_brand_code)) {
            if ($this->lfExistsBrand($brand_id, $parent_brand_code) == 0) {
                $arrErr['parent_brand_code'] =
                    'ブランドが見つかりません。<br />';
            }
        }

        return $arrErr;
    }

    /**
     * DBにブランドデータを登録する
     * 
     * @param object $objUpFile SC_UploadFileインスタンス
     * @param array $arrList フォーム入力パラメーター配列
     * @return integer 登録ブランドID
     */
    function lfRegistBrand(&$objUpFile, $arrList) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // トランザクションを開始
        $objQuery->begin();

        // ブランドIDを持っているかどうかで、新規か更新かを判断する
        $brand_id = 0;
        $sql = '';
        if ($arrList['brand_id'] == "") {
            /* 新規登録 */
            $brand_id = $objQuery->nextVal("dtb_brand_brand_id");

            // 新規SQL
            $sql =<<< __EOS
INSERT INTO dtb_brand (
    brand_id
  , brand_name
  , brand_code
  , rank
  , disp_start_date
  , disp_end_date
  , category_id
  , product_disp_num
  , img_disp_num
  , metatag
  , parent_id
  , pc_comment
  , pc_free_space1
  , pc_free_space2
  , pc_free_space3
  , pc_free_space4
  , pc_free_space5
  , sp_comment
  , sp_free_space1
  , sp_free_space2
  , sp_free_space3
  , sp_free_space4
  , sp_free_space5
  , mb_comment
  , mb_free_space1
  , mb_free_space2
  , mb_free_space3
  , mb_free_space4
  , mb_free_space5
  , creator_id
  , create_date
  , updator_id
  , update_date
) VALUES (
    {$brand_id}
  , '{$arrList['brand_name']}'
  , '{$arrList['brand_code']}'
  , {$arrList['rank']}
  , '{$arrList['disp_start_date']}'
  , '{$arrList['disp_end_date']}'
  , IF ('{$arrList['category_id']}' = '', NULL, '{$arrList['category_id']}')
  , IF ('{$arrList['product_disp_num']}' = '',
        NULL, '{$arrList['product_disp_num']}')
  , IF ('{$arrList['img_disp_num']}' = '', NULL, '{$arrList['img_disp_num']}')
  , IF ('{$arrList['metatag']}' = '', NULL, '{$arrList['metatag']}')
  , IF ('{$arrList['parent_id']}' = '', NULL, '{$arrList['parent_id']}')
  , IF ('{$arrList['pc_comment']}' = '', NULL, '{$arrList['pc_comment']}')
  , IF ('{$arrList['pc_free_space1']}' = '',
        NULL, '{$arrList['pc_free_space1']}')
  , IF ('{$arrList['pc_free_space2']}' = '',
        NULL, '{$arrList['pc_free_space2']}')
  , IF ('{$arrList['pc_free_space3']}' = '',
        NULL, '{$arrList['pc_free_space3']}')
  , IF ('{$arrList['pc_free_space4']}' = '',
        NULL, '{$arrList['pc_free_space4']}')
  , IF ('{$arrList['pc_free_space5']}' = '',
        NULL, '{$arrList['pc_free_space5']}')
  , IF ('{$arrList['sp_comment']}' = '', NULL, '{$arrList['sp_comment']}')
  , IF ('{$arrList['sp_free_space1']}' = '',
        NULL, '{$arrList['sp_free_space1']}')
  , IF ('{$arrList['sp_free_space2']}' = '',
        NULL, '{$arrList['sp_free_space2']}')
  , IF ('{$arrList['sp_free_space3']}' = '',
        NULL, '{$arrList['sp_free_space3']}')
  , IF ('{$arrList['sp_free_space4']}' = '',
        NULL, '{$arrList['sp_free_space4']}')
  , IF ('{$arrList['sp_free_space5']}' = '',
        NULL, '{$arrList['sp_free_space5']}')
  , IF ('{$arrList['mb_comment']}' = '', NULL, '{$arrList['mb_comment']}')
  , IF ('{$arrList['mb_free_space1']}' = '',
        NULL, '{$arrList['mb_free_space1']}')
  , IF ('{$arrList['mb_free_space2']}' = '',
        NULL, '{$arrList['mb_free_space2']}')
  , IF ('{$arrList['mb_free_space3']}' = '',
        NULL, '{$arrList['mb_free_space3']}')
  , IF ('{$arrList['mb_free_space4']}' = '',
        NULL, '{$arrList['mb_free_space4']}')
  , IF ('{$arrList['mb_free_space5']}' = '',
        NULL, '{$arrList['mb_free_space5']}')
  , {$_SESSION['member_id']}
  , NOW()
  , {$_SESSION['member_id']}
  , NOW()
)
__EOS;
        } else {
            /* 更新 */
            $brand_id = $arrList['brand_id'];

            // 更新SQL
            $sql =<<< __EOS
UPDATE dtb_brand
   SET brand_name = '{$arrList['brand_name']}'
     , brand_code = '{$arrList['brand_code']}'
     , rank =  {$arrList['rank']}
     , disp_start_date = '{$arrList['disp_start_date']}'
     , disp_end_date = '{$arrList['disp_end_date']}'
     , category_id =
       IF ('{$arrList['category_id']}' = '', NULL, '{$arrList['category_id']}')
     , product_disp_num =
       IF ('{$arrList['product_disp_num']}' = '',
           NULL, '{$arrList['product_disp_num']}')
     , img_disp_num =
       IF ('{$arrList['img_disp_num']}' = '',
           NULL, '{$arrList['img_disp_num']}')
     , metatag =
       IF ('{$arrList['metatag']}' = '',
           NULL, '{$arrList['metatag']}')
     , parent_id =
       IF ('{$arrList['parent_id']}' = '', NULL, '{$arrList['parent_id']}')
     , pc_comment =
       IF ('{$arrList['pc_comment']}' = '', NULL, '{$arrList['pc_comment']}')
     , pc_free_space1 =
       IF ('{$arrList['pc_free_space1']}' = '',
           NULL, '{$arrList['pc_free_space1']}')
     , pc_free_space2 =
       IF ('{$arrList['pc_free_space2']}' = '',
           NULL, '{$arrList['pc_free_space2']}')
     , pc_free_space3 =
       IF ('{$arrList['pc_free_space3']}' = '',
           NULL, '{$arrList['pc_free_space3']}')
     , pc_free_space4 =
       IF ('{$arrList['pc_free_space4']}' = '',
           NULL, '{$arrList['pc_free_space4']}')
     , pc_free_space5 =
       IF ('{$arrList['pc_free_space5']}' = '',
           NULL, '{$arrList['pc_free_space5']}')
     , sp_comment =
       IF ('{$arrList['sp_comment']}' = '', NULL, '{$arrList['sp_comment']}')
     , sp_free_space1 =
       IF ('{$arrList['sp_free_space1']}' = '',
           NULL, '{$arrList['sp_free_space1']}')
     , sp_free_space2 =
       IF ('{$arrList['sp_free_space2']}' = '',
           NULL, '{$arrList['sp_free_space2']}')
     , sp_free_space3 =
       IF ('{$arrList['sp_free_space3']}' = '',
           NULL, '{$arrList['sp_free_space3']}')
     , sp_free_space4 =
       IF ('{$arrList['sp_free_space4']}' = '',
           NULL, '{$arrList['sp_free_space4']}')
     , sp_free_space5 =
       IF ('{$arrList['sp_free_space5']}' = '',
           NULL, '{$arrList['sp_free_space5']}')
     , mb_comment =
       IF ('{$arrList['mb_comment']}' = '', NULL, '{$arrList['mb_comment']}')
     , mb_free_space1 =
       IF ('{$arrList['mb_free_space1']}' = '',
           NULL, '{$arrList['mb_free_space1']}')
     , mb_free_space2 =
       IF ('{$arrList['mb_free_space2']}' = '',
           NULL, '{$arrList['mb_free_space2']}')
     , mb_free_space3 =
       IF ('{$arrList['mb_free_space3']}' = '',
           NULL, '{$arrList['mb_free_space3']}')
     , mb_free_space4 =
       IF ('{$arrList['mb_free_space4']}' = '',
           NULL, '{$arrList['mb_free_space4']}')
     , mb_free_space5 =
       IF ('{$arrList['mb_free_space5']}' = '',
           NULL, '{$arrList['mb_free_space5']}')
     , updator_id = {$_SESSION['member_id']}
     , update_date = NOW()
 WHERE brand_id = {$brand_id}
__EOS;
        }

        // 実行
        $objQuery->exec($sql);

        // ブランド商品を追加
        $product_cds = explode(",", $this->arrForm['add_product_cds']);
        for ($i = 0; $i < count($product_cds); ++$i) {
            if (!empty($product_cds[$i])) {
                $this->lfInsertBrandProduct($brand_id, $product_cds[$i]);
            }
        }

        // ブランド商品を削除
        $product_cds = explode(",", $this->arrForm['del_product_cds']);
        for ($i = 0; $i < count($product_cds); ++$i) {
            if (!empty($product_cds[$i])) {
                $this->lfDeleteBrandProduct($brand_id, $product_cds[$i]);
            }
        }

        // トランザクション終了
        $objQuery->commit();

        return $brand_id;
    }

    /**
     * DBに登録するデータを登録用に編集
     * 
     * @param array $arrList フォーム入力パラメーター配列
     * @return integer 登録ブランドID
     */
    function lfRegistBrandDataEdit(&$arrList) {

        if (strlen($arrList['metatag'])) {
            $arrList['metatag'] = addslashes($arrList['metatag']);
        }
        if (strlen($arrList['pc_comment'])) {
            $arrList['pc_comment'] = addslashes($arrList['pc_comment']);
        }
        if (strlen($arrList['pc_free_space1'])) {
            $arrList['pc_free_space1'] = addslashes($arrList['pc_free_space1']);
        }
        if (strlen($arrList['pc_free_space2'])) {
            $arrList['pc_free_space2'] = addslashes($arrList['pc_free_space2']);
        }
        if (strlen($arrList['pc_free_space3'])) {
            $arrList['pc_free_space3'] = addslashes($arrList['pc_free_space3']);
        }
        if (strlen($arrList['pc_free_space4'])) {
            $arrList['pc_free_space4'] = addslashes($arrList['pc_free_space4']);
        }
        if (strlen($arrList['pc_free_space5'])) {
            $arrList['pc_free_space5'] = addslashes($arrList['pc_free_space5']);
        }
        if (strlen($arrList['sp_comment'])) {
            $arrList['sp_comment'] = addslashes($arrList['sp_comment']);
        }
        if (strlen($arrList['sp_free_space1'])) {
            $arrList['sp_free_space1'] = addslashes($arrList['sp_free_space1']);
        }
        if (strlen($arrList['sp_free_space2'])) {
            $arrList['sp_free_space2'] = addslashes($arrList['sp_free_space2']);
        }
        if (strlen($arrList['sp_free_space3'])) {
            $arrList['sp_free_space3'] = addslashes($arrList['sp_free_space3']);
        }
        if (strlen($arrList['sp_free_space4'])) {
            $arrList['sp_free_space4'] = addslashes($arrList['sp_free_space4']);
        }
        if (strlen($arrList['sp_free_space5'])) {
            $arrList['sp_free_space5'] = addslashes($arrList['sp_free_space5']);
        }
        if (strlen($arrList['mb_comment'])) {
            $arrList['mb_comment'] = addslashes($arrList['mb_comment']);
        }
        if (strlen($arrList['mb_free_space1'])) {
            $arrList['mb_free_space1'] = addslashes($arrList['mb_free_space1']);
        }
        if (strlen($arrList['mb_free_space2'])) {
            $arrList['mb_free_space2'] = addslashes($arrList['mb_free_space2']);
        }
        if (strlen($arrList['mb_free_space3'])) {
            $arrList['mb_free_space3'] = addslashes($arrList['mb_free_space3']);
        }
        if (strlen($arrList['mb_free_space4'])) {
            $arrList['mb_free_space4'] = addslashes($arrList['mb_free_space4']);
        }
        if (strlen($arrList['mb_free_space5'])) {
            $arrList['mb_free_space5'] = addslashes($arrList['mb_free_space5']);
        }
    }

    /**
     * DBからブランドマスタデータを取得する
     * 
     * @param integer $brand_id ブランドID
     * @return array ブランドマスタデータ
     */
    function lfGetBrand($brand_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<< __EOS
SELECT bd.brand_id
     , bd.brand_name
     , bd.brand_code
     , bd.rank
     , DATE_FORMAT(bd.disp_start_date, '%Y/%m/%d') AS disp_start_date
     , DATE_FORMAT(bd.disp_end_date, '%Y/%m/%d') AS disp_end_date
     , bd.category_id
     , cg.category_code
     , cg.category_name
     , bd.product_disp_num
     , bd.img_disp_num
     , bd.metatag
     , bd.parent_id
     , pb.brand_code AS parent_brand_code
     , pb.brand_name AS parent_brand_name
     , bd.pc_comment
     , bd.pc_free_space1
     , bd.pc_free_space2
     , bd.pc_free_space3
     , bd.pc_free_space4
     , bd.pc_free_space5
     , bd.sp_comment
     , bd.sp_free_space1
     , bd.sp_free_space2
     , bd.sp_free_space3
     , bd.sp_free_space4
     , bd.sp_free_space5
     , bd.mb_comment
     , bd.mb_free_space1
     , bd.mb_free_space2
     , bd.mb_free_space3
     , bd.mb_free_space4
     , bd.mb_free_space5
  FROM dtb_brand bd
  LEFT JOIN dtb_category cg
    ON bd.category_id = cg.category_id
   AND cg.del_flg = 0
  LEFT JOIN dtb_brand pb
    ON bd.parent_id = pb.brand_id
   AND pb.del_flg = 0
 WHERE bd.del_flg = 0
   AND bd.brand_id = {$brand_id}
__EOS;

        $results = $objQuery->getAll($sql);

        return $results[0];
    }

    /**
     * DBからブランド商品コードを取得する
     * 
     * @param integer $brand_id ブランドID
     * @return string カンマ区切りブランド商品コード
     */
    function lfGetOrgBrandProductsCode($brand_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        if (empty($brand_id)) {
            return "";
        }

        $sql =<<< __EOS
SELECT bp.product_code
  FROM dtb_brand_products bp
 WHERE bp.del_flg = 0
   AND bp.brand_id = {$brand_id}
__EOS;

        $results = array();

        $r = $objQuery->getAll($sql);
        foreach ($r as $row) {
            $results[] = $row['product_code'];
        }

        return implode(",", $results);
    }

    /**
     * DBからブランド商品マスタデータを取得する
     * 
     * @param integer $brand_id ブランドID
     * @return array ブランド商品マスタデータ
     */
    function lfGetBrandProducts($brand_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql = '';

        if (empty($brand_id)) {
            if (empty($this->arrForm['add_product_cds'])) {
                return array();
            }

            $cds = $this->arrForm['add_product_cds'];
            $cds = str_replace(",", "','", $cds);

            $sql =<<< __EOS
SELECT pc.product_code
     , pd.name AS product_name
     , cg.name AS product_class_name
  FROM dtb_products_class pc
 INNER JOIN dtb_products pd
    ON pc.product_id = pd.product_id
   AND pd.del_flg = 0
  LEFT JOIN dtb_class_combination cc
    ON pc.class_combination_id = cc.class_combination_id
  LEFT JOIN dtb_classcategory cg
    ON cc.classcategory_id = cg.classcategory_id
   AND cg.del_flg = 0
 WHERE pc.del_flg = 0
   AND pc.product_code IN ('{$cds}')
__EOS;
        } else {
            $fmt =<<< __EOS
SELECT bp.product_code
     , pd.name AS product_name
     , cg.name AS product_class_name
  FROM dtb_brand_products bp
  LEFT JOIN dtb_products_class pc
    ON bp.product_code = pc.product_code
   AND pc.del_flg = 0
  LEFT JOIN dtb_products pd
    ON pc.product_id = pd.product_id
   AND pd.del_flg = 0
  LEFT JOIN dtb_class_combination cc
    ON pc.class_combination_id = cc.class_combination_id
  LEFT JOIN dtb_classcategory cg
    ON cc.classcategory_id = cg.classcategory_id
   AND cg.del_flg = 0
 WHERE bp.del_flg = 0
   AND bp.brand_id = {$brand_id}
%s
%s
__EOS;

            $cond1 = '';
            if (!empty($this->arrForm['del_product_cds'])) {
                $cds = $this->arrForm['del_product_cds'];
                $cds = str_replace(",", "','", $cds);
                $cond1 =<<< __EOS
   AND bp.product_code NOT IN ('{$cds}')
__EOS;
            }
            $cond2 = '';
            if (!empty($this->arrForm['add_product_cds'])) {
                $cds = $this->arrForm['add_product_cds'];
                $cds = str_replace(",", "','", $cds);
                $cond2 =<<< __EOS
 UNION
SELECT pc.product_code
     , pd.name AS product_name
     , cg.name AS product_class_name
  FROM dtb_products_class pc
 INNER JOIN dtb_products pd
    ON pc.product_id = pd.product_id
   AND pd.del_flg = 0
  LEFT JOIN dtb_class_combination cc
    ON pc.class_combination_id = cc.class_combination_id
  LEFT JOIN dtb_classcategory cg
    ON cc.classcategory_id = cg.classcategory_id
   AND cg.del_flg = 0
 WHERE pc.del_flg = 0
   AND pc.product_code IN ('{$cds}')
__EOS;
            }
            $sql = sprintf($fmt, $cond1, $cond2);
        }

        $results = $objQuery->getAll($sql);

        return $results;
    }

    /**
     * DBにブランド商品マスタデータを追加する
     * 
     * @param integer $brand_id ブランドID
     * @param integer $product_code 商品コード
     */
    function lfInsertBrandProduct($brand_id, $product_code) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<< __EOS
INSERT INTO dtb_brand_products (
    brand_id
  , product_code
  , creator_id
  , create_date
  , updator_id
  , update_date
) VALUES (
    {$brand_id}
  , '{$product_code}'
  , {$_SESSION['member_id']}
  , NOW()
  , {$_SESSION['member_id']}
  , NOW()
)
__EOS;

        // 実行
        $objQuery->exec($sql);
    }

    /**
     * DBからブランド商品マスタデータを削除する
     * 
     * @param integer $brand_id ブランドID
     * @param integer $product_code 商品コード
     */
    function lfDeleteBrandProduct($brand_id, $product_code) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<< __EOS
DELETE FROM dtb_brand_products
 WHERE brand_id     = {$brand_id}
   AND product_code = '{$product_code}'
__EOS;

        // 実行
        $objQuery->exec($sql);
    }

    /**
     * ブランドデータが存在するかを取得する
     *
     * @param integer $brand_id ブランドID
     * @param integer $brand_code ブランドコード
     * @return integer 0:なし、1以上:あり
     */
    function lfExistsBrand($brand_id, $brand_code) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $addWhere = '';
        if ($brand_id > 0) {
            $addWhere = '   AND brand_id <> ' . $brand_id;
        }

        $sql =<<< __EOS
SELECT COUNT(*) AS count
  FROM dtb_brand
 WHERE del_flg = 0
   AND brand_code = '{$brand_code}'
{$addWhere}
__EOS;

        $results = $objQuery->getAll($sql);

        return $results[0]['count'];
    }

    /**
     * カテゴリデータが存在するかを取得する
     *
     * @param string $category_code カテゴリコード
     * @return integer 0:なし、1以上:あり
     */
    function lfExistsCategory($category_code) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<< __EOS
SELECT COUNT(*) AS count
  FROM dtb_category
 WHERE del_flg = 0
   AND category_code = '{$category_code}'
__EOS;

        $results = $objQuery->getAll($sql);

        return $results[0]['count'];
    }
}
?>
