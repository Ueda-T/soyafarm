<?php
/**
 * カートセッション管理クラス
 *
 * @author LOCKON CO.,LTD.
 * @version $Id: SC_CartSession.php 91 2012-04-11 04:39:04Z hira $
 */
class SC_CartSession {
    /** ユニークIDを指定する. */
    var $key_tmp;

    /** カートのセッション変数. */
    var $cartSession;

    /** 割引対象外. */
    var $noDiscountProductClass;

    /* コンストラクタ */
    function SC_CartSession($cartKey = 'cart') {
        if (!isset($_SESSION[$cartKey])) {
            $_SESSION[$cartKey] = array();
        }
        $this->cartSession =& $_SESSION[$cartKey];

		// 割引対象外商品を配列にセット
		$this->noDiscountProductClass = explode(",", DISCOUNT_NO_PRODUCT_CLASS_ID);
    }

    // 商品購入処理中のロック
    function saveCurrentCart($key_tmp, $productTypeId) {
        $this->key_tmp = "savecart_" . $key_tmp;
        // すでに情報がなければ現状のカート情報を記録しておく
        if(count($_SESSION[$this->key_tmp]) == 0) {
            $_SESSION[$this->key_tmp] = $this->cartSession[$productTypeId];
        }
        // 1世代古いコピー情報は、削除しておく
        foreach($_SESSION as $k => $val) {
            if($k != $this->key_tmp && preg_match("/^savecart_/", $k)) {
                unset($this->cartSession[$productTypeId][$k]);
            }
        }
    }

    // 商品購入中の変更があったかをチェックする。
    function getCancelPurchase($productTypeId) {
        $ret = isset($this->cartSession[$productTypeId]['cancel_purchase'])
            ? $this->cartSession[$productTypeId]['cancel_purchase'] : "";
        $this->cartSession[$productTypeId]['cancel_purchase'] = false;
        return $ret;
    }

    // 購入処理中に商品に変更がなかったかを判定
    function checkChangeCart($productTypeId) {
        $change = false;
        $max = $this->getMax($productTypeId);
        for($i = 1; $i <= $max; $i++) {
            if ($this->cartSession[$productTypeId][$i]['quantity']
                != $_SESSION[$this->key_tmp][$i]['quantity']) {

                $change = true;
                break;
            }
            if ($this->cartSession[$productTypeId][$i]['id']
                != $_SESSION[$this->key_tmp][$i]['id']) {

                $change = true;
                break;
            }
        }
        if ($change) {
            // 一時カートのクリア
            unset($_SESSION[$this->key_tmp]);
            $this->cartSession[$productTypeId][$key]['cancel_purchase'] = true;
        } else {
            $this->cartSession[$productTypeId]['cancel_purchase'] = false;
        }
        return $this->cartSession[$productTypeId]['cancel_purchase'];
    }

    // 次に割り当てるカートのIDを取得する
    function getNextCartID($productTypeId) {
        foreach($this->cartSession[$productTypeId] as $key => $val){
            $arrRet[] = $this->cartSession[$productTypeId][$key]['cart_no'];
        }
        return max($arrRet) + 1;
    }

    /**
     * 商品ごとの合計価格
     * XXX 実際には、「商品」ではなく、「カートの明細行(≒商品規格)」のような気がします。
     *
     * @param integer $id
     * @return string 商品ごとの合計価格(税込み)
     * @deprecated SC_CartSession::getCartList() を使用してください
     */
    function getProductTotal($id, $productTypeId) {
        $max = $this->getMax($productTypeId);
        for($i = 0; $i <= $max; $i++) {
            if(isset($this->cartSession[$productTypeId][$i]['id'])
               && $this->cartSession[$productTypeId][$i]['id'] == $id) {

                // 税込み合計
                $price = $this->cartSession[$productTypeId][$i]['price'];
                $quantity = $this->cartSession[$productTypeId][$i]['quantity'];
                $incTax = SC_Helper_DB_Ex::sfCalcIncTax($price);
                $total = $incTax * $quantity;
                return $total;
            }
        }
        return 0;
    }

    // 値のセット
    function setProductValue($id, $key, $val, $productTypeId) {
        $max = $this->getMax($productTypeId);
        for($i = 0; $i <= $max; $i++) {
            if(isset($this->cartSession[$productTypeId][$i]['id'])
               && $this->cartSession[$productTypeId][$i]['id'] == $id) {
                $this->cartSession[$productTypeId][$i][$key] = $val;
            }
        }
    }

    // カート内商品の最大要素番号を取得する。
    function getMax($productTypeId) {
        $max = 0;
        if (count($this->cartSession[$productTypeId]) > 0){
            foreach($this->cartSession[$productTypeId] as $key => $val) {
                if (is_numeric($key)) {
                    if($max < $key) {
                        $max = $key;
                    }
                }
            }
        }
        return $max;
    }

    // カート内商品数量の合計
    function getTotalQuantity($productTypeId) {
        $total = 0;
        $max = $this->getMax($productTypeId);
        for($i = 0; $i <= $max; $i++) {
            $total+= $this->cartSession[$productTypeId][$i]['quantity'];
        }
        return $total;
    }

    // 全商品の合計価格
    function getAllProductsTotal($productTypeId) {
        // 税込み合計
        $total = 0;
        $max = $this->getMax($productTypeId);
        for($i = 0; $i <= $max; $i++) {

            if (!isset($this->cartSession[$productTypeId][$i]['price'])) {
                $this->cartSession[$productTypeId][$i]['price'] = "";
            }

            $price = $this->cartSession[$productTypeId][$i]['price'];

            if (!isset($this->cartSession[$productTypeId][$i]['quantity'])) {
                $this->cartSession[$productTypeId][$i]['quantity'] = "";
            }
            $quantity = $this->cartSession[$productTypeId][$i]['quantity'];

            $incTax = SC_Helper_DB_Ex::sfCalcIncTax($price);
            $total+= ($incTax * $quantity);
        }
        return $total;
    }

    // 全商品の合計税金
    function getAllProductsTax($productTypeId) {
        // 税合計
        $total = 0;
        $max = $this->getMax($productTypeId);
        for($i = 0; $i <= $max; $i++) {
            $price = $this->cartSession[$productTypeId][$i]['price'];
            $quantity = $this->cartSession[$productTypeId][$i]['quantity'];
            $tax = SC_Helper_DB_Ex::sfTax($price);
            $total+= ($tax * $quantity);
        }
        return $total;
    }

    // 全商品の合計ポイント
    function getAllProductsPoint($productTypeId) {
        // ポイント合計
        $total = 0;
        if (USE_POINT !== false) {
            $max = $this->getMax($productTypeId);
            for($i = 0; $i <= $max; $i++) {
                $price = $this->cartSession[$productTypeId][$i]['price'];
                $quantity = $this->cartSession[$productTypeId][$i]['quantity'];

                if (!isset($this->cartSession[$productTypeId][$i]['point_rate'])) {
                    $this->cartSession[$productTypeId][$i]['point_rate'] = "";
                }
                $point_rate = $this->cartSession[$productTypeId][$i]['point_rate'];

                if (!isset($this->cartSession[$productTypeId][$i]['id'][0])) {
                    $this->cartSession[$productTypeId][$i]['id'][0] = "";
                }
                $point = SC_Utils_Ex::sfPrePoint($price, $point_rate);
                $total+= ($point * $quantity);
            }
        }
        return $total;
    }

    // 全商品の割引合計価格
    function getAllProductsDiscountTotal($productTypeId, $point = 0) {
        // 税込み合計
        $total = 0;
		$discount = 0;
		$noDiscount = 0;

		return $point;

		// 割引期間を過ぎていれば終了
		/*
		if (DISCOUNT_END_DATE < date('Ymd')) {
			return $total;
		}
		 */
        $max = $this->getMax($productTypeId);
        for($i = 0; $i <= $max; $i++) {

			if (!isset($this->cartSession[$productTypeId][$i]['id'])) {
				continue;
			}
			// 割引対象外商品の場合、次の商品へ
			if (array_search($this->cartSession[$productTypeId][$i]['id']
							, $this->noDiscountProductClass) !== false) {
				$price = $this->cartSession[$productTypeId][$i]['price'];
				$quantity = $this->cartSession[$productTypeId][$i]['quantity'];
				$incTax = SC_Helper_DB_Ex::sfCalcIncTax($price);
				$noDiscount += ($incTax * $quantity);
				continue;
			}
            if (!isset($this->cartSession[$productTypeId][$i]['price'])) {
                $this->cartSession[$productTypeId][$i]['price'] = "";
            }

            $price = $this->cartSession[$productTypeId][$i]['price'];

            if (!isset($this->cartSession[$productTypeId][$i]['quantity'])) {
                $this->cartSession[$productTypeId][$i]['quantity'] = "";
            }
            $quantity = $this->cartSession[$productTypeId][$i]['quantity'];

            $incTax = SC_Helper_DB_Ex::sfCalcIncTax($price);
            $total+= ($incTax * $quantity);
        }

		// ポイント利用がある場合
		if ($point) {
			$chkDiscount = $noDiscount - $point * POINT_VALUE;
			if ($chkDiscount < 0) {
				$total += $chkDiscount;
			}
		}

		// 割引期間の間
		//if (DISCOUNT_END_DATE >= date('Ymd')) {
			if ($total >= DISCOUNT_PRICE_OVER1) {
				$discount = sprintf("%d", ($total * DISCOUNT_PRICE_RATE1) / 100);
			} else if ($total >= DISCOUNT_PRICE_OVER2) {
				$discount = sprintf("%d", ($total * DISCOUNT_PRICE_RATE2) / 100);
			} else if ($total >= DISCOUNT_PRICE_OVER3) {
				$discount = sprintf("%d", ($total * DISCOUNT_PRICE_RATE3) / 100);
			}
		//}

        return $discount;
    }

    /**
     * カートへの商品追加
     *
     * @param $product_class_id 商品規格ID
     * @param $quantity 数量
     * @param $regular_flg 定期購入フラグ
     */
    function addProduct($product_class_id, $quantity, $regular_flg = REGULAR_PURCHASE_FLG_OFF) {
        $objProduct = new SC_Product_Ex();
        $arrProduct = $objProduct->getProductsClass($product_class_id);
        $productTypeId = $arrProduct['product_type_id'];
        $find = false;
        if (SC_Utils_Ex::isBlank($regular_flg)) {
            $regular_flg = REGULAR_PURCHASE_FLG_OFF;
        }
        $max = $this->getMax($productTypeId);
        for($i = 0; $i <= $max; $i++) {

            // 2013.12.06
            if($this->cartSession[$productTypeId][$i]['id'] == $product_class_id &&
                $this->cartSession[$productTypeId][$i]['regular_flg'] == $regular_flg) {

                $val = $this->cartSession[$productTypeId][$i]['quantity'] + $quantity;
                if(strlen($val) <= INT_LEN) {
                    $this->cartSession[$productTypeId][$i]['quantity'] += $quantity;
                }
                $find = true;
            }
        }
        if(!$find) {
            $this->cartSession[$productTypeId][$max+1]['id'] = $product_class_id;
            $this->cartSession[$productTypeId][$max+1]['quantity'] = $quantity;
            $this->cartSession[$productTypeId][$max+1]['cart_no'] = $this->getNextCartID($productTypeId);
            // 2013.12.06 add 定期フラグ
            $this->cartSession[$productTypeId][$max+1]['regular_flg'] = $regular_flg;
        }
    }

    // 前頁のURLを記録しておく
    function setPrevURL($url, $excludePaths = array()) {
        // 前頁として記録しないページを指定する。
        $arrExclude = array(
            "/shopping/"
        );
        $arrExclude = array_merge($arrExclude, $excludePaths);
        $exclude = false;
        // ページチェックを行う。
        foreach($arrExclude as $val) {
            if(preg_match("|" . preg_quote($val) . "|", $url)) {
                $exclude = true;
                break;
            }
        }
        // 除外ページでない場合は、前頁として記録する。
        if(!$exclude) {
            $_SESSION['prev_url'] = $url;
        }
    }

    // 前頁のURLを取得する
    function getPrevURL() {
        return isset($_SESSION['prev_url']) ? $_SESSION['prev_url'] : "";
    }

    // キーが一致した商品の削除
    function delProductKey($keyname, $val, $productTypeId) {
        $max = count($this->cartSession[$productTypeId]);
        for($i = 0; $i < $max; $i++) {
            if($this->cartSession[$productTypeId][$i][$keyname] == $val) {
                unset($this->cartSession[$productTypeId][$i]);
            }
        }
    }

    function setValue($key, $val, $productTypeId) {
        $this->cartSession[$productTypeId][$key] = $val;
    }

    function getValue($key, $productTypeId) {
        return $this->cartSession[$productTypeId][$key];
    }

    /**
     * 商品種別ごとにカート内商品の一覧を取得する.
     *
     * @param integer $productTypeId 商品種別ID
     * @return array カート内商品一覧の配列
     */
    function getCartList($productTypeId) {
        $objProduct = new SC_Product_Ex();
        $objCustomer = new SC_Customer_Ex();
        $max = $this->getMax($productTypeId);
        $arrRet = array();
        // 定期商品用
        $arrRegular = array();

        for($i = 0; $i <= $max; $i++) {
            if(isset($this->cartSession[$productTypeId][$i]['cart_no'])
                && $this->cartSession[$productTypeId][$i]['cart_no'] != "") {

                // 商品情報は常に取得
                $this->cartSession[$productTypeId][$i]['productsClass'] =&
                        $objProduct->getDetailAndProductsClass(
                                    $this->cartSession[$productTypeId][$i]['id']);

                // 顧客区分を取得
                $customer_kbn = $objCustomer->getValue('customer_kbn');

                // 社員
                if ($customer_kbn == CUSTOMER_KBN_EMPLOYEE) {
                    $price = $this->cartSession[$productTypeId][$i]['productsClass']['price02'];
                // 社員以外
                } else {
                    $price = $this->cartSession[$productTypeId][$i]['productsClass']['price01'];
                }
                $this->cartSession[$productTypeId][$i]['price'] = $price;

                $this->cartSession[$productTypeId][$i]['point_rate'] =
                    $this->cartSession[$productTypeId][$i]['productsClass']['point_rate'];

                $quantity = $this->cartSession[$productTypeId][$i]['quantity'];
                $incTax = SC_Helper_DB_Ex::sfCalcIncTax($price);
                $total = $incTax * $quantity;

                $this->cartSession[$productTypeId][$i]['total_inctax'] = $total;

                // カート内の並び替えのため一旦、別配列へ格納
                if($this->cartSession[$productTypeId][$i]['regular_flg'] != REGULAR_PURCHASE_FLG_ON) {
                    $arrRet[] =& $this->cartSession[$productTypeId][$i];
                } else {
                    $arrRegular[] =& $this->cartSession[$productTypeId][$i];
                }
            }
            // 単回商品情報の後に定期商品情報を連結
            $arrResult = array();
            $arrResult = array_merge($arrRet, $arrRegular);
        }
		// プロモーション適用確認
		$this->isProductsPriceCampaign($productTypeId);

        return $arrResult;
    }

    /**
     * すべてのカートの内容を取得する.
     *
     * @return array すべてのカートの内容
     */
    function getAllCartList() {
        $results = array();
        $cartKeys = $this->getKeys();
        $i = 0;
        foreach ($cartKeys as $key) {
            $cartItems = $this->getCartList($key);
            foreach (array_keys($cartItems) as $itemKey) {
                $cartItem =& $cartItems[$itemKey];
                $results[$key][$i] =& $cartItem;
                $i++;
            }
        }
        return $results;
    }

    // カート内にある商品ＩＤを全て取得する
    /**
     * @deprected getAllProductClassID を使用して下さい
     */
    function getAllProductID($productTypeId) {
        $max = $this->getMax($productTypeId);
        for($i = 0; $i <= $max; $i++) {
            if($this->cartSession[$productTypeId][$i]['cart_no'] != "") {
                $arrRet[] = $this->cartSession[$productTypeId][$i]['id'][0];
            }
        }
        return $arrRet;
    }

    /**
     * カート内にある商品規格IDを全て取得する.
     *
     * @param integer $productTypeId 商品種別ID
     * @return array 商品規格ID の配列
     */
    function getAllProductClassID($productTypeId) {
        $max = $this->getMax($productTypeId);
        for($i = 0; $i <= $max; $i++) {
            if($this->cartSession[$productTypeId][$i]['cart_no'] != "") {
                $arrRet[] = $this->cartSession[$productTypeId][$i]['id'];
            }
        }
        return $arrRet;
    }

    /**
     * 商品種別ID を指定して, カート内の商品をすべて削除する.
     *
     * @param integer $productTypeId 商品種別ID
     * @return void
     */
    function delAllProducts($productTypeId) {
        $max = $this->getMax($productTypeId);
        for($i = 0; $i <= $max; $i++) {
            unset($this->cartSession[$productTypeId][$i]);
        }
    }

    // 商品の削除
    function delProduct($cart_no, $productTypeId) {
        $max = $this->getMax($productTypeId);
        for($i = 0; $i <= $max; $i++) {
            if($this->cartSession[$productTypeId][$i]['cart_no'] == $cart_no) {
                unset($this->cartSession[$productTypeId][$i]);
            }
        }
    }

    // 数量の増加
    function upQuantity($cart_no, $productTypeId) {
        $quantity = $this->getQuantity($cart_no, $productTypeId);
        if (strlen($quantity + 1) <= INT_LEN) {
            $this->setQuantity($quantity + 1, $cart_no, $productTypeId);
        }
    }

    // 数量の減少
    function downQuantity($cart_no, $productTypeId) {
        $quantity = $this->getQuantity($cart_no, $productTypeId);
        if ($quantity > 1) {
            $this->setQuantity($quantity - 1, $cart_no, $productTypeId);
        }
    }

    /**
     * カート番号と商品種別IDを指定して, 数量を取得する.
     *
     * @param integer $cart_no カート番号
     * @param integer $productTypeId 商品種別ID
     * @return integer 該当商品規格の数量
     */
    function getQuantity($cart_no, $productTypeId) {
        $max = $this->getMax($productTypeId);
        for ($i = 0; $i <= $max; $i++) {
            if ($this->cartSession[$productTypeId][$i]['cart_no'] == $cart_no) {
                return $this->cartSession[$productTypeId][$i]['quantity'];
            }
        }
    }

