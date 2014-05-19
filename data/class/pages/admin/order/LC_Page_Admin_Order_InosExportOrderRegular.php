<?php
// {{{ requires
require_once (CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php');
require_once CLASS_EX_REALDIR . 'helper_extends/SC_Helper_CSV_Ex.php';

/**
 * INOSシステム連携 受注・定期エクスポートページ のページクラス
 *
 * @package Page
 * @author IQUEVE Co.,Ltd.
 * @version $Id:$
 */
class LC_Page_Admin_Order_InosExportOrderRegular extends LC_Page_Admin_Ex
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
        $this->tpl_mainpage = 'order/inos_export_order_regular.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'inos_export_order_regular';
        $this->tpl_pager = 'pager.tpl';
        $this->tpl_maintitle = '受注関連';
        $this->tpl_subtitle = '受注・定期エクスポート';

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

        // 最終出力日時を取得
        $this->order_last_send_date =
            SC_Helper_DB_Ex::sfGetLastSendDate(INOS_DATA_TYPE_SEND_ORDER);

        $this->regular_last_send_date =
            SC_Helper_DB_Ex::sfGetLastSendDate(INOS_DATA_TYPE_SEND_REGULAR);

        // 検索条件
        $orderWhere = "oh.send_flg = 0 AND oh.status = " . ORDER_NEW;

        /* -----------------------------------------------
         * 処理を実行
         * ----------------------------------------------- */
        switch ($this->getMode()) {

            // CSVを送信する。
            case 'csv':

                // 件数取得
                $orderLineMax = $this->getNumberOfLines($orderWhere);
                $regularLineMax = $this->getRegularNumberOfLines();
                if ($orderLineMax < 1 && $regularLineMax < 1) {
                    $this->tpl_onload =
                        "window.alert('既にエクスポート処理が完了しています。再度検索を行ってください。');";
                    break;
                }

                $arrFile = array();
                // 受注データCSV生成
                $arrFile["order"] = $this->doMakeOrderCSV($orderWhere, $orderLineMax);
                // 定期データCSV生成
                $arrFile["regular"] = $this->doMakeRegularCSV($regularLineMax);

                // CSVファイルダウンロード
                $this->doOutputCSV($arrFile);
                exit;
            break;

            // 検索実行
            default:
                // 受注件数取得
                $this->tpl_orderCnt = $this->getNumberOfLines($orderWhere);
                $this->tpl_orderCsvCnt = $this->getMakeOrderCSVCnt($orderWhere);
                // 定期件数取得
                $this->tpl_regularCnt = $this->getRegularNumberOfLines();
                $this->tpl_regularCsvCnt = $this->getMakeRegularCSVCnt();
        }
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
    case
        when shpref.name is null
        then sh.shipping_addr01
        else concat(shpref.name, sh.shipping_addr01)
        end AS addr01,
    sh.shipping_addr02,
    od.course_cd,
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
    oh.invoice_num,
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
    oh.payment_total,
    oh.purchase_motive_code,
    oh.input_assistance_code,
    oh.event_code,
    (select kikan_id 
     from mtb_device_type
     where id = oh.device_type_id
    ) as order_kbn,
    oh.regular_base_no,
    oh.order_id,
    oh.customer_id,
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
    od.cut_rate
from
    dtb_order as oh
    inner join dtb_shipping as sh
        on sh.order_id = oh.order_id
    inner join dtb_order_detail as od
        on od.order_id = oh.order_id
    inner join dtb_customer as cs
        on cs.customer_id = oh.customer_id
    left join mtb_pref as shpref
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
     *  CSV出力件数取得
     *
     * @param string $where 検索条件
     * @return integer 検索結果の行数
     */
    function getMakeOrderCSVCnt($where) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<__EOS
select count(A.order_id) as cnt
from (
    select
        oh.order_id as order_id
    from
        dtb_order as oh
        inner join dtb_shipping as sh
            on sh.order_id = oh.order_id
        inner join dtb_order_detail as od
            on od.order_id = oh.order_id
        inner join dtb_customer as cs
            on cs.customer_id = oh.customer_id
        left join mtb_pref as shpref
            on shpref.id = sh.shipping_pref
    where
        $where
    group by oh.order_id
) A
__EOS;

        return $objQuery->getOne($sql);
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
    dtb_order as oh
    inner join dtb_shipping as sh
        on sh.order_id = oh.order_id
    inner join dtb_order_detail as od
        on od.order_id = oh.order_id
    inner join dtb_customer as cs
        on cs.customer_id = oh.customer_id
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
    case
        when rpref.name is null
        then rh.order_addr01
        else concat(rpref.name, rh.order_addr01)
        end as order_addr01,
    rh.order_addr02,
    rd.course_cd,
    rh.status,
    rd.todoke_kbn,
    rd.todoke_day,
    rd.todoke_week,
    rd.todoke_week2,
    date_format(rd.next_arrival_date, '%Y/%m/%d') as next_arrival_date,
    date_format(rh.next_ship_date, '%Y/%m/%d') as next_ship_date,
    date_format(rd.after_next_arrival_date, '%Y/%m/%d') as after_next_arrival_date,
    date_format(rh.after_next_ship_date, '%Y/%m/%d') as after_next_ship_date,
    date_format(rd.cancel_date, '%Y/%m/%d') as cancel_date,
    rd.cancel_reason_cd,
    rh.shipment_cd,
    case
        when rh.deliv_id = {$deliv_id_yamato} OR
            rh.deliv_id = {$deliv_id_yamato_mail}
                then {$inos_deliv_id_yamato}
        when rh.deliv_id = {$deliv_id_sagawa}
                then {$inos_deliv_id_sagawa}
        else "" end as deliv_id,
    rh.box_size,
    rh.invoice_num,
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
    case when pc.product_code is null then rd.product_code
         else pc.product_code end as product_code,
    rd.product_name,
    rd.quantity,
    rd.price,
    rd.cut_rate
from
    dtb_regular_order_detail rd
    inner join dtb_regular_order as rh
        on rh.regular_id = rd.regular_id
    left join dtb_order as oh
        on oh.order_id = rh.order_id
    inner join dtb_customer as c
        on c.customer_id = rh.customer_id
    left join dtb_products_class as pc
        on pc.product_class_id = rd.product_class_id
    left join mtb_pref as rpref
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
     *  定期データCSV出力件数取得
     *
     * @param none
     * @return integer 検索結果の行数
     */
    function getMakeRegularCSVCnt() {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $where = "rh.send_flg = " . INOS_SEND_FLG_OFF;

        $sendFlgOn = INOS_SEND_FLG_ON;

        $sql =<<<__EOS
select count(A.regular_id) as cnt
from (
    select
        rh.regular_id as regular_id
    from
        dtb_regular_order_detail rd
        inner join dtb_regular_order as rh
            on rh.regular_id = rd.regular_id
        left join dtb_order as oh
            on oh.order_id = rh.order_id
        inner join dtb_customer as c
            on c.customer_id = rh.customer_id
        left join dtb_products_class as pc
            on pc.product_class_id = rd.product_class_id
        left join mtb_pref as rpref
            on rpref.id = rh.order_pref
    where
        $where
        and (rh.order_id IS NULL
        or (rh.order_id = oh.order_id
        and oh.send_flg = $sendFlgOn))
    group by rh.regular_id
) A
__EOS;

        return $objQuery->getOne($sql);
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
    dtb_regular_order_detail rd
    inner join dtb_regular_order as rh
        on rh.regular_id = rd.regular_id
    inner join dtb_customer as c
        on c.customer_id = rh.customer_id
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
