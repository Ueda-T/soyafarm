<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 
    'page_extends/mypage/LC_Page_AbstractMypage_Ex.php';

/**
 * MyPage お届け内容変更のページクラス.
 *
 * @package Page
 * @author IQUEVE CO.,LTD.
 * @version $Id:$
 */
class LC_Page_MyPage_RegularChange extends LC_Page_AbstractMypage_Ex {


    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
	$this->tpl_mainpage = 'mypage/regular_detail.tpl';
        $this->tpl_mypageno = 'regular';
        if (SC_Display_Ex::detectDevice() === DEVICE_TYPE_MOBILE){
            $this->tpl_subtitle = 'MYページ';
        } else {
            $this->tpl_subtitle = '定期購入一覧詳細';
        }
        $this->httpCacheControl('nocache');

        $masterData         = new SC_DB_MasterData_Ex();
        $this->arrPref      = $masterData->getMasterData('mtb_pref');

        // 支払い方法の取得
        $this->arrPayment =
            SC_Helper_DB_Ex::sfGetIDValueList(
                "dtb_payment", "payment_id", "payment_method");

        // お届け間隔
        $this->arrTodokeKbn = $masterData->getMasterData('mtb_todoke_kbn');

        // お届け曜日
        $this->arrTodokeWeekNo =
            $masterData->getMasterData('mtb_todoke_week');

        // XXX 既存のマスタとIDが一致しないため、独自で設定
        //$this->arrTodokeWeek = $masterData->getMasterData('mtb_wday');
        $this->arrTodokeWeek = array(1 => '日',
                                     2 => '月',
                                     3 => '火',
                                     4 => '水',
                                     5 => '木',
                                     6 => '金',
                                     7 => '土');

        // コースCD
        $this->arrCourseCd = $masterData->getMasterData('mtb_course_cd');

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
        $_SESSION["MYPAGENO"] = $this->tpl_mypageno;

        $objRegular = new SC_Helper_Regular_Ex();
        $objFormParam = new SC_FormParam_Ex();
        $objCartSess = new SC_CartSession_Ex(CART_REGULAR_KEY);
        $objCartSess->registerKey(CART_REGULAR_KEY);

        // パラメーター情報の初期化
        $this->lfInitParam($objFormParam);
        // POST値をフォームにセット
        $this->lfSetParam($objFormParam, $_REQUEST);
        // POST値の入力文字変換
        $objFormParam->convParam();
        // 変換後のPOST値を取得
        $this->arrForm  = $objFormParam->getHashArray();

        $cart_no = $objFormParam->getValue('cart_no');
        $cartKey = $objFormParam->getValue('cartKey');
        $arrDelCart = $objFormParam->getValue('del_cart');

        // 定期受注ID
        $regular_id = $objFormParam->getValue('regular_id');
        // 行NO
        $line_no = $objFormParam->getValue('line_no');

        $this->mode = $this->getMode();

	if (SC_Utils_Ex::isBlank($this->mode)) {
	    if (empty($regular_id) || empty($line_no)) {
		SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
	    }
	} else {
	    // ブラウザの戻る対応
	    if ($this->mode != "complete" && !isset($_SESSION["REGULAR"])) {
		$this->mode = "";
	    }
	}

        // ▼モバイル対応
        if(SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE) {
            $this->mode = $this->getMode();
            if (isset($_REQUEST['return'])) {
                $this->mode = 'return';
            }
            // 商品追加
            else if (isset($_REQUEST['product_add'])) {
                $this->tpl_product_mode = 'add';
                $this->mode = 'add';
            }
        }
        // ▲モバイル対応

