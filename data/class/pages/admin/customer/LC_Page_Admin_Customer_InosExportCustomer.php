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
	/* Batch起動のためコメントアウト
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
	 */

        set_time_limit(0);
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        //$this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {

        // 最終出力日時を取得
        $this->last_send_date = SC_Helper_DB_Ex::sfGetLastSendDate
            (INOS_DATA_TYPE_SEND_CUSTOMER);

        // WHERE句
        $where = "c.send_flg = 0";

	// 顧客データCSV生成
	$this->tpl_linemax = $this->getNumberOfLines($where);
	$this->doOutputCSV($where, $this->tpl_linemax);
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
        $csvFile = $objCsv->sfDownloadMakeCsvFromSql
            ($sql, $arrval, $file_name_head, "");
        if ($csvFile) {
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

	// エクスポートフォルダへファイルを配置する
	$this->doSetOutputCsv($csvFile);
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

    /**
     * エクスポートした顧客データを連動フォルダへ移動
     *
     * @param array  $filepath   CSVファイル名
     * @return void
     */
    function doSetOutputCSV($csvFile) {

        // 顧客情報
	if (is_file($csvFile)) {
	    // 受注情報をセットする
            $fileName = sprintf(INOS_FILE_SEND_CUSTOMER, date("YmdHis"));
            $filePath = INOS_DIR_SEND_CUSTOMER . $fileName;
            $bkFilePath = INOS_DIR_SEND_CUSTOMER . INOS_OK_DIR . "/" . $fileName;
	    rename($csvFile, $filePath);
	    chmod($filePath, 0666);
	    copy($filePath, $bkFilePath);
	}
    }

}
?>
