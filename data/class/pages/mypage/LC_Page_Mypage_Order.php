<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/mypage/LC_Page_AbstractMypage_Ex.php';

/**
 * 受注履歴からカート遷移 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Mypage_Order.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Mypage_Order extends LC_Page_AbstractMypage_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
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
        $objCustomer = new SC_Customer_Ex();

        //受注詳細データの取得
        $arrOrderDetail = $this->lfGetOrderDetail($_POST['order_id']);

        //ログインしていない、またはDBに情報が無い場合
        if (empty($arrOrderDetail)){
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }

        $this->lfAddCartProducts($arrOrderDetail);
        SC_Response_Ex::sendRedirect(CART_URLPATH);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    // 受注詳細データの取得
    function lfGetOrderDetail($order_id) {
        $objQuery       = SC_Query_Ex::getSingletonInstance();

        $objCustomer    = new SC_Customer_Ex();
        //customer_idを検証
        $customer_id    = $objCustomer->getValue("customer_id");

        $sql_count_order =<<<EOF
SELECT
    COUNT(*)
FROM
    dtb_order
WHERE
    order_id = $order_id
    AND customer_id = $customer_id
EOF;
        $order_count = $objQuery->getOne($sql_count_order);

        if ($order_count != 1) return array();

        $sql_select_order =<<<EOF
SELECT
    product_class_id,
    quantity
FROM
    dtb_order_detail
    LEFT JOIN dtb_products_class USING(product_class_id)
WHERE
    order_id = $order_id
ORDER BY order_detail_id
EOF;

        return $objQuery->getAll($sql_select_order);

    }

    // 商品をカートに追加
    function lfAddCartProducts($arrOrderDetail) {

        $objCartSess = new SC_CartSession_Ex();
        foreach($arrOrderDetail as $order_row) {

            $objCartSess->addProduct($order_row['product_class_id'],
                                     $order_row['quantity']);
        }
    }
}
