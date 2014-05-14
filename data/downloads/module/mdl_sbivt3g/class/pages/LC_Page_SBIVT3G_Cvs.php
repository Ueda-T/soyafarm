<?php
/**
 * LC_Page_SBIVT3G_Cvs.php - LC_Page_SBIVT3G_Cvs クラスを定義
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: LC_Page_SBIVT3G_Cvs.php 193 2013-07-31 01:24:57Z kaji $
 * @link        http://www.veritrans.co.jp/3gps
*/


require_once MDL_SBIVT3G_PAGE_PATH . 'LC_Page_SBIVT3G.php';

/**
 * 3Gモジュール コンビニ決済ページクラス
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
class LC_Page_SBIVT3G_Cvs extends LC_Page_SBIVT3G {

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
        $this->tpl_mainpage = $this->getTplPath('cvs.tpl');
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

        // 入力値を取得
        $objForm->setParam($_POST);
        $objForm->convParam();

        // 有効コンビニ店舗を取得
        $this->arrCvsShop = $this->objSetting->getCvsShop();

        // モードに沿って処理
        switch ($mode) {
        case 'exec' :
            // 入力チェック
            $this->arrErr = $objForm->checkError();
            if (SC_Utils_Ex::isBlank($this->arrErr) == true) {
                // コンビニ決済処理
                if ($this->cvsExecute($objForm->getHashArray()) == false) {
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

        $objForm->addParam('コンビニエンスストア',
            'serviceOptionType',
            MDL_SBIVT3G_SERVICE_OPTION_MAXLEN,
            'n',
            array('EXIST_CHECK', 'MAX_LENGTH_CHECK')
        );

        return $objForm;
    }

    /**
     * コンビニ決済実行
     *
     * @access protected
     * @param array $arrForm 入力値
     * @return boolean 処理の成功・失敗
     */
    function cvsExecute($arrForm) {
        $logger =& TGMDK_Logger::getInstance();
        $arrOrder =& $this->arrOrder;
        $objMob =& $this->objMobile;

        // 要求電文パラメータ値の指定
        $objRequest = new CvsAuthorizeRequestDto();

        // コンビニ店舗
        $objRequest->setServiceOptionType($arrForm['serviceOptionType']);

        // 受注番号(ゼロパディング)
        $objRequest->setOrderId($this->getMdkOrderId($arrOrder['order_id']));

        // 決済金額
        $objRequest->setAmount($arrOrder['payment_total']);

        // 氏名1(モバイル利用時は半角カナが設定されている可能性がある)
        $objRequest->setName1(mb_convert_kana($arrOrder['order_kana01']));

        // 氏名2(モバイル利用時は半角カナが設定されている可能性がある)
        $objRequest->setName2(mb_convert_kana($arrOrder['order_kana02']));

        // 電話番号
        $telno = $arrOrder['order_tel01'];
        $telno .= '-' . $arrOrder['order_tel02'];
        $telno .= '-' . $arrOrder['order_tel03'];
        $objRequest->setTelNo($telno);

        // 支払期限
        $limitDate = GC_Utils_SBIVT3G::getAddDateFormat(
            $this->objSetting->get('V_limitDays'), 'Y/m/d');
        $objRequest->setPayLimit($limitDate);

        // 支払区分(固定値)
        $objRequest->setPaymentType('0');

        // 備考1(店舗名:セブンイレブン以外)
        if ($arrForm['serviceOptionType'] != MDL_SBIVT3G_CVS_TYPE_SEVEN) {
            $objRequest->setFree1($this->objSetting->get('V_shopName'));
        }

        // 備考2(備考欄:
        //       「セブンイレブン」
        //       「ローソン・ミニストップ・セイコーマート」
        //       「ローソン・ファミリーマート・ミニストップ・セイコーマート」
        //       以外)
        if ($arrForm['serviceOptionType'] != MDL_SBIVT3G_CVS_TYPE_SEVEN &&
            $arrForm['serviceOptionType'] != MDL_SBIVT3G_CVS_TYPE_LAWSON &&
            $arrForm['serviceOptionType'] != MDL_SBIVT3G_CVS_TYPE_ECON) {
            $objRequest->setFree2($this->objSetting->get('V_note'));
        }

        // 携帯版 TRAD対応
        if ($objMob->isMobile() == true) {
            $objTradReq = new TradRequestDto();
            $objTradReq->setScaleCode('902');
            $objRequest->setOptionParams(array($objTradReq));
        }

        // 実行
        $logger->info("コンビニ決済通信実行");
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
            // 選択したタイプ(店舗) ※入力値をそのまま
            $this->arrRes['serviceOptionType'] = $arrForm['serviceOptionType'];
            // 有効期限 ※入力値をそのまま
            $this->arrRes['limitDate'] = $limitDate;
            // 電話番号 ※入力値をそのまま
            $this->arrRes['telNo'] = $objRequest->getTelNo();
            // 受付番号
            $this->arrRes['receiptNo'] = $objResponse->getReceiptNo();
            // 払込URL(一部店舗のみ)
            $this->arrRes['haraikomiUrl'] = $objResponse->getHaraikomiUrl();
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

        // 表示強調用のタグを利用
        if ($objMob->isMobile() == true) {
            $startTag = '<div style="border:solid 1px #FF0000">';
        } else {
            $startTag = '<div style="border:solid 2px #FF0000; padding:5px; '
                . 'font-size:120%; border-radius: 5px;">';
        }
        $endTag = '</div>';

        $objIF->setCompDispRC($startTag . 'お支払い先ストア',
            $this->arrCvsShop[$this->arrRes['serviceOptionType']]);
        $objIF->setCompMailRC('お支払い先ストア',
            $this->arrCvsShop[$this->arrRes['serviceOptionType']]);
        // 受付番号設定
        $arrNo = GC_Utils_SBIVT3G::translateRecpNo(
            $this->arrRes['serviceOptionType'],
            $this->arrRes['receiptNo'],
            $this->arrRes['telNo'],
            true
        );
        foreach ($arrNo as $k => $v) {
            $objIF->setCompBothRC($k, $v);
        }
        // セブンイレブンであれば設定
        if (strcmp($this->arrRes['serviceOptionType'], MDL_SBIVT3G_CVS_TYPE_SEVEN) == 0) {
            $objIF->setCompDispRC('払込票URL', sprintf('<a href="%s">%s</a>',
                $this->arrRes['haraikomiUrl'],
                $this->arrRes['haraikomiUrl']));
            $objIF->setCompMailRC('払込票URL', $this->arrRes['haraikomiUrl']);
        }
        $objIF->setCompDispRC('お支払い期限',
            $this->arrRes['limitDate'] . $endTag);
        $objIF->setCompMailRC('お支払い期限', $this->arrRes['limitDate']);

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

        // 説明取得
        $objIF->setCompBothRC('', GC_Utils_SBIVT3G::getExplain(
            $this->arrRes['serviceOptionType']
        ));

        // 注文完了画面へ渡す
        $objIF->pushCompDispRC();

        // 受注ステータスは"入金待ち"
        $arrOrder['status'] = ORDER_PAY_WAIT;

        // memo01:決済状態を保存
        $arrOrder['memo01'] = $this->arrRes['payStatus'];

        // memo02:メールでの記述情報
        $arrOrder['memo02'] = $objIF->getCompMailRC();

        // memo03:ログ情報
        $other = '成功';
        $other .= ' 店舗[';
        $other .= $this->arrCvsShop[$this->arrRes['serviceOptionType']];
        $other .= ']';
        $other .= GC_Utils_SBIVT3G::translateRecpNo(
            $this->arrRes['serviceOptionType'],
            $this->arrRes['receiptNo'],
            $this->arrRes['telNo']
        );
        $other .= ' 支払期限['. $this->arrRes['limitDate'] .']';
        $arrOrder['memo03'] = GC_Utils_SBIVT3G::putPaymentLogString(
            MDL_SBIVT3G_INNER_ID_CVS,
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
