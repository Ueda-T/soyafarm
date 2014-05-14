<?php
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';
require_once CLASS_EX_REALDIR . 'helper_extends/SC_Helper_CSV_Ex.php';

class LC_Page_Admin_Order_ExpMixRegular extends LC_Page_Admin_Ex {
    function init() {
        parent::init();

        $this->tpl_mainpage = 'order/mix_regular.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subnavi = 'order/subnavi.tpl';
        $this->tpl_subno = 'export_mix_regular';

        $this->httpCacheControl('nocache');
    }

    function process() {
        $this->action();
        $this->sendResponse();
    }

    function action() {
        switch ($this->getMode()) {
        case 'search':
	    $this->doExportCsv();
	    break;
        default:
            break;
        }
    }

    function doExportCsv() {
	$sql =<<<__EOS
    select a.customer_id
         , c.customer_cd
         , c.name
      from dtb_customer c

inner join (select h.customer_id
                 , d.todoke_day
	         , d.todoke_week
	         , d.todoke_week2
	         , d.course_cd
	         , min(p.status)
	         , max(p.status)
	    from dtb_regular_order_detail d
      inner join dtb_products p
	      on d.product_id = p.product_id
	     and p.brand_id = 1
      inner join dtb_regular_order h
	      on h.regular_id = d.regular_id
	     and h.status < 2
	   where d.status = 1
	group by h.customer_id
	       , d.todoke_day
	       , d.todoke_week
	       , d.todoke_week2
	       , d.course_cd
	  having min(p.status) <> max(p.status)) a
        on a.customer_id = c.customer_id
;
__EOS;

        $header = array("顧客ID", "顧客番号", "顧客名");
        $objCsv = new SC_Helper_CSV_Ex();
	$objCsv->sfDownloadCsvFromSql($sql, null, "mixture", $header, true);
	exit;
    }
}
?>