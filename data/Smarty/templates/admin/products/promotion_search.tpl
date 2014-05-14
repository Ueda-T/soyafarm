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
</script>

<div id="products" class="contents-main">
<form name="search_form" id="search_form" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="search" />
    <h2>検索条件設定</h2>

    <!--検索条件設定テーブルここから-->
    <table>
        <tr>
            <th>プロモーション区分</th>
            <td>
                <!--{assign var=key value="search_promotion_kbn"}-->
                <span class="attention"><!--{$arrErr[$key]|h}--></span>
                <!--{html_checkboxes name="$key" options=$arrPromotionKbn selected=$arrForm[$key].value}-->
            </td>
        </tr>
        <tr>
            <th>有効期間</th>
            <td>
                <!--{assign var=key value="search_valid_from"}-->
                <span class="attention"><!--{$arrErr[$key]|h}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="10" style="<!--{if $arrErr[$key] != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="10" class="calendar" />
				〜
                <!--{assign var=key value="search_valid_to"}-->
                <span class="attention"><!--{$arrErr[$key]|h}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="10" style="<!--{if $arrErr[$key] != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="10" class="calendar" />
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
    <input type="hidden" name="promotion_cd" value="" />
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

    <!--{if count($arrPromotions) > 0}-->

        <!--{include file=$tpl_pager}-->

        <!--検索結果表示テーブル-->
        <table class="list" id="promotion-search-result">
            <colgroup width="10%">
            <colgroup width="49%">
            <colgroup width="12%">
            <colgroup width="12%">
            <colgroup width="12%">
            <colgroup width="5%">
            <tr>
                <th>ﾌﾟﾛﾓｰｼｮﾝ<br />ｺｰﾄﾞ</th>
                <th>プロモーション名</th>
                <th>ﾌﾟﾛﾓｰｼｮﾝ<br />区分</th>
                <th>開始日</th>
                <th>終了日</th>
                <th>詳細</th>
            </tr>

            <!--{section name=cnt loop=$arrPromotions}-->
                <!--▼プロモーション<!--{$smarty.section.cnt.iteration}-->-->
                <tr>
					<!--{* プロモーションコード *}-->
                    <td class="id"><!--{$arrPromotions[cnt].promotion_cd|h}--></td>
					<!--{* プロモーション名 *}-->
                    <td><!--{$arrPromotions[cnt].promotion_name|h}--></td>
					<!--{* プロモーション区分 *}-->
					<!--{assign var='key' value=$arrPromotions[cnt].promotion_kbn}-->
                    <td><!--{$arrPromotionKbn[$key]|h}--></td>
					<!--{* 開始日 *}-->
                    <td><!--{$arrPromotions[cnt].valid_from|h}--></td>
					<!--{* 終了日 *}-->
                    <td><!--{$arrPromotions[cnt].valid_to|h}--></td>

                    <td class="menu"><span class="icon_edit"><a href="javascript:;" onclick="fnChangeAction('./promotion.php'); fnModeSubmit('', 'promotion_cd', '<!--{$arrPromotions[cnt].promotion_cd}-->'); return false;" >詳細</a></span></td>
                </tr>
                <!--▲プロモーション<!--{$smarty.section.cnt.iteration}-->-->
            <!--{/section}-->
        </table>
        <input type="hidden" name="item_cnt" value="<!--{$arrPromotions|@count}-->" />
        <!--検索結果表示テーブル-->
    <!--{/if}-->
</form>
<!--★★検索結果一覧★★-->        
<!--{/if}-->
</div>
