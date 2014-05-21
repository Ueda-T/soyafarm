<?php
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * 顧客情報修正 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_Customer_Edit.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Admin_Customer_Edit extends LC_Page_Admin_Ex {
    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'customer/edit.tpl';
        $this->tpl_mainno = 'customer';
        $this->tpl_subno = 'customer';
        $this->tpl_pager = 'pager.tpl';
        $this->tpl_maintitle = '顧客管理';
        $this->tpl_subtitle = '顧客登録';

        $master = new SC_DB_MasterData_Ex();
        $this->arrPref = $master->getMasterData('mtb_pref');
        $this->arrSex = $master->getMasterData("mtb_sex");
        $this->arrReminder = $master->getMasterData("mtb_reminder");
        $this->arrStatus = $master->getMasterData("mtb_customer_status");
        $this->arrCustomerKbn = array(0 => '一般', 1 => '社員');
        $this->arrKashidaoreKbn = array(0 => '通常顧客', 1 => '貸倒顧客');
        $this->arrMailMagazineType = $master->getMasterData("mtb_mail_magazine_type");
        $this->arrPageMax = $master->getMasterData("mtb_page_max");

        // 日付プルダウン設定
        $objDate = new SC_Date_Ex(BIRTH_YEAR);
        $this->arrYear = $objDate->getYear();
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();

        $objDate = new SC_Date_Ex(RELEASE_YEAR);
        $this->arrPointValidYear = $objDate->getYear();
        $this->arrPointValidMonth = $objDate->getMonth();
        $this->arrPointValidDay = $objDate->getDay();

        $objDate = new SC_Date_Ex(RELEASE_YEAR);
        $this->arrBirthPointValidYear = $objDate->getYear();
        $this->arrBirthPointValidMonth = $objDate->getMonth();
        $this->arrBirthPointValidDay = $objDate->getDay();

        // 支払い方法種別
        $objDb = new SC_Helper_DB_Ex();
        $this->arrPayment = $objDb->sfGetIDValueList
	    ("dtb_payment", "payment_id", "payment_method");

        // 顧客形態
        $objDb = new SC_Helper_DB_Ex();
        $this->arrCustomerTypeCd = $objDb->sfGetIDValueList
	    ("dtb_customer_type", "customer_type_cd", "customer_type_name");
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
        $objFormParam = new SC_FormParam_Ex();
        $objFormSearchParam = new SC_FormParam_Ex();

        // モードによる処理切り替え
        switch ($this->getMode()) {
        case 'edit_search':
            //検索引き継ぎ用パラメーター処理
            $this->lfInitSearchParam($objFormSearchParam);
            $objFormSearchParam->setParam($_REQUEST);
            $this->arrErr = $this->lfCheckErrorSearchParam($objFormSearchParam);
            $this->arrSearchData = $objFormSearchParam->getSearchArray();
            if(!SC_Utils_Ex::isBlank($this->arrErr)) {
                return;
            }

            //指定顧客の情報をセット
            $this->arrForm = SC_Helper_Customer_Ex::sfGetCustomerData
		($objFormSearchParam->getValue("edit_customer_id"), true);

	    $this->tpl_linemax = $this->getNumberOfLines
		($objFormSearchParam->getValue("edit_customer_id"));

	    $page_max = SC_Utils_Ex::sfGetSearchPageMax
		($objFormSearchParam->getValue('search_page_max'));

            //購入履歴情報の取得
            list($this->arrPurchaseHistory, $this->objNavi) =
		$this->lfPurchaseHistory
		($objFormSearchParam->getValue("edit_customer_id"), $page_max);

            $this->arrPagenavi = $this->objNavi->arrPagenavi;
            $this->arrPagenavi['mode'] = 'return';
            $this->tpl_pageno = '1';
            break;
        case 'confirm':
            //パラメーター処理
            $this->lfInitParam($objFormParam);
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();
            // 入力パラメーターチェック
            $this->arrErr = $this->lfCheckError($objFormParam);
            $this->arrForm = $objFormParam->getHashArray();

            //検索引き継ぎ用パラメーター処理
            $this->lfInitSearchParam($objFormSearchParam);
            $objFormSearchParam->setParam($objFormParam->getValue("search_data"));
            $this->arrSearchErr = $this->lfCheckErrorSearchParam($objFormSearchParam);
            $this->arrSearchData = $objFormSearchParam->getSearchArray();
            if(!SC_Utils_Ex::isBlank($this->arrErr["password"]) or !SC_Utils_Ex::isBlank($this->arrSearchErr)) {
                return;
            }
            // 確認画面テンプレートに切り替え
            $this->tpl_mainpage = 'customer/edit_confirm.tpl';
            break;
        case 'return':
            //パラメーター処理
            $this->lfInitParam($objFormParam);
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();
            // 入力パラメーターチェック
            //$this->arrErr = $this->lfCheckError($objFormParam);
            $this->arrForm = $objFormParam->getHashArray();
            //検索引き継ぎ用パラメーター処理
            $this->lfInitSearchParam($objFormSearchParam);
            $objFormSearchParam->setParam($objFormParam->getValue("search_data"));
            $this->arrSearchErr = $this->lfCheckErrorSearchParam($objFormSearchParam);
            $this->arrSearchData = $objFormSearchParam->getSearchArray();
            if(!SC_Utils_Ex::isBlank($this->arrErr) or !SC_Utils_Ex::isBlank($this->arrSearchErr)) {
                return;
            }
	    $this->tpl_linemax = $this->getNumberOfLines
		($objFormParam->getValue("customer_id"));

	    $page_max = SC_Utils_Ex::sfGetSearchPageMax
		($objFormParam->getValue('search_page_max'));

            //購入履歴情報の取得
            list($this->arrPurchaseHistory, $this->objNavi) =
		$this->lfPurchaseHistory
		($objFormParam->getValue("customer_id"), $page_max,
		 $objFormParam->getValue("search_pageno"));

            $this->arrPagenavi = $this->objNavi->arrPagenavi;
            $this->arrPagenavi['mode'] = 'return';
            $this->tpl_pageno = $objFormParam->getValue("search_pageno");
            break;
        case 'complete':
            //登録・保存処理
            //パラメーター処理
            $this->lfInitParam($objFormParam);
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();
            // 入力パラメーターチェック
            $this->arrErr = $this->lfCheckError($objFormParam);
            $this->arrForm = $objFormParam->getHashArray();
            //検索引き継ぎ用パラメーター処理
            $this->lfInitSearchParam($objFormSearchParam);
            $objFormSearchParam->setParam($objFormParam->getValue("search_data"));
            $this->arrSearchErr = $this->lfCheckErrorSearchParam($objFormSearchParam);
            $this->arrSearchData = $objFormSearchParam->getSearchArray();
            if(!SC_Utils_Ex::isBlank($this->arrErr["password"]) or !SC_Utils_Ex::isBlank($this->arrSearchErr)) {
                return;
            }
	    $this->updatePassword($objFormParam);
            $this->tpl_mainpage = 'customer/edit_complete.tpl';
            break;
        case 'complete_return':
            //検索引き継ぎ用パラメーター処理
            $this->lfInitParam($objFormParam);
            $objFormParam->setParam($_POST);
            $this->lfInitSearchParam($objFormSearchParam);
            $objFormSearchParam->setParam($objFormParam->getValue("search_data"));
            $this->arrSearchErr = $this->lfCheckErrorSearchParam($objFormSearchParam);
            $this->arrSearchData = $objFormSearchParam->getSearchArray();
            if(!SC_Utils_Ex::isBlank($this->arrSearchErr)) {
                return;
            }
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
        // 顧客項目のパラメーター取得
        SC_Helper_Customer_Ex::sfCustomerEntryParam($objFormParam, true);

        // 検索結果一覧画面への戻り用パラメーター
        $objFormParam->addParam
	    ("検索用データ", "search_data", "", "", array(), "", false);

        // 顧客購入履歴ページング用
        $objFormParam->addParam
	    ("", "search_pageno", INT_LEN, 'n',
	     array("NUM_CHECK", "MAX_LENGTH_CHECK"), "", false);

        $objFormParam->addParam
	    ("表示件数", "search_page_max", INT_LEN, 'n',
	     array("MAX_LENGTH_CHECK", "NUM_CHECK"));
    }

    /**
     * 検索パラメーター引き継ぎ用情報の初期化
     *
     * @param array $objFormParam フォームパラメータークラス
     * @return void
     */
    function lfInitSearchParam(&$objFormParam) {
        SC_Helper_Customer_Ex::sfSetSearchParam($objFormParam);
        // 初回受け入れ時用
        $objFormParam->addParam("編集対象顧客ID", "edit_customer_id", INT_LEN, 'n', array("NUM_CHECK", "MAX_LENGTH_CHECK"));
    }

    /**
     * 検索パラメーターエラーチェック
     *
     * @param array $objFormParam フォームパラメータークラス
     * @return array エラー配列
     */
    function lfCheckErrorSearchParam(&$objFormParam) {
        return SC_Helper_Customer_Ex::sfCheckErrorSearchParam($objFormParam);
    }

    /**
     * フォーム入力パラメーターエラーチェック
     *
     * @param array $objFormParam フォームパラメータークラス
     * @return array エラー配列
     */
    function lfCheckError(&$objFormParam) {
        $arrErr = SC_Helper_Customer_Ex::sfCustomerMypageErrorCheck($objFormParam, true);

#if 1 // #12
	if (SC_Utils_Ex::isBlank($objFormParam->getValue('email')) &&
	    SC_Utils_Ex::isBlank($objFormParam->getValue('email_mobile'))) {
	    $arrErr['email'] = $arrErr['email_mobile'] =
            'メールアドレスが入力されていません。';
	}
#endif

        //メアド重複チェック(共通ルーチンは使えない)
        $objQuery   =& SC_Query_Ex::getSingletonInstance();
        $col = "email, email_mobile, customer_id";
        $table = "dtb_customer";
        $where = "del_flg <> 1 AND (email Like ? OR email_mobile Like ?)";
        $arrVal = array($objFormParam->getValue('email'), $objFormParam->getValue('email_mobile'));
        if($objFormParam->getValue("customer_id")) {
            $where .= " AND customer_id <> ?";
            $arrVal[] = $objFormParam->getValue("customer_id");
        }
        $arrData = $objQuery->getRow($col, $table, $where, $arrVal);
        if(!SC_Utils_Ex::isBlank($arrData['email'])) {
            if($arrData['email'] == $objFormParam->getValue('email')) {
                $arrErr['email'] = '※ すでに他の顧客(ID:' . $arrData['customer_id'] . ')が使用しているアドレスです。';
            }else if($arrData['email'] == $objFormParam->getValue('email_mobile')) {
                $arrErr['email_mobile'] = '※ すでに他の顧客(ID:' . $arrData['customer_id'] . ')が使用しているアドレスです。';
            }
        }
        if(!SC_Utils_Ex::isBlank($arrData['email_mobile'])) {
            if($arrData['email_mobile'] == $objFormParam->getValue('email_mobile')) {
                $arrErr['email_mobile'] = '※ すでに他の顧客(ID:' . $arrData['customer_id'] . ')が使用している携帯アドレスです。';
            }else if($arrData['email_mobile'] == $objFormParam->getValue('email')) {
            	if ($arrErr['email'] == "") {
                    $arrErr['email'] = '※ すでに他の顧客(ID:' . $arrData['customer_id'] . ')が使用している携帯アドレスです。';
                }
            }
        }
        return $arrErr;
    }

    function getSalt($cust) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sql =<<<__EOS
select
    salt
from
    dtb_customer
where
    customer_id = ?
__EOS;

	return $objQuery->getOne($sql, array($cust));
    }

    function updatePassword(&$objFormParam) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
	$arrForm = $objFormParam->getHashArray();
	$uid = $_SESSION['member_id'];
	$passwd = $arrForm['password'];
	$custid = $arrForm['customer_id'];
	$s = "";
        $sql =<<<__EOS