    /**
     * カート番号と商品種別IDを指定して, 数量を設定する.
     *
     * @param integer $quantity 設定する数量
     * @param integer $cart_no カート番号
     * @param integer $productTypeId 商品種別ID
     * @retrun void
     */
    function setQuantity($quantity, $cart_no, $productTypeId) {
        $max = $this->getMax($productTypeId);
        for ($i = 0; $i <= $max; $i++) {
            if ($this->cartSession[$productTypeId][$i]['cart_no'] == $cart_no) {
                $this->cartSession[$productTypeId][$i]['quantity'] = $quantity;
            }
        }
    }

    /**
     * カート番号と商品種別IDを指定して, 商品規格IDを取得する.
     *
     * @param integer $cart_no カート番号
     * @param integer $productTypeId 商品種別ID
     * @return integer 商品規格ID
     */
    function getProductClassId($cart_no, $productTypeId) {
        for ($i = 0; $i <= $max; $i++) {
            if ($this->cartSession[$productTypeId][$i]['cart_no'] == $cart_no) {
                return $this->cartSession[$productTypeId][$i]['id'];
            }
        }
    }

    /**
     * カート内の商品の妥当性をチェックする.
     *
     * エラーが発生した場合は, 商品をカート内から削除又は数量を調整し,
     * エラーメッセージを返す.
     *
     * 1. 商品種別に関連づけられた配送業者の存在チェック
     * 2. 削除/非表示商品のチェック
     * 3. 商品購入制限数のチェック
     * 4. 在庫数チェック
     *
     * @param string $key 商品種別ID
     * @return string エラーが発生した場合はエラーメッセージ
     */
    function checkProducts($productTypeId) {
        $objProduct = new SC_Product_Ex();
        $tpl_message = "";

        // カート内の情報を取得
        $items = $this->getCartList($productTypeId);
        foreach (array_keys($items) as $key) {
            $item =& $items[$key];
            $product =& $item['productsClass'];

            /*
             * 配送業者のチェック
             */
            $arrDeliv = SC_Helper_Purchase_Ex::getDeliv($productTypeId);
            if (SC_Utils_Ex::isBlank($arrDeliv)) {
                $tpl_message .= "※「" . $product['name'] . "」はまだ配送の準備ができておりません。恐れ入りますがお問い合わせページよりお問い合わせください。\n";
            }

            /*
             * 表示/非表示商品のチェック
             */
            if (SC_Utils_Ex::isBlank($product)) {
                $this->delProduct($item['cart_no'], $productTypeId);
                $tpl_message .= "※ 現時点で販売していない商品が含まれておりました。該当商品をカートから削除しました。\n";
            }

            /*
             * 商品購入制限数, 在庫数のチェック
             */
            $stock = $product['stock'];
            $stock_unlimited = $product['stock_unlimited'];
            $min = $objProduct->getBuyMinimumNumber($product);
            $limit = $objProduct->getBuyLimit($product);

            if ($item['quantity'] > $stock && $item['quantity'] >= $min && $stock_unlimited != '1') {
                    $this->setProductValue($item['id'], 'quantity', $limit, $productTypeId);
                    $this->setProductValue($item['id'], 'total_inctax', SC_Helper_DB_Ex::sfCalcIncTax($item['price']) * $limit, $productTypeId);
                    $tpl_message .= "※「" . $product['name'] . "」は在庫が不足しておりますので、{$limit}個以上の購入はできません。\n\n";
                    continue;
            }
            else if (!is_null($limit) && $item['quantity'] > $limit) {
                if ($limit > 0) {
                    $this->setProductValue($item['id'], 'quantity', $limit, $productTypeId);
                    $this->setProductValue($item['id'], 'total_inctax', SC_Helper_DB_Ex::sfCalcIncTax($item['price']) * $limit, $productTypeId);
                    $tpl_message .= "「" . $product['name'] . "」は{$limit}個までのご注文に限らせていただいています。\n";
                    $tpl_message .= "お一人様の購入数はこの範囲内でお願いします。\n";
                } else {
                    $this->delProduct($item['cart_no'], $productTypeId);
                    $tpl_message .= "※「" . $product['name'] . "」は売り切れました。\n";
                    continue;
                }
            }
            else if (!is_null($min) && $item['quantity'] < $min) {
                $this->setProductValue($item['id'], 'quantity', $min, $productTypeId);
                $this->setProductValue($item['id'], 'total_inctax', SC_Helper_DB_Ex::sfCalcIncTax($item['price']) * $min, $productTypeId);
                $tpl_message .= "「" . $product['name'] . "」は{$min}個以上からのご注文とさせていただきます。\n";
            }
        }
        return $tpl_message;
    }

    /**
     * 送料無料条件を満たすかどうかチェックする
     *
     * @param integer $productTypeId 商品種別ID
     * @return boolean 送料無料の場合 true
     */
    function isDelivFree($productTypeId) {
        $objDb = new SC_Helper_DB_Ex();
        $objCustomer = new SC_Customer_Ex();

		// 定期顧客の場合送料無料チェック
		if ($this->checkRegularCustomer($objCustomer->getValue('customer_id'))) {
			return true;
		}

        $subtotal = $this->getAllProductsTotal($productTypeId);
		// 割引金額取得
        $discount = $this->getAllProductsDiscountTotal($productTypeId);
		$subtotal -= $discount;

        // 送料無料の購入数が設定されている場合
        if (DELIV_FREE_AMOUNT > 0) {
            // 商品の合計数量
            $total_quantity = $this->getTotalQuantity($productTypeId);

            if($total_quantity >= DELIV_FREE_AMOUNT) {
                return true;
            }
        }

        // 送料無料条件が設定されている場合
        $arrInfo = $objDb->sfGetBasisData();

        // ログインユーザの顧客区分取得
        $customer_kbn = $objCustomer->getValue('customer_kbn');

        switch ($customer_kbn) {
        case CUSTOMER_KBN_NORMAL: //一般
            if ($arrInfo['free_rule'] > 0) {
                // 小計が無料条件を超えている場合
                if($subtotal >= $arrInfo['free_rule']) {
                    return true;
                }
            }
            break;
        case CUSTOMER_KBN_EMPLOYEE: //社員
            if ($arrInfo['free_rule2'] > 0) {
                // 小計が無料条件を超えている場合
                if($subtotal >= $arrInfo['free_rule2']) {
                    return true;
                }
            }
            break;
        default:
            if ($arrInfo['free_rule'] > 0) {
                // 小計が無料条件を超えている場合
                if($subtotal >= $arrInfo['free_rule']) {
                    return true;
                }
            }
            break;
        }

        // メール便の場合は送料無料
        if ($this->checkMailDelivery($productTypeId) === true) {
            return true;
        }

		// 社員以外はプロモーションチェック
		if ($customer_kbn != CUSTOMER_KBN_EMPLOYEE) {
			// 送料無料キャンペーンの場合は送料無料
			if ($this->isDelivFreeCampaign($productTypeId) === true) {
				return true;
			}
		}

        return false;
    }

    /**
     * プローモーション商品金額チェックする
     *
     * @param integer $productTypeId 商品種別ID
     * @return boolean プロモーション適用時 true
     */
	function isProductsPriceCampaign($productTypeId)
	{

        $objCustomer = new SC_Customer_Ex();

        // ログインユーザの顧客区分取得
        $customer_kbn = $objCustomer->getValue("customer_kbn");
		// ログインユーザID取得
        $customer_id = $objCustomer->getValue('customer_id');
		// 社員の場合は、プロモーション適用しない
		if ($customer_kbn == CUSTOMER_KBN_EMPLOYEE) {
			return false;
		}

		$arrData = array();
		$sqlProducts = "";
		$arrCnt = array();

		// 商品最大数を取得
		$productsCnt = $this->getMax($productTypeId);
		for ($i = 0; $i <= $productsCnt; $i++) {
			// カート商品確認
			if (isset($this->cartSession[$productTypeId][$i]["cart_no"])
				&& $this->cartSession[$productTypeId][$i]["cart_no"] != "") {

				// 定期購入期間
				if (isset($this->cartSession[$productTypeId][$i]["course_cd"])) {
					$courseCd = sprintf("%d"
						, $this->cartSession[$productTypeId][$i]["course_cd"]);
				} else {
					$courseCd = 0;
				}
				// お届間隔にて配列生成
				if (!isset($arrCnt[$courseCd])) {
					$arrCnt[$courseCd] = 0;
				}
				$dataCnt = $arrCnt[$courseCd];

				// KEY情報
				$arrData[$courseCd][$dataCnt]["key"] = $i;
				// 商品コード
				$arrData[$courseCd][$dataCnt]["product_code"] = 
					$this->cartSession[$productTypeId][$i]["productsClass"]["product_code"];
				// 購入数量
				$arrData[$courseCd][$dataCnt]["quantity"] = 
					$this->cartSession[$productTypeId][$i]["quantity"];
				// 購入金額
				$arrData[$courseCd][$dataCnt]["base_price"] = 
					$this->cartSession[$productTypeId][$i]["price"];
				$arrData[$courseCd][$dataCnt]["price"] = 
					$this->cartSession[$productTypeId][$i]["total_inctax"];
				// 単品定期区分
				$arrData[$courseCd][$dataCnt]["regular_flg"] = 
					$this->cartSession[$productTypeId][$i]["regular_flg"];
				// SQL用商品コード編集
				if ($arrData[$courseCd][$dataCnt]["product_code"]) {
					if ($sqlProducts) {
						$sqlProducts .= ",";
					}
					// 商品コードSQLように加工
					$sqlProducts .= sprintf("'%s'"
								, $arrData[$courseCd][$dataCnt]["product_code"]);
				}
				$arrCnt[$courseCd]++;
			}
		}

		// 商品がない場合終了
		if (!$sqlProducts) {
			return false;
		}
		// 定期情報編集時
		if ($this->getKey() == CART_REGULAR_KEY) {
			// 商品金額を確認
			$arrDelivData = $this->getProductsDiscountPromotionRegular
														($sqlProducts);
			// キャンペンコード指定定期の場合
			if ($_SESSION["REGULAR_CAMPAIGN_CODE"]) {
				$arrCPDelivData = $this->getDiscountPromotionCampaignRegular
														($sqlProducts);
				if (isset($arrDelivData[0]["promotion_cd"])) {
					if (isset($arrCPDelivData[0]["promotion_cd"])) {
						for ($i = 0; $i < count($arrCPDelivData); $i++) {
							$arrDelivData[] = $arrCPDelivData[$i];
						}
					}
				} else {
					if (isset($arrCPDelivData[0]["promotion_cd"])) {
						$arrDelivData = $arrCPDelivData;
					}
				}
			}

		// 通常カート
		} else {
			// 商品金額を確認
			$arrDelivData = $this->getProductsDiscountPromotion($sqlProducts);
			// キャンペーンコード入力時は入力用も取得
			if ($_SESSION["CAMPAIGN_CODE"]) {
				$arrCPDelivData = $this->getDiscountPromotionCampaign($sqlProducts);
				if (isset($arrDelivData[0]["promotion_cd"])) {
					if (isset($arrCPDelivData[0]["promotion_cd"])) {
						for ($i = 0; $i < count($arrCPDelivData); $i++) {
							$arrDelivData[] = $arrCPDelivData[$i];
						}
					}
				} else {
					if (isset($arrCPDelivData[0]["promotion_cd"])) {
						$arrDelivData = $arrCPDelivData;
					}
				}
			}
		}

		// 割引対象がない場合設定なし
		if (!isset($arrDelivData[0]["promotion_cd"])) {
			// 顧客別割引確認
			$this->checkCustomerDiscount($customer_id, $productTypeId);
			return false;
		}

		// 定期情報編集時
		if ($this->getKey() == CART_REGULAR_KEY) {
			// 割引対象チェック
			$res = $this->checkDiscountCampaignRegular($productTypeId, $arrDelivData, $arrData);
		} else {
			$res = $this->checkDiscountCampaign($productTypeId, $arrDelivData, $arrData);
		}
		// 顧客別割引確認
		$this->checkCustomerDiscount($customer_id, $productTypeId);
		return $res;
	}

    /**
     * 商品単価が顧客割引率が安いか判定する
     *
     * @param integer $customerId 顧客ID
     * @param integer $productTypeId 商品種別ID
     * @return none
     */
    function checkCustomerDiscount($customerId, $productTypeId) {

		$objQuery =& SC_Query_Ex::getSingletonInstance();

		$sql =<<<EOF
SELECT 
    t.cut_rate as cut_rate,
    c.customer_type_cd as customer_type_cd
FROM 
	dtb_customer c
INNER JOIN dtb_customer_type t
ON c.customer_type_cd = t.customer_type_cd
AND t.del_flg = 0
WHERE 
    c.del_flg = 0
AND 
    c.customer_id = ?
EOF;

		$rate = DEFAULT_CUSTOMER_TYPE_CD;
		// 顧客IDがある時割引率を顧客マスタより取得
		if ($customerId) {
			// 割引率取得
			$arrRate = $objQuery->getAll($sql, array($customerId));
			$rate = $arrRate[0]["cut_rate"];
			$code = $arrRate[0]["customer_type_cd"];
		}

		// 割引率がない場合、今回購入内容から割引率取得
		if (!$rate || $rate == DEFAULT_CUSTOMER_TYPE_CD) {
			$quantity = 0;
			$course_cd = "";
			foreach ($this->cartSession[$productTypeId] as $key => $item) {
				// 定期の場合チェックする
				if ($item["regular_flg"] == REGULAR_PURCHASE_FLG_ON) {
					$quantity += $item["quantity"];
					$course_cd = $item["course_cd"];
				}
			}
			// 定期購入がある場合、割引率取得
			if ($quantity) {
				$sql =<<<EOF
SELECT cut_rate,
	customer_type_cd
FROM dtb_customer_type
WHERE course_cd = ?
AND quantity_from <= ?
AND quantity_to >= ?
EOF;
				// 割引率取得
				$arrRate = $objQuery->getAll($sql
									, array($course_cd, $quantity, $quantity));
				$rate = $arrRate[0]["cut_rate"];
				$code = $arrRate[0]["customer_type_cd"];
			}
		}

		if ($rate) {
			foreach ($this->cartSession[$productTypeId] as $key => $item) {
				$price = $item["productsClass"]["price01"];
				$discount = $item["price"];
				$quantity = $item["quantity"];
				$chkPrice = $price - ceil($price * $rate / 100);
				if (intval($discount) > intval($chkPrice)) {
					$this->cartSession[$productTypeId][$key]["price"] = $chkPrice;
					$this->cartSession[$productTypeId][$key]["total_inctax"] = 
						$chkPrice * $quantity;
					$this->cartSession[$productTypeId][$key]["cut_rate"] = $rate;
				}
			}
		}
		$_SESSION["DISCOUNT_RATE"] = $rate;
		$_SESSION["DISCOUNT_CODE"] = $code;
    }

    /**
     * キャンペーンコードなしのプロモーション取得
     *
     * @param integer $productsCode 商品コード
     * @return array  プロモーション情報
     */
	function getProductsDiscountPromotion($productsCode)
	{
		$objQuery =& SC_Query_Ex::getSingletonInstance();

		$fmt =<<<EOF
SELECT 
	OP.product_cd as product_cd,
	DP.product_cd as discount_product_cd,
	DP.sales_price as sales_price,
	DP.cut_rate as cut_rate,
	PR.*
FROM 
	dtb_promotion PR
INNER JOIN 
	dtb_promotion_order_product OP
	ON PR.promotion_cd = OP.promotion_cd
	AND OP.del_flg = 0
LEFT JOIN 
	dtb_promotion_order_kbn OK
	ON PR.promotion_cd = OK.promotion_cd
	AND OK.del_flg = 0
INNER JOIN 
	dtb_promotion_discount_product DP
	ON PR.promotion_cd = DP.promotion_cd
	AND DP.del_flg = 0
WHERE 
	PR.promotion_cd NOT IN (
		SELECT 
			promotion_cd 
		FROM 
			dtb_promotion_media
		WHERE 
			del_flg = 0
		)
AND 
	PR.del_flg = 0
AND 
	PR.promotion_kbn = 1
AND 
	PR.valid_kbn = 1
AND 
	(PR.valid_from IS NULL
	OR date_format(PR.valid_from, '%%Y%%m%%d') <= date_format(now(), '%%Y%%m%%d'))
AND 
	(PR.valid_to IS NULL
	OR date_format(PR.valid_to, '%%Y%%m%%d') >= date_format(now(), '%%Y%%m%%d'))
AND 
	(OK.order_kbn = %s OR OK.order_kbn IS NULL)
AND 
	OP.product_cd IN (%s)
ORDER BY 
	PR.promotion_cd, OP.product_cd
EOF;

		// プローモーション用受注区分取得
		$orderKbn = $this->getPromotionOrderKbn();

		//$sql = sprintf($fmt, PROMOTION_ORDER_KBN_WEB, $productsCode);
		$sql = sprintf($fmt, $orderKbn, $productsCode);

		$arrData = $objQuery->getAll($sql);

		return $arrData;
	}

    /**
     * キャンペーンコードからプロモーション取得
     *
     * @param integer $productsCode 商品コード
     * @return array  プロモーション情報
     */
	function getDiscountPromotionCampaign($productsCode)
	{
		$objQuery =& SC_Query_Ex::getSingletonInstance();

		$fmt =<<<EOF
SELECT 
	OP.product_cd as product_cd,
	DP.product_cd as discount_product_cd,
	DP.sales_price as sales_price,
	DP.cut_rate as cut_rate,
	PR.*
FROM 
	dtb_promotion PR
INNER JOIN 
	dtb_promotion_order_product OP
	ON PR.promotion_cd = OP.promotion_cd
	AND OP.del_flg = 0
LEFT JOIN 
	dtb_promotion_order_kbn OK
	ON PR.promotion_cd = OK.promotion_cd
	AND OK.del_flg = 0
INNER JOIN 
	dtb_promotion_discount_product DP
	ON PR.promotion_cd = DP.promotion_cd
	AND DP.del_flg = 0
INNER JOIN 
	dtb_promotion_media PM
	ON PR.promotion_cd = PM.promotion_cd
	AND PM.del_flg = 0
INNER JOIN 
	dtb_planning PL
	ON PM.media_code = PL.media_code
	AND PL.del_flg = 0
WHERE 
	PR.del_flg = 0
AND 
	PL.campaign_code = '%s'
AND 
	(PL.start_date IS NULL 
	OR date_format(PL.start_date, '%%Y%%m%%d') <= date_format(now(), '%%Y%%m%%d'))
AND 
	(PL.end_date IS NULL 
	OR date_format(PL.end_date, '%%Y%%m%%d') >= date_format(now(), '%%Y%%m%%d'))
AND 
	PR.promotion_kbn = 1
AND 
	PR.valid_kbn = 1
AND 
	(PR.valid_from IS NULL
	OR date_format(PR.valid_from, '%%Y%%m%%d') <= date_format(now(), '%%Y%%m%%d'))
AND 
	(PR.valid_to IS NULL
	OR date_format(PR.valid_to, '%%Y%%m%%d') >= date_format(now(), '%%Y%%m%%d'))
AND 
	(OK.order_kbn = %s OR OK.order_kbn IS NULL)
AND 
	OP.product_cd IN (%s)
ORDER BY 
	PR.promotion_cd, OP.product_cd
EOF;

		// プローモーション用受注区分取得
		$orderKbn = $this->getPromotionOrderKbn();

		// $sql = sprintf($fmt, $_SESSION["CAMPAIGN_CODE"]
		// 					, PROMOTION_ORDER_KBN_WEB, $productsCode);
		$sql = sprintf($fmt, $_SESSION["CAMPAIGN_CODE"]
							, $orderKbn, $productsCode);

		$arrData = $objQuery->getAll($sql);

		return $arrData;
	}

