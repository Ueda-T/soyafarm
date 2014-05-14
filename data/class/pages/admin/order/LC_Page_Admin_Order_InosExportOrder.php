<?php
// {{{ requires
require_once (CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php');
require_once CLASS_EX_REALDIR . 'helper_extends/SC_Helper_CSV_Ex.php';

/**
 * INOSシステム連携 受注エクスポートページ のページクラス
 *
 * @package Page
 * @author IQUEVE Co.,Ltd.
 * @version $Id:$
 */
class LC_Page_Admin_Order_InosExportOrder extends LC_Page_Admin_Ex
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
        $this->tpl_mainpage = 'order/inos_export_order.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'inos_export_order';
        $this->tpl_pager = 'pager.tpl';
        $this->tpl_maintitle = '受注関連';
        $this->tpl_subtitle = '受注エクスポート';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPageMax = $masterData->getMasterData("mtb_page_max");

        // 支払い方法の取得
        $this->arrPayments = SC_Helper_DB_Ex::sfGetIDValueList
            ("dtb_payment", "payment_id", "payment_method");

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
            SC_Helper_DB_Ex::sfGetLastSendDate(INOS_DATA_TYPE_SEND_ORDER);
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
                $where = "oh.send_flg = 0 AND oh.status = " . ORDER_NEW;

                /* -----------------------------------------------
                 * 処理を実行
                 * ----------------------------------------------- */
                switch($this->getMode()) {
                // CSVを送信する。
                case 'csv':

                    // 出力可能なレコードがあるか？
                    $this->tpl_linemax = $this->getNumberOfLines($where);
                    $regularLineMax = $this->getRegularNumberOfLines();
                    if ($this->tpl_linemax < 1 && $regularLineMax < 1) {
                        $this->tpl_onload =
                            "window.alert('既にエクスポート処理が完了しています。再度検索を行ってください。');";
                        break;
                    }

		    $arrFile = array();
		    // 受注データCSV生成
                    $arrFile["order"] = $this->doMakeOrderCSV($where, $this->tpl_linemax);
		    // 定期データCSV生成
                    $arrFile["regular"] = $this->doMakeRegularCSV($regularLineMax);

		    $this->doOutputCSV($arrFile);
                    exit;
                    break;
                // 検索実行
                default:
                    // 行数の取得
                    $this->tpl_linemax = $this->getNumberOfLines($where);
                    $this->tpl_regularCnt = $this->getRegularNumberOfLines();
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
                    $this->arrResults = $this->findOrders
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
     *  CSV出力を行う
     *
     * @param string $where 検索条件
     * @param int    $process_count 処理件数
     * @return void
     */
    function doMakeOrderCSV($where, $process_count) {

        // WEB配送業者ID
        $deliv_id_yamato      = DELIV_ID_YAMATO;
        $deliv_id_yamato_mail = DELIV_ID_YAMATO_MAIL;
        $deliv_id_sagawa      = DELIV_ID_SAGAWA;
        
        // INOS配送業者ID
        $inos_deliv_id_yamato = INOS_DELIV_ID_YAMATO;
        $inos_deliv_id_sagawa = INOS_DELIV_ID_SAGAWA;

        // 配送時間指定が無しの場合は0
        $deliv_time_id_none = DELIV_TIME_ID_NONE;

        // 受注データの送信フラグ,送信日時を送信準備中に更新
        $this->updateOrderSendFlg($where, $send_date, 2);

	// 送信準備中のデータを抽出する
	$where = "oh.send_flg = 2";

        $sql =<<<__EOS
select
    oh.order_base_no,
    cs.customer_cd,
    date_format(oh.create_date, '%Y/%m/%d') as create_date,
    sh.shipping_kana,
    sh.shipping_name,
    sh.shipping_tel,
    sh.shipping_zip,
    sh.shipping_addr_kana,
    concat(shpref.name, sh.shipping_addr01) AS addr01,
    sh.shipping_addr02,
    oh.status,
    date_format(sh.shipping_commit_date, '%Y/%m/%d') as shipping_commit_date,
    sh.shipping_area_code,
    case
        when oh.deliv_id = {$deliv_id_yamato} OR
            oh.deliv_id = {$deliv_id_yamato_mail}
                then {$inos_deliv_id_yamato}
        when oh.deliv_id = {$deliv_id_sagawa}
                then {$inos_deliv_id_sagawa}
        else "" end as deliv_id,
    oh.deliv_box_id,
    date_format(sh.shipping_date, '%Y/%m/%d') as shipping_date,
    ifnull(sh.time_id, {$deliv_time_id_none}) as time_id,
    oh.note,
    sh.deliv_kbn,
    sh.cool_kbn,
    sh.shipping_num,
    oh.include_kbn,
    oh.payment_id,
    oh.subtotal,
    oh.deliv_fee,
    oh.use_point as use_point_price,
    oh.payment_total,
    oh.add_point,
    oh.use_point,
    oh.purchase_motive_code,
    oh.input_assistance_code,
    oh.event_code,
    (select kikan_id 
     from mtb_device_type
     where id = oh.device_type_id
    ) as order_kbn,
    oh.regular_base_no,
    oh.return_num,
    oh.return_amount,
    oh.order_id,
    oh.customer_id,
    oh.memo04,
    (select group_concat(op.promotion_cd separator ',')
        from dtb_order_promotion op
        where oh.order_id = op.order_id
    ) as promotion_cd,
    oh.del_flg,
    date_format(oh.update_date, '%Y/%m/%d %H:%i:%s') as update_date,
    (select count(*)+1 from dtb_order_detail as od2
        where od2.order_id = od.order_id
            and od2.order_detail_id < od.order_detail_id) AS line_no,
    od.product_code,
    od.product_name,
    od.quantity,
    od.price,
    od.price * od.quantity AS price_total,
    od.course_cd,
    od.return_quantity
from
    dtb_order as oh
    inner join dtb_shipping as sh
        on sh.order_id = oh.order_id
    inner join dtb_order_detail as od
        on od.order_id = oh.order_id
    inner join dtb_customer as cs
        on cs.customer_id = oh.customer_id
    inner join mtb_pref as shpref
        on shpref.id = sh.shipping_pref
where
    $where
__EOS;

        // CSVダウンロード実行
        $objCsv = new SC_Helper_CSV_Ex();
        $file_name_head = 'order';

        $orderFile = $objCsv->sfDownloadMakeCsvFromSql
            ($sql, $arrval, $file_name_head, "");

        if ($orderFile) {
            $res = INOS_ERROR_FLG_EXIST_NORMAL;
        } else {
            $res = INOS_ERROR_FLG_EXIST_ERROR;
        }

        // バッチ処理管理情報を更新
        $send_date = date("Y-m-d H:i:s");
        SC_Helper_DB_Ex::sfUpdateLastSendDate
            (INOS_DATA_TYPE_SEND_ORDER, $send_date);

        // バッチ処理履歴情報へデータ登録
        SC_Helper_DB_Ex::sfInsertBatchHistory
            (INOS_DATA_TYPE_SEND_ORDER, $process_count, $res);

        // 受注データの送信フラグ,送信日時を更新
        $this->updateOrderSendFlg($where, $send_date, INOS_SEND_FLG_ON);

	return $orderFile;
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
     * 検索結果の行数を取得する.
     *
     * @param string $where 検索条件の WHERE 句
     * @param array $arrValues 検索条件のパラメーター
     * @return integer 検索結果の行数
     */
    function getNumberOfLines($where) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<__EOS
select
    count(*)
from
    dtb_order as oh
where
    $where
__EOS;

        return $objQuery->getOne($sql);
    }

    /**
     * 受注を検索する.
     *
     * @param string $where 検索条件の WHERE 句
     * @param integer $limit 表示件数
     * @param integer $offset 開始件数
     * @return array 受注の検索結果
     */
    function findOrders($where, $limit, $offset) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<__EOS
select
    oh.order_id,
    oh.create_date,
    oh.order_name,
    oh.payment_total,
    oh.payment_id
from
    dtb_order as oh
    inner join dtb_shipping as sh
        on sh.order_id = oh.order_id
where
    $where
order by oh.order_id
limit $limit offset $offset
__EOS;

        GC_Utils::gfFrontLog($sql);
        return $objQuery->getAll($sql);

    }

    /**
     * エクスポートした受注データを出力済みへ更新
     *
     * @param string $where     検索条件の WHERE 句
     * @param string $send_date 送信日時
     * @return void
     */
    function updateOrderSendFlg($where, $send_date, $send_flg) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql = <<<__EOS
UPDATE
    dtb_order oh
SET
    oh.send_flg  = '$send_flg',
    oh.send_date = '$send_date'
WHERE
    $where
__EOS;

        $objQuery->query($sql);
    }

    /**
     * 定期データ検索結果の行数を取得する.
     *
     * @param none
     * @return integer 検索結果の行数
     */
    function getRegularNumberOfLines() {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sendFlgOn = INOS_SEND_FLG_ON;
        $where = "rh.send_flg = " . INOS_SEND_FLG_OFF;

        $sql =<<<__EOS
select
    count(*)
from
    dtb_regular_order rh
    inner join dtb_regular_order_detail as rd
        on rd.regular_id = rh.regular_id
    left join dtb_order as oh
        on oh.order_id = rh.order_id
where
    $where
    and (rh.order_id IS NULL
    or (rh.order_id = oh.order_id
    and oh.send_flg = $sendFlgOn))
__EOS;

        return $objQuery->getOne($sql);
    }

    /**
     *  定期データCSV出力を行う
     *
     * @param int    $process_count 処理件数
     * @return void
     */
    function doMakeRegularCSV($process_count) {

        $where = "rh.send_flg = " . INOS_SEND_FLG_OFF;

        // WEB配送業者ID
        $deliv_id_yamato      = DELIV_ID_YAMATO;
        $deliv_id_yamato_mail = DELIV_ID_YAMATO_MAIL;
        $deliv_id_sagawa      = DELIV_ID_SAGAWA;
        
        // INOS配送業者ID
        $inos_deliv_id_yamato = INOS_DELIV_ID_YAMATO;
        $inos_deliv_id_sagawa = INOS_DELIV_ID_SAGAWA;

        $sendFlgOn = INOS_SEND_FLG_ON;

        // 受注データの送信フラグ,送信日時を送信準備中に更新
        $this->updateRegularSendFlg($where, $send_date, 2);

        $where = "rh.send_flg = 2";

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
    and (rh.order_id IS NULL
    or (rh.order_id = oh.order_id
    and oh.send_flg = $sendFlgOn))
__EOS;

        // CSVダウンロード実行
        $objCsv = new SC_Helper_CSV_Ex();
        $file_name_head = 'regular';
        $regularFile = $objCsv->sfDownloadMakeCsvFromSql
            ($sql, $arrval, $file_name_head, "");

        if ($regularFile) {
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
        $this->updateRegularSendFlg($where, $send_date, INOS_SEND_FLG_ON);

	return $regularFile;
    }

    /**
     * エクスポートしたDBの定期レコードを出力済みへ更新
     *
     * @param string $where     検索条件の WHERE 句
     * @param string $send_date 送信日時
     * @param string $send_flg  送信フラグ
     * @return void
     */
    function updateRegularSendFlg($where, $send_date, $send_flg) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

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

    /**
     * エクスポートしたDBの定期レコードを出力済みへ更新
     *
     * @param array  $arrFile   CSVファイル名
     * @return void
     */
    function doOutputCSV($arrFile) {

	$zipname = date("YmdHis") . ".zip";
	$zippath = CSV_TEMP_REALDIR . $zipname;
	$z = new ZipArchive();

	if ($z->open($zippath, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== true) {
	    return;
	}

	// 注文情報
	if ($fp = fopen($arrFile["order"], "r")) {
	    $filename = sprintf("order_%s.csv", date("YmdHis"));
	    $data = fread($fp, filesize($arrFile["order"]));
	    $z->addFromString($filename, $data);
	    fclose($fp);
	}

	// 定期情報
	if ($fp = fopen($arrFile["regular"], "r")) {
	    $filename = sprintf("regular_%s.csv", date("YmdHis"));
	    $data = fread($fp, filesize($arrFile["regular"]));
	    $z->addFromString($filename, $data);
	    fclose($fp);
	}
	$z->close();

	if (filesize($zippath)) {
	    header('Content-Type: application/zip; name="' . $zipname . '"');
	    header('Content-Disposition: attachment; filename="' . $zipname . '"');
	    header('Content-Length: ' . filesize($zippath));
	    echo file_get_contents($zippath);
	}

	unlink($zippath);
	unlink($arrFile["order"]);
	unlink($arrFile["regular"]);
    }
}
?>
