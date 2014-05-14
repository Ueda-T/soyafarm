<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * ご利用規約 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Entry_Kiyaku.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_Entry_Kiyaku extends LC_Page_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_title = "ご利用規約";
	// モバイル用
        $this->tpl_notitle = true;
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

        $arrKiyaku = $this->lfGetKiyakuData();
        $this->max = count($arrKiyaku);

        $offset    = '';
        // mobile時はGETでページ指定
	/* モバイルも1ページ表記に変更
        if ( SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE ){
            $this->offset = $this->lfSetOffset($_GET['offset']);
        }
	 */

        $this->tpl_kiyaku_text
            = $this->lfMakeKiyakuText($arrKiyaku, $this->max, $this->offset);
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
     * 規約文の作成
     *
     * @param mixed $arrKiyaku
     * @param mixed $max
     * @param mixed $offset
     * @access public
     * @return string 規約の内容をテキストエリアで表示するように整形したデータ
     */
    function lfMakeKiyakuText($arrKiyaku, $max, $offset) {
        $this->tpl_kiyaku_text = "";
        for ($i = 0; $i < $max; $i++) {
            if ($offset !== null && ($offset - 1) <> $i) continue;
            $tpl_kiyaku_text.=$arrKiyaku[$i]['kiyaku_title'] . "\n\n";
            $tpl_kiyaku_text.=$arrKiyaku[$i]['kiyaku_text'] . "\n\n";
        }
        return $tpl_kiyaku_text;
    }

    /**
     * 規約内容の取得
     *
     * @access private
     * @return array $arrKiyaku 規約の配列
     */
    function lfGetKiyakuData() {

        $objQuery   = SC_Query_Ex::getSingletonInstance();

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

        return $objQuery->getAll($sql);
    }

    /**
     *
     * 携帯の場合getで来る次ページのidを適切に処理する
     *
     * @param mixed $offset
     * @access private
     * @return int
     */
    function lfSetOffset($offset) {
       return is_numeric($offset) === true ? intval($offset) : 1;
    }

}
?>
