<!--▼CONTENTS-->
<!--{include file="`$smarty.const.TEMPLATE_REALDIR`mypage/pankuzu.tpl"}-->

<div id="mainMyPage">
	<h1><img src="<!--{$TPL_URLPATH}-->img/rohto/mypage_title_sub2.gif" width="700" height="70" alt="ご登録内容の変更" /></h1>
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
                        <!--{if $arrForm.email}--><tr>
				<th>メールアドレス</th>
				<td><a href="<!--{$arrForm.email|escape:'hex'}-->"><!--{$arrForm.email|escape:'hexentity'}--></a></td>
			</tr><!--{/if}-->
			<!--{if $arrForm.password}--><tr>
				<th>パスワード<br />
				</th>
				<td><!--{$passlen}--></td>
			</tr><!--{/if}-->
		</table>

		<p class="btn">
			<a href="javascript:void(0);" onclick="document.form1.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/rohto/register.gif" alt="登録" name="complete" id="complete" class="swp" /></a>
		</p>
			<a href="javascript:void(0);" onclick="fnModeSubmit('return', '', ''); return false;">
				<img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_back.gif" alt="戻る" name="back" id="back" class="swp" />
			</a>
		</form>
	</div>
</div>
<!--▲CONTENTS-->
