<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

require_once CLASS_EX_REALDIR . 'page_extends/mypage/LC_Page_AbstractMypage_Ex.php';
require_once(MODULE_REALDIR . 'mdl_smbc/inc/include.php');
require_once(MDL_SMBC_CLASS_PATH . 'SC_Mdl_SMBC.php');
require_once(MDL_SMBC_CLASS_PATH . 'SC_SMBC.php');
require_once(MDL_SMBC_CLASS_PATH . 'SC_SMBC_Page.php');

/**
 * MyPage のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Mypage.php 23182 2013-09-03 08:18:10Z h_yoshimoto $
 */
class LC_Page_Mypage_Regular_List extends LC_Page_AbstractMypage_Ex
{
    /** ページナンバー */
    public $tpl_pageno;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        $this->skip_load_page_layout = true;
        parent::init();
        $this->tpl_mypageno = 'regular_list';
        if (SC_Display_Ex::detectDevice() === DEVICE_TYPE_MOBILE) {
            $this->tpl_subtitle = 'MYページ';
        } else {
            $this->tpl_subtitle = '定期申込一覧';
        }
        // テンプレートの設定
        $template = MDL_SMBC_TEMPLATE_PATH . 'regular_list';
        $template .= SC_MobileUserAgent::isMobile() ? '_mobile' : '';
        $template .= SC_SmartphoneUserAgent::isSmartphone() ? '_sphone' : '';
        $this->tpl_mainpage = $template.'.tpl';
		$this->tpl_column_num = 1; //左右にカラムのない画面（1カラムの画面）であることを指定

        // スマートフォンの場合はEC-CUBE標準のフレームを使わない
        if(SC_SmartphoneUserAgent::isSmartphone()){
            $this->template = MDL_SMBC_TEMPLATE_PATH.'sphone_frame.tpl';
        }

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrCustomerOrderStatus = $masterData->getMasterData('mtb_customer_order_status');

        $this->httpCacheControl('nocache');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        parent::process();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    public function action()
    {

        if (version_compare(ECCUBE_VERSION, '2.13', '>=')) {
            //決済処理中ステータスのロールバック
            $objPurchase = new SC_Helper_Purchase_Ex();
            $objPurchase->cancelPendingOrder(PENDING_ORDER_CANCEL_FLAG);
        }

        $objCustomer = new SC_Customer_Ex();
        $customer_id = $objCustomer->getValue('customer_id');

		if(!SC_Utils_Ex::isBlank($_REQUEST['pageno'])){
			$mode = "move_page";
		}else{
			$mode = $this->getMode();
		}

        switch ($mode) {
            case 'regular_order':
                if (strlen($_POST['order_id']) > 0) {
                    $order_id = intval($_POST['order_id']);
                } else {
                    $order_id = intval($_GET['order_id']);
                }
                $this->lfRegularOrder($order_id);
                break;
            case 'cancel':
                if (strlen($_REQUEST['order_id']) > 0 && strlen($_REQUEST['shoporder_no']) > 0) {
                    $order_id = intval($_REQUEST['order_id']);
                    $shoporder_no = htmlentities($_REQUEST['shoporder_no'], ENT_QUOTES);
                }
                $arrResults = $this->doCancel($shoporder_no, $order_id);
                if (SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE) {
                    $this->message = $arrResults['header']['res'];
                } else {
                    echo SC_Utils_Ex::jsonEncode($arrResults);
                    SC_Response_Ex::actionExit();
                }
            default:
                break;
        }

        //ページ送り用
        $this->objNavi = new SC_PageNavi_Ex($_REQUEST['pageno'],
                                            $this->lfGetOrderHistory($customer_id),
                                            SEARCH_PMAX,
                                            'eccube.movePage',
                                            NAVI_PMAX,
                                            'pageno=#page#',
                                            SC_Display_Ex::detectDevice() !== DEVICE_TYPE_MOBILE);

        $this->arrOrder = $this->lfGetOrderHistory($customer_id, $this->objNavi->start_row);
        foreach (array_keys($this->arrOrder) as $key) {
            $arrProducts = explode('<br />', $this->arrOrder[$key]['product_name']);
            $i = 0;
            foreach ($arrProducts as $arrProduct) {
                $arrProductsClass = explode('/', $arrProduct);
                $this->arrOrder[$key]['products'][$i]['product_name'] = $arrProductsClass[0];
                $this->arrOrder[$key]['products'][$i]['classcategory_name1'] = str_replace('(なし)', '', $arrProductsClass[1]);
                $this->arrOrder[$key]['products'][$i]['classcategory_name2'] = str_replace('(なし)', '', $arrProductsClass[2]);
                $i++;
            }

            $this->arrOrder[$key]['product_name'] = $arrProduct[0];
            $this->arrOrder[$key]['classcategory_name1'] = $arrProduct[1];
            $this->arrOrder[$key]['classcategory_name2'] = $arrProduct[2];
        }
        // 1ページあたりの件数
        $this->dispNumber = SEARCH_PMAX;
        $this->json_customer_order_status = SC_Utils::jsonEncode($this->arrCustomerOrderStatus);
    }

