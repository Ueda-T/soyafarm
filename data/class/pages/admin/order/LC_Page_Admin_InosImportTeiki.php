<?php
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';
require_once CLASS_EX_REALDIR . 'helper_extends/SC_Helper_CSV_Ex.php';

define('COL_COUNT', 40);



/*
 *
 */
class LC_Page_Admin_InosImportTeiki extends LC_Page_Admin_Ex {

    var $arrRowResult;

    var $INOS_DELIV_ID_YAMATO;
    var $DELIV_ID_YAMATO;
    var $DELIV_ID_YAMATO_MAIL;
    var $DELIV_BOX_ID_TAKUHAI;
    var $DELIV_BOX_ID_MAIL;

    /** 削除用時必須チェック必要カラム **/
    var $arrExistsDelPtnCol;

    /** 削除用時必須チェック不要カラム **/
    var $arrNoExistsDelPtnCol;

    /** 休止、保留、解約用時必須チェック不要カラム **/
    var $arrNoExistsHoldPtnCol;

    /*
     * Page を初期化する.
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'order/inos_import_teiki.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'inos_import_teiki';
        $this->tpl_maintitle = '定期関連';
        $this->tpl_subtitle = 'INOS定期情報インポート';
        $this->csv_id = '8';

        // 基幹宅配便コード
        $this->INOS_DELIV_ID_YAMATO = INOS_DELIV_ID_YAMATO;

        // WEB宅配便コード
        $this->DELIV_ID_YAMATO = DELIV_ID_YAMATO;
        $this->DELIV_ID_YAMATO_MAIL = DELIV_ID_YAMATO_MAIL;

        // WEB箱ID
        $this->DELIV_BOX_ID_TAKUHAI = DELIV_BOX_ID_TAKUHAI;
        $this->DELIV_BOX_ID_MAIL = DELIV_BOX_ID_MAIL;

        // 休止、保留、解約時用の必須チェック不要カラム
        $this->arrNoExistsHoldPtnCol
            = array('next_ship_date',
                    'after_next_ship_date'
                   );
        // 明細用休止、解約時用の必須チェック不要カラム
        $this->arrNoExistsDtlHoldPtnCol
            = array('next_arrival_date',
                    'after_next_arrival_date'
                   );
        // 削除時用の必須チェック不要カラム
        $this->arrNoExistsDelPtnCol
            = array('customer_cd',
                    'order_date',
                    'order_name',
                    'order_tel',
                    'order_zip',
                    'order_addr01',
                    'status',
                    'next_ship_date',
                    'after_next_ship_date',
                    'shipment_cd',
                    'deliv_id',
                    'box_size',
                    'invoice_num',
                    'time_id',
                    'include_kbn',
                    'payment_id',
                    'deliv_fee',
                    'buy_num',
                    'line_no',
                    'product_code',
                    'quantity',
                    'price',
                    'cut_rate',
                    'course_cd',
                    'status',
                    'todoke_kbn',
                    'next_arrival_date',
                    'after_next_arrival_date',
                   );
        // 削除時用の必須チェック必要カラム
        $this->arrExistsDelPtnCol
            = array();

        set_time_limit(0);
    }

    /*
     * Page のプロセス.
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /*
     * Page のアクション.
     */
    function action() {
        $objCsv = new SC_Helper_CSV_Ex();
        $arrCsvFrame = $objCsv->sfGetCsvOutput($this->csv_id);

        // CSV構造がインポート可能かのチェック
        if (!$objCsv->sfIsImportCsvFrame($arrCsvFrame)) {
            // 無効なフォーマットなので初期状態に強制変更
            $arrCsvFrame = $objCsv->sfGetCsvOutput
		($this->csv_id, '', array(), 'no');
            $this->tpl_is_format_default = true;
        }

        // CSV構造は更新可能なフォーマットかのフラグ取得
        $this->tpl_is_update = $objCsv->sfIsUpdateCsvFrame($arrCsvFrame);

        // CSVファイルアップロード情報の初期化
        $objUpFile = new SC_UploadFile
	    (CSV_TEMP_REALDIR, CSV_SAVE_REALDIR);
        $this->lfInitFile($objUpFile);

        // パラメーター情報の初期化
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam, $arrCsvFrame);

        $objFormParam->setHtmlDispNameArray();
        $this->arrTitle = $objFormParam->getHtmlDispNameArray();

