<?php
/*
 * LC_Page_SBIVT3G_Config.php - LC_Page_SBIVT3G_Configクラスを定義
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: LC_Page_SBIVT3G_Config.php 193 2013-07-31 01:24:57Z kaji $
 * @link        http://www.veritrans.co.jp/3gps
 */

require_once(CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php');
/**
 *
 * 当該モジュール 店舗設定画面クラス.
 *
 * @category  Veritrans
 * @package   Lib
 * @copyright 2011 SBI VeriTrans Co., Ltd.
 * @license   http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version   Release: @package_version@
 * @link      http://www.veritrans.co.jp/3gps
 * @access    public
 * @author    
 */
class LC_Page_SBIVT3G_Config extends LC_Page_Admin_Ex {

    /**
     * Page を初期化する.
     *
     * @access public
     * @return void
     */
    function init() {
        parent::init();

        // テンプレートサブタイトル
        $this->tpl_subtitle = MDL_SBIVT3G_MODULE_NAME;

        // バージョン確認
        $target_version = "2.11.0";
        if (GC_Utils_SBIVT3G::compareVersion($target_version) < 0) {
            // 2.11.0未満は設定を行わせない
            SC_Utils_Ex::sfDispException();
        }
    }

    /**
     * Page のプロセス.
     * 
     * @access public
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @access public
     * @return void
     */
    function action() {
        $this->tpl_mainpage = MDL_SBIVT3G_TPL_PATH . 'admin/admin_config.tpl';

        // 店舗別設定ヘルパー
        $this->objSetting =& SC_Helper_SBIVT3G_Setting::getSingletonInstance();
        // インストールヘルパー
        $this->objInstall =& SC_Helper_SBIVT3G_Install::getSingletonInstance();

        // 商品タイプ
        $this->arrCarrierItemTypes = $this->objSetting->getCarrierItemTypes();

        // 売上フラグ
        $this->arrCardCaptures = $this->objSetting->getCardCaptures();
        $this->arrPaypalCaptures = $this->objSetting->getPaypalCaptures();
        $this->arrCarrierCaptures = $this->objSetting->getCarrierCaptures();

        // 本人認証タイプ
        $this->arrMpiOption = $this->objSetting->getMpiOptionString();

        // 本人認証(3Dセキュア)
        $this->arrCarrier3D = $this->objSetting->getCarrier3D();

        // クレジットカードの種類
        $this->arrCredit = $this->objSetting->getSettingCardBrand();

        // 設定用クレジットカード支払回数
        $this->arrSettingCount = $this->objSetting->getSettingCount();

        // インストールファイル名を取得
        $this->installFiles = $this->objInstall->getInstallFiles();

        // パラメータ管理クラス
        $objFormParam = new LC_SBIVT3G_FormParam();
        $this->initParam($objFormParam);

        switch($this->getMode()) {
        case 'exec':
            // POST値取得
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();

            // 入力チェック
            if ($arrErr = $this->checkError($objFormParam)) {
                $this->arrErr  = $arrErr;
                $this->objInstall->printLog("Config Error: arrErr="
                    . print_r($this->arrErr,true));
                break;
            }
            // 登録処理
            $this->execAction($objFormParam);
            break;
        default:
            // 初期表示
            $subData = $this->objSetting->getSetting();
            $subData = $this->setDefault($subData);
            $objFormParam->setParam($subData);
            $objFormParam->convParam();
            break;
        }
        $this->arrForm = $objFormParam->getFormParamList();
        $this->setTemplate($this->tpl_mainpage);
    }

    /**
     * 登録処理
     *
     * @access public
     * @param  object $objForm
     * @return void
     */
    function execAction(&$objForm) {
        
        $objInstall =& $this->objInstall;

        $arrForm = $objForm->getHashArray();
         
        // 設定を保存(sub_dataを登録)
        $this->objSetting->saveSetting($arrForm);

        // MDKライブラリファイルの補完リストを取得
        $arrMdkFiles = $objInstall->getCompletionFiles();
        foreach ($arrMdkFiles as $files) {
            // 無ければ補完する
            if ($objInstall->setCompletionProperties($error,
                $files['dst'],
                MDL_SBIVT3G_EXTRA . $files['src']) == false) {

                $this->arrErr['err'] .= $error;
            }

            // エラーがあれば終了
            if (strcmp($this->arrErr['err'], '') != 0) {
                return;
            }
        }

        // 3GPSMDK.properties の書き込み権限を調べる
        if ($objInstall->checkProperties($error,
            MDL_SBIVT3G_PROPERTIES_PATH) == false) {

            $this->arrErr['err'] .= $error;
            return;
        }

        // log4php.properties の書き込み権限を調べる
        if ($objInstall->checkProperties($error,
            MDL_SBIVT3G_LOG_PROPERTIES_PATH) == false) {

            $this->arrErr['err'] .= $error;
            return;
        }

        // 3GPSMDK.propertiesの更新
        $objInstall->setMdkProperties($arrForm);
        // log4php.propertiesの更新
        $objInstall->setLogProperties();
        
        // 上書きしないを選択していなければファイル上書き
        if (isset($arrForm['doNotOverride']) == false
        || strcmp($arrForm['doNotOverride'], '1') != 0) {

            // インストール対象ファイル一覧取得
            $arrFiles = $objInstall->getInstallFiles();

            // バックアップディレクトリの作成
            $backupDir = MDL_SBIVT3G_BACKUP_RESERVE_PATH;
            if (!is_writable($backupDir) && !mkdir($backupDir, 0777, true)) {
                $this->arrErr['err'] = 'バックアップディレクトリ '
                    . $backupDir . ' の作成に失敗しました' . LF;
                return;
            }
            $backupDir = realpath($backupDir);

            // バックアップ
            $bol = $objInstall->doBackUp($backupDir, $arrFiles, $arrFailed);
            if ($bol == false) {
                if (count($arrFailed) > 0) {
                    foreach($arrFailed as $file) {
                        $this->arrErr['err'] =
                            $file . 'のバックアップ作成に失敗しました' . LF;
                    }
                } else {
                    $this->arrErr['err'] =
                        'ファイルのバックアップ作成に失敗しました' . LF;
                }
                return;
            }
            $objInstall->printLog("バックアップを作成しました $backupDir");

            $arrFailed= $objInstall->doInstall($arrFiles);
            if (count($arrFailed) > 0) {
                foreach($arrFailed as $file) {
                    $this->arrErr['err'] = realpath($file)
                        . 'のファイルを書き込み可能に変更してください' . LF; 
                }
                return;
            }
        }
             
        // 支払い方法テーブルを更新する
        // クレジット決済
        if(isset($arrForm["C_validFlg"]) && $arrForm["C_validFlg"] == "1"){
            $objInstall->updatePaymentTable(MDL_SBIVT3G_INNER_ID_CREDIT);
        } else {
            $objInstall->deletePaymentTable(MDL_SBIVT3G_INNER_ID_CREDIT);
        }

        // コンビニ決済
        if ((isset($arrForm['V_sejFlg']) && $arrForm['V_sejFlg'] == '1') ||
            (isset($arrForm['V_lawsonFlg']) && $arrForm['V_lawsonFlg'] == '1') ||
            (isset($arrForm['V_famimaFlg']) && $arrForm['V_famimaFlg'] == '1') ||
            (isset($arrForm['V_econFlg']) && $arrForm['V_econFlg'] == '1') ||
            (isset($arrForm['V_otherFlg']) && $arrForm['V_otherFlg'] == '1')) {
            $objInstall->updatePaymentTable(MDL_SBIVT3G_INNER_ID_CVS);
        } else {
            $objInstall->deletePaymentTable(MDL_SBIVT3G_INNER_ID_CVS);
        }

        // 銀行決済(Pay-easy ATM)
        if(isset($arrForm["B_atmFlg"]) && $arrForm["B_atmFlg"] == "1"){
            $objInstall->updatePaymentTable(MDL_SBIVT3G_INNER_ID_PAYEASY_ATM);
        } else {
            $objInstall->deletePaymentTable(MDL_SBIVT3G_INNER_ID_PAYEASY_ATM);
        }
        
        // 銀行決済(Pay-easy ネットバンキング)
        if(isset($arrForm["B_netFlg"]) && $arrForm["B_netFlg"] == "1"){
            $objInstall->updatePaymentTable(MDL_SBIVT3G_INNER_ID_PAYEASY_NET);
        } else {
            $objInstall->deletePaymentTable(MDL_SBIVT3G_INNER_ID_PAYEASY_NET);
        }

        // モバイルEdy
        if(isset($arrForm["EE_mobFlg"]) && $arrForm["EE_mobFlg"] == "1"){
            $objInstall->updatePaymentTable(MDL_SBIVT3G_INNER_ID_EDY_MOBILE_MAIL);
        } else {
            $objInstall->deletePaymentTable(MDL_SBIVT3G_INNER_ID_EDY_MOBILE_MAIL);
        }

        // サイバーEdy
        if(isset($arrForm["EE_pcFlg"]) && $arrForm["EE_pcFlg"] == "1"){
            $objInstall->updatePaymentTable(MDL_SBIVT3G_INNER_ID_EDY_PC_APP);
        } else {
            $objInstall->deletePaymentTable(MDL_SBIVT3G_INNER_ID_EDY_PC_APP);
        }

        // モバイルSuica(メール型)
        if(isset($arrForm["ES_mobMailFlg"]) && $arrForm["ES_mobMailFlg"] == "1"){
            $objInstall->updatePaymentTable(MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_MAIL);
        } else {
            $objInstall->deletePaymentTable(MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_MAIL);
        }

        // モバイルSuica(アプリ型)
        if(isset($arrForm["ES_mobAppFlg"]) && $arrForm["ES_mobAppFlg"] == "1"){
            $objInstall->updatePaymentTable(MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_APP);
        } else {
            $objInstall->deletePaymentTable(MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_APP);
        }

        // Suicaインターネットサービス(メール型)
        if(isset($arrForm["ES_pcMailFlg"]) && $arrForm["ES_pcMailFlg"] == "1"){
            $objInstall->updatePaymentTable(MDL_SBIVT3G_INNER_ID_SUICA_PC_MAIL);
        } else {
            $objInstall->deletePaymentTable(MDL_SBIVT3G_INNER_ID_SUICA_PC_MAIL);
        }

        // Suicaインターネットサービス(アプリ型)
        if(isset($arrForm["ES_pcAppFlg"]) && $arrForm["ES_pcAppFlg"] == "1"){
            $objInstall->updatePaymentTable(MDL_SBIVT3G_INNER_ID_SUICA_PC_APP);
        } else {
            $objInstall->deletePaymentTable(MDL_SBIVT3G_INNER_ID_SUICA_PC_APP);
        }

        // Waon(モバイル版)
        if(isset($arrForm["EW_mobFlg"]) && $arrForm["EW_mobFlg"] == "1"){
            $objInstall->updatePaymentTable(MDL_SBIVT3G_INNER_ID_WAON_MOBILE_APP);
        } else {
            $objInstall->deletePaymentTable(MDL_SBIVT3G_INNER_ID_WAON_MOBILE_APP);
        }

        // Waon(PC版)
        if(isset($arrForm["EW_pcFlg"]) && $arrForm["EW_pcFlg"] == "1"){
            $objInstall->updatePaymentTable(MDL_SBIVT3G_INNER_ID_WAON_PC_APP);
        } else {
            $objInstall->deletePaymentTable(MDL_SBIVT3G_INNER_ID_WAON_PC_APP);
        }
        
        // 銀聯ネット決済
        // 2013.07.18 廃止のため、常に削除を実施する
        $objInstall->deletePaymentTable(MDL_SBIVT3G_INNER_ID_CUP);

        // PayPal決済
        if(isset($arrForm["P_validFlg"]) && $arrForm["P_validFlg"] == "1"){
            $objInstall->updatePaymentTable(MDL_SBIVT3G_INNER_ID_PAYPAL);
        } else {
            $objInstall->deletePaymentTable(MDL_SBIVT3G_INNER_ID_PAYPAL);
        }

        // キャリア決済
        if ((isset($arrForm["CA_docomoFlg"]) &&
             $arrForm["CA_docomoFlg"] == "1") ||
            (isset($arrForm["CA_auFlg"]) &&
             $arrForm["CA_auFlg"] == "1") ||
            (isset($arrForm["CA_sb_ktaiFlg"]) &&
             $arrForm["CA_sb_ktaiFlg"] == "1") ||
            (isset($arrForm["CA_sb_matometeFlg"]) &&
             $arrForm["CA_sb_matometeFlg"] == "1") ||
            (isset($arrForm["CA_s_bikkuriFlg"]) &&
             $arrForm["CA_s_bikkuriFlg"] == "1")) {
            $objInstall->updatePaymentTable(MDL_SBIVT3G_INNER_ID_CARRIER);
        } else {
            $objInstall->deletePaymentTable(MDL_SBIVT3G_INNER_ID_CARRIER);
        }

        // 自動配信プログラム
        if (MDL_SBIVT3G_AUTOMAIL_ENABLED == true) {
            $objMasterData = new SC_DB_MasterData_Ex();

            // 期限前メール
            if (@$arrForm["noticeMailFlg"] == "1") { // 設定
                $objInstall->setMtbMailTemplate($objMasterData,
                    MDL_SBIVT3G_MAIL_TPL_TITLE_PAY_NOTICE,
                    MDL_SBIVT3G_MAIL_TPL_FILE_PAY_NOTICE,
                    6
                );
            } else { // 解除
                $objInstall->setMtbMailTemplate($objMasterData,
                    MDL_SBIVT3G_MAIL_TPL_TITLE_PAY_NOTICE,
                    MDL_SBIVT3G_MAIL_TPL_FILE_PAY_NOTICE,
                    6,
                    false
                );
            }
            // 期限切れメール
            if (@$arrForm["expireMailFlg"] == "1") { // 設定
                $objInstall->setMtbMailTemplate($objMasterData,
                    MDL_SBIVT3G_MAIL_TPL_TITLE_PAY_EXPIRE,
                    MDL_SBIVT3G_MAIL_TPL_FILE_PAY_EXPIRE,
                    7
                );
            } else { // 解除
                $objInstall->setMtbMailTemplate($objMasterData,
                    MDL_SBIVT3G_MAIL_TPL_TITLE_PAY_EXPIRE,
                    MDL_SBIVT3G_MAIL_TPL_FILE_PAY_EXPIRE,
                    7,
                    false
                );
            }
        }

        $this->tpl_onload = "window.alert('設定を保存しました。各支払方法は、"
            . '\n基本情報管理＞配送設定'
            . '\n基本情報管理＞支払方法設定'
            . '\nで支払方法の設定をすることで有効になります。'
            . "'); window.close();";
    }

    /**
     * 設定値のデフォルト設定
     *
     * @access public
     * @param array $arrData
     * @return array
     */
    function setDefault($arrData) {

        // デフォルト値を取得
        $objDb = new SC_Helper_DB_Ex();
        $arrInfo = $objDb->sfGetBasisData();
        // dtb_baseinfo.shop_name
        $shopName = $arrInfo['shop_name'];
        // dtb_baseinfo.shop_kana
        $shopKana = $arrInfo['shop_kana'];
        // dtb_baseinfo.email01
        $email01 = $arrInfo['email01'];

        // デフォルト値を設定
        // クレジット決済
        if(empty($arrData['C_validFlg'])) {
            // 売上フラグ
            $arrData['C_captureFlg'] = '1';
        }
        if(empty($arrData['C_settingCount'])) {
            $arrData['C_settingCount'] = array();
        }
        
        // コンビニ決済
        if (empty($arrData['V_sejFlg']) &&
            empty($arrData['V_lawsonFlg']) &&
            empty($arrData['V_famimaFlg']) &&
            empty($arrData['V_econFlg']) &&
            empty($arrData['V_otherFlg'])) {
            // 店舗名
            $arrData['V_shopName'] = 
                mb_substr(mb_convert_kana($shopName, 'AKS', 'UTF-8'), 0, V_SHOPNAME_LEN / 2);
            // 決済期限日数 
            $arrData['V_limitDays'] = DEFAULT_PAYMENT_TERM_DAY; 
        }

        // Edy決済
        if(empty($arrData['EE_mobFlg']) && empty($arrData['EE_pcFlg'])) {
        
            // 決済期限日数 
            $arrData['EE_limitDays'] = DEFAULT_PAYMENT_TERM_DAY; 
            // 店舗名
            $arrData['EE_shopName'] =
                mb_substr(mb_convert_kana($shopName, 'AKS', 'UTF-8'), 0, EE_SHOPNAME_LEN / 2);
            // 依頼・返金メール要否
            $arrData['EE_bccMailFlg'] = "1";
            // 依頼・返金メールBCCアドレス 
            $arrData['EE_bccMailAddr'] =
                mb_substr(mb_convert_kana($email01, 'KS', 'UTF-8'), 0, EE_BCC_MAIL_ADDR_LEN);
        }

        // Suica決済
        if(empty($arrData['ES_mobMailFlg']) && empty($arrData['ES_mobAppFlg']) &&
            empty($arrData['ES_pcMailFlg']) && empty($arrData['ES_pcAppFlg'])) {
                
            // 決済期限日数 
            $arrData['ES_limitDays'] = DEFAULT_PAYMENT_TERM_DAY;
            // 店舗名
            $arrData['ES_shopName'] = DEFAULT_ES_SHOPNAME;
            // 依頼・返金メール要否
            $arrData['ES_bccMailFlg'] = "1";
            // 依頼・返金メールBCCアドレス 
            $arrData['ES_bccMailAddr'] =
                mb_substr(mb_convert_kana($email01, 'KS', 'UTF-8'), 0, ES_BCC_MAIL_ADDR_LEN);
        }
        // Waon決済
        if(empty($arrData['EW_mobFlg']) && empty($arrData['EW_pcFlg'])) {
            // 決済期限日数 
            $arrData['EW_limitDays'] = DEFAULT_PAYMENT_TERM_DAY;
            // 返金期限日数 
            $arrData['EW_limitCancel'] = DEFAULT_PAYMENT_TERM_DAY;
        }
        // Pay-easy決済
        if(empty($arrData['B_atmFlg']) && empty($arrData['B_netFlg'])) {
            // 決済期限日数 
            $arrData['B_limitDays'] = DEFAULT_PAYMENT_TERM_DAY;
            // 請求内容
            $arrData['B_note'] =
                mb_substr(mb_convert_kana($shopName, 'AKS', 'UTF-8'), 0, B_NOTE_LEN / 2);
            // 請求内容カナ
            $arrData['B_noteKana'] =
                mb_substr(mb_convert_kana($shopKana, 'AKS', 'UTF-8'), 0, B_NOTE_KANA_LEN / 2);
        }
        // PayPal決済
        if(empty($arrData['P_validFlg'])) {
            // 売上フラグ
            $arrData['P_captureFlg'] = '1';
        }
        // キャリア決済
        if (empty($arrData['CA_docomoFlg']) &&
            empty($arrData['CA_auFlg']) &&
            empty($arrData['CA_sb_ktaiFlg']) &&
            empty($arrData['CA_sb_matometeFlg']) &&
            empty($arrData['CA_s_bikkuriFlg'])) {
            // 本人認証(3Dセキュア)
            $arrData['CA_3DFlg'] = MDL_SBIVT3G_CARRIER_D3FLAG_BYPASS;
            // 商品タイプ
            $arrData['CA_itemType'] = MDL_SBIVT3G_CARRIER_ITEMTYPE_DIGITAL;
            // 売上フラグ
            $arrData['CA_captureFlg'] = MDL_SBIVT3G_CARRIER_WC_CAPTURE;
        }
        return $arrData;
    }

    /**
     * フォームパラメータ初期化
     *
     * @access public
     * @param SC_FormParam $objForm
     * @return SC_FormParam
     */
    function initParam(&$objForm) {

        // 全共通設定
        $objForm->addParam('マーチャントCCID',
            'merchantCcId',
            MERCHANT_CCID_LEN, 'a', 
            array('EXIST_CHECK','SPTAB_CHECK','NO_SPTAB',
            'ALNUM_CHECK','MAX_LENGTH_CHECK')
        );
        $objForm->addParam('マーチャントパスワード',
            'merchantPass', MERCHANT_PASS_LEN, '', 
            array('EXIST_CHECK' ,'SPTAB_CHECK','NO_SPTAB','ALNUM_CHECK',
            'MAX_LENGTH_CHECK')
        );
        $objForm->addParam('ダミーモード',
            'dummyModeFlg', 1, 'a', array('NUM_CHECK', 'MAX_LENGTH_CHECK')
        );
        $objForm->addParam('取引IDプレフィックス',
            'dummyModePrefix', DUMMY_MODE_PREFIX_LEN, 'an',
            array('ALNUM_PLUS_CHECK', 'MAX_LENGTH_CHECK')
        );
        $objForm->addParam('ファイル自動上書き選択',
            'doNotOverride',1,'a', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objForm->addParam('お支払い期限前メール配信',
            'noticeMailFlg',1,'a', array('NUM_CHECK', 'MAX_LENGTH_CHECK')
        );
        $objForm->addParam('お支払い期限切れメール配信',
            'expireMailFlg',1,'a', array('NUM_CHECK', 'MAX_LENGTH_CHECK')
        );
        $objForm->addParam('決済期限日数',
            'noticeDays',1,'na', array('NUM_CHECK','MAX_LENGTH_CHECK')
        );

        // クレジットカード決済
        $objForm->addParam("クレジットカード決済有効フラグ",
            "C_validFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam("売上フラグ",
            "C_captureFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam("VISA",
            "C_visaFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam("MasterCard",
            "C_masterFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam("JCB",
            "C_jcbFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam("Diners Club",
            "C_dinersFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam("AMEX",
            "C_amexFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam("支払回数",
            "C_settingCount", 3, "an"
        );
        $objForm->addParam("セキュリティコード認証",
            "C_securityFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam("本人認証(3Dセキュア)",
            "C_mpiFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam("本人認証タイプ",
            "C_mpiOption", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam("再取引機能",
            "C_reTradeFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );

        // コンビニ決済
        $objForm->addParam("セブンイレブンの利用",
            "V_sejFlg", 1, "a",
            array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam("ローソン・ミニストップ・セイコーマートの利用",
            "V_lawsonFlg", 1, "a",
            array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam("ファミリーマートの利用",
            "V_famimaFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam(
            "ローソン・ファミリーマート・ミニストップ・セイコーマートの利用",
            "V_econFlg", 1, "a",
            array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam("サークルKサンクス・デイリーヤマザキの利用",
            "V_otherFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam('決済期限日数',
            'V_limitDays', 2, 'a', 
            array('NUM_CHECK','MAX_LENGTH_CHECK')
        );
        $objForm->addParam('店舗名',
            'V_shopName', V_SHOPNAME_LEN, 'K',
            array('SPTAB_CHECK','MAX_BYTE_LENGTH_CHECK','HANKAKU_KANA_CHECK')
        );
        $objForm->addParam('備考',
            'V_note', V_NOTE_LEN, 'K',
            array('SPTAB_CHECK','MAX_BYTE_LENGTH_CHECK','HANKAKU_KANA_CHECK')
        );

        // 電子マネー決済(Edy)
        $objForm->addParam(PAYMENT_NAME_EDY_MOBILE . "有効フラグ",
            "EE_mobFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam(PAYMENT_NAME_EDY_CYBER . "有効フラグ",
            "EE_pcFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam('決済期限日数',
            'EE_limitDays', 2, '', array('MAX_LENGTH_CHECK','NUM_CHECK')
        );
        $objForm->addParam('店舗名',
            'EE_shopName', EE_SHOPNAME_LEN, 'K',
            array('SPTAB_CHECK','MAX_BYTE_LENGTH_CHECK','HANKAKU_KANA_CHECK')
        );
        $objForm->addParam("依頼メールBCC要否",
            "EE_bccMailFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam('依頼メールBCCアドレス',
            'EE_bccMailAddr', EE_BCC_MAIL_ADDR_LEN, '', 
            array("MAX_LENGTH_CHECK",'EMAIL_CHECK', 'EMAIL_CHAR_CHECK')
        );
        $objForm->addParam('依頼メール付加情報',
            'EE_reqMailInfo', EE_REQ_MAIL_INFO_LEN, 'K', 
            array('SPTAB_CHECK','MAX_BYTE_LENGTH_CHECK','HANKAKU_KANA_CHECK')
        );
        $objForm->addParam('完了メール付加情報',
            'EE_cmpMailInfo', EE_CMP_MAIL_INFO_LEN, 'K', 
            array('SPTAB_CHECK','MAX_BYTE_LENGTH_CHECK','HANKAKU_KANA_CHECK')
        );
        
        // 電子マネー決済(Suica)
        $objForm->addParam(PAYMENT_NAME_SUICA_MOBILE_MAIL . "有効フラグ",
            "ES_mobMailFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam(PAYMENT_NAME_SUICA_MOBILE_APP . "有効フラグ",
            "ES_mobAppFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam(PAYMENT_NAME_SUICA_PC_MAIL . "有効フラグ",
            "ES_pcMailFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam(PAYMENT_NAME_SUICA_PC_APP . "有効フラグ",
            "ES_pcAppFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam('決済期限日数',
            'ES_limitDays', 3, '', array('NUM_CHECK','MAX_LENGTH_CHECK')
        );
        $objForm->addParam('表示商品・サービス名',
            'ES_shopName', ES_SHOPNAME_LEN, 'K', 
            array('SPTAB_CHECK','MAX_BYTE_LENGTH_CHECK','HANKAKU_KANA_CHECK')
        );
        $objForm->addParam("依頼・返金メールBCC要否",
            "ES_bccMailFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam('依頼・返金メールBCCアドレス',
            'ES_bccMailAddr', ES_BCC_MAIL_ADDR_LEN, '', 
            array("MAX_LENGTH_CHECK",'EMAIL_CHECK', 'EMAIL_CHAR_CHECK')
        );
        $objForm->addParam('依頼メール付加情報',
            'ES_reqMailInfo', ES_REQ_MAIL_INFO_LEN, 'K', 
            array('SPTAB_CHECK','MAX_BYTE_LENGTH_CHECK','HANKAKU_KANA_CHECK')
        );
        $objForm->addParam('完了メール付加情報',
            'ES_cmpMailInfo', ES_CMP_MAIL_INFO_LEN, 'K', 
            array('SPTAB_CHECK','MAX_BYTE_LENGTH_CHECK','HANKAKU_KANA_CHECK')
        );
        $objForm->addParam('内容確認付加情報',
            'ES_cnfDispInfo', ES_CNF_DISP_INFO_LEN, 'K', 
            array('SPTAB_CHECK','MAX_BYTE_LENGTH_CHECK','HANKAKU_KANA_CHECK')
        );
        $objForm->addParam('完了画面付加情報',
            'ES_cmpDispInfo', ES_CMP_DISP_INFO_LEN, 'K', 
            array('SPTAB_CHECK','MAX_BYTE_LENGTH_CHECK','HANKAKU_KANA_CHECK')
        );
        
        // 電子マネー決済(Waon)
        $objForm->addParam(PAYMENT_NAME_WAON_MOBILE . "有効フラグ",
            "EW_mobFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam(PAYMENT_NAME_WAON_PC . "有効フラグ",
            "EW_pcFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam('決済期限日数',
            'EW_limitDays', 3, 'a', array('NUM_CHECK','MAX_LENGTH_CHECK')
        );
        $objForm->addParam('返金期限日数',
            'EW_limitCancel', 3, 'a', array('NUM_CHECK','MAX_LENGTH_CHECK')
        );
        
        // Pay-easy決済
        $objForm->addParam(PAYMENT_NAME_ATM . "有効フラグ",
            "B_atmFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam(PAYMENT_NAME_NETBANK . "有効フラグ",
            "B_netFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam('決済期限日数',
            'B_limitDays', 2, '', array('NUM_CHECK', 'MAX_LENGTH_CHECK')
        );
        $objForm->addParam('請求内容',
            'B_note', B_NOTE_LEN, 'K', 
            array('SPTAB_CHECK','MAX_BYTE_LENGTH_CHECK','HANKAKU_KANA_CHECK')
        );
        $objForm->addParam('請求内容カナ',
            'B_noteKana', B_NOTE_KANA_LEN, 'KHVC', 
            array('SPTAB_CHECK','MAX_BYTE_LENGTH_CHECK','HANKAKU_KANA_CHECK')
        );

        // PayPal決済
        $objForm->addParam("有効フラグ",
            "P_validFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam("売上フラグ",
            "P_captureFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam('オーダー説明',
            'P_note', P_NOTE_LEN, 'K', 
            array('SPTAB_CHECK','MAX_BYTE_LENGTH_CHECK','HANKAKU_KANA_CHECK')
        );

        // キャリア決済
        $objForm->addParam("ドコモケータイ払いの利用",
            "CA_docomoFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam("PCでドコモケータイ払いを許可",
            "CA_docomoPcFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam("au かんたん決済の利用",
            "CA_auFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam("ソフトバンクまとめて支払い（B）の利用",
            "CA_sb_ktaiFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam("本人認証(3Dセキュア)",
            "CA_3DFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam("ソフトバンクまとめて支払い（A）の利用",
            "CA_sb_matometeFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam("S!まとめて支払いの利用",
            "CA_s_bikkuriFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam("商品タイプ",
            "CA_itemType", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam("売上フラグ",
            "CA_captureFlg", 1, "a", array("NUM_CHECK", "MAX_LENGTH_CHECK")
        );
        $objForm->addParam('商品情報',
            'CA_itemInfo', CA_ITEM_INFO_LEN, 'K', 
            array('SPTAB_CHECK','MAX_BYTE_LENGTH_CHECK','HANKAKU_KANA_CHECK')
        );

        return $objForm;
    }

    /**
     * 入力パラメータの検証
     *
     * @access public
     * @param  SC_FormParam $objForm
     * @return array $arrErr
     */
    function checkError($objForm) {
        
        $arrErr = $objForm->checkError();

        $arrForm = $objForm->getHashArray();

        // 共通設定
        if (@$arrForm['noticeMailFlg'] != 1) {
            if (isset($arrForm['noticeDays'])
            && strcmp($arrForm['noticeDays'], '') != 0) {
                $arrErr['noticeDays'] = '※入力は無効です<br/>';
            }
        } else if (@$arrForm['noticeMailFlg'] == '1') {
            if (isset($arrForm['noticeDays'])
            && strcmp($arrForm['noticeDays'], '') == 0) {
                $arrErr['noticeDays'] = '※必ず入力して下さい<br/>';
            }
        }

        // クレジット決済
        if (isset($arrForm['C_validFlg']) && $arrForm['C_validFlg'] == 1) {
            // ブランドを整理
            $arrForm['C_cardType'] = '';
            $arrValidBland = array();
            foreach (array_keys($this->arrCredit) as $val) {
                if (isset($arrForm[$val]) && $arrForm[$val] == '1') {
                    $arrForm['C_cardType'] = '1';
                    $arrValidBland[$val] = true;
                } else {
                    $arrValidBland[$val] = false;
                }
            }

            $objCreditForm = new LC_SBIVT3G_FormParam;

            if(isset($arrForm['C_mpiFlg']) && $arrForm['C_mpiFlg'] == 1) {
                $objCreditForm->addParam("本人認証タイプ", "C_mpiOption", 1,"a",
                    array("EXIST_CHECK","NUM_CHECK", "MAX_LENGTH_CHECK"));
            }
            $objCreditForm->addParam("売上フラグ", "C_captureFlg", 1, "a", 
                array("EXIST_CHECK","NUM_CHECK", "MAX_LENGTH_CHECK"));
            $objCreditForm->addParam('カードブランド', 'C_cardType', 1, 'a', 
                array('EXIST_CHECK'));
            $objCreditForm->setParam($arrForm);
            $objCreditForm->convParam();
            $arrCreditErr = $objCreditForm->checkError();
            if ($arrForm['C_cardType'] == '1') {
                // 予め整理したブランドと支払回数を照合
                $bol = $this->objSetting->isValidPaymentCount(
                    $arrValidBland,
                    $arrForm['C_settingCount']);
                if ($bol == false) {
                    $arrCreditErr['C_settingCount'] =
                        '※カードブランドと支払回数に不整合があります<br/>';
                }
            }
            $arrErr = $this->arrMerge($arrErr, $arrCreditErr);
        }
        
        // コンビニ決済
        if ((isset($arrForm['V_sejFlg']) && $arrForm['V_sejFlg'] == '1') ||
            (isset($arrForm['V_lawsonFlg']) && $arrForm['V_lawsonFlg'] == '1') ||
            (isset($arrForm['V_famimaFlg']) && $arrForm['V_famimaFlg'] == '1') ||
            (isset($arrForm['V_econFlg']) && $arrForm['V_econFlg'] == '1') ||
            (isset($arrForm['V_otherFlg']) && $arrForm['V_otherFlg'] == '1')) {

            $objConveniForm = new LC_SBIVT3G_FormParam;
            $objConveniForm->addParam("決済期限日数", "V_limitDays", CONVENI_PAYMENT_TERM_DAY, 'a', 
                array('EXIST_CHECK','NUM_CHECK','MAX_CHECK','ZERO_CHECK'));
            
            $objConveniForm->setParam($arrForm);
            $objConveniForm->convParam();
            $arrErr = $this->arrMerge($arrErr, $objConveniForm->checkError());
        }

        // Edy決済
        if ((isset($arrForm['EE_mobFlg']) && $arrForm['EE_mobFlg'] == '1') ||
            (isset($arrForm['EE_pcFlg']) && $arrForm['EE_pcFlg'] == '1')) {

            $objEdyForm = new LC_SBIVT3G_FormParam;
            $objEdyForm->addParam('決済期限日数', 'EE_limitDays', EDY_PAYMENT_TERM_DAY, '', 
                array('NUM_CHECK','EXIST_CHECK','MAX_CHECK','ZERO_CHECK'));
            
            if(isset($arrForm['EE_bccMailFlg']) && $arrForm['EE_bccMailFlg'] == "1"){
                $objEdyForm->addParam('依頼メールBCCアドレス', 'EE_bccMailAddr', EE_BCC_MAIL_ADDR_LEN, '', 
                    array('EXIST_CHECK','MAX_LENGTH_CHECK','NO_SPTAB','EMAIL_CHECK', 'SPTAB_CHECK', 'EMAIL_CHAR_CHECK'));
            }

            $objEdyForm->setParam($arrForm);
            $objEdyForm->convParam();
            $arrErr = $this->arrMerge($arrErr, $objEdyForm->checkError());
        }

        // Suica決済
        if ((isset($arrForm['ES_mobMailFlg']) && $arrForm['ES_mobMailFlg'] == '1') ||
            (isset($arrForm['ES_mobAppFlg']) && $arrForm['ES_mobAppFlg'] == '1') ||
            (isset($arrForm['ES_pcMailFlg']) && $arrForm['ES_pcMailFlg'] == '1') ||
            (isset($arrForm['ES_pcAppFlg']) && $arrForm['ES_pcAppFlg'] == '1')) {
            
            $objSuicaForm = new LC_SBIVT3G_FormParam;
            $objSuicaForm->addParam('決済期限日数', 'ES_limitDays', SUICA_PAYMENT_TERM_DAY, '', 
                array('EXIST_CHECK','NUM_CHECK','MAX_CHECK','ZERO_CHECK'));
            $objSuicaForm->addParam('表示商品・サービス名', 'ES_shopName', ES_SHOPNAME_LEN, '', 
                array('EXIST_CHECK','SPTAB_CHECK','MAX_BYTE_LENGTH_CHECK','HANKAKU_KANA_CHECK'));
            
            if(isset($arrForm['ES_bccMailFlg']) && $arrForm['ES_bccMailFlg'] == "1"){
                $objSuicaForm->addParam('依頼・返金メールBCCアドレス', 'ES_bccMailAddr', ES_BCC_MAIL_ADDR_LEN, '', 
                    array('EXIST_CHECK','MAX_LENGTH_CHECK','NO_SPTAB','EMAIL_CHECK', 'SPTAB_CHECK', 'EMAIL_CHAR_CHECK'));
            }

                $objSuicaForm->setParam($arrForm);
                $objSuicaForm->convParam();
                $arrErr = $this->arrMerge($arrErr, $objSuicaForm->checkError());
        }

        // Waon決済
        if ((isset($arrForm['EW_mobFlg']) && $arrForm['EW_mobFlg'] == '1') ||
            (isset($arrForm['EW_pcFlg']) && $arrForm['EW_pcFlg'] == '1')) {

            $objWaonForm = new LC_SBIVT3G_FormParam;
            $objWaonForm->addParam('決済期限日数', 'EW_limitDays', WAON_PAYMENT_TERM_DAY, '', 
                array('EXIST_CHECK','NUM_CHECK','MAX_CHECK','ZERO_CHECK'));
            $objWaonForm->addParam('返金期限日数', 'EW_limitCancel', WAON_PAYMENT_TERM_DAY, '', 
                array('EXIST_CHECK','NUM_CHECK','MAX_CHECK','ZERO_CHECK'));

            $objWaonForm->setParam($arrForm);
            $objWaonForm->convParam();
            $arrErr = $this->arrMerge($arrErr, $objWaonForm->checkError());
        }

        // Pay-easy決済
        if ((isset($arrForm['B_atmFlg']) && $arrForm['B_atmFlg'] == '1') ||
            (isset($arrForm['B_netFlg']) && $arrForm['B_netFlg'] == '1')) {
           
            $objBankForm = new LC_SBIVT3G_FormParam;
            $objBankForm->addParam('決済期限日数', 'B_limitDays', BANK_PAYMENT_TERM_DAY, 'a', 
                array('EXIST_CHECK','NUM_CHECK','MAX_CHECK', 'ZERO_CHECK'));
            $objBankForm->addParam('請求内容', 'B_note', B_NOTE_LEN, '', 
                array('EXIST_CHECK','SPTAB_CHECK','MAX_BYTE_LENGTH_CHECK','HANKAKU_KANA_CHECK'));
            $objBankForm->addParam('請求内容カナ', 'B_noteKana', B_NOTE_KANA_LEN, 'KC', 
                array('EXIST_CHECK','SPTAB_CHECK','MAX_BYTE_LENGTH_CHECK','HANKAKU_KANA_CHECK','KANA_CHECK'));

            $objBankForm->setParam($arrForm);
            $objBankForm->convParam();
            $arrErr = $this->arrMerge($arrErr, $objBankForm->checkError());
        }

        // PayPal決済
        if(isset($arrForm["P_validFlg"]) && $arrForm["P_validFlg"] == "1"){
            $objPayPalForm = new LC_SBIVT3G_FormParam;
            $objPayPalForm->addParam("売上フラグ", "P_captureFlg", 1, "a", 
                array('EXIST_CHECK','NUM_CHECK', 'MAX_LENGTH_CHECK'));
        
            $objPayPalForm->setParam($arrForm);
            $objPayPalForm->convParam();
            $arrErr = $this->arrMerge($arrErr, $objPayPalForm->checkError());
        }

        // キャリア決済
        if ((isset($arrForm["CA_docomoFlg"]) &&
             $arrForm["CA_docomoFlg"] == "1") ||
            (isset($arrForm["CA_auFlg"]) &&
             $arrForm["CA_auFlg"] == "1") ||
            (isset($arrForm["CA_sb_ktaiFlg"]) &&
             $arrForm["CA_sb_ktaiFlg"] == "1") ||
            (isset($arrForm["CA_sb_matometeFlg"]) &&
             $arrForm["CA_sb_matometeFlg"] == "1") ||
            (isset($arrForm["CA_s_bikkuriFlg"]) &&
             $arrForm["CA_s_bikkuriFlg"] == "1")) {
            $objCarrierForm = new LC_SBIVT3G_FormParam;
            $objCarrierForm->addParam(
                "本人認証(3Dセキュア)", "CA_3DFlg", 1, "a", 
                array('EXIST_CHECK','NUM_CHECK', 'MAX_LENGTH_CHECK'));
            $objCarrierForm->addParam(
                "商品タイプ", "CA_itemType", 1, "a", 
                array('EXIST_CHECK','NUM_CHECK', 'MAX_LENGTH_CHECK'));
            $objCarrierForm->addParam(
                "売上フラグ", "CA_captureFlg", 1, "a", 
                array('EXIST_CHECK','NUM_CHECK', 'MAX_LENGTH_CHECK'));
        
            $objCarrierForm->setParam($arrForm);
            $objCarrierForm->convParam();
            $arrErr = $this->arrMerge($arrErr, $objCarrierForm->checkError());
        }

        return $arrErr;
    }

    /**
     * 配列をマージする
     *
     * @access public
     * @param  array $arrBase $arrTarget
     * @return array arrRet
     */
    function arrMerge($arrBase, $arrTarget) {
        if (is_null($arrBase)) {
            $arrRet = $arrTarget;
        } else {
            if (is_null($arrTarget)) {
                $arrRet = $arrBase;
            } else {
                $arrRet = array_merge($arrBase, $arrTarget);
            }
        }
        return $arrRet;
    }
}
?>
