<?php
/* ※使用条件※
    ・formタグに以下を追加する。
        <input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->">
    ・ソースの最初に以下を記述する。
        $objPage->tpl_pageno = $_POST['pageno'];
    ・$func_nameに指定するJavaScriptの例
        // ページナビで使用する
        function fnNaviPage(pageno) {
            document.form1['pageno'].value = pageno;
            document.form1.submit();
        }
*/
/**
 * ページナビクラス
 *
 * @package Page
 * @author IQUEVE CO.,LTD.
 * @version $Id: SC_PageNavi.php 106 2012-04-24 02:38:55Z hira $
 */
class SC_PageNavi {
    var $now_page;      // 現在のページ番号
    var $max_page;      // 最終のページ番号
    var $start_row;     // 開始レコード
    var $strnavi;       // ページ送り文字列
    var $arrPagenavi = array(); // ページ

    // コンストラクタ
    function SC_PageNavi($now_page, $all_row, $page_row, $func_name, $navi_max = NAVI_PMAX, $urlParam = '', $display_number = true) {
        $this->arrPagenavi['mode'] = 'search';

        //現在ページ($now_page)が正しい数値でない場合
        if (!preg_match("/^[[:digit:]]+$/", $now_page) || $now_page < 1 || strlen($now_page) == 0) {
            $this->now_page = 1;
        } else {
            $this->now_page = htmlspecialchars($now_page, ENT_QUOTES, CHAR_CODE);
        }
        $this->arrPagenavi['now_page'] = $this->now_page;

        // 最終ページの計算
        $this->max_page = ceil($all_row/$page_row);

        // 最終ページよりも現在ページが大きい場合は、最初に戻す。
        if ($this->max_page < $this->now_page) {
            $this->now_page = 1;
        }

        $this->start_row    = ($this->now_page - 1) * $page_row;
        $this->all_row      = $all_row;

        // 開始行が不正な場合
        if (!($this->start_row < $all_row && $this->start_row >= 0)) {
            $this->start_row = 0;
        }

        if ($all_row > 1) {

            //「前へ」「次へ」の設定
            $before = "";
            $next = "";
            if ($this->now_page > 1) {
                $this->arrPagenavi['before'] = $this->now_page - 1;
                $urlParamThis = str_replace('#page#', $this->arrPagenavi['before'], $urlParam);
                $urlParamThis = htmlentities($urlParamThis, ENT_QUOTES);
                $this->arrPagenavi['beforeUrlParam'] = $urlParamThis;
                $before = "<li><a href=\"?$urlParamThis\" onclick=\"$func_name('{$this->arrPagenavi['before']}'); return false;\">&lt;&lt;前へ</a></li>";
            } else {
                $this->arrPagenavi['before'] = $this->now_page;
            }

            if ($this->now_page < $this->max_page) {
                $this->arrPagenavi['next'] = $this->now_page + 1;
                $urlParamThis = str_replace('#page#', $this->arrPagenavi['next'], $urlParam);
                $urlParamThis = htmlentities($urlParamThis, ENT_QUOTES);
                $this->arrPagenavi['nextUrlParam'] = $urlParamThis;
                $next = "<li><a href=\"?$urlParamThis\" onclick=\"$func_name('{$this->arrPagenavi['next']}'); return false;\">次へ&gt;&gt;</a></li>";
            } else {
                $this->arrPagenavi['next'] = $this->now_page;
            }

            // 表示する最大ナビ数を決める。
            if ($navi_max == "" || $navi_max > $this->max_page) {
                // 制限ナビ数の指定がない。ページ最大数が制限ナビ数より少ない。
                $disp_max = $this->max_page;
            } else {
                // 現在のページ＋制限ナビ数が表示される。
                $disp_max = $this->now_page + $navi_max - 1;
                // ページ最大数を超えている場合は、ページ最大数に合わせる。
                if ($disp_max > $this->max_page) {
                    $disp_max = $this->max_page;
                }
            }

            // 表示する最小ナビ数を決める。
            if ($navi_max == "" || $navi_max > $this->now_page) {
                // 制限ナビ数の指定がない。現在ページ番号が制限ナビ数より少ない。
                $disp_min = 1;
            } else {
                // 現在のページ-制限ナビ数が表示される。
                $disp_min = $this->now_page - $navi_max + 1;
            }

            $this->arrPagenavi['arrPageno'] = array();
            $page_number = "";
            for ($i=$disp_min; $i <= $disp_max; $i++) {

                if ($i != $disp_max) {
                    $sep = " ";
                } else {
                    $sep = "";
                }

                if ($i == $this->now_page) {
                    $page_number .= "<li><strong>$i</strong></li>";
                } else {
                    $urlParamThis = str_replace('#page#', $i, $urlParam);
                    $urlParamThis = htmlentities($urlParamThis, ENT_QUOTES);
                    $page_number .= "<li><a href=\"?$urlParamThis\" onclick=\"$func_name('$i'); return false;\">$i</a></li>";
                }

                $page_number .= $sep;

                $this->arrPagenavi['arrPageno'][$i] = $i;
            }

            if ($before && $next) {
                $this->strnavi = $before .(($display_number) ? $page_number : ' | ') .$next;
            } else if ($before || $next) {
                $this->strnavi = $before .(($display_number) ? $page_number : '') .$next;
            }
        } else {
            $this->arrPagenavi['arrPageno'][0] = 1;
            $this->arrPagenavi['before'] = 1;
            $this->arrPagenavi['next'] = 1;
        }
    }
}

?>
