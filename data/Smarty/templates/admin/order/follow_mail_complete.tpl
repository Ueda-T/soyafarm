<!--{* -*- coding: utf-8-unix; -*- *}-->
<div class="complete-wrapper">
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="planning_id" value="" />
<!--{foreach key=key item=item from=$arrSearchHidden}-->
    <!--{if is_array($item)}-->
        <!--{foreach item=c_item from=$item}-->
        <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
        <!--{/foreach}-->
    <!--{else}-->
        <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
    <!--{/if}-->
<!--{/foreach}-->
    <div id="complete">
        <div class="complete-top"></div>
        <div class="contents">
            <div class="message">
                <!--{if $arrForm.planning_id == ""}-->登録<!--{else}-->編集<!--{/if}-->が完了致しました。
            </div>
        </div>
        <div class="btn-area-top"></div>
        <div class="btn-area">
            <ul>
                <!--{if count($arrSearchHidden) > 0}-->
                <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_ORDER_URLPATH}-->followMail_search.php'); fnModeSubmit('search','',''); return false;"><span class="btn-prev">検索結果へ戻る</span></a></li>
                <!--{/if}-->
				<!--{if $arrForm.planning_id == ""}-->
                <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_ORDER_URLPATH}-->followMail.php'); fnModeSubmit('','',''); return false;"><span class="btn-next">続けて登録を行う</span></a></li>
				<!--{/if}-->
            </ul>
        </div>
        <div class="btn-area-bottom"></div>
    </div>
</form>
</div>