    /**
     * 割引キャンペーンチェック
     *
     * @param integer $arrPromotion カートキー情報
     * @param array   $arrPromotion プロモーション情報
     * @param array   $arrAllProduct   商品情報
     * @return boolean 割引商品がある場合 true
     */
	function checkDiscountCampaign($productTypeId, $arrPromotion, $arrAllProduct)
	{

		$res = false;

		$objQuery =& SC_Query_Ex::getSingletonInstance();

		foreach ($arrAllProduct as $arrProduct) {
			$oldPromotion = "";
			for ($i = 0; $i < count($arrPromotion); $i++) {
				$chkFlg = 2;
				// プロモーションが異なる場合初期化
				if ($oldPromotion != $arrPromotion[$i]["promotion_cd"]) {
					$promotionQuantity = 0;
					$promotionPrice = 0;
					$promotionProducts = "";
					$arrDiscountProduct = array();
				}
				for ($j = 0; $j < count($arrProduct); $j++) {
					// 商品コードが同一のみチェック対象
					if ($arrPromotion[$i]["product_cd"] != $arrProduct[$j]["product_code"]) {
						continue;
					}
					// コース区分確認
					if ($arrPromotion[$i]["course_kbn"] != $arrProduct[$j]["regular_flg"]
						&& $arrPromotion[$i]["course_kbn"] != PROMOTION_COURSE_KBN_ALL) {
						continue;
					}

					// 対象商品確認
					for ($spCnt = 0; $spCnt < count($arrProduct); $spCnt++) {
						// 商品コードが同一のみチェック対象
						if ($arrPromotion[$i]["discount_product_cd"] != $arrProduct[$spCnt]["product_code"]) {
							continue;
						}
						// コース区分確認
						if ($arrPromotion[$i]["course_kbn"] != $arrProduct[$spCnt]["regular_flg"]
							&& $arrPromotion[$i]["course_kbn"] != PROMOTION_COURSE_KBN_ALL) {
							continue;
						}
						if (!isset($arrDiscountProduct[$spCnt])) {
							$arrDiscountProduct[$spCnt] = $i;

							$promotionQuantity += $arrProduct[$spCnt]["quantity"];
							$promotionPrice += $arrProduct[$spCnt]["price"];
							if ($promotionProducts) {
								$promotionProducts .= ",";
							}
							$promotionProducts .= sprintf("'%s'", $arrProduct[$spCnt]["product_code"]);
						}

						// 明細単位でのチェック時
						if ($arrPromotion[$i]["quantity_kbn"] == PROMOTION_QUANTITY_KBN_DETAIL) {
							// 購入数量チェック
							if ($arrPromotion[$i]["quantity_from"] && $arrPromotion[$i]["quantity_to"]) {
								if ($arrPromotion[$i]["quantity_from"] <= $arrProduct[$spCnt]["quantity"]
									&& $arrPromotion[$i]["quantity_to"] >= $arrProduct[$spCnt]["quantity"]) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["quantity_from"]) {
								if ($arrPromotion[$i]["quantity_from"] <= $arrProduct[$spCnt]["quantity"]) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["quantity_to"]) {
								if ($arrPromotion[$i]["quantity_to"] >= $arrProduct[$spCnt]["quantity"]) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							}

							// 購入金額チェック
							if ($arrPromotion[$i]["amount_from"] && $arrPromotion[$i]["amount_to"]) {
								if ($arrPromotion[$i]["amount_from"] <= $arrProduct[$spCnt]["price"]
									&& $arrPromotion[$i]["amount_to"] >= $arrProduct[$spCnt]["price"]) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["amount_from"]) {
								if ($arrPromotion[$i]["amount_from"] <= $arrProduct[$spCnt]["price"]) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["amount_to"]) {
								if ($arrPromotion[$i]["amount_to"] >= $arrProduct[$spCnt]["price"]) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							}

							// 購入回数チェック
							if ($arrPromotion[$i]["count_from"] 
								|| $arrPromotion[$i]["count_to"]) {
								// 購入回数取得
								$chkCnt = $this->getProductsBuyCount($arrPromotion[$i]["promotion_cd"]);
								// 定期の場合は、かならず１回目
								if ($arrProduct[$spCnt]["regular_flg"] == 
									REGULAR_PURCHASE_FLG_ON) {
									// コース区分が全体ではない場合
									if ($arrPromotion[$i]["course_kbn"] != 
										PROMOTION_COURSE_KBN_ALL) {
										$chkCnt = 1;
									}
								}
								if ($arrPromotion[$i]["count_from"] 
									&& $arrPromotion[$i]["count_to"]) {
									if ($arrPromotion[$i]["count_from"] <= $chkCnt
										&& $arrPromotion[$i]["count_to"] >= $chkCnt) {
										$chkFlg = 1;
									} else {
										$chkFlg = 0;
										break;
									}
								} else if ($arrPromotion[$i]["count_from"]) {
									if ($arrPromotion[$i]["count_from"] <= $chkCnt) {
										$chkFlg = 1;
									} else {
										$chkFlg = 0;
										break;
									}
								} else if ($arrPromotion[$i]["count_to"]) {
									if ($arrPromotion[$i]["count_to"] >= $chkCnt) {
										$chkFlg = 1;
									} else {
										$chkFlg = 0;
										break;
									}
								}
							}
							// 適用可能回数チェック
							if ($arrPromotion[$i]["use_count"]) {
								// 適用回数取得
								$chkCnt = $this->getPromotionBuyCount("'".$arrPromotion[$i]["promotion_cd"]."'");
								// 定期の場合は、かならず１回目
								if ($arrProduct[$spCnt]["regular_flg"] == 
									REGULAR_PURCHASE_FLG_ON) {
									// コース区分が全体ではない場合
									if ($arrPromotion[$i]["course_kbn"] != 
										PROMOTION_COURSE_KBN_ALL) {
										$chkCnt = 1;
									}
								}
								if ($arrPromotion[$i]["use_count"] >= $chkCnt) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							}
						// 全明細単位でのチェック時
						} else if ($arrPromotion[$i]["quantity_kbn"] == PROMOTION_QUANTITY_KBN_ALL) {
							// 購入数量チェック
							if ($arrPromotion[$i]["quantity_from"] && $arrPromotion[$i]["quantity_to"]) {
								if ($arrPromotion[$i]["quantity_from"] <= $promotionQuantity
									&& $arrPromotion[$i]["quantity_to"] >= $promotionQuantity) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["quantity_from"]) {
								if ($arrPromotion[$i]["quantity_from"] <= $promotionQuantity) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["quantity_to"]) {
								if ($arrPromotion[$i]["quantity_to"] >= $promotionQuantity) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							}

							// 購入金額チェック
							if ($arrPromotion[$i]["amount_from"] && $arrPromotion[$i]["amount_to"]) {
								if ($arrPromotion[$i]["amount_from"] <= $promotionPrice
									&& $arrPromotion[$i]["amount_to"] >= $promotionPrice) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["amount_from"]) {
								if ($arrPromotion[$i]["amount_from"] <= $promotionPrice) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["amount_to"]) {
								if ($arrPromotion[$i]["amount_to"] >= $promotionPrice) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							}

							// 購入回数チェック
							if ($arrPromotion[$i]["count_from"] 
								|| $arrPromotion[$i]["count_to"]) {
								// 購入回数取得
								$chkCnt = $this->getProductsBuyCount($arrPromotion[$i]["promotion_cd"]);
								// 定期の場合は、かならず１回目
								if ($arrProduct[$spCnt]["regular_flg"] == 
									REGULAR_PURCHASE_FLG_ON) {
									// コース区分が全体ではない場合
									if ($arrPromotion[$i]["course_kbn"] != 
										PROMOTION_COURSE_KBN_ALL) {
										$chkCnt = 1;
									}
								}
								if ($arrPromotion[$i]["count_from"] 
									&& $arrPromotion[$i]["count_to"]) {
									if ($arrPromotion[$i]["count_from"] <= $chkCnt
										&& $arrPromotion[$i]["count_to"] >= $chkCnt) {
										$chkFlg = 1;
									} else {
										$chkFlg = 0;
										break;
									}
								} else if ($arrPromotion[$i]["count_from"]) {
									if ($arrPromotion[$i]["count_from"] <= $chkCnt) {
										$chkFlg = 1;
									} else {
										$chkFlg = 0;
										break;
									}
								} else if ($arrPromotion[$i]["count_to"]) {
									if ($arrPromotion[$i]["count_to"] >= $chkCnt) {
										$chkFlg = 1;
									} else {
										$chkFlg = 0;
										break;
									}
								}
							}
							// 適用可能回数チェック
							if ($arrPromotion[$i]["use_count"]) {
								// 適用回数取得
								$chkCnt = $this->getPromotionBuyCount($promotionProducts);
								// 定期の場合は、かならず１回目
								if ($arrProduct[$spCnt]["regular_flg"] == 
									REGULAR_PURCHASE_FLG_ON) {
									// コース区分が全体ではない場合
									if ($arrPromotion[$i]["course_kbn"] != 
										PROMOTION_COURSE_KBN_ALL) {
										$chkCnt = 1;
									}
								}
								if ($arrPromotion[$i]["use_count"] >= $chkCnt) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							}
						}
						if ($chkFlg) {
							// 商品金額が安く適用したプロモーションコードをSESSIONに保持
							if ($arrProduct[$spCnt]["base_price"] > $arrPromotion[$i]["sales_price"]) {
								// 適用したプローモーションコードがある場合、再設定する
								if (isset($arrProduct[$spCnt]["promotion_cd"]) 
									&& $arrProduct[$spCnt]["promotion_cd"]) {
									$oldPromotionCd = $arrProduct[$spCnt]["promotion_cd"];
									if (($unsetCnt = 
										array_search($oldPromotionCd
										, $_SESSION["ORDER_PROMOTION_CD"])) 
										!== false) {
										unset($_SESSION["ORDER_PROMOTION_CD"][$unsetCnt]);
										unset($arrSet[$oldPromotionCd]);
									}
								}
								$key = $arrProduct[$spCnt]["key"];
								// 処理判定用配列更新
								$arrSet[$arrPromotion[$i]["promotion_cd"]] = 
											$arrPromotion[$i]["promotion_cd"];
								$arrProduct[$spCnt]["promotion_cd"] = 
											$arrPromotion[$i]["promotion_cd"];
								$arrProduct[$spCnt]["base_price"] = 
											$arrPromotion[$i]["sales_price"];
								$arrProduct[$spCnt]["price"] = 
											$arrPromotion[$i]["sales_price"] * 
											$arrProduct[$spCnt]["quantity"];
								// カート商品情報更新
								$this->cartSession[$productTypeId][$key]["price"] = 
											$arrPromotion[$i]["sales_price"];
								$this->cartSession[$productTypeId][$key]["total_inctax"] = 
											$arrPromotion[$i]["sales_price"] * 
											$arrProduct[$spCnt]["quantity"];
								$this->cartSession[$productTypeId][$key]["cut_rate"] = 
											$arrPromotion[$i]["cut_rate"];
							}
						}
					}
					break;
				}
				$oldPromotion = $arrPromotion[$i]["promotion_cd"];
			}
		}

		// 適用したプロモーションコードをSESSIONに保持
		if (is_array($arrSet)) {
			if (is_array($_SESSION["ORDER_PROMOTION_CD"])) {
				$_SESSION["ORDER_PROMOTION_CD"] = array_merge($_SESSION["ORDER_PROMOTION_CD"], $arrSet);
				$_SESSION["ORDER_PROMOTION_CD"] = array_unique($_SESSION["ORDER_PROMOTION_CD"]);
			} else {
				$_SESSION["ORDER_PROMOTION_CD"] = $arrSet;
			}
			$res = true;
		}

		return $res;
	}

    /**
     * 定期用キャンペーンコードなしのプロモーション取得
     *
     * @param integer $productsCode 商品コード
     * @return array  プロモーション情報
     */
	function getProductsDiscountPromotionRegular($productsCode)
	{
		$objQuery =& SC_Query_Ex::getSingletonInstance();

		$fmt =<<<EOF
SELECT 
	OP.product_cd as product_cd,
	DP.product_cd as discount_product_cd,
	DP.sales_price as sales_price,
	PR.*
FROM 
	dtb_promotion PR
INNER JOIN 
	dtb_promotion_order_product OP
	ON PR.promotion_cd = OP.promotion_cd
	AND OP.del_flg = 0
LEFT JOIN 
	dtb_promotion_order_kbn OK
	ON PR.promotion_cd = OK.promotion_cd
	AND OK.del_flg = 0
INNER JOIN 
	dtb_promotion_discount_product DP
	ON PR.promotion_cd = DP.promotion_cd
	AND DP.del_flg = 0
WHERE 
	PR.promotion_cd NOT IN (
		SELECT 
			promotion_cd 
		FROM 
			dtb_promotion_media
		WHERE 
			del_flg = 0
		)
AND 
	PR.del_flg = 0
AND 
	PR.promotion_kbn = 1
AND 
	PR.valid_kbn = 1
AND 
	(PR.valid_from IS NULL
	OR date_format(PR.valid_from, '%%Y%%m%%d') <= date_format(now(), '%%Y%%m%%d'))
AND 
	(PR.valid_to IS NULL
	OR date_format(PR.valid_to, '%%Y%%m%%d') >= date_format(now(), '%%Y%%m%%d'))
 %s 
AND 
	PR.course_kbn IN (%s)
AND 
	OP.product_cd IN (%s)
ORDER BY 
	PR.promotion_cd, OP.product_cd
EOF;

		// コース区分（定期・全体）
		$courseKbn = sprintf("%s,%s", PROMOTION_COURSE_KBN_REGULAR
									, PROMOTION_COURSE_KBN_ALL);

		$whereOrderFmt = " AND (OK.order_kbn = %s OR OK.order_kbn IS NULL) ";
		$where = "";
		// 注文時のDEVICE　TYPEを適用する
		if ($_SESSION["REGULAR_DEVICE_TYPE"]) {
			// プローモーション用受注区分取得
			$orderKbn = $this->getPromotionOrderKbn($_SESSION["REGULAR_DEVICE_TYPE"]);
			$where = sprintf($whereOrderFmt, $orderKbn);
		}

		$sql = sprintf($fmt, $where, $courseKbn, $productsCode);

		$arrData = $objQuery->getAll($sql);

		return $arrData;
	}

    /**
     * 定期用キャンペーンコードからプロモーション取得
     *
     * @param integer $productsCode 商品コード
     * @return array  プロモーション情報
     */
	function getDiscountPromotionCampaignRegular($productsCode)
	{
		$objQuery =& SC_Query_Ex::getSingletonInstance();

		$fmt =<<<EOF
SELECT 
	OP.product_cd as product_cd,
	DP.product_cd as discount_product_cd,
	DP.sales_price as sales_price,
	PR.*
FROM 
	dtb_promotion PR
INNER JOIN 
	dtb_promotion_order_product OP
	ON PR.promotion_cd = OP.promotion_cd
	AND OP.del_flg = 0
LEFT JOIN 
	dtb_promotion_order_kbn OK
	ON PR.promotion_cd = OK.promotion_cd
	AND OK.del_flg = 0
INNER JOIN 
	dtb_promotion_discount_product DP
	ON PR.promotion_cd = DP.promotion_cd
	AND DP.del_flg = 0
INNER JOIN 
	dtb_promotion_media PM
	ON PR.promotion_cd = PM.promotion_cd
	AND PM.del_flg = 0
INNER JOIN 
	dtb_planning PL
	ON PM.media_code = PL.media_code
	AND PL.del_flg = 0
WHERE 
	PR.del_flg = 0
AND 
	PL.campaign_code = '%s'
AND 
	(PL.start_date IS NULL 
	OR date_format(PL.start_date, '%%Y%%m%%d') <= date_format(now(), '%%Y%%m%%d'))
AND 
	(PL.end_date IS NULL 
	OR date_format(PL.end_date, '%%Y%%m%%d') >= date_format(now(), '%%Y%%m%%d'))
AND 
	PR.promotion_kbn = 1
AND 
	PR.valid_kbn = 1
AND 
	(PR.valid_from IS NULL
	OR date_format(PR.valid_from, '%%Y%%m%%d') <= date_format(now(), '%%Y%%m%%d'))
AND 
	(PR.valid_to IS NULL
	OR date_format(PR.valid_to, '%%Y%%m%%d') >= date_format(now(), '%%Y%%m%%d'))
 %s 
AND 
	PR.course_kbn IN (%s)
AND 
	OP.product_cd IN (%s)
ORDER BY 
	PR.promotion_cd, OP.product_cd
EOF;

		// プローモーション用受注区分取得
		$orderKbn = $this->getPromotionOrderKbn();

		// $sql = sprintf($fmt, $_SESSION["CAMPAIGN_CODE"]
		// 					, PROMOTION_ORDER_KBN_WEB, $productsCode);
		$sql = sprintf($fmt, $_SESSION["CAMPAIGN_CODE"]
							, $orderKbn, $productsCode);

		$courseKbn = sprintf("%s,%s", PROMOTION_COURSE_KBN_REGULAR
									, PROMOTION_COURSE_KBN_ALL);

		$whereOrderFmt = " AND (OK.order_kbn = %s OR OK.order_kbn IS NULL) ";
		$where = "";
		// 注文時のDEVICE　TYPEを適用する
		if ($_SESSION["REGULAR_DEVICE_TYPE"]) {
			// プローモーション用受注区分取得
			$orderKbn = $this->getPromotionOrderKbn($_SESSION["REGULAR_DEVICE_TYPE"]);
			$where = sprintf($whereOrderFmt, $orderKbn);
		}

		$sql = sprintf($fmt, $_SESSION["REGULAR_CAMPAIGN_CODE"], $where
						, $courseKbn, $productsCode);

		$arrData = $objQuery->getAll($sql);

		return $arrData;
	}

