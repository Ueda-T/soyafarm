<?php
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/*
 * お届け先追加 のページクラス.
 */
class LC_Page_Mypage_DeliveryAddr extends LC_Page_Ex {
    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_title    = "配送先の登録・修正";
        $masterData         = new SC_DB_MasterData_Ex();
        $this->arrPref      = $masterData->getMasterData('mtb_pref');
        $this->httpCacheControl('nocache');
        $this->validUrl = array(MYPAGE_DELIVADDR_URLPATH,
                                DELIV_URLPATH,
                                SHOPPING_PAYMENT_URLPATH,
                                MULTIPLE_URLPATH);
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
     * Page のAction.
     *
     * @return void
     */
    function action() {
        $objCustomer = new SC_Customer_Ex();
        $ParentPage  = MYPAGE_DELIVADDR_URLPATH;

        // GETでページを指定されている場合には指定ページに戻す
        if (isset($_GET['page'])) {
            $ParentPage = htmlspecialchars($_GET['page'], ENT_QUOTES);
        } else if (isset($_POST['ParentPage'])) {
            $ParentPage = htmlspecialchars($_POST['ParentPage'], ENT_QUOTES);
        }

        // 正しい遷移かをチェック
        $arrParentPageList = array(DELIV_URLPATH, MYPAGE_DELIVADDR_URLPATH, MULTIPLE_URLPATH, SHOPPING_PAYMENT_URLPATH);
        if(!SC_Utils_Ex::isBlank($ParentPage) && !in_array($ParentPage, $arrParentPageList)) {
            // 遷移が正しくない場合、デフォルトであるマイページの配送先追加の画面を設定する
            $ParentPage  = MYPAGE_DELIVADDR_URLPATH;
        }

        $this->ParentPage = $ParentPage;

        /*
         * ログイン判定 及び 退会判定
         * 未ログインでも, 複数配送設定ページからのアクセスの場合は表示する
         *
         * TODO 購入遷移とMyPageで別クラスにすべき
         */
        if (!$objCustomer->isLoginSuccess(true) && $ParentPage != MULTIPLE_URLPATH){
            //$this->tpl_onload = "fnUpdateParent('". $this->getLocation($_POST['ParentPage']) ."'); window.close();";
            $this->tpl_onload = "fnUpdateParent('". $ParentPage ."'); window.close();";
        }

        // other_deliv_id のあるなしで追加か編集か判定しているらしい
        $_SESSION['other_deliv_id'] = $_REQUEST['other_deliv_id'];

        // パラメーター管理クラス,パラメーター情報の初期化
        $objFormParam   = new SC_FormParam_Ex();
        SC_Helper_Customer_Ex::sfCustomerOtherDelivParam($objFormParam);
	// 
	if (is_array($_POST)) {
	    if (strlen($_POST['zip'])) {
		$s = preg_replace('/-/', '', trim($_POST['zip']));
		$_POST['zip'] = substr($s, 0, 3) . "-" . substr($s, 3, 4);
	    }
	    if (strlen($_POST['tel'])) {
		$_POST['tel'] = preg_replace('/-/', '', $_POST['tel']);
	    }
    }
	// 番地なし補正
	if ($_POST['house_no'] != "") {
	    $_POST['addr02'] = "番地なし";
    }

	$objFormParam->setParam($_POST);
        $this->arrForm = $objFormParam->getHashArray();

        switch ($this->getMode()) {
            // 入力は必ずedit
            case 'edit':
                $this->arrErr = SC_Helper_Customer_Ex::sfCustomerOtherDelivErrorCheck($objFormParam);
                // 入力エラーなし
                if (!empty($this->arrErr)) {
		    break;
                }

		// TODO ここでやるべきではない
		if (in_array($_POST['ParentPage'], $this->validUrl)) {
		    $this->tpl_onload = "fnUpdateParent('". $this->getLocation($_POST['ParentPage']) ."'); window.close();";
		} else {
		    SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
		}

		if ($objCustomer->isLoginSuccess(true)) {
		    $this->lfRegistData($objFormParam, $objCustomer->getValue("customer_id"));
		} else {
		    $this->lfRegistDataNonMember($objFormParam);
		}

		if(SC_Display_Ex::detectDevice() === DEVICE_TYPE_MOBILE) {
		    // モバイルの場合、元のページに遷移
		    SC_Response_Ex::sendRedirect($this->getLocation($_POST['ParentPage']));
		    exit;
		}
                break;
            case 'multiple':
                // 複数配送先用
                break;
            default :

                if ($_GET['other_deliv_id'] != ""){
                    $arrOtherDeliv = $this->lfGetOtherDeliv(
                        $objCustomer->getValue("customer_id"), $_SESSION['other_deliv_id']);

                    //不正アクセス判定
                    if (!$objCustomer->isLoginSuccess(true)
                        || count($arrOtherDeliv) == 0){
                        SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
                    }

                    //別のお届け先情報取得
                    $this->arrForm = $arrOtherDeliv[0];
                }
                break;
        }

        if (SC_Display_Ex::detectDevice() === DEVICE_TYPE_MOBILE) {
            $this->tpl_mainpage = 'mypage/delivery_addr.tpl';
        } else {
            $this->setTemplate('mypage/delivery_addr.tpl');
        }
    }

