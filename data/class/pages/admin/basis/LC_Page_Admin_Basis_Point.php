<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * ポイント設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_Basis_Point.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Admin_Basis_Point extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'basis/point.tpl';
        $this->tpl_subno = 'point';
        $this->tpl_mainno = 'basis';
        $this->tpl_maintitle = '基本情報管理';
        $this->tpl_subtitle = 'ポイント設定';
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

        // パラメーター管理クラス
        $objFormParam = new SC_FormParam_Ex();
        // パラメーター情報の初期化
        $this->lfInitParam($objFormParam);
        // POST値の取得
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
            $this->arrErr = $objFormParam->checkError();

            if(count($this->arrErr) == 0) {
                switch($this->getMode()) {
                case 'update':
                    // 既存編集
                    $this->lfUpdateData($objFormParam->getHashArray());
                    break;
                case 'insert':
                    // 新規作成
                    $this->lfInsertData($objFormParam->getHashArray());
                    break;
                default:
                    break;
                }
                // 再表示
                //sfReload();
                $this->tpl_onload = "window.alert('ポイント設定が完了しました。');";
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
        $objFormParam->addParam("ポイント付与率", "point_rate", PERCENTAGE_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("会員登録時付与ポイント", "welcome_point", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
    }

    function lfUpdateData($post) {
        // 入力データを渡す。
        $sqlval = $post;
        $sqlval['update_date'] = 'Now()';
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // UPDATEの実行
        $ret = $objQuery->update("dtb_baseinfo", $sqlval);
    }

    function lfInsertData($post) {
        // 入力データを渡す。
        $sqlval = $post;
        $sqlval['update_date'] = 'Now()';
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // INSERTの実行
        $ret = $objQuery->insert("dtb_baseinfo", $sqlval);
    }
}
?>
