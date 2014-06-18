<?php
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';
require_once CLASS_EX_REALDIR . 'helper_extends/SC_Helper_CSV_Ex.php';
require_once CLASS_REALDIR . 'pages/admin/customer/LC_Page_Admin_Customer_InosImportCustomer.php';
require_once CLASS_EX_REALDIR . 'page_extends/admin/order/LC_Page_Admin_Order_InosImportOrder_Ex.php';
require_once CLASS_EX_REALDIR . 'page_extends/admin/order/LC_Page_Admin_InosImportTeiki_Ex.php';
require_once CLASS_EX_REALDIR . 'page_extends/admin/products/LC_Page_Admin_Products_PromotionImport_Ex.php';

/**
 * 各種インポートCSVのページクラス.
 */
class LC_Page_Admin_Order_InosImport extends LC_Page_Admin_Ex {

    var $arrRowResult;
    var $arrRowErr;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'order/inos_import.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'inos_import_order';
        $this->tpl_maintitle = '受注関連';
        $this->tpl_subtitle = 'INOS各種情報インポート';
        //$this->csv_id = '9';

        set_time_limit(0);
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
        $this->objDb = new SC_Helper_DB_Ex();

        switch ($this->getMode()) {
        case 'csv_upload':
	    // 顧客データ
	    $objCustomerImport = new LC_Page_Admin_Customer_InosImportCustomer();
	    $objCustomerImport->init();
	    $objCustomerImport->process();
	    $this->tpl_customer_err = $objCustomerImport->getResCustomerImport
			    ($this->arrRowErr, $this->arrRowResult);
	    // 受注データ
	    $objOrderImport = new LC_Page_Admin_Order_InosImportOrder_Ex();
	    $objOrderImport->init();
	    $objOrderImport->process();
	    $this->tpl_order_err = $objOrderImport->getResOrderImport
			    ($this->arrRowErr, $this->arrRowResult);
	    // 定期データ
	    $objRegularImport = new LC_Page_Admin_InosImportTeiki_Ex();
	    $objRegularImport->init();
	    $objRegularImport->process();
	    $this->tpl_regular_err = $objRegularImport->getResRegularImport
			    ($this->arrRowErr, $this->arrRowResult);
	    // プロモーションマスタ
	    $objPromotionImport = new LC_Page_Admin_Products_PromotionImport_Ex();
	    $objPromotionImport->init();
	    $objPromotionImport->process();
	    $objPromotionImport->getResPromotionImport
			    ($this->arrRowErr, $this->arrRowResult);
	    $this->tpl_mainpage = 'order/inos_import_allcomplete.tpl';
            break;

        case 'customer_errcsv_download':
	    // 顧客データ
	    $objCustomerImport = new LC_Page_Admin_Customer_InosImportCustomer();
	    $objCustomerImport->init();
	    $objCustomerImport->process();
            exit;

        case 'order_errcsv_download':
	    // 受注データ
	    $objOrderImport = new LC_Page_Admin_Order_InosImportOrder_Ex();
	    $objOrderImport->init();
	    $objOrderImport->process();
            exit;

        case 'regular_errcsv_download':
	    // 定期データ
	    $objRegularImport = new LC_Page_Admin_InosImportTeiki_Ex();
	    $objRegularImport->init();
	    $objRegularImport->process();
	    exit;

        default:
            break;
        }
    }

}

/*
 * fin.
 */
