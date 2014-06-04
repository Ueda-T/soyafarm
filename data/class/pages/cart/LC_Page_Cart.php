<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * カート のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:$
 */
class LC_Page_Cart extends LC_Page_Ex {

    // {{{ properties

    /** 商品規格情報の配列 */
    var $arrData;

    /** 動作モード */
    var $mode;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_title = "お買い物カゴ";
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrProductType = $masterData->getMasterData("mtb_product_type");

        // お届け間隔
        $this->arrCourseCd = $masterData->getMasterData("mtb_course_cd");
        $this->arrTodokeKbn = $masterData->getMasterData("mtb_todoke_kbn");
        // お届け間隔は月のみ
        unset($this->arrTodokeKbn[1]);
        // お届け曜日
        $this->arrTodokeWeekNo = $masterData->getMasterData("mtb_todoke_week");
        // XXX 既存のマスタとIDが一致しないため、独自で設定
        //$this->arrTodokeWeek = $masterData->getMasterData('mtb_wday');
        $this->arrTodokeWeek = array(1 => '日',
                                     2 => '月',
                                     3 => '火',
                                     4 => '水',
                                     5 => '木',
                                     6 => '金',
                                     7 => '土');

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
	if (isset($CLICK_ANALYZER_STATIC["cart"])) {
	    $this->tpl_clickAnalyzer = $CLICK_ANALYZER_STATIC["cart"];
	}

        $objCartSess = new SC_CartSession_Ex();
        $objSiteSess = new SC_SiteSession_Ex();
        $objCustomer = new SC_Customer_Ex();
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objFormParam = $this->lfInitParam($_REQUEST);

        $this->mode = $this->getMode();
        $this->tpl_message = "";

        // モバイル対応
        if (SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE) { 
            if (isset($_GET['cart_no'])) {
                $objFormParam->setValue('cart_no', $_GET['cart_no']); 
            }
            if (isset($_GET['cartKey'])) {
                $objFormParam->setValue('cartKey', $_GET['cartKey']);
            }
            // 次へボタン押下時
            if (isset($_POST['confirm']) && $_POST['confirm'] != null) {
                $this->mode = 'confirm';
            }
            // 再計算ボタン押下時
            if (isset($_POST['re_calc']) && $_POST['re_calc'] != null) {
                $this->mode = 're_calc';
            }
        }

        $this->cartKeys = $objCartSess->getKeys();
        foreach ($this->cartKeys as $key) {
            // 商品購入中にカート内容が変更された。
            if($objCartSess->getCancelPurchase($key)) {
                $this->tpl_message .= "商品購入中にカート内容が変更されましたので、お手数ですが購入手続きをやり直して下さい。";
            }
        }
        $this->cartItems =& $objCartSess->getAllCartList();

        $cart_no = $objFormParam->getValue('cart_no');
        $cartKey = $objFormParam->getValue('cartKey');
        $arrDelCart = $objFormParam->getValue('del_cart');
        $usePoint = $objFormParam->getValue('use_point');

        // 顧客区分取得
        $customer_kbn = $objCustomer->getValue('customer_kbn');
        $this->tpl_user_point = $objCustomer->getValue('point');

