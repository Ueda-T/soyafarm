<div class="sagasu">
	<form name="search_form_bloc" id="search_form_bloc" method="get" action="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php">
		<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
		<input type="hidden" name="mode" value="search" />
		<select name="category_id" class="tree">
			<option label="カテゴリ指定なし" value="">カテゴリ指定なし</option>
			<!--{html_options options=$arrCatList selected=$category_id}-->
		</select>
	   <!--{if $arrBrandList}-->
			<select name="brand_id" class="brand">
				<option label="ブランド指定なし" value="">ブランド指定なし</option>
				<!--{html_options options=$arrBrandList selected=$brand_id}-->
			</select>
		<!--{/if}-->
	   <!--{if $arrMakerList}-->
			<select name="maker_id" class="maker">
				<option label="メーカー指定なし" value="">メーカー指定なし</option>
				<!--{html_options options=$arrMakerList selected=$maker_id}-->
			</select>

		<!--{/if}-->
		<input type="text" name="name" class="search" maxlength="50" value="<!--{$smarty.get.name|h}-->" />
		<a href="javascript:void(0);" onclick="document.search_form_bloc.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/rohto/gnav_btn_kensaku.gif" src="swp" /></a>
	</form>
</div>
