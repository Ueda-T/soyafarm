<!--{* -*- coding: utf-8-unix; -*- *}-->
<script type="text/javascript">
<!--

/**
 * カテゴリを設定します
 */
function setCategory(id, name) {
    $("#category_id").val(id);
    $("#category_name").text(name);
    $("#h_category_name").val(name);
}

/**
 * カテゴリ検索ダイアログを表示します
 */
function openDialogSearchCategory() {
    // デフォルトのダイアログパラメータをコピーする
    var params = $.extend(true, {}, defaultParams);

    // カテゴリ検索ダイアログ向けにパラメータを設定
    url = "<!--{$smarty.const.ROOT_URLPATH}-->dialog/search_category.php";
    params.url = url;
    params.width = 740;
    params.height = 490;

    openDialog("dialogCategory", params, function(dialogId, data) {
        // カテゴリコードをセットして変更イベントを呼び出す
        $("#category_code").val(data["selectedCategoryCode"]);
        $("#category_code").change();
    });

    return false;
}

/**
 * 親ブランドを設定します
 */
function setParentBrand(id, name) {
    $("#parent_id").val(id);
    $("#parent_brand_name").text(name);
    $("#h_parent_brand_name").val(name);
}

/**
 * 親ブランド検索ダイアログを表示します
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
        $("#parent_brand_code").val(data["selectedBrandCode"]);
        $("#parent_brand_code").change();
    });

    return false;
}

/**
 * ブランド商品一覧から明細を削除します
 */
function doRemoveProduct(product_code) {
    // 商品コードを追加候補リストから削除
    fnRemoveVal('add_product_cds', ',', product_code);

    // 商品コードを削除候補リストに追加
    fnAppendVal('del_product_cds', ',', product_code);

    // 行を削除
    $('#row_' + product_code).remove();

    return false;
}

/**
 * ブランド商品一覧に明細を追加します
 */
function doAppendProduct(product_code, product_name) {
    var tagTr, tagTd;

    // 隠し行をクローンして取得
    tagTr = $("#clone_base").clone();
    tagTr.attr("id", "row_" + product_code);
    tagTr.attr("style", "");

    // 商品番号
    tagTd = tagTr.children(":first");
    tagTd.html(product_code);

    // 商品名
    tagTd = tagTd.next();
    tagTd.html(product_name);

    // 削除
    tagTd = tagTd.next();
    var tagSpan = tagTd.children(":first");
    var tagA = tagSpan.children(":first");
    tagA.on("click", {"product_code": product_code}, function(event) {
        return doRemoveProduct(event.data.product_code);
    });

    // 行を追加
    $("#brand-product-list tr:last").after(tagTr);
}

/**
 * 商品検索ダイアログを表示します
 */
function openDialogSearchProducts() {
    // デフォルトのダイアログパラメータをコピーする
    var params = $.extend(true, {}, defaultParams);

    // 商品検索ダイアログ向けにパラメータを設定
    url = "<!--{$smarty.const.ROOT_URLPATH}-->dialog/search_products.php";
    params.url = url;
    params.width = 740;
    params.height = 490;

    openDialog("dialogProducts", params, function(dialogId, data) {
        // ダイアログで選択した後の処理
        //   ブランド商品一覧に商品を追加する
        for (var key in data) {
            // 「|」区切りの文字列から配列を作成
            //   0番目: 商品コード
            //   1番目: 商品名
            var row = data[key].split("|");

            // 表示上の明細行追加と管理上の商品コード追加を行うべきかどうか
            // 判断する
            if (fnExistsVal("del_product_cds", ",", row[0])) {
                //  管理上の削除候補から除外する
                fnRemoveVal("del_product_cds", ",", row[0]);

                if (!fnExistsVal("org_product_cds", ",", row[0])) {
                    // 新規で商品が追加された後に削除され、再度商品コードを
                    // 追加する場合
                    //   管理上の追加候補に加える。
                    fnAppendVal("add_product_cds", ",", row[0]);
                }
            } else if (fnExistsVal("add_product_cds", ",", row[0]) ||
                       fnExistsVal("org_product_cds", ",", row[0])) {
                // 追加候補に商品コードがある場合や元々存在する場合
                //   処理なし
                continue;
            } else {
                // まったくの新規
                //   管理上も表示上も追加する
                fnAppendVal("add_product_cds", ",", row[0]);
            }

            // 表示上の明細行を追加する
            doAppendProduct(row[0], row[1]);
        }
    });

    return false;
}

/**
 * 初期設定
 */
