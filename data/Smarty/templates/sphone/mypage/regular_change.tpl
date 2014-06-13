<link rel="stylesheet" href="<!--{$TPL_URLPATH}-->js/mypage/jquery-ui.css" type="text/css" media="all" />
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/mypage/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/mypage/jquery-ui.min.js"></script>

<link rel="stylesheet" href="<!--{$TPL_URLPATH}-->css/jquery_ui.css" type="text/css" media="all" />
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/jquery.ui.core.min.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/jquery.ui.datepicker-ja.min.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/jquery.ui.datepicker.min.js"></script>

<script type="text/javascript">//<![CDATA[
//]]>
$(document).ready(function() {
    // お届け曜日の表示制御
    function selectTodokeKbn() {
        var todoke_cycle = 'select[name="todoke_cycle"]';
        // 「日ごと)」を選択した場合はお届け曜日を非表示
        if ($(todoke_cycle).val() ==
            '<!--{$smarty.const.TODOKE_CYCLE_DAY}-->') {

            $("#todoke_week").val("");
            $("#todoke_week2").val("");
            $("#todoke_week").css("display", "none");
            $("#todoke_week2").css("display", "none");

        } else {
            $("#todoke_week").css("display", "");
            $("#todoke_week2").css("display", "");
        }
    }
    var type_all_exp = 'select[name^="todoke_cycle"]';
    $(type_all_exp).each(function(){
        var index = $(this).attr('name').replace('todoke_cycle', '');
        selectTodokeKbn();
        $(this).change(function(){selectTodokeKbn();});
    });

    if ($("#after_product_id").val() != "") {
        // 削除リンク表示
        $("#after_product_delete").css("display", "");
    }
    
});

// 追加商品行の削除
function deleteProduct(no) {
    if (no === undefined) {
        // 変更後商品情報を削除
        if(!confirm("変更後商品を削除します。")) {
            return;
        }
        document.form1['after_product_id'].value = '';
        document.form1['after_product_class_id'].value = '';
        document.form1['after_product_name'].value = '';
        document.form1['after_product_name_str'].value = '';

        // 削除リンクを非表示
        $("#after_product_delete").css("display", "none");

    } else {
        // 追加商品情報を削除
        if(!confirm("追加商品を削除します。")) {
            return;
        }
        $('#delete_no').val(no);
        document.form1['mode'].value = 'delete_product';
        document.form1['anchor_key'].value = 'order_products';
        document.form1.submit();
    }
}

// 次回お届け日カレンダー表示（datepicker）
$(function() {
	$(".calendar").datepicker();
    $(".calendar").datepicker("option", "showOn", 'both');
    $(".calendar").datepicker("option", "buttonImage",
                              '<!--{$TPL_URLPATH}-->img/common/calendar.png');
    $(".calendar").datepicker("option", "buttonImageOnly", true);
});

</script>

<section id="mypagecolumn">
  <h2 class="spNaked">マイページ</h2>
  <h3 class="title_mypage">定期購入詳細</h3>

<!--{* 選択済みの商品エラーメッセージ *}-->
<!--{if $tpl_message}-->
<p class="attention naked"><!--{$tpl_message}--></p>
<!--{/if}-->

   <form name="form1" id="form1" method="post" action="regular_change.php">
      <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
      <input type="hidden" name="cart_no" value="" />
      <input type="hidden" name="cartKey" value="1" />
	  <input type="hidden" name="mode" value="confirm" />
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
                <!--{assign var="mode" value="change_date"}-->
                <span class="attention"><!--{$arrErr.next_arrival_date}--></span>
                <input type="text" name="next_arrival_date" value="<!--{$arrForm.next_arrival_date.value|h}-->" onChange="fnModeSubmit('<!--{$mode}-->', '', '');" maxlength="10" style="<!--{if $arrErr.next_arrival_date != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="12" class="calendar" />
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
                <p><span class="attention"><!--{$arrErr.course_cd}--></span></p>
                <p><span class="attention"><!--{$arrErr.todoke_cycle}--></span></p>
                <p><span class="attention"><!--{$arrErr.todoke_week}--></span></p>
                <p><span class="attention"><!--{$arrErr.todoke_week2}--></span></p>
                <!--{assign var="mode" value="change_cycle"}-->
                <input type="hidden" name="todoke_kbn" value="<!--{$arrForm.todoke_kbn.value}-->" id="todoke_kbn" />
                <!--{* 1～3, 20～90 *}-->
                <select name="course_cd" id="course_cd" onChange="fnModeSubmit('<!--{$mode}-->', '', '');">
                    <!--{html_options options=$arrCourseCd selected=$arrForm.course_cd.value}-->
                </select>&nbsp;

                <!--{* 日ごと, ヶ月ごと *}-->
                <select name="todoke_cycle" id="todoke_cycle" onChange="fnModeSubmit('<!--{$mode}-->', '', '');">
                    <!--{html_options options=$arrTodokeKbn selected=$arrForm.todoke_cycle.value}-->
                </select>&nbsp;&nbsp;

                <!--{* 第1～第4 *}-->
                <select name="todoke_week" id="todoke_week" onChange="fnModeSubmit('<!--{$mode}-->', '', '');">
                    <option></option>
                    <!--{html_options options=$arrTodokeWeekNo selected=$arrForm.todoke_week.value}-->
                </select>&nbsp;&nbsp;

                <!--{* 日～土 *}-->
                <select name="todoke_week2" id="todoke_week2" onChange="fnModeSubmit('<!--{$mode}-->', '', '');">
                    <option></option>
                    <!--{html_options options=$arrTodokeWeek selected=$arrForm.todoke_week2.value}-->
                </select>&nbsp;&nbsp;
            </td>
        </tr>
      </table>
      <h2 style="background-color:#88C442;color:#FFF;border-radius:4px 4px 0 0;margin-top:10px;padding:10px;font-weight:bold;font-size:0.85em;">定期購入 変更商品</h2>
			<table cellpadding="0" cellspacing="0" class="cartGoods" style="margin-top:0;">
			<!--{foreach from=$arrCartKeys item=key}-->
			<!--{foreach from=$arrCart[$key] item=item}-->
                <tr style="<!--{if $item.error}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->">
