<div id="pankuzu">
<table width="100%" cellspacing="0" cellpadding="1" bordercolor="#dfedf5" border="0" bgcolor="#dfedf5"><tr><td><font size="-1"><!--{$TopicPath}--></font></td></tr></table>
</div>

<!--{if $arrBrand.mb_free_space3}-->
<!--{$arrBrand.mb_free_space3}-->
<!--{else}-->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<th bgcolor="#dfedf5" align="left"><h2><font size="-1"><!--{$arrBrand.brand_name|h}--></font></h2></th>
</tr>
</table>
<!--{/if}-->
<!--{$arrBrand.mb_comment}-->
<!--{$arrBrand.mb_free_space4}-->

<!--{if !$tpl_child_brand}-->
<table width="100%" cellspacing="0" cellpadding="1" bordercolor="#003b9b" border="0" bgcolor="#003b9b">
<tr>
<th align="center" colspan="2"><h2><font size="-1" color="#ffffff"><!--{$arrBrand.brand_name|h}-->&nbsp;商品一覧</font></h2></th>
</tr>
</table>
<!--{/if}-->

<!--{foreach from=$arrChildBrand item=childBrand name=arrChildBrand}-->

<!--{if $tpl_child_brand}-->
	<!--{if $childBrand.mb_free_space1}-->
		<!--{$childBrand.mb_free_space1}-->
	<!--{else}-->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<th bgcolor="#dfedf5" align="left"><h2><font size="-1"><!--{$childBrand.brand_name|h}--></font></h2></th>
</tr>
</table>
	<!--{/if}-->
<!--{/if}-->

<!--{assign var=brand_id value=$childBrand.brand_id}-->
<!--{foreach from=$arrBrandProduct[$brand_id] item=arrProduct name=arrProducts}-->
<!--{assign var=id value=$arrProduct.product_id}-->
	<!--{if $childBrand.product_disp_num}-->
		<!--{if $smarty.foreach.arrProducts.iteration > $childBrand.product_disp_num}-->
			<!--{php}-->break;<!--{/php}-->
		<!--{/if}-->
	<!--{/if}-->
	<!--{if $smarty.foreach.arrProducts.iteration %2 == 0}-->
<table width="100%" border="0" cellpadding="1" cellspacing="0" bgcolor="#eaeaea" bordercolor="#eaeaea">
	<!--{else}-->
<table width="100%" border="0" cellpadding="1" cellspacing="0" bgcolor="#ffffff" bordercolor="#ffffff">
	<!--{/if}-->
<!-- ▼商品 ここから -->
<tr>
<!--{assign var=img_flg value=1}-->
<!--{if $childBrand.img_disp_num}-->
	<!--{if $smarty.foreach.arrProducts.iteration > $childBrand.img_disp_num}-->
		<!--{assign var=img_flg value=0}-->
	<!--{/if}-->
