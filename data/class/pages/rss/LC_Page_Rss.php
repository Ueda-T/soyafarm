<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

/**
 * RSS のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Rss.php 91 2012-04-11 04:39:04Z hira $
 */
class LC_Page_RSS extends LC_Page_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = "rss/index.tpl";
        $this->encode = "UTF-8";
        $this->description = "新着情報";
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objView = new SC_SiteView_Ex(false);

        //新着情報を取得
        $arrNews = $this->lfGetNews($objQuery);

        //キャッシュしない(念のため)
        header("pragma: no-cache");

        //XMLテキスト(これがないと正常にRSSとして認識してくれないツールがあるため)
        header("Content-type: application/xml");

        //新着情報をセット
        $this->arrNews = $arrNews;

        //店名をセット
        $this->site_title = $arrNews[0]['shop_name'];

        //代表Emailアドレスをセット
        $this->email = $arrNews[0]['email'];

        //セットしたデータをテンプレートファイルに出力
        $objView->assignobj($this);

        //画面表示
        $objView->display($this->tpl_mainpage, true);
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
     * 新着情報を取得する
     *
     * @param SC_Query $objQuery DB操作クラス
     * @return array $arrNews 取得結果を配列で返す
     */
    function lfGetNews(&$objQuery){

        $sql =<<<EOF
SELECT
    news_id   
    ,news_title
    ,news_comment
    ,news_date
    ,news_url 
    ,news_select
    ,(SELECT shop_name FROM dtb_baseinfo limit 1) AS shop_name
    ,(SELECT email04 FROM dtb_baseinfo limit 1) AS email
FROM
    dtb_news
WHERE
    del_flg = 0
ORDER BY rank DESC
EOF;
        $arrNews = $objQuery->getAll($sql);

        // RSS用に変換
        foreach (array_keys($arrNews) as $key) {
            $netUrlHttpUrl = new Net_URL(HTTP_URL);

            $row =& $arrNews[$key];
            // 日付
            $row['news_date'] = date('r', strtotime($row['news_date']));
            // 新着情報URL
            if (SC_Utils_Ex::isBlank($row['news_url'])) {
                $row['news_url'] = HTTP_URL;
            }
            elseif ($row['news_url'][0] == '/') {
                // 変換(絶対パス→URL)
                $netUrl = new Net_URL($row['news_url']);
                $netUrl->protocol = $netUrlHttpUrl->protocol;
                $netUrl->user = $netUrlHttpUrl->user;
                $netUrl->pass = $netUrlHttpUrl->pass;
                $netUrl->host = $netUrlHttpUrl->host;
                $netUrl->port = $netUrlHttpUrl->port;
                $row['news_url'] = $netUrl->getUrl();
            }
        }

        return $arrNews;
    }
}
?>