$(function() {
    // カレンダー表示（datepicker）
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

    // 所属カテゴリ名を検索して表示するコールバック関数
    var f_search_category = function() {
        var data = new Object();

        data["category_code"] = $("#category_code").val();

        if (data["category_code"] == "") {
            setCategory("", "");
            return;
        }

        $.ajax({
            "url": "<!--{$smarty.const.INPUT_CATEGORY_URLPATH}-->",
            "data": data,
            "success": function(data) {
                if (data == "") {
                    setCategory("", "");
                    return;
                }

                // JSON フォーマット文字をデコード
                var value = eval("(" + data + ")");

                setCategory(value["category_id"], value["category_name"]);
            },
            "error": function(data) {
                setCategory("", "");
            }
        });
    };

    // 所属カテゴリ入力欄の変更時イベントを設定
    $("#category_code").on("change", f_search_category);

    // 親ブランド名を検索して表示するコールバック関数
    var f_search_parent_brand = function() {
        var data = new Object();

        data["brand_code"] = $("#parent_brand_code").val();

        if (data["brand_code"] == "") {
            setParentBrand("", "");
            return;
        }

        $.ajax({
            "url": "<!--{$smarty.const.INPUT_BRAND_URLPATH}-->",
            "data": data,
            "success": function(data) {
                if (data == "") {
                    setParentBrand("", "");
                    return;
                }

                // JSON フォーマット文字をデコード
                var value = eval("(" + data + ")");

                setParentBrand(value["brand_id"], value["brand_name"]);
            },
            "error": function(data) {
                setParentBrand("", "");
            }
        });
    };

    // 親ブランド入力欄の変更時イベントを設定
    $("#parent_brand_code").on("change", f_search_parent_brand);
});

