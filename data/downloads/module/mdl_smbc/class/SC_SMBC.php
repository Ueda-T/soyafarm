<?php
if (version_compare(ECCUBE_VERSION, '2.12', '>=')) {
    require_once(DATA_REALDIR . 'module/HTTP/Request.php');
} else {
    require_once(DATA_REALDIR . 'module/Request.php');
}
require_once(MDL_SMBC_CLASS_PATH . 'SC_Mdl_SMBC.php');

// 請求内容（漢字）
define('MDL_SMBC_SEIKYUU_NAME', 'お申込代金');
// 請求内容（カナ）
define('MDL_SMBC_SEIKYUU_KANA', 'オモウシコミダイキン');
// 商品名。商品数が２０以上の場合。
define('MDL_SMBC_GOODS_NAME_OHER', 'その他');
// 商品の項目数上限
define('MDL_SMBC_PRODUCT_CNT_MAX', 20);
// 商品名の上限バイト数
define('MDL_SMBC_GOODS_NAME_MAX_LEN', 100);
// 顧客名の上限バイト数
define('MDL_SMBC_CUSTOMER_NAME_MAX_LEN', 60);
// 顧客住所１項目の上限バイト数
define('MDL_SMBC_ADDR_NAME_MAX_LEN', 50);
// 商品数の上限
define('MDL_SMBC_PRODUCT_QUANTITY_LIMIT', 1000);
// 商品数が上限を超えた時の
define('MDL_SMBC_PRODUCT_QUANTITY_OVER', 999);

// セキュリティコードの最大文字数
define('MDL_SMBC_SECURITY_CODE_LEN', 4);


class SC_SMBC {

    // 連携データ用
    var $arrParam;

    // 受注番号
    var $order_id;

    // セッションのカート情報を格納する配列
    var $arrCart;

    var $objQuery;

    /**
     *  コンストラクタ
     */
    function SC_SMBC() {
        SC_SMBC::init();
    }

    /**
     *  初期化
     */
    function init() {
        $this->objQuery = new SC_Query();

        SC_SMBC::clearArrParam();

        $masterData = new SC_DB_MasterData();
        $this->arrPref = $masterData->getMasterData("mtb_pref");
    }

    /**
     * $this->arrParamを初期化
     *
     * @param void
     * @return void
     */
    function clearArrParam() {
        $this->arrParam = array();
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
        SC_SMBC::addArrParam("shoporder_no", 17);
        SC_SMBC::addArrParam("seikyuu_kingaku", 13);
        SC_SMBC::addArrParam("shouhi_tax", 13);
        SC_SMBC::addArrParam("bill_no", 14);
        SC_SMBC::addArrParam("bill_name", MDL_SMBC_CUSTOMER_NAME_MAX_LEN);
        SC_SMBC::addArrParam("bill_kana", MDL_SMBC_CUSTOMER_NAME_MAX_LEN);
        SC_SMBC::addArrParam("bill_zip", 8);
        SC_SMBC::addArrParam("bill_phon", 14);
        SC_SMBC::addArrParam("bill_mail", 256);
        SC_SMBC::addArrParam("bill_mail_kbn", 1);
        SC_SMBC::addArrParam("seiyaku_date", 8);
        SC_SMBC::addArrParam("seikyuu_name", 100);
        SC_SMBC::addArrParam("seikyuu_kana", 48);
        SC_SMBC::addArrParam("bill_adr", 250);
        for ($i=1; $i <= MDL_SMBC_PRODUCT_CNT_MAX; $i++) {
            SC_SMBC::addArrParam('goods_name_' . $i, 100);
            SC_SMBC::addArrParam('unit_price_' . $i, 11);
            SC_SMBC::addArrParam('quantity_' . $i, 3);
        }
    }

