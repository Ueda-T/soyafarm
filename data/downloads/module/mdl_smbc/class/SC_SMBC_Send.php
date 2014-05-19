<?php
require_once(MODULE_REALDIR . 'mdl_smbc/inc/include.php');
require_once(MDL_SMBC_CLASS_PATH . 'SC_SMBC.php');

define('PLG_SMBC_NEW_ORDER_VERSION', '21B');

class SC_SMBC_Send extends SC_SMBC {

    /**
     *  コンストラクタ
     */
    function SC_SMBC_Send() {
        parent::SC_SMBC();
        $this->init();
    }

    /**
     *  初期化
     */
    function init() {
    }

    /**
     * 連携データ用配列を初期化
     *
     * @param void
     * @return void
     */
    function initArrParam() {

        parent::initArrParam();

        // 追加
        parent::addArrParam("version"             , 3);
        parent::addArrParam("bill_method"         , 2 );
        parent::addArrParam("kessai_id"           , 4 );
        parent::addArrParam("shop_pwd"            , 20);

    }

    /**
     * 連携データを設定する
     *
     * @param unknown_type $order_id
     * @return array $arrParam 連携データ配列
     */
    function makeParam ($order_id) {

//        $objSMBC = new SC_SMBC();
        $arrParam = array();

        // 全決済共通の連携データを設定
        $arrParam = parent::makeParam($order_id);

        // データ連携に必要な項目を追加
        $arrOrderTemp = parent::getOrderTemp($order_id);
        // バージョン
        $arrParam['version'] = PLG_SMBC_NEW_ORDER_VERSION;
        // 決済手段区分
        $arrParam['bill_method'] = $this->getBillMethod($arrOrderTemp['payment_id']);
        // 決済手段区分
        $arrParam['kessai_id'] = $this->getKessaiId($arrOrderTemp['payment_id']);
        // 内送料
        $arrParam['souryou'] = $arrOrderTemp['deliv_fee'];

        // データ連携に不要な項目を削除
        unset($arrParam['card_info_keep']);
        unset($arrParam['card_info_pwd']);
        unset($arrParam['arr_conveni']);
        unset($arrParam['arr_pay_method']);
        unset($arrParam['security_code_flg']);
        unset($arrParam['customer_id']);

        return $arrParam;
    }

    /**
     * 決済手段区分の取得
     *
     * @param  int     支払方法ID
     * @return string  決済手段区分
     */
    function getBillMethod ($payment_id) {
        $this->objQuery =& SC_Query_Ex::getSingletonInstance();
        if (!empty($payment_id)) {
            $res = $this->objQuery->getOne('SELECT memo01 FROM dtb_payment WHERE payment_id=?', array($payment_id));
            if (strlen($res)>0) {
                $res = str_pad($res , 2, "0", STR_PAD_LEFT);
                return $res;
            }
        }
        return '  ';
    }

    // 決済種類コードの取得
    function getKessaiId ($payment_id) {
        return '    ';
    }

    /**
     * 連携データを設定後、決済ステーションへ送信する
     *
     * @param string $serverUrl 送信先URL
     * @return array $arrResponse レスポンスボディ
     */
    function setSendData($arrParam) {

        // 接続先
        if($arrParam['connect_url'] == "real"){
            // 本番用
            $connect_url = MDL_SMBC_CREDIT_INFO_KEEP_LINK_URL_REAL;
        }else{
            // テスト用
            $connect_url = MDL_SMBC_CREDIT_INFO_KEEP_LINK_URL_TEST;
        }
        unset($arrParam['connect_url']);

        // 送信データを設定する
        $this->setParam($arrParam);

        // 決済ステーションへ送信
        $arrResponse = $this->sendParam($connect_url);

        return $arrResponse;

    }
    /**
     * データを決済ステーションへ送信する
     *
     * @param string $serverUrl 送信先URL
     * @return array $arrResponse レスポンスボディ
     */
    function sendParam($serverUrl) {

        $objReq  = new HTTP_Request($serverUrl);
        $arrResponse = array();

        // POSTで送信
        $objReq->setMethod('POST');

        // 送信データの文字コード変換・サイズ調整
        $arrSendParam = $this->convParamStr();

//print_r($arrSendParam);

        // 送信データとして設定。
        $objReq->addPostDataArray($arrSendParam);

        // 変換後の送信データをログ出力
        $this->printLog($arrSendParam);

        // 送信
        $ret = $objReq->sendRequest();

        if (PEAR::isError($ret)) {
            $arrResponse['rescd'] = "エラー";
            $arrResponse['res'] = "通信ができませんでした。" . $ret->getMessage();

            // エラー内容をログ出力
            $this->printLog($ret);

            return $arrResponse;
        }

        if ($objReq->getResponseCode() !== 200) {
            $arrResponse['rescd'] = "エラー";
            $arrResponse['res'] = "通信ができませんでした。";

            // エラー内容をログ出力
            $this->printLog($objReq->getResponseCode());

            return $arrResponse;
        }

        // 決済ステーションからのレスポンスを解析
        $arrResponse = $this->parse($objReq->getResponseBody());

        // レスポンスをログ出力
        $this->printLog($arrResponse);

        return $arrResponse;
    }
}
?>
