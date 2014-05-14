<?php
/**
 * LC_Page_SBIVT3G_Payeasy.php - LC_Page_SBIVT3G_Payeasy クラスを定義
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: LC_Page_SBIVT3G_Payeasy.php 185 2012-07-30 07:20:45Z hira $
 * @link        http://www.veritrans.co.jp/3gps
*/


require_once MDL_SBIVT3G_PAGE_PATH . 'LC_Page_SBIVT3G.php';

/**
 * 3Gモジュール 銀行(Pay-easy)決済ページクラス
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
class LC_Page_SBIVT3G_Payeasy extends LC_Page_SBIVT3G {

    // {{{ properties
    /** 処理種別(ATM or NET) */
    var $method;

    /** 金融機関配列(モジュール内で金融機関を選択する時) */
    var $arrBanks;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @access public
     * @param string $method ATM or NET
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

        // ネットバンキング決済で金融機関を選択する時
        if ($this->method == 'NET' && MDL_SBIVT3G_NETBANK_IS_SELECT == true) {
            // フォーム初期化
            $objForm = $this->initParam();
            $objForm->setParam($_POST);
            $objForm->convParam();
        }

        // モードに沿って処理
        switch ($mode) {
        case 'exec' :
            if ($this->method == 'NET'
                    && MDL_SBIVT3G_NETBANK_IS_SELECT == true) {
                // 入力チェック
                $this->arrErr = $objForm->checkError();
                if (SC_Utils_Ex::isBlank($this->arrErr) == false) {
                    break; // 入力エラー
                }
                // ネットバンキング決済処理(金融機関選択)
                if ($this->netbankingExecute($objForm->getHashArray()) == false) {
                    break; // エラー終了
                }
                // 完了画面へ
                $this->goToCompleteForNet();
                exit();
            }
            break;
        case 'back' :
            // 確認画面へ
            $this->playBackToConfirm();
            exit();
            break;
        default : // 決済実行
            if ($this->method == 'ATM') {
                // ATM決済処理
                if ($this->atmExecute() == true) {
                    $this->goToCompleteForATM();
                    exit();
                }
            } else if ($this->method == 'NET'
                    && MDL_SBIVT3G_NETBANK_IS_SELECT == false) {
                // ネットバンキング決済処理(金融機関未選択)
                if ($this->netbankingExecute(array()) == true) {
                    $this->goToCompleteForNet();
                    exit();
                }
            }
            break;
        }

        // ネットバンキング決済で金融機関を選択する時
        if ($this->method == 'NET' && MDL_SBIVT3G_NETBANK_IS_SELECT == true) {
            // 金融機関を取得
            if (($this->arrBanks = $this->searchBanks()) !== false) {
                // 選択画面を表示
                $this->tpl_mainpage = $this->getTplPath('netbank.tpl');
            }
            // フォームからリストを取得
            $this->arrForm = $objForm->getFormParamList();

        } else if (is_array($this->arrRes) == false) {
            // ここまでで遷移していなければエラー扱い
            $this->arrRes = $this->initArrRes();
        }
    }

    /**
     * SC_FormParam_Exの初期化
     *
     * @access protected
     * @return SC_FormParam_Ex
     */
    function initParam() {
        $objForm = new SC_FormParam_Ex();

        $objForm->addParam('金融機関',
            'payCsv',
            MDL_SBIVT3G_PAY_CSV_MAXLEN,
            'n',
            array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK')
        );

        return $objForm;
    }

    /**
     * ATM決済実行
     *
     * @access protected
     * @return boolean 処理の成功・失敗
     */
    function atmExecute($arrOrder) {
        $logger =& TGMDK_Logger::getInstance();
        $arrOrder =& $this->arrOrder;
        $objMob =& $this->objMobile;

        // 要求電文パラメータ値の指定
        $objRequest = new BankAuthorizeRequestDto();

        // サービスオプション(ATM)
        $objRequest->setServiceOptionType(MDL_SBIVT3G_BANK_TYPE_ATM);

        // 受注番号(ゼロパディング)
        $objRequest->setOrderId($this->getMdkOrderId($arrOrder['order_id']));

        // 決済金額
        $objRequest->setAmount($arrOrder['payment_total']);

        // 顧客名1(モバイル利用時は半角カナが設定されている可能性がある)
        $objRequest->setName1(mb_convert_kana($arrOrder['order_kana01']));

        // 顧客名2(モバイル利用時は半角カナが設定されている可能性がある)
        $objRequest->setName2(mb_convert_kana($arrOrder['order_kana02']));

        // 顧客名カナ1(モバイル利用時は半角カナが設定されている可能性がある)
        $objRequest->setKana1(mb_convert_kana($arrOrder['order_kana01']));

        // 顧客名カナ2(モバイル利用時は半角カナが設定されている可能性がある)
        $objRequest->setKana2(mb_convert_kana($arrOrder['order_kana02']));

        // 支払期限
        $limitDate = GC_Utils_SBIVT3G::getAddDateFormat(
            $this->objSetting->get('B_limitDays'), 'Ymd');
        $objRequest->setPayLimit($limitDate);

        // 請求内容
        $objRequest->setContents($this->objSetting->get('B_note'));

        // 請求内容カナ
        $objRequest->setContentsKana($this->objSetting->get('B_noteKana'));

        // 携帯版 TRAD対応
        if ($objMob->isMobile() == true) {
            $objTradReq = new TradRequestDto();
            $objTradReq->setScaleCode('902');
            $objRequest->setOptionParams(array($objTradReq));
        }

        // 実行
        $logger->info("ATM決済通信実行");
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
            // 収納機関番号
            $this->arrRes['orgNo'] = $objResponse->getShunoKikanNo();
            // お客様番号
            $this->arrRes['customerNo'] = $objResponse->getCustomerNo();
            // 確認番号
            $this->arrRes['confirmNo'] = $objResponse->getConfirmNo();
            // 支払期限 ※入力値を編集
            $this->arrRes['limitDate'] =  GC_Utils_SBIVT3G::getAddDateFormat(
                $this->objSetting->get('B_limitDays'), 'Y/m/d');
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
     * 決済モジュールから注文完了画面へ(ATM)
     * オーバーライド
     *
     * @access protected
     * @return void
     */
    function goToCompleteForATM() {
        $logger =& TGMDK_Logger::getInstance();
        $arrOrder =& $this->arrOrder;
        $objMob =& $this->objMobile;

        // 完了記述情報
        $objIF = new SC_If_SBIVT3G_CompleteResource();
        $objIF->setCompBothRCTitle($arrOrder['payment_method']);

        // メッセージ
        $message = 'ATM端末でお支払いの際には「収納機関番号[58191]」'
            . '・「お客様番号(数字20桁) 」・「確認番号(数字6桁) 」が必要と'
            . 'なります。';  

        // 表示強調用のタグを利用
        if ($objMob->isMobile() == true) {
            $startTag = '<div style="border:solid 1px #FF0000">';
        } else {
            $startTag = '<div style="border:solid 2px #FF0000; padding:5px; '
                . 'font-size:120%; border-radius: 5px;">';
            $message = '<strong>' .  $message . '</strong>';
        }
        $endTag = '</div>';

        $objIF->setCompDispRC('', $startTag . $message);
        $objIF->setCompBothRC('収納機関番号', $this->arrRes['orgNo']);
        $objIF->setCompBothRC('お客様番号', $this->arrRes['customerNo']);
        $objIF->setCompBothRC('確認番号', $this->arrRes['confirmNo']);
        $objIF->setCompDispRC('支払期限', $this->arrRes['limitDate'].$endTag);
        $objIF->setCompMailRC('支払期限', $this->arrRes['limitDate']);

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
            MDL_SBIVT3G_BANK_TYPE_ATM
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
        $other .= ' 収納機関番号['. $this->arrRes['orgNo'] .']';
        $other .= ' お客様番号['. $this->arrRes['customerNo'] .']';
        $other .= ' 確認番号['. $this->arrRes['confirmNo'] .']';
        $other .= ' 支払期限['. $this->arrRes['limitDate'] .']';
        $arrOrder['memo03'] = GC_Utils_SBIVT3G::putPaymentLogString(
            MDL_SBIVT3G_INNER_ID_PAYEASY_ATM,
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
        parent::goToComplete($arrOrder);
    }

    /**
     * ネットバンキング決済実行
     *
     * @access protected
     * @param array $arrForm 入力値
     * @return boolean 処理の成功・失敗
     */
    function netbankingExecute($arrForm) {
        $logger =& TGMDK_Logger::getInstance();
        $arrOrder =& $this->arrOrder;
        $objMob =& $this->objMobile;

        // 要求電文パラメータ値の指定
        $objRequest = new BankAuthorizeRequestDto();

        // サービスオプション
        if ($objMob->isDoCoMo() == true) {
            // DoCoMo
            $type = MDL_SBIVT3G_BANK_TYPE_NET_DC;
        } else if ($objMob->isEZweb() == true) {
            // au
            $type = MDL_SBIVT3G_BANK_TYPE_NET_AU;
        } else if ($objMob->isSoftBank() == true) {
            // ソフトバンク
            $type = MDL_SBIVT3G_BANK_TYPE_NET_SB;
        } else {
            // それ以外はPC
            $type = MDL_SBIVT3G_BANK_TYPE_NET_PC;
        }
        $objRequest->setServiceOptionType($type);

        // 受注番号(ゼロパディング)
        $objRequest->setOrderId($this->getMdkOrderId($arrOrder['order_id']));

        // 決済金額
        $objRequest->setAmount($arrOrder['payment_total']);

        // 顧客名1(モバイル利用時は半角カナが設定されている可能性がある)
        $objRequest->setName1(mb_convert_kana($arrOrder['order_kana01']));

        // 顧客名2(モバイル利用時は半角カナが設定されている可能性がある)
        $objRequest->setName2(mb_convert_kana($arrOrder['order_kana02']));

        // 顧客名カナ1(モバイル利用時は半角カナが設定されている可能性がある)
        $objRequest->setKana1(mb_convert_kana($arrOrder['order_kana01']));

        // 顧客名カナ2(モバイル利用時は半角カナが設定されている可能性がある)
        $objRequest->setKana2(mb_convert_kana($arrOrder['order_kana02']));

        // 支払期限
        $limitDate = GC_Utils_SBIVT3G::getAddDateFormat(
            $this->objSetting->get('B_limitDays'), 'Ymd');
        $objRequest->setPayLimit($limitDate);

        // 請求内容
        $objRequest->setContents($this->objSetting->get('B_note'));

        // 請求内容カナ
        $objRequest->setContentsKana($this->objSetting->get('B_noteKana'));

        // (あれば)金融機関コード
        if (isset($arrForm['payCsv']) == true) {
            $objRequest->setPayCsv($arrForm['payCsv']);
        }

        // 画面言語(固定)
        $objRequest->setViewLocale('ja');

        // 携帯版 TRAD対応
        if ($objMob->isMobile() == true) { $objTradReq = new TradRequestDto();
            $objTradReq->setScaleCode('902');
            $objRequest->setOptionParams(array($objTradReq));
        }

        // 実行
        $logger->info("ネットバンキング決済通信実行");
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
            // 収納機関番号
            $this->arrRes['orgNo'] = $objResponse->getShunoKikanNo();
            // 支払いパターン
            $this->arrRes['billPattern'] = $objResponse->getBillPattern();
            // 支払暗号文字列
            $this->arrRes['bill'] = $objResponse->getBill();
            // URL
            $this->arrRes['url'] = $objResponse->getUrl();
            // 画面情報
            $this->arrRes['view'] = $objResponse->getView();
            // 支払期限 ※入力値を編集
            $this->arrRes['limitDate'] =  GC_Utils_SBIVT3G::getAddDateFormat(
                $this->objSetting->get('B_limitDays'), 'Y/m/d');
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

        // 金融機関コードがある->金融機関選択画面へ戻るため
        if (isset($arrForm['payCsv']) == true) {
            // 再実行時の取引ID重複を避けるため受注番号更新
            $this->revolveOrderId();
        }

        return false;
    }

    /**
     * 決済モジュールから注文完了画面(ネットバンキング)へ
     * オーバーライド
     *
     * @access protected
     * @return void
     */
    function goToCompleteForNet() {
        $logger =& TGMDK_Logger::getInstance();
        $arrOrder =& $this->arrOrder;
        $objMob =& $this->objMobile;

        // 完了記述情報
        $objIF = new SC_If_SBIVT3G_CompleteResource();
        $objIF->setCompBothRCTitle($arrOrder['payment_method']);

        // あればtrAdのURLをセッションに格納
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
        // アプリ起動URLの設定
        if ($objMob->isMobile() == true) {
            // モバイル用
            $objIF->setCompDispRC('', $this->genButtonForMobile(
                $this->arrRes['url'],
                $this->arrRes['orgNo'],
                $this->arrRes['billPattern'],
                $this->arrRes['bill'])
            );
        } else {
            // それ以外
            $objIF->setCompDispRC('', $this->getRedirectScript(
                $this->arrRes['url'],
                $this->arrRes['orgNo'],
                $this->arrRes['billPattern'],
                $this->arrRes['bill'])
            );
        }

        // 注文完了画面へ渡す
        $objIF->pushCompDispRC();

        // 受注ステータスは"入金待ち"
        $arrOrder['status'] = ORDER_PAY_WAIT;

        // memo01:決済状態を保存
        $arrOrder['memo01'] = $this->arrRes['payStatus'];

        // memo02:メールでの記述情報
        $objIF->setCompMailRC('支払期限', $this->arrRes['limitDate']);
        $arrOrder['memo02'] = $objIF->getCompMailRC();

        // memo03:ログ情報
        $other = '成功';
        $other .= ' 支払期限['. $this->arrRes['limitDate'] .']';
        $arrOrder['memo03'] = GC_Utils_SBIVT3G::putPaymentLogString(
            MDL_SBIVT3G_INNER_ID_PAYEASY_NET,
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
     * ネットバンキングで次の画面へ遷移するスクリプトを生成
     *
     * @access protected
     * @param string $url 遷移先URL
     * @param string $orgNo 収納機関番号
     * @param string $billPattern 支払いパターン
     * @param string $bill 支払暗号文字列
     * @return string スクリプト(HTML)
     */
    function getRedirectScript($url, $orgNo, $billPattern, $bill) {
        $html = <<<EOD
<form id="netbankingRedirect" method="post" action="$url">
  <input type="hidden" name="skno" value="$orgNo" />
  <input type="hidden" name="bptn" value="$billPattern" />
  <input type="hidden" name="bill" value="$bill" />
</form>
<script type="text/javascript">
$(function(){
$('#netbankingRedirect').submit();
});
</script>
EOD;
        return GC_Utils_SBIVT3G::removeLf($html);
    }

    /**
     * 注文完了画面からの決済処理へ遷移するボタンを生成(モバイル用)
     *
     * @access protected
     * @param string $url 遷移先URL
     * @param string $orgNo 収納機関番号
     * @param string $billPattern 支払いパターン
     * @param string $bill 支払暗号文字列
     * @return string HTML
     */
    function genButtonForMobile($url, $orgNo, $billPattern, $bill) {
        $html = <<<EOD
<form method="post" action="$url">
  <input type="hidden" name="skno" value="$orgNo" />
  <input type="hidden" name="bptn" value="$billPattern" />
  <input type="hidden" name="bill" value="$bill" />
  <input type="submit" value="ネットバンク決済開始" />
  <p>
    必ずこのボタンから決済を開始してください。<br/>
    <font color="#FF0000">※このページから決済を開始せずにブラウザを閉じてしまった場合、ご注文を取消とさせていただく場合がありますのでご注意ください。</font>
  </p>
</form>
EOD;
        return GC_Utils_SBIVT3G::removeLf($html);
    }
    /**
     * 金融機関取得
     *
     * @access protected
     * @return mixed 金融機関配列またはfalse
     */
    function searchBanks() {
        $logger =& TGMDK_Logger::getInstance();
        $arrOrder =& $this->arrOrder;

        // 要求電文パラメータ値の指定
        $objRequest = new BankAuthorizeRequestDto();

        // 検索要求パラメータの設定
        $objRequest = new SearchRequestDto();
        $objRequest->setMasterNames("bankFinancialInstInfo");

        // 実行
        $logger->info("金融機関の取得通信実行");
        $objTransaction = new TGMDK_Transaction();
        $objResponse = $objTransaction->execute($objRequest);

        // レスポンスの初期化
        $arrSearchRes = $this->initArrRes();

        // レスポンス検証
        if (isset($objResponse) == false) {
            // システムエラー
            $logger->fatal("レスポンス生成に失敗");
            return false;
        }

        // 結果コード取得
        $arrSearchRes['mStatus'] = $objResponse->getMStatus();
        // 詳細コード取得
        $arrSearchRes['vResultCode'] = $objResponse->getVResultCode();
        // エラーメッセージ取得
        $arrSearchRes['mErrMsg'] = $objResponse->getMerrMsg();

        // 結果を取得する
        $arrBanks = false;
        if ($arrSearchRes['mStatus'] == MLD_SBIVT3G_MSTATUS_OK) {
            // 結果取得
            $arrSearchRes['isOK'] = true;
            $objMasterInfos = $objResponse->getMasterInfos();
            $arrMasterInfo = $objMasterInfos->getMasterInfo();
            $objMaster = $arrMasterInfo[0]->getMasters();
            $arrObjBanks = $objMaster->getBankFinancialInstInfo();

            $arrBanks = array();
            settype($arrObjBanks, 'array');
            foreach ($arrObjBanks as $objBank) {
                if ($objBank->getDeviceCode() == '01') {
                    $arrBanks[$objBank->getBankCode()] = $objBank->getBankName();
                }
            }
        } else if (is_array($this->arrRes) == false) {
            // 他のエラーが無いなら上書きしてエラーメッセージを表示
            $this->arrRes = $arrSearchRes;
        }
        $logger->debug(print_r($arrSearchRes, true));

        // 結果を返す
        return $arrBanks;
    }
}

?>
