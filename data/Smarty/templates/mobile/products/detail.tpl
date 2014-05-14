<!--{if $arrProduct.mb_comment1}-->
<!--{$arrProduct.mb_comment1}-->
<br>
<!--{/if}-->

<!--★詳細メインコメント★-->
<font size="-1"><!--{$arrProduct.main_list_comment}--></font>
<br>

<!--★商品画像★-->
<!--{if $smarty.get.image != ''}-->
<!--{assign var=key value="`$smarty.get.image`"}-->
<!--{else}-->
<!--{assign var=key value="main_image"}-->
<!--{/if}-->
<center><img src="<!--{$arrFile[$key].filepath}-->" width="120" height="120"></center>
<br>

<!--▼商品ステータス-->
<!--{assign var=ps value=$productStatus[$tpl_product_id]}-->
<!--{if count($ps) > 0}-->
<div align="center">
<!--{foreach from=$ps item=status}-->
<img src="<!--{$TPL_URLPATH}--><!--{$arrSTATUS[$status].image|h}-->" alt="<!--{$arrSTATUS[$status].name|h}-->" id="icon<!--{$status}-->">
<!--{/foreach}-->
<br>
<a href="<!--{$smarty.const.ROOT_URLPATH}-->contents/icon.php"><font size="-2" color="#666666">表示アイコンについて</font></a>
</div>
<br>
<!--{/if}-->
<!--▲商品ステータス-->

<!--★商品サブ画像★-->
<center>
<!--{if $subImageFlag == true}-->
<br>画像
<!--{if ($smarty.get.image == "" || $smarty.get.image == "main_image")}-->
[1]
<!--{else}-->
[<a href="?product_id=<!--{$tpl_product_id}-->&amp;image=main_image">1</a>]
<!--{/if}-->
  
<!--{assign var=num value="2"}-->
<!--{section name=cnt loop=$smarty.const.PRODUCTSUB_MAX}-->
<!--{assign var=key value="sub_image`$smarty.section.cnt.iteration`"}-->
<!--{if $arrFile[$key].filepath != ""}-->
    <!--{if $key == $smarty.get.image}-->
[<!--{$num}-->]
    <!--{else}-->
[<a href="?product_id=<!--{$tpl_product_id}-->&amp;image=<!--{$key}-->"><!--{$num}--></a>]
    <!--{/if}-->
    <!--{assign var=num value="`$num+1`"}-->
  <!--{/if}-->
  <!--{/section}-->
<br>
<!--{/if}-->
<br>
</center>
<!--{* オペビルダー用 *}-->
<!--{if "sfViewDetailOpe"|function_exists === TRUE}-->
<!--{include file=`$smarty.const.MODULE_REALDIR`mdl_opebuilder/detail_ope_mb_view.tpl}-->
<!--{/if}-->

