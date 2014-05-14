<?php

require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * プラグインのインストールのページクラス
 *
 * FIXME インストール直後のレンダリング時点では、上部ナビに反映されない
 * TODO Transaction Token を使用する
 *
 * @package Page
 * @author Seasoft 塚田将久
 * @version $Id: LC_Page_Admin_Plugin_Install.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Admin_Plugin_Install extends LC_Page_Admin_Ex {

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        if (DEBUG_LOAD_PLUGIN !== true) SC_Utils_Ex::sfDispException('プラグインは有効化されていない'); // XXX 開発途上対応
        parent::init();

        $this->tpl_mainpage = 'plugin/install.tpl';
        $this->tpl_mainno   = 'plugin';
        $this->tpl_subno    = 'install';
        $this->tpl_subtitle = 'プラグインのインストール';
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
        // パラメーター管理クラス
        $this->objFormParam = new SC_FormParam_Ex();
        // パラメーター情報の初期化
        $this->lfInitParam();
        // POST値の取得
        $this->objFormParam->setParam($_REQUEST);
        // 入力情報を渡す
        $this->arrForm = $this->objFormParam->getHashArray();
        $this->arrErr = $this->objFormParam->checkError();
        if (count($this->arrErr) == 0) {
        // インストール
            $this->lfInstall($this->arrForm['path']);
            $this->tpl_result = '完了しました。';
        } else {
            SC_Utils_Ex::sfDispException();
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
     * インストール
     *
     * @return void
     */
    function lfInstall($path) {

        // アンインストール SQL を実行 (クリーンアップ)
        SC_Helper_DB_Ex::sfExecSqlByFile(PLUGIN_REALDIR . "$path/sql/uninstall.sql");

        // インストール SQL を実行
        SC_Helper_DB_Ex::sfExecSqlByFile(PLUGIN_REALDIR . "$path/sql/install.sql");

        // プラグイン XML に追加
        $this->lfAddToPluginsXml($path);
    }

    /**
     * プラグイン XML に追加
     *
     * @return void
     */
    function lfAddToPluginsXml($path) {
        $pluginsXml = SC_Utils_Ex::sfGetPluginsXml();
        $addPluginXml = $pluginsXml->addChild('plugin');
        $addPluginXml->addChild('path', $path);
        $arrPluginInfo = SC_Utils_Ex::sfGetPluginInfoArray($path);
        $addPluginXml->addChild('name', $arrPluginInfo['name']);
        SC_Utils_Ex::sfPutPluginsXml($pluginsXml);
    }

    /**
     * パラメーター情報の初期化
     *
     * @return void
     */
    function lfInitParam() {
        $this->objFormParam->addParam('プラグインのパス', 'path', STEXT_LEN, '', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
    }
}
?>