        switch($this->mode){
            case "confirm":
                $this->arrErr = $this->lfCheckError($objFormParam);
		// カート内商品確認
		$this->tpl_message = $this->lfCheckProducts($objCartSess);
                if(empty($this->arrErr) && !$this->tpl_message) {

                    // 次々回お届け日を再セット
                    $after_next_arrival_date =
                        $this->getAfterNextArrivalDate($objFormParam);
                    $objFormParam->setValue
                        ("after_next_arrival_date",
                         $after_next_arrival_date
                    );

                    $this->tpl_mainpage =
                        'mypage/regular_change_confirm.tpl';
                }
                break;
            case "complete":

                // 変更前情報として定期受注詳細情報を取得
		$arrBeforeRegular = $_SESSION["REGULAR"];

		// 商品情報取得
		$arrCart = $objCartSess->getAllCartList();
		$arrRegularDetail = $arrCart[PRODUCT_TYPE_NORMAL];

		// 再読み込み防止
		if (isset($_SESSION["REGULAR"])) {
		    // トランザクション開始
		    $objQuery =& SC_Query_Ex::getSingletonInstance();
		    $objQuery->begin();

		    // 定期受注明細にデータ登録
		    $arrRegular = $this->lfRegisterRegularOrderDetail(
			$regular_id, $objFormParam, $arrBeforeRegular, $arrRegularDetail);

		    // 送信フラグを未送信へセット
		    $objRegular->updateRegularOrderSendFlg($regular_id);

		    // トランザクション終了
		    $objQuery->commit();

		    // 変更完了メールの送信
		    $this->lfSendChangeRegularMail($arrBeforeRegular,
						   $arrRegular,
						   $arrRegularDetail);
		    // カート内のSESSION情報削除
		    $objCartSess->delAllProducts(PRODUCT_TYPE_NORMAL);
		    $objCartSess->unsetKey();
		    unset($_SESSION["REGULAR"]);
		    unset($_SESSION["REGULAR_CHG"]);
		    unset($_SESSION["ORDER_PROMOTION_CD"]);
		    unset($_SESSION["REGULAR_CAMPAIGN_CODE"]);
		    unset($_SESSION["REGULAR_DEVICE_TYPE"]);
		}

		// 完了ページに移動
		SC_Response_Ex::sendRedirect('regular_change_complete.php');
                //$this->tpl_mainpage = 'mypage/regular_change_complete.tpl';
		exit();

                break;
            case "return":
                break;

	    case 'up'://1個追加
		$objCartSess->upQuantity($cart_no, $cartKey);
		break;

	    case 'down'://1個減らす
		$objCartSess->downQuantity($cart_no, $cartKey);
		break;

	    case 'delete'://カートから削除

		// カートから商品を削除
		$this->tpl_message = $this->lfDelProducts
			($objCartSess, $arrDelCart, $cart_no, $cartKey);
		break;

	    case 'add_products'://カートへ追加

		$productClassId = $objFormParam->getValue('add_product_class_id');
		// カートに商品を追加
		$objCartSess->addProduct(
			$productClassId, 1, REGULAR_PURCHASE_FLG_ON);
		break;

            // 次回お届け日の変更
            case 'change_date':
                $objFormParam->setParam($_POST);
                $objFormParam->convParam();
                $this->arrErr = $this->lfCheckError($objFormParam);
                if(empty($this->arrErr)) {
                    // 次々回お届け日を再セット
                    $after_next_arrival_date =
                        $this->getAfterNextArrivalDate($objFormParam);
                    $objFormParam->setValue
                        ("after_next_arrival_date",
                         $after_next_arrival_date
                    );
                }
                break;
            // お届け間隔の変更
            case 'change_cycle':
                $objFormParam->setParam($_POST);
                $objFormParam->convParam();
                $this->arrErr = $this->lfCheckError($objFormParam);
                if(empty($this->arrErr)) {
                    // 次々回お届け日を再セット
                    $after_next_arrival_date =
                        $this->getAfterNextArrivalDate($objFormParam);
                    $objFormParam->setValue
                        ("after_next_arrival_date",
                         $after_next_arrival_date
                    );
                }
                break;
            // 商品一覧表示(モバイル・スマートフォン用)
            case 'change':
            case 'add':

                if ($this->mode == 'change') {
                    $this->tpl_product_mode = 'change';
                } else {
                    $this->tpl_product_mode = 'add';
                }

                // ブランドID
                $brand_id = $objFormParam->getValue('brand_id');
                // 商品一覧を取得
                $this->arrProductList =
                    $this->lfGetRegularProductListByBrandId($brand_id);

                $this->tpl_mainpage =
                    'mypage/regular_product.tpl';

                break;
            // 商品選択からの戻り(モバイル・スマートフォン用)
            case 'product_select':

                // 選択した商品をフォームへセット
                //$this->lfSetFormProductData($objFormParam);
                $this->lfSetFormProductData($objCartSess);

                break;
            default: // 初期表示

                // 定期受注詳細を設定
                $arrRegularDetail =$objRegular->getRegularOrderDetail(
                    $regular_id, $line_no);

		// ブランドIDがある商品の場合グループ取得
		if ($arrRegularDetail["brand_id"]) {
		    // 定期受注詳細を同一グループで設定
		    $arrRegularDetailGroup =$objRegular->getRegularOrderDetailGroup(
			$regular_id, $arrRegularDetail);
		} else {
		    $arrRegularDetailGroup[0] = $arrRegularDetail;
		}

		// SESSION内の商品を削除
		$objCartSess->delAllProducts(PRODUCT_TYPE_NORMAL);
		// 定期情報をSESSIONにセット
		$this->lfSetRegularData($objCartSess, $arrRegularDetailGroup);
		$_SESSION["REGULAR"] = $arrRegularDetailGroup;
		$this->arrRegularDetail = $arrRegularDetailGroup;

                $this->lfSetParam($objFormParam, $arrRegularDetail);

		// プロモーション確認用
		$_SESSION["REGULAR_CAMPAIGN_CODE"] = $arrRegularDetail["campaign_cd"];
		$_SESSION["REGULAR_DEVICE_TYPE"] = $arrRegularDetail["device_type_id"];

                break;
        }

