<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * ログ のページクラス.
 *
 * @package Page
 * @author
 * @version $Id: LC_Page_Admin_System_Log.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Admin_System_Log extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'system/log.tpl';
        $this->tpl_subno    = 'log';
        $this->tpl_mainno   = 'system';
        $this->tpl_maintitle = 'システム設定';
        $this->tpl_subtitle = 'ログ表示';
        $this->line_max     = 50;
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
        $objFormParam = new SC_FormParam;

        // パラメーター情報初期化
        $this->lfInitParam($objFormParam);

        // POST値をセット
        $objFormParam->setParam($_POST);

        if (SC_Utils_Ex::sfIsInt($tmp = $objFormParam->getValue('line'))) {
            $this->line_max = $tmp;
        }

        $this->tpl_ec_log = $this->getEccubeLog();
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
     * パラメーターの初期化.
     *
     * @return object SC_FormParam インスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        $objFormParam->addParam('line_max', 'line_max', INT_LEN, '', array('NUM_CHECK', 'MAX_LENGTH_CHECK', 'EXIST_CHECK'));
    }

    /**
     * EC-CUBE ログを取得する.
     *
     * @return array $arrLogs 取得したログ
     */
    function getEccubeLog() {

        $index = 0;
        $arrLogs = array();
        for ($gen = 0 ; $gen <= MAX_LOG_QUANTITY; $gen++) {
            $path = LOG_REALFILE;
            if ($gen != 0) {
                $path .= ".$gen";
            }

            // ファイルが存在しない場合、前世代のログへ
            if (!file_exists($path)) continue;

            $arrLogTmp = array_reverse(file($path));

            $arrBodyReverse = array();
            foreach ($arrLogTmp as $line) {
                $line = chop($line);
                if (preg_match('/^(\d+\/\d+\/\d+ \d+:\d+:\d+) \[([^\]]+)\] (.*)$/', $line, $arrMatch)) {
                    $arrLogLine = array();
                    // 日時
                    $arrLogLine['date'] = $arrMatch[1];
                    // パス
                    $arrLogLine['path'] = $arrMatch[2];
                    // 内容
                    $arrBodyReverse[] = $arrMatch[3];
                    $arrLogLine['body'] = implode("\n", array_reverse($arrBodyReverse));
                    $arrBodyReverse = array();

                    $arrLogs[] = $arrLogLine;

                    // 上限に達した場合、処理を抜ける
                    if (count($arrLogs) >= $this->line_max) break 2;
                } else {
                    // 内容
                    $arrBodyReverse[] = $line;
                }
            }
        }
        return $arrLogs;
    }
}
