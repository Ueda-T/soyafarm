<!--▼CONTENTS-->
<!--{include file="`$smarty.const.TEMPLATE_REALDIR`mypage/pankuzu.tpl"}-->

<div id="mainMyPageTop">
	<!--{if !$tpl_disable_logout}-->
	<form name="header_login_form" id="header_login_form" method="post" action="<!--{$smarty.const.ROOT_URLPATH}-->frontparts/login_check.php" onsubmit="return fnCheckLogin('header_login_form')">
		<input type="hidden" name="mode" value="login" />
		<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
		<input type="hidden" name="url" value="<!--{$smarty.server.REQUEST_URI|h}-->" />
		<p class="logout">
			<a href="javascript:void(0);" onclick="fnFormModeSubmit('header_login_form', 'logout', '', ''); return false;"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/logout.gif" alt="ログアウト" class="swp" /></a>
		</p>
	</form>
	<!--{/if}-->

	<div class="wrapCustomer">
		<div class="wrapResult">
			<div class="wrapResultEle">
				<h2 class="result">お客様情報の変更を受付ました。</h2>
			</div><!--／wrapResultEle-->
		</div>

		<a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/">
			<img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_back.gif" alt="戻る" name="back" id="back" class="swp" />
		</a>
	</div>
</div>
<!--▲CONTENTS-->
