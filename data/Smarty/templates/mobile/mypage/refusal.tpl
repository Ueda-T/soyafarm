会員から登録削除をされますと、登録されているお届け先の情報など全て削除されますがよろしいでしょうか？<br>
<br>
<div align="center">
<form action="?" method="post">
    <input type="hidden" name="mode" value="complete">
    <input type="hidden" name="refusal_transactionid" value="<!--{$refusal_transactionid}-->" />
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />

	<input type="submit" name="complete" value="登録削除する">
</form>
</div>
<br>
