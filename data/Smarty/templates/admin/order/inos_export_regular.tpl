<script>

function fnDoCustomerExport (mode) {

    document.form1.action="inos_export_order.php";

    if(!confirm("定期データをエクスポートします。よろしいですか。")) {
        return;
    }
    fnModeSubmit('csv','','');

    document.form1.action="?";
}

</script>

<div id="order" class="contents-main">
<form name="search_form" id="search_form" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="search" />
    <h2>定期エクスポート</h2>
    <!--{* 検索条件設定テーブルここから *}-->
    <table>
        <tr>
            <th>最終出力日時</th>
            <td><!--{$arrForm.last_send_date.value|date_format:"%Y年%m月%d日 %H時%M分%S秒"|h}-->
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
        <!--{if ($tpl_csv_download_auth == $smarty.const.CSV_DOWNLOAD_AUTH_ON) && (count($arrResults) > 0 || $tpl_orderCnt > 0)}-->
        <a class="btn-normal" href="javascript:;" onclick="fnDoCustomerExport('csv'); return false;">エクスポート</a>
        <!--{/if}-->
    </div>
    <!--{if count($arrResults) > 0}-->

    <!--{include file=$tpl_pager}-->

    <!--{* 検索結果表示テーブル *}-->
        <table class="list">
        <colgroup width="10%">
        <colgroup width="25%">
        <colgroup width="40%">
        <colgroup width="25%">
        <tr>
            <th>顧客番号</th>
            <th>顧客名</th>
            <th>商品名</th>
            <th>お届け間隔</th>
        </tr>

        <!--{section name=cnt loop=$arrResults}-->
        <tr>
            <td  class="center"><!--{$arrResults[cnt].customer_id|h}--></td>
            <td ><!--{$arrResults[cnt].name|h}--></td>
            <td ><!--{$arrResults[cnt].product_name|h}--></td>
            <td ><!--{$arrResults[cnt].course_cd|h}-->
                <!--{assign var=todoke_week value=$arrResults[cnt].todoke_week}-->
                <!--{assign var=todoke_week2 value=$arrResults[cnt].todoke_week2}-->
                <!--{if $todoke_week != "" && $todoke_week2 != ""}-->
                    <!--{$arrTodokeWeekNo[$todoke_week]|h}-->
                    <!--{$arrTodokeWeek[$todoke_week2]|h}-->曜日
                <!--{/if}-->
            </td>
          </tr>
        <!--{/section}-->
        <!--{/if}-->
    </table>
    <!--{* 検索結果表示テーブル *}-->
</form>
<!--{/if}-->
</div>