        switch($this->mode) {
        case 'confirm':
	    // プロモーション同梱品情報
	    if (isset($_SESSION["INCLUDE_PROMOTION"])) {
		unset($_SESSION["INCLUDE_PROMOTION"]);
	    }
	    // プロモーションコード情報
	    if (isset($_SESSION["ORDER_PROMOTION_CD"])) {
		unset($_SESSION["ORDER_PROMOTION_CD"]);
	    }
	    // キャンペーンコードセット
            $this->lfSetCampaignCode($objFormParam);

            // 定期関連の入力情報をカートセッションへ保存する
            $objCartSess->setRegularInfo(&$objFormParam, $cartKey);

	    // 割引プロモーションチェック
	    $objCartSess->isProductsPriceCampaign($cartKey);
	    // 同梱品プロモーションチェック
	    $objCartSess->isProductsIncludeCampaign($cartKey);

            // 入力チェック
	    $this->arrErr = $this->lfCheckError($objFormParam, $this->cartItems
				    , $objCartSess->getAllProductsTotal($cartKey));
            if(isset($this->arrErr) && !empty($this->arrErr)) { 
                break;
            }

            // 購入商品のチェック
            $this->tpl_message .= 
                $this->lfCheckPurchase($objCartSess, $this->cartItems);
            if ($this->tpl_message != "") {
                break;
            }

            // 合計金額が10万以上の場合はエラー
            $this->tpl_payment_total_err = 
                $objCartSess->checkPaymentTotal($cartKey, $objCustomer);
            if ($this->tpl_payment_total_err != "") {
                break;
            }

            // 社員の場合
            if ($customer_kbn == CUSTOMER_KBN_EMPLOYEE) {
                // 社員チェック
                $this->arrEmployeeErr =
                    $this->lfCheckEmployeeError($objCustomer,
                                                $this->cartItems);
                foreach ($this->arrEmployeeErr as $employee_err) {
                    if (strlen($employee_err) > 0) {
                        break 2;
                    }
                }
            }
	    // キャンペーンコード存在確認
	    if (!$this->lfCheckCampaignCode($_SESSION["CAMPAIGN_CODE"])) {
		    $this->tpl_order_promotion_err = true;
		    break;
	    }
            // カート内情報の取得
            //$cartList = $objCartSess->getCartList($cartKey);
            $cartList = $objCartSess->calculate($cartKey, $objCustomer, $usePoint);
            $objPurchase->saveOrderTemp($objSiteSess->getUniqId(), $cartList, $objCustomer);
            // カート商品が1件以上存在する場合
            if(count($cartList) > 0) {
                // カートを購入モードに設定
		$this->lfSetCurrentCart($objSiteSess, $objCartSess, $cartKey, $usePoint);
                // 購入ページへ
                SC_Response_Ex::sendRedirect(SHOPPING_URL);
                exit;
            }
            break;
        case 'up'://1個追加
	    // キャンペーンコードセット
	    $this->lfSetCampaignCode($objFormParam);
            $objCartSess->upQuantity($cart_no, $cartKey);
            SC_Response_Ex::reload(array('category_id' => $objFormParam->getValue('category_id'),'use_point' => $objFormParam->getValue('use_point')), true);
            exit;
            break;
        case 'down'://1個減らす
	    // キャンペーンコードセット
	    $this->lfSetCampaignCode($objFormParam);

            $objCartSess->downQuantity($cart_no, $cartKey);
            SC_Response_Ex::reload(array('category_id' => $objFormParam->getValue('category_id'),'use_point' => $objFormParam->getValue('use_point')), true);
            exit;
            break;
        case 'setQuantity'://数量変更
            $objCartSess->setQuantity($objFormParam->getValue('quantity'), $cart_no, $cartKey);
            SC_Response_Ex::reload(array('category_id' => $objFormParam->getValue('category_id'),'use_point' => $objFormParam->getValue('use_point')), true);
            exit;
            break;
        case 'delete'://カートから削除
	    // キャンペーンコードセット
	    $this->lfSetCampaignCode($objFormParam);

	    for ($i = 0; $i < count($arrDelCart); $i++) {
		$objCartSess->delProduct($arrDelCart[$i], $cartKey);
	    }
	    if (!$arrDelCart && $cart_no) {
		$objCartSess->delProduct($cart_no, $cartKey);
	    }
            SC_Response_Ex::reload(array('category_id' => $objFormParam->getValue('category_id'),'use_point' => $objFormParam->getValue('use_point')), true);
            exit;
            break;
        case 're_calc'://再計算
	    // キャンペーンコードセット
	    $this->lfSetCampaignCode($objFormParam);
            // 入力チェック
	    $this->arrErr = $this->lfCheckError
		($objFormParam, $this->cartItems,
		 $objCartSess->getAllProductsTotal($cartKey));
	    /*
	     * 必ずプローモーションチェックを行うように変更
	    // プロモーション同梱品情報
	    if (isset($_SESSION["INCLUDE_PROMOTION"])) {
		unset($_SESSION["INCLUDE_PROMOTION"]);
	    }
	    // プロモーションコード情報
	    if (isset($_SESSION["ORDER_PROMOTION_CD"])) {
		unset($_SESSION["ORDER_PROMOTION_CD"]);
	    }
	    // 割引プロモーションチェック
	    $objCartSess->isProductsPriceCampaign($cartKey);
	    // 同梱品プロモーションチェック
	    $objCartSess->isProductsIncludeCampaign($cartKey);
	     */
            break;

        case 'set_regular'://お届け間隔変更時
            // 定期関連の入力情報をカートセッションへ保存する
            $objCartSess->setRegularInfo(&$objFormParam, $cartKey);
            break;

        default:
            // 購入商品のチェック
            $this->tpl_message .=
                $this->lfCheckPurchase($objCartSess, $this->cartItems);

	    // テンポラリテーブルより情報取得
	    $arrOrderTemp = $objPurchase->getOrderTemp($objSiteSess->getUniqId());
	    /*
	    if (is_array($arrOrderTemp)) {
		$objFormParam->setParam($arrOrderTemp);
                $usePoint = $objFormParam->getValue('use_point');
            }
	     */

            // #406 ポイントの再セット
            if (isset($_SESSION["USE_POINT"])) {
                $objFormParam->setValue("use_point", $_SESSION["USE_POINT"]);
                $usePoint = $_SESSION["USE_POINT"];
            }

            // セッションから定期情報をセットする
            $this->lfSetCartData($objFormParam, $this->cartItems);
            break;
        }