    /**
     * 各項目ごとにサイズと文字コード情報を配列として格納する
     *
     * @param string $key 項目名
     * @param string $size 該当項目の文字サイズ（バイト数）
     * @param string $encode 変換文字コード
     * @return void
     */
    function addArrParam($key, $size, $encode = "SJIS-win") {
        $this->arrParam[$key]['size'] = $size;
        $this->arrParam[$key]['encode'] = $encode;
    }

    /**
     * 項目エレメント名をセットする
     *
     * @param string $key 項目キー
     * @param string $value 項目名
     * @return void
     */
    function setValArrParam($key, $value) {
        $this->arrParam[$key]['value'] = $value;
    }

    /**
     * 連携データを設定する
     *
     * @param array $arrParam 連携データ用配列
     * @return void
     */
    function setParam($arrParam) {
        foreach ($arrParam as $key => $val) {
            SC_SMBC::setValArrParam($key, $val);
        }
    }

    /**
     * 全決済の共通項目の連携データを設定する
     *
     * @param unknown_type $order_id
     * @return array $arrParam
     */
    function makeParam ($order_id) {
        if (version_compare(ECCUBE_VERSION, '2.13', '>=')) {
            $arrTaxRule = SC_Helper_TaxRule_Ex::getTaxRule();
            $tax = 1 + ($arrTaxRule['tax_rate'] / 100);
        } else {
            $arrConf = SC_Helper_DB_Ex::sfGetBasisData();
            $tax = 1 + ($arrConf['tax_rate'] / 100);
        }
        $arrParam = array();

        // dtb_orderの情報を取得
        $arrOrderTemp = $this->getOrderTemp($order_id);

        // 請求番号
        $this->order_id = $arrOrderTemp['order_id'];
        $arrParam['shoporder_no'] = str_pad($this->order_id, 17, "0", STR_PAD_LEFT);

        // 請求金額
        $arrParam['seikyuu_kingaku'] = $arrOrderTemp['payment_total'];
        // 内消費税
        $arrParam['shouhi_tax'] = floor($arrOrderTemp['payment_total']-($arrOrderTemp['payment_total']/$tax));
        // 成約日
        $arrParam['seiyaku_date'] = date('Ymd');
        // 請求内容（漢字）
        $arrParam['seikyuu_name'] = MDL_SMBC_SEIKYUU_NAME;
        // 請求内容（カナ）
        $arrParam['seikyuu_kana'] = MDL_SMBC_SEIKYUU_KANA;

        // モジュールマスタの連携データを取得
        $arrParam = array_merge($arrParam, $this->getModuleMasterData($arrOrderTemp['payment_id']));

        // 顧客情報を取得
        $arrParam = array_merge($arrParam, $this->getCustomerData($arrOrderTemp));

        // 商品情報を連携データを取得
        $arrParam = array_merge($arrParam, $this->getProductsData($this->arrCart));

        return $arrParam;
    }

    /**
     * dtb_order.order_idをnextvalして値を渡す
     *
     */
    function getNextOrderId() {
        return $this->objQuery->nextval("dtb_order_order_id");
    }

    /**
     * dtb_orderからデータを取得
     *
     * @param  string $order_id
     * @return array $arrOrder情報
     */
    function getOrderTemp($order_id) {
        // $order_idをキーにdtb_orderを取得
        $arrOrder = $this->objQuery->select("*","dtb_order", "order_id = ?", array($order_id));

        // order_idをキーにdtb_order_detailを取得
        $this->objQuery->setOrder('order_detail_id');
        $this->arrCart = $this->objQuery->select("*","dtb_order_detail", "order_id = ?", array($order_id));

        $this->objQuery->setOrder('');
         return $arrOrder[0];
    }

