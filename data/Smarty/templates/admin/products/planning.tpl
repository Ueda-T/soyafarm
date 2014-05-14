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

<form name="form1" id="form1" method="post" action="?" enctype="multipart/form-data">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<!--{foreach key=key item=item from=$arrSearchHidden}-->
    <!--{if is_array($item)}-->
        <!--{foreach item=c_item from=$item}-->
        <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
        <!--{/foreach}-->
    <!--{else}-->
        <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
    <!--{/if}-->
<!--{/foreach}-->
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="planning_id" value="<!--{$arrForm.planning_id|h}-->" />
<!--{foreach key=key item=item from=$arrForm.arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
<!--{/foreach}-->
<div id="products" class="contents-main">

    <!--{if $arrForm.arrHidden|@count > 0 || $smarty.post.planning_id|escape}-->
    <div class="btn-area-head">
        <!--▼検索結果へ戻る-->
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_PRODUCTS_URLPATH}-->'); fnModeSubmit('search','',''); return false;"><span class="btn-prev">検索画面に戻る</span></a></li>
        <!--▲検索結果へ戻る-->
        </ul>
    </div>
    <!--{/if}-->

    <h2>企画マスタ<!--{if $arrForm.planning_id == ""}-->登録<!--{else}-->編集<!--{/if}--></h2>
    <table class="form">
        <tr>
            <th>企画ID</th>
            <td><!--{$arrForm.planning_id|h}--></td>
        </tr>
        <tr>
            <th>企画名<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.planning_name}--></span>
                <input type="text" name="planning_name" value="<!--{$arrForm.planning_name|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.planning_name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="60" class="box60" />
            </td>
        </tr>
        <tr>
            <th>開始日</th>
            <td>
                <span class="attention"><!--{$arrErr.start_date}--></span>
                <input type="text" name="start_date" value="<!--{$arrForm.start_date|h}-->" maxlength="10" style="<!--{if $arrErr.start_date != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="10" class="calendar" />
            </td>
        </tr>
        <tr>
            <th>終了日</th>
            <td>
                <span class="attention"><!--{$arrErr.end_date}--></span>
                <input type="text" name="end_date" value="<!--{$arrForm.end_date|h}-->" maxlength="10" style="<!--{if $arrErr.end_date != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="10" class="calendar" />
            </td>
        </tr>
        <tr>
            <th>企画タイプ<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.planning_type}--></span>
				<!--{assign var='style' value=''}-->
				<!--{if $arrErr.planning_type != ""}-->
				    <!--{assign var='style' value="background-color: `$smarty.const.ERR_COLOR`;"}-->
				<!--{/if}-->
                <!--{html_radios name="planning_type" options=$arrPlanningType selected=$arrForm.planning_type separator='&nbsp;&nbsp;' style="`$style`"}-->
            </td>
        </tr>
        <tr>
            <th>広告媒体<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.media_code}--></span>
                <input type="text" name="media_code" value="<!--{$arrForm.media_code|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.media_code != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="20" class="box20" /> <!--{$arrForm.media_name|h}-->
            </td>
        </tr>
        <tr>
            <th>キャンペーン</th>
            <td>
                <span class="attention"><!--{$arrErr.campaign_code}--></span>
                <input type="text" name="campaign_code" value="<!--{$arrForm.campaign_code|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.campaign_code != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="10" class="box10" />
				<!--{$arrForm.campaign_name|h}-->
            </td>
        </tr>
        <tr>
            <th>並び順<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.rank}--></span>
                <input type="text" name="rank" value="<!--{$arrForm.rank|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.rank != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="10" class="box10" />
            </td>
        </tr>
    </table>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_PRODUCTS_URLPATH}-->planning_search.php'); fnModeSubmit('search', '', ''); return false;"><span class="btn-prev">検索画面に戻る</span></a></li>
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_PRODUCTS_URLPATH}-->planning.php'); fnModeSubmit('edit', '', ''); return false;"><span class="btn-next">確認ページへ</span></a></li>
            <!--{/if}-->
        </ul>
    </div>
</div>
</form>
