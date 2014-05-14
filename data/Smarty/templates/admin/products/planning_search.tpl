<!--{* -*- coding: utf-8-unix; -*- *}-->
<div id="products" class="contents-main">
<form name="search_form" id="search_form" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="search" />
    <h2>検索条件設定</h2>

    <!--検索条件設定テーブルここから-->
    <table>
        <tr>
            <th>企画タイプ</th>
            <td>
                <!--{assign var=key value="search_planning_type"}-->
                <span class="attention"><!--{$arrErr[$key]|h}--></span>
                <!--{html_checkboxes name="$key" options=$arrPlanType selected=$arrForm[$key].value}-->
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
                <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('search_form', 'search', '', ''); return false;"><span class="btn-next">この条件で検索する</span></a></li>
                <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
                <li><a class="btn-action" href="javascript:;" name="subm2" onclick="fnChangeAction('./planning.php'); fnModeSubmit('', '', ''); return false;"><span class="btn-next">企画を新規入力</span></a></li>
                <!--{/if}-->
            </ul>
        </div>

    </div>
    <!--検索条件設定テーブルここまで-->
</form>  

<!--★★検索結果一覧★★-->
<form name="form1" id="form1" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="search" />
    <input type="hidden" name="planning_id" value="" />
    <!--{foreach key=key item=item from=$arrHidden}-->
        <!--{if is_array($item)}-->
            <!--{foreach item=c_item from=$item}-->
            <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
            <!--{/foreach}-->
        <!--{else}-->
            <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
        <!--{/if}-->
    <!--{/foreach}-->

    <!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'delete')}-->
    <h2>検索結果一覧</h2>
    <div class="btn">
        <span class="attention"><!--検索結果数--><!--{$tpl_linemax}-->件</span>&nbsp;が該当しました。
    </div>

        <!--{if count($arrPlannings) > 0}-->

        <!--{include file=$tpl_pager}-->

        <!--検索結果表示テーブル-->
        <table class="list" id="planning-search-result">
            <colgroup width="5%">
            <colgroup width="31%">
            <colgroup width="12%">
            <colgroup width="12%">
            <colgroup width="13%">
            <colgroup width="10%">
            <colgroup width="7%">
            <colgroup width="5%">
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <colgroup width="5%">
            <!--{/if}-->
            <tr>
                <th>企画<br />ID</th>
                <th>企画名</th>
                <th>開始日</th>
                <th>終了日</th>
                <th>企画タイプ</th>
                <th>広告媒体</th>
                <th>キャン<br />ペーン</th>
                <th>編集</th>
                <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
                <th>削除</th>
                <!--{/if}-->
            </tr>

            <!--{section name=cnt loop=$arrPlannings}-->
                <!--▼企画<!--{$smarty.section.cnt.iteration}-->-->
                <tr>
					<!--{* 企画ID *}-->
                    <td class="id"><!--{$arrPlannings[cnt].planning_id|h}--></td>
					<!--{* 企画名 *}-->
                    <td><!--{$arrPlannings[cnt].planning_name|h}--></td>
					<!--{* 開始日 *}-->
                    <td><!--{$arrPlannings[cnt].start_date|h}--></td>
					<!--{* 終了日 *}-->
                    <td><!--{$arrPlannings[cnt].end_date|h}--></td>
					<!--{* 企画タイプ *}-->
					<!--{assign var='key' value=$arrPlannings[cnt].planning_type}-->
                    <td><!--{$arrPlanType[$key]|h}--></td>
					<!--{* 広告媒体 *}-->
                    <td><!--{$arrPlannings[cnt].media_code|h}--></td>
					<!--{* キャンペーン *}-->
                    <td><!--{$arrPlannings[cnt].campaign_code|h}--></td>

                    <td class="menu"><span class="icon_edit"><a href="javascript:;" onclick="fnChangeAction('./planning.php'); fnModeSubmit('', 'planning_id', '<!--{$arrPlannings[cnt].planning_id}-->'); return false;" >編集</a></span></td>
                    <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
                    <td class="menu"><span class="icon_delete"><a href="javascript:;" onclick="fnModeSubmit('delete', 'planning_id', '<!--{$arrPlannings[cnt].planning_id}-->'); return false;">削除</a></span></td>
                    <!--{/if}-->
                </tr>
                <!--▲企画<!--{$smarty.section.cnt.iteration}-->-->
            <!--{/section}-->
        </table>
        <input type="hidden" name="item_cnt" value="<!--{$arrPlannings|@count}-->" />
        <!--検索結果表示テーブル-->
        <!--{/if}-->
    <!--{/if}-->
</form>
<!--★★検索結果一覧★★-->        
</div>