        $this->arrForm = $objFormParam->getFormParamList();
	if (!is_array($arrRegularDetail)) {
	    $this->arrForm["brand_id"]["value"] = $_SESSION["REGULAR"][0]["brand_id"];
	}
	// モバイルの場合のみ入力情報をSESSIONに保持
        if(SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE) {
	    switch ($this->mode) {
		case "up":
		case 'down'://1個減らす
		case 'delete'://カートから削除
		    $this->arrForm = $_SESSION["REGULAR_CHG"];
		    break;

		default:
		    $_SESSION["REGULAR_CHG"] = $this->arrForm;
	    }
	}

	// 値引き確認
	$objCartSess->isProductsPriceCampaign(PRODUCT_TYPE_NORMAL);
	// カートキー取得
	$this->arrCartKeys = $objCartSess->getKeys();
	// 商品情報取得
	$this->arrCart = $objCartSess->getAllCartList();
	// カート内商品確認
	if (!$this->tpl_message) {
	    $this->tpl_message .= $this->lfCheckProducts($objCartSess);
	}
	$this->tpl_message .= $objCartSess->checkProducts(PRODUCT_TYPE_NORMAL);
        $objCustomer = new SC_Customer_Ex();
	// 送料を取得
	$this->arrData = $objCartSess->calculate(PRODUCT_TYPE_NORMAL, $objCustomer);

    }

    /**
     * パラメーター情報の初期化を行う.
     *
     * @param object $objFormParam SC_FormParamのインスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {

        $objFormParam->addParam("カートキー", 'cartKey',
            INT_LEN, 'n', array('NUM_CHECK',"MAX_LENGTH_CHECK"));
        $objFormParam->addParam("カートナンバー", "cart_no",
            INT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"));
	// カートから削除用
        $objFormParam->addParam("削除カートナンバー", "del_cart",
            INT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"));
        // スマートフォン版での数量変更用
        $objFormParam->addParam("数量", 'quantity',
            INT_LEN, 'n', array("ZERO_CHECK", "NUM_CHECK", "MAX_LENGTH_CHECK"));

        $objFormParam->addParam("定期受注ID", "regular_id",
            INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("行NO", "line_no",
            INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("次回お届け日", "next_arrival_date", 
            STEXT_LEN, 'n', 
            array("EXIST_CHECK", "DATE_CHECK","SPTAB_CHECK", 
            "MAX_LENGTH_CHECK"));

        $objFormParam->addParam(
            "次々回お届け日", "after_next_arrival_date");

        $objFormParam->addParam("お届け間隔", 'todoke_cycle',
            INT_LEN, 'n', 
            array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("お届け日指定区分", 'todoke_kbn',
            INT_LEN, 'n', 
            array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("お届け日", 'todoke_day',
            INT_LEN, 'n', 
            array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("お届け曜日", 'todoke_week',
            INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("お届け曜日", 'todoke_week2',
            INT_LEN, 'n',
            array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("コースCD", 'course_cd',
            INT_LEN, '', array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("状況", 'status', 
            INT_LEN, '',
            array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("キャンセル日", "cancel_date",
            STEXT_LEN, 'n', 
            array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
 
        $objFormParam->addParam("キャンセル理由", 'cancel_reason_cd',
            INT_LEN, 'n',
            array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("ブランドID", "brand_id");

        $objFormParam->addParam("氏名", "order_name");
        $objFormParam->addParam("氏名(カナ)", "order_kana");
        $objFormParam->addParam("TEL", "order_tel");
        $objFormParam->addParam("郵便番号", "order_zip");
        $objFormParam->addParam("都道府県", "order_pref");
        $objFormParam->addParam("住所１", "order_addr01");
        $objFormParam->addParam("住所２", "order_addr02");
        $objFormParam->addParam("支払方法", "payment_id");

        // 追加商品情報
        $objFormParam->addParam("商品項番", "product_class_id",
            INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"), '0');

        $objFormParam->addParam("商品種別ID", "product_type_id",
            INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"), '0');

        $objFormParam->addParam("単価", 'price',
            INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"), '0');

        $objFormParam->addParam("数量", 'quantity',
            INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"), '0');

        $objFormParam->addParam("商品ID", "product_id",
            INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"), '0');

        $objFormParam->addParam("商品規格ID", "product_class_id",
            INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"), '0');

        $objFormParam->addParam("商品コード", "product_code");

        $objFormParam->addParam("商品名", "product_name");

        $objFormParam->addParam("規格名1", "classcategory_name1");

        $objFormParam->addParam("規格名2", "classcategory_name2");

        $objFormParam->addParam("削除用項番", "delete_no",
            INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("最低購入数", "sale_minimum_number");

        $objFormParam->addParam("購入制限数", "sale_limit");

        $objFormParam->addParam("商品項番", 'no',
            INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("追加商品規格ID", "add_product_class_id",
            INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("修正商品規格ID", "edit_product_class_id",
            INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("お届け日目安", "deliv_date_id");
    }

    /**
     * フォームへデータセットする
     *
     * @param object $objFormParam SC_FormParamのインスタンス
     * @param array セットするデータ配列
     * @param void
     */
    function lfSetParam(&$objFormParam, $arrData) {

        // POST値をセットする
        $objFormParam->setParam($arrData);

        $objRegular = new SC_Helper_Regular_Ex();

        // コースCDからお届け間隔をセット
        if (!empty($arrData["course_cd"])) {
            // コースCDの値からお届け間隔(日ごと、月ごと)をセットする

            $todoke_cycle = $objRegular->getTodokeCycle
                ($objFormParam->getValue("course_cd"));
            $objFormParam->setValue("todoke_cycle", $todoke_cycle);
        }

        // お届け日指定区分をセット
        $todoke_kbn = $objRegular->getTodokeKbn
            ($objFormParam->getValue("todoke_week"),
             $objFormParam->getValue("todoke_week2"));
        $objFormParam->setValue("todoke_kbn", $todoke_kbn);

    }

    /**
     * 入力エラーチェック
     * 
     * @param object $objFormParam SC_FormParamのインスタンス
     * @return array エラーメッセージの配列
     */
    function lfCheckError(&$objFormParam) {

        // 入力パラメーターチェック
        $arrParams = $objFormParam->getHashArray();
        $objErr = new SC_CheckError_Ex($arrParams);
        $objErr->arrErr = $objFormParam->checkError();

        if (!empty($arrParams['todoke_week']) ||
            !empty($arrParams['todoke_week2'])) {

            $objErr->doFunc(array(
                "お届け曜日", "todoke_week"), array("EXIST_CHECK"));
            $objErr->doFunc(array(
                "お届け曜日", "todoke_week2"), array("EXIST_CHECK"));
        }

        // 次回お届け日の前後チェック
        if (!empty($arrParams['next_arrival_date']) &&
            !empty($arrParams['after_next_arrival_date'])) {

            // 「次回お届け日＞次々回お届け日」の場合はエラーとする
            $objErr->doFunc(array("次回お届け日" ,"次々回お届け日", 
                "next_arrival_date", "after_next_arrival_date", REGULAR_FUTURE_MONTH),
                array("DATE_TERM_CHECK", "DATE_FUTURE_MONTH_CHECK"));
        }

        // 「ヶ月ごと」の場合は「1～3」、
        // 「日ごと」の場合は「20～90」のみ入力可能
        if (!empty($arrParams['course_cd']) &&
            !empty($arrParams['todoke_cycle'])) {

            if ($arrParams['todoke_cycle'] == TODOKE_CYCLE_DAY &&
                ($arrParams['course_cd'] > COURSE_CD_DAY_MAX ||
                    $arrParams['course_cd'] < COURSE_CD_DAY_MIN)) {

                $objErr->arrErr['course_cd']  = '※お届け間隔(日ごと)は';
                $objErr->arrErr['course_cd'] .= COURSE_CD_DAY_MIN . '～';
                $objErr->arrErr['course_cd'] .= COURSE_CD_DAY_MAX;
                $objErr->arrErr['course_cd'] .= 'で選択してください。<br />';

            } else if ($arrParams['todoke_cycle'] == TODOKE_CYCLE_MONTH &&
                ($arrParams['course_cd'] > COURSE_CD_MONTH_MAX ||
                    $arrParams['course_cd'] < COURSE_CD_MONTH_MIN)) {

                $objErr->arrErr['course_cd']  = '※お届け間隔(月ごと)は';
                $objErr->arrErr['course_cd'] .= COURSE_CD_MONTH_MIN . '～';
                $objErr->arrErr['course_cd'] .= COURSE_CD_MONTH_MAX;
                $objErr->arrErr['course_cd'] .= 'で選択してください。<br />';
            }
        }

        // お届け日目安ID
        $deliv_date_id = $objFormParam->getValue('deliv_date_id');
        // 商品の発送日目安を参照し、それ以前の場合はエラー
        if (!empty($arrParams['next_arrival_date']) &&
            !empty($deliv_date_id)) {

            // お届け目安日を取得
            $deliv_date = $this->lfGetShortestDelivDate($deliv_date_id);
            if ($arrParams['next_arrival_date'] < $deliv_date) {

                $deliv_date = date('Y年m月d日', strtotime($deliv_date));

                $objErr->arrErr['next_arrival_date']  = '※次回お届け日は';
                $objErr->arrErr['next_arrival_date'] .=
                    $deliv_date . '以降の日付を入力してください<br />';
            }
        }
        return $objErr->arrErr;
    }

    /**
     * 商品をSESSIONから削除する
     * 
     * @param object $objCartSess SC_CartSessのインスタンス
     * @param array $arrDelCart 削除商品の配列
     * @param string $cart_no カートNO
     * @param string $cartKey カートKEY（種別）
     * @return string エラーメッセージ
     */
    function lfDelProducts(&$objCartSess, $arrDelCart, $cart_no, $cartKey) {

	$msg = "";
	// SESSION内の商品ID取得
	$arrProductId = $objCartSess->getAllProductID(PRODUCT_TYPE_NORMAL);

	// 商品IDが複数ある場合削除可能
	if (count($arrProductId) > 1) {
	    for ($i = 0; $i < count($arrDelCart); $i++) {
		$objCartSess->delProduct($arrDelCart[$i], $cartKey);
	    }
	    if (!$arrDelCart && $cart_no) {
		$objCartSess->delProduct($cart_no, $cartKey);
	    }
	} else {
	    // 最後の商品の場合削除不可
	    $msg = "全ての商品を削除することはできません。";
	}

	return $msg;
    }

    /**
     * SESSIONセットされている商品数を確認する
     * 
     * @param object $objCartSess SC_CartSessのインスタンス
     * @return string エラーメッセージ
     */
    function lfCheckProducts(&$objCartSess) {

	$msg = "";
	// SESSION内の商品取得
	$arrCart = $objCartSess->getAllCartList();

	// 商品IDが複数ある場合削除可能
	if (!count($arrCart)) {
	    // 商品がない場合
	    $msg = "商品が指定されておりません。";
	}

	return $msg;
    }

    /**
     * 定期受注明細情報を登録する
     *
     * @param integer $regular_id 定期受注ID
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param array $arrBeforeRegular 変更前商品情報
     * @param array $arrRegularDetail 変更後商品情報
     * @return array 追加した商品情報の配列
     */
    function lfRegisterRegularOrderDetail($regular_id, &$objFormParam
				    , $arrBeforeRegular, $arrRegularDetail) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objRegular = new SC_Helper_Regular_Ex();

        // 定期受注情報の配列
        $arrRegular = $objFormParam->getSwapArray(array(
            "todoke_kbn",
            "todoke_day",
            "todoke_week",
            "todoke_week2",
            "course_cd",
            "status",
            "next_arrival_date",
            "after_next_arrival_date",
            "cancel_date",
            "cancel_reason_cd",
        ));

	$arrLine = array();
	for ($i = 0; $i < count($arrBeforeRegular); $i++) {
	    // 変更前行番号取得
	    $arrLine[] = $arrBeforeRegular[$i]["line_no"];
	}

	// 変更前明細情報削除
	$lineNo = implode(",", $arrLine);
	$where =<<<EOF
