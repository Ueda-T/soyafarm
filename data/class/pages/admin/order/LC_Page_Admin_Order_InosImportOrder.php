<?php
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';
require_once CLASS_EX_REALDIR . 'helper_extends/SC_Helper_CSV_Ex.php';

/**
 * 顧客マスタ登録CSVのページクラス.
 */
class LC_Page_Admin_Order_InosImportOrder extends LC_Page_Admin_Ex {

    var $arrRowResult;

    var $INOS_DELIV_ID_YAMATO;
    var $DELIV_ID_YAMATO;
    var $DELIV_ID_YAMATO_MAIL;
    var $DELIV_BOX_ID_TAKUHAI;
    var $DELIV_BOX_ID_MAIL;

    /** 削除用時必須チェック不要カラム **/
    var $arrNoExistsDelPtnCol;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'order/inos_import_order.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'inos_import_order';
        $this->tpl_maintitle = '受注関連';
        $this->tpl_subtitle = 'INOS受注情報インポート';
        $this->csv_id = '9';

        // 基幹宅配便コード
        $this->INOS_DELIV_ID_YAMATO = INOS_DELIV_ID_YAMATO;

        // WEB宅配便コード
        $this->DELIV_ID_YAMATO = DELIV_ID_YAMATO;
        $this->DELIV_ID_YAMATO_MAIL = DELIV_ID_YAMATO_MAIL;

        // WEB箱ID
        $this->DELIV_BOX_ID_TAKUHAI = DELIV_BOX_ID_TAKUHAI;
        $this->DELIV_BOX_ID_MAIL = DELIV_BOX_ID_MAIL;

        // 削除時用の必須チェック不要カラム
        $this->arrNoExistsDelPtnCol = array('customer_cd'
                                          , 'create_date'
                                          , 'shipping_name'
                                          , 'shipping_tel'
                                          , 'shipping_zip'
                                          , 'shipping_addr01'
                                          , 'course_cd'
                                          , 'status'
                                          , 'commit_date'
                                          , 'shipping_area_code'
                                          , 'deliv_id'
                                          , 'deliv_box_id'
                                          , 'invoice_num'
                                          , 'time_id'
                                          , 'deliv_kbn'
                                          , 'cool_kbn'
                                          , 'include_kbn'
                                          , 'payment_id'
                                          , 'subtotal'
                                          , 'deliv_fee'
                                          , 'payment_total'
                                          , 'order_kbn'
                                          , 'regular_base_no'
                                          , 'line_no'
                                          , 'product_code'
                                          , 'product_name'
                                          , 'quantity'
                                          , 'price'
                                          , 'price_total'
                                          , 'cut_rate'
                                           );

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
        $this->objDb = new SC_Helper_DB_Ex();

        // CSV管理ヘルパー
        $objCSV = new SC_Helper_CSV_Ex();
        // CSV構造読み込み
        $arrCSVFrame = $objCSV->sfGetCsvOutput($this->csv_id);

        // CSV構造がインポート可能かのチェック
        if (!$objCSV->sfIsImportCSVFrame($arrCSVFrame)) {
            // 無効なフォーマットなので初期状態に強制変更
            $arrCSVFrame = $objCSV->sfGetCsvOutput($this->csv_id, '', array(), 'no');
            $this->tpl_is_format_default = true;
        }

        // CSV構造は更新可能なフォーマットかのフラグ取得
        $this->tpl_is_update = $objCSV->sfIsUpdateCSVFrame($arrCSVFrame);

        // CSVファイルアップロード情報の初期化
        $objUpFile = new SC_UploadFile_Ex(CSV_TEMP_REALDIR, CSV_SAVE_REALDIR);
        $this->lfInitFile($objUpFile);

