<!--{* -*- coding: utf-8-unix; -*- *}-->
<form name="form1" id="form1" method="post" action="?" enctype="multipart/form-data">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="complete" />
<!--{foreach key=key item=item from=$arrSearchHidden}-->
    <!--{if is_array($item)}-->
        <!--{foreach item=c_item from=$item}-->
            <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
        <!--{/foreach}-->
    <!--{else}-->
        <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
    <!--{/if}-->
<!--{/foreach}-->
<!--{foreach key=key item=item from=$arrForm}-->
    <!--{if is_array($item)}-->
        <!--{foreach item=c_item from=$item}-->
            <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
        <!--{/foreach}-->
    <!--{else}-->
        <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
    <!--{/if}-->
<!--{/foreach}-->
<div id="products" class="contents-main">
    <!--{*
    <div class="btn-area-head">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnModeSubmit('confirm_return','',''); return false;"><span class="btn-prev">前のページに戻る</span></a></li>
        </ul>
    </div>
    *}-->

    <table>
        <tr>
            <th>カテゴリ名</th>
            <td>
                <!--{$arrForm.category_name|h}-->
            </td>
        </tr>
        <tr>
            <th>カテゴリコード</th>
            <td>
                <!--{$arrForm.category_code|h}-->
            </td>
        </tr>
        <tr>
            <th>状態</th>
            <td>
                <!--{$arrStatus[$arrForm.status]}-->
            </td>
        </tr>
        <tr>
            <th>カテゴリ画像</th>
            <td>
                <!--{assign var=key value="image"}-->
                <!--{if $arrForm.arrFile[$key].filepath != ""}-->
                    <img src="<!--{$arrForm.arrFile[$key].filepath}-->" alt="<!--{$arrForm.category_name|h}-->" /><br />
                <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>METAタグ</th>
            <td>
                <!--{$arrForm.metatag|h}-->
            </td>
        </tr>
    </table>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnModeSubmit('confirm_return','',''); return false;"><span class="btn-prev">前のページに戻る</span></a></li>
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <li><a class="btn-action" href="javascript:;" onclick="document.form1.submit(); return false;"><span class="btn-next">この内容で登録する</span></a></li>
            <!--{/if}-->
        </ul>
    </div>
</div>
</form>
