<?php
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';
require_once(MDL_SMBC_CLASS_PATH . 'SC_SMBC_Data.php');

/**
 * コンビニ決済（番号受付）情報入力画面 のページクラス.
 *
 * @package Page
 */
class LC_Page_Mdl_SMBC_Conveni extends LC_Page_Ex {

    var $arrForm;

    // エラー内容を格納する配列
    var $arrErr;

    // 決済連携対象の受注番号
    var $order_id;

    // 連携データ管理クラス
    var $objSmbcData;

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        // 現在のページが、決済画面であることを伝える。
        $this->page_mdl_smbc = true;

        parent::init();
        $this->objSmbcData = new SC_SMBC_Data();

        // 送信用データの配列初期化
        $this->initArrParam();

        // テンプレートの設定
        $template = MDL_SMBC_TEMPLATE_PATH . 'conveni';
        $template .= SC_MobileUserAgent::isMobile() ? '_mobile' : '';
        $template .= SC_SmartphoneUserAgent::isSmartphone() ? '_sphone' : '';
        $this->tpl_mainpage = $template.'.tpl';

        // スマートフォンの場合はEC-CUBE標準のフレームを使わない
        if(SC_SmartphoneUserAgent::isSmartphone()){
            $this->template = MDL_SMBC_TEMPLATE_PATH.'sphone_frame.tpl';
        }

        $this->tpl_title = "SMBC決済 コンビニ（番号方式）";

        //使えるコンビニ初期化
        $arrCONVENI = array();
        $arrCONVENI[MDL_SMBC_CONVENI_SEVENELEVEN_KESSAI_ID]  = "セブン-イレブン";
        $arrCONVENI[MDL_SMBC_CONVENI_LAWSON_KESSAI_ID]  = "ローソン";
        $arrCONVENI[MDL_SMBC_CONVENI_SEICOMART_KESSAI_ID]  = "セイコーマート";
        $arrCONVENI[MDL_SMBC_CONVENI_FAMILYMART_KESSAI_ID]  = "ファミリーマート";
        $arrCONVENI[MDL_SMBC_CONVENI_CIRCLEKSUNKUS_KESSAI_ID]  = "サークルＫ・サンクス";

        $this->arrCONVENI = $arrCONVENI;

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
        $_SESSION['MDL_SMBC']['order_id'] = $_SESSION['order_id'];

        // 連携データを取得
        $this->arrParam = $this->objSmbcData->makeParam($_SESSION['order_id']);

        //使えるコンビニ
        foreach($this->arrParam['arr_conveni'] as $key => $val){
            if($val != 1){
                unset($this->arrCONVENI[$key]);
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
                exit; // リダイレクトするためexitする
                break;
            // 初回表示
            default:
                $objForm = $this->initParam();
                $this->arrForm = $objForm->getFormParamList();
                $this->arrForm['conveni']['value'] = MDL_SMBC_CONVENI_SEVENELEVEN_KESSAI_ID;
                break;
        }

        // 前のページで正しく登録手続きが行われた記録があるか判定
        $arrOrderForCheck = $this->objSmbcData->getOrderTemp($_SESSION['order_id']);
        if ($arrOrderForCheck['status'] != ORDER_PENDING) {
            unset($_SESSION['order_id']);
            unset($_SESSION['MDL_SMBC']['order_id']);
            SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, $objSiteSess);
        }

    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * コンビニ決済に関する送信データ項目の配列の初期化
     */
    function initArrParam() {
        $this->objSmbcData->initArrParam();

        $this->objSmbcData->addArrParam("version", 3, MDL_SMBC_TO_ENCODE);
        $this->objSmbcData->addArrParam("bill_method", 2, MDL_SMBC_TO_ENCODE);
        $this->objSmbcData->addArrParam("kessai_id", 4, MDL_SMBC_TO_ENCODE);
    }

