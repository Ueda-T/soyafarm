<script type="text/javascript">
<!--
//self.moveTo(20,20);self.focus();
//-->
</script>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->
    <h2>受注管理</h2>
    <table class="form">
        <tr>
            <th>注文番号</th>
            <td><!--{$arrForm.order_id.value|h}--></td>
        </tr>
        <tr>
            <th>受注日</th>
            <td><!--{$arrForm.create_date.value|sfDispDBDate|h}--></td>
        </tr>
        <tr>
            <th>対応状況</th>
            <td>
                <!--{assign var=val value=$arrForm.status.value}-->
                <!--{$arrORDERSTATUS[$val]}-->
            </td>
        </tr>
        <tr>
            <th>発送日</th>
            <td><!--{$arrForm.commit_date.value|sfDispDBDate|default:"未発送"|h}--></td>
        </tr>
        <!--{*
        <tr>
            <th>入金日</th>
            <td><!--{$arrForm.payment_date.value|sfDispDBDate|default:"未入金"|h}--></td>
        </tr>
        *}-->
            <th>キャンペーンコード</th>
            <td><!--{$arrForm.campaign_cd.value|h}--></td>
        <tr>
        </tr>
        <tr>
            <th>適用プロモーション</th>
            <td><!--{$arrForm.promotion.value|h}--></td>
        </tr>
    </table>

    <h2>注文者情報
        <!--{if $tpl_mode == 'add'}-->
            <a class="btn-normal" href="javascript:;" name="address_input" onclick="fnOpenWindow('<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->customer/search_customer.php','search','600','650'); return false;">顧客検索</a>
        <!--{/if}-->
    </h2>
    <table class="form">
        <tr>
            <th>顧客ID</th>
            <td>
                <!--{if $arrForm.customer_id.value > 0}-->
                    <!--{$arrForm.customer_id.value|h}-->
                    <input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id.value|h}-->" />
                <!--{else}-->
                    (非会員)
                <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>顧客番号（基幹）</th>
            <td>
                <!--{$arrForm.customer_cd.value|h}-->
            </td>
        </tr>
        <tr>
            <th>お名前</th>
            <td>
                <!--{assign var=key value="order_name"}-->
                <!--{$arrForm[$key].value}-->
            </td>
        </tr>
        <tr>
            <th>お名前(フリガナ)</th>
            <td>
                <!--{assign var=key value="order_kana"}-->
                <!--{$arrForm[$key].value}-->
            </td>
        </tr>
        <tr>
            <th>メールアドレス</th>
            <td>
                <!--{assign var=key value="order_email"}-->
                <!--{$arrForm[$key].value}-->
            </td>
        </tr>
        <tr>
            <th>TEL</th>
            <td>
                <!--{assign var=key value="order_tel"}-->
                <!--{$arrForm[$key].value}-->
            </td>
        </tr>
        <tr>
            <th>住所</th>
            <td>
                <!--{assign var=key value="order_zip"}-->
                <!--{$arrForm[$key].value}--><br />
                <!--{assign var=key value="order_pref"}-->
                <!--{assign var=val value=$arrForm[$key].value}-->
                <!--{assign var=key value="order_addr01"}-->
                <!--{$arrPref[$val]}--><!--{$arrForm[$key].value}--><br />
                <!--{assign var=key value="order_addr02"}-->
                <!--{$arrForm[$key].value}-->
            </td>
        </tr>