<!--{*
					<td rowspan="3" width="120">
						<img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.main_list_image|sfNoImageMainList|h}-->" alt="<!--{$item.productsClass.name|h}-->" width="65" />
					</td>
*}-->
					<td style="padding:10px 9px; width:100%;">
						<!--{* 商品名 *}-->
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
					</td>
				</tr>
				<tr>
					<td style="padding:15px 9px 0 0; text-align:right;">
						<!--{$item.price|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
                    </td>
				</tr>
				<tr>
					<td style="padding:5px 9px 0 0; text-align:right;vertical-align:middle;">
						<table style="width:auto;margin:0 0 0 auto;">
							<tr>
								<td style="vertical-align:middle;">
									数量:<!--{$item.quantity}-->
								</td>
								<td style="vertical-align:middle;"><a href="?" onclick="fnFormModeSubmit('form<!--{$key}-->','down','cart_no','<!--{$item.cart_no}-->'); return false"><img src="<!--{$TPL_URLPATH}-->img/button/btn_minus.jpg" width="20" height="20" alt="-" style="margin:0 1.5em;" /></a>
								</td>
								<td style="vertical-align:middle;padding-right:1em;">
									<a href="?" onclick="fnFormModeSubmit('form<!--{$key}-->','up','cart_no','<!--{$item.cart_no}-->'); return false"><img src="<!--{$TPL_URLPATH}-->img/button/btn_plus.jpg" width="20" height="20" alt="＋" style="margin:0 1.5em 0 0;" /></a>
								</td>
								<td style="vertical-align:middle;">
			                        <a href="?" onclick="fnFormModeSubmit('form<!--{$key}-->', 'delete', 'cart_no', '<!--{$item.cart_no}-->'); return false;" class="btnGray02">削除</a>
			                    </td>
		                    </tr>
	                    </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="border-bottom: dotted 1px #7f7f7f; text-align:right;">
						<!--{assign var=sub_total value=$sub_total+$item.total_inctax}-->
						<div class="money"><!--{$item.total_inctax|number_format}-->円</div>
                    </td>
                </tr>
			<!--{/foreach}-->
			<!--{/foreach}-->
			</table>
			<!--{if $arrForm.brand_id.value}-->
			<p style="margin:10px 0;">
				<a href="javascript:;" onclick="fnModeSubmit('add', '', '');" class="btnGray">商品を追加</a>
				<input type="hidden" name="brand_id" value="<!--{$arrForm.brand_id.value}-->" id="brand_id" />
			</p>
			<!--{/if}-->
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
      <table summary="定期購入詳細" class="tblOrder delivname" style="width:100%;">
        <tr>
            <th>お支払方法</th>
        </tr>
        <tr>
            <td>
                <input type="hidden" name="payment_id" value="<!--{$arrForm.payment_id.value}-->" id="payment_id" />
                <!--{$arrPayment[$arrForm.payment_id.value]|h}-->
            </td>
        </tr>
    </table>
    <br />

    <table summary="定期購入用のお客様情報" class="tblOrder delivname" style="width:100%;">
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
        <p style="margin:10px auto;">
            <a href="javascript:void(0);" onclick="fnSetVal('mode', 'confirm'); document.form1.submit();return false;" class="btnBlue">確認</a>
        </p>
    <div class="wrapCoan">
        <div class="orderBtn">
            <p class="left"><a href="./regular.php" class="btnGray03">一覧へ戻る</a></p>
        </div>
    </div>
    </form>
