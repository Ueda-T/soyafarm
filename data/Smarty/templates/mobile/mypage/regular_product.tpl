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
<font size="-1">
<form name="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="product_select" />
<input type="hidden" name="product_mode" value="<!--{$tpl_product_mode}-->" />
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

<input type="hidden" name="next_arrival_date" value="<!--{$arrForm.next_arrival_date.value}-->" id="next_arrival_date" />
<input type="hidden" name="after_next_arrival_date" value="<!--{$arrForm.after_next_arrival_date.value}-->" id="after_next_arrival_date" />
<input type="hidden" name="course_cd" value="<!--{$arrForm.course_cd.value}-->" id="course_cd" />
<input type="hidden" name="todoke_kbn" value="<!--{$arrForm.todoke_kbn.value}-->" id="todoke_kbn" />
<input type="hidden" name="todoke_cycle" value="<!--{$arrForm.todoke_cycle.value}-->" id="todoke_cycle" />
<input type="hidden" name="todoke_week" value="<!--{$arrForm.todoke_week.value}-->" id="todoke_week" />
<input type="hidden" name="todoke_week2" value="<!--{$arrForm.todoke_week2.value}-->" id="todoke_week2" />
<input type="hidden" name="brand_id" value="<!--{$arrForm.brand_id.value}-->" id="brand_id" />
<input type="hidden" name="before_product_id" value="<!--{$arrForm.before_product_id.value}-->" id="before_product_id" />
<input type="hidden" name="before_product_class_id" value="<!--{$arrForm.before_product_class_id.value}-->" id="before_product_class_id" />
<input type="hidden" name="before_product_name" value="<!--{$arrForm.before_product_name.value}-->" id="before_product_name" />
<input type="hidden" name="after_product_id" value="<!--{$arrForm.after_product_id.value}-->" id="after_product_id" />
<input type="hidden" name="after_product_class_id" value="<!--{$arrForm.after_product_class_id.value}-->" id="after_product_class_id" />
<input type="hidden" name="after_product_name" value="<!--{$arrForm.after_product_name.value}-->" id="after_product_name" />
<input type="hidden" name="regular_quantity" value="<!--{$arrForm.regular_quantity.value}-->" id="regular_quantity" />
<input type="hidden" name="before_sale_minimum_number" value="<!--{$arrForm.before_sale_minimum_number.value}-->" id="before_sale_minimum_number" />
<input type="hidden" name="before_sale_limit" value="<!--{$arrForm.before_sale_limit.value}-->" id="before_sale_limit" />

<!--{section name=cnt loop=$arrForm.quantity.value}-->
<!--{assign var=product_index value="`$smarty.section.cnt.index`"}-->
<input type="hidden" name="product_name[<!--{$product_index}-->]" value="<!--{$arrForm.product_name.value[$product_index]|h}-->" id="product_name_<!--{$product_index}-->" />
<input type="hidden" name="classcategory_name1[<!--{$product_index}-->]" value="<!--{$arrForm.classcategory_name1.value[$product_index]|h}-->" id="classcategory_name1_<!--{$product_index}-->" />
<input type="hidden" name="classcategory_name2[<!--{$product_index}-->]" value="<!--{$arrForm.classcategory_name2.value[$product_index]|h}-->" id="classcategory_name2_<!--{$product_index}-->" />
<br />
<input type="hidden" name="product_type_id[<!--{$product_index}-->]" value="<!--{$arrForm.product_type_id.value[$product_index]|h}-->" id="product_type_id_<!--{$product_index}-->" />
<input type="hidden" name="product_id[<!--{$product_index}-->]" value="<!--{$arrForm.product_id.value[$product_index]|h}-->" id="product_id_<!--{$product_index}-->" />
<input type="hidden" name="product_class_id[<!--{$product_index}-->]" value="<!--{$arrForm.product_class_id.value[$product_index]|h}-->" id="product_class_id_<!--{$product_index}-->" />
<input type="hidden" name="point_rate[<!--{$product_index}-->]" value="<!--{$arrForm.point_rate.value[$product_index]|h}-->" id="point_rate_<!--{$product_index}-->" />
<!--{assign var=key value="price"}-->
<input type="hidden" name="<!--{$key}-->[<!--{$product_index}-->]" value="<!--{$arrForm[$key].value[$product_index]|h}-->" id="<!--{$key}-->_<!--{$product_index}-->" />
<!--{assign var=key value="quantity"}-->
<span class="attention"><!--{$arrErr[$key][$product_index]}--></span>
<input type="hidden" name="<!--{$key}-->[<!--{$product_index}-->]" value="<!--{$arrForm[$key].value[$product_index]|h}-->" id="<!--{$key}-->_<!--{$product_index}-->" />
<input type="hidden" name="sale_minimum_number[<!--{$product_index}-->]" value="<!--{$arrForm.sale_minimum_number.value[$product_index]|h}-->" id="sale_minimum_number_<!--{$product_index}-->" />
<input type="hidden" name="sale_limit[<!--{$product_index}-->]" value="<!--{$arrForm.sale_limit.value[$product_index]|h}-->" id="sale_limit_<!--{$product_index}-->" />
<!--{/section}-->
<input type="hidden" name="payment_id" value="<!--{$arrForm.payment_id.value}-->" id="payment_id" />

<!--{* 詳細画面へ戻る *}-->
<input type="submit" name="return" value="戻る"><br>
<br>

商品を選択してください。
<br>
<br>
<!--{section name=cnt loop=$arrProductList}-->
<!--{*
<img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrProductList[cnt].main_list_image|h}-->" width="60" >
*}-->

<!--{$arrProductList[cnt].product_name|h}-->　<!--{$arrProductList[cnt].product_class_name|h}--><br>
<input type="hidden" id="select_product_class_id" name="select_product_class_id" value="<!--{$arrProductList[cnt].product_class_id|h}-->" />

<input type='submit' name="product_select_<!--{$arrProductList[cnt].product_class_id|h}-->" value="選択">
<br>
<hr>
<!--{/section}-->

<!--{* 詳細画面へ戻る *}-->
<br>
<input type="submit" name="return" value="戻る"><br>
<br>
</font>
</form>