    /**
     * モジュールマスタからデータを取得
     *
     * @param integer $payment_id 支払番号
     * @return array
     */
    function getModuleMasterData($payment_id, $type = null) {
        $arrModule = array();

        // payment_idをキーにして、モジュールデータを取得
        $col = "sub_data";
        $from = "dtb_module";
        $where = "module_code = (SELECT module_code FROM dtb_payment WHERE payment_id = ?)";
        $arrModule = $this->objQuery->select($col, $from, $where, array($payment_id));

        // 取得したモジュールデータをunserializeして復元
        $arrModule = unserialize($arrModule[0]['sub_data']);

        $arrRet = array('connect_url' => $arrModule['connect_url'],
                        'shop_cd' => $arrModule['shop_cd'],
                        'syuno_co_cd' => $arrModule['syuno_co_cd'],
                        'shop_pwd' => $arrModule['shop_pwd'],
                        'hakkou_kbn' => $arrModule['payment_slip_issue'],
                        'yuusousaki_kbn' => $arrModule['payment_slip_destination'],
                        'security_code_flg' => $arrModule['security_code_flg'],
                        'card_info_keep' => $arrModule['card_info_keep'],
                        'card_info_pwd' => $arrModule['card_info_pwd'],
                        'arr_conveni' => array(
                            MDL_SMBC_CONVENI_SEVENELEVEN_KESSAI_ID => $arrModule['seven_eleven'],
                            MDL_SMBC_CONVENI_LAWSON_KESSAI_ID => $arrModule['lawson'],
                            MDL_SMBC_CONVENI_SEICOMART_KESSAI_ID => $arrModule['seicomart'],
                            MDL_SMBC_CONVENI_FAMILYMART_KESSAI_ID => $arrModule['familymart'],
                            MDL_SMBC_CONVENI_CIRCLEKSUNKUS_KESSAI_ID => $arrModule['circlek_sunkus'],
                        ),
                        'arr_pay_method' => array(
                            1 => $arrModule['pay_once'],
                            2 => $arrModule['pay_twice'],
                            3 => $arrModule['pay_monthly03'],
                            5 => $arrModule['pay_monthly05'],
                            6 => $arrModule['pay_monthly06'],
                            10 => $arrModule['pay_monthly10'],
                            12 => $arrModule['pay_monthly12'],
                            15 => $arrModule['pay_monthly15'],
                            18 => $arrModule['pay_monthly18'],
                            20 => $arrModule['pay_monthly20'],
                            24 => $arrModule['pay_monthly24'],
                            80 => $arrModule['pay_revolving'],
                            91 => $arrModule['pay_bonus'],
                        )
                       );
        if($type == "recv"){
            $arrRet['over_deposit'] = $arrModule['over_deposit'];
            $arrRet['short_deposit'] = $arrModule['short_deposit'];
            $arrRet['request_deposit'] = $arrModule['request_deposit'];
            $arrRet['request_deposit'] = $arrModule['request_deposit'];
        }

        return $arrRet;
    }