<!--{*
        <tr>
            <th>備考</th>
            <td><!--{$arrForm.message.value|h|nl2br}--></td>
        </tr>
*}-->
        <tr>
            <th>現在ポイント</th>
            <td>
                <!--{if $arrForm.customer_id > 0}-->
                    <!--{$arrForm.customer_point.value|number_format}--> pt
                <!--{else}-->
                    (非会員)
            <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>アクセス端末</th>
            <td><!--{$arrDeviceType[$arrForm.device_type_id.value]|h}--></td>
        </tr>

    </table>
    <!--▲お客様情報ここまで-->
        
    <!--▼受注商品情報ここから-->
    <a name="order_products"></a>
    <h2 id="order_products">
        受注商品情報
    </h2>

    <table class="list" id="order-edit-products">

        <colgroup width="10%"></colgroup>
        <colgroup width="25%"></colgroup>
        <colgroup width="12%"></colgroup>
        <colgroup width="10%"></colgroup>
        <colgroup width="7%"></colgroup>
        <colgroup width="13%"></colgroup>
        <colgroup width="13%"></colgroup>
        <tr>
            <th class="id">商品コード</th>
            <th class="name">商品名/規格1</th>
            <th class="course">お届け間隔</th>
            <th class="price">単価</th>
            <th class="qty">数量</th>
            <th class="price">税込み価格</th>
            <th class="price">小計</th>
        </tr>
        <!--{section name=cnt loop=$arrForm.quantity.value}-->
        <!--{assign var=product_index value="`$smarty.section.cnt.index`"}-->
        <tr>
            <td>
                <!--{$arrForm.product_code.value[$product_index]|h}-->
            </td>
            <td>
                <!--{$arrForm.product_name.value[$product_index]|h}-->/<!--{$arrForm.classcategory_name1.value[$product_index]|default:"(なし)"|h}-->
            </td>
            <td>
                <!--{if $arrForm.course_cd.value[$product_index] > 0 && $arrForm.course_cd.value[$product_index] <= 3}-->
                    <!--{$arrForm.course_cd.value[$product_index]|h}-->ヶ月ごと
                <!--{elseif $arrForm.course_cd.value[$product_index] >= 20 && $arrForm.course_cd.value[$product_index] <= 90}-->
                    <!--{$arrForm.course_cd.value[$product_index]|h}-->日ごと
                <!--{/if}-->
            </td>
            <td align="center">
                <!--{assign var=key value="price"}-->
                <!--{$arrForm[$key].value[$product_index]|number_format}-->円
            </td>
            <td align="center">
                <!--{assign var=key value="quantity"}-->
                <!--{$arrForm[$key].value[$product_index]|h}-->
            </td>
            <!--{assign var=price value=`$arrForm.price.value[$product_index]`}-->
            <!--{assign var=quantity value=`$arrForm.quantity.value[$product_index]`}-->
            <td class="right"><!--{$price|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}--> 円</td>
            <td class="right"><!--{$price|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|sfMultiply:$quantity|number_format}--> 円</td>
        </tr>
        <!--{/section}-->
        <tr>
            <th colspan="6" class="column right">小計</th>
            <td class="right"><!--{$arrForm.subtotal.value|number_format}--> 円</td>
        </tr>
<!--{*
        <tr>
            <th colspan="6" class="column right">値引</th>
            <td class="right">
                <!--{assign var=key value="discount"}-->
                <!--{$arrForm[$key].value|h}--> 円
            </td>
        </tr>
*}-->
        <tr>
            <th colspan="6" class="column right">送料</th>
            <td class="right">
                <!--{assign var=key value="deliv_fee"}-->
                <!--{$arrForm[$key].value|h}--> 円
            </td>
        </tr>
