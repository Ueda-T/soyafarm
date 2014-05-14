<?php
/**
 * LC_Page_SBIVT3G_WaonApp.php - LC_Page_SBIVT3G_WaonApp クラスを定義
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: LC_Page_SBIVT3G_WaonApp.php 185 2012-07-30 07:20:45Z hira $
 * @link        http://www.veritrans.co.jp/3gps
*/


require_once MDL_SBIVT3G_PAGE_PATH . 'LC_Page_SBIVT3G.php';

/**
 * 3Gモジュール Waon決済(アプリ起動型)ページクラス
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
class LC_Page_SBIVT3G_WaonApp extends LC_Page_SBIVT3G {

    // {{{ properties
    /** 処理種別(PC or MOBILE) */
    var $method;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @access public
     * @param string $method PC or MOBILE
     * @return void
     */
    function init($method) {
        parent::init();

        // 種別を設定
        $this->method = $method;
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
        default :
            // ユーザーのブラウザを判定
            if ($this->method == 'MOBILE') {
                // モバイル or 電子マネー対応スマートフォンであること
                if ($this->objMobile->isMobile() == false
                        && GC_Utils_SBIVT3G::isValidSphoneForEMoney() == false){
                    $this->arrRes = $this->initArrRes();
                    // エラーメッセージ
                    $this->arrRes['mErrMsg'] = 
                        MDL_SBIVT3G_EM_UNVALID_BROWSER_MOBILE_MSG;
                    break;
                }
            } else {
                // PaSoRi推奨のブラウザであること
                if (GC_Utils_SBIVT3G::isValidBrowserForPasori() == false) {
                    $this->arrRes = $this->initArrRes();
                    // エラーメッセージ
                    $this->arrRes['mErrMsg'] =
                        MDL_SBIVT3G_EM_UNVALID_BROWSER_PASORI_MSG;
                    break;
                }
            }
            // Waon決済処理
            if ($this->waonAppExecute() == false) {
                // エラー終了
                break;
            }
            // 完了画面へ
            $this->goToComplete();
            exit();
            break;
        }
        // ここまでで遷移していなければエラー扱い
        if (is_array($this->arrRes) == false) {
            $this->arrRes = $this->initArrRes();
        }
    }

    /**
     * Waonアプリ起動型決済実行
     *
     * @access protected
     * @return boolean 処理の成功・失敗
     */
    function waonAppExecute() {
        $logger =& TGMDK_Logger::getInstance();
        $arrOrder =& $this->arrOrder;
        $objMob =& $this->objMobile;

        // 要求電文パラメータ値の指定
        $objRequest = new EmAuthorizeRequestDto();

        // サービスオプション 処理種別によって変える
        if ($this->method == 'MOBILE') {
            $optionType = MDL_SBIVT3G_EM_TYPE_MOBILE_WAON_APP;
        } else {
            $optionType = MDL_SBIVT3G_EM_TYPE_PC_WAON_APP;
        }
        $objRequest->setServiceOptionType($optionType);

        // 受注番号(ゼロパディング)
        $objRequest->setOrderId($this->getMdkOrderId($arrOrder['order_id']));

        // 決済金額
        $objRequest->setAmount($arrOrder['payment_total']);

        // 決済期限
        $limitDate = GC_Utils_SBIVT3G::getAddDateFormat(
            $this->objSetting->get('EW_limitDays'), 'Ymd235959');
        $objRequest->setSettlementLimit($limitDate);

        // エラー報知メールアドレス
        $objRequest->setMailAddr($arrOrder['order_email']);

        if ($this->method != 'MOBILE') {
            // 成功時URL
            $objRequest->setSuccessUrl(HTTP_URL);

            // 失敗時URL
            $objRequest->setFailureUrl(HTTP_URL);

            // キャンセル時URL
            $objRequest->setCancelUrl(HTTP_URL);
        }

        // 支払取消期限
        $limitCancelDate = GC_Utils_SBIVT3G::getAddDateFormat(
            $this->objSetting->get('EW_limitCancel'), 'Ymd235959');
        $objRequest->setCancelLimit($limitCancelDate);

        // 携帯版 TRAD対応
        if ($objMob->isMobile() == true) {
            $objTradReq = new TradRequestDto();
            $objTradReq->setScaleCode('902');
            $objRequest->setOptionParams(array($objTradReq));
        }

        // 実行
        $logger->info(sprintf('Waon決済(%s:アプリ型)通信実行', $this->method));
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
            $this->arrRes['orderId'] = $objResponse->getOrderId();
            // 受付番号
            $this->arrRes['receiptNo'] = $objResponse->getReceiptNo();
            // 支払期限 ※入力値を編集
            $this->arrRes['limitDate'] =  GC_Utils_SBIVT3G::getAddDateFormat(
                $this->objSetting->get('EW_limitDays'), 'Y/m/d');
            // アプリ起動URL
            $this->arrRes['appUrl'] = $objResponse->getAppUrl();
            // 決済状態を保存
            $this->arrRes['payStatus'] = MDL_SBIVT3G_STATUS_REQUEST;
            // trAd URL取得
            $this->arrRes['tradUrl'] = $objResponse->getTradUrl();
        }
        $logger->debug(print_r($this->arrRes, true));

        // 結果を返す
        if ($this->arrRes['isOK'] == true) {
            return true;
        }
        $logger->debug(print_r($objRequest, true));
        $logger->debug(print_r($objResponse, true));
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

        // 完了記述情報
        $objIF = new SC_If_SBIVT3G_CompleteResource();
        $objIF->setCompBothRCTitle($arrOrder['payment_method']);

        // アプリ起動URLの設定
        $objIF->setCompDispRC('', $this->genPaymentButton(
            $this->arrRes['appUrl']));

        // あればtrAdのURLをセッションに格納
        // 2012/07/30修正 携帯trAd非対応
        if (strcmp($this->arrRes['tradUrl'], '') != 0
            && $objMob->isMobile() == false
        ) {
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
        }

        // 注文完了画面へ渡す
        $objIF->pushCompDispRC();

        // 受注ステータスは"入金待ち"
        $arrOrder['status'] = ORDER_PAY_WAIT;

        // memo01:決済状態を保存
        $arrOrder['memo01'] = $this->arrRes['payStatus'];

        // memo02:メールでの記述情報
        $objIF->setCompMailRC('支払期限',  $this->arrRes['limitDate']);
        $objIF->setCompMailRC('決済用URL', $this->arrRes['appUrl']);
        $arrOrder['memo02'] = $objIF->getCompMailRC();

        // memo03:ログ情報
        if ($this->method == 'MOBILE') {
            $innerPay = MDL_SBIVT3G_INNER_ID_WAON_MOBILE_APP;
        } else {
            $innerPay = MDL_SBIVT3G_INNER_ID_WAON_PC_APP;
        }
        $other = '成功';
        $other .= ' 支払期限['. $this->arrRes['limitDate'] .']';
        $arrOrder['memo03'] = GC_Utils_SBIVT3G::putPaymentLogString(
            $innerPay,
            $this->arrRes,
            $other
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

    /**
     * アプリ起動の画面へ遷移するボタンを生成
     *
     * @access protected
     * @param string $url アプリ起動URL
     * @return string HTML
     */
    function genPaymentButton($url) {
        // 処理種別によって変える
        if ($this->method == 'MOBILE') {
            // モバイル用
            $html = <<<EOD
<div align="center">
  <a href="$url"><font size="+1">モバイルWaon決済を開始</font></a>
</div>
必ず上記リンクをクリックし、電子マネー事業者のサイトページでお支払いを完了させてください。<br/>
<font color="#FF0000">※このページから決済を開始せずにブラウザを閉じてしまった場合、ご注文を取消とさせていただく場合がありますのでご注意ください。</font>
EOD;
        } else {
            // PC用
            $html = <<<EOD
必ず下記リンクをクリックし、電子マネー事業者のサイトページでお支払いを完了させてください。<br/>
<a href="$url" style="display:block;margin:10px;font-size:120%;">Waon決済を開始する</a>
<span class="attention">※このページから決済を開始せずにブラウザを閉じてしまった場合、ご注文を取消とさせていただく場合がありますのでご注意ください。</span>
EOD;
        }
        return GC_Utils_SBIVT3G::removeLf($html);
    }
}

?>
