<?php

/**
 * 商品購入関連のヘルパークラス.
 *
 * TODO 購入時強制会員登録機能(#521)の実装を検討
 * TODO dtb_customer.buy_times, dtb_customer.buy_total の更新
 *
 * @package Helper
 * @author Kentaro Ohkouchi
 * @version $Id: SC_Helper_Purchase.php 91 2012-04-11 04:39:04Z hira $
 */
class SC_Helper_Purchase {

    /**
     * 受注を完了する.
     *
     * 下記のフローで受注を完了する.
     *
     * 1. トランザクションを開始する
     * 2. カートの内容を検証する.
     * 3. 受注一時テーブルから受注データを読み込む
     * 4. ユーザーがログインしている場合はその他の発送先へ登録する
     * 5. 受注データを受注テーブルへ登録する
     * 6. トランザクションをコミットする
     *
     * 実行中に, 何らかのエラーが発生した場合, 処理を中止しエラーページへ遷移する
     *
     * 決済モジュールを使用する場合は受注ステータスを「決済処理中」に設定し,
     * 決済完了後「新規受付」に変更すること
     *
     * @param integer $orderStatus 受注処理を完了する際に設定する受注ステータス
     * @return void
     */
    function completeOrder($orderStatus = ORDER_NEW) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objSiteSession = new SC_SiteSession_Ex();
        $objCartSession = new SC_CartSession_Ex();
        $objCustomer = new SC_Customer_Ex();
        $customerId = $objCustomer->getValue('customer_id');

