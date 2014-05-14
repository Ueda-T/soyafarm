<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * 入力内容確認のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Shopping_Confirm.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Shopping_Confirm extends LC_Page_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_title = "ご入力内容のご確認";
        $masterData = new SC_DB_MasterData();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrSex = $masterData->getMasterData("mtb_sex");
        $this->arrMAILMAGATYPE = $masterData->getMasterData("mtb_mail_magazine_type");
        $this->arrReminder = $masterData->getMasterData("mtb_reminder");
        $this->arrDeliv = SC_Helper_DB_Ex::sfGetIDValueList("dtb_deliv", "deliv_id", "service_name");
        $this->httpCacheControl('nocache');
        // 宅配BOX選択肢
        $this->arrBoxFlg = $masterData->getMasterData('mtb_box_flg');
        // 請求書(明細書)の送付選択肢
        $this->arrIncludeKbn = array(
            INCLUDE_KBN_BESSOU => '商品と別送(ご注文者様の住所に郵送)',
            INCLUDE_KBN_DOUKON => '商品と同封(お支払い明細書が商品に同梱されます)'
        );
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        parent::process();
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $objCartSess = new SC_CartSession_Ex();
        $objSiteSess = new SC_SiteSession_Ex();
        $objCustomer = new SC_Customer_Ex();
        $objQuery = new SC_Query_Ex();
        $objDb = new SC_Helper_DB_Ex();
        $objPurchase = new SC_Helper_Purchase_Ex();

        $this->is_multiple = $objPurchase->isMultiple();

        // 前のページで正しく登録手続きが行われた記録があるか判定
        if (!$objSiteSess->isPrePage()) {
            SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, $objSiteSess);
        }

        // ユーザユニークIDの取得と購入状態の正当性をチェック
        $this->tpl_uniqid = $objSiteSess->getUniqId();
        $objPurchase->verifyChangeCart($this->tpl_uniqid, $objCartSess);

        $this->cartKey = $objCartSess->getKey();

        // カート内商品のチェック
        $this->tpl_message = $objCartSess->checkProducts($this->cartKey);
        if (!SC_Utils_Ex::isBlank($this->tpl_message)) {
            SC_Response_Ex::sendRedirect(CART_URLPATH);
            exit;
        }

        // ログインユーザの顧客区分取得
        $customer_kbn = $objCustomer->getValue("customer_kbn");
		// 社員の場合は、プロモーション適用しない
		if ($customer_kbn == CUSTOMER_KBN_EMPLOYEE) {
			// キャンペーンコード削除
			if (isset($_SESSION["CAMPAIGN_CODE"])) {
				unset($_SESSION["CAMPAIGN_CODE"]);
			}
		}

		// プロモーション同梱品情報
		if (isset($_SESSION["INCLUDE_PROMOTION"])) {
			unset($_SESSION["INCLUDE_PROMOTION"]);
		}
		// プロモーションコード情報
		if (isset($_SESSION["ORDER_PROMOTION_CD"])) {
			unset($_SESSION["ORDER_PROMOTION_CD"]);
		}
		// 割引プロモーションチェック
		$objCartSess->isProductsPriceCampaign($this->cartKey);
		// 同梱品プロモーションチェック
		$objCartSess->isProductsIncludeCampaign($this->cartKey);

        // カートの商品を取得
        $this->arrShipping = $objPurchase->getShippingTemp($this->is_multiple);
        $this->arrCartItems = $objCartSess->getCartList($this->cartKey);

        // ▼ 2011.08.23
        // 送料は配送先ごとに計算
        if(count($this->arrShipping) > 1){
            $this->shippingData = array();
            
            foreach($this->arrShipping as $key1 => $shippingItem){
                $total = "";
                $deliv_fee = "1";
                $arrData = array();
                //$arrFee=array();
                foreach($shippingItem["shipment_item"] as $key2 => $item){
                    //$total += $item["productsClass"]["price02"] * $item["quantity"];
                    $total += $item["price"] * $item["quantity"];
                    $arrData["shipping_id"] = $item["shipping_id"];
                }

            // 配送先ごとの支払合計
            $arrData["total_price"] = $total;

            // 送料無料チェック
            if($this->ShippingDelivFree(1,$arrData["total_price"]) == true){ // true==送料無料
                $arrData["total_fee"] = 0;
            } else {
            // 配送業者の送料を加算
                if (OPTION_DELIV_FEE == 1
                    && !SC_Utils_Ex::isBlank($shippingItem["shipping_pref"])
                    && !SC_Utils_Ex::isBlank($shippingItem["deliv_id"])) {
                    $arrData["total_fee"] =
                        $objDb->sfGetDelivFee($shippingItem["shipping_pref"],
                        $shippingItem["deliv_id"]);
                }
            }
            array_push($this->shippingData,$arrData);
            }
            foreach($this->shippingData as $value) {
                $_SESSION["shipping"][$value["shipping_id"]]["fee"] = $value["total_fee"];
            }

            $delivFee = "";
            foreach($this->shippingData as $value){
                $delivFee += $value["total_fee"];
            }
        }
        // ▲ 2011.08.23

        // 合計金額
        $this->tpl_total_inctax[$this->cartKey] = $objCartSess->getAllProductsTotal($this->cartKey);
        // 税額
        $this->tpl_total_tax[$this->cartKey] = $objCartSess->getAllProductsTax($this->cartKey);
        // ポイント合計
        $this->tpl_total_point[$this->cartKey] = $objCartSess->getAllProductsPoint($this->cartKey);

        // 一時受注テーブルの読込
        $arrOrderTemp = $objPurchase->getOrderTemp($this->tpl_uniqid);

		// ディスカウントはポイントの為リセット
		$arrOrderTemp["discount"] = 0;
        // カート集計を元に最終計算
        $arrCalcResults = $objCartSess->calculate($this->cartKey, $objCustomer,
                                                  $arrOrderTemp['use_point'],
                                                  $objPurchase->getShippingPref($this->is_multiple),
                                                  $arrOrderTemp['charge'],
                                                  $arrOrderTemp['discount'],
                                                  $arrOrderTemp['deliv_id']);

        // ▼ 2011.08.23
        // 複数配送指定の場合は、各配送先の送料合計を加算
        if(count($this->arrShipping) > 1){
            if($arrCalcResults["deliv_fee"] == 0) {
                $arrCalcResults["total"] += $delivFee;
                $arrCalcResults["payment_total"] += $delivFee;
            }
            $arrCalcResults["deliv_fee"] = $delivFee;
        }
        if(count($this->arrShipping) < 2){
            $_SESSION["shipping"][0]["fee"] = $arrCalcResults["deliv_fee"];
        }
        // ▲ 2011.08.23

        $this->arrForm = array_merge($arrOrderTemp, $arrCalcResults);

        // 会員ログインチェック
        if($objCustomer->isLoginSuccess(true)) {
            $this->tpl_login = '1';
            $this->tpl_user_point = $objCustomer->getValue('point');
        }

        // 決済モジュールを使用するかどうか
        $this->use_module = $this->useModule($this->arrForm['payment_id']);

        switch($this->getMode()) {
        // 前のページに戻る
        case 'return':
            // 正常な推移であることを記録しておく
            $objSiteSess->setRegistFlag();
            SC_Response_Ex::sendRedirect(SHOPPING_PAYMENT_URLPATH);
            exit;
            break;
        case 'confirm':
            // 定期商品でお届け日指定していない場合、お届け指定可能日付の最短日付をセットする
            // お届け日一覧の取得
            $arrDelivDate = $objPurchase->getDelivDate($objCartSess, $this->cartKey);
            // 定期商品が存在するか判断
            foreach($this->arrCartItems as $key => $item) {
                if ($item['course_cd'] != ""){
                    $teiki_flg = 1;
                    break;
                }
            }
            if ($_SESSION['shipping'][0]['shipping_date'] == "" && $teiki_flg == 1){
                $_SESSION['shipping'][0]['shipping_date'] = array_shift($arrDelivDate);
            }

            /*
             * 決済モジュールで必要なため, 受注番号を取得
             */
            $this->arrForm["order_id"] = $objQuery->nextval("dtb_order_order_id");
            $_SESSION["order_id"] = $this->arrForm['order_id'];

            // 集計結果を受注一時テーブルに反映
            $objPurchase->saveOrderTemp($this->tpl_uniqid, $this->arrForm,
                                        $objCustomer);

            // 正常に登録されたことを記録しておく
            $objSiteSess->setRegistFlag();

			// 新規顧客登録時メール送信を行う
			if ($_SESSION["new_customer_id"]) {
				$mailHelper = new SC_Helper_Mail_Ex();
				$mailHelper->sfSendRegistMail($_SESSION["new_secret_key"]
											, $_SESSION["new_customer_id"]
											, SC_MobileUserAgent_Ex::isMobile());
			}

            // 決済モジュールを使用する場合
            if ($this->use_module) {
                $objPurchase->completeOrder(ORDER_PENDING);
                SC_Response_Ex::sendRedirect(SHOPPING_MODULE_URLPATH);
            }
            // 購入完了ページ
            else {
                $objPurchase->completeOrder(ORDER_NEW);
                $objPurchase->sendOrderMail($this->arrForm["order_id"]);
                SC_Response_Ex::sendRedirect(SHOPPING_COMPLETE_URLPATH);
            }
            exit;
            break;
        default:
            break;
        }
        // メール便判定
        $this->mail_deliv_flg = $objCartSess->checkMailDelivery($this->cartKey);
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
     * 決済モジュールを使用するかどうか.
     *
     * dtb_payment.memo03 に値が入っている場合は決済モジュールと見なす.
     *
     * @param integer $payment_id 支払い方法ID
     * @return boolean 決済モジュールを使用する支払い方法の場合 true
     */
    function useModule($payment_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $memo03 = $objQuery->get('memo03', 'dtb_payment', 'payment_id = ?',
                                 array($payment_id));
        return !SC_Utils_Ex::isBlank($memo03);
    }

    /**
     * 
     * 送料無料条件を満たすかどうかチェックする
     * 
     *
     * @param integer $productTypeId 商品種別ID
     * @return boolean 送料無料の場合 true
     */
    function ShippingDelivFree($productTypeId, $shipping_total) {
        $objCartSess = new SC_CartSession_Ex();
        $objDb = new SC_Helper_DB_Ex();
        
        // 送料無料の購入数が設定されている場合
        if (DELIV_FREE_AMOUNT > 0) {
            // 商品の合計数量
            $total_quantity = $objCartSess->getTotalQuantity($productTypeId);

            if($total_quantity >= DELIV_FREE_AMOUNT) {
                return true;
            }
        }

        // 送料無料条件が設定されている場合
        $arrInfo = $objDb->sfGetBasisData();
        if ($arrInfo['free_rule'] > 0) {
            // 小計が無料条件を超えている場合
            if($shipping_total >= $arrInfo['free_rule']) {
                return true;
            }
        }
        return false;
    }

}
?>
