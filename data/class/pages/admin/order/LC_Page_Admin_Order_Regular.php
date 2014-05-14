<?php
require_once (CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php');

/**
 * 定期受注情報(詳細) のページクラス
 *
 * @package Page
 * @author IQUEVE Co.,Ltd.
 * @version $Id:$
 */
class LC_Page_Admin_Order_Regular extends LC_Page_Admin_Ex
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'order/regular.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'regular_search';
        $this->tpl_maintitle = '定期照会';
        $this->tpl_subtitle = '定期購入情報詳細';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');

        // 支払い方法の取得
        $this->arrPayment =
            SC_Helper_DB_Ex::sfGetIDValueList(
                "dtb_payment", "payment_id", "payment_method");

        // お届け間隔
        $this->arrTodokeKbn = $masterData->getMasterData('mtb_todoke_kbn');

        // お届け曜日
        $this->arrTodokeWeekNo =
            $masterData->getMasterData('mtb_todoke_week');
        //$this->arrTodokeWeek = $masterData->getMasterData('mtb_wday');
        $this->arrTodokeWeek = array(1 => '日',
                                     2 => '月',
                                     3 => '火',
                                     4 => '水',
                                     5 => '木',
                                     6 => '金',
                                     7 => '土');

        // 状況
        $this->arrRegularOrderStatus =
            $masterData->getMasterData("mtb_regular_order_status");

        // 請求書送付方法
        $this->arrIncludeKbn = 
            $masterData->getMasterData("mtb_include_kbn");

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
        $objPurchase = new SC_Helper_Purchase_Ex();

        // パラメーター初期化, 取得
        $this->lfInitFormParam($objFormParam, $_POST);
        $this->arrForm = $objFormParam->getHashArray();

        // 検索パラメーター引き継ぎ
        $this->arrSearchHidden = $objFormParam->getSearchArray();

        if ($this->arrForm['regular_id'] != "" &&
            $this->arrForm['line_no'] != "") {

            $this->arrForm = $this->lfGetRegularOrder
		($this->arrForm['regular_id'], $this->arrForm['line_no']);

	    // メール履歴取得
            $this->arrMailHistory = $this->getMailHistory
		($this->arrForm['regular_id'], $this->arrForm['line_no']);
        }

        // 配送業者に対応した、お届け時間の配列を取得
        if ($this->arrForm['deliv_id'] != "") {
            $this->arrDelivTime =
                $objPurchase->getDelivTime($this->arrForm['deliv_id']);
        }
    
    }

    /**
     * 指定された注文番号のメール履歴を取得する。
     * @var int order_id
     */
    function getMailHistory($regular_id, $line_no) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
	$sql =<<<__EOS
select send_date as send_date
     , subject as subject
     , send_id as send_id
  from dtb_regular_mail_history
 where regular_id = {$regular_id}
__EOS;

        return $objQuery->getAll($sql);
    }

    /**
     * パラメーター情報の初期化
     *
     * @param object $objFormParam SC_FormParamインスタンス
     * @param array $arrPost $_POSTデータ
     * @return void
     */
    function lfInitFormParam(&$objFormParam, $arrPost) {

        // POSTされる値
        $objFormParam->addParam("定期受注ID", "regular_id",
                                INT_LEN, 'n',
                                array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("行NO", "line_no",
                                INT_LEN, 'n',
                                array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        // 検索条件
        $objFormParam->addParam("顧客ID", "search_customer_id",
                                INT_LEN, 'n',
                                array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("顧客コード", "search_customer_cd",
                                INOS_CUSTOMER_CD_LEN, 'n',
                                array("MAX_LENGTH_CHECK", "NUM_CHECK"));

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

        $objFormParam->addParam("表示件数", "search_page_max",
                                INT_LEN, 'n',
                                array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("基幹連動", "search_kikan_flg",
                                INT_LEN, 'n',
                                array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->setParam($arrPost);
        $objFormParam->convParam();
    }

    /**
     * DBから定期受注情報を取得する
     *
     * @param int $regular_id
     * @param int $line_no
     * @return 定期受注情報の連想配列
     */
    function lfGetRegularOrder($regular_id, $line_no) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // 1～3日ごと
        $month_min = COURSE_CD_MONTH_MIN;
        $month_max = COURSE_CD_MONTH_MAX;
        // 20～90日ごと
        $day_min = COURSE_CD_DAY_MIN;
        $day_max = COURSE_CD_DAY_MAX;

        $sql =<<<EOS
SELECT R.regular_id
     , R.order_date
     , R.regular_base_no
     , R.payment_id
     , R.deliv_id
     , R.time_id
     , R.include_kbn
     , R.customer_id
     , C.name
     , C.kana
     , C.customer_cd
     , case
         when P.product_code is null
           then D.product_code
         else P.product_code end as product_code
     , D.product_name
     , D.quantity
     , case
         when D.course_cd >= {$month_min} and D.course_cd <= {$month_max}
           then concat(D.course_cd, 'ヶ月ごと')
         when D.course_cd >= {$day_min} and D.course_cd <= {$day_max}
           then concat(D.course_cd, '日ごと')
       else "" end as course_cd
     , D.todoke_week
     , D.todoke_week2
     , DATE_FORMAT(D.next_arrival_date, '%Y/%m/%d') AS next_arrival_date
     , DATE_FORMAT(D.after_next_arrival_date, '%Y/%m/%d') AS after_next_arrival_date
     , DATE_FORMAT(D.cancel_date,   '%Y/%m/%d') AS cancel_date
     , D.status
     , R.order_name
     , R.order_kana
     , R.order_tel
     , R.order_zip
     , R.order_pref
     , R.order_addr01
     , R.order_addr02
  FROM dtb_regular_order R
  LEFT JOIN dtb_regular_order_detail D
    ON R.regular_id = D.regular_id
        AND D.line_no = '{$line_no}'
        AND D.del_flg = 0
  LEFT JOIN dtb_order O
    ON R.order_id = O.order_id
        AND O.del_flg = 0
  LEFT JOIN dtb_customer C
    ON R.customer_id = C.customer_id
        AND C.del_flg = 0
  LEFT JOIN dtb_products_class P
    ON D.product_id = P.product_id
        AND P.del_flg = 0
WHERE R.regular_id = '{$regular_id}'
  AND R.del_flg = 0
EOS;

        $results = $objQuery->getAll($sql);

        return $results[0];

    }
}
?>
