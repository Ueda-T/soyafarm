<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * メール配信履歴 のページクラス.
 *
 * @package Page
 * @author IQUEVE CO.,LTD.
 * @version $Id: LC_Page_Admin_Mail_History.php 106 2012-04-24 02:38:55Z hira $
 */
class LC_Page_Admin_Mail_History extends LC_Page_Admin_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'mail/history.tpl';
        /* メニュー移動 (メルマガ管理 → 顧客関連)
        $this->tpl_mainno = 'mail';
        */
        $this->tpl_mainno   = 'customer';
        $this->tpl_subnavi  = 'customer/subnavi.tpl';
        $this->tpl_subno    = 'history';
        $this->tpl_maintitle = 'メルマガ管理';
        $this->tpl_subtitle = '配信履歴';
        $this->tpl_pager = 'pager.tpl';
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
        switch ($this->getMode()) {
        case 'delete':
            if (SC_Utils_Ex::sfIsInt($_GET['send_id'])) {
                // 削除時
                $this->lfDeleteHistory($_GET['send_id']);
                $this->objDisplay->reload(null, true);
            }
            break;
        default:
            break;
        }

        list($this->tpl_linemax, $this->arrDataList, $this->arrPagenavi) = $this->lfDoSearch($_POST['search_pageno']);
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
     * 実行履歴の取得
     * 
     * @param integer $search_pageno 表示したいページ番号
     * @return array( integer 全体件数, mixed メール配信データ一覧配列, mixed SC_PageNaviオブジェクト)
     */
    function lfDoSearch($search_pageno = 1) {

        // 引数の初期化
        if(SC_Utils_Ex::sfIsInt($search_pageno)===false) $search_pageno = 1;
        // 
        $objSelect =& SC_Query_Ex::getSingletonInstance();    // 一覧データ取得用
        $objQuery =& SC_Query_Ex::getSingletonInstance();    // 件数取得用

        // 該当全体件数の取得
        $sql_count =<<<EOF
SELECT
    COUNT(*)
FROM
    dtb_send_history
WHERE
    del_flg = 0
EOF;
        $linemax = $objQuery->getOne($sql_count);

        // ページ送りの取得
        $offset = SEARCH_PMAX * ($search_pageno - 1);
        $limit = SEARCH_PMAX;

        $sql =<<<EOF
SELECT
    *,
    (SELECT COUNT(*) FROM dtb_send_customer WHERE dtb_send_customer.send_id = dtb_send_history.send_id) AS count_all,
    (SELECT COUNT(*) FROM dtb_send_customer WHERE dtb_send_customer.send_id = dtb_send_history.send_id AND send_flag = 1) AS count_sent,
    (SELECT COUNT(*) FROM dtb_send_customer WHERE dtb_send_customer.send_id = dtb_send_history.send_id AND send_flag = 2) AS count_error,
    (SELECT COUNT(*) FROM dtb_send_customer WHERE dtb_send_customer.send_id = dtb_send_history.send_id AND send_flag IS NULL) AS count_unsent
FROM
    dtb_send_history
WHERE
    del_flg = 0
ORDER BY
    start_date DESC, send_id DESC
LIMIT $limit OFFSET $offset
EOF;
        $arrResult = $objSelect->getAll($sql);

        $objNavi = new SC_PageNavi_Ex($search_pageno,
                                    $linemax,
                                    SEARCH_PMAX,
                                    "fnNaviSearchPage");

        return array($linemax, $arrResult, $objNavi->arrPagenavi);
    }

    /**
     * 送信履歴の削除
     * @param integer $send_id　削除したい送信履歴のID
     * @return void
     */
    function lfDeleteHistory($send_id){
            $objQuery =& SC_Query_Ex::getSingletonInstance(); 
            $objQuery->update("dtb_send_history",
                              array('del_flg' =>1),
                              "send_id = ?",
                              array($send_id));
    }
}
?>
