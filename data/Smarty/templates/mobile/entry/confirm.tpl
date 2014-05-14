<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="mode" value="complete">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<!--{foreach from=$arrForm key=key item=item}-->
	<input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->">
<!--{/foreach}-->
下記の内容でご登録してもよろしいですか？<br>
<br>

●漢字氏名<br>
<!--{$arrForm.name|h}--><br>
<br>

●カタカナ氏名<br>
<!--{$arrForm.kana|h}--><br>
<br>

●電話番号<br>
<!--{$arrForm.tel|h}--><br>
<br>

●住所<br>
〒<!--{$arrForm.zip|h}--><br>
<!--{$arrPref[$arrForm.pref]|h}--><!--{$arrForm.addr01|h}--><!--{$arrForm.addr02|h}--><br>
<br>

●メールアドレス<br>
<!--{$arrForm.email|escape:'hexentity'}--><br>
<br>

●パスワード<br>
<!--{$passlen}--><br>
<br>

●性別<br>
<!--{if $arrForm.sex eq 1}-->
男性
<!--{elseif $arrForm.sex eq 2}-->
女性
<!--{else}-->
未登録
<!--{/if}--><br>
<br>

●生年月日<br>
<!--{if strlen($arrForm.year) > 0 && strlen($arrForm.month) > 0 && strlen($arrForm.day) > 0}-->
<!--{$arrForm.year|h}-->年<!--{$arrForm.month|h}-->月<!--{$arrForm.day|h}-->日
<!--{else}-->
未登録
<!--{/if}--><br>

<!--{if $arrForm.other_addr_flg}-->
<br>

●お届け先：漢字氏名<br>
<!--{$arrForm.shipping_name|h}--><br>
<br>

●お届け先：ｶﾀｶﾅ氏名<br>
<!--{$arrForm.shipping_kana|h}--><br>
<br>

●お届け先：電話番号<br>
<!--{$arrForm.shipping_tel|h}--><br>
<br>

●お届け先：住所<br><br>
〒<!--{$arrForm.shipping_zip|h}--><br>
<!--{$arrPref[$arrForm.shipping_pref]|h}--><!--{$arrForm.shipping_addr01|h}--><!--{$arrForm.shipping_addr02|h}--><br>
<!--{/if}-->
<br>

<center>
<input type="submit" name="submit" value="登録"><br>
<input type="submit" name="return" value="戻る">
</center>
</form>
