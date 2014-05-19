<?php
// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';
require_once(MODULE_REALDIR . 'mdl_smbc/inc/include.php');
require_once(MDL_SMBC_CLASS_PATH . 'SC_SMBC_Data.php');

/**
 * ご注文完了 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:
 */
class LC_Page_Mdl_SMBC_Shopping_Complete extends LC_Page_Ex {

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

        $objQuery = new SC_Query();

        // 前のページで正しく登録手続きが行われた記録があるか判定
        $arrOrderForCheck = $this->objSmbcData->getOrderTemp($_SESSION['order_id']);
        if ($arrOrderForCheck['status'] == ORDER_PENDING) {
            $objPurchase = new SC_Helper_Purchase_Ex();
            $objPurchase->sfUpdateOrderStatus($_SESSION['order_id'], ORDER_NEW);
            $objPurchase->sendOrderMail($_SESSION['order_id']);
        }

        // その他情報の取得
        $arrResults = $objQuery->getall("SELECT memo02 FROM dtb_order WHERE order_id = ? ", array($_SESSION['order_id']));

        if (count($arrResults) > 0) {
            if (isset($arrResults[0]["memo02"])) {
                // 完了画面で表示する決済内容
                $arrOther = unserialize($arrResults[0]["memo02"]);

                // データを編集
                foreach($arrOther as $key => $val){
                    // URLの場合にはリンクつきで表示させる
                    if (preg_match('/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/', $val["value"])) {
                        $arrOther[$key]["value"] = "<a href='#' onClick=\"window.open('". $val["value"] . "'); \" >" . $val["value"] ."</a>";
                    }
                }

                $this->arrOther = $arrOther;
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

    function doValidToken() {
        // nothing.
    }
}
?>
