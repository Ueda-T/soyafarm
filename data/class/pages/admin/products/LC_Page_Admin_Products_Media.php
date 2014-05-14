<?php
// {{{ requires
require_once (CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php');

/**
 * 広告媒体マスタ登録 のページクラス
 *
 * @package Page
 * @author IQUEVE Co.,Ltd.
 * @version $Id: LC_Page_Admin_Products_Media.php 368 2014-01-10 06:25:38Z kaji $
 */
class LC_Page_Admin_Products_Media extends LC_Page_Admin_Ex
{
    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/media.tpl';

        $this->tpl_complete = 'products/media_complete.tpl';
        $this->tpl_confirm = 'products/media_confirm.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'media_search';
        $this->tpl_maintitle = '広告媒体管理';
        $this->tpl_subtitle = '広告媒体登録';
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
                $media_id = $this->lfRegistMedia($this->arrForm);
                // 完了画面表示設定
                $this->tpl_mainpage = $this->tpl_complete;
            }
            break;

        // 確認ページからの戻り
        case 'confirm_return':
            break;

        default:
            if ($this->arrForm['media_id'] != "") {
                /* 編集モード */
                $this->arrForm =
                    $this->lfGetMedia($this->arrForm['media_id']);
            }
            break;
        }
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
     *
     * @param object $objFormParam SC_FormParamインスタンス
     * @param array $arrPost $_POSTデータ
     * @return void
     */
    function lfInitFormParam(&$objFormParam, $arrPost) {
        $objFormParam->addParam
            ("広告媒体ID", "media_id", INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("広告媒体コード", "media_code", INT_LEN, 'n',
             array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("広告媒体名", 'media_name', STEXT_LEN, '',
             array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));

        // 検索条件
        $objFormParam->addParam
            ("広告媒体コード(FROM)", "search_media_code_from", INT_LEN, 'n',
             array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("広告媒体コード(TO)", "search_media_code_to", INT_LEN, 'n',
             array("MAX_LENGTH_CHECK"));
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

        // 広告媒体コードの重複チェック
        $media_id   = $objFormParam->getValue('media_id');
        $media_code = $objFormParam->getValue('media_code');
        if (!empty($media_code)) {
            if ($this->lfExistsMedia($media_id, $media_code) > 0) {
                $arrErr['media_code'] =
                    '広告媒体コードが重複しています。<br />';
            }
        }

        return $arrErr;
    }

    /**
     * DBに広告媒体データを登録する
     * 
     * @param array $arrList フォーム入力パラメーター配列
     * @return integer 登録企画ID
     */
    function lfRegistMedia($arrList) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // トランザクションを開始
        $objQuery->begin();

        // 広告媒体IDを持っているかどうかで、新規か更新かを判断する
        $media_id = 0;
        $sql = '';
        if ($arrList['media_id'] == "") {
            /* 新規登録 */
            $media_id = $objQuery->nextVal("dtb_media_media_id");

            // 新規SQL
            $sql =<<< __EOS
INSERT INTO dtb_media (
    media_id
  , media_code
  , media_name
  , creator_id
  , create_date
  , update_id
  , update_date
) VALUES (
    {$media_id}
  , {$arrList['media_code']}
  , '{$arrList['media_name']}'
  , {$_SESSION['member_id']}
  , NOW()
  , {$_SESSION['member_id']}
  , NOW()
)
__EOS;
        } else {
            /* 更新 */
            $media_id = $arrList['media_id'];

            // 更新SQL
            $sql =<<< __EOS
UPDATE dtb_media
   SET media_code  = {$arrList['media_code']}
     , media_name  = '{$arrList['media_name']}'
     , update_id   = {$_SESSION['member_id']}
     , update_date = NOW()
 WHERE media_id    = {$media_id}
__EOS;
        }

        // 実行
        $objQuery->exec($sql);

        // トランザクション終了
        $objQuery->commit();

        return $media_id;
    }

    /**
     * DBから広告媒体データを取得する
     * 
     * @param integer $media_id 広告媒体ID
     * @return array 広告媒体データ
     */
    function lfGetMedia($media_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<< __EOS
SELECT *
  FROM dtb_media
 WHERE del_flg = 0
   AND media_id = {$media_id}
__EOS;

        $results = $objQuery->getAll($sql);

        return $results[0];
    }

    /**
     * 広告媒体データが存在するかを取得する
     *
     * @param integer $media_id 広告媒体ID
     * @param integer $media_code 広告媒体コード
     * @return integer 0:なし、1以上:あり
     */
    function lfExistsMedia($media_id, $media_code) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $addWhere = '';
        if ($media_id > 0) {
            $addWhere = '   AND media_id <> ' . $media_id;
        }

        $sql =<<< __EOS
SELECT COUNT(*) AS count
  FROM dtb_media
 WHERE del_flg = 0
   AND media_code = '{$media_code}'
{$addWhere}
__EOS;

        $results = $objQuery->getAll($sql);

        return $results[0]['count'];
    }
}
?>
