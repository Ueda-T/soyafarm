<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * 支払い方法選択 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:$
 */
class LC_Page_Shopping_Payment extends LC_Page_Ex {

    // {{{ properties

    /** フォームパラメーターの配列 */
    var $objFormParam;

    /** 会員情報のインスタンス */
    var $objCustomer;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_title = "お支払方法・お届け時間等の指定";
        $this->tpl_page_category = 'shopping';
        $masterData = new SC_DB_MasterData();
        $objCustomer = new SC_Customer_Ex();
        $objPurchase = new SC_Helper_Purchase_Ex();
        $this->customer_kbn = $objCustomer->getValue('customer_kbn');
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        // 宅配BOX選択肢
        $this->arrBoxFlg = $masterData->getMasterData('mtb_box_flg');
        // 請求書(明細書)の送付選択肢
        $this->arrIncludeKbn = array(
            INCLUDE_KBN_DOUKON => '商品と一緒に請求書(明細書)を送る',
            INCLUDE_KBN_BESSOU => '商品とは別に請求書(明細書)を送る'
        );
        // アンケート選択肢
        $this->arrPlanningData = $objPurchase->getPlanningData(PLANNING_TYPE_ENQUETE);
        // お届け間隔
        $this->arrCourseCd = $masterData->getMasterData("mtb_course_cd");
        $this->arrTodokeKbn = $masterData->getMasterData("mtb_todoke_kbn");
        // お届け曜日
        $this->arrTodokeWeekNo =
            $masterData->getMasterData("mtb_todoke_week");
        // XXX 既存のマスタとIDが一致しないため、独自で設定
        $this->arrTodokeWeek = array(1 => '日',
                                     2 => '月',
                                     3 => '火',
                                     4 => '水',
                                     5 => '木',
                                     6 => '金',
                                     7 => '土');

        // キャンペーンコード情報
        if (isset($_SESSION['CAMPAIGN_CODE']) && strlen($_SESSION['CAMPAIGN_CODE']) > 0) {
            $this->tpl_campaign_code = $_SESSION['CAMPAIGN_CODE'];
        }
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
	global $CLICK_ANALYZER_STATIC;

	// CLICK ANALYZER用埋め込み
	$this->tpl_clickAnalyzer = "";
	if (isset($CLICK_ANALYZER_STATIC["payment"])) {
	    $this->tpl_clickAnalyzer = $CLICK_ANALYZER_STATIC["payment"];
	}

        $objSiteSess = new SC_SiteSession_Ex();
        $objCartSess = new SC_CartSession_Ex();
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objCustomer = new SC_Customer_Ex();
        $objFormParam = new SC_FormParam_Ex();

        $this->is_multiple = $objPurchase->isMultiple();

        // カートの情報を取得
        $this->arrShipping = $objPurchase->getShippingTemp($this->is_multiple);

        $this->tpl_uniqid = $objSiteSess->getUniqId();
        $cart_key = $objCartSess->getKey();
        $this->cartKey = $cart_key;
        $objPurchase->verifyChangeCart($this->tpl_uniqid, $objCartSess);

        // 配送業者を取得
        $this->arrDeliv = $objPurchase->getDeliv($cart_key);
        // 2013.12.05 配送業者の選択を不可する
        //$this->is_single_deliv = $this->isSingleDeliv($this->arrDeliv);
        $this->is_single_deliv = true;

        // 会員情報の取得
        if ($objCustomer->isLoginSuccess(true) && !$_SESSION["new_customer_id"]) {
            $this->tpl_login = '1';
            $this->tpl_user_point = $objCustomer->getValue('point');
            $this->name = $objCustomer->getValue('name');
			$this->tpl_arrCustomer = $_SESSION["customer"];
			// 登録済み住所を取得
			$this->arrAddr = $objCustomer->getCustomerAddress
							($objCustomer->getValue('customer_id'));
			$this->tpl_addrmax = count($this->arrAddr);
        }

        // 戻り URL の設定
        $this->tpl_back_url = $this->getPreviousURL($objCustomer->isLoginSuccess(true), $cart_key, $this->is_multiple);