<form name="form1" method="post" action="?">
<table width="100%" border="0" cellpadding="1" cellspacing="0" bgcolor="#dfedf5" bordercolor="#dfedf5">
<tr>
<td align="center">
<!--★社員価格★-->
<!--{if $tpl_customer_kbn == $smarty.const.CUSTOMER_KBN_EMPLOYEE}-->
<font color="#ff8800" size="-1">
￥<!--{if $arrProduct.price02_min == $arrProduct.price02_max}--><!--{$arrProduct.price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}--><!--{else}--><!--{$arrProduct.price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～\<!--{$arrProduct.price02_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
<!--{/if}-->
</font>
</td>
</tr>
<!--{else}-->
<tr>
<td align="center">
<!--{if $arrProduct.price01_max > 0}-->
<!--★通常価格★-->
<font color="#ff8800" size="-1">
価格（税込） ￥<!--{if $arrProduct.price01_min == $arrProduct.price01_max}--><!--{$arrProduct.price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}--><!--{else}--><!--{$arrProduct.price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～\<!--{$arrProduct.price01_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
<!--{/if}-->
</font>
<!--{/if}-->

<!--{if $arrProduct.mb_comment2}-->
<br>
<!--{$arrProduct.mb_comment2}-->
<!--{/if}-->
</td>
</tr>
<!--{/if}-->

<tr>
<!--{if $tpl_stock_find}-->

<form name="form1" method="post" action="?">
<input type="hidden" name="mode" value="<!--{$tpl_mode}-->">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->">
<input type="hidden" name="product_class_id" value="<!--{$tpl_product_class_id}-->">

<td align="center">

<!--{if $arrProduct.sale_start_date && $arrProduct.sale_start_date > $smarty.now|date_format:"%Y-%m-%d 00:00:00"}-->
<!--{** ▼販売期間前の表示 **}-->
<!--{* 規格の無い商品は数量選択 *}-->
<!--{if $tpl_classcat_find1 === false && $tpl_classcat_find2 === false}-->
<select name="quantity" style="<!--{$arrErr.quantity|sfGetErrorColor}-->">
<!--{html_options options=$tpl_arrQuantity[$tpl_product_class_id] }-->
</select>
<!--{/if}-->
<input type="submit" name="select" id="cart" value="予約する">
<!--{** ▲販売期間前の表示 **}-->
<!--{else}-->
<!--{** ▼販売期間中の表示 **}-->

<!--{** ▼販売終了日過ぎていない場合 **}-->
<!--{if strlen($arrProduct.sale_end_date) == 0 || $arrProduct.sale_end_date >= $smarty.now|date_format:"%Y-%m-%d"}-->
<!--{* 規格の無い商品は数量選択 *}-->
<!--{if $tpl_classcat_find1 === false && $tpl_classcat_find2 === false}-->
<select name="quantity" style="<!--{$arrErr.quantity|sfGetErrorColor}-->">
<!--{html_options options=$tpl_arrQuantity[$tpl_product_class_id] }-->
</select>
<!--{/if}-->
<!--{* 定期購入ボタン *}-->
<!--{if $arrProduct.teiki_flg == $smarty.const.REGULAR_PURCHASE_FLG_ON}-->
<!--{* 社員は定期購入不可 *}-->
<!--{if $tpl_customer_kbn != $smarty.const.CUSTOMER_KBN_EMPLOYEE }-->
<input type="submit" name="select_regular" id="cart_regular" value="定期購入する">
<!--{/if}-->
<!--{/if}-->
<input type="submit" name="select" id="cart" value="カートに入れる">


<!--{** ▲販売終了日過ぎていない場合 **}-->
<!--{else}-->
<!--{** ▼販売終了 **}-->
<font size="-1">終了しました</font>
<!--{** ▲販売終了 **}-->
<!--{/if}-->

<!--{** ▲販売期間中の表示 **}-->
<!--{/if}-->
</td>

</form>
<!--{else}-->
<!--{* 売切れ時時の表示 *}-->
<td><font color="#FF0000"><!--{$tpl_stock_status_name[$tpl_product_class_id]}--></font></td>
<!--{/if}-->
</tr>
</table>

<!--{if $arrProduct.mb_comment4}-->
<!--{$arrProduct.mb_comment4}-->
<br>
<!--{/if}-->

<!--★商品詳細★-->
<table width="100%" border="0" cellpadding="1" cellspacing="0" bgcolor="#003b9b" bordercolor="#003b9b">
<tr>
<th align="left" colspan="2"><h2><font color="#ffffff" size="-1">商品詳細</font></h2></th>
</tr>
</table>

<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#b9d0dc" bordercolor="#b9d0dc">
<tr>
<td bgcolor="#dfedf5" width="40%"><font size="-1">商品名</font></td>
<td bgcolor="#ffffff"><font size="-1"><!--{if isset($arrProduct.disp_name)}--><!--{$arrProduct.disp_name|h}--><!--{else}--><!--{$arrProduct.name|h}--><!--{/if}--></font></td>
</tr>
<tr>
<td bgcolor="#dfedf5" width="40%"><font size="-1">商品番号</font></td>
<td bgcolor="#ffffff"><font size="-1">
<!--{if $arrProduct.product_code_min == $arrProduct.product_code_max}-->
<!--{$arrProduct.product_code_min|h}-->
<!--{else}-->
<!--{$arrProduct.product_code_min|h}-->～<!--{$arrProduct.product_code_max|h}-->
<!--{/if}--></font></td>
</tr>
<tr>
<td bgcolor="#dfedf5" width="40%"><font size="-1">容量</font></td>
<td bgcolor="#ffffff"><font size="-1"><!--{$arrProduct.capacity|h}--></font></td>
</tr>
<tr>
<td bgcolor="#dfedf5" width="40%"><font size="-1">価格(税込)</font></td>
<td bgcolor="#ffffff"><font size="-1"> <!--{if $arrProduct.price01_max > 0}-->

<!--{if $tpl_customer_kbn == $smarty.const.CUSTOMER_KBN_EMPLOYEE}-->
<!--{* 社員価格 *}-->
￥<!--{if $arrProduct.price02_min == $arrProduct.price02_max}--><!--{$arrProduct.price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->(税込)<!--{else}--><!--{$arrProduct.price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～\<!--{$arrProduct.price02_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->(税込)
<!--{/if}-->

<!--{else}-->
<!--★通常価格★-->
￥<!--{if $arrProduct.price01_min == $arrProduct.price01_max}--><!--{$arrProduct.price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->(税込)<!--{else}--><!--{$arrProduct.price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～\<!--{$arrProduct.price01_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->(税込)
<!--{/if}-->

<!--{/if}-->

<!--{/if}-->&nbsp;</font></td>
</tr>
<tr>
<td bgcolor="#dfedf5" width="40%"><font size="-1">ﾌﾞﾗﾝﾄﾞ</font></td>
<td bgcolor="#ffffff"><font size="-1"><!--{$arrProduct.brand_name|h}--></font></td>
</tr>
</table>

<!--{* ▼お買い物カゴに入れる *}-->
<table width="100%" cellspacing="0" cellpadding="1" border="0">
<tr>
<!--{if $tpl_stock_find}-->

<form name="form1" method="post" action="?">
<input type="hidden" name="mode" value="<!--{$tpl_mode}-->">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->">
<input type="hidden" name="product_class_id" value="<!--{$tpl_product_class_id}-->">

<td align="center">

<!--{if $arrProduct.sale_start_date && $arrProduct.sale_start_date > $smarty.now|date_format:"%Y-%m-%d 00:00:00"}-->
<!--{** ▼販売期間前の表示 **}-->
<!--{* 規格の無い商品は数量選択 *}-->
<!--{if $tpl_classcat_find1 === false && $tpl_classcat_find2 === false}-->
<select name="quantity" style="<!--{$arrErr.quantity|sfGetErrorColor}-->">
<!--{html_options options=$tpl_arrQuantity[$tpl_product_class_id] }-->
</select>
<!--{/if}-->
<input type="submit" name="select" id="cart" value="予約する">
<!--{** ▲販売期間前の表示 **}-->
<!--{else}-->
<!--{** ▼販売期間中の表示 **}-->

<!--{** ▼販売終了日過ぎていない場合 **}-->
<!--{if strlen($arrProduct.sale_end_date) == 0 || $arrProduct.sale_end_date >= $smarty.now|date_format:"%Y-%m-%d"}-->
<!--{* 規格の無い商品は数量選択 *}-->
<!--{if $tpl_classcat_find1 === false && $tpl_classcat_find2 === false}-->
<select name="quantity" style="<!--{$arrErr.quantity|sfGetErrorColor}-->">
<!--{html_options options=$tpl_arrQuantity[$tpl_product_class_id] }-->
</select>
<!--{/if}-->

<!--{* 定期購入ボタン *}-->
<!--{if $arrProduct.teiki_flg == $smarty.const.REGULAR_PURCHASE_FLG_ON}-->
<!--{* 社員は定期購入不可 *}-->
<!--{if $tpl_customer_kbn != $smarty.const.CUSTOMER_KBN_EMPLOYEE }-->
<input type="submit" name="select_regular" id="cart_regular" value="定期購入する">
<!--{/if}-->
<!--{/if}-->
<input type="submit" name="select" id="cart" value="カートに入れる">

<!--{** ▲販売終了日過ぎていない場合 **}-->
<!--{else}-->
<!--{** ▼販売終了 **}-->
&nbsp;
<!--{** ▲販売終了 **}-->
<!--{/if}-->

<!--{** ▲販売期間中の表示 **}-->
<!--{/if}-->
</td>

</form>
<!--{else}-->
<!--{* 売切れ時時の表示 *}-->
<td><font color="#FF0000"><!--{$tpl_stock_status_name[$tpl_product_class_id]}--></font></td>
<!--{/if}-->
</tr>
</table>
<!--{* ▲お買い物カゴに入れる *}-->
<br>

    <!--▼関連商品-->
    <!--{if $arrRecommend}-->
<table width="100%" border="0" cellpadding="1" cellspacing="0" bgcolor="#003b9b" bordercolor="#003b9b">
<tr>
<th align="left" colspan="2"><h2><font size="-1" color="#ffffff">この商品の関連商品</font></h2></th>
</tr>
</table>

            <!--{section name=cnt loop=$arrRecommend}-->
                <!--{if $arrRecommend[cnt]}-->
<!--{if $smarty.section.cnt.iteration%2 == 0}-->
<table width="100%" cellspacing="0" cellpadding="1" bordercolor="#eaeaea" border="0" bgcolor="#eaeaea">
<!--{else}-->
<table width="100%" cellspacing="0" cellpadding="1" bordercolor="#ffffff" border="0" bgcolor="#ffffff">
<!--{/if}-->
<tr>
<td width="33%">
<img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH|sfTrimURL}-->/<!--{$arrRecommend[cnt].main_list_image|sfNoImageMainList|h}-->" class="picture" width="80" />
</td>
<td>
<font size="-1">
<a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrRecommend[cnt].product_id|u}-->"><!--{$arrRecommend[cnt].name|h}--></a>
<br>
    <!--{if $tpl_customer_kbn != $smarty.const.CUSTOMER_KBN_EMPLOYEE}-->
      <!--{* ▼通常価格 *}-->
      <!--{assign var=price01_min value=`$arrRecommend[cnt].price01_min`}-->
      <!--{assign var=price01_max value=`$arrRecommend[cnt].price01_max`}-->

      <!--{if $price01_min == $price01_max}-->
        <!--{$price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
      <!--{else}-->
        <!--{$price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～<!--{$price01_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
      <!--{/if}-->円(税込)
      <!--{* ▲通常価格 *}-->

    <!--{else}-->

      <!--{* ▼社員価格 *}-->
      <!--{assign var=price02_min value=`$arrRecommend[cnt].price02_min`}-->
      <!--{assign var=price02_max value=`$arrRecommend[cnt].price02_max`}-->

      <!--{if $price02_min == $price02_max}-->
        <!--{$price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
      <!--{else}-->
        <!--{$price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～<!--{$price02_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
      <!--{/if}-->円(税込)
      <!--{* ▲社員価格 *}-->
    <!--{/if}-->
</font>
</td>
</tr>
<td colspan="2"><font size="-1"><!--{$arrRecommend[cnt].main_list_comment|nl2br}--></font></td>
</tr>
</table>
                <!--{/if}-->
        <!--{/section}-->
    <!--{/if}-->
    <!--▲関連商品-->

<!--{if $arrProduct.mb_comment5}-->
<!--★商品概要★-->
<table width="100%" border="0" cellpadding="1" cellspacing="0" bgcolor="#003b9b" bordercolor="#003b9b">
<tr>
<th align="left" colspan="2"><h2><font color="#ffffff" size="-1">商品概要</font></h2></th>
</tr>
</table>
<!--{$arrProduct.mb_comment5}-->
<!--{/if}-->


<!--{if $arrProduct.mb_comment3}-->
<!--{$arrProduct.mb_comment3}--><br>
<!--{/if}-->

<div align="center">
[emoji:110]<a href="mailto:@?subject=&body=<!--{$smarty.const.HTTP_URL}-->detail.php?product_id=<!--{$tpl_product_id}-->"><font size="-1">この商品を友達に教える</font></a><br><br>
</div>
