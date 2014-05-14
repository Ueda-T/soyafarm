<!--{* -*- coding: utf-8-unix; -*- *}-->
<div class="complete-wrapper">
<form name="form1" id="form1" method="post" action="./product_class.php">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="product_id" value="" />
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
                登録が完了致しました。
            </div>
        </div>
        <div class="btn-area-top"></div>
        <div class="btn-area">
            <ul>
                <!--{if count($arrSearchHidden) > 0}-->
                <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_PRODUCTS_URLPATH}-->'); fnModeSubmit('search','',''); return false;"><span class="btn-prev">検索結果へ戻る</span></a></li>
                <!--{/if}-->
                <!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
                <li><a class="btn-action" href="?" onclick="fnModeSubmit('pre_edit', 'product_id', '<!--{$arrForm.product_id}-->'); return false;"><span class="btn-next">この商品の規格を登録する</span></a></li>
                <!--{/if}-->
            </ul>
        </div>
        <div class="btn-area-bottom"></div>
    </div>
</form>
</div>



<!--{* オペビルダー用 *}-->
<!--{if "sfViewAdminOpe"|function_exists === TRUE}-->
<!--{include file=`$smarty.const.MODULE_REALDIR`mdl_opebuilder/admin_ope_view.tpl}-->
<!--{/if}-->
