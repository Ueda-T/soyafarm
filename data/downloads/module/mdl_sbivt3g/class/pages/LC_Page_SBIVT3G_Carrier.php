<?php
/**
 * LC_Page_SBIVT3G_Carrier.php - LC_Page_SBIVT3G_Carrier クラスを定義
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: LC_Page_SBIVT3G_Carrier.php 211 2013-12-26 04:47:25Z takao $
 * @link        http://www.veritrans.co.jp/3gps
*/


require_once MDL_SBIVT3G_PAGE_PATH . 'LC_Page_SBIVT3G.php';

/**
 * 3Gモジュール キャリア決済ページクラス
 *
 * @category    Veritrans
 * @package     Lib
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version    Release: @package_version@
 * @link        http://www.veritrans.co.jp/3gps
 * @access  public
 * @author  T.Kajioka
 */
class LC_Page_SBIVT3G_Carrier extends LC_Page_SBIVT3G {

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
        $this->tpl_mainpage = $this->getTplPath('carrier.tpl');
    }

    /**
     * Page のアクション.
     *
     * @access public
     * @return void
     */
    function action() {
        $logger =& TGMDK_Logger::getInstance();

        // モード取得
        $mode = $this->getMode();

        // フォーム初期化
        $objForm = $this->initParam();

        // 入力値を取得
        if (SC_Utils_Ex::isBlank($_POST)) {
            $objForm->setParam($_GET);
        } else {
            $objForm->setParam($_POST);
        }
        $objForm->convParam();

        // 選択可能なキャリアサービスを取得
        $this->arrCarrierServices = $this->objSetting->getCarrierServices();

        // モードに沿って処理
        switch ($mode) {
        case 'exec':
            // 入力チェック
            $this->arrErr = $objForm->checkError();
            if (SC_Utils_Ex::isBlank($this->arrErr) == true) {
                // キャリア決済処理
                $arrHashForm = $objForm->getHashArray();
                if ($this->carrierExecute($arrHashForm) == false) {
                    // エラー終了
                    break;
                }
            }
            break;

        case 'back':
        case 'cancel':
            // 確認画面へ
            $this->playBackToConfirm();
            exit();
            break;

        case 'success':
            // 完了画面へ
            $this->goToComplete();
            exit();
            break;

        case 'error':
            // レスポンスの初期化
            $this->arrRes = $this->initArrRes();
            // 結果コード取得
            $this->arrRes['mStatus'] = $_GET['mstatus'];
            // 詳細コード取得
            $this->arrRes['vResultCode'] = $_GET['vResultCode'];
            // エラーメッセージ
            $this->arrRes['mErrMsg'] =
                GC_Utils_SBIVT3G::getCarrierErrorMessage($_GET['vResultCode']);

            $logger->fatal(print_r($this->arrRes, true));

            // 次の取引ができるよう注文ID(取引ID)を更新する
            $this->revolveOrderId();
        default:
            break;
        }

        // フォームからリストを取得
        $this->arrForm = $objForm->getFormParamList();
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
        // ベリトランスからのリダイレクト時はチェックしない
        if ($this->getMode() == 'success' ||
            $this->getMode() == 'cancel' ||
            $this->getMode() == 'error') {
            return; 
        }
        parent::doValidToken($is_admin);
    }

    /**
     * SC_FormParam_Exの初期化
     *
     * @access protected
     * @return SC_FormParam_Ex
     */
    function initParam() {
        $objForm = new LC_SBIVT3G_FormParam();

        $objForm->addParam('キャリア',
            'serviceOptionType',
            MDL_SBIVT3G_SERVICE_OPTION_MAXLEN,
            'n',
            array('EXIST_CHECK', 'MAX_LENGTH_CHECK')
        );

        return $objForm;
    }

    /**
     * キャリア決済実行
     *
     * @access protected
     * @param array $arrForm 入力値
     * @return boolean 処理の成功・失敗
     */
    function carrierExecute($arrForm) {
        $logger =& TGMDK_Logger::getInstance();
        $arrOrder =& $this->arrOrder;
        $objMob =& $this->objMobile;

        // 要求電文パラメータ値の指定
        $objRequest = new CarrierAuthorizeRequestDto();

        // 取引ID
        $objRequest->setOrderId($this->getMdkOrderId($arrOrder['order_id']));
        // サービスオプションタイプ
        $objRequest->setServiceOptionType($arrForm['serviceOptionType']);
        // 決済金額
        $objRequest->setAmount($arrOrder['payment_total']);
        // 端末種別
        $terminal = MDL_SBIVT3G_CARRIER_TERMINAL_PC;
        if ($objMob->isMobile()) {
            $terminal = MDL_SBIVT3G_CARRIER_TERMINAL_KTAI;
        } else if ($objMob->isSmartphone()) {
            $terminal = MDL_SBIVT3G_CARRIER_TERMINAL_SMAHO;
        }
        if ($arrForm['serviceOptionType'] == MDL_SBIVT3G_CARRIER_TYPE_DOCOMO &&
            $terminal == MDL_SBIVT3G_CARRIER_TERMINAL_PC &&
            $this->objSetting->get('CA_docomoPcFlg')) {
            // PCでドコモケータイ払いを許可する場合、端末種別をスマホにする
            $terminal = MDL_SBIVT3G_CARRIER_TERMINAL_SMAHO;
        }
        $objRequest->setTerminalKind($terminal);
        // 商品タイプ
        $objRequest->setItemType($this->objSetting->get('CA_itemType'));
        // 都度/継続区分（都度固定）
        $objRequest->setAccountingType(MDL_SBIVT3G_CARRIER_ACCTYPE_TSUDO);
        // 決済種別
        if ($this->objSetting->get('CA_captureFlg') ==
            MDL_SBIVT3G_CARRIER_WC_CAPTURE) {
            $objRequest->setWithCapture('true');
        } else {
            $objRequest->setWithCapture('false');
        }
        // 本人認証(3Dセキュア)
        if ($arrForm['serviceOptionType'] == MDL_SBIVT3G_CARRIER_TYPE_SB_KTAI) {
            $objRequest->setD3Flag($this->objSetting->get('CA_3DFlg'));
        }
        // 商品情報
        if ($arrForm['serviceOptionType'] ==
            MDL_SBIVT3G_CARRIER_TYPE_DOCOMO ||
            $arrForm['serviceOptionType'] ==
            MDL_SBIVT3G_CARRIER_TYPE_SB_MATOMETE) {
            $objRequest->setItemInfo($this->objSetting->get('CA_itemInfo'));
        }
        // 決済完了時URL
        $objRequest->setSuccessUrl
            (MDL_SBIVT3G_CARRIER_SUCCESS_URL .
             '&serviceOptionType=' . $arrForm['serviceOptionType'] .
             '&terminalKind=' . $terminal .
             '&' . session_name() . '=' . session_id());
        // 決済キャンセル時URL
        $objRequest->setCancelUrl
            (MDL_SBIVT3G_CARRIER_CANCEL_URL .
             '&' . session_name() . '=' . session_id());
        // 決済エラー時URL
        $objRequest->setErrorUrl
            (MDL_SBIVT3G_CARRIER_ERROR_URL .
             '&' . session_name() . '=' . session_id());
        // UID（S!まとめて支払いの場合のみ設定）
        if ($arrForm['serviceOptionType'] ==
            MDL_SBIVT3G_CARRIER_TYPE_S_BIKKURI) {
            $objRequest->setSbUid($_SERVER['HTTP_X_JPHONE_UID']);
        }

        // 実行
        $logger->info('キャリア決済通信実行');
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

        if ($this->arrRes['mStatus'] != MLD_SBIVT3G_MSTATUS_OK) {
            // 通信失敗
            $logger->debug(print_r($this->arrRes, true));
            return false;
        }

        // 非同期で正常終了の結果通知を受け取った際に
        // 同期処理で正常終了したときと同じ状態にするために必要な
        // 情報をこの段階で保存しておく
        {
            $arrRes = array();

            // memo04:最終受注ID
            $arrOrder['memo04'] = $objResponse->getOrderId();

            // 取引ID
            $arrRes['orderId'] = $objResponse->getOrderId();
            // サービスオプションタイプ
            $arrRes['serviceOptionType'] = $arrForm['serviceOptionType'];
            // 端末種別
            $arrRes['terminalKind'] = $terminal;
            // 決済状態
            $status = MDL_SBIVT3G_STATUS_CAPTURE;
            if ($this->objSetting->get('CA_captureFlg') ==
                MDL_SBIVT3G_CARRIER_WC_AUTHORIZE) {
                $status = MDL_SBIVT3G_STATUS_AUTH;
            }
            $arrRes['payStatus'] = $status;

            // memo05:再決済用情報
            $arrOrder['memo05'] = serialize($arrRes);

            // 受注更新
            $objQuery =& SC_Query_Ex::getSingletonInstance();
            $arrCond = array($arrOrder['order_id']);
            $objQuery->update('dtb_order', $arrOrder, 'order_id = ?', $arrCond);
        }

        $redirectUrl = $objResponse->getRedirectUrl();
        if (empty($redirectUrl) === false) {
            // S!まとめて支払いの場合
            header("Location: " . $redirectUrl, true, 302);
            exit();
        } else {
            // 上記以外
            $responseHtml = $objResponse->getResponseContents();
            $serviceOptionType = $arrForm['serviceOptionType'];
            if ($serviceOptionType == MDL_SBIVT3G_CARRIER_TYPE_DOCOMO ||
                $serviceOptionType == MDL_SBIVT3G_CARRIER_TYPE_SB_KTAI) {
                header("Content-type: text/html; charset=Shift_JIS");
                $responseHtml = mb_convert_encoding
                    ($responseHtml, "SJIS", "UTF-8");
            } else {
                header("Content-type: text/html; charset=UTF-8");
            }
            echo $responseHtml;
            exit();
        }

        return true;
    }

    /**
     * 決済モジュールから注文確認画面へ戻る
     * オーバーライド
     *
     * @access protected
     * @return void
     */
    function playBackToConfirm() {
        $logger =& TGMDK_Logger::getInstance();
        $arrOrder =& $this->arrOrder;

        $logger->info('確認画面へ戻る');

        // キャリア決済からブラウザバックなどで不正に戻ってきたなど、
        // 既に注文確定しているケースに対応する。
        if ($arrOrder['status'] != ORDER_NEW) {
            // 受注番号を取得
            $orderId = $this->getOrderId();

            // ▼ 2013.12.25 mod
            // 2.11系、2.12系のみ実行
            if (GC_Utils_SBIVT3G::compareVersion('2.12.6') <= 0) {
                // 受注情報の復元
                $objPurchase = new SC_Helper_Purchase_Ex();
                $objPurchase->rollbackOrder($orderId, ORDER_PENDING, true);
                unset($_SESSION['order_id']);
            }
            // ▲ 2013.12.25 mod
        }

        // セッション保護
        $objSiteSess = new SC_SiteSession_Ex();
        $objSiteSess->setRegistFlag();

        // 確認画面へ戻す
        SC_Response_Ex::sendRedirect(SHOPPING_CONFIRM_URLPATH);
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
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrRes =& $this->arrRes;

        // トランザクション開始
        $objQuery->begin();

        // 対象の受注番号を取得
        $orderId = $this->getOrderIdOfRequestStatus($_GET['orderId']);
        if ($orderId == null) {
            $objQuery->rollback();
            $logger->warn('該当する受注番号なし');
            exit();
        }

        // 対象受注を取得
        $objPurchase = new SC_Helper_Purchase_Ex();
        $this->arrOrder = $objPurchase->getOrder($orderId);
        $arrOrder =& $this->arrOrder;

        $alreadyRegist = false;
        if ($arrOrder['status'] == ORDER_NEW) {
            // 結果通知処理で既に登録およびメール配信処理済み
            $alreadyRegist = true;
        }

        // 取引ID
        $arrRes['orderId'] = $_GET['orderId'];
        // サービスオプションタイプ
        $arrRes['serviceOptionType'] = $_GET['serviceOptionType'];
        // 端末種別
        $arrRes['terminalKind'] = $_GET['terminalKind'];
        // 決済状態
        $status = MDL_SBIVT3G_STATUS_CAPTURE;
        if ($this->objSetting->get('CA_captureFlg') ==
            MDL_SBIVT3G_CARRIER_WC_AUTHORIZE) {
            $status = MDL_SBIVT3G_STATUS_AUTH;
        }
        $arrRes['payStatus'] = $status;

        // 完了記述情報
        $objIF = new SC_If_SBIVT3G_CompleteResource();
        $objIF->setCompBothRCTitle($arrOrder['payment_method']);

        // 受注ステータスは"新規注文"
        $arrOrder['status'] = ORDER_NEW;

        // memo01:決済状態を保存
        $arrOrder['memo01'] = $status;

        // memo02:メールでの記述情報
        $service = '';
        switch ($arrRes['serviceOptionType']) {
        case MDL_SBIVT3G_CARRIER_TYPE_DOCOMO:
            $service = PAYMENT_NAME_CARRIER_DOCOMO;
            break;
        case MDL_SBIVT3G_CARRIER_TYPE_AU:
            $service = PAYMENT_NAME_CARRIER_AU;
            break;
        case MDL_SBIVT3G_CARRIER_TYPE_SB_KTAI:
            $service = PAYMENT_NAME_CARRIER_SB_KTAI;
            break;
        case MDL_SBIVT3G_CARRIER_TYPE_SB_MATOMETE:
            $service = PAYMENT_NAME_CARRIER_SB_MATOMETE;
            break;
        case MDL_SBIVT3G_CARRIER_TYPE_S_BIKKURI:
            $service = PAYMENT_NAME_CARRIER_S_BIKKURI;
            break;
        default:
            break;
        }
        $objIF->setCompDispRC('お支払い方法', $service . '<br/>');
        $objIF->setCompMailRC('お支払い方法', $service);
        $arrOrder['memo02'] = $objIF->getCompMailRC();

        // memo03:ログ情報
        $other  = '成功';
        $other .= ' サービスオプションタイプ[';
        $other .= $arrRes['serviceOptionType'] . ']';
        $other .= ' 端末種別[' . $arrRes['terminalKind'] . ']';
        $arrOrder['memo03'] = GC_Utils_SBIVT3G::putPaymentLogString
            (MDL_SBIVT3G_INNER_ID_CARRIER, $arrRes, $other);

        // memo04:最終受注ID
        $arrOrder['memo04'] = $arrRes['orderId'];

        // memo05:再決済用情報
        $arrOrder['memo05'] = serialize($arrRes);

        // memo06:空白
        $arrOrder['memo06'] = '';

        // 注文完了画面へ渡す
        $objIF->pushCompDispRC();

        // 実行
        //   結果通知処理で既に処理済みかどうかにより処理分岐
        //   ・未処理の場合は、登録とメール配信を実施して完了ページに
        //     遷移させる。
        //   ・処理済みの場合は、登録とメール配信処理は行わず、完了ページ
        //     に遷移させる。
        $logger->info('受注完了処理');
        if ($alreadyRegist == false) {
            $logger->info('受注更新とメール配信処理');
            // 受注完了処理
            $objPurchase = new SC_Helper_Purchase_Ex();
            $objPurchase->registerOrder($arrOrder['order_id'], $arrOrder);
            $objPurchase->sendOrderMail($arrOrder['order_id']);
        }

        // セッション保護
        $objSiteSess = new SC_SiteSession_Ex();
        $objSiteSess->setRegistFlag();

        // トランザクション終了
        $objQuery->commit();

        $logger->info('注文完了画面へ');

        // 完了画面へリダイレクト
        SC_Response_Ex::sendRedirect(SHOPPING_COMPLETE_URLPATH);
    }

    /**
     * 取引IDから受注番号を取得
     *
     * @access protected
     * @param string $paymentOrderId 決済を実施した取引ID
     * @return string 受注番号
     */
    function getOrderIdOfRequestStatus($paymentOrderId) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        $sql = <<<EOD
SELECT order_id
FROM dtb_order
WHERE memo04 = ?
ORDER BY order_id ASC LIMIT 1
FOR UPDATE
EOD;
        $res = $objQuery->getOne($sql, array($paymentOrderId));

        return $res;
    }
}

?>
