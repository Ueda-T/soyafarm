<!--▼CONTENTS-->
<div id="mainMyPage">
	<h2 class="spNaked"><!--{$tpl_title}-->/<!--{$tpl_subtitle|h}--></h2>

	<!--{if $tpl_navi != ""}-->
		<!--{include file=$tpl_navi}-->
	<!--{else}-->
		<!--{include file=`$smarty.const.TEMPLATE_REALDIR`mypage/navi.tpl}-->
	<!--{/if}-->

      <div class="form_area">
		<table class="tblOrder">
			<tr>
				<th>ご注文日</th>
			</tr>
			<tr>
				<td><!--{$tpl_arrOrderData.create_date|date_format:"%Y年%m月%d日"|h}--></td>
			</tr>
			<tr>
				<th>注文番号</th>
			</tr>
			<tr>
				<td><!--{$tpl_arrOrderData.order_id}--></td>
			</tr>
			<tr>
				<th>処理状況</th>
			</tr>
			<tr>
				<td>
                <!--{assign var=i value=$tpl_arrOrderData.status}-->
                <!--{if $arrOrderStatus[$i].image_l}-->
                <div align="center">
                <img src="<!--{$TPL_URLPATH}-->img/rohto/<!--{$arrOrderStatus[$i].image_l}-->" width="250" />
                </div>
                <!--{else}-->&nbsp;<!--{$arrOrderStatus[$i].name}--><br /><!--{/if}-->
				</td>
			<tr>
				<th>送り主</th>
			</tr>
			<tr>
				<td>
                    〒<!--{$tpl_arrOrderData.order_zip|h}--><br />
                    <!--{$arrPref[$tpl_arrOrderData.order_pref]}--><!--{$tpl_arrOrderData.order_addr01}--><!--{$tpl_arrOrderData.order_addr02}--><br />
                    <!--{$tpl_arrOrderData.order_name}-->&nbsp;様<br />
                    電話番号:<!--{$tpl_arrOrderData.order_tel}-->
				</td>
			</tr>
			<tr>
				<th>ご購入金額</th>
			</tr>
			<tr>
				<td>
                    &nbsp;商品金額(税込)：<!--{$tpl_arrOrderData.subtotal|number_format}-->円<br />
                    &nbsp;送料(税込)：<!--{assign var=key value="deliv_fee"}--><!--{$tpl_arrOrderData[$key]|number_format|h}-->円<br />
                    &nbsp;合計金額(税込)：<!--{$tpl_arrOrderData.payment_total|number_format}-->円<br /><br />
				</td>
			<tr>
				<th>お支払方法</th>
			</tr>
			<tr>
				<td><!--{$arrPayment[$tpl_arrOrderData.payment_id]|h}--><br /></td>
			</tr>
			<tr>
				<th>出荷日</th>
			</tr>
			<tr>
				<td><!--{$tpl_arrOrderData.commit_date|date_format:"%Y年%m月%d日"|h}--></td>
			<tr>
				<th>配送方法</th>
			</tr>
			<tr>
				<td><!--{$arrDelivBox[$tpl_arrOrderData.deliv_box_id]|h}--><br /></td>
			<tr>
				<th>請求書送付方法</th>
			</tr>
			<tr>
				<td><!--{$arrIncludeKbn[$tpl_arrOrderData.include_kbn]|h}--></td>
			</tr>
			<tr>
				<th>お届け先</th>
			</tr>
			<tr>
				<td>
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
				</td>
			</tr>
		</table>

             <form action="order.php" method="post">
              <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
              <input type="hidden" name="order_id" value="<!--{$tpl_arrOrderData.order_id}-->">
          </form>

      <!--▼商品 -->
		<table class="tblOrder mt20">
			<tr>
				<th>ご注文商品</th>
			</tr>
			<tr>
      <!--{foreach from=$tpl_arrOrderDetail item=orderDetail name=orderDetail}-->
			<tr>
				<td>
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
				</td>
			</tr>
  <!--{/foreach}-->
		</table>
<!--▲商品 -->

   </div><!--▲formBox -->

<!--{*
       <p style="margin:0 10px;"><a rel="external" class="btn_more" href="<!--{$smarty.const.ROOT_URLPATH}-->contact/">このご注文へのお問い合わせ</a></p>
*}-->

  </div><!--▲form_area -->

</div>
<!--▲CONTENTS -->