        $this->arrInfo = SC_Helper_DB_Ex::sfGetBasisData();
        $totalIncTax = 0;

        foreach ($this->cartKeys as $key) {
	    // プロモーション同梱品情報
	    if (isset($_SESSION["INCLUDE_PROMOTION"])) {
		unset($_SESSION["INCLUDE_PROMOTION"]);
	    }
	    // プロモーションコード情報
	    if (isset($_SESSION["ORDER_PROMOTION_CD"])) {
		unset($_SESSION["ORDER_PROMOTION_CD"]);
	    }
            // 定期関連の入力情報をカートセッションへ保存する
            $objCartSess->setRegularInfo(&$objFormParam, $key);
	    // 割引プロモーションチェック
	    $objCartSess->isProductsPriceCampaign($key);
	    // 同梱品プロモーションチェック
	    $objCartSess->isProductsIncludeCampaign($key);

            // カート集計処理
            $this->tpl_message .= $objCartSess->checkProducts($key);
            $this->tpl_total_inctax[$key] = $objCartSess->getAllProductsTotal($key);
            $this->tpl_discount[$key] = $objCartSess->getAllProductsDiscountTotal($key, $usePoint);
            $totalIncTax += $this->tpl_total_inctax[$key];
            $totalDiscount += $this->tpl_discount[$key];
            $this->tpl_total_tax[$key] = $objCartSess->getAllProductsTax($key);
            // ポイント合計
            $this->tpl_total_point[$key] = $objCartSess->getAllProductsPoint($key);
	    // 入力チェック
	    $this->arrErr = $this->lfCheckError
		($objFormParam, $this->cartItems,
		 $this->tpl_total_inctax[$key]);

	    if (isset($this->arrErr["use_point"])) {
		$usePoint = 0;
	    }

            $this->arrData[$key] = $objCartSess->calculate
		($key, $objCustomer, $usePoint);

            // 合計金額が10万以上の場合はメッセージをセット
            $this->tpl_payment_total_err = 
                $objCartSess->checkPaymentTotal($key, $objCustomer);

            // 送料無料チェック
            if ($objCartSess->isDelivFree($key)) {
                $this->arrData[$key]['is_deliv_free'] =
		    $objCartSess->isDelivFree($key);
            }

            // 送料無料までの金額を取得
            $free_rule = $this->lfGetFreeRule($customer_kbn);

            // 送料無料までの金額を計算
            $this->tpl_deliv_free[$key] =
		$free_rule - ($this->tpl_total_inctax[$key] - $this->tpl_discount[$key]);
        }
        // #129 在庫エラー時の再表示のため再取得
        $this->cartItems =& $objCartSess->getAllCartList();

