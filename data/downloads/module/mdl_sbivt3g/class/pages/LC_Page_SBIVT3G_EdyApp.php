<?php
/**
 * LC_Page_SBIVT3G_EdyApp.php - LC_Page_SBIVT3G_EdyApp クラスを定義
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: LC_Page_SBIVT3G_EdyApp.php 185 2012-07-30 07:20:45Z hira $
 * @link        http://www.veritrans.co.jp/3gps
*/


require_once MDL_SBIVT3G_PAGE_PATH . 'LC_Page_SBIVT3G.php';

/**
 * 3Gモジュール サイバーEdy決済(アプリ起動型)ページクラス
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
class LC_Page_SBIVT3G_EdyApp extends LC_Page_SBIVT3G {

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
        default :
            // ユーザーのブラウザを判定
            if (GC_Utils_SBIVT3G::isValidBrowserForPasori() == false) {
                $this->arrRes = $this->initArrRes();
                // PaSoRi推奨のブラウザでなければエラー
                $this->arrRes['mErrMsg'] =
                    MDL_SBIVT3G_EM_UNVALID_BROWSER_PASORI_MSG;
                break;
            }
            // Edy決済(アプリ起動型)処理
            if ($this->edyAppExecute() == false) {
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
     * Edy決済(アプリ起動型)実行
     *
     * @access protected
     * @return boolean 処理の成功・失敗
     */
    function edyAppExecute() {
        $logger =& TGMDK_Logger::getInstance();
        $arrOrder =& $this->arrOrder;
        $objMob =& $this->objMobile;

        // 要求電文パラメータ値の指定
        $objRequest = new EmAuthorizeRequestDto();

        // サービスオプション サイバーEdy
        $objRequest->setServiceOptionType(MDL_SBIVT3G_EM_TYPE_CYBER_EDY);

        // 受注番号(ゼロパディング)
        $objRequest->setOrderId($this->getMdkOrderId($arrOrder['order_id']));

        // 決済金額
        $objRequest->setAmount($arrOrder['payment_total']);

        // 決済完了通知URL(トップページ)
        $objRequest->setCompleteNoticeUrl(HTTP_URL);

        // 携帯版 TRAD対応
        if ($objMob->isMobile() == true) {
            $objTradReq = new TradRequestDto();
            $objTradReq->setScaleCode('902');
            $objRequest->setOptionParams(array($objTradReq));
        }

        // 実行
        $logger->info('Edy決済(PC:アプリ起動型)通信実行');
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
            // アプリ起動URL
            $this->arrRes['appUrl'] = $objResponse->getAppUrl();
            // 支払期限 ※固定値を編集
            $this->arrRes['limitDate'] =  GC_Utils_SBIVT3G::getAddDateFormat(
                EDY_APP_PAYMENT_TERM_DAY, 'Y/m/d');
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
        $objIF->setCompDispRC('',
            $this->genPaymentButton($this->arrRes['appUrl']));

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
        $objIF->setCompMailRC('支払期限', $this->arrRes['limitDate']);
        $objIF->setCompMailRC('決済用URL', $this->arrRes['appUrl']);
        $arrOrder['memo02'] = $objIF->getCompMailRC();

        // memo03:ログ情報
        $other = '成功';
        $other .= ' 支払期限['. $this->arrRes['limitDate'] .']';
        $arrOrder['memo03'] = GC_Utils_SBIVT3G::putPaymentLogString(
            MDL_SBIVT3G_INNER_ID_EDY_PC_APP,
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
        $html = <<<EOD

必ず下記リンクをクリックし、電子マネー事業者のサイトページでお支払いを完了させてください。<br/>
<a href="$url" style="display:block;margin:10px;font-size:120%">CyberEdyでの決済を開始する</a>
<span class="attention">※このページから決済を開始せずにブラウザを閉じてしまった場合、ご注文を取消とさせていただく場合がありますのでご注意ください。</span><br/>
EOD;
        return GC_Utils_SBIVT3G::removeLf($html);
    }
}

?>
