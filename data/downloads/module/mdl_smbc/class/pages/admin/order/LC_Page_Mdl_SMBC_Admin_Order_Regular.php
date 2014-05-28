<?php
// {{{ requires
require_once(CLASS_EX_REALDIR . "page_extends/admin/LC_Page_Admin_Ex.php");
require_once(MODULE_REALDIR . 'mdl_smbc/inc/include.php');
require_once(MDL_SMBC_CLASS_PATH . 'SC_Mdl_SMBC.php');
require_once(MDL_SMBC_CLASS_PATH . 'SC_SMBC.php');
/**
 * 定期購入課金管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Mdl_SMBC_Admin_Order_Regular extends LC_Page_Admin_Ex {

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
        $this->skip_load_page_layout = true;
        parent::init();
        $this->tpl_mainpage = MDL_SMBC_TEMPLATE_PATH . 'admin/order/regular.tpl';
        $this->tpl_subnavi = TEMPLATE_ADMIN_REALDIR . 'order/subnavi.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'regular';
        $this->tpl_pager = TEMPLATE_ADMIN_REALDIR . 'pager.tpl';
        $this->tpl_subtitle = '定期購入課金管理';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrORDERSTATUS = $masterData->getMasterData("mtb_order_status");
        $this->arrPageMax = $masterData->getMasterData("mtb_page_max");

        $this->arrSmbcRegularStatus = $GLOBALS['arrSmbcRegularStatus'];

        $objMdlSMBC = SC_Mdl_SMBC::getInstance();
        $this->subData = $objMdlSMBC->getSubData();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function action() {


        // パラメータ管理クラス
        $this->objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParam();
        $this->objFormParam->setParam($_POST);

        // 検索ワードの引き継ぎ
        foreach ($_POST as $key => $val) {
            if (preg_match('/^search_/', $key)) {
                $this->arrHidden[$key] = $val;
            }
        }

        // ページ送り用
        $this->arrHidden['search_pageno'] =
            isset($_POST['search_pageno']) ? $_POST['search_pageno'] : "";


        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        $this->arrForm = $_POST;
        switch ($this->getMode()){
            // 請求確定
            case 'execute_settled':
                while(@ob_end_clean());
                ob_start();
                $arrResults = $this->doSettled();
                ob_end_clean();
                echo SC_Utils_Ex::jsonEncode($arrResults);
                SC_Response_Ex::actionExit();
                break;

            // 与信
            case 'execute_authorization':
                while(@ob_end_clean());
                ob_start();
                $arrResults = $this->doAuthorization();
                ob_end_clean();
                echo SC_Utils_Ex::jsonEncode($arrResults);
                SC_Response_Ex::actionExit();
                break;

            // 削除
            case 'execute_delete':
                while(@ob_end_clean());
                ob_start();
                $arrResults = $this->doDelete(htmlentities($_POST['shoporder_no'], ENT_QUOTES),
                                              htmlentities($_POST['bill_no'], ENT_QUOTES),
                                              intval($_POST['order_id']));
                ob_end_clean();
                echo SC_Utils_Ex::jsonEncode($arrResults);
                SC_Response_Ex::actionExit();
                break;

            // 休止間隔更新
            case 'update_interval':
                while(@ob_end_clean());
                ob_start();
                $arrResults = $this->doUpdateInterval(intval($_POST['order_id']),
                                                      intval($_POST['regular_interval_from_year']),
                                                      intval($_POST['regular_interval_from_month']),
                                                      intval($_POST['regular_interval_to_year']),
                                                      intval($_POST['regular_interval_to_month']));
                ob_end_clean();
                echo SC_Utils_Ex::jsonEncode($arrResults);
                SC_Response_Ex::actionExit();
                break;

            case 'csv':
                echo $this->doDownloadCsv();
                SC_Response_Ex::actionExit();
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
            $this->lfGetCreditData($arrRet);
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

        $objDate->setEndYear(date('Y', mktime(0, 0, 0, date('m'), date('d'), date('Y') + 5)));
        $this->arrIntervalYear = $objDate->getYear();
        // 入力値の取得
        $this->arrForm = $this->objFormParam->getFormParamList();
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
    }

    /*
     * クレジット請求一覧の取得
     */
    function lfGetCreditData($arrRet, $no_limit = false){

        /*
         * 与信OK, 与信NG, 請求確定, 有効性NGを検索した場合は, 次回の受注が生成されているため, 未請求を除外する.
         * その他は最新の dtb_mdl_smbc_regular_order を表示する
         */
        $without_status_none = '';
        if ($arrRet['search_regular_status'] == MDL_SMBC_REGULAR_STATUS_COMPLETED
            || $arrRet['search_regular_status'] == MDL_SMBC_REGULAR_STATUS_SETTLED
            || $arrRet['search_regular_status'] == MDL_SMBC_REGULAR_STATUS_DENIED
            || $arrRet['search_regular_status'] == MDL_SMBC_REGULAR_STATUS_CHECKNG
            ) {
            $without_status_none = ' AND regular_status <> ' . MDL_SMBC_REGULAR_STATUS_NONE;
        }

        $select = SC_SMBC::regularOrderSelectSQL($without_status_none);
        $from = SC_SMBC::regularOrderFromSQL($without_status_none);
        $where = " T.del_flg = 0";

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
                $nonsp_val = preg_replace('/[ 　]/u', '', $val);
                $arrval[] = "%$nonsp_val%";
                break;
            case 'search_order_kana':
                if(DB_TYPE == "pgsql"){
                    $where .= " AND order_kana01||order_kana02 ILIKE ?";
                }elseif(DB_TYPE == "mysql"){
                    $where .= " AND concat(order_kana01,order_kana02) ILIKE ?";
                }
                $nonsp_val = preg_replace('/[ 　]/u', '', $val);
                $arrval[] = "%$nonsp_val%";
                break;
            case 'search_order_id1':
                $where .= " AND T.order_id >= ?";
                $arrval[] = $val;
                break;
            case 'search_order_id2':
                $where .= " AND T.order_id <= ?";
                $arrval[] = $val;
                break;
            case 'search_sorderyear':
                $date = SC_Utils_Ex::sfGetTimestamp($_POST['search_sorderyear'], $_POST['search_sordermonth'], $_POST['search_sorderday']);
                $where.= " AND T.create_date >= ?";
                $arrval[] = $date;
                break;
            case 'search_eorderyear':
                $date = SC_Utils_Ex::sfGetTimestamp($_POST['search_eorderyear'], $_POST['search_eordermonth'], $_POST['search_eorderday'], true);
                $where.= " AND T.create_date <= ?";
                $arrval[] = $date;
                break;
            case 'search_supdateyear':
                $date = SC_Utils_Ex::sfGetTimestamp($_POST['search_supdateyear'], $_POST['search_supdatemonth'], $_POST['search_supdateday']);
                $where.= " AND T.update_date >= ?";
                $arrval[] = $date;
                break;
            case 'search_eupdateyear':
                $date = SC_Utils_Ex::sfGetTimestamp($_POST['search_eupdateyear'], $_POST['search_eupdatemonth'], $_POST['search_eupdateday'], true);
                $where.= " AND T.update_date <= ?";
                $arrval[] = $date;
                break;
            case 'search_regular_status':
                $where.= " AND T.regular_status = ?";
                $arrval[] = $val;
                break;

            case 'order_status': // プルダウン
                $where.= " AND dtb_order.status = ?";
                $arrval[] = $val;
                break;
            default:
                if (!isset($arrval)) $arrval = array();
                break;
            }
        }

        $objQuery = SC_Query_Ex::getSingletonInstance();
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
        if (!$no_limit) {
            $objQuery->setlimitoffset($page_max, $startno);
        }
        //表示順序
        $order = "T.create_date DESC";
        $objQuery->setorder($order);

        //検索結果の取得
        $this->arrCreditData = $objQuery->select($select, $from, $where, $arrval);
        return $this->arrCreditData;
    }

    /* パラメータ情報の初期化 */
    function lfInitParam() {
        $this->objFormParam->addParam("注文番号1", "search_order_id1", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("注文番号2", "search_order_id2", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("対応状況", "search_order_status", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("請求ステータス", "search_credit_status", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("定期注文状況", "search_regular_status", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
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
     * 請求確定を実行します.
     */
    protected function doSettled() {
        // 契約情報取得
        $arrContractResponse = $this->getCustomerContracts();
        // エラーが発生した場合は結果を返す
        if ($arrContractResponse['header']['rescd'] != MDL_SMBC_RES_OK) {
            return $arrContractResponse;
        }

        $batch_error = 0;
        $checked = 0;
        foreach ($arrContractResponse['body'] as $arrBody) {
            // 受注情報を取得
            $arrRegularOrder = $this->getCurrentOrder($arrBody);
            if (SC_Utils_Ex::isBlank($arrRegularOrder)) {
                SC_SMBC::printLog('受注が存在しないためスキップします: ' . print_r($arrBody, true), true);
                continue;
            }
            // 請求金額チェック
            if ($this->doCheckPrice($arrBody, $arrRegularOrder) === false) {
                $batch_error++;
                continue;
            }
            // 請求可否チェック
            if ($this->checkDemand($arrRow, $arrRegularOrder) === false) {
                // バッチエラーにはカウントしない
                continue;
            }
            // 受注ステータスチェック
            if ($this->checkOrderStatus($arrBody, $arrRegularOrder) === false) {
                $batch_error++;
                continue;
            }
            // 請求確定
            if ($this->sendSettled($arrBody, $arrRegularOrder) === false) {
                $batch_error++;
                continue;
            }
            // 受注生成
            $this->createNewOrder($arrBody, $arrRegularOrder);
            $checked++;
        }

        if ($checked < 1) {
            $arrContractResponse['header']['rescd'] = 'ERROR';
            $arrContractResponse['header']['res'] = '請求確定可能な受注が存在しませんでした。';
        }
        if ($batch_error > 0) {
            $arrContractResponse['header']['rescd'] = 'WARNING';
            $arrContractResponse['header']['res'] = '一部の受注にエラーが発生しました。定期状況を「バッチエラー」で検索し、エラーの内容を確認してください。';
        }
        return $arrContractResponse;
    }

    /**
     * 与信結果を取得する.
     */
    protected function doAuthorization() {
        $objSmbc = new SC_SMBC();
        $this->initAddParam($objSmbc);
        $objSmbc->addArrParam("shori_kbn", 4);
        $objSmbc->addArrParam("yoshin_res_date_from", 8);
        $objSmbc->addArrParam("yoshin_res_time_from", 6);
        $objSmbc->addArrParam("yoshin_res_date_to", 8);
        $objSmbc->addArrParam("yoshin_res_time_to", 6);

        // 与信確認年月日
        // 1日〜13日までは前月〜当月までが対象
        if (date('j') >= 1 && date('j') <= 13) {
            $yoshin_res_from = mktime(0, 0, 0, date('m') - 1, 1, date('Y'));
            $yoshin_res_to = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
        }
        // その他は当月が対象
        else {
            $yoshin_res_from = mktime(0, 0, 0, date('m'), 1, date('Y'));
            $yoshin_res_to = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
        }

        $arrParams = array(
            'version' => MDL_SMBC_REGULAR_CHECK_VERSION,
            'bill_method' => MDL_SMBC_CREDIT_BILL_METHOD,
            'shop_cd' => $this->subData['regular_shop_cd'],
            'syuno_co_cd' => $this->subData['regular_syuno_co_cd'],
            'shop_pwd' => $this->subData['regular_deal_pwd'],
            'shori_kbn' => MDL_SMBC_AUTHORIZED_REQUEST_SHORI_KBN,
            'yoshin_res_date_from' => date('Ymd', $yoshin_res_from),
            'yoshin_res_time_from' => date('His', $yoshin_res_from),
            'yoshin_res_date_to' => date('Ymd', $yoshin_res_to),
            'yoshin_res_time_to' => date('His', $yoshin_res_to),
            // 'shoporder_no' => '', // テスト用
            // 'yks_hantei_res_kbn' => '0', // テスト用 有効性チェックOKのみ
            // 'card_info_upd_umu_kbn' => '0' // テスト用 カード情報更新有無 無しのみ
        );

        $objSmbc->setParam($arrParams);
        $connect_url = ($this->subData['connect_url'] == 'real') ? MDL_SMBC_TRHKINFO_URL_REAL : MDL_SMBC_TRHKINFO_URL_TEST;
        $arrResponse = $objSmbc->sendParam($connect_url);

        // エラーが発生した場合は結果を返す
        if ($arrResponse['header']['rescd'] != MDL_SMBC_RES_OK) {
            return $arrResponse;
        }

        $authorized = 0;
        $auth_ng = 0;
        if (SC_Utils_Ex::isBlank($arrResponse['body'])) {
            $arrResponse['header']['rescd'] = 'ERROR';
            $arrResponse['header']['res'] = '与信結果が存在しませんでした。';
        } else {
            foreach ($arrResponse['body'] as $arrBody) {
                // 請求確定済のデータに対して与信結果を反映
                $arrResult = $this->saveAuthorizedResults($arrBody);
                if ($arrResult !== false) {
                    $authorized++;
                    if ($arrResult['regular_status'] == MDL_SMBC_REGULAR_STATUS_DENIED) {
                        $auth_ng++;
                    }
                }
            }
            if ($authorized < 1) {
                $arrResponse['header']['rescd'] = 'WARNING';
                $arrResponse['header']['res'] = '反映可能な受注が存在しませんでした。';
            }
            if ($auth_ng > 0) {
                $arrResponse['header']['rescd'] = 'WARNING';
                $arrResponse['header']['res'] = '与信NGの受注が存在します。定期注文状況を「与信NG」で検索し、確認してください。';
            }
        }

        return $arrResponse;
    }

    /**
     * 顧客契約情報を取得する.
     *
     * 請求期間を指定する場合は, 以下のパラメータを送信する.
     * seikyuu_kikan, seikyuu_kikan_from, seikyuu_kikan_to
     */
    protected function getCustomerContracts() {
        $objSmbc = new SC_SMBC();
        $this->initAddParam($objSmbc);
        $objSmbc->addArrParam("shori_kbn", 4);

        $arrParams = array(
            'version' => MDL_SMBC_REGULAR_CHECK_VERSION,
            'bill_method' => MDL_SMBC_CREDIT_BILL_METHOD,
            'shop_cd' => $this->subData['regular_shop_cd'],
            'syuno_co_cd' => $this->subData['regular_syuno_co_cd'],
            'shop_pwd' => $this->subData['regular_deal_pwd'],
            'shori_kbn' => MDL_SMBC_CONTRACT_REQUEST_SHORI_KBN
        );

        $objSmbc->setParam($arrParams);
        $connect_url = ($this->subData['connect_url'] == 'real') ? MDL_SMBC_TRHKINFO_URL_REAL : MDL_SMBC_TRHKINFO_URL_TEST;
        $arrResponse = $objSmbc->sendParam($connect_url);
        return $arrResponse;
    }

    /**
     * 請求金額をチェックします.
     *
     * EC-CUBE の請求金額と, 決済ステーションの請求金額が異なる場合は,
     * EC-CUBE の請求金額を決済ステーションに送信します.
     */
    protected function doCheckPrice($arrRow, $arrRegularOrder) {
        // 請求金額(初回)
        $payment_total1 = $arrRow[13];
        // 請求金額(2回目以降)
        $payment_total2 = $arrRow[14];

        // 請求金額が一致していた場合は true
        if ($payment_total1 == $arrRegularOrder['payment_total']
             && $payment_total2 == $arrRegularOrder['payment_total']) {
            return true;
        }

        SC_SMBC::printLog('EC-CUBE請求金額と決済ステーションの請求金額が異なるため契約情報を変更します. ' . print_r($arrRow, true)
                          . ' ' . print_r($arrRegularOrder, true), true);

        $objSmbc = new SC_SMBC();
        $this->initAddParam($objSmbc);
        $objSmbc->addArrParam("kessai_id", 4);
        $objSmbc->addArrParam("shoporder_no", 23);
        $objSmbc->addArrParam("bill_no", 14);

        $objMdlSMBC = SC_Mdl_SMBC::getInstance();
        $arrModule = $objMdlSMBC->getSubData();

        $arrParams = array(
            'version' => MDL_SMBC_REGULAR_CHANGE_VERSION,
            'bill_method' => MDL_SMBC_CREDIT_BILL_METHOD,
            'shop_cd' => $this->subData['regular_shop_cd'],
            'syuno_co_cd' => $this->subData['regular_syuno_co_cd'],
            'shop_pwd' => $this->subData['regular_shop_pwd'],
            'kessai_id' => MDL_SMBC_CREDIT_KESSAI_ID,
            'shoporder_no' => $arrRegularOrder['shoporder_no'],
            'bill_no' => str_pad($arrRegularOrder['bill_no'], 14, "0", STR_PAD_LEFT),
            'seikyuu_kingaku1' => $arrRegularOrder['payment_total'],
            'seikyuu_kingaku2' => $arrRegularOrder['payment_total']
        );

        $objSmbc->setParam($arrParams);
        $connect_url = ($this->subData['connect_url'] == 'real') ? MDL_SMBC_CREDIT_KAKUTEI_LINK_URL_REAL : MDL_SMBC_CREDIT_KAKUTEI_LINK_URL_TEST;
        $arrResponse = $objSmbc->sendParam($connect_url);

        $arrValues['rescd'] = $arrResponse['rescd'];
        $arrValues['res'] = mb_convert_encoding($arrResponse['res'], CHAR_CODE, 'SJIS-win');
        $result = false;
        if ($arrResponse['rescd'] != MDL_SMBC_RES_OK) {
            // バッチ実行エラー
            $arrValues['regular_status'] = MDL_SMBC_REGULAR_STATUS_ERROR;
            $arrValues['update_date'] = 'CURRENT_TIMESTAMP';
            $objQuery->update('dtb_mdl_smbc_regular_order',
                              $arrValues,
                              'shoporder_no = ? AND order_id = ? AND bill_no = ?',
                              array($arrRegularOrder['shoporder_no'], $arrRegularOrder['order_id'], $arrRegularOrder['bill_no']));
        } else {
            $result = true;
        }

        return $result;
    }

    /**
     * 受注状況をチェックします.
     */
    function checkOrderStatus($arrRow, $arrRegularOrder) {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $current = time();
        // 27日以降の場合は翌月1日として判定
        if (date('d') >= 27) {
            $current = mktime(0, 0, 0, date('m') + 1, 1, date('Y'));
            SC_SMBC::printLog('27日以降ですので, 翌月分として休止期間を判定します.', true);
        }
        // 休止期間を除外
        $has_from = false;
        $from_time = 0;
        if (!SC_Utils_Ex::isBlank($arrRegularOrder['regular_interval_from'])) {
            $from_year = substr($arrRegularOrder['regular_interval_from'], 0, 4);
            $from_month = substr($arrRegularOrder['regular_interval_from'], 4, 2);
            $has_from = true;
            $from_time = mktime(0, 0, 0, $from_month, 1, $from_year);
        }
        $has_to = false;
        $to_time = 0;
        if (!SC_Utils_Ex::isBlank($arrRegularOrder['regular_interval_to'])) {
            $to_year = substr($arrRegularOrder['regular_interval_to'], 0, 4);
            $to_month = substr($arrRegularOrder['regular_interval_to'], 4, 2);
            $has_to = true;
            $to_time = mktime(0, 0, 0, $to_month, date('t'), $to_year);
        }
        $is_interval = false;

        // 開始&終了が設定されていた場合
        if (($has_from && $has_to)
            && ($from_time <= $current && $to_time >= $current)) {
            $is_interval = true;
            SC_SMBC::printLog('from & to', true);
        }
        // 開始のみが設定されていた場合
        else if (!$has_to && $from_time > 0 && $from_time <= $current) {
            $is_interval = true;
            SC_SMBC::printLog('from only', true);
        }
        // 終了のみが設定されていた場合
        else if (!$has_from && $to_time >= $current) {
            $is_interval = true;
            SC_SMBC::printLog('to only', true);
        }
        if ($is_interval) {
            SC_SMBC::printLog('休止期間のため除外しました (' . ($has_from ? date('Y/m', $from_time) : '') . '〜' . ($has_to ? date('Y/m', $to_time) : '') . ') dtb_mdl_smbc_regular_order:  ' . print_r($arrRegularOrder, true), true);
            if ($has_to) {
                $arrParams['target_ym'] = date('Ym', $to_time);
            } else {
                $arrParams['target_ym'] = '';
            }
            $arrParams['update_date'] = 'CURRENT_TIMESTAMP';
            $objQuery->update('dtb_mdl_smbc_regular_order',
                              $arrParams,
                              'shoporder_no = ? AND order_id = ? AND bill_no = ?',
                              array($arrRegularOrder['shoporder_no'], $arrRegularOrder['order_id'], $arrRegularOrder['bill_no']));
            return false;
        }

        $objPurchase = new SC_Helper_Purchase_Ex();
        $arrOrder = $objPurchase->getOrder($arrRegularOrder['order_id']);

        $result = true;
        // 会員チェック
        if ($arrOrder['customer_id'] > 0) {
            $arrCustomer = $objQuery->getRow('*', 'dtb_customer', 'customer_id = ?', $arrOrder['customer_id']);
            if ($arrCustomer['del_flg'] == 1) {
                $msg = '会員が退会しているため除外しました';
                $arrParams['rescd'] = 'ERROR';
                $arrParams['res'] = $msg;
                SC_SMBC::printLog($msg . ' dtb_mdl_smbc_regular_order:  ' . print_r($arrRegularOrder, true), true);
                $result = false;
            }
        }

        // 商品チェック
        $arrOrderDetails = $objPurchase->getOrderDetail($arrOrder['order_id']);
        if ($result) {
            foreach ($arrOrderDetails as $arrOrderDetail) {
                if ($arrOrderDetail['product_class_id'] === '0'
                    || $arrOrderDetail['product_class_id'] === 0) {
                    // product_class_id = 0 の場合は定期CSV登録された商品なので, スキップ
                    break;
                }
                $objProduct = new SC_Product_Ex();
                $arrProduct = $objProduct->getProductsClass($arrOrderDetail['product_class_id']);

                // 存在チェック
                if (SC_Utils_Ex::isBlank($arrProduct)) {
                    $msg = '商品が存在しないため除外しました';
                    $arrParams['rescd'] = 'ERROR';
                    $arrParams['res'] = $msg;
                    SC_SMBC::printLog($msg . ' dtb_mdl_smbc_regular_order:  ' . print_r($arrRegularOrder, true), true);
                    $result = false;
                }

                // 表示チェック
                $arrProductDetail = $objProduct->getDetail($arrProduct['product_id']);
                if ($arrProductDetail['status'] == 2) {
                    $msg = '商品が非表示になっているため除外しました';
                    $arrParams['rescd'] = 'ERROR';
                    $arrParams['res'] = $msg;
                    SC_SMBC::printLog($msg . ' dtb_mdl_smbc_regular_order:  ' . print_r($arrRegularOrder, true), true);
                    $result = false;
                }

                // 在庫チェック
                $limit = $objProduct->getBuyLimit($arrProduct);
                if (!is_null($limit) && $arrOrderDetail['quantity'] > $limit) {
                    $msg = '在庫が不足しているため除外しました';
                    $arrParams['rescd'] = 'ERROR';
                    $arrParams['res'] = $msg;
                    SC_SMBC::printLog($msg . ' dtb_mdl_smbc_regular_order:  ' . print_r($arrRegularOrder, true), true);
                    $result = false;
                }

                // 在庫の減少処理. 失敗した場合の在庫の巻き戻しは行なわない
                if (!$objProduct->reduceStock($arrOrderDetail['product_class_id'], $arrOrderDetail['quantity'])) {
                    if ($arrParams['cleaning_result'] === '0'
                        || $arrParams['cleaning_result'] === 0) {
                        $msg = '在庫の減少に失敗したため除外しました';
                        $arrParams['rescd'] = 'ERROR';
                        $arrParams['res'] = $msg;
                        SC_SMBC::printLog($msg . ' dtb_mdl_smbc_regular_order:  ' . print_r($arrRegularOrder, true), true);
                        $result = false;
                    }
                }
            }
        }

        if ($result) {
            // チェックOKの場合はバッチ実行中に変更
            $arrParams['regular_status'] = MDL_SMBC_REGULAR_STATUS_EXECUTING;
        } else {
            // チェックNGの場合はバッチ実行エラーに変更
            $arrParams['regular_status'] = MDL_SMBC_REGULAR_STATUS_ERROR;
        }
        $arrParams['update_date'] = 'CURRENT_TIMESTAMP';
        $objQuery->update('dtb_mdl_smbc_regular_order',
                          $arrParams,
                          'shoporder_no = ? AND order_id = ? AND bill_no = ?',
                          array($arrRegularOrder['shoporder_no'], $arrRegularOrder['order_id'], $arrRegularOrder['bill_no']));

        return $result;
    }

    /**
     * 請求可否を判定します.
     */
    function checkDemand($arrRow, $arrRegularOrder) {
        $yks_kekka = $arrRow[32];       // 請求可否
        if ($yks_kekka === '1' || $yks_kekka === 1) {
            SC_SMBC::printLog('請求可否NGのため, 請求確定をスキップします ' . print_r($arrRow, true)
                              . ' ' . print_r($arrRegularOrder, true), true);
            return false;
        }
        return true;
    }

    /**
     * 請求確定を送信する.
     *
     * @param array $arrRegularOrder 定期受注情報の配列
     * @return boolean 請求確定の送信が成功した場合 true, バッチエラーの場合 false
     */
    function sendSettled($arrRow, $arrRegularOrder) {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objSmbc = new SC_SMBC();
        $this->initAddParam($objSmbc);
        $objSmbc->addArrParam("kessai_id", 4);
        $objSmbc->addArrParam("shoporder_no", 23);
        $objSmbc->addArrParam("bill_no", 14);

        $arrParams = array(
            'version' => MDL_SMBC_REGULAR_SETTLED_VERSION,
            'bill_method' => MDL_SMBC_CREDIT_BILL_METHOD,
            'shop_cd' => $this->subData['regular_shop_cd'],
            'syuno_co_cd' => $this->subData['regular_syuno_co_cd'],
            'shop_pwd' => $this->subData['regular_shop_pwd'],
            'kessai_id' => MDL_SMBC_CREDIT_KESSAI_ID,
            'shoporder_no' => $arrRegularOrder['shoporder_no'],
            'bill_no' => str_pad($arrRegularOrder['bill_no'], 14, "0", STR_PAD_LEFT)
        );

        $objSmbc->setParam($arrParams);
        $connect_url = ($this->subData['connect_url'] == 'real') ? MDL_SMBC_CREDIT_KAKUTEI_LINK_URL_REAL : MDL_SMBC_CREDIT_KAKUTEI_LINK_URL_TEST;
        $arrResponse = $objSmbc->sendParam($connect_url);

        $arrValues['rescd'] = $arrResponse['rescd'];
        $arrValues['res'] = mb_convert_encoding($arrResponse['res'], CHAR_CODE, 'SJIS-win');
        $result = false;
        if ($arrResponse['rescd'] == MDL_SMBC_RES_OK) {
            // 請求確定済
            $arrValues['regular_status'] = MDL_SMBC_REGULAR_STATUS_SETTLED;
            $result = true;
        } else {
            // 請求確定済みエラーの場合は, ステータス請求確定のままにする
            if ($arrResponse['rescd'] == '943279') {
                $arrValues['regular_status'] = MDL_SMBC_REGULAR_STATUS_SETTLED;
            } else {
                // バッチ実行エラー
                $arrValues['regular_status'] = MDL_SMBC_REGULAR_STATUS_ERROR;
            }
        }

        $arrValues['update_date'] = 'CURRENT_TIMESTAMP';
        $objQuery->update('dtb_mdl_smbc_regular_order',
                          $arrValues,
                          'shoporder_no = ? AND order_id = ? AND bill_no = ?',
                          array($arrRegularOrder['shoporder_no'], $arrRegularOrder['order_id'], $arrRegularOrder['bill_no']));
        return $result;
    }

    /**
     * 与信結果を反映する
     */
    protected function saveAuthorizedResults($arrRow) {
        $shoporder_no = $arrRow[13];
        $bill_no = intval($arrRow[14]);
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objPurchase = new SC_Helper_Purchase_Ex();

        // 最新の請求確定データを取得
        $objQuery->setOrder('create_date DESC');
        $objQuery->setLimit(1);
        $where = 'del_flg = 0 AND shoporder_no = ? AND bill_no = ? AND regular_status = ?';
        $arrRegularOrder = $objQuery->getRow('*', 'dtb_mdl_smbc_regular_order',
                                             $where,
                                             array($shoporder_no, $bill_no,
                                                   MDL_SMBC_REGULAR_STATUS_SETTLED));

        // 受注が存在しない場合はスキップ
        if (SC_Utils_Ex::isBlank($arrRegularOrder)) {
            return false;
        }

        $arrValues['rescd'] = $arrResponse['rescd'];
        $arrValues['res'] = mb_convert_encoding($arrResponse['res'], CHAR_CODE, 'SJIS-win');

        $order_id = $arrRegularOrder['order_id'];
        // 決済受付番号
        $arrParams['kessai_no'] = $arrRow[12];
        // 与信結果
        if ($arrRow[20] === '0' || $arrRow[20] === '1') {
            // 与信OK の場合は, ステータスを与信OKに変更
            $arrParams['regular_status'] = MDL_SMBC_REGULAR_STATUS_COMPLETED;
        } else {
            // 与信NG の場合は, ステータスを与信NGに変更
            $arrParams['regular_status'] = MDL_SMBC_REGULAR_STATUS_DENIED;
        }
        $arrParams['update_date'] = 'CURRENT_TIMESTAMP';
        $objQuery->update('dtb_mdl_smbc_regular_order',
                          $arrParams,
                          'shoporder_no = ? AND order_id = ? AND bill_no = ?',
                          array($shoporder_no, $order_id, $bill_no));

        $where = 'del_flg = 0 AND shoporder_no = ? AND bill_no = ? AND order_id = ?';
        $arrRegularOrder = $objQuery->getRow('*', 'dtb_mdl_smbc_regular_order',
                                             $where,
                                             array($shoporder_no, $bill_no, $order_id));

        return $arrRegularOrder;
    }

    /**
     * dtb_order 及び関連のレコードを登録する.
     */
    public function createNewOrder($arrRow, $arrRegularOrder) {
        // 有効性結果(最新)
        $yks_taisho_flg = $arrRow[31];

        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->begin();
        $objPurchase = new SC_Helper_Purchase_Ex();

        $old_order_id = $arrRegularOrder['order_id'];
        $new_order_id = $objPurchase->getNextOrderID();
        $arrOldOrder = $objPurchase->getOrder($old_order_id);
        $arrOrderTemp = $objPurchase->getOrderTemp($arrOldOrder['order_temp_id']);

        // dtb_order_temp を作成
        $arrOrder = $objQuery->extractOnlyColsOf('dtb_order_temp', $arrOrderTemp);
        $arrOrder['order_id'] = $new_order_id;
        $arrOrder['order_temp_id'] = SC_Utils_Ex::sfGetUniqRandomId();
        $arrOrder['status'] = ORDER_NEW; // ステータス変更は後でするため, ここでは新規受付で受注を生成する

        $arrOrder['memo05'] = $this->subData['regular_product_type_id'];
            /* 使用しない
            $arrOrder['memo01'] = '';
            $arrOrder['memo02'] = '';
            $arrOrder['memo03'] = '';
            $arrOrder['memo04'] = '';
            $arrOrder['memo05'] = '';
            $arrOrder['memo06'] = '';
            $arrOrder['memo07'] = '';
            $arrOrder['memo08'] = '';
            $arrOrder['memo09'] = '';
            $arrOrder['memo10'] = '';
            */
        $arrOrder['use_point'] = '0';   // 初回購入はポイント使用可, 2回目以降はポイント使用不可
        $arrOrder['payment_total'] = $arrOrder['total']; // ポイント使用不可なので total の金額を使用する
        $arrOrder['create_date'] = 'CURRENT_TIMESTAMP';
        $arrOrder['update_date'] = 'CURRENT_TIMESTAMP';
        $arrOrder['del_flg'] = '0';
        $objQuery->insert('dtb_order_temp', $arrOrder);

        // dtb_order を作成
        $arrOrder['commit_date'] = '';
        $arrOrder['payment_date'] = '';
        $arrOrder = $objQuery->extractOnlyColsOf('dtb_order', $arrOrder);


        // dtb_order_detail を作成
        $arrDetails = $objPurchase->getOrderDetail($old_order_id);
        foreach ($arrDetails as $arrDetail) {
            $arrDetail['order_detail_id'] = $objQuery->nextVal('dtb_order_detail_order_detail_id');
            $arrDetail['order_id'] = $new_order_id;
            $arrDetail = $objQuery->extractOnlyColsOf('dtb_order_detail', $arrDetail);
            $objQuery->insert('dtb_order_detail', $arrDetail);
        }

        // dtb_shipping を作成
        $arrShippings = $objPurchase->getShippings($old_order_id);
        foreach ($arrShippings as $arrShipping) {
            $arrShipping['order_id'] = $new_order_id;
            $arrShipping['shipping_commit_date'] = '';
            $arrShipping['create_date'] = 'CURRENT_TIMESTAMP';
            $arrShipping['update_date'] = 'CURRENT_TIMESTAMP';
            $arrShipping = $objQuery->extractOnlyColsOf('dtb_shipping', $arrShipping);
            $objQuery->insert('dtb_shipping', $arrShipping);

            // dtb_shipment_item を作成
            $arrShipItems = $objPurchase->getShipmentItems($old_order_id, $arrShipping['shipping_id']);
            foreach ($arrShipItems as $arrShipItem) {
                $arrShipItem['shipping_id'] = $arrShipping['shipping_id'];
                $arrShipItem['order_id'] = $new_order_id;
                $arrShipItem = $objQuery->extractOnlyColsOf('dtb_shipment_item', $arrShipItem);
                $objQuery->insert('dtb_shipment_item', $arrShipItem);
            }
        }

        // dtb_mdl_smbc_regular_order を作成
        $arrNewRegularOrder = $arrRegularOrder;
        $arrNewRegularOrder['rescd'] = '';
        $arrNewRegularOrder['res'] = '';
        $arrNewRegularOrder['order_id'] = $new_order_id;
        $arrNewRegularOrder['regular_status'] = MDL_SMBC_REGULAR_STATUS_NONE;
        if (SC_Utils_Ex::isBlank($arrRegularOrder['target_ym'])) {
            // 空の場合は翌月
            $arrNewRegularOrder['target_ym'] = date('Ym', mktime(0, 0, 0, date('m') + 1, 1, date('Y')));
        } else {
            $target_year = substr($arrRegularOrder['target_ym'], 0, 4);
            $target_month = substr($arrRegularOrder['target_ym'], 4, 2);
            $arrNewRegularOrder['target_ym'] = date('Ym', mktime(0, 0, 0, $target_month + 1, 1, $target_year));
        }
        $arrNewRegularOrder['create_date'] = 'CURRENT_TIMESTAMP';
        $arrNewRegularOrder['update_date'] = 'CURRENT_TIMESTAMP';
        $objQuery->insert('dtb_mdl_smbc_regular_order',
                          $objQuery->extractOnlyColsOf('dtb_mdl_smbc_regular_order', $arrNewRegularOrder));

        $arrOrder['note'] = '管理番号: ' . $arrRegularOrder['shoporder_no'] . PHP_EOL;
        $arrOrder['note'] .= '請求確定年月: ' . substr($arrNewRegularOrder['target_ym'], 0, 4) . '/' . substr($arrNewRegularOrder['target_ym'], 4, 2);
        $objQuery->insert('dtb_order', $arrOrder);

        if ($yks_taisho_flg === '0' || $yks_taisho_flg === 0) {
            // 有効性OKの場合は現在受注を入金済みに
            $objPurchase->sfUpdateOrderStatus($old_order_id, ORDER_PRE_END);
            // 次回受注を新規受付に変更
            $objPurchase->sfUpdateOrderStatus($arrOrder['order_id'], ORDER_NEW, $arrOrder['add_point']);
        } else {
            // 有効性NGの場合はキャンセル
            $objPurchase->sfUpdateOrderStatus($arrOrder['order_id'], ORDER_CANCEL);
            // 有効性NGを記録する
            $arrValues = array();
            $arrValues['regular_status'] = MDL_SMBC_REGULAR_STATUS_CHECKNG;
            $arrValues['update_date'] = 'CURRENT_TIMESTAMP';
            $objQuery->update('dtb_mdl_smbc_regular_order',
                              $arrValues,
                              'shoporder_no = ? AND order_id = ? AND bill_no = ?',
                              array($arrRegularOrder['shoporder_no'], $arrRegularOrder['order_id'], $arrRegularOrder['bill_no']));

        }
        $objQuery->commit();
        return $arrOrder;
    }

    /**
     * 定期受注情報を論理削除する.
     * 同時に決済ステーションへ請求終了年月を送信する.
     */
    protected function doDelete($shoporder_no, $bill_no, $order_id) {
        $objSmbc = new SC_SMBC();
        $this->initAddParam($objSmbc);
        $objSmbc->addArrParam("kessai_id", 4);
        $objSmbc->addArrParam("shoporder_no", 23);
        $objSmbc->addArrParam("bill_no", 14);

        $objMdlSMBC = SC_Mdl_SMBC::getInstance();
        $arrModule = $objMdlSMBC->getSubData();

        $objPurchase = new SC_Helper_Purchase_Ex();
        $arrOrder = $objPurchase->getOrder($order_id);

        $arrParams = array(
            'version' => MDL_SMBC_REGULAR_CHANGE_VERSION,
            'bill_method' => MDL_SMBC_CREDIT_BILL_METHOD,
            'shop_cd' => $this->subData['regular_shop_cd'],
            'syuno_co_cd' => $this->subData['regular_syuno_co_cd'],
            'shop_pwd' => $this->subData['regular_shop_pwd'],
            'kessai_id' => MDL_SMBC_CREDIT_KESSAI_ID,
            'shoporder_no' => $shoporder_no,
            'bill_no' => str_pad($bill_no, 14, "0", STR_PAD_LEFT),
            'seikyuu_kingaku1' => $arrOrder['payment_total'],
            'seikyuu_kingaku2' => $arrOrder['payment_total'],
            'seikyuu_shuryo_ym' => date('Ym', mktime(0, 0, 0, date('m'), 1, date('Y')))
        );

        $objSmbc->setParam($arrParams);
        $connect_url = ($this->subData['connect_url'] == 'real') ? MDL_SMBC_CREDIT_KAKUTEI_LINK_URL_REAL : MDL_SMBC_CREDIT_KAKUTEI_LINK_URL_TEST;
        $arrResponse = $objSmbc->sendParam($connect_url);

        $arrValues['rescd'] = $arrResponse['rescd'];
        $arrValues['res'] = mb_convert_encoding($arrResponse['res'], CHAR_CODE, 'SJIS-win');

        $objQuery = SC_Query_Ex::getSingletonInstance();
        $result = false;
        if ($arrResponse['rescd'] != MDL_SMBC_RES_OK) {
            // バッチ実行エラー
            $arrValues['regular_status'] = MDL_SMBC_REGULAR_STATUS_ERROR;
            $arrValues['update_date'] = 'CURRENT_TIMESTAMP';
            $objQuery->update('dtb_mdl_smbc_regular_order',
                              $arrValues,
                              'shoporder_no = ? AND order_id = ? AND bill_no = ?',
                              array($shoporder_no, $order_id, $bill_no));
            $arrResponse = array('header' => array());
            $arrResponse['header']['rescd'] = $arrValues['rescd'];
            $arrResponse['header']['res'] = $arrValues['res'];
        } else {

            $arrOrder['note'] .= PHP_EOL . '定期購入マスタ削除 (' . date('Y/m/d') . ')';
            $arrOrder['status'] = ORDER_CANCEL;
            $objPurchase->registerOrder($order_id, $arrOrder);

            $objQuery->update('dtb_mdl_smbc_regular_order',
                              array('del_flg' => 1, 'update_date' => 'CURRENT_TIMESTAMP'),
                              'shoporder_no = ?',
                              array($shoporder_no));
            $arrResponse = array('header' => array());
            $arrResponse['header']['rescd'] = 'DELETED';
            $arrResponse['header']['res'] = '受注(' . $shoporder_no . ')を削除しました。';
        }
        return $arrResponse;
    }

    /**
     * 休止間隔を更新する
     */
    protected function doUpdateInterval($order_id,
                                        $regular_interval_from_year,
                                        $regular_interval_from_month,
                                        $regular_interval_to_year,
                                        $regular_interval_to_month) {
        $interval_from = 0;
        $interval_to = 0;
        $target = '';
        if ($regular_interval_from_year && $regular_interval_from_month) {
            $interval_from = mktime(0, 0, 0, $regular_interval_from_month, 1, $regular_interval_from_year);
            $arrValues['regular_interval_from'] = date('Ym', $interval_from);
        } else {
            $arrValues['regular_interval_from'] = '';
        }
        if ($regular_interval_to_year && $regular_interval_to_month) {
            $interval_to = mktime(0, 0, 0, $regular_interval_to_month, 1, $regular_interval_to_year);
            $current = mktime(0, 0, 0, date('m'), 1, date('Y'));
            if ($interval_from > 0 && $interval_from <= $current
                && $interval_to > $current) {
                $target = date('Ym', mktime(0, 0, 0, $regular_interval_to_month + 1, 1, $regular_interval_to_year));
            }
            $arrValues['regular_interval_to'] = date('Ym', $interval_to);
        } else {
            $arrValues['regular_interval_to'] = '';
        }
        if ($interval_from > 0 && $interval_to > 0
            && $interval_from > $interval_to) {
            $arrResponse['header']['rescd'] = 'ERROR';
            $arrResponse['header']['res'] = '休止期間終了年月は、休止期間開始年月よりも大きい年月を選択してください。';
            return $arrResponse;
        }

        $arrValues['target_ym'] = $target;   // 休止期間を更新した場合は対象年月をリセットする.
        $arrValues['update_date'] = 'CURRENT_TIMESTAMP';
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->update('dtb_mdl_smbc_regular_order', $arrValues,
                          'order_id = ?', array($order_id));

        $objPurchase = new SC_Helper_Purchase_Ex();
        $arrOrder = $objPurchase->getOrder($order_id);

        if (SC_Utils_Ex::isBlank($arrValues['regular_interval_from']) && SC_Utils_Ex::isBlank($arrValues['regular_interval_to'])) {
            $arrOrder['note'] .= PHP_EOL . '請求確定休止期間解除';
        } else {
            $arrOrder['note'] .= PHP_EOL . '請求確定休止期間: ';
            if (!SC_Utils_Ex::isBlank($arrValues['regular_interval_from'])) {
                $arrOrder['note'] .= substr($arrValues['regular_interval_from'], 0, 4) . '/' . substr($arrValues['regular_interval_from'], 4, 2);
            }
            $arrOrder['note'] .= '〜';
            if (!SC_Utils_Ex::isBlank($arrValues['regular_interval_to'])) {
                $arrOrder['note'] .= substr($arrValues['regular_interval_to'], 0, 4) . '/' . substr($arrValues['regular_interval_to'], 4, 2);
            }
        }
        $arrOrder['note'] .= ' (' . date('Y/m/d') . ' 変更)';
        if (!SC_Utils_Ex::isBlank($target)) {
            $arrOrder['note'] .= PHP_EOL . '請求確定年月: ' . substr($arrValues['target_ym'], 0, 4) . '/' . substr($arrValues['target_ym'], 4, 2) . ' (' . date('Y/m/d') . ' 変更)';
        }
        $objPurchase->registerOrder($order_id, $arrOrder);

        $arrResponse = array('header' => array());
        $arrResponse['header']['rescd'] = '000000';
        $arrResponse['header']['res'] = '休止期間を変更しました。';
        return $arrResponse;
    }

    protected function doDownloadCsv() {
        $this->objFormParam = new SC_FormParam();
        $this->lfInitParam();
        $this->objFormParam->setParam($_POST);
        $arrRet = $this->objFormParam->getHashArray();
        $prefix = 'settle_csv';
        $tmp_filename = tempnam(CSV_TEMP_REALDIR, $prefix);
        $fp = fopen($tmp_filename, 'w+');
        fputcsv($fp, array('10'));
        $arrResults = $this->lfGetCreditData($arrRet, true);
        foreach ($arrResults as $arrRow) {
            $arrBody = array(
                '20',
                '05',
                '0501',
                $this->subData['regular_shop_cd'],
                $this->subData['regular_syuno_co_cd'],
                '',
                $arrRow['shoporder_no'],
                str_pad($arrRow['bill_no'], 14, "0", STR_PAD_LEFT)
            );
            fputcsv($fp, $arrBody);
        }

        fputcsv($fp, array('80', '5'));
        fclose($fp);
        $file_name = $prefix . date('YmdHis') . '.csv';
        /* HTTPヘッダの出力 */
        Header("Content-disposition: attachment; filename={$file_name}");
        Header("Content-type: application/octet-stream; name={$file_name}");
        Header('Cache-Control: ');
        Header('Pragma: ');
        echo file_get_contents($tmp_filename);
    }

    /**
     * 最新の受注データを取得します.
     */
    protected function getCurrentOrder($arrRow) {
        $shoporder_no = $arrRow[9];
        $bill_no = intval($arrRow[6]);

        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->setOrder('T1.create_date DESC');
        $objQuery->setLimit(1);
        $from = 'dtb_mdl_smbc_regular_order T1 JOIN dtb_order T2 ON T1.order_id = T2.order_id';
        $where = 'T1.del_flg = 0 AND T2.del_flg = 0 AND shoporder_no = ? AND bill_no = ?';
        $arrRegularOrder = $objQuery->getRow('*', $from, $where,
                                             array($shoporder_no, $bill_no));
        return $arrRegularOrder;
    }

    /**
     * 共通項目を初期化します
     */
    protected function initAddParam($objSmbc) {
        $objSmbc->addArrParam("version", 3);
        $objSmbc->addArrParam("bill_method", 2);
        $objSmbc->addArrParam("shop_cd", 7);
        $objSmbc->addArrParam("syuno_co_cd", 8);
        $objSmbc->addArrParam("shop_pwd", 20);
    }
}
?>