        $objQuery->begin();
        if (!$objSiteSession->isPrePage()) {
            SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, $objSiteSession);
        }

        $uniqId = $objSiteSession->getUniqId();
        $this->verifyChangeCart($uniqId, $objCartSession);

        $orderTemp = $this->getOrderTemp($uniqId);

        $orderTemp['status'] = $orderStatus;
        $cartkey = $objCartSession->getKey();
        $orderId = $this->registerOrderComplete($orderTemp, $objCartSession,
                                                $cartkey, $arrShip);
        $isMultiple = SC_Helper_Purchase::isMultiple();
        $shippingTemp =& $this->getShippingTemp( $isMultiple );
        foreach ($shippingTemp as $shippingId => $val) {
            $this->registerShipmentItem($orderId, $shippingId,
                                        $val['shipment_item']);
        }

        $this->registerShipping($orderId, $shippingTemp, true, $arrShip);

        if ($orderStatus == ORDER_NEW) {
            // ▼2013.12.20 #30 定期受注情報を登録する
            $objRegular = new SC_Helper_Regular_Ex();
            $objRegular->registerRegularOrderComplete(  
                $orderTemp, $shippingTemp[0], $objCartSession, 
                $cartkey, $arrShip
            );
        } else {
            // 決済モジュールの場合はここでは定期情報を登録しない
            // 定期情報をセッションへ保存しておく
            $this->saveRegularTemp
                ($orderTemp, $shippingTemp[0], $objCartSession, 
                    $cartkey, $arrShip
            );
        }

        $objQuery->commit();
        $this->cleanupSession($orderId, $objCartSession, $objCustomer, $cartkey);

        GC_Utils_Ex::gfFrontLog("order complete. customerId=" . $customerId);
    }

    /**
     * 受注をキャンセルする.
     *
     * 受注完了後の受注をキャンセルする.
     * この関数は, 主に決済モジュールにて, 受注をキャンセルする場合に使用する.
     *
     * 受注ステータスを引数 $orderStatus で指定したステータスに変更する.
     * (デフォルト ORDER_CANCEL)
     * 引数 $is_delete が true の場合は, 受注データを論理削除する.
     * 商品の在庫数は, 受注前の在庫数に戻される.
     *
     * @param integer $order_id 受注ID
     * @param integer $orderStatus 受注ステータス
     * @param boolean $is_delete 受注データを論理削除する場合 true
     * @return void
     */
    function cancelOrder($order_id, $orderStatus = ORDER_CANCEL, $is_delete = false) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $in_transaction = $objQuery->inTransaction();
        if (!$in_transaction) {
            $objQuery->begin();
        }

        $arrParams['status'] = $orderStatus;
        if ($is_delete) {
            $arrParams['del_flg'] = 1;
        }

        $this->registerOrder($order_id, $arrParams);

        $arrOrderDetail = $this->getOrderDetail($order_id);
        foreach ($arrOrderDetail as $arrDetail) {
            $objQuery->update('dtb_products_class', array(),
                              "product_class_id = ?", array($arrDetail['product_class_id']),
                              array('stock' => 'stock + ?'), array($arrDetail['quantity']));
        }
        if (!$in_transaction) {
            $objQuery->commit();
        }
    }

    /**
     * 受注をキャンセルし, カートをロールバックして, 受注一時IDを返す.
     *
     * 受注完了後の受注をキャンセルし, カートの状態を受注前の状態へ戻す.
     * この関数は, 主に, 決済モジュールに遷移した後, 購入確認画面へ戻る場合に使用する.
     *
     * 受注ステータスを引数 $orderStatus で指定したステータスに変更する.
     * (デフォルト ORDER_CANCEL)
     * 引数 $is_delete が true の場合は, 受注データを論理削除する.
     * 商品の在庫数, カートの内容は受注前の状態に戻される.
     *
     * @param integer $order_id 受注ID
     * @param integer $orderStatus 受注ステータス
     * @param boolean $is_delete 受注データを論理削除する場合 true
     * @return string 受注一時ID
     */
    function rollbackOrder($order_id, $orderStatus = ORDER_CANCEL, $is_delete = false) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $in_transaction = $objQuery->inTransaction();
        if (!$in_transaction) {
            $objQuery->begin();
        }

        $this->cancelOrder($order_id, $orderStatus, $is_delete);
        $arrOrderTemp = $this->getOrderTempByOrderId($order_id);
        $_SESSION = array_merge($_SESSION, unserialize($arrOrderTemp['session']));

        $objSiteSession = new SC_SiteSession_Ex();
        $objCartSession = new SC_CartSession_Ex();
        $objCustomer = new SC_Customer_Ex();

        // 新たに受注一時情報を保存する
        $objSiteSession->unsetUniqId();
        $uniqid = $objSiteSession->getUniqId();
        $arrOrderTemp['del_flg'] = 0;
        $this->saveOrderTemp($uniqid, $arrOrderTemp, $objCustomer);
        $this->verifyChangeCart($uniqid, $objCartSession);
        $objSiteSession->setRegistFlag();

        if (!$in_transaction) {
            $objQuery->commit();
        }
        return $uniqid;
    }

    /**
     * カートに変化が無いか検証する.
     *
     * ユニークIDとセッションのユニークIDを比較し, 異なる場合は
     * エラー画面を表示する.
     *
     * カートが空の場合, 購入ボタン押下後にカートが変更された場合は
     * カート画面へ遷移する.
     *
     * @param string $uniqId ユニークID
     * @param SC_CartSession $objCartSession
     * @return void
     */
    function verifyChangeCart($uniqId, &$objCartSession) {
        $cartKeys = $objCartSession->getKeys();

        // カート内が空でないか
        if (SC_Utils_Ex::isBlank($cartKeys)) {
            SC_Response_Ex::sendRedirect(CART_URLPATH);
            exit;
        }

        foreach ($cartKeys as $cartKey) {
            // 初回のみカートの内容を保存
            $objCartSession->saveCurrentCart($uniqId, $cartKey);

            /*
             * POSTのユニークIDとセッションのユニークIDを比較
             *(ユニークIDがPOSTされていない場合はスルー)
             */
            if(!SC_SiteSession_Ex::checkUniqId()) {
                SC_Utils_Ex::sfDispSiteError(CANCEL_PURCHASE);
                exit;
            }

            // 購入ボタンを押してから変化がないか
            $quantity = $objCartSession->getTotalQuantity($cartKey);
            if($objCartSession->checkChangeCart($cartKey) || !($quantity > 0)) {
                SC_Response_Ex::sendRedirect(CART_URLPATH);
                exit;
            }
        }
    }

    /**
     * 受注一時情報を取得する.
     *
     * @param integer $uniqId 受注一時情報ID
     * @return array 受注一時情報の配列
     */
    function getOrderTemp($uniqId) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->getRow("*", "dtb_order_temp", "order_temp_id = ?",
                                 array($uniqId));
    }

    /**
     * 受注IDをキーにして受注一時情報を取得する.
     *
     * @param integer $order_id 受注ID
     * @return array 受注一時情報の配列
     */
    function getOrderTempByOrderId($order_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        return $objQuery->getRow("*", "dtb_order_temp", "order_id = ?",
                                 array($order_id));
    }

    /**
     * 受注一時情報を保存する.
     *
     * 既存のデータが存在しない場合は新規保存. 存在する場合は更新する.
     * 既存のデータが存在せず, ユーザーがログインしている場合は,
     * 会員情報をコピーする.
     *
     * @param integer $uniqId 受注一時情報ID
     * @param array $params 登録する受注情報の配列
     * @param SC_Customer $objCustomer SC_Customer インスタンス
     * @return array void
     */
    function saveOrderTemp($uniqId, $params, &$objCustomer) {
        if (SC_Utils_Ex::isBlank($uniqId)) {
            return;
        }
        $params['device_type_id'] = SC_Display::detectDevice();
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // 存在するカラムのみを対象とする
        $cols = $objQuery->listTableFields('dtb_order_temp');
        foreach ($params as $key => $val) {
            if (in_array($key, $cols)) {
                $sqlval[$key] = $val;
            }
        }

        $sqlval['session'] = serialize($_SESSION);
        $exists = $this->getOrderTemp($uniqId);
        $this->copyFromCustomer($sqlval, $objCustomer);
        if (SC_Utils_Ex::isBlank($exists)) {
            $sqlval['order_temp_id'] = $uniqId;
            $sqlval['create_date'] = "now()";
            $objQuery->insert("dtb_order_temp", $sqlval);
        } else {
            $objQuery->update("dtb_order_temp", $sqlval, 'order_temp_id = ?',
                              array($uniqId));
        }
    }

    /**
     * 配送情報をセッションから取得する.
     *
     * @param bool $has_shipment_item 配送商品を保有している配送先のみ返す。
     */
    function getShippingTemp($has_shipment_item) {
        if ($has_shipment_item) {
            $arrReturn = array();
            foreach ($_SESSION['shipping'] as $key => $arrVal) {
                if (count($arrVal['shipment_item']) == 0) continue;
                $arrReturn[$key] = $arrVal;
            }
            return $arrReturn;
        }

        return $_SESSION['shipping'];
    }

    /**
     * 配送商品をクリア(消去)する
     *
     * @param integer $shipping_id 配送先ID
     * @return void
     */
    function clearShipmentItemTemp($shipping_id = null) {
        if (is_null($shipping_id)) {
            unset($_SESSION['shipping']);
        } else {
            unset($_SESSION['shipping'][$shipping_id]);
        }
    }

    /**
     * 配送商品を設定する.
     *
     * @param integer $shipping_id 配送先ID
     * @param integer $product_class_id 商品規格ID
     * @param integer $quantity 数量
     * @return void
     */
    function setShipmentItemTemp($shipping_id, $product_class_id, $quantity) {
        $objCustomer = new SC_Customer_Ex();

        // 配列が長くなるので, リファレンスを使用する
        $arrItems =& $_SESSION['shipping'][$shipping_id]['shipment_item'][$product_class_id];

        $arrItems['shipping_id'] = $shipping_id;
        $arrItems['product_class_id'] = $product_class_id;
        $arrItems['quantity'] = $quantity;

        $objProduct = new SC_Product_Ex();

        // カート情報から読みこめば済むと思うが、一旦保留。むしろ、カート情報も含め、セッション情報を縮小すべきかもしれない。
        /*
        $objCartSession = new SC_CartSession_Ex();
        $cartKey = $objCartSession->getKey();
        // 詳細情報を取得
        $cartItems = $objCartSession->getCartList($cartKey);
        */

        if (empty($arrItems['productsClass'])) {
            $product =& $objProduct->getDetailAndProductsClass($product_class_id);
            $arrItems['productsClass'] = $product;
        }
        // 顧客区分取得
        $customer_kbn = $objCustomer->getValue('customer_kbn');

        // 社員
        if ($customer_kbn == CUSTOMER_KBN_EMPLOYEE) {
            $arrItems['price'] = $arrItems['productsClass']['price02'];
        // 社員以外
        } else {
            $arrItems['price'] = $arrItems['productsClass']['price01'];
        }
        $inctax = SC_Helper_DB_Ex::sfCalcIncTax($arrItems['price']);
        $arrItems['total_inctax'] = $inctax * $arrItems['quantity'];
    }

    /**
     * 配送先都道府県の配列を返す.
     */
    function getShippingPref($is_multiple) {
        $results = array();
        foreach (SC_Helper_Purchase_Ex::getShippingTemp($is_multiple) as $val) {
            $results[] = $val['shipping_pref'];
        }
        return $results;
    }

    /**
     * 複数配送指定の購入かどうか.
     *
     * @return boolean 複数配送指定の購入の場合 true
     */
    function isMultiple() {
        return count(SC_Helper_Purchase_Ex::getShippingTemp(true)) >= 1;
    }

    /**
     * 配送情報をセッションに保存する.
     *
     * @param array $arrSrc 配送情報の連想配列
     * @param integer $shipping_id 配送先ID
     * @return void
     */
    function saveShippingTemp($arrSrc, $shipping_id = 0) {
        // 配送商品は引き継がない
        unset($arrSrc['shipment_item']);

        if (empty($_SESSION['shipping'][$shipping_id])) {
            $_SESSION['shipping'][$shipping_id] = $arrSrc;
            $_SESSION['shipping'][$shipping_id]['shipping_id'] = $shipping_id;
        } else {
            $_SESSION['shipping'][$shipping_id] = array_merge($_SESSION['shipping'][$shipping_id], $arrSrc);
            $_SESSION['shipping'][$shipping_id]['shipping_id'] = $shipping_id;
        }
    }

    /**
     * セッションの配送情報を破棄する.
     */
    function unsetShippingTemp() {
        unset($_SESSION['shipping']);
        unset($_SESSION['multiple_temp']);
    }

    /**
     * 会員情報を受注情報にコピーする.
     *
     * ユーザーがログインしていない場合は何もしない.
     * 会員情報を $dest の order_* へコピーする.
     * customer_id は強制的にコピーされる.
     *
     * @param array $dest コピー先の配列
     * @param SC_Customer $objCustomer SC_Customer インスタンス
     * @param string $prefix コピー先の接頭辞. デフォルト order
     * @param array $keys コピー対象のキー
     * @return void
     */
    function copyFromCustomer(&$dest, &$objCustomer, $prefix = 'order',
                              $keys = array('name', 'kana',
                                            'sex', 'zip01', 'zip02', 'zip', 'pref',
                                            'addr01', 'addr02', 'addr_kana',
                                            'tel',
                                            'birth', 'email')) {
        if ($objCustomer->isLoginSuccess(true)) {

            foreach ($keys as $key) {
                if (in_array($key, $keys)) {
                    $dest[$prefix . '_' . $key] = $objCustomer->getValue($key);
                }
            }

            $dest['customer_id'] = $objCustomer->getValue('customer_id');
            $dest['update_date'] = 'Now()';
        }
    }

    /**
     * 受注情報を配送情報にコピーする.
     *
     * 受注情報($src)を $dest の order_* へコピーする.
     *
     * TODO 汎用的にして SC_Utils へ移動
     *
     * @param array $dest コピー先の配列
     * @param array $src コピー元の配列
     * @param array $keys コピー対象のキー
     * @param string $prefix コピー先の接頭辞. デフォルト shipping
     * @param string $src_prefix コピー元の接頭辞. デフォルト order
     * @return void
     */
    function copyFromOrder(&$dest, $src,
                           $prefix = 'shipping', $src_prefix = 'order',
                           $keys = array('name', 'kana',
                                         'sex', 'zip01', 'zip02', 'zip', 'pref',
                                         'addr01', 'addr02', 'addr_kana',
                                         'tel')) {
        if (!SC_Utils_Ex::isBlank($prefix)) {
            $prefix = $prefix . '_';
        }
        if (!SC_Utils_Ex::isBlank($src_prefix)) {
            $src_prefix = $src_prefix . '_';
        }
        foreach ($keys as $key) {
            if (in_array($key, $keys)) {
                $dest[$prefix . $key] = $src[$src_prefix . $key];
            }
        }
    }

    /**
     * 購入金額に応じた支払方法を取得する.
     *
     * @param integer $total 購入金額
     * @param integer $deliv_id 配送業者ID
     * @return array 購入金額に応じた支払方法の配列
     */
    function getPaymentsByPrice($total, $deliv_id) {

        $arrPaymentIds = $this->getPayments($deliv_id);
        if (SC_Utils_Ex::isBlank($arrPaymentIds)) {
            return array();
        }

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $objCustomer = new SC_Customer_Ex();

        // 削除されていない支払方法を取得
        $where = 'del_flg = 0 AND payment_id IN (' . implode(', ', array_pad(array(), count($arrPaymentIds), '?')) . ')';
        $objQuery->setOrder("rank DESC");
        $payments = $objQuery->select("payment_id, payment_method, rule, upper_rule, note, payment_image, charge", "dtb_payment", $where, $arrPaymentIds);
        foreach ($payments as $data) {

            // 支払方法が初回購入除外支払方法の場合
            //if (intVal($data['payment_id']) == INIT_EXCLUSION_PAYMENT_ID) {
            //    // ログインしていない場合
            //    if (!$objCustomer->isLoginSuccess(true)) {
            //        continue;
            //    }
            //    // 購入回数取得
            //    $buyTimes = $objCustomer->getValue('buy_times');
            //    // 購入回数が0の場合
            //    if (is_null($buyTimes) || $buyTimes <= 0) {
            //        continue;
            //    }
            //}

            // 振込の場合
            if (intVal($data['payment_id']) == PAYMENT_ID_FURIKOMI) {
                // 初回購入なら
                if ($objCustomer->checkFirstTimePurchase()) {
                    // 上限4999円に設定
                    $data['upper_rule'] = UPPER_RULE_FURIKOMI_FIRST_TIME;
                }
            }

            // 下限と上限が設定されている
            if (strlen($data['rule']) != 0 && strlen($data['upper_rule']) != 0) {
                if ($data['rule'] <= $total && $data['upper_rule'] >= $total) {
                    $arrPayment[] = $data;
                }
            }
            // 下限のみ設定されている
            elseif (strlen($data['rule']) != 0) {
                if($data['rule'] <= $total) {
                    $arrPayment[] = $data;
                }
            }
            // 上限のみ設定されている
            elseif (strlen($data['upper_rule']) != 0) {
                if($data['upper_rule'] >= $total) {
                    $arrPayment[] = $data;
                }
            }
            // いずれも設定なし
            else {
                $arrPayment[] = $data;
            }
          }
        return $arrPayment;
    }

    /**
     * お届け日一覧を取得する.
     */
    function getDelivDate(&$objCartSess, $productTypeId) {
        $cartList = $objCartSess->getCartList($productTypeId);
        $delivDateIds = array();

	$chkRegularFlg = false;
        foreach ($cartList as $item) {
            $delivDateIds[] = $item['productsClass']['deliv_date_id'];
	    // 定期有無確認
            if ($item['regular_flg'] == REGULAR_PURCHASE_FLG_ON) {
		$chkRegularFlg = true;
	    }
        }

        //発送目安
        $id = max($delivDateIds);


        $objQuery =& SC_Query_Ex::getSingletonInstance();

	$start_day = "";
	if ($id) {
	    $sql =<<<__EOS
select can_be_delivered from mtb_delivery_date where id = {$id};
__EOS;
	    $start_day = $objQuery->getOne($sql);
	}

        //お届け可能日のスタート値から、お届け日の配列を取得する
	$arrDelivDate = $this->getDateArray($start_day, DELIV_DATE_END_MAX
					    , $chkRegularFlg);

        return $arrDelivDate;
    }

    /**
     * お届け可能日のスタート値から, お届け日の配列を取得する.
     */
    function getDateArray($start_day, $end_day, $regularFlg = false) {
        $masterData = new SC_DB_MasterData();
        $arrWDAY = $masterData->getMasterData("mtb_wday");
	$arrRegularPattern = explode(",", REGULAR_DELIV_PATTERN);

        //お届け可能日のスタート値がセットされていれば
        if ($start_day >= 1) {
            $now_time = time();
            $max_day = $start_day + $end_day;
            // 集計
            for ($i = $start_day; $i < $max_day; $i++) {
                // 基本時間から日数を追加していく
                $tmp_time = $now_time + ($i * 24 * 3600);
                list($y, $m, $d, $w) = explode(" ", date("Y m d w", $tmp_time));
		if (array_search($d, $arrRegularPattern) === false) {
		    continue;
		}
                $val = sprintf("%04d/%02d/%02d(%s)", $y, $m, $d, $arrWDAY[$w]);
                $arrDate[$val] = $val;
            }
        } else {
            $arrDate = false;
        }
        return $arrDate;
    }

    /**
     * 配送業者IDからお届け時間の配列を取得する.
     *
     * @param integer $deliv_id 配送業者ID
     * @return array お届け時間の配列
     */
    function getDelivTime($deliv_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<EOF
SELECT
    time_id,
    deliv_time
FROM
    dtb_delivtime
WHERE
    deliv_id = '$deliv_id'
ORDER BY time_id
EOF;
        $results = $objQuery->getAll($sql);

        $arrDelivTime = array();

        foreach ($results as $val) {
            $arrDelivTime[$val['time_id']] = $val['deliv_time'];
        }

        return $arrDelivTime;
    }

    /**
     * 商品種別ID から配送業者を取得する.
     *
     * @param integer $product_type_id 商品種別ID
     * @return array 配送業者の配列
     */
    function getDeliv($product_type_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<EOF
SELECT
    *
FROM
    dtb_deliv
WHERE
    product_type_id = '{$product_type_id}'
    AND del_flg = 0
ORDER BY rank DESC
EOF;

        return $objQuery->getAll($sql);
    }

    /**
     * 配送業者ID から, 有効な支払方法IDを取得する.
     *
     * @param integer $deliv_id 配送業者ID
     * @return array 有効な支払方法IDの配列
     */
    function getPayments($deliv_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objQuery->setOrder('rank');
        return $objQuery->getCol('payment_id', 'dtb_payment_options',
                                 'deliv_id = ?',
                                 array($deliv_id), MDB2_FETCHMODE_ORDERED);
    }

    /**
     * 配送情報の登録を行う.
     *
     * $arrParam のうち, dtb_shipping テーブルに存在するカラムのみを登録する.
     *
     * TODO UPDATE/INSERT にする
     *
     * @param integer $order_id 受注ID
     * @param array $arrParams 配送情報の連想配列
     * @param boolean $convert_shipping_date yyyy/mm/dd(EEE) 形式の配送日付を変換する場合 true
     * @param array $arrAddShip 配送情報の追加項目連想配列
     * @return void
     */
    function registerShipping($order_id, $arrParams, $convert_shipping_date = true, $arrAddShip = array()) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $table = 'dtb_shipping';
        $where = 'order_id = ?';
        $objQuery->delete($table, $where, array($order_id));

        foreach ($arrParams as $key => $arrShipping) {

            $arrValues = $objQuery->extractOnlyColsOf($table, $arrShipping);

            // 配送日付を timestamp に変換
            if (!SC_Utils_Ex::isBlank($arrValues['shipping_date'])
                && $convert_shipping_date) {
                $d = mb_strcut($arrValues["shipping_date"], 0, 10);
                $arrDate = explode("/", $d);
                $ts = mktime(0, 0, 0, $arrDate[1], $arrDate[2], $arrDate[0]);
                $arrValues['shipping_date'] = date("Y-m-d", $ts);
            }

            // 非会員購入の場合は shipping_id が存在しない
            if (!isset($arrValues['shipping_id'])) {
                $arrValues['shipping_id'] = $key;
            }
            $arrValues['order_id'] = $order_id;
            $arrValues['create_date'] = 'Now()';
            $arrValues['update_date'] = 'Now()';
			// 追加項目対応
            $arrValues['shipping_area_code'] = $arrAddShip['shipping_area_code'];
            $arrValues['deliv_kbn'] = $arrAddShip['deliv_kbn'];
            $arrValues['cool_kbn'] = $arrAddShip['cool_kbn'];
            $objQuery->insert($table, $arrValues);
        }
    }

    /**
     * 配送商品を登録する.
     *
     * @param integer $order_id 受注ID
     * @param integer $shipping_id 配送先ID
     * @param array $arrParams 配送商品の配列
     * @return void
     */
    function registerShipmentItem($order_id, $shipping_id, $arrParams) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $table = 'dtb_shipment_item';
        $where = 'order_id = ? AND shipping_id = ?';
        $objQuery->delete($table, $where, array($order_id, $shipping_id));

        $objProduct = new SC_Product_Ex();
        foreach ($arrParams as $arrValues) {
            if (SC_Utils_Ex::isBlank($arrValues['product_class_id'])) {
                continue;
            }
            $d = $objProduct->getDetailAndProductsClass($arrValues['product_class_id']);
            $name = SC_Utils_Ex::isBlank($arrValues['product_name'])
                ? $d['name']
                : $arrValues['product_name'];

            $code = SC_Utils_Ex::isBlank($arrValues['product_code'])
                ? $d['product_code']
                : $arrValues['product_code'];

            $cname1 = SC_Utils_Ex::isBlank($arrValues['classcategory_name1'])
                ? $d['classcategory_name1']
                : $arrValues['classcategory_name1'];

            $cname2 = SC_Utils_Ex::isBlank($arrValues['classcategory_name2'])
                ? $d['classcategory_name2']
                : $arrValues['classcategory_name2'];

            $price = SC_Utils_Ex::isBlank($arrValues['price'])
                ? $d['price']
                : $arrValues['price'];

            $arrValues['order_id'] = $order_id;
            $arrValues['shipping_id'] = $shipping_id;
            $arrValues['product_name'] = $name;
            $arrValues['product_code'] = $code;
            $arrValues['classcategory_name1'] = $cname1;
            $arrValues['classcategory_name2'] = $cname2;
            $arrValues['price'] = $price;

            $arrExtractValues = $objQuery->extractOnlyColsOf($table, $arrValues);
            $objQuery->insert($table, $arrExtractValues);
        }
    }

    /**
     * 受注登録を完了する.
     *
     * 引数の受注情報を受注テーブル及び受注詳細テーブルに登録する.
     * 登録後, 受注一時テーブルに削除フラグを立てる.
     *
     * @param array $orderParams 登録する受注情報の配列
     * @param SC_CartSession $objCartSession カート情報のインスタンス
     * @param integer $cartKey 登録を行うカート情報のキー
     * @param array $arrShip 配送情報に設定する内容
     * @param integer 受注ID
     */
    function registerOrderComplete($orderParams, &$objCartSession, $cartKey, &$arrShip) {
        $objCustomer = new SC_Customer_Ex();
        $customer_kbn = $objCustomer->getValue('customer_kbn');
        $objQuery =& SC_Query_Ex::getSingletonInstance();


		// 商品購入プレゼントがセットされている場合
		if (PRESENT_PRODUCT_CLASS_ID) {
			// 商品購入プレゼント商品を配列で取得
			$this->getPresentProduct($arrPresentBase, $arrPresentProduct);
		}

        // 不要な変数を unset
        $unsets = array('mailmaga_flg', 'deliv_check', 'point_check', 'password',
                        'reminder', 'reminder_answer', 'mail_flag', 'session');
        foreach ($unsets as $unset) {
            unset($orderParams[$unset]);
        }

        // 注文ステータスの指定が無い場合は新規受付
        if(SC_Utils_Ex::isBlank($orderParams['status'])) {
            $orderParams['status'] = ORDER_NEW;
        }

        // 社員
        if ($customer_kbn == CUSTOMER_KBN_EMPLOYEE) {
            // アンケート登録しない
            $orderParams['event_code'] = null;
        // 社員以外
        } else {
            if (isset($_SESSION["CAMPAIGN_CODE"])) {
            	$orderParams['campaign_cd'] = $_SESSION["CAMPAIGN_CODE"];
                // キャンペーンコード入力済みの場合は広告媒体コードをセット
		$sql =<<<EOF
SELECT
media_code
FROM dtb_planning
WHERE del_flg = 0
AND campaign_code = '{$_SESSION["CAMPAIGN_CODE"]}'
AND (DATE_FORMAT(start_date, '%Y%m%d') <= DATE_FORMAT(now(), '%Y%m%d')
OR start_date IS NULL)
AND (DATE_FORMAT(end_date, '%Y%m%d') >= DATE_FORMAT(now(), '%Y%m%d')
OR end_date IS NULL)
EOF;
		$orderParams['event_code'] = $objQuery->getOne($sql);
            }
        }
        $orderParams['create_date'] = 'Now()';
        $orderParams['update_date'] = 'Now()';

        // 詳細情報を取得
        $cartItems = $objCartSession->getCartList($cartKey);

        // 定期受注関連のデータをセット
        $this->setRegularOrder($orderParams, $objCartSession, $cartKey);

        $this->registerOrder($orderParams['order_id'], $orderParams);

        // 詳細情報を生成
        $objProduct = new SC_Product_Ex();
        $i = 0;
		$arrShip = array();
        foreach ($cartItems as $item) {
            $p =& $item['productsClass'];
            $arrDetail[$i]['order_id'] = $orderParams['order_id'];
            $arrDetail[$i]['product_id'] = $p['product_id'];
            $arrDetail[$i]['product_class_id'] = $p['product_class_id'];
            $arrDetail[$i]['product_name'] = $p['name'];
            $arrDetail[$i]['product_code'] = $p['product_code'];
            $arrDetail[$i]['classcategory_name1'] = $p['classcategory_name1'];
            $arrDetail[$i]['classcategory_name2'] = $p['classcategory_name2'];
            $arrDetail[$i]['point_rate'] = $item['point_rate'];
            $arrDetail[$i]['price'] = $item['price'];
            $arrDetail[$i]['quantity'] = $item['quantity'];
            $arrDetail[$i]['sell_flg'] = 1;
			$arrShip["shipping_area_code"] = $p["drop_shipment"];
			$arrShip["deliv_kbn"] = $p["deliv_kbn1"];
			$arrShip["cool_kbn"] = $p["deliv_kbn2"];

            // 定期受注明細関連のデータをセット
            $this->setRegularOrderDetail($arrDetail[$i], $item);

            // 在庫の減少処理
            if (!$objProduct->reduceStock($p['product_class_id'], $item['quantity'])) {
                $objQuery->rollback();
                SC_Utils_Ex::sfDispSiteError(SOLD_OUT, "", true);
			} else {
				// 購入プレゼントがある場合
				if (isset($arrPresentBase[0])) {
					// 対象商品か確認
					if (($key = array_search($p['product_class_id'], $arrPresentBase)) !== false) {
						for ($j = 0; $j < count($arrPresentProduct[$key]); $j++) {
							$i++;
							// 受注詳細情報追加
							$arrDetail[$i] = $this->getProductDetail($arrPresentProduct[$key][$j]);
							// 注文数に応じてプレゼント数を連動させる
							$arrDetail[$i]['quantity'] = $item['quantity'];
						}
					}
				}
			}
            $i++;
        }
		// 同梱品がある場合、受注明細にセット
		if (isset($_SESSION["INCLUDE_PROMOTION"]) 
			&& is_array($_SESSION["INCLUDE_PROMOTION"])) {
			for ($iCnt = 0; $iCnt < count($_SESSION["INCLUDE_PROMOTION"]); $iCnt++) {
				// 商品規格ID取得
				$includeId = $this->getProductClassId($_SESSION["INCLUDE_PROMOTION"][$iCnt]["product_cd"]);

				// 受注詳細情報追加
				$arrDetail[$i] = $this->getProductDetail($includeId);
				$arrDetail[$i]["quantity"] = $_SESSION["INCLUDE_PROMOTION"][$iCnt]["quantity"];
				$i++;
			}
		}

		// 受注明細登録
        $this->registerOrderDetail($orderParams['order_id'], $arrDetail);

        $objQuery->update("dtb_order_temp", array('del_flg' => 1),
                          "order_temp_id = ?",
                          array(SC_SiteSession_Ex::getUniqId()));

		// プロモーション適用時コード登録
		if (is_array($_SESSION["ORDER_PROMOTION_CD"])) {
			foreach ($_SESSION["ORDER_PROMOTION_CD"] as $key => $val) {
				$arrOrderPromotion = array();
				$arrOrderPromotion["order_id"] = $orderParams["order_id"];
				$arrOrderPromotion["promotion_cd"] = $val;
				// 受注適用プロモーション情報登録
				$objQuery->insert("dtb_order_promotion", $arrOrderPromotion);
			}
		}
        return $orderParams['order_id'];
    }

    /**
     * 受注情報を登録する.
     *
     * 既に受注IDが存在する場合は, 受注情報を更新する.
     * 引数の受注IDが, 空白又は null の場合は, 新しく受注IDを発行して登録する.
     *
     * @param integer $order_id 受注ID
     * @param array $arrParams 受注情報の連想配列
     * @return integer 受注ID
     */
    function registerOrder($order_id, $arrParams) {

        $table = 'dtb_order';
        $where = 'order_id = ?';
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $arrValues = $objQuery->extractOnlyColsOf($table, $arrParams);

        $exists = $objQuery->count($table, $where, array($order_id));
        if ($exists > 0) {

            $this->sfUpdateOrderStatus($order_id, $arrValues['status'],
                                       $arrValues['add_point'],
                                       $arrValues['use_point'],
                                       $arrValues);
            $this->sfUpdateOrderNameCol($order_id);

            $arrValues['update_date'] = 'now()';
            $objQuery->update($table, $arrValues, $where, array($order_id));

            // ▼定期情報登録
            // セッションに定期情報無い、または新規購入時以外はスキップ
            if (!isset($_SESSION['regular']) ||
                $arrValues['status'] != ORDER_NEW) {
                return $order_id;
            }

            //$objQuery->begin;

            // 届け日をセットするため処理追加
            $sql =<<<EOF
SELECT date_format(shipping_date, "%d")
FROM dtb_shipping
WHERE order_id = {$order_id}
EOF;
            $todokeDay = $objQuery->getOne($sql);

            $arrRegular = $_SESSION['regular'];
            $objRegular = new SC_Helper_Regular_Ex();
            $objRegular->registerRegularOrder($arrRegular['regular_id'], 
                                        $arrRegular['order'],
                                        $arrRegular['shipping']);
            $line_no = 1;
            foreach($arrRegular['detail'] as $detail) {
                if ($detail['regular_flg'] == REGULAR_PURCHASE_FLG_OFF) {
                    continue;
                }
                // 届け日をセット
                $detail['todoke_day'] = intval($todokeDay);
                $objRegular->registerRegularOrderDetail
                    ($arrRegular['regular_id'],
                     $line_no,
                     $detail
                );
                $line_no++;
            }
            //$objQuery->commit();
            unset($_SESSION['regular']);
            // ▲定期情報登録

        } else {
            if (SC_Utils_Ex::isBlank($order_id)) {
                $order_id = $objQuery->nextVal('dtb_order_order_id');
            }
            /*
             * 新規受付の場合は受注ステータス null で insert し,
             * sfUpdateOrderStatus で ORDER_NEW に変更する.
             */
            $status = $arrValues['status'];
            $arrValues['status'] = null;
            $arrValues['order_id'] = $order_id;
            $arrValues['customer_id'] =
                    SC_Utils_Ex::isBlank($arrValues['customer_id'])
                    ? 0 : $arrValues['customer_id'];
            $arrValues['create_date'] = 'now()';
            $arrValues['update_date'] = 'now()';
            $objQuery->insert($table, $arrValues);

            $this->sfUpdateOrderStatus($order_id, $status,
                                       $arrValues['add_point'],
                                       $arrValues['use_point'],
                                       $arrValues);
            $this->sfUpdateOrderNameCol($order_id);

        }
        return $order_id;
    }

    /**
     * 受注詳細情報を登録する.
     *
     * 既に, 該当の受注が存在する場合は, 受注情報を削除し, 登録する.
     *
     * @param integer $order_id 受注ID
     * @param array $arrParams 受注情報の連想配列
     * @return void
     */
    function registerOrderDetail($order_id, $arrParams) {
        $table = 'dtb_order_detail';
        $where = 'order_id = ?';
        $objQuery = SC_Query_Ex::getSingletonInstance();

        $objQuery->delete($table, $where, array($order_id));
        foreach ($arrParams as $arrDetail) {
            $arrValues = $objQuery->extractOnlyColsOf($table, $arrDetail);
            $arrValues['order_detail_id'] = $objQuery->nextVal('dtb_order_detail_order_detail_id');
            $arrValues['order_id'] = $order_id;
            $objQuery->insert($table, $arrValues);
        }
    }

    /**
     * 受注情報を取得する.
     *
     * @param integer $order_id    受注ID
     * @param integer $customer_id 顧客ID
     * @return array  $arrOrder    受注情報の配列
     */
    function getOrder($order_id, $customer_id = null) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $arrOrder = array ();

        if (!SC_Utils_Ex::isBlank($customer_id)) {
            $where = ' AND o.customer_id = ' . $customer_id;
        } else {
            $where = '';
        }

        $sql =<<<EOF
