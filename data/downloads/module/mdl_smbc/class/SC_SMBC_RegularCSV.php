<?php
if (version_compare(ECCUBE_VERSION, '2.12', '>=')) {
    require_once(DATA_REALDIR . 'module/HTTP/Request.php');
} else {
    require_once(DATA_REALDIR . 'module/Request.php');
}
require_once(MDL_SMBC_CLASS_PATH . 'SC_Mdl_SMBC.php');

class SC_SMBC_RegularCSV extends SC_SMBC {

    const VERSION = MDL_SMBC_REGULAR_REGISTER_VERSION;
    const BILL_METHOD = MDL_SMBC_CREDIT_BILL_METHOD;
    const KESSAI_ID = MDL_SMBC_CREDIT_KESSAI_ID;
    const SEIKYUU_KAISHI_YM = '209912';
    const SEIKYUU_SHURYO_YM = '999912';
    const SEIKYUU_HOHO = '1';

    // 連携データ用
    var $arrParam;

    // 受注番号
    var $order_id;

    var $objQuery;

    /**
     *  コンストラクタ
     */
    function __construct() {
        parent::init();
    }

    /**
     * 連携データ用配列を初期化
     *
     * @param void
     * @return void
     */
    function initArrParam() {
        SC_SMBC::addArrParam("shop_cd", 7);
        SC_SMBC::addArrParam("syuno_co_cd", 8);
        SC_SMBC::addArrParam("shop_pwd", 20);
        SC_SMBC::addArrParam("shoporder_no", 25);
        SC_SMBC::addArrParam("bill_no", 14);
        SC_SMBC::addArrParam("bill_name", MDL_SMBC_CUSTOMER_NAME_MAX_LEN);
        SC_SMBC::addArrParam("version", 3, MDL_SMBC_TO_ENCODE);
        SC_SMBC::addArrParam("bill_method", 2, MDL_SMBC_TO_ENCODE);
        SC_SMBC::addArrParam("kessai_id", 4, MDL_SMBC_TO_ENCODE);
        SC_SMBC::addArrParam("card_no", 16, MDL_SMBC_TO_ENCODE);
        SC_SMBC::addArrParam("card_yukokigen", 4, MDL_SMBC_TO_ENCODE);
    }


    /**
     * 全決済の共通項目の連携データを設定する
     *
     * @param unknown_type $order_id
     * @return array $arrParam
     */
    function makeParam ($arrOrderTemp) {
        $arrParam = array();
        // モジュールマスタの連携データを取得
        $objSMBC = SC_Mdl_SMBC::getInstance();
        $subData = $objSMBC->getSubData();

        $arrParam['version'] = self::VERSION;
        $arrParam['bill_method'] = self::BILL_METHOD;
        $arrParam['kessai_id'] = self::KESSAI_ID;
        $arrParam['shop_cd'] = $subData['regular_shop_cd'];
        $arrParam['syuno_co_cd'] = $subData['regular_syuno_co_cd'];
        $arrParam['shop_pwd'] = $subData['regular_shop_pwd'];
        $arrParam['shoporder_no'] = $arrOrderTemp['shoporder_no'];
        $arrParam['card_no'] = $arrOrderTemp['card_no'];
        $arrParam['card_yukokigen'] = $arrOrderTemp['card_yukokigen'];
        $arrParam['seikyuu_kingaku1'] = $arrOrderTemp['payment_total'];
        $arrParam['seikyuu_kingaku2'] = $arrOrderTemp['payment_total'];
        $arrParam['seikyuu_kaishi_ym'] = $arrOrderTemp['seikyuu_kaishi_ym'];
        $arrParam['seikyuu_shuryo_ym'] = $arrOrderTemp['seikyuu_shuryo_ym'];
        $arrParam['seikyuu_hoho'] = self::SEIKYUU_HOHO;

        // 顧客情報を取得
        $arrParam = array_merge($arrParam, $this->getCustomerData($arrOrderTemp));

        $arrUnset = array(
            'shiharai_kbn',
            'seikyuu_kingaku',
            'shouhi_tax',
            'connect_url',
            'hakkou_kbn',
            'yuusousaki_kbn',
            'security_code_flg',
            'card_info_keep',
            'card_info_pwd',
            'arr_conveni',
            'arr_pay_method',
            'customer_id');
        foreach (array_keys($arrUnset) as $key) {
            unset($arrParam[$key]);
        }
        return $arrParam;
    }

    /**
     * 顧客名を取得する
     *
     * @param array $arrOrderTemp 受注一時テーブルの内容
     * @return array $arrCustomer 顧客情報を格納したテーブル
     */
    function getCustomerData($arrOrderTemp) {
        $arrCustomer = array();

        // 顧客番号。ゲスト購入の時（customer_id = 0）は空を格納
        $arrCustomer['customer_id'] = $arrOrderTemp['customer_id'];
        if ($arrOrderTemp['customer_id'] < 1) {
            $objQuery = SC_Query_Ex::getSingletonInstance();
            $arrOrderTemp['customer_id'] = $objQuery->nextVal('dtb_customer_customer_id');
        }
        $arrCustomer['bill_no'] = ($arrOrderTemp['customer_id'] >0) ? str_pad($arrOrderTemp['customer_id'], 14, "0", STR_PAD_LEFT) : "";

        // 顧客名。英数字を全角にする。
        $arrCustomer['bill_name'] = mb_convert_kana($arrOrderTemp['order_name01'] . $arrOrderTemp['order_name02'], "KVAN");
        return $arrCustomer;
    }

    /**
     * モードを返す.
     *
     * @param array　$arrResponse　決済ステーションから返ってきた結果の内容
     * @return string $res_mode 結果コードに対するモード
     */
    function getMode($arrResponse) {
        $res_mode = '';
        // 決済エラー
        if ($arrResponse['rescd'] != MDL_SMBC_RES_OK && $arrResponse['rescd'] != MDL_SMBC_RES_SECURE) {
            $res_mode = 'error';

        // 3Dセキュア
        } elseif ($arrResponse['rescd'] == MDL_SMBC_RES_SECURE) {
            $res_mode = 'secure';

        // 決済OK
        } elseif ($arrResponse['rescd'] == MDL_SMBC_RES_OK) {
            $res_mode = 'complete';
        }

        return $res_mode;
    }

}
?>
