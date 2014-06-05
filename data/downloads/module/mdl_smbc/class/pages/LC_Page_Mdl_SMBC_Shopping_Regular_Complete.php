<?php
// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';
require_once(MODULE_REALDIR . 'mdl_smbc/inc/include.php');
require_once(MDL_SMBC_CLASS_PATH . 'SC_SMBC_Data.php');

/**
 * クレジットカードでご注文完了 のページクラス.
 * LC_Page_Shopping_Completeとほぼ同じ
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:
 */
class LC_Page_Mdl_SMBC_Shopping_Regular_Complete extends LC_Page_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        $this->skip_load_page_layout = true;
        parent::init();
        $template = MDL_SMBC_TEMPLATE_PATH . "complete";
        $template .= SC_MobileUserAgent::isMobile() ? '_mobile' : '';
        $template .= SC_SmartphoneUserAgent::isSmartphone() ? '_sphone' : '';
        $this->tpl_mainpage = $template.'.tpl';
        $this->tpl_title = "ご注文完了";
		$this->tpl_column_num = 1;  //左右にカラムのない画面（1カラムの画面）であることを指定

        // スマートフォンの場合はEC-CUBE標準のフレームを使わない
        if(SC_SmartphoneUserAgent::isSmartphone()){
            $this->template = MDL_SMBC_TEMPLATE_PATH.'sphone_frame.tpl';
        }
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
        unset($_SESSION['order_id']);
        unset($_SESSION['regular_order_id']);
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $this->objSmbcData = new SC_SMBC_Data();
        $this->arrInfo = SC_Helper_DB_Ex::sfGetBasisData();

        if(!empty($_POST['mode']) && $_POST['mode'] == 'send' && !empty($_SESSION['credit_regist'])){
            $template = MDL_SMBC_TEMPLATE_PATH . "credit_complete";
            $template .= SC_MobileUserAgent::isMobile() ? '_mobile' : '';
            $template .= SC_SmartphoneUserAgent::isSmartphone() ? '_sphone' : '';
            $this->tpl_mainpage = $template.'.tpl';
   
        }else{
            $this->completeOrder();
            unset($_SESSION['regular_order_id']);
            unset($_SESSION['MOVE_SMBC']);

            // 完了画面へリダイレクト
            SC_Response_Ex::sendRedirect(SHOPPING_COMPLETE_URLPATH);
        }
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
    }
    
    function doValidToken() {
    }
    
    /**
     * dtb_order 及び関連のレコードを登録する.
     */
    public function completeOrder() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objCustomer = new SC_Customer_Ex();
        $objCartSession = new SC_CartSession_Ex();

        $_SESSION['regular_order_id'] = $_SESSION['order_id'];

	// 顧客情報に取引IDが設定されていない場合セットする
	$sql =<<<EOF
SELECT torihiki_id
FROM dtb_customer
WHERE customer_id = ?
AND torihiki_status = 1
EOF;
	$customerId = $objCustomer->getValue('customer_id');
	$torihikiId = $objQuery->getOne($sql, array($customerId));

        $objQuery->begin();
        
	if (!$torihikiId) {
	    $torihikiId = str_pad($customerId, 14, "0", STR_PAD_LEFT);
	    $arrCustomer = array('torihiki_id' => $torihikiId,
				'torihiki_status' => 1,
				'send_flg' => 0,
				'updator_id' => $customerId,
				'update_date' => 'Now()');
	    $where = "customer_id = ?";
	    $objQuery->update("dtb_customer", $arrCustomer
			    , $where, array($customerId));
	}
        $arrRegularOrder = array();
        $arrRegularOrder['del_flg'] = '0';
        $arrRegularOrder['regular_status'] = MDL_SMBC_REGULAR_STATUS_NONE;
        $arrRegularOrder['update_date'] = 'CURRENT_TIMESTAMP';
        $objQuery->update("dtb_mdl_smbc_regular_order", $arrRegularOrder, "order_id = ?", array($_SESSION['regular_order_id']));

        $arrResult = $objQuery->getRow('*', 'dtb_mdl_smbc_regular_order', 'order_id = ?', array($_SESSION['regular_order_id']));
        
        $arrOrder = array();
        $arrOrder['memo01'] = '管理番号: ' . $arrResult['shoporder_no'] . PHP_EOL;
        $arrOrder['memo01'] .= '請求年月: ' . substr($arrResult['target_ym'], 0, 4) . '/' . substr($arrResult['target_ym'], 4, 2);
        $arrOrder['status'] = ORDER_NEW;
        $arrOrder['del_flg'] = '0';
        $arrOrder['update_date'] = 'CURRENT_TIMESTAMP';
        //$objQuery->update("dtb_order", $arrOrder, "order_id = ?", array($_SESSION['regular_order_id']));
        
        // メールを送信
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objPurchase->registerOrder($_SESSION['regular_order_id'], $arrOrder);
        $objPurchase->sendOrderMail($_SESSION['regular_order_id']);

        $objQuery->commit();

        // SESSION情報破棄
        $cartkey = $objCartSession->getKey();
        $objPurchase->cleanupSession($_SESSION['regular_order_id'], $objCartSession, $objCustomer, $cartkey);
    }
}
?>
