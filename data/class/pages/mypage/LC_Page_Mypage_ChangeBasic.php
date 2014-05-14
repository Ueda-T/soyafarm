<?php
require_once CLASS_EX_REALDIR . 'page_extends/mypage/LC_Page_AbstractMypage_Ex.php';

/**
 * 登録内容変更 のページクラス.
 */
class LC_Page_Mypage_ChangeBasic extends LC_Page_AbstractMypage_Ex {
    /*
     *
     */
    function init() {
        parent::init();
        $this->tpl_subtitle = 'メールアドレスとパスワードの変更';
        $this->tpl_mypageno = 'change_basic';
        $this->tpl_mainpage = 'mypage/change_basic.tpl';
        $this->httpCacheControl('nocache');
    }

    /*
     *
     */
    function initParam (&$objFormParam) {
        $objFormParam->addParam
            ('メールアドレス', 'email', INOS_CUSTOMER_EMAIL_LEN, 'a',
             array("NO_SPTAB", "EMAIL_CHECK", "SPTAB_CHECK",
                   "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));

        $objFormParam->addParam
            ('メールアドレス(確認)', "email02", null, 'a',
             array("NO_SPTAB", "EMAIL_CHECK", "SPTAB_CHECK",
                   "EMAIL_CHAR_CHECK"), "", false);

        $objFormParam->addParam
            ("新しいパスワード", 'password', STEXT_LEN, 'a',
             array("SPTAB_CHECK",
                   "GRAPH_CHECK", "MAX_LENGTH_CHECK"));

        $objFormParam->addParam
            ("新しいパスワード(確認)", 'password02', STEXT_LEN, 'a',
             array("SPTAB_CHECK",
                   "GRAPH_CHECK", "MAX_LENGTH_CHECK"), "", false);
    }

    /*
     *
     */
    function action() {
        $_SESSION["MYPAGENO"] = $this->tpl_mypageno;

        $objCustomer = new SC_Customer_Ex();
        $customerId = $objCustomer->getValue('customer_id');

        // mobile用（戻るボタンでの遷移かどうかを判定）
        if (!empty($_POST['return'])) {
            $_POST['mode'] = 'return';
        }

        // パラメーター情報の初期化
        $objFormParam = new SC_FormParam_Ex();
        $this->initParam($objFormParam);

        if (isset($_POST)) {
            $objFormParam->setParam($_POST);
        }

        $this->arrForm = $objFormParam->getHashArray();

        switch ($this->getMode()) {
        case 'confirm':
            // 入力エラーなし
            $this->arrErr = $this->doCheck($objFormParam);
            if (!empty($this->arrErr)) {
                break;
            }
            $this->passlen = SC_Utils_Ex::sfPassLen
                (strlen($this->arrForm['password']));
            $this->tpl_title = '会員登録(確認ページ)';
            $this->tpl_mainpage = 'mypage/change_basic_confirm.tpl';
            break;
        case 'complete':
            // 会員情報の登録
            $this->doUpdate($objFormParam, $customerId);

            //セッション情報を最新の状態に更新する
            $objCustomer->updateSession();

            // 完了ページに移動
            SC_Response_Ex::sendRedirect('change_basic_complete.php');
            break;
        case 'return':
            break;
        default:
            // $this->arrForm = SC_Helper_Customer_Ex::sfGetCustomerData($customerId);
            break;
        }
    }

    /*
     *
     */
    function doCheck($objFormParam) {
        $objFormParam->convParam();
        $arrParams = $objFormParam->getHashArray();
        $objErr = new SC_CheckError_Ex($arrParams);
        $objErr->arrErr = $objFormParam->checkError();
        $arrForm = $objFormParam->getHashArray();

        if (!empty($this->arrErr)) {
            return $objErr->arrErr;
        }

	if (!strlen($arrForm['email']) &&
	    !strlen($arrForm['password'])) {
            $objErr->arrErr['email'] = "メールアドレスが入力されていません";
            $objErr->arrErr['password'] = "パスワードが入力されていません";
            return $objErr->arrErr;
	}

	if (strlen($arrForm['email'])) {
	    $objErr->doFunc
		(array('メールアドレス', 'メールアドレス(確認)', 
		       'email', 'email02'), array("EQUAL_CHECK"));

	    $objErr->doFunc
		(array('メールアドレス', 'email')
		, array("CHECK_REGIST_CUSTOMER_EMAIL"));
	}

	if (strlen($arrForm['password'])) {
	    $objErr->doFunc
		(array('新しいパスワード', '新しいパスワード(確認)',
		       'password', 'password02'), array("EQUAL_CHECK"));
	}

        return $objErr->arrErr;
    }

    /*
     *
     */
    function getSalt($customerId) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sql =<<<__EOS
SELECT
    salt
FROM
    dtb_customer
WHERE
    customer_id = $customerId
__EOS;

        GC_Utils_Ex::gfPrintLog($sql);
        $r = $objQuery->getAll($sql);
        return $r[0]['salt'];
    }

    /*
     *
     */
    function doUpdate($objFormParam, $customerId) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrParams = $objFormParam->getHashArray();
        $s = "";
        $format =<<<__EOS
UPDATE dtb_customer SET %s WHERE customer_id = $customerId
__EOS;

        // メールアドレス
        if (strlen($arrParams['email'])) {
            $s .= sprintf("email = \"%s\"", $arrParams['email']);
        }
        
        // パスワード
        if (strlen($arrParams['password'])) {
            if (strlen($s) > 0) {
                $s .= ", ";
            }
            $salt = $this->getSalt($customerId);
            $s .= sprintf("password = \"%s\"",
                          SC_Utils_Ex::sfGetHashString
                          ($arrParams['password'], $salt));
        }

        // 更新者ID
        $s .= sprintf(",updator_id = \"%s\"", $customerId);
        $s .= ",update_date = now()";

        // 送信フラグ(0:未送信をセット)
        if (strlen($s) > 0) {
            $s .= ", ";
        }
        $s .= sprintf("send_flg = \"%s\"", INOS_SEND_FLG_OFF);

        $sql = sprintf($format, $s);

        $objQuery->query($sql);

        SC_Helper_Mail_Ex::sfSendChangeRegistMail($customerId);
    }
}
