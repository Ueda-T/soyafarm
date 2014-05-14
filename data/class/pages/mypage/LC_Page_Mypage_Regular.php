<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 
    'page_extends/mypage/LC_Page_AbstractMypage_Ex.php';

/**
 * MyPage 定期購入一覧のページクラス.
 *
 * @package Page
 * @author IQUEVE CO.,LTD.
 * @version $Id:$
 */
class LC_Page_MyPage_Regular extends LC_Page_AbstractMypage_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mypageno = 'regular';
        if (SC_Display_Ex::detectDevice() === DEVICE_TYPE_MOBILE){
            $this->tpl_subtitle = 'MYページ';
        } else {
            $this->tpl_subtitle = '定期購入一覧';
        }
        $this->httpCacheControl('nocache');
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

        $objCustomer = new SC_Customer_Ex();
        $customer_id = $objCustomer->getvalue('customer_id');
        
        $objRegular = new SC_Helper_Regular_Ex();

        // 件数取得
        $detail_count =
            $objRegular->getRegularOrderDetailCount($customer_id);

        //ページ送り用
        $this->objNavi = new SC_PageNavi_Ex(
             $_REQUEST['pageno'],
             $detail_count,
             SEARCH_PMAX,
             'fnNaviPage',
             NAVI_PMAX,
             'pageno=#page#',
             SC_Display_Ex::detectDevice() !== DEVICE_TYPE_MOBILE
         );

        // DBから定期情報一覧を取得
        $this->arrRegularDetail
            = $objRegular->getRegularOrderDetailList(
                $customer_id, $this->objNavi->start_row);

        // 1ページあたりの件数
        $this->dispNumber = SEARCH_PMAX;

    }

}
