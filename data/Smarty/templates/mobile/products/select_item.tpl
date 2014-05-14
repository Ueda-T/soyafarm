<div align="center">数量指定</div>
<hr>

<!--{if $arrErr.classcategory_id2 != ""}-->
<font color="#FF0000">※数量を入力して下さい｡</font><br>
<!--{/if}-->
<form method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="text" name="quantity" size="3" value="<!--{$arrForm.quantity.value|default:1|h}-->" maxlength=<!--{$smarty.const.INT_LEN}--> istyle="4"><br>
<input type="hidden" name="mode" value="cart">
<input type="hidden" name="classcategory_id1" value="<!--{$arrForm.classcategory_id1.value}-->">
<input type="hidden" name="classcategory_id2" value="<!--{$arrForm.classcategory_id2.value}-->">
<input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->">
<input type="hidden" name="product_class_id" value="<!--{$tpl_product_class_id}-->">
<input type="hidden" name="product_type" value="<!--{$tpl_product_type}-->">
<input type="hidden" name="regular_flg" value="<!--{$arrForm.regular_flg.value}-->">
<center><input type="submit" name="submit" value="かごに入れる"></center>
</form>