        // 社員の場合
        if ($customer_kbn == CUSTOMER_KBN_EMPLOYEE) {
            // 社員チェック
            $this->arrEmployeeErr =
                $this->lfCheckEmployeeError($objCustomer, $this->cartItems);
            // 当月の購入可能な商品メッセージを表示
            $this->tpl_employee_order_msg = 
                $objCartSess->getEmployeeOrderLimitMsgThisMonth
		($objCustomer, $this->cartItems);
        }
        //商品の合計金額をセット
        $this->tpl_all_total_inctax = $totalIncTax;
        $this->tpl_all_total_discount = $totalDiscount;
        $this->tpl_category_id = $objFormParam->getValue('category_id');

        // 顧客区分をセット
        $this->tpl_customer_kbn = $customer_kbn;

        // ログイン判定
        if($objCustomer->isLoginSuccess(true)) {
            $this->tpl_login = true;
            $this->tpl_user_point = $objCustomer->getValue('point');
            $this->tpl_user_point_valid_date = $objCustomer->getValue('point_valid_date');
            $this->tpl_user_birth_point = $objCustomer->getValue('birth_point');
            $this->tpl_user_birth_point_valid_date = $objCustomer->getValue('birth_point_valid_date');
            $this->tpl_name = $objCustomer->getValue('name');
        }

        // 前頁のURLを取得
        // TODO: SC_CartSession::setPrevURL()利用不可。
        $this->lfGetCartPrevUrl($_SESSION,$_SERVER['HTTP_REFERER']);
        $this->tpl_prev_url = (isset($_SESSION['cart_prev_url'])) ? $_SESSION['cart_prev_url'] : '';

	// ポイント情報がある場合
	if ($_SESSION["USE_POINT"]) {
	    $objFormParam->setValue("use_point", $_SESSION["USE_POINT"]);
	}
        // フォームへデータをセットする
        $this->arrForm = $objFormParam->getFormParamList();

        // 定期購入判定
        $objRegular = new SC_Helper_Regular_Ex();
        $this->tpl_regular_purchase_flg =
            $objRegular->checkRegularPurchase($this->cartItems);

	// 定期商品にお届け間隔が1ヶ月指定が含まれているかチェック
	if ($this->lfCheckRegularOneMonth($this->cartItems)) {
	    // 定期間隔を1ヶ月以外除外する
	    unset($this->arrCourseCd[3]);
	    unset($this->arrCourseCd[2]);
	}

	$this->tpl_order_promotion_err = false;
	$this->tpl_input_campaign_ok_flg = false;
	if ($_SESSION["CAMPAIGN_CODE"]) {
	    // キャンペーンコードをTPL用変数にセット
	    $this->tpl_campaign_code = $_SESSION["CAMPAIGN_CODE"];
	    // キャンペーンコード存在確認
	    if (!$this->lfCheckCampaignCode($_SESSION["CAMPAIGN_CODE"])) {
		$this->tpl_order_promotion_err = true;
	    }
	    // キャンペーンコード適用確認
	    if ($this->lfCheckCampaignCodeTekiyo($_SESSION["CAMPAIGN_CODE"])) {
		$this->tpl_input_campaign_ok_flg = true;
	    }
	}

	// 同梱品情報取得
	$this->tpl_include_product_flg = false;
        $this->arrIncludeProduct = $objPurchase->getIncludeProducts();
        if (is_array($this->arrIncludeProduct)) {
	    $this->tpl_include_product_flg = true;
	}

