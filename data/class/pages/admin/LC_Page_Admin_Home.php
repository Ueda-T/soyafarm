<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';
require_once DATA_REALDIR . 'module/Request.php';

/**
 * 管理画面ホーム のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_Home.php 138 2012-06-13 08:52:26Z nagata $
 */
class LC_Page_Admin_Home extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'home.tpl';
        $this->tpl_subtitle = 'ホーム';
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

        // DBバージョンの取得
        $this->db_version = $this->lfGetDBVersion();

        // PHPバージョンの取得
        $this->php_version = $this->lfGetPHPVersion();

        // 現在の会員数
        $this->customer_cnt = $this->lfGetCustomerCnt();

/*
        // 昨日の売上高
        $r = $this->lfGetOrderYesterday();
        $this->order_yesterday_amount = $r['amount'];

        // 昨日の売上件数
        $this->order_yesterday_cnt = $r['cnt'];

        // 今月の売上高
        $r = $this->lfGetOrderMonth();
        $this->order_month_amount = $r['amount'];

        // 今月の売上件数
        $this->order_month_cnt = $r['cnt'];
*/
	list($this->order_yesterday_amount,
	     $this->order_yesterday_cnt,
	     $this->order_month_amount,
	     $this->order_month_cnt) = $this->lfGetOrderSummary();
/*
        // 会員の累計ポイント
        $this->customer_point = $this->lfGetTotalCustomerPoint();

        //昨日のレビュー書き込み数
        $this->review_yesterday_cnt = $this->lfGetReviewYesterday();

        //レビュー書き込み非表示数
        $this->review_nondisp_cnt = $this->lfGetReviewNonDisp();

        // 品切れ商品
        $this->arrSoldout = $this->lfGetSoldOut();
*/

        // 新規受付一覧
        $this->arrNewOrder = $this->lfGetNewOrder();

        // お知らせ一覧の取得
        //$this->arrInfo = $this->lfGetInfo();
    }

    function lfGetOrderSummary() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
	$sql =<<<__EOS
select damount
     , dcount
     , mamount
     , mcount
  from dtb_summary
__EOS;

        $r = $objQuery->getAll($sql);
	return array($r[0]['damount'], $r[0]['dcount'],
		     $r[0]['mamount'], $r[0]['mcount']);
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
     * PHPバージョンの取得
     *
     * @return string PHPバージョン情報
     */
    function lfGetPHPVersion() {
        return "PHP " . phpversion();
    }

    /**
     * DBバージョンの取得
     *
     * @return mixed DBバージョン情報
     */
    function lfGetDBVersion() {
        $dbFactory = SC_DB_DBFactory_Ex::getInstance();
        return $dbFactory->sfGetDBVersion();
    }

    /**
     * 現在の会員数の取得
     *
     * @return integer 会員数
     */
    function lfGetCustomerCnt(){

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<EOF
SELECT
    COUNT(customer_id)
FROM
    dtb_customer
WHERE
    del_flg = 0
    AND status = 2
EOF;

        return $objQuery->getOne($sql);
    }

    /**
     * 昨日の売上データの取得
     *
     * @param string $method 取得タイプ 件数:'COUNT' or 金額:'SUM'
     * @return integer 結果数値
     */
    function lfGetOrderYesterday(){
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $r;

        // TODO: DBFactory使わないでも共通化できそうな気もしますが
        $dbFactory = SC_DB_DBFactory_Ex::getInstance();
        $sql = $dbFactory->getOrderYesterdaySql();
	$r = $objQuery->getAll($sql);
        return $r[0];
    }

    /**
     * 今月の売上データの取得
     *
     * @param string $method 取得タイプ 件数:'COUNT' or 金額:'SUM'
     * @return integer 結果数値
     */
    function lfGetOrderMonth() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $month = date("Y/m", mktime());

        // TODO: DBFactory使わないでも共通化できそうな気もしますが
        $dbFactory = SC_DB_DBFactory_Ex::getInstance();
        $sql = $dbFactory->getOrderMonthSql();
	$r = $objQuery->getAll($sql);
        return $r[0];
    }

    /**
     * 会員の保持ポイント合計の取得
     *
     * @return integer 会員の保持ポイント合計
     */
    function lfGetTotalCustomerPoint() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<EOF
SELECT
    SUM(point)
FROM
    dtb_customer
WHERE
    del_flg = 0
EOF;

        return $objQuery->getOne($sql);
    }

    /**
     * 会員の誕生日保持ポイント合計の取得
     *
     * @return integer 会員の誕生日保持ポイント合計
     */
    function lfGetTotalCustomerBirthPoint() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<EOF