        // パラメーター情報の初期化
        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam, $arrCSVFrame);

        $objFormParam->setHtmlDispNameArray();
        $this->arrTitle = $objFormParam->getHtmlDispNameArray();

        switch ($this->getMode()) {
        case 'csv_upload':
            $this->importCsvFile($objFormParam, $objUpFile);
            break;
        case 'errcsv_download':
            $this->doOutputErrCSV();
            exit;
            break;

        default:
            break;
        }
    }

    // 一時テーブルへ登録
    function loadCsvFile($file) {

    $sql =<<<__EOS
truncate table dtb_order_inos_import
;
set character_set_database=utf8
;
load data local infile '{$file}' into table dtb_order_inos_import
fields terminated by ',' enclosed by '"' lines terminated by '\r\n'
set order_base_no = nullif(order_base_no, '')
  , customer_cd = nullif(customer_cd, '')
  , create_date = nullif(create_date, '0000-00-00 00:00:00')
  , shipping_kana = nullif(shipping_kana, '')
  , shipping_name = nullif(shipping_name, '')
  , shipping_tel = nullif(shipping_tel, '')
  , shipping_zip = nullif(shipping_zip, '')
  , shipping_addr_kana = nullif(shipping_addr_kana, '')
  , shipping_addr01 = nullif(shipping_addr01, '')
  , shipping_addr02 = nullif(shipping_addr02, '')
  , course_cd = nullif(course_cd, '')
  , status = nullif(status, '')
  , commit_date = nullif(commit_date, '0000-00-00')
  , shipping_area_code = nullif(shipping_area_code, '')
  , deliv_id = nullif(deliv_id, '')
  , deliv_box_id = nullif(deliv_box_id, '')
  , invoice_num = nullif(invoice_num, '')
  , shipping_date = nullif(shipping_date, '0000-00-00 00:00:00')
  , time_id = nullif(time_id, '')
  , note = nullif(note, '')
  , deliv_kbn = nullif(deliv_kbn, '')
  , cool_kbn = nullif(cool_kbn, '')
  , shipping_num = nullif(shipping_num, '')
  , include_kbn = nullif(include_kbn, '')
  , payment_id = nullif(payment_id, '')
  , subtotal = nullif(subtotal, '')
  , deliv_fee = nullif(deliv_fee, '')
  , payment_total = nullif(payment_total, '')
  , purchase_motive_code = nullif(purchase_motive_code, '')
  , input_assistance_code = nullif(input_assistance_code, '')
  , event_code = nullif(event_code, '')
  , order_kbn = nullif(order_kbn, '')
  , regular_base_no = nullif(regular_base_no, '')
  , web_order_no = nullif(web_order_no, '')
  , web_customer_id = nullif(web_customer_id, '')
  , del_flg = nullif(del_flg, '')
  , update_date = nullif(update_date, '0000-00-00 00:00:00')
  , line_no = nullif(line_no, '')
  , product_code = nullif(product_code, '')
  , product_name = nullif(product_name, '')
  , quantity = nullif(quantity, '')
  , price = nullif(price, '')
  , price_total = nullif(price_total, '')
  , cut_rate = nullif(cut_rate, '')
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

    // インポートデータ共通
    function inosOrderImportCheck(&$objFormParam) {
        // 受注NO必須チェック
        $this->importOrderBaseNoCheck();
        // 項目チェック
        $this->orderColCheck($objFormParam);
        // 基幹顧客CDとWEB顧客CDから顧客情報の存在チェック
        $this->importExistWebCustomerIdCheck();
        // 行NO重複チェック
        $this->importLineNoCheck();
    }

    /* 項目チェック
     *
     * param  $objFormParam
     * return void
     */
    function orderColCheck(&$objFormParam) {

    $sql =<<<__EOS
select *
  from dtb_order_inos_import imp
 where error_flg = 0 
__EOS;

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // 一時テーブルのデータ取得
        $arrImportData = $objQuery->getAll($sql);

        // 一行ずつ読込
        foreach ($arrImportData as $arrData) {

        // 項目がNULLの場合は空文字を挿入
        foreach ($arrData as $key => &$val) {
            if ($val == null) {
                $val = '';
            }
        }

        // シーケンス配列を格納する。
        $objFormParam->setParam($arrData);
        // 入力値の変換
        $objFormParam->convParam();

        // 項目チェック処理実行、基幹受注NOをキーに結果取得
        $arrErrData[][$arrData["order_base_no"]][$arrData["line_no"]]
            = $this->lfCheckError($objFormParam);
        }

        // 結果配列ループ
        foreach ($arrErrData as $arrVal) {
            foreach ($arrVal as $order_base_no => $arrDetail) {
                foreach ($arrDetail as $line_no => $arrErrMsg) {

                    // エラー結果がなければスキップ
                    if (count($arrVal[$order_base_no]) == 0) {
                        continue;
                    }
                    if (count($arrDetail[$line_no]) == 0) {
                        continue;
                    }
                    // 配列のエラーメッセージを文字列に変換
                    $err_msg = implode($arrErrMsg);

                    // エラーデータに更新
                    $sql =<<<__EOS
        update dtb_order_inos_import imp
           set imp.error_flg = 1
             , imp.error_name = '{$err_msg}'
         where imp.order_base_no = '{$order_base_no}'
         and imp.line_no = '{$line_no}'
__EOS;
                    $objQuery->query($sql);
                }
            }
        }
        return;
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

            // 必須チェック消去
            $objDelFormParam->removeCheck
                ($this->arrNoExistsDelPtnCol, 'EXIST_CHECK');

            $arrErr = 
                $objDelFormParam->checkError(false);
        // 通常
        } else {
            $arrErr = 
                $objFormParam->checkError(false);
        }
        return $arrErr;
    }

    // 追加パターン
    function insertCheck() {
        ; // 処理なし
    }

    // 更新パターン
    function updateCheck() {
        ; // 処理なし
    }

    // 削除パターン
    function deleteCheck() {
        // WEB顧客CDから顧客情報の存在チェック
        $this->deleteExistCustomerCheck();
        // WEB受注IDから受注情報の存在チェック
        $this->deleteExistWebOrderNoCheck();
    }

    // 基幹顧客CDからWEB顧客CDの存在チェック
    function importExistWebCustomerIdCheck() {

    $sql =<<<__EOS
    update dtb_order_inos_import t1
       set t1.error_flg = 1
         , t1.error_name = '基幹顧客CDとWEB顧客CDから対象の顧客情報が見つかりません。'
     where t1.customer_cd is not null
       and t1.web_customer_id is not null
       and t1.error_flg = 0
       and not exists (select 'X'
                         from dtb_customer c
                        where c.customer_cd = t1.customer_cd
                          and c.customer_id = t1.web_customer_id
                          and c.del_flg = 0)
__EOS;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->query($sql);
    }

    // 基幹受注NOからWEB受注NOの付与
    function addWebOrderNoByOrderBaseNo() {

    // ※基幹受注NOが既に受注情報に取り込まれている場合、WEB受注NOをセット
    // 登録データ → 更新データへ変更 
    $sql =<<<__EOS
    update dtb_order_inos_import imp
inner join dtb_order o
        on o.order_base_no = imp.order_base_no
       set imp.web_order_no = o.order_id
     where imp.web_order_no is null
       and imp.error_flg = 0
;
__EOS;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->query($sql);
    }

    // 削除パターンWEB顧客CDから顧客情報の存在チェック
    function deleteExistCustomerCheck() {

    $sql =<<<__EOS
    update dtb_order_inos_import t1
       set t1.error_flg = 1
         , t1.error_name = '削除データ - WEB顧客CDから対象の顧客情報が見つかりません。'
     where t1.web_customer_id is not null
       and t1.del_flg = 1
       and t1.error_flg = 0
       and not exists (select 'X'
                         from dtb_customer c
                        where c.customer_id = t1.web_customer_id
                          and c.del_flg = 0)
__EOS;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->query($sql);
    }

    // 削除パターンWEB受注IDから受注情報の存在チェック
    function deleteExistWebOrderNoCheck() {

    $sql =<<<__EOS
    update dtb_order_inos_import t1
       set t1.error_flg = 1
         , t1.error_name = '削除データ - WEB受注IDから対象の受注情報が見つかりません。'
     where t1.web_order_no is not null
       and t1.del_flg = 1
       and t1.error_flg = 0
       and not exists (select 'X'
                         from dtb_order o
                        where o.order_id = t1.web_order_no)
__EOS;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->query($sql);
    }

    // 受注明細エラーチェック
    function importExistsDetailCheck() {

    $sql =<<<__EOS
update dtb_order_inos_import a
inner join dtb_order_inos_import b 
on a.order_base_no = b.order_base_no 
and b.error_flg = 1
set a.error_flg = 1
where a.error_flg = 0
__EOS;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->query($sql);
    }

    // 基幹受注NO必須チェック
    function importOrderBaseNoCheck() {

    $sql =<<<__EOS
update dtb_order_inos_import t1
   set t1.error_flg = 1
     , t1.error_name = '基幹受注NO - 必須項目エラー'
 where t1.order_base_no is null
   and t1.error_flg = 0
__EOS;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->query($sql);
    }

    // 行NOの重複チェック
    function importLineNoCheck() {

    $sql =<<<__EOS
update dtb_order_inos_import t1
     , (select order_base_no
          from dtb_order_inos_import
      group by order_base_no, line_no
        having count(line_no) > 1) t2
   set t1.error_flg = 1
     , t1.error_name = 'インポートファイル内 行NO重複エラー'
 where t1.order_base_no = t2.order_base_no

__EOS;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->query($sql);
    }

    // インポート初期処理
    function prepareImport(&$objFormParam) {
        // インポートデータの共通チェック
        $this->inosOrderImportCheck($objFormParam);
        // 追加パターンのチェック
        $this->insertCheck();
        // 更新パターンのチェック
        $this->updateCheck();
        // 削除パターンのチェック
        $this->deleteCheck();
        // 受注明細エラーチェック
        $this->importExistsDetailCheck();
    }

    // インポート実行処理
    function doImport() {
    $objQuery =& SC_Query_Ex::getSingletonInstance();

    // 基幹受注NOからWEB受注NOを付与
    if(!$this->addWebOrderNoByOrderBaseNo()) {
        return array(0, INOS_ERROR_FLG_EXIST_ERROR);
    }
    // 追加
    if(!$this->insertImport()) {
        return array(0, INOS_ERROR_FLG_EXIST_ERROR);
    }
    // 更新
    if(!$this->updateImport()) {
        return array(0, INOS_ERROR_FLG_EXIST_ERROR);
    }
    // 削除
    if(!$this->deleteImport()) {
        return array(0, INOS_ERROR_FLG_EXIST_ERROR);
    }
    /*
    // 受注プロモーション登録
    if(!$this->orderPromotionImport()) {
        return array(0, INOS_ERROR_FLG_EXIST_ERROR);
    }
    */
    // 取込んだ件数取得
    $count = $objQuery->count
        ('dtb_order_inos_import', 'error_flg = ?', 0);

    return array($count, INOS_ERROR_FLG_EXIST_NORMAL);
    }

    // 新規登録インポート処理
    function insertImport() {

    $objQuery =& SC_Query_Ex::getSingletonInstance();

    // 受注情報ID採番
    $SEQ = $objQuery->currVal('dtb_order_order_id');
    // 受注詳細情報ID採番
    $SEQ_DETAIL = $objQuery->currVal('dtb_order_detail_order_detail_id');

    // 受注IDをセット
    $sql =<<<__EOS
set @i := {$SEQ};
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }

    // 受注詳細IDをセット
    $sql =<<<__EOS