    /**
     * 顧客名・顧客カナ名・顧客郵便番号・顧客住所
     * 顧客メールアドレス・顧客メールアドレス区分を取得する
     *
     * @param array $arrOrderTemp 受注一時テーブルの内容
     * @return array $arrCustomer 顧客情報を格納したテーブル
     */
    function getCustomerData($arrOrderTemp) {
        $arrCustomer = array();

        $objQuery =& SC_Query_Ex::getSingletonInstance();

	// 顧客情報に取引IDが設定されていない場合セットする
	$sql =<<<EOF
SELECT torihiki_id
FROM dtb_customer
WHERE customer_id = ?
EOF;
	$torihikiId = $objQuery->getOne($sql, array($arrOrderTemp['customer_id']));

        // 顧客番号。ゲスト購入の時（customer_id = 0）は空を格納
        $arrCustomer['customer_id'] = $arrOrderTemp['customer_id'];
	if ($torihikiId) {
	    $arrCustomer['bill_no'] = $torihikiId;
	} else {
	    $arrCustomer['bill_no'] = ($arrOrderTemp['customer_id'] >0) ? str_pad($arrOrderTemp['customer_id'], 14, "0", STR_PAD_LEFT) : "";
	}

        // 顧客名。英数字を全角にする。
        //$arrCustomer['bill_name'] = mb_convert_kana($arrOrderTemp['order_name01'] . $arrOrderTemp['order_name02'], "KVAN");
        $arrCustomer['bill_name'] = mb_convert_kana($arrOrderTemp['order_name'], "KVAN");

        // 顧客カナ名。半角カナに変換。
        //$arrCustomer['bill_kana'] = mb_convert_kana($arrOrderTemp['order_kana01'] . $arrOrderTemp['order_kana02'], "kan");
        $arrCustomer['bill_kana'] = mb_convert_kana($arrOrderTemp['order_kana'], "kan");

        // 顧客郵便番号
        //$arrCustomer['bill_zip'] = $arrOrderTemp['order_zip01'] . $arrOrderTemp['order_zip02'];
        $arrCustomer['bill_zip'] = str_replace("-", "", $arrOrderTemp['order_zip']);

        // 顧客電話番号
        //$arrCustomer['bill_phon'] = $arrOrderTemp['order_tel01'] . $arrOrderTemp['order_tel02'] . $arrOrderTemp['order_tel03'];
        $arrCustomer['bill_phon'] = $arrOrderTemp['order_tel'];

        // 顧客メールアドレス
        $arrCustomer['bill_mail'] = $arrOrderTemp['order_email'];

        // 顧客メールアドレス区分(PC:0 モバイル:1)
        //$arrCustomer['bill_mail_kbn'] = (SC_Helper_Mobile::gfIsMobileMailAddress($arrOrderTemp['order_email'])) ? 1 : 0; // TODO 定数化する
        $arrCustomer['bill_mail_kbn'] = 0; // 全てPC扱い

        // 顧客住所。
        $arrCustomer['bill_adr'] = mb_convert_kana($this->arrPref[$arrOrderTemp['order_pref']] . $arrOrderTemp['order_addr01'] . $arrOrderTemp['order_addr02'], "AKNS");
        $arrCustomer['bill_adr'] = str_replace("－", "―", $arrCustomer['bill_adr']);

        return $arrCustomer;
    }

    /**
     * 復元したセッションからカート内の商品数を取得
     * 商品数が20未満の場合はそのままgoods_name_1～goods_name_20に商品名を入れる。
     * 商品数が21以上の場合は、goods_name_1～goods_name_19には通常通り設定し、
     * goods_name_20には「その他」、quantity_20は'1'、unit_price_20は20以上の送金額を設定
     *
     * @param array $arrCart セッションのカート情報
     * @return array $arrProducts カート内の商品情報
     */
    function getProductsData($arrCart) {
        $arrProducts = array();
        $arrProductsName = array();

        $cnt = 1; // 商品項目数
        $over_limit_price = 0; // 商品20以上の合計価格
        if (count($arrCart) > 0){
            foreach($arrCart as $key => $val) {
                if (is_numeric($key) == true && strlen($arrCart[$key]['order_detail_id']) > 0) {
                    if ($cnt >= MDL_SMBC_PRODUCT_CNT_MAX) {
                        $over_limit_price += $arrCart[$key]['price'] * $arrCart[$key]['quantity'];
                    }

                    if ($cnt <= MDL_SMBC_PRODUCT_CNT_MAX) {
                        //　商品名を取得
                        // 文字コード変換後。
                        $arrProducts['goods_name_' . $cnt] = $arrCart[$key]['product_name'];

                        // 商品単価。
                        $arrProducts['unit_price_' . $cnt] = $arrCart[$key]['price'];

                        // 商品の数量。数量が1000以上の場合は999とする。
                        if ($arrCart[$i]['quantity'] < MDL_SMBC_PRODUCT_QUANTITY_LIMIT) {
                            $arrProducts['quantity_' . $cnt] = $arrCart[$key]['quantity'];
                        } else {
                            $arrProducts['quantity_' . $cnt] = MDL_SMBC_PRODUCT_QUANTITY_OVER;
                        }
                    }
                    $cnt++;
                }
            }
        }

        // 商品数が20を超えた場合、商品名="その他"・商品単価=[商品数]*[商品単価]・商品数=1とする
        if ($cnt-1 > MDL_SMBC_PRODUCT_CNT_MAX) {
            $arrProducts['goods_name_20'] = MDL_SMBC_GOODS_NAME_OHER;
            $arrProducts['unit_price_20'] = $over_limit_price;
            $arrProducts['quantity_20'] = "1";

        // 商品数が20未満の場合は、初期化して超えた分の送信用配列の商品情報部分を削除
        } else {
            for ($i = $cnt; $i <= MDL_SMBC_PRODUCT_CNT_MAX; $i++) {
                unset($this->arrParam['goods_name_' . $i]);
                unset($this->arrParam['unit_price_' . $i]);
                unset($this->arrParam['quantity_' . $i]);
            }
        }

        return $arrProducts;
    }

