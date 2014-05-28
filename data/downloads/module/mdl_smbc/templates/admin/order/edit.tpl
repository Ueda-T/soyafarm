<!--{*
/*
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
 */
*}-->
<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->
<script language="JavaScript">
<!--
<!--{$tpl_javascript}-->
function fnCreditEditSubmit() {
    if(!window.confirm("請求金額の変更処理を行います。\nよろしいですか？")){
        return;
    }
    document.form1['mode'].value = "edit";
    document.form1['anchor_key'].value = 'order_products';
    document.form1.submit();
    return false;
}
//-->
</script>
<form name="form1" id="form1" method="post" action="?<!--{if $smarty.get.type}-->&type=<!--{$smarty.get.type|h}--><!--{/if}-->">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="<!--{$tpl_mode|default:"edit"}-->">
<input type="hidden" name="order_id" value="<!--{$tpl_order_id}-->">
<input type="hidden" name="discount" value="<!--{$arrForm.discount.value}-->">
<input type="hidden" name="anchor_key" value="">
<input type="hidden" name="total" value="<!--{$arrForm.total.value}-->">
<input type="hidden" name="payment_total" value="<!--{$arrForm.payment_total.value}-->">


<h2>受注詳細</h2>

<table class="form">
    <colgroup width="30%">
    <colgroup width="80%">
    <tr>
        <th>対応状況</th>
        <td>
            <!--{if $arrForm.delete.value == 1}-->削除済み
            <!--{else}-->
            <!--{assign var=status value=`$arrForm.status.value`}-->
            <!--{$arrORDERSTATUS[$status]}-->
            <!--{/if}-->
        </td>
    </tr>
    <tr>
        <th>入金日</th>
        <td><!--{$arrForm.payment_date.value|sfDispDBDate|default:"未入金"}--></td>
    </tr>
    <tr>
        <th>発送日</th>
        <td><!--{$arrForm.commit_date.value|sfDispDBDate|default:"未発送"}--></td>
    </tr>
</table>

<h3>お客様情報</h3>
<table class="form">
    <colgroup width="20%">
    <colgroup width="80%">
    <tr>
        <th>注文番号</th>
        <td><!--{$arrForm.order_id.value}--></td>
    </tr>
    <tr>
        <th>受注日</th>
        <td><!--{$arrForm.create_date.value|sfDispDBDate}--></td>
    </tr>
    <tr>
        <th>顧客ID</th>
        <td>
        <!--{if $arrForm.customer_id.value > 0}-->
            <!--{$arrForm.customer_id.value}-->
        <!--{else}-->
            (非会員)
        <!--{/if}-->
        </td>
    </tr>
    <tr>
        <th>顧客名</th>
        <td><!--{$arrForm.order_name01.value|h}--> <!--{$arrForm.order_name02.value|h}--></td>
    </tr>
    <tr>
        <th>顧客名(カナ)</th>
        <td><!--{$arrForm.order_kana01.value|h}--> <!--{$arrForm.order_kana02.value|h}--></td>
    </tr>
    <tr>
        <th>メールアドレス</th>
        <td><a href="mailto:<!--{$arrForm.order_email.value|h}-->"><!--{$arrForm.order_email.value|h}--></a></td>
    </tr>
    <tr>
        <th>TEL</th>
        <td><!--{$arrForm.order_tel01.value}-->-<!--{$arrForm.order_tel02.value}-->-<!--{$arrForm.order_tel03.value}--></td>
    </tr>
    <tr>
        <th>住所</th>
        <td>
            〒<!--{$arrForm.order_zip01.value}-->-<!--{$arrForm.order_zip02.value}--><br />
            <!--{assign var=key value=$arrForm.order_pref.value}-->
            <!--{$arrPref[$key]}--><!--{$arrForm.order_addr01.value}--><!--{$arrForm.order_addr02.value}-->
        </td>
    </tr>
    <tr>
        <th>備考</th>
        <td><!--{$arrForm.message.value|h|nl2br}--></td>
    </tr>
</table>

<!--▼お届け先情報ここから-->
<h3>お届け先情報</h3>
<!--{section name=shipping loop=$arrForm.shipping_quantity.value}-->
<!--{assign var=shipping_index value="`$smarty.section.shipping.index`"}-->
<!--{if $arrForm.shipping_quantity.value > 1}-->
    <h4>お届け先<!--{$smarty.section.shipping.iteration}--></h4>