        $arrOrderTemp = $objPurchase->getOrderTemp($this->tpl_uniqid);
        // 正常に受注情報が格納されていない場合はカート画面へ戻す
        if (SC_Utils_Ex::isBlank($arrOrderTemp)) {
            $arrValues = array();
            $objPurchase->copyFromCustomer($arrValues, $objCustomer, 'shipping');
            $objPurchase->saveShippingTemp($arrValues);
            $objPurchase->saveOrderTemp($this->tpl_uniqid, $arrValues, $objCustomer);
            $arrOrderTemp = $objPurchase->getOrderTemp($this->tpl_uniqid);
            if (SC_Utils_Ex::isBlank($arrOrderTemp)) {
                SC_Response_Ex::sendRedirect(CART_URLPATH);
                exit;
            }
        }

        // カート内商品の妥当性チェック
        $this->tpl_message = $objCartSess->checkProducts($cart_key);
        if (strlen($this->tpl_message) >= 1) {
            SC_Response_Ex::sendRedirect(CART_URLPATH);
            exit;
        }

		// カート内情報取得
        $this->arrCartItems = $objCartSess->getCartList($cart_key);

        /*
         * 購入金額の取得
         * ここでは送料を加算しない
         */
        $this->arrPrices = $objCartSess->calculate($cart_key, $objCustomer);

        // お届け日一覧の取得
        $this->arrDelivDate = $objPurchase->getDelivDate($objCartSess, $cart_key);

        // 冷蔵・冷凍商品のチェック
        $this->cartItems =& $objCartSess->getAllCartList();
        $this->tpl_is_cool = 
            $objCartSess->checkCoolKbnProduct($this->cartItems);