SELECT
    o.*
   ,c.customer_cd
FROM
    dtb_order o
INNER JOIN
    dtb_customer c
    ON c.customer_id = o.customer_id
WHERE
    o.order_id = $order_id
    $where
EOF;
        $arrOrder = $objQuery->getAll($sql);

        return $arrOrder[0];
    }

    /**
     * 受注詳細を取得する.
     *
     * @param integer $order_id 受注ID
     * @param boolean $has_order_status 受注ステータス, 入金日も含める場合 true
     * @return array 受注詳細の配列
     */
    function getOrderDetail($order_id, $has_order_status = true) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        if ($has_order_status) {
            $payment_date 
                = 'T1.status AS status, T1.payment_date AS payment_date,';
        }

        $sql =<<<EOF
SELECT
    T3.product_id,
    T3.product_class_id as product_class_id,
    T3.product_type_id AS product_type_id,
    T2.product_code,
    T2.product_name,
    T2.classcategory_name1 AS classcategory_name1,
    T2.classcategory_name2 AS classcategory_name2,
    T2.price,
    T2.quantity,
    T2.course_cd,
    T2.point_rate,
    T1.status AS status,
    $payment_date
    CASE
        WHEN EXISTS(
            SELECT * FROM dtb_products
                WHERE
                    product_id = T3.product_id
                    AND del_flg = 0
                    AND status = 1
            )
        THEN '1' ELSE '0'
        END AS enable,
    NULL AS effective
