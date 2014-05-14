<?php
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/*
 *
 */
class LC_Page_Admin_Order_FollowMailHistory extends LC_Page_Admin_Ex {
    /*
     *
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'order/follow_mail_history.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'followMail_history';
        $this->tpl_pager = 'pager.tpl';
        $this->tpl_maintitle = 'フォローメール履歴照会';
        $this->tpl_subtitle = 'フォローメール履歴照会';

        $master = new SC_DB_MasterData_Ex();
        $this->arrPageMax = $master->getMasterData("mtb_page_max");

        $this->httpCacheControl('nocache');
    }

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
            if (count($this->arrErr) > 0) {
		break;
            }

	    list($where, $arrval) = $this->buildQuery($objFormParam);

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
	    $this->arrResults = $this->findFollowMailHistory
		($where, $arrval,$page_max, $objNavi->start_row);
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
        $objFormParam->addParam("フォローメール", "search_follow_mail",
                                STEXT_LEN, 'KVa',
                                array("MAX_LENGTH_CHECK", "SPTAB_CHECK"));

        $objFormParam->addParam("配信日(From)", "search_date_from",
                                STEXT_LEN, 'a',
                                array("MAX_LENGTH_CHECK", "SPTAB_CHECK"));

        $objFormParam->addParam("配信日(To)", "search_date_to",
                                STEXT_LEN, 'a',
                                array("MAX_LENGTH_CHECK", "SPTAB_CHECK"));

        $objFormParam->addParam("ページ送り番号","search_pageno",
                                INT_LEN, 'n',
                                array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("表示件数", "search_page_max",
                                INT_LEN, 'n',
                                array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("行NO", "line_no",
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
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return 
     */
    function buildQuery(&$objFormParam) {
	$arrParam = $objFormParam->getHashArray();
	$where = "";
	$arrValues = array();

	foreach ($arrParam as $key => $val) {
	    if($val == "") {
		continue;
	    }

	    switch ($key) {
		// フォローメール
	    case 'search_follow_mail':
		$where .= strlen($where) ? " and " : "";
		$where .= "hst.subject like ?";
		$arrValues[] = sprintf('%%%s%%', $objFormParam->getValue($key));
		break;
		// 配信日(From)
	    case 'search_date_from':
		$where .= strlen($where) ? " and " : "";
		$where .= "hst.start_date >= ?";
		$arrValues[] = $objFormParam->getValue($key). " 00:00:00";
		break;
		// 配信日(To)
	    case 'search_date_to':
		$where .= strlen($where) ? " and " : "";
		$where .= "hst.start_date <= ?";
		$arrValues[] = $objFormParam->getValue($key). " 23:59:59";
		break;
	    default:
		break;
	    }
	}

	if (strlen($where) > 0) {
	    $where = "where " . $where;
	}

	return array($where, $arrValues);
    }

    /**
     * 検索結果の行数を取得する.
     *
     * @param string $where 検索条件の WHERE 句
     * @param array $arrValues 検索条件のパラメーター
     * @return integer 検索結果の行数
     */
    function getNumberOfLines($where, $arrValues) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sql = <<<EOF
select
    count(*)
from
    dtb_follow_mail_history hst
$where
EOF;

        // 件数を取得
        return $objQuery->getOne($sql, $arrValues);
    }

    /**
     * 情報を検索する
     *
     * @param string  $where 検索条件の WHERE 句
     * @param array   $arrValues 検索条件のパラメーター
     * @param integer $limit 表示件数
     * @param integer $offset 開始件数
     * @return array  定期受注の検索結果
     */
    function findFollowMailHistory($where, $arrValues, $limit, $offset) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sql =<<<EOS
select
    hst.send_id
   ,hst.start_date
   ,hst.end_date
   ,hst.subject
   ,(SELECT COUNT(*)
       FROM dtb_follow_mail_customer cs0
      WHERE cs0.send_id = hst.send_id) AS all_count
   ,(SELECT COUNT(*)
       FROM dtb_follow_mail_customer cs1
      WHERE cs1.send_id = hst.send_id
        AND cs1.send_flg = 1) AS sent_count
   ,(SELECT COUNT(*)
       FROM dtb_follow_mail_customer cs2
      WHERE cs2.send_id = hst.send_id
        AND cs2.send_flg = 2) AS err_count
   ,(SELECT COUNT(*)
       FROM dtb_follow_mail_customer cs3
      WHERE cs3.send_id = hst.send_id
        AND cs3.send_flg IS NULL) AS unsent_count
from
    dtb_follow_mail_history hst
$where
group by
    hst.send_id
   ,hst.start_date
   ,hst.end_date
   ,hst.subject
order by
    hst.start_date desc
limit {$offset}, {$limit}
EOS;

        return $objQuery->getAll($sql, $arrValues);
    }
}
?>
