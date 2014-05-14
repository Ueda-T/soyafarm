<!--{* -*- coding: utf-8-unix; -*- *}-->
<script type="text/javascript">
<!--

$(function() {
    // タブ表示（tabs）
    $("#ui-tab").tabs();
    $("#ui-tab .ui-tabs-nav").removeClass('ui-corner-all');
});

// -->
</script>

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
            <li><a class="btn-action" href="javascript:;" onclick="fnModeSubmit('confirm_return','',''); return false;"><span class="btn-prev">前のページに戻る</span></a></li>
        </ul>
    </div>

    <table>
        <tr>
            <th>ブランドコード</th>
            <td>
                <!--{$arrForm.brand_code|h}-->
            </td>
        </tr>
        <tr>
            <th>ブランド名</th>
            <td>
                <!--{$arrForm.brand_name|h}-->
            </td>
        </tr>
        <tr>
            <th>掲載開始日</th>
            <td>
                <!--{$arrForm.disp_start_date|h}-->
            </td>
        </tr>
        <tr>
            <th>掲載終了日</th>
            <td>
                <!--{$arrForm.disp_end_date|h}-->
            </td>
        </tr>
        <tr>
            <th>所属カテゴリ</th>
            <td>
                <!--{$arrForm.category_code|h}-->　<!--{$arrForm.category_name|h}-->
            </td>
        </tr>
        <tr>
            <th>親ブランド</th>
            <td>
				<!--{$arrForm.parent_brand_code|h}-->　<!--{$arrForm.parent_brand_name|h}-->
            </td>
        </tr>
        <tr>
            <th>並び順</th>
            <td>
				<!--{$arrForm.rank|h}-->
            </td>
        </tr>
        <tr>
            <th>商品表示件数</th>
            <td>
				<!--{$arrForm.product_disp_num|h}-->
            </td>
        </tr>
        <tr>
            <th>画像表示件数</th>
            <td>
				<!--{$arrForm.img_disp_num|h}-->
            </td>
        </tr>
        <tr>
            <th>METAタグ</th>
            <td>
				<!--{$arrForm.metatag|h}-->
            </td>
        </tr>
    </table>

	<div id="ui-tab" style="margin-bottom: 20px;">
	    <ul>
			<li><a href="#pc-tab"><span>PCサイト用</span></a></li>
			<li><a href="#sp-tab"><span>スマホサイト用</span></a></li>
			<li><a href="#mb-tab"><span>モバイルサイト用</span></a></li>
		</ul>
		<div id="pc-tab">
		    <table class="form">
				<tr>
					<th>コメント<br />（ブランド名の下）</th>
					<td><!--{$arrForm.pc_comment|h}--></td>
				</tr>
				<tr>
					<th>フリースペース１<br />※親ブランドで表示（ブランド名と切替）</th>
					<td><!--{$arrForm.pc_free_space1|h}--></td>
				</tr>
				<tr>
					<th>フリースペース２<br />※親ブランドで表示（ブランド一覧下部）</th>
					<td><!--{$arrForm.pc_free_space2|h}--></td>
				</tr>
				<tr>
					<th>フリースペース３<br />（ブランド名と切替）</th>
					<td><!--{$arrForm.pc_free_space3|h}--></td>
				</tr>
				<tr>
					<th>フリースペース４<br />（ブランドページ上部）</th>
					<td><!--{$arrForm.pc_free_space4|h}--></td>
				</tr>
				<tr>
					<th>フリースペース５<br />（ブランドページの下部）</th>
					<td><!--{$arrForm.pc_free_space5|h}--></td>
				</tr>
			</table>
		</div>
		<div id="sp-tab">
		    <table class="form">
				<tr>
					<th>コメント<br />（ブランド名の下）</th>
					<td><!--{$arrForm.sp_comment|h}--></td>
				</tr>
				<tr>
					<th>フリースペース１<br />※親ブランドで表示（ブランド名と切替）</th>
					<td><!--{$arrForm.sp_free_space1|h}--></td>
				</tr>
				<tr>
					<th>フリースペース２<br />※親ブランドで表示（ブランド一覧下部）</th>
					<td><!--{$arrForm.sp_free_space2|h}--></td>
				</tr>
				<tr>
					<th>フリースペース３<br />（ブランド名と切替）</th>
					<td><!--{$arrForm.sp_free_space3|h}--></td>
				</tr>
				<tr>
					<th>フリースペース４<br />（ブランドページ上部）</th>
					<td><!--{$arrForm.sp_free_space4|h}--></td>
				</tr>
				<tr>
					<th>フリースペース５<br />（ブランドページの下部）</th>
					<td><!--{$arrForm.sp_free_space5|h}--></td>
				</tr>
			</table>
		</div>
		<div id="mb-tab">
		    <table class="form">
				<tr>
					<th>コメント<br />（ブランド名の下）</th>
					<td><!--{$arrForm.mb_comment|h}--></td>
				</tr>
				<tr>
					<th>フリースペース１<br />※親ブランドで表示（ブランド名と切替）</th>
					<td><!--{$arrForm.mb_free_space1|h}--></td>
				</tr>
				<tr>
					<th>フリースペース２<br />※親ブランドで表示（ブランド一覧下部）</th>
					<td><!--{$arrForm.mb_free_space2|h}--></td>
				</tr>
				<tr>
					<th>フリースペース３<br />（ブランド名と切替）</th>
					<td><!--{$arrForm.mb_free_space3|h}--></td>
				</tr>
				<tr>
					<th>フリースペース４<br />（ブランドページ上部）</th>
					<td><!--{$arrForm.mb_free_space4|h}--></td>
				</tr>
				<tr>
					<th>フリースペース５<br />（ブランドページの下部）</th>
					<td><!--{$arrForm.mb_free_space5|h}--></td>
				</tr>
			</table>
		</div>
	</div>

    <div class="btn">
		<span>ブランド商品一覧</span>
    </div>

	<!--ブランド商品一覧-->
    <table class="list" id="brand-product-list">
		<colgroup width="15%">
		<colgroup width="85%">
		<tr>
			<th>商品番号</th>
			<th>商品名</th>
		</tr>

		<!--{section name=cnt loop=$arrBrandProducts}-->
	    <!--▼ブランド商品<!--{$smarty.section.cnt.iteration}-->-->
	    <tr id="row_<!--{$smarty.section.cnt.index}-->">
			<!--{* 商品番号 *}-->
			<td class="id"><!--{$arrBrandProducts[cnt].product_code|h}--></td>
			<!--{* 商品名 *}-->
			<td><!--{$arrBrandProducts[cnt].product_name|h}-->　<!--{$arrBrandProducts[cnt].product_class_name|h}--></td>
		</tr>
		<!--▲ブランド商品<!--{$smarty.section.cnt.iteration}-->-->
		<!--{/section}-->
	</table>
	<!--検索結果表示テーブル-->

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