SELECT
    SUM(birth_point)
FROM
    dtb_customer
WHERE
    del_flg = 0
EOF;

        return $objQuery->getOne($sql);
    }

    /**
     * 昨日のレビュー書き込み数の取得
     *
     * @return integer 昨日のレビュー書き込み数
     */
    function lfGetReviewYesterday(){
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // TODO: DBFactory使わないでも共通化できそうな気もしますが
        $dbFactory = SC_DB_DBFactory_Ex::getInstance();
        $sql = $dbFactory->getReviewYesterdaySql();
        return $objQuery->getOne($sql);
    }

    /**
     * レビュー書き込み非表示数の取得
     *
     * @return integer レビュー書き込み非表示数
     */
    function lfGetReviewNonDisp(){
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<EOF
SELECT
    COUNT(*)
FROM
    dtb_review AS A
    LEFT JOIN dtb_products AS B
        ON A.product_id = B.product_id
WHERE
    A.del_flg = 0
    AND A.status = 2
    AND B.del_flg = 0
EOF;
        return $objQuery->getOne($sql);
    }

    /**
     * 品切れ商品の取得
     *
     * @return array 品切れ商品一覧
     */
    function lfGetSoldOut() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $UNLIMITED_FLG_LIMITED = UNLIMITED_FLG_LIMITED;

        $sql =<<<EOF
SELECT
    product_id,
    name
FROM
    dtb_products
WHERE
    product_id IN (SELECT
                       product_id
                   FROM
                       dtb_products_class
                   WHERE
                       stock_unlimited = $UNLIMITED_FLG_LIMITED
                       AND stock <= 0
                   )
EOF;

        return $objQuery->getAll($sql);
    }

    /**
     * 新規受付一覧の取得
     *
     * @return array 新規受付一覧配列
     */
    function lfGetNewOrder() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $status_cancel  = ORDER_CANCEL;
        $status_pending = ORDER_PENDING;

        $sql =<<<EOF
SELECT ord.order_id
     , ord.customer_id
     , ord.order_name
     , ord.total
     , ord.create_date
     , (SELECT dd.product_name
          FROM dtb_order_detail dd
         WHERE ord.order_id = dd.order_id
         ORDER BY dd.order_detail_id
         LIMIT 1) AS product_name
     , pm.payment_method
  FROM (SELECT order_id
             , customer_id
             , order_name
             , total
             , create_date
             , payment_id
          FROM dtb_order
         WHERE del_flg = 0
           AND status <> $status_cancel
           AND status <> $status_pending
         ORDER BY order_id DESC
         LIMIT 10 OFFSET 0) AS ord
 INNER JOIN dtb_payment pm
    ON ord.payment_id = pm.payment_id
 ORDER BY ord.order_id DESC
EOF;

        $arrNewOrder = $objQuery->getAll($sql);
        foreach ($arrNewOrder as $key => $val) {
            $arrNewOrder[$key]['create_date'] = str_replace("-", "/", substr($val['create_date'], 0,19));
        }
        return $arrNewOrder;
    }

    /**
     * リリース情報を取得する.
     *
     * @return array 取得した情報配列
     */
    function lfGetInfo() {
        // 更新情報の取得ON/OFF確認
        if (!ECCUBE_INFO) return array();

        // パラメーター「UPDATE_HTTP」が空文字の場合、処理しない。
        // XXX これと別に on/off を持たせるべきか。
        if (strlen(UPDATE_HTTP) == 0) return array();

        $query = '';
        // サイト情報の送信可否設定
        // XXX インストール時に問い合わせて送信可否設定を行うように設定すべきか。
        // XXX (URLは強制送信すべきではないと思うが)バージョンは強制送信すべきか。
        if (UPDATE_SEND_SITE_INFO === true) {
            $query = '?site_url=' . HTTP_URL . '&eccube_version=' . ECCUBE_VERSION;
        }

        $url = UPDATE_HTTP . $query;

        // タイムアウト時間設定
        $context = array('http' => array('timeout' => HTTP_REQUEST_TIMEOUT));

        $jsonStr = @file_get_contents($url, false, stream_context_create($context));

        $arrTmpData = is_string($jsonStr) ? SC_Utils_Ex::jsonDecode($jsonStr) : null;

        if (empty($arrTmpData)) {
            SC_Utils_Ex::sfErrorHeader(">> 更新情報の取得に失敗しました。");
            return array();
        }
        $arrInfo = array();
        foreach ($arrTmpData as $objData) {
            $arrInfo[] = get_object_vars($objData);
        }
        return $arrInfo;
    }
}
?>
