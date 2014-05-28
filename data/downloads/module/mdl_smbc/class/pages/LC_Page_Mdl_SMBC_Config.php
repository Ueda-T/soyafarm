<?php
require_once(CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php');
require_once(CLASS_EX_REALDIR . 'page_extends/admin/ownersstore/LC_Page_Admin_OwnersStore_Ex.php');
require_once(MDL_SMBC_CLASS_PATH . 'SC_Mdl_SMBC.php');
require_once CONFIG_REALFILE;

/**
 * SMBC決済モジュールの管理画面クラス.
 *
 * @package Page
 */
class LC_Page_Mdl_SMBC_Config extends LC_Page_Admin_Ex {
    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = MDL_SMBC_TEMPLATE_PATH . 'config.tpl';
        $this->tpl_subtitle = 'SMBCファイナンスサービス決済モジュール';

        $this->arrDeposit = array(1 => '「入金済み」にする', 0 => '「入金済み」にしない');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objSMBC =& SC_Mdl_SMBC::getInstance();
        $objSMBC->install();

        $objView = new SC_AdminView;

        $mode = isset($_POST['mode']) ? $_POST['mode'] : '';

        switch($mode) {
        // 登録ボタンが押された時
        case 'register':
            $this->registerMode();
            if(version_compare(ECCUBE_VERSION, '2.12.0') >= 0) {
                // コンパイルファイルのクリア処理
                // SC_Utils_Ex::clearCompliedTemplate();
                // /adminのみクリア
                SC_Helper_FileManager_Ex::deleteFile(COMPILE_ADMIN_REALDIR, false);
            }
            break;
        default:
            $this->defaultMode();
            break;
        }

        $objView->assignObj($this);
        $objView->display($this->tpl_mainpage);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
    }

     /**
     * 初回表示処理
     *
     */
    function defaultMode() {
        $objSMBC =& SC_Mdl_SMBC::getInstance();
        $subData = $objSMBC->getSubData();

        $this->initParam($subData);
        $this->arrForm = $this->objFormParam->getFormParamList();
    }

    /**
     * フォームパラメータ初期化
     *
     * @param array $arrData
     * @return SC_FormParam
     */
    function initParam($arrData = null) {
        if (is_null($arrData) == true) {
            $arrData = $_POST;
        }

        // パラメータ管理クラス
        $this->objFormParam = new SC_FormParam();

        // パラメータ情報の初期化
        $this->objFormParam->addParam("接続先", "connect_url", INT_LEN, "na", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "SPTAB_CHECK"));

        // いずれかの入力があれば、必須チェックを付与する
        if (!SC_Utils_Ex::isBlank($arrData['shop_cd'])
            || !SC_Utils_Ex::isBlank($arrData['syuno_co_cd'])
            || !SC_Utils_Ex::isBlank($arrData['shop_pwd'])) {
            $this->objFormParam->addParam("契約コード", "shop_cd", MDL_SMBC_SHOP_CD_LEN, "na", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
            $this->objFormParam->addParam("収納企業コード", "syuno_co_cd", MDL_SMBC_SYUNO_CO_CD_LEN, "na", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
            $this->objFormParam->addParam("ショップパスワード", "shop_pwd", MDL_SMBC_SHOP_PWD_LEN, "na", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        } else {
            $this->objFormParam->addParam("契約コード", "shop_cd", MDL_SMBC_SHOP_CD_LEN, "na", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
            $this->objFormParam->addParam("収納企業コード", "syuno_co_cd", MDL_SMBC_SYUNO_CO_CD_LEN, "na", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
            $this->objFormParam->addParam("ショップパスワード", "shop_pwd", MDL_SMBC_SHOP_PWD_LEN, "na", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        }

        $this->objFormParam->addParam("クレジット", "credit", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("コンビニ（番号方式）", "conveni_number", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("払込票（コンビニ、ゆうちょ等）", "payment_slip", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("銀行振込", "bank_transfer", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("ペイジー", "pay_easy", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("電子マネー", "electronic_money", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("ネットバンク", "netbank", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("クレジットカード(継続課金)", "credit_regular", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        if ($arrData['credit'] == 1) {
            $this->objFormParam->addParam("１回払い", "pay_once", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
            $this->objFormParam->addParam("２回払い", "pay_twice", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
            $this->objFormParam->addParam("分割払い（３回）", "pay_monthly03", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
            $this->objFormParam->addParam("分割払い（５回）", "pay_monthly05", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
            $this->objFormParam->addParam("分割払い（６回）", "pay_monthly06", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
            $this->objFormParam->addParam("分割払い（１０回）", "pay_monthly10", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
            $this->objFormParam->addParam("分割払い（１２回）", "pay_monthly12", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
            $this->objFormParam->addParam("分割払い（１５回）", "pay_monthly15", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
            $this->objFormParam->addParam("分割払い（１８回）", "pay_monthly18", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
            $this->objFormParam->addParam("分割払い（２０回）", "pay_monthly20", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
            $this->objFormParam->addParam("分割払い（２４回）", "pay_monthly24", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
            $this->objFormParam->addParam("リボ払い", "pay_revolving", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
            $this->objFormParam->addParam("ボーナス払い", "pay_bonus", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));

            $this->objFormParam->addParam("セキュリティコード", "security_code_flg", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
            $this->objFormParam->addParam("クレジットカードお預かり機能", "card_info_keep", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
            if ($arrData['card_info_keep'] == 1) {
                $this->objFormParam->addParam("お預かり機能用パスワード", "card_info_pwd", MDL_SMBC_CARD_INFO_PWD_LEN, "na", array("ALNUM_CHECK", "EXIST_CHECK", "MAX_LENGTH_CHECK", "SPTAB_CHECK"));
            }
        }

        if ($arrData['bank_transfer'] == 1) {
            $this->objFormParam->addParam("過入金", "over_deposit", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
            $this->objFormParam->addParam("一部入金", "short_deposit", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
            $this->objFormParam->addParam("依頼入金", "request_deposit", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        }

        if ($arrData['conveni_number'] == 1) {
            $this->objFormParam->addParam("セブンイレブン", "seven_eleven", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
            $this->objFormParam->addParam("ローソン", "lawson", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
            $this->objFormParam->addParam("セイコーマート", "seicomart", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
            $this->objFormParam->addParam("ファミリーマート", "familymart", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
            $this->objFormParam->addParam("サークルK・サンクス", "circlek_sunkus", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        }

        if ($arrData['payment_slip'] == 1) {
            $this->objFormParam->addParam("払込票の印刷", "payment_slip_issue", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
            if ($arrData['payment_slip_issue'] == MDL_SMBC_PAYMENT_SLIP_ISSUE_SMBC) {
                $this->objFormParam->addParam("郵送先", "payment_slip_destination", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
            }
        }

        // 定期販売用設定
        // いずれかの入力があれば、必須チェックを付与する
        if (!SC_Utils_Ex::isBlank($arrData['regular_shop_cd'])
            || !SC_Utils_Ex::isBlank($arrData['regular_syuno_co_cd'])
            || !SC_Utils_Ex::isBlank($arrData['regular_shop_pwd'])
            || !SC_Utils_Ex::isBlank($arrData['regular_deal_pwd'])) {
            $this->objFormParam->addParam("契約コード", "regular_shop_cd", MDL_SMBC_SHOP_CD_LEN, "na", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
            $this->objFormParam->addParam("収納企業コード", "regular_syuno_co_cd", MDL_SMBC_SYUNO_CO_CD_LEN, "na", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
            $this->objFormParam->addParam("ショップパスワード", "regular_shop_pwd", MDL_SMBC_SHOP_PWD_LEN, "na", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
            $this->objFormParam->addParam("取引検索パスワード", "regular_deal_pwd", MDL_SMBC_SHOP_PWD_LEN, "na", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        } else {
            $this->objFormParam->addParam("契約コード", "regular_shop_cd", MDL_SMBC_SHOP_CD_LEN, "na", array( "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
            $this->objFormParam->addParam("収納企業コード", "regular_syuno_co_cd", MDL_SMBC_SYUNO_CO_CD_LEN, "na", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
            $this->objFormParam->addParam("ショップパスワード", "regular_shop_pwd", MDL_SMBC_SHOP_PWD_LEN, "na", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
            $this->objFormParam->addParam("取引検索パスワード", "regular_deal_pwd", MDL_SMBC_SHOP_PWD_LEN, "na", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        }

        $this->objFormParam->setParam($arrData);
        $this->objFormParam->convParam();
    }

    /**
     * 入力パラメータの検証
     *
     * @param SC_FormParam $objForm
     * @return array|null
     */
    function checkError() {
        // 入力データを渡す。
        $arrRet =  $this->objFormParam->getHashArray();
        $objErr = new SC_CheckError($arrRet);
        $objErr->arrErr = $this->objFormParam->checkError();

        if (SC_Utils_Ex::isBlank($arrRet['shop_cd'])
            && SC_Utils_Ex::isBlank($arrRet['syuno_co_cd'])
            && SC_Utils_Ex::isBlank($arrRet['shop_pwd'])
            && SC_Utils_Ex::isBlank($arrRet['regular_shop_cd'])
            && SC_Utils_Ex::isBlank($arrRet['regular_syuno_co_cd'])
            && SC_Utils_Ex::isBlank($arrRet['regular_shop_pwd'])
            && SC_Utils_Ex::isBlank($arrRet['regular_deal_pwd'])) {
            $objErr->arrErr['top'] = '※ 都度決済設定または、定期販売設定の情報は、どちらか必ず入力してください。';
        }

        if (SC_Utils_Ex::isBlank($arrRet['shop_cd'])
            && SC_Utils_Ex::isBlank($arrRet['syuno_co_cd'])
            && SC_Utils_Ex::isBlank($arrRet['shop_pwd'])) {
            if ($arrRet['credit'] == 1
                || $arrRet['conveni_number'] == 1
                || $arrRet['payment_slip'] == 1
                || $arrRet['bank_transfer'] == 1
                || $arrRet['pay_easy'] == 1
                || $arrRet['electronic_money'] == 1
                || $arrRet['netbank'] == 1) {
                $objErr->arrErr['top'] = '※ 都度決済設定が入力されていません。';
            }
        }

        if (SC_Utils_Ex::isBlank($arrRet['regular_shop_cd'])
            && SC_Utils_Ex::isBlank($arrRet['regular_syuno_co_cd'])
            && SC_Utils_Ex::isBlank($arrRet['regular_shop_pwd'])
            && SC_Utils_Ex::isBlank($arrRet['regular_deal_pwd'])) {
            if ($arrRet['credit_regular'] == 1) {
                $objErr->arrErr['top'] = '※ 定期販売設定が入力されていません。';
            }
        }

        if ($arrRet['credit'] != 1 && $arrRet['conveni_number'] != 1 && $arrRet['payment_slip'] != 1 && $arrRet['bank_transfer'] != 1 &&
            $arrRet['pay_easy'] != 1 && $arrRet['electronic_money'] != 1 && $arrRet['netbank'] != 1 && $arrRet['credit_regular'] != 1) {
            $objErr->arrErr['pay_type'] = "※ 利用決済が入力されていません。<br />";
        }

        if ($arrRet['credit'] == 1 &&
            $arrRet['pay_once'] != 1 &&
            $arrRet['pay_twice'] != 1 &&
            $arrRet['pay_monthly03'] != 1 &&
            $arrRet['pay_monthly05'] != 1 &&
            $arrRet['pay_monthly06'] != 1 &&
            $arrRet['pay_monthly10'] != 1 &&
            $arrRet['pay_monthly12'] != 1 &&
            $arrRet['pay_monthly15'] != 1 &&
            $arrRet['pay_monthly18'] != 1 &&
            $arrRet['pay_monthly20'] != 1 &&
            $arrRet['pay_monthly24'] != 1 &&
            $arrRet['pay_revolving'] != 1 &&
            $arrRet['pay_bonus'] != 1 ) {
            $objErr->arrErr['paymethod'] = "※ お支払い区分が入力されていません。<br />";
        }

        if ($arrRet['conveni_number'] == 1 && $arrRet['seven_eleven'] != 1 && $arrRet['lawson'] != 1 && $arrRet['seicomart'] != 1 &&
            $arrRet['familymart'] != 1 && $arrRet['circlek_sunkus'] != 1 ) {
            $objErr->arrErr['conveni'] = "※ 対応コンビニが入力されていません。<br />";
        }

        if (extension_loaded("openssl") === false) {
            $objErr->arrErr['top'] = "※ openssl拡張モジュールをロードできません。PHPの拡張モジュールをインストールしてください。";
        }

        return $objErr->arrErr;
    }

    /**
     * 支払い方法テーブルを更新する.
     *
     * @param boolean $diffData
     */
    function updatePaymentTable($diffData) {
        $objSMBC =& SC_Mdl_SMBC::getInstance();
        $moduleCode = $objSMBC->getCode(true);

        $objQuery = new SC_Query;
        $objSess = new SC_Session;

        // 登録データ構築
        $arrPaymentInfo = array(
            "fix"            => '3',
            "module_code"    => $objSMBC->getCode(true),
            "del_flg"        => "0",
            'memo03'         => "###", // 購入フロー中、決済情報入力ページへの遷移振り分けをmemo03で判定しているため
            "creator_id"     => $objSess->member_id,
            "update_date"    => "NOW()",
        );

        // ランクの最大値を取得する
        $max_rank = $objQuery->getOne("SELECT max(rank) FROM dtb_payment");
        $arrPaymentInfo['rank'] = $max_rank + 1;

        // 存在するカラムのみを対象とする
        $cols = $objQuery->listTableFields('dtb_payment');
        foreach ($diffData as $key => $val) {
            if (in_array($key, $cols)) {
                $arrPaymentInfo[$key] = $val;
            }
        }

        $count = $objQuery->count('dtb_payment', 'module_code = ? AND memo01 = ?', array($moduleCode, $diffData['memo01']));
        if($count) {
            $objQuery->update("dtb_payment", $arrPaymentInfo, "module_code = ? AND memo01 = ?", array($moduleCode, $diffData['memo01']));
        } else {
            $arrPaymentInfo['payment_id'] = $objQuery->nextVal("dtb_payment_payment_id");
            $objQuery->insert("dtb_payment", $arrPaymentInfo);
        }
    }

    /**
     * 支払方法の削除
     *
     */
    function deletePaymentType($paymentType) {
        $objSMBC =& SC_Mdl_SMBC::getInstance();
        $moduleCode = $objSMBC->getCode(true);

        $objQuery = new SC_Query;
        $objQuery->update(
            "dtb_payment", array('del_flg' => '1'),
            "module_code = ? AND memo01 = ?", array($moduleCode, $paymentType)
        );
    }

    /**
     * 全テーブルリストを取得する
     *
     */
    function getTableList(){
        $dbFactory = SC_DB_DBFactory_Ex::getInstance();
        $arrMasterDataName = $dbFactory->findTableNames();

        return $arrMasterDataName;
    }

    /**
     * 登録ボタン押下時の処理
     *
     */
    function registerMode() {
        $objQuery = new SC_Query();

        // パラメータの初期化
        $this->initParam();

        // エラーチェック
        $this->arrErr = $this->checkError();

        if (count($this->arrErr) == 0) {
            $arrForm = $this->objFormParam->getHashArray();
            $objSMBC =& SC_Mdl_SMBC::getInstance();

            ////////////////////////////////////////////////////////////
            // ファイルのコピー                                       //
            ////////////////////////////////////////////////////////////
            $arrFailedFile = $objSMBC->updateFile();
            if (count($arrFailedFile) > 0) {
                $this->arrForm = $this->objFormParam->getFormParamList();
                foreach($arrFailedFile as $file) {
                    $alert = $file . 'に書き込み権限を与えてください。';
                    $this->tpl_onload .= "alert('" . $alert . "');";
                }
                return;
            }

            ////////////////////////////////////////////////////////////
            // dtb_mdl_smbc_orderテーブルの存在チェックを行い         //
            // 存在しなければテーブルを作成する                       //
            ////////////////////////////////////////////////////////////
            // テーブルの存在チェック
            $arrTableList = $this->getTableList();
            // 存在していなければ作成
            if(!in_array("dtb_mdl_smbc_order", $arrTableList)){
                $cre_sql = "create table dtb_mdl_smbc_order (
                    order_id INT4,
                    credit_status INT2,
                    update_date timestamp
                );
            ";
                $objQuery->query($cre_sql);
            }

            if(!in_array("dtb_mdl_smbc_customer", $arrTableList)){
                $cre_sql = "create table dtb_mdl_smbc_customer (
                    customer_id INT4,
                    branch_code TEXT,
                    account_number TEXT
                );
            ";
                $objQuery->query($cre_sql);
            }

            if(!in_array("dtb_mdl_smbc_bankaccount", $arrTableList)){
                $cre_sql = "create table dtb_mdl_smbc_bankaccount (
                    bank_code TEXT,
                    branch_code TEXT,
                    account_type INT2,
                    account_number TEXT,
                    account_name TEXT,
                    shop_cd TEXT,
                    syuno_co_cd TEXT,
                    kyoten_cd TEXT,
                    bill_no TEXT,
                    bill_name TEXT,
                    change_flg INT2,
                    update_date timestamp
                );
            ";
                $objQuery->query($cre_sql);
            }
            
            if(!in_array("dtb_mdl_smbc_payment", $arrTableList)){
                $cre_sql = "create table dtb_mdl_smbc_payment (
                    order_id INT4,
                    payment_status INT2,
                    payment_date timestamp,
		    payment_name TEXT,
		    payment_amount numeric
                );
            ";
                $objQuery->query($cre_sql);
            }
            
            if(!in_array("dtb_mdl_smbc_regular_customer", $arrTableList)){
                $cre_sql = "create table dtb_mdl_smbc_regular_customer (
                    bill_no bigint NOT NULL,
                    customer_id int NOT NULL,
                    name01 text,
                    name02 text,
                    kana01 text,
                    kana02 text,
                    zip01 text,
                    zip02 text,
                    pref smallint,
                    addr01 text,
                    addr02 text,
                    email text,
                    email_mobile text,
                    tel01 text,
                    tel02 text,
                    tel03 text,
                    sex smallint,
                    del_flg smallint NOT NULL DEFAULT 0,
                    create_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    update_date timestamp NOT NULL,
                    PRIMARY KEY (bill_no)
                );
            ";
                $objQuery->query($cre_sql);
            }
            
            if(!in_array("dtb_mdl_smbc_regular_order", $arrTableList)){
                $cre_sql = "create table dtb_mdl_smbc_regular_order (
                    bill_no bigint NOT NULL,
                    shoporder_no varchar(25) NOT NULL,
                    order_id int NOT NULL,
                    regular_status smallint NOT NULL,
                    cleaning_ym TEXT,
                    cleaning_result smallint,
                    regular_interval_from TEXT,
                    regular_interval_to TEXT,
                    target_ym varchar(6),
                    kessai_no bigint,
                    rescd text,
                    res text,
                    del_flg smallint NOT NULL DEFAULT 0,
                    create_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    update_date timestamp NOT NULL,
                    PRIMARY KEY (shoporder_no, bill_no, order_id)
                );
            ";
                $objQuery->query($cre_sql);
            }

            // インデックスの設定
            $arrIndexes = $objQuery->listTableIndexes("dtb_mdl_smbc_regular_order");
            if(!in_array("dtb_mdl_smbc_regular_order_create_date", $arrIndexes)){
                $cre_sql = "CREATE INDEX dtb_mdl_smbc_regular_order_create_date_idx ON dtb_mdl_smbc_regular_order (create_date);";
                $objQuery->query($cre_sql);
            }
            if(!in_array("dtb_mdl_smbc_regular_order_target_ym", $arrIndexes)){
                $cre_sql = "CREATE INDEX dtb_mdl_smbc_regular_order_target_ym_idx ON dtb_mdl_smbc_regular_order (target_ym);";
                $objQuery->query($cre_sql);
            }
            $arrIndexes = $objQuery->listTableIndexes("dtb_order_detail");
            if(!in_array("dtb_order_detail_order_id", $arrIndexes)){
                $cre_sql = "CREATE INDEX dtb_order_detail_order_id_idx ON dtb_order_detail (order_id);";
                $objQuery->query($cre_sql);
            }

            ////////////////////////////////////////////////////////////
            // dtb_paymentへ支払方法を設定する。                      //
            // チェックボックスで選択されなかった支払方法は論理削除。 //
            ////////////////////////////////////////////////////////////
            if(version_compare(ECCUBE_VERSION, '2.13') >= 0) {
                $php_path = 'mdl_smbc/';
            } else {
                $php_path = MDL_SMBC_PATH;
            }
            // ネットバンク
            if (isset($arrForm['netbank']) && $arrForm['netbank'] == '1') {
                $this->updatePaymentTable(
                    array('module_path' => $php_path . 'netbank.php', 'rule' => 1, 'rule_max' => 1, 'upper_rule' => MDL_SMBC_NETBUNK_UPPER_RULE_MAX, 'memo01' => MDL_SMBC_NETBUNK_BILL_METHOD, "payment_method" => MDL_SMBC_NETBUNK_PAY_TYPE)
                );
            } else {
                $this->deletePaymentType(MDL_SMBC_NETBUNK_BILL_METHOD);
            }
            // 電子マネー
            if (isset($arrForm['electronic_money']) && $arrForm['electronic_money'] == '1') {
                $this->updatePaymentTable(
                    array('module_path' => $php_path . 'e_money.php', 'rule' => 1, 'rule_max' => 1, 'upper_rule' => MDL_SMBC_ELECTRONIC_MONEY_UPPER_RULE_MAX, 'memo01' => MDL_SMBC_ELECTRONIC_MONEY_BILL_METHOD, "payment_method" => MDL_SMBC_ELECTRONIC_MONEY_PAY_TYPE)
                );
            } else {
                $this->deletePaymentType(MDL_SMBC_ELECTRONIC_MONEY_BILL_METHOD);
            }
            // ペイジー
            if (isset($arrForm['pay_easy']) && $arrForm['pay_easy'] == '1') {
                $this->updatePaymentTable(
                    array('module_path' => $php_path . 'payeasy.php', 'rule' => 1, 'rule_max' => 1, 'upper_rule' => MDL_SMBC_PAYEASY_UPPER_RULE_MAX, 'memo01' => MDL_SMBC_PAYEASY_BILL_METHOD, "payment_method" => MDL_SMBC_PAYEASY_PAY_TYPE)
                );
            } else {
                $this->deletePaymentType(MDL_SMBC_PAYEASY_BILL_METHOD);
            }
            // 銀行振込
            if (isset($arrForm['bank_transfer']) && $arrForm['bank_transfer'] == '1') {
                $this->updatePaymentTable(
                    array('module_path' => $php_path . 'bank_transfer.php', 'rule' => 1, 'rule_max' => 1, 'upper_rule' => MDL_SMBC_BANK_TRANSFER_UPPER_RULE_MAX, 'memo01' => MDL_SMBC_BANK_TRANSFER_BILL_METHOD, "payment_method" => MDL_SMBC_BANK_TRANSFER_PAY_TYPE)
                );
            } else {
                $this->deletePaymentType(MDL_SMBC_BANK_TRANSFER_BILL_METHOD);
            }
            // 払込票（コンビニ、ゆうちょ等）
            if (isset($arrForm['payment_slip']) && $arrForm['payment_slip'] == '1') {
                $this->updatePaymentTable(
                    array('module_path' => $php_path . 'payment_slip.php', 'rule' => 1, 'rule_max' => 1, 'upper_rule' => MDL_SMBC_PAYMENT_SLIP_UPPER_RULE_MAX, 'memo01' => MDL_SMBC_PAYMENT_SLIP_BILL_METHOD, "payment_method" => MDL_SMBC_PAYMENT_SLIP_PAY_TYPE)
                );
            } else {
                $this->deletePaymentType(MDL_SMBC_PAYMENT_SLIP_BILL_METHOD);
            }
            // コンビニ（番号方式）
            if (isset($arrForm['conveni_number']) && $arrForm['conveni_number'] == '1') {
                $this->updatePaymentTable(
                    array('module_path' => $php_path . 'conveni.php', 'rule' => 1, 'rule_max' => 1, 'upper_rule' => MDL_SMBC_CONVENI_NUMBER_UPPER_RULE_MAX, 'memo01' => MDL_SMBC_CONVENI_NUMBER_BILL_METHOD, "payment_method" => MDL_SMBC_CONVENI_NUMBER_PAY_TYPE)
                );
            } else {
                $this->deletePaymentType(MDL_SMBC_CONVENI_NUMBER_BILL_METHOD);
            }
            // クレジット決済
            if (isset($arrForm['credit']) && $arrForm['credit'] == '1') {
                $this->updatePaymentTable(
                    array('module_path' => $php_path . 'credit.php', 'rule' => 1, 'rule_max' => 1, 'upper_rule' => MDL_SMBC_CREDIT_UPPER_RULE_MAX, 'memo01' => MDL_SMBC_CREDIT_BILL_METHOD, "payment_method" => MDL_SMBC_CREDIT_PAY_TYPE)
                );
            } else {
                $this->deletePaymentType(MDL_SMBC_CREDIT_BILL_METHOD);
            }
            // クレジット決済(継続課金)
            if (isset($arrForm['credit_regular']) && $arrForm['credit_regular'] == '1') {
                $this->updatePaymentTable(
                    array('module_path' => $php_path . 'credit_regular.php', 'rule' => 1, 'rule_max' => 1, 'upper_rule' => MDL_SMBC_REGULAR_UPPER_RULE_MAX, 'memo01' => MDL_SMBC_CREDIT_REGULAR_BILL_METHOD, "payment_method" => MDL_SMBC_CREDIT_REGULAR_PAY_TYPE)
                );
                $regular_product_type_id = $this->lfSetKeizoku($objQuery);
                if (strlen($regular_product_type_id) > 0 ) {
                    $arrForm['regular_product_type_id'] = $regular_product_type_id;
                }
                $arrForm['keizoku_flg'] = '1';
            } else {
                $this->deletePaymentType(MDL_SMBC_CREDIT_REGULAR_BILL_METHOD);
                $product_type_id = $objQuery->get('id', 'mtb_product_type', 'name = ?', array('定期対応購入商品'));
                if (!SC_Utils_Ex::isBlank($product_type_id)) {
                    $objQuery->delete('mtb_product_type', 'id = ?', array($product_type_id));
                    SC_Helper_FileManager_Ex::deleteFile(DATA_REALDIR . 'cache/mtb_product_type.serial', true);
                    $objQuery->update('dtb_deliv', array('del_flg' => '1', 'update_date' => 'CURRENT_TIMESTAMP'),
                                      'product_type_id = ?', array($product_type_id));
                }
                $arrForm['keizoku_flg'] = '0';
            }
            ////////////////////////////////////////////////////////////
            // 入力内容を、dtb_module.sub_dataへ登録                  //
            ////////////////////////////////////////////////////////////
            $objSMBC->registerSubData($arrForm);

            SC_Utils_Ex::clearCompliedTemplate();

            // 2.12系はプラグインのインストールを行う
            if(defined('PLUGIN_UPLOAD_REALDIR') && version_compare(ECCUBE_VERSION, '2.12.0') >= 0) {
                if (! $this->installPlugin() ) {
                    $this->tpl_onload = "alert('プラグインのインストールに失敗しました。');";
                }
            }

            $this->tpl_onload = "alert('登録完了しました。". '\n基本情報＞支払方法設定より詳細設定をしてください。' . "'); window.close();";
        }

        $this->arrForm = $this->objFormParam->getFormParamList();
    }

    /**
     * ファイルをコピーする
     *
     */
    function updateFile($arrUpdateFile) {
        foreach($arrUpdateFile as $array) {
            $dst = $array['dst'];
            $src = $array['src'];
            if (file_exists($dst)) {
                if(sha1_file($src) != sha1_file($dst)) {
                    if(is_writable($dst)) {
                        copy($src, $dst);
                    } else {
                        return false;
                    }
                }
            } else {
                $dir = dirname($dst);
                if (! is_dir($dir) )
                    mkdir($dir, 0777, true);
                if (is_writable($dir)) {
                    copy($src, $dst);
                } else {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * プラグインをインストールする
     *
     */
    function installPlugin() {
        require_once(MDL_SMBC_PLUGIN_PATH . "plugin_info.php");
        $objReflection = new ReflectionClass('plugin_info');
        $arrPluginInfo = LC_Page_Admin_OwnersStore_Ex::getPluginInfo($objReflection);
        $plugin_path = PLUGIN_UPLOAD_REALDIR . $arrPluginInfo['PLUGIN_CODE'];

        // ファイルのアップロード
        $arrTarget = array(
            array(
                'src' => MDL_SMBC_PLUGIN_PATH . "MdlSmbc.php",
                'dst' => $plugin_path .'/MdlSmbc.php',
            ),
            array(
                'src' => MDL_SMBC_PLUGIN_PATH . "smbc_admin_order_edit.tpl",
                'dst' => $plugin_path .'/smbc_admin_order_edit.tpl',
            ),
            array(
                'src' => MDL_SMBC_PLUGIN_PATH . "smbc_admin_customer_edit.tpl",
                'dst' => $plugin_path .'/smbc_admin_customer_edit.tpl',
            ),
            array(
                'src' => MDL_SMBC_PLUGIN_PATH . "smbc_admin_customer_edit_confirm.tpl",
                'dst' => $plugin_path .'/smbc_admin_customer_edit_confirm.tpl',
            ),
            array(
                "src" => MDL_SMBC_PATH . "copy/bankaccount.php",
                "dst" => HTML_REALDIR . ADMIN_DIR . 'system/bankaccount.php'
            ),
            array(
                'src' => MDL_SMBC_PLUGIN_PATH . "systemSubnavi.tpl",
                'dst' => $plugin_path .'/systemSubnavi.tpl',
            ),
            array(
                'src' => MDL_SMBC_PLUGIN_PATH . "logo.png",
                'dst' => PLUGIN_HTML_REALDIR . $arrPluginInfo['PLUGIN_CODE'] . '/logo.png',
            ),
        );
        if (! $this->updateFile($arrTarget) ) {
            GC_Utils_Ex::gfDebugLog('installPlugin failed');
            return false;
        }

        // Pluginテーブル設定
        $objQuery =& SC_Query::getSingletonInstance();
        $table = 'dtb_plugin';
        $count = $objQuery->count($table, 'plugin_code = ?', array($arrPluginInfo['PLUGIN_CODE']));

        if ($count > 0) {
            $arrUpdate = array(
                'enable' => PLUGIN_ENABLE_TRUE,
                'update_date' => 'CURRENT_TIMESTAMP',
            );
            $objQuery->update($table, $arrUpdate, 'plugin_code = ?', array($arrPluginInfo['PLUGIN_CODE']));
        } else {
            $plugin_id = $objQuery->nextVal('dtb_plugin_plugin_id');
            $arrInsert = array(
                'plugin_id' => $plugin_id,
                'plugin_name' => $arrPluginInfo['PLUGIN_NAME'],
                'plugin_code' => $arrPluginInfo['PLUGIN_CODE'],
                'plugin_version' => $arrPluginInfo['PLUGIN_VERSION'],
                'compliant_version' => $arrPluginInfo['COMPLIANT_VERSION'],
                'class_name' => $arrPluginInfo['CLASS_NAME'],
                'author' => $arrPluginInfo['AUTHOR'],
                'author_site_url' => $arrPluginInfo['AUTHOR_SITE_URL'],
                'plugin_site_url' => $arrPluginInfo['PLUGIN_SITE_URL'],
                'plugin_description' => $arrPluginInfo['DESCRIPTION'],
                'priority' => '0',
                'enable' => PLUGIN_ENABLE_TRUE,
                'update_date' => 'CURRENT_TIMESTAMP',
            );
            $objQuery->insert($table, $arrInsert);

            // フックポイント
            foreach ($arrPluginInfo['HOOK_POINTS'] as $h) {
                $arrInsert = array(
                    'plugin_hookpoint_id' => $objQuery->nextVal('dtb_plugin_hookpoint_plugin_hookpoint_id'),
                    'plugin_id' => $plugin_id,
                    'hook_point' => $h[0],
                    'callback' => $h[1],
                    'update_date' => 'CURRENT_TIMESTAMP',
                );
                $objQuery->insert('dtb_plugin_hookpoint', $arrInsert);
            }
        }

        return true;
    }

    /**
     * 定期用商品種別・配送情報をDBへ登録
     * 
     * @param type $objQuery 
     */
    function lfSetKeizoku($objQuery){

        
        $product_type_count = $objQuery->count('mtb_product_type', 'name = ?', array('定期対応購入商品'));
        $regular_product_type_id = null;
        if ($product_type_count <= 0) {
            // mtb_product_typeを登録
            $regular_product_type_id = $objQuery->max('id', 'mtb_product_type') + 1;
            $arrInsertProductType = array(
                "id" => $regular_product_type_id,
                "name" => '定期対応購入商品',
                "rank" => $objQuery->max('rank', 'mtb_product_type') + 1
            );
            $objQuery->insert('mtb_product_type', $arrInsertProductType);
                
            // mtb_product_typeのキャッシュファイルを削除
            SC_Helper_FileManager_Ex::deleteFile(DATA_REALDIR . 'cache/mtb_product_type.serial', true);

            // dtb_delivに配送情報を登録
            $arrInsertDeliv = array(
                "deliv_id" => $objQuery->nextVal('dtb_deliv_deliv_id'),
                "product_type_id" => $regular_product_type_id,
                "name" => "定期購入配送業者",
                "service_name" => "定期購入配送業者",
                "remark" => null,
                "confirm_url" => null,
                "rank" => $objQuery->max('rank', 'dtb_deliv') + 1,
                "status" => "1",
                "del_flg" => "0",
                "creator_id" => "2",
                "create_date" => 'CURRENT_TIMESTAMP',
                "update_date" => 'CURRENT_TIMESTAMP'
            );
            $objQuery->insert('dtb_deliv', $arrInsertDeliv);       
        } else {
            $arrRegularProductTypeId = $objQuery->select('id', 'mtb_product_type', 'name = ?', array('定期対応購入商品'));
            $regular_product_type_id = $arrRegularProductTypeId[0]['id'];
        }

        return $regular_product_type_id;
    }
}
?>
