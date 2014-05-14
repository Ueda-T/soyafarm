<?php
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';
require_once CLASS_EX_REALDIR . 'helper_extends/SC_Helper_CSV_Ex.php';

class LC_Page_Admin_Mail_Export extends LC_Page_Admin_Ex {
    function init() {
        parent::init();
        $this->tpl_mainpage = 'mail/export.tpl';

        $this->tpl_mainno = 'customer';
        $this->tpl_subnavi = 'customer/subnavi.tpl';
        $this->tpl_subno = 'export';
        $this->tpl_maintitle = 'メルマガ管理';
        $this->tpl_subtitle = 'データ出力';

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
select
    cust.customer_id as customer_id
   ,cust.name as name
   ,cust.email as email
   ,sex.name as sex
   ,pref.name as pref
   ,date_format(cust.create_date, "%Y/%m/%d") as regist
   ,date_format(cust.birth, "%Y/%m/%d") as birth
   ,case
       when cust.customer_kbn = 0 then "一般"
       when cust.customer_kbn = 1 then "社員"
       when cust.customer_kbn = 2 then "公用"
       else "" end as kubun
from
    dtb_customer cust
left join
    mtb_sex sex on sex.id = cust.sex
inner join
    mtb_pref pref on pref.id = cust.pref
where
    cust.del_flg = 0
    and cust.mailmaga_flg = 1
__EOS;
	$header = array("顧客番号", "氏名", "メールアドレス", "性別", "都道府県",
			"登録日", "生年月日", "顧客区分");

        $objCsv = new SC_Helper_CSV_Ex();
	$objCsv->sfDownloadCsvFromSql($sql, null, "mailmag", $header, true);
	exit;
    }
}
?>
