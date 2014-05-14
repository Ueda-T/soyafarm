<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * パラメーター設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_System_Parameter.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Admin_System_Parameter extends LC_Page_Admin_Ex {

    // {{{ properties

    /** 定数キーとなる配列 */
    var $arrKeys;

    /** 定数コメントとなる配列 */
    var $arrComments;

    /** 定数値となる配列 */
    var $arrValues;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'system/parameter.tpl';
        $this->tpl_subno = 'parameter';
        $this->tpl_mainno = 'system';
        $this->tpl_maintitle = 'システム設定';
        $this->tpl_subtitle = 'パラメーター設定';
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
        $masterData = new SC_DB_MasterData_Ex();

        // キーの配列を生成
        $this->arrKeys = $this->getParamKeys($masterData);

        switch ($this->getMode()) {
        case 'update':
            // データの引き継ぎ
            $this->arrForm = $_POST;

            // エラーチェック
            $this->arrErr = $this->errorCheck($this->arrKeys, $this->arrForm);
            // エラーの無い場合は update
            if (empty($this->arrErr)) {
                $this->update($this->arrKeys, $this->arrForm);
                $this->tpl_onload = "window.alert('パラメーターの設定が完了しました。');";
            } else {
                $this->arrValues = SC_Utils_Ex::getHash2Array($this->arrForm,
                                                              $this->arrKeys);
                $this->tpl_onload = "window.alert('エラーが発生しました。入力内容をご確認下さい。');";
            }
            break;
        default:
            break;
        }

        if (empty($this->arrErr)) {
            $this->arrValues = SC_Utils_Ex::getHash2Array(
                                       $masterData->getDBMasterData("mtb_constants"));
        }

        // コメント, 値の配列を生成
        $this->arrComments = SC_Utils_Ex::getHash2Array(
                                     $masterData->getDBMasterData("mtb_constants",
                                             array('id', 'remarks', 'rank')));

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
     * パラメーター情報を更新する.
     *
     * 画面の設定値で mtb_constants テーブルの値とキャッシュを更新する.
     *
     * @access private
     * @return void
     */
    function update(&$arrKeys, &$arrForm) {
        $data = array();
        $masterData = new SC_DB_MasterData_Ex();
        foreach ($arrKeys as $key) {
            $data[$key] = $arrForm[$key];
        }

        // DBのデータを更新
        $masterData->updateMasterData('mtb_constants', array(), $data);

        // キャッシュを生成
        $masterData->createCache('mtb_constants', array(), true, array('id', 'remarks'));
    }

    /**
     * エラーチェックを行う.
     *
     * @access private
     * @param array $arrForm $_POST 値
     * @return void
     */
    function errorCheck(&$arrKeys, &$arrForm) {
        $objErr = new SC_CheckError_Ex($arrForm);
        for ($i = 0; $i < count($arrKeys); $i++) {
            $objErr->doFunc(array($arrKeys[$i],
                                  $arrForm[$arrKeys[$i]]),
                            array("EXIST_CHECK_REVERSE", "EVAL_CHECK"));
        }
        return $objErr->arrErr;
    }

    /**
     * パラメーターのキーを配列で返す.
     *
     * @access private
     * @return array パラメーターのキーの配列
     */
    function getParamKeys(&$masterData) {
        $keys = array();
        $i = 0;
        foreach ($masterData->getDBMasterData("mtb_constants") as $key => $val) {
            $keys[$i] = $key;
            $i++;
        }
        return $keys;
    }
}
?>