    /**
     * $this->arrParamに格納されている情報に合わせて、文字コード変換・サイズ調整を行う
     *
     * @param void
     * @return array $arrSendParam 変換後の送信用データ
     */
    function convParamStr () {
        $arrSendParam = array();

        // 変換前送信データをログ出力
        $this->printLog($this->arrParam);

        foreach ($this->arrParam as $key => $val) {
            $arrSendParam[$key] = mb_convert_encoding($val['value'], $val['encode'], "auto");

            if (strlen($val['size']) > 0) {
                // 顧客住所は50バイトずつに区切り、250バイトを超えた分は割愛
                if ($key == "bill_adr") {
                    $order_addr = substr($arrSendParam[$key], 0, 250);
                    $cnt = ceil(strlen($order_addr) / MDL_SMBC_ADDR_NAME_MAX_LEN);
                    for ($i = 1; $i <= $cnt; $i++) {
                        $arrSendParam['bill_adr_' . $i] = substr($order_addr, ($i-1)*MDL_SMBC_ADDR_NAME_MAX_LEN, MDL_SMBC_ADDR_NAME_MAX_LEN);
                    }
                    unset($arrSendParam['bill_adr']);
                } else {
                    $arrSendParam[$key] = substr($arrSendParam[$key], 0, $val['size']);
                }
            }
        }
        return $arrSendParam;
    }

    /**
     *　受注番号を取得する
     *
     */
    function getOrderId() {
        return $this->order_id;
    }

    /**
     * 定期受注ID(継続課金での shoporder_no) を生成する.
     */
    function createRegularOrderId() {
	/*
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $regular_order_id = str_replace('.', '', (uniqid('R', true)));
        while($exists = $objQuery->get('shoporder_no', 'dtb_mdl_smbc_regular_order',
                                       'shoporder_no = ?', array($regular_order_id))) {
            $regular_order_id = str_replace('.', '', (uniqid('R', true)));
        }
	 */
	// 固定値にするため処理変更
        $regular_order_id = 0;
        return $regular_order_id;
    }

