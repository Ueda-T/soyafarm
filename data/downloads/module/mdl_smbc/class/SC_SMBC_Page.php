<?php
require_once(MDL_SMBC_CLASS_PATH . 'SC_SMBC.php');
require_once(MDL_SMBC_CLASS_PATH . 'SC_SMBC_RegularCSV.php');

class SC_SMBC_Page extends SC_SMBC {

    // コンストラクタ
    function SC_SMBC_Page() {
        parent::SC_SMBC();
        $this->init();
    }

    // 初期化
    function init() {
        $this->initArrParam();
    }

    function initArrParam() {
        parent::initArrParam();
        $this->addArrParam("version", 3);
        $this->addArrParam("shop_link", 256);
        $this->addArrParam("shop_res_link", 256);
        $this->addArrParam("shop_error_link", 256);
        $this->addArrParam("hakkou_kbn", 1);
        $this->addArrParam("yuusousaki_kbn", 1);
        $this->addArrParam("riyou_nengetsu", 6);
        $this->addArrParam("seikyuu_nengetsu", 6);

    }

    /**
     * 送信用配列に格納されている情報に合わせて、サイズ調整を行う
     */
    function convParamStr () {
        $arrParam =  parent::convParamStr();
        mb_convert_variables('UTF-8', 'auto', $arrParam);

        // 送信データを全角カタカナで送る必要がある項目があるが
        // モバイルの場合SC_Helper_Mobileでob_startを使って、
        // カタカナを半角にする処理が入っているため、そのまま送るとエラーになってしまう。
        // そのため、ob_end_flush()つかって一旦バッファを無くし、再度必要なものだけ設定する
        if (SC_MobileUserAgent::isMobile() == true) {
            while(ob_get_level()) {
                 ob_end_flush();
            }
            mb_http_output('SJIS-win');
            ob_start(array('SC_MobileEmoji', 'handler'));
            ob_start('mb_output_handler');
        }

        return $arrParam;
    }

    /**
     * 連携データを設定する
     *
     * @param unknown_type $order_id
     */
    function makeParam ($order_id) {
        $arrParam = array();

        // 全決済共通の連携データを設定
        $arrParam = parent::makeParam($order_id);

        if (strlen($arrParam['hakkou_kbn']) <= 0) $arrParam['hakkou_kbn'] = "";
        if (strlen($arrParam['yuusousaki_kbn']) <= 0) $arrParam['yuusousaki_kbn'] = "";

        if(SC_MobileUserAgent::isMobile()){//MB
            // バージョンを設定
            $arrParam['version'] = MDL_SMBC_PAGE_LINK_MOBILE_VERSION;
            // 遷移先URLを設定
            $arrParam['shop_link'] = HTTPS_URL . "smbc/complete.php?PHPSESSID=".$_GET['PHPSESSID'];
            // エラー時遷移先URL
            $arrParam['shop_error_link'] = HTTPS_URL . 'smbc/shop_error.php';

        }elseif(SC_SmartphoneUserAgent::isSmartphone()){//SP
            $arrParam['version'] = MDL_SMBC_PAGE_LINK_PC_VERSION;
            $arrParam['shop_link'] = HTTPS_URL . "smbc/complete.php";
            $arrParam['shop_error_link'] = HTTPS_URL . 'smbc/shop_error.php';

        }else{//PC
            $arrParam['version'] = MDL_SMBC_PAGE_LINK_PC_VERSION;
            $arrParam['shop_link'] = HTTPS_URL . "smbc/complete.php";
            $arrParam['shop_error_link'] = HTTPS_URL . 'smbc/shop_error.php';

        }

        // 結果通知URL
        $arrParam['shop_res_link'] = HTTPS_URL . "smbc/order_recv.php";

        // 利用年月
        $arrParam['riyou_nengetsu'] = date('Ym');

        // 請求年月
        $arrParam['seikyuu_nengetsu'] = date('Ym');

        return $arrParam;
    }
    
