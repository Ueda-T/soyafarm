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

/**
 * 商品を追加する
 *
 */
function doAppendProduct(product_id, product_class_id, min, max, price, product_name) {

	document.form1['add_product_class_id'].value = product_class_id;
	document.form1['mode'].value = 'add_products';
	document.form1.submit();

}
    

// 追加商品行の削除
<!--{*
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

        min = parseInt($('#before_sale_minimum_number').val());
        max = parseInt($('#before_sale_limit').val());

        // 数量再設定
        $("#after_quantity").children().remove();
        for (i = min; i <= max; i++) {
            $("#after_quantity").append($("<option>").html(i).val(i));
        }

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
*}-->

// 商品選択のダイアログ
/**
 * 商品検索ダイアログを表示します
 */
function openDialogSearchProducts(brand_id, dialogMode) {

    // デフォルトのダイアログパラメータをコピーする
    var params = $.extend(true, {}, defaultParams);

    // 商品検索ダイアログ向けにパラメータを設定
    url = "<!--{$smarty.const.ROOT_URLPATH}-->dialog/search_mypage_products.php";
    params.url = url;
    params.width = 590;
    params.height = 520;
    // 単一選択モードに設定
    params.isSingleSelect = "1";
    // 自動検索ON
    params.autoSearch = "1";
    // ブランドID
    params.brand_id = brand_id;
    // ダイアログのタイトル
    params.dialogTitle = dialogMode;

    openDialog("dialogMypageProducts", params, function(dialogId, data) {
        // ダイアログで選択した後の処理

        // 「|」区切りの文字列から配列を作成
        //   0番目: 商品ID
        //   1番目: 商品規格ID
        //   2番目: 商品名
        //   3番目: 商品価格
        //   4番目: 購入制限数
        //   5番目: 最低購入数
        var row = data['product'].split("|");
        productId      = row[0];
        productClassId = row[1];
        productName    = row[2];
        price          = row[3];
        max            = row[4];
        min            = row[5];

        // 既に選択中の商品はフォームに反映しない
		<!--{*
        if (checkExistsProduct(productClassId)) {
            alert("既に選択中の商品です。");
            return;
        }
		*}-->

<!--{*
        if (dialogMode == 'edit') {
            // 商品変更時
             
            // 変更前商品情報にデータをセット
            $("#after_product_id").val(productId);
            $("#after_product_class_id").val(productClassId);
            $("#after_product_name").val(productName);
            $("#after_product_name_str").val(productName);

            // 削除リンク表示
            $("#after_product_delete").css("display", "");

            // 数量再設定
            $("#after_quantity").children().remove();
            for (i = min; i <= max; i++) {
                $("#after_quantity").append($("<option>").html(i).val(i));
            }

        }
        else if (dialogMode == 'add') {
*}-->
            // 商品追加時

            // 追加商品欄に行を追加する
            doAppendProduct(productId, productClassId, min, max, price, productName);
		<!--{*
        //}
		*}-->
    });
    return false;
}

/**
 * 商品選択時の重複チェック
 *
 */
function checkExistsProduct(addProductClassId) {
    // 変更前商品との重複チェック
    beforeProductClassId = $('#before_product_class_id').val();
    if (addProductClassId == beforeProductClassId) {
        return true;
    }

    // 変更後商品との重複チェック
    afterProductClassId = $('#after_product_class_id').val();
    if (addProductClassId == afterProductClassId) {
        return true;
    }

    var row_cnt = parseInt($('#target_product_list_cnt').val());
    // 追加商品との重複チェック
    for (i = 0; i < row_cnt; i = i + 1){
        appendProductClassId = $('#product_class_id_' + i).val();
        if (addProductClassId == appendProductClassId) {
            return true;
        }
    }
    return false;
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

<h1><img src="<!--{$TPL_URLPATH}-->img/rohto/regular_change_title.gif" alt="定期変更手続き" /></h1>

<div class="wrapCoan">
	<p>変更したい箇所を修正いただき、「変更確認ページへ進む」ボタンをクリックしてください。</p>
</div>

	<form name="form1" method="post" action="?">
	<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
	<input type="hidden" name="cart_no" value="" />
	<input type="hidden" name="cartKey" value="1" />
	<input type="hidden" name="mode" value="confirm" />
	<input type="hidden" name="brand_id" value="<!--{$arrForm.brand_id.value}-->" id="brand_id" />
	<input type="hidden" name="regular_id" value="<!--{$arrForm.regular_id.value}-->" />
	<input type="hidden" name="line_no" value="<!--{$arrForm.line_no.value}-->" />
	<input type="hidden" name="pageno" value="<!--{$objNavi->nowpage}-->" />
	
    <input type="hidden" name="todoke_day" value="<!--{$arrForm.todoke_day.value}-->" />
    <input type="hidden" name="status" value="<!--{$arrForm.status.value}-->" />
    <input type="hidden" name="cancel_date" value="<!--{$arrForm.cancel_date.value}-->" />
    <input type="hidden" name="cancel_reason_cd" value="<!--{$arrForm.cancel_reason_cd.value}-->" />
    <input type="hidden" name="deliv_date_id" value="<!--{$arrForm.deliv_date_id.value}-->" />

    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    
    <input type="hidden" name="anchor_key" value="" />
    <input type="hidden" id="add_product_id" name="add_product_id" value="" />
    <input type="hidden" id="add_product_class_id" name="add_product_class_id" value="" />
    <input type="hidden" id="edit_product_id" name="edit_product_id" value="" />
    <input type="hidden" id="edit_product_class_id" name="edit_product_class_id" value="" />
    <input type="hidden" id="no" name="no" value="" />
    <input type="hidden" id="delete_no" name="delete_no" value="" />

    <!--{foreach key=key item=item from=$arrSearchHidden}-->
        <!--{if is_array($item)}-->
            <!--{foreach item=c_item from=$item}-->
            <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
            <!--{/foreach}-->
        <!--{else}-->
            <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
        <!--{/if}-->
    <!--{/foreach}-->

<div class="wrapCoan">
	<h2 style="margin:30px 0 20px 0;"><img src="<!--{$TPL_URLPATH}-->img/rohto/method_midashi_r05.gif" width="820" height="35" alt="お届け間隔"></h2>

    <!--{* ▼お届け日情報 *}-->
	<table summary="定期購入一覧" style="margin-top:15px;" class="tblOrder">
        <colgroup width="20%"></colgroup>
        <colgroup width="80%"></colgroup>
		<tr>
			<th><span>次回お届け日</span></th>
			<td>
                <!--{assign var="mode" value="change_date"}-->
                <span class="attention"><!--{$arrErr.next_arrival_date}--></span>
                <input type="text" name="next_arrival_date" value="<!--{$arrForm.next_arrival_date.value|h}-->" onChange="fnModeSubmit('<!--{$mode}-->', '', '');" maxlength="10" style="<!--{if $arrErr.next_arrival_date != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="12" class="calendar" />
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
</div>
<!--{* ▲お届け日情報 *}-->

<div class="cartList">
<p id="container">
	<h2 style="margin:30px 0 20px 0;"><img src="<!--{$TPL_URLPATH}-->img/rohto/method_midashi_r04.gif" width="820" height="35" alt="定期購入変更商品" /></h2>

	<!--{if $tpl_message}-->
	<p class="error"><!--{$tpl_message|h|nl2br}--></p>
	<!--{/if}-->

	<table class="list regularItemList">
		<tr>
			<th class="alignC">商品名</th>
			<th class="num">数量</th>
			<th class="price" nowrap>小計<span class="dyn">(税込)</span></th>
			<th class="deleteBtn">取消</th>
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
				<ul id="quantity_level">
					<li><a href="?" onclick="fnFormModeSubmit('form<!--{$key}-->','down','cart_no','<!--{$item.cart_no}-->'); return false"><img src="<!--{$TPL_URLPATH}-->img/button/btn_minus.jpg" width="16" height="16" alt="-" /></a></li>
					<li><a href="?" onclick="fnFormModeSubmit('form<!--{$key}-->','up','cart_no','<!--{$item.cart_no}-->'); return false"><img src="<!--{$TPL_URLPATH}-->img/button/btn_plus.jpg" width="16" height="16" alt="＋" /></a></li>
				</ul>
			</td>
			<td class="alignR">
			<!--{assign var=sub_total value=$sub_total+$item.total_inctax}-->
			<!--{$item.total_inctax|number_format}-->円
			</td>
			<td class="alignC"><a href="?" onclick="fnFormModeSubmit('form<!--{$key}-->', 'delete', 'cart_no', '<!--{$item.cart_no}-->'); return false;"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/delete.gif" alt="取消" /></a>
			</td>
		</tr>
		<!--{/foreach}-->
		<!--{/foreach}-->
	</table>

    <!--{* ▼追加商品 *}-->
    <!--{if $arrForm.brand_id.value}-->
        <p class="regularItemAddBtn">
            <a class="btn-normal" href="javascript:;" onclick="openDialogSearchProducts('<!--{$arrForm.brand_id.value}-->', 'add');"><img src="<!--{$TPL_URLPATH}-->img/rohto/regular_change_btn_add.gif" alt="商品の追加" class="swp" /></a>
        </p>
    <!--{/if}-->

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

<div class="wrapCoan">
	<h2 style="margin:30px 0 20px 0;"><img src="<!--{$TPL_URLPATH}-->img/rohto/method_midashi05.gif" width="820" height="35" alt="お支払方法"></h2>
    <!--{* ▼お届け先情報 *}-->
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
    </table>
</div>

<div style="width: 820px;margin: 30px 70px;">
	<div style="background:#DEF1FA; padding:20px; margin-bottom:20px;">
		<h2 style="font-size:1.4em; color:#3e8dd5; font-weight:bold;">定期購入用のお客様情報</h2>
		<p class="naked">以下のご登録情報は、マイページからご変更いただくことができません。<br>
		定期購入専用ダイヤルまたは <a href="<!--{$smarty.const.ROOT_URLPATH}-->contact/" class="link">お問い合わせフォーム</a>をご利用ください。</p>

		<div class="wrapForm">
			<table cellspacing="0" class="typ2">
				<tr>
					<th rowspan="2">【1】</th>
					<th rowspan="2">お届け先情報</th>
					<th><span>お届け先住所</span></th>
					<td>
		                <input type="hidden" name="order_name" value="<!--{$arrForm.order_name.value}-->" id="order_name" />
		                <input type="hidden" name="order_zip" value="<!--{$arrForm.order_zip.value}-->" id="order_zip" />
		                <input type="hidden" name="order_pref" value="<!--{$arrForm.order_pref.value}-->" id="order_pref" />
		                <input type="hidden" name="order_addr01" value="<!--{$arrForm.order_addr01.value}-->" id="order_addr01" />
		                <input type="hidden" name="order_addr02" value="<!--{$arrForm.order_addr02.value}-->" id="order_addr02" />
		                <input type="hidden" name="order_tel" value="<!--{$arrForm.order_tel.value}-->" id="order_tel" />
						〒<!--{$arrForm.order_zip.value|h}-->
						<!--{$arrPref[$arrForm.order_pref.value]|h}--><!--{$arrForm.order_addr01.value|h}--><!--{$arrForm.order_addr02.value|h}--><br />
						<!--{$arrForm.order_name.value|h}--> 様
						
					</td>
				</tr>
				<tr>
					<th>お届け先お電話番号</th>
					<td><!--{$arrForm.order_tel.value|h}--></td>
				</tr>
			</table>
		</div><!--／wrapForm-->
	</div>
</div>
<!--{* ▲お届け先情報 *}-->

<!--{* 確認ページへ進む *}-->
<div class="wrapCoan">
    <div class="orderBtn">
		<p class="left"><a href="./regular.php" ><img src="<!--{$TPL_URLPATH}-->img/rohto/btn_modoru_list.gif" alt="一覧へ戻る" class="swp" /></a></p>
		<span class="f-right" style="width:600px;float:right;text-align:right;">
            <a href="javascript:void(0);" onclick="document.form1.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/rohto/regular_change_btn03.gif" alt="確認ページへ進む" name="refusal" id="refusal" class="swp" /></a>
        </span>
    </div>
</div>
</form>
