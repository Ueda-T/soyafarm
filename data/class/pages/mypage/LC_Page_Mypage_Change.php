<?php
require_once CLASS_EX_REALDIR . 'page_extends/mypage/LC_Page_AbstractMypage_Ex.php';

/*
 * 登録内容変更 のページクラス.
 */
class LC_Page_Mypage_Change extends LC_Page_AbstractMypage_Ex {
    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_subtitle = 'ご登録内容の変更';
        $this->tpl_mypageno = 'change';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrMAILMAGATYPE = $masterData->getMasterData("mtb_mail_magazine_type");
        $this->arrSex = $masterData->getMasterData("mtb_sex");
        $this->arrQuestionnaire = $masterData->getMasterData("mtb_questionnaire");
        $this->httpCacheControl('nocache');

        // 生年月日選択肢の取得
        $objDate = new SC_Date_Ex(BIRTH_YEAR, date('Y',strtotime('now')));
        $this->arrYear = $objDate->getYear('', START_BIRTH_YEAR, '');
        $this->arrMonth = $objDate->getMonth(true);
        $this->arrDay = $objDate->getDay(true);
    }

    function setAddrLimit($objFormParam) {
	$prefBytes = mb_strlen($this->arrPref[$this->arrForm['pref']]) * 2;
	$objFormParam->setLength("addr01", ADDRESS_LEN - $prefBytes);
    }

    function initParam(&$objFormParam) {
        SC_Helper_Customer_Ex::sfCustomerMypageParam($objFormParam);

        $objFormParam->addParam('誕生日', 'birth');
    }

    /**
     * Page のプロセス
     * @return void
     */
    function action() {
	$_SESSION["MYPAGENO"] = $this->tpl_mypageno;
        $objCustomer = new SC_Customer_Ex();
        $customer_id = $objCustomer->getValue('customer_id');
        $objFormParam = new SC_FormParam_Ex();

        // mobile用（戻るボタンでの遷移かどうかを判定）
        if (!empty($_POST['return'])) {
            $_POST['mode'] = 'return';
        }

        // パラメーター管理クラス,パラメーター情報の初期化
	$this->initParam($objFormParam);

	// 番地なし補正
	if ($_POST['house_no'] != "") {
	    $_POST['addr02'] = "番地なし";
	}
	// POST値の取得
        $objFormParam->setParam($_POST);
        $this->arrForm = $objFormParam->getHashArray();

        switch ($this->getMode()) {
        case 'confirm':
	    // 選択されている都道府県に応じて住所1の制限文字数を調整する
	    $this->setAddrLimit($objFormParam);
	    //-- 確認
            $this->arrErr = SC_Helper_Customer_Ex::sfCustomerMypageErrorCheck
		($objFormParam);

            // 入力エラーなし
            if(empty($this->arrErr)) {
		$this->arrForm['kana'] = mb_convert_kana($this->arrForm['kana'], "ahks");
                $this->tpl_mainpage = 'mypage/change_confirm.tpl';
                $this->tpl_title = '会員登録(確認ページ)';
            }
            break;
        case 'complete':
            //-- 会員登録と完了画面

            // 会員情報の登録
            $this->lfRegistCustomerData($objFormParam, $customer_id);

            // 完了ページに移動させる。
            SC_Response_Ex::sendRedirect('change_complete.php');
            break;
        case 'return':
            break;
        default:
            $this->arrForm =
		SC_Helper_Customer_Ex::sfGetCustomerData($customer_id);
	    // 番地なし補正
	    if (!strcmp($this->arrForm['addr02'], "番地なし")) {
		$this->arrForm['house_no'] = "checked";
	    }
            break;
        }
    }

    /**
     *  会員情報を登録する
     *
     * @param mixed $objFormParam
     * @param mixed $customer_id
     * @access private
     * @return void
     */
    function lfRegistCustomerData(&$objFormParam, $customer_id) {
        $arrRet = $objFormParam->getHashArray();
        $sqlval = $objFormParam->getDbArray();
        $sqlval['birth'] = SC_Utils_Ex::sfGetTimestamp
	    ($arrRet['year'], $arrRet['month'], $arrRet['day']);
        $sqlval['updator_id'] = $customer_id;
        SC_Helper_Customer_Ex::sfEditCustomerData($sqlval, $customer_id);
        SC_Helper_Mail_Ex::sfSendChangeRegistMail($customer_id);
    }
}
