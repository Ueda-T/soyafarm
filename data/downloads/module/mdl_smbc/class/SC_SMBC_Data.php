<?php
require_once(MDL_SMBC_CLASS_PATH . 'SC_SMBC.php');

class SC_SMBC_Data extends SC_SMBC {

    /**
     *  コンストラクタ
     */
    function SC_SMBC_Data() {
        parent::SC_SMBC();
        $this->init();
    }

    /**
     *  初期化
     */
    function init() {
    }

    function initArrParam() {
        parent::initArrParam();
    }

    /**
     * 連携データを設定する
     *
     * @param unknown_type $order_id
     * @return array $arrParam 連携データ配列
     */
    function makeParam ($order_id) {
        $arrParam = array();

        // 全決済共通の連携データを設定
        $arrParam = parent::makeParam($order_id);

        // データ連携に不要な項目を削除
        unset($arrParam['hakkou_kbn']);
        unset($arrParam['yuusousaki_kbn']);

        return $arrParam;
    }

    function getArrParam () {
        return $this->arrParam;
    }


    function checkCardInfo ($arrParam, $shori_kbn) {

        // クレジットお預かり用連携データを作成
        $arrRet = array();
        // バージョン
        $arrRet['version'] = MDL_SMBC_DATA_LINK_CARD_INFO_KEEP_VERSION;
        // 処理区分（01:登録・更新 02:照会 03:削除）
        $arrRet['shori_kbn'] = $shori_kbn;
        // 決済手段区分
        $arrRet['bill_method'] = MDL_SMBC_CREDIT_BILL_METHOD;
        // 契約コード
        $arrRet['shop_cd'] = $arrParam['shop_cd'];
        // 収納企業コード
        $arrRet['syuno_co_cd'] = $arrParam['syuno_co_cd'];
        // 認証パスワード
        $arrRet['auth_pwd'] = $arrParam['card_info_pwd'];
        // 顧客ID
        $arrRet['kmt_kok_id'] = str_pad($arrParam['customer_id'], 14, "0", STR_PAD_LEFT);
        // 識別番号
        $arrRet['shikibetsu_no'] = 1;

        if($shori_kbn == "01"){
            // 決済受付番号
            $arrRet['kessai_no'] = $arrParam['kessai_no'];
        }

        // 接続先
        if($arrParam['connect_url'] == "real"){
            // 本番用
            $connect_url = MDL_SMBC_CREDIT_INFO_KEEP_LINK_URL_REAL;
        }else{
            // テスト用
            $connect_url = MDL_SMBC_CREDIT_INFO_KEEP_LINK_URL_TEST;
        }

        // 送信データを設定する
        $this->setParam($arrRet);

        // 決済ステーションへ送信
        $arrResponse = $this->sendParam($connect_url);

        return $arrResponse;
    }

}
?>
