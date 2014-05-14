<form name="search_form" id="search_form" method="get" action="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php">
<input type="hidden" name="transactionid" value="<!--{$transactionid}-->">
<input type="hidden" name="mode" value="search">
<!--{assign var=top value="`$smarty.const.ROOT_URLPATH`index.php"}-->
<!--{if $smarty.server.PHP_SELF == $top}-->
<table width="100%" border="0" cellpadding="1" cellspacing="0" bgcolor="#dfedf5" bordercolor="#dfedf5">
<!--{else}-->
<hr color="#dfedf5">
<table width="100%" border="0" cellpadding="1" cellspacing="0">
<!--{/if}-->
<tr>
<td colspan="2"><font size="-2">
<font color="blue">[emoji:119]</font>ｷｰﾜｰﾄﾞ検索(商品名･商品ｺｰﾄﾞ)
</font></td>
</tr>
<tr>
<td align="center"><font size="-1">
<input type="text" name="name" value="" size="16"><input type="submit" name="search" value="検索"><br>
</font></td>
</tr>
<tr>
<td align="center" colspan="2"><font size="-2">
[emoji:176]<a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/history_list.php">購入履歴</a>|<font color="orange">[emoji:65]</font><a href="<!--{$smarty.const.ROOT_URLPATH}-->products/catalogue.php">ｸｲｯｸ注文</a>
</font></td>
</tr>
</table>
</form>
