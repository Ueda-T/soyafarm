<form name="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="edit">
<input type="hidden" name="other_deliv_id" value="<!--{$smarty.session.other_deliv_id}-->" />
<input type="hidden" name="ParentPage" value="<!--{$ParentPage}-->">

<font size="-1" color="#FF0000">*は必須項目です。</font><br>
<hr>
<font size="-1" color="#FF0000"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></font>
<font size="-1">お名前 <font color="#FF0000">*</font></font><br>
<!--{assign var=key1 value="`$prefix`name"}-->
<!--{if $arrErr[$key1]}-->
<font color="#FF0000"><!--{$arrErr[$key1]}--></font>
<!--{/if}-->
<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="1">
<font size="-1" color="#666666"><!--{$smarty.const.SAMPLE_NAME}--></font><br>

<font size="-1">フリガナ <font color="#FF0000">*</font></font><br>
<!--{assign var=key1 value="`$prefix`kana"}-->
<!--{if $arrErr[$key1]}-->
<font color="#FF0000"><!--{$arrErr[$key1]}--></font>
<!--{/if}-->
<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="2">
<font size="-1" color="#666666"><!--{$smarty.const.SAMPLE_KANA}--></font><br>
<br>

<!--{assign var=key value="zip"}-->
<!--{assign var=key3 value="`$prefix`pref"}-->
<!--{assign var=key4 value="`$prefix`addr01"}-->
<!--{assign var=key5 value="`$prefix`addr02"}-->
<font size="-1">郵便番号 <font color="#FF0000">*</font></font><br>
<!--{if $arrErr[$key]}-->
<font color="#FF0000"><!--{$arrErr[$key]}--></font>
<!--{/if}-->
〒<input type="text" name="<!--{$key}-->" id="<!--{$key}-->" value="<!--{$arrForm[$key]|h}-->" maxlength="<!--{math equation="a+b" a=$smarty.const.ZIP_LEN b=1}-->" size="<!--{$smarty.const.ZIP_LEN+2}-->">
<font size="-1" color="#666666"><!--{$smarty.const.SAMPLE_ZIP}--></font><br>

<font size="-1">都道府県<font color="#FF0000">*</font></font><br>
<!--{if $arrErr[$key3] || $arrErr[$key4] || $arrErr[$key5]}-->
<font color="#FF0000"><!--{$arrErr[$key3]}--><!--{$arrErr[$key4]}--><!--{$arrErr[$key5]}--></font>
<!--{/if}-->
<select name="<!--{$key3}-->" id="pref" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->">
<option value="" selected="selected">都道府県を選択</option>
<!--{html_options options=$arrPref selected=$arrForm[$key3]}-->
</select><br>

<font size="-1">市区町村<font color="#FF0000">*</font></font><br>
<font color="#FF0000"><!--{$arrErr.addr01}--></font>
<input type="text" name="<!--{$key4}-->" id="addr1" value="<!--{$arrForm[$key4]|h}-->" maxlength="40" istyle="1">
<font size="-1" color="#666666"><!--{$smarty.const.SAMPLE_ADDRESS1}--></font><br>

<font size="-1">番地<font color="#FF0000">*</font></font><br>
<input type="text" name="<!--{$key5}-->" id="addr2" value="<!--{$arrForm[$key5]|h}-->" maxlength="40"><input type="checkbox" name="house_no" id="house_no"><font size="-1">番地なし</font>
<font size="-1" color="#666666"><!--{$smarty.const.SAMPLE_ADDRESS2}--></font><br>
<font size="-1">住所は2つに分けてご記入ください。マンション名は必ず記入してください。</font><br>

<font size="-1">電話番号<font color="#FF0000">*</font></font><br>
<!--{assign var=key1 value="`$prefix`tel"}-->
<!--{if $arrErr[$key1]}-->
<font color="#FF0000"><!--{$arrErr[$key1]}--></font>
<!--{/if}-->
<input type="text" name="<!--{$key1}-->" id="tel" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN*3}-->" size="<!--{$smarty.const.TEL_ITEM_LEN*3}-->">
<font size="-1" color="#666666"><!--{$smarty.const.SAMPLE_TEL}--></font><br>

<br>

<div align="center"><input type="submit" name="submit" value="次へ"></div>

<!--{foreach from=$list_data key=key item=item}-->
<input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->">
<!--{/foreach}-->
</form>

