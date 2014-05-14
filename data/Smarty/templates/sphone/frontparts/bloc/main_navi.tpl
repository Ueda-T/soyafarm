<div id="spInxNav">
	<table cellpadding="0" cellspacing="0" class="ftBtn">
		<tr>
			<td class="f01"><a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/">ﾏｲﾍﾟｰｼﾞ</a></td>
			<td class="f02"><a href="<!--{$smarty.const.CART_URLPATH}-->">お買い物ｶｺﾞ</a></td>
			<td class="f03"><a href="<!--{$smarty.const.ROOT_URLPATH}-->contents/welcome.php">はじめての方</a></td>
			<td class="f04"><a href="<!--{$smarty.const.ROOT_URLPATH}-->contents/guide.php">ｼｮｯﾋﾟﾝｸﾞ <br>ｶﾞｲﾄﾞ</a></td>
			<td class="f05"><a href="<!--{$smarty.const.ROOT_URLPATH}-->contents/merit.php">ｼｮｯﾌﾟ特典</a></td>
		</tr>
	</table><!--//ftBtn-->
</div><!-- //spInxNav -->

<!--{*
<nav class="top_menu clearfix">
 <!--{if $tpl_login}-->
<ul>
  <li><a rel="external" href="javascript:void(document.login_form_bloc.submit())"><img src="<!--{$TPL_URLPATH}-->img/icon/ico_login.png" width="22" height="21" alt="ログアウト" />ログアウト</a></li>
  <li><a rel="external" href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/mypage/login.php" data-transition="slideup"><img src="<!--{$TPL_URLPATH}-->img/icon/ico_mypage.png" width="22" height="21" alt="MYページ" />MYページ</a></li>
  <li><a rel="external" href="<!--{$smarty.const.CART_URLPATH|h}-->"><img src="<!--{$TPL_URLPATH}-->img/icon/ico_cart.png" width="22" height="21" alt="カートを見る" />カートを見る</a></li>
</ul>
<!--{else}-->
<ul>
  <li><a href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/mypage/login.php" data-transition="slideup"><img src="<!--{$TPL_URLPATH}-->img/icon/ico_login.png" width="22" height="21" alt="ログイン" />ログイン</a></li>
  <li><a href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/mypage/login.php" data-transition="slideup"><img src="<!--{$TPL_URLPATH}-->img/icon/ico_mypage.png" width="22" height="21" alt="MYページ" />MYページ</a></li>
  <li><a rel="external" href="<!--{$smarty.const.CART_URLPATH|h}-->"><img src="<!--{$TPL_URLPATH}-->img/icon/ico_cart.png" width="22" height="21" alt="カートを見る" />カートを見る</a></li>
</ul>
<!--{/if}-->
</nav>
<form name="login_form_bloc" id="login_form_bloc" method="post" action="<!--{$smarty.const.HTTPS_URL}-->frontparts/login_check.php" onsubmit="return fnCheckLogin('login_form_bloc')">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="logout" />
        <input type="hidden" name="url" value="<!--{$smarty.server.PHP_SELF|h}-->" />
</form>
*}-->