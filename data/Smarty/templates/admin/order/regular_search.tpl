<!--{* -*- coding: utf-8-unix; -*- *}-->
<script type="text/javascript">

// カレンダー表示（datepicker）
$(function() {
    $(".calendar").datepicker();
    $(".calendar").datepicker("option", "showOn", 'both');
    $(".calendar").datepicker("option", "buttonImage",
                              '<!--{$TPL_URLPATH}-->img/common/calendar.png');
    $(".calendar").datepicker("option", "buttonImageOnly", true);
});

$(document).ready(function(){
    $.spin.imageBasePath = '<!--{$TPL_URLPATH}-->img/spin1/';
    $('#spin1').spin({
        min: 0,
        interval: 100,
	timeInterval: 150
    });
    $('#spin2').spin({
        min: 0,
        interval: 100,
	timeInterval: 150
    });
});

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
    // 単一選択モードに設定
    params.isSingleSelect = "1";

    openDialog("dialogProducts", params, function(dialogId, data) {
        // ↓ダイアログで選択した後の処理

        // 検索フォームに商品コードと商品名をセットする
        for (var key in data) {
            // 「|」区切りの文字列から配列を作成
            //   0番目: 商品コード
            //   1番目: 商品名
            var row = data[key].split("|");
            pcode = row[0];
            pname = row[1];
        }

        $("#search_product_code").val(pcode);
        $("#search_product_name").val(pname);
    });

    return false;
}
</script>
<div id="order" class="contents-main">
<form name="search_form" id="search_form" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="search" />
    <h2>検索条件設定</h2>
    <!--{* 検索条件設定テーブルここから *}-->
    <table>
        <tr>
            <th style="width:130px;">顧客ID</th>
            <td>
                <!--{assign var=key value="search_customer_id"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box10" />
            </td>
            <th style="width:130px;">顧客コード(基幹)</th>
            <td>
                <!--{assign var=key value="search_customer_cd"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
        </tr>
        <tr>
            <th style="width:130px;">お名前</th>
            <td>
                <!--{assign var=key value="search_order_name"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
            <th style="width:130px;">お名前(フリガナ)</th>
            <td>
                <!--{assign var=key value="search_order_kana"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
        </tr>
        <tr>
            <th>状況</th>
            <td>
                <!--{assign var=key value="search_status"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                <option value="">選択してください</option>
                <!--{html_options options=$arrRegularOrderStatus selected=$arrForm[$key].value}-->
                </select>
            </td>
            <th style="width:130px;"></th>
            <td>
            </td>
        </tr>
        <tr>
            <th style="width:130px;">支払方法</th>
            <td colspan="3">
                <!--{assign var=key value="search_payment_id"}-->
                <span class="attention"><!--{$arrErr[$key]|h}--></span>
                <!--{html_checkboxes name="$key" options=$arrPayments selected=$arrForm[$key].value}-->
            </td>
        </tr>
        <tr>
            <th style="width:130px;">申込み日</th>
            <td>
                <!--{assign var=key value="search_order_date_from"}-->
                <span class="attention"><!--{$arrErr[$key]|h}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" size="10" maxlength="10" style="<!--{if $arrErr[$key] != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="7" class="calendar" />～
                <!--{assign var=key value="search_order_date_to"}-->
                <span class="attention"><!--{$arrErr[$key]|h}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" size="10" maxlength="10" style="<!--{if $arrErr[$key] != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="7" class="calendar" />
            </td>
            <th style="width:130px;">終了日</th>
            <td>
                <!--{assign var=key value="search_cancel_date_from"}-->
                <span class="attention"><!--{$arrErr[$key]|h}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" size="10" maxlength="10" style="<!--{if $arrErr[$key] != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="7" class="calendar" />～
                <!--{assign var=key value="search_cancel_date_to"}-->
                <span class="attention"><!--{$arrErr[$key]|h}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" size="10" maxlength="10" style="<!--{if $arrErr[$key] != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="7" class="calendar" />
            </td>
        </tr>
        <tr>
            <th style="width:130px;">商品コード</th>
            <td>
                <!--{assign var=key value="search_product_code"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" id="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />

            </td>
            <th style="width:130px;">商品名</th>
            <td>
                <!--{assign var=key value="search_product_name"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" id="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30"  />

<!--{* 商品コード検索ダイアログ
				<a href="javascript:;" onclick="openDialogSearchProducts();"><img id="search_category" src="<!--{$TPL_URLPATH}-->img/common/btn_search.gif" style="cursor: pointer; vertical-align: middle;" /></a>
*}-->
            </td>
        </tr>
        <tr>
            <th style="width:130px;">基幹連動</th>
            <td colspan="3">
                <!--{assign var=key value="search_kikan_flg"}-->
                <span class="attention"><!--{$arrErr[$key]|h}--></span>
                <!--{html_checkboxes name="$key" options=$arrKikanFlg selected=$arrForm[$key].value}-->
            </td>
        </tr>

    </table>

    <div class="btn">
        <p class="page_rows">検索結果表示件数
        <!--{assign var=key value="search_page_max"}-->
        <span class="attention"><!--{$arrErr[$key]}--></span>
        <select name="<!--{$arrForm[$key].keyname}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
        <!--{html_options options=$arrPageMax selected=$arrForm[$key].value}-->
        </select> 件</p>
        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('search_form', 'search', '', ''); return false;"><span class="btn-next">この条件で検索する</span></a></li>
            </ul>
        </div>
    </div>
    <!--検索条件設定テーブルここまで-->
</form>

<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'delete')}-->

<!--★★検索結果一覧★★-->
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="search" />
<input type="hidden" name="regular_id" value="" />
<input type="hidden" name="line_no" value="" />
<!--{foreach key=key item=item from=$arrHidden}-->
    <!--{if is_array($item)}-->
        <!--{foreach item=c_item from=$item}-->
        <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
        <!--{/foreach}-->
    <!--{else}-->
        <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
    <!--{/if}-->
<!--{/foreach}-->

    <h2>検索結果一覧</h2>
        <div class="btn">
        <span class="attention"><!--検索結果数--><!--{$tpl_linemax}-->件</span>&nbsp;が該当しました。
    </div>
    <!--{if count($arrResults) > 0}-->

    <!--{include file=$tpl_pager}-->

    <!--{* 検索結果表示テーブル *}-->
        <table class="list">
        <colgroup width="12%">
        <colgroup width="13%">
        <colgroup width="20%">
        <colgroup width="10%">
        <colgroup width="30%">
        <colgroup width="10%">
        <colgroup width="5%">
       
        <tr>
            <th>申込み日</th>
            <th>定期申込番号</th>
            <th>顧客名</th>
            <th>支払方法</th>
            <th>商品</th>
            <th>状況</th>
            <th>詳細</th>
        </tr>

        <!--{section name=cnt loop=$arrResults}-->
        <tr>
            <td class="center"><!--{$arrResults[cnt].order_date|h}--></td>
            <td class="center"><!--{$arrResults[cnt].regular_id}--></td>
            <td><!--{$arrResults[cnt].customer_name|h}--></td>
            <!--{assign var=payment_id value="`$arrResults[cnt].payment_id`"}-->
            <td class="center"><!--{$arrPayments[$payment_id]}--></td><!-- 支払方法 -->
            <td><!--{$arrResults[cnt].product_name|h}--></td><!-- 商品 -->
            <!--{assign var=status value="`$arrResults[cnt].status`"}-->
            <td class="center"><!--{$arrRegularOrderStatus[$status]|h}--></td><!-- 状況 -->

            <td class="center"><a href="javascript:;" onclick="fnChangeAction('./regular.php'); fnSetVal('line_no', '<!--{$arrResults[cnt].line_no}-->'); fnModeSubmit('pre_edit', 'regular_id', '<!--{$arrResults[cnt].regular_id}-->'); return false;"><span class="icon_edit">詳細</span></a></td>

          </tr>
        <!--{/section}-->
    </table>
    <!--{* 検索結果表示テーブル *}-->
    <!--{/if}-->
</form>
<!--{/if}-->
</div>
