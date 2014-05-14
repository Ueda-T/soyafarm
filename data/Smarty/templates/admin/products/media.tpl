<!--{* -*- coding: utf-8-unix; -*- *}-->
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
<input type="hidden" name="media_id" value="<!--{$arrForm.media_id|h}-->" />
<!--{foreach key=key item=item from=$arrForm.arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
<!--{/foreach}-->
<div id="products" class="contents-main">

    <!--{if $arrForm.arrHidden|@count > 0 || $smarty.post.media_id|escape}-->
    <div class="btn-area-head">
        <!--▼検索結果へ戻る-->
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_PRODUCTS_URLPATH}-->media_search.php'); fnModeSubmit('search','',''); return false;"><span class="btn-prev">検索画面に戻る</span></a></li>
        <!--▲検索結果へ戻る-->
        </ul>
    </div>
    <!--{/if}-->

    <h2>広告媒体マスタ<!--{if $arrForm.media_id == ""}-->登録<!--{else}-->編集<!--{/if}--></h2>
    <table class="form">
        <tr>
            <th>広告媒体ID</th>
            <td><!--{$arrForm.media_id|h}--></td>
        </tr>
        <tr>
            <th>広告媒体コード<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.media_code}--></span>
                <input type="text" name="media_code" value="<!--{$arrForm.media_code|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.media_code != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="15" class="box15" />
			</td>
        </tr>
        <tr>
            <th>広告媒体名<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.media_name}--></span>
                <input type="text" name="media_name" value="<!--{$arrForm.media_name|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.media_name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="60" class="box60" />
            </td>
        </tr>
    </table>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_PRODUCTS_URLPATH}-->media_search.php'); fnModeSubmit('search', '', ''); return false;"><span class="btn-prev">検索画面に戻る</span></a></li>
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_PRODUCTS_URLPATH}-->media.php'); fnModeSubmit('edit', '', ''); return false;"><span class="btn-next">確認ページへ</span></a></li>
            <!--{/if}-->
        </ul>
    </div>
</div>
</form>
