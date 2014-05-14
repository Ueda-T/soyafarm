<!--▼CONTENTS-->
<font size="-1"><font color="#FF0000">登録住所を削除する場合には､画面下部の｢削除する｣ﾎﾞﾀﾝを押してください｡</font></font><br>
<br>

<hr>

<form name="form1" method="post" action="?" >
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="delete">
<input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->">
<input type="hidden" name="other_deliv_id" value="<!--{$arrDeliv.other_deliv_id}-->">

お名前:<br>
<!--{$arrDeliv.name}--><br>
<br>
ﾌﾘｶﾞﾅ:<br>
<!--{$arrDeliv.kana}--><br>
<br>
郵便番号:<br>
<!--{$arrDeliv.zip}--><br>
<br>
都道府県:<br>
<!--{$arrPref[$arrDeliv.pref]}--><br>
<br>
市区町村:<br>
<!--{$arrDeliv.addr01}--><br>
<br>
番地:<br>
<!--{$arrDeliv.addr02}--><br>
<br>
電話番号:<br>
<!--{$arrDeliv.tel}--><br>
<br>

<input type="submit" value="削除する">
</form>

<br>
<!--▲CONTENTS-->