        switch($this->getMode()) {
        /*
         * 配送業者選択時のアクション
         * モバイル端末以外の場合は, JSON 形式のデータを出力し, ajax で取得する.
         */
        case 'select_deliv':
            $this->setFormParams($objFormParam, $arrOrderTemp, true, $this->arrShipping);
            $objFormParam->setParam($_POST);
            $this->arrErr = $objFormParam->checkError();
            if (SC_Utils_Ex::isBlank($this->arrErr)) {
                $deliv_id = $objFormParam->getValue('deliv_id');
                $arrSelectedDeliv = $this->getSelectedDeliv($objPurchase, $objCartSess, $deliv_id, $arrOrderTemp["payment_total"]);
                $arrSelectedDeliv['error'] = false;
            } else {
                $arrSelectedDeliv = array('error' => true);
                $this->tpl_mainpage = 'shopping/select_deliv.tpl'; // モバイル用
            }

            if (SC_Display_Ex::detectDevice() != DEVICE_TYPE_MOBILE) {
                echo SC_Utils_Ex::jsonEncode($arrSelectedDeliv);
                exit;
            } else {
                $this->arrPayment = $arrSelectedDeliv['arrPayment'];
                $this->arrDelivTime = $arrSelectedDeliv['arrDelivTime'];
            }
            break;

        // 登録処理
        case 'confirm':
            // パラメーター情報の初期化
            $this->setFormParams($objFormParam, $_POST, false, $this->arrShipping);

            $deliv_id = $objFormParam->getValue('deliv_id');
            $arrSelectedDeliv = $this->getSelectedDeliv($objPurchase, $objCartSess, $deliv_id, $arrOrderTemp["payment_total"]);
            $this->arrPayment = $arrSelectedDeliv['arrPayment'];
            $this->arrDelivTime = $arrSelectedDeliv['arrDelivTime'];

            $this->arrErr = $this->lfCheckError($objFormParam, $this->arrPrices['subtotal']);

			if ($this->tpl_login) {
				$this->registerDeliv($objFormParam, $this->tpl_uniqid,
                                     $objPurchase, $objCustomer);
			}

            if (SC_Utils_Ex::isBlank($this->arrErr)) {
                $this->saveShippings($objFormParam, $this->arrDelivTime);
				$this->lfRegistData($this->tpl_uniqid, $objFormParam->getDbArray()
									, $objPurchase, $this->arrPayment, $objCustomer);

                // 正常に登録されたことを記録しておく
                $objSiteSess->setRegistFlag();
                // 確認ページへ移動
                SC_Response_Ex::sendRedirect(SHOPPING_CONFIRM_URLPATH);
                exit;
            } else {
                // 受注一時テーブルからの情報を格納
                $this->img_show = $arrSelectedDeliv['img_show'];
                $objFormParam->setParam($objPurchase->getOrderTemp($this->tpl_uniqid));
            }
            break;

        // 前のページに戻る
        case 'return':

            // 正常な推移であることを記録しておく
            $objSiteSess->setRegistFlag();
            SC_Response_Ex::sendRedirect(SHOPPING_URL);
            exit;
            break;

        default:
            // FIXME 前のページから戻ってきた場合は別パラメーター(mode)で処理分岐する必要があるのかもしれない
            $this->setFormParams($objFormParam, $arrOrderTemp, false, $this->arrShipping);

            // メール便判定
            $this->mail_deliv_flg = $objCartSess->checkMailDelivery($cart_key);
			// 配送業者取得
			$deliv_id = $objCartSess->checkDeliv($this->mail_deliv_flg, $cart_key);
			/*
            if ($this->mail_deliv_flg === true) {
                // 「ヤマトメール便」
                $deliv_id = DELIV_ID_YAMATO_MAIL;
                $objFormParam->setValue('deliv_id', DELIV_ID_YAMATO_MAIL);
            } else {
                // 「ヤマト運輸」
                $deliv_id = DELIV_ID_YAMATO;
                $objFormParam->setValue('deliv_id', DELIV_ID_YAMATO);
            }
			 */

            if (!SC_Utils_Ex::isBlank($deliv_id)) {
                $objFormParam->setValue('deliv_id', $deliv_id);
                $arrSelectedDeliv = $this->getSelectedDeliv($objPurchase, $objCartSess, $deliv_id, $arrOrderTemp["payment_total"]);
                $this->arrPayment = $arrSelectedDeliv['arrPayment'];
                $this->arrDelivTime = $arrSelectedDeliv['arrDelivTime'];
                $this->img_show = $arrSelectedDeliv['img_show'];
            }
            break;
        }

        // メール便判定
        $this->mail_deliv_flg = $objCartSess->checkMailDelivery($cart_key);
		// 配送業者取得
		$deliv_id = $objCartSess->checkDeliv($this->mail_deliv_flg, $cart_key);
		$objFormParam->setValue('deliv_id', $deliv_id);
		/*
        if ($this->mail_deliv_flg === true) {
            // 「ヤマトメール便」
            $deliv_id = DELIV_ID_YAMATO_MAIL;
            $objFormParam->setValue('deliv_id', DELIV_ID_YAMATO_MAIL);
        } else {
            // 「ヤマト運輸」
            $deliv_id = DELIV_ID_YAMATO;
            $objFormParam->setValue('deliv_id', DELIV_ID_YAMATO);
        }
		 */

        // モバイル用 ポストバック処理
        if (SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE
            && SC_Utils_Ex::isBlank($this->arrErr)) {
            $this->tpl_mainpage = $this->getMobileMainpage($this->is_single_deliv, $this->getMode());
        }

        // 同梱品情報取得
        $this->tpl_include_product_flg = false;
        $this->arrIncludeProduct = $objPurchase->getIncludeProducts();
        if (is_array($this->arrIncludeProduct)) {
            $this->tpl_include_product_flg = true;
        }

        // 定期購入判定
        $objRegular = new SC_Helper_Regular_Ex();
        $this->tpl_regular_flg =
            $objRegular->checkRegularPurchase($this->cartItems);

