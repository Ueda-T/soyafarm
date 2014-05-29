<?php
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';
require_once(MDL_SMBC_CLASS_PATH . 'SC_SMBC_Data.php');
require_once(MDL_SMBC_CLASS_PATH . 'SC_SMBC_Page.php');

/**
 * クレジット決済情報入力画面 のページクラス.
 *
 * @package Page
 */
class LC_Page_Mdl_SMBC_Credit extends LC_Page_Ex {

    var $arrForm;

    // エラー内容を格納する配列
    var $arrErr;

    // 決済連携対象の受注番号
    var $order_id;

    // 連携データ管理クラス
    var $objSmbcData;
    
    // 送信データ用配列
    var $arrParam;

    // 送信先URL
    var $server_url;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        // 現在のページが、決済画面であることを伝える。
        $this->page_mdl_smbc = true;
        $this->skip_load_page_layout = true;

        parent::init();
        $this->objSmbcData = new SC_SMBC_Data();
      
        // 送信用データの配列初期化
        $this->initArrParam();

        // テンプレートの設定
        $this->product_type_id = $this->lfGetProductTypeId();
        $objSMBC =& SC_Mdl_SMBC::getInstance();
        $this->arrModule = $objSMBC->getSubData();
        
        if ($this->product_type_id == $this->arrModule['regular_product_type_id']) {
            $template = MDL_SMBC_TEMPLATE_PATH . 'page_link';
            $template .= SC_MobileUserAgent::isMobile() ? '_mobile' : '';
            $template .= SC_SmartphoneUserAgent::isSmartphone() ? '_sphone' : '';

            if (SC_MobileUserAgent::isMobile() == true) {
                $this->tpl_mainpage = $template.'.tpl';
            } else {
                $this->template = $template.'.tpl';
            }
        } else {
            $template = MDL_SMBC_TEMPLATE_PATH . 'credit';
            $template .= SC_MobileUserAgent::isMobile() ? '_mobile' : '';
            $template .= SC_SmartphoneUserAgent::isSmartphone() ? '_sphone' : '';
            $this->tpl_mainpage = $template.'.tpl';
        }

		$this->tpl_column_num = 1;  //左右にカラムのない画面（1カラムの画面）であることを指定
        $this->tpl_title = "SMBC決済 クレジット";

        // 年月プルダウンの初期化
        $objDate = new SC_Date();
        $objDate->setStartYear(date('Y'));
        $objDate->setEndYear(date('Y') + MDL_SMBC_CREDIT_ADD_YEAR);
        $this->arrYear  = $objDate->getZeroYear();
        $this->arrMonth = $objDate->getZeroMonth();

        //支払い方法のプルダウン初期化
        $arrPayMethod = array();
        $arrPayMethod[1]  = "１回払い";
        $arrPayMethod[2]  = "２回払い";
        $arrPayMethod[3]  = "分割払い(３回)";
        $arrPayMethod[5]  = "分割払い(５回)";
        $arrPayMethod[6]  = "分割払い(６回)";
        $arrPayMethod[10] = "分割払い(１０回)";
        $arrPayMethod[12] = "分割払い(１２回)";
        $arrPayMethod[15] = "分割払い(１５回)";
        $arrPayMethod[18] = "分割払い(１８回)";
        $arrPayMethod[20] = "分割払い(２０回)";
        $arrPayMethod[24] = "分割払い(２４回)";
        $arrPayMethod[80] = "リボ払い";
        $arrPayMethod[91] = "ボーナス一括払い";

        $this->arrPayMethod = $arrPayMethod;
        $this->allowClientCache();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        parent::process();
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    function action() {
        $objCartSess = new SC_CartSession();
        $objSiteSess = new SC_SiteSession();
        $objCustomer = new SC_Customer_Ex();
        $_SESSION['MDL_SMBC']['order_id'] = $_SESSION['order_id'];

        // 連携データを取得
        $this->arrParam = $this->objSmbcData->makeParam($_SESSION['order_id']);

        $this->product_type_id = $this->lfGetProductTypeId();
        // 継続課金商品の場合は画面連携
        if ($this->product_type_id == $this->arrModule['regular_product_type_id']) {           
            if ($objCustomer->isLoginSuccess() == false) {
                SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, '', false, '定期商品の非会員購入は出来ません。');
                exit;
            }
            $this->lfRegularOrder();
        } else {        
            //支払い方法のプルダウン
            foreach($this->arrParam['arr_pay_method'] as $key => $val){
                if($val != 1){
                    unset($this->arrPayMethod[$key]);
                }
            }

            switch($_POST['mode']) {
                // 次へボタン押下時
                case 'send':
                    $this->sendMode();
                    break;
                // 戻るボタン押下時
                case 'return':
                    $this->returnMode();
                    exit;
                    break;
                // 初回表示
                default:
                    $objForm = $this->initParam();
                    $this->arrForm = $objForm->getHashArray();
                    break;
            }

            // 前のページで正しく登録手続きが行われた記録があるか判定
            $arrOrderForCheck = $this->objSmbcData->getOrderTemp($_SESSION['order_id']);
            if ($arrOrderForCheck['status'] != ORDER_PENDING) {
                unset($_SESSION['order_id']);
                unset($_SESSION['MDL_SMBC']['order_id']);
                SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, $objSiteSess);
            }
            // 完了画面で登録ボタンの表示判定のため
            $_SESSION['credit_regist'] = false;