FROM
    dtb_order T1
        JOIN dtb_order_detail T2 ON T1.order_id = T2.order_id
        JOIN dtb_products_class T3 ON T2.product_class_id = T3.product_class_id
WHERE
    T1.order_id = $order_id
ORDER BY T2.order_detail_id
EOF;

        return $objQuery->getAll($sql);
    }

    /**
     * 受注詳細を取得する.（外部結合版）
     *
     * @param integer $order_id 受注ID
     * @param boolean $has_order_status 受注ステータス, 入金日も含める場合 true
     * @return array 受注詳細の配列
     */
    function getOrderDetailOuter($order_id, $has_order_status = true) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        if ($has_order_status) {
            $payment_date 
                = 'T1.status AS status, T1.payment_date AS payment_date,';
        }

        $sql =<<<EOF
SELECT
    T3.product_id,
    T3.product_class_id as product_class_id,
    T3.product_type_id AS product_type_id,
    T2.product_code,
    T2.product_name,
    T2.classcategory_name1 AS classcategory_name1,
    T2.classcategory_name2 AS classcategory_name2,
    T2.price,
    T2.quantity,
    T2.point_rate,
    T2.course_cd,
    T1.status AS status,
    T4.main_list_image AS main_list_image,
    T4.main_image AS main_image,
    T4.main_large_image AS main_large_image,
    $payment_date
    CASE
        WHEN EXISTS(
            SELECT * FROM dtb_products
                WHERE
                    product_id = T3.product_id
                    AND del_flg = 0
                    AND status = 1
            )
        THEN '1' ELSE '0'
        END AS enable,
    NULL AS effective