<!--{/if}-->
<!--{if $img_flg}-->
<td width="33%">
<img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH|sfTrimURL}-->/<!--{$arrProduct.main_list_image|sfNoImageMainList|h}-->" width="80">
</td>
<td>
<!--{else}-->
<td colspan="2">
<!--{/if}-->
<font size="-1">
<!-- 商品名 -->
<a href="<!--{$smarty.const.MOBILE_P_DETAIL_URLPATH}--><!--{$arrProduct.product_id|u}-->"><!--{if isset($arrProduct.disp_name)}--><!--{$arrProduct.disp_name|h}--><!--{else}--><!--{$arrProduct.name|h}--><!--{/if}--></a>
<br>
<font color="#ff6600">
<!--★価格★-->
<!--{if strlen($tpl_customer_kbn) == null || $tpl_customer_kbn == $smarty.const.CUSTOMER_KBN_NORMAL}-->
￥
<!--{if $arrProduct.price01_min == $arrProduct.price01_max}-->
<!--{$arrProduct.price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
<!--{else}-->
<!--{$arrProduct.price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～<!--{$arrProduct.price01_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
<!--{/if}-->
(税込)
<!--★社員価格★-->
<!--{elseif $tpl_customer_kbn == $smarty.const.CUSTOMER_KBN_EMPLOYEE }-->
￥
<!--{if $arrProduct.price02_min == $arrProduct.price02_max}-->
<!--{$arrProduct.price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
<!--{else}-->
<!--{$arrProduct.price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～<!--{$arrProduct.price02_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
<!--{/if}-->
(税込)
<!--{/if}-->
</font>
</td>
</tr>
<td colspan="2"><font size="-1">
<!--{$arrProduct.main_list_comment}--><br>

<!--{* 規格の有無を判定 *}-->
<!--{if $tpl_classcat_find1[$arrProduct.product_id] === false && $tpl_classcat_find2[$arrProduct.product_id] === false }-->
<!--{assign var=mode value="cart"}--><!--{* カートへ遷移 *}-->
<!--{else}-->
<!--{assign var=mode value="select"}--><!--{* 規格選択へ遷移 *}-->
<!--{/if}-->

<!--{if $tpl_stock_find[$id]}-->
<!--{* ▼在庫がある場合 *}-->

<!--{if $arrProduct.sale_start_date && $arrProduct.sale_start_date > $smarty.now|date_format:"%Y-%m-%d 00:00:00"}-->
<!--{** ▼販売期間前の表示 **}-->
<font color="#ff0000"><font color="red">[emoji:69]</font></font><a href="<!--{$smarty.const.ROOT_URLPATH}-->products/detail.php?mode=<!--{$mode}-->&product_id=<!--{$arrProduct.product_id|u}-->&quantity=1&<!--{$smarty.const.TRANSACTION_ID_NAME}-->=<!--{$transactionid}-->"><font color="#ff6600">予約する≫</font></a>
<!--{** ▲販売期間前の表示 **}-->

<!--{else}-->
<!--{** ▼販売期間中の表示 **}-->
<!--{if strlen($arrProduct.sale_end_date) == 0 || $arrProduct.sale_end_date >= $smarty.now|date_format:"%Y-%m-%d"}--><!--{** ▼販売期間中のみボタンを表示 **}-->

<!--{* 定期購入ボタン *}-->
<!--{if $arrProduct.teiki_flg == $smarty.const.REGULAR_PURCHASE_FLG_ON}-->
<!--{* 社員は定期購入不可 *}-->
<!--{if $tpl_customer_kbn != $smarty.const.CUSTOMER_KBN_EMPLOYEE }-->
<font color="#ff0000"><font color="red">[emoji:69]</font></font><a href="<!--{$smarty.const.ROOT_URLPATH}-->products/detail.php?mode=<!--{$mode}-->&product_id=<!--{$arrProduct.product_id|u}-->&quantity=1&select_regular=1&<!--{$smarty.const.TRANSACTION_ID_NAME}-->=<!--{$transactionid}-->"><font color="#ff6600">定期購入する</font></a>
<br>
<!--{/if}-->
<!--{/if}-->

<font color="#ff0000"><font color="red">[emoji:69]</font></font><a href="<!--{$smarty.const.ROOT_URLPATH}-->products/detail.php?mode=<!--{$mode}-->&product_id=<!--{$arrProduct.product_id|u}-->&quantity=1&<!--{$smarty.const.TRANSACTION_ID_NAME}-->=<!--{$transactionid}-->"><font color="#ff6600">カートに入れる</font></a>
</font>

<!--{** ▲販売期間中のみボタンを表示 **}-->
<!--{else}-->

<!--{* ▼販売終了 *}-->
<font size="-1">終了しました。</font>
<!--{* ▲販売終了 *}-->
<!--{/if}-->

<!--{** ▲販売期間中の表示 **}-->
<!--{/if}-->

<!--{* ▲在庫がある場合 *}-->
<!--{else}-->

<!--{* ▼在庫無し *}-->
<!--{foreach from=$tpl_stock_status_name key=key item=stock_status }-->
<font size="-1"><!--{$stock_status}--></font>
<!--{/foreach}-->
<!--{* ▲在庫無し *}-->
<!--{/if}-->
<br>
&nbsp;

</td>
</tr>
</table>
    <!--{/foreach}-->
<!-- ▲商品 ここまで -->
<!--{$childBrand.mb_free_space2}-->
<!--{/foreach}-->
<!--{$arrBrand.mb_free_space5}-->

<div align="right" class="pageTop"> <font size="-1"><a href="#top"><font color="#003b9b">ﾍﾟｰｼﾞTOP▲</font></a><br><br></font></div>
