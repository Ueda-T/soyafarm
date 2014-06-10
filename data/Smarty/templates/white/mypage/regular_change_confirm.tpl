<!--▼CONTENTS-->
<p class="pankuzu">
	<a href="<!--{$smarty.const.ROOT_URLPATH}-->"><!--{$smarty.const.TPL_PC_HOME_NAME}--></a>
	&nbsp;&gt;&nbsp;
	<a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/"><!--{$tpl_title}--></a>
	&nbsp;&gt;&nbsp;
	<a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/regular.php">定期購入一覧</a>
	&nbsp;&gt;&nbsp;
	<!--{$tpl_subtitle}-->
</p>

<!--{if $tpl_navi != ""}-->
	<!--{include file=$tpl_navi}-->
<!--{else}-->
	<!--{include file=`$smarty.const.TEMPLATE_REALDIR`mypage/navi.tpl}-->
<!--{/if}-->

<h1><img src="<!--{$TPL_URLPATH}-->img/rohto/regular_change_title.gif" alt="定期購入変更手続き" /></h1>

<!--{*
<!--{if !$tpl_disable_logout}-->
<p class="logout">
	<input type="image" src="<!--{$TPL_URLPATH}-->img/soyafarm/logout.gif" onclick="fnFormModeSubmit('header_login_form', 'logout', '', ''); return false;" alt="ログアウト" class="swp" />
</p>
<!--{/if}-->
*}-->

<div class="wrapCoan">
	<div class="alert2">
		<h3>ご変更手続きはまだ完了していません。</h3>
	</div>
	<p>以下の内容をご確認の上、画面下の「変更を確定する」ボタンをクリックしてください。</p>
</div>

<form name="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="complete" />
<input type="hidden" name="regular_id" value="<!--{$arrForm.regular_id.value}-->" />
<input type="hidden" name="line_no" value="<!--{$arrForm.line_no.value}-->" />
<input type="hidden" name="pageno" value="<!--{$objNavi->nowpage}-->" />

<input type="hidden" name="todoke_day" value="<!--{$arrForm.todoke_day.value}-->" />
<input type="hidden" name="status" value="<!--{$arrForm.status.value}-->" />
<input type="hidden" name="cancel_date" value="<!--{$arrForm.cancel_date.value}-->" />
<input type="hidden" name="cancel_reason_cd" value="<!--{$arrForm.cancel_reason_cd.value}-->" />
    <input type="hidden" name="deliv_date_id" value="<!--{$arrForm.deliv_date_id.value}-->" />

<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />

<input type="hidden" name="order_id" value="<!--{$arrForm.order_id.value|h}-->" />
<input type="hidden" name="edit_customer_id" value="" />
<input type="hidden" name="anchor_key" value="" />
<input type="hidden" id="add_product_id" name="add_product_id" value="" />
<input type="hidden" id="add_product_class_id" name="add_product_class_id" value="" />
<input type="hidden" id="edit_product_id" name="edit_product_id" value="" />
<input type="hidden" id="edit_product_class_id" name="edit_product_class_id" value="" />
<input type="hidden" id="no" name="no" value="" />
<input type="hidden" id="delete_no" name="delete_no" value="" />

<div class="wrapCoan">
	<h2 style="margin:30px 0 20px 0;"><img src="<!--{$TPL_URLPATH}-->img/rohto/method_midashi_r05.gif" width="820" height="35" alt="お届け間隔"></h2>
	<table summary="定期購入一覧" style="margin-top:15px;" class="tblOrder">
        <colgroup width="20%"></colgroup>
        <colgroup width="80%"></colgroup>
		<tr>
			<th><span>次回お届け日</span></th>
			<td>
                <input type="hidden" name="next_arrival_date" value="<!--{$arrForm.next_arrival_date.value}-->" id="next_arrival_date" />
                <!--{$arrForm.next_arrival_date.value|date_format:"%Y年%m月%d日"|h}-->
            </td>
		</tr>
		<tr>
			<th><span>次々回お届け日</span></th>
			<td>
                <input type="hidden" name="after_next_arrival_date" value="<!--{$arrForm.after_next_arrival_date.value}-->" id="after_next_arrival_date" />
                <!--{$arrForm.after_next_arrival_date.value|date_format:"%Y年%m月%d日"|h}-->
            </td>
		</tr>
		<tr>
			<th><span>お届け間隔</span></th>
			<td>
                <input type="hidden" name="course_cd" value="<!--{$arrForm.course_cd.value}-->" id="course_cd" />
                <input type="hidden" name="todoke_kbn" value="<!--{$arrForm.todoke_kbn.value}-->" id="todoke_kbn" />
                <input type="hidden" name="todoke_cycle" value="<!--{$arrForm.todoke_cycle.value}-->" id="todoke_cycle" />
                <input type="hidden" name="todoke_week" value="<!--{$arrForm.todoke_week.value}-->" id="todoke_week" />
                <input type="hidden" name="todoke_week2" value="<!--{$arrForm.todoke_week2.value}-->" id="todoke_week2" />
                <!--{$arrCourseCd[$arrForm.course_cd.value]|h}--><!--{$arrTodokeKbn[$arrForm.todoke_cycle.value]|h}-->
                &nbsp;
                <!--{if $arrForm.todoke_week.value}-->
                <!--{$arrTodokeWeekNo[$arrForm.todoke_week.value]|h}--><!--{$arrTodokeWeek[$arrForm.todoke_week2.value]|h}-->曜日
                <!--{/if}-->
            </td>
		</tr>
    </table>