        switch ($this->getMode()) {
        case 'csv_upload':
	        $this->importCsvFile($objFormParam, $objUpFile);
            break;

        case 'errcsv_download':
	        $this->doOutputErrCSV();
            exit;

        default:
            break;
        }
    }



    /*
     * CSVファイルの一時取込
     */
    function loadCsvFile($file) {
	$sql =<<<__EOS
truncate table dtb_regular_inos_import
;
set character_set_database=utf8
;
load data local infile '{$file}' into table dtb_regular_inos_import 
fields terminated by ',' enclosed by '"' lines terminated by '\r\n'
set regular_base_no = nullif(regular_base_no, ''),
    customer_cd = nullif(customer_cd, ''),
    order_date = nullif(order_date, ''),
    order_kana = nullif(order_kana, ''),
    order_name = nullif(order_name, ''),
    order_tel = nullif(order_tel, ''),
    order_zip = nullif(order_zip, ''),
    order_addr_kana = nullif(order_addr_kana, ''),
    order_addr01 = nullif(order_addr01, ''),
    order_addr02 = nullif(order_addr02, ''),
    course_cd = nullif(course_cd, ''),
    status = nullif(status, ''),
    todoke_kbn = nullif(todoke_kbn, ''),
    todoke_day = nullif(todoke_day, ''),
    todoke_week = nullif(todoke_week, ''),
    todoke_week2 = nullif(todoke_week2, ''),
    next_arrival_date = nullif(next_arrival_date, ''),
    next_ship_date = nullif(next_ship_date, ''),
    after_next_arrival_date = nullif(after_next_arrival_date, ''),
    after_next_ship_date = nullif(after_next_ship_date, ''),
    cancel_date = nullif(cancel_date, ''),
    cancel_reason_cd = nullif(cancel_reason_cd, ''),
    shipment_cd = nullif(shipment_cd, ''),
    deliv_id = nullif(deliv_id, ''),
    box_size = nullif(box_size, ''),
    invoice_num = nullif(invoice_num, ''),
    time_id = nullif(time_id, ''),
    remarks = nullif(remarks, ''),
    include_kbn = nullif(include_kbn, ''),
    payment_id = nullif(payment_id, ''),
    deliv_fee = nullif(deliv_fee, ''),
    buy_num = nullif(buy_num, ''),
    order_id = nullif(order_id, ''),
    del_flg = nullif(del_flg, ''),
    update_date = nullif(update_date, '0000-00-00 00:00:00'),
    line_no = nullif(line_no, ''),
    product_code = nullif(product_code, ''),
    product_name = nullif(product_name, ''),
    quantity = nullif(quantity, ''),
    price = nullif(price, ''),
    cut_rate = nullif(cut_rate, '')
;
__EOS;

    $mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);
    if ($mysqli->multi_query($sql)) {
        do {
            if ($result = $mysqli->store_result()) {
                while ($row = $result->fetch_row()) {
                ;
                }
                $result->free();
            }
            if ($mysqli->more_results()) {
                ;
            }
        } while ($mysqli->next_result());
    }
    // エラーの場合はログ出力
    if ($mysqli->errno) {
        $this->outputMysqliErrorMsg($mysqli, $sql,  __FUNCTION__);
        $mysqli->close();
        return false;
    }
        $mysqli->close();
        return true;
    }

    /*
     * 取込準備
     */
    function prepareImport(&$objFormParam) {
    // インポートデータの共通チェック
    $this->inosRegularImportCheck($objFormParam);
	// 更新パターンのチェック
	$this->updateCheck();
	// 削除パターンのチェック
	$this->deleteCheck();
	// 定期明細エラーチェック
	$this->importExistsDetailCheck();
    }

    // インポートデータ共通チェック処理
    function inosRegularImportCheck(&$objFormParam) {
        // コース受注NO必須チェック
        $this->existsRegularBaseNoCheck();
        // 商品情報存在チェック
        $this->existsProductCodeCheck();
        // 項目チェック
        $this->regularColCheck($objFormParam);
        // 行NO重複チェック
        $this->importLineNoCheck();
    }

    // 共通コース受注NO必須項目チェック
    function existsRegularBaseNoCheck() {
    $sql =<<<__EOS
update dtb_regular_inos_import t1
   set t1.error_flg = 1
     , t1.error_name = 'コース受注NO - 必須項目エラー'
 where t1.regular_base_no is null
__EOS;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->query($sql);
    }

    // 商品情報の存在チェック
    function existsProductCodeCheck() {
    $sql =<<<__EOS
    update dtb_regular_inos_import t1
   set t1.error_flg = 1
     , t1.error_name = '商品マスタに該当する商品が存在しておりません'
 where t1.error_flg = 0
   and t1.del_flg = 0
   and t1.status < 9
   and not exists (select 'X'
                     from dtb_products_class pc
                    where t1.product_code = pc.product_code
                      and pc.del_flg = 0)
__EOS;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->query($sql);
    }
    /* 項目チェック
     *
     * param  $objFormParam
     * return void
     */
    function regularColCheck(&$objFormParam) {

    $sql =<<<__EOS
select *
  from dtb_regular_inos_import imp
 where error_flg = 0 
__EOS;

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // 一時テーブルのデータ取得
        $arrImportData = $objQuery->getAll($sql);

        // 一行ずつ読込
        foreach ($arrImportData as $arrData) {

        // 項目がNULLの場合は空文字を挿入
        foreach ($arrData as $key => &$val) {
            if ($val === null) {
                $val = '';
            }
        }
        // シーケンス配列を格納する。
        $objFormParam->setParam($arrData);
        // 入力値の変換
        $objFormParam->convParam();

        // 項目チェック処理実行、コース受注NOをキーに結果取得
        $arrErrData[][$arrData["regular_base_no"]][$arrData["line_no"]]
            = $this->lfCheckError($objFormParam);
        }

        // 結果配列ループ
        foreach ($arrErrData as $arrVal) {
            foreach ($arrVal as $regular_base_no => $arrDetail) {
                foreach ($arrDetail as $line_no => $arrErrMsg) {

                    // エラー結果がなければスキップ
                    if (count($arrVal[$regular_base_no]) == 0) {
                        continue;
                    }
                    if (count($arrDetail[$line_no]) == 0) {
                        continue;
                    }
                    // 配列のエラーメッセージを文字列に変換
                    $err_msg = implode($arrErrMsg);

                    // エラーデータに更新
                    $sql =<<<__EOS
        update dtb_regular_inos_import imp
           set imp.error_flg = 1
             , imp.error_name = '{$err_msg}'
         where imp.regular_base_no = '{$regular_base_no}'
         and imp.line_no = '{$line_no}'
__EOS;
                    $objQuery->query($sql);

                }
            }
        }
        return;
    }

    // 行NOの重複チェック
    function importLineNoCheck() {

    $sql =<<<__EOS
update dtb_regular_inos_import t1
     , (select regular_base_no
          from dtb_regular_inos_import
      group by regular_base_no, line_no
        having count(line_no) > 1) t2
   set t1.error_flg = 1
     , t1.error_name = 'インポートファイル内 行NO重複エラー'
 where t1.regular_base_no = t2.regular_base_no

__EOS;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->query($sql);
    }
    /**
     * 入力チェックを行う.
     *
     * @return void
     */
    function lfCheckError(&$objFormParam) {
        // 入力データを渡す。
        $arrRet = $objFormParam->getHashArray();

        $objErr = new SC_CheckError_Ex($arrRet);

        // 削除データ時
        if ($arrRet["del_flg"] == 1) {
            // 削除時のチェック用にオブジェクトを複製
            $objDelFormParam = clone $objFormParam;
            // 必須チェック追加
            $objDelFormParam->addCheck
                ($this->arrExistsDelPtnCol, 'EXIST_CHECK');
            // 必須チェック消去
            $objDelFormParam->removeCheck
                ($this->arrNoExistsDelPtnCol, 'EXIST_CHECK');

            $arrErr = $objDelFormParam->checkError(false);
        // 通常
        } else {
            // 状況が0:受注中、1：購入中で無いときは必須一部解除
            if ($arrRet["status"] != REGULAR_ORDER_STATUS_ORDER
                && $arrRet["status"] != REGULAR_ORDER_STATUS_PURCHASE) {
                $objFormParam->removeCheck
                    ($this->arrNoExistsHoldPtnCol, 'EXIST_CHECK');
            }

            $arrErr = $objFormParam->checkError(false);
        }
        return $arrErr;
    }

    // 更新パターンチェック
    function updateCheck() {
        // 基幹顧客CDから顧客情報の存在チェック
        $this->importExistsCustomerCdCheck();
        // WEB受注IDから定期情報の存在チェック
        $this->importExistsOrderIdCheck();
    }

    // 削除パターンチェック
    function deleteCheck() {
        ; // 処理なし
    }

    // 基幹顧客CD存在チェック
    function importExistsCustomerCdCheck() {

    $sql =<<<__EOS
    update dtb_regular_inos_import t1

       set t1.error_flg = 1
         , t1.error_name = '基幹顧客CDから対象の顧客情報が見つかりません。'
     where t1.customer_cd is not null
       and t1.error_flg = 0
       and t1.del_flg = 0
       and not exists (select 'X'
                         from dtb_customer c
                        where c.customer_cd = t1.customer_cd
                          and c.del_flg = 0)
__EOS;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->query($sql);
    }

    // 定期情報の存在チェック
    function importExistsOrderIdCheck() {

    $sql =<<<__EOS
    update dtb_regular_inos_import t1 

       set t1.error_flg = 1
         , t1.error_name = 'コース受注NOとWEB受注IDから対象の定期情報が見つかりません。'
     where t1.order_id is not null
       and t1.error_flg = 0
       and t1.del_flg = 0
       and exists (select 'X'
                     from dtb_regular_order r
                    where r.order_id = t1.order_id
                      and r.regular_base_no != t1.regular_base_no
                      and t1.regular_base_no is not null
                      and r.del_flg = 0)
__EOS;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->query($sql);
    }

    // 定期データ明細エラーチェック
    function importExistsDetailCheck() {

    $sql =<<<__EOS
update dtb_regular_inos_import a
inner join dtb_regular_inos_import b 
on a.regular_base_no = b.regular_base_no 
and b.error_flg = 1
set a.error_flg = 1
where a.error_flg = 0
__EOS;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->query($sql);
    }

    /*
     * 取込み処理実行
     */
    function doImport() {
    $objQuery =& SC_Query_Ex::getSingletonInstance();

    // コース受注NOからWEB受注IDを付与
    if(!$this->addOrderIdByRegularBaseNo()) {
        return array(0, INOS_ERROR_FLG_EXIST_ERROR);
    }
    // 追加パターン
    if(!$this->insertImport()) {
        return array(0, INOS_ERROR_FLG_EXIST_ERROR);
    }
    // 更新パターン
    if(!$this->updateImport()) {
        return array(0, INOS_ERROR_FLG_EXIST_ERROR);
    }
    // 削除パターン
    if(!$this->deleteImport()) {
        return array(0, INOS_ERROR_FLG_EXIST_ERROR);
    }
    // 取込み完了した件数取得
    $count = $objQuery->count
        ('dtb_regular_inos_import', 'error_flg = ?', 0);

    return array($count, INOS_ERROR_FLG_EXIST_NORMAL);
    }

    // コース受注NOからWEB受注IDを付与
    function addOrderIdByRegularBaseNo() {

    // ※コース受注NOが既に定期情報に
    // 取り込まれている場合、WEB受注IDをセット
    // 登録データ → 更新データへ変更 
    $sql =<<<__EOS
    update dtb_regular_inos_import imp
inner join dtb_regular_order t1
        on imp.regular_base_no = t1.regular_base_no
       set imp.order_id = t1.order_id
     where imp.regular_base_no is not null
       and t1.regular_base_no is not null
       and imp.order_id is null
       and imp.error_flg = 0
;
__EOS;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->query($sql);
    }

    // 新規登録インポート処理
    function insertImport() {
    $objQuery =& SC_Query_Ex::getSingletonInstance();

    // 定期情報ID採番
    $SEQ = $objQuery->currVal('dtb_regular_order_regular_id');

    // 定期IDをセット
    $sql =<<<__EOS
set @i := {$SEQ};
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }

    // 定期情報 登録処理
    $sql =<<<__EOS