    /**
     * 定期受注検索の SELECT 句を返す.
     *
     * @param string $extended_where dtb_mdl_smbc_regular_order の WHERE 句を拡張する場合に使用する.
     * @return string 定期受注検索の SELECT 句
     */
    public static function regularOrderSelectSQL($extended_where = '') {
        $select = "T.shoporder_no, T.create_date, T.regular_interval_from, T.regular_interval_to, T.regular_status, T.order_id, T.rescd, T.res, T.bill_no ";

        // 商品名
        // XXX サブクエリの影響で SC_DB_Factory でうまく置換できない
        if (DB_TYPE == 'pgsql') {
            $select .= <<< __EOS__
                   , (SELECT ARRAY_TO_STRING(ARRAY(
                             SELECT product_name || '/' || COALESCE(classcategory_name1, '(なし)') || '/' || COALESCE(classcategory_name2, '(なし)')
                               FROM dtb_order_detail
                              WHERE dtb_order_detail.order_id =
                                 (SELECT order_id
                                    FROM dtb_mdl_smbc_regular_order T1
                                   WHERE T1.shoporder_no = T.shoporder_no AND del_flg = 0 {$extended_where}
                                  ORDER BY create_date DESC LIMIT 1)), '<br />')) as product_name
__EOS__;
        } else {
            $select .= <<< __EOS__
                       , (SELECT GROUP_CONCAT(CONCAT(product_name, '/', IFNULL(classcategory_name1, '(なし)'), '/', IFNULL(classcategory_name2, '(なし)')) SEPARATOR '<br />')
                               FROM dtb_order_detail
                              WHERE dtb_order_detail.order_id =
                                 (SELECT order_id
                                    FROM dtb_mdl_smbc_regular_order T1
                                   WHERE T1.shoporder_no = T.shoporder_no AND del_flg = 0 {$extended_where}
                                  ORDER BY create_date DESC LIMIT 1))  as product_name
__EOS__;
        }
        // お名前
        $name_sql = (DB_TYPE == 'pgsql')
                    ? "name01 || name02"
                    : "CONCAT(name01, name02)";
        $select .= <<< __EOS__
                   , (SELECT {$name_sql}
                        FROM dtb_mdl_smbc_regular_customer T1
                       WHERE bill_no =
                                 (SELECT bill_no
                                    FROM dtb_mdl_smbc_regular_order T1
                                   WHERE T1.shoporder_no = T.shoporder_no AND del_flg = 0 {$extended_where}
                                  ORDER BY create_date DESC LIMIT 1)
                       ) as name
__EOS__;
        $regular_status_settled = MDL_SMBC_REGULAR_STATUS_SETTLED;
        $regular_status_completed = MDL_SMBC_REGULAR_STATUS_COMPLETED;
        // 購入回数
        $select .= <<< __EOS__
                   , (SELECT count(order_id)
                        FROM dtb_mdl_smbc_regular_order T1
                       WHERE T1.shoporder_no = T.shoporder_no AND del_flg = 0 {$extended_where}
                         AND regular_status IN ({$regular_status_completed}, {$regular_status_settled})) AS purchased
__EOS__;

        // 購入金額
        $select .= <<< __EOS__
                   , (SELECT payment_total
                               FROM dtb_order
                              WHERE dtb_order.order_id =
                                 (SELECT order_id
                                    FROM dtb_mdl_smbc_regular_order T1
                                   WHERE T1.shoporder_no = T.shoporder_no AND del_flg = 0 {$extended_where}
                                  ORDER BY create_date DESC LIMIT 1)) as payment_total
__EOS__;

        // 受注ステータス
        $select .= <<< __EOS__
                   , (SELECT status
                               FROM dtb_order
                              WHERE dtb_order.order_id =
                                 (SELECT order_id
                                    FROM dtb_mdl_smbc_regular_order T1
                                   WHERE T1.shoporder_no = T.shoporder_no AND del_flg = 0 {$extended_where}
                                  ORDER BY create_date DESC LIMIT 1)) as status
__EOS__;

        return $select;
    }

    /**
     * 定期受注検索の FROM 句を返す.
     *
     * @param string $extended_where dtb_mdl_smbc_regular_order の WHERE 句を拡張する場合に使用する.
     * @return string FROM 句
     */
    public static function regularOrderFromSQL($extended_where = '') {
        $from = <<< __EOS__
                dtb_mdl_smbc_regular_order T
            JOIN (SELECT
                     MAX(create_date) AS new_date, shoporder_no
                    FROM dtb_mdl_smbc_regular_order WHERE del_flg = 0 {$extended_where}
                   GROUP BY shoporder_no) AS A
              ON A.shoporder_no = T.shoporder_no AND del_flg = 0
             AND A.new_date = T.create_date
            JOIN dtb_order
              ON T.order_id = dtb_order.order_id
__EOS__;
        return $from;
    }

