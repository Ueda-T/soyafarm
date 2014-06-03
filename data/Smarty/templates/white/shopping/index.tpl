<!--▼CONTENTS-->
<div id="undercolumn">
	<div id="undercolumn_login">
		<h1><img src="<!--{$TPL_URLPATH}-->img/soyafarm/order_title_step1.gif" alt="購入手続き" /></h1>

		<div class="wrapLogin">
			<table cellspacing="0">
				<tr>
					<td class="dyn1">
						<h2><img src="<!--{$TPL_URLPATH}-->img/rohto/head3.gif" width="390" height="75" alt="ログインまたはお客様情報の確認"></h2>
						<form name="member_form" id="member_form" method="post" action="?" onsubmit="return fnCheckLogin('member_form')">
						<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
						<input type="hidden" name="mode" value="login" />
						<div>
							<table cellspacing="0" class="innr">
								<tr>
									<!--{assign var=key value="login_email"}-->
									<th><img src="<!--{$TPL_URLPATH}-->img/rohto/id.gif" width="85" height="25" alt="メールアドレス"></th>
									<td><!--{if strlen($arrErr[$key]) >= 1}--><span class="attention"><!--{$arrErr[$key]}--></span><br /><!--{/if}-->
								<input type="text" name="<!--{$key}-->" value="<!--{$tpl_login_email|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->; ime-mode: disabled;" class="box300" />
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<!--{assign var=key value="login_memory"}-->
<!--{*
										<input type="checkbox" name="<!--{$key}-->" value="1"<!--{$tpl_login_memory|sfGetChecked:1}--> id="login_memory" style="width:auto;" />
										<label for="login_memory" style="font-size:0.75em;">メールアドレスをコンピューターに記憶させる</label>
*}-->
									</td>
								</tr>
								<tr>
									<!--{assign var=key value="login_pass"}-->
									<th><img src="<!--{$TPL_URLPATH}-->img/rohto/pw.gif" width="85" height="25" alt="パスワード"></th>
									<td>
										<input type="password" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box300" />
										<span class="attention"><!--{$arrErr[$key]}--></span>
									</td>
								</tr>
							</table>
							<input type="image" src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_login.gif" alt="ログイン" name="log" id="log" class="swp" />
							<p class="naked">
								<a class="icon1" href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->" onclick="win01('<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->','forget','600','460'); return false;" target="_blank">パスワードを忘れた方はこちら</a><br />
								※メールアドレスを忘れた方は、お手数ですが、<a href="<!--{$smarty.const.ROOT_URLPATH}-->contact/<!--{$smarty.const.DIR_INDEX_PATH}-->">お問い合わせページ</a>からお問い合わせください。
							</p>
							<ul class="kome">
								<li><span class="red">クッキー（Cookie）を有効にしてください。</span><br />
								（詳しくは<a href="<!--{$smarty.const.ROOT_URLPATH}-->contents/browser.php">ご利用環境</a>をご覧ください。）</li>
							</ul>
						</div>
						</form>
					</td>
					<td class="dyn2">
						<h2><img src="<!--{$TPL_URLPATH}-->img/rohto/head2.gif" alt="初めての方" width="390" height="75"></h2>
						<div>
							<p class="nakedTop"><img src="<!--{$TPL_URLPATH}-->img/rohto/first.gif" alt="初めてロート通販オンラインショップをご利用になる方"></p>
							<p class="naked">会員登録をすると便利なMyページをご利用いただけます。<br />
								また、ログインするだけで、毎回お名前や住所などを入力することなくスムーズにお買い物をお楽しみいただけます。
							</p>
							<p class="naked">
								<a href="<!--{$smarty.const.ROOT_URLPATH}-->entry/">
								<img src="<!--{$TPL_URLPATH}-->img/rohto/regist.gif" alt="会員登録をする" border="0" name="b_gotoentry" /></a>
							</p>
						</div>
					</td>
				</tr>
			</table>
		</div>
<!--{*
		<div class="wrapCoan">
			<h2 class="order"><img src="<!--{$TPL_URLPATH}-->img/rohto/method_hajimete.gif" width="820" height="41" alt="初めてロート通販オンラインショップをご利用になる方"></h2>
			<p>会員登録をせずに購入手続きをされたい方は、下記よりお進みください。</p>
			<form name="member_form2" id="member_form2" method="post" action="?">
			<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
			<input type="hidden" name="mode" value="nonmember" />
			<div class="inputbox">
				<div class="btn_area">
					<ul>
						<li>
							<input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_buystep_on.jpg',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_buystep.jpg',this)" src="<!--{$TPL_URLPATH}-->img/button/btn_buystep.jpg" alt="購入手続きへ" name="buystep" id="buystep" />
						</li>
					</ul>
				</div>
			</div>
			</form>
		</div>
*}-->
	</div>
</div>
<!--▲CONTENTS-->