set @k := {$SEQ_DETAIL};
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }

    // 受注情報登録処理
    $sql =<<<__EOS
INSERT INTO dtb_order (
    order_id
   ,order_base_no
   ,customer_id
   ,order_name
   ,order_kana
   ,order_email
   ,order_tel
   ,order_zip
   ,order_pref
   ,order_addr01
   ,order_addr02
   ,order_sex
   ,order_birth
   ,subtotal
   ,deliv_id
   ,deliv_box_id
   ,invoice_num
   ,deliv_fee
   ,tax
   ,total
   ,payment_total
   ,payment_id
   ,payment_method
   ,note
   ,status
   ,create_date
   ,update_date
   ,commit_date
   ,device_type_id
   ,include_kbn
   ,send_flg
   ,recv_date
   ,purchase_motive_code
   ,input_assistance_code
   ,event_code
   ,regular_base_no
   ,del_flg
)
SELECT
    (@i := @i + 1) AS order_id
   ,IM.order_base_no
   ,CS.customer_id
   ,CS.name
   ,CS.kana
   ,CS.email
   ,CS.tel
   ,CS.zip
   ,CS.pref
   ,CS.addr01
   ,CS.addr02
   ,CS.sex
   ,CS.birth
   ,IM.subtotal
   ,CASE WHEN IM.deliv_id = {$this->INOS_DELIV_ID_YAMATO}
     AND IM.deliv_box_id = {$this->DELIV_BOX_ID_MAIL}
    THEN {$this->DELIV_ID_YAMATO_MAIL}
    WHEN IM.deliv_id = {$this->INOS_DELIV_ID_YAMATO}
     AND IM.deliv_box_id = {$this->DELIV_BOX_ID_TAKUHAI}
    THEN {$this->DELIV_ID_YAMATO} END AS deliv_id
   ,IM.deliv_box_id
   ,IM.invoice_num
   ,IM.deliv_fee
   ,0
   ,(IM.subtotal + IM.deliv_fee) AS total
   ,IM.payment_total
   ,IM.payment_id
   ,PM.payment_method
   ,IM.note
   ,IM.status
   ,IM.create_date
   ,IM.update_date
   ,IM.commit_date
   ,DVC.id AS device_type_id
   ,IM.include_kbn
   ,1
   ,NOW()
   ,IM.purchase_motive_code
   ,IM.input_assistance_code
   ,IM.event_code
   ,IM.regular_base_no
   ,IM.del_flg