    /**
     * データを決済ステーションへ送信する
     *
     * @param string $serverUrl 送信先URL
     * @return array $arrResponse レスポンスボディ
     */
    function sendParam($serverUrl) {
        $objReq = new HTTP_Request($serverUrl);
        $arrResponse = array();

        // POSTで送信
        $objReq->setMethod('POST');

        // 送信データの文字コード変換・サイズ調整
        $arrSendParam = $this->convParamStr();

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

    /**
     * 決済ステーションからのレスポンスを解析
     *
     * ○○○=□□□
     * から
     * array[○○○]=□□□
     * の形式にする
     *
     * @param unknown_type $response
     * @return unknown
     */
    function parse($response) {
        // 先頭が " の場合は CSV形式
        if (preg_match('/^"/', $response)) {
            return $this->parseCSV($response);
        }

        $arrResponse = array();

        $response = explode("\n", $response);

        foreach ($response as $key => $val) {
            $arrTemp = explode("=", $val, 2);
            $arrResponse[$arrTemp[0]] = rtrim($arrTemp[1], "\r");
        }
        return $arrResponse;
    }

    /**
     * CSV 形式の結果を parse する
     */
    function parseCSV($response) {
        $arrHeader = array();
        $arrBody = array();
        $arrFooter = array();
        $arrResults = array();

        $arrResponse = explode("\r\n", $response);
        foreach ($arrResponse as $row) {
            $arrRow = explode(",", $row);
            foreach ($arrRow as &$col) {
                $col = trim($col, '"');
                $col = mb_convert_encoding($col, CHAR_CODE, 'SJIS-win');
            }
            switch ($arrRow[0]) {
                // ヘッダレコード
                case 10:
                    $arrHeader = $arrRow;
                    $arrHeader['rescd'] = $arrRow[1];
                    $arrHeader['res'] = $arrRow[2];
                    break;
                // ボディレコード
                case 20:
                    $arrBody[] = $arrRow;
                    break;
                // フッタレコード
                case 80:
                    $arrFooter = $arrRow;
                    break;
                default:
            }
        }
        $arrResults['header'] = $arrHeader;
        $arrResults['body'] = $arrBody;
        $arrResults['footer'] = $arrFooter;

        return $arrResults;
    }


    /**
     * 送信データの文字コード変換（SJIS）を行う。
     *
     * @param array $arrSendData 変換対象
     * @return array 変換した値
     */
    function send_data_encoding($arrSendData) {
        foreach ($arrSendData as $key => $val) {
            $arrSendData[$key] = mb_convert_encoding($val, "SJIS-win", "auto");
        }
        return $arrSendData;
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

    /**
     * ログを出力.
     *
     * @param string $msg
     * @param mixed $data
     */
    function printLog($msg, $raw = false) {
        require_once CLASS_REALDIR . 'SC_Customer.php';
        $objCustomer = new SC_Customer;
        $userId = $objCustomer->getValue('customer_id');
        $path = DATA_REALDIR . 'logs/mdl_smbc.log';

        // パスワード等をマスクする
        if (!$raw && is_array($msg)) {
            $keys = array('card_no', 'security_cd');
            foreach ($keys as $key) {
                if (isset($msg[$key]) && !is_array($msg[$key])) {
                    $msg[$key] = str_pad('', strlen($msg[$key]), '*');
                }
            }

            $msg = print_r($msg, true);
        }

        // 決済ステーション送信データに文字コードの不正な情報がないか調査できるよう
        // 個別に文字コードを判断して変換せずに、$msg全体を同一文字コードで変換する
        mb_convert_variables('UTF-8', 'auto', $msg);

        GC_Utils::gfPrintLog("user=$userId: " . $msg, $path);
    }
}
?>
