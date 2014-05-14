<div align="center"><!--{$tpl_class_name1}--></div>
<hr>

<!--{if $arrErr.classcategory_id1 != ""}-->
<font color="#FF0000">※<!--{$tpl_class_name1}-->を入力して下さい｡</font><br>
<!--{/if}-->
<form method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<select name="classcategory_id1">
<option value="">選択してください</option>
<!--{html_options options=$arrClassCat1 selected=$arrForm.classcategory_id1.value}-->
</select><br>
<input type="hidden" name="mode" value="select2">
<input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->">
<input type="hidden" name="regular_flg" value="<!--{$arrForm.regular_flg.value}-->">
<center><input type="submit" name="submit" value="次へ"></center>
</form>