FROM
    dtb_order_inos_import IM
LEFT JOIN dtb_customer CS
       ON CS.customer_cd = IM.customer_cd
LEFT JOIN mtb_device_type DVC
       ON IM.order_kbn = DVC.kikan_id
LEFT JOIN dtb_payment PM
       ON PM.payment_id = IM.payment_id
 WHERE IM.error_flg = 0
   AND IM.del_flg = 0
   AND IM.web_order_no is null
 GROUP BY IM.order_base_no
;
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }

    // 受注情報IDの最大値を更新
    $sql =<<<__EOS
UPDATE dtb_order_order_id_seq
   SET sequence = (SELECT MAX(order_id)
                     FROM dtb_order)
;
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }

    // 配送先情報の登録
    $sql =<<<__EOS
INSERT INTO dtb_shipping (
    shipping_id
   ,order_id
   ,shipping_name
   ,shipping_kana
   ,shipping_tel
   ,shipping_pref
   ,shipping_zip
   ,shipping_addr01
   ,shipping_addr02
   ,shipping_addr_kana
   ,deliv_id
   ,time_id
   ,shipping_time
   ,shipping_num
   ,shipping_date
   ,shipping_area_code
   ,shipping_commit_date
   ,deliv_kbn
   ,cool_kbn
   ,send_mail_flg
   ,create_date
   ,update_date
   ,del_flg
)
SELECT
    0
   ,ORD.order_id
   ,IM.shipping_name
   ,IM.shipping_kana
   ,IM.shipping_tel
   ,PR.id AS shipping_pref
   ,IM.shipping_zip
   ,replace(IM.shipping_addr01, IFNULL(PR.NAME, ''), '') AS shipping_addr01
   ,IM.shipping_addr02
   ,IM.shipping_addr_kana
   ,CASE WHEN IM.deliv_id = {$this->INOS_DELIV_ID_YAMATO}
     AND IM.deliv_box_id = {$this->DELIV_BOX_ID_MAIL}
    THEN {$this->DELIV_ID_YAMATO_MAIL}
    WHEN IM.deliv_id = {$this->INOS_DELIV_ID_YAMATO}
     AND IM.deliv_box_id = {$this->DELIV_BOX_ID_TAKUHAI}
    THEN {$this->DELIV_ID_YAMATO} END AS deliv_id
   ,IM.time_id
   ,(SELECT DT.deliv_time AS shipping_time
       FROM dtb_delivtime DT
      WHERE DT.time_id = IM.time_id
        AND DT.deliv_id = CASE WHEN IM.deliv_id = {$this->INOS_DELIV_ID_YAMATO}
                                AND IM.deliv_box_id = {$this->DELIV_BOX_ID_MAIL}
                               THEN {$this->DELIV_ID_YAMATO_MAIL}
                               WHEN IM.deliv_id = {$this->INOS_DELIV_ID_YAMATO}
                                AND IM.deliv_box_id = {$this->DELIV_BOX_ID_TAKUHAI}
                               THEN {$this->DELIV_ID_YAMATO} END
    )
   ,IM.shipping_num
   ,IM.shipping_date
   ,IM.shipping_area_code
   ,IM.commit_date
   ,IM.deliv_kbn
   ,IM.cool_kbn
   ,0
   ,IM.create_date
   ,IM.update_date
   ,IM.del_flg
FROM
    dtb_order_inos_import IM
INNER JOIN dtb_order ORD
        ON ORD.order_base_no = IM.order_base_no
LEFT OUTER JOIN mtb_zip ZP
        ON ZP.zipcode = replace(IM.shipping_zip, '-', '')
LEFT OUTER JOIN mtb_pref PR
        ON PR.name = ZP.state
     WHERE IM.error_flg = 0
       AND IM.web_order_no is null
       AND IM.del_flg = 0
  GROUP BY IM.order_base_no
;
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }

    // 受注詳細情報の登録
    $sql =<<<__EOS
INSERT INTO dtb_order_detail (
    order_detail_id
   ,order_id
   ,product_id
   ,product_class_id
   ,product_name
   ,product_code
   ,price
   ,quantity
   ,cut_rate
   ,course_cd
   ,sell_flg
)
SELECT
    (@k := @k + 1) AS order_detail_id
   ,ORD.order_id
   ,PC.product_id
   ,PC.product_class_id
   ,IM.product_name
   ,IM.product_code
   ,IM.price
   ,IM.quantity
   ,IM.cut_rate
   ,CASE WHEN IM.course_cd = 30 THEN 1
    WHEN IM.course_cd = 60 THEN 2
    WHEN IM.course_cd = 90 THEN 3
    ELSE IM.course_cd END AS course_cd
   ,1
