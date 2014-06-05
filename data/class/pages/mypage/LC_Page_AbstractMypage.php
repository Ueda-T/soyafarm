<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * Mypage の基底クラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_AbstractMypage.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_AbstractMypage extends LC_Page_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        // mypage 共通
        $this->tpl_title        = 'マイページ';
        $this->tpl_navi         = 'mypage/navi.tpl';
        $this->tpl_mainno       = 'mypage';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        parent::process();
        // ログインチェック
        $objCustomer = new SC_Customer_Ex();

        // ログインしていない場合は必ずログインページを表示する
        if($objCustomer->isLoginSuccess(true) === false) {
            // クッキー管理クラス
            $objCookie = new SC_Cookie_Ex(COOKIE_EXPIRE);
            // クッキー判定(メールアドレスをクッキーに保存しているか）
            $this->tpl_login_email = $objCookie->getCookie('login_email');
            if($this->tpl_login_email != "") {
                $this->tpl_login_memory = "1";
            }

            // POSTされてきたIDがある場合は優先する。
            if(isset($_POST['login_email'])
               && $_POST['login_email'] != "") {
                $this->tpl_login_email = $_POST['login_email'];
            }

            /* #351 簡単ログイン機能を廃止
            // 携帯端末IDが一致する会員が存在するかどうかをチェックする。
            if (SC_Display_Ex::detectDevice() === DEVICE_TYPE_MOBILE){
                $this->tpl_valid_phone_id = $objCustomer->checkMobilePhoneId();
            }
             */

            //$this->tpl_title      = 'マイページ(ログイン)';
            //$this->tpl_mainpage   = 'mypage/login.tpl';
            //$this->tpl_column_num = 1;

            $this->tpl_title      = '';
            $this->tpl_subtitle   = '';
            $layout = new SC_Helper_PageLayout_Ex();
            $url = preg_replace('|^' . preg_quote(HTTPS_URL) . '|', '', URL_MYPAGE_TOP);
            $layout->sfGetPageLayout($this, false, $url,
                                 $this->objDisplay->detectDevice());

        } else {
            //マイページ会員情報表示用共通処理
            $this->tpl_login     = true;
            $this->CustomerName = $objCustomer->getvalue('name');
            $this->CustomerPoint = $objCustomer->getvalue('point');
            $this->CustomerPointValidDate = $objCustomer->getvalue('point_valid_date');
            $this->CustomerBirthPoint = $objCustomer->getvalue('birth_point');
            $this->CustomerBirthPointValidDate = $objCustomer->getvalue('birth_point_valid_date');
            $this->CustomerId = $objCustomer->getvalue('customer_id');
            $this->action();
//            print_r($this);
        }
        $this->sendResponse();
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }
}
