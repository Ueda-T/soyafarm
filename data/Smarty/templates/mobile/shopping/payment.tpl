<form method="post" action="<!--{$smarty.const.MOBILE_SHOPPING_PAYMENT_URLPATH}-->">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="confirm">
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
<!--{assign var=key value="deliv_id"}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->">
■お届け先<br>
<font size="-1" color="#db1b1c">新たな配送先につきましては､ﾏｲﾍﾟｰｼﾞ｢配送先新規登録｣にてご登録いただけます｡</font><br>
<input type="hidden" name="other_deliv_id" value="" />
<!--{if $arrErr.deli != ""}-->
	<font color="#FF0000"><!--{$arrErr.deli}--></font>
<!--{/if}-->

<!--{section name=cnt loop=$arrAddr}-->

<!--{if $smarty.section.cnt.first}-->
	<input type="radio" name="deliv_check" id="chk_id_<!--{$smarty.section.cnt.iteration}-->" value="-1" <!--{if $arrForm.deliv_check.value == "" || $arrForm.deliv_check.value == -1}--> checked="checked"<!--{/if}--> />
<!--{else}-->
	<input type="radio" name="deliv_check" id="chk_id_<!--{$smarty.section.cnt.iteration}-->" value="<!--{$arrAddr[cnt].other_deliv_id}-->"<!--{if $arrForm.deliv_check.value == $arrAddr[cnt].other_deliv_id}--> checked="checked"<!--{/if}--> />
<!--{/if}-->

<label for="chk_id_<!--{$smarty.section.cnt.iteration}-->">
<!--{$arrAddr[cnt].name|h}-->
</label><br>

<label for="chk_id_<!--{$smarty.section.cnt.iteration}-->">
〒<!--{$arrAddr[cnt].zip|h}-->
</label>

<!--{assign var=key value=$arrAddr[cnt].pref}-->
<!--{$arrPref[$key]}--><!--{$arrAddr[cnt].addr01|h}--><!--{$arrAddr[cnt].addr02|h}--><br>
<!--{/section}-->
<br>