<!--{*
        <tr>
            <th colspan="6" class="column right">手数料</th>
            <td class="right">
                <!--{assign var=key value="charge"}-->
                <!--{$arrForm[$key].value|h}--> 円
            </td>
        </tr>
*}-->
        <tr>
            <th colspan="6" class="column right">合計</th>
            <td class="right">
                <span class="attention"><!--{$arrErr.total}--></span>
                <!--{$arrForm.total.value|number_format}--> 円
            </td>
        </tr>
        <!--{if $smarty.const.USE_POINT !== false}-->
            <tr>
                <th colspan="6" class="column right">使用ポイント</th>
                <td class="right">
                    <!--{assign var=key value="use_point"}-->
                    <!--{$arrForm[$key].value|h}--> pt
                </td>
            </tr>
            <!--{*
            <!--{if $arrForm.birth_point.value > 0}-->
            <tr>
                <th colspan="6" class="column right">お誕生日ポイント</th>
                <td class="right">
                    <!--{$arrForm.birth_point.value|number_format}--> pt
                </td>
            </tr>
            <!--{/if}-->
            <tr>
                <th colspan="6" class="column right">加算ポイント</th>
                <td class="right">
                    <!--{$arrForm.add_point.value|number_format|default:0}--> pt
                </td>
            </tr>
            *}-->
        <!--{/if}-->
        <tr>
            <th colspan="6" class="column right">お支払い合計</th>
            <td class="right">
                <!--{$arrForm.payment_total.value|number_format}--> 円
            </td>
        </tr>
    </table>

    <!--{assign var=key value="shipping_quantity"}-->
    <input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" />
    <!--▼お届け先情報ここから-->
    <a name="shipping"></a>
    <h2>お届け先情報</h2>

    <!--{foreach name=shipping from=$arrAllShipping item=arrShipping key=shipping_index}-->
        <!--{if $arrForm.shipping_quantity.value > 1}-->
            <h3>お届け先<!--{$smarty.foreach.shipping.iteration}--></h3>
        <!--{/if}-->
        <!--{assign var=key value="shipping_id"}-->
        <input type="hidden" name="<!--{$key}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$key]|default:"0"|h}-->" id="<!--{$key}-->_<!--{$shipping_index}-->" />
        <!--{if $arrForm.shipping_quantity.value > 1}-->
            <!--{assign var=product_quantity value="shipping_product_quantity"}-->
            <input type="hidden" name="<!--{$product_quantity}-->[<!--{$shipping_index}-->]" value="<!--{$arrShipping[$product_quantity]|h}-->" />

            <!--{if count($arrShipping.shipment_product_class_id) > 0}-->
                <table class="list" id="order-edit-products">
                    <tr>
                        <th class="id">商品コード</th>
                        <th class="name">商品名/規格1/規格2</th>
                        <th class="price">単価</th>
                        <th class="qty">数量</th>
                    </tr>
                    <!--{section name=item loop=$arrShipping.shipment_product_class_id|@count}-->
                        <!--{assign var=item_index value="`$smarty.section.item.index`"}-->

                        <tr>
                            <td>
                                <!--{$arrShipping[$key][$item_index]|h}-->
                            </td>
                            <td>
                                <!--{assign var=key1 value="shipment_product_name"}-->
                                <!--{assign var=key2 value="shipment_classcategory_name1"}-->
                                <!--{assign var=key3 value="shipment_classcategory_name2"}-->
                                <!--{$arrShipping[$key1][$item_index]|h}-->/<!--{$arrShipping[$key2][$item_index]|default:"(なし)"|h}-->/<!--{$arrShipping[$key3][$item_index]|default:"(なし)"|h}-->
                            </td>
                            <td class="right">
                                <!--{assign var=key value="shipment_price"}-->
                                <!--{$arrShipping[$key][$item_index]|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}--> 円
                            </td>
                            <td class="right">
                                <!--{assign var=key value="shipment_quantity"}-->
                                <!--{$arrShipping[$key][$item_index]|h}-->
                            </td>
                        </tr>
                    <!--{/section}-->
                </table>
            <!--{/if}-->
        <!--{/if}-->

        <table class="form">
            <tr>
                <th>お名前</th>
                <td>
                    <!--{assign var=key1 value="shipping_name"}-->
                    <!--{$arrShipping[$key1]|h}-->
                </td>
            </tr>
            <tr>
                <th>お名前(フリガナ)</th>
                <td>
                    <!--{assign var=key1 value="shipping_kana"}-->
                    <!--{$arrShipping[$key1]|h}-->
                </td>
            </tr>
            <tr>
                <th>TEL</th>
                <td>
                    <!--{assign var=key value="shipping_tel"}-->
                    <!--{$arrShipping[$key]|h}-->
                </td>
            </tr>
            <tr>
                <th>住所</th>
                <td>
                    <!--{assign var=key value="shipping_zip"}-->
                    〒<!--{$arrShipping[$key]|h}--><br />

                    <!--{assign var=key value="shipping_pref"}-->
                    <!--{assign var=val value=$arrShipping[$key]}-->
                    <!--{assign var=key value="shipping_addr01"}-->
                    <!--{$arrPref[$val]}--><!--{$arrShipping[$key]}--><br />
                    <!--{assign var=key value="shipping_addr02"}-->
                    <!--{$arrShipping[$key]}-->
                </td>
            </tr>
            <tr>
                <th>お届け時間</th>
                <td>
                    <!--{assign var=key value="time_id"}-->
                    <!--{assign var=val value=$arrShipping[$key]}-->
                    <!--{if $val}--><!--{$arrDelivTime[$val]}--><!--{else}-->指定無し<!--{/if}-->
                </td>
            </tr>
            <tr>
                <th>お届け日</th>
                <td>
                    <!--{assign var=key1 value="shipping_date_year"}-->
                    <!--{assign var=key2 value="shipping_date_month"}-->
                    <!--{assign var=key3 value="shipping_date_day"}-->
                    <!--{if $arrShipping[$key1] && $arrShipping[$key2] && $arrShipping[$key3]}-->
		    <!--{$arrShipping[$key1]}-->年<!--{$arrShipping[$key2]}-->月<!--{$arrShipping[$key3]}-->日
                    <!--{else}-->
                    指定なし
                    <!--{/if}-->
                </td>
            </tr>
            <tr>
                <th>請求書送付方法</th>
                <td>
                    <!--{if $arrForm.include_kbn.value == 1}-->同梱<!--{else}-->別送<!--{/if}-->
                </td>
            </tr>
            <tr>
                <th>配送伝票番号</th>
                <td>
                    <!--{assign var=key value="shipping_num"}-->
                    <!--{$arrShipping[$key]|h}-->
                </td>
            </tr>
            <tr>
                <th>配送業者</th>
                <td>
                    <!--{assign var=key value="deliv_id"}-->
                    <!--{assign var=val value=$arrForm[$key].value}-->
                    <!--{$arrDeliv[$val]}-->
                </td>
            </tr>
            <tr>
                <th>お支払方法</th>
                <td>
                    <!--{assign var=key value="payment_id"}-->
                    <!--{assign var=val value=$arrForm[$key].value}-->
                    <!--{$arrPayment[$val]}-->
                </td>
            </tr>
            <tr>
                <th>配達時のご要望</th>
                <td>
                    <!--{assign var=key value="box_flg"}-->
                    <!--{assign var=val value=$arrShipping[$key]}-->
                    <!--{$arrBoxFlg[$val]}-->
                </td>
            </tr>
            <tr>
                <th>アンケート</th>
                <td>
                    <!--{$arrForm.event_code.value|h}-->
                </td>
            </tr>
        </table>
    <!--{/foreach}-->
    <!--▲お届け先情報ここまで-->

        <a name="deliv"></a>
        <table class="form">
<!--▼ Veritrans 3G Module -->
<!--{include file=$smarty.const.MDL_SBIVT3G_TPL_PATH|cat:"admin/order/disp_payment.tpl"}-->
<!--▲ Veritrans 3G Module -->

            <!--{if $arrForm.payment_info|@count > 0}-->
            <tr>
                <th><!--{$arrForm.payment_type}-->情報</th>
                <td>
                    <!--{foreach key=key item=item from=$arrForm.payment_info}-->
                    <!--{if $key != "title"}--><!--{if $item.name != ""}--><!--{$item.name}-->：<!--{/if}--><!--{$item.value}--><br/><!--{/if}-->
                    <!--{/foreach}-->
                </td>
            </tr>
            <!--{/if}-->
        </table>
    
        <div class="btn-area"  >
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="window.close(); return false;"><span class="btn-next">閉じる</span></a></li>
            </ul>
        </div>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
