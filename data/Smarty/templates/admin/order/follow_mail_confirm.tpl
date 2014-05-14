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
    <div class="btn-area-head">
        <ul>
            <!--{*<li><a class="btn-action" href="javascript:;" onclick="fnModeSubmit('confirm_return','',''); return false;"><span class="btn-prev">前のページに戻る</span></a></li>*}-->
            <li><a class="btn-action" href="javascript:;" onclick="history.back();"><span class="btn-prev">前のページに戻る</span></a></li>
        </ul>
    </div>

    <table>
        <tr>
            <th>フォローメールコード</th>
            <td>
                <!--{$arrForm.follow_code|h}-->
            </td>
        </tr>
        <tr>
            <th>フォローメール名</th>
            <td>
                <!--{$arrForm.follow_name|h}-->
            </td>
        </tr>
        <tr>
            <th>送信日設定</th>
            <td>
                <!--{$arrForm.send_term|h}-->
            </td>
        </tr>
        <tr>
            <th>状態</th>
            <td>
                <!--{$arrFollowMailStatus[$arrForm.status]}-->
            </td>
        </tr>
        <tr>
            <th>メールタイトル</th>
            <td>
                <!--{$arrForm.subject|h}-->
            </td>
        </tr>
        <tr>
            <th>メール本文</th>
            <td>
                <!--{$arrForm.mail_body|h|nl2br}-->
            </td>
        </tr>
    </table>

    <div class="btn">
		<span>対象購入商品一覧</span>
    </div>

	<!--対象購入商品一覧-->
    <table class="list" id="brand-product-list">
		<colgroup width="15%">
		<colgroup width="85%">
		<tr>
			<th>商品番号</th>
			<th>商品名</th>
		</tr>

		<!--{section name=cnt loop=$arrFollowMailProducts}-->
	    <!--▼対象購入商品<!--{$smarty.section.cnt.iteration}-->-->
	    <tr id="row_<!--{$smarty.section.cnt.index}-->">
			<!--{* 商品番号 *}-->
			<td class="id"><!--{$arrFollowMailProducts[cnt].product_code|h}--></td>
			<!--{* 商品名 *}-->
			<td><!--{$arrFollowMailProducts[cnt].product_name|h}--></td>
		</tr>
		<!--▲対象購入商品<!--{$smarty.section.cnt.iteration}-->-->
		<!--{/section}-->
	</table>
	<!--検索結果表示テーブル-->

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnModeSubmit('confirm_return','',''); return false;"><span class="btn-prev">前のページに戻る</span></a></li>

            <!--{if $tpl_update_auth == $smarty.const.UPDATE_AUTH_ON}-->
            <li><a class="btn-action" href="javascript:;" onclick="document.form1.submit(); return false;"><span class="btn-next">この内容で登録する</span></a></li>
            <!--{/if}-->
        </ul>
    </div>
</div>
</form>
