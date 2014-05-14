<!--{* -*- coding: utf-8-unix; -*- *}-->
<!--▼CONTENTS-->
メールアドレスとパスワードの変更を行います。<br>
<br>

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="confirm" />
<input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id|h}-->" />

<table width="100%" cellspacing="0" cellpadding="1" border="0">
<tr>
<td bgcolor="#efefef">
<font size="-1"><font color="#003b9b">▼</font>新しいメールアドレス</font><br>
<font size="-1" color="#ff0000"></font></td></tr>
</table>

<!--{assign var=key1 value="email"}-->
<!--{assign var=key2 value="email02"}-->
<!--{if $arrErr[$key1] || $arrErr[$key2]}-->
<font size="-1"><font color="#FF0000"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></font></font>
<!--{/if}-->
<input type="text" name="<!--{$key1}-->" id="email" value="<!--{$arrForm[$key1]|h}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;">
<br>
<br>

<table width="100%" cellspacing="0" cellpadding="1" border="0">
<tr>
<td bgcolor="#efefef">
<font size="-1"><font color="#003b9b">▼</font>新しいメールアドレスを確認のため再度ご入力ください。</font><br>
<font size="-1" color="#ff0000"></font></td></tr>
</table>

<input type="text" name="<!--{$key2}-->" id="email02" value="<!--{$arrForm[$key2]|h}-->" style="<!--{$arrErr[$key1]|cat:$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;">
<br>
<br>

<table width="100%" cellspacing="0" cellpadding="1" border="0">
<tr>
<td bgcolor="#efefef">
<font size="-1"><font color="#003b9b">▼</font>新しいパスワード:半角<!--{$smarty.const.PASSWORD_MIN_LEN}-->文字以上<!--{$smarty.const.PASSWORD_MAX_LEN}-->文字以内</font><br>
<font size="-1" color="#ff0000"></font></td></tr>
</table>

<!--{if $arrErr.password || $arrErr.password02}-->
<font size="-1"><font color="#FF0000"><!--{$arrErr.password}--><!--{$arrErr.password02}--></font></font>
<!--{/if}-->

<input type="password" name="password" value="<!--{$arrForm.password|h}-->" maxlength="<!--{$smarty.const.PASSWORD_MAX_LEN}-->" style="<!--{$arrErr.password|sfGetErrorColor}-->">
<br>
<br>

<table width="100%" cellspacing="0" cellpadding="1" border="0">
<tr>
<td bgcolor="#efefef">
<font size="-1"><font color="#003b9b">▼</font>新しいパスワードを確認のため再度入力ください。</font><br>
<font size="-1" color="#ff0000"></font></td></tr>
</table>

<input type="password" name="password02" value="<!--{$arrForm.password02|h}-->" maxlength="<!--{$smarty.const.PASSWORD_MAX_LEN}-->" style="<!--{$arrErr.password|cat:$arrErr.password02|sfGetErrorColor}-->">
<br>
<br>
<input type="submit" value="確認">
        </form>
<!--▲CONTENTS-->