// -->
</script>

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
<input type="hidden" name="brand_id" value="<!--{$arrForm.brand_id|h}-->" />
<input type="hidden" name="org_product_cds" value="<!--{$arrForm.org_product_cds}-->" />
<input type="hidden" name="add_product_cds" value="<!--{$arrForm.add_product_cds|h}-->" />
<input type="hidden" name="del_product_cds" value="<!--{$arrForm.del_product_cds|h}-->" />
<input type="hidden" name="image_key" value="" />
<input type="hidden" id="h_category_name" name="category_name" value="<!--{$arrForm.category_name|h}-->" />
<input type="hidden" id="h_parent_brand_name" name="parent_brand_name" value="<!--{$arrForm.parent_brand_name|h}-->" />
<input type="hidden" id="select_tab_index" name="select_tab_index" value="<!--{$arrForm.select_tab_index|h}-->" />
<!--{foreach key=key item=item from=$arrForm.arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
<!--{/foreach}-->
<div id="brand" class="contents-main">
    <!--{if $arrForm.arrHidden|@count > 0 || $smarty.post.brand_id|escape}-->
    <div class="btn-area-head">
        <!--▼検索結果へ戻る-->
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_PRODUCTS_URLPATH}-->brand_search.php'); fnModeSubmit('search','',''); return false;"><span class="btn-prev">検索画面に戻る</span></a></li>
        <!--▲検索結果へ戻る-->
        </ul>
    </div>
    <!--{/if}-->

    <h2>ブランドマスタ<!--{if $arrForm.brand_id == ""}-->登録<!--{else}-->編集<!--{/if}--></h2>
    <table class="form">
        <tr>
            <th>生成URL</th>
            <td>
            <!--{if $arrForm.brand_id}-->
            <a href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/products/brand.php?brand_code=<!--{$arrForm.brand_code|h}-->" target="_blank" ><!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/products/brand.php?brand_code=<!--{$arrForm.brand_code|h}--></a><br />
            <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>ブランドID</th>
            <td><!--{$arrForm.brand_id|h}--></td>
        </tr>
        <tr>
            <th>ブランドコード<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.brand_code}--></span>
                <input type="text" name="brand_code" value="<!--{$arrForm.brand_code|h}-->" maxlength="<!--{$smarty.const.BRAND_CODE_LEN}-->" style="width:180px; <!--{if $arrErr.brand_code != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="<!--{$smarty.const.BRAND_CODE_LEN}-->" />
            </td>
        </tr>
        <tr>
            <th>ブランド名<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.brand_name}--></span>
                <input type="text" name="brand_name" value="<!--{$arrForm.brand_name|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.brand_name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="60" class="box60" />
            </td>
        </tr>
        <tr>
            <th>掲載開始日<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.disp_start_date}--></span>
                <input type="text" name="disp_start_date" value="<!--{$arrForm.disp_start_date|h}-->" maxlength="10" style="<!--{if $arrErr.disp_start_date != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="10" class="calendar box10" />
            </td>
        </tr>
        <tr>
            <th>掲載終了日<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.disp_end_date}--></span>
                <input type="text" name="disp_end_date" value="<!--{$arrForm.disp_end_date|h}-->" maxlength="10" style="<!--{if $arrErr.disp_end_date != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="10" class="calendar box10" />
            </td>
        </tr>
        <tr>
            <th>所属カテゴリ</th>
            <td>
                <span class="attention"><!--{$arrErr.category_id}--></span>
				<input type="hidden" id="category_id" name="category_id" value="<!--{$arrForm.category_id|h}-->" />
                <input type="text" id="category_code" name="category_code" value="<!--{$arrForm.category_code|h}-->" maxlength="<!--{$smarty.const.CATEGORY_CODE_LEN}-->" style="<!--{if $arrErr.category_code != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="<!--{$smarty.const.CATEGORY_CODE_LEN}-->" class="box10" />
				<img id="search_category" src="<!--{$TPL_URLPATH}-->img/common/btn_search.gif" style="cursor: pointer; vertical-align: middle;" onclick="openDialogSearchCategory();" />&nbsp;&nbsp;<span id="category_name"><!--{$arrForm.category_name|h}--></span>
            </td>
        </tr>
        <tr>
            <th>親ブランド</th>
            <td>
                <span class="attention"><!--{$arrErr.parent_id}--></span>
				<input type="hidden" id="parent_id" name="parent_id" value="<!--{$arrForm.parent_id|h}-->" />
                <input type="text" id="parent_brand_code" name="parent_brand_code" value="<!--{$arrForm.parent_brand_code|h}-->" maxlength="<!--{$smarty.const.BRAND_CODE_LEN}-->" style="<!--{if $arrErr.parent_brand_code != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="<!--{$smarty.const.BRAND_CODE_LEN}-->" class="box10" />
				<img id="search_parent_brand" src="<!--{$TPL_URLPATH}-->img/common/btn_search.gif" style="cursor: pointer; vertical-align: middle;" onclick="openDialogSearchBrand();" />&nbsp;&nbsp;<span id="parent_brand_name"><!--{$arrForm.parent_brand_name|h}--></span>
            </td>
        </tr>
        <tr>
            <th>並び順<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.rank}--></span>
                <input type="text" name="rank" value="<!--{$arrForm.rank|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.rank != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="5" class="box5" />
            </td>
        </tr>
        <tr>
            <th>商品表示件数</th>
            <td>
                <span class="attention"><!--{$arrErr.product_disp_num}--></span>
                <input type="text" name="product_disp_num" value="<!--{$arrForm.product_disp_num|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.product_disp_num != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="5" class="box5" />
				（モバイルでの商品表示件数）
            </td>
        </tr>
        <tr>
            <th>画像表示件数</th>
            <td>
                <span class="attention"><!--{$arrErr.img_disp_num}--></span>
                <input type="text" name="img_disp_num" value="<!--{$arrForm.img_disp_num|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.img_disp_num != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="5" class="box5" />
				（モバイルでの画像付き商品表示件数）
            </td>
        </tr>
        <tr>
            <th>METAタグ</th>
            <td>
	        <span class="attention"><!--{$arrErr.metatag}--></span>
                <textarea name="metatag" cols="60" rows="8" class="area60" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr.metatag|sfGetErrorColor}-->"><!--{$arrForm.metatag|h}--></textarea>
                <span class="attention"> (上限<!--{$smarty.const.LLTEXT_LEN}-->文字)</span><br />
            </td>
        </tr>
    </table>

	<div id="ui-tab" style="margin-bottom: 20px;">
	    <ul>
			<li><a href="#pc-tab"><span>PCサイト用</span></a></li>
			<li><a href="#sp-tab"><span>スマホサイト用</span></a></li>
			<li><a href="#mb-tab"><span>モバイルサイト用</span></a></li>
		</ul>
		<div id="pc-tab">
		    <table class="form">
				<tr>
					<th>コメント<br />（ブランド名の下）</th>
					<td>
						<span class="attention"><!--{$arrErr.pc_comment}--></span>
						<textarea name="pc_comment" style="width: 100%; height: 80px;" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr.pc_comment|sfGetErrorColor}-->"><!--{$arrForm.pc_comment|h}--></textarea>
					</td>
				</tr>
				<tr>
					<th>フリースペース１<br />※親ブランドで表示（ブランド名と切替）</th>
					<td>
						<span class="attention"><!--{$arrErr.pc_free_space1}--></span>
						<textarea name="pc_free_space1" style="width: 100%; height: 80px;" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr.pc_free_space1|sfGetErrorColor}-->"><!--{$arrForm.pc_free_space1|h}--></textarea>
					</td>
				</tr>
				<tr>
					<th>フリースペース２<br />※親ブランドで表示（ブランド一覧下部）</th>
					<td>
						<span class="attention"><!--{$arrErr.pc_free_space2}--></span>
						<textarea name="pc_free_space2" style="width: 100%; height: 80px;" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr.pc_free_space2|sfGetErrorColor}-->"><!--{$arrForm.pc_free_space2|h}--></textarea>
					</td>
				</tr>
				<tr>
					<th>フリースペース３<br />（ブランド名と切替）</th>
					<td>
						<span class="attention"><!--{$arrErr.pc_free_space3}--></span>
						<textarea name="pc_free_space3" style="width: 100%; height: 80px;" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr.pc_free_space3|sfGetErrorColor}-->"><!--{$arrForm.pc_free_space3|h}--></textarea>
					</td>
				</tr>
				<tr>
					<th>フリースペース４<br />（ブランドページ上部）</th>
					<td>
						<span class="attention"><!--{$arrErr.pc_free_space4}--></span>
						<textarea name="pc_free_space4" style="width: 100%; height: 80px;" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr.pc_free_space4|sfGetErrorColor}-->"><!--{$arrForm.pc_free_space4|h}--></textarea>
					</td>
				</tr>
				<tr>
					<th>フリースペース５<br />（ブランドページの下部）</th>
					<td>
						<span class="attention"><!--{$arrErr.pc_free_space5}--></span>
						<textarea name="pc_free_space5" style="width: 100%; height: 80px;" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr.pc_free_space5|sfGetErrorColor}-->"><!--{$arrForm.pc_free_space5|h}--></textarea>
					</td>
				</tr>
			</table>
		</div>
		<div id="sp-tab">
		    <table class="form">
				<tr>
					<th>コメント<br />（ブランド名の下）</th>
					<td>
						<span class="attention"><!--{$arrErr.sp_comment}--></span>
						<textarea name="sp_comment" style="width: 100%; height: 80px;" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr.sp_comment|sfGetErrorColor}-->"><!--{$arrForm.sp_comment|h}--></textarea>
					</td>
				</tr>
				<tr>
					<th>フリースペース１<br />※親ブランドで表示（ブランド名と切替）</th>
					<td>
						<span class="attention"><!--{$arrErr.sp_free_space1}--></span>
						<textarea name="sp_free_space1" style="width: 100%; height: 80px;" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr.sp_free_space1|sfGetErrorColor}-->"><!--{$arrForm.sp_free_space1|h}--></textarea>
					</td>
				</tr>
				<tr>
					<th>フリースペース２<br />※親ブランドで表示（ブランド一覧下部）</th>
					<td>
						<span class="attention"><!--{$arrErr.sp_free_space2}--></span>
						<textarea name="sp_free_space2" style="width: 100%; height: 80px;" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr.sp_free_space2|sfGetErrorColor}-->"><!--{$arrForm.sp_free_space2|h}--></textarea>
					</td>
				</tr>
				<tr>
					<th>フリースペース３<br />（ブランド名と切替）</th>
					<td>
						<span class="attention"><!--{$arrErr.sp_free_space3}--></span>
						<textarea name="sp_free_space3" style="width: 100%; height: 80px;" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr.sp_free_space3|sfGetErrorColor}-->"><!--{$arrForm.sp_free_space3|h}--></textarea>
					</td>
				</tr>
				<tr>
					<th>フリースペース４<br />（ブランドページ上部）</th>
					<td>
						<span class="attention"><!--{$arrErr.sp_free_space4}--></span>
						<textarea name="sp_free_space4" style="width: 100%; height: 80px;" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr.sp_free_space4|sfGetErrorColor}-->"><!--{$arrForm.sp_free_space4|h}--></textarea>
					</td>
				</tr>
				<tr>
					<th>フリースペース５<br />（ブランドページの下部）</th>
					<td>
						<span class="attention"><!--{$arrErr.sp_free_space5}--></span>
						<textarea name="sp_free_space5" style="width: 100%; height: 80px;" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr.sp_free_space5|sfGetErrorColor}-->"><!--{$arrForm.sp_free_space5|h}--></textarea>
					</td>
				</tr>
			</table>
		</div>
		<div id="mb-tab">
		    <table class="form">
				<tr>
					<th>コメント<br />（ブランド名の下）</th>
					<td>
						<span class="attention"><!--{$arrErr.mb_comment}--></span>
						<textarea name="mb_comment" style="width: 100%; height: 80px;" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr.mb_comment|sfGetErrorColor}-->"><!--{$arrForm.mb_comment|h}--></textarea>
					</td>
				</tr>
				<tr>
					<th>フリースペース１<br />※親ブランドで表示（ブランド名と切替）</th>
					<td>
						<span class="attention"><!--{$arrErr.mb_free_space1}--></span>
						<textarea name="mb_free_space1" style="width: 100%; height: 80px;" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr.mb_free_space1|sfGetErrorColor}-->"><!--{$arrForm.mb_free_space1|h}--></textarea>
					</td>
				</tr>
				<tr>
					<th>フリースペース２<br />※親ブランドで表示（ブランド一覧下部）</th>
					<td>
						<span class="attention"><!--{$arrErr.mb_free_space2}--></span>
						<textarea name="mb_free_space2" style="width: 100%; height: 80px;" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr.mb_free_space2|sfGetErrorColor}-->"><!--{$arrForm.mb_free_space2|h}--></textarea>
					</td>
				</tr>
				<tr>
					<th>フリースペース３<br />（ブランド名と切替）</th>
					<td>
						<span class="attention"><!--{$arrErr.mb_free_space3}--></span>
						<textarea name="mb_free_space3" style="width: 100%; height: 80px;" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr.mb_free_space3|sfGetErrorColor}-->"><!--{$arrForm.mb_free_space3|h}--></textarea>
					</td>
				</tr>
				<tr>
					<th>フリースペース４<br />（ブランドページ上部）</th>
					<td>
						<span class="attention"><!--{$arrErr.mb_free_space4}--></span>
						<textarea name="mb_free_space4" style="width: 100%; height: 80px;" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr.mb_free_space4|sfGetErrorColor}-->"><!--{$arrForm.mb_free_space4|h}--></textarea>
					</td>
				</tr>
				<tr>
					<th>フリースペース５<br />（ブランドページの下部）</th>
					<td>
						<span class="attention"><!--{$arrErr.mb_free_space5}--></span>
						<textarea name="mb_free_space5" style="width: 100%; height: 80px;" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$arrErr.mb_free_space5|sfGetErrorColor}-->"><!--{$arrForm.mb_free_space5|h}--></textarea>
					</td>
				</tr>
			</table>
		</div>
	</div>

    <div class="btn">
		<span>ブランド商品一覧</span>　<!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}--><a class="btn-normal" href="javascript:;" onclick="openDialogSearchProducts();"><span>ブランド商品登録</span></a><!--{/if}-->
    </div>

	<!--ブランド商品一覧-->
    <table class="list" id="brand-product-list">
		<colgroup width="15%">
		<colgroup width="75%">
		<colgroup width="10%">
		<tr>
			<th>商品番号</th>
			<th>商品名</th>
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
			<th>削除</th>
            <!--{/if}-->
		</tr>

	    <tr id="clone_base" style="display: none;">
			<!--{* 商品番号 *}-->
			<td class="id"></td>
			<!--{* 商品名 *}-->
			<td></td>

            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
			<td class="menu"><span class="icon_delete"><a href="javascript:;">削除</a></span></td>
            <!--{/if}-->
		</tr>

		<!--{section name=cnt loop=$arrBrandProducts}-->
	    <!--▼ブランド商品<!--{$smarty.section.cnt.iteration}-->-->
	    <tr id="row_<!--{$arrBrandProducts[cnt].product_code|h}-->">
			<!--{* 商品番号 *}-->
			<td class="id"><!--{$arrBrandProducts[cnt].product_code|h}--></td>
			<!--{* 商品名 *}-->
			<td><!--{$arrBrandProducts[cnt].product_name|h}-->　<!--{$arrBrandProducts[cnt].product_class_name|h}--></td>

            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
			<td class="menu"><span class="icon_delete"><a href="javascript:;" onclick="doRemoveProduct('<!--{$arrBrandProducts[cnt].product_code|h}-->');">削除</a></span></td>
            <!--{/if}-->
		</tr>
		<!--▲ブランド商品<!--{$smarty.section.cnt.iteration}-->-->
		<!--{/section}-->
	</table>
	<!--検索結果表示テーブル-->

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_PRODUCTS_URLPATH}-->brand_search.php'); fnModeSubmit('search', '', ''); return false;"><span class="btn-prev">検索画面に戻る</span></a></li>
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_PRODUCTS_URLPATH}-->brand.php'); fnModeSubmit('edit', '', ''); return false;"><span class="btn-next">確認ページへ</span></a></li>
            <!--{/if}-->
        </ul>
    </div>
</div>
</form>
