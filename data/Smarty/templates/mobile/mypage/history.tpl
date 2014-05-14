【注文日】<br>
　<!--{$tpl_arrOrderData.create_date|date_format:"%Y年%m月%d日"|h}--><br>
【ｵｰﾀﾞｰ番号】<br>
　<!--{$tpl_arrOrderData.order_id}--><br>
【ご購入金額】<br>
　商品金額(税込):<!--{$tpl_arrOrderData.subtotal|number_format}-->円<br>
　送料(税込):<!--{assign var=key value="deliv_fee"}--><!--{$tpl_arrOrderData[$key]|number_format|h}-->円<br>
　使用ポイント:<!--{assign var=key value="use_point"}--><!--{$tpl_arrOrderData[$key]|number_format|h}-->pt<br>
　合計金額(税込):<!--{$tpl_arrOrderData.payment_total|number_format}-->円<br>
<br>

【お支払方法】<br>
　<!--{$arrPayment[$tpl_arrOrderData.payment_id]|h}--><br>
【出荷日】<br>
　<!--{$tpl_arrOrderData.commit_date|date_format:"%Y年%m月%d日"|h}--><br>
【配送方法】<br>
　<!--{$arrDelivBox[$tpl_arrOrderData.deliv_box_id]|h}--><br>
【請求書送付方法】<br>
　<!--{$arrIncludeKbn[$tpl_arrOrderData.include_kbn]|h}--><br>
<br>

<!--{foreach item=shippingItem name=shippingItem from=$arrShipping}-->
【お届け先】<br>
〒<!--{$shippingItem.shipping_zip}--><br>
<!--{$arrPref[$shippingItem.shipping_pref]}--><!--{$shippingItem.shipping_addr01|h}--><!--{$shippingItem.shipping_addr02|h}--><br>
 <!--{$shippingItem.shipping_name|h}-->　様<br>
電話番号:<!--{$shippingItem.shipping_tel}--><br>
【お届け日】<br>
　<!--{$shippingItem.shipping_date|default:'指定なし'|h}--><br>
【時間帯指定】<br>
　<!--{$shippingItem.shipping_time|default:'指定なし'|h}--><br>
【配達時のご要望】<br>
　<!--{$tpl_arrOrderData.note|default:'指定なし'|h}--><br>
【伝票番号】<br>
　<!--{$shippingItem.shipping_num|default:''|h}--><br>

【ご注文商品】<br>
<!--{foreach from=$tpl_arrOrderDetail item=orderDetail}-->
<br>
　<!--{$orderDetail.product_name|h}--><br>
<!--{assign var=price value=`$orderDetail.price`}-->
<!--{assign var=quantity value=`$orderDetail.quantity`}-->
<!--{$price|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format|h}-->円<br>
<!--{$quantity|h}-->個<br>
<!--{/foreach}-->

<!--{/foreach}-->
<br>
