<?php
// {{{ requires
require_once (CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php');
require_once CLASS_EX_REALDIR . 'helper_extends/SC_Helper_CSV_Ex.php';
require_once CLASS_EX_REALDIR . 'page_extends/admin/order/LC_Page_Admin_Order_InosExportOrder_Ex.php';

/**
 * INOSシステム連携 定期エクスポートページ のページクラス
 *
 * @package Page
 * @author IQUEVE Co.,Ltd.
 * @version $Id:$
 */
class LC_Page_Admin_Order_InosExportRegular extends LC_Page_Admin_Ex
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
        $this->tpl_mainpage = 'order/inos_export_regular.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'inos_export_regular';
        $this->tpl_pager = 'pager.tpl';
        $this->tpl_maintitle = '受注関連';
        $this->tpl_subtitle = '定期エクスポート';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPageMax = $masterData->getMasterData("mtb_page_max");

        // 支払い方法の取得
        $this->arrPayments = SC_Helper_DB_Ex::sfGetIDValueList
            ("dtb_payment", "payment_id", "payment_method");

        // お届け曜日
        $this->arrTodokeWeekNo =
            $masterData->getMasterData('mtb_todoke_week');

        // XXX 既存のマスタとIDが一致しないため、独自で設定
        //$this->arrTodokeWeek = $masterData->getMasterData('mtb_wday');
        $this->arrTodokeWeek = array(1 => '日',
                                     2 => '月',
                                     3 => '火',
                                     4 => '水',
                                     5 => '木',
                                     6 => '金',
                                     7 => '土');

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

        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam);

        $objPurchase = new SC_Helper_Purchase_Ex();

        $objFormParam->setParam($_POST);

        // 最終出力日時を取得
        $last_send_date =
            SC_Helper_DB_Ex::sfGetLastSendDate(INOS_DATA_TYPE_SEND_REGULAR);
        $objFormParam->setValue('last_send_date', $last_send_date);

        $this->arrHidden = $objFormParam->getSearchArray();
        $this->arrForm = $objFormParam->getFormParamList();

        switch ($this->getMode()) {
        // 検索パラメーター生成後に処理実行するため breakしない
        case 'csv':
        // 検索パラメーターの生成
        case 'search':
            $objFormParam->convParam();
            $objFormParam->trimParam();
            $this->arrErr = $this->lfCheckError($objFormParam);
            $arrParam = $objFormParam->getHashArray();

            if (count($this->arrErr) == 0) {

                // 検索条件
                $where = "rh.send_flg = " . INOS_SEND_FLG_OFF;

                /* -----------------------------------------------
                 * 処理を実行
                 * ----------------------------------------------- */
                switch($this->getMode()) {
                // CSVを送信する。
                case 'csv':

                    // 出力可能なレコードがあるか？
                    $this->tpl_linemax = $this->getNumberOfLines
                        ($where, $arrval);
                    if ($this->tpl_linemax < 1) {
                        $this->tpl_onload =
                            "window.alert('既にエクスポート処理が完了しています。再度検索を行ってください。');";
                        break;
                    }

                    $this->doOutputCSV($where, $this->tpl_linemax);
                    
                    exit;
                    break;
                // 検索実行
                default:
                    // 行数の取得
                    $this->tpl_linemax = $this->getNumberOfLines
                        ($where, $arrval);
		    $orderWhere = "oh.send_flg = 0 AND oh.status = " . ORDER_NEW;
                    $this->tpl_orderCnt = LC_Page_Admin_Order_InosExportOrder_Ex::getNumberOfLines($orderWhere);
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
                    //
                    $this->arrResults = $this->lfSearchRegular
                        ($where, $page_max, $objNavi->start_row);

                }
            }
            break;
        default:
            break;
        }
    }

    /**
     * パラメーター情報の初期化
     *
     * @param object $objFormParam SC_FormParamインスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {

        $objFormParam->addParam("最終出力日時", "last_send_date");

        $objFormParam->addParam("表示件数", "search_page_max",
            INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("ページ送り番号","search_pageno",
            INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));

    }

    /**
     * 検索結果の行数を取得する.
     *
     * @param string $where 検索条件の WHERE 句
     * @param array $arrValues 検索条件のパラメーター
     * @return integer 検索結果の行数
     */
    function getNumberOfLines($where, $arrValues) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<__EOS
select
    count(*)
from
    dtb_regular_order rh
    inner join dtb_regular_order_detail as rd
        on rd.regular_id = rh.regular_id
where
    $where
__EOS;

        return $objQuery->getOne($sql);
    }

    /**
     * エクスポート対象の定期情報一覧を検索する
     *
     * @param string $where WHERE句
     * @param integer $limit 表示件数
     * @param integer $offset 開始件数
     * @return array 定期情報の連想配列
     */
    function lfSearchRegular($where, $limit, $offset) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // 1～3日ごと
        $month_min = COURSE_CD_MONTH_MIN;
        $month_max = COURSE_CD_MONTH_MAX;
        // 20～90日ごと
        $day_min = COURSE_CD_DAY_MIN;
        $day_max = COURSE_CD_DAY_MAX;

        $sql =<<<__EOS
