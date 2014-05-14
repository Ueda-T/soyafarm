<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * 携帯メールアドレス登録のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Entry_EmailMobile.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Entry_EmailMobile extends LC_Page_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();

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
        $objCustomer    = new SC_Customer;
        $objFormParam   = new SC_FormParam_Ex();

        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->arrErr = $this->lfCheckError($objFormParam);

            if (empty($this->arrErr)) {
                // 2013/12/25 mod okayama 小文字変換させない
                //$email_mobile = $this->lfRegistEmailMobile(strtolower($objFormParam->getValue('email_mobile')),
                $email_mobile = $this->lfRegistEmailMobile($objFormParam->getValue('email_mobile'),
                    $objCustomer->getValue('customer_id'));

                $objCustomer->setValue('email_mobile', $email_mobile);
                $this->tpl_mainpage = 'entry/email_mobile_complete.tpl';
                $this->tpl_title = '携帯メール登録完了';
            }
        }

        $this->tpl_name = $objCustomer->getValue('name01');
        $this->arrForm  = $objFormParam->getFormParamList();
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
     * lfInitParam
     *
     * @access public
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        // 2013/12/17 del メールアドレスは小文字変換しない
        $objFormParam->addParam('メールアドレス', 'email_mobile', null, 'a',
                                array('NO_SPTAB', 'EXIST_CHECK', 'EMAIL_CHAR_CHECK', 'EMAIL_CHECK', 'MOBILE_EMAIL_CHECK'));
    }

    /**
     * エラーチェックする
     *
     * @param mixed $objFormParam
     * @param mixed $objCustomer
     * @access private
     * @return array エラー情報の配列
     */
    function lfCheckError(&$objFormParam) {
        $objFormParam->convParam();
        $objErr         = new SC_CheckError_Ex();
        $objErr->arrErr = $objFormParam->checkError();

        // FIXME: lfInitParam() で設定すれば良いように感じる
        $objErr->doFunc(array("メールアドレス", "email_mobile"), array("CHECK_REGIST_CUSTOMER_EMAIL"));

        return $objErr->arrErr;
    }

    /**
     *
     * 携帯メールアドレスが登録されていないユーザーに携帯アドレスを登録する
     *
     * 登録完了後にsessionのemail_mobileを更新する
     *
     * @param mixed $objFormParam
     * @param mixed $objCustomer
     * @access private
     * @return void
     */
    function lfRegistEmailMobile($email_mobile, $customer_id) {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->update('dtb_customer',
                          array('email_mobile' => $email_mobile),
                          'customer_id = ?', array($customer_id));

        return $email_mobile;
    }
}
?>
