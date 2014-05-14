<font size="-1" color="#ff0000">※新しくパスワードを発行いたしますので、お忘れになったパスワードはご利用できなくなります。</font>
<br>
<br>

<!--{if $errmsg}-->
<font size="-1" color="#ff0000"><!--{$errmsg}--></font><br>
<!--{/if}-->

<!--{if @$tpl_kara_mail_to != ''}-->
■ご登録時のメールアドレスからメールを送れる方は、次のリンクをクリックして空メールを送信してください。<br>
<br>
<center><a href="mailto:<!--{$tpl_kara_mail_to|u}-->">メール送信</a></center>

<br>

■メールを送れない方は、ご登録時のメールアドレスとお名前を入力して「次へ」ボタンをクリックしてください。<br>
<!--{else}-->
<font size="-1">ご登録時のメールアドレスとお名前を入力して「次へ」ボタンをクリックしてください。<br></font>
<!--{/if}-->
<br>

<form action="?" method="post">
<input type="hidden" name="mode" value="mail_check">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />

<font size="-1">メールアドレス：</font><br>
<font color="#FF0000"><!--{$arrErr.email}--></font><br>
<input type="text" name="email" value="<!--{$arrForm.email|default:$tpl_login_email|h}-->" size="40" istyle="3"><br>
<br>
<font size="-1">お名前：</font><br>
<font color="#FF0000"><!--{$arrErr.name}--></font><br>
<input type="text" name="name" value="<!--{$arrForm.name|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="1"><br>

<br>
<center><input type="submit" value="次へ" name="next"></center>
</form>
