<form name="form1" id="form1" method="post" action="?" enctype="multipart/form-data"">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="csv_upload" />
<div id="order" class="contents-main">
    <div class="message">
        <span>CSV登録を実行しました。</span>
    </div>
    <!--{if $arrRowErr}-->
        <table class="form">
            <tr>
                <td>
                    <!--{foreach item=err from=$arrRowErr}-->
                        <span class="attention"><!--{$err|h}--><br /></span>
                    <!--{/foreach}-->
                </td>
            </tr>
        </table>
    <!--{/if}-->
    <!--{if $arrRowResult}-->
        <table class="form">
            <tr>
                <td>
                    <!--{foreach item=result from=$arrRowResult}-->
                    <span><!--{$result}--><br/></span>
                    <!--{/foreach}-->
                    <!--{if $tpl_err_count > 0}-->
                    <span class="attention"><!--{$tpl_err_count}-->件のエラーデータがありました。<br/></span>
                    <!--{/if}-->
                </td>
            </tr>
        </table>
    <!--{/if}-->
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="./inos_import.php"><span class="btn-prev">戻る</span></a></li>
            <!--{if $tpl_customer_err > 0}-->
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'customer_errcsv_download', '', ''); return false;"><span>顧客エラーデータ出力</span></a></li>
            <!--{/if}-->
            <!--{if $tpl_order_err > 0}-->
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'order_errcsv_download', '', ''); return false;"><span>受注エラーデータ出力</span></a></li>
            <!--{/if}-->
            <!--{if $tpl_regular_err > 0}-->
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'regular_errcsv_download', '', ''); return false;"><span>定期エラーデータ出力</span></a></li>
            <!--{/if}-->
        </ul>
    </div>
</div>
</form>