    /**
     * 定期用割引キャンペーンチェック
     *
     * @param integer $arrPromotion カートキー情報
     * @param array   $arrPromotion プロモーション情報
     * @param array   $arrAllProduct   商品情報
     * @return boolean 割引商品がある場合 true
     */
	function checkDiscountCampaignRegular($productTypeId, $arrPromotion, $arrAllProduct)
	{

		$res = false;

		$objQuery =& SC_Query_Ex::getSingletonInstance();

		foreach ($arrAllProduct as $arrProduct) {
			$oldPromotion = "";
			for ($i = 0; $i < count($arrPromotion); $i++) {
				$chkFlg = 2;
				// プロモーションが異なる場合初期化
				if ($oldPromotion != $arrPromotion[$i]["promotion_cd"]) {
					$promotionQuantity = 0;
					$promotionPrice = 0;
					$promotionProducts = "";
					$arrDiscountProduct = array();
				}
				for ($j = 0; $j < count($arrProduct); $j++) {
					// 商品コードが同一のみチェック対象
					if ($arrPromotion[$i]["product_cd"] != $arrProduct[$j]["product_code"]) {
						continue;
					}
					// コース区分確認
					if ($arrPromotion[$i]["course_kbn"] != $arrProduct[$j]["regular_flg"]
						&& $arrPromotion[$i]["course_kbn"] != PROMOTION_COURSE_KBN_ALL) {
						continue;
					}

					// 対象商品確認
					for ($spCnt = 0; $spCnt < count($arrProduct); $spCnt++) {
						// 商品コードが同一のみチェック対象
						if ($arrPromotion[$i]["discount_product_cd"] != $arrProduct[$spCnt]["product_code"]) {
							continue;
						}
						// コース区分確認
						if ($arrPromotion[$i]["course_kbn"] != $arrProduct[$spCnt]["regular_flg"]
							&& $arrPromotion[$i]["course_kbn"] != PROMOTION_COURSE_KBN_ALL) {
							continue;
						}
						if (!isset($arrDiscountProduct[$spCnt])) {
							$arrDiscountProduct[$spCnt] = $i;

							$promotionQuantity += $arrProduct[$spCnt]["quantity"];
							$promotionPrice += $arrProduct[$spCnt]["price"];
							if ($promotionProducts) {
								$promotionProducts .= ",";
							}
							$promotionProducts .= sprintf("'%s'", $arrProduct[$spCnt]["product_code"]);
						}

						// 明細単位でのチェック時
						if ($arrPromotion[$i]["quantity_kbn"] == PROMOTION_QUANTITY_KBN_DETAIL) {
							// 購入数量チェック
							if ($arrPromotion[$i]["quantity_from"] && $arrPromotion[$i]["quantity_to"]) {
								if ($arrPromotion[$i]["quantity_from"] <= $arrProduct[$spCnt]["quantity"]
									&& $arrPromotion[$i]["quantity_to"] >= $arrProduct[$spCnt]["quantity"]) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["quantity_from"]) {
								if ($arrPromotion[$i]["quantity_from"] <= $arrProduct[$spCnt]["quantity"]) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["quantity_to"]) {
								if ($arrPromotion[$i]["quantity_to"] >= $arrProduct[$spCnt]["quantity"]) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							}

							// 購入金額チェック
							if ($arrPromotion[$i]["amount_from"] && $arrPromotion[$i]["amount_to"]) {
								if ($arrPromotion[$i]["amount_from"] <= $arrProduct[$spCnt]["price"]
									&& $arrPromotion[$i]["amount_to"] >= $arrProduct[$spCnt]["price"]) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["amount_from"]) {
								if ($arrPromotion[$i]["amount_from"] <= $arrProduct[$spCnt]["price"]) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["amount_to"]) {
								if ($arrPromotion[$i]["amount_to"] >= $arrProduct[$spCnt]["price"]) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							}

							// 購入回数チェック
							if ($arrPromotion[$i]["count_from"] 
								|| $arrPromotion[$i]["count_to"]) {
								// 購入回数取得
								$chkCnt = $this->getProductsBuyCount($arrPromotion[$i]["promotion_cd"]);
								if ($arrPromotion[$i]["count_from"] 
									&& $arrPromotion[$i]["count_to"]) {
									if ($arrPromotion[$i]["count_from"] <= $chkCnt
										&& $arrPromotion[$i]["count_to"] >= $chkCnt) {
										$chkFlg = 1;
									} else {
										$chkFlg = 0;
										break;
									}
								} else if ($arrPromotion[$i]["count_from"]) {
									if ($arrPromotion[$i]["count_from"] <= $chkCnt) {
										$chkFlg = 1;
									} else {
										$chkFlg = 0;
										break;
									}
								} else if ($arrPromotion[$i]["count_to"]) {
									if ($arrPromotion[$i]["count_to"] >= $chkCnt) {
										$chkFlg = 1;
									} else {
										$chkFlg = 0;
										break;
									}
								}
							}
							// 適用可能回数チェック
							if ($arrPromotion[$i]["use_count"]) {
								// 適用回数取得
								$chkCnt = $this->getPromotionBuyCount("'".$arrPromotion[$i]["promotion_cd"]."'");
								if ($arrPromotion[$i]["use_count"] >= $chkCnt) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							}
						// 全明細単位でのチェック時
						} else if ($arrPromotion[$i]["quantity_kbn"] == PROMOTION_QUANTITY_KBN_ALL) {
							// 購入数量チェック
							if ($arrPromotion[$i]["quantity_from"] && $arrPromotion[$i]["quantity_to"]) {
								if ($arrPromotion[$i]["quantity_from"] <= $promotionQuantity
									&& $arrPromotion[$i]["quantity_to"] >= $promotionQuantity) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["quantity_from"]) {
								if ($arrPromotion[$i]["quantity_from"] <= $promotionQuantity) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["quantity_to"]) {
								if ($arrPromotion[$i]["quantity_to"] >= $promotionQuantity) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							}

							// 購入金額チェック
							if ($arrPromotion[$i]["amount_from"] && $arrPromotion[$i]["amount_to"]) {
								if ($arrPromotion[$i]["amount_from"] <= $promotionPrice
									&& $arrPromotion[$i]["amount_to"] >= $promotionPrice) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["amount_from"]) {
								if ($arrPromotion[$i]["amount_from"] <= $promotionPrice) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["amount_to"]) {
								if ($arrPromotion[$i]["amount_to"] >= $promotionPrice) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							}

							// 購入回数チェック
							if ($arrPromotion[$i]["count_from"] 
								|| $arrPromotion[$i]["count_to"]) {
								// 購入回数取得
								$chkCnt = $this->getProductsBuyCount($arrPromotion[$i]["promotion_cd"]);
								if ($arrPromotion[$i]["count_from"] 
									&& $arrPromotion[$i]["count_to"]) {
									if ($arrPromotion[$i]["count_from"] <= $chkCnt
										&& $arrPromotion[$i]["count_to"] >= $chkCnt) {
										$chkFlg = 1;
									} else {
										$chkFlg = 0;
										break;
									}
								} else if ($arrPromotion[$i]["count_from"]) {
									if ($arrPromotion[$i]["count_from"] <= $chkCnt) {
										$chkFlg = 1;
									} else {
										$chkFlg = 0;
										break;
									}
								} else if ($arrPromotion[$i]["count_to"]) {
									if ($arrPromotion[$i]["count_to"] >= $chkCnt) {
										$chkFlg = 1;
									} else {
										$chkFlg = 0;
										break;
									}
								}
							}
							// 適用可能回数チェック
							if ($arrPromotion[$i]["use_count"]) {
								// 適用回数取得
								$chkCnt = $this->getPromotionBuyCount($promotionProducts);
								if ($arrPromotion[$i]["use_count"] >= $chkCnt) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							}
						}
						if ($chkFlg) {
							// 商品金額が安く適用したプロモーションコードをSESSIONに保持
							if ($arrProduct[$spCnt]["base_price"] > $arrPromotion[$i]["sales_price"]) {
								// 適用したプローモーションコードがある場合、再設定する
								if (isset($arrProduct[$spCnt]["promotion_cd"]) 
									&& $arrProduct[$spCnt]["promotion_cd"]) {
									$oldPromotionCd = $arrProduct[$spCnt]["promotion_cd"];
									if (($unsetCnt = 
										array_search($oldPromotionCd
										, $_SESSION["ORDER_PROMOTION_CD"])) 
										!== false) {
										unset($_SESSION["ORDER_PROMOTION_CD"][$unsetCnt]);
										unset($arrSet[$oldPromotionCd]);
									}
								}
								$key = $arrProduct[$spCnt]["key"];
								// 処理判定用配列更新
								$arrSet[$arrPromotion[$i]["promotion_cd"]] = 
											$arrPromotion[$i]["promotion_cd"];
								$arrProduct[$spCnt]["promotion_cd"] = 
											$arrPromotion[$i]["promotion_cd"];
								$arrProduct[$spCnt]["base_price"] = 
											$arrPromotion[$i]["sales_price"];
								$arrProduct[$spCnt]["price"] = 
											$arrPromotion[$i]["sales_price"] * 
											$arrProduct[$spCnt]["quantity"];
								// カート商品情報更新
								$this->cartSession[$productTypeId][$key]["price"] = 
											$arrPromotion[$i]["sales_price"];
								$this->cartSession[$productTypeId][$key]["total_inctax"] = 
											$arrPromotion[$i]["sales_price"] * 
											$arrProduct[$spCnt]["quantity"];
							}
						}
					}
					break;
				}
				$oldPromotion = $arrPromotion[$i]["promotion_cd"];
			}
		}

		// 適用したプロモーションコードをSESSIONに保持
		if (is_array($arrSet)) {
			if (is_array($_SESSION["ORDER_PROMOTION_CD"])) {
				$_SESSION["ORDER_PROMOTION_CD"] = array_merge($_SESSION["ORDER_PROMOTION_CD"], $arrSet);
				$_SESSION["ORDER_PROMOTION_CD"] = array_unique($_SESSION["ORDER_PROMOTION_CD"]);
			} else {
				$_SESSION["ORDER_PROMOTION_CD"] = $arrSet;
			}
			$res = true;
		}

		return $res;
	}

    /**
     * プローモーション同梱品チェックする
     *
     * @param integer $productTypeId 商品種別ID
     * @return boolean 同梱品がある場合 true
     */
	function isProductsIncludeCampaign($productTypeId)
	{

        $objCustomer = new SC_Customer_Ex();

        // ログインユーザの顧客区分取得
        $customer_kbn = $objCustomer->getValue("customer_kbn");
		// 社員の場合は、プロモーション適用しない
		if ($customer_kbn == CUSTOMER_KBN_EMPLOYEE) {
			return false;
		}

		$arrData = array();
		$sqlProducts = "";
		$arrCnt = array();

		// 商品最大数を取得
		$productsCnt = $this->getMax($productTypeId);
		for ($i = 0; $i <= $productsCnt; $i++) {
			// カート商品確認
			if (isset($this->cartSession[$productTypeId][$i]["cart_no"])
				&& $this->cartSession[$productTypeId][$i]["cart_no"] != "") {

				// 定期購入期間
				if (isset($this->cartSession[$productTypeId][$i]["course_cd"])) {
					$courseCd = sprintf("%d"
						, $this->cartSession[$productTypeId][$i]["course_cd"]);
				} else {
					$courseCd = 0;
				}
				// お届間隔にて配列生成
				if (!isset($arrCnt[$courseCd])) {
					$arrCnt[$courseCd] = 0;
				}
				$dataCnt = $arrCnt[$courseCd];

				// KEY情報
				$arrData[$courseCd][$dataCnt]["key"] = $i;
				// 商品コード
				$arrData[$courseCd][$dataCnt]["product_code"] = 
					$this->cartSession[$productTypeId][$i]["productsClass"]["product_code"];
				// 購入数量
				$arrData[$courseCd][$dataCnt]["quantity"] = 
					$this->cartSession[$productTypeId][$i]["quantity"];
				// 購入金額
				$arrData[$courseCd][$dataCnt]["base_price"] = 
					$this->cartSession[$productTypeId][$i]["price"];
				$arrData[$courseCd][$dataCnt]["price"] = 
					$this->cartSession[$productTypeId][$i]["total_inctax"];
				// 単品定期区分
				$arrData[$courseCd][$dataCnt]["regular_flg"] = 
					$this->cartSession[$productTypeId][$i]["regular_flg"];
				// SQL用商品コード編集
				if ($arrData[$courseCd][$dataCnt]["product_code"]) {
					if ($sqlProducts) {
						$sqlProducts .= ",";
					}
					// 商品コードSQLように加工
					$sqlProducts .= sprintf("'%s'"
								, $arrData[$courseCd][$dataCnt]["product_code"]);
				}
				$arrCnt[$courseCd]++;
			}
		}

		// 商品がない場合終了
		if (!$sqlProducts) {
			return false;
		}
		// 同梱品を確認
		$arrDelivData = $this->getProductsIncludePromotion($sqlProducts);
		// キャンペーンコード入力時は入力用も取得
		if ($_SESSION["CAMPAIGN_CODE"]) {
			$arrCPDelivData = $this->getIncludePromotionCampaign($sqlProducts);
			if (isset($arrDelivData[0]["promotion_cd"])) {
				if (isset($arrCPDelivData[0]["promotion_cd"])) {
					for ($i = 0; $i < count($arrCPDelivData); $i++) {
						$arrDelivData[] = $arrCPDelivData[$i];
					}
				}
			} else {
				if (isset($arrCPDelivData[0]["promotion_cd"])) {
					$arrDelivData = $arrCPDelivData;
				}
			}
		}

		// 同梱品プロモーションがない場合設定なし
		if (!isset($arrDelivData[0]["promotion_cd"])) {
			return false;
		}

		// 同梱品対象チェック
		return $this->checkIncludeCampaign($productTypeId, $arrDelivData, $arrData);

	}

    /**
     * キャンペーンコードなしのプロモーション取得
     *
     * @param integer $productsCode 商品コード
     * @return array  プロモーション情報
     */
	function getProductsIncludePromotion($productsCode)
	{
		$objQuery =& SC_Query_Ex::getSingletonInstance();

		$fmt =<<<EOF
SELECT 
	OP.product_cd as product_cd,
	IP.product_cd as include_product_cd,
	IP.quantity as include_quantity,
	PC.deliv_judgment as include_deliv_judgment,
	PR.*
FROM 
	dtb_promotion PR
INNER JOIN 
	dtb_promotion_order_product OP
	ON PR.promotion_cd = OP.promotion_cd
	AND OP.del_flg = 0
LEFT JOIN 
	dtb_promotion_order_kbn OK
	ON PR.promotion_cd = OK.promotion_cd
	AND OK.del_flg = 0
INNER JOIN 
	dtb_promotion_include_product IP
	ON PR.promotion_cd = IP.promotion_cd
	AND IP.del_flg = 0
INNER JOIN 
	dtb_products_class PC
	ON IP.product_cd = PC.product_code
	AND PC.del_flg = 0
WHERE 
	PR.promotion_cd NOT IN (
		SELECT 
			promotion_cd 
		FROM 
			dtb_promotion_media
		WHERE 
			del_flg = 0
		)
AND 
	PR.del_flg = 0
AND 
	PR.promotion_kbn = 3
AND 
	PR.valid_kbn = 1
AND 
	(PR.valid_from IS NULL
	OR date_format(PR.valid_from, '%%Y%%m%%d') <= date_format(now(), '%%Y%%m%%d'))
AND 
	(PR.valid_to IS NULL
	OR date_format(PR.valid_to, '%%Y%%m%%d') >= date_format(now(), '%%Y%%m%%d'))
AND 
	(OK.order_kbn = %s OR OK.order_kbn IS NULL)
AND 
	OP.product_cd IN (%s)
EOF;

		// プローモーション用受注区分取得
		$orderKbn = $this->getPromotionOrderKbn();

		//$sql = sprintf($fmt, PROMOTION_ORDER_KBN_WEB, $productsCode);
		$sql = sprintf($fmt, $orderKbn, $productsCode);

		$arrData = $objQuery->getAll($sql);

		return $arrData;
	}

    /**
     * キャンペーンコードからプロモーション取得
     *
     * @param integer $productsCode 商品コード
     * @return array  プロモーション情報
     */
	function getIncludePromotionCampaign($productsCode)
	{
		$objQuery =& SC_Query_Ex::getSingletonInstance();

		$fmt =<<<EOF
SELECT 
	OP.product_cd as product_cd,
	IP.product_cd as include_product_cd,
	IP.quantity as include_quantity,
	PC.deliv_judgment as include_deliv_judgment,
	PR.*
FROM 
	dtb_promotion PR
INNER JOIN 
	dtb_promotion_order_product OP
	ON PR.promotion_cd = OP.promotion_cd
	AND OP.del_flg = 0
LEFT JOIN 
	dtb_promotion_order_kbn OK
	ON PR.promotion_cd = OK.promotion_cd
	AND OK.del_flg = 0
INNER JOIN 
	dtb_promotion_include_product IP
	ON PR.promotion_cd = IP.promotion_cd
	AND IP.del_flg = 0
INNER JOIN 
	dtb_promotion_media PM
	ON PR.promotion_cd = PM.promotion_cd
	AND PM.del_flg = 0
INNER JOIN 
	dtb_planning PL
	ON PM.media_code = PL.media_code
	AND PL.del_flg = 0
INNER JOIN 
	dtb_products_class PC
	ON IP.product_cd = PC.product_code
	AND PC.del_flg = 0
WHERE 
	PR.del_flg = 0
AND 
	PL.campaign_code = '%s'
AND 
	(PL.start_date IS NULL 
	OR date_format(PL.start_date, '%%Y%%m%%d') <= date_format(now(), '%%Y%%m%%d'))
AND 
	(PL.end_date IS NULL 
	OR date_format(PL.end_date, '%%Y%%m%%d') >= date_format(now(), '%%Y%%m%%d'))
AND 
	PR.promotion_kbn = 3
AND 
	PR.valid_kbn = 1
AND 
	(PR.valid_from IS NULL
	OR date_format(PR.valid_from, '%%Y%%m%%d') <= date_format(now(), '%%Y%%m%%d'))
AND 
	(PR.valid_to IS NULL
	OR date_format(PR.valid_to, '%%Y%%m%%d') >= date_format(now(), '%%Y%%m%%d'))
AND 
	(OK.order_kbn = %s OR OK.order_kbn IS NULL)
AND 
	OP.product_cd IN (%s)
EOF;

		// プローモーション用受注区分取得
		$orderKbn = $this->getPromotionOrderKbn();

		//$sql = sprintf($fmt, $_SESSION["CAMPAIGN_CODE"]
		//					, PROMOTION_ORDER_KBN_WEB, $productsCode);

		$sql = sprintf($fmt, $_SESSION["CAMPAIGN_CODE"]
							, $orderKbn, $productsCode);

		$arrData = $objQuery->getAll($sql);

		return $arrData;
	}