INSERT INTO dtb_regular_order (
    regular_id
   ,customer_id
   ,regular_base_no
   ,order_date
   ,order_name
   ,order_kana
   ,order_tel
   ,order_zip
   ,order_pref
   ,order_addr01
   ,order_addr02
   ,order_addr_kana
   ,status
   ,next_ship_date
   ,after_next_ship_date
   ,shipment_cd
   ,deliv_id
   ,box_size
   ,invoice_num
   ,time_id
   ,remarks
   ,include_kbn
   ,payment_id
   ,deliv_fee
   ,buy_num
   ,order_id
   ,send_flg
   ,recv_date
   ,del_flg
   ,create_date
   ,update_date
)
    SELECT (@i := @i + 1) AS regular_id
         , CS.customer_id
         , IM.regular_base_no
         , IM.order_date
         , IM.order_name
         , IM.order_kana
         , IM.order_tel
         , IM.order_zip
         , PR.id AS order_pref
         , replace(IM.order_addr01, ifnull(PR.name, ''), '') as order_addr01
         , ifnull(nullif(IM.order_addr02, ''), '番地なし') as order_addr02
         , IM.order_addr_kana
         , IM.status
         , IM.next_ship_date
         , IM.after_next_ship_date
         , IM.shipment_cd
         , CASE WHEN IM.deliv_id = {$this->INOS_DELIV_ID_YAMATO}
                 AND IM.box_size = {$this->DELIV_BOX_ID_MAIL}
                THEN {$this->DELIV_ID_YAMATO_MAIL}
                WHEN IM.deliv_id = {$this->INOS_DELIV_ID_YAMATO}
                 AND IM.box_size = {$this->DELIV_BOX_ID_TAKUHAI}
                THEN {$this->DELIV_ID_YAMATO} END AS deliv_id
         , IM.box_size
         , IM.invoice_num
         , IM.time_id
         , IM.remarks
         , IM.include_kbn
         , IM.payment_id
         , IM.deliv_fee
         , IM.buy_num
         , IM.order_id
         , 1
         , now()
         , IM.del_flg
         , IM.order_date
         , IM.update_date
      FROM dtb_regular_inos_import IM
