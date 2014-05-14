<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * 定期照会 のページクラス
 *
 * @package Page
 * @author IQUEVE CO.,LTD.
 * @version $Id:$
 */
class LC_Page_Admin_Order_RegularSearch extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'order/regular_search.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'regular_search';
        $this->tpl_pager = 'pager.tpl';
        $this->tpl_maintitle = '定期照会';
        $this->tpl_subtitle = '定期照会';

        $masterData = new SC_DB_MasterData_Ex();

        // 状況 検索用
        $this->arrRegularOrderStatus 
            = $masterData->getMasterData("mtb_regular_order_status");
        // 支払い方法 検索用
        $this->arrPayments = SC_Helper_DB_Ex::sfGetIDValueList(
            "dtb_payment", "payment_id", "payment_method");
        // 検索結果表示件数
        $this->arrPageMax = $masterData->getMasterData("mtb_page_max");
        $this->arrKikanFlg = array(1=>"連携済み", 2=>"未連携");

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

        $objFormParam->setParam($_POST);
        $this->arrHidden = $objFormParam->getSearchArray();
        $this->arrForm = $objFormParam->getFormParamList();

        switch ($this->getMode()) {
        // 検索パラメーターの生成
        case 'search':
            $objFormParam->convParam();
            $objFormParam->trimParam();
            $this->arrErr = $this->lfCheckError($objFormParam);
            $arrParam = $objFormParam->getHashArray();

            if (count($this->arrErr) == 0) {
                $where = "RD.del_flg = 0";
                foreach ($arrParam as $key => $val) {
                    if($val == "") {
                        continue;
                    }
                    $this->buildQuery($key, $where, $arrval, $objFormParam);
                }

                // 行数の取得
                $this->tpl_linemax = $this->getNumberOfLines($where, $arrval);

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
                $this->arrResults = $this->findRegularOrders
                    ($where, $arrval,$page_max, $objNavi->start_row);

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
        $objFormParam->addParam("顧客ID", "search_customer_id",
                                INT_LEN, 'n',
                                array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("顧客コード", "search_customer_cd",
                                INOS_CUSTOMER_CD_LEN, 'n',
	                            array("MAX_LENGTH_CHECK", "ALNUM_CHECK"));

        $objFormParam->addParam("状況", "search_status",
                                INT_LEN, 'n',
                                array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("お名前", "search_order_name",
                                STEXT_LEN, 'KVa',
                                array("MAX_LENGTH_CHECK"));

        $objFormParam->addParam("お名前(フリガナ)", "search_order_kana",
                                STEXT_LEN, 'kVCa',
                                array("KANA_CHECK","MAX_LENGTH_CHECK"));

        $objFormParam->addParam("支払い方法", "search_payment_id",
                                INT_LEN, 'n',
                                array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("申込日(From)", "search_order_date_from",
                                STEXT_LEN, 'a',
                                array("MAX_LENGTH_CHECK", "SPTAB_CHECK"));

        $objFormParam->addParam("申込日(To)", "search_order_date_to",
                                STEXT_LEN, 'a',
                                array("MAX_LENGTH_CHECK", "SPTAB_CHECK"));

        $objFormParam->addParam("終了日(From)", "search_cancel_date_from",
                                STEXT_LEN, 'a',
                                array("MAX_LENGTH_CHECK", "SPTAB_CHECK"));

        $objFormParam->addParam("終了日(To)", "search_cancel_date_to",
                                STEXT_LEN, 'a',
                                array("MAX_LENGTH_CHECK", "SPTAB_CHECK"));

        $objFormParam->addParam("商品名", "search_product_name",
                                STEXT_LEN, '',
                                array("MAX_LENGTH_CHECK"));

        $objFormParam->addParam("商品コード", "search_product_code",
                                STEXT_LEN, 'KVa',
                                array("MAX_LENGTH_CHECK"));

        $objFormParam->addParam("ページ送り番号","search_pageno",
                                INT_LEN, 'n',
                                array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("定期受注ID", "regular_id",
                                INT_LEN, 'n',
                                array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("行NO", "line_no",
                                INT_LEN, 'n',
                                array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("表示件数", "search_page_max",
                                INT_LEN, 'n',
                                array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("基幹連動", "search_kikan_flg",
                                INT_LEN, 'n',
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
    function buildQuery($key, &$where, &$arrValues, &$objFormParam) {
        $dbFactory = SC_DB_DBFactory_Ex::getInstance();
        switch ($key) {
        // 顧客ID
        case 'search_customer_id':
            $where .= " AND R.customer_id = ?";
            $arrValues[] = sprintf('%d', $objFormParam->getValue($key));
            break;
        // 基幹顧客CD
        case 'search_customer_cd':
            $where .= " AND C.customer_cd = ?";
            $arrValues[] = sprintf('%s', $objFormParam->getValue($key));
            break;
        // 状況
        case 'search_status':
            $where.= " AND R.status = ?";
            $arrValues[] = $objFormParam->getValue($key);
            break;
        // お名前
        case 'search_order_name':
            $where .= " AND replace(replace(" . $prefix . "C.name, ' ', ''), '　', '') LIKE ?";
            $arrValues[] = sprintf('%%%s%%', mb_ereg_replace("[ 　]", "", $objFormParam->getValue($key)));
            break;
        // お名前(フリガナ)
        case 'search_order_kana':
            $where .= " AND replace(replace(" . $prefix . "C.kana, ' ', ''), '　', '') LIKE ?";
            $arrValues[] = sprintf('%%%s%%', mb_ereg_replace("[ 　]", "", $objFormParam->getValue($key)));
            break;
        // 支払方法
        case 'search_payment_id':
            $tmp_where = "";
            foreach($objFormParam->getValue($key) as $element) {
                if($element != "") {
                    if($tmp_where == "") {
                        $tmp_where .= " AND (R.payment_id = ?";
                    } else {
                        $tmp_where .= " OR R.payment_id = ?";
                    }
                    $arrValues[] = $element;
                }
            }
            if(!SC_Utils_Ex::isBlank($tmp_where)) {
                $tmp_where .= ")";
                $where .= " $tmp_where ";
            }
            break;
        // 申込み日(From)
        case 'search_order_date_from':
            $where .= " AND R.order_date >= ?";
            $arrValues[] = $objFormParam->getValue($key);
            break;
        // 申込み日(To)
        case 'search_order_date_to':
            $where .= " AND R.order_date <= ?";
            $arrValues[] = $objFormParam->getValue($key);
            break;
        // 終了日(From)
        case 'search_cancel_date_from':
            $where .= " AND RD.cancel_date >= ?";
            $arrValues[] = $objFormParam->getValue($key);
            break;
        // 終了日(To)
        case 'search_cancel_date_to':
            $where .= " AND RD.cancel_date <= ?";
            $arrValues[] = $objFormParam->getValue($key);
            break;
        // 商品コード
        case 'search_product_code':
            $where .= " AND PC.product_code LIKE ?";
            $arrValues[] = sprintf('%%%s%%', $objFormParam->getValue($key));
            break;
        // 商品名
        case 'search_product_name':
            $where .= " AND RD.product_name LIKE ?";
            $arrValues[] = sprintf('%%%s%%', $objFormParam->getValue($key));
            break;
        // 基幹連動
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
                        $tmp_where .= "R.regular_base_no IS NOT NULL ";
		    } else if ($element == 2) {
                        $tmp_where .= "R.regular_base_no IS NULL ";
		    }
                    $arrValues[] = $element;
                }
            }
            if(!SC_Utils_Ex::isBlank($tmp_where)) {
                $tmp_where .= ")";
                $where .= " $tmp_where ";
            }
            break;
        default:
        }
    }

    /**
     * 検索結果の行数を取得する.
     *
     * @param string $where 検索条件の WHERE 句
     * @param array $arrValues 検索条件のパラメーター
     * @return integer 検索結果の行数
     */
    function getNumberOfLines($where, $arrValues) {

        $objQuery   = SC_Query_Ex::getSingletonInstance();

        $sql = <<<EOF
SELECT
    COUNT(*)
FROM
    dtb_regular_order_detail RD
    INNER JOIN dtb_regular_order R
        ON RD.regular_id = R.regular_id
        AND RD.del_flg = 0
    INNER JOIN dtb_products_class PC
        ON RD.product_class_id = PC.product_class_id
    INNER JOIN dtb_customer C
        ON R.customer_id = C.customer_id
WHERE {$where}
EOF;
        // 件数を取得
        return $objQuery->getOne($sql, $arrValues);
    }

    /**
     * 定期受注情報を検索する
     *
     * @param string  $where 検索条件の WHERE 句
     * @param array   $arrValues 検索条件のパラメーター
     * @param integer $limit 表示件数
     * @param integer $offset 開始件数
     * @return array  定期受注の検索結果
     */
    function findRegularOrders($where, $arrValues, $limit, $offset) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sql =<<<EOS
SELECT DATE_FORMAT(R.order_date, '%Y/%m/%d') AS order_date
     , RD.regular_id
     , RD.line_no
     , C.name AS customer_name
     , R.order_name
     , R.payment_id
     , RD.product_name
     , RD.status
FROM
    dtb_regular_order_detail RD
    INNER JOIN dtb_regular_order R
        ON RD.regular_id = R.regular_id
        AND RD.del_flg = 0
    LEFT JOIN dtb_products_class PC
        ON RD.product_class_id = PC.product_class_id
    INNER JOIN dtb_customer C
        ON R.customer_id = C.customer_id
WHERE {$where}
ORDER BY RD.regular_id, RD.line_no
LIMIT {$offset}, {$limit}

EOS;
        return $objQuery->getAll($sql, $arrValues);
    }

}
?>
