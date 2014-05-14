<!--{* -*- coding: utf-8-unix; -*- *}-->
<script type="text/javascript">
/**
 * 対象購入商品一覧（オリジナル）から明細を削除します
 */
function doOrgRemoveProduct(product_code) {
    // 商品コードをオリジナルリストから削除
    fnRemoveVal('org_product_cds', ',', product_code);

    // 商品コードを追加候補リストから削除
    fnRemoveVal('add_product_cds', ',', product_code);

    // 商品コードを削除候補リストに追加
    fnAppendVal('del_product_cds', ',', product_code);

    // 行を削除
    $('#row_' + product_code).remove();
}
/**
 * 対象購入商品一覧から明細を削除します
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
 * 対象購入商品一覧に明細を追加します
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
    // オリジナルの対象購入商品コード一覧を保存
    var elements = $("#brand-product-list").find("tr[id^='row_']");
    $(elements).each(function() {
        var tagTd = $(this).children(":first");
        fnAppendVal("org_product_cds", ",", tagTd.html());
    });
});
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
<input type="hidden" name="follow_id" value="<!--{$arrForm.follow_id|h}-->" />
<input type="hidden" name="org_product_cds" value="" />
<input type="hidden" name="add_product_cds" value="<!--{$arrForm.add_product_cds|h}-->" />
<input type="hidden" name="del_product_cds" value="<!--{$arrForm.del_product_cds|h}-->" />
<!--{foreach key=key item=item from=$arrForm.arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
<!--{/foreach}-->
<div id="followMail" class="contents-main">
    <!--{if $arrForm.arrHidden|@count > 0 || $smarty.post.follow_id|escape}-->
    <div class="btn-area-head">
        <!--▼検索結果へ戻る-->
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_ORDER_URLPATH}-->followMail_search.php'); fnModeSubmit('search','',''); return false;"><span class="btn-prev">検索画面に戻る</span></a></li>
        <!--▲検索結果へ戻る-->
        </ul>
    </div>
    <!--{/if}-->

    <h2>フォローメールマスタ<!--{if $arrForm.follow_id == ""}-->登録<!--{else}-->編集<!--{/if}--></h2>
    <table class="form">
        <tr>
            <th>フォローメールコード<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.follow_code}--></span>
                <input type="text" name="follow_code" value="<!--{$arrForm.follow_code|h}-->" maxlength="10" style="<!--{if $arrErr.follow_code != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="10" class="box10" />
            </td>
        </tr>
        <tr>
            <th>フォローメール名<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.follow_name}--></span>
                <input type="text" name="follow_name" value="<!--{$arrForm.follow_name|h}-->" maxlength="40" style="<!--{if $arrErr.follow_name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="50" class="box50" />
            </td>
        </tr>
        <tr>
            <th>送信日設定<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.send_term}--></span>
                <input type="text" name="send_term" value="<!--{$arrForm.send_term|h}-->" maxlength="4" style="<!--{if $arrErr.send_term != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="6" class="box6" />日（発送日からの経過日）
            </td>
        </tr>
        <tr>
            <th>状態<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.status}--></span>
				<!--{assign var='style' value=''}-->
				<!--{if $arrErr.status != ""}-->
				    <!--{assign var='style' value="background-color: `$smarty.const.ERR_COLOR`;"}-->
				<!--{/if}-->
                <!--{html_radios name="status" options=$arrFollowMailStatus selected=$arrForm.status separator='&nbsp;&nbsp;' style="`$style`"}-->
            </td>
        </tr>
        <tr>
            <th>メールタイトル<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.subject}--></span>
                <input type="text" name="subject" value="<!--{$arrForm.subject|h}-->" maxlength="<!--{$smarty.const.SMTEXT_LEN}-->" style="<!--{if $arrErr.subject != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="60" class="box60" />
            </td>
        </tr>
        <tr>
            <th>メール本文<span class="attention"> *</span></th>
            <td>
	            <span class="attention"><!--{$arrErr.mail_body}--></span>
                <textarea name="mail_body" cols="60" rows="8" class="area60" maxlength="99999" style=""><!--{$arrForm.mail_body|h}--></textarea>
            </td>
        </tr>
    </table>

    <div class="btn">
		<span>対象購入商品一覧</span>　<!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}--><a class="btn-normal" href="javascript:;" onclick="openDialogSearchProducts();"><span>対象購入商品登録</span></a><!--{/if}-->
        <span class="attention"><!--{$arrErr.target_products}--></span>
    </div>

	<!--対象購入商品一覧-->
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
		<!--{section name=cnt loop=$arrFollowMailProducts}-->
	    <!--▼対象購入商品<!--{$smarty.section.cnt.iteration}-->-->
	    <tr id="row_<!--{$arrFollowMailProducts[cnt].product_code|h}-->">
			<!--{* 商品番号 *}-->
			<td class="id"><!--{$arrFollowMailProducts[cnt].product_code|h}--></td>
			<!--{* 商品名 *}-->
			<td><!--{$arrFollowMailProducts[cnt].product_name|h}--></td>

            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
			<td class="menu"><span class="icon_delete"><a href="javascript:;" onclick="doOrgRemoveProduct('<!--{$arrFollowMailProducts[cnt].product_code|h}-->');">削除</a></span></td>
            <!--{/if}-->
		</tr>
		<!--▲対象購入商品<!--{$smarty.section.cnt.iteration}-->-->
		<!--{/section}-->
	</table>
	<!--検索結果表示テーブル-->

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_ORDER_URLPATH}-->followMail_search.php'); fnModeSubmit('search', '', ''); return false;"><span class="btn-prev">検索画面に戻る</span></a></li>
            <!--{if $tpl_update_auth == $smarty.const.UPDATE_AUTH_ON}-->
            <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_ORDER_URLPATH}-->followMail.php'); fnModeSubmit('edit', '', ''); return false;"><span class="btn-next">確認ページへ</span></a></li>
            <!--{/if}-->
        </ul>
    </div>
</div>
</form>
