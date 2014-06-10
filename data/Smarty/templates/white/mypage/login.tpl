<!--▼CONTENTS-->
<div class="wrapLogin02">
    <div id="undercolumn_login">
        <form name="login_mypage" id="login_mypage" method="post" action="<!--{$smarty.const.HTTPS_URL}-->frontparts/login_check.php" onsubmit="return fnCheckLogin('login_mypage')">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="login" />
        <input type="hidden" name="url" value="<!--{$smarty.server.REQUEST_URI|h}-->" />
		<div id="login-box-s">
			<dl id="member-login">
				<dt>Web会員の方</dt>
				<dd>
					<!--{assign var=key value="login_email"}-->
					<span class="attention"><!--{$arrErr[$key]}--></span>
					<p id="id"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/id.gif" width="85" height="25" alt="メールアドレス">
						<input type="text" name="<!--{$key}-->" value="<!--{$tpl_login_email|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->; ime-mode: disabled;" />
					</p>
<!--{*
						<p class="login_memory">
							<!--{assign var=key value="login_memory"}-->
							<input type="checkbox" name="<!--{$key}-->" value="1"<!--{$tpl_login_memory|sfGetChecked:1}--> id="login_memory" style="width:auto;" /><label for="login_memory" style="font-size:0.75em;">メールアドレスをコンピューターに記憶させる</label>
						</p>
*}-->
					<!--{assign var=key value="login_pass"}-->
					<span class="attention"><!--{$arrErr[$key]}--></span>
					<p id="pass"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/pw.gif" width="85" height="25" alt="パスワード">
						<input type="password" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box300" />
					</p>
					<input type="image" src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_login.gif" alt="ログイン" name="log" id="log" class="swp" />
					<p id="login-help">
						<a class="link" href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->" onclick="win01('<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->','forget','600','460'); return false;" target="_blank">パスワードを忘れた方はこちら</a>
					</p>
				</dd>
			</dl>
		</div>
    </div>
</div>

<!--▲CONTENTS-->