SELECT
    rh.customer_id,
    rh.order_name,
    c.name,
    rd.product_name,
    case
        when rd.course_cd >= {$month_min} and rd.course_cd <= {$month_max}
            then concat(cast(rd.course_cd aS char), 'ヶ月ごと')
        when rd.course_cd >= {$day_min} and rd.course_cd <= {$day_max}
            then concat(cast(rd.course_cd as char), '日ごと')
        else "" end as course_cd,
    rd.todoke_week,
    rd.todoke_week2
from
    dtb_regular_order_detail rd
    inner join dtb_regular_order as rh
        on rh.regular_id = rd.regular_id
    inner join dtb_customer as c
        on c.customer_id = rh.customer_id
where
    $where
order by rd.regular_id
limit $limit offset $offset
__EOS;

        return $objQuery->getAll($sql);
    }

    /**
     *  CSV出力を行う
     *
     * @param  検索条件
     * @param int    $process_count 処理件数
     * @return void
     */
    function doOutputCsv($where, $process_count) {

        // WEB配送業者ID
        $deliv_id_yamato      = DELIV_ID_YAMATO;
        $deliv_id_yamato_mail = DELIV_ID_YAMATO_MAIL;
        $deliv_id_sagawa      = DELIV_ID_SAGAWA;
        
        // INOS配送業者ID
        $inos_deliv_id_yamato = INOS_DELIV_ID_YAMATO;
        $inos_deliv_id_sagawa = INOS_DELIV_ID_SAGAWA;

        $sql =<<<__EOS
select
    rh.regular_base_no,
     c.customer_cd,
    date_format(rh.order_date, '%Y/%m/%d') as order_date,
    rh.order_kana,
    rh.order_name,
    rh.order_tel,
    rh.order_zip,
    rh.order_addr_kana,
    concat(rpref.name, rh.order_addr01) as order_addr01,
    rh.order_addr02,
    rh.status,
    date_format(rh.next_ship_date, '%Y/%m/%d') as next_ship_date,
    date_format(rh.after_next_ship_date, '%Y/%m/%d') as after_next_ship_date,
    rh.shipment_cd,
    case
        when rh.deliv_id = {$deliv_id_yamato} OR
            rh.deliv_id = {$deliv_id_yamato_mail}
                then {$inos_deliv_id_yamato}
        when rh.deliv_id = {$deliv_id_sagawa}
                then {$inos_deliv_id_sagawa}
        else "" end as deliv_id,
    rh.box_size,
    rh.time_id,
    oh.note,
    rh.include_kbn,
    rh.payment_id,
    rh.deliv_fee,
    rh.buy_num,
    rh.order_id,
    rh.del_flg,
    date_format(rh.update_date, '%Y/%m/%d %H:%i:%s') as update_date,
    rd.line_no,
    pc.product_code,
    rd.product_name,
    rd.quantity,
    rd.price,
    rd.course_cd,
    rd.status as detail_status,
    rd.todoke_kbn,
    rd.todoke_day,
    rd.todoke_week,
    rd.todoke_week2,
    date_format(rd.next_arrival_date, '%Y/%m/%d') as next_arrival_date,
    date_format(rd.after_next_arrival_date, '%Y/%m/%d') as after_next_arrival_date,
    date_format(rd.cancel_date, '%Y/%m/%d') as cancel_date,
    rd.cancel_reason_cd
from
    dtb_regular_order_detail rd
    inner join dtb_regular_order as rh
        on rh.regular_id = rd.regular_id
    left join dtb_order as oh
        on oh.order_id = rh.order_id
    inner join dtb_customer as c
        on c.customer_id = rh.customer_id
    inner join dtb_products_class as pc
        on pc.product_class_id = rd.product_class_id
    inner join mtb_pref as rpref
        on rpref.id = rh.order_pref
where
    $where
__EOS;

        // CSVダウンロード実行
        $objCsv = new SC_Helper_CSV_Ex();
        $file_name_head = 'regular';
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
            (INOS_DATA_TYPE_SEND_REGULAR, $send_date);

        // バッチ処理履歴情報へデータ登録
        SC_Helper_DB_Ex::sfInsertBatchHistory
            (INOS_DATA_TYPE_SEND_REGULAR, $process_count, $res);

        // 受注データの送信フラグ,送信日時を更新
        $this->updateRegularSendFlg($where, $send_date);

    }

    /**
     * フォーム入力パラメーターのエラーチェック
     * 
     * @param object $objFormParam SC_FormParamインスタンス
     * @param array $arrPost $_POSTデータ
     * @return array エラー情報を格納した連想配列
     */
    function lfCheckError(&$objFormParam, $arrPost) {
        // 入力パラメーターチェック
        $arrErr = $objFormParam->checkError();

        return $arrErr;
    }

    /**
     * エクスポートしたDBの定期レコードを出力済みへ更新
     *
     * @param string $where     検索条件の WHERE 句
     * @param string $send_date 送信日時
     * @return void
     */
    function updateRegularSendFlg($where, $send_date) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $send_flg = INOS_SEND_FLG_ON;

        $sql = <<<__EOS
UPDATE
    dtb_regular_order rh
SET
    rh.send_flg  = '$send_flg',
    rh.send_date = '$send_date'
WHERE
    $where
__EOS;

        $objQuery->query($sql);
    }

}
?>
