<?php
// {{{ requires
require_once (CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php');

/**
 * 企画マスタ登録 のページクラス
 *
 * @package Page
 * @author IQUEVE Co.,Ltd.
 * @version $Id: LC_Page_Admin_Products_Planning.php 327 2014-01-07 12:46:19Z nagata $
 */
class LC_Page_Admin_Products_Planning extends LC_Page_Admin_Ex
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
        $this->tpl_mainpage = 'products/planning.tpl';

        $this->tpl_complete = 'products/planning_complete.tpl';
        $this->tpl_confirm = 'products/planning_confirm.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'planning_search';
        $this->tpl_maintitle = '企画管理';
        $this->tpl_subtitle = '企画登録';

        $this->arrPlanningType = array(1 => 'キャンペーン用',
                                       2 => 'アンケート用');
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
                $planning_id = $this->lfRegistPlanning($this->arrForm);
                // 完了画面表示設定
                $this->tpl_mainpage = $this->tpl_complete;
            }
            break;

        // 確認ページからの戻り
        case 'confirm_return':
            break;

        default:
            if ($this->arrForm['planning_id'] != "") {
                /* 編集モード */
                $this->arrForm =
                    $this->lfGetPlanning($this->arrForm['planning_id']);
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
            ("企画ID", "planning_id", INT_LEN, 'n',
             array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("企画名", 'planning_name', STEXT_LEN, '',
             array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("開始日", 'start_date', STEXT_LEN, 'a',
             array("SPTAB_CHECK", "DATE_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("終了日", 'end_date', STEXT_LEN, 'a',
             array("SPTAB_CHECK", "DATE_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("企画タイプ", 'planning_type', INT_LEN, 'n',
             array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("広告媒体", "media_code", INT_LEN, 'n',
             array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("キャンペーン", 'campaign_code', 4, 'a',
             array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam
            ("並び順", 'rank', INT_LEN, 'n',
             array("EXIST_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));

        // 検索条件
        $objFormParam->addParam("企画タイプ", "search_planning_type",
                                INT_LEN, 'n',
                                array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam("ページ送り番号", "search_pageno",
                                INT_LEN, 'n',
                                array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("表示件数", "search_page_max",
                                INT_LEN, 'n',
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

        return $arrErr;
    }

    /**
     * DBに企画データを登録する
     * 
     * @param array $arrList フォーム入力パラメーター配列
     * @return integer 登録企画ID
     */
    function lfRegistPlanning($arrList) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // トランザクションを開始
        $objQuery->begin();

        // 企画IDを持っているかどうかで、新規か更新かを判断する
        $planning_id = 0;
        $sql = '';
        if ($arrList['planning_id'] == "") {
            /* 新規登録 */
            $planning_id = $objQuery->nextVal("dtb_planning_planning_id");

            // 新規SQL
            $sql =<<< __EOS
INSERT INTO dtb_planning (
    planning_id
  , planning_name
  , rank
  , comment
  , start_date
  , end_date
  , planning_type
  , media_code
  , campaign_code
  , creator_id
  , create_date
  , update_id
  , update_date
) VALUES (
    {$planning_id}
  , '{$arrList['planning_name']}'
  , {$arrList['rank']}
  , '{$arrList['comment']}'
  , IF ('{$arrList['start_date']}' = '', NULL, '{$arrList['start_date']}')
  , IF ('{$arrList['end_date']}'   = '', NULL, '{$arrList['end_date']}')
  , {$arrList['planning_type']}
  , {$arrList['media_code']}
  , '{$arrList['campaign_code']}'
  , {$_SESSION['member_id']}
  , NOW()
  , {$_SESSION['member_id']}
  , NOW()
)
__EOS;
        } else {
            /* 更新 */
            $planning_id = $arrList['planning_id'];

            // 更新SQL
            $sql =<<< __EOS
UPDATE dtb_planning
   SET planning_name = '{$arrList['planning_name']}'
     , rank          = {$arrList['rank']}
     , comment       = '{$arrList['comment']}'
     , start_date    =
       IF ('{$arrList['start_date']}' = '', NULL, '{$arrList['start_date']}')
     , end_date      =
       IF ('{$arrList['end_date']}'   = '', NULL, '{$arrList['end_date']}')
     , planning_type = {$arrList['planning_type']}
     , media_code    = {$arrList['media_code']}
     , campaign_code = '{$arrList['campaign_code']}'
     , update_id     = {$_SESSION['member_id']}
     , update_date   = NOW()
 WHERE planning_id   = {$planning_id}
__EOS;
        }

        // 実行
        $objQuery->exec($sql);

        // トランザクション終了
        $objQuery->commit();

        return $planning_id;
    }

    /**
     * DBから企画データを取得する
     * 
     * @param integer $planning_id 企画ID
     * @return array 企画データ
     */
    function lfGetPlanning($planning_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<< __EOS
SELECT planning_id
     , planning_name
     , rank
     , comment
     , DATE_FORMAT(start_date, '%Y/%m/%d') AS start_date
     , DATE_FORMAT(end_date,   '%Y/%m/%d') AS end_date
     , planning_type
     , media_code
     , campaign_code
  FROM dtb_planning
 WHERE del_flg = 0
   AND planning_id = {$planning_id}
__EOS;

        $results = $objQuery->getAll($sql);

        return $results[0];
    }
}
?>