    /**
     * 受注履歴を返す
     *
     * @param mixed $customer_id
     * @param mixed $startno     0以上の場合は受注履歴を返却する -1の場合は件数を返す
     * @access private
     * @return void
     */
    public function lfGetOrderHistory($customer_id, $startno = -1)
    {

        $objQuery   = SC_Query_Ex::getSingletonInstance();
        $col = SC_SMBC::regularOrderSelectSQL();
        $from = SC_SMBC::regularOrderFromSQL();

        $where = " T.del_flg = 0 AND customer_id = ?";
        $arrWhereVal = array($customer_id);
        $order = "T.create_date DESC";

        if ($startno == -1) {
            return $objQuery->count($from, $where, $arrWhereVal);
        }

        $objQuery->setLimitOffset(SEARCH_PMAX, $startno);
        // 表示順序
        $objQuery->setOrder($order);

        $arrRegularOrder = $objQuery->select($col, $from, $where, $arrWhereVal);
        //購入履歴の取得
        return $arrRegularOrder;
    }

    /**
     * 定期受注情報をキャンセルする.
     */
    protected function doCancel($shoporder_no, $order_id) {
        $arrResponse = array('header' => array());
        $objCustomer = new SC_Customer_Ex();
        $customer_id = $objCustomer->getValue('customer_id');
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $arrRegularOrder = $objQuery->select('*', 'dtb_mdl_smbc_regular_order',
                                             'shoporder_no = ? AND order_id = ? AND bill_no = ?',
                                             array($shoporder_no, $order_id, $customer_id));
        if (SC_Utils_Ex::isBlank($arrRegularOrder)) {
            $arrResponse['header']['rescd'] = 'ERROR';
            $arrResponse['header']['res'] = '受注が存在しませんでした。';
            return $arrResponse;
        }

        $objSmbc = new SC_SMBC();
        $objSmbc->addArrParam("version", 3);
        $objSmbc->addArrParam("bill_method", 2);
        $objSmbc->addArrParam("shop_cd", 7);
        $objSmbc->addArrParam("syuno_co_cd", 8);
        $objSmbc->addArrParam("shop_pwd", 20);
        $objSmbc->addArrParam("shoporder_no", 23);
        $objSmbc->addArrParam("bill_no", 14);

        $objMdlSMBC = SC_Mdl_SMBC::getInstance();
        $subData = $objMdlSMBC->getSubData();
        $arrParams = array(
            'version' => MDL_SMBC_REGULAR_DELETE_VERSION,
            'bill_method' => MDL_SMBC_CREDIT_BILL_METHOD,
            'shop_cd' => $subData['regular_shop_cd'],
            'syuno_co_cd' => $subData['regular_syuno_co_cd'],
            'shop_pwd' => $subData['regular_shop_pwd'],
            'kessai_id' => MDL_SMBC_CREDIT_KESSAI_ID,
            'shoporder_no' => $shoporder_no,
            'bill_no' => str_pad($customer_id, 14, "0", STR_PAD_LEFT)
        );

        $objSmbc->setParam($arrParams);
        $connect_url = ($this->subData['connect_url'] == 'real') ? MDL_SMBC_CREDIT_KAKUTEI_LINK_URL_REAL : MDL_SMBC_CREDIT_KAKUTEI_LINK_URL_TEST;
        $arrResponse = $objSmbc->sendParam($connect_url);

        $arrValues['rescd'] = $arrResponse['rescd'];
        $arrValues['res'] = mb_convert_encoding($arrResponse['res'], CHAR_CODE, 'SJIS-win');
        $result = false;
        if ($arrResponse['rescd'] != MDL_SMBC_RES_OK) {
            $arrResponse['header']['rescd'] = $arrValues['rescd'];
            $arrResponse['header']['res'] = $arrValues['res'];
            return $arrResponse;
        } else {
            $arrResponse['header']['rescd'] = 'CANCEL';
            $arrResponse['header']['res'] = '受注をキャンセルしました。';
        }

        $objQuery->begin();
        $objQuery->update('dtb_mdl_smbc_regular_order',
                          array('regular_status' => MDL_SMBC_REGULAR_STATUS_CANCEL,
                                'update_date' => 'CURRENT_TIMESTAMP'),
                          'shoporder_no = ?', array($shoporder_no));
        $objQuery->update('dtb_order',
                          array('status' => ORDER_CANCEL, 'update_date' => 'CURRENT_TIMESTAMP'),
                          'order_id = ?', array($order_id));
        $objQuery->commit();
        return $arrResponse;
    }
    
