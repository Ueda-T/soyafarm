<!--▼CONTENTS-->
<section id="mainMyPage">
	<h2 class="spNaked">ご登録内容の変更</h2>
	<p class="naked">以下のご登録内容をご確認いただき、よろしければ「登録」ボタンを押してください。</p>

	<div class="wrapForm">
		<form name="form1" id="form1" method="post" action="?">
		<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
		<input type="hidden" name="mode" value="complete" />
		<input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id|h}-->" />
		<!--{foreach from=$arrForm key=key item=item}-->
			<!--{if $key ne "mode" && $key ne "subm"}-->
			<input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
			<!--{/if}-->
		<!--{/foreach}-->
		<table summary=" " class="tblOrder delivname">
			<tr>
			    <th>顧客番号</th>
</tr>
<tr>
			    <td>
			      <!--{$arrForm.customer_id|h}-->
			    </td>
			</tr>
			<tr>
				<th>お名前</th>
</tr>
<tr>
				<td><!--{$arrForm.name|h}--></td>
			</tr>
			<tr>
				<th>お名前(フリガナ)</th>
</tr>
<tr>
				<td><!--{$arrForm.kana|h}--></td>
			</tr>
			<tr>
				<th>電話番号</th>
</tr>
<tr>
				<td><!--{$arrForm.tel|h}--></td>
			</tr>
			<tr>
				<th>郵便番号</th>
</tr>
<tr>
				<td><!--{$arrForm.zip}--></td>
			</tr>
			<tr>
				<th>住所</th>
</tr>
<tr>
				<td><!--{$arrPref[$arrForm.pref]}--><!--{$arrForm.addr01|h}--><!--{$arrForm.addr02|h}--></td>
			</tr>
<!--{*
			<tr>
				<th>FAX</th>
</tr>
<tr>
				<td><!--{if strlen($arrForm.fax) > 0}--><!--{$arrForm.fax}--><!--{else}-->未登録<!--{/if}--></td>
			</tr>
*}-->
			<tr>
				<th>メールアドレス</th>
</tr>
<tr>
				<td><!--{$arrForm.email}--></td>
			</tr>
			<tr>
				<th>性別</th>
</tr>
<tr>
				<td><!--{if strlen($arrSex[$arrForm.sex]) > 0}--><!--{$arrSex[$arrForm.sex]}--><!--{else}-->未登録<!--{/if}--></td>
			</tr>
			<tr>
				<th>生年月日</th>
</tr>
<tr>
				<td><!--{if strlen($arrForm.year) > 0 && strlen($arrForm.month) > 0 && strlen($arrForm.day) > 0}--><!--{$arrForm.year|h}-->年<!--{$arrForm.month|h}-->月<!--{$arrForm.day|h}-->日<!--{else}-->未登録<!--{/if}--></td>
			</tr>
			<tr>
				<th>メールのご案内</th>
</tr>
<tr>
				<td><!--{$arrMAILMAGATYPE[$arrForm.mailmaga_flg]}--></td>
			</tr>
		</table>

		<p class="btn" style="margin-top:1em;">
			<a href="javascript:void(0);" onclick="document.form1.submit();return false;" class="btnBlue">登録</a>
		</p>
		<p style="margin:1em 0;">
			<a href="javascript:void(0);" onclick="fnModeSubmit('return', '', ''); return false;" class="btnGray03">戻る</a>
		</p>
		</form>
	</div>
</section>
<!--▲CONTENTS-->