        $this->arrForm = $objFormParam->getFormParamList();
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
     * パラメーターの初期化を行い, 初期値を設定する.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param array $arrParam 設定する値の配列
     * @param boolean $deliv_only deliv_id チェックのみの場合 true
     * @param array $arrShipping 配送先情報の配列
     */
    function setFormParams(&$objFormParam, $arrParam, $deliv_only, &$arrShipping) {
        $this->lfInitParam($objFormParam, $deliv_only, $arrShipping);
        $objFormParam->setParam($arrParam);
        $objFormParam->convParam();
    }

    /**
     * パラメーター情報の初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param boolean $deliv_only 必須チェックは deliv_id のみの場合 true
     * @param array $arrShipping 配送先情報の配列
     * @return void
     */
    function lfInitParam(&$objFormParam, $deliv_only, &$arrShipping) {

        $objFormParam->addParam("配送業者", "deliv_id", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("その他お問い合わせ", 'message', LTEXT_LEN / 6, 'KVa', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("ポイントを使用する", "point_check", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"), '2');
        $objFormParam->addParam("請求書(明細書)の送付", "include_kbn", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));

        // 社員以外のキャンペーンコード未入力時
        if ((!isset($_SESSION['CAMPAIGN_CODE']) || strlen($_SESSION['CAMPAIGN_CODE']) == 0) &&
             $this->customer_kbn != CUSTOMER_KBN_EMPLOYEE) {
            $objFormParam->addParam("アンケート", "event_code", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        }
        if ($deliv_only) {
            $objFormParam->addParam("お支払い方法", "payment_id", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        } else {
            $objFormParam->addParam("お支払い方法", "payment_id", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));

            foreach ($arrShipping as $val) {
                $objFormParam->addParam("お届け時間", "deliv_time_id" . $val['shipping_id'], INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
                $objFormParam->addParam("お届け日", "deliv_date" . $val['shipping_id'], STEXT_LEN, 'KVa', array("MAX_LENGTH_CHECK"));
                // 配送時のご要望 
                $objFormParam->addParam("配送時のご要望", "box_flg" . $val['shipping_id'], STEXT_LEN, 'KVa', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
            }
        }

		// 会員の場合のみお届け先選択あり
		if ($this->tpl_login) {
			$objFormParam->addParam("その他のお届け先ID", "other_deliv_id", INT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"));
			$objFormParam->addParam("お届け先チェック", "deliv_check", INT_LEN, 'n', array("MAX_LENGTH_CHECK"));
		}

        $objFormParam->setParam($arrParam);
        $objFormParam->convParam();
    }

    /**
     * 入力内容のチェックを行なう.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param integer $subtotal 購入金額の小計
     * @return array 入力チェック結果の配列
     */
    function lfCheckError(&$objFormParam, $subtotal) {
        // 入力データを渡す。
        $arrForm =  $objFormParam->getHashArray();
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->arrErr = $objFormParam->checkError();

        if (USE_POINT === false) {
            return $objErr->arrErr;
        }

        // [代金引換時]選択時
        if ($arrForm['payment_id'] == PAYMENT_ID_DAIBIKI) {
            // [配達時のご要望]が選択されている場合はエラー
            if ($arrForm['box_flg0'] != "") {
                $objErr->arrErr['box_flg0'] = "※ お支払方法に代金引換をご指定の場合は、配達時のご要望の指定はできません。";
            }
            // 明細書[請求書]別送の場合はエラー
            if ($arrForm['include_kbn'] == INCLUDE_KBN_BESSOU) {
                $objErr->arrErr['include_kbn'] = "※ お支払方法に代金引換をご指定の場合は、"
                                               . "請求書送付方法は「商品と同封」を選択して下さい。";
            }
        }
        return $objErr->arrErr;
    }

    /**
     * お届け先チェックの値に応じて, お届け先情報を保存する.
     *
     * 会員住所がチェックされている場合は, 会員情報からお届け先を取得する.
     * その他のお届け先がチェックされている場合は, その他のお届け先からお届け先を取得する.
     * お届け先チェックの値が不正な場合は false を返す.
     *
     * @param object  $objFormParam SC_Helper_Purchase インスタンス
     * @param integer $deliv_check お届け先チェック
     * @param string $uniqid 受注一時テーブルのユニークID
     * @param SC_Helper_Purchase $objPurchase SC_Helper_Purchase インスタンス
     * @param SC_Customer $objCustomer SC_Customer インスタンス
     * @return boolean お届け先チェックの値が妥当な場合 true
     */
    function registerDeliv(&$objFormParam, $uniqid, &$objPurchase, &$objCustomer) {
        $this->log("register deliv. deliv_check=" . $deliv_check, "Debug");

        $arrForm = $objFormParam->getHashArray();
		$deliv_check = $arrForm["deliv_check"];

        $arrValues = array();
        // 会員登録住所がチェックされている場合
        if ($deliv_check == '-1') {
            $objPurchase->copyFromCustomer($arrValues, $objCustomer, 'shipping');
            $objPurchase->saveShippingTemp($arrValues);
            $objPurchase->saveOrderTemp($uniqid, $arrValues, $objCustomer);
            return true;
        }
        // 別のお届け先がチェックされている場合
        elseif ($deliv_check >= 1) {
            $objQuery =& SC_Query_Ex::getSingletonInstance();
            $arrOtherDeliv = $objQuery->getRow("*", "dtb_other_deliv",
                                               "customer_id = ? AND other_deliv_id = ?",
                                               array($objCustomer->getValue('customer_id'), $deliv_check));
            if (SC_Utils_Ex::isBlank($arrOtherDeliv)) {
                return false;
            }

            $objPurchase->copyFromOrder($arrValues, $arrOtherDeliv, 'shipping', '');
            $objPurchase->saveShippingTemp($arrValues);
            $objPurchase->saveOrderTemp($uniqid, $arrValues, $objCustomer);
            return true;
        }
        // お届け先チェックが不正な場合
        else {
            return false;
        }
    }

    /**
     * 配送情報を保存する.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param array $arrDelivTime 配送時間の配列
     */
    function saveShippings(&$objFormParam, $arrDelivTime) {
        $deliv_id = $objFormParam->getValue('deliv_id');

        /* TODO
         * SC_Purchase::getShippingTemp() で取得して,
         * リファレンスで代入すると, セッションに添字を追加できない？
         */
        foreach (array_keys($_SESSION['shipping']) as $key) {
            $shipping_id = $_SESSION['shipping'][$key]['shipping_id'];
            $time_id = $objFormParam->getValue('deliv_time_id' . $shipping_id);
            $_SESSION['shipping'][$key]['deliv_id'] = $deliv_id;
            $_SESSION['shipping'][$key]['time_id'] = $time_id;
            $_SESSION['shipping'][$key]['shipping_time'] = $arrDelivTime[$time_id];
            $_SESSION['shipping'][$key]['shipping_date'] = $objFormParam->getValue('deliv_date' . $shipping_id);
            // 配送時のご要望
            $_SESSION['shipping'][$key]['box_flg'] = $objFormParam->getValue('box_flg' . $shipping_id);
        }
    }

    /**
     * 受注一時テーブルへ登録を行う.
     *
     * @param integer $uniqid 受注一時テーブルのユニークID
     * @param array $arrForm フォームの入力値
     * @param SC_Helper_Purchase $objPurchase SC_Helper_Purchase インスタンス
     * @param array $arrPayment お支払い方法の配列
     * @param SC_Customer $objCustomer SC_Customer インスタンス
     * @return void
     */
    function lfRegistData($uniqid, $arrForm, &$objPurchase, $arrPayment, $objCustomer) {

        $arrForm['order_temp_id'] = $uniqid;
        $arrForm['update_date'] = 'Now()';

        foreach ($arrPayment as $payment) {
            if ($arrForm['payment_id'] == $payment['payment_id']) {
                $arrForm['charge'] = $payment['charge'];
                $arrForm['payment_method'] = $payment['payment_method'];
                break;
            }
        }

        // 宅配BOX選択肢をセット
        $arrForm['note'] = $this->arrBoxFlg[$arrForm['box_flg0']];

        // 箱フラグをセット
        $arrForm['deliv_box_id']
            = $this->getDelivBoxId($arrForm['deliv_id']);

        $objPurchase->saveOrderTemp($uniqid, $arrForm, $objCustomer);
    }

    /**
     * 配送業者IDから, 支払い方法, お届け時間の配列を取得する.
     *
     * 結果の連想配列の添字の値は以下の通り
     * - 'arrDelivTime' - お届け時間の配列
     * - 'arrPayment' - 支払い方法の配列
     * - 'img_show' - 支払い方法の画像の有無
     *
     * @param SC_Helper_Purchase $objPurchase SC_Helper_Purchase インスタンス
     * @param SC_CartSession $objCartSess SC_CartSession インスタンス
     * @param integer $deliv_id 配送業者ID
     * @return array 支払い方法, お届け時間を格納した配列
     */
    function getSelectedDeliv(&$objPurchase, &$objCartSess, $deliv_id, $total) {
        $arrResults = array();
        $arrResults['arrDelivTime'] = $objPurchase->getDelivTime($deliv_id);
        //$total = $objCartSess->getAllProductsTotal($objCartSess->getKey(),
        //                                           $deliv_id);
        $arrResults['arrPayment'] = $objPurchase->getPaymentsByPrice($total,
                                                                     $deliv_id);
        $arrResults['img_show'] = $this->hasPaymentImage($arrResults['arrPayment']);
        return $arrResults;
    }

    /**
     * 支払い方法の画像があるかどうか.
     *
     * @param array $arrPayment 支払い方法の配列
     * @return boolean 支払い方法の画像がある場合 true
     */
    function hasPaymentImage($arrPayment) {
        foreach ($arrPayment as $val) {
            if (!SC_Utils_Ex::isBlank($val['payment_image'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * 配送業者が1社のみかどうか.
     *
     * @param array $arrDeliv 配送業者の配列
     * @return boolean 配送業者が1社のみの場合 true
     */
    function isSingleDeliv($arrDeliv) {
        if (count($arrDeliv) == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 前に戻るボタンの URL を取得する.
     *
     * @param boolean $is_login ユーザーがログインしている場合 true
     * @param integer $product_type_id 商品種別ID
     * @param boolean $is_multiple 複数配送の場合 true
     * @return string 前に戻るボタンの URL
     */
    function getPreviousURL($is_login = false, $product_type_id, $is_multiple) {
        if ($is_multiple) {
            return MULTIPLE_URLPATH . '?from=multiple';
        }
        if ($is_login && !$_SESSION["new_customer_id"]) {
            if ($product_type_id == PRODUCT_TYPE_DOWNLOAD) {
                return CART_URLPATH;
            } else {
                return CART_URLPATH;
                //return DELIV_URLPATH;
            }
        } else {
            //return SHOPPING_URL . "?from=nonmember";
            return ENTRY_URL;
        }
    }

    /**
     * モバイル用テンプレートのパスを取得する.
     *
     * @param boolean $is_single_deliv 配送業者が1社の場合 true
     * @param string $mode フォームパラメーター 'mode' の文字列
     * @return string モバイル用テンプレートのパス
     */
    function getMobileMainpage($is_single_deliv = true, $mode) {
        switch($mode) {
        case 'select_deliv':
            return 'shopping/payment.tpl';
            break;

        case 'confirm':
        case 'return':
        default:
            if ($is_single_deliv) {
                return 'shopping/payment.tpl';
            } else {
                return 'shopping/select_deliv.tpl';
            }
        }
    }

    /**
     * 配送業者IDから箱IDを取得する
     *
     * @param integer $deliv_id 配送業者ID
     * @return 箱ID(0:宅配便、1：メール便)
     */
    function getDelivBoxId($deliv_id) {

        if ($deliv_id == DELIV_ID_YAMATO || $deliv_id == DELIV_ID_SAGAWA) {
            return DELIV_BOX_ID_TAKUHAI;
        } else if ($deliv_id == DELIV_ID_YAMATO_MAIL) {
            return DELIV_BOX_ID_MAIL;
        } else {
            return;
        }
    }
}
?>