    /**
     * 同梱品プロモーションチェック
     *
     * @param integer $arrPromotion カートキー情報
     * @param array   $arrPromotion プロモーション情報
     * @param array   $arrAllProduct   商品情報
     * @return boolean 割引商品がある場合 true
     */
	function checkIncludeCampaign($productTypeId, $arrPromotion, $arrAllProduct)
	{

		$res = false;

		$objQuery =& SC_Query_Ex::getSingletonInstance();

		$oldPromotion = "";
		foreach ($arrAllProduct as $arrProduct) {
			for ($i = 0; $i < count($arrPromotion); $i++) {
				$chkFlg = 2;
				// プロモーションが異なる場合初期化
				if ($oldPromotion != $arrPromotion[$i]["promotion_cd"]) {
					$promotionQuantity = 0;
					$promotionPrice = 0;
					$promotionProducts = "";
				}
				for ($j = 0; $j < count($arrProduct); $j++) {
					// 商品コードが同一のみチェック対象
					if ($arrPromotion[$i]["product_cd"] != $arrProduct[$j]["product_code"]) {
						continue;
					}
					// コース区分確認
					if ($arrPromotion[$i]["course_kbn"] != $arrProduct[$j]["regular_flg"]
						&& $arrPromotion[$i]["course_kbn"] != PROMOTION_COURSE_KBN_ALL) {
						continue;
					}

					$promotionQuantity += $arrProduct[$j]["quantity"];
					$promotionPrice += $arrProduct[$j]["price"];
					if ($promotionProducts) {
						$promotionProducts .= ",";
					}
					$promotionProducts .= sprintf("'%s'", $arrProduct[$j]["product_code"]);

					// 明細単位でのチェック時
					if ($arrPromotion[$i]["quantity_kbn"] == PROMOTION_QUANTITY_KBN_DETAIL) {
						// 購入数量チェック
						if ($arrPromotion[$i]["quantity_from"] && $arrPromotion[$i]["quantity_to"]) {
							if ($arrPromotion[$i]["quantity_from"] <= $arrProduct[$j]["quantity"]
								&& $arrPromotion[$i]["quantity_to"] >= $arrProduct[$j]["quantity"]) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						} else if ($arrPromotion[$i]["quantity_from"]) {
							if ($arrPromotion[$i]["quantity_from"] <= $arrProduct[$j]["quantity"]) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						} else if ($arrPromotion[$i]["quantity_to"]) {
							if ($arrPromotion[$i]["quantity_to"] >= $arrProduct[$j]["quantity"]) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						}

						// 購入金額チェック
						if ($arrPromotion[$i]["amount_from"] && $arrPromotion[$i]["amount_to"]) {
							if ($arrPromotion[$i]["amount_from"] <= $arrProduct[$j]["price"]
								&& $arrPromotion[$i]["amount_to"] >= $arrProduct[$j]["price"]) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						} else if ($arrPromotion[$i]["amount_from"]) {
							if ($arrPromotion[$i]["amount_from"] <= $arrProduct[$j]["price"]) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						} else if ($arrPromotion[$i]["amount_to"]) {
							if ($arrPromotion[$i]["amount_to"] >= $arrProduct[$j]["price"]) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						}

						// 購入回数チェック
						if ($arrPromotion[$i]["count_from"] 
							|| $arrPromotion[$i]["count_to"]) {
							// 購入回数取得
							$chkCnt = $this->getProductsBuyCount($arrPromotion[$i]["promotion_cd"]);
							// 定期の場合は、かならず１回目
							if ($arrProduct[$j]["regular_flg"] == 
								REGULAR_PURCHASE_FLG_ON) {
								// コース区分が全体ではない場合
								if ($arrPromotion[$i]["course_kbn"] != 
									PROMOTION_COURSE_KBN_ALL) {
									$chkCnt = 1;
								}
							}
							if ($arrPromotion[$i]["count_from"] 
								&& $arrPromotion[$i]["count_to"]) {
								if ($arrPromotion[$i]["count_from"] <= $chkCnt
									&& $arrPromotion[$i]["count_to"] >= $chkCnt) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["count_from"]) {
								if ($arrPromotion[$i]["count_from"] <= $chkCnt) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["count_to"]) {
								if ($arrPromotion[$i]["count_to"] >= $chkCnt) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							}
						}
						// 適用可能回数チェック
						if ($arrPromotion[$i]["use_count"]) {
							// 適用回数取得
							$chkCnt = $this->getPromotionBuyCount("'".$arrPromotion[$i]["promotion_cd"]."'");
							// 定期の場合は、かならず１回目
							if ($arrProduct[$j]["regular_flg"] == 
								REGULAR_PURCHASE_FLG_ON) {
								// コース区分が全体ではない場合
								if ($arrPromotion[$i]["course_kbn"] != 
									PROMOTION_COURSE_KBN_ALL) {
									$chkCnt = 1;
								}
							}
							if ($arrPromotion[$i]["use_count"] >= $chkCnt) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						}
					// 明細単位でのチェック時
					} else if ($arrPromotion[$i]["quantity_kbn"] == PROMOTION_QUANTITY_KBN_ALL) {
						// 購入数量チェック
						if ($arrPromotion[$i]["quantity_from"] && $arrPromotion[$i]["quantity_to"]) {
							if ($arrPromotion[$i]["quantity_from"] <= $promotionQuantity
								&& $arrPromotion[$i]["quantity_to"] >= $promotionQuantity) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						} else if ($arrPromotion[$i]["quantity_from"]) {
							if ($arrPromotion[$i]["quantity_from"] <= $promotionQuantity) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						} else if ($arrPromotion[$i]["quantity_to"]) {
							if ($arrPromotion[$i]["quantity_to"] >= $promotionQuantity) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						}

						// 購入金額チェック
						if ($arrPromotion[$i]["amount_from"] && $arrPromotion[$i]["amount_to"]) {
							if ($arrPromotion[$i]["amount_from"] <= $promotionPrice
								&& $arrPromotion[$i]["amount_to"] >= $promotionPrice) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						} else if ($arrPromotion[$i]["amount_from"]) {
							if ($arrPromotion[$i]["amount_from"] <= $promotionPrice) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						} else if ($arrPromotion[$i]["amount_to"]) {
							if ($arrPromotion[$i]["amount_to"] >= $promotionPrice) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						}

						// 購入回数チェック
						if ($arrPromotion[$i]["count_from"] 
							|| $arrPromotion[$i]["count_to"]) {
							// 購入回数取得
							$chkCnt = $this->getProductsBuyCount($arrPromotion[$i]["promotion_cd"]);
							// 定期の場合は、かならず１回目
							if ($arrProduct[$j]["regular_flg"] == 
								REGULAR_PURCHASE_FLG_ON) {
								// コース区分が全体ではない場合
								if ($arrPromotion[$i]["course_kbn"] != 
									PROMOTION_COURSE_KBN_ALL) {
									$chkCnt = 1;
								}
							}
							if ($arrPromotion[$i]["count_from"] 
								&& $arrPromotion[$i]["count_to"]) {
								if ($arrPromotion[$i]["count_from"] <= $chkCnt
									&& $arrPromotion[$i]["count_to"] >= $chkCnt) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["count_from"]) {
								if ($arrPromotion[$i]["count_from"] <= $chkCnt) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["count_to"]) {
								if ($arrPromotion[$i]["count_to"] >= $chkCnt) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							}
						}
						// 適用可能回数チェック
						if ($arrPromotion[$i]["use_count"]) {
							// 適用回数取得
							$chkCnt = $this->getPromotionBuyCount($promotionProducts);
							// 定期の場合は、かならず１回目
							if ($arrProduct[$j]["regular_flg"] == 
								REGULAR_PURCHASE_FLG_ON) {
								// コース区分が全体ではない場合
								if ($arrPromotion[$i]["course_kbn"] != 
									PROMOTION_COURSE_KBN_ALL) {
									$chkCnt = 1;
								}
							}
							if ($arrPromotion[$i]["use_count"] >= $chkCnt) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						}
					}
					if ($chkFlg) {
						if (is_array($arrSet)) {
							if (array_search($arrPromotion[$i]["promotion_cd"], $arrSet)) {
								// 既にセット済みの場合はセットしない
								break;
							}
						}
						// プロモーションコード保持
						$arrSet[$arrPromotion[$i]["promotion_cd"]] = $arrPromotion[$i]["promotion_cd"];
						$arrInclude["promotion_cd"] = $arrPromotion[$i]["promotion_cd"];
						$arrInclude["product_cd"] = $arrPromotion[$i]["include_product_cd"];
						$arrInclude["quantity"] = $arrPromotion[$i]["include_quantity"];
						$arrInclude["deliv_judgment"] = $arrPromotion[$i]["include_deliv_judgment"];
						// 同梱品情報をSESSIONにセット
						$_SESSION["INCLUDE_PROMOTION"][] = $arrInclude;
					}
					break;
				}
				$oldPromotion = $arrPromotion[$i]["promotion_cd"];
			}
		}

		// 適用したプロモーションコードをSESSIONに保持
		if (is_array($arrSet)) {
			if (is_array($_SESSION["ORDER_PROMOTION_CD"])) {
				$_SESSION["ORDER_PROMOTION_CD"] = array_merge($_SESSION["ORDER_PROMOTION_CD"], $arrSet);
				$_SESSION["ORDER_PROMOTION_CD"] = array_unique($_SESSION["ORDER_PROMOTION_CD"]);
			} else {
				$_SESSION["ORDER_PROMOTION_CD"] = $arrSet;
			}
			$res = true;
		}

		return $res;
	}

    /**
     * 送料無料キャンペーンチェックする
     *
     * @param integer $productTypeId 商品種別ID
     * @return boolean 送料無料の場合 true
     */
	function isDelivFreeCampaign($productTypeId)
	{

		$arrData = array();
		$sqlProducts = "";
		$arrCnt = array();

		// 商品最大数を取得
		$productsCnt = $this->getMax($productTypeId);
		for ($i = 0; $i <= $productsCnt; $i++) {
			// カート商品確認
			if (isset($this->cartSession[$productTypeId][$i]["cart_no"])
				&& $this->cartSession[$productTypeId][$i]["cart_no"] != "") {

				// 定期購入期間
				if (isset($this->cartSession[$productTypeId][$i]["course_cd"])) {
					$courseCd = sprintf("%d"
						, $this->cartSession[$productTypeId][$i]["course_cd"]);
				} else {
					$courseCd = 0;
				}
				// お届間隔にて配列生成
				if (!isset($arrCnt[$courseCd])) {
					$arrCnt[$courseCd] = 0;
				}
				$dataCnt = $arrCnt[$courseCd];

				// 商品コード
				$arrData[$courseCd][$dataCnt]["product_code"] = 
					$this->cartSession[$productTypeId][$i]["productsClass"]["product_code"];
				// 購入数量
				$arrData[$courseCd][$dataCnt]["quantity"] = 
					$this->cartSession[$productTypeId][$i]["quantity"];
				// 購入金額
				$arrData[$courseCd][$dataCnt]["price"] = 
					$this->cartSession[$productTypeId][$i]["total_inctax"];
				// 単品定期区分
				$arrData[$courseCd][$dataCnt]["regular_flg"] = 
					$this->cartSession[$productTypeId][$i]["regular_flg"];
				// SQL用商品コード編集
				if ($arrData[$courseCd][$dataCnt]["product_code"]) {
					if ($sqlProducts) {
						$sqlProducts .= ",";
					}
					// 商品コードSQLように加工
					$sqlProducts .= sprintf("'%s'"
								, $arrData[$courseCd][$dataCnt]["product_code"]);
				}
				$arrCnt[$courseCd]++;
			}
		}

		// 定期情報編集時
		if ($this->getKey() == CART_REGULAR_KEY) {
			// 送料無料確認
			$arrDelivData = $this->getDelivFreePromotionRegular($sqlProducts);
			if ($_SESSION["REGULAR_CAMPAIGN_CODE"]) {
				// キャンペーンコード入力時は入力用も取得
				if ($_SESSION["CAMPAIGN_CODE"]) {
					$arrCPDelivData = $this->getDelivFreePromotionCampaignRegular($sqlProducts);
					if (isset($arrDelivData[0]["promotion_cd"])) {
						if (isset($arrCPDelivData[0]["promotion_cd"])) {
							for ($i = 0; $i < count($arrCPDelivData); $i++) {
								$arrDelivData[] = $arrCPDelivData[$i];
							}
						}
					} else {
						if (isset($arrCPDelivData[0]["promotion_cd"])) {
							$arrDelivData = $arrCPDelivData;
						}
					}
				}
			}
		} else {
			// 送料無料確認
			$arrDelivData = $this->getDelivFreePromotion($sqlProducts);
			// キャンペーンコード入力時は入力用も取得
			if ($_SESSION["CAMPAIGN_CODE"]) {
				$arrCPDelivData = $this->getDelivFreePromotionCampaign($sqlProducts);
				if (isset($arrDelivData[0]["promotion_cd"])) {
					if (isset($arrCPDelivData[0]["promotion_cd"])) {
						for ($i = 0; $i < count($arrCPDelivData); $i++) {
							$arrDelivData[] = $arrCPDelivData[$i];
						}
					}
				} else {
					if (isset($arrCPDelivData[0]["promotion_cd"])) {
						$arrDelivData = $arrCPDelivData;
					}
				}
			}
		}

		// 送料無料対象がない場合、送料必要
		if (!isset($arrDelivData[0]["promotion_cd"])) {
			return false;
		}

		// 定期情報編集の場合
		if ($this->getKey() == CART_REGULAR_KEY) {
			// 送料無料対象チェック
			return $this->checkDelivFreeCampaignRegular($arrDelivData, $arrData);
		} else {
			// 送料無料対象チェック
			return $this->checkDelivFreeCampaign($arrDelivData, $arrData);
		}

	}

    /**
     * キャンペーンコードからプロモーション取得
     *
     * @param integer $productsCode 商品コード
     * @return array  プロモーション情報
     */
	function getDelivFreePromotionCampaign($productsCode)
	{
		$objQuery =& SC_Query_Ex::getSingletonInstance();

		$fmt =<<<EOF
SELECT 
	OP.product_cd as product_cd,
	PR.*
FROM 
	dtb_promotion PR
INNER JOIN 
	dtb_promotion_order_product OP
	ON PR.promotion_cd = OP.promotion_cd
	AND OP.del_flg = 0
LEFT JOIN 
	dtb_promotion_order_kbn OK
	ON PR.promotion_cd = OK.promotion_cd
	AND OK.del_flg = 0
INNER JOIN 
	dtb_promotion_media PM
	ON PR.promotion_cd = PM.promotion_cd
	AND PM.del_flg = 0
INNER JOIN 
	dtb_planning PL
	ON PM.media_code = PL.media_code
	AND PL.del_flg = 0
WHERE 
	PR.del_flg = 0
AND 
	PL.campaign_code = '%s'
AND 
	(PL.start_date IS NULL 
	OR date_format(PL.start_date, '%%Y%%m%%d') <= date_format(now(), '%%Y%%m%%d'))
AND 
	(PL.end_date IS NULL 
	OR date_format(PL.end_date, '%%Y%%m%%d') >= date_format(now(), '%%Y%%m%%d'))
AND 
	PR.promotion_kbn = 2
AND 
	PR.valid_kbn = 1
AND 
	(PR.valid_from IS NULL
	OR date_format(PR.valid_from, '%%Y%%m%%d') <= date_format(now(), '%%Y%%m%%d'))
AND 
	(PR.valid_to IS NULL
	OR date_format(PR.valid_to, '%%Y%%m%%d') >= date_format(now(), '%%Y%%m%%d'))
AND 
	PR.deliv_fee_kbn = 1
AND 
	(OK.order_kbn = %s OR OK.order_kbn IS NULL)
AND 
	OP.product_cd IN (%s)
EOF;

		// プローモーション用受注区分取得
		$orderKbn = $this->getPromotionOrderKbn();

		//$sql = sprintf($fmt, $_SESSION["CAMPAIGN_CODE"]
		//					, PROMOTION_ORDER_KBN_WEB, $productsCode);

		$sql = sprintf($fmt, $_SESSION["CAMPAIGN_CODE"]
							, $orderKbn, $productsCode);

		$arrData = $objQuery->getAll($sql);

		return $arrData;
	}

    /**
     * キャンペーンコードなしのプロモーション取得
     *
     * @param integer $productsCode 商品コード
     * @return array  プロモーション情報
     */
	function getDelivFreePromotion($productsCode)
	{
		$objQuery =& SC_Query_Ex::getSingletonInstance();

		$fmt =<<<EOF
SELECT 
	OP.product_cd as product_cd,
	PR.*
FROM 
	dtb_promotion PR
INNER JOIN 
	dtb_promotion_order_product OP
	ON PR.promotion_cd = OP.promotion_cd
	AND OP.del_flg = 0
LEFT JOIN 
	dtb_promotion_order_kbn OK
	ON PR.promotion_cd = OK.promotion_cd
	AND OK.del_flg = 0
WHERE 
	PR.promotion_cd NOT IN (
		SELECT 
			promotion_cd 
		FROM 
			dtb_promotion_media
		WHERE 
			del_flg = 0
		)
AND 
	PR.del_flg = 0
AND 
	PR.promotion_kbn = 2
AND 
	PR.valid_kbn = 1
AND 
	(PR.valid_from IS NULL
	OR date_format(PR.valid_from, '%%Y%%m%%d') <= date_format(now(), '%%Y%%m%%d'))
AND 
	(PR.valid_to IS NULL
	OR date_format(PR.valid_to, '%%Y%%m%%d') >= date_format(now(), '%%Y%%m%%d'))
AND 
	PR.deliv_fee_kbn = 1
AND 
	(OK.order_kbn = %s OR OK.order_kbn IS NULL)
AND 
	OP.product_cd IN (%s)
EOF;

		// プローモーション用受注区分取得
		$orderKbn = $this->getPromotionOrderKbn();

		//$sql = sprintf($fmt, PROMOTION_ORDER_KBN_WEB, $productsCode);
		$sql = sprintf($fmt, $orderKbn, $productsCode);

		$arrData = $objQuery->getAll($sql);

		return $arrData;
	}

