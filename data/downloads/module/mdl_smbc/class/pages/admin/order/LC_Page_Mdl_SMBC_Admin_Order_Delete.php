<?php
// {{{ requires
require_once(CLASS_REALDIR . "pages/LC_Page.php");
require_once(MODULE_REALDIR . 'mdl_smbc/inc/include.php');
require_once(MDL_SMBC_CLASS_PATH . 'SC_Mdl_SMBC.php');
require_once(MDL_SMBC_CLASS_PATH . 'SC_SMBC.php');
require_once(MDL_SMBC_CLASS_PATH . 'SC_SMBC_Data.php');

/**
 * カード情報削除管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Mdl_SMBC_Admin_Order_Delete extends LC_Page {

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
        $this->tpl_mainpage = MDL_SMBC_TEMPLATE_PATH . 'admin/order/delete.tpl';
        $this->tpl_subnavi = TEMPLATE_ADMIN_REALDIR . 'order/subnavi.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'delete';
        $this->tpl_pager = TEMPLATE_ADMIN_REALDIR . 'pager.tpl';
        $this->tpl_subtitle = 'カード情報削除管理';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData("mtb_pref");
        $this->arrPageMax = $masterData->getMasterData("mtb_page_max");
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->objSmbc = new SC_SMBC();
        $objView = new SC_AdminView();
        $objDate = new SC_Date();
        // 登録・更新日検索用
        $objDate->setStartYear(RELEASE_YEAR);
        $objDate->setEndYear(DATE("Y"));
        $this->arrYear = $objDate->getYear();   //　日付プルダウン設定
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();
        $this->objDate = $objDate;

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
        foreach ($_POST as $key => $val) {
            if (ereg("^card_", $key)) {
                $this->arrCard[$key] = $val;
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
            case 'check':
                // 照会
                $this->lfCardInfo("02", 2);
                break;
            case 'delete':
                // 削除
                $this->lfCardInfo("03", 1);
                break;
            default:
                break;
        }

        // 入力値の変換
        $this->objFormParam->convParam();
        $this->arrErr = $this->lfCheckError();
        $arrRet = $this->objFormParam->getHashArray();

        //検索結果の取得と表示
        if (count($this->arrErr) == 0) {
            $this->lfGetCreditData($arrRet);
        }

        // 入力値の取得
        $this->arrForm = $this->objFormParam->getFormParamList();
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

    /**
     * 決済ステーションへ問い合わせ
     *
     * @param $action, $retParam
     * @return void
     */
    function lfCardInfo($action, $retParam) {

        $this->arrCard['card_'.$this->arrForm['customer_id']] = 1;

        if(!empty($this->arrForm['customer_id'])){
            $objQuery = new SC_Query();

            $select ="DISTINCT payment_id";
            $from = "dtb_mdl_smbc_order INNER JOIN dtb_order USING(order_id)";
            $where = "del_flg = 0 AND customer_id = ?";
            $arrval[] = $this->arrForm['customer_id'];

            //検索結果の取得
            $order_data = $objQuery->select($select, $from, $where, $arrval);
        }
        if(!empty($order_data[0]['payment_id'])){
            $this->objSmbcData = new SC_SMBC_Data();

            $arrParam = $this->objSmbcData->getModuleMasterData($order_data[0]['payment_id']);
            $arrParam['customer_id'] = $this->arrForm['customer_id'];
            // 照会 or 削除
            $arrResponse = $this->objSmbcData->checkCardInfo($arrParam, $action);

            if($arrResponse['rescd'] == MDL_SMBC_RES_OK && $arrResponse['crd_cnt'] > 0 ){
                $this->arrCard['card_'.$this->arrForm['customer_id']] = $retParam;

            // 加盟店顧客IDが登録されていないエラーは「登録なし」の正常と見なすためエラーにしない
            }elseif($arrResponse['rescd'] != MDL_SMBC_RES_OK && $arrResponse['rescd'] != MDL_SMBC_RES_IF_ERROR_KMT_KOK_ID){
                $this->arrCard['card_'.$this->arrForm['customer_id']] = 0;
                $this->lfDispError($arrResponse);
            }
        }
    }

    /*
     * クレジット請求一覧の取得
     */
    function lfGetCreditData($arrRet){
        $dbFactory = SC_DB_DBFactory_Ex::getInstance();
        $select = "*";
        $from = "dtb_customer";

        $where = "del_flg = 0";

        foreach ($arrRet as $key => $val) {
            if($val == "") {
                continue;
            }

            switch ($key) {
            case 'search_customer_id':
                $where .= " AND customer_id = ?";
                $arrval[] = $val;
                break;
            case 'search_pref':
                $where .= " AND pref = ?";
                $arrval[] = $val;
                break;
            case 'search_order_name':
                $where .= " AND " . $dbFactory->concatColumn(array("name01", "name02")) . " LIKE ?";
                $nonsp_val = mb_ereg_replace("[ 　]+","",$val);
                $arrval[] = "%$nonsp_val%";
                break;
            case 'search_order_kana':
                $where .= " AND " . $dbFactory->concatColumn(array("kana01", "kana02")) . " LIKE ?";
                $nonsp_val = mb_ereg_replace("[ 　]+","",$val);
                $arrval[] = "%$nonsp_val%";
                break;
            case 'search_tel':
                $where .= " AND " . $dbFactory->concatColumn(array("tel01", "tel02", "tel03")) . " LIKE ?";
                $nonsp_val = ereg_replace("-", "",$val);
                $arrval[] = "%$nonsp_val%";
                break;
            case 'search_email':
                $where .= " AND email ILIKE ?";
                $nonsp_val = mb_ereg_replace("[ 　]+","",$val);
                $arrval[] = "%$nonsp_val%";
                break;
            case 'search_syear':
                $date = SC_Utils_Ex::sfGetTimestamp($_POST['search_syear'], $_POST['search_smonth'], $_POST['search_sday']);
                $where.= " AND update_date >= ?";
                $arrval[] = $date;
                break;
            case 'search_eyear':
                $date = SC_Utils_Ex::sfGetTimestamp($_POST['search_eyear'], $_POST['search_emonth'], $_POST['search_eday'], true);
                $where.= " AND update_date <= ?";
                $arrval[] = $date;
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
        $order = "customer_id DESC";
        $objQuery->setorder($order);

        $sql = $objQuery->getsql($select, $from, $where);

        //検索結果の取得
        $this->search_data = $objQuery->getall($sql, $arrval);
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
        $this->arrError['customer_id'] = $this->arrForm['customer_id'];
    }

    /**
     * クレジット請求確定連携の送信データ項目の配列の初期化
     *
     * @param void
     * @return void
     */
    function lfInitArrParam() {
        $this->objSmbc->addArrParam("version", 3);
        $this->objSmbc->addArrParam("shori_kbn", 2);
        $this->objSmbc->addArrParam("shop_cd", 7);
        $this->objSmbc->addArrParam("syuno_co_cd", 8);
        $this->objSmbc->addArrParam("auth_pwd", 20);
        $this->objSmbc->addArrParam("kmt_kok_id", 256);
    }

    /* パラメータ情報の初期化 */
    function lfInitParam() {
        $this->objFormParam->addParam("顧客コード", "search_customer_id", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("都道府県", "search_pref", 2, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("顧客名", "search_order_name", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("顧客名(カナ)", "search_order_kana", STEXT_LEN, "KVCa", array("KANA_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("メールアドレス", "search_email", STEXT_LEN, "a", array("EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("表示件数", "search_page_max", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("電話番号", "search_tel", TEL_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("開始日", "search_syear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("開始日", "search_smonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("開始日", "search_sday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("終了日", "search_eyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("終了日", "search_emonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("終了日", "search_eday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
    }

    /* 入力内容のチェック */
    function lfCheckError() {
        // 入力データを渡す。
        $arrRet = $this->objFormParam->getHashArray();
        $objErr = new SC_CheckError($arrRet);
        $objErr->arrErr = $this->objFormParam->checkError();

        // 特殊項目チェック
        $objErr->doFunc(array("開始日", "search_syear", "search_smonth", "search_sday"), array("CHECK_DATE"));
        $objErr->doFunc(array("終了日", "search_eyear", "search_emonth", "search_eday"), array("CHECK_DATE"));
        $objErr->doFunc(array("開始日", "終了日", "search_syear", "search_smonth", "search_sday", "search_eyear", "search_emonth", "search_eday"), array("CHECK_SET_TERM"));

        return $objErr->arrErr;
    }
}
?>
