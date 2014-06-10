<?php
// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/mypage/LC_Page_AbstractMypage_Ex.php';

/**
 * MyPage のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Mypage.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_MyPage extends LC_Page_AbstractMypage_Ex {

    /** ページナンバー */
    var $tpl_pageno;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mypageno = 'index';
        $this->tpl_subtitle = 'マイページ';
        /*
        if (SC_Display_Ex::detectDevice() === DEVICE_TYPE_MOBILE){
            $this->tpl_subtitle = 'MYページ';
        } else {
            $this->tpl_subtitle = '配送先の登録・修正';
        }
        */
        $this->httpCacheControl('nocache');
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->httpCacheControl('nocache');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        parent::process();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    function action() {
	global $CLICK_ANALYZER_STATIC;

	// CLICK ANALYZER用埋め込み
	$this->tpl_clickAnalyzer = "";
	if (isset($CLICK_ANALYZER_STATIC["mypage"])) {
	    $this->tpl_clickAnalyzer = $CLICK_ANALYZER_STATIC["mypage"];
	}

	$_SESSION["MYPAGENO"] = $this->tpl_mypageno;

        $objCustomer = new SC_Customer_Ex();
        $customer_id = $objCustomer->getvalue('customer_id');

        //ページ送り用
        $this->objNavi = new SC_PageNavi_Ex
	    ($_REQUEST['pageno'],
	     $this->lfGetOrderHistory($customer_id),
	     SEARCH_PMAX,
	     'fnNaviPage',
	     NAVI_PMAX,
	     'pageno=#page#',
	     SC_Display_Ex::detectDevice() !== DEVICE_TYPE_MOBILE);

        $this->arrOrder = $this->lfGetOrderHistory
	    ($customer_id, $this->objNavi->start_row);

        $this->arrOrderMs = $this->lfGetOrderHistoryMs($customer_id, $this->arrOrder);

        switch ($this->getMode()) {
	case "getList":
	    echo SC_Utils_Ex::jsonEncode($this->arrOrder);
	    exit;
	    break;
	default:
	    break;
        }

        // 支払い方法の取得
        $this->arrPayment = SC_Helper_DB_Ex::sfGetIDValueList("dtb_payment", "payment_id", "payment_method");
        // 1ページあたりの件数
        $this->dispNumber = SEARCH_PMAX;

        $objFormParam   = new SC_FormParam_Ex();

        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();

        switch ($this->getMode()) {
        // お届け先の削除
        case 'delete':
            if ($objFormParam->checkError()) {
                SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
                exit;
            }

            $this->deleteOtherDeliv(
                $customer_id, $objFormParam->getValue('other_deliv_id'));
            break;

        // スマートフォン版のもっと見るボタン用
        case 'getList':
            $arrData = $objFormParam->getHashArray();

            //別のお届け先情報
            $arrOtherDeliv = $this->getOtherDeliv($customer_id, (($arrData['pageno'] - 1) * SEARCH_PMAX));

            //県名をセット
            $arrOtherDeliv = $this->setPref($arrOtherDeliv, $this->arrPref);
            $arrOtherDeliv['delivCount'] = count($arrOtherDeliv);

            $this->arrOtherDeliv = $arrOtherDeliv;

            echo SC_Utils_Ex::jsonEncode($this->arrOtherDeliv);
            exit;
            break;

        // お届け先の表示
        default:
            break;
        }

        //別のお届け先情報
        $this->arrOtherDeliv = $this->getOtherDeliv($customer_id);

        //お届け先登録数
        $this->tpl_linemax = count($this->arrOtherDeliv);

	// 割引率を取得する
	$typeCode = $_SESSION['customer']['customer_type_cd'];
	$this->tpl_discount = $this->getDiscountRate($typeCode);
    }

    function getDiscountRate($typeCode) {
        $objQuery = SC_Query_Ex::getSingletonInstance();
	$sql =<<<__EOS
select *
  from dtb_customer_type
 where customer_type_cd = {$typeCode}
   and del_flg = 0
__EOS;

        $result = $objQuery->getAll($sql);

        return $result[0];
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
     * 受注履歴を返す
     *
     * @param mixed $customer_id
     * @param mixed $startno 0以上の場合は受注履歴を返却する -1の場合は件数を返す
     * @access private
     * @return void
     */
    function lfGetOrderHistory($customer_id, $startno = -1) {
        $objQuery   = SC_Query_Ex::getSingletonInstance();
	$pending = ORDER_PENDING;
	$lineofpage = SEARCH_PMAX;

        if ($startno == -1) {
            $sql = <<<EOF
SELECT
    COUNT(*)
FROM
    dtb_order
WHERE
    del_flg = 0
    AND status != {$pending}
    AND customer_id = '{$customer_id}'
EOF;

            // 件数を取得
            return $objQuery->getOne($sql);
        }

	//        $objQuery->setLimitOffset(SEARCH_PMAX, $startno);

        $sql = <<<EOF
SELECT
    order_id,
    create_date,
    payment_id,
    payment_total
FROM
    dtb_order
WHERE
    del_flg = 0
    AND status != {$pending}
    AND customer_id = '{$customer_id}'
ORDER BY order_id DESC
LIMIT {$lineofpage} OFFSET {$startno}
EOF;

        //購入履歴の取得
        return $objQuery->getAll($sql);
    }

    /**
     * フォームパラメータの初期化
     *
     * @return SC_FormParam
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam('お届け先ID', 'other_deliv_id', INT_LEN, '', array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam("現在ページ", "pageno", INT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"), "", false);
    }

    /**
     * お届け先の取得
     *
     * @param integer $customerId
     * @param integer $startno
     * @return array
     */
    function getOtherDeliv($customer_id, $startno = '') {

        if (empty($customer_id)) {
            return;
        }

        $objQuery   =& SC_Query_Ex::getSingletonInstance();

        //スマートフォン用の処理
        if ($startno != '') {
            $objQuery->setLimitOffset(SEARCH_PMAX, $startno);
        }

        $sql =<<<EOF
SELECT
    *
FROM
    dtb_other_deliv
WHERE
    customer_id = $customer_id
ORDER BY other_deliv_id DESC
EOF;
        return $objQuery->getAll($sql);
    }

    /**
     * お届け先の削除
     *
     * @param integer $customerId
     * @param integer $delivId
     */
    function deleteOtherDeliv($customer_id, $deliv_id) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sql =<<<EOF
DELETE
FROM
    dtb_other_deliv
WHERE
    customer_id = $customer_id
    AND other_deliv_id = $deliv_id
EOF;
        $objQuery->query($sql);
    }
    
    /**
     * 県名をセット
     *
     * @param array $arrOtherDeliv
     * @param array $arrPref
     * return array
     */
    function setPref($arrOtherDeliv, $arrPref) {
        if (is_array($arrOtherDeliv)) {
            foreach($arrOtherDeliv as $key => $arrDeliv) {
                $arrOtherDeliv[$key]['prefname'] = $arrPref[$arrDeliv['pref']];
            }
        }
        return $arrOtherDeliv;
    }

    /**
     * 受注履歴（明細）を返す
     *
     * @param mixed $customer_id
     * @param mixed $startno 0以上の場合は受注履歴を返却する -1の場合は件数を返す
     * @access private
     * @return void
     */
    function lfGetOrderHistoryMs($customer_id, &$arrOrder) {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $pending = ORDER_PENDING;
	$ids = "";
	$nOrder = count($arrOrder);

	if ($nOrder == 0) {
	    return array();
	}

	for ($i = 0; $i < $nOrder; ++$i) {
	    $ids .= $arrOrder[$i]['order_id'];
	    if ($i < ($nOrder - 1)) {
		$ids .= ",";
	    }
	}

        $sql = <<<EOF
select
    dtb_order_detail.order_id,
    dtb_order_detail.product_id,
    dtb_order_detail.product_class_id,
    dtb_order_detail.product_name,
    dtb_order_detail.course_cd,
    dtb_order_detail.classcategory_name1,
    dtb_order_detail.classcategory_name2,
    dtb_products_class.sample_flg,
    dtb_products_class.present_flg,
    dtb_products_class.sell_flg,
    dtb_products_class.product_class_id as product_class_product_class_id,
    date_format(dtb_products.sale_start_date, '%Y%m%d') as sale_start_date,
    date_format(dtb_products.sale_end_date, '%Y%m%d') as sale_end_date,
    1 as product_valid_flg,
    dtb_products.status,
    date_format(dtb_products.disp_start_date, '%Y%m%d') as disp_start_date
from
    dtb_order_detail
inner join dtb_order
  on dtb_order_detail.order_id = dtb_order.order_id
  and dtb_order.order_id in ({$ids})
left outer join dtb_products
  on dtb_order_detail.product_id = dtb_products.product_id
left outer join dtb_products_class
  on dtb_order_detail.product_class_id = dtb_products_class.product_class_id
  and dtb_products_class.del_flg = 0
where
    dtb_order.del_flg = 0
    and dtb_order.status != {$pending}
    and dtb_order.customer_id = '{$customer_id}'
    and dtb_order_detail.sell_flg = 1
order by
    dtb_order_detail.product_id asc
EOF;

        //購入履歴の取得
        $arrOrderMs = $objQuery->getAll($sql);

        // 販売可否を判断
        foreach($arrOrderMs as $orderMs_index => $orderMsData) {
            foreach($orderMsData as $key => $val) {
                // 販売可否フラグ（1:有効 0:無効）
                $product_valid_flg = 1;

                // 規格IDが存在しないものは販売不可能
                if ($arrOrderMs[$orderMs_index]['product_class_product_class_id'] == null) {
                    $product_valid_flg = 0;
                }
                // 販売期間外は販売不可能
                if (!(empty($arrOrderMs[$orderMs_index]['sale_start_date'])) &&
                    $arrOrderMs[$orderMs_index]['sale_start_date'] > date("Ymd")) {
                    $product_valid_flg = 0;
                }
                if (!(empty($arrOrderMs[$orderMs_index]['sale_end_date'])) &&
                    $arrOrderMs[$orderMs_index]['sale_end_date'] < date("Ymd")) {
                    $product_valid_flg = 0;
                }
                // #227 販売対象フラグの判定追加
                if ($arrOrderMs[$orderMs_index]['sell_flg'] != SELL_FLG_ON) {
                    $product_valid_flg = 0;
                }
                // #227 公開・非公開の判定追加
                if ($arrOrderMs[$orderMs_index]['status'] ==
                    DEFAULT_PRODUCT_DISP) {
                        $product_valid_flg = 0;
                }
                // #227 掲載開始日の判定追加
                if (!(empty($arrOrderMs[$orderMs_index]['disp_start_date']))
                    && $arrOrderMs[$orderMs_index]['disp_start_date'] > date("Ymd")) {
                    $product_valid_flg = 0;
                }

                $arrOrderMs[$orderMs_index]['product_valid_flg'] = $product_valid_flg;
            }
        }
	return $arrOrderMs;
    }
}
