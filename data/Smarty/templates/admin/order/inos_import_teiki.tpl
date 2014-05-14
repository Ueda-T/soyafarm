<form name="form1" id="form1" method="post" action="?" enctype="multipart/form-data"">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="csv_upload" />
<div id="products" class="contents-main">
    <!--{if $tpl_errtitle != ""}-->
        <div class="message">
            <span class="attention"><!--{$tpl_errtitle}--></span><br />
            <!--{foreach key=key item=item from=$arrCSVErr}-->
                <span class="attention"><!--{$item}-->
                <!--{if $key != 'blank'}-->
                    [値：<!--{$arrParam[$key]}-->]
                <!--{/if}-->
                </span><br />
            <!--{/foreach}-->
        </div>
    <!--{/if}-->

    <!--▼登録テーブルここから-->
    <table>
        <tr>
            <th>定期情報CSVファイル</th>
            <td>
                <!--{if $arrErr.csv_file}-->
                    <span class="attention"><!--{$arrErr.csv_file}--></span>
                <!--{/if}-->
                <input type="file" name="csv_file" size="40" />
            </td>
        </tr>
        <tr>
            <th>登録情報</th>
            <td>
                <!--{foreach name=title key=key item=item from=$arrTitle}-->
                    <!--{$smarty.foreach.title.iteration}-->項目：<!--{$item}--><br />
                <!--{/foreach}-->
            </td>
        </tr>
    </table>
    <!--▲登録テーブルここまで-->
    <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'csv_upload', '', ''); return false;"><span class="btn-next">この内容で登録する</span></a></li>
        </ul>
    </div>
    <!--{/if}-->
</div>
</form>