    /**
     * 送料無料キャンペーンチェック
     *
     * @param array  $arrPromotion プロモーション情報
     * @param array  $arrAllProduct   商品情報
     * @return boolean 送料無料の場合 true
     */
	function checkDelivFreeCampaign($arrPromotion, $arrAllProduct)
	{

		$res = false;

		$objQuery =& SC_Query_Ex::getSingletonInstance();

		$oldPromotion = "";
		foreach ($arrAllProduct as $arrProduct) {
			for ($i = 0; $i < count($arrPromotion); $i++) {
				$chkFlg = 2;
				// プロモーションが異なる場合初期化
				if ($oldPromotion != $arrPromotion[$i]["promotion_cd"]) {
					$promotionQuantity = 0;
					$promotionPrice = 0;
					$promotionProducts = "";
				}
				for ($j = 0; $j < count($arrProduct); $j++) {
					// 商品コードが同一のみチェック対象
					if ($arrPromotion[$i]["product_cd"] != $arrProduct[$j]["product_code"]) {
						continue;
					}
					// コース区分確認
					if ($arrPromotion[$i]["course_kbn"] != $arrProduct[$j]["regular_flg"]
						&& $arrPromotion[$i]["course_kbn"] != PROMOTION_COURSE_KBN_ALL) {
						continue;
					}

					$promotionQuantity += $arrProduct[$j]["quantity"];
					$promotionPrice += $arrProduct[$j]["price"];
					if ($promotionProducts) {
						$promotionProducts .= ",";
					}
					$promotionProducts .= sprintf("'%s'", $arrProduct[$j]["product_code"]);

					// 明細単位でのチェック時
					if ($arrPromotion[$i]["quantity_kbn"] == PROMOTION_QUANTITY_KBN_DETAIL) {
						// 購入数量チェック
						if ($arrPromotion[$i]["quantity_from"] && $arrPromotion[$i]["quantity_to"]) {
							if ($arrPromotion[$i]["quantity_from"] <= $arrProduct[$j]["quantity"]
								&& $arrPromotion[$i]["quantity_to"] >= $arrProduct[$j]["quantity"]) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						} else if ($arrPromotion[$i]["quantity_from"]) {
							if ($arrPromotion[$i]["quantity_from"] <= $arrProduct[$j]["quantity"]) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						} else if ($arrPromotion[$i]["quantity_to"]) {
							if ($arrPromotion[$i]["quantity_to"] >= $arrProduct[$j]["quantity"]) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						}

						// 購入金額チェック
						if ($arrPromotion[$i]["amount_from"] && $arrPromotion[$i]["amount_to"]) {
							if ($arrPromotion[$i]["amount_from"] <= $arrProduct[$j]["price"]
								&& $arrPromotion[$i]["amount_to"] >= $arrProduct[$j]["price"]) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						} else if ($arrPromotion[$i]["amount_from"]) {
							if ($arrPromotion[$i]["amount_from"] <= $arrProduct[$j]["price"]) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						} else if ($arrPromotion[$i]["amount_to"]) {
							if ($arrPromotion[$i]["amount_to"] >= $arrProduct[$j]["price"]) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						}

						// 購入回数チェック
						if ($arrPromotion[$i]["count_from"] 
							|| $arrPromotion[$i]["count_to"]) {
							// 購入回数取得
							$chkCnt = $this->getProductsBuyCount($arrPromotion[$i]["promotion_cd"]);
							// 定期の場合は、かならず１回目
							if ($arrProduct[$j]["regular_flg"] == 
								REGULAR_PURCHASE_FLG_ON) {
								// コース区分が全体ではない場合
								if ($arrPromotion[$i]["course_kbn"] != 
									PROMOTION_COURSE_KBN_ALL) {
									$chkCnt = 1;
								}
							}
							if ($arrPromotion[$i]["count_from"] 
								&& $arrPromotion[$i]["count_to"]) {
								if ($arrPromotion[$i]["count_from"] <= $chkCnt
									&& $arrPromotion[$i]["count_to"] >= $chkCnt) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["count_from"]) {
								if ($arrPromotion[$i]["count_from"] <= $chkCnt) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["count_to"]) {
								if ($arrPromotion[$i]["count_to"] >= $chkCnt) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							}
						}
						// 適用可能回数チェック
						if ($arrPromotion[$i]["use_count"]) {
							// 適用回数取得
							$chkCnt = $this->getPromotionBuyCount("'".$arrPromotion[$i]["promotion_cd"]."'");
							// 定期の場合は、かならず１回目
							if ($arrProduct[$j]["regular_flg"] == 
								REGULAR_PURCHASE_FLG_ON) {
								// コース区分が全体ではない場合
								if ($arrPromotion[$i]["course_kbn"] != 
									PROMOTION_COURSE_KBN_ALL) {
									$chkCnt = 1;
								}
							}
							if ($arrPromotion[$i]["use_count"] >= $chkCnt) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						}
					// 明細単位でのチェック時
					} else if ($arrPromotion[$i]["quantity_kbn"] == PROMOTION_QUANTITY_KBN_ALL) {
						// 購入数量チェック
						if ($arrPromotion[$i]["quantity_from"] && $arrPromotion[$i]["quantity_to"]) {
							if ($arrPromotion[$i]["quantity_from"] <= $promotionQuantity
								&& $arrPromotion[$i]["quantity_to"] >= $promotionQuantity) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						} else if ($arrPromotion[$i]["quantity_from"]) {
							if ($arrPromotion[$i]["quantity_from"] <= $promotionQuantity) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						} else if ($arrPromotion[$i]["quantity_to"]) {
							if ($arrPromotion[$i]["quantity_to"] >= $promotionQuantity) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						}

						// 購入金額チェック
						if ($arrPromotion[$i]["amount_from"] && $arrPromotion[$i]["amount_to"]) {
							if ($arrPromotion[$i]["amount_from"] <= $promotionPrice
								&& $arrPromotion[$i]["amount_to"] >= $promotionPrice) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						} else if ($arrPromotion[$i]["amount_from"]) {
							if ($arrPromotion[$i]["amount_from"] <= $promotionPrice) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						} else if ($arrPromotion[$i]["amount_to"]) {
							if ($arrPromotion[$i]["amount_to"] >= $promotionPrice) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						}

						// 購入回数チェック
						if ($arrPromotion[$i]["count_from"] 
							|| $arrPromotion[$i]["count_to"]) {
							// 購入回数取得
							$chkCnt = $this->getProductsBuyCount($arrPromotion[$i]["promotion_cd"]);
							// 定期の場合は、かならず１回目
							if ($arrProduct[$j]["regular_flg"] == 
								REGULAR_PURCHASE_FLG_ON) {
								// コース区分が全体ではない場合
								if ($arrPromotion[$i]["course_kbn"] != 
									PROMOTION_COURSE_KBN_ALL) {
									$chkCnt = 1;
								}
							}
							if ($arrPromotion[$i]["count_from"] 
								&& $arrPromotion[$i]["count_to"]) {
								if ($arrPromotion[$i]["count_from"] <= $chkCnt
									&& $arrPromotion[$i]["count_to"] >= $chkCnt) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["count_from"]) {
								if ($arrPromotion[$i]["count_from"] <= $chkCnt) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["count_to"]) {
								if ($arrPromotion[$i]["count_to"] >= $chkCnt) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							}
						}
						// 適用可能回数チェック
						if ($arrPromotion[$i]["use_count"]) {
							// 適用回数取得
							$chkCnt = $this->getPromotionBuyCount($promotionProducts);
							// 定期の場合は、かならず１回目
							if ($arrProduct[$j]["regular_flg"] == 
								REGULAR_PURCHASE_FLG_ON) {
								// コース区分が全体ではない場合
								if ($arrPromotion[$i]["course_kbn"] != 
									PROMOTION_COURSE_KBN_ALL) {
									$chkCnt = 1;
								}
							}
							if ($arrPromotion[$i]["use_count"] >= $chkCnt) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						}
					}
					if ($chkFlg) {
						// 適用したプロモーションコードをSESSIONに保持
						$arrSet[$arrPromotion[$i]["promotion_cd"]] = $arrPromotion[$i]["promotion_cd"];
						if (is_array($_SESSION["ORDER_PROMOTION_CD"])) {
							$_SESSION["ORDER_PROMOTION_CD"] = array_merge($_SESSION["ORDER_PROMOTION_CD"], $arrSet);
							$_SESSION["ORDER_PROMOTION_CD"] = array_unique($_SESSION["ORDER_PROMOTION_CD"]);
						} else {
							$_SESSION["ORDER_PROMOTION_CD"] = $arrSet;
						}
						return true;
					}
					break;
				}
				$oldPromotion = $arrPromotion[$i]["promotion_cd"];
				//$arrSet[$oldPromotion] = $oldPromotion;
			}
		}

		// 適用したプロモーションコードをSESSIONに保持
		if (is_array($arrSet)) {
			if (is_array($_SESSION["ORDER_PROMOTION_CD"])) {
				$_SESSION["ORDER_PROMOTION_CD"] = array_merge($_SESSION["ORDER_PROMOTION_CD"], $arrSet);
				$_SESSION["ORDER_PROMOTION_CD"] = array_unique($_SESSION["ORDER_PROMOTION_CD"]);
			} else {
				$_SESSION["ORDER_PROMOTION_CD"] = $arrSet;
			}
			$res = true;
		}

		return $res;
	}

    /**
     * 定期用キャンペーンコードなしのプロモーション取得
     *
     * @param integer $productsCode 商品コード
     * @return array  プロモーション情報
     */
	function getDelivFreePromotionRegular($productsCode)
	{
		$objQuery =& SC_Query_Ex::getSingletonInstance();

		$fmt =<<<EOF
SELECT 
	OP.product_cd as product_cd,
	PR.*
FROM 
	dtb_promotion PR
INNER JOIN 
	dtb_promotion_order_product OP
	ON PR.promotion_cd = OP.promotion_cd
	AND OP.del_flg = 0
LEFT JOIN 
	dtb_promotion_order_kbn OK
	ON PR.promotion_cd = OK.promotion_cd
	AND OK.del_flg = 0
WHERE 
	PR.promotion_cd NOT IN (
		SELECT 
			promotion_cd 
		FROM 
			dtb_promotion_media
		WHERE 
			del_flg = 0
		)
AND 
	PR.del_flg = 0
AND 
	PR.promotion_kbn = 2
AND 
	PR.valid_kbn = 1
AND 
	(PR.valid_from IS NULL
	OR date_format(PR.valid_from, '%%Y%%m%%d') <= date_format(now(), '%%Y%%m%%d'))
AND 
	(PR.valid_to IS NULL
	OR date_format(PR.valid_to, '%%Y%%m%%d') >= date_format(now(), '%%Y%%m%%d'))
AND 
	PR.deliv_fee_kbn = 1
 %s 
AND 
	PR.course_kbn IN (%s)
AND 
	OP.product_cd IN (%s)
EOF;

		// コース区分（定期・全体）
		$courseKbn = sprintf("%s,%s", PROMOTION_COURSE_KBN_REGULAR
									, PROMOTION_COURSE_KBN_ALL);

		$whereOrderFmt = " AND (OK.order_kbn = %s OR OK.order_kbn IS NULL) ";
		$where = "";
		// 注文時のDEVICE　TYPEを適用する
		if ($_SESSION["REGULAR_DEVICE_TYPE"]) {
			// プローモーション用受注区分取得
			$orderKbn = $this->getPromotionOrderKbn($_SESSION["REGULAR_DEVICE_TYPE"]);
			$where = sprintf($whereOrderFmt, $orderKbn);
		}

		$sql = sprintf($fmt, $where, $courseKbn, $productsCode);

		$arrData = $objQuery->getAll($sql);

		return $arrData;
	}

    /**
     * 定期用キャンペーンコードからプロモーション取得
     *
     * @param integer $productsCode 商品コード
     * @return array  プロモーション情報
     */
	function getDelivFreePromotionCampaignRegular($productsCode)
	{
		$objQuery =& SC_Query_Ex::getSingletonInstance();

		$fmt =<<<EOF
SELECT 
	OP.product_cd as product_cd,
	PR.*
FROM 
	dtb_promotion PR
INNER JOIN 
	dtb_promotion_order_product OP
	ON PR.promotion_cd = OP.promotion_cd
	AND OP.del_flg = 0
LEFT JOIN 
	dtb_promotion_order_kbn OK
	ON PR.promotion_cd = OK.promotion_cd
	AND OK.del_flg = 0
INNER JOIN 
	dtb_promotion_media PM
	ON PR.promotion_cd = PM.promotion_cd
	AND PM.del_flg = 0
INNER JOIN 
	dtb_planning PL
	ON PM.media_code = PL.media_code
	AND PL.del_flg = 0
WHERE 
	PR.del_flg = 0
AND 
	PL.campaign_code = '%s'
AND 
	(PL.start_date IS NULL 
	OR date_format(PL.start_date, '%%Y%%m%%d') <= date_format(now(), '%%Y%%m%%d'))
AND 
	(PL.end_date IS NULL 
	OR date_format(PL.end_date, '%%Y%%m%%d') >= date_format(now(), '%%Y%%m%%d'))
AND 
	PR.promotion_kbn = 2
AND 
	PR.valid_kbn = 1
AND 
	(PR.valid_from IS NULL
	OR date_format(PR.valid_from, '%%Y%%m%%d') <= date_format(now(), '%%Y%%m%%d'))
AND 
	(PR.valid_to IS NULL
	OR date_format(PR.valid_to, '%%Y%%m%%d') >= date_format(now(), '%%Y%%m%%d'))
AND 
	PR.deliv_fee_kbn = 1
 %s 
AND 
	PR.course_kbn IN (%s)
AND 
	OP.product_cd IN (%s)
EOF;

		$courseKbn = sprintf("%s,%s", PROMOTION_COURSE_KBN_REGULAR
									, PROMOTION_COURSE_KBN_ALL);

		// コース区分（定期・全体）
		$courseKbn = sprintf("%s,%s", PROMOTION_COURSE_KBN_REGULAR
									, PROMOTION_COURSE_KBN_ALL);

		$whereOrderFmt = " AND (OK.order_kbn = %s OR OK.order_kbn IS NULL) ";
		$where = "";
		// 注文時のDEVICE　TYPEを適用する
		if ($_SESSION["REGULAR_DEVICE_TYPE"]) {
			// プローモーション用受注区分取得
			$orderKbn = $this->getPromotionOrderKbn($_SESSION["REGULAR_DEVICE_TYPE"]);
			$where = sprintf($whereOrderFmt, $orderKbn);
		}

		$sql = sprintf($fmt, $_SESSION["CAMPAIGN_CODE"], $where
							, $courseKbn, $productsCode);

		$arrData = $objQuery->getAll($sql);

		return $arrData;
	}

