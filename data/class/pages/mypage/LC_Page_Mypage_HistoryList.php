<?php
require_once CLASS_EX_REALDIR . 'page_extends/mypage/LC_Page_AbstractMypage_Ex.php';

/**
 * MyPage のページクラス.
 */
class LC_Page_MyPage_HistoryList extends LC_Page_AbstractMypage_Ex {
    /** ページナンバー */
    var $tpl_pageno;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mypageno = 'history_list';

        if (SC_Display_Ex::detectDevice() === DEVICE_TYPE_MOBILE) {
            $this->tpl_subtitle = 'MYページ';
        } else {
            $this->tpl_subtitle = 'ご注文履歴';
        }
        $this->httpCacheControl('nocache');
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    function action() {
	$_SESSION["MYPAGENO"] = $this->tpl_mypageno;

        $objCartSess = new SC_CartSession_Ex();
        $chk_flg = true;
        $master = new SC_DB_MasterData_Ex();
        $this->arrOrderStatus = $master->getDbMasterDataNatural
	    ("mtb_order_status", array("id", "name", "image_s", "image_l"));

        // 商品チェック時はカート画面に遷移
        if (isset($_POST["post_flg"])) {
	    for ($i = 0; $i < $_POST["product_cnt"]; ++$i) {
                if (isset($_POST["chk_product_".$i])) {
                    $product_class_id = $_POST["chk_product_".$i];
                    // 購入区分 単回:0 定期:1
            	    $how = $_POST["course_cd_".$i];
                    if ($how != 0) {
                        $how = 1;
                    }

            	    $objCartSess->addProduct($product_class_id, 1, $how);
            	    $redirect = true;
                }
	    }

	    if ($redirect == true) {
		SC_Response_Ex::sendRedirect(CART_URLPATH);
		exit;
	    }
        }

        $objCustomer = new SC_Customer_Ex();
        $customer_id = $objCustomer->getvalue('customer_id');

        //ページ送り用
        $this->objNavi = new SC_PageNavi_Ex
	    ($_REQUEST['pageno'], $this->lfGetOrderHistory($customer_id),
	     SEARCH_PMAX, 'fnNaviPage', NAVI_PMAX, 'pageno=#page#',
	     SC_Display_Ex::detectDevice() !== DEVICE_TYPE_MOBILE);

        $this->arrOrder = $this->lfGetOrderHistory
	    ($customer_id, $this->objNavi->start_row);

        $arrOrderMs = $this->lfGetOrderHistoryMs($customer_id, $this->arrOrder);

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

        $this->arrOrderMs = $arrOrderMs;

        // 支払い方法の取得
        $this->arrPayment = SC_Helper_DB_Ex::sfGetIDValueList("dtb_payment", "payment_id", "payment_method");
        // 1ページあたりの件数
        $this->dispNumber = SEARCH_PMAX;
    }

    /**
     * 受注履歴を返す
     *
     * @param mixed $customer_id
     * @param mixed $startno 0以上の場合は受注履歴を返却する -1の場合は件数を返す
     * @access private
     * @return void
     */
    function lfGetOrderHistory($customer_id, $offset = -1) {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $pending = ORDER_PENDING;

        if ($offset == -1) {
            $sql = <<<EOF
select count(*)
  from dtb_order
 where del_flg = 0
   and status != {$pending}
   and customer_id = '{$customer_id}'
EOF;

            // 件数を取得
            return $objQuery->getOne($sql);
        }

	$limit = SEARCH_PMAX;
        $sql = <<<EOF
  select order_id
       , create_date
       , status
       , payment_id
       , payment_total
    from dtb_order o
   where del_flg = 0
     and status != {$pending}
     and customer_id = '{$customer_id}'
order by order_id desc
   limit {$limit}
  offset {$offset}
EOF;

        //購入履歴の取得
        return $objQuery->getAll($sql);
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
        return $objQuery->getAll($sql);
    }
}