FROM
    dtb_order T1
         INNER JOIN dtb_order_detail T2 ON T1.order_id = T2.order_id
    LEFT OUTER JOIN dtb_products_class T3 ON T2.product_class_id = T3.product_class_id
    LEFT OUTER JOIN dtb_products T4 ON T3.product_id = T4.product_id
WHERE
    T1.order_id = $order_id
AND
    T2.sell_flg = 1
ORDER BY T2.order_detail_id
EOF;

        return $objQuery->getAll($sql);
    }

    /**
     * 当月の購入情報を取得する.
     *
     * @param integer $customer_id 顧客ID
     * @return array  受注情報の配列
     */
    function getOrderThisMonth($customer_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // 受注ステータス取得
        $ORDER_CANCEL  = ORDER_CANCEL;  // キャンセル
        $ORDER_PENDING = ORDER_PENDING; // 決済処理中
	$from = date('Y-m-01') . " 00:00:00";
	$to = date('Y-m-t') . " 23:59:59";
        $sql =<<<__EOS
    SELECT SUM(OD.quantity) AS count
         , PC.employee_sale_cd
      FROM dtb_order O
INNER JOIN dtb_order_detail OD
        ON O.order_id = OD.order_id
INNER JOIN dtb_products PC
        ON OD.product_id = PC.product_id
     WHERE O.customer_id = {$customer_id}
       AND O.status NOT IN({$ORDER_CANCEL}, {$ORDER_PENDING})
       AND O.create_date between '{$from}' and '{$to}'
       AND O.del_flg = 0
  GROUP BY PC.employee_sale_cd
__EOS;
        return $objQuery->getAll($sql);
    }

    /**
     * 過去3年の購入回数を取得する.
     *
     * @param  integer $customer_id 顧客ID
     * @return integer $arrOrder[0]['count'] 購入回数
     */
    function getOrderCountLastThreeYears($customer_id = null) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // 受注ステータス取得
        $ORDER_CANCEL  = ORDER_CANCEL;  // キャンセル
        $ORDER_PENDING = ORDER_PENDING; // 決済処理中
	$yearsago3 = date('Y-m-d 00:00:00', strtotime("-3 year"));

        if (!SC_Utils_Ex::isBlank($customer_id)) {
            $where = 'AND customer_id = ' . $customer_id;
        } else {
            $where = '';
        }

