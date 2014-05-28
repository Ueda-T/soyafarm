<?php
require_once("../require.php");
require_once(CLASS_REALDIR . "pages/LC_Page.php");
require_once(MODULE_REALDIR . 'mdl_smbc/inc/include.php');
require_once(MODULE_REALDIR . 'mdl_smbc/class/SC_SMBC.php');
require_once(MODULE_REALDIR . 'mdl_smbc/class/SC_SMBC_Data.php');
require_once(MODULE_REALDIR . 'mdl_smbc/class/SC_SMBC_Recv.php');

class LC_Mdl_SMBC_Credit_Secure extends LC_Page{

    // 支払方法ID
    var $payment_id;

    // 注文番号
    var $order_id;

    function process() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $objRecv = new SC_SMBC_Recv();
            $this->objSmbc = new SC_SMBC();
            $this->objSmbcData = new SC_SMBC_Data();

            // 現在のページが、決済画面であることを伝える。
            $this->page_mdl_smbc = true;

            $arrPost = $_POST;

            // 通知内容をログ出力
            $this->objSmbc->printLog($arrPost);

            // 注文番号
            $this->order_id = intval($arrPost['MD']);

            // 送信データ用配列を初期化
            $this->initArrParam();

//          // セッション情報を復元
//          $this->getSessionData($arrPost);

            // 連携データを作成
            $arrParam = $this->makeParam($arrPost);

            // 送信データを設定する
            $this->objSmbcData->setParam($arrParam);

            // 決済ステーションへ送信
            $arrResponse = $this->objSmbcData->sendParam($this->connect_url);

            // 連携結果を取得
            $res_mode = $this->objSmbcData->getMode($arrResponse);
            // 請求番号が受注マスタに存在しない場合(false)はエラーとする
            if ($objRecv->searchOrderId($this->order_id) == false) {
                $res_mode = "error";
            }
            switch($res_mode) {
                // 完了ページへ遷移
                case 'complete':
                    $this->completeMode();
                    exit;
                    break;
                // エラーページへ遷移
                case 'error':
                    $this->dispError($arrResponse);

                    // テンプレートの設定
                    $template = MDL_SMBC_TEMPLATE_PATH . 'error';
                    $template .= SC_SmartphoneUserAgent::isSmartphone() ? '_sphone' : '';
                    $this->tpl_mainpage = $template.'.tpl';

                    // スマートフォンの場合はEC-CUBE標準のフレームを使わない
                    if(SC_SmartphoneUserAgent::isSmartphone()){
                        $this->template = MDL_SMBC_TEMPLATE_PATH.'sphone_frame.tpl';
                    }
                    $this->tpl_title = "SMBC決済 クレジット";

                    if(defined('PLUGIN_UPLOAD_REALDIR') && version_compare(ECCUBE_VERSION, '2.12.0') >= 0) {
                        // transformでフックしているばあいに, 再度エラーが発生するため, コールバックを無効化.
                        $objHelperPlugin = SC_Helper_Plugin_Ex::getSingletonInstance($this->plugin_activate_flg);
                        $objHelperPlugin->arrRegistedPluginActions = array();
                    }
                    $this->sendResponse();
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * SMBC受注テーブルにデータ送信後、完了ページへ遷移
     *
     * @param void
     * @return void
     */
    function completeMode() {
        $objQuery = new SC_Query();

        // dtb_mdl_smbc_orderに登録
        $sqlval = array('order_id' => $this->order_id,
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

    function makeParam($arrPost) {
        $arrParam = array();

        // バージョン
        $arrParam['version'] = MDL_SMBC_DATA_LINK_CREDIT_SECURE_VERSION;

        // 決済手段区分
        $arrParam['bill_method'] = MDL_SMBC_CREDIT_BILL_METHOD;

        // 決済種類コード
        $arrParam['kessai_id'] = MDL_SMBC_CREDIT_KESSAI_ID;

        // 決済モジュール設定内容を取得
        $objQuery = new SC_Query();
        $payment_id = $objQuery->select("payment_id", "dtb_order", "order_id = ?", array($this->order_id));
        $arrModule = $this->objSmbc->getModuleMasterData($payment_id[0]['payment_id']);

        // 契約コード
        $arrParam['shop_cd'] = $arrModule['shop_cd'];

        // 収納企業コード
        $arrParam['syuno_co_cd'] = $arrModule['syuno_co_cd'];

        // ショップパスワード
        $arrParam['shop_pwd'] = $arrModule['shop_pwd'];

        // 請求番号
        $arrParam['shoporder_no'] = $arrPost['MD'];

        // セッションID
        $arrParam['sessionid'] = $_SESSION['credit_sessionid'];

        // PaRes
        $arrParam['pares'] = $arrPost['PaRes'];

        // 接続先
        if($arrModule['connect_url'] == "real"){
            // 本番用
            $this->connect_url = MDL_SMBC_SECURE_LINK_URL_REAL;
        }else{
            // テスト用
            $this->connect_url = MDL_SMBC_SECURE_LINK_URL_TEST;
        }

        return $arrParam;
    }

    /**
     * クレジット決済3Dセキュア連携に関する送信データ項目の配列の初期化
     *
     * @param void
     * @return void
     */
    function initArrParam() {
        $this->objSmbcData->addArrParam("version", 3);
        $this->objSmbcData->addArrParam("bill_method", 2);
        $this->objSmbcData->addArrParam("kessai_id", 4);
        $this->objSmbcData->addArrParam("shop_cd", 7);
        $this->objSmbcData->addArrParam("syuno_co_cd", 8);
        $this->objSmbcData->addArrParam("shop_pwd", 20);
        $this->objSmbcData->addArrParam("shoporder_no", 17);
        $this->objSmbcData->addArrParam("sessionid", 70);
        $this->objSmbcData->addArrParam("pares", "");
    }

    /**
     * 決済ステーションから受け取ったエラー情報を、表示用データにする.
     *
     * @param array $arrResponse 決済ステーションからのレスポンスボディ
     * @return void
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
    }

    function doValidToken() {
    }

//  function getSessionData ($arrPost) {
//      $objQuery = new SC_Query();
//
//      $arrOrderSession = $objQuery->select("payment_id, session", "dtb_order_temp", "order_id = ?", array($this->order_id));
//
//      $_SESSION = unserialize($arrOrderSession[0]['session']);
//
//      $this->payment_id = $arrOrderSession[0]['payment_id'];
//  }
}
$object = new LC_Mdl_SMBC_Credit_Secure();
$object->init();
$object->process();
?>
