<section id="mypagecolumn">
  <h2 class="spNaked">マイページ</h2>
  <h3 class="title_mypage">定期購入詳細(確認)</h3>

   <form name="form1" id="form1" method="post" action="regular_change.php">
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

      <input type="hidden" name="anchor_key" value="" />
      <input type="hidden" id="add_product_id" name="add_product_id" value="" />
      <input type="hidden" id="add_product_class_id" name="add_product_class_id" value="" />
      <input type="hidden" id="edit_product_id" name="edit_product_id" value="" />
      <input type="hidden" id="edit_product_class_id" name="edit_product_class_id" value="" />
      <input type="hidden" id="no" name="no" value="" />
      <input type="hidden" id="delete_no" name="delete_no" value="" />

    <table summary="定期購入詳細" class="tblOrder delivname" style="width:100%;">

        <tr>
            <th>次回お届け日</th>
        </tr>
        <tr>
			<td>
                <input type="hidden" name="next_arrival_date" value="<!--{$arrForm.next_arrival_date.value}-->" id="next_arrival_date" />
                <!--{$arrForm.next_arrival_date.value|date_format:"%Y年%m月%d日"|h}-->
            </td>
        </tr>
        <tr>
            <th>次々回お届け日</th>
        </tr>
        <tr>
            <td>
                <input type="hidden" name="after_next_arrival_date" value="<!--{$arrForm.after_next_arrival_date.value}-->" id="after_next_arrival_date" />
                <!--{$arrForm.after_next_arrival_date.value|date_format:"%Y年%m月%d日"|h}-->
            </td>
        </tr>
        <tr>
            <th>お届け間隔</th>
        </tr>
        <tr>
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
        <tr>
            <th>定期購入 変更商品</th>
            <input type="hidden" name="brand_id" value="<!--{$arrForm.brand_id.value}-->" id="brand_id" />
        </tr>
		<!--{foreach from=$arrCartKeys item=key}-->
		<!--{foreach from=$arrCart[$key] item=item}-->
			<!--{assign var=sub_total value=$sub_total+$item.total_inctax}-->
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
			数量：<!--{$item.quantity|h}--><br />
			価格：<!--{$item.price|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
			小計：<!--{$item.total_inctax|number_format}-->円
			</td>
		</tr>
		<!--{/foreach}-->
		<!--{/foreach}-->
        <tr>
            <td>
			<table cellpadding="0" cellspacing="0" class="cartGoods02" style="margin-bottom:10px;">
				<tr>
					<td colspan="2" style="background-color:#FFF;padding:15px 9px;color:#464646; font-weight:bold; vertical-align:middle; text-align:center;">
						小計
						<span style="font-size:1.125em;color:#ff8f00;font-weight:bold;"><!--{$sub_total|number_format}-->円</span>
						＋
						送料
						<span style="font-size:1.125em;color:#ff8f00;font-weight:bold;"><!--{$arrData.deliv_fee|number_format}-->円</span>
					</td>
				</tr>
			</table>
            </td>
        </tr>
        <tr>
            <th>お支払方法</th>
        </tr>
        <tr>
            <td>
                <input type="hidden" name="payment_id" value="<!--{$arrForm.payment_id.value}-->" id="payment_id" />
                <!--{$arrPayment[$arrForm.payment_id.value]|h}-->
            </td>
        </tr>
        <tr>
            <th>お届け先情報</th>
        </tr>
        <tr>
            <td>
                <input type="hidden" name="order_name" value="<!--{$arrForm.order_name.value}-->" id="order_name" />
                <input type="hidden" name="order_zip" value="<!--{$arrForm.order_zip.value}-->" id="order_zip" />
                <input type="hidden" name="order_pref" value="<!--{$arrForm.order_pref.value}-->" id="order_pref" />
                <input type="hidden" name="order_addr01" value="<!--{$arrForm.order_addr01.value}-->" id="order_addr01" />
                <input type="hidden" name="order_addr02" value="<!--{$arrForm.order_addr02.value}-->" id="order_addr02" />
                <input type="hidden" name="order_tel" value="<!--{$arrForm.order_tel.value}-->" id="order_tel" />
                お届け先住所：<br />
                〒<!--{$arrForm.order_zip.value|h}-->
                <!--{$arrPref[$arrForm.order_pref.value]|h}--><!--{$arrForm.order_addr01.value|h}--><!--{$arrForm.order_addr02.value|h}--><br />
                <!--{$arrForm.order_name.value|h}--> 様<br />
                お届け先お電話番号：<br />
                <!--{$arrForm.order_tel.value|h}-->
            </td>
        </tr>

    </table>

    <!--{* 確認ページへ進む *}-->
    <p class="btn" style="margin-top:1em;">
        <a href="javascript:void(0);" onclick="document.form1.submit();return false;" class="btnOrange">登録</a>
    </p>
    <p style="margin:1em 0;">
        <a href="javascript:void(0);" onclick="fnModeSubmit('return', '', ''); return false;" class="btnGray03">戻る</a>
    </p>

    </form>
