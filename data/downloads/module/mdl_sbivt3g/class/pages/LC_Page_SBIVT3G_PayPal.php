<?php
/**
 * LC_Page_SBIVT3G_PayPal.php - LC_Page_SBIVT3G_PayPal クラスを定義
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: LC_Page_SBIVT3G_PayPal.php 185 2012-07-30 07:20:45Z hira $
 * @link        http://www.veritrans.co.jp/3gps
*/


require_once MDL_SBIVT3G_PAGE_PATH . 'LC_Page_SBIVT3G.php';

/**
 * 3Gモジュール PayPal決済ページクラス
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
class LC_Page_SBIVT3G_PayPal extends LC_Page_SBIVT3G {

    // {{{ properties
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
    }

    /**
     * Page のアクション.
     *
     * @access public
     * @return void
     */
    function action() {

        // モード取得
        $mode = $this->getMode();

        // モードに沿って処理
        switch ($mode) {
        case 'back' :
            // 確認画面へ
            $this->playBackToConfirm();
            exit();
            break;
        case 'exec' :
            // PayPal決済(DO)処理
            if ($this->paypalDoExecute() == false) {
                // エラー終了
                break;
            }
            // 完了画面へ
            $this->goToComplete();
            exit();
            break;
        default :
            // ユーザーのブラウザを判定
            if ($this->objMobile->isMobile() == true
            || $this->objMobile->isSmartphone() == true) {
                $this->arrRes = $this->initArrRes();
                $this->arrRes['mErrMsg'] = MDL_SBIVT3G_PAYPAL_UNSUPPORT_MSG;
                break;
            }
            // PayPal決済(SET)処理
            if ($this->paypalSetExecute() == false) {
                // エラー終了
                break;
            }
            // PayPalログイン画面へ
            header('Location: ' . $this->arrRes['loginUrl']);
            exit();
            break;
        }
        // ここまでで遷移していなければエラー扱い
        if (is_array($this->arrRes) == false) {
            $this->arrRes = $this->initArrRes();
        }
    }

    /**
     * POST アクセスの妥当性を検証する.
     *
     * @access protected
     * @param boolean $is_admin 管理画面でエラー表示をする場合 true
     * @return void
     * @see LC_Page::doValidToken()
     */
    function doValidToken($is_admin = false) {
        // PayPalから返ってきた時はチェックしない
        if ( $this->getMode() == 'exec') {
            return; 
        }
        parent::doValidToken($is_admin);
    }

    /**
     * PayPal決済実行
     *
     * @access protected
     * @return boolean 処理の成功・失敗
     */
    function paypalSetExecute() {
        $logger =& TGMDK_Logger::getInstance();
        $arrOrder =& $this->arrOrder;

        // 要求電文パラメータ値の指定
        if ($this->objSetting->get('P_captureFlg') == true) {
            // 与信同時売上
            $objRequest = new PayPalCaptureRequestDto();
        } else {
            // 与信のみ
            $objRequest = new PaypalAuthorizeRequestDto();
        }

        // 受注番号(ゼロパディング)
        $objRequest->setOrderId($this->getMdkOrderId($arrOrder['order_id']));

        // 決済金額
        $objRequest->setAmount($arrOrder['payment_total']);

        // アクションタイプ : 固定値"set"
        $objRequest->setAction('set');

        // 戻り先URL
        $objRequest->setReturnUrl(MDL_SBIVT3G_PAYPAL_RETURN_URL);

        // キャンセルURL
        $objRequest->setCancelUrl(MDL_SBIVT3G_PAYPAL_CANCEL_URL);

        // 配送先フラグ : 固定値"0":無効
        $objRequest->setShippingFlag('0');

        // オーダー説明
        $objRequest->setOrderDescription($this->objSetting->get('P_note'));

        // 実行
        $logger->info('PayPal決済通信実行');
        $objTransaction = new TGMDK_Transaction();
        $objResponse = $objTransaction->execute($objRequest);

        // レスポンスの初期化
        $this->arrRes = $this->initArrRes();

        // レスポンス検証
        if (isset($objResponse) == false) {
            // システムエラー
            $logger->fatal('レスポンス生成に失敗');
            return false;
        }

        // 結果コード取得
        $this->arrRes['mStatus'] = $objResponse->getMStatus();
        // 詳細コード取得
        $this->arrRes['vResultCode'] = $objResponse->getVResultCode();
        // エラーメッセージ取得
        $this->arrRes['mErrMsg'] = $objResponse->getMerrMsg();

        // 正常終了？
        if ($this->arrRes['mStatus'] == MLD_SBIVT3G_MSTATUS_OK) {
            $this->arrRes['isOK'] = true;
            // 取引ID取得
            $this->arrRes['loginUrl'] = $objResponse->getLoginUrl();
        }
        $logger->debug(print_r($this->arrRes, true));

        // 結果を返す
        if ($this->arrRes['isOK'] == true) {
            return true;
        }
        return false;
    }

    /**
     * PayPal側での決済後、結果を検証
     *
     * @access protected
     * @return void
     */
    function paypalDoExecute() {
        $logger =& TGMDK_Logger::getInstance();
        $arrOrder =& $this->arrOrder;

        // 要求電文パラメータ値の指定
        if ($this->objSetting->get('P_captureFlg') == true) {
            // 与信同時売上
            $objRequest = new PayPalCaptureRequestDto();
        } else {
            // 与信のみ
            $objRequest = new PaypalAuthorizeRequestDto();
        }

        // アクションタイプ : 固定値"do"
        $objRequest->setAction('do');

        // 顧客ID
        $payerId = htmlspecialchars(@$_GET['PayerID']);
        $objRequest->setPayerId($payerId);

        // トークン
        $token = htmlspecialchars(@$_GET['token']);
        $objRequest->setToken($token);

        // 実行
        $logger->info("PayPal決済の検証通信実行");
        $objTransaction = new TGMDK_Transaction();
        $objResponse = $objTransaction->execute($objRequest);

        // レスポンスの初期化
        $this->arrRes = $this->initArrRes();

        // レスポンス検証
        if (isset($objResponse) == false) {
            // システムエラー
            $logger->fatal("レスポンス生成に失敗");
            return false;
        }

        // 結果コード取得
        $this->arrRes['mStatus'] = $objResponse->getMStatus();
        // 詳細コード取得
        $this->arrRes['vResultCode'] = $objResponse->getVResultCode();
        // エラーメッセージ取得
        $this->arrRes['mErrMsg'] = $objResponse->getMerrMsg();

        // 正常終了？
        if ($this->arrRes['mStatus'] == MLD_SBIVT3G_MSTATUS_OK) {
            $this->arrRes['isOK'] = true;
            // 取引ID取得
            $this->arrRes['orderId'] = $objResponse->getOrderId();
            // 決済状態を保存
            if ($this->objSetting->get('P_captureFlg') == true) {
                $this->arrRes['payStatus'] = MDL_SBIVT3G_STATUS_CAPTURE;
            } else {
                $this->arrRes['payStatus'] = MDL_SBIVT3G_STATUS_AUTH;
            }
            // trAd URL取得
            $this->arrRes['tradUrl'] = $objResponse->getTradUrl();
        }
        $logger->debug(print_r($this->arrRes, true));

        // 結果を返す
        if ($this->arrRes['isOK'] == true) {
            return true;
        }
        return false;
    }

    /**
     * 決済モジュールから注文完了画面へ
     * オーバーライド
     *
     * @access protected
     * @return void
     */
    function goToComplete() {
        $logger =& TGMDK_Logger::getInstance();
        $arrOrder =& $this->arrOrder;
        $objMob =& $this->objMobile;

        // あればtrAdのURLをセッションに格納
        // 2012/07/30修正 携帯trAd非対応
        if (strcmp($this->arrRes['tradUrl'], '') != 0
            && $objMob->isMobile() == false
        ) {
            $objIF = new SC_If_SBIVT3G_CompleteResource();
            $objIF->setCompDispRCTitle($arrOrder['payment_method']);
            if ($objMob->isSmartphone() == true) {
                // 2012/07/24追加 スマートフォン用
                $objIF->setCompDispRC('', GC_Utils_SBIVT3G::setShowAdForSP(
                    $this->arrRes['vResultCode'],
                    $this->arrRes['tradUrl'])
                );
            } else {
                // それ以外
                $objIF->setCompDispRC('', GC_Utils_SBIVT3G::setShowAd(
                    $this->arrRes['vResultCode'],
                    $this->arrRes['tradUrl'])
                );
            }
            $objIF->pushCompDispRC();
        }

        // 受注ステータスは"入金待ち"
        //$arrOrder['status'] = ORDER_PAY_WAIT;
        $arrOrder['status'] = ORDER_NEW;

        // memo01:決済状態を保存
        $arrOrder['memo01'] = $this->arrRes['payStatus'];

        // memo02:空白
        $arrOrder['memo02'] = '';

        // memo03:ログ情報
        $arrOrder['memo03'] = GC_Utils_SBIVT3G::putPaymentLogString(
            MDL_SBIVT3G_INNER_ID_PAYPAL,
            $this->arrRes,
            '成功'
        );

        // memo04:最終受注ID
        $arrOrder['memo04'] = $this->arrRes['orderId'];

        // memo05:再決済用情報
        $arrOrder['memo05'] = serialize($this->arrRes);

        // memo06:空白
        $arrOrder['memo06'] = '';

        // 実行
        parent::goToComplete();
    }
}

?>
