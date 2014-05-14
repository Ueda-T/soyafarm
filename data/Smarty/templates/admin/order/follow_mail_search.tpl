<!--{* -*- coding: utf-8-unix; -*- *}-->
<div id="products" class="contents-main">
<form name="form1" id="form1" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="search" />
    <input type="hidden" name="search_pageno" value="<!--{$tpl_pageno}-->" >
    <h2>検索条件設定</h2>

    <!--検索条件設定テーブルここから-->
    <table>
        <tr>
            <th>フォローメール名</th>
            <td>
                <!--{assign var=key value="search_follow_name"}-->
                <span class="attention"><!--{$arrErr[$key]|h}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="60" class="box60" />
            </td>
        </tr>
        <tr>
            <th>送信日設定</th>
            <td>
                <!--{assign var=key value="search_send_term"}-->
                <span class="attention"><!--{$arrErr[$key]|h}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="6" class="box6" />日 （発送日からの経過日数）
            </td>
        </tr>
    </table>
    <div class="btn">
        <p class="page_rows">検索結果表示件数
        <!--{assign var=key value="search_page_max"}-->
        <!--{if $arrErr[$key]}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
        <!--{/if}-->
        <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
            <!--{html_options options=$arrPageMax selected=$arrForm.search_page_max.value}-->
        </select> 件</p>

        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'search', '', ''); return false;"><span class="btn-next">この条件で検索する</span></a></li>
                <!--{if $tpl_update_auth == $smarty.const.UPDATE_AUTH_ON}-->
                <li><a class="btn-action" href="javascript:;" name="subm2" onclick="fnChangeAction('./followMail.php'); fnModeSubmit('', '', ''); return false;"><span class="btn-next">フォローメールを新規入力</span></a></li>
                <!--{/if}-->
            </ul>
        </div>

    </div>
    <!--検索条件設定テーブルここまで-->

<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'delete')}-->

<!--★★検索結果一覧★★-->
    <input type="hidden" name="follow_id" value="" />

    <h2>検索結果一覧</h2>
    <div class="btn">
        <span class="attention"><!--検索結果数--><!--{$tpl_linemax}-->件</span>&nbsp;が該当しました。
    </div>

    <!--{if count($arrFollowMails) > 0}-->

        <!--{include file=$tpl_pager}-->

        <!--検索結果表示テーブル-->
        <table class="list" id="followMail-search-result">
            <colgroup width="12%">
            <colgroup width="34%">
            <colgroup width="24%">
            <colgroup width="10%">
            <colgroup width="10%">
            <!--{if $tpl_update_auth == $smarty.const.UPDATE_AUTH_ON}-->
            <colgroup width="10%">
            <!--{/if}-->
            <tr>
                <th>ﾌｫﾛｰﾒｰﾙ<br />ｺｰﾄﾞ</th>
                <th>フォローメール名</th>
                <th>送信日設定<br />（配送日からの経過日数）</th>
                <th>状態</th>
                <th>編集</th>
                <!--{if $tpl_update_auth == $smarty.const.UPDATE_AUTH_ON}-->
                <th>削除</th>
                <!--{/if}-->
            </tr>

            <!--{section name=cnt loop=$arrFollowMails}-->
                <!--▼フォローメール<!--{$smarty.section.cnt.iteration}-->-->
                <tr>
					<!--{* フォローメールコード *}-->
                    <td class="center"><!--{$arrFollowMails[cnt].follow_code|h}--></td>
					<!--{* フォローメール名 *}-->
                    <td><!--{$arrFollowMails[cnt].follow_name|h}--></td>
					<!--{* 送信日設定 *}-->
                    <td class="center"><!--{$arrFollowMails[cnt].send_term|h}-->日</td>
					<!--{* 状態 *}-->
                    <!--{assign var=status value=$arrFollowMails[cnt].status}-->
                    <td class="center"><!--{$arrFollowMailStatus[$status]|h}--></td>

                    <td class="menu"><span class="icon_edit"><a href="javascript:;" onclick="fnChangeAction('./followMail.php'); fnModeSubmit('', 'follow_id', '<!--{$arrFollowMails[cnt].follow_id}-->'); return false;" >編集</a></span></td>
                    <!--{if $tpl_update_auth == $smarty.const.UPDATE_AUTH_ON}-->
                    <td class="menu"><span class="icon_delete"><a href="javascript:;" onclick="fnModeSubmit('delete', 'follow_id', '<!--{$arrFollowMails[cnt].follow_id}-->'); return false;">削除</a></span></td>
                    <!--{/if}-->
                </tr>
                <!--▲フォローメール<!--{$smarty.section.cnt.iteration}-->-->
            <!--{/section}-->
        </table>
        <input type="hidden" name="item_cnt" value="<!--{$arrFollowMails|@count}-->" />
        <!--検索結果表示テーブル-->
    <!--{/if}-->
</form>
<!--★★検索結果一覧★★-->
<!--{/if}-->
</div>
