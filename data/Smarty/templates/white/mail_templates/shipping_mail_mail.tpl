<!--{$arrOrder.order_name}--> 様

<!--{$tpl_header}-->

--------------------------------------------------
【オーダー番号】　<!--{$arrOrder.order_id}-->
【ご注文日】　<!--{$createDate}-->
【ご注文者】　<!--{$arrOrder.order_name}-->(<!--{$arrOrder.order_kana}-->)様
【お電話番号】　<!--{$arrOrder.order_tel}-->

【ご注文明細】

<!--{foreach item=shipping name=shipping from=$arrShipping}-->
・お届け先：<!--{$shipping.shipping_name}-->　様  
<!--{section name=cnt loop=$arrOrderDetail}-->
　<!--{$smarty.section.cnt.iteration}-->.<!--{$arrOrderDetail[cnt].product_name}--> <!--{$arrOrderDetail[cnt].classcategory_name1}--> <!--{$arrOrderDetail[cnt].classcategory_name2}-->
　　単価：<!--{$arrOrderDetail[cnt].price|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円　×　数量：<!--{$arrOrderDetail[cnt].quantity}-->　＝　商品代金：<!--{$arrOrderDetail[cnt].price*$arrOrderDetail[cnt].quantity|number_format}-->円

<!--{/section}-->
<!--{/foreach}-->


【お買上金額（税込）】
　商品代金：　　<!--{$arrOrder.subtotal|number_format|default:0}-->円
　送料：　　　　<!--{$arrOrder.deliv_fee|number_format|default:0}-->円
<!--{if $arrOrder.discount}-->
　割引金額：　　-<!--{$arrOrder.discount|number_format|default:0}-->円
<!--{/if}-->
　合計金額：　　<!--{$arrOrder.payment_total|number_format|default:0}-->円


【お支払方法】　　　<!--{$arrOrder.payment_method}-->
【請求書送付方法】　<!--{$arrOrder.dsp_include_kbn}-->
【配達方法】　　　　<!--{if $arrOrder.deliv_box_id}-->メール便<!--{else}-->宅配便<!--{/if}-->



★商品はゆうメールにてお届けします★

通常、ご注文受付後、10日前後でお届けいたします。
※メール便でのお届けのため、お時間をいただく場合がございます。


<!--{$tpl_footer}-->
