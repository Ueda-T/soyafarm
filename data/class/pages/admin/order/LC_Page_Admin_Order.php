<?php
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';
require_once CLASS_EX_REALDIR . 'helper_extends/SC_Helper_CSV_Ex.php';

/**
 * 受注管理 のページクラス
 */
class LC_Page_Admin_Order extends LC_Page_Admin_Ex {
    /*
     *
     */
    function extractOrderStatus($arrOrderStatus, $key) {
	foreach ($arrOrderStatus as $index => $value) {
	    $r[$index] = $value[$key];
	}
	return $r;
    }

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();

        if ($_SESSION['critical_menu'] == CRITICAL_MENU_OFF) {
	    SC_Response_Ex::sendRedirect("../home.php");
	    exit;
	}

        $this->tpl_mainpage = 'order/index.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'index';
        $this->tpl_pager = 'pager.tpl';
        $this->tpl_maintitle = '受注管理';
        $this->tpl_subtitle = '受注管理';

        $master = new SC_DB_MasterData_Ex();
        $arrOS = $master->getDbMasterDataNatural
	    ("mtb_order_status", array("id", "name", "color"));
	$this->arrOrderStatus = $this->extractOrderStatus($arrOS, "name");
	$this->arrOrderStatusColor = $this->extractOrderStatus($arrOS, "color");
        $this->arrPageMax = $master->getMasterData("mtb_page_max");
        $this->arrORDERSTATUS = $master->getMasterData("mtb_order_status");
        $this->arrKikanFlg = array(1=>"連携済み", 2=>"未連携");

        $objDate = new SC_Date_Ex();
        // 登録・更新日検索用
        $objDate->setStartYear(RELEASE_YEAR);
        $objDate->setEndYear(DATE('Y'));
        $this->arrRegistYear = $objDate->getYear();

        // 2011.05.09 お届日指定 検索用
        $objDate->setStartYear(RELEASE_YEAR);
        // 最大発送日目安(2ヵ月後) + お届け日指定受付可能日数(DELIV_DAET_MAX)
        // の年を検索範囲の最大値とする
        $end_date = strtotime(sprintf("+%s day", 62 + DELIV_DATE_END_MAX));
        $objDate->setEndYear(DATE("Y", $end_date));
        $this->arrDelivYear = $objDate->getYear();

        // 月日の設定
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();

        // 支払い方法の取得
        $this->arrPayments = SC_Helper_DB_Ex::sfGetIDValueList("dtb_payment", "payment_id", "payment_method");

        $this->httpCacheControl('nocache');
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

        // 2011.04.26 追加
        $objPurchase = new SC_Helper_Purchase_Ex();
        
        $objFormParam->setParam($_POST);
	$objFormParam->convParam();
        $this->arrForm = $objFormParam->getFormParamList();
        $this->arrHidden = $objFormParam->getSearchArray();
        