    /**
     * 決済ステーションへデータを送る
     *
     */
    function sendMode() {
        // フォームパラメータの初期化
        $objForm = $this->initParam();

        $this->arrForm = $objForm->getFormParamList();

        // 入力フォームのエラーチェック
        $this->arrErr = $objForm->checkError();

        if (count($this->arrErr) == 0) {
            $arrParam = array();

            // 連携データを取得
            $arrParam =  $this->objSmbcData->makeParam($_SESSION['order_id']);

            // コンビニ決済用連携データを作成
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

            // 送信データを設定する
            $this->objSmbcData->setParam($arrParam);

            // 決済ステーションへ送信
            $arrResponse = $this->objSmbcData->sendParam($connect_url);

            // 連携結果を取得
            $res_mode = $this->objSmbcData->getMode($arrResponse);
            switch($res_mode) {
                // 決済処理を行う
                case 'complete':
                    $this->completeMode($arrResponse);
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
        }
    }

    /**
     * コンビニ決済用連携データを設定
     *
     */
    function makeParam($arrParam) {
         // バージョン
        $arrParam['version'] = SC_MobileUserAgent::isMobile() ? MDL_SMBC_DATA_LINK_MOBILE_CONVENI_VERSION : MDL_SMBC_DATA_LINK_PC_VERSION;

        // 決済手段区分
        $arrParam['bill_method'] = MDL_SMBC_CONVENI_NUMBER_BILL_METHOD;

        // 決済種類コード。
        $arrParam['kessai_id'] = $this->arrForm['conveni']['value'];

        return $arrParam;
    }

    /**
     * 完了画面へリダイレクトする
     *
     */
    function completeMode($arrResponse) {
        $objQuery = new SC_Query();
        $arrGuide = array();

        // コンビニの支払情報を完了画面表示のためにＤＢ更新
        switch($arrResponse['kessai_id']) {
            case MDL_SMBC_CONVENI_SEVENELEVEN_KESSAI_ID:
                $arrGuide['title']['value'] = "1";
                $arrGuide['title']['name'] = "セブンイレブンでのお支払";
                $arrGuide['haraidashi_no1']['name'] = "払込票番号";
                $arrGuide['haraidashi_no1']['value'] = $arrResponse['haraidashi_no1'];
                $arrGuide['haraidashi_no2']['name'] = "払込票URL";
                $arrGuide['haraidashi_no2']['value'] = $arrResponse['haraidashi_no2'];
                $arrGuide['guide']['name'] = "セブンイレブンでのお支払方法について";
                $arrGuide['guide']['value'] = "http://kb.smbc-fs.co.jp/oshiharai/sej/";
                break;
            case MDL_SMBC_CONVENI_LAWSON_KESSAI_ID:
                $arrGuide['title']['value'] = "1";
                $arrGuide['title']['name'] = "ローソンでのお支払";
                $arrGuide['haraidashi_no1']['name'] = "支払受付番号";
                $arrGuide['haraidashi_no1']['value'] = $arrResponse['haraidashi_no1'];
                $arrGuide['guide']['name'] = "ローソンでのお支払方法について";
                $arrGuide['guide']['value'] = "http://kb.smbc-fs.co.jp/oshiharai/lawson/";
                break;
            case MDL_SMBC_CONVENI_SEICOMART_KESSAI_ID:
                $arrGuide['title']['value'] = "1";
                $arrGuide['title']['name'] = "セイコーマートでのお支払";
                $arrGuide['haraidashi_no1']['name'] = "支払受付番号";
                $arrGuide['haraidashi_no1']['value'] = $arrResponse['haraidashi_no1'];
                $arrGuide['guide']['name'] = "セイコーマートでのお支払方法について";
                $arrGuide['guide']['value'] = "http://kb.smbc-fs.co.jp/oshiharai/seicomart/";
                break;
            case MDL_SMBC_CONVENI_FAMILYMART_KESSAI_ID:
                $arrGuide['title']['value'] = "1";
                $arrGuide['title']['name'] = "ファミリーマートでのお支払";
                $arrGuide['haraidashi_no1']['name'] = "企業コード";
                $arrGuide['haraidashi_no1']['value'] = $arrResponse['haraidashi_no1'];
                $arrGuide['haraidashi_no2']['name'] = "注文番号";
                $arrGuide['haraidashi_no2']['value'] = $arrResponse['haraidashi_no2'];
                $arrGuide['guide']['name'] = "ファミリーマートでのお支払方法について";
                $arrGuide['guide']['value'] = "http://kb.smbc-fs.co.jp/oshiharai/family/";
                break;
            case MDL_SMBC_CONVENI_CIRCLEKSUNKUS_KESSAI_ID:
                $arrGuide['title']['value'] = "1";
                $arrGuide['title']['name'] = "サークルＫ・サンクスでのお支払";
                $arrGuide['haraidashi_no1']['name'] = "オンライン決済番号";
                $arrGuide['haraidashi_no1']['value'] = $arrResponse['haraidashi_no1'];
                $arrGuide['guide']['name'] = "サークルＫ・サンクスでのお支払方法について";
                $arrGuide['guide']['value'] = "http://kb.smbc-fs.co.jp/oshiharai/circleksunkus/";
                break;
            default:
                break;
        }
        $sqlval['memo02'] = serialize($arrGuide);

        $objQuery->update("dtb_order", $sqlval, "order_id = ?", array($_SESSION['order_id']));

        $_SESSION['credit_regist'] = false;
        unset($_SESSION['MDL_SMBC']);

        // 完了画面へリダイレクト
        $objSiteSess = new SC_SiteSession();
        $objSiteSess->setRegistFlag();

        SC_Response_Ex::sendRedirect(ROOT_URLPATH . "smbc/complete.php");
        exit;
    }

    /**
     * フォームパラメータの初期化
     *
     * @return SC_FormParam
     */
    function initParam() {
        $objForm = new SC_FormParam();

        $objForm->addParam("コンビニの選択", "conveni", INT_LEN, "n", array('EXIST_CHECK', "MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objForm->setParam($_POST);
        $objForm->convParam();
        return $objForm;
    }

    /**
     * 戻るボタンのリダイレクト処理
     *
     */
    function returnMode() {
        $objSiteSess = new SC_SiteSession;
        $objSiteSess->setRegistFlag();
        SC_Response_Ex::sendRedirect(SHOPPING_CONFIRM_URLPATH);
    }

    /**
     * 決済ステーションから受け取ったエラー情報を、表示用データにする.
     *
     */
    function dispError($arrResponse) {
        $objQuery = new SC_Query();

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

        // 結果内容
        $this->arrErr['res'] = mb_convert_encoding($arrResponse['res'], "UTF-8", "auto");
        // 結果コード
        $this->arrErr['rescd'] = $arrResponse['rescd'];

        return;
    }
}
?>