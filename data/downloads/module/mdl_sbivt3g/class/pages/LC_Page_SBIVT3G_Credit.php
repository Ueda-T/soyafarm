<?php
/**
 * LC_Page_SBIVT3G_Credit.php - LC_Page_SBIVT3G_Credit クラスを定義
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: LC_Page_SBIVT3G_Credit.php 236 2014-02-04 05:02:23Z kaji $
 * @link        http://www.veritrans.co.jp/3gps
*/


require_once MDL_SBIVT3G_PAGE_PATH . 'LC_Page_SBIVT3G.php';

/**
 * 3Gモジュール クレジットカード決済ページクラス
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
class LC_Page_SBIVT3G_Credit extends LC_Page_SBIVT3G {

    // {{{ properties
    /** 再取引可否 */
    var $tpl_canReTrade;

    /** 再取引用カード情報の配列 */
    var $arrReTradeCard;

    /** 再取引用レスポンス配列 */
    var $arrReTradeRes;

    /** MPIホスティング実施正否 */
    var $tpl_isMpi;

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
        $this->tpl_mainpage = $this->getTplPath('credit.tpl');

        // 再取引可否の判定(設定があって購入者が会員ログイン中)
        $objCustomer = new SC_Customer_Ex();
        $dont_check = false;
        if ($this->objMobile->isMobile()) {
            $dont_check = true;
        }
        if ($this->objSetting->get('C_reTradeFlg') == true &&
            $objCustomer->isLoginSuccess($dont_check) == true) {
            $this->tpl_canReTrade = true;
        } else {
            $this->tpl_canReTrade = false;
        }

        // MPIホスティングの判定(設定があって購入者ブラウザがPC)
        if ($this->objSetting->get('C_mpiFlg') == true
        && $this->objMobile->isMobile() == false
        && $this->objMobile->isSmartphone() == false) {
            $this->tpl_isMpi = true;
        } else {
            $this->tpl_isMpi = false;
        }
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

        // フォーム初期化
        $objForm = $this->initParam();
        if ($this->tpl_canReTrade == true) {
            $objFormReTrade = $this->initParamForReTrade();
        }

        // 入力値を取得
        $objForm->setParam($_POST);
        $objForm->convParam();
        if ($this->tpl_canReTrade == true) {
            $objFormReTrade->setParam($_POST);
            $objFormReTrade->convParam();
        }

        // 支払方法
        $this->arrPaymentType = $this->objSetting->getPaymentType();
        // 支払回数
        $this->arrPaymentCount = $this->objSetting->getPaymentCount();

        // 再取引履歴を取得
        if ($this->tpl_canReTrade == true) {
            $accountId = $this->getAccountId();
            $this->arrReTradeCard = $this->getCardInfos($accountId);
            if (count($this->arrReTradeCard) == 0) {
                // もし対象がなければ再取引中止
                $this->tpl_canReTrade = false;
            }
        }

        // モードに沿って処理
        switch ($mode) {
        case 'retrade' :
            // 入力チェック
            $this->arrErr = $this->lfCheckError($objFormReTrade);
            if (SC_Utils_Ex::isBlank($this->arrErr) == true) {
                // カード決済処理
                $arrHashForm = $objFormReTrade->getHashArray();
                if ($this->cardExecute($arrHashForm) == false) {
                    // エラー終了
                    break;
                }
                if ($this->arrRes['isMpi'] == true) {
                    // 本人認証成功:レスポンスをそのまま表示
                    echo $this->arrRes['resResponseContents'];
                } else {
                    // その他のカード決済:完了画面へ
                    $this->goToComplete();
                }
                exit();
            }
            if (SC_Display::detectDevice() == DEVICE_TYPE_SMARTPHONE) {
                $this->tpl_anchor = 'reTradeTop';
            }
            break;
        case 'exec' :
            // 入力チェック
            $this->arrErr = $this->lfCheckError($objForm);
            if (SC_Utils_Ex::isBlank($this->arrErr) == true) {
                // カード決済処理
                $arrHashForm = $objForm->getHashArray();
                if ($this->cardExecute($arrHashForm) == false) {
                    // エラー終了
                    break;
                }
                if ($this->arrRes['isMpi'] == true) {
                    // 本人認証成功:レスポンスをそのまま表示
                    echo $this->arrRes['resResponseContents'];
                } else {
                    // その他のカード決済:完了画面へ
                    $this->goToComplete();
                }
                exit();
            }
            break;
        case 'back' :
            // 確認画面へ
            $this->playBackToConfirm();
            exit();
            break;
        case 'comp' :
            // 本人認証の結果検証
            if ($this->mpiVerify() == false) {
                break;
            }
            // 本人認証カード決済:完了画面へ
            $this->goToComplete();
            exit();
            break;
        default :
            break;
        }

        // フォームからリストを取得
        if ($this->tpl_canReTrade == true) {
            $this->arrForm = array_merge(
                $objFormReTrade->getFormParamList(),
                $objForm->getFormParamList()
            );
        } else {
            $this->arrForm = $objForm->getFormParamList();
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
        // 本人認証から返ってきた時はチェックしない
        if ( $this->getMode() == 'comp') {
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

        $objForm->addParam('カード番号',
            'cardNo',
            MDL_SBIVT3G_CARD_NO_MAXLEN,
            'n',
            array('EXIST_CHECK', 'MAX_LENGTH_CHECK')
        );
        $objForm->addParam('有効期限(月)',
            'expiryMon',
            2,
            'n',
            array('EXIST_CHECK', 'NUM_COUNT_CHECK', 'NUM_CHECK')
        );
        $objForm->addParam('有効期限(年)',
            'expiryYear',
            2,
            'n',
            array('EXIST_CHECK', 'NUM_COUNT_CHECK', 'NUM_CHECK')
        );
        $objForm->addParam('カード名義(名)',
            'firstName',
            MDL_SBIVT3G_CARD_FIRST_NAME_MAXLEN,
            'n',
            array('CHANGE_UPPER', 'EXIST_CHECK', 'ALPHA_CHECK')
        );
        $objForm->addParam('カード名義(姓)',
            'lastName',
            MDL_SBIVT3G_CARD_LAST_NAME_MAXLEN,
            'n',
            array('CHANGE_UPPER', 'EXIST_CHECK', 'ALPHA_CHECK')
        );
        $objForm->addParam('お支払い方法',
            'paymentType',
            2,
            'n',
            array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK')
        );
        $objForm->addParam('お支払い回数',
            'paymentCount',
            3,
            'n',
            array('MAX_LENGTH_CHECK')
        );

        // セキュリティコード有効時
        if ($this->objSetting->get('C_securityFlg') == true) {
            $objForm->addParam('セキュリティコード',
                'securityCode',
                MDL_SBIVT3G_SECURITY_CODE_MAXLEN,
                'n',
                array('EXIST_CHECK', 'NUM_CHECK', 'MAX_LENGTH_CHECK')
            );
        }

        return $objForm;
    }

    /**
     * 再取引用のSC_FormParam_Exの初期化
     *
     * @access protected
     * @return SC_FormParam_Ex
     */
    function initParamForReTrade() {
        $objForm = new LC_SBIVT3G_FormParam();

        $objForm->addParam('カード番号',
            'cardId',
            STEXT_LEN,
            'n',
            array('EXIST_CHECK', 'MAX_LENGTH_CHECK')
        );
        $objForm->addParam('お支払い方法',
            'reTradePaymentType',
            2,
            'n',
            array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK')
        );
        $objForm->addParam('お支払い回数',
            'reTradePaymentCount',
            3,
            'n',
            array('MAX_LENGTH_CHECK')
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
        // モード取得
        $mode = $this->getMode();

        // 共通チェック
        $arrErr = $objForm->checkError();

        // 個別チェック
        $arrForm = $objForm->getHashArray();

        if ($mode == 'exec') {
            // カード番号
            if (isset($arrErr['cardNo']) == false) {
                if (preg_match('/^[0-9\-]+$/', $arrForm['cardNo']) == false) {
                    $arrErr['cardNo'] =
                        '※半角数字とハイフンで入力して下さい。<br/>';
                }
                if (preg_match('/-{2,}/', $arrForm['cardNo']) == true
                    || strlen(str_replace('-', '', $arrForm['cardNo']))
                        < MDL_SBIVT3G_CARD_NO_MINLEN) {
                    $arrErr['cardNo'] = '※入力書式が不正です。<br/>';
                }
            }
            // 有効期限
            if (isset($arrErr['expiryMon']) == false
                    && isset($arrErr['expiryYear']) == false) {
                $isCheck = checkdate($arrForm['expiryMon'], 1,
                    '20' + $arrForm['expiryYear']);
                if ($isCheck == false) {
                    $arrErr['expiryMon'] = '※不正な年月です。<br/>';
                    $arrErr['expiryYear'] = ' ';
                }
                $bol = GC_Utils_SBIVT3g::isValidExpiry(
                    $arrForm['expiryMon'], $arrForm['expiryYear']);
                if ($bol == false ) {
                    $arrErr['expiryMon'] = '※有効期間をご確認下さい。<br/>';
                    $arrErr['expiryYear'] = ' ';
                }
            }
        }

        if ($mode == 'exec') {
            $paymentType = 'paymentType';
            $paymentCount = 'paymentCount';
        } else {
            $paymentType = 'reTradePaymentType';
            $paymentCount = 'reTradePaymentCount';
        }
        // 支払い方法・回数
        if (isset($arrErr[$paymentType]) == false
                && isset($arrErr[$paymentCount]) == false) {
            if ($arrForm[$paymentType] == MDL_SBIVT3G_PTYPE_SPLIT) {
                if ($arrForm[$paymentCount] == '') {
                    $arrErr[$paymentCount] = 
                        '※お支払い回数を選択して下さい。<br/>';
                }
            } else {
                if ($arrForm[$paymentCount] != '') {
                    $arrErr[$paymentCount] = 
                        '※お支払い回数の選択は不要です。<br/>';
                }
            }
        }

        // セキュリティコード有効時
        if ($this->objSetting->get('C_securityFlg') == true) {
            if ($mode == 'exec') {
                // セキュリティーコード
                if (isset($arrErr['securityCode']) == false) {
                    if (strlen($arrForm['securityCode'])
                        < MDL_SBIVT3G_SECURITY_CODE_MINLEN) {
                            $arrErr['securityCode'] =
                                sprintf('※%d文字以上で入力して下さい。<br/>',
                                    MDL_SBIVT3G_SECURITY_CODE_MINLEN);
                    }
                }
            }
        }
        return $arrErr;
    }

    /**
     * クレジットカード決済実行
     *
     * @access protected
     * @param array $arrForm 入力値
     * @return boolean 処理の成功・失敗
     */
    function cardExecute($arrForm) {
        $logger =& TGMDK_Logger::getInstance();
        $arrOrder =& $this->arrOrder;
        $objMob =& $this->objMobile;

        // 再取引？
        $isReTrade = false;
        if ($this->getMode() == 'retrade' && $this->tpl_canReTrade == true) {
            $isReTrade = true;
        }

        // 要求電文パラメータ値の指定
        if ($this->tpl_isMpi == true) {
            // 本人認証用リクエストオブジェクト
            $objRequest = new MpiAuthorizeRequestDto();
        } else {
            // 通常クレジット用リクエストオブジェクト
            $objRequest = new CardAuthorizeRequestDto();
        }

        // 受注番号(ゼロパディング)
        $objRequest->setOrderId($this->getMdkOrderId($arrOrder['order_id']));

        // 決済金額
        $objRequest->setAmount($arrOrder['payment_total']);

        // 会員ID
        $objRequest->setAccountId($this->getAccountId());

        if ($isReTrade == true) { // 再取引
            // カードID
            $objRequest->setCardId($arrForm['cardId']);

            // 支払い方法＋支払回数
            $jpo = $arrForm['reTradePaymentType']
                . $arrForm['reTradePaymentCount'];
            $objRequest->setJpo($jpo);
        } else { // それ以外
            // クレジットカード番号
            $cardNo = str_replace('-', '', $arrForm['cardNo']);
            $objRequest->setCardNumber($cardNo);

            // カード有効期間
            $cardExpire = $arrForm['expiryMon'] . '/' . $arrForm['expiryYear'];
            $objRequest->setCardExpire($cardExpire);

            // 支払い方法＋支払回数
            $jpo = $arrForm['paymentType'] . $arrForm['paymentCount'];
            $objRequest->setJpo($jpo);

            // あればセキュリティコード
            if ($this->objSetting->get('C_securityFlg') == true
                    && isset($arrForm['securityCode'])) {
                $objRequest->setSecurityCode($arrForm['securityCode']);
            }
        }

        // 決済種別
        if ($this->objSetting->get('C_captureFlg') == true) {
            $objRequest->setWithCapture('true');
        } else {
            $objRequest->setWithCapture('false');
        }

        // 本人認証であれば設定
        if ($this->tpl_isMpi == true) {
            // MPIオプション
            $arrMpiOption = $this->objSetting->getMpiOption();
            $option = $arrMpiOption[$this->objSetting->get('C_mpiOption')];
            $objRequest->setServiceOptionType($option);
            // リダイレクションURI
            $objRequest->setRedirectionUri(MDL_SBIVT3G_MPI_RETURN_URL);
            // HTTPユーザエージェント
            $objRequest->setHttpUserAgent($_SERVER['HTTP_USER_AGENT']);
            // HTTPアセプト
            $objRequest->setHttpAccept($_SERVER['HTTP_ACCEPT']);
        }

        // 実行
        if ($this->tpl_isMpi == true) {
            $logger->info('クレジットカード決済(本人認証)通信実行');
        } else {
            $logger->info('クレジットカード決済通信実行');
        }
        $objTransaction = new TGMDK_Transaction();
        $objResponse = $objTransaction->execute($objRequest);

        // レスポンスの初期化
        if ($isReTrade == true) {
            $arrRes =& $this->arrReTradeRes;
        } else {
            $arrRes =& $this->arrRes;
        }
        $arrRes = $this->initArrRes();

        // レスポンス検証
        if (isset($objResponse) == false) {
            // システムエラー
            $logger->fatal('レスポンス生成に失敗');
            return false;
        }

        // 結果コード取得
        $arrRes['mStatus'] = $objResponse->getMStatus();
        // 詳細コード取得
        $arrRes['vResultCode'] = $objResponse->getVResultCode();
        // エラーメッセージ取得
        $arrRes['mErrMsg'] = $objResponse->getMerrMsg();

        if ($arrRes['mStatus'] == MLD_SBIVT3G_MSTATUS_NG) {
            // 通信失敗
            $logger->debug(print_r($arrRes, true));
            return false;

        } else if ($this->tpl_isMpi == false
        && $arrRes['mStatus'] == MLD_SBIVT3G_MSTATUS_PENDING) {
            // 保留時は所定回数だけリトライ実施
            $isRetryOK = false;
            for ($i = 0; $i < MLD_SBIVT3G_CARD_RETRY_LIMIT; $i++) {
                // インターバルを置く
                sleep(MLD_SBIVT3G_CARD_RETRY_WAIT);

                $logger->info('リトライ実行');
                // 実行
                $objRetryReq = new CardRetryRequestDto();
                $objRetryReq->setOrderId($objRequest->getOrderId());
                $objTransaction = new TGMDK_Transaction();
                $objResponse = $objTransaction->execute($objRetryReq);
                if (isset($objResponse) == false) {
                    // システムエラー
                    $logger->fatal('レスポンス生成に失敗(Retry)');
                    return false;
                }
                // レスポンス取得
                $arrRes = $this->initArrRes();
                $arrRes['mStatus'] = $objResponse->getMStatus();
                $arrRes['vResultCode'] = $objResponse->getVResultCode();
                $arrRes['mErrMsg'] = $objResponse->getMerrMsg();
                if ($arrRes['mStatus'] == MLD_SBIVT3G_MSTATUS_NG) {
                    // 通信失敗
                    $logger->debug(print_r($arrRes, true));
                    return false;

                } else if ($arrRes['mStatus'] == MLD_SBIVT3G_MSTATUS_OK) {
                    // リトライ成功
                    $isRetryOK = true;
                    break;
                }
            }
            if ($isRetryOK == false) {
                $logger->fatal('リトライ終了(失敗)');
                return false;
            }
        }

        if ($this->tpl_isMpi == true) {
            if ($this->judgeMpiExecute($arrRes['vResultCode']) == false) {
                $logger->debug(print_r($arrRes, true));
                return false;
            }
            // 本人認証カード決済での正常終了
            $arrRes['isOK'] = true;
            $arrRes['isMpi'] = $this->tpl_isMpi;
            // 結果HTML
            $arrRes['resResponseContents'] =
                $objResponse->getResResponseContents();

        } else {
            // 通常カード決済での正常終了
            $arrRes['isOK'] = true;
            // 取引ID取得
            $arrRes['orderId'] = $objResponse->getOrderId();
            // 実行したカード番号の加工値を取得
            $arrRes['reqCardNo'] = $objResponse->getReqCardNumber();
            // 入力したカード名義
            $arrRes['firstName'] = $arrForm['firstName'];
            $arrRes['lastName']  = $arrForm['lastName'];
            // 選択した支払い方法・支払い回数
            $reqJpo = $objResponse->getReqJpoInformation();
            $arrRes['paymentType'] = substr($reqJpo, 0, 2);
            $arrRes['paymentCount'] = substr($reqJpo, 2);
            // 決済状態を保存
            if ($this->objSetting->get('C_captureFlg') == true) {
                $arrRes['payStatus'] = MDL_SBIVT3G_STATUS_CAPTURE;
            } else {
                $arrRes['payStatus'] = MDL_SBIVT3G_STATUS_AUTH;
            }
            $arrRes['mpiHosting'] = false;
            // trAd URL取得
            $arrRes['tradUrl'] = $objResponse->getTradUrl();
        }
        $logger->debug(print_r($arrRes, true));

        if ($isReTrade == true) {
            // 再取引ならレスポンスを正規の配列へ移す
            $this->arrRes = $this->arrReTradeRes;
            unset($this->arrReTradeRes);
        }
        return true;
    }

    /**
     * 本人認証の通信結果の判定
     *
     * @access protected
     * @param string $vResultCode
     * @return boolean
     */
    function judgeMpiExecute($vResultCode) {
        // MPIオプションを取得
        $mpiOption = $this->objSetting->get('C_mpiOption');

        // 結果詳細コードの先頭4桁を取得
        $resultTop = substr($vResultCode, 0, 4);

        // オプション毎に処理
        $bolReturn = false;
        switch ($mpiOption) {
        case MDL_SBIVT3G_MPI_ID_COMPLETE : // 完全認証
            switch ($resultTop) {
            case 'G001' :
                // 本人認証可
                $bolReturn = true;
                break;
            default :
                // エラー
                $bolReturn = false;
                break;
            }
            break;
        case MDL_SBIVT3G_MPI_ID_COMPANY :  // 通常認証 カード会社リスク
            switch ($resultTop) {
            case 'G001' :
                // 本人認証可
                $bolReturn = true; 
                break;
            case 'G002' :
            case 'G003' :
                // カード会社リスク負担により決済処理へ移行
                $bolReturn = true; 
                break;
            default :
                // エラー
                $bolReturn = false;
                break;
            }
            break;
        case MDL_SBIVT3G_MPI_ID_MERCHANT : // 通常認証 加盟店リスク
            switch ($resultTop) {
            case 'G001' :
                // 本人認証可
                $bolReturn = true;
                break;
            case 'G002' :
            case 'G003' :
            case 'G004' :
            case 'G005' :
                // カード会社あるいは加盟店側のリスク負担により決済処理へ移行
                $bolReturn = true;
                break;
            default :
                // エラー
                $bolReturn = false;
                break;
            }
            break;
        }
        return $bolReturn;
    }

    /**
     * 本人認証の結果検証実行
     *
     * @access protected
     * @return boolean 処理の成功・失敗
     */
    function mpiVerify() {
        $logger =& TGMDK_Logger::getInstance();
        $arrOrder =& $this->arrOrder;

        // リクエストIDを取得する
        $requestId = htmlspecialchars(@$_REQUEST['RequestId']);
        if (strcmp($requestId, '') == 0) {
            $logger->fatal('リクエストID取得失敗');
            return false;
        }

        // 要求電文パラメータ値の指定
        $objRequest = new SearchRequestDto();

        // リクエストID
        $objRequest->setRequestId($requestId);

        // 実行
        $logger->info('本人認証の結果検証通信実行');
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

        if ($this->arrRes['mStatus'] != MLD_SBIVT3G_MSTATUS_OK) {
            // 検索リクエストそのものののエラー
            $this->arrRes['vResultCode'] = $objResponse->getVResultCode();
            $this->arrRes['mErrMsg'] = $objResponse->getMerrMsg();
            $logger->debug(print_r($this->arrRes, true));
            return false;
        }

        // 検索結果の取得
        $objOrderInfos = $objResponse->getOrderInfos();
        $arrOrderInfo  = $objOrderInfos->getOrderInfo();
        $objProperOrder = $arrOrderInfo[0]->getProperOrderInfo();
        $objTranInfos = $arrOrderInfo[0]->getTransactionInfos();
        $arrTranInfo  = $objTranInfos->getTransactionInfo();

        // 最新のMPIのレコードを探索
        $objLastTran = null;
        $objLastProperTran = null;
        if (is_array($arrTranInfo) == true) {
            $lastMpiTxn = null;
            foreach ($arrTranInfo as $objTran) {
                $txnId = $objTran->getTxnId();
                $objProperTran = $objTran->getProperTransactionInfo();
                switch ($objProperTran->getTxnKind()) {
                case 'mpi' :
                    if ($txnId > $lastMpiTxn) {
                        $objLastTran = $objTran;
                        $objLastProperTran = $objProperTran;
                        $lastMpiTxn = $txnId;
                    }
                    break;
                default :
                    break;
                }
            }
        }
        if ($objLastTran == null) {
            // 対象が見つからない
            $this->arrRes = $this->initArrRes();
            $this->arrRes['mErrMsg'] = '本人認証が未認証の可能性があります';
            $logger->debug(print_r($this->arrRes, true));
            return false;
        }

        // 結果コード取得
        $this->arrRes['mStatus'] = $objLastTran->getMStatus();
        // 詳細コード取得
        $this->arrRes['vResultCode'] = $objLastTran->getVResultCode();

        // 結果詳細コードを取得して判定
        if ($this->arrRes['mStatus'] != MLD_SBIVT3G_MSTATUS_OK
            || $this->judgeMpiVerify($this->arrRes['vResultCode']) == false
        ) {
            $this->arrRes['mErrMsg'] = '本人認証に失敗しました';
            $logger->debug(print_r($this->arrRes, true));
            return false;
        }
        if ($this->judgeCardVerify($this->arrRes['vResultCode']) == false) {
            $this->arrRes['mErrMsg'] = 'カード与信処理に失敗しました';
            $logger->debug(print_r($this->arrRes, true));
            return false;
        }

        // 正常終了
        $this->arrRes['isOK'] = true;
        $this->arrRes['mErrMsg'] = '';
        // 取引ID取得
        $this->arrRes['orderId'] = $arrOrderInfo[0]->getOrderId();
        // 実行したカード番号の加工値を取得
        $this->arrRes['reqCardNo'] =
            $objLastProperTran->getReqCardNumber();
        // 選択した支払い方法・支払い回数
        $reqJpo = $objLastProperTran->getReqJpoInformation();
        $this->arrRes['paymentType'] = substr($reqJpo, 0, 2);
        $this->arrRes['paymentCount'] = substr($reqJpo, 2);
        // 決済状態を保存
        if ($this->objSetting->get('C_captureFlg') == true) {
            $this->arrRes['payStatus'] = MDL_SBIVT3G_STATUS_CAPTURE;
        } else {
            $this->arrRes['payStatus'] = MDL_SBIVT3G_STATUS_AUTH;
        }
        $this->arrRes['mpiHosting'] = true;
        // trAd URL取得
        $this->arrRes['tradUrl'] = $objProperOrder->getTradUrl();

        $logger->debug(print_r($this->arrRes, true));

        return true;
    }

    /**
     * 本人認証の検証結果の判定
     *
     * @access protected
     * @param string $vResultCode
     * @return boolean
     */
    function judgeMpiVerify($vResultCode) {
        // MPIオプションを取得
        $mpiOption = $this->objSetting->get('C_mpiOption');

        // 結果詳細コードの先頭4桁を取得
        $resultTop = substr($vResultCode, 0, 4);

        // オプション毎に処理
        $bolReturn = false;
        switch ($mpiOption) {
        case MDL_SBIVT3G_MPI_ID_COMPLETE : // 完全認証
            switch ($resultTop) {
            case 'G011' :
                // 本人認証成功
                $bolReturn = true;
                break;
            default :
                // エラー
                $bolReturn = false;
                break;
            }
            break;
        case MDL_SBIVT3G_MPI_ID_COMPANY :  // 通常認証 カード会社リスク
            switch ($resultTop) {
            case 'G011' :
                // 本人認証成功
                $bolReturn = true; 
                break;
            case 'G012' :
            case 'G002' :
            case 'G003' :
                // 認証は成功なのでカード取引結果を使用
                $bolReturn = true;
                break;
            default :
                // エラー
                $bolReturn = false;
                break;
            }
            break;
        case MDL_SBIVT3G_MPI_ID_MERCHANT : // 通常認証 加盟店リスク
            switch ($resultTop) {
            case 'G011' :
                // 本人認証成功
                $bolReturn = true;
                break;
            case 'G012' :
            case 'G013' :
            case 'G014' :
            case 'G002' :
            case 'G003' :
            case 'G004' :
            case 'G005' :
                // 認証は成功なのでカード取引結果を使用
                $bolReturn = true;
                break;
            default :
                // エラー
                $bolReturn = false;
                break;
            }
            break;
        }
        return $bolReturn;
    }

    /**
     * カード与信の検証結果の判定
     *
     * @access protected
     * @param string $vResultCode
     * @return boolean
     */
    function judgeCardVerify($vResultCode) {

        // 第2ブロックの4桁を取得
        $resultSecond = substr($vResultCode, 4, 4);
        $bolReturn = true;

        // 条件付き成功なら第2ブロックを検証
        switch ($resultSecond) {
        case 'A001' : // 成功
        case 'A003' : // 複製の成功
            // 成功のまま
            break;
        default :
            // エラーとして扱う
            $bolReturn = false;
            break;
        }
        return $bolReturn;
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
        $arrRes =& $this->arrRes;

        // 完了記述情報(あればtrAdのURL)
        $objIF = new SC_If_SBIVT3G_CompleteResource();
        // 2012/07/30追加 携帯trAd非対応
        if (strcmp($arrRes['tradUrl'], '') != 0
            && $objMob->isMobile() == false
        ) {
            $objIF->setCompDispRCTitle($arrOrder['payment_method']);
            if ($this->tpl_isMpi == true) {
                // MPI用
                $tradScript = GC_Utils_SBIVT3G::setShowAdForMpi(
                    $arrRes['tradUrl']);
            } else if ($objMob->isSmartphone() == true) {
                // 2012/07/24追加 スマートフォン用
                $tradScript = GC_Utils_SBIVT3G::setShowAdForSP(
                    $arrRes['vResultCode'],
                    $arrRes['tradUrl']);
            } else {
                // 通常用
                $tradScript = GC_Utils_SBIVT3G::setShowAd(
                    $arrRes['vResultCode'],
                    $arrRes['tradUrl']);
            }
            $objIF->setCompDispRC('', $tradScript);
            $objIF->pushCompDispRC();
        }

        // 受注ステータスは"新規注文"
        $arrOrder['status'] = ORDER_NEW;

        // memo01:決済状態を保存
        $arrOrder['memo01'] = $this->arrRes['payStatus'];

        // memo02:メールでの記述情報
        $objIF->setCompMailRCTitle($arrOrder['payment_method']);
        $objIF->setCompMailRC('お支払い方法',
            $this->arrPaymentType[$arrRes['paymentType']]);
        if ($arrRes['paymentType'] == MDL_SBIVT3G_PTYPE_SPLIT) {
            $objIF->setCompMailRC('お支払回数',
                $this->arrPaymentCount[$arrRes['paymentCount']]);
        }
        $arrOrder['memo02'] = $objIF->getCompMailRC();

        // memo03:ログ情報
        $other = '成功';
        if ($arrRes['mpiHosting'] == true) {
            $other .= '(本人認証利用)';
        }
        $other .= ' ' . $this->arrPaymentType[$arrRes['paymentType']];
        if ($arrRes['paymentType'] == MDL_SBIVT3G_PTYPE_SPLIT) {
            $other .= '/' . $this->arrPaymentCount[$arrRes['paymentCount']];
        }
        if (isset($arrRes['firstName']) && isset($arrRes['lastName'])) {
            $other .= ' (NAME) ' . $arrRes['firstName'];
            $other .= '-' . $arrRes['lastName'];
        }
        $arrOrder['memo03'] = GC_Utils_SBIVT3G::putPaymentLogString(
            MDL_SBIVT3G_INNER_ID_CREDIT,
            $arrRes,
            $other
        );

        // memo04:最終受注ID
        $arrOrder['memo04'] = $arrRes['orderId'];

        // memo05:再決済用情報
        $arrOrder['memo05'] = serialize($arrRes);

        // memo06:再取引選択用にカード番号を保存
        $arrOrder['memo06'] = $arrRes['reqCardNo'];

        // 取引IDをセットする
        $this->setAccountId();

        // 実行
        parent::goToComplete();
    }

    /**
     * 会員IDに紐付けされたカード情報をベリトランスから取得する
     *
     * @access protected
     * @param string accountId クレジット会員ID
     * @return array クレジットカード情報の配列
     */
    function getCardInfos($accountId) {
        $result = array();
        $logger =& TGMDK_Logger::getInstance();

        $objRequest = new CardInfoGetRequestDto();

        // 会員ID
        $objRequest->setAccountId($accountId);

        // 実行
        $objTransaction = new TGMDK_Transaction();
        $objResponse = $objTransaction->execute($objRequest);

        // レスポンスの初期化
        $arrRes = $this->initArrRes();

        // レスポンス検証
        if (isset($objResponse) == false) {
            // システムエラー
            $logger->fatal('レスポンス生成に失敗');
            return $result;
        }

        // 結果コード取得
        $arrRes['mStatus'] = $objResponse->getMStatus();
        // 詳細コード取得
        $arrRes['vResultCode'] = $objResponse->getVResultCode();
        // エラーメッセージ取得
        $arrRes['mErrMsg'] = $objResponse->getMerrMsg();

        if ($arrRes['mStatus'] == MLD_SBIVT3G_MSTATUS_NG) {
            // 通信失敗
            $logger->debug(print_r($arrRes, true));
            return $result;
        }

        // レスポンスからカード情報の配列を取り出す
        $cardInfos =
            $objResponse->getPayNowIdResponse()->getAccount()->getCardInfo();

        // 画面表示用に配列を組み直す
        foreach ($cardInfos as $cardInfo) {
            // 標準カードに指定されたカードだけ取得する
            if ($cardInfo->getDefaultCard() == '0') {
                continue;
            }

            $result[$cardInfo->getCardId()] = array(
                // カードID
                'cardId' => $cardInfo->getCardId(),
                // マスクカード番号
                'cardNumber' => $cardInfo->getCardNumber(),
                // 有効期限
                'cardExpire' => $cardInfo->getCardExpire());
        }

        return $result;
    }

    /**
     * 会員IDを取得する。ない場合は採番して返却する。
     * 採番ルール： "EC" + 数値10桁
     * 数値10桁には、customer_idを利用する。
     *
     * @param none
     * @return string 会員ID
     */
    function getAccountId() {
        $objCustomer = new SC_Customer_Ex();

        $accountId = $objCustomer->getValue('torihiki_id');
        if (empty($accountId)) {

            $customer_id = $objCustomer->getValue('customer_id');
            $accountId = sprintf('EC%010s', $customer_id);

        }

        return $accountId;
    }

    /**
     * 会員IDを取得する。ない場合は採番してDBへ登録する。
     * 採番ルール： "EC" + 数値10桁
     * 数値10桁には、customer_idを利用する。
     *
     * @param none
     * @return string 会員ID
     */
    function setAccountId() {
        $objCustomer = new SC_Customer_Ex();

        $accountId = $objCustomer->getValue('torihiki_id');
        if (empty($accountId)) {
            $objQuery = new SC_Query_Ex();

            $customer_id = $objCustomer->getValue('customer_id');
            $accountId = sprintf('EC%010s', $customer_id);

            $sqlval = array('torihiki_id' => $accountId,
                            'send_flg' => 0,
                            'updator_id' => $customer_id,
                            'update_date' => 'Now()');
            $where = 'customer_id = ?';
            $objQuery->update
                ('dtb_customer', $sqlval, $where, array($customer_id));

            $objCustomer->setValue('torihiki_id', $accountId);
        }
    }
}

?>