/*
        $sql =<<<EOF
SELECT COUNT(*) AS count 
  FROM dtb_order
 WHERE DATE_FORMAT(create_date, '%Y-%m-%d')
       >= DATE_FORMAT(NOW() + INTERVAL -3 YEAR, '%Y-%m-%d') 
   AND status NOT IN({$ORDER_CANCEL}, {$ORDER_PENDING})
   {$where}
EOF;
*/

        $sql =<<<EOF
select count(*) as count 
  from dtb_order
 where create_date >= '{$yearsago3}'
   and status not in({$ORDER_CANCEL}, {$ORDER_PENDING})
   and del_flg = 0
   {$where}
EOF;
        $arrOrderCount = $objQuery->getOne($sql);

        return intval($arrOrderCount['count']);
    }

    /**
     * ダウンロード可能フラグを, 受注詳細に設定する.
     *
     * ダウンロード可能と判断されるのは, 以下の通り.
     *
     * 1. ダウンロード可能期限が期限内かつ, 入金日が入力されている
     * 2. 販売価格が 0 円である
     *
     * 受注詳細行には, is_downloadable という真偽値が設定される.
     * @param array 受注詳細の配列
     * @return void
     */
    function setDownloadableFlgTo(&$arrOrderDetail) {
        foreach (array_keys($arrOrderDetail) as $key) {
            // 販売価格が 0 円
            if ($arrOrderDetail[$key]['price'] == '0') {
                $arrOrderDetail[$key]['is_downloadable'] = true;
            }
            // ダウンロード期限内かつ, 入金日あり
            elseif ($arrOrderDetail[$key]['effective'] == '1'
                    && !SC_Utils_Ex::isBlank($arrOrderDetail[$key]['payment_date'])) {
                $arrOrderDetail[$key]['is_downloadable'] = true;
            } else {
                $arrOrderDetail[$key]['is_downloadable'] = false;
            }
        }
    }

    /**
     * 配送情報を取得する.
     *
     * @param integer $order_id 受注ID
     * @param boolean $has_items 結果に配送商品も含める場合 true
     * @return array 配送情報の配列
     */
    function getShippings($order_id, $has_items = true) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrResults = array();

        $sql =<<<EOF
SELECT
    *
FROM
    dtb_shipping
WHERE
    order_id = $order_id