■お支払方法 <font color="#FF0000">*</font><br>
<!--{assign var=key value="payment_id"}-->
<!--{if $arrErr[$key] != ""}-->
<font color="#FF0000"><!--{$arrErr[$key]}--></font>
<!--{/if}-->
<!--{section name=cnt loop=$arrPayment}-->
<input type="radio" name="<!--{$key}-->" value="<!--{$arrPayment[cnt].payment_id}-->" <!--{$arrPayment[cnt].payment_id|sfGetChecked:$arrForm[$key].value}-->>
<!--{$arrPayment[cnt].payment_method|h}-->
<br>
<!--{/section}-->
<br>
<!--{if $cartKey != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
    <!--{if $mail_deliv_flg === false}--><!--{* ▼メール便判定 *}-->
■お届け時間の指定<br>
<!--{foreach item=shippingItem name=shippingItem from=$arrShipping}-->
<!--{assign var=index value=$shippingItem.shipping_id}-->

<!--{if $is_multiple}-->
    ▼<!--{$shippingItem.shipping_name01}--><!--{$shippingItem.shipping_name02}-->
    <!--{$arrPref[$shippingItem.shipping_pref]}--><!--{$shippingItem.shipping_addr01}--><!--{$shippingItem.shipping_addr02}--><br>
<!--{/if}-->

<!--★お届け日★-->
<!--{assign var=key value="deliv_date`$index`"}-->
<font color="#FF0000"><!--{$arrErr[$key]}--></font>
お届け日：<br>
<!--{if !$arrDelivDate}-->
    ご指定頂けません。
<!--{else}-->
    <select name="<!--{$key}-->">
        <option value="" selected="">指定なし</option>
        <!--{assign var=shipping_date_value value=$arrForm[$key].value|default:$shippingItem.shipping_date}-->
        <!--{html_options options=$arrDelivDate selected=$shipping_date_value}-->
    </select>
<!--{/if}-->
<br>
<!--★お届け時間★-->
<!--{assign var=key value="deliv_time_id`$index`"}-->
<font color="#FF0000"><!--{$arrErr[$key]}--></font>
お届け時間：<br>
<select name="<!--{$key}-->" id="<!--{$key}-->">
    <option value="" selected="">指定なし</option>
    <!--{assign var=shipping_time_value value=$arrForm[$key].value|default:$shippingItem.time_id}-->
    <!--{html_options options=$arrDelivTime selected=$shipping_time_value}-->
</select>
<br>

<br>
<!--{/foreach}-->
■配達時のご要望<br>
<!--{assign var=key value="box_flg`$index`"}-->
<!--{if $tpl_is_cool === false }-->
<select name="<!--{$key}-->" id="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
<option value="" selected=""></option>
<!--{assign var=box_flg value=$arrForm[$key].value|default:$shippingItem.box_flg}-->
<!--{html_options options=$arrBoxFlg selected=$box_flg}-->
</select>
<!--{else}-->
冷蔵・冷凍便のため、ご指定頂けません。<br>
<!--{/if}-->
<br />
<font color="#FF0000;"><!--{$arrErr[$key]}--></font><br />
<font color="#FF0000;">※冷蔵・冷凍便を含む場合は、宅配BOXへのお届けは出来ません。</font><br>
<br>

    <!--{else}-->
■配送の種類 メール便<br>
<font color="#FF0000;">※郵便受けへのお届けにつき、配達日時のご指定は承れません。</font><br>
<br>

    <!--{/if}-->
<!--{/if}-->

■請求書(明細書)のご送付方法 <font color="#FF0000">*</font><br>
<!--{assign var=key value="include_kbn"}-->
<!--{if $arrErr[$key] != ""}-->
<font color="#FF0000"><!--{$arrErr[$key]}--></font><br>
<!--{/if}-->
<!--{foreach from=$arrIncludeKbn item=str_include_kbn key=idx}-->
<input type="radio" id="radio_inc_kbn_<!--{$idx}-->" name="<!--{$key}-->"  value="<!--{$idx}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" <!--{$idx|sfGetChecked:$arrForm[$key].value}--> /><!--{$str_include_kbn}--><br>
<!--{/foreach}-->
<br>

<!--{if $tpl_campaign_code|default:''|strlen == 0 && $customer_kbn != $smarty.const.CUSTOMER_KBN_EMPLOYEE}-->
<div class="wrapCoanEle">
■アンケート <font color="#FF0000">*</font><br>
<!--{assign var=key value="event_code"}-->
<!--{if $arrErr[$key] != ""}-->
<font color="#FF0000"><!--{$arrErr[$key]}--></font>
<!--{/if}-->
今回お買い求めいただいたきっかけをお聞かせください。<br>
<!--{foreach from=$arrPlanningData item=arrRecord key=idx}-->
<input type="radio" id="event_code_<!--{$idx}-->" name="<!--{$key}-->"  value="<!--{$arrRecord.media_code}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" <!--{$arrRecord.media_code|sfGetChecked:$arrForm[$key].value}--> /><label for="event_code_<!--{$idx}-->"><!--{$arrRecord.planning_name|h}--></label>
<!--{/foreach}-->
<br><br>
<!--{/if}-->

<!--{*
■その他お問い合わせ<br>
<!--{assign var=key value="message"}-->
<!--{if $arrErr[$key] != ""}-->
<font color="#FF0000"><!--{$arrErr[$key]}--></font>
<!--{/if}-->
<textarea cols="20" rows="2" name="<!--{$key}-->"><!--{$arrForm[$key].value|h}--></textarea>
<br>
<br>
*}-->

<!--{*
<!--{if $tpl_login == 1 && $smarty.const.USE_POINT !== false}-->
■ポイント使用の指定<br>
現在の所持ポイントは「<font color="#FF0000"><!--{$tpl_user_point|number_format|default:0}-->Pt</font>」です。<br>
<br>
今回ご購入合計金額：<font color="#FF0000"><!--{$arrPrices.subtotal|number_format}-->円</font><br>
(送料、手数料を含みません。)<br>
<br>
<input type="radio" name="point_check" value="1" <!--{$arrForm.point_check.value|sfGetChecked:1}-->>ポイントを使用する<br>
<!--{assign var=key value="use_point"}-->
<!--{if $arrErr[$key] != ""}-->
<font color="#FF0000"><!--{$arrErr[$key]}--></font>
<!--{/if}-->
<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|default:$tpl_user_point}-->" maxlength="<!--{$arrForm[$key].length}-->" size="6">&nbsp;ポイントを使用する。<br>
<input type="radio" name="point_check" value="2" <!--{$arrForm.point_check.value|sfGetChecked:2}-->>ポイントを使用しない<br>
<br>
<!--{/if}-->
*}-->

<center><input type="submit" value="次へ"></center>
</form>

<!--{if $is_single_deliv}-->
<form action="<!--{$tpl_back_url|h}-->" method="get">
<!--{if $is_multiple}-->
<input type="hidden" name="from" value="multiple">
<!--{/if}-->
<!--{else}-->
<form action="<!--{$smarty.const.MOBILE_SHOPPING_PAYMENT_URLPATH}-->" method="post">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="">
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
<!--{/if}-->
<center><input type="submit" name="return" value="戻る"></center>
</form>

