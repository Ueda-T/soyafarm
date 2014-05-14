<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * 特定商取引法 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_Basis_Tradelaw.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Admin_Basis_Tradelaw extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'basis/tradelaw.tpl';
        $this->tpl_subno = 'tradelaw';
        $this->tpl_mainno = 'basis';
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrTAXRULE = $masterData->getMasterData("mtb_taxrule");
        $this->tpl_maintitle = '基本情報管理';
        $this->tpl_subtitle = '特定商取引法';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $objDb = new SC_Helper_DB_Ex();

        $objFormParam = new SC_FormParam_Ex();
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_POST);

        $cnt = $objDb->sfGetBasisCount();
        if ($cnt > 0) {
            $this->tpl_mode = 'update';
        } else {
            $this->tpl_mode = 'insert';
        }

        if(!empty($_POST)) {
            // 入力値の変換
            $objFormParam->convParam();
            $this->arrErr = $this->lfCheckError($objFormParam);

            if(count($this->arrErr) == 0) {
                switch($this->getMode()) {
                case 'update':
                    $this->lfUpdateData($objFormParam->getHashArray()); // 既存編集
                    break;
                case 'insert':
                    $this->lfInsertData($objFormParam->getHashArray()); // 新規作成
                    break;
                default:
                    break;
                }
                // 再表示
                //sfReload();
                $this->tpl_onload = "window.alert('特定商取引法の登録が完了しました。');";
            }
        } else {
            $arrCol = $objFormParam->getKeyList(); // キー名一覧を取得
            $col    = SC_Utils_Ex::sfGetCommaList($arrCol);
            $arrRet = $objDb->sfGetBasisData(true, $col);
            $objFormParam->setParam($arrRet);
        }
        $this->arrForm = $objFormParam->getFormParamList();
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /* パラメーター情報の初期化 */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam("販売事業者", "law_company", STEXT_LEN, 'KVa', array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("運営責任者", "law_manager", STEXT_LEN, 'KVa', array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("郵便番号1", "law_zip01", ZIP01_LEN, 'n', array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $objFormParam->addParam("郵便番号2", "law_zip02", ZIP02_LEN, 'n', array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $objFormParam->addParam("都道府県", "law_pref", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("住所1", "law_addr01", MTEXT_LEN, 'KVa', array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("住所2", "law_addr02", MTEXT_LEN, 'KVa', array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("電話番号1", "law_tel01", TEL_ITEM_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $objFormParam->addParam("電話番号2", "law_tel02", TEL_ITEM_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $objFormParam->addParam("電話番号3", "law_tel03", TEL_ITEM_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $objFormParam->addParam("FAX番号1", "law_fax01", TEL_ITEM_LEN, 'n', array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $objFormParam->addParam("FAX番号2", "law_fax02", TEL_ITEM_LEN, 'n', array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $objFormParam->addParam("FAX番号3", "law_fax03", TEL_ITEM_LEN, 'n', array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $objFormParam->addParam("メールアドレス", "law_email", null, 'KVa', array("EXIST_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK"));
        $objFormParam->addParam('URL', "law_url", STEXT_LEN, 'KVa', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "URL_CHECK"));
        $objFormParam->addParam("送料", "law_term01", LTEXT_LEN, 'KVa', array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("注文方法", "law_term02", LTEXT_LEN, 'KVa', array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("代金の支払方法", "law_term03", LTEXT_LEN, 'KVa', array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("代金の支払時期", "law_term04", LTEXT_LEN, 'KVa', array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("商品の引渡時期", "law_term05", LTEXT_LEN, 'KVa', array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("返品・交換・返金", "law_term06", LTEXT_LEN, 'KVa', array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
    }

    function lfUpdateData($sqlval) {
        $sqlval['update_date'] = 'Now()';
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // UPDATEの実行
        $ret = $objQuery->update("dtb_baseinfo", $sqlval);
    }

    function lfInsertData($sqlval) {
        $sqlval['update_date'] = 'Now()';
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // INSERTの実行
        $ret = $objQuery->insert("dtb_baseinfo", $sqlval);
    }

    /* 入力内容のチェック */
    function lfCheckError(&$objFormParam) {
        // 入力データを渡す。
        $arrRet =  $objFormParam->getHashArray();
        $objErr = new SC_CheckError_Ex($arrRet);
        $objErr->arrErr = $objFormParam->checkError();

        // 電話番号チェック
        $objErr->doFunc(array('TEL', "law_tel01", "law_tel02", "law_tel03"), array("TEL_CHECK"));
        $objErr->doFunc(array('FAX', "law_fax01", "law_fax02", "law_fax03"), array("TEL_CHECK"));
        $objErr->doFunc(array("郵便番号", "law_zip01", "law_zip02"), array("ALL_EXIST_CHECK"));

        return $objErr->arrErr;
    }
}
?>
