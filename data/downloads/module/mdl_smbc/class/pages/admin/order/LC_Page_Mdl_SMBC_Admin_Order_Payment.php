<?php
// {{{ requires
require_once(CLASS_REALDIR . "pages/LC_Page.php");
require_once(MODULE_REALDIR . 'mdl_smbc/inc/include.php');
require_once(MDL_SMBC_CLASS_PATH . 'SC_Mdl_SMBC.php');
require_once(MDL_SMBC_CLASS_PATH . 'SC_SMBC.php');
/**
 * クレジット請求管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Mdl_SMBC_Admin_Order_Payment extends LC_Page {

    // 請求確定連携データの配列
    var $arrParam;

    // エラー内容を格納する配列
    var $arrErr;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = MDL_SMBC_TEMPLATE_PATH . 'admin/order/payment.tpl';
        $this->tpl_subnavi = TEMPLATE_ADMIN_REALDIR . 'order/subnavi.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'credit';
        $this->tpl_pager = TEMPLATE_ADMIN_REALDIR . 'pager.tpl';
        $this->tpl_subtitle = '入金管理';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrORDERSTATUS = $masterData->getMasterData("mtb_order_status");
        $this->arrPageMax = $masterData->getMasterData("mtb_page_max");

        $this->arrPAYMENTSTATUS = array("1" => "入金済み", "2" => "過入金", "3" => "一部入金");
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->objSmbc = new SC_SMBC();
        $objView = new SC_AdminView();

        // パラメータ管理クラス
        $this->objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParam();
        $this->objFormParam->setParam($_POST);

        // 検索ワードの引き継ぎ
        foreach ($_POST as $key => $val) {
            if (ereg("^search_", $key)) {
                $this->arrHidden[$key] = $val;
            }
        }

        // ページ送り用
        $this->arrHidden['search_pageno'] =
            isset($_POST['search_pageno']) ? $_POST['search_pageno'] : "";

        // 認証可否の判定
        $objSess = new SC_Session();
        SC_Utils_Ex::sfIsSuccess($objSess);

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        $this->arrForm = $_POST;

        switch ($this->arrForm['mode']){
            case 'update':
                switch ($this->objFormParam->getValue('change_status')) {
                    case '':
                        break;
                        // 削除
                    case 'delete':
                        $this->lfDelete($this->objFormParam->getValue('move'));
                        break;
                        // 更新
                    default:
                        $this->lfStatusMove($this->objFormParam->getValue('change_status'), $this->objFormParam->getValue('move'));
                        break;
                }

                // 対応状況
                $status = !is_null($this->objFormParam->getValue('status')) ? $this->objFormParam->getValue('status') : "";
                break;
            default:
                break;
        }

        // 入力値の変換
        $this->objFormParam->convParam();
        $this->arrErr = $this->lfCheckError();
        $arrRet = $this->objFormParam->getHashArray();

        //ステータス情報
        if(!empty($this->arrForm['order_status'])){
            $this->SelectedOrderStatus = $arrRet['order_status'] = $this->arrForm['order_status'];
        }
        if(!empty($this->arrForm['credit_status'])){
            $this->SelectedStatus = $arrRet['credit_status'] = $this->arrForm['credit_status'];
        }

        //検索結果の取得と表示
        if (count($this->arrErr) == 0) {
            $this->lfGetPaymentData($arrRet);
        }

        $objDate = new SC_Date();
        // 登録・更新日検索用
        $objDate->setStartYear(RELEASE_YEAR);
        $objDate->setEndYear(DATE("Y"));
        $this->arrRegistYear = $objDate->getYear();
        $this->arrBirthYear = $objDate->getYear();
        // 月日の設定
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();

        // 入力値の取得
        $this->arrForm = $this->objFormParam->getFormParamList();

        switch ($this->arrForm['mode']){
            case 'send': // 請求確定
            case 'batch': // 請求確定（一括）
            case 'cancel': // 請求取消（一括）
                unset($this->arrForm['show_batch_order_id']);
                unset($this->arrForm['show_cancel_order_id']);
                unset($this->arrForm['hidden_batch_order_id']);
                unset($this->arrForm['hidden_cancel_order_id']);
        }

        $objView->assignobj($this);
        $objView->display(MAIN_FRAME);
    }
    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /*
     * クレジット請求一覧の取得
     */
    function lfGetPaymentData($arrRet){

        $select ="*";
       
        if ($arrRet['not_payment'] == 1) {
            $from = "dtb_mdl_smbc_payment RIGHT JOIN dtb_order USING(order_id)";
        } else {
            $from = "dtb_mdl_smbc_payment INNER JOIN dtb_order USING(order_id)";
        }

        $where = "del_flg = 0 AND status <> ". ORDER_PENDING;
        foreach ($arrRet as $key => $val) {
            if($val == "") {
                continue;
            }

            switch ($key) {
            case 'search_order_name':
                if(DB_TYPE == "pgsql"){
                    $where .= " AND order_name01||order_name02 ILIKE ?";
                }elseif(DB_TYPE == "mysql"){
                    $where .= " AND concat(order_name01,order_name02) ILIKE ?";
                }
                $nonsp_val = mb_ereg_replace("[ 　]+","",$val);
                $arrval[] = "%$nonsp_val%";
                break;
            case 'search_order_kana':
                if(DB_TYPE == "pgsql"){
                    $where .= " AND order_kana01||order_kana02 ILIKE ?";
                }elseif(DB_TYPE == "mysql"){
                    $where .= " AND concat(order_kana01,order_kana02) ILIKE ?";
                }
                $nonsp_val = mb_ereg_replace("[ 　]+","",$val);
                $arrval[] = "%$nonsp_val%";
                break;
            case 'search_order_id1':
                $where .= " AND order_id >= ?";
                $arrval[] = $val;
                break;
            case 'search_order_id2':
                $where .= " AND order_id <= ?";
                $arrval[] = $val;
                break;
            case 'search_sorderyear':
                $date = SC_Utils_Ex::sfGetTimestamp($_POST['search_sorderyear'], $_POST['search_sordermonth'], $_POST['search_sorderday']);
                $where.= " AND create_date >= ?";
                $arrval[] = $date;
                break;
            case 'search_eorderyear':
                $date = SC_Utils_Ex::sfGetTimestamp($_POST['search_eorderyear'], $_POST['search_eordermonth'], $_POST['search_eorderday'], true);
                $where.= " AND create_date <= ?";
                $arrval[] = $date;
                break;
            case 'search_supdateyear':
                $date = SC_Utils_Ex::sfGetTimestamp($_POST['search_supdateyear'], $_POST['search_supdatemonth'], $_POST['search_supdateday']);
                $where.= " AND dtb_order.update_date >= ?";
                $arrval[] = $date;
                break;
            case 'search_eupdateyear':
                $date = SC_Utils_Ex::sfGetTimestamp($_POST['search_eupdateyear'], $_POST['search_eupdatemonth'], $_POST['search_eupdateday'], true);
                $where.= " AND dtb_order.update_date <= ?";
                $arrval[] = $date;
                break;
            case 'search_order_status':
            case 'order_status': // プルダウン
                $where.= " AND status = ?";
                $arrval[] = $val;
                break;
            case 'search_payment_status':
            case 'credit_status': // プルダウン
                if ($val == MDL_SMBC_PAYMENT_STATUS_OK || $val == MDL_SMBC_PAYMENT_STATUS_OVER || $val == MDL_SMBC_PAYMENT_STATUS_SHORT) {
                    $where.= " AND payment_status = ?";
                    $arrval[] = $val;
                }
                break;
            default:
                if (!isset($arrval)) $arrval = array();
                break;
            }
        }

        $objQuery = new SC_Query();
        // 行数の取得
        $linemax = $objQuery->count($from, $where, $arrval);
        $this->tpl_linemax = $linemax;               // 何件が該当しました。表示用

        // ページ送りの処理
        if(is_numeric($_POST['search_page_max'])) {
            $page_max = $_POST['search_page_max'];
        } else {
            $page_max = SEARCH_PMAX;
        }

        // ページ送りの取得
        $objNavi = new SC_PageNavi($this->arrHidden['search_pageno'],
                                   $linemax, $page_max,
                                   "fnNaviSearchPage", NAVI_PMAX);
        $startno = $objNavi->start_row;
        $this->arrPagenavi = $objNavi->arrPagenavi;

        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setlimitoffset($page_max, $startno);
        //表示順序
        $order = "dtb_order.order_id DESC";
        $objQuery->setorder($order);

        //検索結果の取得
        $this->arrPaymentData = $objQuery->select($select, $from, $where, $arrval);
    }


    /**
     * クレジット決済以外の受注の場合、エラーメッセージを設定する
     */
    function lfErrCheck() {
        $objQuery = new SC_Query();
        $errMsg = "";

        $col = "memo01";
        $from = "dtb_payment";
        $where = "payment_id = (SELECT payment_id FROM dtb_order WHERE order_id = ?);";

        $payment = $objQuery->select($col, $from, $where, array($this->arrForm['order_id']));

        if ($payment[0]['memo01'] != MDL_SMBC_CREDIT_BILL_METHOD) {
            $errMsg = "クレジット決済以外の受注です。";
        }
        return $errMsg;
    }

    /**
     * 決済ステーションから受け取ったエラー情報を、表示用データにする.
     *
     * @param array $arrResponse 決済ステーションからのレスポンスボディ
     * @return void
     */
    function lfDispError($arrResponse) {
        // 結果内容
        $this->arrError['res'] = mb_convert_encoding($arrResponse['res'], "UTF-8", "auto");
        // 結果コード
        $this->arrError['rescd'] = $arrResponse['rescd'];
        // 対象レコード
        $this->arrError['order_id'] = $this->arrForm['order_id'];
    }

    /**
     * 請求確定処理を行う
     *
     * @param void
     * @return void
     */
    function lfCompleteMode() {
        $objQuery = new SC_Query();

        $sqlval = array();
        $sqlval['credit_status'] = MDL_SMBC_CREDIT_STATUS_KAKUTEI;
        $sqlval['update_date'] = "NOW()";

        $objQuery->update("dtb_mdl_smbc_order", $sqlval, "order_id = ?", array($this->arrForm['order_id']));

        // 受注の対応状況を入金済みに設定する
        $sqlval = array();
        $sqlval['status'] = ORDER_PRE_END;
        $sqlval['payment_date'] = "NOW()";
        $sqlval['update_date'] = "NOW()";
        $objQuery->update("dtb_order", $sqlval, " order_id = ?", array($this->arrForm['order_id']));
    }

    /**
     * 請求取消処理を行う
     *
     * @param void
     * @return void
     */
    function lfCompleteCancelMode() {
        $objQuery = new SC_Query();

        $sqlval = array();
        $sqlval['credit_status'] = MDL_SMBC_CREDIT_STATUS_CANCEL;
        $sqlval['update_date'] = "NOW()";

        $objQuery->update("dtb_mdl_smbc_order", $sqlval, "order_id = ?", array($this->arrForm['order_id']));

        // 受注の対応状況をキャンセルに設定する
        $sqlval = array();
        $sqlval['status'] = ORDER_CANCEL;
        $sqlval['update_date'] = "NOW()";
        $objQuery->update("dtb_order", $sqlval, " order_id = ?", array($this->arrForm['order_id']));
    }

    /**
     * 全決済の共通項目の連携データを設定する
     *
     * @param unknown_type $order_id 受注番号
     * @return array $arrParam
     */
    function lfMakeParam ($order_id, $version) {
        $objQuery = new SC_Query();

        // バージョン
        $this->arrParam['version'] = $version;

        // 決済手段区分
        $this->arrParam['bill_method'] = MDL_SMBC_CREDIT_BILL_METHOD;

        // 決済種類コード
        $this->arrParam['kessai_id'] = MDL_SMBC_CREDIT_KESSAI_ID;

        // 請求番号
        $this->arrParam['shoporder_no'] = str_pad($this->arrForm['order_id'], 17, "0", STR_PAD_LEFT);

        // 請求金額
        $this->arrParam['seikyuu_kingaku'] = $this->arrForm['payment_total'];

        // モジュールマスタからデータを取得
        $payment_id = $objQuery->select("payment_id", "dtb_order", "order_id = ?", array($order_id));
        $arrModule = $this->objSmbc->getModuleMasterData($payment_id[0]['payment_id']);

        // 契約コード
        $this->arrParam['shop_cd'] = $arrModule['shop_cd'];

        // 収納企業コード
        $this->arrParam['syuno_co_cd'] = $arrModule['syuno_co_cd'];

        // ショップパスワード
        $this->arrParam['shop_pwd'] = $arrModule['shop_pwd'];

        // 接続先
        if($arrModule['connect_url'] == "real"){
            // 本番用
            $this->connect_url = MDL_SMBC_CREDIT_KAKUTEI_LINK_URL_REAL;
        }else{
            // テスト用
            $this->connect_url = MDL_SMBC_CREDIT_KAKUTEI_LINK_URL_TEST;
        }
    }

    /**
     * クレジット請求確定連携の送信データ項目の配列の初期化
     *
     * @param void
     * @return void
     */
    function lfInitArrParam() {
        $this->objSmbc->addArrParam("version", 3);
        $this->objSmbc->addArrParam("bill_method", 2);
        $this->objSmbc->addArrParam("kessai_id", 4);
        $this->objSmbc->addArrParam("shop_cd", 7);
        $this->objSmbc->addArrParam("syuno_co_cd", 8);
        $this->objSmbc->addArrParam("shop_pwd", 20);
        $this->objSmbc->addArrParam("shoporder_no", 17);
        $this->objSmbc->addArrParam("seikyuu_kingaku", 13);
    }

    /* パラメータ情報の初期化 */
    function lfInitParam() {
        $this->objFormParam->addParam("注文番号1", "search_order_id1", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("注文番号2", "search_order_id2", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("対応状況", "search_order_status", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("請求ステータス", "search_credit_status", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("顧客名", "search_order_name", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("顧客名(カナ)", "search_order_kana", STEXT_LEN, "KVCa", array("KANA_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("表示件数", "search_page_max", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("開始日", "search_sorderyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("開始日", "search_sordermonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("開始日", "search_sorderday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("終了日", "search_eorderyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("終了日", "search_eordermonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("終了日", "search_eorderday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("開始日", "search_supdateyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("開始日", "search_supdatemonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("開始日", "search_supdateday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("終了日", "search_eupdateyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("終了日", "search_eupdatemonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("終了日", "search_eupdateday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("変更前対応状況", 'status', INT_LEN, 'n', array( "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("変更後対応状況", "change_status", STEXT_LEN, 'KVa', array( "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("移動注文番号", 'move', INT_LEN, 'n', array( "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("入金情報のない注文情報も表示する", 'not_payment', INT_LEN, 'n', array( "MAX_LENGTH_CHECK", "NUM_CHECK"));
    }

    /* 入力内容のチェック */
    function lfCheckError() {
        // 入力データを渡す。
        $arrRet = $this->objFormParam->getHashArray();
        $objErr = new SC_CheckError($arrRet);
        $objErr->arrErr = $this->objFormParam->checkError();

        // 特殊項目チェック
        $objErr->doFunc(array("注文番号1", "注文番号2", "search_order_id1", "search_order_id2"), array("GREATER_CHECK"));
        $objErr->doFunc(array("開始日", "search_sorderyear", "search_sordermonth", "search_sorderday"), array("CHECK_DATE"));
        $objErr->doFunc(array("終了日", "search_eorderyear", "search_eordermonth", "search_eorderday"), array("CHECK_DATE"));
        $objErr->doFunc(array("開始日", "終了日", "search_sorderyear", "search_sordermonth", "search_sorderday", "search_eorderyear", "search_eordermonth", "search_eorderday"), array("CHECK_SET_TERM"));

        $objErr->doFunc(array("開始日", "search_supdateyear", "search_supdatemonth", "search_supdateday"), array("CHECK_DATE"));
        $objErr->doFunc(array("終了日", "search_eupdateyear", "search_eupdatemonth", "search_eupdateday"), array("CHECK_DATE"));
        $objErr->doFunc(array("開始日", "終了日", "search_supdateyear", "search_supdatemonth", "search_supdateday", "search_eupdateyear", "search_eupdatemonth", "search_eupdateday"), array("CHECK_SET_TERM"));

        return $objErr->arrErr;
    }

        /**
     * 対応状況の更新
     */
    function lfStatusMove($statusId, $arrOrderId) {
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objQuery = new SC_Query_Ex();

        if (!isset($arrOrderId) || !is_array($arrOrderId)) {
            return false;
        }
        $masterData = new SC_DB_MasterData_Ex();
        $arrORDERSTATUS = $masterData->getMasterData("mtb_order_status");

        $objQuery->begin();

        foreach ($arrOrderId as $orderId) {
            $objPurchase->sfUpdateOrderStatus($orderId, $statusId);
        }

        $objQuery->commit();

        $this->tpl_onload = "window.alert('選択項目を" . $arrORDERSTATUS[$statusId] . "へ移動しました。');";
        return true;
    }

    /**
     * 受注テーブルの論理削除
     */
    function lfDelete($arrOrderId) {
        $objQuery = new SC_Query_Ex();

        if (!isset($arrOrderId) || !is_array($arrOrderId)) {
            return false;
        }

        $arrUpdate = array(
             'del_flg'      => 1,
             'update_date'  => 'CURRENT_TIMESTAMP'
        );

        $objQuery->begin();

        foreach ($arrOrderId as $orderId) {
            $objQuery->update('dtb_order', $arrUpdate, 'order_id = ?', array($orderId));
        }

        $objQuery->commit();

        $this->tpl_onload = "window.alert('選択項目を削除しました。');";
        return true;
    }
}
?>
