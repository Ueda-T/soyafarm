<?php
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * 顧客管理 のページクラス.
 *
 * @package Page
 * @author IQUEVE CO.,LTD.
 * @version $Id: LC_Page_Admin_Customer.php 108 2012-04-24 03:33:44Z hira $
 */
class LC_Page_Admin_Customer extends LC_Page_Admin_Ex {
    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();

        if ($_SESSION['critical_menu'] == CRITICAL_MENU_OFF) {
	    SC_Response_Ex::sendRedirect("../home.php");
	    exit;
	}

        $this->tpl_mainpage = 'customer/index.tpl';
        $this->tpl_mainno = 'customer';
        $this->tpl_subno = 'index';
        $this->tpl_pager = 'pager.tpl';
        $this->tpl_maintitle = '顧客管理';
        $this->tpl_subtitle = '顧客マスター';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrSex = $masterData->getMasterData("mtb_sex");
        $this->arrPageMax = $masterData->getMasterData("mtb_page_max");
        $this->arrStatus = $masterData->getMasterData("mtb_customer_status");
        $this->arrCustomerKbn = array(0 => '一般', 1 => '社員');
        $this->arrKashidaoreKbn = array(0 => '通常顧客', 1 => '貸倒顧客');
        $this->arrMagazineType = $masterData->getMasterData("mtb_magazine_type");

        // 日付プルダウン設定
        $objDate = new SC_Date_Ex();
        // 登録・更新日検索用
        $objDate->setStartYear(RELEASE_YEAR);
        $objDate->setEndYear(DATE('Y'));
        $this->arrRegistYear = $objDate->getYear();
        // 生年月日検索用
        $objDate->setStartYear(BIRTH_YEAR);
        $objDate->setEndYear(DATE('Y'));
        $this->arrBirthYear = $objDate->getYear();
        // 月日の設定
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();

        // カテゴリ一覧設定
        $objDb = new SC_Helper_DB_Ex();
        $this->arrCatList = $objDb->sfGetCategoryList();

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
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        // パラメーター管理クラス
        $objFormParam = new SC_FormParam_Ex();
        // パラメーター設定
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);
        $objFormParam->convParam();
        // パラメーター読み込み
        $this->arrForm = $objFormParam->getFormParamList();
        // 検索ワードの引き継ぎ
        $this->arrHidden = $objFormParam->getSearchArray();

        // 入力パラメーターチェック
        $this->arrErr = $this->lfCheckError($objFormParam);
        if(!SC_Utils_Ex::isBlank($this->arrErr)) {
            return;
        }

        // モードによる処理切り替え
        switch ($this->getMode()) {
        case 'delete':
            $this->is_delete = $this->lfDoDeleteCustomer($objFormParam->getValue('edit_customer_id'));
            list($this->tpl_linemax, $this->arrData, $this->objNavi) =
		SC_Helper_Customer_Ex::sfGetSearchData
		($objFormParam->getHashArray());
            $this->arrPagenavi = $this->objNavi->arrPagenavi;
            break;
        case 'resend_mail':
            $this->is_resendmail = $this->lfDoResendMail
		($objFormParam->getValue('edit_customer_id'));
            list($this->tpl_linemax, $this->arrData, $this->objNavi) =
		SC_Helper_Customer_Ex::sfGetSearchData
		($objFormParam->getHashArray());
            $this->arrPagenavi = $this->objNavi->arrPagenavi;
            break;
        case 'search':
            list($this->tpl_linemax, $this->arrData, $this->objNavi) =
		SC_Helper_Customer_Ex::sfGetSearchData
		($objFormParam->getHashArray());
            $this->arrPagenavi = $this->objNavi->arrPagenavi;
            break;
        case 'csv':
            $this->lfDoCSV($objFormParam->getHashArray());
            exit;
        default:
            break;
        }
    }

    /**
     * パラメーター情報の初期化
     *
     * @param array $objFormParam フォームパラメータークラス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        SC_Helper_Customer_Ex::sfSetSearchParam($objFormParam);
        $objFormParam->addParam('編集対象顧客ID', 'edit_customer_id', INT_LEN, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
    }

    /**
     * エラーチェック
     *
     * @param array $objFormParam フォームパラメータークラス
     * @return array エラー配列
     */
    function lfCheckError(&$objFormParam) {
        return SC_Helper_Customer_Ex::sfCheckErrorSearchParam($objFormParam);
    }

    /**
     * 顧客を削除する処理
     *
     * @param integer $customer_id 顧客ID
     * @return boolean true:成功 false:失敗
     */
    function lfDoDeleteCustomer($customer_id) {
        $arrData = SC_Helper_Customer_Ex::sfGetCustomerDataFromId($customer_id, "del_flg = 0");
        if(SC_Utils_Ex::isBlank($arrData)) {
            //対象となるデータが見つからない。
            return false;
        }
        // XXXX: 仮会員は物理削除となっていたが論理削除に変更。
        $arrVal["del_flg"] = "1";
        $arrVal["update_date"] ="now()";
        SC_Helper_Customer_Ex::sfEditCustomerData($arrVal, $customer_id);
        return true;
    }

    /**
     * 顧客に登録メールを再送する処理
     *
     * @param integer $customer_id 顧客ID
     * @return boolean true:成功 false:失敗
     */
    function lfDoResendMail($customer_id) {
        $arrData = SC_Helper_Customer_Ex::sfGetCustomerDataFromId($customer_id);
        if(SC_Utils_Ex::isBlank($arrData) or $arrData['del_flg'] == 1) {
            //対象となるデータが見つからない、または削除済み
            return false;
        }
        // 登録メール再送
        $objHelperMail = new SC_Helper_Mail_Ex();
        $objHelperMail->sfSendRegistMail($arrData['secret_key'], $customer_id);
        return true;
    }

    /**
     * 顧客一覧CSVを検索してダウンロードする処理
     *
     * @param array $arrParam 検索パラメーター連想配列
     * @return boolean true:成功 false:失敗
     */
    function lfDoCSV($arrParam) {
        $objSelect = new SC_CustomerList_Ex($arrParam, 'customer');
        $order = "update_date DESC, customer_id DESC";

        require_once CLASS_EX_REALDIR . 'helper_extends/SC_Helper_CSV_Ex.php';
        $objCSV = new SC_Helper_CSV_Ex();
        list($where, $arrVal) = $objSelect->getWhere();
        return $objCSV->sfDownloadCsv('2', $where, $arrVal, $order, true);
    }
}
?>
