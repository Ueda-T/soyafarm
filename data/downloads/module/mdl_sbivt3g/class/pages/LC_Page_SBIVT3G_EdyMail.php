<?php
/**
 * LC_Page_SBIVT3G_EdyMail.php - LC_Page_SBIVT3G_EdyMail クラスを定義
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: LC_Page_SBIVT3G_EdyMail.php 185 2012-07-30 07:20:45Z hira $
 * @link        http://www.veritrans.co.jp/3gps
*/


require_once MDL_SBIVT3G_PAGE_PATH . 'LC_Page_SBIVT3G.php';

/**
 * 3Gモジュール モバイルEdy決済(メール型)ページクラス
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
class LC_Page_SBIVT3G_EdyMail extends LC_Page_SBIVT3G {

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

        // 表示テンプレート
        $this->tpl_mainpage = $this->getTplPath('edy_mail_mobile.tpl');
    }

    /**
     * Page のアクション.
     *
     * @access public
     * @return void
     */
    function action() {
        $arrOrder =& $this->arrOrder;

        // モード取得
        $mode = $this->getMode();

        // フォーム初期化
        $objForm = $this->initParam();

        // 入力値を取得
        $objForm->setParam($_POST);
        $objForm->convParam();

        // モードに沿って処理
        switch ($mode) {
        case 'exec' :
            // 入力チェック
            $this->arrErr = $this->lfCheckError($objForm);
            if (SC_Utils_Ex::isBlank($this->arrErr) == true) {
                // モバイルEdy決済処理
                if ($this->edyMailExecute($objForm->getHashArray()) == false) {
                    // エラー終了
                    break;
                }
                // 完了画面へ
                $this->goToComplete();
                exit();
            }
            break;
        case 'back' :
            // 確認画面へ
            $this->playBackToConfirm();
            exit();
            break;
        default :
            // もし未設定なら初期値の取得を試みる
            if ($objForm->getValue('mailAddr') == '') {
                $addr = $this->getValidMailAddr($this->arrOrder);
                if ($addr !== false) {
                    $objForm->setValue('mailAddr', $addr);
                }
            }
            break;
        }

        // フォームからリストを取得
        $this->arrForm = $objForm->getFormParamList();
    }

    /**
     * SC_FormParam_Exの初期化
     *
     * @access protected
     * @return SC_FormParam_Ex
     */
    function initParam() {
        $objForm = new SC_FormParam_Ex();

        $objForm->addParam('携帯メールアドレス',
            'mailAddr',
            MDL_SBIVT3G_MAIL_ADDR_MAXLEN,
            'na',
             array('MAX_LENGTH_CHECK','EXIST_CHECK','EMAIL_CHECK')
        );

        return $objForm;
    }

    /**
     * 入力内容のチェックを実施
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return array 入力チェック結果の配列
     */
    function lfCheckError(&$objForm) {

        // 共通チェック
        $arrErr = $objForm->checkError();

        // 個別チェック
        $arrForm = $objForm->getHashArray();
 
        // メールアドレス
        if (isset($arrErr['mailAddr']) == false) {
            // 2012/07/25 携帯キャリアメール以外での利用も許可する
            /*
            $isMob = SC_Helper_Mobile_Ex::gfIsMobileMailAddress(
                $arrForm['mailAddr']);

            if ($isMob == false) {
                $arrErr['mailAddr']
                    = '※ 携帯電話のメールアドレスを入力して下さい。<br/>';
            }
             */
        }
        return $arrErr;
    }

    /**
     * Edy決済(メール型)実行
     *
     * @access protected
     * @param array $arrForm 入力値
     * @return boolean 処理の成功・失敗
     */
    function edyMailExecute($arrForm) {
        $logger =& TGMDK_Logger::getInstance();
        $arrOrder =& $this->arrOrder;
        $objMob =& $this->objMobile;

        // 要求電文パラメータ値の指定
        $objRequest = new EmAuthorizeRequestDto();

        // サービスオプション モバイルEdy
        $objRequest->setServiceOptionType(MDL_SBIVT3G_EM_TYPE_MOBILE_EDY);

        // 受注番号(ゼロパディング)
        $objRequest->setOrderId($this->getMdkOrderId($arrOrder['order_id']));

        // 決済金額
        $objRequest->setAmount($arrOrder['payment_total']);

        // 決済期限
        $limitDate = GC_Utils_SBIVT3G::getAddDateFormat(
            $this->objSetting->get('EE_limitDays'), 'Ymd235959');
        $objRequest->setSettlementLimit($limitDate);

        // メールアドレス
        $objRequest->setMailAddr($arrForm['mailAddr']);

        // 転送メール送信要否
        $forwardFlag = sprintf('%d', $this->objSetting->get('EE_bccMailFlg'));
        $objRequest->setForwardMailFlag($forwardFlag);
        if ($forwardFlag == '1') {
            // マーチャントメールアドレス
            $objRequest->setMerchantMailAddr(
                $this->objSetting->get('EE_bccMailAddr'));
        }

        // 依頼メール送信付加情報
        $objRequest->setRequestMailAddInfo(
            $this->objSetting->get('EE_reqMailInfo'));

        // 完了メール送信付加情報
        $objRequest->setCompleteMailAddInfo(
            $this->objSetting->get('EE_cmpMailInfo'));

        // ショップ名
        $objRequest->setShopName($this->objSetting->get('EE_shopName'));

        // 携帯版 TRAD対応
        if ($objMob->isMobile() == true) {
            $objTradReq = new TradRequestDto();
            $objTradReq->setScaleCode('902');
            $objRequest->setOptionParams(array($objTradReq));
        }

        // 実行
        $logger->info('Edy決済(MOBILE:メール型)通信実行');
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
            // メールアドレス ※入力値をそのまま
            $this->arrRes['mailAddr'] = $arrForm['mailAddr'];
            // 支払期限 ※入力値を編集
            $this->arrRes['limitDate'] =  GC_Utils_SBIVT3G::getAddDateFormat(
                $this->objSetting->get('EE_limitDays'), 'Y/m/d');
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

        // 再実行時の取引ID重複を避けるため受注番号更新
        $this->revolveOrderId();

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
        $objIF->setCompBothRC('支払期限', $this->arrRes['limitDate']);
        $objIF->setCompBothRC('決済メール送付アドレス',
            $this->arrRes['mailAddr']);
        $objIF->pushCompDispRC();

        // 受注ステータスは"入金待ち"
        $arrOrder['status'] = ORDER_PAY_WAIT;

        // memo01:決済状態を保存
        $arrOrder['memo01'] = $this->arrRes['payStatus'];

        // memo02:メールでの記述情報
        $arrOrder['memo02'] = $objIF->getCompMailRC();

        // memo03:ログ情報
        $other = '成功';
        $other .= ' 支払期限['. $this->arrRes['limitDate'] .']';
        $arrOrder['memo03'] = GC_Utils_SBIVT3G::putPaymentLogString(
            MDL_SBIVT3G_INNER_ID_EDY_MOBILE_MAIL,
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
}

?>
