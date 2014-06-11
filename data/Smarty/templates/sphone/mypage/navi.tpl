	<!--{strip}-->
	<!--▼現在のポイント-->
	<!--{if $point_disp !== false}-->
	<ul class="myPageName clearfix">
		<li><strong><!--{$CustomerName|h}--></strong>様</li>
		<li>割引率：<!--{$tpl_discount.customer_type_name|h}--></li>
		<!--{*<br />お客様番号：<!--{$CustomerId}-->*}-->
	</ul>

	<!--{if $smarty.const.USE_POINT !== false && $CustomerPoint}-->
	<div class="myPageRotta">
		<table cellspacing="0" width="100%">
			<tr>
				<td bgcolor="#ff8a00"><span style="color:#FFF;">現在のポイント残高：<!--{$CustomerPoint|number_format|default:"0"|h}-->&nbsp;ポイント</span></td>
			</tr>
			<tr>
				<td bgcolor="#ffe9cf">
					<p><!--{if $CustomerPointValidDate neq ""}--><!--{$CustomerPointValidDate|date_format:"%Y年%m月%d日"}-->で<!--{$CustomerPoint|number_format|default:"0"|h}-->ポイントが消滅します。<!--{/if}--></p>
				</td>
			</tr>
		</table>
	</div>
	<!--{/if}-->

	<!--{*
	<!--{if $smarty.const.USE_POINT !== false}-->&nbsp;<br>
		現在の所持ポイントは&nbsp;<span class="point st"><!--{$CustomerPoint|number_format|default:"0"|h}-->pt</span><!--{if $CustomerPointValidDate neq ""}-->(<!--{$CustomerPointValidDate|date_format:"%Y/%m/%d"}-->まで有効)<!--{/if}-->、&nbsp;
		お誕生日ポイントは&nbsp;<span class="point st"><!--{$CustomerBirthPoint|number_format|default:"0"|h}-->pt</span><!--{if $CustomerBirthPointValidDate neq ""}-->(<!--{$CustomerBirthPointValidDate|date_format:"%Y/%m/%d"}-->まで有効)<!--{/if}-->&nbsp;です。
	<!--{/if}-->
	*}-->
	<!--{/if}-->
	<!--▲現在のポイント-->
</div>
<div class="myPageMenu">

	<ul class="myPageMenu clearfix">
		<!--{* 会員状態 *}-->
		<!--{if $tpl_login}-->
<!--{*
			<li><a href="<!--{$smarty.const.CART_URLPATH}-->">お買い物カゴを見る</a></li>
*}-->
			<li class="mp_nav_idx"><a href="index.php" class="<!--{if $tpl_mypageno == 'index'}-->selected<!--{elseif $tpl_mypageno == 'regular'}-->bgNone<!--{/if}-->"><span>購入履歴</span></a></li>

			<li class="mp_nav_regular"><a href="regular.php" class="<!--{if $tpl_mypageno == 'regular'}-->selected<!--{/if}-->"><span>定期購入一覧</span></a></li>

			<li class="mp_nav_chg_b"><a href="change_basic.php" class="<!--{if $tpl_mypageno == 'change_basic'}--> selected<!--{/if}-->"><span>メールアドレスとパスワードの変更</span></a></li>

			<li class="mp_nav_chg"><a href="change.php" class="<!--{if $tpl_mypageno == 'change'}-->selected<!--{/if}-->"><span>登録情報変更</span></a></li>

			<li class="mp_nav_deliv"><a href="delivery.php" class="<!--{if $tpl_mypageno == 'delivery'}-->selected<!--{/if}-->"><span>お届け先変更</span></a></li>
			<li class="mp_nav_contact"><a href="<!--{$smarty.const.HTTPS_URL}-->"><span>お問い合わせ</span></a></li>
<!--{*
			<li><a href="refusal.php" width="240" height="115" class="swp">登録削除
			<span class="dsc">ご登録内容､注文履歴を削除します。</span></a></li>
*}-->
		<!--{else}-->
		<!--{/if}-->
	</ul>
	<!--{if !$tpl_disable_logout}-->
	<form name="header_login_form" id="header_login_form" method="post" action="<!--{$smarty.const.ROOT_URLPATH}-->frontparts/login_check.php" onsubmit="return fnCheckLogin('header_login_form')">
		<input type="hidden" name="mode" value="login" />
		<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
		<input type="hidden" name="url" value="<!--{$smarty.server.REQUEST_URI|h}-->" />
		<p class="mt20 mb20" style="text-align:right;">
			<a href="javascript:void(0);" onclick="fnFormModeSubmit('header_login_form', 'logout', '', ''); return false;" class="btnGray">ログアウト</a>
		</p>
	</form>
	<!--{/if}-->
	<!--{/strip}-->
<!--▲NAVI-->
