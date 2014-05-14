<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<!--{foreach key=key item=item from=$arrHidden}-->
    <!--{if is_array($item)}-->
        <!--{foreach item=c_item from=$item}-->
            <input type="hidden" name="<!--{$key}-->[]" value="<!--{$c_item|h}-->" />
        <!--{/foreach}-->
    <!--{else}-->
        <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
    <!--{/if}-->
<!--{/foreach}-->
<input type="hidden" name="mode" value="template" />
<input type="hidden" name="mail_method" value="<!--{$arrForm.mail_method.value}-->" />
<div id="mail" class="contents-main">
    <table class="form">
        <tr>
            <th>テンプレート選択<span class="attention"> *</span></th>
            <td>
                <!--{assign var=key value="template_id"}-->
                <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
                <select name="<!--{$key}-->" onchange="return fnInsertValAndSubmit( document.form1, 'mode', 'template', '' ) " style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                <option value="" selected="selected">選択してください</option>
                <!--{html_options options=$arrTemplate selected=$arrForm[$key].value}-->
                </select>
            </td>
        </tr>
		<!--{if $smarty.const.MELMAGA_BATCH_MODE}-->
        <tr>
            <th>配信時間設定<span class="attention"> *</span></th>
            <td>
			<!--{if $arrErr.send_year || $arrErr.send_month || $arrErr.send_day || $arrErr.send_hour || $arrErr.send_minutes}--><span class="red12"><!--{$arrErr.send_year}--><!--{$arrErr.send_month}--><!--{$arrErr.send_day}--><!--{$arrErr.send_hour}--><!--{$arrErr.send_minutes}--></span><br><!--{/if}-->

			<select name="send_year" style="<!--{$arrErr.send_year|sfGetErrorColor}-->">
			<!--{html_options options=$arrYear selected=$arrForm.send_year.value|h}-->
			</select>年

			<select name="send_month" style="<!--{$arrErr.send_month|sfGetErrorColor}-->">
			<!--{html_options options=$objDate->getMonth() selected=$arrForm.send_month.value|h}-->
			</select>月
			<select name="send_day" style="<!--{$arrErr.send_day|sfGetErrorColor}-->">
			<!--{html_options options=$objDate->getDay() selected=$arrForm.send_day.value|h}-->
			</select>日
			<select name="send_hour" style="<!--{$arrErr.send_hour|sfGetErrorColor}-->">
			<!--{html_options options=$objDate->getHour() selected=$arrForm.send_hour.value|h}-->
			</select>時
			<select name="send_minutes" style="<!--{$arrErr.send_minutes|sfGetErrorColor}-->">
			<!--{html_options options=$objDate->getMinutesInterval() selected=$arrForm.send_minutes.value|h}-->
			</select>分
            </td>
        </tr>
		<!--{/if}-->
    </table>

    <!--{if $arrForm.template_id.value}-->
    <table class="form">
        <tr>
            <th>Subject<span class="attention"> *</span><br />（名前差し込み時は {name} といれてください）</th>
            <td>
                <!--{assign var=key value="subject"}-->
                <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
                <input type="text" name="subject" size="65" class="box65" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" value="<!--{$arrForm[$key].value|h}-->" />
            </td>
        </tr>
        <tr>
            <th>本文<span class="attention"> *</span><br />（名前差し込み時は {name} といれてください）</th>
            <td>
                <!--{assign var=key value="body"}-->
                <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
                <textarea name="body" cols="90" rows="40" class="area90" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"><!--{$arrForm[$key].value|h}--></textarea>
            </td>
        </tr>
    </table>
    <!--{/if}-->

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="return fnInsertValAndSubmit( document.form1, 'mode', 'back', '' ); return false;"><span class="btn-prev">検索画面に戻る</span></a></li>
            <li><a class="btn-action" href="javascript:;" onclick="return fnInsertValAndSubmit( document.form1, 'mode', 'regist_confirm', '' ); return false;" ><span class="btn-next">確認ページへ</span></a></li>
        </ul>
    </div>
</div>
</form>
