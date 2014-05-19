<?php
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';
require_once CLASS_EX_REALDIR . 'helper_extends/SC_Helper_CSV_Ex.php';

/**
 * 顧客マスタ登録CSVのページクラス.
 */
class LC_Page_Admin_Customer_InosImportCustomer extends LC_Page_Admin_Ex {

    var $arrRowResult;
    var $arrRowErr;

    /** 削除用時必須チェック必要カラム **/
    var $arrExistsDelPtnCol;

    /** 削除用時必須チェック不要カラム **/
    var $arrNoExistsDelPtnCol;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'customer/customer_import.tpl';
        $this->tpl_mainno = 'customer';
        $this->tpl_subno = 'customer_import';
        $this->tpl_maintitle = '顧客マスタ管理';
        $this->tpl_subtitle = '顧客マスタ登録CSV';
        $this->csv_id = '2';

        // 削除時用の必須チェック不要カラム
        $this->arrNoExistsDelPtnCol
            = array('kana',
                    'name',
                    'tel',
                    'zip',
                    'addr01',
                    'email',
                    'sex',
                    'dm_flg',
                    'tel_flg',
                    'mailmaga_flg',
                    'privacy_kbn',
                    'kashidaore_kbn',
                    'customer_type_cd',
                   );
        // 削除時用の必須チェック必要カラム
        $this->arrExistsDelPtnCol
            = array('customer_id',
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

    // 一時テーブルへのローディング
    function loadCsvFile($file) {
    $sql =<<<__EOS
truncate table dtb_customer_inos_import
;
set character_set_database=utf8
;
load data local infile '{$file}' into table dtb_customer_inos_import
fields terminated by ',' enclosed by '"' lines terminated by '\r\n'
set customer_cd = nullif(customer_cd, '')
  , kana = nullif(kana, '')
  , name = nullif(name, '')
  , tel = nullif(tel, '')
  , zip = nullif(zip, '')
  , addr_kana = nullif(addr_kana, '')
  , addr01 = nullif(addr01, '')
  , addr02 = nullif(addr02, '')
  , email = nullif(email, '')
  , birth = nullif(birth, '')
  , sex = nullif(sex, '')
  , dm_flg = nullif(dm_flg, '')
  , tel_flg = nullif(tel_flg, '')
  , mailmaga_flg = nullif(mailmaga_flg, '')
  , privacy_kbn = nullif(privacy_kbn, '')
  , kashidaore_kbn = nullif(kashidaore_kbn, '')
  , customer_id = nullif(customer_id, '')
  , customer_type_cd = nullif(customer_type_cd, '')
  , create_date = nullif(create_date, '0000-00-00 00:00:00')
  , update_date = nullif(update_date, '0000-00-00 00:00:00')
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

    // EMailアドレスの補正
    function adjustEmail() {
    $sql =<<<__EOS
update dtb_customer_inos_import t1
   set t1.email = concat(t1.customer_cd, 'dummy@rohto.co.jp')
     , t1.mailmaga_flg = 0
 where t1.email is null
   and t1.customer_id is not null
   and t1.customer_cd is not null
   and t1.del_flg = 0
__EOS;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->query($sql);
    }

    // 追加パターンチェック処理
    function insertCheck() {
        // 基幹顧客CDからWEB顧客CDが存在するかチェック
        $this->insertExistWebCustomerCdCheck();
        // メールアドレス重複チェック
        $this->insertMailCheck();
    }

    // 更新パターンチェック処理
    function updateCheck() {
        // 退会ユーザチェック
        $this->updateExistsDelCustomerCheck();
        // メールアドレス重複チェック
        $this->updateMailCheckCd();
        $this->updateMailCheckId();
    }

    // 削除パターンチェック処理
    function deleteCheck() {
        ; // 処理なし
    }

    // インポートデータ共通チェック処理
    function inosCustomerImportCheck(&$objFormParam) {
        // 基幹顧客CD必須チェック
        $this->existsCustomerCdCheck();
        // 項目チェック
        $this->customerColCheck($objFormParam);
        // メールアドレス重複チェック
        $this->importMailDoubleCheck();
        // 基幹顧客CDとWEB顧客CDから顧客情報の存在チェック
        $this->importExistsCustomerCdCheck();
    }


    // 追加パターン基幹顧客CDからWEB顧客CDの存在チェック
    function insertExistWebCustomerCdCheck() {

    // ※基幹顧客CDが取り込まれている場合、WEB顧客CDをセット
    $sql =<<<__EOS
    update dtb_customer_inos_import t1
inner join dtb_customer t2
        on t1.customer_cd = t2.customer_cd
       and t2.del_flg = 0
       set t1.customer_id = t2.customer_id
     where t1.del_flg = 0
       and t1.customer_id is null
       and t1.customer_cd is not null
__EOS;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->query($sql);
    }

    // 追加パターンメールアドレス重複チェック
    function insertMailCheck() {
    $sql =<<<__EOS
    update dtb_customer_inos_import t1
inner join dtb_customer t2
        on t1.email = t2.email
       and t2.del_flg = 0
       set t1.error_flg = 1
         , t1.error_name = '新規登録データ - 既にメールアドレスが存在しています'
     where t1.del_flg = 0
       and t1.customer_id is null
       and t1.customer_cd is not null
__EOS;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->query($sql);
    }

    // 既に退会したユーザーかチェック
    function updateExistsDelCustomerCheck() {

    $sql =<<<__EOS
    update dtb_customer_inos_import t1
inner join dtb_customer t2
        on t1.customer_cd = t2.customer_cd
       and t1.customer_id = t2.customer_id
       set t1.error_flg = 1
         , t1.error_name = '更新データ - 既にユーザーが退会されています。'
     where t1.del_flg = 0
       and t1.error_flg = 0
       and t1.customer_id is not null
       and t1.customer_cd is not null

       and t2.del_flg = 1
__EOS;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->query($sql);
    }

    // 更新パターンメールアドレス重複チェック
    function updateMailCheckCd() {
    $sql =<<<__EOS
    update dtb_customer_inos_import t1
    inner join dtb_customer c
    on t1.customer_id = c.customer_id
    and t1.email = c.email
    and t1.customer_cd != c.customer_cd
    and c.del_flg = 0
       set t1.error_flg = 1
         , t1.error_name = '更新データ - 既にメールアドレスが存在しています'
     where t1.del_flg = 0
       and t1.customer_id is not null
       and t1.customer_cd is not null
__EOS;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->query($sql);
    }

    // 更新パターンメールアドレス重複チェック
    function updateMailCheckId() {
    $sql =<<<__EOS
    update dtb_customer_inos_import t1
    inner join dtb_customer c
    on t1.email = c.email
    and t1.customer_cd = c.customer_cd
    and t1.customer_id != c.customer_id
    and c.del_flg = 0
       set t1.error_flg = 1
         , t1.error_name = '更新データ - 既にメールアドレスが存在しています'
     where t1.del_flg = 0
       and t1.customer_id is not null
       and t1.customer_cd is not null
__EOS;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->query($sql);
    }

    // メールアドレス重複チェック
    function importMailDoubleCheck() {

    $sql =<<<__EOS
    update dtb_customer_inos_import t1
inner join (select email
              from dtb_customer_inos_import
             where error_flg = 0
          group by email
            having count(email) > 1) t2
        on t1.email = t2.email
       set t1.error_flg = 1
         , t1.error_name = 'インポートファイル内メールアドレス重複'
__EOS;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->query($sql);
    }

    // 基幹顧客CDとWEB顧客CDの存在チェック
    function importExistsCustomerCdCheck() {

    // 顧客情報の基幹顧客CDがある場合
    // WEB顧客と基幹顧客CDで存在チェック

    // 顧客情報の基幹顧客CDがない場合
    // WEB顧客で存在チェック
    $sql =<<<__EOS
    update dtb_customer_inos_import t1
       set t1.error_flg = 1
         , t1.error_name = '基幹顧客CD・WEB顧客CDから対象の顧客情報が見つかりません。'
     where t1.customer_cd is not null
       and t1.error_flg = 0
       and t1.customer_id is not null
       and (exists (select 'X'
                      from dtb_customer c
                     where c.customer_id = t1.customer_id
                       and c.del_flg = 0
                       and c.customer_cd != t1.customer_cd
                       and c.customer_cd is not null)
         or exists (select 'X'
                      from dtb_customer c
                     where c.customer_id != t1.customer_id
                       and c.del_flg = 0
                       and c.customer_cd = t1.customer_cd
                       and c.customer_cd is not null)
         or not exists (select 'X'
                          from dtb_customer c
                         where c.customer_id = t1.customer_id
                           and ((c.del_flg = 0
                           and t1.del_flg = 0)
                           or (t1.del_flg = 1))))
__EOS;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->query($sql);
    }

    // 共通基幹顧客CD必須項目チェック
    function existsCustomerCdCheck() {
    $sql =<<<__EOS
update dtb_customer_inos_import t1
   set t1.error_flg = 1
     , t1.error_name = '基幹顧客CD - 必須項目エラー'
 where t1.customer_cd is null
__EOS;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->query($sql);
    }

    /* 項目チェック
     *
     * param  $objFormParam
     * return void
     */
    function customerColCheck(&$objFormParam) {

    $sql =<<<__EOS
select *
  from dtb_customer_inos_import imp
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

        // 項目チェック処理実行、基幹顧客CDをキーに結果取得
        $arrErrData[][$arrData["customer_cd"]]
            = $this->lfCheckError($objFormParam);
        }

        // 結果配列ループ
        foreach ($arrErrData as $arrVal) {
            foreach ($arrVal as $customer_cd => $arrErrMsg) {

            // エラー結果がなければスキップ
            if (count($arrVal[$customer_cd]) == 0) {
                continue;
            }
            // 配列のエラーメッセージを文字列に変換
            $err_msg = implode($arrErrMsg);

            // エラーデータに更新
            $sql =<<<__EOS
update dtb_customer_inos_import imp
   set imp.error_flg = 1
     , imp.error_name = '{$err_msg}'
 where imp.customer_cd = '{$customer_cd}'
__EOS;
            $objQuery->query($sql);
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
            // 必須チェック追加
            $objDelFormParam->addCheck
                ($this->arrExistsDelPtnCol, 'EXIST_CHECK');
            // 必須チェック消去
            $objDelFormParam->removeCheck
                ($this->arrNoExistsDelPtnCol, 'EXIST_CHECK');
            $arrErr
                = $objDelFormParam->checkError(false);
        // 通常
        } else {
            $arrErr
                = $objFormParam->checkError(false);
        }
        return $arrErr;
    }

    // 一時テーブル初期処理
    function prepareImport(&$objFormParam) {
    // EMailアドレスの補正 
    $this->adjustEmail();
    // インポートデータの共通チェック
    $this->inosCustomerImportCheck($objFormParam);
    // 追加パターンのチェック
    $this->insertCheck();
    // 更新パターンのチェック
    $this->updateCheck();
    // 削除パターンのチェック
    $this->deleteCheck();
    }

    // 顧客情報インポート処理
    function doImport() {
    $objQuery =& SC_Query_Ex::getSingletonInstance();
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
        ('dtb_customer_inos_import', 'error_flg = ?', 0);

    return array($count, INOS_ERROR_FLG_EXIST_NORMAL);

    }

    // 追加パターンインポート処理
    function insertImport() {
    $objQuery =& SC_Query_Ex::getSingletonInstance();

    // 現在のWEB顧客IDを取得
    $SEQ = $objQuery->currVal('dtb_customer_customer_id');

    // 新規登録するためのWEB顧客CDをセット
    $sql =<<<__EOS
set @i := {$SEQ};
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }

    // シークレットキーを生成するための一意なIDをセット
    $sql =<<<__EOS
set @k := {$SEQ};
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }

    // 新規登録実行
    $sql =<<<__EOS
insert into dtb_customer (
    customer_cd
  , kana
  , name
  , tel
  , zip
  , pref
  , addr_kana
  , addr01
  , addr02
  , email
  , birth
  , sex
  , dm_flg
  , tel_flg
  , mailmaga_flg
  , privacy_kbn
  , kashidaore_kbn
  , torihiki_id
  , customer_id
  , customer_type_cd
  , del_flg
  , create_date
  , update_date
  , secret_key
  , password
  , salt
  , creator_id
  , updator_id
  , recv_date)
select 
    imp.customer_cd
  , imp.kana
  , imp.name
  , replace(imp.tel, '-', '')
  , imp.zip
  , pre.id as pref
  , nullif(imp.addr_kana, '')
  , replace(imp.addr01, ifnull(pre.name, ''), '') as addr01
  , nullif(imp.addr02, '') as addr02
  , imp.email
  , imp.birth
  , imp.sex
  , imp.dm_flg
  , imp.tel_flg
  , imp.mailmaga_flg
  , imp.privacy_kbn
  , imp.kashidaore_kbn
  , nullif(imp.torihiki_id, '')
  , (@i := @i + 1) as customer_id
  , imp.customer_type_cd
  , imp.del_flg
  , imp.create_date
  , imp.update_date
  , concat('r1', lpad((@k := @k + 1), 20, 0)) as secret_key
  , substring(md5(rand()), 1, 30)
  , substring(md5(rand()), 1, 10)
  , {$_SESSION['member_id']}
  , {$_SESSION['member_id']}
  , NOW()
from dtb_customer_inos_import imp
left outer join mtb_zip mzip
   on mzip.zipcode = replace(imp.zip, '-', '')
left outer join mtb_pref pre
   on pre.name = mzip.state
where imp.customer_cd is not null
  and imp.error_flg = 0
  and imp.del_flg = 0
  and imp.customer_id is null
group by imp.customer_cd
;
__EOS;
    if (!$objQuery->query($sql)) {
        return false;
    }

    // WEB顧客CDの最大値を更新
    $sql =<<<__EOS
UPDATE dtb_customer_customer_id_seq
   SET sequence = (SELECT MAX(customer_id)
                     FROM dtb_customer)
;
__EOS;

    return $objQuery->query($sql);
    }

