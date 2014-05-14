<form name="form1" id="form1" method="post" action="?">
  <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
	<input type="hidden" name="mode" value="complete">
	<!--{foreach from=$arrForm key=key item=item}-->
		<input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->">
	<!--{/foreach}-->
	下記の内容でご登録してもよろしいですか？<br>
	<br>

    顧客番号<br>
    <!--{$arrForm.customer_id|h}--><br>
    <hr>

	お名前<br>
	<!--{$arrForm.name|h}--><br>
    <hr>

	フリガナ<br>
	<!--{$arrForm.kana|h}--><br>
    <hr>

	電話番号<br>
    <!--{assign var=key1 value="`$prefix`tel"}-->
    <!--{$arrForm[$key1]|h}--><br>
    <hr>

	<!--{assign var=key1 value="zip"}-->
	郵便番号<br>
	<!--{$arrForm.zip|h}--><br>
    <hr>

	都道府県<br>
    <!--{$arrPref[$arrForm.pref]}--><br>
    <hr>

	市区町村<br>
	<!--{$arrForm.addr01|h}--><br>
    <hr>

	番地･ﾋﾞﾙ名<br>
	<!--{$arrForm.addr02|h}--><br>
    <hr>

	ﾒｰﾙｱﾄﾞﾚｽ<br>
	<!--{$arrForm.email|h}--><br>
    <hr>

	性別<br>
    <!--{if strlen($arrSex[$arrForm.sex]) > 0}--><!--{$arrSex[$arrForm.sex]}--><!--{else}-->未登録<!--{/if}--><br>
    <hr>

	生年月日<br>
    <!--{if strlen($arrForm.year) > 0 && strlen($arrForm.month) > 0 && strlen($arrForm.day) > 0}--><!--{$arrForm.year|h}-->年<!--{$arrForm.month|h}-->月<!--{$arrForm.day|h}-->日<!--{else}-->未登録<!--{/if}--></td>

    <hr>

	ﾒｰﾙのご案内<br>
    <!--{$arrMAILMAGATYPE[$arrForm.mailmaga_flg]}-->

	<br>

	<center>
	<input type="submit" name="submit" value="変更"><br>
	<input type="submit" name="return" value="戻る">
	</center>
</form>
