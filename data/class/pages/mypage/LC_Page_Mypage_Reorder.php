<?php
require_once CLASS_EX_REALDIR .
    'page_extends/mypage/LC_Page_AbstractMypage_Ex.php';

/**
 * MyPage / 過去注文からのご購入のページクラス.
 */
class LC_Page_MyPage_Reorder extends LC_Page_AbstractMypage_Ex {
    /** ページナンバー */
    var $tpl_pageno;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mypageno = 'reorder.tpl';

        if (SC_Display_Ex::detectDevice() === DEVICE_TYPE_MOBILE) {
            $this->tpl_subtitle = 'MYページ';
        } else {
            $this->tpl_subtitle = '過去注文からのご購入';
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
            ($_REQUEST['pageno'], $this->lfGetOrderDetailHistoryCount
                ($customer_id),
	     SEARCH_PMAX, 'fnNaviPage', NAVI_PMAX, 'pageno=#page#',
         SC_Display_Ex::detectDevice() !== DEVICE_TYPE_MOBILE);

        // 購入商品一覧を取得
        $this->arrHistory = $this->lfGetOrderDetailHistory
            ($customer_id, $this->objNavi->start_row);

        // 販売可否を判断
        foreach($this->arrHistory as $index => $detail) {
                // 販売可否フラグ（1:有効 0:無効）
                $product_valid_flg = 1;

                // 規格IDが存在しない・サンプルフラグ・プレゼントフラグが立っているものは販売不可能
                if ($detail['product_class_product_class_id'] == null) {
                    $product_valid_flg = 0;
                }

                if ($detail['sample_flg'] == 1) {
                    $product_valid_flg = 0;
                }

                if ($detail['present_flg'] == 1) {
                    $product_valid_flg = 0;
                }

                // 販売期間外は販売不可能
                if (!(empty($detail['sale_start_date'])) && $detail['sale_start_date'] > date("Ymd")) {
                    $product_valid_flg = 0;
                }

                if (!(empty($detail['sale_end_date'])) && $detail['sale_end_date'] < date("Ymd")) {
                    $product_valid_flg = 0;
                }

                // #227 公開・非公開の判定追加
                if ($detail['status'] == DEFAULT_PRODUCT_DISP) {
                        $product_valid_flg = 0;
                }

                // #227 掲載開始日の判定追加
                if (!(empty($detail['disp_start_date']))
                    && $detail['disp_start_date'] > date("Ymd")) {

                    $product_valid_flg = 0;
                }

                $this->arrHistory[$index]['product_valid_flg'] = $product_valid_flg;
        }

        // 1ページあたりの件数
        $this->dispNumber = SEARCH_PMAX;
    }

    /**
     * 過去に注文した商品一覧をDBから取得する
     *
     * @param integer $customer_id 顧客ID
     * @param mixed $offset 開始行数
     */
    function lfGetOrderDetailHistory($customer_id, $offset) {
        
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $pending = ORDER_PENDING;
        $limit = SEARCH_PMAX;

        $sql =<<<__EOS
SELECT D.product_name
      ,D.classcategory_name1
      ,D.classcategory_name2
      ,D.product_id
      ,D.product_class_id
      ,D.course_cd
      ,PC.sample_flg
      ,PC.present_flg
      ,PC.product_class_id AS product_class_product_class_id
      ,DATE_FORMAT(P.sale_start_date, '%Y%m%d') AS sale_start_date
      ,DATE_FORMAT(P.sale_end_date, '%Y%m%d') AS sale_end_date
      ,1 AS product_valid_flg
      ,P.status
      ,DATE_FORMAT(P.disp_start_date, '%Y%m%d') AS disp_start_date
FROM dtb_order_detail D 
    INNER JOIN dtb_order H
        ON D.order_id = H.order_id
    LEFT OUTER JOIN dtb_products P
     ON D.product_id = P.product_id
        AND P.del_flg = 0
    LEFT OUTER JOIN dtb_products_class PC
     ON D.product_class_id = PC.product_class_id
        AND PC.del_flg = 0
WHERE
    customer_id = '{$customer_id}'
AND H.del_flg = 0
AND H.status != {$pending}
GROUP BY D.product_class_id
LIMIT $limit
OFFSET $offset
__EOS;

        return $objQuery->getAll($sql);

    }

    /**
     * 過去に注文した商品一覧の件数を取得する
     *
     * @param mixed $customer_id
     * @access private
     * @return void
     */
    function lfGetOrderDetailHistoryCount($customer_id) {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $pending = ORDER_PENDING;

        $sql = <<<__EOS
SELECT
    COUNT(DISTINCT D.product_class_id)
FROM dtb_order_detail D 
    INNER JOIN dtb_order H
        ON D.order_id = H.order_id
    LEFT OUTER JOIN dtb_products P
     ON D.product_id = P.product_id
        AND P.del_flg = 0
    LEFT OUTER JOIN dtb_products_class PC
     ON D.product_class_id = PC.product_class_id
        AND PC.del_flg = 0
WHERE
    customer_id = '{$customer_id}'
AND H.del_flg = 0
AND H.status != {$pending}
__EOS;
        // 件数を取得
        return $objQuery->getOne($sql);
    }

}