    // 更新パターンインポート処理
    function updateImport() {
    $objQuery =& SC_Query_Ex::getSingletonInstance();

    $sql =<<<__EOS
    update dtb_customer c
inner join (select imp.customer_cd
                 , imp.kana
                 , imp.name
                 , replace(imp.tel, '-', '') as tel
                 , imp.zip
                 , pre.id as pref
                 , nullif(imp.addr_kana, '') as addr_kana
                 , replace(imp.addr01, ifnull(pre.name, ''), '') as addr01
                 , nullif(imp.addr02, '') as addr02
                 , imp.email
                 , imp.birth
                 , imp.sex
                 , imp.dm_flg
                 , imp.tel_flg
                 , imp.mailmaga_flg
                 , imp.privacy_kbn
                 , imp.kashidaore_kbn
                 , nullif(imp.torihiki_id, '') as torihiki_id
                 , imp.customer_id
                 , imp.customer_type_cd
                 , imp.update_date
              from dtb_customer_inos_import imp
   left outer join mtb_zip mzip
                on mzip.zipcode = replace(imp.zip, '-', '')
   left outer join mtb_pref pre
                on pre.name = mzip.state
             where imp.customer_cd is not null
               and imp.error_flg = 0
               and imp.del_flg = 0
               and imp.customer_id is not null
          group by imp.customer_cd) imp2
                on c.customer_id = imp2.customer_id

               set c.customer_cd = imp2.customer_cd
                 , c.kana = imp2.kana
                 , c.name = imp2.name
                 , c.tel = imp2.tel
                 , c.zip = imp2.zip
                 , c.pref = imp2.pref
                 , c.addr_kana = imp2.addr_kana
                 , c.addr01 = imp2.addr01
                 , c.addr02 = imp2.addr02
                 , c.email = imp2.email
                 , c.birth = imp2.birth
                 , c.sex = imp2.sex
                 , c.dm_flg = imp2.dm_flg
                 , c.tel_flg = imp2.tel_flg
                 , c.mailmaga_flg = imp2.mailmaga_flg
                 , c.privacy_kbn = imp2.privacy_kbn
                 , c.kashidaore_kbn = imp2.kashidaore_kbn
                 , c.torihiki_id = IF(LENGTH(imp2.torihiki_id), imp2.torihiki_id, c.torihiki_id)
                 , c.customer_id = imp2.customer_id
                 , c.customer_type_cd = imp2.customer_type_cd
                 , c.update_date = imp2.update_date
                 , c.updator_id = {$_SESSION['member_id']}
                 , c.recv_date = NOW()
;
__EOS;

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->query($sql);
    }

