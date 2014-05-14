<?php
/**
 * LC_Page_SBIVT3G_SuicaMail.php - LC_Page_SBIVT3G_SuicaMail クラスを定義
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: LC_Page_SBIVT3G_SuicaMail.php 188 2012-08-07 06:57:51Z hira $
 * @link        http://www.veritrans.co.jp/3gps
*/


require_once MDL_SBIVT3G_PAGE_PATH . 'LC_Page_SBIVT3G.php';

/**
 * 3Gモジュール モバイルSuica決済(メール型)ページクラス
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
class LC_Page_SBIVT3G_SuicaMail extends LC_Page_SBIVT3G {

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

        // 表示テンプレート
        if ($this->method == 'MOBILE') {
            $this->tpl_mainpage = $this->getTplPath('suica_mail_mobile.tpl');
        } else {
            $this->tpl_mainpage = $this->getTplPath('suica_mail_pc.tpl');
        }
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
                // Suicaメール型決済処理
                if ($this->suicaMailExecute($objForm->getHashArray()) == false) {
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
                $isMobile = ($this->method == 'MOBILE')? true : false;
                $addr = $this->getValidMailAddr($arrOrder, $isMobile);
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

        // モバイル or PC によって文言を変える
        if ($this->method == 'MOBILE') {
            $mailAddr = '携帯メールアドレス';
        } else {
            $mailAddr = 'メールアドレス';
        }

        $objForm->addParam($mailAddr,
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
            // モバイル or PC によってチェック内容を変える
            if ($this->method == 'MOBILE') {
                // 2012/07/25 携帯キャリアメール以外での利用も許可する
                /*
                $isMob = SC_Helper_Mobile_Ex::gfIsMobileMailAddress(
                    $arrForm['mailAddr']);

                if ($isMob == false) {
                    $arrErr['mailAddr']
                        = '※ 携帯電話のメールアドレスを入力して下さい。<br/>';
                }
                 */
            } else {
                $isMob = SC_Helper_Mobile_Ex::gfIsMobileMailAddress(
                    $arrForm['mailAddr']);

                if ($isMob == true) {
                    $arrErr['mailAddr']
                        = '※ 携帯電話以外のメールアドレスを入力して下さい。<br/>';
                }
            }
        }
        return $arrErr;
    }

    /**
     * Suicaメール型決済処理
     *
     * @access protected
     * @param array $arrForm 入力値
     * @return boolean 処理の成功・失敗
     */
    function suicaMailExecute($arrForm) {
        $logger =& TGMDK_Logger::getInstance();
        $arrOrder =& $this->arrOrder;
        $objMob =& $this->objMobile;

        // 要求電文パラメータ値の指定
        $objRequest = new EmAuthorizeRequestDto();

        // サービスオプション 処理種別によって変える
        if ($this->method == 'MOBILE') {
            $optionType = MDL_SBIVT3G_EM_TYPE_MOBILE_SUICA_MAIL;
        } else {
            $optionType = MDL_SBIVT3G_EM_TYPE_PC_SUICA_MAIL;
        }
        $objRequest->setServiceOptionType($optionType);

        // 受注番号(ゼロパディング)
        $objRequest->setOrderId($this->getMdkOrderId($arrOrder['order_id']));

        // 決済金額
        $objRequest->setAmount($arrOrder['payment_total']);

        // 決済期限
        $limitDate = GC_Utils_SBIVT3G::getAddDateFormat(
            $this->objSetting->get('ES_limitDays'), 'Ymd235959');
        $objRequest->setSettlementLimit($limitDate);

        // メールアドレス
        $objRequest->setMailAddr($arrForm['mailAddr']);

        // 転送メール送信要否
        $forwardFlag = sprintf('%d', $this->objSetting->get('ES_bccMailFlg'));
        $objRequest->setForwardMailFlag($forwardFlag);

        // マーチャントメールアドレス
        if ($forwardFlag == '1') {
            $objRequest->setMerchantMailAddr(
                $this->objSetting->get('ES_bccMailAddr'));
        }

        // 依頼メール送信付加情報
        $objRequest->setRequestMailAddInfo(
            $this->objSetting->get('ES_reqMailInfo'));

        // 完了メール送信付加情報
        $objRequest->setCompleteMailAddInfo(
            $this->objSetting->get('ES_cmpMailInfo'));

        // 完了メール送信要否：固定で 1:送信する に設定する
        $objRequest->setCompleteMailFlag('1');

        // 内容確認画面の付加情報
        $objRequest->setConfirmScreenAddInfo(
            $this->objSetting->get('ES_cnfDispInfo'));

        // 完了画面の付加情報
        $objRequest->setCompleteScreenAddInfo(
            $this->objSetting->get('ES_cmpDispInfo'));

        // 画面タイトル(商品・サービス名)
        $objRequest->setScreenTitle($this->objSetting->get('ES_shopName'));

        // 携帯版 TRAD対応
        if ($objMob->isMobile() == true) {
            $objTradReq = new TradRequestDto();
            $objTradReq->setScaleCode('902');
            $objRequest->setOptionParams(array($objTradReq));
        }

        // 実行
        $logger->info(sprintf('Suica決済(%s:メール型)通信実行', $this->method));
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
                $this->objSetting->get('ES_limitDays'), 'Y/m/d');
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
        if ($this->method == 'MOBILE') {
            $innerPay = MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_MAIL;
        } else {
            $innerPay = MDL_SBIVT3G_INNER_ID_SUICA_PC_MAIL;
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
}

?>
