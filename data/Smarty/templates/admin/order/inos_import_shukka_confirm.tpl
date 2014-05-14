<div id="products" class="contents-main">
    <div class="message">
        <span>出荷実績インポートを実行しました。</span>
    </div>
    <!--{if $arrRowErr}-->
        <table class="form">
            <tr>
                <td>
                    <!--{foreach item=err from=$arrRowErr}-->
                        <span class="attention"><!--{$err|h}--></span><br />
                    <!--{/foreach}-->
                </td>
            </tr>
        </table>
    <!--{/if}-->
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="send_mail" />
    <!--{if $arrRowResult}-->
        <table class="form">
            <tr>
                <td>
                    <!--{foreach item=result from=$arrRowResult}-->
                    <span><!--{$result}--><br/></span>
                    <!--{/foreach}-->
                </td>
            </tr>
        </table>
    <!--{/if}-->
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="./inos_import_shukka.php"><span class="btn-prev">戻る</span></a></li>
    <!--{if $arrRowResult}-->
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'send_mail', '', ''); return false;"><span class="btn-next">出荷情報を送信する</span></a></li>
    <!--{/if}-->
        </ul>
    </div>
	</form>
</div>