ORDER BY shipping_id
EOF;
        $arrShippings = $objQuery->getAll($sql);

        // shipping_id ごとの配列を生成する
        foreach ($arrShippings as $shipping) {
            foreach ($shipping as $key => $val) {
                $arrResults[$shipping['shipping_id']][$key] = $val;
            }
        }

        if ($has_items) {
            $objProduct = new SC_Product_Ex();
            foreach (array_keys($arrResults) as $shipping_id) {
                $arrResults[$shipping_id]['shipment_item']
                        =& $this->getShipmentItems($order_id, $shipping_id);
            }
        }
        return $arrResults;
    }

    /**
     * 配送商品を取得する.
     *
     * @param integer $order_id 受注ID
     * @param integer $shipping_id 配送先ID
     * @param boolean $has_detail 商品詳細も取得する場合 true
     * @return array 商品規格IDをキーにした配送商品の配列
     */
    function getShipmentItems($order_id, $shipping_id, $has_detail = true) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objProduct = new SC_Product_Ex();
        $arrResults = array();
        $arrItems = $objQuery->select("*", "dtb_shipment_item",
                                      "order_id = ? AND shipping_id = ?",
                                      array($order_id, $shipping_id));

        foreach ($arrItems as $key => $arrItem) {
            $product_class_id = $arrItem['product_class_id'];

            foreach ($arrItem as $detailKey => $detailVal) {
                $arrResults[$key][$detailKey] = $detailVal;
            }
            // 商品詳細を関連づける
            if ($has_detail) {
                $arrResults[$key]['productsClass']
                    =& $objProduct->getDetailAndProductsClass($product_class_id);
            }
        }
        return $arrResults;
    }

    /**
     * 受注完了メールを送信する.
     *
     * HTTP_USER_AGENT の種別により, 携帯電話の場合は携帯用の文面,
     * PC の場合は PC 用の文面でメールを送信する.
     *
     * @param integer $orderId 受注ID
     * @return void
     */
    function sendOrderMail($orderId) {
        $mailHelper = new SC_Helper_Mail_Ex();
        $mailHelper->sfSendOrderMail($orderId,
                                     SC_MobileUserAgent_Ex::isMobile() ? 2 : 1);
    }

    /**
     * 受注.対応状況の更新
     *
     * 必ず呼び出し元でトランザクションブロックを開いておくこと。
     *
     * @param integer $orderId 注文番号
     * @param integer|null $newStatus 対応状況 (null=変更無し)
     * @param integer|null $newAddPoint 加算ポイント (null=変更無し)
     * @param integer|null $newUsePoint1 使用ポイント (null=変更無し)
     * @param array $sqlval 更新後の値をリファレンスさせるためのパラメーター
     * @return void
     */
    function sfUpdateOrderStatus($orderId, $newStatus = null, $newAddPoint = null, $newUsePoint1 = null, &$sqlval) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $arrOrderOld = $objQuery->getRow('status, add_point, use_point, customer_id', 'dtb_order', 'order_id = ?', array($orderId));

        // 対応状況が変更無しの場合、DB値を引き継ぐ
        if (is_null($newStatus)) {
            $newStatus = $arrOrderOld['status'];
        }

        // 使用ポイント、DB値を引き継ぐ
        if (is_null($newUsePoint1)) {
            $newUsePoint1 = $arrOrderOld['use_point'];
        }

        // 加算ポイント、DB値を引き継ぐ
        if (is_null($newAddPoint)) {
            $newAddPoint = $arrOrderOld['add_point'];
        }

        if (USE_POINT !== false) {
            // 会員.ポイントの加減値
            $addCustomerPoint1 = 0;

            // ▼使用ポイント
            // 変更前の対応状況が利用対象の場合、変更前の使用ポイント分を戻す
            if ($this->isUsePoint($arrOrderOld['status'])) {
                $addCustomerPoint1 += $arrOrderOld['use_point'];
            }

            // 変更後の対応状況が利用対象の場合、変更後の使用ポイント分を引く
            if ($this->isUsePoint($newStatus)) {
                $addCustomerPoint1 -= intval($newUsePoint1);
            }

            // ▲使用ポイント

            // ▼加算ポイント
            // 変更前の対応状況が加算対象の場合、変更前の加算ポイント分を戻す
            if ($this->isAddPoint($arrOrderOld['status'])) {
                $addCustomerPoint1 -= $arrOrderOld['add_point'];
            }

            // 変更後の対応状況が加算対象の場合、変更後の加算ポイント分を足す
            if ($this->isAddPoint($newStatus)) {
                $addCustomerPoint1 += $newAddPoint;
            }
            // ▲加算ポイント

            if ($addCustomerPoint1 != 0) {
		$arrUpdate = array();
		$arrUpdate["update_date"] = "Now()";
		if (isset($_SESSION["customer"]["customer_id"])
		    && !SC_Utils_Ex::isBlank($_SESSION["customer"]["customer_id"])) {
		    $arrUpdate["updator_id"] = $_SESSION["customer"]["customer_id"];
		} else if (isset($_SESSION["member_id"]) 
		    && !SC_Utils_Ex::isBlank($_SESSION["member_id"])) {
		    $arrUpdate["updator_id"] = $_SESSION["member_id"];
		}

                // ▼会員テーブルの更新
                $objQuery->update('dtb_customer', $arrUpdate,
                                  'customer_id = ?', array($arrOrderOld['customer_id']),
                                  array('point' => 'point + ?'), array($addCustomerPoint1));
                // ▲会員テーブルの更新

                // 会員.ポイントをマイナスした場合、
                if ($addCustomerPoint1 < 0) {
                    $sql = 'SELECT point FROM dtb_customer WHERE customer_id = ?';
                    $point = $objQuery->getOne($sql, array($arrOrderOld['customer_id']));
                    // 変更後の会員.ポイントがマイナスの場合、
                    if ($point < 0) {
                        // ロールバック
                        $objQuery->rollback();
                        // エラー
                        SC_Utils_Ex::sfDispSiteError(LACK_POINT);
                    }
                }
            }
        }

        // ▼受注テーブルの更新
        if (empty($sqlval)) {
            $sqlval = array();
        }

        if (USE_POINT !== false) {
            $sqlval['add_point'] = $newAddPoint;
            $sqlval['use_point'] = $newUsePoint1;
        }
        // ステータスが発送済みに変更の場合、発送日を更新
        if ($arrOrderOld['status'] != ORDER_DELIV && $newStatus == ORDER_DELIV) {
            $sqlval['commit_date'] = 'Now()';
        }
        // ステータスが入金済みに変更の場合、入金日を更新
        elseif ($arrOrderOld['status'] != ORDER_PRE_END && $newStatus == ORDER_PRE_END) {
            $sqlval['payment_date'] = 'Now()';
        }

        $sqlval['status'] = $newStatus;
        $sqlval['update_date'] = 'Now()';

        $dest = $objQuery->extractOnlyColsOf('dtb_order', $sqlval);
        $objQuery->update('dtb_order', $dest, 'order_id = ?', array($orderId));
        // ▲受注テーブルの更新

        //会員情報の最終購入日、購入合計を更新
        if($arrOrderOld['customer_id'] > 0 and $arrOrderOld['status'] != $newStatus){
            SC_Customer_Ex::updateOrderSummary($arrOrderOld['customer_id']);
        }
    }

    /**
     * 受注の名称列を更新する
     *
     * @param integer $order_id 更新対象の注文番号
     * @param boolean $temp_table 更新対象は「受注_Temp」か
     * @static
     */
    function sfUpdateOrderNameCol($order_id, $temp_table = false) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        if ($temp_table) {
            $tgt_table = 'dtb_order_temp';
            $sql_where = 'order_temp_id = ?';
        } else {
            $tgt_table = 'dtb_order';
            $sql_where = 'order_id = ?';

            $objQuery->update('dtb_shipping', array(),
                              $sql_where,
                              array($order_id),
                              array('shipping_time' =>
                                    "(SELECT deliv_time FROM dtb_delivtime WHERE time_id = dtb_shipping.time_id AND deliv_id = dtb_shipping.deliv_id)"));

        }

        $objQuery->update($tgt_table, array(),
                          $sql_where,
                          array($order_id),
                          array('payment_method' =>
                                "(SELECT payment_method FROM dtb_payment WHERE payment_id = " . $tgt_table . ".payment_id)"));
    }

    /**
     * ポイント使用するかの判定
     *
     * $status が null の場合は false を返す.
     *
     * @param integer $status 対応状況
     * @return boolean 使用するか(会員テーブルから減算するか)
     */
    function isUsePoint($status) {
        if ($status == null) {
            return false;
        }
        switch ($status) {
            case ORDER_CANCEL:      // キャンセル
                return false;
            default:
                break;
        }

        return true;
    }

    /**
     * ポイント加算するかの判定
     *
     * @param integer $status 対応状況
     * @return boolean 加算するか
     */
    function isAddPoint($status) {
        switch ($status) {
            case ORDER_NEW:         // 新規注文
            case ORDER_PAY_WAIT:    // 入金待ち
            case ORDER_PRE_END:     // 入金済み
            case ORDER_CANCEL:      // キャンセル
            case ORDER_BACK_ORDER:  // 取り寄せ中
                return false;

            case ORDER_DELIV:       // 発送済み
                return true;

            default:
                break;
        }

        return false;
    }

    /**
     * セッションに保持している情報を破棄する.
     *
     * 通常、受注処理(completeOrder)完了後に呼び出され、
     * セッション情報を破棄する.
     *
     * 決済モジュール画面から確認画面に「戻る」場合を考慮し、
     * セッション情報を破棄しないカスタマイズを、モジュール側で
     * 加える機会を与える.
     *
     * @param integer $orderId 注文番号
     * @param SC_CartSession $objCartSession カート情報のインスタンス
     * @param SC_Customer $objCustomer SC_Customer インスタンス
     * @param integer $cartKey 登録を行うカート情報のキー
     */
    function cleanupSession($orderId, &$objCartSession, &$objCustomer, $cartKey) {
        // カートの内容を削除する.
        $objCartSession->delAllProducts($cartKey);
        SC_SiteSession_Ex::unsetUniqId();

        // セッションの配送情報を破棄する.
        $this->unsetShippingTemp();
        $objCustomer->updateSession();
		// キャンペーン、プローモーション用情報破棄
		unset($_SESSION["CAMPAIGN_CODE"]);
		unset($_SESSION["ORDER_PROMOTION_CD"]);
		unset($_SESSION["INCLUDE_PROMOTION"]);
		unset($_SESSION["USE_POINT"]);
		// 新規顧客用情報破棄
		unset($_SESSION["new_customer"]);
		unset($_SESSION["new_customer_id"]);
		unset($_SESSION["new_secret_key"]);
		unset($_SESSION["new_other_deliv_id"]);
    }

    /**
     * 購入プレゼント定義を配列にセットする
     *
     *
     * @param array   $arrProduct 対象商品情報
     * @param array   $arrPresent プレゼント商品情報
	 *
     */
    function getPresentProduct(&$arrProduct, &$arrPresent) {

		$arrProduct = array();
		$arrPresent = array();

		// 定義が設定されている場合
		if (PRESENT_PRODUCT_CLASS_ID) {
			// 商品単位にセット
			$arrSet = explode(";", PRESENT_PRODUCT_CLASS_ID);

			// 商品単位のIDを対象とプレゼントに分ける
			for ($i = 0; $i < count($arrSet); $i++) {
				$arrChk = array();
				// 対象商品を抽出
				$arrChk = explode(":", $arrSet[$i]);
				$arrProduct[$i] = $arrChk[0];

				// プレゼント商品を抽出
				$arrPresent[$i] = explode(",", $arrChk[1]);
			}
		}
    }

    /**
     * 商品マスタ明細情報取得
     *  1.商品規格IDから商品明細情報を取得
     *
     * @param String $productClassId 商品規格ID
     * @return array $arrProduct     商品明細情報
     *
     */
    function getProductDetail($productClassId){
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sql =<<<EOF
SELECT
    T1.product_id AS product_id
   ,T1.name AS product_name
   ,T1.main_list_image AS main_list_image
   ,T2.product_class_id AS product_class_id
   ,T2.product_code AS product_code
   ,T4.name AS classcategory_name1
   ,0 AS price
   ,1 AS quantity
FROM
    dtb_products           T1
    LEFT JOIN dtb_products_class T2
        ON T1.product_id = T2.product_id
    LEFT JOIN dtb_class_combination T3
        ON T2.class_combination_id = T3.class_combination_id
    LEFT JOIN dtb_classcategory     T4
        ON T3.classcategory_id     = T4.classcategory_id
WHERE 
    T2.product_class_id     = '$productClassId'
ORDER BY T4.rank DESC
EOF;

        // 商品明細情報取得
        $arrProduct = $objQuery->getAll($sql);
        
        return $arrProduct[0];
    }

    /**
     * 商品規格ID取得
     *  1.商品コードから商品規格IDを取得
     *
     * @param String $productCode    商品規格コード
     * @return array $arrProduct     商品規格ID
     *
     */
    function getProductClassId($productCode){
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sql =<<<EOF
SELECT
   product_class_id
FROM
    dtb_products_class
WHERE 
    product_code     = '$productCode'
EOF;

        // 商品明細情報取得
        $arrProduct = $objQuery->getAll($sql);

        return $arrProduct[0]["product_class_id"];
    }

    /**
     * 社員購入グループ情報を取得.
     *
     * @param integer $customer_id 顧客ID
     * @return array  受注情報の配列
     */
    function getEmployeeSale() {

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrOrder = array ();

        $sql =<<<EOF
SELECT *
  FROM dtb_employee_sale
 WHERE del_flg = 0 
EOF;
        return $objQuery->getAll($sql);
    }

    /**
     * 社員購入グループCDリストを取得.
     *
     * @param  none
     * @return array  受注情報の配列
     */
    function getEmployeeSaleCdList() {

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrOrder = array ();

        $sql =<<<EOF
SELECT *
  FROM dtb_employee_sale
 WHERE del_flg = 0 
EOF;
        $arrRecord = $objQuery->getAll($sql);

        foreach ($arrRecord as $arrVal) {
            $arrRes[] = $arrVal["employee_sale_cd"];
        }
        return $arrRes;
    }

    /**
     * 社員購入グループ名リストを取得.
     *
     * @param  none
     * @return array  受注情報の配列
     */
    function getEmployeeSaleNameList() {

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrOrder = array ();

        $sql =<<<EOF
SELECT *
  FROM dtb_employee_sale
 WHERE del_flg = 0 
EOF;
        $arrRecord = $objQuery->getAll($sql);

        foreach ($arrRecord as $arrVal) {
            $arrRes[$arrVal["employee_sale_cd"]] = $arrVal["employee_sale_name"];
        }
        return $arrRes;
    }

    /**
     * 受注データに定期関連のデータをセット
     *
     * @param array $arrDetail データ登録用の受注情報の連想配列
     * @param array $cartItems カート情報
     * @return void
     */
    function setRegularOrder(&$orderParams, &$objCartSession, $cartKey) {
        // 定期購入判定
        $objRegular = new SC_Helper_Regular_Ex();
        $regular_purchase_flg =
            $objRegular->checkRegularPurchase
                ($objCartSession->getAllCartList(), $cartKey);

        if ($regular_purchase_flg === true) {
            // 基幹定期受注NO(コース受注NO)
            $orderParams['regular_base_no'] = null;
        } else {
            $orderParams['regular_base_no'] = 0;
        }
    }
    /**
     * 受注明細データに定期関連のデータをセット
     *
     * @param array $arrDetail データ登録用の受注明細の連想配列
     * @param array $item カート内の商品情報の連想配列(1商品分)
     * @return void
     */
    function setRegularOrderDetail(&$arrDetail, $item) {

        // コースCDのセット
        if (isset($item['course_cd'])) {
            // 定期購入
            $arrDetail['course_cd'] = $item['course_cd'];
        } else {
            // 単回購入
            $arrDetail['course_cd'] = 0;
        }
    }

    /**
     * 企画情報を取得
     *
     * @param  integer $type 1:キャンペーン
     *                       2:アンケート用
     * @return array   $arrRecord 企画情報配列
     */
    function getPlanningData($type = -1) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // 初期値定義
        $where = ""; 
        $arrRecord = array(); 

        // キャンペーン
        if ($type == PLANNING_TYPE_CAMPAIGN) {
            $where = " AND planning_type = ". $type;
        // アンケート
        } elseif ($type == PLANNING_TYPE_ENQUETE) {
            $where = " AND planning_type = ". $type;
        }
        // SQL生成
        $sql = <<<EOS

  SELECT *
    FROM dtb_planning
   WHERE ((start_date <= DATE(NOW()) AND end_date IS NULL)
      OR (start_date IS NULL AND end_date >= DATE(NOW()))
      OR (start_date <= DATE(NOW()) AND end_date >= DATE(NOW()))
      OR (start_date IS NULL AND end_date IS NULL))
  $where
     AND del_flg = 0
