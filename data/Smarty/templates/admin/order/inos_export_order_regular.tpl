<script>

function fnDoOrderExport (mode) {

    if(!confirm("受注・定期データをエクスポートします。よろしいですか。")) {
        return;
    }
    fnModeSubmit('csv','','');
}

</script>

<div id="order" class="contents-main">
<form name="search_form" id="search_form" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="search" />
    <h2>受注・定期エクスポート</h2>
    <!--{* 検索条件設定テーブルここから *}-->
    <table>
        <tr>
            <th>受注最終出力日時</th>
            <td><!--{$order_last_send_date|date_format:"%Y年%m月%d日 %H時%M分%S秒"|default:"前回送信履歴なし"|h}-->
            </td>
        </tr>
        <tr>
            <th>定期最終出力日時</th>
            <td><!--{$regular_last_send_date|date_format:"%Y年%m月%d日 %H時%M分%S秒"|default:"前回送信履歴なし"|h}-->
            </td>
        </tr>
    </table>
    <!--{** ▼検索実行ボタン **}-->
    <div class="btn">
        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('search_form', 'search', '', ''); return false;"><span class="btn-next">未出力データを検索する</span></a></li>
            </ul>
        </div>
    </div>
    <!--{** ▲検索実行ボタン **}-->

</form>

<!--{if $smarty.post.mode == 'search'}-->

<!--★★未出力件数一覧★★-->
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="search" />
    <h2>未出力データ件数</h2>
        <div class="btn">
        受注データ：<span class="attention"><!--検索結果数--><!--{$tpl_orderCnt}-->件</span>【CSV出力件数：<!--{$tpl_orderCsvCnt}-->件】&nbsp;が該当しました。<br />
        定期データ：<span class="attention"><!--検索結果数--><!--{$tpl_regularCnt}-->件</span>【CSV出力件数：<!--{$tpl_regularCsvCnt}-->件】&nbsp;が該当しました。
        <!--{if ($tpl_csv_download_auth == $smarty.const.CSV_DOWNLOAD_AUTH_ON) && ($tpl_orderCnt > 0 || $tpl_regularCnt > 0)}-->
	<br />
        <a class="btn-normal" href="javascript:;" onclick="fnDoOrderExport('csv'); return false;">エクスポート</a>
        <!--{/if}-->
    </div>
</form>
<!--{/if}-->
</div>