FROM
    dtb_order_inos_import IM
INNER JOIN dtb_order ORD
        ON IM.order_base_no = ORD.order_base_no
LEFT OUTER JOIN dtb_products_class PC
        ON IM.product_code = PC.product_code
       AND PC.del_flg = 0
     WHERE IM.error_flg = 0
       AND IM.web_order_no is null
       AND IM.del_flg = 0
;
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }

    // 受注詳細IDの最大値を更新
    $sql =<<<__EOS
UPDATE dtb_order_detail_order_detail_id_seq
   SET sequence = (SELECT MAX(order_detail_id)
                     FROM dtb_order_detail)
;
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }
        return true;
    }

    // 更新インポート処理
    function updateImport() {

    $objQuery =& SC_Query_Ex::getSingletonInstance();

    // 受注詳細情報IDの最大値を取得
    $SEQ_DETAIL = $objQuery->currVal('dtb_order_detail_order_detail_id');

    // 受注詳細IDをセット
    $sql =<<<__EOS
set @k := {$SEQ_DETAIL};
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }

    // 受注情報の更新
    $sql =<<<__EOS
    update dtb_order o
inner join (SELECT IM.web_order_no
                  ,IM.order_base_no
                  ,IM.web_customer_id
                  ,IM.subtotal
                  ,CASE WHEN IM.deliv_id = {$this->INOS_DELIV_ID_YAMATO}
                    AND IM.deliv_box_id = {$this->DELIV_BOX_ID_MAIL}
                   THEN {$this->DELIV_ID_YAMATO_MAIL}
                   WHEN IM.deliv_id = {$this->INOS_DELIV_ID_YAMATO}
                    AND IM.deliv_box_id = {$this->DELIV_BOX_ID_TAKUHAI}
                   THEN {$this->DELIV_ID_YAMATO} END AS deliv_id
                  ,IM.deliv_box_id
                  ,IM.invoice_num
                  ,IM.deliv_fee
                  ,(IM.subtotal + IM.deliv_fee) AS total
                  ,IM.payment_total
                  ,IM.payment_id
                  ,PM.payment_method
                  ,IM.note
                  ,IM.status
                  ,IM.update_date
                  ,IM.commit_date
                  ,IM.include_kbn
                  ,IM.purchase_motive_code
                  ,IM.input_assistance_code
                  ,IM.event_code
                  ,IM.regular_base_no
                  ,IM.del_flg
               FROM dtb_order_inos_import IM
          LEFT JOIN dtb_payment PM
                 ON PM.payment_id = IM.payment_id
              WHERE IM.error_flg = 0
                AND IM.del_flg = 0
                AND IM.web_order_no is not null
           GROUP BY IM.order_base_no) imp2
             on o.order_id = imp2.web_order_no

            set o.order_base_no = imp2.order_base_no
               ,o.customer_id = imp2.web_customer_id
               ,o.subtotal = imp2.subtotal
               ,o.deliv_id = imp2.deliv_id
               ,o.deliv_box_id = imp2.deliv_box_id
               ,o.invoice_num = imp2.invoice_num
               ,o.deliv_fee = imp2.deliv_fee
               ,o.total = imp2.total
               ,o.payment_total = imp2.payment_total
               ,o.payment_id = imp2.payment_id
               ,o.payment_method = imp2.payment_method
               ,o.note = imp2.note
               ,o.status = imp2.status
               ,o.update_date = imp2.update_date
               ,o.commit_date = imp2.commit_date
               ,o.include_kbn = imp2.include_kbn
               ,o.recv_date = NOW()
               ,o.purchase_motive_code = imp2.purchase_motive_code
               ,o.input_assistance_code = imp2.input_assistance_code
               ,o.event_code = imp2.event_code
               ,o.regular_base_no = imp2.regular_base_no
               ,o.del_flg = imp2.del_flg
;
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }

    // 配送先情報の更新
    $sql =<<<__EOS
    update dtb_shipping s
