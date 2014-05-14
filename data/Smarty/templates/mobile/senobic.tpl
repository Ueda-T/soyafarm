<!--{if count($arrProducts) > 0 }-->

<h1><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/mo/senobic/head.gif" width="100%" alt="成長期応援飲料セノビック"/></h1>
<font size="-1">セノビックは、1袋からご購入いただけるようになりました！お好きな味ごとに、ご注文袋数を指定して、ご購入数を選択してご注文ください。</font>
<p style="margin:10px 0;"><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/display_matome_mo.gif" width="100%" alt="セノビックは、おまとめ買いがお得！"/></p>

<br>
<br>
<!--{* エラーメッセージ *}-->
<font color="red"><!--{$tpl_err_msg|h}--></font>

<form name="form1" action="?" method="post">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />

<!--{foreach from=$arrProducts key=i item=arrProduct name=arrProduct}-->
<!--{assign var=id value=$arrProduct.product_id}-->
<!--{assign var=index value=`$smarty.foreach.arrProduct.index+1`}-->
<input type="hidden" name="product_class_id_<!--{$index}-->" value="<!--{$tpl_product_class_id[$id]}-->" />

<!--{if $smarty.foreach.arrProduct.iteration %2 == 0}-->
<table width="100%" border="0" cellpadding="1" cellspacing="0" bgcolor="#eaeaea" bordercolor="#eaeaea">
<!--{else}-->
<table width="100%" border="0" cellpadding="1" cellspacing="0" bgcolor="#ffffff" bordercolor="#ffffff">
<!--{/if}-->
<!-- ▼商品 ここから -->
<tr>
<td width="33%">
<img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH|sfTrimURL}-->/<!--{$arrProduct.main_list_image|sfNoImageMainList|h}-->" width="80" height="80">
</td>
<td>
<font size="-1">
<!-- 商品名 -->
<a href="<!--{$smarty.const.MOBILE_P_DETAIL_URLPATH}--><!--{$arrProduct.product_id|u}-->"><!--{$arrProduct.name|h}--></a>
<br>
<font color="#ff6600">
<!--★価格★-->
<!--{if $tpl_customer_kbn == $smarty.const.CUSTOMER_KBN_EMPLOYEE}-->
<!--{if $arrProduct.price02_min == $arrProduct.price02_max}-->
￥<!--{$arrProduct.price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->(税込)
<!--{else}-->
￥<!--{$arrProduct.price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～<!--{$arrProduct.price02_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->(税込)
<!--{/if}-->
<!--{else}-->
<!--{if $arrProduct.price01_min == $arrProduct.price01_max}-->
￥<!--{$arrProduct.price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->(税込)
<!--{else}-->
￥<!--{$arrProduct.price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～<!--{$arrProduct.price01_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->(税込)
<!--{/if}-->
<!--{/if}-->
</font>
</td>
</tr>
<td colspan="2"><font size="-1">
<!--{$arrProduct.main_list_comment}--><br>

<!--{* 数量選択 *}-->
<!--{assign var=class_id value=$tpl_product_class_id[$id]}-->
<select name="quantity_<!--{$index}-->" style="<!--{$arrErr.quantity|sfGetErrorColor}-->">
<!--{html_options options=$tpl_arrQuantity[$class_id] }-->
</select>

<br>
&nbsp;

</td>
</tr>
</table>
<!-- ▲商品 ここまで -->
<!--{/foreach}-->
<input type="hidden" name="product_index" value="<!--{$index}-->" />
<!--{if $tpl_customer_kbn != $smarty.const.CUSTOMER_KBN_EMPLOYEE}-->
<center><input type="submit" name="select_regular" value="定期購入する"></center>
<br>
<!--{/if}-->
<center><input type="submit" name="select" value="カートに入れる"></center>

<!--{else}-->
該当商品がありません。<br>
<!--{/if}-->
<br><br>
<font size="-1">ｾﾉﾋﾞｯｸをおﾄｸにお買い求めていただくためには、定期購入がお勧めです！</font>
<img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/senobic_cart_shaker_mo.gif" width="100%" alt=""/>
<font size="-1" color="#ff4200">ふるふるｼｪｲｶｰﾌﾟﾚｾﾞﾝﾄは一世帯一回一個限りとさせていただきます｡</font>

<table width="100%" border="0" cellpadding="1" cellspacing="0" bgcolor="#207ec6" bordercolor="#207ec6" style="margin:20px 0 0 0;">
<tr>
<td align="center" style="padding:10px;"><font color="#ffffff" size="-1">セノビックは、定期購入がもっとお得！<br>
袋数に応じてどんどんお得がUP!</font></td>
</tr>
</table>
<font color="#207ec6" size="-1">お得な定期購入のご紹介</font><br>
<font size="-1">定期購入は、おまとめ数「2袋」から「6袋」までのご注文を承っております。<br />
袋数に応じてどんどんお得がUP！<br />
毎日の栄養補助を続けるなら、ぜひお得な定期購入をご利用ください。</font>
<img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/line.gif" width="100%" height="3">
<table border="0" cellpadding="1" cellspacing="0" bgcolor="#ffffff" bordercolor="#ffffff" style="margin-bottom: 5px; margin-top: 5px;">
<tr>
<td valign="top"><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/fukuro2.gif" alt="" width="50"></td>
<td><font size="-1">【定期おまとめ2袋】なら､通常価格2,000円(税別)+送料が<font color="#207ec6"> もちろん送料無料！</font>で<font color="#d85d40">定期おまとめ価格2,000円(税別) </font> 毎回<font color="#d85d40">送料分</font>お得 </font></td>
</tr>
</table>
<img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/line.gif" width="100%" height="3">
<table border="0" cellpadding="1" cellspacing="0" bgcolor="#ffffff" bordercolor="#ffffff" style="margin-bottom: 5px; margin-top: 5px;">
<tr>
<td valign="top"><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/fukuro3.gif" alt="" width="50"></td>
<td><font size="-1">【定期おまとめ3袋】なら､通常価格3,000円(税別)+送料が<font color="#207ec6"> もちろん送料無料！</font>で<font color="#d85d40">定期おまとめ価格2,850円(</font><font size="-1"><font color="#d85d40">税別</font></font><font color="#d85d40">) </font> 毎回<font color="#d85d40">5%</font>お得 </font></td>
</tr>
</table>
<img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/line.gif" width="100%" height="3">

<table border="0" cellpadding="1" cellspacing="0" bgcolor="#ffffff" bordercolor="#ffffff" style="margin-bottom: 5px; margin-top: 5px;">
<tr>
<td valign="top"><img src="image/senobic/fukuro4.gif" alt="" width="50"></td>
<td><font size="-1">【定期おまとめ4袋】なら､通常価格4,000円(税別)+送料が<font color="#207ec6"> もちろん送料無料！</font>で<font color="#d85d40">定期おまとめ価格3,720円(</font><font size="-1"><font color="#d85d40">税別</font></font><font color="#d85d40">) </font> 毎回<font color="#d85d40">7%</font>お得 </font></td>
</tr>
</table>
<img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/line.gif" width="100%" height="3">

<table border="0" cellpadding="1" cellspacing="0" bgcolor="#ffffff" bordercolor="#ffffff" style="margin-bottom: 5px; margin-top: 5px;">
<tr>
<td valign="top"><img src="image/senobic/fukuro5.gif" alt="" width="50"></td>
<td><font size="-1">【定期おまとめ5袋】なら､通常価格5,000円(税別)+送料が<font color="#207ec6"> もちろん送料無料！</font>で<font color="#d85d40">定期おまとめ価格4,550円(</font><font size="-1"><font color="#d85d40">税別</font></font><font color="#d85d40">) </font> 毎回<font color="#d85d40">9%</font>お得 </font></td>
</tr>
</table>
<img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/line.gif" width="100%" height="3">

<table border="0" cellpadding="1" cellspacing="0" bgcolor="#ffffff" bordercolor="#ffffff" style="margin-bottom: 5px; margin-top: 5px;">
<tr>
<td valign="top"><img src="image/senobic/fukuro6.gif" alt="" width="50"></td>
<td><font size="-1">【定期おまとめ6袋】なら､通常価格6,000円(税別)+送料が<font color="#207ec6"> もちろん送料無料！</font>で<font color="#d85d40">定期おまとめ価格5,340円(</font><font size="-1"><font color="#d85d40">税別</font></font><font color="#d85d40">) </font> 毎回<font color="#d85d40">11%</font>お得 </font></td>
</tr>
</table>
<img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/line.gif" width="100%" height="3" style="margin-bottom: 10px;">

<h3 style="color:#207ec6; font-size:0.9em; margin:30px 0 15px 0;">人気商品につき、ご購入袋数制限のお願い</h3>
<font size="-1">月あたりの購入上限は、20袋までとさせていただいております。購入数量が上限を超えている場合には、ご連絡させていただきます場合がございますので、あしからず了承ください。</font>


<h3 style="color:#207ec6; font-size:0.9em; margin:30px 0 15px 0;">定期購入についての注意事項</h3>
<p class="attention"><font size="-1">･ 定期購入はいつでも解約可能ですが､原則として最低3回以上の継続をお願いします｡
</font></p>
<p class="attention"><font size="-1">･ 定期購入お届け間隔については､最大3ヵ月とさせていただきます｡</font></p>
<p class="attention"><font size="-1">･ 定期購入のお得が適用されるのは、お届け間隔が同じで、同じ日のお届けになるようにおまとめ購入いただいた場合となります。





<div align="right"><font size="-1">
<a href="#top"><font color="#003b9b">ﾍﾟｰｼﾞTOP▲</font></a>
</font></div>
<br>