regular_id = {$regular_id}
AND line_no in ({$lineNo})
EOF;

	$objQuery->delete("dtb_regular_order_detail", $where);

        // 登録用にデータ加工
        if (empty($arrRegular[0]['todoke_day'])) {
            $arrRegular[0]['todoke_day'] = 'NULL';
        }
        if (empty($arrRegular[0]['todoke_week'])) {
            $arrRegular[0]['todoke_week'] = 'NULL';
            $arrRegular[0]['todoke_week2'] = 'NULL';
	    if ($arrRegular[0]['next_arrival_date']) {
		$chkNextArrival = explode("/", $arrRegular[0]['next_arrival_date']);
		$arrRegular[0]['todoke_day'] = $chkNextArrival[2];
	    }
        } else {
            $arrRegular[0]['todoke_day'] = 'NULL';
	}

	// 行NOの最大値を取得
	$max_line_no =
	    $objRegular->getRegularOrderDetailMaxLineNo($regular_id);
	for ($i = 0; $i < count($arrRegularDetail); $i++) {
	    if (isset($arrLine[$i])) {
		$line_no = $arrLine[$i];
		if ($line_no >= $max_line_no) {
		    $max_line_no = $line_no;
		}
	    } else {
		$max_line_no++;
		$line_no = $max_line_no;
	    }
	    // 定期受注明細情報登録
            $objRegular->registerAddRegularOrderDetail(
                $regular_id, $line_no, $arrRegular[0], $arrRegularDetail[$i]);
	}

        return $arrRegular[0];

    }

    /**
     * 最短のお届け日を取得する.
     *
     * @param $deliv_date_id お届け目安ID
     * @return string お届け目安日
     */
    function lfGetShortestDelivDate($deliv_date_id) {

        //発送目安
        switch($deliv_date_id) {
        //即日発送
        case '1':
            $start_day = 1;
            break;
            //1-2日後
        case '2':
            $start_day = 3;
            break;
            //3-4日後
        case '3':
            $start_day = 5;
            break;
            //1週間以内
        case '4':
            $start_day = 7;
            break;
            //2週間以内
        case '5':
            $start_day = 14;
            break;
            //3週間以内
        case '6':
            $start_day = 21;
            break;
            //1ヶ月以内
        case '7':
            $start_day = 32;
            break;
            //2ヶ月以降
        case '8':
            $start_day = 62;
            break;
            //お取り寄せ(商品入荷後)
        case '9':
            $start_day = "";
            break;
        default:
            //お届け日が設定されていない場合
            $start_day = "";
        }

        //お届け可能日のスタート値から、お届け日の配列を取得する
        $masterData = new SC_DB_MasterData();

        $arrWDAY = $masterData->getMasterData("mtb_wday");

        //お届け可能日のスタート値がセットされていれば
        if($start_day >= 1) {
            $now_time = time();
            // 基本時間から日数を追加
            $tmp_time = $now_time + ($start_day * 24 * 3600);
            list($y, $m, $d, $w) = explode(" ", date("Y m d w", $tmp_time));
            $val = sprintf("%04d/%02d/%02d", $y, $m, $d);
            $deliv_date = $val;
        } else {
            $deliv_date = "";
        }
        return $deliv_date;

    }

    /**
     * 次々回お届け日を算出して返却する
     *
     * @param  SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return string 次々回お届け日
     */
    function getAfterNextArrivalDate($objFormParam) {

        $objRegular = new SC_Helper_Regular_Ex();

        // 次回お届け日
        $next_arrival_date = $objFormParam->getValue("next_arrival_date");
        // コースCD
        $course_cd = $objFormParam->getValue("course_cd");
        // 曜日指定
        $todoke_week = $objFormParam->getValue("todoke_week");
        $todoke_week2 = $objFormParam->getValue("todoke_week2");

        if ($objFormParam->getValue("todoke_cycle") == TODOKE_CYCLE_DAY) {
            // 「日ごと」指定の場合
            $interval = 'day';
        } else {
            // 「ヶ月」指定の場合
            $interval = 'month';
        }

        // ○ヶ月後または○日後の日付を取得する
        $after_next_arrival_date = $objRegular->getAfterNextArrivalDate
            ($next_arrival_date, $interval, $course_cd);

        // ヶ月指定の時のみ曜日設定
        if ($objFormParam->getValue("todoke_cycle") == TODOKE_CYCLE_MONTH) {
            // 第○ △曜日を選択している場合は、
            // 算出した次々回お届け日年月の第○曜日の日付を算出
            if (!empty($todoke_week) && !empty($todoke_week2)) {
                $after_next_arrival_date = $objRegular->getWeekDate
                    ($next_arrival_date, $todoke_week, $todoke_week2, $course_cd);
            }
        }


        return $after_next_arrival_date;
    }

    /**
     * お届け内容変更完了メールを送信する
     *
     * @param  array $arrBeforeRegular 変更前定期受注情報の連想配列
     * @param  array $arrAfterRegular  変更後定期受注情報の連想配列
     * @param  array $arrRegularDetail 商品情報の連想配列
     * @return void
     */
    function lfSendChangeRegularMail
        ($arrBeforeRegular, $arrAfterRegular, $arrRegularDetail) {

        $objRegular = new SC_Helper_Regular_Ex();

        // 表示用にお届け間隔をセットしておく
        $arrBeforeRegular[0]["todoke_kankaku"] =
            $objRegular->getTodokeKankakuString
                ($arrBeforeRegular[0]["course_cd"],
                 $arrBeforeRegular[0]["todoke_week"],
                 $arrBeforeRegular[0]["todoke_week2"]
                );
	if ($arrAfterRegular["todoke_week"] == "NULL") {
	    $arrAfterRegular["todoke_week"] = "";
	}
        if ($arrAfterRegular["todoke_week2"] == "NULL") {
	    $arrAfterRegular["todoke_week2"] = "";
	}
        $arrAfterRegular["todoke_kankaku"] =
            $objRegular->getTodokeKankakuString
                ($arrAfterRegular["course_cd"],
                 $arrAfterRegular["todoke_week"],
                 $arrAfterRegular["todoke_week2"]
                );

        // 変更完了メールの送信
	/*
        if(SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE) {
            $mobileFlg = true;
        } else {
            $mobileFlg = false;
        }
	 */

        $objCustomer = new SC_Customer_Ex();
        $customer_id = $objCustomer->getValue('customer_id');

        $res = SC_Helper_Mail_Ex::sfSendChangeRegularMail
            ($arrBeforeRegular, $arrAfterRegular, $arrRegularDetail);
	/*
        $res = SC_Helper_Mail_Ex::sfSendChangeRegularMail
            ($arrBeforeRegular, $arrAfterRegular, $arrDetail, $mobileFlg);
	 */
    }

    /**
     * ブランドIDで定期可能な商品一覧データを商品マスタから取得
     *
     * @param integer $brand_id ブランドID
     *
     */
    function lfGetRegularProductListByBrandId($brand_id) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql =<<<__EOS
