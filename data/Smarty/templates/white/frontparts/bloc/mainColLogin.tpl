<form name="login_form" id="login_form" method="post" action="<!--{$smarty.const.HTTPS_URL}-->frontparts/login_check.php" onsubmit="return fnCheckLogin('login_form')">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="login" />
<input type="hidden" name="url" value="<!--{$smarty.server.REQUEST_URI|h}-->" />
<!--{if $tpl_login}-->
<div class="wrapLoginOn">
	<div class="myPagePersonal">
		<div class="myPageNamae">
			<table cellspacing="0" class="lay1">
				<tr>
					<td>
						<dl class="stage">
							<dt><strong><!--{$tpl_name|h}--> 様</strong></dt>
						</dl>
					</td>
					<!--{if !$tpl_disable_logout}-->
					<td class="cNumber">
						<a href="javascript:void(0);" onclick="fnFormModeSubmit('login_form', 'logout', '', ''); return false;"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/logout.gif" alt="ログアウト" class="swp" style="cursor:pointer;" /></a>
						<!--{*お客様番号：<!--{$CustomerId}-->*}-->
					</td>
					<!--{/if}-->
				</tr>
			</table>
		</div>
	<!--{if $smarty.const.USE_POINT !== false && $tpl_user_point}-->
	<div class="myPageRotta">
		<table cellspacing="0">
			<tr>
				<td style="vertical-align:middle;"><img src="<!--{$TPL_URLPATH}-->img/rohto/rotta_info.gif" width="265" height="30" alt="(現在のポイント残高" /></td>
				<td><!--{$tpl_user_point|number_format|default:0}--></td>
				<td style="vertical-align:middle;"><img src="<!--{$TPL_URLPATH}-->img/rohto/rotta.gif" width="52" height="30" alt="ポイント" /></td>
			</tr>
		</table>
	</div>
	<!--{/if}-->
	</div>
</div>
<!--{else}-->
<div id="login-box">
	<dl id="member-login">
		<dt>Web会員の方</dt>
		<dd>
			<p id="id"><img src="<!--{$TPL_URLPATH}-->img/rohto/id.gif" width="85" height="25" alt="メールアドレス">
				<input type="text" name="login_email" class="box140" value="<!--{$tpl_login_email|h}-->" style="ime-mode: disabled;" />
			</p>
			<p id="pass"><img src="<!--{$TPL_URLPATH}-->img/rohto/pw.gif" width="85" height="25" alt="パスワード">
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
				Web会員登録がお済みでないお客様は、下記ボタンから登録フォームにお進みいただいて、必要事項をご入力のうえ、ご注文手続きを行ってください。
				
			</p>
		</dd>
	</dl>
</div>
<!--{/if}-->
</form>
