<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * お届け先の複数指定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Shopping_Multiple.php 152 2012-08-15 06:43:18Z hira $
 */
class LC_Page_Shopping_Multiple extends LC_Page_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_title = "お届け先の複数指定";
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
     * Page のプロセス.
     *
     * @return void
     */
    function action() {
        $objSiteSess = new SC_SiteSession_Ex();
        $objCartSess = new SC_CartSession_Ex();
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objCustomer = new SC_Customer_Ex();
        $objFormParam = new SC_FormParam_Ex();

        $this->tpl_uniqid = $objSiteSess->getUniqId();

        $this->addrs = $this->getDelivAddrs($objCustomer, $objPurchase,
                                            $this->tpl_uniqid);
        $this->tpl_addrmax = count($this->addrs);
        $this->lfInitParam($objFormParam);

        $objPurchase->verifyChangeCart($this->tpl_uniqid, $objCartSess);

        // #78 複数配送先の操作性改善 / 分割数を保持
        if (isset($_POST['split_num']) == true) {
            $_POST['split_num'] = abs(sprintf('%d', $_POST['split_num']));
            // アドレス最大保持数を超える数値は異常
            if ($_POST['split_num'] > DELIV_ADDR_MAX) {
                $_POST['split_num'] = DELIV_ADDR_MAX;
            }
        }

        switch ($this->getMode()) {
            case 'confirm':
                $objFormParam->setParam($_POST);
                $this->arrErr = $this->lfCheckError($objFormParam);
                if (SC_Utils_Ex::isBlank($this->arrErr)) {
                    // フォームの情報を一時保存しておく
                    $_SESSION['multiple_temp'] = $objFormParam->getHashArray();
                    $this->saveMultipleShippings($this->tpl_uniqid, $objFormParam,
                                                 $objCustomer, $objPurchase,
                                                 $objCartSess);
                    $objSiteSess->setRegistFlag();
                    SC_Response_Ex::sendRedirect("payment.php");
                    exit;
                }
                break;

            // #78 複数配送先の操作性改善 / 配送先追加後も入力を保持
            case 'new_deliv':
                // 届け先追加直前のセッションを保存
                $objFormParam->setParam($_POST);
                $_SESSION['multiple_temp'] = $objFormParam->getHashArray();
                $_SESSION['multiple_temp']['is_new_deliv'] = true;

                // 配送先追加(別ウィンドウ)へリダイレクト
                $url = ROOT_URLPATH . 'mypage/delivery_addr.php?page=';
                $url .= urlencode($_SERVER['PHP_SELF']);
                SC_Response_Ex::sendRedirect($url);
                break;

            default:
                // #78 複数配送先の操作性改善 / 配送先追加後に分割数を復元
                if (isset($_POST['split_num']) == true) {
                    // POSTから
                    $objFormParam->setValue('split_num', $_POST['split_num']);

                } else if (isset($_SESSION['multiple_temp']['is_new_deliv'])) {
                    // セッションから
                    $objFormParam->setValue('split_num',
                        $_SESSION['multiple_temp']['split_num']);
                }
                $this->setParamToSplitItems($objFormParam, $objCartSess);
                // #78 複数配送先の操作性改善 / 配送先追加後に入力を復元
                if (isset($_SESSION['multiple_temp'])) {
                    $objFormParam->setParam($_SESSION['multiple_temp']);
                    unset($_SESSION['multiple_temp']);
                }
        }

        // 前のページから戻ってきた場合
        if ($_GET['from'] == 'multiple') {
            $objFormParam->setParam($_SESSION['multiple_temp']);
        }

        $this->arrForm = $objFormParam->getFormParamList();

        // #78 複数配送先の操作性改善 / 分割指定リストを生成
        $this->arrSplitSel = array();
        for ($i = 1; $i < count($this->addrs); $i++) {
            $this->arrSplitSel[$i] = sprintf('各商品を%d箇所分に', $i);
        }
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
     * フォームを初期化する.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam("商品規格ID", "product_class_id", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("商品名", "name");
        $objFormParam->addParam("規格1", "class_name1");
        $objFormParam->addParam("規格2", "class_name2");
        $objFormParam->addParam("規格分類1", "classcategory_name1");
        $objFormParam->addParam("規格分類2", "classcategory_name2");
        $objFormParam->addParam("メイン画像", "main_image");
        $objFormParam->addParam("メイン一覧画像", "main_list_image");
        $objFormParam->addParam("販売価格", "price");
        $objFormParam->addParam("数量", 'quantity', INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), 1);
        $objFormParam->addParam("配送先住所", 'shipping', INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("カート番号", "cart_no", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("行数", "line_of_num", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        // #78 複数配送先の操作性改善 / 分割数
        $objFormParam->addParam("分割数", "split_num", INT_LEN, 'n', array(), '0');
    }

    /**
     * カートの商品を数量ごとに分割し, フォームに設定する.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param SC_CartSession $objCartSess SC_CartSession インスタンス
     * @return void
     */
    function setParamToSplitItems(&$objFormParam, &$objCartSess) {
        $cartLists =& $objCartSess->getCartList($objCartSess->getKey());
        $arrItems = array();
        $index = 0;
        // #78 複数配送先の操作性改善 / 分割数を取得
        $split_num = $objFormParam->getValue('split_num');
        foreach (array_keys($cartLists) as $key) {
            $arrProductsClass = $cartLists[$key]['productsClass'];
            $quantity = (int) $cartLists[$key]['quantity'];
            // #78 複数配送先の操作性改善 / 任意分割処理
            if ($split_num < 1) {
                // 従来の処理
                for ($i = 0; $i < $quantity; $i++) {
                    foreach ($arrProductsClass as $key2 => $val) {
                        $arrItems[$key2][$index] = $val;
                    }
                    $arrItems['quantity'][$index] = 1;
                    $arrItems['price'][$index] = $cartLists[$key]['price'];
                    $index++;
                }
            } else {
                //// 分割数が指定されている
                // 数量を分割数に振り分ける(満たない場合は分けない)
                $arrBox = array();
                $box_idx = 0;
                for ($i = 0; $i < $quantity; $i++) {
                    if ($box_idx >= $split_num) {
                        $box_idx = 0;
                    }
                    if (isset($arrBox[$box_idx]) == false) {
                        $arrBox[$box_idx] = 0;
                    } 
                    $arrBox[$box_idx++]++;
                }
                // 分割した数量を商品へ割り当てる
                foreach ($arrBox as $qt) {
                    foreach ($arrProductsClass as $key2 => $val) {
                        $arrItems[$key2][$index] = $val;
                    }
                    $arrItems['quantity'][$index] = $qt;
                    $arrItems['price'][$index] = $cartLists[$key]['price'];
                    $index++;
                }
            }
        }
        $objFormParam->setParam($arrItems);
        $objFormParam->setValue('line_of_num', $index);
    }

    /**
     * 配送住所のプルダウン用連想配列を取得する.
     *
     * 会員ログイン済みの場合は, 会員登録住所及び追加登録住所を取得する.
     * 非会員の場合は, 「お届け先の指定」画面で入力した住所を取得する.
     *
     * @param SC_Customer $objCustomer SC_Customer インスタンス
     * @param SC_Helper_Purchase $objPurchase SC_Helper_Purchase インスタンス
     * @param integer $uniqid 受注一時テーブルのユニークID
     * @return array 配送住所のプルダウン用連想配列
     */
    function getDelivAddrs(&$objCustomer, &$objPurchase, $uniqid) {
        $masterData = new SC_DB_MasterData();
        $arrPref = $masterData->getMasterData('mtb_pref');

        $arrResults = array('' => '選択してください');
        // 会員ログイン時
        if ($objCustomer->isLoginSuccess(true)) {
            $arrAddrs = $objCustomer->getCustomerAddress($objCustomer->getValue('customer_id'));
            foreach ($arrAddrs as $val) {
                $other_deliv_id = SC_Utils_Ex::isBlank($val['other_deliv_id']) ? 0 : $val['other_deliv_id'];
                $arrResults[$other_deliv_id] = $val['name']
                    . " " . $arrPref[$val['pref']] . $val['addr01'] . $val['addr02'];
            }
        }
        // 非会員
        else {
            $arrShippings = $objPurchase->getShippingTemp();
            foreach ($arrShippings as $shipping_id => $val) {
                $arrResults[$shipping_id] = $val['shipping_name']
                    . " " . $arrPref[$val['shipping_pref']]
                    . $val['shipping_addr01'] . $val['shipping_addr02'];
            }
        }
        return $arrResults;
    }

    /**
     * 入力チェックを行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return array エラー情報の配列
     */
    function lfCheckError(&$objFormParam) {
        $objCartSess = new SC_CartSession_Ex();

        $objFormParam->convParam();
        // 数量未入力は0に置換
        $objFormParam->setValue('quantity', $objFormParam->getValue('quantity', 0));

        $arrErr = $objFormParam->checkError();

        $arrParams = $objFormParam->getSwapArray();

        if (empty($arrErr)) {
            foreach ($arrParams as $index => $arrParam) {
                // 数量0で、お届け先を選択している場合
                if ($arrParam['quantity'] == 0 && !SC_Utils_Ex::isBlank($arrParam['shipping'])) {
                    $arrErr['shipping'][$index] = '※ 数量が0の場合、お届け先を入力できません。<br />';;
                }
                // 数量の入力があり、お届け先を選択していない場合
                if ($arrParam['quantity'] > 0 && SC_Utils_Ex::isBlank($arrParam['shipping'])) {
                    $arrErr['shipping'][$index] = '※ お届け先が入力されていません。<br />';
                }
            }
        }

        // 入力エラーが無い場合、カゴの中身との数量の整合を確認
        if (empty($arrErr)) {
            $arrQuantity = array();
            // 入力内容を集計
            foreach ($arrParams as $arrParam) {
                $product_class_id = $arrParam['product_class_id'];
                $arrQuantity[$product_class_id] += $arrParam['quantity'];
            }
            // カゴの中身と突き合わせ
            $cartLists =& $objCartSess->getCartList($objCartSess->getKey());
            foreach ($cartLists as $arrCartRow) {
                $product_class_id = $arrCartRow['id'];
                // 差異がある場合、エラーを記録
                if ($arrCartRow['quantity'] != $arrQuantity[$product_class_id]) {
                    foreach ($arrParams as $index => $arrParam) {
                        if ($arrParam['product_class_id'] == $product_class_id) {
                            $arrErr['quantity'][$index] = '※ 数量合計を「' . $arrCartRow['quantity'] .'」にしてください。<br />';
                        }
                    }
                }
            }
        }
        return $arrErr;
    }

    /**
     * 複数配送情報を一時保存する.
     *
     * 会員ログインしている場合は, その他のお届け先から住所情報を取得する.
     *
     * @param integer $uniqid 一時受注テーブルのユニークID
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param SC_Customer $objCustomer SC_Customer インスタンス
     * @param SC_Helper_Purchase $objPurchase SC_Helper_Purchase インスタンス
     * @param SC_CartSession $objCartSess SC_CartSession インスタンス
     * @return void
     */
    function saveMultipleShippings($uniqid, &$objFormParam, &$objCustomer,
                                   &$objPurchase, &$objCartSess) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $arrParams = $objFormParam->getSwapArray();

        foreach ($arrParams as $arrParam) {
            $other_deliv_id = $arrParam['shipping'];

            if ($objCustomer->isLoginSuccess(true)) {
                if ($other_deliv_id != 0) {
                    $sql =<<<EOF
SELECT
    *
FROM
    dtb_other_deliv
WHERE
    other_deliv_id = $other_deliv_id
EOF;
                    $otherDeliv = $objQuery->getAll($sql);

                    foreach ($otherDeliv[0] as $key => $val) {
                        $arrValues[$other_deliv_id]['shipping_' . $key] = $val;
                    }
                } else {
                    $objPurchase->copyFromCustomer($arrValues[0], $objCustomer,
                                                   'shipping');
                }
            } else {
                $arrValues = $objPurchase->getShippingTemp();
            }
            $arrItemTemp[$other_deliv_id][$arrParam['product_class_id']] += $arrParam['quantity'];
        }

        $objPurchase->clearShipmentItemTemp();

        foreach ($arrValues as $shipping_id => $arrVal) {
            $objPurchase->saveShippingTemp($arrVal, $shipping_id);
        }

        foreach ($arrItemTemp as $other_deliv_id => $arrProductClassIds) {
            foreach ($arrProductClassIds as $product_class_id => $quantity) {
                if ($quantity == 0) continue;
                $objPurchase->setShipmentItemTemp($other_deliv_id,
                                                  $product_class_id,
                                                  $quantity);
            }
        }

        // $arrValues[0] には, 購入者の情報が格納されている
        $objPurchase->saveOrderTemp($uniqid, $arrValues[0], $objCustomer);
    }
}
?>
