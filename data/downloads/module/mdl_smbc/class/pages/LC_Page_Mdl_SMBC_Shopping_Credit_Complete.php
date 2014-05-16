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
class LC_Page_Mdl_SMBC_Shopping_Credit_Complete extends LC_Page_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $template = MDL_SMBC_TEMPLATE_PATH . "complete";
        $template .= SC_MobileUserAgent::isMobile() ? '_mobile' : '';
        $template .= SC_SmartphoneUserAgent::isSmartphone() ? '_sphone' : '';
        $this->tpl_mainpage = $template.'.tpl';
        $this->tpl_title = "ご注文完了";

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
            // カード登録
            $this->lfCardRegist();
            $template = MDL_SMBC_TEMPLATE_PATH . "credit_complete";
            $template .= SC_MobileUserAgent::isMobile() ? '_mobile' : '';
            $template .= SC_SmartphoneUserAgent::isSmartphone() ? '_sphone' : '';
            $this->tpl_mainpage = $template.'.tpl';
        }else{
            // 前のページで正しく登録手続きが行われた記録があるか判定
            $arrOrderForCheck = $this->objSmbcData->getOrderTemp($_SESSION['order_id']);
            if ($arrOrderForCheck['status'] == ORDER_PENDING) {
                $objPurchase = new SC_Helper_Purchase_Ex();
                $objPurchase->sfUpdateOrderStatus($_SESSION['order_id'], ORDER_NEW);
                $objPurchase->sendOrderMail($_SESSION['order_id']);
            }
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

    // カードお預かり機能にカードを登録する
    function lfCardRegist(){
        $objQuery = new SC_Query();
        $this->objSmbcData = new SC_SMBC_Data();
        $payment_id = $objQuery->select("payment_id", "dtb_order", "order_id = ?", array($_SESSION['order_id']));
        $arrParam = $this->objSmbcData->getModuleMasterData($payment_id[0]['payment_id']);

        $arrParam['customer_id'] = $_SESSION['customer']['customer_id'];
        $arrParam['kessai_no'] = $_SESSION['smbc_kessai_no'];
        unset($_SESSION['credit_regist']);
        unset($_SESSION['smbc_kessai_no']);

        // 登録
        $arrResponse = $this->objSmbcData->checkCardInfo($arrParam, "01");
        if($arrResponse['rescd'] != MDL_SMBC_RES_OK){
            $this->lfDispError($arrResponse);
        }
    }
    /**
     * 決済ステーションから受け取ったエラー情報を、表示用データにする.
     *
     * @param array $arrResponse 決済ステーションからのレスポンスボディ
     * @return void
     */
    function lfDispError($arrResponse) {
        // 結果内容
        $this->arrErr['res'] = mb_convert_encoding($arrResponse['res'], "UTF-8", "auto");
        // 結果コード
        $this->arrErr['rescd'] = $arrResponse['rescd'];
    }

}
?>
