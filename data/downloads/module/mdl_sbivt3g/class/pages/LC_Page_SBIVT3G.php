<?php
/**
 * LC_Page_SBIVT3G.php - LC_Page_SBIVT3G クラスを定義
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: LC_Page_SBIVT3G.php 236 2014-02-04 05:02:23Z kaji $
 * @link        http://www.veritrans.co.jp/3gps
*/

require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 *
 * 当該モジュール ページ基底クラス
 *
 * @category    Veritrans
 * @package     Lib
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version    Release: @package_version@
 * @link        http://www.veritrans.co.jp/3gps
 * @access  public
 * @author  K.Hiranuma
 */
class LC_Page_SBIVT3G extends LC_Page_Ex {

    // {{{ properties

    /** 店舗別設定ヘルパー */
    var $objSetting;

    /** レスポンス情報の配列 */
    var $arrRes;

    /** 受注情報の配列 */
    var $arrOrder;

    /** Net_UserAgent_Mobileオブジェクト */
    var $objMobile;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @access public
     * @return void
     */
    function init() {
        parent::init();

        // 店舗別設定ヘルパー
        $this->objSetting = SC_Helper_SBIVT3G_Setting::getSingletonInstance();

        // モバイル判定用のオブジェクト
        $this->objMobile = new Net_UserAgent_Mobile();

        // 表示設定
        $this->tpl_column_num = MDL_SBIVT3G_COLUMN_NUM;
        $this->arrPageLayout['header_chk'] = MDL_SBIVT3G_HEADER_CHK;
        $this->arrPageLayout['footer_chk'] = MDL_SBIVT3G_FOOTER_CHK;
        // ただしモバイルはリンク制御できないのでフッタを除去 
        if ($this->objMobile->isMobile() == true) {
            $this->arrPageLayout['footer_chk'] = '2';
        }

        // 表示テンプレート(デフォルトは共通エラー)
        $this->tpl_mainpage = $this->getTplPath('error.tpl');

        // 受注情報取得
        $objPurchase = new SC_Helper_Purchase_Ex();
        $this->arrOrder = $objPurchase->getOrder($this->getOrderId());

        // サブタイトル設定
        $this->tpl_subtitle = $this->arrOrder['payment_method'];

        // 実行時間最大値設定
        $this->setTimeLimit();
    }

    /**
     * Page のプロセス.
     *
     * @access public
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @access public
     * @return void
     */
    function action() {
    }

    /**
     * 実行タイムアウトの設定
     *
     * @access protected
     * @return void
     */
    function setTimeLimit() {
        // tgMDKのタイムアウトが120秒なので1回分の通信までは動作するように
        set_time_limit(MDL_SBIVT3G_STANDARD_EXECUTE_LIMIT);
    }

    /**
     * レスポンス配列を初期化する
     *
     * @access protected
     * @return array レスポンス配列初期値
     */
    function initArrRes() {
        $arrRes = array(
            'isOK'        => false,
            'vResultCode' => 'error',
            'mErrMsg'     => 'システムエラーが発生しました',
        );
        return $arrRes;
    }

    /**
     * 処理モードを返す
     *
     * @access protected
     * @return string 処理モード
     */
    function getMode() {
        $mode = (isset($_GET['mode']))? $_GET['mode'] : '';
        $mode = (isset($_POST['mode']))? $_POST['mode'] : $mode;
        return $mode;
    }

    /**
     * 受注番号を返す
     *
     * @access protected
     * @return integer 受注番号
     */
    function getOrderId() {
        return $_SESSION['order_id'];
    }

    /**
     * 3G MDK仕様の受注番号を返す
     *
     * @access protected
     * @param integer $orderId 受注番号
     * @return string ゼロパディングされた受注番号
     */
    function getMdkOrderId($orderId) {
        return GC_Utils_SBIVT3G::getMdkOrderId($orderId);
    }

    /**
     * デバイスごとのテンプレートファイルパスを取得
     *
     * @access protected
     * @param string $tplFile 対象のSmartyテンプレートファイル名
     * @return void
     */
    function getTplPath($tplFile) {
        switch (SC_Display::detectDevice()) {
        case DEVICE_TYPE_MOBILE:
            $dev = 'mobile'; 
            break;
        case DEVICE_TYPE_SMARTPHONE:
            $dev = 'sphone';
            break;
        default:
            $dev = 'default';
            break;
        }
        return MDL_SBIVT3G_TPL_PATH . $dev .DS. $tplFile;
    }

