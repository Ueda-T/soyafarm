<!--{* -*- coding: utf-8-unix; -*- *}-->
<script type="text/javascript">
<!--

// 表示非表示切り替え
function lfDispSwitch(id) {
    var obj = document.getElementById(id);
    if (obj.style.display == 'none') {
        obj.style.display = '';
    } else {
        obj.style.display = 'none';
    }
}

// セレクトボックスのリストを移動
// (移動元セレクトボックスID, 移動先セレクトボックスID)
function fnMoveSelect(select, target) {
    $('#' + select).children().each(function() {
        if (this.selected) {
            $('#' + target).append(this);
        }
    });
}

// target の子要素を選択状態にする
function selectAll(target) {
    $('#' + target).children().attr({selected: true});
}

/**
 * ブランドを設定します
 */
function setBrand(id, name) {
    $("#brand_id").val(id);
    $("#brand_name").text(name);
    $("#h_brand_name").val(name);
}

/**
 * ブランド検索ダイアログを表示します
 */
function openDialogSearchBrand() {
    // デフォルトのダイアログパラメータをコピーする
    var params = $.extend(true, {}, defaultParams);

    // カテゴリ検索ダイアログ向けにパラメータを設定
    url = "<!--{$smarty.const.ROOT_URLPATH}-->dialog/search_brand.php";
    params.url = url;
    params.width = 740;
    params.height = 490;

    openDialog("dialogBrand", params, function(dialogId, data) {
        // ブランドコードをセットして変更イベントを呼び出す
        $("#brand_code").val(data["selectedBrandCode"]);
        $("#brand_code").change();
    });

    return false;
}

/**
 * 初期設定
 */
$(function() {
    // スピンコントロールの設定
    $.spin.imageBasePath = '<!--{$TPL_URLPATH}-->img/spin1/';
    var options1 = {min: 0, interval: 10, timeInterval: 150};
    var options2 = {min: 0, timeInterval: 150};

    $('#spin1').spin(options1);
    $('#spin2').spin(options1);
    $('#spin3').spin(options2);
    $('#spin4').spin(options2);
    $('#spin5').spin(options2);
    $('#spin6').spin(options2);
    $('#spin7').spin({
        min: 0.00,
        max: 1.00,
        interval: 0.10,
        timeInterval: 150,
        changed: function() {
            // 小数点第2位まで表示
            var objNum = new Number($(this).val());
            $('#spin7').val(objNum.toFixed(2));
        }
    });
    $('#spin8').spin(options2);

    // 掲載開始日・販売開始・終了日カレンダー表示（datepicker）
	$(".calendar").datepicker();
    $(".calendar").datepicker("option", "showOn", 'both');
    $(".calendar").datepicker("option", "buttonImage",
                              '<!--{$TPL_URLPATH}-->img/common/calendar.png');
    $(".calendar").datepicker("option", "buttonImageOnly", true);

    // タブ表示（tabs）
    $("#ui-tab").tabs({
        beforeActivate: function(event, ui) {
            $("#select_tab_index").val(ui.newTab.index());
        },
        active: "<!--{$arrForm.select_tab_index}-->"
    });
    $("#ui-tab .ui-tabs-nav").removeClass('ui-corner-all');

    // ブランド名を検索して表示するコールバック関数
    var f_search_brand = function() {
        var data = new Object();

        data["brand_code"] = $("#brand_code").val();

        if (data["brand_code"] == "") {
            setBrand("", "");
            return;
        }

        $.ajax({
            "url": "<!--{$smarty.const.INPUT_BRAND_URLPATH}-->",
            "data": data,
            "success": function(data) {
                if (data == "") {
                    setBrand("", "");
                    return;
                }

                // JSON フォーマット文字をデコード
                var value = eval("(" + data + ")");

                setBrand(value["brand_id"], value["brand_name"]);
            },
            "error": function(data) {
                setBrand("", "");
            }
        });
    };

    // ブランド入力欄の変更時イベントを設定
    $("#brand_code").on("change", f_search_brand);

    // 定期購入 選択時コールバック関数
    var f_change_teiki_flg = function() {
        var value = $(this).val();
        if (value == "<!--{$smarty.const.REGULAR_PURCHASE_FLG_OFF}-->") {
            // なし の場合
            $("#course_cd").val("");
            $("#course_cd").change();
            $("#course_cd").attr("disabled", "disabled");
        } else {
            // あり の場合
            $("#course_cd").removeAttr("disabled");
        }
    };

    // 定期購入 変更時イベントを設定
    $("#span_teiki_flg").find("input[type='radio']").each(function() {
        $(this).on("change", f_change_teiki_flg);
    });

    // 定期購入 コースCD選択時コールバック関数
    var f_change_course_cd = function() {
        var value = $("#course_cd").val();
        if (value == "") {
            // 選択なし
            $("#todoke_kbn").val("");
            $("#h_todoke_kbn").val("");
        } else if (value >= <!--{$smarty.const.COURSE_CD_MONTH_MIN}--> &&
                   value <= <!--{$smarty.const.COURSE_CD_MONTH_MAX}-->) {
            // ヶ月ごとを選択状態にする
            $("#todoke_kbn").val("<!--{$smarty.const.TODOKE_CYCLE_MONTH}-->");
            $("#h_todoke_kbn").val("<!--{$smarty.const.TODOKE_CYCLE_MONTH}-->");
        } else {
            // 日ごとを選択状態にする
            $("#todoke_kbn").val("<!--{$smarty.const.TODOKE_CYCLE_DAY}-->");
            $("#h_todoke_kbn").val("<!--{$smarty.const.TODOKE_CYCLE_DAY}-->");
        }
    };

    // 定期購入 コースCD変更時イベントを設定
    $("#course_cd").on("change", f_change_course_cd);
    // イベント実行
    $("#course_cd").change();
});

