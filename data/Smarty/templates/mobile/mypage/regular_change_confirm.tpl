<font size="-1">
<!--{$CustomerName|h}--> 様<br><br>
お客様番号:<!--{$CustomerId}--><br>
</font>
<!--★現在のポイント★-->
<!--{if $smarty.const.USE_POINT !== false && $CustomerPoint}-->
<table border="0" cellpadding="2" cellspacing="0" width="100%">
<tr>
<td bgcolor="#ff8a00"><font color="#ffffff" size="-1">
現在の通販ポイント残高:&nbsp;<!--{$CustomerPoint|number_format|default:"0"|h}-->&nbsp;ポイント
</td>
</tr>
<!--{if $CustomerPointValidDate neq ""}-->
<tr>
<td bgcolor="#ffe9cf">
<font color="#666666" size="-2">
<!--{$CustomerPointValidDate|date_format:"%Y年%m月%d日"}-->で<!--{$CustomerPoint|number_format|default:"0"|h}-->ポイントが消滅します。<br>
※期限までにご利用がない場合、ポイントは消滅します。</font>
</td>
</tr>
<!--{/if}-->
</table>
<!--{/if}-->

<table width="100%" bordercolor="#dfedf5" border="0" bgcolor="#dfedf5" cellspacing="0" cellpadding="1">
<tr>
<th align="left"><h2><font size="-1">
定期購入変更手続き</font></h2></th>
</tr>
</table>
<font size="-1">
<form name="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="complete" />
<input type="hidden" name="regular_id" value="<!--{$arrForm.regular_id.value}-->" />
<input type="hidden" name="line_no" value="<!--{$arrForm.line_no.value}-->" />
<input type="hidden" name="pageno" value="<!--{$objNavi->nowpage}-->" />

<input type="hidden" name="todoke_day" value="<!--{$arrForm.todoke_day.value}-->" />
<input type="hidden" name="status" value="<!--{$arrForm.status.value}-->" />
<input type="hidden" name="cancel_date" value="<!--{$arrForm.cancel_date.value}-->" />
<input type="hidden" name="cancel_reason_cd" value="<!--{$arrForm.cancel_reason_cd.value}-->" />
<input type="hidden" name="deliv_date_id" value="<!--{$arrForm.deliv_date_id.value}-->" />

<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />

<input type="hidden" name="anchor_key" value="" />
<input type="hidden" id="add_product_id" name="add_product_id" value="" />
<input type="hidden" id="add_product_class_id" name="add_product_class_id" value="" />
<input type="hidden" id="edit_product_id" name="edit_product_id" value="" />
<input type="hidden" id="edit_product_class_id" name="edit_product_class_id" value="" />
<input type="hidden" id="no" name="no" value="" />
<input type="hidden" id="delete_no" name="delete_no" value="" />

[お届けスケジュール]<br>
 
 次回お届け日<br>
<input type="hidden" name="next_arrival_date" value="<!--{$arrForm.next_arrival_date.value}-->" id="next_arrival_date" />
<!--{$arrForm.next_arrival_date.value|date_format:"%Y年%m月%d日"|h}--><br>
<br>
 次々回お届け日<br>
 <input type="hidden" name="after_next_arrival_date" value="<!--{$arrForm.after_next_arrival_date.value}-->" id="after_next_arrival_date" />
 <!--{$arrForm.after_next_arrival_date.value|date_format:"%Y年%m月%d日"|h}--><br>

<br>

<input type="hidden" name="course_cd" value="<!--{$arrForm.course_cd.value}-->" id="course_cd" />
<input type="hidden" name="todoke_kbn" value="<!--{$arrForm.todoke_kbn.value}-->" id="todoke_kbn" />
<input type="hidden" name="todoke_cycle" value="<!--{$arrForm.todoke_cycle.value}-->" id="todoke_cycle" />
<input type="hidden" name="todoke_week" value="<!--{$arrForm.todoke_week.value}-->" id="todoke_week" />
<input type="hidden" name="todoke_week2" value="<!--{$arrForm.todoke_week2.value}-->" id="todoke_week2" />

<!--{$arrCourseCd[$arrForm.course_cd.value]|h}--><!--{$arrTodokeKbn[$arrForm.todoke_cycle.value]|h}-->

<!--{if $arrForm.todoke_week.value}-->
<!--{$arrTodokeWeekNo[$arrForm.todoke_week.value]|h}--><!--{$arrTodokeWeek[$arrForm.todoke_week2.value]|h}-->曜日
<!--{/if}-->


<br>
<br>

[定期購入商品変更]<br></font>
<input type="hidden" name="brand_id" value="<!--{$arrForm.brand_id.value}-->" id="brand_id" />

<!--{foreach from=$arrCartKeys item=key}-->
<!--{foreach from=$arrCart[$key] item=item name=product}-->
<!--{if $smarty.foreach.product.iteration %2 == 0}-->
<table width="100%" border="0" cellpadding="1" cellspacing="0" bgcolor="#eaeaea" bordercolor="#eaeaea">
<!--{else}-->
<table width="100%" border="0" cellpadding="1" cellspacing="0" bgcolor="#ffffff" bordercolor="#ffffff">
<!--{/if}-->
<tr valign="top">
<td width="33%"><font size="-1">商品名</font></td>
<td><font size="-1">
◎<!--{* 商品名 *}--><!--{$item.productsClass.name|h}--><br>
<!--{* 規格名1 *}--><!--{if $item.productsClass.classcategory_name1 != ""}--><!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--><br><!--{/if}-->
<!--{* 規格名2 *}--><!--{if $item.productsClass.classcategory_name2 != ""}--><!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}--><br><!--{/if}-->
</td>
</tr>
<tr>
<td width="33%"><font size="-1">単価(税込)</font></td>
<td><font size="-1">
<!--{* 販売価格 *}-->
<!--{$item.price|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
</td>
</tr>
<td width="33%"><!--{* 数量 *}--><font size="-1">数量</font></td>
<td><font size="-1"><!--{$item.quantity}-->
</td>
</tr>
<tr>

<!--{assign var=sub_total value=$sub_total+$item.total_inctax}-->
<td width="33%"><!--{* 合計 *}--><font size="-1">小計(税込)</font></td>
<td><font color="#ff6600" size="-1"><!--{$item.total_inctax|number_format}-->円<br>
</font>
</td>
</tr>
</table>
<!--{/foreach}-->
<!--{/foreach}-->
<font size="-1">
<br>

<table width="100%" border="0" cellpadding="1" cellspacing="0" bgcolor="#ffce00" bordercolor="#ffce00">
<tr>
<td colspan="2" align="center"><font size="-1">
商品合計(税込)<font color="#cc0000"><!--{$sub_total|number_format}-->円</font>
＋
送料
<font color="#cc0000"><!--{$arrData.deliv_fee|number_format}-->円</font>

<br>
</font></td>
</tr>
</table>
<br>

[お支払方法]<br>
<input type="hidden" name="payment_id" value="<!--{$arrForm.payment_id.value}-->" id="payment_id" />
<!--{$arrPayment[$arrForm.payment_id.value]|h}-->

<br>
</font>
<input type="submit" name="return" value="戻る"><br>
<input type="submit" name="submit" value="変更を確定する"><br>

</form>
