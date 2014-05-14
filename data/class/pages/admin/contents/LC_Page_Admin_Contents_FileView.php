<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';
require_once CLASS_EX_REALDIR . 'helper_extends/SC_Helper_FileManager_Ex.php';

/**
 * ファイル表示 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_Contents_FileView.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Admin_Contents_FileView extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
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
        switch($this->getMode()){
            default:
                // フォーム操作クラス
                $objFormParam = new SC_FormParam_Ex();
                // パラメーター情報の初期化
                $this->lfInitParam($objFormParam);
                $objFormParam->setParam($_GET);
                $objFormParam->convParam();

                // 表示するファイルにエラーチェックを行う
                if ($this->checkErrorDispFile($objFormParam)) {
                    $this->execFileView($objFormParam);
                } else {
                    SC_Utils_Ex::sfDispError('');
                }
                exit;
            break;
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
     * 初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParamインスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam("ファイル名", 'file', MTEXT_LEN, 'a', array("EXIST_CHECK"));
    }

    /**
     * 表示するファイルにエラーチェックを行う
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return boolen $file_check_flg エラーチェックの結果
     */
    function checkErrorDispFile($objFormParam) {
        $file_check_flg = false;

        // FIXME パスのチェック関数が必要
        $file = $objFormParam->getValue('file');

        if (!preg_match('|\./|', $file)) {
            $file_check_flg = true;
        }

        return $file_check_flg;
    }

    /**
     * ファイル内容を表示する
     * 
     * @return void
     */
    function execFileView($objFormParam) {
        $file = $objFormParam->getValue('file');

        // ソースとして表示するファイルを定義(直接実行しないファイル)
        $arrViewFile = array(
            'html',
            'htm',
            'tpl',
            'php',
            'css',
            'js',
        );

        $extension = pathinfo($file, PATHINFO_EXTENSION);

        if (in_array($extension, $arrViewFile)) {
            $objFileManager = new SC_Helper_FileManager_Ex();
            // ファイルを読み込んで表示
            header("Content-type: text/plain\n\n");
            echo $objFileManager->sfReadFile(USER_REALDIR . $file);
        } else {
            SC_Response_Ex::sendRedirect(USER_URL . $file);
        }
    }
}
?>
