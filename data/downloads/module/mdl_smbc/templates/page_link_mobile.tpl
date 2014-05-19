<form name="form1" method="POST" action="<!--{$server_url}-->" accept-charset="Shift_JIS">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<!--{foreach from=$arrParam key=key item=val}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$val}-->">
<!--{/foreach}-->

決済情報を送信します。<br>
送信ボタンを押してください。<br>
<br>
<center><input type="submit" value="送信"></center>
</form>
