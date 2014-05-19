<?php
// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';
require_once CLASS_EX_REALDIR . 'helper_extends/SC_Helper_CSV_Ex.php';

/**
 * INOSシステム連携 顧客エクスポートページ のページクラス.
 *
 * @package Page
 * @author IQUEVE CO.,LTD.
 * @version $Id:$
 */
class LC_Page_Admin_Customer_InosExportCustomer extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();

        $this->tpl_mainpage = 'customer/inos_export_customer.tpl';
        $this->tpl_mainno = 'customer';
        $this->tpl_subno = 'inos_export_customer';
        $this->tpl_pager = 'pager.tpl';
        $this->tpl_maintitle = '顧客関連';
        $this->tpl_subtitle = '顧客情報エクスポート';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPageMax = $masterData->getMasterData("mtb_page_max");

        $this->httpCacheControl('nocache');

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
        // パラメーター管理クラス
        $objFormParam = new SC_FormParam_Ex();
        // パラメーター設定
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();
        // パラメーター読み込み
        $this->arrForm = $objFormParam->getFormParamList();
        // 検索ワードの引き継ぎ
        $this->arrHidden = $objFormParam->getSearchArray();

        // 最終出力日時を取得
        $last_send_date = SC_Helper_DB_Ex::sfGetLastSendDate
            (INOS_DATA_TYPE_SEND_CUSTOMER);
        $objFormParam->setValue('last_send_date', $last_send_date);
        $this->arrForm = $objFormParam->getFormParamList();

        // 入力パラメーターチェック
        $this->arrErr = $this->lfCheckError($objFormParam);
        if(!SC_Utils_Ex::isBlank($this->arrErr)) {
            return;
        }

        // WHERE句
        $where = "c.send_flg = 0";

        // モードによる処理切り替え
        switch ($this->getMode()) {
        case 'search':
            $objFormParam->convParam();
            $objFormParam->trimParam();

            
            // 行数の取得
            $this->tpl_linemax = $this->getNumberOfLines($where);
            // ページ送りの処理
            $page_max = SC_Utils_Ex::sfGetSearchPageMax
                ($objFormParam->getValue('search_page_max'));
            // ページ送りの取得
            $objNavi = new SC_PageNavi_Ex
                ($this->arrHidden['search_pageno'],
                 $this->tpl_linemax, $page_max,
                 'fnNaviSearchPage',
                 NAVI_PMAX
             );
            $this->arrPagenavi = $objNavi->arrPagenavi;

            // 検索結果の取得
            $this->arrResults = $this->lfSearchCustomer
                ($where, $page_max, $objNavi->start_row);
            break;
        case 'csv':
            // 出力可能なレコードがあるか？
            $this->tpl_linemax = $this->getNumberOfLines($where);
            if ($this->tpl_linemax < 1) {
                $this->tpl_onload =
                    "window.alert('既にエクスポート処理が完了しています。再度検索を行ってください。');";
                break;
            }

            $this->doOutputCSV($where, $this->tpl_linemax);
            exit;
            break;
        default:
            break;
        }
    }

    /**
     * パラメーター情報の初期化
     *
     * @param array $objFormParam フォームパラメータークラス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        SC_Helper_Customer_Ex::sfSetSearchParam($objFormParam);

        $objFormParam->addParam("最終出力日時", "last_send_date");

        $objFormParam->addParam("最終出力日時", "last_send_date");

        $objFormParam->addParam("表示件数", "search_page_max",
            INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("ページ送り番号","search_pageno",
            INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
    }

    /**
     * エラーチェック
     *
     * @param array $objFormParam フォームパラメータークラス
     * @return array エラー配列
     */
    function lfCheckError(&$objFormParam) {
        return SC_Helper_Customer_Ex::sfCheckErrorSearchParam
            ($objFormParam);
    }

    /**
     * 検索結果の行数を取得する.
     *
     * @param string $where 検索条件の WHERE 句
     * @return integer 検索結果の行数
     */
    function getNumberOfLines($where) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<__EOS
select
    count(*)
from
    dtb_customer c
    left join mtb_pref as cpref
        on cpref.id = c.pref
where
    $where
__EOS;

        return $objQuery->getOne($sql);
    }

    /**
     * エクスポート対象の顧客情報一覧を検索する
     *
     * @param string $where WHERE句
     * @param int    $limit
     * @param int    $offset
     * @return array 顧客情報　
     */
    function lfSearchCustomer($where, $limit, $offset) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<__EOS
SELECT
    c.customer_id,
    c.name
from
    dtb_customer c
    left join mtb_pref as cpref
        on cpref.id = c.pref
where
    $where
order by c.customer_id
limit $limit offset $offset
__EOS;

        return $objQuery->getAll($sql);
    }

    /**
     * 顧客一覧CSVを検索してダウンロードする処理
     *
     * @param string $where 検索条件
     * @param int   $process_count 処理件数
     * @return boolean true:成功 false:失敗
     */
    function doOutputCSV($where, $process_count) {
 
        $sql =<<<__EOS
select
    c.customer_cd,
    c.kana,
    c.name,
    c.tel,
    c.zip,
    c.addr_kana,
    concat(ifnull(cpref.name, ""), c.addr01) AS addr01,
    c.addr02,
    c.email,
    date_format(c.birth, '%Y/%m/%d') as birth,
    c.sex,
    c.dm_flg,
    c.tel_flg,
    c.mailmaga_flg,
    c.privacy_kbn,
    c.kashidaore_kbn,
    c.torihiki_id,
    c.customer_id,
    c.customer_type_cd,
    c.del_flg,
    date_format(c.create_date, '%Y/%m/%d %H:%i:%s') as create_date,
    date_format(c.update_date, '%Y/%m/%d %H:%i:%s') as update_date
from
    dtb_customer c
    left join mtb_pref as cpref
        on cpref.id = c.pref
where
    $where
order by c.customer_id asc
__EOS;


        // CSVダウンロード実行
        $objCsv = new SC_Helper_CSV_Ex();
        $file_name_head = 'customer';
        $res = $objCsv->sfDownloadCsvFromSql
            ($sql, $arrval, $file_name_head, "", true);
        if ($res) {
            $res = INOS_ERROR_FLG_EXIST_NORMAL;
        } else {
            $res = INOS_ERROR_FLG_EXIST_ERROR;
        }

        // バッチ処理管理情報を更新
        $send_date = date("Y-m-d H:i:s");
        SC_Helper_DB_Ex::sfUpdateLastSendDate
            (INOS_DATA_TYPE_SEND_CUSTOMER, $send_date);

        // バッチ処理履歴情報へデータ登録
        SC_Helper_DB_Ex::sfInsertBatchHistory
            (INOS_DATA_TYPE_SEND_CUSTOMER, $process_count, $res);

        // 顧客データの送信フラグ,送信日時を更新
        $this->updateCustomerSendFlg($where, $send_date);

    }

    /**
     * エクスポートした顧客データを出力済みへ更新
     *
     * @param string $where     検索条件の WHERE 句
     * @param string $send_date 送信日時
     * @return void
     */
    function updateCustomerSendFlg($where, $send_date) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $send_flg = INOS_SEND_FLG_ON;

        $sql =<<<__EOS
update
    dtb_customer c
set
    c.send_flg  = '$send_flg',
    c.send_date = '$send_date'
where
    $where
__EOS;

        $objQuery->query($sql);
    }

}
?>
