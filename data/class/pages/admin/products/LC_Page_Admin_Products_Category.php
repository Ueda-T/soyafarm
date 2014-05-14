<?php
require_once (CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php');

/**
 * カテゴリ登録 のページクラス
 *
 * @package Page
 * @author IQUEVE Co.,Ltd.
 * @version $Id: LC_Page_Admin_Products_Category.php 1182 2014-03-10 08:13:39Z moriuchi $
 */
class LC_Page_Admin_Products_Category extends LC_Page_Admin_Ex
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/category.tpl';

        $this->tpl_complete = 'products/category_complete.tpl';
        $this->tpl_confirm = 'products/category_confirm.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'category_search';
        $this->tpl_maintitle = 'カテゴリ管理';
        $this->tpl_subtitle = 'カテゴリ登録';

        $this->arrStatus = array(0 => '通常', 1 => '廃止');
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
        $objUpFile = new SC_UploadFile_Ex
            (IMAGE_TEMP_REALDIR, IMAGE_SAVE_REALDIR);
        $this->lfInitFile($objUpFile);
        $objUpFile->setHiddenFileList($_POST);

        // パラメーター初期化, 取得
        $this->lfInitFormParam_UploadImage($objFormParam);
        $this->lfInitFormParam($objFormParam, $_POST);
        $this->arrForm = $objFormParam->getHashArray();

        // 検索パラメーター引き継ぎ
        $this->arrSearchHidden = $objFormParam->getSearchArray();

        $mode = $this->getMode();
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
                // DBへデータ登録
                $this->lfRegistCategory($objUpFile, $this->arrForm);
                // 完了画面表示設定
                $this->tpl_mainpage = $this->tpl_complete;
                // 一時画像ファイルを本番画像ディレクトリに移動する
                $this->lfSaveUploadFiles($objUpFile);
            }
            break;

        // 画像のアップロード
        case 'upload_image':
        case 'delete_image':
            switch ($mode) {
            case 'upload_image':
                // 画像サイズ取得
                if ($_FILES["image"]["tmp_name"]) {
                    $arrSize = getimagesize($_FILES["image"]["tmp_name"]);
                    // アップされた画像サイズのまま保存する
                    $this->lfInitFile($objUpFile, $arrSize[0], $arrSize[1]);
                }
                // ファイルを一時ディレクトリにアップロード
                $this->arrErr[$this->arrForm['image_key']] =
                    $objUpFile->makeTempFile($this->arrForm['image_key'],
                                             IMAGE_RENAME);
                /* リサイズしないようにコメントアウト
                if ($this->arrErr[$this->arrForm['image_key']] == "") {
                    // 縮小画像作成
                    $this->lfSetScaleImage
                        ($objUpFile, $this->arrForm['image_key']);
                }
                 */
                break;

            case 'delete_image':
                // ファイル削除
                $this->lfDeleteTempFile
                    ($objUpFile, $this->arrForm['image_key']);
                break;
            }
            break;

        // 確認ページからの戻り
        case 'confirm_return':
            break;

        default:
            if ($this->arrForm['category_id'] != "") {
                /* 編集モード */
                $this->arrForm =
                    $this->lfGetCategory($this->arrForm['category_id']);
                // DBデータから画像ファイル名の読込
                $objUpFile->setDBFileList($this->arrForm);
            }
            break;
        }

        // 画像ファイル表示用データ取得
        $this->arrForm['arrHidden'] = $objUpFile->getHiddenFileList();
        $this->arrForm['arrFile'] = $objUpFile->getFormFileList
            (IMAGE_TEMP_URLPATH, IMAGE_SAVE_URLPATH);
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
            ("カテゴリID", "category_id", INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("カテゴリ名", 'category_name', STEXT_LEN, '',
             array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("カテゴリコード", 'category_code', CATEGORY_CODE_LEN, '',
             array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("親カテゴリID", "parent_category_id", INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("ランク", 'rank', INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("状態", 'status', INT_LEN, 'n',
             array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));

        $objFormParam->addParam
            ("save_image", "save_image", '', "", array());
        $objFormParam->addParam
            ("temp_image", "temp_image", '', "", array());

        $objFormParam->addParam
            ("METAタグ", 'metatag', LLTEXT_LEN, '',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));

        $objFormParam->setParam($arrPost);
        $objFormParam->convParam();
    }

    /**
     * アップロードファイルパラメーター情報の初期化
     * - 画像ファイル用
     * 
     * @param object $objUpFile SC_UploadFileインスタンス
     * @param string $width     画像サイズ（横）
     * @param string $height    画像サイズ（縦）
     * @return void
     */
    function lfInitFile(&$objUpFile, $width = NORMAL_IMAGE_WIDTH
                        , $height = NORMAL_IMAGE_HEIGHT) {
        $objUpFile->addFile("カテゴリ画像", 'image',
                            array('jpg', 'gif', 'png'),
                            IMAGE_SIZE, false,
                            $width, $height);
    }

    /**
     * 縮小した画像をセットする
     * 
     * @param object $objUpFile SC_UploadFileインスタンス
     * @param string $image_key 画像ファイルキー
     * @return void
     */
    function lfSetScaleImage(&$objUpFile, $image_key){
        switch ($image_key) {
        case "image":
            $this->lfMakeScaleImage($objUpFile, $image_key, "image");
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
    function lfMakeScaleImage(&$objUpFile,
                              $from_key, $to_key, $forced = false) {
        $arrImageKey = array_flip($objUpFile->keyname);
        $from_path = "";

        if ($objUpFile->temp_file[$arrImageKey[$from_key]]) {
            $from_path = $objUpFile->temp_dir .
                $objUpFile->temp_file[$arrImageKey[$from_key]];
        } elseif($objUpFile->save_file[$arrImageKey[$from_key]]){
            $from_path = $objUpFile->save_dir .
                $objUpFile->save_file[$arrImageKey[$from_key]];
        }

        if (file_exists($from_path)) {
            // 生成先の画像サイズを取得
            $to_w = $objUpFile->width[$arrImageKey[$to_key]];
            $to_h = $objUpFile->height[$arrImageKey[$to_key]];

            if ($forced) $objUpFile->save_file[$arrImageKey[$to_key]] = "";

            if (empty($objUpFile->temp_file[$arrImageKey[$to_key]]) &&
                empty($objUpFile->save_file[$arrImageKey[$to_key]])) {
                // リネームする際は、自動生成される画像名に一意となるように、
                // Suffixを付ける
                $dst_file = $objUpFile->lfGetTmpImageName
                    (IMAGE_RENAME, "",
                     $objUpFile->temp_file[$arrImageKey[$from_key]]) .
                    $this->lfGetAddSuffix($to_key);
                $path = $objUpFile->makeThumb
                    ($from_path, $to_w, $to_h, $dst_file);
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
     * @return void
     */
    function lfSaveUploadFiles(&$objUpFile) {
        $objImage = new SC_Image_Ex($objUpFile->temp_dir);
        $arrKeyName = $objUpFile->keyname;
        $arrTempFile = $objUpFile->temp_file;
        $arrSaveFile = $objUpFile->save_file;
        $arrImageKey = array();

        foreach ($arrTempFile as $key => $temp_file) {
            if ($temp_file) {
                $objImage->moveTempImage($temp_file, $objUpFile->save_dir);
            }
        }
    }

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

        // カテゴリコードの重複チェック
        $category_id   = $objFormParam->getValue('category_id');
        $category_code = $objFormParam->getValue('category_code');
        if (!empty($category_code)) {
            if ($this->lfExistsCategory($category_id, $category_code) > 0) {
                $arrErr['category_code'] =
                    'カテゴリコードが重複しています。<br />';
            }
        }

        return $arrErr;
    }

    /**
     * DBにカテゴリを登録する
     * 
     * @param object $objUpFile SC_UploadFileインスタンス
     * @param array $arrList フォーム入力パラメーター配列
     * @return integer 登録企画ID
     */
    function lfRegistCategory(&$objUpFile, $arrList) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // 画像ファイル名をセットする
        $r = $objUpFile->getDBFileList();
        $arrList['image'] = $r['image'];

        // トランザクションを開始
        $objQuery->begin();

        // カテゴリIDを持っているかどうかで、新規か更新かを判断する
        $category_id = 0;
        $sql = '';
        if ($arrList['category_id'] == "") {
            /* 新規登録 */
            $category_id = $objQuery->nextVal("dtb_category_category_id");

            // 自分のレベルを取得する(親のレベル + 1)
            $parent_category_id = $arrList['parent_category_id'];
            $where = "category_id = ?";
            $level = $objQuery->get('level', "dtb_category",
                                    $where, array($parent_category_id)) + 1;
            $arrList['level'] = $level;

            // ランクを取得する
            $rank = 0;
            if ($parent_category_id == 0) {
                // ROOT階層で最大のランクを取得する。
                $where = "parent_category_id = ?";
                $rank = $objQuery->max('rank', "dtb_category",
                                       $where, array($parent_category_id)) + 1;
            } else {
                // 親のランクを自分のランクとする。
                $where = "category_id = ?";
                $rank = $objQuery->get('rank', "dtb_category",
                                       $where, array($parent_category_id));
                // 追加レコードのランク以上のレコードを一つあげる。
                $sqlup =<<< __EOS
UPDATE dtb_category
   SET rank = (rank + 1)
 WHERE rank >= ?
__EOS;
                $objQuery->exec($sqlup, array($rank));
            }
            $arrList['rank'] = $rank;

            // 新規SQL
            $sql =<<< __EOS
INSERT INTO dtb_category (
    category_id
  , category_name
  , category_code
  , parent_category_id
  , level
  , rank
  , status
  , image
  , metatag
  , creator_id
  , create_date
  , updator_id
  , update_date
) VALUES (
    {$category_id}
  , '{$arrList['category_name']}'
  , '{$arrList['category_code']}'
  , {$arrList['parent_category_id']}
  , {$arrList['level']}
  , {$arrList['rank']}
  , IF ('{$arrList['status']}' = '', NULL, '{$arrList['status']}')
  , IF ('{$arrList['image']}' = '', NULL, '{$arrList['image']}')
  , '{$arrList['metatag']}'
  , {$_SESSION['member_id']}
  , NOW()
  , {$_SESSION['member_id']}
  , NOW()
)
__EOS;
        } else {
            /* 更新 */
            $category_id = $arrList['category_id'];

            // 更新SQL
            $sql =<<< __EOS
UPDATE dtb_category
   SET category_name = '{$arrList['category_name']}'
     , category_code = '{$arrList['category_code']}'
     , rank          = {$arrList['rank']}
     , status        =
       IF ('{$arrList['status']}' = '', NULL, '{$arrList['status']}')
     , image         =
       IF ('{$arrList['image']}' = '', NULL, '{$arrList['image']}')
     , metatag          = '{$arrList['metatag']}'
     , updator_id    = {$_SESSION['member_id']}
     , update_date   = NOW()
 WHERE category_id   = {$category_id}
__EOS;
        }

        // 実行
        $objQuery->exec($sql);

        // トランザクション終了
        $objQuery->commit();

        return $category_id;
    }

    /**
     * DBからカテゴリを取得する
     * 
     * @param integer $category_id カテゴリID
     * @return array カテゴリデータ
     */
    function lfGetCategory($category_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<< __EOS
SELECT category_id
     , category_code
     , category_name
     , parent_category_id
     , level
     , rank
     , status
     , image
     , metatag
  FROM dtb_category
 WHERE del_flg = 0
   AND category_id = {$category_id}
__EOS;

        $results = $objQuery->getAll($sql);

        return $results[0];
    }

    /**
     * カテゴリデータが存在するかを取得する
     *
     * @param integer $category_id カテゴリID
     * @param integer $category_code カテゴリコード
     * @return integer 0:なし、1以上:あり
     */
    function lfExistsCategory($category_id, $category_code) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $addWhere = '';
        if ($category_id > 0) {
            $addWhere = '   AND category_id <> ' . $category_id;
        }

        $sql =<<< __EOS
SELECT COUNT(*) AS count
  FROM dtb_category
 WHERE del_flg = 0
   AND category_code = '{$category_code}'
{$addWhere}
__EOS;

        $results = $objQuery->getAll($sql);

        return $results[0]['count'];
    }
}
?>
