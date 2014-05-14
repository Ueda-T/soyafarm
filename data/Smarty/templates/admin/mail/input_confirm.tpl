<script type="text/javascript">
<!--
function winSubmitMail(URL,formName,Winname,Wwidth,Wheight){
    var WIN = window.open(URL,Winname,"width="+Wwidth+",height="+Wheight+",scrollbars=yes,resizable=yes,toolbar=no,location=no,directories=no,status=no,menubar=no");
    document.forms[formName].target = Winname;
    document.forms[formName].submit();
    WIN.focus();
}
//-->
</script>
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="template">
<input type="hidden" name="subject" value="<!--{$arrForm.subject.value|h}-->">
<input type="hidden" name="body" value="<!--{$arrForm.body.value|h}-->">
<input type="hidden" name="mail_method" value="<!--{$arrForm.mail_method.value|h}-->">
<input type="hidden" name="template_id" value="<!--{$arrForm.template_id.value|h}-->">
<!--{if $smarty.const.MELMAGA_BATCH_MODE}-->
<input type="hidden" name="send_year" value="<!--{$arrForm.send_year.value|h}-->">
<input type="hidden" name="send_month" value="<!--{$arrForm.send_month.value|h}-->">
<input type="hidden" name="send_day" value="<!--{$arrForm.send_day.value|h}-->">
<input type="hidden" name="send_hour" value="<!--{$arrForm.send_hour.value|h}-->">
<input type="hidden" name="send_minutes" value="<!--{$arrForm.send_minutes.value|h}-->">
<!--{/if}-->
<!--{foreach key=key item=item from=$arrHidden}-->
    <!--{if is_array($item)}-->
        <!--{foreach item=c_item from=$item}-->
            <input type="hidden" name="<!--{$key}-->[]" value="<!--{$c_item|h}-->" />
        <!--{/foreach}-->
    <!--{else}-->
        <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
    <!--{/if}-->
<!--{/foreach}-->
<div id="mail" class="contents-main">
    <table class="form">
		<!--{if $smarty.const.MELMAGA_BATCH_MODE}-->
        <tr>
            <th>配信時間設定<span class="attention"> *</span></th>
            <td><!--{$arrForm.send_year.value|h}-->年<!--{$arrForm.send_month.value|h}-->月<!--{$arrForm.send_day.value|h}-->日<!--{$arrForm.send_hour.value|h}-->時<!--{$arrForm.send_minutes.value|h}-->分</td>
        </tr>
		<!--{/if}-->
        <tr>
            <th>Subject<span class="attention"> *</span><br />（名前差し込み時は {name} といれてください）</th>
            <td><!--{$arrForm.subject.value|h}--></td>
        </tr>
        <!--{if $arrForm.mail_method.value ne 2}-->
            <tr>
                <td colspan="2"><a href="javascript:;" onClick="winSubmitMail('','form2','preview',650,700); return false;">HTMLで確認</a>
            </tr>
        <!--{/if}-->
        <tr>
            <th>本文<span class="attention"> *</span><br />（名前差し込み時は {name} といれてください）</th>
            <td><!--{$arrForm.body.value|h|nl2br}--></td>
        </tr>
    </table>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" name="subm02" onclick="return fnInsertValAndSubmit( document.form1, 'mode', 'regist_back', '' ); return false;"><span class="btn-prev">テンプレート設定画面へ戻る</span></a></li>
            <li><a class="btn-action" href="javascript:;" name="subm03" onClick="return fnInsertValAndSubmit( document.form1, 'mode', 'regist_complete', '' ); return false;"><span class="btn-next"><!--{if $smarty.const.MELMAGA_BATCH_MODE}-->配信を予約する<!--{else}-->配信する<!--{/if}--></span></a></li>
        </ul>
    </div>
</div>
</form>
<form name="form2" id="form2" method="post" action="./preview.php" target="_blank">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="presend" />
    <input type="hidden" name="body" value="<!--{$arrForm.body.value|h}-->" />
</form>