SELECT pc.product_id
     , pc.product_class_id
     , pd.name AS product_name
     , cg.name AS product_class_name
     , pc.price01 AS price
     , pc.sale_limit
     , pc.sale_minimum_number
     , pd.main_list_image
  FROM dtb_products_class pc
 INNER JOIN dtb_products pd
    ON pc.product_id = pd.product_id
   AND pd.del_flg = 0
  LEFT JOIN dtb_class_combination cc
    ON pc.class_combination_id = cc.class_combination_id
  LEFT JOIN dtb_classcategory cg
    ON cc.classcategory_id = cg.classcategory_id
   AND cg.del_flg = 0
 WHERE pc.del_flg = 0
   AND pd.brand_id = "{$brand_id}"
   AND pc.teiki_flg = 1
   AND pd.status = 1
   AND pc.sell_flg = 1
   AND (pd.disp_start_date IS NULL OR
        pd.disp_start_date <= now())
   AND (pd.sale_start_date IS NULL OR
        pd.sale_start_date <=  DATE_FORMAT(now(), '%Y-%m-%d') )
   AND (pd.sale_end_date IS NULL OR
        pd.sale_end_date >= DATE_FORMAT(now(), '%Y-%m-%d'))
 ORDER BY pc.product_code
        , pd.product_id
