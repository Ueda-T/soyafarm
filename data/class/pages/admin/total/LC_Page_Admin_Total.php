<?php
// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';
require_once CLASS_REALDIR . 'graph/SC_GraphPie.php';
require_once CLASS_REALDIR . 'graph/SC_GraphLine.php';
require_once CLASS_REALDIR . 'graph/SC_GraphBar.php';

/**
 * 売上集計 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_Total.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Admin_Total extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        // GDライブラリのインストール判定
        $this->install_GD = function_exists("gd_info") ? true : false;
        $this->tpl_mainpage         = 'total/index.tpl';
        $this->tpl_mainno           = 'order';
        $this->tpl_subno            = 'total';
        $this->tpl_subnavi          = 'order/subnavi.tpl';
        $this->tpl_graphsubtitle    = 'total/subtitle.tpl';
        $this->tpl_titleimage       = ROOT_URLPATH.'img/title/title_sale.jpg';
        $this->tpl_maintitle = '売上集計';

        $masterData                 = new SC_DB_MasterData_Ex();
        $this->arrWDAY              = $masterData->getMasterData("mtb_wday");
        $this->arrSex               = $masterData->getMasterData("mtb_sex");

        // 登録・更新日検索用
        $objDate                    = new SC_Date_Ex();
        $objDate->setStartYear(RELEASE_YEAR);
        $objDate->setEndYear(DATE('Y'));
        $this->arrYear              = $objDate->getYear();
        $this->arrMonth             = $objDate->getMonth();
        $this->arrDay               = $objDate->getDay();

        // 種別
        $this->arrType = array('term'     => '期間別'
                             , 'products' => '商品別'
                             , 'age'      => '年代別'
                             , 'member'   => '会員別'
                         );

        // ページタイトル todo あとでなおす
        $this->arrTitle['']         = "期間別集計";
        $this->arrTitle['term']     = "期間別集計";
        $this->arrTitle['products'] = "商品別集計";
        $this->arrTitle['age']      = "年代別集計";
        $this->arrTitle['member']   = "会員別集計";

        // 月度集計のkey名
        $this->arrSearchForm1       = array('search_startyear_m', 'search_startmonth_m');

        // 期間別集計のkey名
        $this->arrSearchForm2       = array('search_startyear',
                                            'search_startmonth',
                                            'search_startday',
                                            'search_endyear',
                                            'search_endmonth',
                                            'search_endday');
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
        if(isset($_GET['draw_image']) && $_GET['draw_image'] != ""){
            define('DRAW_IMAGE' , true);
        }else{
            define('DRAW_IMAGE' , false);
        }

        // パラメーター管理クラス
        $objFormParam = new SC_FormParam_Ex();
        // パラメーター情報の初期化
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->setParam($_GET);

        // 検索ワードの引き継ぎ
        $this->arrHidden = $objFormParam->getSearchArray();

        switch($this->getMode()) {
        case 'csv':
        case 'search':

            $this->arrErr = $this->lfCheckError($objFormParam);
            if (empty($this->arrErr)) {

                // 日付
                list($sdate, $edate) = $this->lfSetStartEndDate($objFormParam);

                // ページ
                $page = ($objFormParam->getValue('page')) ? $objFormParam->getValue('page') : 'term';

                // 集計種類
                $type = ($objFormParam->getValue('type')) ? $objFormParam->getValue('type'): 'all';

                $this->tpl_page_type = "total/page_". $page .".tpl";
                list($this->arrResults, $this->tpl_image) = call_user_func_array(array($this, 'lfGetOrder'.$page),
                                                                                 array($type, $sdate, $edate));
                if($this->getMode() == 'csv') {
                    // CSV出力タイトル行の取得
                    list($arrTitleCol, $arrDataCol) = $this->lfGetCSVColum($page);
                    $head = SC_Utils_Ex::sfGetCSVList($arrTitleCol);
                    $data = $this->lfGetDataColCSV($this->arrResults, $arrDataCol);

                    // CSVを送信する。
                    list($fime_name, $data) = SC_Utils_Ex::sfGetCSVData($head.$data);
                    $this->sendResponseCSV($fime_name, $data);
                    exit;
                }
            }
            break;
        default:
        }

        // 画面宣しても日付が保存される
        $_SESSION           = $this->lfSaveDateSession($_SESSION, $this->arrHidden);
        $objFormParam->setParam($_SESSION['total']);
        // 入力値の取得
        $this->arrForm      = $objFormParam->getFormParamList();
        $this->tpl_subtitle = $this->arrTitle[$objFormParam->getValue('page')];

    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /* デフォルト値の取得 */
    function lfGetDateDefault() {
        $year = date("Y");
        $month = date("m");
        $day = date("d");

        $list = isset($_SESSION['total']) ? $_SESSION['total'] : "";

        // セッション情報に開始月度が保存されていない。
        if(empty($_SESSION['total']['startyear_m'])) {
            $list['startyear_m'] = $year;
            $list['startmonth_m'] = $month;
        }

        // セッション情報に開始日付、終了日付が保存されていない。
        if(empty($_SESSION['total']['startyear']) && empty($_SESSION['total']['endyear'])) {
            $list['startyear'] = $year;
            $list['startmonth'] = $month;
            $list['startday'] = $day;
            $list['endyear'] = $year;
            $list['endmonth'] = $month;
            $list['endday'] = $day;
        }

        return $list;
    }

    /* パラメーター情報の初期化 */
    function lfInitParam(&$objFormParam) {
        // デフォルト値の取得
        $arrList = $this->lfGetDateDefault();

        // 月度集計
        $objFormParam->addParam("月度", "search_startyear_m", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['startyear_m']);
        $objFormParam->addParam("月度", "search_startmonth_m", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['startmonth_m']);
        // 期間集計
        $objFormParam->addParam("開始日", "search_startyear", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['startyear']);
        $objFormParam->addParam("開始日", "search_startmonth", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['startmonth']);
        $objFormParam->addParam("開始日", "search_startday", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['startday']);
        $objFormParam->addParam("終了日", "search_endyear", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['endyear']);
        $objFormParam->addParam("終了日", "search_endmonth", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['endmonth']);
        $objFormParam->addParam("終了日", "search_endday", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"), $arrList['endday']);

        // hiddenデータの取得用
        $objFormParam->addParam("", 'page');
        $objFormParam->addParam("", 'type');
        $objFormParam->addParam("", 'mode');
        $objFormParam->addParam("", 'form');
    }

    /* 入力内容のチェック */
    function lfCheckError(&$objFormParam) {

        $objFormParam->convParam();
        $objErr         = new SC_CheckError_Ex();
        $objErr->arrErr = $objFormParam->checkError();

        // 特殊項目チェック
        if($objFormParam->getValue('form') == 1) {
            $objErr->doFunc(array("月度", "search_startyear_m"), array("ONE_EXIST_CHECK"));
        }

        if($objFormParam->getValue('form') == 2) {
            $objErr->doFunc(array("期間", "search_startyear", "search_startmonth", "search_startday", "search_endyear", "search_endmonth", "search_endday"), array("FULL_EXIST_CHECK"));
        }
        $objErr->doFunc(array("月度", "search_startyear_m", "search_startmonth_m"), array("ALL_EXIST_CHECK"));
        $objErr->doFunc(array("開始日", "search_startyear", "search_startmonth", "search_startday"), array("CHECK_DATE"));
        $objErr->doFunc(array("終了日", "search_endyear", "search_endmonth", "search_endday"), array("CHECK_DATE"));
        $objErr->doFunc(array("開始日", "終了日", "search_startyear", "search_startmonth", "search_startday", "search_endyear", "search_endmonth", "search_endday"), array("CHECK_SET_TERM"));
        return $objErr->arrErr;
    }

    /* サブナビを移動しても日付が残るようにセッションに入力期間を記録する */
    function lfSaveDateSession($session, $arrForm) {

        // session の初期化をする
        if (!isset($session['total'])) {
            $session['total'] = $this->lfGetDateInit();
        }

        if (!empty($arrForm)) {
            $session['total'] = array_merge($session['total'], $arrForm);
        }

        return $session;
    }

    /* 日付の初期値 */
    function lfGetDateInit() {
        $search_startyear_m     = $search_startyear  = $search_endyear  = date('Y');
        $search_startmonth_m    = $search_startmonth = $search_endmonth = date('m');
        $search_startday        = $search_endday     = date('d');

        return compact($this->arrSearchForm1, $this->arrSearchForm2);
    }

    /* フォームで入力された日付を適切な形にする */
    function lfSetStartEndDate(&$objFormParam) {

        $arrRet = $objFormParam->getHashArray();

        foreach ($arrRet as $key => $val) {
            if($val == "") {
                continue;
            }
            switch ($key) {
            case 'search_startyear':
                $sdate = $objFormParam->getValue('search_startyear') . "/" . $objFormParam->getValue('search_startmonth') . "/" . $objFormParam->getValue('search_startday');
                break;
            case 'search_endyear':
                $edate = $objFormParam->getValue('search_endyear') . "/" . $objFormParam->getValue('search_endmonth') . "/" . $objFormParam->getValue('search_endday');
                break;
            case 'search_startyear_m':
                list($sdate, $edate) = SC_Utils_Ex::sfTermMonth($objFormParam->getValue('search_startyear_m'),
                                                                $objFormParam->getValue('search_startmonth_m'),
                                                                CLOSE_DAY);
                break;
            default:
                break;
            }
        }

        return array($sdate, $edate);
    }

    /* 折れ線グラフの作成 */
    function lfGetGraphLine($arrResults, $keyname, $type, $xtitle, $ytitle, $sdate, $edate) {

        $ret_path = "";

        // 結果が0行以上ある場合のみグラフを生成する。
        if(count($arrResults) > 0 && $this->install_GD) {

            // グラフの生成
            $arrList = SC_Utils_Ex::sfArrKeyValue($arrResults, $keyname, 'total');

            // 一時ファイル名の取得
            $pngname = $this->lfGetGraphPng($type);

            $path = GRAPH_REALDIR . $pngname;

            // ラベル表示インターバルを求める
            $interval = intval(count($arrList) / 20);
            if($interval < 1) {
                $interval = 1;
            }
            $objGraphLine = new SC_GraphLine();

            // 値のセット
            $objGraphLine->setData($arrList);
            $objGraphLine->setXLabel(array_keys($arrList));

            // ラベル回転(日本語不可)
            if($keyname == "key_day"){
                $objGraphLine->setXLabelAngle(45);
            }

            // タイトルセット
            $objGraphLine->setXTitle($xtitle);
            $objGraphLine->setYTitle($ytitle);

            // メインタイトル作成
            list($sy, $sm, $sd) = preg_split("|[/ ]|" , $sdate);
            list($ey, $em, $ed) = preg_split("|[/ ]|" , $edate);
            $start_date = $sy . "年" . $sm . "月" . $sd . "日";
            $end_date = $ey . "年" . $em . "月" . $ed . "日";
            $objGraphLine->drawTitle("集計期間：" . $start_date . " - " . $end_date);

            // グラフ描画
            $objGraphLine->drawGraph();

            // グラフの出力
            if(DRAW_IMAGE){
                $objGraphLine->outputGraph();
                exit();
            }

            // ファイルパスを返す
            $ret_path = GRAPH_URLPATH . $pngname;
        }
        return $ret_path;
    }

    // 円グラフの作成
    function lfGetGraphPie($arrResults, $keyname, $type, $title = "", $sdate = "", $edate = "") {

        $ret_path = "";
        // 結果が0行以上ある場合のみグラフを生成する。
        if(count($arrResults) > 0 && $this->install_GD) {
            // #36 ↓でデータが欠落する by kaji
            // グラフの生成
            $arrList = SC_Utils_Ex::sfArrKeyValue($arrResults, $keyname,
                                                  'total', GRAPH_PIE_MAX,
                                                  GRAPH_LABEL_MAX);
            // #36 ↑でデータが欠落する by kaji

            // 一時ファイル名の取得
            $pngname = $this->lfGetGraphPng($type);
            $path = GRAPH_REALDIR . $pngname;

            $objGraphPie = new SC_GraphPie();

            // データをセットする
            $objGraphPie->setData($arrList);
            // 凡例をセットする
            $objGraphPie->setLegend(array_keys($arrList));

            // メインタイトル作成
            list($sy, $sm, $sd) = preg_split("|[/ ]|" , $sdate);
            list($ey, $em, $ed) = preg_split("|[/ ]|" , $edate);
            $start_date = $sy . "年" . $sm . "月" . $sd . "日";
            $end_date = $ey . "年" . $em . "月" . $ed . "日";
            $objGraphPie->drawTitle("集計期間：" . $start_date . " - " . $end_date);

            // 円グラフ描画
            $objGraphPie->drawGraph();

            // グラフの出力
            if(DRAW_IMAGE){
                $objGraphPie->outputGraph();
                exit();
            }

            // ファイルパスを返す
            $ret_path = GRAPH_URLPATH . $pngname;
        }
        return $ret_path;
    }

    // 棒グラフの作成
    function lfGetGraphBar($arrResults, $keyname, $type, $xtitle, $ytitle, $sdate, $edate) {
        $ret_path = "";

        // 結果が0行以上ある場合のみグラフを生成する。
        if(count($arrResults) > 0 && $this->install_GD) {
            // グラフの生成
            $arrList = SC_Utils_Ex::sfArrKeyValue($arrResults, $keyname, 'total', GRAPH_PIE_MAX, GRAPH_LABEL_MAX);

            // 一時ファイル名の取得
            $pngname = $this->lfGetGraphPng($type);
            $path = GRAPH_REALDIR . $pngname;

            $objGraphBar = new SC_GraphBar();

            foreach(array_keys($arrList) as $val) {
                $arrKey[] = ereg_replace("～", "-", $val);
            }

            // グラフ描画
            $objGraphBar->setXLabel($arrKey);
            $objGraphBar->setXTitle($xtitle);
            $objGraphBar->setYTitle($ytitle);
            $objGraphBar->setData($arrList);

            // メインタイトル作成
            $arrKey = array_keys($arrList);
            list($sy, $sm, $sd) = preg_split("|[/ ]|" , $sdate);
            list($ey, $em, $ed) = preg_split("|[/ ]|" , $edate);
            $start_date = $sy . "年" . $sm . "月" . $sd . "日";
            $end_date = $ey . "年" . $em . "月" . $ed . "日";
            $objGraphBar->drawTitle("集計期間：" . $start_date . " - " . $end_date);

            $objGraphBar->drawGraph();

            if(DRAW_IMAGE){
                $objGraphBar->outputGraph();
                exit();
            }

            // ファイルパスを返す
            $ret_path = GRAPH_URLPATH . $pngname;
        }
        return $ret_path;
    }

    // グラフ用のPNGファイル名
    function lfGetGraphPng($keyname) {

        if($_POST['search_startyear_m'] != "") {
            $pngname = sprintf("%s_%02d%02d.png", $keyname, substr($_POST['search_startyear_m'],2), $_POST['search_startmonth_m']);
        } else {
            $pngname = sprintf("%s_%02d%02d%02d_%02d%02d%02d.png", $keyname, substr($_POST['search_startyear'], 2), $_POST['search_startmonth'], $_POST['search_startday'], substr($_POST['search_endyear'],2), $_POST['search_endmonth'], $_POST['search_endday']);
        }
        return $pngname;
    }

    // 会員、非会員集計のWHERE分の作成
    function lfGetWhereMember($col_date, $sdate, $edate, $type, $col_member = "customer_id") {
        $where = "";
        // 取得日付の指定
        if($sdate != "") {
            if ($where != "") {
                $where.= " AND ";
            }
            $where.= " $col_date >= '". $sdate ."'";
        }

        if($edate != "") {
            if ($where != "") {
                $where.= " AND ";
            }
            $edate = date("Y/m/d",strtotime("1 day" ,strtotime($edate)));
            $where.= " $col_date < date('" . $edate ."')";
        }

        // 会員、非会員の判定
        switch($type) {
            // 全体
        case 'all':
            break;
        case 'member':
            if ($where != "") {
                $where.= " AND ";
            }
            $where.= " $col_member <> 0";
            break;
        case 'nonmember':
            if ($where != "") {
                $where.= " AND ";
            }
            $where.= " $col_member = 0";
            break;
        default:
            break;
        }

        return array($where, array());
    }

    /** 会員別集計 **/
    function lfGetOrderMember($type, $sdate, $edate) {
        $objQuery = SC_Query_Ex::getSingletonInstance();

        list($where, $arrval) =
            $this->lfGetWhereMember('create_date', $sdate, $edate, $type);
        $where .=
            " AND del_flg = 0 AND status <> " . ORDER_CANCEL;

        // 会員集計の取得
        $sql =<<<EOF
SELECT
    COUNT(order_id) AS order_count,
    SUM(total) AS total,
    AVG(total) AS total_average,
    CASE
        WHEN customer_id <> 0 THEN 1
        ELSE 0
    END AS member,
    order_sex
FROM
    dtb_order
WHERE
    $where
GROUP BY member, order_sex
EOF;

        $arrTotalResults = $objQuery->getAll($sql);

        foreach(array_keys($arrTotalResults) as $key) {
            $arrResult =& $arrTotalResults[$key];
            $member_key = $arrResult['order_sex'];
            if($member_key != "") {
                $arrResult['member_name'] = (($arrResult['member']) ? '会員' : '非会員') . $this->arrSex[$member_key];
            } else {
                $arrResult['member_name'] = "未回答";
            }
        }

        $tpl_image = $this->lfGetGraphPie($arrTotalResults, "member_name", 'member', "(売上比率)", $sdate, $edate);

        return array($arrTotalResults, $tpl_image);
    }

    /** 商品別集計 **/
    function lfGetOrderProducts($type, $sdate, $edate) {
        $objQuery = SC_Query_Ex::getSingletonInstance();

        list($where, $arrval) = $this->lfGetWhereMember('create_date', $sdate, $edate, $type);

        $where .= " AND dtb_order.del_flg = 0 AND dtb_order.status <> " . ORDER_CANCEL;

        $sql =<<<EOF
SELECT
    product_id,
    product_code,
    product_name,
    SUM(quantity) AS products_count,
    COUNT(order_id) AS order_count,
    price,
    (price * SUM(quantity)) AS total
FROM
    dtb_order_detail JOIN dtb_order USING(order_id)
WHERE
    $where
GROUP BY product_id, product_name, product_code, price
ORDER BY total DESC
EOF;
        $arrTotalResults = $objQuery->getAll($sql);

        $tpl_image  = $this->lfGetGraphPie($arrTotalResults, "product_name", "products_" . $type, "(売上比率)", $sdate, $edate);

        return array($arrTotalResults, $tpl_image);
    }

    /** 年代別集計 **/
    function lfGetOrderAge($type, $sdate, $edate) {

        $objQuery = SC_Query_Ex::getSingletonInstance();

        list($where, $arrval) = $this->lfGetWhereMember('create_date', $sdate, $edate, $type);

        $dbFactory = SC_DB_DBFactory_Ex::getInstance();
        $col_age = $dbFactory->getOrderTotalAgeColSql() . ' AS age';

        $sql =<<<EOF
SELECT
    $col_age,
    COUNT(order_id) AS order_count,
    SUM(total) AS total,
    AVG(total) AS total_average
FROM
    dtb_order
WHERE
    $where
GROUP BY age
ORDER BY age DESC
EOF;
        $arrTotalResults = $objQuery->getAll($sql);

        foreach(array_keys($arrTotalResults) as $key) {
            $arrResult =& $arrTotalResults[$key];
            $age_key = $arrResult['age'];
            if($age_key != "") {
                $arrResult['age_name'] = $arrResult['age'] . '代';
            } else {
                $arrResult['age_name'] = "未回答";
            }

        }
        $tpl_image = $this->lfGetGraphBar($arrTotalResults, "age_name", "age_" . $type, "(年齢)", "(売上合計)", $sdate, $edate);

        return array($arrTotalResults, $tpl_image);
    }

    /** 期間別集計 **/
    // todo あいだの日付埋める
    function lfGetOrderTerm($type, $sdate, $edate) {
        $objQuery   = SC_Query_Ex::getSingletonInstance();

        list($where, $arrval) = $this->lfGetWhereMember('create_date', $sdate, $edate);
        $where .= " AND del_flg = 0 AND status <> " . ORDER_CANCEL;

        switch($type){
        case 'month':
            $xtitle = "(月別)";
            $ytitle = "(売上合計)";
            $format = '%m';
            break;
        case 'year':
            $xtitle = "(年別)";
            $ytitle = "(売上合計)";
            $format = '%Y';
            break;
        case 'wday':
            $xtitle = "(曜日別)";
            $ytitle = "(売上合計)";
            $format = '%a';
            break;
        case 'hour':
            $xtitle = "(時間別)";
            $ytitle = "(売上合計)";
            $format = '%H';
            break;
        default:
            $xtitle = "(日別)";
            $ytitle = "(売上合計)";
            $format = '%Y-%m-%d';

            break;
        }

        $dbFactory = SC_DB_DBFactory_Ex::getInstance();
        // todo postgres
        $col = $dbFactory->getOrderTotalDaysWhereSql($type);

        $objQuery->setGroupBy('str_date');
        $objQuery->setOrder('str_date');
        // 検索結果の取得
        $arrTotalResults = $objQuery->select($col, 'dtb_order', $where);

        $arrTotalResults = $this->lfAddBlankLine($arrTotalResults, $type, $sdate, $edate);
        // todo GDない場合の処理
        $tpl_image       = $this->lfGetGraphLine($arrTotalResults, 'str_date', "term_" . $type, $xtitle, $ytitle, $sdate, $edate);
        $arrTotalResults = $this->lfAddTotalLine($arrTotalResults);

        return array($arrTotalResults, $tpl_image);
    }

    /*
     * 期間中の日付を埋める
     */
    function lfAddBlankLine($arrResults, $type, $st, $ed) {

        $arrDateList = $this->lfDateTimeArray($type, $st, $ed);

        foreach($arrResults as $arrResult) {
            $strdate                = $arrResult['str_date'];
            $arrDateResults[$strdate] = $arrResult;
        }

        foreach ($arrDateList as $date) {

            if(array_key_exists($date, $arrDateResults)) {

                $arrRet[] = $arrDateResults[$date];

            } else {
                $arrRet[]['str_date'] = $date;
            }
        }
        return $arrRet;
    }

    /*
     * 日付の配列を作成する
     *
     */
    function lfDateTimeArray($type, $st, $ed) {
        switch($type){
            case 'month':
                $format        = 'm';
                break;
            case 'year':
                $format        = 'Y';
                break;
            case 'wday':
                $format        = 'D';
                break;
            case 'hour':
                $format        = 'H';
                break;
            default:
                $format        = 'Y-m-d';
                break;
        }

        if ($type == 'hour') {
            $arrDateList = array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23');

        } else {
            $arrDateList = array();
            $tmp    = strtotime($st);
            $nAday  = 60*60*24;
            $edx    = strtotime($ed);
            while( $tmp <= $edx ){
                $sDate = date($format, $tmp);
                if( !in_array($sDate, $arrDateList) ){
                    $arrDateList[] = $sDate;
                }
                $tmp += $nAday;
            }
        }
        return $arrDateList;
    }

    /*
     * 合計を付与する
     */
    function lfAddTotalLine($arrResults) {
        // 検索結果が0でない場合
        if(count($arrResults) > 0) {

            // 合計の計算
            foreach ($arrResults as $arrResult) {
                foreach(array_keys($arrResult) as $value) {
                    $arrTotal[$value] += $arrResult[$value];
                }
            }
            // 平均値の計算
            $arrTotal['total_average'] = $arrTotal['total'] / $arrTotal['total_order'];
            $arrResults[] = $arrTotal;
        }

        return $arrResults;
    }

    // 必要なカラムのみ抽出する(CSVデータで取得する)
    function lfGetDataColCSV($arrData, $arrDataCol) {
        $max = count($arrData);
        $csv_data = "";
        for($i = 0; $i < $max; $i++) {
            foreach($arrDataCol as $val) {
                $arrRet[$i][$val] = $arrData[$i][$val];
            }
            $csv_data.= SC_Utils_Ex::sfGetCSVList($arrRet[$i]);
        }
        return $csv_data;
    }

    function lfGetCSVColum($page) {
        switch($page) {
            // 商品別集計
        case 'products':
            $arrTitleCol = array(
                                 '商品コード',
                                 '商品名',
                                 '購入件数',
                                 '数量',
                                 '単価',
                                 '金額'
                                 );
            $arrDataCol = array(
                                'product_code',
                                'product_name',
                                'order_count',
                                'products_count',
                                'price',
                                'total',
                                );
            break;
            // 会員別集計
        case 'member':
            $arrTitleCol = array(
                                 '会員',
                                 '購入件数',
                                 '購入合計',
                                 '購入平均',
                                 );
            $arrDataCol = array(
                                'member_name',
                                'order_count',
                                'total',
                                'total_average',
                                );
            break;
            // 年代別集計
        case 'age':
            $arrTitleCol = array(
                                 '年齢',
                                 '購入件数',
                                 '購入合計',
                                 '購入平均',
                                 );
            $arrDataCol = array(
                                'age_name',
                                'order_count',
                                'total',
                                'total_average',
                                );
            break;
            // 期間別集計
        default:
            $arrTitleCol = array(
                                 '期間',
                                 '購入件数',
                                 '男性',
                                 '女性',
                                 '男性(会員)',
                                 '男性(非会員)',
                                 '女性(会員)',
                                 '女性(非会員)',
                                 '購入合計',
                                 '購入平均',
                                 );
            $arrDataCol = array(
                                'str_date',
                                'total_order',
                                'men',
                                'women',
                                'men_member',
                                'men_nonmember',
                                'women_member',
                                'women_nonmember',
                                'total',
                                'total_average'
                                );
            break;
        }

        return array($arrTitleCol, $arrDataCol);
    }
}
?>
