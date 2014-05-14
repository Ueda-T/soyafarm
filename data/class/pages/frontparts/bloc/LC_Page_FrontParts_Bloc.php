<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * ブロック の基底クラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_FrontParts_Bloc.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_FrontParts_Bloc extends LC_Page_Ex {

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        // 開始時刻を設定する。
        $this->timeStart = SC_Utils_Ex::sfMicrotimeFloat();

        $this->tpl_authority = $_SESSION['authority'];

        // ディスプレイクラス生成
        $this->objDisplay = new SC_Display_Ex();

        // プラグインクラス生成
        $this->objPlugin = new SC_Helper_Plugin_Ex();
        $this->objPlugin->preProcess($this);
        $this->setTplMainpage($this->blocItems['tpl_path']);

        // トランザクショントークンの検証と生成
        $this->setTokenTo();
    }

    /**
     * ブロックファイルに応じて tpl_mainpage を設定する
     *
     * @param string $bloc_file ブロックファイル名
     * @return void
     */
    function setTplMainpage($bloc_file) {
        if (SC_Utils_Ex::isAbsoluteRealPath($bloc_file)) {
            $this->tpl_mainpage = $bloc_file;
        } else {
            $this->tpl_mainpage = SC_Helper_PageLayout_Ex::getTemplatePath($this->objDisplay->detectDevice()) . BLOC_DIR . $bloc_file;
        }

        $this->setTemplate($this->tpl_mainpage);
        $debug_message = "block：" . $this->tpl_mainpage . "\n";
        GC_Utils_Ex::gfDebugLog($debug_message);
    }
}
?>