    /**
     * 注文情報、会員情報からモバイル(or PC)メールアドレスの取得を試みる
     *
     * @access protected
     * @param array $arrOrder 受注情報配列
     * @param boolean $isMobile true:モバイルアドレス false:非モバイルアドレス
     * @return mixed メールアドレス or false
     */
    function getValidMailAddr($arrOrder, $isMobile = true) {

        // 注文情報のメアド取得
        $arrAddr[] = $arrOrder['order_email'];

        // 会員情報クラス
        $objCustomer = new SC_Customer_Ex();
        if ($objCustomer->isLoginSuccess() == true) {
            if ($isMobile == true) {
                // モバイルメールアドレスの取得を試みる
                $arrAddr[] = $objCustomer->getValue('email_mobile');
            } else {
                // PCメールアドレスの取得を試みる
                $arrAddr[] = $objCustomer->getValue('email');
            }
        }

        // 順に走査
        $returnMailAddr = false;
        foreach ($arrAddr as $addr) {
            $isMobMail = SC_Helper_Mobile_Ex::gfIsMobileMailAddress($addr);
            if ($isMobile == true && $isMobMail == true) {
                // モバイルアドレス発見
                $returnMailAddr = $addr;
                break;
            } else if ($isMobile == false && $isMobMail == false) {
                // 非モバイルアドレス発見
                $returnMailAddr = $addr;
                break;
            }
        }
        return $returnMailAddr;
    }

    /**
     * 注文IDをインクレメント
     *
     * @access protected
     * @return void
     */
    function revolveOrderId() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $logger =& TGMDK_Logger::getInstance();

        // 現行の受注番号を取得
        $orderId = $this->getOrderId();

        // セッションの値を更新
        $_SESSION['order_id'] = $objQuery->nextval('dtb_order_order_id');

        // 受注番号更新
        $arrMod['order_id'] = $this->getOrderId();
        $arrCon = array($orderId);
        $objQuery->update('dtb_order', $arrMod, 'order_id = ?', $arrCon);
        $objQuery->update('dtb_order_temp', $arrMod, 'order_id = ?', $arrCon);
        $objQuery->update('dtb_order_detail', $arrMod, 'order_id = ?', $arrCon);
        $objQuery->update('dtb_shipping', $arrMod, 'order_id = ?', $arrCon);
        $objQuery->update('dtb_shipment_item', $arrMod, 'order_id = ?', $arrCon)
;

        $logger->info(sprintf('受注番号更新[%d]', $this->getOrderId()));
    }

    /**
     * 決済モジュールから注文確認画面へ戻る
     *
     * @access protected
     * @return void
     */
    function playBackToConfirm() {
        $logger =& TGMDK_Logger::getInstance();
        $logger->info('確認画面へ戻る');

        // 受注番号を取得
        $orderId = $this->getOrderId();

        $objPurchase = new SC_Helper_Purchase_Ex();

        // ▼ 2013.12.25 mod
        // 2.11系、2.12系のみ実行
        if (GC_Utils_SBIVT3G::compareVersion('2.12.6') <= 0) {
            // 受注情報の復元
            $objPurchase->rollbackOrder($orderId, ORDER_PENDING, true);
            unset($_SESSION['order_id']);
        }
        // ▲ 2013.12.25 mod

        // セッション保護
        $objSiteSess = new SC_SiteSession_Ex();
        $objSiteSess->setRegistFlag();

        // 確認画面へ戻す
        SC_Response_Ex::sendRedirect(SHOPPING_CONFIRM_URLPATH);
    }

    /**
     * 決済モジュールから注文完了画面へ
     *
     * @access protected
     * @return void
     */
    function goToComplete() {
        $logger =& TGMDK_Logger::getInstance();
        $arrOrder =& $this->arrOrder;
        $logger->info('受注完了処理');

        // 受注完了処理
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objPurchase->registerOrder($arrOrder['order_id'], $arrOrder);
        $objPurchase->sendOrderMail($arrOrder['order_id']);

        // セッション保護
        $objSiteSess = new SC_SiteSession_Ex();
        $objSiteSess->setRegistFlag();

        $logger->info('注文完了画面へ');

        // 完了画面へリダイレクト
        SC_Response_Ex::sendRedirect(SHOPPING_COMPLETE_URLPATH);
    }
}
?>
