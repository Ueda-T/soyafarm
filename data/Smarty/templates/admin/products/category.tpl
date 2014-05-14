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
<input type="hidden" name="parent_category_id" value="<!--{$arrForm.parent_category_id|h}-->" />
<input type="hidden" name="category_id" value="<!--{$arrForm.category_id|h}-->" />
<input type="hidden" name="image_key" value="" />
<input type="hidden" name="rank" value="<!--{$arrForm.rank|h}-->" />
<!--{foreach key=key item=item from=$arrForm.arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
<!--{/foreach}-->
<div id="products" class="contents-main">

    <!--{* if $arrForm.arrHidden|@count > 0 || $smarty.post.category_id|escape}-->
    <div class="btn-area-head">
        <!--▼検索結果へ戻る-->
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_PRODUCTS_URLPATH}-->'); fnModeSubmit('search','',''); return false;"><span class="btn-prev">検索画面に戻る</span></a></li>
        <!--▲検索結果へ戻る-->
        </ul>
    </div>
    <!--{/if *}-->

    <h2>カテゴリ<!--{if $arrForm.category_id == ""}-->登録<!--{else}-->編集<!--{/if}--></h2>
    <table class="form">
        <tr>
            <th>生成URL</th>
            <td>
            <!--{if $arrForm.category_id}-->
            <a href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/products/list.php?category_id=<!--{$arrForm.category_id|h}-->" target="_blank" ><!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/products/list.php?category_id=<!--{$arrForm.category_id|h}--></a><br />
            <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>カテゴリID</th>
            <td><!--{$arrForm.category_id|h}--></td>
        </tr>
        <tr>
            <th>カテゴリ名<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.category_name}--></span>
                <input type="text" name="category_name" value="<!--{$arrForm.category_name|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.category_name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="60" class="box60" />
            </td>
        </tr>
        <tr>
            <th>カテゴリコード<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.category_code}--></span>
                <input type="text" name="category_code" value="<!--{$arrForm.category_code|h}-->" maxlength="<!--{$smarty.const.CATEGORY_CODE_LEN}-->" style="width:180px; <!--{if $arrErr.category_code != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" />
            </td>
        </tr>
        <tr>
            <th>状態<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.status}--></span>
				<!--{assign var='style' value=''}-->
				<!--{if $arrErr.status != ""}-->
				    <!--{assign var='style' value="background-color: `$smarty.const.ERR_COLOR`;"}-->
				<!--{/if}-->
                <!--{html_radios name="status" options=$arrStatus selected=$arrForm.status separator='&nbsp;&nbsp;' style="`$style`"}-->
            </td>
        </tr>
        <tr>
            <!--{assign var=key value="image"}-->
            <th>カテゴリ画像<br />[<!--{$smarty.const.NORMAL_IMAGE_WIDTH}-->×<!--{$smarty.const.NORMAL_IMAGE_HEIGHT}-->]</th>
            <td>
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{if $arrForm.arrFile[$key].filepath != ""}-->
                <img src="<!--{$arrForm.arrFile[$key].filepath}-->" alt="<!--{$arrForm.category_name|h}-->" />　<a href="" onclick="fnModeSubmit('delete_image', 'image_key', '<!--{$key}-->'); return false;">[画像の取り消し]</a><br />
                <!--{/if}-->
                <input type="file" name="<!--{$key}-->" size="40" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
                <a class="btn-normal" href="javascript:;" name="btn" onclick="fnModeSubmit('upload_image', 'image_key', '<!--{$key}-->'); return false;">アップロード</a>
            </td>
        </tr>
        <tr>
            <th>METAタグ</th>
            <td>
	        <span class="attention"><!--{$arrErr.metatag}--></span>
                <textarea name="metatag" cols="60" rows="8" class="area60" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr.metatag|sfGetErrorColor}-->"><!--{$arrForm.metatag|h}--></textarea>
                <span class="attention"> (上限<!--{$smarty.const.LLTEXT_LEN}-->文字)</span><br />
            </td>
    </table>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_PRODUCTS_URLPATH}-->category_search.php'); fnModeSubmit('search', '', ''); return false;"><span class="btn-prev">検索画面に戻る</span></a></li>
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_PRODUCTS_URLPATH}-->category.php'); fnModeSubmit('edit', '', ''); return false;"><span class="btn-next">確認ページへ</span></a></li>
            <!--{/if}-->
        </ul>
    </div>
</div>
</form>
