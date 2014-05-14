<font size="-1"><br>
いつも、ロート通販をご利用いただきまして、誠にありがとうございます。<br>
2014年3月17日のシステムリニューアルに伴い、以前ご利用のお客さまには、初回ログイン時にパスワードの再設定を行っていただく必要がございます。<br>
大変お手数ですが、ご登録時のメールアドレスとご登録されたお名前を入力して「次へ」ボタンをクリックしてください。<br>
<br>
<b>初回ログイン用のパスワードをメールにて発行いたします。</b>
<br>
<!--{if $arrErr.name || $arrErr.email}-->
<font color="red">ご入力内容にエラーがあります。内容とエラーメッセージをご確認いただき、再度ご入力ください。</font>
<!--{/if}-->

<form action="?" method="post" name="form1">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="mail_check" />

<!--{if $errmsg}-->
<font color="red"><!--{$errmsg}--></font><br>
<!--{/if}-->

ご登録済みのメールアドレス<br>
<input type="text" name="email" value="<!--{$arrForm.email|default:$tpl_login_email|h}-->" class="box300" style="<!--{$arrErr.email|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;&nbsp;<font color="#ea673b">半角英数</font>
<br>
ご登録済みのお名前(漢字氏名)<br>
<input type="text" class="box240" name="name" value="<!--{$arrForm.name|default:''|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN*2}-->" style="<!--{$arrErr.name|sfGetErrorColor}-->; ime-mode: auto;" />&nbsp;&nbsp;<font color="#ea673b">例：呂登 太郎</font>
<div align="center">
<input type="submit" value="次へ" name="next" id="next" />
</div>
</form>
</font>
