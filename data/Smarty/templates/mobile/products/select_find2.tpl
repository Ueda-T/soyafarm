<div align="center"><!--{$tpl_class_name2}--></div>
<hr>

<!--{if $arrErr.classcategory_id2 != ""}-->
<font color="#FF0000">※<!--{$tpl_class_name2}-->を入力して下さい｡</font><br>
<!--{/if}-->
<form method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<select name="classcategory_id2">
    <option value="">選択してください</option>
    <!--{html_options options=$arrClassCat2 selected=$arrForm.classcategory_id2.value}-->
</select><br>
<input type="hidden" name="mode" value="selectItem">
<input type="hidden" name="classcategory_id1" value="<!--{$arrForm.classcategory_id1.value}-->">
<input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->">
<input type="hidden" name="regular_flg" value="<!--{$arrForm.regular_flg.value}-->">
<center><input type="submit" name="submit" value="次へ"></center>
</form>
