<!--▼CONTENTS-->
<section id="mainMyPageTop">
	<h2 class="spNaked"><!--{$tpl_title}--></h2>
	<!--{if !$tpl_disable_logout}-->
	<form name="header_login_form" id="header_login_form" method="post" action="<!--{$smarty.const.ROOT_URLPATH}-->frontparts/login_check.php" onsubmit="return fnCheckLogin('header_login_form')">
		<input type="hidden" name="mode" value="login" />
		<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
		<input type="hidden" name="url" value="<!--{$smarty.server.REQUEST_URI|h}-->" />
		<p style="text-align:right;margin-bottom:10px;">
			<a href="javascript:void(0);" onclick="fnFormModeSubmit('header_login_form', 'logout', '', ''); return false;" class="btnGray">ログアウト</a>
		</p>
	</form>
	<!--{/if}-->

	<div class="wrapCustomer">
		<div class="myPagePersonal">
			<!--{if $tpl_navi != ""}-->
				<!--{include file=$tpl_navi}-->
			<!--{else}-->
				<!--{include file=`$smarty.const.TEMPLATE_REALDIR`mypage/navi.tpl}-->
			<!--{/if}-->
		</div>
	</div>

	<div style="margin:10px auto;">
		<p class="naked">「旬穀旬菜」と「ロート通販」は同じお買物カゴでお買物ができます。</p>
		<ul style="text-align:center;">
			<li><a href="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php?category_id=31"><img src="<!--{$TPL_URLPATH}-->img/rohto/bnr_shun.gif" width="100%" /></a></li>
			<li><a href="<!--{$smarty.const.ROOT_URLPATH}-->"><img src="<!--{$TPL_URLPATH}-->img/rohto/bnr_rohto.gif" width="100%" /></a></li>
		</ul>
	</div>
</section>
