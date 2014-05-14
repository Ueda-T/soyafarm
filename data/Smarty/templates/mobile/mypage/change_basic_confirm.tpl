<!--▼CONTENTS-->
以下のご登録内容をご確認いただき、よろしければ「登録」ボタンを押してください。<br>
<br>

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="complete" />
<input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id|h}-->" />

<!--{foreach from=$arrForm key=key item=item}-->
<!--{if $key ne "mode" && $key ne "subm"}-->
<input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->">
<!--{/if}-->
<!--{/foreach}-->
<!--{if $arrForm.email}-->
<table width="100%" cellspacing="0" cellpadding="1" border="0">
<tr>
<td bgcolor="#efefef">
<font size="-1"><font color="#003b9b">▼</font>新しいメールアドレス</font><br>
<font size="-1" color="#ff0000"></font></td></tr>
</table>
<!--{$arrForm.email|escape:'hexentity'}-->
<br>
<br>
<!--{/if}-->
<!--{if $arrForm.password}-->
<table width="100%" cellspacing="0" cellpadding="1" border="0">
<tr>
<td bgcolor="#efefef">
<font size="-1"><font color="#003b9b">▼</font>新しいパスワード:半角英数字<!--{$smarty.const.PASSWORD_MIN_LEN}-->文字以上<!--{$smarty.const.PASSWORD_MAX_LEN}-->文字以内</font><br>
<font size="-1" color="#ff0000"></font></td></tr>
</table>

<!--{$passlen}-->
<br><br>
<!--{/if}-->

<input type="submit" value="登録">

</form>
</div>
</div>
<!--▲CONTENTS-->
