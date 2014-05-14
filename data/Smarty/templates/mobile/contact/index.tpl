<form name="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="confirm" />

件名:<br>
<font color="#FF0000"><!--{$arrErr.subject}--></font>
<select name="subject" style="<!--{$arrErr.subject|sfGetErrorColor}-->">
<option value="" selected="selected">↓お選びください</option>
<!--{html_options options=$arrSubject selected=$arrForm.subject}-->
</select>
<br>

お名前:<br>
<font color="#FF0000"><!--{$arrErr.name}--></font>
<input type="text" class="box150" name="name" value="<!--{$arrForm.name.value|h|default:$arrData.name|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name|sfGetErrorColor}-->;" />
<br>

ﾒｰﾙｱﾄﾞﾚｽ:<br>
<font color="#FF0000"><!--{$arrErr.email}--></font>
<input type="text" class="box380 top" name="email" value="<!--{$arrForm.email.value|h|default:$arrData.email|h}-->" style="<!--{$arrErr.email|sfGetErrorColor}-->;" /><br />

電話番号:<br>
<font color="#FF0000"><!--{$arrErr.tel}--></font>
<input type="text" class="box180" name="tel" value="<!--{$arrForm.tel.value|h|default:$arrData.tel|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN*3}-->" style="<!--{$arrErr.tel|sfGetErrorColor}-->;" />
<br>

内容:<br>
<font color="#FF0000"><!--{$arrErr.contents}--></font>
<textarea name="contents" class="box380" cols="20" rows="5" style="<!--{$arrErr.contents.value|h|sfGetErrorColor}-->; ime-mode: active;"><!--{$arrForm.contents.value|h}--></textarea>
<br>
<input type="hidden" name="now" value="<!--{$smarty.now|date_format:'%Y/%m/%d %H:%M:%S'}-->" />

<input type="submit" name="confirm" value="入力内容を確認" >


</form>
