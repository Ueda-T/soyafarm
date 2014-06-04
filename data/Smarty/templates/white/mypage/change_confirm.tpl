<!--▼CONTENTS-->
<!--{include file="`$smarty.const.TEMPLATE_REALDIR`mypage/pankuzu.tpl"}-->

<div id="mainMyPage">
	<h1><img src="<!--{$TPL_URLPATH}-->img/soyafarm/mypage_title_sub1.gif"  alt="ご登録内容の変更" /></h1>
	<p class="intro">以下のご登録内容をご確認いただき、よろしければ「登録」ボタンを押してください。</p>

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
		<table summary=" " class="delivname">
			<colgroup width="30%"></colgroup>
			<colgroup width="70%"></colgroup>
			<tr>
			    <th>顧客番号</th>
			    <td>
			      <!--{$arrForm.customer_id|h}-->
			    </td>
			</tr>
			<tr>
				<th>お名前</th>
				<td><!--{$arrForm.name|h}--></td>
			</tr>
			<tr>
				<th>お名前(フリガナ)</th>
				<td><!--{$arrForm.kana|h}--></td>
			</tr>
			<tr>
				<th>電話番号</th>
				<td><!--{$arrForm.tel|h}--></td>
			</tr>
			<tr>
				<th>郵便番号</th>
				<td><!--{$arrForm.zip}--></td>
			</tr>
			<tr>
				<th>住所</th>
				<td><!--{$arrPref[$arrForm.pref]}--><!--{$arrForm.addr01|h}--><!--{$arrForm.addr02|h}--></td>
			</tr>
<!--{*
			<tr>
				<th>FAX</th>
				<td><!--{if strlen($arrForm.fax) > 0}--><!--{$arrForm.fax}--><!--{else}-->未登録<!--{/if}--></td>
			</tr>
*}-->
			<tr>
				<th>メールアドレス</th>
				<td><!--{$arrForm.email}--></td>
			</tr>
			<tr>
				<th>性別</th>
				<td><!--{if strlen($arrSex[$arrForm.sex]) > 0}--><!--{$arrSex[$arrForm.sex]}--><!--{else}-->未登録<!--{/if}--></td>
			</tr>
			<tr>
				<th>生年月日</th>
				<td><!--{if strlen($arrForm.year) > 0 && strlen($arrForm.month) > 0 && strlen($arrForm.day) > 0}--><!--{$arrForm.year|h}-->年<!--{$arrForm.month|h}-->月<!--{$arrForm.day|h}-->日<!--{else}-->未登録<!--{/if}--></td>
			</tr>
<!--{*
			<tr>
				<th>希望するパスワード<br />
				</th>
				<td><!--{$passlen}--></td>
			</tr>
			<tr>
				<th>パスワードを忘れた時のヒント</th>
				<td>質問：&nbsp;<!--{$arrReminder[$arrForm.reminder]|h}--><br />
						答え：&nbsp;<!--{$arrForm.reminder_answer|h}--></td>
			</tr>
*}-->

			<tr>
				<th>メールマガジン送付について</th>
				<td><!--{$arrMAILMAGATYPE[$arrForm.mailmaga_flg]}--></td>
			</tr>
<!--{*
			<tr>
				<th>アンケートついて</th>
				<td>
					<!--{if $arrForm.questionnaire eq ''}-->
					未登録
					<!--{else}-->
					<!--{$arrQuestionnaire[$arrForm.questionnaire]|h}-->
					<!--{if $arrForm.questionnaire_other neq ''}-->
					<br />
					<!--{$arrForm.questionnaire_other|h}-->
					<!--{/if}-->
					<!--{/if}-->
				</td>
			</tr>
*}-->
		</table>

		<p class="btn">
			<a href="javascript:void(0);" onclick="document.form1.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_register.gif" alt="登録" name="complete" id="complete" class="swp" /></a>
		</p>
			<a href="javascript:void(0);" onclick="fnModeSubmit('return', '', ''); return false;">
				<img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_back.gif" alt="戻る" name="back" id="back" class="swp" />
			</a>
		</form>
	</div>
</div>
<!--▲CONTENTS-->
