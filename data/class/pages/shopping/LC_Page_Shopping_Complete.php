<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * ご注文完了 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Shopping_Complete.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_Shopping_Complete extends LC_Page_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_title = "ご注文完了";
        // ▼2012.03.27 add takao
        $this->arrCompleteOrder = array();
        // ▲2012.03.27 end
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
	global $CLICK_ANALYZER_STATIC;

	// CLICK ANALYZER用埋め込み
	$this->tpl_clickAnalyzer = "";
	if (isset($CLICK_ANALYZER_STATIC["complete"])) {
	    $this->tpl_clickAnalyzer = $CLICK_ANALYZER_STATIC["complete"];
	}

        $this->arrInfo = SC_Helper_DB_Ex::sfGetBasisData();

        // Google Analytics Ecommerce Tracking用
        if (isset($_SESSION['order_id'])) {
            $this->lfSetEcInfo($_SESSION['order_id']);
        }

        $this->tpl_order_id = $_SESSION['order_id'];

        // 2014.02.04
        // コンバージョンタグをセット
        $this->lfSetTag($this, $this->tpl_order_id);

        unset($_SESSION['order_id']);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * 決済モジュールから遷移する場合があるため, トークンチェックしない.
     */
    function doValidToken() {
        // nothing.
    }


    /**
     * 2012.03.27
     * Google Analytics Ecommerce Tracking タグ用の情報を取得、
     * テンプレート変数にセットする
     */
    function lfSetEcInfo($order_id) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sql_order=<<<EOS
SELECT
    ODR.order_id
   ,ODR.subtotal
   ,ODR.total
   ,ODR.payment_total
   ,ODR.tax
   ,ODR.deliv_fee
   ,ODR.order_addr01
   ,PRF.name AS pref_name
   ,DTL.product_name
   ,DTL.product_code
   ,DTL.price
   ,DTL.quantity
FROM
    dtb_order ODR
    INNER JOIN dtb_order_detail DTL
        ON ODR.order_id = DTL.order_id
    INNER JOIN mtb_pref  PRF
        ON ODR.order_pref = PRF.id
WHERE
    ODR.order_id = ?
    AND DTL.product_id != 0
EOS;
        $this->arrCompleteOrder = $objQuery->getall($sql_order, array($order_id));
    }

    /**
     * 計測用タグをセットする
     *
     *
     */
    function lfSetTag(&$thisPage, $order_id = 0) {

        $objTagView = new SC_SiteView_Ex();
        $objPage = new LC_Page_Ex();

        // WEB注文番号
        $objPage->tpl_order_id = $order_id;
        // 売上合計
        /*
        $objPage->tpl_amount =
            $this->arrCompleteOrder[0]['payment_total'];
         */
        // 2014.03.20 takao
        // 購入商品の合計のみセット
        $objPage->tpl_amount =
            $this->arrCompleteOrder[0]['subtotal'];

        // アサイン
        $objTagView->assignobj($objPage);

        // タグテンプレートパス取得
        $tag_tpl_dir = "";

        // 端末判定
        $device = SC_Display_Ex::detectDevice();
        switch ($device){
        case DEVICE_TYPE_SMARTPHONE:
                // スマートフォン
                $tag_tpl_dir = SMARTPHONE_TAG_TEMPLATE_REALDIR;
                break;
            case DEVICE_TYPE_PC:
                // PC 
                $tag_tpl_dir = TAG_TEMPLATE_REALDIR;
                break;
        }

        // 各種タグテンプレートをセット
        if (!empty($tag_tpl_dir)) {
            // UNITAGセット
            $thisPage->tpl_tag_unitag = 
                $objTagView->fetch($tag_tpl_dir . "unitag.tpl");
        }
    }
}
?>
