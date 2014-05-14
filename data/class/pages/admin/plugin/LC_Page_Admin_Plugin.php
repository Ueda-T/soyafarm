<?php

require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * プラグイン管理のページクラス
 *
 * @package Page
 * @author Seasoft 塚田将久
 * @version $Id: LC_Page_Admin_Plugin.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Admin_Plugin extends LC_Page_Admin_Ex {

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        if (DEBUG_LOAD_PLUGIN !== true) SC_Utils_Ex::sfDispException('プラグインは有効化されていない'); // XXX 開発途上対応
        parent::init();

        $this->tpl_mainpage = 'plugin/index.tpl';
        $this->tpl_mainno   = 'plugin';
        $this->tpl_subno    = 'index';
        $this->tpl_subtitle = 'プラグイン管理';
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
        $this->loadPluginsList();
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
     * プラグインの一覧を読み込む
     *
     * @return void
     */
    function loadPluginsList() {
        $plugins = array();
        $this->arrInstalledPlugin = array();
        $this->arrInstallablePlugin = array();

        $d = dir(PLUGIN_REALDIR);
        while (false !== ($entry = $d->read())) {
            if ($entry == '.') continue;
            if ($entry == '..') continue;
            if (!is_dir($d->path . $entry)) continue;

            $plugins[$entry]['dir_exists'] = true;
        }
        $d->close();

        $pluginsXml = SC_Utils_Ex::sfGetPluginsXml();
        foreach ($pluginsXml->plugin as $plugin) {
            $plugins[(string)$plugin->path]['installed'] = true;
        }

        foreach ($plugins as $path=>$plugin) {
            $plugin['info'] = SC_Utils_Ex::sfGetPluginInfoArray($path);
            $plugin['path'] = $path;
            if ($plugin['installed']) {
                $this->arrInstalledPlugin[] = $plugin;
            } else {
                $this->arrInstallablePlugin[] = $plugin;
            }
        }

    }
}
?>
