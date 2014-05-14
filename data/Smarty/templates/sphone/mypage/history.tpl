<!--▼CONTENTS-->
<section id="mypagecolumn">
	<h2 class="spNaked"><!--{$tpl_title}-->/<!--{$tpl_subtitle|h}--></h2>
      <div class="form_area">
               【ご注文日】<br />
               &nbsp;<!--{$tpl_arrOrderData.create_date|date_format:"%Y年%m月%d日"|h}--><br />
               【オーダー番号】<br />
               &nbsp;<!--{$tpl_arrOrderData.order_id}--><br />
               【処理状況】<br />
                <!--{assign var=i value=$tpl_arrOrderData.status}-->
                <!--{if $arrOrderStatus[$i].image_l}-->
                <div align="center">
                <img src="<!--{$TPL_URLPATH}-->img/rohto/<!--{$arrOrderStatus[$i].image_l}-->" width="250" />
                </div>
                <!--{else}-->&nbsp;<!--{$arrOrderStatus[$i].name}--><br /><!--{/if}-->
               【送り主】<br />
                    &nbsp;〒<!--{$tpl_arrOrderData.order_zip|h}--><br />
                    &nbsp;<!--{$arrPref[$tpl_arrOrderData.order_pref]}--><!--{$tpl_arrOrderData.order_addr01}--><!--{$tpl_arrOrderData.order_addr02}--><br />
                    &nbsp;<!--{$tpl_arrOrderData.order_name}-->&nbsp;様<br />
                    &nbsp;TEL:<!--{$tpl_arrOrderData.order_tel}--><br /><br />
               【ご購入金額】<br />
                    &nbsp;商品金額(税込)：<!--{$tpl_arrOrderData.subtotal|number_format}-->円<br />
                    &nbsp;送料(税込)：<!--{assign var=key value="deliv_fee"}--><!--{$tpl_arrOrderData[$key]|number_format|h}-->円<br />
                    &nbsp;使用ポイント：<!--{assign var=key value="use_point"}--><!--{$tpl_arrOrderData[$key]|number_format|h}-->pt<br />
                    &nbsp;合計金額(税込)：<!--{$tpl_arrOrderData.payment_total|number_format}-->円<br /><br />
               【お支払方法】<br />
               &nbsp;<!--{$arrPayment[$tpl_arrOrderData.payment_id]|h}--><br /><br />
               【出荷日】<br />
               &nbsp;<!--{$tpl_arrOrderData.commit_date|date_format:"%Y年%m月%d日"|h}--><br /><br />
               【配送方法】<br />
               &nbsp;<!--{$arrDelivBox[$tpl_arrOrderData.deliv_box_id]|h}--><br /><br />
               【請求書送付方法】<br />
               &nbsp;<!--{$arrIncludeKbn[$tpl_arrOrderData.include_kbn]|h}--><br />
               <br />
          【お届け先】<br />
            <!--{foreach item=shippingItem name=shippingItem from=$arrShipping}-->
               &nbsp;〒<!--{$shippingItem.shipping_zip}--><br />
               &nbsp;<!--{$arrPref[$shippingItem.shipping_pref]}--><!--{$shippingItem.shipping_addr01|h}--><!--{$shippingItem.shipping_addr02|h}--><br />
               &nbsp;<!--{$shippingItem.shipping_name|h}-->&nbsp;様<br />
               &nbsp;電話番号:<!--{$shippingItem.shipping_tel}--><br />
               &nbsp;【お届け希望日】<br />
               &nbsp;&nbsp;<!--{$shippingItem.shipping_date|default:'指定なし'|h}--><br />
               &nbsp;【時間指定】<br />
               &nbsp;&nbsp;<!--{$shippingItem.shipping_time|default:'指定なし'|h}--><br />
               &nbsp;【配送時のご要望】<br />
               &nbsp;&nbsp;<!--{$tpl_arrOrderData.note|default:'指定なし'|h}--><br />
               &nbsp;【伝票番号】<br />
               &nbsp;&nbsp;<!--{$shippingItem.shipping_num|default:''|h}--><br />
               &nbsp;【荷物問合せURL】<br />
               &nbsp;&nbsp;<a href="<!--{$shippingItem.confirm_url|default:''|h}-->" target="_blank"><!--{$shippingItem.confirm_url|default:''|h}--></a>
            <!--{/foreach}-->


             <form action="order.php" method="post">
              <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
              <input type="hidden" name="order_id" value="<!--{$tpl_arrOrderData.order_id}-->">
          </form>

      【ご注文商品】

      <!--▼商品 -->
      <!--{foreach from=$tpl_arrOrderDetail item=orderDetail name=orderDetail}-->
        <div>
                                <div>
           <!--{if !$smarty.foreach.orderDetail.first}-->
           <br>
           <!--{/if}-->

           <p>&nbsp;<!--{* 商品名 *}--><a<!--{if $orderDetail.enable}--> href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$orderDetail.product_id|u}-->"<!--{/if}--> rel="external"><!--{$orderDetail.product_name|h}--></a><!--←商品名-->
               <!--{if $orderDetail.classcategory_name1 != ""}--><br />
                   &nbsp;<!--{$orderDetail.classcategory_name1}-->
               <!--{/if}-->
               <!--{if $orderDetail.classcategory_name2 != ""}-->
                   &nbsp;<!--{$orderDetail.classcategory_name2}-->
               <!--{/if}-->
           </p>
           <p>
              <!--{* 金額 *}-->
              <!--{assign var=price value=`$orderDetail.price`}-->
              <!--{assign var=quantity value=`$orderDetail.quantity`}-->
              &nbsp;価格:<!--{$price|number_format|h}-->円<!--←金額-->
           </p>
           <p>
              <!--{* 数量 *}-->
              &nbsp;数量：<!--{$quantity|h}-->
           </p>
           <p>
              <!--{* お届け間隔 *}-->
              &nbsp;お届け間隔：
                <!--{assign var=key1 value=`$orderDetail.course_cd`}-->
                <!--{if $key1 > 0 && $key1 <= 3}-->
                    <!--{$key1|h}-->ヶ月ごと
                <!--{elseif $key1 >= 20 && $key1 <= 90}-->
                    <!--{$key1|h}-->日ごと
                <!--{/if}-->
           </p>
    </div>
  </div>
  <!--{/foreach}-->
<!--▲商品 -->

   </div><!--▲formBox -->

       <p style="margin:0 10px;"><a rel="external" class="btn_more" href="<!--{$smarty.const.ROOT_URLPATH}-->contact/">このご注文へのお問い合わせ</a></p>

  </div><!--▲form_area -->

</section>
<!--▲CONTENTS -->