    /**
     * 連携データを設定する
     *
     * @param unknown_type $order_id
     */
    function regularMakeParam ($order_id) {
        $arrParam = array();

        
        if(SC_MobileUserAgent::isMobile()){//MB
            // バージョンを設定
            $arrParam['version'] = MDL_SMBC_REGULAR_PAGE_LINK_MOBILE_VERSION;
            // 遷移先URLを設定
            $arrParam['shop_link'] = HTTPS_URL . "smbc/regular_complete.php?PHPSESSID=".$_GET['PHPSESSID'];
            // エラー時遷移先URL
            $arrParam['shop_error_link'] = HTTPS_URL . 'smbc/shop_error.php';
        }elseif(SC_SmartphoneUserAgent::isSmartphone()){//SP
            $arrParam['version'] = MDL_SMBC_REGULAR_PAGE_LINK_PC_VERSION;
            $arrParam['shop_link'] = HTTPS_URL . "smbc/regular_complete.php";
            $arrParam['shop_error_link'] = HTTPS_URL . 'smbc/shop_error.php';

        }else{//PC
            $arrParam['version'] = MDL_SMBC_REGULAR_PAGE_LINK_PC_VERSION;
            $arrParam['shop_link'] = HTTPS_URL . "smbc/regular_complete.php";
            $arrParam['shop_error_link'] = HTTPS_URL . 'smbc/shop_error.php';
        }
        
        // 決済手段区分
        $arrParam['bill_method'] = MDL_SMBC_CREDIT_BILL_METHOD;

        // 決済種類コード
        $arrParam['kessai_id'] = MDL_SMBC_CREDIT_KESSAI_ID;
        
        // dtb_orderの情報を取得
        $arrOrderTemp = $this->getOrderTemp($order_id);
        
        // モジュールマスタの連携データを取得
        $arrModuleMaster = SC_SMBC::getModuleMasterData($arrOrderTemp['payment_id']);
        
        // 契約コード
        $arrParam['connect_url'] = $arrModuleMaster['connect_url'];

        $objMdlSMBC = SC_Mdl_SMBC::getInstance();
        $subData = $objMdlSMBC->getSubData();

        // 契約コード
        $arrParam['shop_cd'] = $subData['regular_shop_cd'];
        
        // 収納企業コード
        $arrParam['syuno_co_cd'] = $subData['regular_syuno_co_cd'];
        
        // ショップパスワード
        $arrParam['shop_pwd'] = $subData['regular_shop_pwd'];

        // 上書可否区分
        $arrParam['koushin_kbn'] = 1;
        
        // 顧客情報を取得
        $arrCustomer = SC_SMBC::getCustomerData($arrOrderTemp);
        
        // 顧客番号
        $arrParam['bill_no'] = $arrCustomer['bill_no'];
        
        // 顧客名
        $arrParam['bill_name'] = $arrCustomer['bill_name'];
        
        // 請求開始年月
        $kaishi_ym = date('Ym', mktime(0, 0, 0, date('m') + 1, 1, date('Y')));
/*
        // 27日以降は翌月
        if (date('j') >= 27) {
            $kaishi_ym = date('Ym', mktime(0, 0, 0, date('m') + 1, 1, date('Y')));
        }
        // その他は当月が対象
        else {
            $kaishi_ym = date('Ym');
        }
*/
        $arrParam['seikyuu_kaishi_ym'] = $kaishi_ym;
        
        // 請求終了年月
        $arrParam['seikyuu_shuryo_ym'] = '999912';
        
        // 請求方法
        $arrParam['seikyuu_hoho'] = '1';
        
        // ショップ連絡先電話番号表示区分
        $arrParam['shop_phon_hyoji_kbn'] = '0';
        
        // ショップ連絡先メールアドレス表示区分
        $arrParam['shop_mail_hyoji_kbn'] = '0';      
               
        //　請求金額（初回）
        //$arrParam['seikyuu_kingaku1'] = $arrOrderTemp['payment_total'];
        
        //　請求金額（2 回目以降）
        //$arrParam['seikyuu_kingaku2'] = $arrOrderTemp['payment_total'];
        
        //　請求金額（変額）
        $arrParam['seikyuu_kin_hengaku'] = '0';

        return $arrParam;
    }

    function getArrParam () {
        return $this->arrParam;
    }
}
?>
