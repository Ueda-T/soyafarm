<?php
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';
require_once(MDL_SMBC_CLASS_PATH . 'SC_SMBC_Page.php');

/**
 * ネットバンク決済情報入力画面 のページクラス.
 *
 * @package Page
 */
class LC_Page_Mdl_SMBC_Netbank extends LC_Page_Ex {

    // 画面連携管理クラス
    var $objSmbcPage;

    // 送信データ用配列
    var $arrParam;

    // 送信先URL
    var $server_url;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        // 現在のページが、決済画面であることを伝える。
        $this->page_mdl_smbc = true;

        $this->skip_load_page_layout = true;
        parent::init();
        $this->objSmbcPage = new SC_SMBC_Page();

        $this->tpl_onload="document.form1.submit();";
        $this->tpl_title = "決済情報送信";

        // 送信用データの配列初期化
        $this->initArrParam();

        // テンプレートの設定
        $template = MDL_SMBC_TEMPLATE_PATH . 'page_link';
        $template .= SC_MobileUserAgent::isMobile() ? '_mobile' : '';
        $template .= SC_SmartphoneUserAgent::isSmartphone() ? '_sphone' : '';

        if (SC_MobileUserAgent::isMobile() == true) {
            $this->tpl_mainpage = $template.'.tpl';
        } else {
            $this->template = $template.'.tpl';
        }

        $this->allowClientCache();
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
        $objCartSess = new SC_CartSession();
        $objSiteSess = new SC_SiteSession();
        $_SESSION['MDL_SMBC']['order_id'] = $_SESSION['order_id'];

        // 連携データを取得
        $arrParam = $this->objSmbcPage->makeParam($_SESSION['order_id']);

        // 決済用連携データを作成
        $this->arrParam = $this->makeParam($arrParam);

        // 接続先
        if($this->arrParam['connect_url'] == "real"){
            // 本番用
            $connect_url_PC = MDL_SMBC_PAGE_LINK_PC_URL_REAL;
            $connect_url_SP = MDL_SMBC_PAGE_LINK_SP_URL_REAL;
            $connect_url_MB = MDL_SMBC_PAGE_LINK_MOBILE_URL_REAL;
        }else{
            // テスト用
            $connect_url_PC = MDL_SMBC_PAGE_LINK_PC_URL_TEST;
            $connect_url_SP = MDL_SMBC_PAGE_LINK_SP_URL_TEST;
            $connect_url_MB = MDL_SMBC_PAGE_LINK_MOBILE_URL_TEST;
        }
        unset($this->arrParam['connect_url']);

        // PC/SP/MBによって送信先URLを切り替え
        if(SC_MobileUserAgent::isMobile()){//MB
            $this->server_url = $connect_url_MB;
        }elseif(SC_SmartphoneUserAgent::isSmartphone()){//SP
            $this->server_url = $connect_url_SP;
        }else{//PC
            $this->server_url = $connect_url_PC;
        }

        // 送信データを設定する
        $this->objSmbcPage->setParam($this->arrParam);

        // 送信データのサイズ調整・エンコード変換を行う
        $this->arrParam = $this->objSmbcPage->convParamStr();

        // 送信データをログ出力
        $this->objSmbcPage->printLog($this->arrParam);

        $_SESSION['credit_regist'] = false;
        unset($_SESSION['MDL_SMBC']);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
    }

    /**
     * ネットバンク決済に関する送信データ項目の配列の初期化
     */
    function initArrParam() {
        $this->objSmbcPage->addArrParam("bill_method", 2, MDL_SMBC_TO_ENCODE);
        $this->objSmbcPage->addArrParam("kessai_id", 4, MDL_SMBC_TO_ENCODE);
    }

    /**
     * ネットバンク決済用連携データを設定
     *
     */
    function makeParam($arrParam) {
        // 決済手段区分
        $arrParam['bill_method'] = MDL_SMBC_NETBUNK_BILL_METHOD;

        // 決済種類コード。
        $arrParam['kessai_id'] = MDL_SMBC_NETBUNK_KESSAI_ID;

        return $arrParam;
    }
}
?>