        switch ($this->getMode()) {
        case 'csv':
        case 'search':
            $objFormParam->trimParam();
            $this->arrErr = $this->lfCheckError($objFormParam);
            $arrParam = $objFormParam->getHashArray();

            if (count($this->arrErr) == 0) {
                $where = "oh.del_flg = 0";
                foreach ($arrParam as $key => $val) {
                    if($val == "") {
                        continue;
                    }
                    $this->buildQuery($key, $where, $arrval, $objFormParam, "oh");
                }

                $order = "oh.order_id DESC";

                /* -----------------------------------------------
                 * 処理を実行
                 * ----------------------------------------------- */
                switch($this->getMode()) {
                // CSVを送信する。
                case 'csv':
                    $this->doOutputCsv($where, $arrval);
                    break;

                // 検索実行
                default:
		    $join =<<<EOF
    left join dtb_customer as c
    on c.customer_id = oh.customer_id 
EOF;
                    // 行数の取得
                    $this->tpl_linemax = $this->getNumberOfLines
			($where, $arrval, $join, "oh");

                    // ページ送りの処理
                    $page_max = SC_Utils_Ex::sfGetSearchPageMax
			($objFormParam->getValue('search_page_max'));

                    // ページ送りの取得
                    $objNavi = new SC_PageNavi_Ex
			($this->arrHidden['search_pageno'],
			 $this->tpl_linemax, $page_max,
			 'fnNaviSearchPage', NAVI_PMAX);
                    $this->arrPagenavi = $objNavi->arrPagenavi;

                    // 検索結果の取得
                    $this->arrResults = $this->findOrders
			($where, $arrval, $page_max, $objNavi->start_row,
			 $order, $join, "oh");

                    // ▼ 2011.04.27 注文番号(order_id)に対応する配送先情報を取得(お届け指定日を取得)
                    // 注文番号
                    $order_id = array();
                    foreach($this->arrResults as $key1 => $val1){
                        foreach($val1 as $key2 => $val2){
                            if ($key2 == "order_id"){
                                array_push($order_id, $val2);
                            }
                        } 
                    }
                    
                    // お届け指定日
                    $this->arrShippingDate = array();
                    foreach($order_id as $key1 => $val1){
                        // 各配送先情報からお届け指定日を取得
                        $arrShippings = $objPurchase->getShippings($val1);
                        $shipping_date = array();
                        foreach($arrShippings as $key2 => $val2){
                            foreach($val2 as $key3 => $val3){
                                if ($key3 == "shipping_date"){  
                                    array_push($shipping_date, $val3);
                                }
                            }
                        }
                        array_push($this->arrShippingDate, $shipping_date);
                    }
                    // ▲ 2011.04.27
                }
            }
            break;
        default:
            break;
        }
    }

    /**
     * パラメーター情報の初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam
	    ("顧客ID", "search_customer_id", INT_LEN, 'n',
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam
	    ("顧客コード", "search_customer_cd", INOS_CUSTOMER_CD_LEN, 'n',
	     array("MAX_LENGTH_CHECK", "ALNUM_CHECK"));

        $objFormParam->addParam
	    ("注文番号1", "search_order_id1", INT_LEN, 'n',
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam
	    ("注文番号2", "search_order_id2", INT_LEN, 'n',
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam
	    ("対応状況", "search_order_status", INT_LEN, 'n',
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam
	    ("注文者 お名前", "search_order_name", STEXT_LEN, 'KVa',
	     array("MAX_LENGTH_CHECK"));

        $objFormParam->addParam
	    ("注文者 お名前(フリガナ)", "search_order_kana", STEXT_LEN, 'hks',
	     array("KANA_CHECK","MAX_LENGTH_CHECK"));

        $objFormParam->addParam
	    ("メールアドレス", "search_order_email", STEXT_LEN, 'KVa',
	     array("MAX_LENGTH_CHECK"));

        $objFormParam->addParam
	    ('TEL', "search_order_tel", STEXT_LEN, 'KVa',
	     array("MAX_LENGTH_CHECK"));

        $objFormParam->addParam
	    ("支払い方法", "search_payment_id", INT_LEN, 'n',
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        // 受注日
        $objFormParam->addParam
	    ("開始年", "search_sorderyear", INT_LEN, 'n',
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam
	    ("開始月", "search_sordermonth", INT_LEN, 'n',
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam
	    ("開始日", "search_sorderday", INT_LEN, 'n',
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam
	    ("終了年", "search_eorderyear", INT_LEN, 'n',
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam
	    ("終了月", "search_eordermonth", INT_LEN, 'n',
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam
	    ("終了日", "search_eorderday", INT_LEN, 'n',
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        // 更新日
        $objFormParam->addParam
	    ("開始年", "search_supdateyear", INT_LEN, 'n',
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam
	    ("開始月", "search_supdatemonth", INT_LEN, 'n',
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam
	    ("開始日", "search_supdateday", INT_LEN, 'n',
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam
	    ("終了年", "search_eupdateyear", INT_LEN, 'n',
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam
	    ("終了月", "search_eupdatemonth", INT_LEN, 'n',
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam
	    ("終了日", "search_eupdateday", INT_LEN, 'n',
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        // 2011.05.06 お届け日指定
        $objFormParam->addParam
	    ("開始日", "search_sdelivyear", INT_LEN, "n",
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam
	    ("開始日", "search_sdelivmonth", INT_LEN, "n",
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam
	    ("開始日", "search_sdelivday", INT_LEN, "n",
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam
	    ("終了日", "search_edelivyear", INT_LEN, "n",
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam
	    ("終了日", "search_edelivmonth", INT_LEN, "n",
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam
	    ("終了日", "search_edelivday", INT_LEN, "n",
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam
	    ("ページ送り番号","search_pageno", INT_LEN, 'n',
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam
	    ("購入金額1", "search_total1", INT_LEN, 'n',
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam
	    ("購入金額2", "search_total2", INT_LEN, 'n',
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

	// キャンペーンコード
        $objFormParam->addParam
	    ('TEL', "search_campaign_cd", STEXT_LEN, 'KVa',
	     array("ALNUM_CHECK", "MAX_LENGTH_CHECK"));

	// 商品コード
        $objFormParam->addParam
	    ("商品コード", "search_product_cd", STEXT_LEN, 'KVa',
	     array("ALNUM_CHECK", "MAX_LENGTH_CHECK"));

	// 商品名
        $objFormParam->addParam
	    ("商品名", "search_product_name", STEXT_LEN, '',
	     array("MAX_LENGTH_CHECK"));

	// 基幹連携
        $objFormParam->addParam
	    ("支払い方法", "search_kikan_flg", INT_LEN, 'n',
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam
	    ("表示件数", "search_page_max", INT_LEN, 'n',
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam
	    ("受注ID", "order_id", INT_LEN, 'n',
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));
    }

    /**
     * 入力内容のチェックを行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfCheckError(&$objFormParam) {
        $objErr = new SC_CheckError_Ex($objFormParam->getHashArray());
        $objErr->arrErr = $objFormParam->checkError();

        // 相関チェック
        $objErr->doFunc(array("注文番号1", "注文番号2", "search_order_id1", "search_order_id2"), array("GREATER_CHECK"));
        $objErr->doFunc(array("年齢1", "年齢2", "search_age1", "search_age2"), array("GREATER_CHECK"));
        $objErr->doFunc(array("購入金額1", "購入金額2", "search_total1", "search_total2"), array("GREATER_CHECK"));

        // 2011.05.09お届け日指定
        $objErr->doFunc(array("開始", "search_sdelivyear", "search_sdelivmonth", "search_sdelivday"), array("CHECK_DATE"));
        $objErr->doFunc(array("終了", "search_edelivyear", "search_edelivmonth", "search_edelivday"), array("CHECK_DATE"));
        $objErr->doFunc(array("開始", "終了", "search_sdelivyear", "search_sdelivmonth", "search_sdelivday", "search_edelivyear", "search_edelivmonth", "search_edelivday"), array("CHECK_SET_TERM"));

        // 受注日
        $objErr->doFunc(array("開始", "search_sorderyear", "search_sordermonth", "search_sorderday"), array("CHECK_DATE"));
        $objErr->doFunc(array("終了", "search_eorderyear", "search_eordermonth", "search_eorderday"), array("CHECK_DATE"));
        $objErr->doFunc(array("開始", "終了", "search_sorderyear", "search_sordermonth", "search_sorderday", "search_eorderyear", "search_eordermonth", "search_eorderday"), array("CHECK_SET_TERM"));

        // 更新日
        $objErr->doFunc(array("開始", "search_supdateyear", "search_supdatemonth", "search_supdateday"), array("CHECK_DATE"));
        $objErr->doFunc(array("終了", "search_eupdateyear", "search_eupdatemonth", "search_eupdateday"), array("CHECK_DATE"));
        $objErr->doFunc(array("開始", "終了", "search_supdateyear", "search_supdatemonth", "search_supdateday", "search_eupdateyear", "search_eupdatemonth", "search_eupdateday"), array("CHECK_SET_TERM"));

        return $objErr->arrErr;
    }

    /**
     * クエリを構築する.
     *
     * 検索条件のキーに応じた WHERE 句と, クエリパラメーターを構築する.
     * クエリパラメーターは, SC_FormParam の入力値から取得する.
     *
     * 構築内容は, 引数の $where 及び $arrValues にそれぞれ追加される.
     *
     * @param string $key 検索条件のキー
     * @param string $where 構築する WHERE 句
     * @param array $arrValues 構築するクエリパラメーター
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function buildQuery($key, &$where, &$arrValues, &$objFormParam, $prefix = null) {
	if ($prefix) {
	    $prefix .= ".";
	}

        switch ($key) {
        case 'search_customer_id':
            $where .= " AND " . $prefix . "customer_id = ?";
            $arrValues[] = sprintf('%d', $objFormParam->getValue($key));
            break;

        case 'search_customer_cd':
            $where .= " AND c.customer_cd = ?";
            $arrValues[] = sprintf('%s', $objFormParam->getValue($key));
            break;
        case 'search_order_name':
            $where .= " AND replace(replace(" . $prefix . "order_name, ' ', ''), '　', '') LIKE ?";
            $arrValues[] = sprintf('%%%s%%', mb_ereg_replace("[ 　]", "", $objFormParam->getValue($key)));
            break;
        case 'search_order_kana':
            $where .= " AND replace(replace(" . $prefix . "order_kana, ' ', ''), '　', '') LIKE ?";
            $arrValues[] = sprintf('%%%s%%', mb_ereg_replace("[ 　]", "", $objFormParam->getValue($key)));
            break;

        case 'search_order_id1':
            $where .= " AND " . $prefix . "order_id >= ?";
            $arrValues[] = sprintf('%d', $objFormParam->getValue($key));
            break;
        case 'search_order_id2':
            $where .= " AND " . $prefix . "order_id <= ?";
            $arrValues[] = sprintf('%d', $objFormParam->getValue($key));
            break;
        case 'search_order_tel':
            $where .= " AND " . $prefix . "order_tel LIKE ?";
            $arrValues[] = sprintf('%%%d%%', preg_replace('/[()-]+/','', $objFormParam->getValue($key)));
            break;
        case 'search_order_email':
            $where .= " AND " . $prefix . "order_email LIKE ?";
            $arrValues[] = sprintf('%%%s%%', $objFormParam->getValue($key));
            break;
        case 'search_payment_id':
            $tmp_where = "";
            foreach($objFormParam->getValue($key) as $element) {
                if($element != "") {
                    if($tmp_where == "") {
                        $tmp_where .= " AND (" . $prefix . "payment_id = ?";
                    } else {
                        $tmp_where .= " OR " . $prefix . "payment_id = ?";
                    }
                    $arrValues[] = $element;
                }
            }

            if(!SC_Utils_Ex::isBlank($tmp_where)) {
                $tmp_where .= ")";
                $where .= " $tmp_where ";
            }
            break;
        case 'search_kikan_flg':
            $tmp_where = "";
            foreach($objFormParam->getValue($key) as $element) {
                if($element != "") {
                    if($tmp_where == "") {
                        $tmp_where .= " AND (";
                    } else {
                        $tmp_where .= " OR ";
                    }
		    if ($element == 1) {
                        $tmp_where .= $prefix . "order_base_no IS NOT NULL";
		    } else if ($element == 2) {
                        $tmp_where .= $prefix . "order_base_no IS NULL";
		    }
                }
            }

            if(!SC_Utils_Ex::isBlank($tmp_where)) {
                $tmp_where .= ")";
                $where .= " $tmp_where ";
            }
            break;
        case 'search_total1':
            $where .= " AND " . $prefix . "total >= ?";
            $arrValues[] = sprintf('%d', $objFormParam->getValue($key));
            break;
        case 'search_total2':
            $where .= " AND " . $prefix . "total <= ?";
            $arrValues[] = sprintf('%d', $objFormParam->getValue($key));
            break;
        case 'search_sorderyear':
            $date = SC_Utils_Ex::sfGetTimestamp
		($objFormParam->getValue('search_sorderyear'),
		 $objFormParam->getValue('search_sordermonth'),
		 $objFormParam->getValue('search_sorderday'));
            $where.= " AND " . $prefix . "create_date >= ?";
            $arrValues[] = $date;
            break;
        case 'search_eorderyear':
            $date = SC_Utils_Ex::sfGetTimestamp
		($objFormParam->getValue('search_eorderyear'),
		 $objFormParam->getValue('search_eordermonth'),
		 $objFormParam->getValue('search_eorderday'), true);
            $where.= " AND " . $prefix . "create_date <= ?";
            $arrValues[] = $date;
            break;
        case 'search_supdateyear':
            $date = SC_Utils_Ex::sfGetTimestamp
		($objFormParam->getValue('search_supdateyear'),
		 $objFormParam->getValue('search_supdatemonth'),
		 $objFormParam->getValue('search_supdateday'));
            $where.= " AND " . $prefix . "update_date >= ?";
            $arrValues[] = $date;
            break;
        case 'search_eupdateyear':
            $date = SC_Utils_Ex::sfGetTimestamp
		($objFormParam->getValue('search_eupdateyear'),
		 $objFormParam->getValue('search_eupdatemonth'),
		 $objFormParam->getValue('search_eupdateday'), true);
            $where.= " AND " . $prefix . "update_date <= ?";
            $arrValues[] = $date;
            break;
        case 'search_order_status':
            $where.= " AND " . $prefix . "status = ?";
            $arrValues[] = $objFormParam->getValue($key);
            break;
        
        // ▼ 2011.05.09 お届け日指定を検索条件に追加
        case 'search_sdelivyear':
            $date = SC_Utils_Ex::sfGetTimestamp
		($objFormParam->getValue('search_sdelivyear'),
		 $objFormParam->getValue('search_sdelivmonth'),
		 $objFormParam->getValue('search_sdelivday'));
            $where .= " AND EXISTS (SELECT order_id FROM dtb_shipping sh WHERE sh.order_id = oh.order_id AND sh.shipping_date >= ?)";
            $arrValues[] = $date;
            break;
        case 'search_edelivyear':
            $date = SC_Utils_Ex::sfGetTimestamp
		($objFormParam->getValue('search_edelivyear'),
		 $objFormParam->getValue('search_edelivmonth'),
		 $objFormParam->getValue('search_edelivday'));
            $where .= " AND EXISTS (SELECT order_id FROM dtb_shipping sh WHERE sh.order_id = oh.order_id AND sh.shipping_date <= ?)";
            $arrValues[] = $date;
            break;
        // ▲ 2011.05.09 お届け日指定を検索条件に追加

        case 'search_product_cd':
            $where .= " AND EXISTS (SELECT od.order_id FROM dtb_order_detail od WHERE od.order_id = " . $prefix . "order_id AND od.product_code LIKE ?)";
            $arrValues[] = sprintf('%%%s%%', $objFormParam->getValue($key));
            break;

        case 'search_product_name':
            $where .= " AND EXISTS (SELECT od.order_id FROM dtb_order_detail od WHERE od.order_id = " . $prefix . "order_id AND od.product_name LIKE ?)";
            $arrValues[] = sprintf('%%%s%%', $objFormParam->getValue($key));
            break;

        case 'search_campaign_cd':
            $where.= " AND " . $prefix . "campaign_cd = ?";
            $arrValues[] = $objFormParam->getValue($key);
            break;

        default:
            break;
        }
    }

    function doOutputCsv($where, $arrval) {
	$sql =<<<__EOS
select
    oh.order_id,
    oh.order_base_no,
    oh.customer_id,
    oh.order_name,
    oh.order_kana,
    oh.order_email,
    oh.order_tel,
    oh.order_zip,
    opref.name as order_pref,
    oh.order_addr01,
    oh.order_addr02,
    sex.name as order_sex,
    oh.order_birth,
    oh.subtotal,
    case
        when oh.deliv_id = 0 then "ヤマト"
        when oh.deliv_id = 1 then "佐川"
        else "" end as deliv_id,
    oh.deliv_box_id,
    oh.deliv_fee,
    oh.use_point,
    oh.total,
    oh.payment_total,
    oh.payment_method,
    oh.note,
    os.name,
    oh.create_date,
    oh.update_date,
    oh.commit_date,
    oh.payment_date,
    dt.name as device_type_id,
    ik.name as include_kbn,
    oh.campaign_cd,
    oh.event_code,
    oh.regular_base_no,
    oh.return_num,
    oh.return_amount,
    sh.shipping_name,
    sh.shipping_kana,
    sh.shipping_tel,
    spref.name as shipping_pref,
    sh.shipping_zip,
    sh.shipping_addr01,
    sh.shipping_addr02,
    sh.shipping_time,
    sh.shipping_num,
    sh.shipping_date,
    sh.shipping_commit_date,
    case
        when sh.shipping_area_code = 1 then "紀泉"
        when sh.shipping_area_code = 2 then "産直"
        else "" end as shipping_area_code,
    case
        when sh.deliv_kbn = 0 then "通常"
        when sh.deliv_kbn = 1 then "ワレモノ"
        when sh.deliv_kbn = 2 then "なまもの"
        else "" end as deliv_kbn,
    case
        when sh.cool_kbn = 0 then "通常"
        when sh.cool_kbn = 1 then "冷凍"
        when sh.cool_kbn = 2 then "冷蔵"
        else "" end as cool_kbn,
    case
        when sh.send_mail_flg = 0 then "未送信"
        when sh.send_mail_flg = 1 then "送信済み"
        else "" end as send_mail_flg,
    sh.send_mail_date,
    od.product_name,
    od.product_code,
    od.classcategory_name1,
    od.price,
    od.quantity,
    od.course_cd,
    od.return_quantity,
    case
        when od.sell_flg = 1 then "販売対象"
        else "" end as sell_flg
from
    dtb_order as oh
    inner join dtb_shipping as sh
        on sh.order_id = oh.order_id
    inner join dtb_order_detail as od
        on od.order_id = oh.order_id
    left join mtb_pref as opref
        on opref.id = oh.order_pref
    left join mtb_sex as sex
        on sex.id = oh.order_sex
    left join mtb_order_status as os
        on os.id = oh.status
    left join mtb_device_type as dt
        on dt.id = oh.device_type_id
    left join mtb_include_kbn as ik
        on ik.id = oh.include_kbn
    left join mtb_pref as spref
        on spref.id = sh.shipping_pref
    left join mtb_deliv_box as db
        on db.id = oh.deliv_box_id
    left join dtb_customer as c
        on c.customer_id = oh.customer_id
where
    $where
__EOS;

	$header = array
	    ("受注ID",
	     "基幹受注No",
	     "顧客ID",
	     "顧客名",
	     "顧客カナ",
	     "顧客メールアドレス",
	     "顧客電話番号",
	     "顧客郵便番号",
	     "顧客都道府県ID",
	     "顧客住所1",
	     "顧客住所2",
	     "顧客性別",
	     "顧客生年月日",
	     "小計",
	     "配送業者ID",
	     "箱ID",
	     "送料",
	     "使用ポイント",
	     "合計",
	     "支払合計",
	     "支払方法",
	     "備考",
	     "受注状態",
	     "作成日時",
	     "更新日時",
	     "発送済み日時",
	     "入金日時",
	     "端末種別ID",
	     "明細書同梱区分",
	     "適用キャンペーンコード",
	     "イベントCD",
	     "基幹定期No",
	     "返品回数",
	     "返品金額",
	     "配送先名",
	     "配送先カナ",
	     "配送先電話番号",
	     "配送先都道府県ID",
	     "配送先顧客郵便番号",
	     "配送先住所1",
	     "配送先住所2",
	     "配送時間",
	     "配送伝票番号",
	     "配達予定日",
	     "発送日時",
	     "出荷場所CD",
	     "配送区分",
	     "冷凍冷蔵区分",
	     "メール送信フラグ",
	     "メール送信日",
	     "商品名",
	     "商品コード",
	     "商品規格名1",
	     "価格",
	     "個数",
	     "コースCD",
	     "返品数量",
	     "販売対象フラグ");

        $objCsv = new SC_Helper_CSV_Ex();
	$objCsv->sfDownloadCsvFromSql($sql, $arrval, "order", $header, true);
	exit;
    }

    /**
     * 検索結果の行数を取得する.
     *
     * @param string $where 検索条件の WHERE 句
     * @param array $arrValues 検索条件のパラメーター
     * @return integer 検索結果の行数
     */
    function getNumberOfLines($where, $arrValues, $join, $alias = null) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
	$table = "dtb_order";

	if ($alias) {
	    $table .= " as " . $alias;
	}
	$table .= $join;

        return $objQuery->count($table, $where, $arrValues);
    }

    /**
     * 受注を検索する.
     *
     * @param string $where 検索条件の WHERE 句
     * @param array $arrValues 検索条件のパラメーター
     * @param integer $limit 表示件数
     * @param integer $offset 開始件数
     * @param string $order 検索結果の並び順
     * @return array 受注の検索結果
     */
    function findOrders($where, $arrValues, $limit, $offset, $order, $join, $alias = null) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
	$table = "dtb_order";

        $objQuery->setLimitOffset($limit, $offset);
        $objQuery->setOrder($order);

	$col = "*";
	if ($alias) {
	    $table .= " as " . $alias;
	    $col = $alias . ".*";
	}
	$table .= $join;

        return $objQuery->select($col, $table, $where, $arrValues);
    }
}
?>