INNER JOIN dtb_customer CS
        ON CS.customer_cd = IM.customer_cd
 LEFT JOIN mtb_zip ZP
        ON ZP.zipcode = replace(IM.order_zip, '-', '')
 LEFT JOIN mtb_pref PR
        ON PR.name = ZP.state
     WHERE IM.order_id is null
       AND IM.error_flg = 0
       AND IM.del_flg = 0
       AND NOT EXISTS (SELECT 'X'
                         FROM dtb_regular_order T1
                        WHERE T1.regular_base_no = IM.regular_base_no)
  GROUP BY IM.regular_base_no
;
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }

    // 定期情報IDの最大値を更新
    $sql =<<<__EOS
UPDATE dtb_regular_order_regular_id_seq
   SET sequence = (SELECT MAX(regular_id)
                    FROM dtb_regular_order)
;
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }

    /*
     ************************************
     * 定期詳細情報は
     * この時点で登録しない。
     * 
     * ※更新データをキーに
     * 　定期詳細情報の物理削除を行う為
     ************************************
     */

    return true;
    }

    // 更新インポート処理
    function updateImport() {
    $objQuery =& SC_Query_Ex::getSingletonInstance();

    // 定期情報 更新処理 ※更新キー：コース受注NO
    $sql =<<<__EOS
    UPDATE dtb_regular_order R
INNER JOIN (SELECT CS.customer_id
                 , IM.regular_base_no
                 , IM.order_date
                 , IM.order_name
                 , IM.order_kana
                 , IM.order_tel
                 , IM.order_zip
                 , PR.id AS order_pref
                 , replace(IM.order_addr01, ifnull(PR.name, ''), '') as order_addr01
                 , ifnull(nullif(IM.order_addr02, ''), '番地なし') as order_addr02
                 , IM.order_addr_kana
                 , IM.status
                 , IM.next_ship_date
                 , IM.after_next_ship_date
                 , IM.shipment_cd
                 , CASE WHEN IM.deliv_id = {$this->INOS_DELIV_ID_YAMATO}
                         AND IM.box_size = {$this->DELIV_BOX_ID_MAIL}
                        THEN {$this->DELIV_ID_YAMATO_MAIL}
                        WHEN IM.deliv_id = {$this->INOS_DELIV_ID_YAMATO}
                         AND IM.box_size = {$this->DELIV_BOX_ID_TAKUHAI}
                        THEN {$this->DELIV_ID_YAMATO} END AS deliv_id
                 , IM.box_size
                 , IM.invoice_num
                 , IM.time_id
                 , IM.remarks
                 , IM.include_kbn
                 , IM.payment_id
                 , IM.deliv_fee
                 , IM.buy_num
                 , IM.order_id
                 , IM.del_flg
                 , IM.update_date
              FROM dtb_regular_inos_import IM
        INNER JOIN dtb_customer CS
                ON CS.customer_cd = IM.customer_cd
         LEFT JOIN mtb_zip ZP
                ON ZP.zipcode = replace(IM.order_zip, '-', '')
         LEFT JOIN mtb_pref PR
                ON PR.name = ZP.state
             WHERE IM.error_flg = 0
               AND IM.del_flg = 0
               AND EXISTS (SELECT 'X'
                                 FROM dtb_regular_order R1
                                WHERE R1.regular_base_no = IM.regular_base_no)
          GROUP BY IM.regular_base_no) IM2
        ON R.regular_base_no = IM2.regular_base_no

       SET R.customer_id = IM2.customer_id
         , R.regular_base_no = IM2.regular_base_no
         , R.order_date = IM2.order_date
         , R.order_name = IM2.order_name
         , R.order_kana = IM2.order_kana
         , R.order_tel = IM2.order_tel
         , R.order_zip = IM2.order_zip
         , R.order_pref = IM2.order_pref
         , R.order_addr01 = IM2.order_addr01
         , R.order_addr02 = IM2.order_addr02
         , R.order_addr_kana = IM2.order_addr_kana
         , R.status = IM2.status
         , R.next_ship_date = IM2.next_ship_date
         , R.after_next_ship_date = IM2.after_next_ship_date
         , R.shipment_cd = IM2.shipment_cd
         , R.deliv_id = IM2.deliv_id
         , R.box_size = IM2.box_size
         , R.invoice_num = IM2.invoice_num
         , R.time_id = IM2.time_id
         , R.remarks = IM2.remarks
         , R.include_kbn = IM2.include_kbn
         , R.payment_id = IM2.payment_id
         , R.deliv_fee = IM2.deliv_fee
         , R.buy_num = IM2.buy_num
         , R.order_id = IM2.order_id
         , R.recv_date = now()
         , R.update_date = IM2.update_date
;
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }

    // 定期情報 更新処理 ※更新キー：WEB受注NO
    $sql =<<<__EOS
    UPDATE dtb_regular_order R
