<!--▼検索バー -->
<!--{assign var=top value="`$smarty.const.ROOT_URLPATH`index.php"}-->
<!--{if $smarty.server.PHP_SELF == $top}-->
<section class="spInxKey">
	<h2><img src="<!--{$TPL_URLPATH}-->img/rohto/icon_keyword.png" width="15" height="13">ｷｰﾜｰﾄﾞ検索(商品名･商品ｺｰﾄﾞ)</h2>
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

<table cellpadding="0" cellspacing="0" class="btn">
	<tr>
		<td class="conL"><a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/history_list.php" class="btnGray03"><span class="spNaked"><img src="<!--{$TPL_URLPATH}-->img/rohto/icon_history.gif" alt="購入履歴" width="17" height="13"></span>購入履歴</a></td>
		<td class="conR"><a href="<!--{$smarty.const.ROOT_URLPATH}-->products/catalogue.php" class="btnGray03"><img src="<!--{$TPL_URLPATH}-->img/rohto/icon_mcform.gif" alt="商品番号から注文" width="19" height="13">商品番号から注文</a></td>
	</tr>
</table>

	</div>
<!--{assign var=top value="`$smarty.const.ROOT_URLPATH`index.php"}-->
<!--{if $smarty.server.PHP_SELF == $top}-->
</section>
<!--{/if}-->
<!--▲検索バー -->
