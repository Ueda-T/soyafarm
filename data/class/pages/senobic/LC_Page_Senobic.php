<?php
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

class LC_Page_Senobic extends LC_Page_Ex
{
    /*
     */
    function init() {
        parent::init();
    }

    /**
     * Page のプロセス.
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    function action() {

        $objProduct = new SC_Product_Ex();
        $objCustomer = new SC_Customer_Ex();
        $objCartSess = new SC_CartSession_Ex();

        $this->arrProducts = $this->getProducts($objProduct);
        $this->tpl_product_class_id = $objProduct->product_class_id;
        $this->tpl_arrQuantity = $this->adjustQty($objProduct->arrQuantity);

        // ログイン判定 
        if($objCustomer->isLoginSuccess(true)) {
            $this->tpl_customer_kbn =
                $objCustomer->getValue('customer_kbn');
        } else {
            $this->tpl_customer_kbn = null;
        }

        if (empty($_POST)) {
            return;
        }

        $this->tpl_err_msg = "";
        $redirect = false;

        if(SC_Display_Ex::detectDevice() != DEVICE_TYPE_MOBILE) {
            // モバイル以外の処理

            // 定期購入判定
            if (!empty($_POST["regular_flg"]) &&
                $_POST["regular_flg"] == REGULAR_PURCHASE_FLG_ON) {
                // 定期購入
                    $regular_flg = REGULAR_PURCHASE_FLG_ON;
            } else {
                // 単回購入 
                $regular_flg = REGULAR_PURCHASE_FLG_OFF;
            }
        } else {
            // モバイルの処理

            // 定期購入判定
            if (!empty($_POST["select_regular"])) {
                // 定期購入
                $regular_flg = REGULAR_PURCHASE_FLG_ON;
            } else {
                // 単回購入 
                $regular_flg = REGULAR_PURCHASE_FLG_OFF;
            }
        }


        $product_cnt = 0;
        for($index = 1; $index <= $_POST["product_index"]; $index++) {

            // 商品規格ID
            $key = sprintf("product_class_id_%d", $index);
            $product_class_id = $this->arrForm[$key] = $_POST[$key];

            // 数量
            $key = sprintf("quantity_%d", $index);
            $quantity = $this->arrForm[$key] = $_POST[$key];

            if (empty($quantity) || $quantity == 0) {
                continue;
            }

            // 商品のカート挿入
            $objCartSess->addProduct
                ($product_class_id, $quantity, $regular_flg);
            $redirect = true;

            $product_cnt++;
        }

        if ($product_cnt < 1) {
            $this->tpl_err_msg .= "※数量を選択してください。";
        }

        if ($redirect == true && $this->tpl_err_msg == "") {
            // カート画面へリダイレクト
            SC_Response_Ex::sendRedirect(CART_URLPATH);
            exit;
        }
    }

    /**
     * 数量プルダウン用の配列を生成
     *
     * @param array $arrQty 数量情報の配列
     * @return $arrQty 生成した数量の配列
     */
    function adjustQty($arrQty) {

        foreach ($arrQty as $key => $qty) {
            // 0を先頭にセットする
            array_unshift($qty, 0);
            $arrQty[$key] = $qty;
        }
        return $arrQty;
    }

    /**
     * 商品情報一覧を取得
     *
     * @param  SC_Product $objProduct SC_Productのインスタンス
     * @return array 商品情報一覧
     */
    function getProducts(&$objProduct) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // 指定した商品IDのうち有効なものだけ抽出
        $sql =<<<_EOS
SELECT
    P.product_id
FROM
    dtb_products P
    INNER JOIN dtb_products_class PC
        ON PC.product_id = P.product_id
    INNER JOIN mtb_senobic S
        ON P.product_id = S.name
WHERE
    P.del_flg = 0
AND P.status = 1
AND PC.teiki_flg = 1
AND PC.sell_flg = 1
AND (P.disp_start_date IS NULL OR
     DATE_FORMAT(P.disp_start_date, '%Y%m%d') <= DATE_FORMAT(now(), '%Y%m%d'))
ORDER BY S.rank
_EOS;
        $result_id = $objQuery->getAll($sql);

        $ids = array();
        foreach ($result_id as $id) {
            $ids[] = $id["product_id"];
        }

        if (empty($ids)) {
            return;
        }

        // 商品一覧を取得する
        $where  = 'product_id IN (' . implode(',', $ids) . ')';
        // 表示順指定
        $order = 'FIELD(product_id, ' . implode(',', $ids) . ')';
        $objQuery->setWhere($where);
        $objQuery->setOrder($order);

        $arrProducts = $objProduct->lists($objQuery);

        $objProduct->setProductsClassByProductIds($ids);

        return $arrProducts;
    }
}

?>