ORDER BY rank ASC

EOS;
        // SQL実行
        $arrRecord = $objQuery->getAll($sql);
        return $arrRecord;
    }

    /**
     * イノス配送業者IDをWEB配送業者IDに変換する
     *
     * @param integer $deliv_id イノス配送業者ID
     * @param integer $box_id
     * @return WEB配送業者ID
     */
    function convertDelivIdToWeb($deliv_id, $box_id) {

        // メール便の場合
        if ($box_id == DELIV_BOX_ID_MAIL) {
            // ヤマトメール便
            return DELIV_ID_YAMATO_MAIL;
        }

        // ヤマトで宅配便
        else if ($deliv_id == INOS_DELIV_ID_YAMATO &&
            $box_id == DELIV_BOX_ID_TAKUHAI) {
            // ヤマト宅配便
            return DELIV_ID_YAMATO;
        }

        // 佐川で宅配便
        else if ($deliv_id == INOS_DELIV_ID_SAGAWA &&
            $box_id == DELIV_BOX_ID_TAKUHAI) {
            // 佐川宅配便
            return DELIV_ID_SAGAWA;
        }
        return;
    }

    /**
     * 配送時間名称を取得する.
     *
     * @param int $deliv_id 配送業者ID
     * @param int $time_id  配送時間ID
     * @pamra 配送時間名称
     */
    function getDelivTimeName($deliv_id = "", $time_id = "") {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        if (empty($deliv_id) || empty($time_id) || $time_id == '0') {
            return "";
        }

        $sql =<<<__EOF
SELECT
    deliv_time
FROM
    dtb_delivtime
WHERE
    deliv_id = '{$deliv_id}'
AND time_id = '{$time_id}'
__EOF;
        $deliv_time = $objQuery->getOne($sql);

        return $deliv_time;
    }

    /**
     * カード決済完了後の定期情報登録のため
     * 定期購入情報をセッションに保存する
     *
     * @param array $arrShipping 受注情報の連想配列
     * @param array $arrShipping 配送情報の連想配列
     * @param  SC_CartSession $objCartSession カート情報のインスタンス
     * @param  integer $cartKey 登録を行うカート情報のキー
     * @param array $arrAddShip 配送情報の追加項目連想配列
     * @return void
     */
    function saveRegularTemp($arrOrder, $arrShipping,
        &$objCartSession, $cartKey, $arrAddShip) {

        if (isset($_SESSION['regular'])) {
            unset($_SESSION['regular']);
        }

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objRegular = new SC_Helper_Regular_Ex();

        // 定期購入か判定
        if ($objRegular->checkRegularPurchase
            ($objCartSession->getAllCartList(), $cartKey) === false) {
            return;
        }

        // 定期受注IDを採番
        $arrRegular['regular_id'] =
            $objQuery->nextVal('dtb_regular_order_regular_id');

        // 受注情報
        $arrRegular['order'] = $arrOrder;
        // 配送先情報
        $arrShipping["shipment_cd"] = $arrAddShip["shipping_area_code"];
        $arrRegular['shipping'] = $arrShipping;

        // 受注明細情報を取得
        $cartItems = $objCartSession->getCartList($cartKey);
        $arrRegular['detail'] = $cartItems;

        // セッションへ保存
        $_SESSION['regular'] = $arrRegular;

    }

    /**
     * 同梱品がある場合、商品情報をセットする
     *
     * @param none
     * @return array arrProduct 同梱品商品情報
     */
    function getIncludeProducts() {

		// 同梱品がある場合、受注明細にセット
		if (isset($_SESSION["INCLUDE_PROMOTION"]) 
			&& is_array($_SESSION["INCLUDE_PROMOTION"])) {
			$arrProduct = array();
			for ($iCnt = 0; $iCnt < count($_SESSION["INCLUDE_PROMOTION"]); $iCnt++) {
				// 商品規格ID取得
				$includeId = $this->getProductClassId($_SESSION["INCLUDE_PROMOTION"][$iCnt]["product_cd"]);

				// 受注詳細情報追加
				$arrProduct[$iCnt] = $this->getProductDetail($includeId);
				$arrProduct[$iCnt]["quantity"] = $_SESSION["INCLUDE_PROMOTION"][$iCnt]["quantity"];
			}
			return $arrProduct;
		}
		return false;
	}

}