    /**
     * 送料無料キャンペーンチェック
     *
     * @param array  $arrPromotion プロモーション情報
     * @param array  $arrAllProduct   商品情報
     * @return boolean 送料無料の場合 true
     */
	function checkDelivFreeCampaignRegular($arrPromotion, $arrAllProduct)
	{

		$res = false;

		$objQuery =& SC_Query_Ex::getSingletonInstance();

		$oldPromotion = "";
		foreach ($arrAllProduct as $arrProduct) {
			for ($i = 0; $i < count($arrPromotion); $i++) {
				$chkFlg = 2;
				// プロモーションが異なる場合初期化
				if ($oldPromotion != $arrPromotion[$i]["promotion_cd"]) {
					$promotionQuantity = 0;
					$promotionPrice = 0;
					$promotionProducts = "";
				}
				for ($j = 0; $j < count($arrProduct); $j++) {
					// 商品コードが同一のみチェック対象
					if ($arrPromotion[$i]["product_cd"] != $arrProduct[$j]["product_code"]) {
						continue;
					}
					// コース区分確認
					if ($arrPromotion[$i]["course_kbn"] != $arrProduct[$j]["regular_flg"]
						&& $arrPromotion[$i]["course_kbn"] != PROMOTION_COURSE_KBN_ALL) {
						continue;
					}

					$promotionQuantity += $arrProduct[$j]["quantity"];
					$promotionPrice += $arrProduct[$j]["price"];
					if ($promotionProducts) {
						$promotionProducts .= ",";
					}
					$promotionProducts .= sprintf("'%s'", $arrProduct[$j]["product_code"]);

					// 明細単位でのチェック時
					if ($arrPromotion[$i]["quantity_kbn"] == PROMOTION_QUANTITY_KBN_DETAIL) {
						// 購入数量チェック
						if ($arrPromotion[$i]["quantity_from"] && $arrPromotion[$i]["quantity_to"]) {
							if ($arrPromotion[$i]["quantity_from"] <= $arrProduct[$j]["quantity"]
								&& $arrPromotion[$i]["quantity_to"] >= $arrProduct[$j]["quantity"]) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						} else if ($arrPromotion[$i]["quantity_from"]) {
							if ($arrPromotion[$i]["quantity_from"] <= $arrProduct[$j]["quantity"]) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						} else if ($arrPromotion[$i]["quantity_to"]) {
							if ($arrPromotion[$i]["quantity_to"] >= $arrProduct[$j]["quantity"]) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						}

						// 購入金額チェック
						if ($arrPromotion[$i]["amount_from"] && $arrPromotion[$i]["amount_to"]) {
							if ($arrPromotion[$i]["amount_from"] <= $arrProduct[$j]["price"]
								&& $arrPromotion[$i]["amount_to"] >= $arrProduct[$j]["price"]) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						} else if ($arrPromotion[$i]["amount_from"]) {
							if ($arrPromotion[$i]["amount_from"] <= $arrProduct[$j]["price"]) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						} else if ($arrPromotion[$i]["amount_to"]) {
							if ($arrPromotion[$i]["amount_to"] >= $arrProduct[$j]["price"]) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						}

						// 購入回数チェック
						if ($arrPromotion[$i]["count_from"] 
							|| $arrPromotion[$i]["count_to"]) {
							// 購入回数取得
							$chkCnt = $this->getProductsBuyCount($arrPromotion[$i]["promotion_cd"]);
							if ($arrPromotion[$i]["count_from"] 
								&& $arrPromotion[$i]["count_to"]) {
								if ($arrPromotion[$i]["count_from"] <= $chkCnt
									&& $arrPromotion[$i]["count_to"] >= $chkCnt) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["count_from"]) {
								if ($arrPromotion[$i]["count_from"] <= $chkCnt) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["count_to"]) {
								if ($arrPromotion[$i]["count_to"] >= $chkCnt) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							}
						}
						// 適用可能回数チェック
						if ($arrPromotion[$i]["use_count"]) {
							// 適用回数取得
							$chkCnt = $this->getPromotionBuyCount("'".$arrPromotion[$i]["promotion_cd"]."'");
							if ($arrPromotion[$i]["use_count"] >= $chkCnt) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						}
					// 明細単位でのチェック時
					} else if ($arrPromotion[$i]["quantity_kbn"] == PROMOTION_QUANTITY_KBN_ALL) {
						// 購入数量チェック
						if ($arrPromotion[$i]["quantity_from"] && $arrPromotion[$i]["quantity_to"]) {
							if ($arrPromotion[$i]["quantity_from"] <= $promotionQuantity
								&& $arrPromotion[$i]["quantity_to"] >= $promotionQuantity) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						} else if ($arrPromotion[$i]["quantity_from"]) {
							if ($arrPromotion[$i]["quantity_from"] <= $promotionQuantity) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						} else if ($arrPromotion[$i]["quantity_to"]) {
							if ($arrPromotion[$i]["quantity_to"] >= $promotionQuantity) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						}

						// 購入金額チェック
						if ($arrPromotion[$i]["amount_from"] && $arrPromotion[$i]["amount_to"]) {
							if ($arrPromotion[$i]["amount_from"] <= $promotionPrice
								&& $arrPromotion[$i]["amount_to"] >= $promotionPrice) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						} else if ($arrPromotion[$i]["amount_from"]) {
							if ($arrPromotion[$i]["amount_from"] <= $promotionPrice) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						} else if ($arrPromotion[$i]["amount_to"]) {
							if ($arrPromotion[$i]["amount_to"] >= $promotionPrice) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						}

						// 購入回数チェック
						if ($arrPromotion[$i]["count_from"] 
							|| $arrPromotion[$i]["count_to"]) {
							// 購入回数取得
							$chkCnt = $this->getProductsBuyCount($arrPromotion[$i]["promotion_cd"]);
							if ($arrPromotion[$i]["count_from"] 
								&& $arrPromotion[$i]["count_to"]) {
								if ($arrPromotion[$i]["count_from"] <= $chkCnt
									&& $arrPromotion[$i]["count_to"] >= $chkCnt) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["count_from"]) {
								if ($arrPromotion[$i]["count_from"] <= $chkCnt) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							} else if ($arrPromotion[$i]["count_to"]) {
								if ($arrPromotion[$i]["count_to"] >= $chkCnt) {
									$chkFlg = 1;
								} else {
									$chkFlg = 0;
									break;
								}
							}
						}
						// 適用可能回数チェック
						if ($arrPromotion[$i]["use_count"]) {
							// 適用回数取得
							$chkCnt = $this->getPromotionBuyCount($promotionProducts);
							if ($arrPromotion[$i]["use_count"] >= $chkCnt) {
								$chkFlg = 1;
							} else {
								$chkFlg = 0;
								break;
							}
						}
					}
					if ($chkFlg) {
						// 適用したプロモーションコードをSESSIONに保持
						$arrSet[$arrPromotion[$i]["promotion_cd"]] = $arrPromotion[$i]["promotion_cd"];
						if (is_array($_SESSION["ORDER_PROMOTION_CD"])) {
							$_SESSION["ORDER_PROMOTION_CD"] = array_merge($_SESSION["ORDER_PROMOTION_CD"], $arrSet);
							$_SESSION["ORDER_PROMOTION_CD"] = array_unique($_SESSION["ORDER_PROMOTION_CD"]);
						} else {
							$_SESSION["ORDER_PROMOTION_CD"] = $arrSet;
						}
						return true;
					}
					break;
				}
				$oldPromotion = $arrPromotion[$i]["promotion_cd"];
			}
		}

		// 適用したプロモーションコードをSESSIONに保持
		if (is_array($arrSet)) {
			if (is_array($_SESSION["ORDER_PROMOTION_CD"])) {
				$_SESSION["ORDER_PROMOTION_CD"] = array_merge($_SESSION["ORDER_PROMOTION_CD"], $arrSet);
				$_SESSION["ORDER_PROMOTION_CD"] = array_unique($_SESSION["ORDER_PROMOTION_CD"]);
			} else {
				$_SESSION["ORDER_PROMOTION_CD"] = $arrSet;
			}
			$res = true;
		}

		return $res;
	}

    /**
     * 購入回数取得
     *
     * @param integer  $promotionCd プロモーションコード
     * @return integer 購入回数
     */
	function getProductsBuyCount($promotionCd)
	{
		$objQuery =& SC_Query_Ex::getSingletonInstance();

		$fmt =<<<EOF
SELECT count(ANS.cnt)
FROM (
	SELECT 
		count(*) as cnt
	FROM 
		dtb_order O
	INNER JOIN 
		dtb_order_detail D
		ON O.order_id = D.order_id
	WHERE 
		O.del_flg = 0
	AND
		O.status NOT IN (%s)
	AND
		O.customer_id = '%s'
	AND
		D.product_code IN (
			SELECT product_cd 
			FROM dtb_promotion_order_product
			WHERE promotion_cd = '%s'
		)
	GROUP BY O.order_id
) ANS
EOF;

		$cnt = 0;
		if (isset($_SESSION["customer"]["customer_id"])) {
			$orderStatus = sprintf("%s,%s", ORDER_PENDING, ORDER_CANCEL);
			$sql = sprintf($fmt, $orderStatus, $_SESSION["customer"]["customer_id"]
								, $promotionCd);
			$cnt = $objQuery->getOne($sql);
		}

		$cnt++;

		return $cnt;
	}

    /**
     * 適用回数取得
     *
     * @param integer  $promotionCd プロモーションコード
     * @return integer 購入回数
     */
	function getPromotionBuyCount($promotionCd)
	{
		$objQuery =& SC_Query_Ex::getSingletonInstance();

		$fmt =<<<EOF
SELECT 
	count(*) as cnt
FROM 
	dtb_order O
INNER JOIN 
	dtb_order_promotion P
	ON O.order_id = P.order_id
WHERE 
	O.del_flg = 0
AND
	O.status NOT IN (%s)
AND
	O.customer_id = '%s'
AND
	date_format(O.create_date, '%%Y%%m%%d') >= date_format(date_add(now(), INTERVAL -%d YEAR), '%%Y%%m%%d')
AND
	P.promotion_cd IN (%s)
EOF;

		$cnt = 0;
		if (isset($_SESSION["customer"]["customer_id"])) {
			$orderStatus = sprintf("%s,%s", ORDER_PENDING, ORDER_CANCEL);
			$sql = sprintf($fmt, $orderStatus, $_SESSION["customer"]["customer_id"]
								, PROMOTION_USE_YEAR, $promotionCd);

			$cnt = $objQuery->getOne($sql);
		}
		$cnt++;

		return $cnt;
	}

    /**
     * カートの内容を計算する.
     *
     * カートの内容を計算し, 下記のキーを保持する連想配列を返す.
     *
     * - tax: 税額
     * - subtotal: カート内商品の小計
     * - deliv_fee: カート内商品の合計送料
     * - total: 合計金額
     * - payment_total: お支払い合計
     * - add_point: 加算ポイント
     *
     * @param integer $productTypeId 商品種別ID
     * @param SC_Customer $objCustomer ログイン中の SC_Customer インスタンス
     * @param integer $use_point 今回使用ポイント
     * @param integer|array $deliv_pref 配送先都道府県ID.
                                        複数に配送する場合は都道府県IDの配列
     * @param integer $charge 手数料
     * @param integer $discount 値引
     * @param integer $deliv_id 配送業者ID
     * @return array カートの計算結果の配列
     */
    function calculate($productTypeId, &$objCustomer, $use_point = 0,
                       $deliv_pref = "", $charge = 0, $discount = 0, $deliv_id = 0) {
        $objDb = new SC_Helper_DB_Ex();

        $total_point = $this->getAllProductsPoint($productTypeId);
        $results['tax'] = $this->getAllProductsTax($productTypeId);
        $results['subtotal'] = $this->getAllProductsTotal($productTypeId);
        $results['discount'] = $this->getAllProductsDiscountTotal($productTypeId, $use_point);
        $results['deliv_fee'] = 0;

        // 商品ごとの送料を加算
        if (OPTION_PRODUCT_DELIV_FEE == 1) {
            $cartItems = $this->getCartList($productTypeId);
            foreach ($cartItems as $item) {
                $results['deliv_fee'] += $item['productsClass']['deliv_fee'] * $item['quantity'];
            }
        }

        // SHOPマスタ情報取得
        $arrInfo = $objDb->sfGetBasisData();

		// カート画面に送料を表示するための対応
        $results['deliv_fee'] += $arrInfo['deliv_fee'];

        // 送料無料チェック
        if ($this->isDelivFree($productTypeId)) {
            $results['deliv_fee'] = 0;
        }

        // 冷凍・冷蔵品は送料にプラス\300（送料無料でもプラス）
        // 冷凍・冷蔵品が含まれるかどうか判断
        $cartItems = $this->getCartList($productTypeId);
        foreach ($cartItems as $item) {
            if ($item['productsClass']['deliv_kbn2'] == COOL_KBN_REIZOU
                || $item['productsClass']['deliv_kbn2'] == COOL_KBN_REITOU) {

                // クール便送料加算
                $results['deliv_fee'] += $arrInfo['cool_deliv_fee'];
                break;
            }
        }

        // 合計を計算
        $results['total'] = $results['subtotal'];
        $results['total'] += $results['deliv_fee'];
        $results['total'] += $charge;
        $results['total'] -= $discount;
        $results['total'] -= $results['discount'];

        // お支払い合計
        //$results['payment_total'] = $results['total'] - $use_point * POINT_VALUE;
        $results['payment_total'] = $results['total'];

        // 加算ポイントの計算
        if (USE_POINT !== false) {
            $results['add_point'] = SC_Helper_DB_Ex::sfGetAddPoint($total_point,
                                                                   $use_point);
            if($objCustomer != "") {
                // 誕生日月であった場合
                if($objCustomer->isBirthMonth()) {
                    $results['birth_point'] = BIRTH_MONTH_POINT;
                    $results['add_point'] += $results['birth_point'];
                }
            }
            if($results['add_point'] < 0) {
                $results['add_point'] = 0;
            }
        }
        return $results;
    }

    /**
     * カートが保持するキー(商品種別ID)を配列で返す.
     *
     * @return array 商品種別IDの配列
     */
    function getKeys() {
        $keys = array_keys($this->cartSession);
        // 数量が 0 の商品種別は削除する
        foreach ($keys as $key) {
            $quantity = $this->getTotalQuantity($key);
            if ($quantity < 1) {
                unset($this->cartSession[$key]);
            }
        }
        return array_keys($this->cartSession);
    }

    /**
     * カートに設定された現在のキー(商品種別ID)を登録する.
     *
     * @param integer $key 商品種別ID
     * @return void
     */
    function registerKey($key) {
        $_SESSION['cartKey'] = $key;
    }

    /**
     * カートに設定された現在のキー(商品種別ID)を削除する.
     *
     * @return void
     */
    function unsetKey() {
        unset($_SESSION['cartKey']);
    }

    /**
     * カートに設定された現在のキー(商品種別ID)を取得する.
     *
     * @return integer 商品種別ID
     */
    function getKey() {
        return $_SESSION['cartKey'];
    }

    /**
     * 複数配送扱いかどうか.
     *
     * @return boolean カートが複数配送扱いの場合 true
     */
    function isMultiple() {
        return count($this->getKeys()) > 1;
    }

    /**
     * 引数の商品種別の商品がカートに含まれるかどうか.
     *
     * @param integer $product_type_id 商品種別ID
     * @return boolean 指定の商品種別がカートに含まれる場合 true
     */
    function hasProductType($product_type_id) {
        return in_array($product_type_id, $this->getKeys());
    }