// -->
</script>

<!--{section name=cnt loop=$arrErr}-->
<span class="attention"><!--{$arrErr[cnt]}--></span>
<!--{/section}-->

<form name="form1" id="form1" method="post" action="?" enctype="multipart/form-data">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<!--{foreach key=key item=item from=$arrSearchHidden}-->
    <!--{if is_array($item)}-->
        <!--{foreach item=c_item from=$item}-->
        <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
        <!--{/foreach}-->
    <!--{else}-->
        <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
    <!--{/if}-->
<!--{/foreach}-->
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="image_key" value="" />
<input type="hidden" name="down_key" value="">
<input type="hidden" name="product_id" value="<!--{$arrForm.product_id|h}-->" />
<input type="hidden" name="product_class_id" value="<!--{$arrForm.product_class_id|h}-->" />
<input type="hidden" name="anchor_key" value="" />
<input type="hidden" name="select_recommend_no" value="" />
<input type="hidden" name="has_product_class" value="<!--{$arrForm.has_product_class|h}-->" />
<input type="hidden" id="h_brand_name" name="brand_name" value="<!--{$arrForm.brand_name|h}-->" />
<input type="hidden" name="product_type_id" value="<!--{$smarty.const.PRODUCT_TYPE_NORMAL}-->" />
<input type="hidden" id="brand_id" name="brand_id" value="<!--{$arrForm.brand_id|h}-->" />
<input type="hidden" name="point_rate" value="0" />
<input type="hidden" id="select_tab_index" name="select_tab_index" value="<!--{$arrForm.select_tab_index|h}-->" />
<input type="hidden" id="h_todoke_kbn" name="todoke_kbn" value="<!--{$arrForm.todoke_kbn|h}-->" />
<!--{foreach key=key item=item from=$arrForm.arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
<!--{/foreach}-->
<div id="products" class="contents-main">

    <!--{if $arrForm.arrHidden|@count > 0 || $smarty.post.product_id|escape}-->
    <div class="btn-area-head">
        <!--▼検索結果へ戻る-->
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_PRODUCTS_URLPATH}-->'); fnModeSubmit('search','',''); return false;"><span class="btn-prev">検索画面に戻る</span></a></li>
        <!--▲検索結果へ戻る-->
        </ul>
    </div>
    <!--{/if}-->

    <h2>基本情報</h2>
    <table class="form">
        <tr>
            <th>生成URL</th>
            <td>
            <!--{if $arrForm.product_id}-->
            <a href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrForm.product_id|h}-->" target="_blank" ><!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/products/detail.php?product_id=<!--{$arrForm.product_id|h}--></a><br />
            <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>商品ID</th>
            <td><!--{$arrForm.product_id|h}--></td>
        </tr>
        <tr>
            <th>商品名<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.name}--></span>
                <input type="text" name="name" value="<!--{$arrForm.name|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="60" class="box60" />
                <span class="attention"> (上限<!--{$smarty.const.PRODUCT_NAME_BYTE_LEN}-->バイト)</span>
            </td>
        </tr>

        <tr>
            <th>表示用商品名</th>
            <td>
                <span class="attention"><!--{$arrErr.disp_name}--></span>
                <input type="text" name="disp_name" value="<!--{$arrForm.disp_name|h}-->" maxlength="<!--{$smarty.const.DISP_NAME_LEN}-->" style="<!--{if $arrErr.disp_name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="100" class="box100" /><span class="attention"> (上限<!--{$smarty.const.DISP_NAME_LEN}-->文字)</span>
            </td>
        </tr>

        <!--{if $arrForm.has_product_class == false}-->
        <tr>
            <th>商品コード<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.product_code}--></span>
                <input type="text" name="product_code" value="<!--{$arrForm.product_code|h}-->" maxlength="<!--{$smarty.const.PRODUCT_CODE_LEN}-->" style="<!--{if $arrErr.product_code != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="10" class="box10" />
            </td>
        </tr>
        <!--{/if}-->

        <tr>
            <th>商品カテゴリ<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.category_id}--></span>
                <table class="layout">
                    <tr>
                        <td>
                            <select name="category_id[]" id="category_id" style="<!--{if $arrErr.category_id != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}--> height: 120px; min-width: 200px;" onchange="" size="10" multiple>
                                <!--{html_options options=$arrSelCat}-->
                            </select>
                        </td>
                        <td style="padding: 15px;">
                            <a class="btn-normal" href="javascript:;" name="on_select" onclick="fnMoveSelect('category_id_unselect','category_id'); return false;">&nbsp;&nbsp;&lt;-&nbsp;登録&nbsp;&nbsp;</a><br /><br />
                            <a class="btn-normal" href="javascript:;" name="un_select" onclick="fnMoveSelect('category_id','category_id_unselect'); return false;">&nbsp;&nbsp;削除&nbsp;-&gt;&nbsp;&nbsp;</a>
                        </td>
                        <td>
                            <select name="category_id_unselect[]" id="category_id_unselect" onchange="" size="10" style="height: 120px; min-width: 200px;" multiple>
                                <!--{html_options options=$arrNonCat}-->
                            </select>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <th>公開・非公開<span class="attention"> *</span></th>
            <td>
                <!--{html_radios name="status" options=$arrDISP selected=$arrForm.status separator='&nbsp;&nbsp;'}-->
            </td>
        </tr>
        <tr>
            <th>掲載開始日</th>
            <td>
                <span class="attention"><!--{$arrErr.disp_start_date}--></span>
                <input type="text" name="disp_start_date" value="<!--{$arrForm.disp_start_date|h}-->" size="10" class="calendar" style="<!--{if $arrErr.disp_start_date != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" />
            </td>
        </tr>
        <tr>
            <th>販売期間</th>
            <td>
                <span class="attention"><!--{$arrErr.sale_start_date}--></span>
                <input type="text" name="sale_start_date" value="<!--{$arrForm.sale_start_date|h}-->" size="10" class="calendar" style="<!--{if $arrErr.sale_start_date != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" />～
                <span class="attention"><!--{$arrErr.sale_end_date}--></span>
                <input type="text" name="sale_end_date" value="<!--{$arrForm.sale_end_date|h}-->" size="10" class="calendar" style="<!--{if $arrErr.sale_end_date != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" />
            </td>
        </tr>
        <tr>
            <th>ブランド</th>
            <td>
                <span class="attention"><!--{$arrErr.parent_id}--></span>
                <input type="text" id="brand_code" name="brand_code" value="<!--{$arrForm.brand_code|h}-->" maxlength="10" style="<!--{if $arrErr.brand_code != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="10" class="box10" />
				<img id="search_brand" src="<!--{$TPL_URLPATH}-->img/common/btn_search.gif" style="cursor: pointer; vertical-align: middle;" onclick="openDialogSearchBrand();" />&nbsp;&nbsp;<span id="brand_name"><!--{$arrForm.brand_name|h}--></span>
			</td>
        </tr>
        <tr>
            <th>販売名</th>
            <td>
                <span class="attention"><!--{$arrErr.sales_name}--></span>
                <input type="text" name="sales_name" value="<!--{$arrForm.sales_name|h}-->" maxlength="<!--{$smarty.const.SALES_NAME_LEN}-->" style="<!--{if $arrErr.sales_name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="100" class="box100" />
                <span class="attention"> (上限<!--{$smarty.const.SALES_NAME_LEN}-->文字)</span>
            </td>
        </tr>
        <tr>
            <th>産直区分</th>
            <td><!--{html_radios name="drop_shipment" options=$arrSANTYOKU selected=$arrForm.drop_shipment separator='&nbsp;&nbsp;'}--></td>
        </tr>
        <tr>
            <th>配送区分1</th>
            <td><!--{html_radios name="deliv_kbn1" options=$arrHAISOKBN_1 selected=$arrForm.deliv_kbn1 separator='&nbsp;&nbsp;'}--></td>
        </tr>
        <tr>
            <th>配送区分2</th>
            <td><!--{html_radios name="deliv_kbn2" options=$arrHAISOKBN_2 selected=$arrForm.deliv_kbn2 separator='&nbsp;&nbsp;'}--></td>
        </tr>
        <tr>
            <th>メール便業者</th>
            <td><!--{html_radios name="mail_deliv_id" options=$arrMAILDELIV selected=$arrForm.mail_deliv_id separator='&nbsp;&nbsp;'}--></td>
        </tr>
        <tr>
            <th>成分表示</th>
            <td>
                <span class="attention"><!--{$arrErr.component_flg}--></span>
                <!--{html_radios name="component_flg" options=$arrCOMPONENT_FLG selected=$arrForm.component_flg separator='&nbsp;&nbsp;'}--></td>
        </tr>
        <tr>
            <th>検索除外</th>
            <td>
				<span class="attention"><!--{$arrErr.not_search_flg}--></span>
				<!--{html_radios name="not_search_flg" options=$arrNOT_SEARCH_FLG selected=$arrForm.not_search_flg separator='&nbsp;&nbsp;'}--></td>
        </tr>
        <tr>
            <th>定期購入</th>
            <td>
                <span class="attention"><!--{$arrErr.teiki_flg}--> <!--{$arrErr.course_cd}--></span>
                <span id="span_teiki_flg">
				<!--{html_radios name="teiki_flg" options=$arrTEIKI_FLG selected=$arrForm.teiki_flg separator='&nbsp;&nbsp;'}-->
                </span>
				（
                <select id="course_cd" name="course_cd" style="<!--{$arrErr.course_cd|sfGetErrorColor}-->" <!--{if $arrForm.teiki_flg == '0'}-->disabled<!--{/if}-->>
                    <option value=""></option>
                    <!--{html_options options=$arrCourseCd selected=$arrForm.course_cd}-->
                </select>
                <select id="todoke_kbn" name="select_todoke_kbn" style="<!--{$arrErr.todoke_kbn|sfGetErrorColor}-->" disabled>
                    <option value=""></option>
                    <!--{html_options options=$arrTodokeKbn selected=$arrForm.todoke_kbn}-->
                </select>
				）
			</td>
        </tr>
        <tr>
            <th>サンプル区分</th>
            <td>
				<span class="attention"><!--{$arrErr.sample_flg}--></span>
				<!--{html_radios name="sample_flg" options=$arrSAMPLE_FLG selected=$arrForm.sample_flg separator='&nbsp;&nbsp;'}--></td>
        </tr>
        <tr>
            <th>プレゼント区分</th>
            <td>
				<span class="attention"><!--{$arrErr.present_flg}--></span>
				<!--{html_radios name="present_flg" options=$arrPRESENT_FLG selected=$arrForm.present_flg separator='&nbsp;&nbsp;'}--></td>
        </tr>
        <tr>
            <th>販売対象フラグ</th>
            <td>
				<span class="attention"><!--{$arrErr.sell_flg}--></span>
				<!--{html_radios name="sell_flg" options=$arrSELL_FLG selected=$arrForm.sell_flg separator='&nbsp;&nbsp;'}--></td>
        </tr>
        <tr>
            <th>社員購入グループ</th>
            <td>
                <span class="attention"><!--{$arrErr.employee_sale_cd}--></span>
                <select name="employee_sale_cd" style="<!--{$arrErr.employee_sale_cd|sfGetErrorColor}-->">
                    <option value="">選択してください</option>
                    <!--{html_options options=$arrEMPLOYEE_CD_NAME selected=$arrForm.employee_sale_cd}-->
                </select>
            </td>
        </tr>

        <!--{if $arrForm.has_product_class == false}-->
        <tr>
            <th><!--{$smarty.const.NORMAL_PRICE_TITLE}-->(税込)<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.price01}--></span>
                <input type="text" id="spin1" name="price01" value="<!--{$arrForm.price01|h}-->" size="6" class="box6" maxlength="<!--{$smarty.const.PRICE_LEN}-->" style="<!--{if $arrErr.price01 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->"/>円
                <span class="attention"> (半角数字で入力)</span>
            </td>
        </tr>
        <tr>
            <th><!--{$smarty.const.SALE_PRICE_TITLE}-->(税込)<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.price02}--></span>
                <input type="text" id="spin2" name="price02" value="<!--{$arrForm.price02|h}-->" size="6" class="box6" maxlength="<!--{$smarty.const.PRICE_LEN}-->" style="<!--{if $arrErr.price02 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->"/>円
                <span class="attention"> (半角数字で入力)</span>
            </td>
        </tr>
        <tr>
            <th>在庫数<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.stock}--></span>
                <input type="text" id="spin3" name="stock" value="<!--{$arrForm.stock|h}-->" size="6" class="box6" maxlength="<!--{$smarty.const.AMOUNT_LEN}-->" style="<!--{if $arrErr.stock != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->"/>
                <input type="checkbox" name="stock_unlimited" value="1" <!--{if $arrForm.stock_unlimited == "1"}-->checked<!--{/if}--> onclick="fnCheckStockLimit('<!--{$smarty.const.DISABLED_RGB}-->');"/>無制限
            </td>
        </tr>
        <!--{/if}-->

        <tr>
            <th>在庫切れ時の表示文言</th>
            <td>
                <span class="attention"><!--{$arrErr.stock_status_name}--></span>
                <input type="text" name="stock_status_name" value="<!--{$arrForm.stock_status_name|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.stock_status_name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="60" class="box60" />
                <span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span>
            </td>
        </tr>
        <tr>
            <th>購入制限<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.sale_minimum_number}--><!--{$arrErr.sale_limit}--></span>
                <input type="text" id="spin8" name="sale_minimum_number" value="<!--{$arrForm.sale_minimum_number|h}-->" size="6" class="box6" maxlength="<!--{$smarty.const.AMOUNT_LEN}-->" style="<!--{if $arrErr.sale_minimum_number != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" /> ～ <input type="text" id="spin5" name="sale_limit" value="<!--{$arrForm.sale_limit|h}-->" size="6" class="box6" maxlength="<!--{$smarty.const.AMOUNT_LEN}-->" style="<!--{if $arrErr.sale_limit != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->"/>
                <span class="attention"> (半角数字で入力)</span>
            </td>
        </tr>
        <tr>
            <th>メール便計算個数</th>
            <td>
                <span class="attention"><!--{$arrErr.deliv_judgment}--></span>
                <input type="text" id="spin7" name="deliv_judgment" value="<!--{if ($arrForm.deliv_judgment) > 1 || ($arrForm.deliv_judgment) < 0}--><!--{$arrForm.deliv_judgment|h}--><!--{else}--><!--{$arrForm.deliv_judgment|number_format:"2"|h}--><!--{/if}-->" size="5" class="box5" maxlength="<!--{$smarty.const.DELIV_JUDGMENT_LEN}-->" style="<!--{if $arrErr.deliv_judgment != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->"/>
                <span class="attention"> (半角数字で入力) </span>
            </td>
        </tr>
        <tr>
            <th>発送日目安<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.deliv_date_id}--></span>
                <select name="deliv_date_id" style="<!--{$arrErr.deliv_date_id|sfGetErrorColor}-->">
                    <option value="">選択してください</option>
                    <!--{html_options options=$arrDELIVERYDATE selected=$arrForm.deliv_date_id}-->
                </select>
            </td>
        </tr>
        <tr>
            <th>容量</th>
            <td>
                <span class="attention"><!--{$arrErr.capacity}--></span>
                <input type="text" name="capacity" value="<!--{$arrForm.capacity|h}-->" maxlength="<!--{$smarty.const.CAPACITY_LEN}-->" style="<!--{if $arrErr.capacity != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="80" class="box80" />
                <span class="attention"> (上限<!--{$smarty.const.CAPACITY_LEN}-->文字)</span>
            </td>
        </tr>
        <tr>
            <th>検索ワード<br /><span class="attention">※複数の場合は、カンマ( , )区切りで入力して下さい</span></th>
            <td>
                <span class="attention"><!--{$arrErr.comment3}--></span>
                <textarea name="comment3" cols="60" rows="8" class="area60" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr.comment3|sfGetErrorColor}-->"><!--{$arrForm.comment3|h}--></textarea><br />
                <span class="attention"> (上限<!--{$smarty.const.LLTEXT_LEN}-->文字)</span>
            </td>
        </tr>
        <tr>
            <th>METAタグ</th>
            <td>
	        <span class="attention"><!--{$arrErr.metatag}--></span>
                <textarea name="metatag" cols="60" rows="8" class="area60" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr.metatag|sfGetErrorColor}-->"><!--{$arrForm.metatag|h}--></textarea>
                <span class="attention"> (上限<!--{$smarty.const.LLTEXT_LEN}-->文字)</span><br />
		<span class="attention">(記載がない場合は、テンプレートに書かれた共通用のMETAタグが読み込まれます)</span>
            </td>
        </tr>
		<tr>
			<th>一覧コメント<br /><span class="attention">(タグ許可)</span></th>
			<td>
				<span class="attention"><!--{$arrErr.main_list_comment}--></span>
				<textarea class="ckeditor" name="main_list_comment" maxlength="<!--{$smarty.const.MLTEXT_LEN}-->" style="<!--{if $arrErr.main_list_comment != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" cols="50" rows="8" class="area50"><!--{$arrForm.main_list_comment}--></textarea><br />
				<span class="attention"> (上限<!--{$smarty.const.MLTEXT_LEN}-->文字)</span>
			</td>
		</tr>
		<tr>
			<!--{assign var=key value="main_list_image"}-->
			<th>一覧画像<br />[<!--{$smarty.const.SMALL_IMAGE_WIDTH}-->×<!--{$smarty.const.SMALL_IMAGE_HEIGHT}-->]</th>
			<td>
				<a name="<!--{$key}-->"></a>
				<a name="main_image"></a>
				<a name="main_large_image"></a>
				<span class="attention"><!--{$arrErr[$key]}--></span>
				<!--{if $arrForm.arrFile[$key].filepath != ""}-->
				<img src="<!--{$arrForm.arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" />　<a href="" onclick="selectAll('category_id'); fnModeSubmit('delete_image', 'image_key', '<!--{$key}-->'); return false;">[画像の取り消し]</a><br />
				<!--{/if}-->
				<input type="file" name="main_list_image" size="40" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
				<a class="btn-normal" href="javascript:;" name="btn" onclick="selectAll('category_id'); fnModeSubmit('upload_image', 'image_key', '<!--{$key}-->'); return false;">アップロード</a>
			</td>
		</tr>
		<tr>
			<th>詳細コメント<br /><span class="attention">(タグ許可)</span></th>
			<td>
				<span class="attention"><!--{$arrErr.main_comment}--></span>
				<textarea class="ckeditor" name="main_comment" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{if $arrErr.main_comment != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" cols="50" rows="8" class="area50"><!--{$arrForm.main_comment}--></textarea><br />
				<span class="attention"> (上限<!--{$smarty.const.LLTEXT_LEN}-->文字)</span>
			</td>
		</tr>
		<tr>
			<!--{assign var=key value="main_image"}-->
			<th>詳細画像<br />[<!--{$smarty.const.NORMAL_IMAGE_WIDTH}-->×<!--{$smarty.const.NORMAL_IMAGE_HEIGHT}-->]</th>
			<td>
				<span class="attention"><!--{$arrErr[$key]}--></span>
				<!--{if $arrForm.arrFile[$key].filepath != ""}-->
				<img src="<!--{$arrForm.arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" />　<a href="" onclick="selectAll('category_id'); fnModeSubmit('delete_image', 'image_key', '<!--{$key}-->'); return false;">[画像の取り消し]</a><br />
				<!--{/if}-->
				<input type="file" name="main_image" size="40" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
				<a class="btn-normal" href="javascript:;" name="btn" onclick="selectAll('category_id'); fnModeSubmit('upload_image', 'image_key', '<!--{$key}-->'); return false;">アップロード</a>
			</td>
		</tr>
		<tr>
			<!--{assign var=key value="main_large_image"}-->
			<th>詳細拡大画像<br />[<!--{$smarty.const.LARGE_IMAGE_WIDTH}-->×<!--{$smarty.const.LARGE_IMAGE_HEIGHT}-->]</th>
			<td>
				<span class="attention"><!--{$arrErr[$key]}--></span>
				<!--{if $arrForm.arrFile[$key].filepath != ""}-->
				<img src="<!--{$arrForm.arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" />　<a href="" onclick="selectAll('category_id'); fnModeSubmit('delete_image', 'image_key', '<!--{$key}-->'); return false;">[画像の取り消し]</a><br />
				<!--{/if}-->
				<input type="file" name="<!--{$key}-->" size="40" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
				<a class="btn-normal" href="javascript:;" name="btn" onclick="selectAll('category_id'); fnModeSubmit('upload_image', 'image_key', '<!--{$key}-->'); return false;">アップロード</a>
			</td>
		</tr>
		<tr>
			<!--{assign var=key value="guide_image"}-->
			<th>カート案内画像<br />[<!--{$smarty.const.GUIDE_IMAGE_WIDTH}-->×<!--{$smarty.const.GUIDE_IMAGE_HEIGHT}-->]</th>
			<td>
				<a name="<!--{$key}-->"></a>
				<a name="main_list_image"></a>
				<a name="main_image"></a>
				<a name="main_large_image"></a>
				<span class="attention"><!--{$arrErr[$key]}--></span>
				<!--{if $arrForm.arrFile[$key].filepath != ""}-->
				<img width="100%" src="<!--{$arrForm.arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" />　<a href="" onclick="selectAll('category_id'); fnModeSubmit('delete_image', 'image_key', '<!--{$key}-->'); return false;">[画像の取り消し]</a><br />
				<!--{/if}-->
				<input type="file" name="guide_image" size="40" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
				<a class="btn-normal" href="javascript:;" name="btn" onclick="selectAll('category_id'); fnModeSubmit('upload_image', 'image_key', '<!--{$key}-->'); return false;">アップロード</a>
			</td>
		</tr>
		<tr>
			<!--{assign var=key value="guide_image_teiki"}-->
			<th>カート案内画像（定期）<br />[<!--{$smarty.const.GUIDE_IMAGE_WIDTH}-->×<!--{$smarty.const.GUIDE_IMAGE_HEIGHT}-->]</th>
			<td>
				<a name="<!--{$key}-->"></a>
				<a name="main_list_image"></a>
				<a name="main_image"></a>
				<a name="main_large_image"></a>
				<span class="attention"><!--{$arrErr[$key]}--></span>
				<!--{if $arrForm.arrFile[$key].filepath != ""}-->
				<img width="100%" src="<!--{$arrForm.arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" />　<a href="" onclick="selectAll('category_id'); fnModeSubmit('delete_image', 'image_key', '<!--{$key}-->'); return false;">[画像の取り消し]</a><br />
				<!--{/if}-->
				<input type="file" name="guide_image_teiki" size="40" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
				<a class="btn-normal" href="javascript:;" name="btn" onclick="selectAll('category_id'); fnModeSubmit('upload_image', 'image_key', '<!--{$key}-->'); return false;">アップロード</a>
			</td>
		</tr>
    </table>

    <!--{* タブを作成 *}-->
	<div id="ui-tab" style="margin-bottom: 20px;">
	    <ul>
			<li><a href="#pc-tab"><span>PCサイト用</span></a></li>
			<li><a href="#sp-tab"><span>スマホサイト用</span></a></li>
			<li><a href="#mb-tab"><span>モバイルサイト用</span></a></li>
		</ul>

		<div id="pc-tab">
            <table class="form">
			<tr>
				<th>アイコン</th>
				<td>
					<ul class="prdIconList clearfix">
						<li><!--{html_checkboxes name="pc_product_status" options=$arrSTATUS selected=$arrForm.pc_product_status separator='</li><li>'}--></li>
					</ul>
				</td>
			</tr>
			<tr>
				<th>コメント１<span class="attention">(タグ許可)</span></th>
				<td>
					<span class="attention"><!--{$arrErr.pc_comment1}--></span>
					<textarea class="ckeditor" name="pc_comment1" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{if $arrErr.pc_comment1 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" cols="60" rows="8" class="area60"><!--{$arrForm.pc_comment1}--></textarea><br />
					<span class="attention"> (上限<!--{$smarty.const.LLTEXT_LEN}-->文字)</span>
				</td>
			</tr>
			<tr>
				<th>コメント２<span class="attention">(タグ許可)</span></th>
				<td>
					<span class="attention"><!--{$arrErr.pc_comment2}--></span>
					<textarea class="ckeditor" name="pc_comment2" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{if $arrErr.pc_comment2 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" cols="60" rows="8" class="area60"><!--{$arrForm.pc_comment2}--></textarea><br />
					<span class="attention"> (上限<!--{$smarty.const.LLTEXT_LEN}-->文字)</span>
				</td>
			</tr>
			<tr>
				<th>コメント３<span class="attention">(タグ許可)</span></th>
				<td>
					<span class="attention"><!--{$arrErr.pc_comment3}--></span>
					<textarea class="ckeditor" name="pc_comment3" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{if $arrErr.pc_comment3 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" cols="60" rows="8" class="area60"><!--{$arrForm.pc_comment3}--></textarea><br />
					<span class="attention"> (上限<!--{$smarty.const.LLTEXT_LEN}-->文字)</span>
				</td>
			</tr>
			<tr>
				<th>コメント４<span class="attention">(タグ許可)</span></th>
				<td>
					<span class="attention"><!--{$arrErr.pc_comment4}--></span>
					<textarea class="ckeditor" name="pc_comment4" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{if $arrErr.pc_comment4 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" cols="60" rows="8" class="area60"><!--{$arrForm.pc_comment4}--></textarea><br />
					<span class="attention"> (上限<!--{$smarty.const.LLTEXT_LEN}-->文字)</span>
				</td>
			</tr>
			<tr>
				<th>カートボタン表示４</th>
				<td>
					<span class="attention"><!--{$arrErr.pc_button4}--></span>
					<!--{html_radios name="pc_button4" options=$arrCART_BTN_FLG selected=$arrForm.pc_button4 separator='&nbsp;&nbsp;'}-->
				</td>
			</tr>
			<tr>
				<th>コメント５<span class="attention">(タグ許可)</span></th>
				<td>
					<span class="attention"><!--{$arrErr.pc_comment5}--></span>
					<textarea class="ckeditor" name="pc_comment5" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{if $arrErr.pc_comment5 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" cols="60" rows="8" class="area60"><!--{$arrForm.pc_comment5}--></textarea><br />
					<span class="attention"> (上限<!--{$smarty.const.LLTEXT_LEN}-->文字)</span>
				</td>
			</tr>
			<tr>
				<th>カートボタン表示５</th>
				<td>
					<span class="attention"><!--{$arrErr.pc_button5}--></span>
					<!--{html_radios name="pc_button5" options=$arrCART_BTN_FLG selected=$arrForm.pc_button5 separator='&nbsp;&nbsp;'}-->
				</td>
			</tr>
		    </table>
		</div>

		<div id="sp-tab">
            <table class="form">
			<tr>
				<th>アイコン</th>
				<td>
					<ul class="prdIconList clearfix">
						<li><!--{html_checkboxes name="sp_product_status" options=$arrSTATUS selected=$arrForm.sp_product_status separator='</li><li>'}--></li>
					</ul>
				</td>
			</tr>
			<tr>
				<th>コメント１<span class="attention">(タグ許可)</span></th>
				<td>
					<span class="attention"><!--{$arrErr.sp_comment1}--></span>
					<textarea class="ckeditor" name="sp_comment1" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{if $arrErr.sp_comment1 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" cols="60" rows="8" class="area60"><!--{$arrForm.sp_comment1}--></textarea><br />
					<span class="attention"> (上限<!--{$smarty.const.LLTEXT_LEN}-->文字)</span>
				</td>
			</tr>
			<tr>
				<th>コメント２<span class="attention">(タグ許可)</span></th>
				<td>
					<span class="attention"><!--{$arrErr.sp_comment2}--></span>
					<textarea class="ckeditor" name="sp_comment2" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{if $arrErr.sp_comment2 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" cols="60" rows="8" class="area60"><!--{$arrForm.sp_comment2}--></textarea><br />
					<span class="attention"> (上限<!--{$smarty.const.LLTEXT_LEN}-->文字)</span>
				</td>
			</tr>
			<tr>
				<th>コメント３<span class="attention">(タグ許可)</span></th>
				<td>
					<span class="attention"><!--{$arrErr.sp_comment3}--></span>
					<textarea class="ckeditor" name="sp_comment3" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{if $arrErr.sp_comment3 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" cols="60" rows="8" class="area60"><!--{$arrForm.sp_comment3}--></textarea><br />
					<span class="attention"> (上限<!--{$smarty.const.LLTEXT_LEN}-->文字)</span>
				</td>
			</tr>
			<tr>
				<th>コメント４<span class="attention">(タグ許可)</span></th>
				<td>
					<span class="attention"><!--{$arrErr.sp_comment4}--></span>
					<textarea class="ckeditor" name="sp_comment4" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{if $arrErr.sp_comment4 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" cols="60" rows="8" class="area60"><!--{$arrForm.sp_comment4}--></textarea><br />
					<span class="attention"> (上限<!--{$smarty.const.LLTEXT_LEN}-->文字)</span>
				</td>
			</tr>
			<tr>
				<th>カートボタン表示４</th>
				<td>
					<span class="attention"><!--{$arrErr.sp_button4}--></span>
					<!--{html_radios name="sp_button4" options=$arrCART_BTN_FLG selected=$arrForm.sp_button4 separator='&nbsp;&nbsp;'}-->
				</td>
			</tr>
			<tr>
				<th>コメント５<span class="attention">(タグ許可)</span></th>
				<td>
					<span class="attention"><!--{$arrErr.sp_comment5}--></span>
					<textarea class="ckeditor" name="sp_comment5" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{if $arrErr.sp_comment5 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" cols="60" rows="8" class="area60"><!--{$arrForm.sp_comment5}--></textarea><br />
					<span class="attention"> (上限<!--{$smarty.const.LLTEXT_LEN}-->文字)</span>
				</td>
			</tr>
			<tr>
				<th>カートボタン表示５</th>
				<td>
					<span class="attention"><!--{$arrErr.sp_button5}--></span>
					<!--{html_radios name="sp_button5" options=$arrCART_BTN_FLG selected=$arrForm.sp_button5 separator='&nbsp;&nbsp;'}-->
				</td>
			</tr>
		    </table>
		</div>

		<div id="mb-tab">
            <table class="form">
			<tr>
				<th>アイコン</th>
				<td>
					<ul class="prdIconList clearfix">
						<li><!--{html_checkboxes name="mb_product_status" options=$arrSTATUS selected=$arrForm.mb_product_status separator='</li><li>'}--></li>
					</ul>
				</td>
			</tr>
			<tr>
				<th>コメント１<span class="attention">(タグ許可)</span></th>
				<td>
					<span class="attention"><!--{$arrErr.mb_comment1}--></span>
					<textarea class="ckeditor" name="mb_comment1" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{if $arrErr.mb_comment1 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" cols="60" rows="8" class="area60"><!--{$arrForm.mb_comment1}--></textarea><br />
					<span class="attention"> (上限<!--{$smarty.const.LLTEXT_LEN}-->文字)</span>
				</td>
			</tr>
			<tr>
				<th>コメント２<span class="attention">(タグ許可)</span></th>
				<td>
					<span class="attention"><!--{$arrErr.mb_comment2}--></span>
					<textarea class="ckeditor" name="mb_comment2" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{if $arrErr.mb_comment2 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" cols="60" rows="8" class="area60"><!--{$arrForm.mb_comment2}--></textarea><br />
					<span class="attention"> (上限<!--{$smarty.const.LLTEXT_LEN}-->文字)</span>
				</td>
			</tr>
			<tr>
				<th>コメント３<span class="attention">(タグ許可)</span></th>
				<td>
					<span class="attention"><!--{$arrErr.mb_comment3}--></span>
					<textarea class="ckeditor" name="mb_comment3" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{if $arrErr.mb_comment3 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" cols="60" rows="8" class="area60"><!--{$arrForm.mb_comment3}--></textarea><br />
					<span class="attention"> (上限<!--{$smarty.const.LLTEXT_LEN}-->文字)</span>
				</td>
			</tr>
			<tr>
				<th>コメント４<span class="attention">(タグ許可)</span></th>
				<td>
					<span class="attention"><!--{$arrErr.mb_comment4}--></span>
					<textarea class="ckeditor" name="mb_comment4" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{if $arrErr.mb_comment4 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" cols="60" rows="8" class="area60"><!--{$arrForm.mb_comment4}--></textarea><br />
					<span class="attention"> (上限<!--{$smarty.const.LLTEXT_LEN}-->文字)</span>
				</td>
			</tr>
			<tr>
				<th>コメント５<span class="attention">(タグ許可)</span></th>
				<td>
					<span class="attention"><!--{$arrErr.mb_comment5}--></span>
					<textarea class="ckeditor" name="mb_comment5" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{if $arrErr.mb_comment5 != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" cols="60" rows="8" class="area60"><!--{$arrForm.mb_comment5}--></textarea><br />
					<span class="attention"> (上限<!--{$smarty.const.LLTEXT_LEN}-->文字)</span>
				</td>
			</tr>
		    </table>
		</div>
	</div>

    <!--{* オペビルダー用 *}-->
    <!--{if "sfViewAdminOpe"|function_exists === TRUE}-->
    <!--{include file=`$smarty.const.MODULE_REALDIR`mdl_opebuilder/admin_ope_view.tpl}-->
    <!--{/if}-->

    <div class="btn">
        <a class="btn-normal" href="javascript:;" onclick="selectAll('category_id'); lfDispSwitch('recommend_select'); return false;"><span>関連商品表示/非表示</span></a>
    </div>

    <!--{if $smarty.const.OPTION_RECOMMEND == 1}-->
    <!--{if count($arrRecommend) > 0}-->
    <div id="recommend_select" style="">
    <!--{else}-->
    <div id="recommend_select" style="display:none">
    <!--{/if}-->
    <h2>関連商品</h2>
    <table class="form">
        <!--▼関連商品-->
        <!--{section name=cnt loop=$smarty.const.RECOMMEND_PRODUCT_MAX}-->
        <!--{assign var=recommend_no value="`$smarty.section.cnt.iteration`"}-->
        <tr>
            <!--{assign var=key value="recommend_id`$smarty.section.cnt.iteration`"}-->
            <!--{assign var=anckey value="recommend_no`$smarty.section.cnt.iteration`"}-->
            <th>関連商品(<!--{$smarty.section.cnt.iteration}-->)<br />
                <!--{if $arrRecommend[$recommend_no].product_id}-->
                    <img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrRecommend[$recommend_no].main_list_image|sfNoImageMainList|h}-->" alt="<!--{$arrRecommend[$recommend_no].name|h}-->" width="65" />
                <!--{/if}-->
            </th>
            <td>
                <a name="<!--{$anckey}-->"></a>
                <input type="hidden" name="<!--{$key}-->" value="<!--{$arrRecommend[$recommend_no].product_id|h}-->" />
                <a class="btn-normal" href="javascript:;" name="change" onclick="selectAll('category_id'); win03('./product_select.php?no=<!--{$smarty.section.cnt.iteration}-->', 'search', '615', '500'); return false;">変更</a>
                <!--{assign var=key value="recommend_delete`$smarty.section.cnt.iteration`"}-->
                <input type="checkbox" name="<!--{$key}-->" value="1" />削除<br />
               <!--{assign var=key value="recommend_comment`$smarty.section.cnt.iteration`"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                商品コード:<!--{$arrRecommend[$recommend_no].product_code_min}--><br />
                商品名:<!--{$arrRecommend[$recommend_no].name|h}--><br />
                <textarea class="ckeditor" name="<!--{$key}-->" cols="60" rows="8" class="area60" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{$arrRecommend[$recommend_no].comment|nl2br_html}--></textarea><br />
                <span class="attention"> (上限<!--{$smarty.const.LTEXT_LEN}-->文字)</span>
            </td>
        </tr>
        <!--{/section}-->
        <!--▲関連商品-->
    </table>
    </div>
    <!--{/if}-->
    <div class="btn-area">
        <ul>
        <!--{if $arrForm.arrHidden|@count > 0 || $smarty.post.product_id|escape}-->
        <!--▼検索結果へ戻る-->
            <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_PRODUCTS_URLPATH}-->'); fnModeSubmit('search','',''); return false;"><span class="btn-prev">検索画面に戻る</span></a></li>
        <!--▲検索結果へ戻る-->
        <!--{/if}-->
        <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <li><a class="btn-action" href="javascript:;" onclick="selectAll('category_id'); document.form1.submit(); return false;"><span class="btn-next">確認ページへ</span></a></li>
        <!--{/if}-->
        </ul>
    </div>
</div>
</form>