	// タグセット
	$this->lfSetTag($this);
    }

    /**
     * ユーザ入力値の処理
     *
     * @return object
     */
    function lfInitParam($arrRequest) {
        $objFormParam = new SC_FormParam_Ex();
        $objFormParam->addParam("カートキー", 'cartKey',
            INT_LEN, 'n', array('NUM_CHECK',"MAX_LENGTH_CHECK"));
        $objFormParam->addParam("カートナンバー", "cart_no",
            INT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        // PC版での値引き継ぎ用
        $objFormParam->addParam("カテゴリID", "category_id",
            INT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"));
	// カートから削除用
        $objFormParam->addParam("削除カートナンバー", "del_cart",
            INT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        // スマートフォン版での数量変更用
        $objFormParam->addParam("数量", 'quantity',
            INT_LEN, 'n', array("ZERO_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));

	// キャンペーンコード
        $objFormParam->addParam("キャンペーンコード", 'campaign_code',
            CAMPAIGN_CODE_LEN, 'a',
            array("NO_SPTAB", "ALNUM_CHECK", "MAX_LENGTH_CHECK"));

	// ポイント
        $objFormParam->addParam("ポイント", "use_point", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK", "ZERO_START"));

        // 定期関連の項目
        $objCartSess = new SC_CartSession_Ex();
        $cartItems =& $objCartSess->getAllCartList();
	$chkCartNo = "";
	$baseCourseCd = "";
	$baseTodokeCycle = "";
        foreach($cartItems[1] as $item) {
            if ($item['regular_flg'] == REGULAR_PURCHASE_FLG_ON) {
		if ($chkCartNo) {
		    $arrRequest['course_cd' . $item['cart_no']] = $baseCourseCd;
		    $arrRequest['todoke_cycle' . $item['cart_no']] = $baseTodokeCycle;
		} else {
		    $chkCartNo = $item['cart_no'];
		    $baseCourseCd = $arrRequest['course_cd' . $item['cart_no']];
		    $baseTodokeCycle = $arrRequest['todoke_cycle' . $item['cart_no']];
		}
                $objFormParam->addParam(
                    "お届け間隔", 'course_cd' . $item['cart_no'],
                    INT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"));
                $objFormParam->addParam(
                    "お届け間隔(日・月)", 'todoke_cycle' . $item['cart_no'],
                    INT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"));
                $objFormParam->addParam(
                    "お届け日指定区分", 'todoke_kbn' . $item['cart_no'],
                    INT_LEN, 'n', array("MAX_LENGTH_CHECK"));
                $objFormParam->addParam(
                    "お届け曜日", 'todoke_week_no' . $item['cart_no'],
                    INT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"));
                $objFormParam->addParam(
                    "お届け曜日", 'todoke_week' . $item['cart_no'],
                    INT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"));
            }
        }

        // 値の取得
        $objFormParam->setParam($arrRequest);
        // 入力値の変換
        $objFormParam->convParam();
        return $objFormParam;
    }

    /**
     * order_temp_id の更新
     *
     * @return
     */
    function lfUpdateOrderTempid($pre_uniqid,$uniqid, $point){
        $sqlval['use_point'] = intval($point);
        $sqlval['order_temp_id'] = $uniqid;
        $where = "order_temp_id = ?";
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $res = $objQuery->update
            ("dtb_order_temp", $sqlval, $where, array($pre_uniqid));
        if($res != 1){
            return false;
        }
        return true;
    }

    /**
     * 前頁のURLを取得
     *
     * @return void
     */
    function lfGetCartPrevUrl(&$session,$referer){
        if (!preg_match("/cart/", $referer)) {
	    if (!empty($session['cart_referer_url'])
		&& (preg_match("/entry/", $referer)
		|| preg_match("/shopping/", $referer))) {
                $session['cart_prev_url'] = $session['cart_referer_url'];
            } else {
		if (SC_Utils_Ex::isBlank($referer)) {
		    $session['cart_referer_url'] = HTTP_URL;
		    $session['cart_prev_url'] = HTTP_URL;
		} else {
		    $session['cart_referer_url'] = $referer;
		    $session['cart_prev_url'] = $referer;
		}
            }
        }
        // 妥当性チェック
        if (!SC_Utils_Ex::sfIsInternalDomain($session['cart_prev_url'])) {
            $session['cart_prev_url'] = '';
        }
    }

    /**
     * カートを購入モードに設定
     *
     * @return void
     */
    function lfSetCurrentCart(&$objSiteSess, &$objCartSess, $cartKey, $point){
        // 正常に登録されたことを記録しておく
        $objSiteSess->setRegistFlag();
        $pre_uniqid = $objSiteSess->getUniqId();
        // 注文一時IDの発行
        $objSiteSess->setUniqId();
        $uniqid = $objSiteSess->getUniqId();
        // エラーリトライなどで既にuniqidが存在する場合は、設定を引き継ぐ
        if($pre_uniqid != "") {
            $this->lfUpdateOrderTempid($pre_uniqid,$uniqid, $point);
        }
        // カートを購入モードに設定
        $objCartSess->registerKey($cartKey);
        $objCartSess->saveCurrentCart($uniqid, $cartKey);
    }

    /**
     * 送料無料までの金額を取得する
     *
     * @param  integer $customer_kbn : dtb_customer.顧客区分
     * @return integer $free_rule 送料無料までの金額
     *
     */
    function lfGetFreeRule($customer_kbn) {

        // 社員
        if ($customer_kbn == CUSTOMER_KBN_EMPLOYEE) {
            $free_rule = $this->arrInfo['free_rule2'];
        // 社員以外
        } else {
            $free_rule = $this->arrInfo['free_rule'];
        }
        return $free_rule;
    }

    /**
     * エラーチェックを行う
     *
     *
     */
    function lfCheckError(&$objFormParam, $cartItems, $subTotal) {

        $arrParams = $objFormParam->getHashArray();
        $objErr = new SC_CheckError_Ex($arrParams);
        $objErr->arrErr = $objFormParam->checkError();

        foreach ($cartItems[1] as $key => $item) {

            // お届け間隔の入力チェック
            if ($item['regular_flg'] == REGULAR_PURCHASE_FLG_ON) {
                $objErr->doFunc(array("お届け間隔",
                    "course_cd". $item['cart_no']),array("EXIST_CHECK"));
                $objErr->doFunc(array("お届け間隔(月・日)",
                    "todoke_cycle". $item['cart_no']),array("EXIST_CHECK"));

                $key1 = 'todoke_week_no' . $item['cart_no'];
                $key2 = 'todoke_week' . $item['cart_no'];
                // 「第○」と「日～土」は片方のみの入力は不可
                if ($arrParams[$key1] != "" || $arrParams[$key2] != "") {
                    $objErr->doFunc(
                        array("お届け曜日", "todoke_week_no". $item['cart_no']),
                        array("EXIST_CHECK"));
                    $objErr->doFunc(
                        array("お届け曜日", "todoke_week". $item['cart_no']),
                        array("EXIST_CHECK"));
                }

                $key3 = 'course_cd' . $item['cart_no'];
                $key4 = 'todoke_cycle' . $item['cart_no'];
                // 「ヶ月ごと」の場合は「1～3」、
                // 「日ごと」の場合は「20～90」のみ入力可能
                if ($arrParams[$key3] != "" && $arrParams[$key4] != "") {
                    if ($arrParams[$key4] == TODOKE_CYCLE_DAY &&
                       ($arrParams[$key3] > COURSE_CD_DAY_MAX ||
                        $arrParams[$key3] < COURSE_CD_DAY_MIN)) {

                        $objErr->arrErr[$key3]  = '※お届け間隔(日ごと)は';
                        $objErr->arrErr[$key3] .= COURSE_CD_DAY_MIN . '～';
                        $objErr->arrErr[$key3] .= COURSE_CD_DAY_MAX;
                        $objErr->arrErr[$key3] .= 'で選択してください。<br />';

                        } else if ($arrParams[$key4] == TODOKE_CYCLE_MONTH &&
                                  ($arrParams[$key3] > COURSE_CD_MONTH_MAX ||
                                   $arrParams[$key3] < COURSE_CD_MONTH_MIN)) {

                        $objErr->arrErr[$key3]  = '※お届け間隔(月ごと)は';
                        $objErr->arrErr[$key3] .= COURSE_CD_MONTH_MIN . '～';
                        $objErr->arrErr[$key3] .= COURSE_CD_MONTH_MAX;
                        $objErr->arrErr[$key3] .= 'で選択してください。<br />';

                    }
                }
            }
        }
        $arrParams = $objFormParam->getHashArray();
	if ($arrParams["use_point"]) {
	    if($arrParams['use_point'] > $this->tpl_user_point) {
                $objErr->arrErr['use_point'] = "※ 入力されたポイントが保有されているポイントを超えています。";
            }
            if(($arrParams['use_point'] * POINT_VALUE) > $subTotal) {
                $objErr->arrErr['use_point'] = "※ 入力されたポイントがご購入金額を超えています。";
            }
	}
        return $objErr->arrErr;
    }

    /**
     * カートセッションからフォームへデータセット
     *
     */
    function lfSetCartData(&$objFormParam, $cartItems) {

        foreach ($cartItems[1] as $item) {

            $key1 = 'course_cd' . $item['cart_no'];
            $key2 = 'todoke_cycle' . $item['cart_no'];
            $key3 = 'todoke_week_no' . $item['cart_no'];
            $key4 = 'todoke_week' . $item['cart_no'];
            // コースCD
            if (isset($item['course_cd'])) {

                $objFormParam->setValue($key1, $item['course_cd']);

            } else if (isset($item['productsClass']['course_cd'])) {

                // 商品マスタのデフォルトコースCDをセット
                $key1 = 'course_cd' . $item['cart_no'];
                $course_cd = $item['productsClass']['course_cd'];
                $objFormParam->setValue($key1, $course_cd);

                if ($course_cd >= COURSE_CD_MONTH_MIN &&
                    $course_cd <= COURSE_CD_MONTH_MAX) {
                    // デフォルトで「ヶ月ごと」をセット
                    $objFormParam->setValue($key2, TODOKE_KBN_WEEK);
                } else {
                    // デフォルトで「日ごと」をセット
                    $objFormParam->setValue($key2, TODOKE_KBN_DAY);
                }
            }

            // お届け間隔
            if (isset($item['todoke_cycle'])) {
                $objFormParam->setValue($key2, $item['todoke_cycle']);
            }

            // 曜日指定１
            if (isset($item['todoke_week_no'])) {
                $objFormParam->setValue($key3, $item['todoke_week_no']);
            }

            // 曜日指定２
            if (isset($item['todoke_week'])) {
                $objFormParam->setValue($key4, $item['todoke_week']);
            }
        }
    }

    /**
     * 社員専用のカート商品購入チェック
     * @param $objCustomer 顧客情報
     * @param $cartItems   カート商品
     * return array $arrEmployeeErr
     */
    function lfCheckEmployeeError($objCustomer, $cartItems) {
        $objCartSess = new SC_CartSession_Ex();

        // 購入グループに属さない商品があればエラー
        $arrEmployeeErr["product_err"] =
            $objCartSess->checkEmployeeProducts($cartItems);
        
        // 当月の購入制限を超過した場合はエラー
        $arrEmployeeErr["limit_err"] =
            $objCartSess->checkLimitPurchaseThisMonth($objCustomer,
                                                      $cartItems);
        // 定期購入があればエラー
        $arrEmployeeErr["teiki_err"] =
            $objCartSess->checkEmployeeTeikiProducts($cartItems);

        return $arrEmployeeErr; 
    }

    /**
     * 購入商品についての各チェックを行う
     *
     * @param object $objCartSess SC_CartSessionのインスタンス
     * @param array  $cartItems   カート商品情報の連想配列
     * @return string エラーメッセージ
     */
    function lfCheckPurchase(&$objCartSess, $cartItems) {

        // 通常商品と産直品の同時購入チェック
        return $objCartSess->checkSimultaneousPurchase($this->cartItems);

    }

    /**
     * キャンペーンコードセット
     *
     * @param object $objFormParam objFormParamのインスタンス
     * @return void
     */
    function lfSetCampaignCode(&$objFormParam)
    {
	// モバイルの時はSESSION情報を優先する
        if (SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE) { 
	    if (isset($_SESSION["CAMPAIGN_CODE"]) 
		&& !isset($_REQUEST["campaign_code"])) {
		$objFormParam->setValue("campaign_code", $_SESSION["CAMPAIGN_CODE"]);
	    }
	    if (isset($_SESSION["USE_POINT"]) 
		&& !isset($_REQUEST["use_point"])) {
		$objFormParam->setValue("use_point", $_SESSION["USE_POINT"]);
	    }
	}
	// 入力されたキャンペーンコード取得
	$campaign_code = $objFormParam->getValue("campaign_code");
	if (preg_match('/[0-9a-zA-Z]/', $campaign_code)) {
	    if ($campaign_code) {
		$_SESSION["CAMPAIGN_CODE"] = $campaign_code;
	    } else {
		if (isset($_SESSION["CAMPAIGN_CODE"])) {
		    unset($_SESSION["CAMPAIGN_CODE"]);
		}
	    }
	} else {
	    if (!$campaign_code) {
		if (isset($_SESSION["CAMPAIGN_CODE"])) {
		    unset($_SESSION["CAMPAIGN_CODE"]);
		}
	    }
	}
	// 入力されたポイント取得
	$point = $objFormParam->getValue("use_point");
	if (preg_match('/[0-9a-zA-Z]/', $point)) {
	    if ($point) {
		$_SESSION["USE_POINT"] = $point;
	    } else {
		if (isset($_SESSION["USE_POINT"])) {
		    unset($_SESSION["USE_POINT"]);
		}
	    }
	}
    }

    /**
     * キャンペーンコード存在チェック
     *
     * @param string   $campaignCode キャンペーンコード
     * @return integer $cnt          件数
     */
    function lfCheckCampaignCode($campaignCode)
    {

	$objQuery =& SC_Query_Ex::getSingletonInstance();

	$fmt =<<<EOF
SELECT 
    count(*) as cnt
FROM 
    dtb_planning
WHERE 
    del_flg = 0
AND 
    campaign_code = '%s'
EOF;

	$sql = sprintf($fmt, $campaignCode);

	$cnt = $objQuery->getOne($sql);

	return $cnt;
    }

    /**
     * キャンペーンコード適用チェック
     *
     * @param string   $campaignCode キャンペーンコード
     * @return boolean キャンペーンコードが適用された場合 true
     */
    function lfCheckCampaignCodeTekiyo($campaignCode)
    {

	$res = false;

	// プロモーション適用がなければfalse
	if (!isset($_SESSION["ORDER_PROMOTION_CD"]) 
	    && !$_SESSION["ORDER_PROMOTION_CD"]) {
	    return $res;
	}

	$objQuery =& SC_Query_Ex::getSingletonInstance();

	$fmt =<<<EOF
SELECT 
    PM.promotion_cd
FROM 
    dtb_promotion_media PM
INNER JOIN dtb_planning PL
ON PM.media_code = PL.media_code
AND PL.del_flg = 0
WHERE 
    PM.del_flg = 0
AND 
    PL.campaign_code = '%s'
GROUP BY PM.promotion_cd
EOF;

	$sql = sprintf($fmt, $campaignCode);

	$arrData = $objQuery->getAll($sql);

	// 取得プロモーションコードが適用されたプロモーションに含まれるかチェック
	for ($i = 0; $i < count($arrData); $i++) {
	    if (array_search($arrData[$i]["promotion_cd"]
			, $_SESSION["ORDER_PROMOTION_CD"]) !== false) {
		$res = true;
		break;
	    }
	}

	return $res;
    }

    /**
     * カート内に定期商品が含まれるか判定
     *
     * @param $arrCartItems カートセッション情報の連想配列
     * @param  integer $cartKey 登録を行うカート情報のキー
     * @param boolean true:定期商品あり false:定期商品なし
     */
    function lfCheckRegularOneMonth($arrCartItems, $cartKey=1) {

	// 1ヶ月指定商品を配列にセット
	$chkProduct = explode(",", REGULAR_ONE_MONTH_PRODUCTS);
        foreach ($arrCartItems as $key => $val) {
	    foreach ($val as $item) {
		// 定期の場合
		if ($item['regular_flg'] == REGULAR_PURCHASE_FLG_ON) {
		    if (array_search($item['productsClass']['product_code']
				    , $chkProduct) !== false) {
			return true;
		    }
		}
	    }
	}
	return false;
    }

    /**
     * 各種計測タグをセット
     *
     */
    function lfSetTag(&$thisPage) {

        $objTagView = new SC_SiteView_Ex();

        // タグテンプレートパス取得
        $tag_tpl_dir = "";

        // 端末判定
        $device = SC_Display_Ex::detectDevice();
        switch ($device){
        case DEVICE_TYPE_SMARTPHONE:
                // スマートフォン
                $tag_tpl_dir = SMARTPHONE_TAG_TEMPLATE_REALDIR;
                break;
            case DEVICE_TYPE_PC:
                // PC 
                $tag_tpl_dir = TAG_TEMPLATE_REALDIR;
                break;
        }

        // 各種タグテンプレートをセット
        if (!empty($tag_tpl_dir)) {
            // BLADEタグ
            $thisPage->tpl_tag_blade =
                $objTagView->fetch($tag_tpl_dir . "blade.tpl");
            // MarketOneタグ
            $thisPage->tpl_tag_market_one =
                $objTagView->fetch($tag_tpl_dir . "market_one.tpl");
        }
    }

}
?>
