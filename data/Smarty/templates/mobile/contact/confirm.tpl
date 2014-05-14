
下記入力内容で送信してもよろしいでしょうか？<br />
よろしければ、一番下の「送信」ボタンをクリックしてください。
<br>
<br>

<form name="form1" method="post" action="?">
<input type="hidden" name="token" value="<!--{$token}-->" />
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="complete" />
<!--{foreach key=key item=item from=$arrForm}-->
<!--{if $key ne 'mode'}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item.value|h}-->" />
<!--{/if}-->
<!--{/foreach}-->

件名:<br>
<!--{$arrSubject[$arrForm.subject.value]}-->
<br>
<br>

お名前:<br>
<!--{$arrForm.name.value|h}-->
<br>
<br>

ﾒｰﾙｱﾄﾞﾚｽ:<br>
<!--{$arrForm.email.value|escape:'hexentity'}-->
<br>
<br>

電話番号:<br>
<!--{if strlen($arrForm.tel.value) > 0 }-->
<!--{$arrForm.tel.value|h}-->
<!--{/if}-->
<br>
<br>

お問い合わせ日:<br>
<!--{$arrForm.now.value|h}-->
<br>
<br>

内容:<br>
<!--{$arrForm.contents.value|h|nl2br}-->
<br>
<br>

<input type="submit" name="confirm" value="送信" >


</form>