INNER JOIN (SELECT CS.customer_id
                 , IM.regular_base_no
                 , IM.order_date
                 , IM.order_name
                 , IM.order_kana
                 , IM.order_tel
                 , IM.order_zip
                 , PR.id AS order_pref
                 , replace(IM.order_addr01, ifnull(PR.name, ''), '') as order_addr01
                 , ifnull(nullif(IM.order_addr02, ''), '番地なし') as order_addr02
                 , IM.order_addr_kana
                 , IM.status
                 , IM.next_ship_date
                 , IM.after_next_ship_date
                 , IM.shipment_cd
                 , CASE WHEN IM.deliv_id = {$this->INOS_DELIV_ID_YAMATO}
                         AND IM.box_size = {$this->DELIV_BOX_ID_MAIL}
                        THEN {$this->DELIV_ID_YAMATO_MAIL}
                        WHEN IM.deliv_id = {$this->INOS_DELIV_ID_YAMATO}
                         AND IM.box_size = {$this->DELIV_BOX_ID_TAKUHAI}
                        THEN {$this->DELIV_ID_YAMATO} END AS deliv_id
                 , IM.box_size
                 , IM.invoice_num
                 , IM.time_id
                 , IM.remarks
                 , IM.include_kbn
                 , IM.payment_id
                 , IM.deliv_fee
                 , IM.buy_num
                 , IM.order_id
                 , IM.del_flg
                 , IM.update_date
              FROM dtb_regular_inos_import IM
        INNER JOIN dtb_customer CS
                ON CS.customer_cd = IM.customer_cd
         LEFT JOIN mtb_zip ZP
                ON ZP.zipcode = replace(IM.order_zip, '-', '')
         LEFT JOIN mtb_pref PR
                ON PR.name = ZP.state
             WHERE IM.error_flg = 0
               AND IM.del_flg = 0
               AND IM.order_id is not null
               AND NOT EXISTS (SELECT 'X'
                                 FROM dtb_regular_order R1
                                WHERE R1.regular_base_no = IM.regular_base_no)
               AND EXISTS (SELECT 'X'
                             FROM dtb_regular_order R2
                            WHERE R2.order_id = IM.order_id)
          GROUP BY IM.regular_base_no) IM2
        ON R.order_id = IM2.order_id

       SET R.customer_id = IM2.customer_id
         , R.regular_base_no = IM2.regular_base_no
         , R.order_date = IM2.order_date
         , R.order_name = IM2.order_name
         , R.order_kana = IM2.order_kana
         , R.order_tel = IM2.order_tel
         , R.order_zip = IM2.order_zip
         , R.order_pref = IM2.order_pref
         , R.order_addr01 = IM2.order_addr01
         , R.order_addr02 = IM2.order_addr02
         , R.order_addr_kana = IM2.order_addr_kana
         , R.status = IM2.status
         , R.next_ship_date = IM2.next_ship_date
         , R.after_next_ship_date = IM2.after_next_ship_date
         , R.shipment_cd = IM2.shipment_cd
         , R.deliv_id = IM2.deliv_id
         , R.box_size = IM2.box_size
         , R.invoice_num = IM2.invoice_num
         , R.time_id = IM2.time_id
         , R.remarks = IM2.remarks
         , R.include_kbn = IM2.include_kbn
         , R.payment_id = IM2.payment_id
         , R.deliv_fee = IM2.deliv_fee
         , R.buy_num = IM2.buy_num
         , R.order_id = IM2.order_id
         , R.recv_date = now()
         , R.update_date = IM2.update_date