inner join (SELECT
               IM.web_order_no
              ,IM.shipping_name
              ,IM.shipping_kana
              ,IM.shipping_tel
              ,PR.id AS shipping_pref
              ,IM.shipping_zip
              ,replace(IM.shipping_addr01, IFNULL(PR.NAME, ''), '') AS shipping_addr01
              ,IM.shipping_addr02
              ,IM.shipping_addr_kana
              ,CASE WHEN IM.deliv_id = {$this->INOS_DELIV_ID_YAMATO}
                AND IM.deliv_box_id = {$this->DELIV_BOX_ID_MAIL}
               THEN {$this->DELIV_ID_YAMATO_MAIL}
               WHEN IM.deliv_id = {$this->INOS_DELIV_ID_YAMATO}
                AND IM.deliv_box_id = {$this->DELIV_BOX_ID_TAKUHAI}
               THEN {$this->DELIV_ID_YAMATO} END AS deliv_id
              ,IM.time_id
              ,(SELECT DT.deliv_time
                  FROM dtb_delivtime DT
                 WHERE DT.time_id = IM.time_id
                   AND DT.deliv_id = 
                       CASE WHEN IM.deliv_id = {$this->INOS_DELIV_ID_YAMATO}
                             AND IM.deliv_box_id = {$this->DELIV_BOX_ID_MAIL}
                            THEN {$this->DELIV_ID_YAMATO_MAIL}
                            WHEN IM.deliv_id = {$this->INOS_DELIV_ID_YAMATO}
                             AND IM.deliv_box_id = {$this->DELIV_BOX_ID_TAKUHAI}
                            THEN {$this->DELIV_ID_YAMATO} END
               ) AS shipping_time
              ,IM.shipping_num
              ,IM.shipping_date
              ,IM.shipping_area_code
              ,IM.commit_date
              ,IM.deliv_kbn
              ,IM.cool_kbn
              ,IF(IM.commit_date = null, 0, 1)
              ,IM.create_date
              ,IM.update_date
              ,IM.del_flg
              FROM dtb_order_inos_import IM
   LEFT OUTER JOIN mtb_zip ZP
                ON ZP.zipcode = replace(IM.shipping_zip, '-', '')
   LEFT OUTER JOIN mtb_pref PR
                ON PR.name = ZP.state
             WHERE IM.error_flg = 0
               AND IM.del_flg = 0
               AND IM.web_order_no is not null
          GROUP BY IM.order_base_no) imp2

             on s.order_id = imp2.web_order_no

            set s.shipping_name = imp2.shipping_name
               ,s.shipping_kana = imp2.shipping_kana
               ,s.shipping_tel = imp2.shipping_tel
               ,s.shipping_pref = imp2.shipping_pref
               ,s.shipping_zip = imp2.shipping_zip
               ,s.shipping_addr01 = imp2.shipping_addr01
               ,s.shipping_addr02 = imp2.shipping_addr02
               ,s.shipping_addr_kana = imp2.shipping_addr_kana
               ,s.deliv_id = imp2.deliv_id
               ,s.time_id = imp2.time_id
               ,s.shipping_time = imp2.shipping_time
               ,s.shipping_num = imp2.shipping_num
               ,s.shipping_date = imp2.shipping_date
               ,s.shipping_area_code = imp2.shipping_area_code
               ,s.shipping_commit_date = imp2.commit_date
               ,s.deliv_kbn = imp2.deliv_kbn
               ,s.cool_kbn = imp2.cool_kbn
               ,s.update_date = imp2.update_date
               ,s.del_flg = imp2.del_flg
;
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }

    // 受注詳細情報の物理削除
    $sql =<<<__EOS
    delete dtb_order_detail od
      from dtb_order_detail od
inner join dtb_order_inos_import imp
        on od.order_id = imp.web_order_no
     where imp.error_flg = 0
       and imp.web_order_no is not null
       and imp.del_flg = 0
;
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }

    // 受注詳細情報の登録処理
    $sql =<<<__EOS
INSERT INTO dtb_order_detail (
    order_detail_id
   ,order_id
   ,product_id
   ,product_class_id
   ,product_name
   ,product_code
   ,price
   ,quantity
   ,cut_rate
   ,course_cd
   ,sell_flg
)
SELECT
    (@k := @k + 1) AS order_detail_id
   ,ORD.order_id
   ,PC.product_id
   ,PC.product_class_id
   ,IM.product_name
   ,IM.product_code
   ,IM.price
   ,IM.quantity
   ,IM.cut_rate
   ,CASE WHEN IM.course_cd = 30 THEN 1
    WHEN IM.course_cd = 60 THEN 2
    WHEN IM.course_cd = 90 THEN 3
    ELSE IM.course_cd END AS course_cd
   ,1
FROM
    dtb_order_inos_import IM
INNER JOIN dtb_order ORD
        ON IM.order_base_no = ORD.order_base_no
LEFT OUTER JOIN dtb_products_class PC
        ON IM.product_code = PC.product_code
       AND PC.del_flg = 0
     WHERE IM.error_flg = 0
       AND IM.del_flg = 0
       AND IM.web_order_no is not null
;
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }

    // 受注詳細情報の受注IDの最大値を更新
    $sql =<<<__EOS
UPDATE dtb_order_detail_order_detail_id_seq
   SET sequence = (SELECT MAX(order_detail_id)
                     FROM dtb_order_detail)
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

    // 受注情報の削除処理
    $sql =<<<__EOS
    update dtb_order o
inner join dtb_order_inos_import imp
        on o.order_id = imp.web_order_no
       set o.del_flg = imp.del_flg
         , o.order_base_no = imp.order_base_no
         , o.update_date = imp.update_date
         , o.recv_date = NOW()
     where imp.error_flg = 0
       and imp.del_flg = 1
;
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }

    // 配送先情報の削除処理
    $sql =<<<__EOS
    update dtb_shipping s
inner join dtb_order_inos_import imp
        on s.order_id = imp.web_order_no
       set s.del_flg = imp.del_flg
         , s.update_date = imp.update_date
     where imp.error_flg = 0
       and imp.del_flg = 1