            //カード情報お預かりサービスを利用している場合はカード番号の存在チェックを行う
            if($this->arrParam['card_info_keep'] == 1 && $this->arrParam['customer_id'] > 0){

                $_SESSION['credit_regist'] = true;

                // 照会
                $arrResponse = $this->objSmbcData->checkCardInfo($this->arrParam, "02");

                // 顧客番号は照会時の戻り値で決済を行う
                $this->bill_no = $arrResponse['bill_no'];

                if($arrResponse['rescd'] == MDL_SMBC_RES_OK && $arrResponse['crd_cnt'] > 0){
                    $this->arrParam['regist_card_num'] = $arrResponse['crd_info_b_1'];// カード番号

                    // カードブランド画像
                    // ブランドごとに任意のファイル名に変更する場合はココでswitch等を使い分岐させる
                    // $arrResponse['crd_info_d_1'] = (int) 1:VISA 2:Master 3:JCB 4:AMEX 5:Diners
                    $this->arrParam['regist_card_brand'] = "card_brand_".$arrResponse['crd_info_d_1'].".jpg";

                    $_SESSION['credit_regist'] = false;
                }else{
                    $this->arrParam['card_info_keep'] = 2;
                }
            }else{
                $this->arrParam['card_info_keep'] = 2;
            }
        }
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
    }

    /**
     * クレジット決済に関する送信データ項目の配列の初期化
     *
     * @param void
     * @return void
     */
    function initArrParam() {
        $this->objSmbcData->initArrParam();

        $this->objSmbcData->addArrParam("version", 3, MDL_SMBC_TO_ENCODE);
        $this->objSmbcData->addArrParam("bill_method", 2, MDL_SMBC_TO_ENCODE);
        $this->objSmbcData->addArrParam("kessai_id", 4, MDL_SMBC_TO_ENCODE);
        $this->objSmbcData->addArrParam("card_no", 16, MDL_SMBC_TO_ENCODE);
        $this->objSmbcData->addArrParam("card_yukokigen", 4, MDL_SMBC_TO_ENCODE);
        $this->objSmbcData->addArrParam("shiharai_kbn", 2, MDL_SMBC_TO_ENCODE);
    }

    /**
     * 決済ステーションへデータを送る
     *
     * @param void
     * @return void
     */
    function sendMode() {
        // フォームパラメータの初期化
        $objForm = $this->initParam();

        $this->arrForm = $objForm->getHashArray();
        $this->arrErr = $objForm->checkError();

        if(!empty($this->arrForm['use_regist_card']) && $this->arrForm['use_regist_card'] == 1){
            $_SESSION['credit_regist'] = false;
        }

        if (count($this->arrErr) == 0) {
            $arrParam = array();

            // 連携データを取得
            $arrParam =  $this->objSmbcData->makeParam($_SESSION['order_id']);

            // クレジット決済用連携データを作成
            $arrParam = $this->makeParam($arrParam);

            // 接続先
            if($arrParam['connect_url'] == "real"){
                // 本番用
                $connect_url = MDL_SMBC_DATA_LINK_URL_REAL;
            }else{
                // テスト用
                $connect_url = MDL_SMBC_DATA_LINK_URL_TEST;
            }
            unset($arrParam['connect_url']);
            unset($arrParam['security_code_flg']);
            unset($arrParam['card_info_pwd']);
            unset($arrParam['arr_pay_method']);
            unset($arrParam['card_info_keep']);
            unset($arrParam['arr_conveni']);

            if(!empty($this->arrForm['use_regist_card']) && $this->arrForm['use_regist_card'] == 1){
                unset($arrParam['card_no']);
                unset($arrParam['card_yukokigen']);
//                unset($arrParam['security_cd']);
//                unset($arrParam['paymethod']);

                if (strlen($this->arrForm['bill_no']) > 0) {
                    $arrParam['bill_no'] = $this->arrForm['bill_no'];
                }
            }

            // 送信データを設定する
            $this->objSmbcData->setParam($arrParam);

            // 決済ステーションへ送信
            $arrResponse = $this->objSmbcData->sendParam($connect_url);

            if($_SESSION['credit_regist'] == true && !empty($arrResponse['kessai_no'])){
                $_SESSION['credit_regist'] = $arrResponse['kessai_no'];
                $_SESSION['smbc_kessai_no'] = $arrResponse['kessai_no'];
            }else{
                $_SESSION['credit_regist'] = '';
            }
            // 連携結果を取得
            $res_mode = $this->objSmbcData->getMode($arrResponse);
            switch($res_mode) {
                // 完了ページへ遷移
                case 'complete':
                    $this->completeMode();
                    exit;
                    break;
                // 3Dセキュア
                case 'secure':
                    $this->secureMode($arrResponse);
                    $this->sendResponse();
                    exit;
                    break;
                // 決済エラー
                case 'error':
                    $this->dispError($arrResponse);
                    break;
                // 初回表示
                default:
                    break;
            }
            unset($_SESSION['credit_regist']);
        }
        return;
    }

    /**
     * クレジット決済用連携データを設定
     *
     * @param array $arrParam 連携用データ
     * @return array $arrParam クレジット決済用データを追加した連携用データ
     */
    function makeParam($arrParam) {
         // バージョン
        $arrParam['version'] = SC_MobileUserAgent::isMobile() ? MDL_SMBC_DATA_LINK_MOBILE_CREDIT_VERSION : MDL_SMBC_DATA_LINK_PC_VERSION;

        // 決済手段区分
        $arrParam['bill_method'] = MDL_SMBC_CREDIT_BILL_METHOD;

        // 決済種類コード
        $arrParam['kessai_id'] = MDL_SMBC_CREDIT_KESSAI_ID;

        // クレジットカード番号
        $arrParam['card_no'] = $this->arrForm['card_no1']
                              .$this->arrForm['card_no2']
                              .$this->arrForm['card_no3']
                              .$this->arrForm['card_no4'];

        // クレジットカード有効期限
        $arrParam['card_yukokigen'] = $this->arrForm['card_month'] . $this->arrForm['card_year'];

        // セキュリティコード
        if($arrParam['security_code_flg'] == "1"){
            $arrParam['security_cd'] = $this->arrForm['security_code'];
        }

        // 登録されているカード番号を使用されていない場合は登録ボタンを表示する。
        if ($this->arrForm['use_regist_card'] != 1 && $this->arrParam['customer_id'] > 0 && $this->arrParam['card_info_keep'] == 1) {
            $_SESSION['credit_regist'] = true;
        }

        // お支払い方法
        $arrParam['shiharai_kbn'] = $this->arrForm['paymethod'];

        return $arrParam;
    }

    /**
     * SMBC受注テーブルにデータ送信後、完了ページへ遷移
     *
     * @param void
     * @return void
     */
    function completeMode() {
        $objQuery = new SC_Query();

        // 受注番号を取得
        $orderId = $this->objSmbcData->getOrderId();

        // dtb_mdl_smbc_orderに登録
        $sqlval = array('order_id' => $orderId,
                        'credit_status' => MDL_SMBC_CREDIT_STATUS_YOSHIN,
                        'update_date' => 'NOW()');
        $objQuery->insert("dtb_mdl_smbc_order", $sqlval);
        unset($_SESSION['MDL_SMBC']);

        // 完了画面へリダイレクト
        $objSiteSess = new SC_SiteSession();
        $objSiteSess->setRegistFlag();

        SC_Response_Ex::sendRedirect(ROOT_URLPATH . "smbc/credit_complete.php");
        exit;
    }

    /**
     * 3Dセキュア連携を行う
     *
     * @param array $arrResponse 決済ステーションからのレスポンスボディ
     * @return void
     */
    function secureMode($arrResponse) {
//        $objDb = new SC_Helper_DB_Ex();

        $_SESSION['credit_sessionid'] = $arrResponse['sessionid'];

//        $arrData['session'] = serialize($_SESSION);

//        // 集計結果を受注一時テーブルに反映
//        $objDb->sfRegistTempOrder($_SESSION['site']['uniqid'], $arrData);

        // 送信データを設定
        $this->arrParam['PaReq'] = $arrResponse['pareq'];
        $this->arrParam['MD'] = $arrResponse['shoporder_no'];
        $this->arrParam['TermUrl'] = HTTPS_URL . "smbc/credit_secure.php";

        // イシュアURLを送信先とする
        $this->server_url = $arrResponse['issuer_url'];

        // 自動的に画面連携されるようにする
        $this->tpl_onload="document.form1.submit();";
        $this->tpl_title = "決済情報送信";

        // 送信用ページのテンプレートを設定
        $this->template = MDL_SMBC_TEMPLATE_PATH . 'page_link.tpl';

    }

    /**
     * フォームパラメータの初期化
     *
     * @param void
     * @return object SC_FormParam
     */
    function initParam() {
        $objForm = new SC_FormParam();

        if(empty($_POST['use_regist_card']) || $_POST['use_regist_card'] != 1){
            $objForm->addParam('カード番号1', 'card_no1', CREDIT_NO_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
            $objForm->addParam('カード番号2', 'card_no2', CREDIT_NO_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
            $objForm->addParam('カード番号3', 'card_no3', CREDIT_NO_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
            $objForm->addParam('カード番号4', 'card_no4', CREDIT_NO_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
            $objForm->addParam("カード期限年", "card_year", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
            $objForm->addParam("カード期限月", "card_month", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        }
        $objForm->addParam("セキュリティコード", "security_code", MDL_SMBC_SECURITY_CODE_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objForm->addParam("登録カード情報", "use_regist_card");
        $objForm->addParam("お支払い区分", "paymethod", 2, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objForm->addParam("連携顧客ID", "bill_no", 256, "KVa", array("MAX_LENGTH_CHECK"));
        $objForm->setParam($_POST);
        $objForm->convParam();
        return $objForm;
    }

    /**
     * 戻るボタンのリダイレクト処理
     *
     * @param void
     * @return void
     */
    function returnMode() {
        $objSiteSess = new SC_SiteSession;
        $objSiteSess->setRegistFlag();
        SC_Response_Ex::sendRedirect(SHOPPING_CONFIRM_URLPATH);

    }

    /**
     * 決済ステーションから受け取ったエラー情報を、表示用データにする.
     *
     * @param array $arrResponse 決済ステーションからのレスポンスボディ
     * @return void
     */
    function dispError($arrResponse) {
        $objQuery = new SC_Query();

        // 決済処理中の受注は受注IDをカウントアップしない。
        $order_status = $objQuery->getCol('status', 'dtb_order', 'order_id = ?', array($_SESSION['order_id']));  
        if ($order_status[0] == ORDER_PENDING) {
            // order_idをnext_valする
            $this->order_id = $objQuery->nextval("dtb_order_order_id");

            // order_idを変更していく
            $objQuery->update("dtb_shipping", array('order_id' => $this->order_id), "order_id = ?", array($_SESSION['order_id']));
            $objQuery->update("dtb_shipment_item", array('order_id' => $this->order_id), "order_id = ?", array($_SESSION['order_id']));
            $objQuery->update("dtb_order", array('order_id' => $this->order_id), "order_id = ?", array($_SESSION['order_id']));
            $objQuery->update("dtb_order_detail", array('order_id' => $this->order_id), "order_id = ?", array($_SESSION['order_id']));
            $objQuery->update("dtb_order_temp", array('order_id' => $this->order_id), "order_id = ?", array($_SESSION['order_id']));

            $_SESSION['order_id'] = $this->order_id;
            $_SESSION['MDL_SMBC']['order_id'] = $this->order_id;
        }
        
        // 結果内容
        $this->arrErr['res'] = mb_convert_encoding($arrResponse['res'], "UTF-8", "auto");
        // 結果コード
        $this->arrErr['rescd'] = $arrResponse['rescd'];
    }
    
    function lfGetProductTypeId() {
        $objQuery = new SC_Query();
        $objProduct = new SC_Product_Ex();
        
        $arrProductClassId = $objQuery->select("product_class_id", "dtb_order_detail", "order_id = ?", array($_SESSION['order_id']));

        $arrProduct = $objProduct->getProductsClass($arrProductClassId[0]['product_class_id']);
        $productTypeId = $arrProduct['product_type_id'];
        
        return $productTypeId;
    }
    
    function lfRegularOrder() {
        $this->objSmbcPage = new SC_SMBC_Page();

	unset($_SESSION['regular_order_id']);
        $_SESSION['regular_order_id'] = $_SESSION['order_id'];
        $this->arrParam = $this->objSmbcPage->regularMakeParam($_SESSION['order_id']);
        $this->arrParam['shoporder_no'] = $this->objSmbcPage->createRegularOrderId();
        
        // 接続先
        if($this->arrParam['connect_url'] == "real"){
            // 本番用
            $connect_url_PC = MDL_SMBC_REGULAR_PAGE_LINK_PC_URL_REAL;
            $connect_url_SP = MDL_SMBC_REGULAR_PAGE_LINK_PC_URL_REAL;
            $connect_url_MB = MDL_SMBC_REGULAR_PAGE_LINK_MOBILE_URL_REAL;
        }else{
            // テスト用
            $connect_url_PC = MDL_SMBC_REGULAR_PAGE_LINK_PC_URL_TEST;
            $connect_url_SP = MDL_SMBC_REGULAR_PAGE_LINK_PC_URL_TEST;
            $connect_url_MB = MDL_SMBC_REGULAR_PAGE_LINK_MOBILE_URL_TEST;
        }
        unset($this->arrParam['connect_url']);

        // PC/SP/MBによって送信先URLを切り替え
        if(SC_MobileUserAgent::isMobile()){//MB
            $this->server_url = $connect_url_MB;
        }elseif(SC_SmartphoneUserAgent::isSmartphone()){//SP
            $this->server_url = $connect_url_SP;
        }else{//PC
            $this->server_url = $connect_url_PC;
        }

        // 送信データをログ出力
        $this->objSmbcPage->printLog($this->arrParam);
               
        $this->registRegularOrder($this->arrParam);
                
        mb_convert_variables('UTF-8', 'auto', $this->arrParam);

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

        $_SESSION['credit_regist'] = false;
        
    }
    
    /**
     * dtb_order 及び関連のレコードを登録する.
     */
    public function registRegularOrder($arrParam) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $objPurchase = new SC_Helper_Purchase_Ex();

        $arrOrderTemp = $objPurchase->getOrderTempByOrderId($_SESSION['order_id']);

        // dtb_mdl_smbc_regular_customer を作成
        $arrRegularCustomer = array();
        SC_Helper_Purchase::copyFromOrder($arrRegularCustomer, $arrOrderTemp, '', 'order',
                                          array('name01', 'name02', 'kana01', 'kana02',
                                                'zip01', 'zip02', 'pref', 'addr01', 'addr02',
                                                'tel01', 'tel02', 'tel03',
                                                'email', 'sex'));
        $arrRegularCustomer['customer_id'] = $arrOrderTemp['customer_id'];
        $arrRegularCustomer['bill_no'] = $arrParam['bill_no'];
        $arrRegularCustomer['create_date'] = $arrOrderTemp['create_date'];
        $arrRegularCustomer['update_date'] = $arrOrderTemp['update_date'];
        $arrRegularCustomer['del_flg'] = '0';
        // bill_no が重複している場合はスキップ
        if (!$objQuery->exists('dtb_mdl_smbc_regular_customer', 'bill_no = ?',
                               array($arrRegularCustomer['bill_no']))) {
                $objQuery->insert('dtb_mdl_smbc_regular_customer', $arrRegularCustomer);
        }

        // dtb_mdl_smbc_regular_order を作成
        $arrRegularOrder['bill_no'] = $arrParam['bill_no'];
        $arrRegularOrder['shoporder_no'] = $arrParam['shoporder_no'];
        $arrRegularOrder['order_id'] = $arrOrderTemp['order_id'];
        $arrRegularOrder['regular_status'] = MDL_SMBC_REGULAR_STATUS_NONE;
        $arrRegularOrder['target_ym'] = $arrParam['seikyuu_kaishi_ym'];
//        $arrRegularOrder['rescd'] = $arrParam['rescd'];
//        $arrRegularOrder['res'] = $arrParam['res'];
        $arrRegularOrder['create_date'] = $arrOrderTemp['create_date'];
        $arrRegularOrder['update_date'] = $arrOrderTemp['update_date'];
        $arrRegularOrder['del_flg'] = '1';
        // データが重複している場合はスキップ
	$where =<<<EOF
shoporder_no = ?
AND bill_no = ?
AND order_id = ?
EOF;
        if (!$objQuery->exists('dtb_mdl_smbc_regular_order', $where,
	    array($arrRegularOrder['shoporder_no']
		, $arrRegularOrder['bill_no']
		, $arrRegularOrder['order_id']))) {
	    $objQuery->insert('dtb_mdl_smbc_regular_order', $arrRegularOrder);
        }
    }
}
?>