;
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }

    // 定期詳細情報 物理削除
    $sql =<<<__EOS
    delete dtb_regular_order_detail rd
      from dtb_regular_order_detail rd
inner join dtb_regular_order rh
        on rh.regular_id = rd.regular_id
inner join dtb_regular_inos_import imp
        on rh.regular_base_no = imp.regular_base_no
     where imp.error_flg = 0
       and imp.del_flg = 0
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }

    // 定期詳細情報 登録処理
    // ※登録データ・更新データ共通
    $sql =<<<__EOS
INSERT INTO dtb_regular_order_detail (
    regular_id
   ,line_no
   ,product_id
   ,product_class_id
   ,product_code
   ,product_name
   ,price
   ,quantity
   ,cut_rate
   ,todoke_kbn
   ,todoke_day
   ,todoke_week
   ,todoke_week2
   ,course_cd
   ,status
   ,next_arrival_date
   ,after_next_arrival_date
   ,cancel_date
   ,cancel_reason_cd
   ,del_flg
   ,create_date
   ,update_date
)
    SELECT RH.regular_id
         , IM.line_no
         , PC.product_id
         , PC.product_class_id
         , IM.product_code
         , IM.product_name
         , IM.price
         , IM.quantity
         , IM.cut_rate
         , IM.todoke_kbn
         , IM.todoke_day
         , IM.todoke_week
         , IM.todoke_week2
         , CASE WHEN IM.course_cd = 30 THEN 1
           WHEN IM.course_cd = 60 THEN 2
           WHEN IM.course_cd = 90 THEN 3
           ELSE IM.course_cd END AS course_cd
         , IM.status
         , IM.next_arrival_date
         , IM.after_next_arrival_date
         , IM.cancel_date
         , IM.cancel_reason_cd
         , IM.del_flg
         , IM.order_date
         , IM.update_date
      FROM dtb_regular_inos_import IM
INNER JOIN dtb_regular_order RH
        ON RH.regular_base_no = IM.regular_base_no
 LEFT JOIN dtb_products_class PC
        ON PC.product_code = IM.product_code
       AND PC.del_flg = 0
     WHERE IM.error_flg = 0
       AND IM.del_flg = 0
;
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }

    return true;
    }

    // 削除インポート処理
    function deleteImport() {

    $objQuery =& SC_Query_Ex::getSingletonInstance();

    // 定期情報 論理削除処理
    $sql =<<<__EOS
    update dtb_regular_order rh
inner join dtb_regular_inos_import imp
        on rh.order_id = imp.order_id
        or rh.regular_base_no = imp.regular_base_no

       set rh.del_flg = imp.del_flg
         , rh.update_date = imp.update_date
         , rh.recv_date = now()
     where imp.error_flg = 0
       and imp.del_flg = 1
;
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }

    // 定期詳細情報 論理削除処理：受注ID
    $sql =<<<__EOS
    update dtb_regular_order_detail rd
inner join dtb_regular_order rh
        on rd.regular_id = rh.regular_id
inner join dtb_regular_inos_import imp
        on rh.order_id = imp.order_id
       and imp.order_id is not null
       and imp.error_flg = 0
       and imp.del_flg = 1

       set rd.del_flg = imp.del_flg
         , rd.update_date = imp.update_date
;
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }

    // 定期詳細情報 論理削除処理：コース受注NO
    $sql =<<<__EOS
    update dtb_regular_order_detail rd
inner join dtb_regular_order rh
        on rd.regular_id = rh.regular_id
inner join dtb_regular_inos_import imp
        on rh.regular_base_no = imp.regular_base_no
       and imp.error_flg = 0
       and imp.del_flg = 1

       set rd.del_flg = imp.del_flg
         , rd.update_date = imp.update_date
