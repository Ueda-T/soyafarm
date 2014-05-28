<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *}-->
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
　【時間帯指定】　<!--{$shipping.shipping_time|default:"指定なし"}-->
　【お届け日】　　<!--{$shipping.shipping_date|date_format:"%Y/%m/%d"|default:"指定なし"}-->
<!--{section name=cnt loop=$arrOrderDetail}-->
　<!--{$smarty.section.cnt.iteration}-->.<!--{$arrOrderDetail[cnt].product_name}--> <!--{$arrOrderDetail[cnt].classcategory_name1}--> <!--{$arrOrderDetail[cnt].classcategory_name2}--><!--{if !$arrOrderDetail[cnt].sell_flg}-->（プレゼント商品）<!--{/if}-->
<!--{if $arrOrderDetail[cnt].sell_flg}-->
　　単価：<!--{$arrOrderDetail[cnt].price|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円　×　数量：<!--{$arrOrderDetail[cnt].quantity}-->　＝　商品代金：<!--{$arrOrderDetail[cnt].price*$arrOrderDetail[cnt].quantity|number_format}-->円
<!--{else}-->
　　数量：<!--{$arrOrderDetail[cnt].quantity}-->
<!--{/if}-->

<!--{/section}-->
<!--{/foreach}-->


【お買上金額（税込）】
　商品代金：<!--{$arrOrder.subtotal|number_format|default:0}-->円
　送料：<!--{$arrOrder.deliv_fee|number_format|default:0}-->円
<!--{if $arrOrder.discount}-->
　値引き：-<!--{$arrOrder.discount|number_format|default:0}-->円
<!--{/if}-->
　合計金額：<!--{$arrOrder.payment_total|number_format|default:0}-->円


【お支払方法】　<!--{$arrOrder.payment_method}-->
【請求書送付方法】　<!--{$arrOrder.dsp_include_kbn}-->
【配達方法】　<!--{if $arrOrder.deliv_box_id}-->メール便<!--{else}-->宅配便<!--{/if}-->


<!--{$tpl_footer}-->