<!--{/if}-->
    <!--{assign var=key value="shipping_id"}-->
    <input type="hidden" name="<!--{$key}-->[<!--{$shipping_index}-->]" value="<!--{$arrForm[$key].value[$shipping_index]|default:"0"|h}-->" id="<!--{$key}-->_<!--{$shipping_index}-->" />
    <!--{if $arrForm.shipping_quantity.value > 1}-->
        <!--{assign var=product_quantity value="shipping_product_quantity"}-->
        <input type="hidden" name="<!--{$product_quantity}-->[<!--{$shipping_index}-->]" value="<!--{$arrForm[$product_quantity].value[$shipping_index]|h}-->" />

        <!--{if $arrForm[$product_quantity].value[$shipping_index] > 0}-->
            <table class="list" id="order-edit-products">
                <tr>
                    <th class="id">商品コード</th>
                    <th class="name">商品名/規格1/規格2</th>
                    <th class="price">単価</th>
                    <th class="qty">数量</th>
                </tr>
                <!--{section name=item loop=$arrForm[$product_quantity].value[$shipping_index]}-->
                    <!--{assign var=item_index value="`$smarty.section.item.index`"}-->

                    <tr>
                        <td>
                            <!--{assign var=key value="shipment_product_class_id"}-->
                            <input type="hidden" name="<!--{$key}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrForm[$key].value[$shipping_index][$item_index]|h}-->" />
                            <!--{assign var=key value="shipment_product_code"}-->
                            <input type="hidden" name="<!--{$key}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrForm[$key].value[$shipping_index][$item_index]|h}-->" />
                            <!--{$arrForm[$key].value[$shipping_index][$item_index]|h}-->
                        </td>
                        <td>
                            <!--{assign var=key1 value="shipment_product_name"}-->
                            <!--{assign var=key2 value="shipment_classcategory_name1"}-->
                            <!--{assign var=key3 value="shipment_classcategory_name2"}-->
                            <input type="hidden" name="<!--{$key1}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrForm[$key1].value[$shipping_index][$item_index]|h}-->" />
                            <input type="hidden" name="<!--{$key2}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrForm[$key2].value[$shipping_index][$item_index]|h}-->" />
                            <input type="hidden" name="<!--{$key3}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrForm[$key3].value[$shipping_index][$item_index]|h}-->" />
                            <!--{$arrForm[$key1].value[$shipping_index][$item_index]|h}-->/<!--{$arrForm[$key2].value[$shipping_index][$item_index]|default:"(なし)"|h}-->/<!--{$arrForm[$key3].value[$shipping_index][$item_index]|default:"(なし)"|h}-->
                        </td>
                        <td class="right">
                            <!--{assign var=key value="shipment_price"}-->
                            <!--{$arrForm[$key].value[$shipping_index][$item_index]|number_format}-->円
                            <input type="hidden" name="<!--{$key}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrForm[$key].value[$shipping_index][$item_index]|h}-->" />
                        </td>
                        <td class="right">
                            <!--{assign var=key value="shipment_quantity"}-->
                            <!--{$arrForm[$key].value[$shipping_index][$item_index]|h}-->
                            <input type="hidden" name="<!--{$key}-->[<!--{$shipping_index}-->][<!--{$item_index}-->]" value="<!--{$arrForm[$key].value[$shipping_index][$item_index]|h}-->" />
                        </td>
                    </tr>
                <!--{/section}-->
            </table>
        <!--{/if}-->
    <!--{/if}-->

    <table class="form">
        <colgroup width="20%">
        <colgroup width="80%">
        <tr>
            <th>お名前</th>
            <td><!--{$arrForm.shipping_name01.value[$shipping_index]|h}--> <!--{$arrForm.shipping_name02.value[$shipping_index]|h}--></td>
        </tr>
        <tr>
            <th>お名前(カナ)</th>
            <td><!--{$arrForm.shipping_kana01.value[$shipping_index]|h}--> <!--{$arrForm.shipping_kana02.value[$shipping_index]|h}--></td>
        </tr>
        <tr>
            <th>TEL</th>
            <td><!--{$arrForm.shipping_tel01.value[$shipping_index]|h}-->-<!--{$arrForm.shipping_tel02.value[$shipping_index]|h}-->-<!--{$arrForm.shipping_tel03.value[$shipping_index]|h}--></td>
        </tr>
        <tr>
            <th>住所</th>
            <td>
                〒<!--{$arrForm.shipping_zip01.value[$shipping_index]|h}-->-<!--{$arrForm.shipping_zip02.value[$shipping_index]|h}--><br />
                    <!--{assign var=key value=$arrForm.shipping_pref.value[$shipping_index]}-->
                    <!--{$arrPref[$key]}--><!--{$arrForm.shipping_addr01.value[$shipping_index]|h}--><!--{$arrForm.shipping_addr02.value[$shipping_index]|h}-->
            </td>
        </tr>
        <tr>
            <th>お届け時間</th>
            <td><!--{if $arrForm.time_id.value[$shipping_index]}-->
                    <!--{assign var=key value=$arrForm.time_id.value[$shipping_index]}-->
                    <!--{$arrDelivTime[$key]}-->
                <!--{else}-->
                    指定なし
                <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>お届け日</th>
            <td><!--{if $arrForm.shipping_date_year.value[$shipping_index] && $arrForm.shipping_date_month.value[$shipping_index] && $arrForm.shipping_date_day.value[$shipping_index]}-->
                    <!--{$arrForm.shipping_date_year.value[$shipping_index]}-->/<!--{$arrForm.shipping_date_month.value[$shipping_index]}-->/<!--{$arrForm.shipping_date_day.value[$shipping_index]}-->
                <!--{else}-->
                    指定なし
                <!--{/if}-->
            </td>
        </tr>
    </table>