    /**
     * ほかのお届け先を取得する
     *
     * @param mixed $customer_id
     * @param mixed $other_deliv_id
     * @access private
     * @return array()
     */
    function lfGetOtherDeliv($customer_id, $other_deliv_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sql =<<<EOF
SELECT
    *
FROM
    dtb_other_deliv
WHERE
    customer_id = $customer_id
    AND other_deliv_id = $other_deliv_id
EOF;
        return $objQuery->getAll($sql);
    }

    /* 登録実行 */
    function lfRegistData($objFormParam, $customer_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrRet = $objFormParam->getHashArray();
        $sqlval  = $objFormParam->getDbArray();

        $sqlval['customer_id'] = $customer_id;

        unset($sqlval['lastlogin_date']);
        $sqlval['update_date'] = 'now()';
        $sqlval['updator_id'] = $customer_id;

        // #216
        // 住所カナ
        $sqlval["addr_kana"] = SC_Helper_DB_Ex::sfGetAddrKanaByZipcode
            (preg_replace('/-/', '', $sqlval["zip"]));

        // 追加
        if (strlen($arrRet['other_deliv_id'] == 0)) {

            // 別のお届け先登録数の取得
            $deliv_count = $this->countOtherDeliv($customer_id);

            // 別のお届け先最大登録数に達している場合、エラー
            if ($deliv_count >= DELIV_ADDR_MAX) {
                SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, "", false, '別のお届け先最大登録数に達しています。');
            }

            // 実行
            $sqlval['other_deliv_id'] =
                $objQuery->nextVal('dtb_other_deliv_other_deliv_id');
            $sqlval['create_date'] = 'now()';
            $sqlval['creator_id'] = $customer_id;
            $objQuery->insert("dtb_other_deliv", $sqlval);

        // 変更
        } else {
            $deliv_count = $this->countOtherDeliv($customer_id, $arrRet['other_deliv_id']);

            if ($deliv_count != 1) {
                SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, "", false, '一致する別のお届け先がありません。');
            }

            // 実行
	    unset($sqlval['create_date']);
            $objQuery->update("dtb_other_deliv", $sqlval, "other_deliv_id = ?", array($arrRet['other_deliv_id']));
        }
    }

    function lfRegistDataNonMember($objFormParam) {
        $arrRegistColumn = $objFormParam->getDbArray();
        foreach ($arrRegistColumn as $key => $val) {
            $arrRegist['shipping_' . $key ] = $val;
        }
        if (count($_SESSION['shipping']) >= DELIV_ADDR_MAX) {
            SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, "", false, '別のお届け先最大登録数に達しています。');
        } else {
            $_SESSION['shipping'][] = $arrRegist;
        }
    }

    /**
     * その他お届け先の件数を取得する
     *
     * @param integer $customerId
     * @param integer $startno
     * @return array
     */
    function countOtherDeliv($customer_id, $deliv_id = "") {

        $objQuery   =& SC_Query_Ex::getSingletonInstance();

        $where_deliv_id = "";
        if (!empty($deliv_id)) {
            $where_deliv_id = "AND other_deliv_id = $deliv_id";
        }

        $sql =<<<EOF
SELECT
    COUNT(*)
FROM
    dtb_other_deliv
WHERE
    customer_id = $customer_id
    $where_deliv_id
EOF;

        return $objQuery->getOne($sql);
    }
}
?>
