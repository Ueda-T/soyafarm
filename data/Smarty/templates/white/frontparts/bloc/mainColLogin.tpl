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
						<img src="<!--{$TPL_URLPATH}-->img/rohto/logout.gif" onclick="fnFormModeSubmit('login_form', 'logout', '', ''); return false;" alt="ログアウト" class="swp" style="cursor:pointer;" />
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
<div class="wrapLogin">
	<table cellspacing="0">
		<tr>
			<td class="dyn1">
				<h2><img src="<!--{$TPL_URLPATH}-->img/rohto/head3_02.gif" width="390" height="44" alt="ログインまたはお客様情報の確認"></h2>

				<div>
					<table cellspacing="0" class="innr">
						<tr>
							<th><img src="<!--{$TPL_URLPATH}-->img/rohto/id.gif" width="85" height="25" alt="メールアドレス"></th>
							<td><input type="text" name="login_email" class="box140" value="<!--{$tpl_login_email|h}-->" style="ime-mode: disabled;" /></td>
						</tr>
<!--{*
						<tr>
							<td colspan="2">
								<input type="checkbox" name="login_memory" id="login_memory" value="1" style="width:auto;" <!--{$tpl_login_memory|sfGetChecked:1}--> />
								<label for="login_memory" style="font-size:0.7em;"><span>メールアドレスをコンピューターに記憶させる</span></label>
							</td>
						</tr>
*}-->
						<tr>
							<th><img src="<!--{$TPL_URLPATH}-->img/rohto/pw.gif" width="85" height="25" alt="パスワード"></th>
							<td><input type="password" name="login_pass" class="box140" /></td>
						</tr>
					</table>
					<p class="passFgtLink"><a href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->" onclick="win01('<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->','forget','600','400'); return false;" target="_blank">パスワードを忘れた方はこちら</a></p>
					<p class="btn">
						<a href="javascript:void(0);" onclick="document.login_form.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/rohto/login.gif" alt="ログイン" /></a>
					</p>
				</div>
			</td>
			<td class="dyn2">
				<h2><img src="<!--{$TPL_URLPATH}-->img/rohto/head2_02.gif" alt="初めての方" width="390" height="44"></h2>
				<div>
					<p class="nakedTop"><img src="<!--{$TPL_URLPATH}-->img/rohto/first.gif" alt="初めてロート通販オンラインショップをご利用になる方"></p>
					<p class="naked">「ご注文主様・お届け情報の入力」をクリックして、次のページでお客様情報の登録をお願いします。
					</p>
				</div>
			</td>
		</tr>
	</table>
</div>
<!--{/if}-->
</form>