    /**
     * メール便判定を行う
     *
     * @param $productTypeId 商品種別ID
     * @return boolean メール便の場合 true
     */
    function checkMailDelivery($productTypeId) {
        $result = false;
        $cartItems = $this->getCartList($productTypeId);
        $sum_deliv_judgment = 0.0;

        // 商品IDごとに数量・送料区分・送料無料個数を取得
        foreach ($cartItems as $item) {

            if (isset($item['productsClass']['deliv_judgment'])) {
                $sum_deliv_judgment +=
                    $item['productsClass']['deliv_judgment'] * $item['quantity'];
            } else {
                // 宅配便
                $result = false;
            }
        }

        // 同梱品も係数を加算
        if (isset($_SESSION['INCLUDE_PROMOTION'])) {
            foreach ($_SESSION['INCLUDE_PROMOTION'] as $item) {
                $sum_deliv_judgment += $item["deliv_judgment"] * $item["quantity"];
            }
        }

        // 1.0未満であればメール便
        if ($sum_deliv_judgment < 1.0) {
            $result = true;
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * 同時購入チェックを行う
     *
     * @param  array  $cartItems カート内商品情報の配列
     * @return string 同時購入あり：エラーメッセージ
     *                同時購入なし：空白
     */
    function checkSimultaneousPurchase($cartItems) {
        // 産直品判定用
        $dropshipment_err = "";
        $normal_product_name = "";
        $arrDropShipProduct = array();
        $arrNormalShipProduct = array();
        // 冷蔵冷凍区分判定用(通常、冷蔵、冷凍)
        $cool_normal_name = "";
        $cool_reizou_name = "";
        $cool_reitou_name = "";
        $arrCoolNormal = array();
        $arrCoolReizou = array();
        $arrCoolReitou = array();

        // 配送区分判定用(通常、ワレモノ、生もの)
        $deliv_normal_name = "";
        $deliv_waremono_name = "";
        $deliv_namamono_name = "";
        $arrDelivNormal = array();
        $arrDelivWaremono = array();
        $arrDelivNamamono = array();

        foreach($cartItems[1] as $key => $item) {

            // 通常商品と産直商品を抽出
            if ($item['productsClass']['drop_shipment']
                == DROP_SHIPMENT_FLG_ON) {

                $arrDropShipProduct[] = $item['productsClass']['name'];
                // エラー用メッセージを成形
                $dropshipment_err .=
                    '「' . $item['productsClass']['name'] . '」、';
            } else {
                $arrNormalShipProduct[] = $item['productsClass']['name'];
                // エラー用メッセージを成形
                $normal_product_name .=
                    '「' . $item['productsClass']['name'] . '」、';
            }

            // 通常・冷蔵・冷凍商品を抽出
            if ($item['productsClass']['deliv_kbn2']
                == COOL_KBN_REIZOU) {

                $arrCoolReizou[] = $item['productsClass']['name'];
                // エラー用メッセージを成形
                $cool_reizou_name .=
                    '「' . $item['productsClass']['name'] . '」、';
            } else if ($item['productsClass']['deliv_kbn2']
                == COOL_KBN_REITOU) {

                $arrCoolReitou[] = $item['productsClass']['name'];
                // エラー用メッセージを成形
                $cool_reitou_name .=
                    '「' . $item['productsClass']['name'] . '」、';
            } else {
                $arrCoolNormal[] = $item['productsClass']['name'];
                // エラー用メッセージを成形
                $cool_normal_name .=
                    '「' . $item['productsClass']['name'] . '」、';
            }

            // 通常・われもの・なまものを抽出
            if ($item['productsClass']['deliv_kbn1']
                == DELIV_KBN_BREAKABLES) {

                $arrDelivWaremono[] = $item['productsClass']['name'];
                // エラー用メッセージを成形
                $deliv_waremono_name .=
                    '「' . $item['productsClass']['name'] . '」、';

            } else if ($item['productsClass']['deliv_kbn1']
                == DELIV_KBN_PERISHABLES) {

                $arrDelivNamamono[] = $item['productsClass']['name'];
                // エラー用メッセージを成形
                $deliv_namamono_name .=
                    '「' . $item['productsClass']['name'] . '」、';
            } else {
                $arrDelivNormal[] = $item['productsClass']['name'];
                // エラー用メッセージを成形
                $deliv_normal_name .=
                    '「' . $item['productsClass']['name'] . '」、';
            }
        }

        // 産直品判定
        if (count($arrNormalShipProduct) > 0
            && count($arrDropShipProduct) > 0) {
            
            // エラー用メッセージを成形
            $dropshipment_err = rtrim($dropshipment_err, "、");
            $dropshipment_err .= 'は産直品のため、';
            $normal_product_name = rtrim($normal_product_name, "、");
            $dropshipment_err .= $normal_product_name;
            $dropshipment_err .= "と同時購入は出来ません。\n";

            return $dropshipment_err;

        }

        // 冷蔵冷凍区分判定
        if (count($arrCoolNormal) > 0
            && count($arrCoolReizou) > 0) {
            
            // エラー用メッセージを成形
            $cool_reizou_name = rtrim($cool_reizou_name, "、");
            $cool_reizou_name .= 'は冷蔵品のため、';
            $cool_normal_name = rtrim($cool_normal_name, "、");
            $cool_reizou_name .= $cool_normal_name;
            $cool_reizou_name .= "と同時購入は出来ません。\n";
            return $cool_reizou_name;

        } else if (count($arrCoolNormal) > 0
            && count($arrCoolReitou) > 0) {

            // エラー用メッセージを成形
            $cool_reitou_name = rtrim($cool_reitou_name, "、");
            $cool_reitou_name .= 'は冷凍品のため、';
            $cool_normal_name = rtrim($cool_normal_name, "、");
            $cool_reitou_name .= $cool_normal_name;
            $cool_reitou_name .= "と同時購入は出来ません。\n";
            return $cool_reitou_name;

        } else if (count($arrCoolReitou) > 0
            && count($arrCoolReizou) > 0) {

            return "冷蔵品と冷凍品の同時購入はできません。\n";
        }

        // 配送区分判定
        if (count($arrDelivNormal) > 0
            && count($arrDelivWaremono) > 0) {
            
            // エラー用メッセージを成形
            $deliv_waremono_name = rtrim($deliv_waremono_name, "、");
            $deliv_waremono_name .= 'はワレモノ商品のため、';
            $deliv_normal_name = rtrim($deliv_normal_name, "、");
            $deliv_waremono_name .= $deliv_normal_name;
            $deliv_waremono_name .= "と同時購入は出来ません。\n";
            return $deliv_waremono_name;

        } else if (count($arrDelivNormal) > 0
            && count($arrDelivNamamono) > 0) {

            // エラー用メッセージを成形
            $deliv_namamono_name = rtrim($deliv_namamono_name, "、");
            $deliv_namamono_name .= 'はなまもの商品のため、';
            $deliv_normal_name = rtrim($deliv_normal_name, "、");
            $deliv_namamono_name .= $deliv_normal_name;
            $deliv_namamono_name .= "と同時購入は出来ません。\n";
            return $deliv_namamono_name;

        } else if (count($arrDelivWaremono) > 0
            && count($arrDelivNamamono) > 0) {

            return "なまもの商品とワレモノ商品の同時購入はできません。\n";
        }

        return "";
    }

    /**
     * カート内に冷蔵・冷凍商品が含まれるかをチェック
     *
     * @param  array   $cartItems カート内商品情報の配列
     * @return boolean 冷凍冷蔵あり：true  なし：false
     */
    function checkCoolKbnProduct ($cartItems) {

        $result = false;

        foreach($cartItems[1] as $key => $item) {
            if ($item['productsClass']['deliv_kbn2'] == COOL_KBN_REIZOU
                || $item['productsClass']['deliv_kbn2'] == COOL_KBN_REITOU) {
                
                $result = true;
                break;
            }
        }
        return $result;
    }

    /**
     * 社員購入グループCDが設定されている商品かチェック 
     *
     * @param  array  $cartItems   カート内商品情報の配列
     * @return string 未設定商品あり：エラーメッセージ
     *                未設定商品なし：空白
     */
    function checkEmployeeProducts($cartItems) {

        // 初期値定義
        $err_msg = "";

        // カート商品をループ
        foreach ($cartItems as $arrItem) {
            foreach ($arrItem as $arrProduct) {

                // グループCDが設定されていればスキップ
                if ($this->existEmployeeProduct($arrProduct["productsClass"]["employee_sale_cd"])) {
                    continue;
                }
                // メッセージを生成
		/*
                $err_msg = "カート内商品「". $arrProduct["productsClass"]["name"]
                         . "」は、社員購入グループコードを設定していない為、購入できません。";
		 */
                $err_msg = "カート内商品「". $arrProduct["productsClass"]["name"]
                         . "」は、社員販売対象外商品となります。";
                return $err_msg;
            }
        }
        return $err_msg;
    }

    /**
     * 社員グループCDが設定されている商品か判定 
     *
     * @param  integer $target_cd 社員購入グループCD
     * @return true：設定されている
     *        false：設定されていない
     */
    function existEmployeeProduct($target_cd) {
        $objPurchase = new SC_Helper_Purchase_Ex();

        // 社員購入グループ情報マスタ取得
        $arrEmployeeSaleCdList = $objPurchase->getEmployeeSaleCdList();

        foreach ($arrEmployeeSaleCdList as $employee_sale_cd) {

            // 一致した場合
            if ($target_cd == $employee_sale_cd) {
                return true;
            }
        }
        return false;
    }

    /**
     * 当月の購入制限チェックを行う
     *
     * @param  object $objCustomer 顧客情報オブジェクト
     * @param  array  $cartItems   カート内商品情報の配列
     * @return string 購入超過あり：エラーメッセージ
     *                購入超過なし：空白
     */
    function checkLimitPurchaseThisMonth(&$objCustomer, $cartItems) {
        $objPurchase = new SC_Helper_Purchase_Ex();

        // 初期値定義
        $err_msg = "";

        // 社員購入グループ情報マスタ取得
        $arrEmployeeSale = $objPurchase->getEmployeeSale();

        // 当月の購入履歴を取得
        $customer_id = $objCustomer->getValue('customer_id');
        $arrHistoryCnt = $objPurchase->getOrderThisMonth($customer_id);

        // 当月の購入履歴を減算
        $arrEmployeeSale = $this->subtractHistoryCnt($arrHistoryCnt, $arrEmployeeSale);

        // カート商品を社員購入グループ別に取得
        $arrOrderCnt = $this->getOrderCnt($cartItems);

        // 数量を集計
        $arrOrderCnt = $this->calcByCnt($arrOrderCnt);

        // 購入制限チェック
        $err_msg = $this->checkEmployeeOrderLimit($arrOrderCnt, $arrEmployeeSale);

        return $err_msg;
    }

    /**
     * 社員の当月購入可能数を文言で取得
     *
     * @param  object $objCustomer 顧客情報配列
     * @param  array  $cartItems   カート内商品情報の配列
     * @return string $msg：当月購入可能数の文言
     */
    function getEmployeeOrderLimitMsgThisMonth(&$objCustomer, $cartItems) {
        $objPurchase = new SC_Helper_Purchase_Ex();

        // 初期値定義
        $msg = "";

        // 社員以外は、メッセージ不要
        $customer_kbn = $objCustomer->getValue('customer_kbn');
        if ($customer_kbn != CUSTOMER_KBN_EMPLOYEE) {
            return $msg;
        }
        // 社員購入グループ情報マスタ取得
        $arrEmployeeSale = $objPurchase->getEmployeeSale();

        // カート商品を社員購入グループ別に取得
        $arrOrderCnt = $this->getOrderCnt($cartItems);

        // 当月の購入履歴を取得
        $customer_id   = $objCustomer->getValue('customer_id');
        $arrHistoryCnt = $objPurchase->getOrderThisMonth($customer_id);

        // 数量を加算
        $arrAllCnt = $this->addByCnt($arrHistoryCnt, $arrOrderCnt);

        // 文言生成 
        $msg = $this->getEmployeeOrderLimitMsg($arrAllCnt, $arrEmployeeSale);

        return $msg;
    }

    /**
     * 購入可能数を文言で取得
     *
     * @param  array  $arrCnt[]["count"]：商品数量
     *                $arrCnt[]["employee_sale_cd"]：社員購入グループCD
     * @param  array  $arrEmployeeSale：社員購入グループ情報配列
     * @return string $msg：購入できる商品の文言
     */
    function getEmployeeOrderLimitMsg($arrCnt, $arrEmployeeSale) {
        // 初期値定義
        $msg = "";

        // 社員購入グループ情報配列をループ
        foreach ($arrEmployeeSale as &$arrEmpRecord) {
            foreach ($arrCnt as $arrVal) {
                if ($arrEmpRecord["employee_sale_cd"] == $arrVal["employee_sale_cd"]) {
                    // 購入制限数を減算
                    $arrEmpRecord["monthly_limit"] -= $arrVal["count"];
                }
            }
            // 購入可能数がマイナスになる事はない
            if ($arrEmpRecord["monthly_limit"] < 0) {
                $arrEmpRecord["monthly_limit"] = 0;
            }

        }
        // 文言生成
        foreach ($arrEmployeeSale as &$arrEmpRecord) {
            if ($arrEmpRecord["monthly_limit"] >= 0) {
                $msg .= $arrEmpRecord["employee_sale_name"]. "はあと" 
                     .  $arrEmpRecord["monthly_limit"]. "個、";
            }
        }
        if (strlen($msg) > 0) {
            $msg .= "ご購入が可能です。";
        }
        return $msg;
    }

    /**
     * カート商品を社員購入グループ別に取得する 
     *
     * @param  array $cartItems   カート内商品情報の配列
     * @return array $arrOrderCnt：社員購入グループ毎の購入数量配列
     */
    function getOrderCnt($cartItems) {

        // 初期値定義
        $arrOrderCnt = array();
        $cnt = 0;

        // カート内の商品カテゴリIDと数量を取得
        foreach ($cartItems as $arrItem) {
            foreach ($arrItem as $arrProduct) {

                // 定期商品は計算しない
                if ($arrProduct['regular_flg'] == REGULAR_PURCHASE_FLG_ON) {
                    continue;
                }
                // 数量取得
                $arrOrderCnt[$cnt]["count"] = $arrProduct["quantity"];

                // カテゴリID取得
                $arrOrderCnt[$cnt]["employee_sale_cd"] =
                    $arrProduct["productsClass"]["employee_sale_cd"];
                $cnt++;
            }
        }
        return $arrOrderCnt;
    }

    /**
     * 社員購入グループ情報マスタから購入履歴分を減算 
     *
     * @param  array $arrHistoryCnt：履歴数量配列 
     * @param  array $arrEmployeeSale：社員購入グループ情報
     * @return array $arrEmployeeSale：減算された社員購入グループ情報
     */
    function subtractHistoryCnt($arrHistoryCnt, $arrEmployeeSale) {

        foreach ($arrEmployeeSale as $key => &$arrEmpRecord) {
            foreach ($arrHistoryCnt as $arrVal) {
                if ($arrVal["employee_sale_cd"] == $arrEmpRecord["employee_sale_cd"]) {
                    $arrEmpRecord["monthly_limit"] -= $arrVal["count"];
                    break;
                }
            }
            // 購入可能数がマイナスになる事はない
            if ($arrEmpRecord["monthly_limit"] < 0) {
                $arrEmpRecord["monthly_limit"] = 0;
            }
        }
        return $arrEmployeeSale;
    }

    /**
     * 購入数量を集計 
     *
     * @param  array $arrCnt：数量配列 
     * @return array $arrCalcCnt：集計された数量配列
     */
    function calcByCnt($arrCnt) {

        // 初期値定義
        $arrEmpSaleCd = array();
        $arrCalcCnt   = array();

        // 社員購入グループCD収集
        foreach ($arrCnt as $arrVal) {
            $arrEmpSaleCd[] = $arrVal["employee_sale_cd"];
        }
        // 重複する社員購入グループCDを消去
        $arrEmpSaleCd = array_unique($arrEmpSaleCd);
        // 採番
        sort($arrEmpSaleCd);

        foreach ($arrEmpSaleCd as $index => $employee_sale_cd) {
            foreach ($arrCnt as $arrVal) {
                // 違う社員購入グループCDはスキップ
                if ($employee_sale_cd != $arrVal["employee_sale_cd"]) {
                    continue;
                }
                // 数量を集計
                $arrCalcCnt[$index]["count"] += $arrVal["count"];
            }
            // 社員購入グループCDをセット
            $arrCalcCnt[$index]["employee_sale_cd"] = $employee_sale_cd;
        }
        return $arrCalcCnt;
    }

    /**
     * 履歴数量とカートの購入数量を加算 
     *
     * @param  array $arrHistoryCnt：履歴数量配列 
     * @param  array $arrOrderCnt  ：受注数量配列
     * @return array $arrAllCnt    ：受注総数量配列
     */
    function addByCnt($arrHistoryCnt, $arrOrderCnt) {

        // 初期値定義
        $arrEmpSaleCd = array();

        // 履歴数量の社員購入グループCD収集
        foreach ($arrHistoryCnt as $arrVal) {
            $arrEmpSaleCd[] = $arrVal["employee_sale_cd"];
        }
        // 受注数量の社員購入グループCD収集
        foreach ($arrOrderCnt as $arrVal) {
            $arrEmpSaleCd[] = $arrVal["employee_sale_cd"];
        }
        // 重複する社員購入グループCDを消去
        $arrEmpSaleCd = array_unique($arrEmpSaleCd);
        // 採番
        sort($arrEmpSaleCd);

        foreach ($arrEmpSaleCd as $index => $employee_sale_cd) {
            // 履歴
            foreach ($arrHistoryCnt as $arrVal) {
                // 違う社員購入グループCDはスキップ
                if ($employee_sale_cd != $arrVal["employee_sale_cd"]) {
                    continue;
                }
                // 数量を集計
                $arrAllCnt[$index]["count"] += $arrVal["count"];
            }
            // 受注
            foreach ($arrOrderCnt as $arrVal) {
                // 違う社員購入グループCDはスキップ
                if ($employee_sale_cd != $arrVal["employee_sale_cd"]) {
                    continue;
                }
                // 数量を集計
                $arrAllCnt[$index]["count"] += $arrVal["count"];
            }
            // 社員購入グループCDをセット
            $arrAllCnt[$index]["employee_sale_cd"] = $employee_sale_cd;
        }
        return $arrAllCnt;
    }

    /**
     * 社員購入制限チェック 
     *
     * @param  array $arrCnt[]["count"]：商品数量
     *               $arrCnt[]["employee_sale_cd"]：社員購入グループCD
     * @param  array $arrEmployeeSale：社員購入グループ情報配列
     *
     * @return none or string $msg：エラーメッセージ
     */
    function checkEmployeeOrderLimit($arrCnt, $arrEmployeeSale) {

        // 初期値定義
        $msg = "";

        // 社員購入グループ情報をループ
        foreach ($arrEmployeeSale as $arrEmp) {

            foreach ($arrCnt as $arrVal) {

                if ($arrEmp["employee_sale_cd"] != $arrVal["employee_sale_cd"]) {
                    continue;
                }
                // 購入制限をこえた場合
                if ($arrVal["count"] > $arrEmp["monthly_limit"]) {
                    // メッセージをセット
                    $msg .= "".$arrEmp["employee_sale_name"] ."が当月の購入制限数を"
                         . ($arrVal["count"] - $arrEmp["monthly_limit"])
                         . "個超えています。";
                }
            }
        }
        return $msg;
    }

    /**
     * 社員の定期商品購入チェック 
     *
     * @param  array  $cartItems   カート内商品情報の配列
     * @return string 定期商品あり：エラーメッセージ
     *                定期商品なし：空白
     */
    function checkEmployeeTeikiProducts($cartItems) {

        // 初期値定義
        $err_msg = "";

        // カート商品をループ
        foreach ($cartItems as $arrItem) {
            foreach ($arrItem as $arrProduct) {

                // グループCDが設定されていればスキップ
                if ($arrProduct["regular_flg"] == REGULAR_PURCHASE_FLG_OFF ||
                    empty($arrProduct["regular_flg"])) {
                    continue;
                }
                // メッセージを生成
                $err_msg = "カート内商品「". $arrProduct["productsClass"]["name"]
                         . "」は、定期購入できません。";
                return $err_msg;
            }
        }
        return $err_msg;
    }

    /**
     * 定期関連の情報をカートセッションへ設定する
     *
     * @param $regularInfo 定期購入情報(お届け間隔、お届け曜日)
     */
    function setRegularInfo(&$objFormParam, $productTypeId) {

        $max = $this->getMax($productTypeId);

        for ($i = 0; $i <= $max; $i++) {
            if ($this->cartSession[$productTypeId][$i]['regular_flg'] == '1') {
                // お届け間隔
                $key1 = 'course_cd' . $this->cartSession[$productTypeId][$i]['cart_no'];
                $this->cartSession[$productTypeId][$i]['course_cd'] =
                    $objFormParam->getValue($key1);

                $key2 = 'todoke_cycle' . $this->cartSession[$productTypeId][$i]['cart_no'];
                $this->cartSession[$productTypeId][$i]['todoke_cycle'] = 
                    $objFormParam->getValue($key2);

                // お届け曜日
                $key3 = 'todoke_week_no' . $this->cartSession[$productTypeId][$i]['cart_no'];
                $this->cartSession[$productTypeId][$i]['todoke_week_no'] = 
                    $objFormParam->getValue($key3);

                $key4 = 'todoke_week' . $this->cartSession[$productTypeId][$i]['cart_no'];
                $this->cartSession[$productTypeId][$i]['todoke_week'] = 
                    $objFormParam->getValue($key4);

                // お届け日指定区分
                $key5 = 'todoke_kbn' . $this->cartSession[$productTypeId][$i]['cart_no'];
                $objRegular = new SC_Helper_Regular_Ex();
                $todoke_kbn = $objRegular->getTodokeKbn($objFormParam->getValue($key3), 
                                                        $objFormParam->getValue($key4));

                $this->cartSession[$productTypeId][$i]['todoke_kbn'] = $todoke_kbn;

            }
        }
    }

    /**
     * 合計金額は10万円以上かチェック 
     *
     * @param $productTypeId 商品種別ID
     * @param $objCustomer 顧客情報オブジェクト
     * @return string 10万円以上：エラーメッセージ
     *                10万円未満：空白
     */
    function checkPaymentTotal($productTypeId, &$objCustomer) {

        // 初期値定義
        $msg = "";
        $arrPaymentTotal = array();

        // 全商品の合計金額を取得
        $arrPaymentTotal = $this->calculate($productTypeId, $objCustomer);

        // 金額を超過した場合はエラー
        if ($arrPaymentTotal['total'] >= PAYMENT_TOTAL_LIMIT) {
            $msg = "注文金額の合計が多すぎます。商品を削除、"
                 . "または数量を減らしてください。";
        }
        return $msg;
    }

    /**
     * 配送業者チェック
     *
     * @param  boolean $mailFlg true:メール便 false:宅急便
     * @param integer $productTypeId 商品種別ID
     * @return integer 配送業者
     */
    function checkDeliv($mailFlg, $productTypeId) {

		$cartItems = $this->getCartList($productTypeId);

		$delivId = DELIV_ID_YAMATO_MAIL;
		foreach($cartItems as $key => $item) {
			// メール便
			if ($mailFlg) {
				$delivId = DELIV_ID_YAMATO_MAIL;
				/* メール便佐川設定ができてから対応
				if ($item["productsClass"]["mail_deliv_id"] == "") {
					$delivId = DELIV_ID_YAMATO_MAIL;
				} else {
					$delivId = DELIV_ID_YAMATO_MAIL;
				}
				 */
			// 宅急便
			} else {
				// 産直の場合、佐川
				if ($item["productsClass"]["drop_shipment"] == DROP_SHIPMENT_FLG_ON) {
					$delivId = DELIV_ID_SAGAWA;
				} else {
					$delivId = DELIV_ID_YAMATO;
					break;
				}
			}
        }
        return $delivId;
    }

    /**
     * プローモーション受注区分取得
     *
     * @param integer WEBデバイスID
     * @return integer  基幹受付ID
     */
	function getPromotionOrderKbn($device = NULL)
	{
		$objQuery =& SC_Query_Ex::getSingletonInstance();

		if ($device) {
			$deviceType = $device;
		} else {
			// DEVICE TYPE取得
			$deviceType = SC_Display_Ex::detectDevice();
		}

		$sql =<<<EOF
SELECT
	kikan_id
FROM
	mtb_device_type
WHERE
	id = {$deviceType}
EOF;

		$kikanOrderKbn = $objQuery->getOne($sql);

		return $kikanOrderKbn;
	}

    /**
     * 定期顧客確認
     *
     * @param integer 顧客ID
     * @return boolean 定期会員の場合 true
     */
	function checkRegularCustomer($customerId)
	{
		$res = false;
		if (!$customerId) {
			return $res;
		}

		$objQuery =& SC_Query_Ex::getSingletonInstance();

		$sql =<<<EOF
SELECT
	count(*)
FROM
	dtb_regular_order
WHERE
	customer_id = ?
AND status != ?
AND del_flg = ?
EOF;

		$chkCnt = $objQuery->getOne($sql
			, array($customerId, REGULAR_ORDER_STATUS_CANCEL, INOS_DEL_FLG_OFF));

		if ($chkCnt) {
			$res = true;
		}
		return $res;
	}

}
?>
