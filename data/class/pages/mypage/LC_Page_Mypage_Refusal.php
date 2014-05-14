<?php
require_once CLASS_EX_REALDIR . 'page_extends/mypage/LC_Page_AbstractMypage_Ex.php';

/*
 * 退会手続き のページクラス.
 */
class LC_Page_Mypage_Refusal extends LC_Page_AbstractMypage_Ex {
    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_subtitle = 'オンラインショップ登録削除';
        $this->tpl_mypageno = 'refusal';
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

        switch ($this->getMode()){
        case 'confirm':
            // トークンを設定
            $this->refusal_transactionid = $this->getRefusalToken();

            $this->tpl_mainpage     = 'mypage/refusal_confirm.tpl';
            //$this->tpl_subtitle     = '退会手続き(確認ページ)';
            break;

        case 'complete':
            // トークン入力チェック
            if(!$this->isValidRefusalToken()) {
                // エラー画面へ遷移する
                SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, '', true);
                SC_Response_Ex::actionExit();
            }

            $objCustomer = new SC_Customer_Ex();
            $this->lfDeleteCustomer($objCustomer->getValue('customer_id'));
            $objCustomer->EndSession();
            SC_Response_Ex::sendRedirect('refusal_complete.php');

        default:
            if (SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE) {
                $this->refusal_transactionid = $this->getRefusalToken();
            }
            break;
        }
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
     * トランザクショントークンを取得する
     *
     * @return string
     */
    function getRefusalToken() {
        if (empty($_SESSION['refusal_transactionid'])) {
            $_SESSION['refusal_transactionid'] = SC_Helper_Session_Ex::createToken();
        }
        return $_SESSION['refusal_transactionid'];
    }

    /**
     * トランザクショントークンのチェックを行う
     */
    function isValidRefusalToken() {
        if(empty($_POST['refusal_transactionid'])) {
            $ret = false;
        } else {
            $ret = $_POST['refusal_transactionid'] === $_SESSION['refusal_transactionid'];
        }

        return $ret;
    }

    /**
     * トランザクショントークを破棄する
     */
    function destroyRefusalToken() {
        unset($_SESSION['refusal_transactionid']);
    }

    /**
     * 会員情報を削除する
     *
     * @access private
     * @return void
     */
    function lfDeleteCustomer($customer_id){
        $objQuery = SC_Query_Ex::getSingletonInstance();
	$sql =<<<__EOS
update
    dtb_customer
set
    del_flg = 1
   ,send_flg = 0
   ,update_date = now()
   ,updator_id = $customer_id
where
    customer_id = $customer_id
__EOS;

        $objQuery->query($sql);
    }

}
