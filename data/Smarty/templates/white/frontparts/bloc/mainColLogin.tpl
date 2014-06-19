<form name="login_form" id="login_form" method="post" action="<!--{$smarty.const.HTTPS_URL}-->frontparts/login_check.php" onsubmit="return fnCheckLogin('login_form')">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="login" />
<input type="hidden" name="url" value="<!--{$smarty.server.REQUEST_URI|h}-->" />
<!--{if $tpl_login}-->
<div class="hdrLoginBox">
	<table cellspacing="0" class="lay1">
		<tr>
			<td class="name"><strong><!--{$tpl_name|h}--></strong>&nbsp;様</td>
			<!--{if !$tpl_disable_logout}-->
			<td class="cNumber">
				<a href="javascript:void(0);" onclick="fnFormModeSubmit('login_form', 'logout', '', ''); return false;"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/logout.gif" alt="ログアウト" class="swp" style="cursor:pointer;" /></a>
			</td>
			<!--{/if}-->
		</tr>
	</table>
	<!--{if $smarty.const.USE_POINT !== false && $tpl_user_point}-->
	<table cellspacing="0">
		<tr>
			<td style="vertical-align:middle;">現在のポイント残高</td>
			<td><!--{$tpl_user_point|number_format|default:0}--></td>
			<td style="vertical-align:middle;">ポイント</td>
		</tr>
	</table>
	<!--{/if}-->
</div>
<!--{else}-->
<div id="login-box">
	<dl id="member-login">
		<dt>Web会員の方</dt>
		<dd>
			<p id="id"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/id.gif" width="85" height="25" alt="メールアドレス">
				<input type="text" name="login_email" class="box140" value="<!--{$tpl_login_email|h}-->" style="ime-mode: disabled;" />
			</p>
			<p id="pass"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/pw.gif" width="85" height="25" alt="パスワード">
				<input type="password" name="login_pass" class="box140" /></td>
			</p>
			<p class="btn">
				<a href="javascript:void(0);" onclick="document.login_form.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_login.gif" alt="ログイン" /></a>
			</p>
			<p id="login-help"><a href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->" onclick="win01('<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->','forget','600','400'); return false;" target="_blank" class="link">パスワードを忘れた方はこちら</a></p>
		</dd>
	</dl>
	<dl id="new-customer">
		<dt>Web会員に登録されていない方</dt>
		<dd>
			<p class="exp">
				<strong class="red">商品のご注文にはWeb会員登録が必要です。</strong>
			</p>
			<p class="exp">
				Web会員登録がお済みでないお客様は、画面下部の「お届け情報の入力」から登録フォームにお進みいただいて、必要事項をご入力のうえ、ご注文手続きを行ってください。
				
			</p>
		</dd>
	</dl>
</div>
<!--{/if}-->
</form>