    function lfRegularOrder($old_order_id) {
        $this->objSmbcPage = new SC_SMBC_Page();
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objProduct = new SC_Product_Ex();
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        
        // テンプレートの設定
        $template = MDL_SMBC_TEMPLATE_PATH . 'page_link';
        $template .= SC_MobileUserAgent::isMobile() ? '_mobile' : '';
        $template .= SC_SmartphoneUserAgent::isSmartphone() ? '_sphone' : '';
        if (SC_MobileUserAgent::isMobile() == true) {
            $this->tpl_mainpage = $template.'.tpl';
        } else {
            $this->template = $template.'.tpl';
        }
        
        $this->arrParam = $this->objSmbcPage->regularMakeParam($old_order_id);

        // 接続先
        if($this->arrParam['connect_url'] == "real"){
            // 本番用
            $connect_url_PC = MDL_SMBC_REGULAR_PAGE_LINK_PC_URL_REAL;
            $connect_url_SP = MDL_SMBC_REGULAR_PAGE_LINK_PC_URL_REAL;
            $connect_url_MB = MDL_SMBC_REGULAR_PAGE_LINK_MOBILE_URL_REAL;
        }else{
            // テスト用
            $connect_url_PC = MDL_SMBC_REGULAR_PAGE_LINK_PC_URL_TEST;
            $connect_url_SP = MDL_SMBC_REGULAR_PAGE_LINK_PC_URL_TEST;
            $connect_url_MB = MDL_SMBC_REGULAR_PAGE_LINK_MOBILE_URL_TEST;
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

        
        $arrRegularOrder = $objQuery->select("*", "dtb_mdl_smbc_regular_order", "order_id = ?", array($old_order_id));
        $this->arrParam['shoporder_no'] = $arrRegularOrder[0]['shoporder_no'];

        // 送信データをログ出力
        $this->objSmbcPage->printLog($this->arrParam);
                
        mb_convert_variables('UTF-8', 'auto', $this->arrParam);

        // 送信データを全角カタカナで送る必要がある項目があるが
        // モバイルの場合SC_Helper_Mobileでob_startを使って、
        // カタカナを半角にする処理が入っているため、そのまま送るとエラーになってしまう。
        // そのため、ob_end_flush()つかって一旦バッファを無くし、再度必要なものだけ設定する
        if (SC_MobileUserAgent::isMobile() == true) {
            while(ob_get_level()) {
                 ob_end_flush();
            }
            mb_http_output('SJIS-win');
            ob_start(array('SC_MobileEmoji', 'handler'));
            ob_start('mb_output_handler');
        }
        
        $objQuery->begin();
        
        // dtb_orderの作成
        $arrOrder = $objQuery->select("*", "dtb_order", "order_id = ?", array($old_order_id));
        $new_order_id = $objQuery->nextVal('dtb_order_order_id');
        $arrOrder[0]['create_date'] = 'CURRENT_TIMESTAMP';
        $arrOrder[0]['update_date'] = 'CURRENT_TIMESTAMP';
        $arrOrder[0]['order_id'] = $new_order_id;
        $arrOrder[0]['status'] = ORDER_PENDING;
        $arrOrder[0]['del_flg'] = 1;
        $arrOrder[0]['order_temp_id'] = SC_Utils_Ex::sfGetUniqRandomId();
        
        $objQuery->insert('dtb_order', $arrOrder[0]);
                      
        // dtb_order_detailの作成
        $arrDetails = $objPurchase->getOrderDetail($old_order_id);
        foreach ($arrDetails as $arrDetail) {
            $arrDetail['order_detail_id'] = $objQuery->nextVal('dtb_order_detail_order_detail_id');
            $arrDetail['order_id'] = $new_order_id;
            $arrDetail = $objQuery->extractOnlyColsOf('dtb_order_detail', $arrDetail);
            
            $objQuery->insert('dtb_order_detail', $arrDetail);
            
            // 在庫を減少させる
            if (!$objProduct->reduceStock($arrDetail['product_class_id'], $arrDetail['quantity'])) {
                SC_Utils_Ex::sfDispSiteError(SOLD_OUT, '', true);
                $objQuery->rollback();
                SC_Response_Ex::actionExit();
            }
        }
        
        // dtb_order_tempの作成
        $arrOrderTemp = $arrOrder[0];
        $arrOrderTemp['order_temp_id'] = $arrOrder[0]['order_temp_id'];
        $arrOrderTemp['del_flg'] = 1;
        $arrOrderTemp['session'] = serialize($arrDetails);
        $sqlval = $objQuery->extractOnlyColsOf('dtb_order_temp', $arrOrderTemp);
        $objQuery->insert('dtb_order_temp', $sqlval);
        
        // dtb_shipping を作成
        $arrShippings = $objPurchase->getShippings($old_order_id);
        foreach ($arrShippings as $arrShipping) {
            $arrShipping['order_id'] = $new_order_id;
            $arrShipping['shipping_commit_date'] = '';
            $arrShipping['create_date'] = 'CURRENT_TIMESTAMP';
            $arrShipping['update_date'] = 'CURRENT_TIMESTAMP';
            $arrShipping = $objQuery->extractOnlyColsOf('dtb_shipping', $arrShipping);
            $objQuery->insert('dtb_shipping', $arrShipping);

            // dtb_shipment_item を作成
            $arrShipItems = $objPurchase->getShipmentItems($old_order_id, $arrShipping['shipping_id']);
            foreach ($arrShipItems as $arrShipItem) {
                $arrShipItem['shipping_id'] = $arrShipping['shipping_id'];
                $arrShipItem['order_id'] = $new_order_id;
                $arrShipItem = $objQuery->extractOnlyColsOf('dtb_shipment_item', $arrShipItem);
                $objQuery->insert('dtb_shipment_item', $arrShipItem);
            }
        }

        // dtb_mdl_smbc_regular_customer を作成
        $arrNewRegularOrder = $arrRegularOrder[0];
        $arrNewRegularOrder['rescd'] = '';
        $arrNewRegularOrder['res'] = '';
        $arrNewRegularOrder['order_id'] = $new_order_id;
        $arrNewRegularOrder['regular_status'] = MDL_SMBC_REGULAR_STATUS_NONE;
        $arrNewRegularOrder['del_flg'] = 1;
        if (SC_Utils_Ex::isBlank($arrRegularOrder['target_ym'])) {
            // 空の場合は翌月
            $arrNewRegularOrder['target_ym'] = date('Ym', mktime(0, 0, 0, date('m') + 1, 1, date('Y')));
        } else {
            $target_year = substr($arrRegularOrder['target_ym'], 0, 4);
            $target_month = substr($arrRegularOrder['target_ym'], 4, 2);
            $arrNewRegularOrder['target_ym'] = date('Ym', mktime(0, 0, 0, $target_month + 1, 1, $target_year));
        }
        $arrNewRegularOrder['create_date'] = 'CURRENT_TIMESTAMP';
        $arrNewRegularOrder['update_date'] = 'CURRENT_TIMESTAMP';

        $objQuery->insert('dtb_mdl_smbc_regular_order', $arrNewRegularOrder);
        
        $objQuery->commit();

        $_SESSION['regular_order_id'] = $new_order_id;
        $_SESSION['credit_regist'] = false;   
    }
}