update
    dtb_customer
set
    updator_id = $uid
   ,update_date = now()
   %s
where
    customer_id = $custid
__EOS;

	$salt = $this->getSalt($custid);
	$passwd = SC_Utils_Ex::sfGetHashString($arrForm['password'], $salt);
	GC_Utils_Ex::gfPrintLog(sprintf("[%s]→[%s]", $arrForm['password'], $passwd));
	if (!empty($arrForm['password']) && ($arrForm['password'] != DEFAULT_PASSWORD)) {
	    $s = ",password = \"$passwd\"";
	}
	$sql = sprintf($sql, $s);
	GC_Utils_Ex::gfPrintLog($sql);
        $objQuery->query($sql);
    }

    function getNumberOfLines($customer_id)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sql =<<<__EOS
SELECT
    COUNT(*)
FROM
    dtb_order
WHERE
    customer_id = $customer_id
    AND del_flg <> 1
__EOS;
        return $objQuery->getOne($sql);
    }

    /**
     * 購入履歴情報の取得
     *
     * @param array $arrParam 検索パラメーター連想配列
     * @return array( integer 全体件数, mixed 顧客データ一覧配列, mixed SC_PageNaviオブジェクト)
     */
    function lfPurchaseHistory($customer_id, $page_max, $pageno = 1) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        if(SC_Utils_Ex::isBlank($customer_id)) {
            return array('0', array(), NULL);
        }

        // ページ送りの取得
        $objNavi = new SC_PageNavi_Ex
	    ($pageno, $this->tpl_linemax, $page_max,
	     "fnNaviSearchPage2", NAVI_PMAX);

        //購入履歴情報の取得
        $sql =<<<EOF
select
    *
from
    dtb_order
where
    customer_id = $customer_id
    and del_flg <> 1
order by
    create_date desc
limit $page_max
offset $objNavi->start_row
EOF;

	$arrPurchaseHistory = $objQuery->getAll($sql);

        return array($arrPurchaseHistory, $objNavi);
    }
}
?>
