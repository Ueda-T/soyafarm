<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * 利用規約について のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Guide_Kiyaku.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Guide_Kiyaku extends LC_Page_Ex {

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
        parent::process();
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $this->lfGetKiyaku(intval($_GET['page']), $this);
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
     * 利用規約を取得し、ページオブジェクトに格納する。
     *
     * @param integer $index 規約のインデックス
     * @param object &$objPage ページオブジェクト
     * @return void
     */
    function lfGetKiyaku($index, &$objPage) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        
        $sql =<<<EOF
SELECT
    kiyaku_title,
    kiyaku_text
FROM
    dtb_kiyaku
WHERE
    del_flg <> 1
ORDER BY rank DESC
EOF;
        $arrKiyaku = $objQuery->getAll($sql);

        $number = count($arrKiyaku);
        if ($number > 0) {
            $last = $number - 1;
        } else {
            $last = 0;
        }

        if ($index < 0) {
            $index = 0;
        } elseif ($index > $last) {
            $index = $last;
        }

        $objPage->tpl_kiyaku_title = $arrKiyaku[$index]['kiyaku_title'];
        $objPage->tpl_kiyaku_text = $arrKiyaku[$index]['kiyaku_text'];
        $objPage->tpl_kiyaku_index = $index;
        $objPage->tpl_kiyaku_last_index = $last;
        $objPage->tpl_kiyaku_is_first = $index <= 0;
        $objPage->tpl_kiyaku_is_last = $index >= $last;
    }
}
?>
