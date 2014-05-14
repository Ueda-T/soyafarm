<script>

function fnDoOrderExport (mode) {

    if(!confirm("受注データをエクスポートします。よろしいですか。")) {
        return;
    }
    fnModeSubmit('csv','','');
}

</script>

<div id="order" class="contents-main">
<form name="search_form" id="search_form" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="search" />
    <h2>受注エクスポート</h2>
    <!--{* 検索条件設定テーブルここから *}-->
    <table>
        <tr>
            <th>最終出力日時</th>
            <td><!--{$arrForm.last_send_date.value|date_format:"%Y年%m月%d日 %H時%M分%S秒"|default:"前回送信履歴なし"|h}-->
            </td>
        </tr>
    </table>
    <!--{** ▼検索実行ボタン **}-->
    <div class="btn">
        <p class="page_rows">検索結果表示件数
        <!--{assign var=key value="search_page_max"}-->
        <span class="attention"><!--{$arrErr[$key]}--></span>
        <select name="<!--{$arrForm[$key].keyname}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
        <!--{html_options options=$arrPageMax selected=$arrForm[$key].value}-->
        </select> 件</p>
        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('search_form', 'search', '', ''); return false;"><span class="btn-next">未出力データを検索する</span></a></li>
            </ul>
        </div>
    </div>
    <!--{** ▲検索実行ボタン **}-->

</form>

<!--{if count($arrErr) == 0 && ($smarty.post.mode == 'search')}-->

<!--★★検索結果一覧★★-->
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="search" />
<input type="hidden" name="order_id" value="" />
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
        <!--{if ($tpl_csv_download_auth == $smarty.const.CSV_DOWNLOAD_AUTH_ON) && (count($arrResults) > 0 || $tpl_regularCnt > 0)}-->
        <a class="btn-normal" href="javascript:;" onclick="fnDoOrderExport('csv'); return false;">エクスポート</a>
        <!--{/if}-->
    </div>
    <!--{if count($arrResults) > 0}-->

    <!--{include file=$tpl_pager}-->

    <!--{* 検索結果表示テーブル *}-->
        <table class="list">
        <colgroup width="20%">
        <colgroup width="15%">
        <colgroup width="30%">
        <colgroup width="20%">
        <colgroup width="15%">
        <tr>
            <th>受注日</th>
            <th>注文番号</th>
            <th>顧客名</th>
            <th>支払方法</th>
            <th>購入金額(円)</th>
        </tr>

        <!--{section name=cnt loop=$arrResults}-->
        <tr>
            <td  class="center" nowrap><!--{$arrResults[cnt].create_date|sfDispDBDate}--></td>
            <td  class="center"><!--{$arrResults[cnt].order_id}--></td>
            <td ><!--{$arrResults[cnt].order_name|h}--></td>
            <!--{assign var=payment_id value="`$arrResults[cnt].payment_id`"}-->
            <td class="center"><!--{$arrPayments[$payment_id]}--></td>
            <td class="right"><!--{$arrResults[cnt].payment_total|number_format}--></td>
          </tr>
        <!--{/section}-->
        <!--{/if}-->
    </table>
    <!--{* 検索結果表示テーブル *}-->
</form>
<!--{/if}-->
</div>