</div>

<!--{* ▼変更前商品情報 *}-->
<div class="cartList">
	<h2 style="margin:30px 0 20px 0;"><img src="<!--{$TPL_URLPATH}-->img/rohto/method_midashi_r04.gif" width="820" height="35" alt="定期購入変更商品" /></h2>
	<table class="list">
		<tr>
			<th class="alignC">商品名</th>
			<th class="num">数量</th>
			<th class="price" nowrap>小計<span class="dyn">(税込)</span></th>
		</tr>
		<!--{foreach from=$arrCartKeys item=key}-->
		<!--{foreach from=$arrCart[$key] item=item}-->
		<tr>
			<td>
			<!--{$item.productsClass.name|h}--><br />
			<!--{if $item.productsClass.product_code_min == $item.productsClass.product_code_max}-->
				<!--{$item.productsClass.product_code_min|h}-->
			<!--{else}-->
				<!--{$item.productsClass.product_code_min|h}-->～<!--{$item.productsClass.product_code_max|h}-->
			<!--{/if}-->

			<!--{if $item.productsClass.classcategory_name1 != ""}-->
				<!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--><br />
			<!--{/if}-->
			<!--{if $item.productsClass.classcategory_name2 != ""}-->
				<!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}-->
			<!--{/if}-->
			<br />
			価格：<!--{$item.price|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
			</td>
			<td class="alignC"><!--{$item.quantity|h}-->
			</td>
			<td class="alignR">
			<!--{assign var=sub_total value=$sub_total+$item.total_inctax}-->
			<!--{$item.total_inctax|number_format}-->円
			</td>
		</tr>
		<!--{/foreach}-->
		<!--{/foreach}-->
	</table>

	<div class="cartTotalBox">
		<div class="inner">
			<p class="subtotal">
				小計
				<strong><!--{$sub_total|number_format}-->円
				＋</strong>
				
				送料
				<strong><!--{$arrData.deliv_fee|number_format}-->円</strong>
			</p>
		</div>
	</div>

</div>
<!--{* ▲変更前商品情報 *}-->

<!--{* ▼お届け先情報 *}-->
<div class="wrapCoan">
	<h2 style="margin:30px 0 20px 0;"><img src="<!--{$TPL_URLPATH}-->img/rohto/method_midashi03.gif" width="820" height="35" alt="お届け先"></h2>
    <table style="margin-top:15px;" class="tblOrder">
        <colgroup width="20%"></colgroup>
        <colgroup width="80%"></colgroup>
		<tr>
			<th><span>お支払方法</span></th>
			<td>
                <input type="hidden" name="payment_id" value="<!--{$arrForm.payment_id.value}-->" id="payment_id" />
                <!--{$arrPayment[$arrForm.payment_id.value]|h}-->
            </td>
		</tr>
		<tr>
			<th><span>お届け先住所</span></th>
			<td>
                <input type="hidden" name="order_name" value="<!--{$arrForm.order_name.value}-->" id="order_name" />
                <input type="hidden" name="order_zip" value="<!--{$arrForm.order_zip.value}-->" id="order_zip" />
                <input type="hidden" name="order_pref" value="<!--{$arrForm.order_pref.value}-->" id="order_pref" />
                <input type="hidden" name="order_addr01" value="<!--{$arrForm.order_addr01.value}-->" id="order_addr01" />
                <input type="hidden" name="order_addr02" value="<!--{$arrForm.order_addr02.value}-->" id="order_addr02" />
                〒<!--{$arrForm.order_zip.value|h}-->
                <!--{$arrPref[$arrForm.order_pref.value]|h}--><!--{$arrForm.order_addr01.value|h}--><!--{$arrForm.order_addr02.value|h}--><br />
                <!--{$arrForm.order_name.value|h}--> 様
                
            </td>
		</tr>
		<tr>
			<th><span>お届け先お電話番号</span></th>
			<td>
                <input type="hidden" name="order_tel" value="<!--{$arrForm.order_tel.value}-->" id="order_tel" />
                <!--{$arrForm.order_tel.value|h}--></td>
		</tr>
    </table>
    <!--{* ▲お届け先情報 *}-->
</div>

<!--{* 完了ページへ進む *}-->
<div class="wrapCoan">
    <div class="orderBtn">
		<p class="modoru"><a href="javascript:void(0);" onclick="fnModeSubmit('return', '', ''); return false;">
            <img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_back.gif" alt="戻る" name="back" id="back" class="swp" />
        </a></p>
        <a href="javascript:void(0);" onclick="document.form1.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/rohto/regular_change_btn04.gif" alt="変更を確定する" name="complete" id="complete" class="swp" /></a>
    </div>
</div>

</form>
