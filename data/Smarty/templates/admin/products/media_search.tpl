<!--{* -*- coding: utf-8-unix; -*- *}-->
<div id="products" class="contents-main">
<form name="search_form" id="search_form" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="search" />
    <h2>検索条件設定</h2>

    <!--検索条件設定テーブルここから-->
    <table>
        <tr>
            <th>広告媒体コード</th>
            <td>
                <!--{assign var=key value="search_media_code_from"}-->
                <span class="attention"><!--{$arrErr[$key]|h}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="15" class="box15" />
                〜
                <!--{assign var=key value="search_media_code_to"}-->
                <span class="attention"><!--{$arrErr[$key]|h}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="15" class="box15" />
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
                <li><a class="btn-action" href="javascript:;" name="subm2" onclick="fnChangeAction('./media.php'); fnModeSubmit('', '', ''); return false;"><span class="btn-next">広告媒体を新規入力</span></a></li>
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
    <input type="hidden" name="media_id" value="" />
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

        <!--{if count($arrMedia) > 0}-->

        <!--{include file=$tpl_pager}-->

        <!--検索結果表示テーブル-->
        <table class="list" id="media-search-result">
            <colgroup width="15%">
            <colgroup width="75%">
            <colgroup width="5%">
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <colgroup width="5%">
            <!--{/if}-->
            <tr>
                <th>広告媒体コード</th>
                <th>広告媒体名</th>
                <th>編集</th>
                <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
                <th>削除</th>
                <!--{/if}-->
            </tr>

            <!--{section name=cnt loop=$arrMedia}-->
                <!--▼広告媒体<!--{$smarty.section.cnt.iteration}-->-->
                <tr>
					<!--{* 広告媒体コード *}-->
                    <td class="id"><!--{$arrMedia[cnt].media_code|h}--></td>
					<!--{* 広告媒体名 *}-->
                    <td><!--{$arrMedia[cnt].media_name|h}--></td>

                    <td class="menu"><span class="icon_edit"><a href="javascript:;" onclick="fnChangeAction('./media.php'); fnModeSubmit('', 'media_id', '<!--{$arrMedia[cnt].media_id}-->'); return false;" >編集</a></span></td>
                    <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
                    <td class="menu"><span class="icon_delete"><a href="javascript:;" onclick="fnModeSubmit('delete', 'media_id', '<!--{$arrMedia[cnt].media_id}-->'); return false;">削除</a></span></td>
                    <!--{/if}-->
                </tr>
                <!--▲広告媒体<!--{$smarty.section.cnt.iteration}-->-->
            <!--{/section}-->
        </table>
        <input type="hidden" name="item_cnt" value="<!--{$arrMedia|@count}-->" />
        <!--検索結果表示テーブル-->
        <!--{/if}-->
    <!--{/if}-->
</form>
<!--★★検索結果一覧★★-->        
</div>
