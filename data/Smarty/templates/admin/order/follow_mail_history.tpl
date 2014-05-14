<!--{* -*- coding: utf-8-unix; -*- *}-->
<script type="text/javascript">
// カレンダー表示（datepicker）
$(function() {
    $(".calendar").datepicker();
    $(".calendar").datepicker("option", "showOn", 'both');
    $(".calendar").datepicker("option", "buttonImage",
                              '<!--{$TPL_URLPATH}-->img/common/calendar.png');
    $(".calendar").datepicker("option", "buttonImageOnly", true);
});

$(document).ready(function(){
    $.spin.imageBasePath = '<!--{$TPL_URLPATH}-->img/spin1/';
    $('#spin1').spin({
        min: 0,
        interval: 100,
	timeInterval: 150
    });
    $('#spin2').spin({
        min: 0,
        interval: 100,
	timeInterval: 150
    });
});
</script>
<div id="order" class="contents-main">
<form name="search_form" id="search_form" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="search" />
    <h2>検索条件設定</h2>
    <!--{* 検索条件設定テーブルここから *}-->
    <table>
        <tr>
            <th>フォローメール</th>
            <td>
                <!--{assign var=key value="search_follow_mail"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
        </tr>
        <tr>
            <th>配信日</th>
            <td>
                <!--{assign var=key value="search_date_from"}-->
                <span class="attention"><!--{$arrErr[$key]|h}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" size="10" maxlength="10" style="<!--{if $arrErr[$key] != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="7" class="calendar" />～
                <!--{assign var=key value="search_date_to"}-->
                <span class="attention"><!--{$arrErr[$key]|h}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" size="10" maxlength="10" style="<!--{if $arrErr[$key] != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="7" class="calendar" />
            </td>
    </table>

    <div class="btn">
        <p class="page_rows">検索結果表示件数
        <!--{assign var=key value="search_page_max"}-->
        <span class="attention"><!--{$arrErr[$key]}--></span>
        <select name="<!--{$arrForm[$key].keyname}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
        <!--{html_options options=$arrPageMax selected=$arrForm[$key].value}-->
        </select> 件</p>
        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('search_form', 'search', '', ''); return false;"><span class="btn-next">この条件で検索する</span></a></li>
            </ul>
        </div>
    </div>
    <!--検索条件設定テーブルここまで-->
</form>

<!--{if count($arrErr) == 0 and $smarty.post.mode == 'search'}-->
<!--★★検索結果一覧★★-->
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="search" />
<input type="hidden" name="regular_id" value="" />
<input type="hidden" name="line_no" value="" />
<!--{foreach key=key item=item from=$arrHidden}-->
    <!--{if is_array($item)}-->
        <!--{foreach item=c_item from=$item}-->
        <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
        <!--{/foreach}-->
    <!--{else}-->
        <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
    <!--{/if}-->
<!--{/foreach}-->

    <h2>検索結果一覧</h2>
        <div class="btn">
        <span class="attention"><!--検索結果数--><!--{$tpl_linemax}-->件</span>&nbsp;が該当しました。
        </div>
        <!--{if count($arrResults) > 0}-->
        <!--{include file=$tpl_pager}-->
        <!--{* 検索結果表示テーブル *}-->
        <table class="list">
            <tr>
                <th>配信開始時刻</th>
                <th rowspan="2">タイトル</th>
                <th rowspan="2">配信<br />総数</th>
                <th rowspan="2">配信<br />済数</th>
                <th rowspan="2">配信<br />失敗数</th>
                <th rowspan="2">未配信数</th>
            </tr>
            <tr>
                <th>配信終了時刻</th>
            </tr>
            <!--{section name=cnt loop=$arrResults}-->
            <tr>
                <td nowrap><!--{$arrResults[cnt].start_date|h}--></td>
                <td rowspan="2" class="left"><!--{$arrResults[cnt].subject|h}--></td>
                <td rowspan="2"><!--{$arrResults[cnt].all_count|h}--></td>
                <td rowspan="2"><!--{$arrResults[cnt].sent_count|h}--></td>
                <td rowspan="2"><!--{$arrResults[cnt].err_count|h}--></td>
                <td rowspan="2"><!--{$arrResults[cnt].unsent_count|h}--></td>
            </tr>
            <tr>
                <td nowrap><!--{$arrResults[cnt].end_date|h}--></td>
            </tr>
            <!--{/section}-->
        </table>
        <!--{* 検索結果表示テーブル *}-->
        <!--{else}-->
        <div id="complete">
            <div class="complete-top"></div>
                <div class="contents">
                    <div class="message">配信履歴はありません</div>
                </div>
            </div>
        </div>
        <!--{/if}-->
</form>
<!--{/if}-->
</div>