;
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }
    return true;
    }

    /*
     * 定期情報ファイルインポート処理開始
     */
    function importCsvFile(&$objFormParam, &$objUpFile) {
	/*
	// ファイルのアップロード
	$file = $this->uploadCsvFile($objFormParam, $objUpFile);
	if (empty($file)) {
	    return;
	}
	 */

	// ファイル情報取得
        $arrFile = SC_Utils_Ex::sfGetDirFile(INOS_DIR_RECV_REGULAR);
	if (!$arrFile[0]) {
	    $this->arrErr["csv_file"] = "取込ファイルがセットされておりません";
	    return;
	}

	$arrNgFile = array();
	$arrOkFile = array();
	$arrEncFile = array();
	// 複数ファイルを１ファイルにまとめる
	for ($i = 0; $i < count($arrFile); $i++) {

	    // ファイルの文字コード変換
	    $file = $this->uploadCsvFile($objFormParam, $arrFile[$i]);
	    if (empty($file)) {
		$arrNgFile[] = $arrFile[$i];
		continue;
	    }
	    $arrOkFile[] = $arrFile[$i];
	    $arrEncFile[] = $file;
	}

	// 文字コード変換ファイルがない場合終了
	if (!$arrEncFile[0]) {
	    SC_Utils_Ex::sfImportFileMove(INOS_DIR_RECV_REGULAR
					, $arrNgFile, INOS_NG_DIR);
	    return;
	}

	// 複数ファイルを１ファイルに変換
	$impFileName = date("YmdHis") . ".csv";
	$impFile = INOS_DIR_RECV_REGULAR . $impFileName;
	$cmd = sprintf("cat %s > %s", implode(" ", $arrEncFile), $impFile);
	system($cmd);

	// 一時テーブルへのローディング
	if (!$this->loadCsvFile($file)) {
	    $this->tpl_mainpage = 'order/inos_import_teiki_complete.tpl';
	    $arrFile[] = $impFileName;
	    SC_Utils_Ex::sfImportFileMove(INOS_DIR_RECV_REGULAR
					, $arrFile, INOS_NG_DIR);
	    return;
	}

	$objQuery =& SC_Query_Ex::getSingletonInstance();
	// 取込準備
	$this->prepareImport($objFormParam);
	$objQuery->begin();

	// 取込実行
	list($count, $r) = $this->doImport();
	if ($r != INOS_ERROR_FLG_EXIST_NORMAL) {
	    $objQuery->rollback();
	    $arrFile[] = $impFileName;
	    SC_Utils_Ex::sfImportFileMove(INOS_DIR_RECV_REGULAR
					, $arrFile, INOS_NG_DIR);
	} else {
	    $objQuery->commit();
	    $arrOkFile[] = $impFileName;
	    SC_Utils_Ex::sfImportFileMove(INOS_DIR_RECV_REGULAR
					, $arrOkFile, INOS_OK_DIR);
	    if ($arrNgFile[0]) {
		SC_Utils_Ex::sfImportFileMove(INOS_DIR_RECV_REGULAR
					    , $arrNgFile, INOS_NG_DIR);
	    }
	}
	// エラーデータがある場合は更新履歴情報をエラー判定に
	$this->tpl_err_count = $objQuery->count
	    ('dtb_regular_inos_import', 'error_flg = ?', 1);
	if ($this->tpl_err_count > 0) {
	    $r = INOS_ERROR_FLG_EXIST_ERROR;
	}

	// バッチ処理履歴情報へデータ登録
	SC_Helper_DB_Ex::sfInsertBatchHistory
	    (INOS_DATA_TYPE_RECV_REGULAR, $count, $r);

	// 実行結果画面を表示
	$this->tpl_mainpage = 'order/inos_import_teiki_complete.tpl';
	$this->addRowCompleteMsg($count);
    }

    /*
     * CSVアップロードを実行します.
     */
    function uploadCsvFile(&$objFormParam, $fileName) {
	/*
        // ファイルアップロードのチェック
        $objUpFile->makeTempFile('csv_file');
        $this->arrErr = $objUpFile->checkExists();
        if (count($this->arrErr) > 0) {
            return null;
        }

        // 一時ファイル名の取得
        $filepath = $objUpFile->getTempFilePath('csv_file');
	 */

	// 取込ファイルパス
        $filepath = INOS_DIR_RECV_REGULAR . $fileName;

        // CSVファイルの文字コード変換
        $enc_filepath
            = SC_Utils_Ex::sfEncodeFile($filepath, CHAR_CODE,
                                        CSV_SAVE_REALDIR, 'cp932');

        // 登録対象の列数
        $col_max_count = $objFormParam->getCount();
        // 行数
        $line_count = 0;
        $errFlag = false;

        $fp = fopen($enc_filepath, 'r');
        while (!feof($fp)) {
            $arrCSV = fgetcsv($fp, CSV_LINE_MAX);
            // 行カウント
            $line_count++;
            // 空行はスキップ
            if (empty($arrCSV)) {
                continue;
            }
            // 列数が異なる場合はエラー
            $col_count = count($arrCSV);
            if ($col_max_count != $col_count) {
                $this->addRowErr($fileName, $line_count,
                                 "※ 項目数が" . $col_count .
                                 "個検出されました。項目数は" .
                                 $col_max_count . "個になります。");
                // 完了画面でエラー表示
	            $this->tpl_mainpage = 'order/inos_import_teiki_complete.tpl';
                $errFlag = true;
                break;
            }
        }
        // エラー発生時
        if ($errFlag) {
            return;
        }
        return $enc_filepath;
    }

    /*
     * ファイル情報の初期化を行う.
     */
    function lfInitFile(&$objUpFile) {
        $objUpFile->addFile
	    ("CSVファイル", 'csv_file', array('csv'), CSV_SIZE,
	     true, 0, 0, false);
    }

    /*
     * 入力情報の初期化を行う
     */
    function lfInitParam(&$objFormParam, &$arrCsvFrame) {
        // CSV項目毎の処理
        foreach ($arrCsvFrame as $item) {
            //サブクエリ構造の場合は AS名 を使用
            if (preg_match_all('/\(.+\)\s+as\s+(.+)$/i',
                               $item['col'], $match, PREG_SET_ORDER)) {
                $col = $match[0][1];
            } else {
                $col = $item['col'];
            }

            // HTML_TAG_CHECKは別途実行なので除去し、別保存しておく
            if (strpos(strtoupper($item['error_check_types']),
		       'HTML_TAG_CHECK') !== FALSE) {
                $this->arrTagCheckItem[] = $item;
                $error_check_types = str_replace
		    ('HTML_TAG_CHECK', '',
		     $item['error_check_types']);
            } else {
                $error_check_types = $item['error_check_types'];
            }

            $arrErrorCheckTypes = explode(',', $error_check_types);
            foreach ($arrErrorCheckTypes as $key => $val) {
                if (trim($val) == "") {
                    unset($arrErrorCheckTypes[$key]);
                } else {
                    $arrErrorCheckTypes[$key] = trim($val);
                }
            }

            // パラメーター登録
            $objFormParam->addParam
                ($item['disp_name'],
                 $col,
                 constant($item['size_const_type']),
                 $item['mb_convert_kana_option'],
                 $arrErrorCheckTypes,
                 $item['default'],
                 ($item['rw_flg'] != CSV_COLUMN_RW_FLG_READ_ONLY)
                 ? true : false);
        }
    }

    /**
     * 取込完了のメッセージをプロパティへ追加する
     *
     * @param integer $line_max 件数
     * @param stirng $message メッセージ
     * @return void
     */
    function addRowCompleteMsg($line_max) {
        $this->arrRowResult[] = "取込結果：". $line_max
                              . "件の取込が完了しました。";
    }

    /*
     * エラーファイル出力
     */
    function doOutputErrCSV() {
        $sql =<<<__EOS
select regular_base_no
     , customer_cd
     , date_format(order_date, '%Y/%m/%d') as order_date
     , order_kana
     , order_name
     , order_tel
     , order_zip
     , order_addr_kana
     , order_addr01
     , order_addr02
     , course_cd
     , status
     , todoke_kbn
     , todoke_day
     , todoke_week
     , todoke_week2
     , date_format(next_arrival_date, '%Y/%m/%d') as next_arrival_date
     , next_ship_date
     , date_format(after_next_arrival_date, '%Y/%m/%d') as after_next_arrival_date
     , after_next_ship_date
     , date_format(cancel_date, '%Y/%m/%d') as cancel_date
     , cancel_reason_cd
     , shipment_cd
     , deliv_id
     , box_size
     , invoice_num
     , time_id
     , remarks
     , include_kbn
     , payment_id
     , deliv_fee
     , buy_num
     , order_id
     , del_flg
     , date_format(update_date, '%Y/%m/%d %H:%i:%s') as update_date
     , line_no
     , product_code
     , product_name
     , quantity
     , price
     , cut_rate
     , error_name
  from dtb_regular_inos_import
 where error_flg = 1
__EOS;

        // ヘッダー行生成
        $arrHeader = array
	    ("コース受注NO", "顧客CD", "受注日", "送付先カナ氏名", "送付先漢字氏名",
	     "送付先電話番号", "送付先郵便番号", "送付先カナ住所", "送付先住所1",
	     "送付先住所2", "コースCD","状況フラグ", "届け日指定区分", "お届け日", 
         "曜日指定1", "曜日指定2", "次回届け日", "次回出荷日", "次々回届け日", 
         "次々回出荷日", "キャンセル日", "キャンセル理由CD", "出荷場所CD", 
         "宅配便CD", "箱サイズ", "送り状枚数","配達時間CD", "指定条件",
         "明細書同梱区分", "支払方法CD", "税込送料", "購入済回数", "WEB受注NO",
         "削除フラグ", "更新日時", "行NO", "商品CD", "商品名称", "数量", "税込単価",
         "値引率", "エラー内容");

        // CSVダウンロード実行
        $objCsv = new SC_Helper_CSV_Ex();
        $objCsv->sfDownloadCsvFromSql
            ($sql, null, 'error_regular', $arrHeader, true);
    }

    /**
     * 登録/編集結果のエラーメッセージをプロパティへ追加する
     *
     * @param integer $line_count 行数
     * @param stirng $message メッセージ
     * @return void
     */
    function addRowErr($file_name, $line_count, $message) {
	$this->arrRowErr[] = sprintf("[%s]%d行目：%s", $file_name, $line_count
				    , $message);
    }

    /**
     * mysqliのログ出力
     *
     * @param object $mysqli mysqliオブジェクト
     * @param string $sql SQL文
     * @param string $function ファンクション名
     * @return void
     */
    function outputMysqliErrorMsg(&$mysqli, $sql, $function) {
        GC_Utils_Ex::gfPrintLog($function. "()：エラー情報あり");
        GC_Utils_Ex::gfPrintLog('エラー番号：'. $mysqli->errno);
        GC_Utils_Ex::gfPrintLog('エラーメッセージ：'. $mysqli->error);
        GC_Utils_Ex::gfPrintLog('SQL：'. $sql);
        $this->arrRowErr[] = "システムエラーが発生しました。";
    }
}

?>
