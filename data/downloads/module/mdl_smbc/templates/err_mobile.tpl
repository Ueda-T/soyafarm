<form method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="return">
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
<input type="hidden" name="payment_id" value="<!--{$arrForm.payment_id.value}-->">

選択いただきましたお支払方法はモバイルサイトではお使いいただけません。<br>
お手数をおかけし申し訳ございませんが、別の決済方法を選択していただけますでしょうか。
<br>

<center><input type="submit" name="return" value="戻る"></center>
</form>