    // 削除パターンインポート処理
    function deleteImport() {
    $objQuery =& SC_Query_Ex::getSingletonInstance();

    $sql =<<<__EOS
    update dtb_customer c
inner join dtb_customer_inos_import imp
        on c.customer_id = imp.customer_id
       set c.del_flg = imp.del_flg
         , c.update_date = imp.update_date
         , c.updator_id = {$_SESSION['member_id']}
         , c.recv_date = NOW()
     where imp.customer_cd is not null
       and imp.error_flg = 0
       and imp.del_flg = 1
       and imp.customer_id is not null
;
__EOS;
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->query($sql);
    }

    /*
     * 顧客情報ファイルインポート処理開始
     */
    function importCsvFile(&$objFormParam, &$objUpFile) {
    // ファイルのアップロード
    $file = $this->uploadCsvFile($objFormParam, $objUpFile);
    if (empty($file)) {
        return;
    }

    // 一時テーブルへのローディング
    if (!$this->loadCsvFile($file)) {
        $this->tpl_mainpage = 'customer/customer_import_complete.tpl';
        return;
    }

    $objQuery =& SC_Query_Ex::getSingletonInstance();

    // 一時テーブル初期処理
    $this->prepareImport($objFormParam);

    $objQuery->begin();

    // 顧客情報のインポート処理
    list($count, $r) = $this->doImport();

    // 結果取得
    if ($r != INOS_ERROR_FLG_EXIST_NORMAL) {
        $objQuery->rollback();
    } else {
        $objQuery->commit();
    }
    // 取込み完了したエラー件数取得
    $err_count = $objQuery->count
        ('dtb_customer_inos_import', 'error_flg = ?', 1);
    // エラーデータがある場合は更新履歴情報をエラー判定に
    if ($err_count > 0) {
        $r = INOS_ERROR_FLG_EXIST_ERROR;
    }

    // バッチ処理履歴情報へデータ登録
    SC_Helper_DB_Ex::sfInsertBatchHistory
        (INOS_DATA_TYPE_RECV_CUSTOMER, $count, $r);

    // 実行結果画面を表示
    $this->tpl_mainpage = 'customer/customer_import_complete.tpl';
    $this->addRowCompleteMsg($count);
    // エラー件数を表示
    $this->tpl_err_count = $err_count;
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
                $this->tpl_mainpage = 'customer/customer_import_complete.tpl';
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
     * エラーデータ出力処理
     *
     * @void
     */
    function doOutputErrCSV() {
 
        $sql =<<<__EOS
  select customer_cd
       , kana
       , name
       , tel
       , zip
       , addr_kana
       , addr01
       , addr02
       , email
       , birth
       , sex
       , dm_flg
       , tel_flg
       , mailmaga_flg
       , privacy_kbn
       , kashidaore_kbn
       , torihiki_id
       , customer_id
       , customer_type_cd
       , del_flg
       , date_format(create_date, '%Y/%m/%d %H:%i:%s') as create_date
       , date_format(update_date, '%Y/%m/%d %H:%i:%s') as update_date
       , error_name
    from dtb_customer_inos_import
   where error_flg = 1
order by customer_cd asc
__EOS;
        // ヘッダー行生成
        $arrHeader = array("顧客CD"
                         , "カナ氏名"
                         , "漢字氏名"
                         , "電話番号"
                         , "郵便番号"
                         , "カナ住所"
                         , "住所1"
                         , "住所2"
                         , "メールアドレス"
                         , "生年月日"
                         , "性別"
                         , "DM区分"
                         , "電話区分"
                         , "メール送信区分"
                         , "個人情報区分"
                         , "償却顧客区分"
                         , "クレジット会員ID"
                         , "WEB顧客CD"
                         , "顧客形態CD"
                         , "削除フラグ"
                         , "登録日時"
                         , "更新日時"
                         , "エラー内容");
        // CSVダウンロード実行
        $objCsv = new SC_Helper_CSV_Ex();
        $file_name_head = 'error_customer';
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
}

/*
 * fin.
 */