;
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }

    return true;
    }

    // 受注プロモーションインポート処理
    function orderPromotionImport() {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // プロモーション物理削除
        $sql =<<<__EOS
    delete dtb_order_promotion op
      from dtb_order_promotion op
inner join dtb_order o
        on o.order_id = op.order_id
inner join dtb_order_inos_import imp
        on imp.order_base_no = o.order_base_no
     where imp.error_flg = 0
       and imp.del_flg = 0
;
__EOS;
        if (!$objQuery->query($sql)) {
            return false;
        }

        // プロモーションが適用されているデータ抽出
        $sql =<<<__EOS
    select imp.promotion_cd
         , o.order_id
      from dtb_order o
inner join dtb_order_inos_import imp
        on imp.order_base_no = o.order_base_no
     where imp.promotion_cd is not null
       and imp.error_flg = 0
       and imp.del_flg = 0
  group by imp.order_base_no
;
__EOS;
        if (!$objQuery->query($sql)) {
            return false;
        }
        $arrAllPromotionData = $objQuery->getAll($sql);

        // 30件ごとに分割 ※大量データを考慮
        $arrChunkPromotionData
            = array_chunk($arrAllPromotionData, ORDER_PROMOTION_MAX_COUNT);

        foreach ($arrChunkPromotionData as $arrPromotionData) {
            // SQL文初期化
            $sql =<<<__EOS
insert into dtb_order_promotion (
       order_id 
     , promotion_cd
) value     
__EOS;
            $cnt = 0;
            foreach ($arrPromotionData as $arrVal) {

                // カンマ区切りを配列へ
                $arrPromotion = explode(",", $arrVal["promotion_cd"]);

                // 受注情報毎にSQL生成
                foreach ($arrPromotion as $promotion_cd) {
                    // 初回
                    if ($cnt == 0) {
                        $sql .= "(". $arrVal["order_id"]. ",'". $promotion_cd. "')";
                    }
                    // 次回以降
                    if ($cnt != 0) {
                        $sql .= ",(". $arrVal["order_id"]. ",'". $promotion_cd. "')";
                    }
                    $cnt++;
                }
            }
            // クエリ実行
            if(!$objQuery->query($sql)) {
                return false;
            }
        }
        return true;
    }

    // インポートファイル取込処理
    function importCsvFile(&$objFormParam, &$objUpFile) {
    // ファイルのアップロード
    $file = $this->uploadCsvFile($objFormParam, $objUpFile);
    if (empty($file)) {
        return;
    }

    // 一時テーブルへのローディング
    if (!$this->loadCsvFile($file)) {
        $this->tpl_mainpage = 'order/inos_import_order_complete.tpl';
        return;
    }

    $objQuery =& SC_Query_Ex::getSingletonInstance();
    // インポート初期処理
    $this->prepareImport($objFormParam);

    $objQuery->begin();

    // インポート実行
    list($count, $r) = $this->doImport();
    if ($r != INOS_ERROR_FLG_EXIST_NORMAL) {
        $objQuery->rollback();
    } else {
        $objQuery->commit();
    }

    // エラーデータがある場合は更新履歴情報をエラー判定に
    $this->tpl_err_count = $objQuery->count
        ('dtb_order_inos_import', 'error_flg = ?', 1);
    if ($this->tpl_err_count > 0) {
        $r = INOS_ERROR_FLG_EXIST_ERROR;
    }

    // バッチ処理履歴情報へデータ登録
    SC_Helper_DB_Ex::sfInsertBatchHistory
        (INOS_DATA_TYPE_RECV_ORDER, $count, $r);

    // 実行結果画面を表示
    $this->tpl_mainpage = 'order/inos_import_order_complete.tpl';
    $this->addRowCompleteMsg($count);

    // 出荷済みに更新したデータについて出荷済みメールを送信する
    // TODO 2014.3.20 takao 一旦コメントアウト
     //$this->doSendMail();
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

    /**
     * CSVアップロードを実行します.
     *
     * @return void
     */
    function uploadCsvFile(&$objFormParam, &$objUpFile) {
        // ファイルアップロードのチェック
        $objUpFile->makeTempFile('csv_file');
        $this->arrErr = $objUpFile->checkExists();
        if (count($this->arrErr) > 0) {
            return null;
        }

        // 一時ファイル名の取得
        $filepath = $objUpFile->getTempFilePath('csv_file');

        // CSVファイルの文字コード変換
        $enc_filepath = 
            SC_Utils_Ex::sfEncodeFile($filepath, CHAR_CODE,
                                      CSV_TEMP_REALDIR, 'cp932');
        // 登録対象の列数
        $col_max_count = $objFormParam->getCount();
        // 行数
        $line_count = 0;
        $errFlag = false;

        // 項目数チェック
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
                $this->addRowErr($line_count,
                                 "※ 項目数が" . $col_count .
                                 "個検出されました。項目数は" .
                                 $col_max_count . "個になります。");
                // 完了画面でエラー表示
                $this->tpl_mainpage = 'order/inos_import_order_complete.tpl';
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

    /**
     * ファイル情報の初期化を行う.
     *
     * @return void
     */
    function lfInitFile(&$objUpFile) {
        $objUpFile->addFile("CSVファイル", 'csv_file', array('csv'),
                CSV_SIZE, true, 0, 0, false);
    }

    /**
     * 入力情報の初期化を行う.
     *
     * @param array CSV構造設定配列
     * @return void
     */
    function lfInitParam(&$objFormParam, &$arrCSVFrame) {
        // CSV項目毎の処理
        foreach ($arrCSVFrame as $item) {
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
                $error_check_types = str_replace('HTML_TAG_CHECK', '', $item['error_check_types']);
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
     * 配送案内メールを送信する
     *
     * @param $arrShippedOrder 出荷済み受注データのWEB受注リスト
     * @return void
     */
    function doSendMail() {

    $ORDER_DELIV = ORDER_DELIV;       //「2:発送済」
    $ORDER_PRE_END = ORDER_PRE_END;   //「3:入金済み」
    $ORDER_PAY_WAIT = ORDER_PAY_WAIT; //「8:一部入金」

    $SEND_FLG_UNSENT = SEND_FLG_UNSENT; //「0:未送信」

        // 発送日が当日のみ対象
        $sql =<<<__EOS
    select imp.*,s.*
      from dtb_order_inos_import imp
    inner join dtb_order o
        on imp.order_base_no = o.order_base_no
    inner join dtb_shipping s
        on o.order_id = s.order_id
     where imp.error_flg = 0
       and imp.del_flg = 0
       and imp.shipping_num is not null
       and imp.status in({$ORDER_DELIV},{$ORDER_PRE_END},{$ORDER_PAY_WAIT})
       and imp.payment_total != imp.return_amount
       and s.send_mail_flg = {$SEND_FLG_UNSENT}
       and date_format(imp.commit_date, '%Y/%m/%d') =
           date_format(NOW(), '%Y/%m/%d')
  group by imp.order_base_no
__EOS;
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrShippedOrder = $objQuery->getAll($sql);

        // 取得できなければ処理中止
        if (count($arrShippedOrder) == 0 ||
            empty($arrShippedOrder)) {
            return;
        }
        foreach($arrShippedOrder as $order) {

            // 使用するメールテンプレートIDを決定
            if ($order['deliv_box_id'] == DELIV_BOX_ID_TAKUHAI) {
                // 宅配便
                $mail_tpl = MAIL_TEMPLATE_ID_SHIPPING_COMP;
            } else {
                $mail_tpl = MAIL_TEMPLATE_ID_SHIPPING_MAIL_COMP;
            }

            // 発送案内メールを送信
            $objMail = new SC_Helper_Mail_Ex();
            $objSendMail = $objMail->sfSendOrderMail
                ($order['order_id'], $mail_tpl);

            // 送信完了した受注NOを配列へ
            $arrOrderId[] = $order['order_id'];
        }

        // 100件ごとに分割 ※大量データを考慮
        $arrChunkOrderId =
            array_chunk($arrOrderId, SHIPPING_MAIL_MAX_COUNT);

        foreach ($arrChunkOrderId as $arrIds) {
            // 送信フラグを更新
            $this->updateSendMailFlg($arrIds);
        }
    }

    /**
     * エラーデータ出力処理
     *
     * @void
     */
    function doOutputErrCSV() {
 
        $sql =<<<__EOS
  select order_base_no
       , customer_cd
       , date_format(create_date, '%Y/%m/%d') as create_date
       , shipping_kana
       , shipping_name
       , shipping_tel
       , shipping_zip
       , shipping_addr_kana
       , shipping_addr01
       , shipping_addr02
       , status
       , date_format(commit_date, '%Y/%m/%d') as commit_date
       , shipping_area_code
       , deliv_id
       , deliv_box_id
       , invoice_num
       , date_format(shipping_date, '%Y/%m/%d') as shipping_date
       , time_id
       , note
       , deliv_kbn
       , cool_kbn
       , shipping_num
       , include_kbn
       , payment_id
       , subtotal
       , deliv_fee
       , payment_total
       , purchase_motive_code
       , input_assistance_code
       , event_code
       , order_kbn
       , regular_base_no
       , web_order_no
       , web_customer_id
       , del_flg
       , date_format(update_date, '%Y/%m/%d %H:%i:%s') as update_date
       , line_no
       , product_code
       , product_name
       , quantity
       , price
       , price_total
       , cut_rate
       , course_cd
       , error_name
    from dtb_order_inos_import
   where error_flg = 1
order by order_base_no asc
__EOS;
        // ヘッダー行生成
        $arrHeader = array("受注NO"
                         , "顧客CD"
                         , "受注日"
                         , "送付先カナ氏名"
                         , "送付先漢字氏名"
                         , "送付先電話番号"
                         , "送付先郵便番号"
                         , "送付先カナ住所"
                         , "送付先住所1"
                         , "送付先住所2"
                         , "レコード状態CD"
                         , "出荷日"
                         , "出荷場所CD"
                         , "宅配便CD"
                         , "箱サイズ"
                         , "送り状枚数"
                         , "納品指定日"
                         , "配達時間CD"
                         , "指定条件"
                         , "配送区分1"
                         , "配送区分2"
                         , "送り状番号"
                         , "明細書同梱区分"
                         , "支払方法CD"
                         , "合計金額"
                         , "税込送料"
                         , "総合計金額"
                         , "購入動機CD"
                         , "入力補助CD"
                         , "イベントCD"
                         , "受注区分"
                         , "コース受注NO"
                         , "WEB受注NO"
                         , "WEB顧客CD"
                         , "削除フラグ"
                         , "更新日時"
                         , "行NO"
                         , "商品CD"
                         , "商品名称"
                         , "数量"
                         , "税込単価"
                         , "税込金額"
                         , "値引率"
                         , "コースCD"
                         , "エラー内容");
        // CSVダウンロード実行
        $objCsv = new SC_Helper_CSV_Ex();
        $file_name_head = 'error_order';
        $objCsv->sfDownloadCsvFromSql
            ($sql, $arrval, $file_name_head, $arrHeader, true);
    }

    /**
     * 登録/編集結果のエラーメッセージをプロパティへ追加する
     *
     * @param integer $line_count 行数
     * @param stirng $message メッセージ
     * @return void
     */
    function addRowErr($line_count, $message) {
        $this->arrRowErr[] = $line_count . "行目：" . $message;
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

    /**
     * メール送信フラグを送信済へ更新する
     *
     * @param $arrOrderId
     * @return void
     */
    function updateSendMailFlg($arrOrderId) {

        // 件数がなければ処理中止
        if (count($arrOrderId) == 0) {
            return;
        }
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $orderIds = implode(',', $arrOrderId);

        // 送信済
        $SEND_FLG_SENT = SEND_FLG_SENT;

        $sql =<<<__EOS
update dtb_shipping
   set send_mail_flg = {$SEND_FLG_SENT}
     , send_mail_date  = now()
 where order_id in ({$orderIds})
__EOS;

        // 実行
        $objQuery->exec($sql);
    }
}

/*
 * fin.
 */
