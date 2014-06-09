<!--▼検索バー -->
<!--{assign var=top value="`$smarty.const.ROOT_URLPATH`index.php"}-->
<!--{if $smarty.server.PHP_SELF == $top}-->
<section class="spInxKey">
	<h2><img src="<!--{$TPL_URLPATH}-->img/rohto/icon_keyword.png" width="15" height="13">商品名で検索</h2>
<!--{else}-->
<h3 class="line">ｷｰﾜｰﾄﾞで探す</h3>
<!--{/if}-->
	<div class="keyword">
		<form method="get" action="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php">
			<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td class="conL"><input type="text" name="name" value="" data-role="none" size="16" style="box-sizing:border-box;"></td>
					<td class="conR btn"><input type="submit" name="search" value="検索" class="btnGray02" data-role="none"></td>
				</tr>
			</table>
		</form>
	</div>
<!--{assign var=top value="`$smarty.const.ROOT_URLPATH`index.php"}-->
<!--{if $smarty.server.PHP_SELF == $top}-->
</section>
<!--{/if}-->
<!--▲検索バー -->