__EOS;

        $results = $objQuery->getAll($sql);

        return $results;
    }

    /**
     * 画面で選択した商品をフォームへセットする
     * (モバイル画面用)
     *
     * @param  SC_CartSession $objCartSess SC_CartSession インスタンス
     */
    function lfSetFormProductData(&$objCartSess) {

        foreach($_POST as $key => $data) {
            if (preg_match("/^product_select/", $key)) {
                // 選択した商品の商品規格ID
                $product_class_id =
                    str_replace("product_select_", "", $key);
            }
        }
	$objCartSess->addProduct(
		$product_class_id, 1, REGULAR_PURCHASE_FLG_ON);

	/*
        // 商品規格IDの重複チェック
        $this->tpl_err_product_exist
            = $this->lfCheckExistProduct($product_class_id);
        if (!empty($this->tpl_err_product_exist)) {
            return;
        }

        $objProduct = new SC_Product_Ex();
        $arrProduct = $objProduct->getDetailAndProductsClass
            ($product_class_id);

        if ($_POST['product_mode'] == 'change') {

            // 変更商品欄へデータセット
            $_POST['after_product_id'] = $arrProduct['product_id'];
            $_POST['after_product_class_id'] = $arrProduct['product_class_id'];
            $product_name  = $arrProduct['name'] . " ";
            $product_name .= $arrProduct['classcategory_name'];
            $_POST['after_product_name'] = $product_name;
            $_POST['regular_quantity'] = $arrProduct['sale_minimum_number'];

        } else {
            // 追加商品欄へデータセット
            $cnt = count($_POST["product_class_id"]);
            
            $_POST["product_id"][$cnt] = $arrProduct['product_id'];
            $_POST["product_class_id"][$cnt] = $arrProduct['product_class_id'];
            $product_name  = $arrProduct['name'] . " ";
            $product_name .= $arrProduct['classcategory_name'];
            $_POST["product_name"][$cnt] = $product_name;
            $_POST["quantity"][$cnt] = $arrProduct['sale_minimum_number'];
            $_POST["price"][$cnt] = $arrProduct['price01'];
            $_POST["sale_limit"][$cnt] = $arrProduct['sale_limit'];
            $_POST["sale_minimum_number"][$cnt] =
                $arrProduct['sale_minimum_number'];
        }
        $objFormParam->setParam($_POST);
	 */
    }

    /**
     * アイテム追加変更時の重複チェック
     * （モバイル画面用）
     *
     * @param integer $product_class_id 選択した商品規格ID
     * @return string エラーメッセージ
     */
    function lfCheckExistProduct($product_class_id) {

        $err = '※既に選択済みの商品です。';

        // 変更前商品とのチェック
        if ($product_class_id == $_POST['before_product_class_id']) {
            return $err;
        }

        // 変更後商品とのチェック
        if ($product_class_id == $_POST['after_product_class_id']) {
            return $err;
        }

        // 追加商品欄とのチェック
        foreach($_POST["product_class_id"] as $pcid) {
            if ($product_class_id == $pcid) {
                return $err;
            }
        }

        return;
    }

    /**
     * カートに商品を追加
     *
     * @return void
     */
    function lfSetRegularData($objCartSess, $arrData){
        $product_class_id = $arrForm['product_class_id'];
	// SESSIONに商品をセットする
	for ($i = 0; $i < count($arrData); $i++) {
	    $objCartSess->addProduct(
		$arrData[$i]['product_class_id'],
		$arrData[$i]['quantity'],
		REGULAR_PURCHASE_FLG_ON
	    );
	}
    }

}