<!--{/section}-->
<!--▲お届け先情報ここまで-->

<!--{assign var=discount value="`$arrForm.discount.value`"}-->
<!--{assign var=add_discount value="`$arrForm.add_discount.value`"}-->
<!--{assign var=org_discount value="`$arrForm.org_discount.value`"}-->

<h3>受注商品情報
    <!--{if $credit_status.value == 3}-->
    <a class="btn-normal" style="border: solid 1px #d5d7df;color: #6d728b;" href="javascript:;" ><span style="font-size: 111%">計算結果の確認</span></a>
    <!--{else}-->
    <a class="btn-normal" onclick="fnModeSubmit('cheek','anchor_key','order_products');"><span style="font-size: 111%">計算結果の確認</span></a>
    <!--{/if}-->

    <!--{if $arrErr or $add_discount == 0}-->
    <a class="btn-normal" style="border: solid 1px #d5d7df;color: #6d728b;" href="javascript:;" ><span style="font-size: 111%">クレジットカード請求金額の変更</span></a>
    <!--{else}-->
    <a class="btn-normal" onclick="fnCreditEditSubmit();"><span style="font-size: 111%">クレジットカード請求金額の変更</span></a>
    <!--{/if}-->
</h3>
<!--{if $arrError.rescd ne '' && $arrError.rescd ne $smarty.const.MDL_SMBC_RES_OK && $arrError.rescd ne $smarty.const.MDL_SMBC_RES_SECURE}-->
<span class="attention">エラーが発生しました。以下の内容をご確認ください。</span><br />
<span class="attention"><!--{$arrError.rescd|escape}-->:<!--{$arrError.res|escape}--></span><br />
<br />
<!--{/if}-->
<table class="list">
    <colgroup width="15%">
    <colgroup width="35%">
    <colgroup width="15%">
    <colgroup width="10%">
    <colgroup width="15%">
    <tr>
        <th>商品コード</th>
        <th>商品名/規格1/規格2</th>
        <th>単価</th>
        <th>数量</th>
        <th>小計</th>
    </tr>
    <!--{section name=cnt loop=$arrForm.quantity.value}-->
    <!--{assign var=key value="`$smarty.section.cnt.index`"}-->
    <tr>
        <td><!--{$arrForm.product_code.value[$key]|h}--></td>
        <td><!--{$arrForm.product_name.value[$key]|h}--><br><!--{$arrForm.classcategory_name1.value[$key]|default:"(なし)"|h}-->/<!--{$arrForm.classcategory_name2.value[$key]|default:"(なし)"|h}--></td>
        <td class="right"><!--{if $arrForm.price.value[$key] != 0}--><!--{$arrForm.price.value[$key]|number_format}-->円<!--{else}-->無料<!--{/if}--></td>
        <td class="center"><!--{$arrForm.quantity.value[$key]|h}--></td>
        <!--{assign var=price value=`$arrForm.price.value[$key]`}-->
        <!--{assign var=quantity value=`$arrForm.quantity.value[$key]`}-->
        <!--{if $version_2_13}-->
            <td class="right"><!--{if $price != 0}--><!--{$price|sfCalcIncTax:$arrForm.tax_rate.value[$key]:$arrForm.tax_rule.value[$key]|sfMultiply:$quantity|number_format}-->円<!--{else}-->無料<!--{/if}--></td>
        <!--{else}-->
            <td class="right"><!--{if $price != 0}--><!--{$price|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|sfMultiply:$quantity|number_format}-->円<!--{else}-->無料<!--{/if}--></td>
        <!--{/if}-->
    </tr>
    <!--{/section}-->
    <tr>
        <th colspan="4" class="column right">小計</th>
        <td align="right"><!--{$arrForm.subtotal.value|number_format}-->円</td>
    </tr>
    <tr>
        <th colspan="4" class="column right">値引き</td>
        <td align="right"><!--{$discount|number_format}-->円</td>
    </tr>
    <tr>
        <th colspan="4" class="column right">ポイント値引き</th>
        <td align="right"><!--{assign var=point_discount value="`$arrForm.use_point.value*$smarty.const.POINT_VALUE`"}--><!--{$point_discount|number_format}-->円</td>
    </tr>
    <!--{assign var=discount value="`$arrForm.discount.value`"}-->
    <tr>
        <th colspan="4" class="column right">クレジットカードの請求金額を減額する金額入力して下さい。<br />減額した金額は値引き額に加算されます。</th>
        <td align="right">
            <!--{assign var=key value="add_discount"}-->
            <!--{if $credit_status.value == 3}-->
            <!--{$add_discount|number_format}--> 円
            <!--{else}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input name="add_discount" style="width:50px;" type="text" value="<!--{$add_discount}-->" > 円
            <input name="org_discount" type="hidden" value="<!--{$org_discount}-->">
            <!--{/if}-->
         </td>
    </tr>
    <tr>
        <th colspan="4" class="column right">送料</th>
        <td align="right"><!--{assign var=key value="deliv_fee"}--><!--{$arrForm[$key].value|number_format|h}--> 円</td>
    </tr>
    <tr>
        <th colspan="4" class="column right">手数料</th>
        <td align="right"><!--{assign var=key value="charge"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span><!--{$arrForm[$key].value|number_format|h}--> 円</td>
    </tr>
    <tr>
        <th colspan="4" class="column right">合計</th>
        <td align="right"><!--{$arrForm.total.value|number_format}--> 円</td>
    </tr>
    <tr>
        <th colspan="4" class="column right">お支払い合計</th>
        <td bgcolor="#FFD9D9" align="right"><!--{$arrForm.payment_total.value|number_format}--> 円</td>
    </tr>
    <tr>
        <th colspan="4" class="column right">使用ポイント</th>
        <td align="right"><!--{assign var=key value="use_point"}--><!--{if $arrForm[$key].value != ""}--><!--{$arrForm[$key].value|number_format}--><!--{else}-->0<!--{/if}--> pt</td>
    </tr>
    <!--{if $arrForm.birth_point.value > 0}-->
    <tr>
        <th colspan="4" class="column right">お誕生日ポイント</th>
        <td align="right">
            <!--{$arrForm.birth_point.value|number_format}-->
            pt</td>
    </tr>
    <!--{/if}-->
    <tr>
        <th colspan="4" class="column right">加算ポイント</th>
        <td align="right">
        <!--{$arrForm.add_point.value|default:0|number_format}-->
            pt</td>
    </tr>
    <tr>
        <!--{if $arrForm.customer_id.value > 0}-->
        <th colspan="4" class="column right">現在ポイント</th>
        <td align="right">
        <!--{$arrForm.point.value|number_format}-->
            pt</td>
        <!--{else}-->
            <th colspan="4" class="column right">現在ポイント</th><td align="center">(なし)</td>
        <!--{/if}-->
    </tr>
    <!--{*
    <tr>
        <th colspan="4" class="column right">反映後ポイント (ポイントの変更は<a href="?" onclick="return fnEdit('<!--{$arrForm.customer_id.value}-->');">顧客編集</a>から手動にてお願い致します。)</th>
        <td align="right">
            <span class="attention"><!--{$arrErr.total_point}--></span>
            <!--{$arrForm.total_point.value|number_format}-->
            pt
        </td>
    </tr>
    *}-->
</table>

<table class="form">
    <colgroup width="30%">
    <colgroup width="80%">
    <tr>
        <th>お支払方法</th>
        <td>
            <!--{assign var=payment_id value="`$arrForm.payment_id.value`"}-->
            <!--{$arrPayment[$payment_id]|h}-->
        </td>
    </tr>
    <!--{if $arrForm.payment_info.value|@count > 0}-->
    <tr>
        <th><!--{$arrForm.payment_typ.valuee}-->情報</th>
        <td>
            <!--{foreach key=key item=item from=$arrForm.payment_info.value}-->
            <!--{if $key != "title"}--><!--{if $item.name != ""}--><!--{$item.name}-->：<!--{/if}--><!--{$item.value}--><br/><!--{/if}-->
            <!--{/foreach}-->
        </td>
    </tr>
    <!--{/if}-->
    <tr>
        <th>メモ</th>
        <td>
            <!--{assign var=key value="note"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <textarea name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" cols="80" rows="6" class="area60" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{$arrForm[$key].value|escape}--></textarea>
        </td>
    </tr>
</table>
</form>
<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
