	<!--{strip}-->
	<!--▼現在のポイント-->
	<!--{if $point_disp !== false}-->
	<div class="hdrLoginBox">
		<table cellspacing="0" class="lay1">
			<tr>
				<td class="name">
					<strong><!--{$CustomerName|h}--></strong>&nbsp;様
				</td>
				<td class="cNumber">
					割引率：<!--{$tpl_discount.customer_type_name|h}-->
					<!--{*お客様番号：<!--{$CustomerId}-->*}-->
				</td>
			</tr>
		</table>
	</div>

	<!--{if $smarty.const.USE_POINT !== false && $CustomerPoint}-->
	<div class="myPageRotta">
		<table cellspacing="0">
			<tr>
				<td style="vertical-align:middle;"><img src="<!--{$TPL_URLPATH}-->img/rohto/rotta_info.gif" width="265" height="30" alt="(現在のポイント残高" /></td>
				<td><!--{$CustomerPoint|number_format|default:"0"|h}--></td>
				<td style="vertical-align:middle;"><img src="<!--{$TPL_URLPATH}-->img/rohto/rotta.gif" width="52" height="30" alt="ポイント" /></td>
			</tr>
		</table>
		<p><!--{if $CustomerPointValidDate neq ""}--><!--{$CustomerPointValidDate|date_format:"%Y年%m月%d日"}-->で<!--{$CustomerPoint|number_format|default:"0"|h}-->ポイントが消滅します。<!--{/if}--></p>
	</div>
	<!--{/if}-->
	<!--{/if}-->
	<!--▲現在のポイント-->
</div>
<br />
<div class="myPageMenu">
	<ul class="menu clearfix">
		<!--{* 会員状態 *}-->
		<!--{if $tpl_login}-->
			<li><a href="change.php" class="<!--{if $tpl_mypageno == 'change'}--> selected<!--{/if}-->"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/mypage_btn01.gif" alt="ご登録内容の変更" class="swp"></a></li>

			<li><a href="change_basic.php" class="<!--{if $tpl_mypageno == 'change_basic'}--> selected<!--{/if}-->"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/mypage_btn02.gif" alt="メールアドレス及びパスワードの変更" class="swp"></a></li>

			<li><a href="history_list.php" class="<!--{if $tpl_mypageno == 'index'}--> selected<!--{/if}-->"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/mypage_btn03.gif" alt="ご注文履歴" class="swp"></a></li>

			<li><a href="regular.php" class="<!--{if $tpl_mypageno == 'regular'}--> selected<!--{/if}-->"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/mypage_btn04.gif" alt="定期購入一覧" class="swp"></a></li>
			<!--{*
			<!--{if $smarty.const.OPTION_FAVOFITE_PRODUCT == 1}-->
				<li><a href="favorite.php" class="<!--{if $tpl_mypageno == 'favorite'}--> selected<!--{/if}-->">
					お気に入り一覧</a></li>
			<!--{/if}-->
			*}-->
			<li><a href="delivery.php" class="<!--{if $tpl_mypageno == 'delivery'}--> selected<!--{/if}-->">
				<img src="<!--{$TPL_URLPATH}-->img/soyafarm/mypage_btn05.gif" alt="アドレス（配送先）新規登録" class="swp"></a></li>
			<li><a href="<!--{$smarty.const.ROOT_URLPATH}-->contact/">
				<img src="<!--{$TPL_URLPATH}-->img/soyafarm/mypage_btn06.gif" alt="お問い合わせ" class="swp"></a></li>
			<!--{*
			<li><a href="refusal.php" class="swp">退会手続き</a></li>
			*}-->

		<!--{* 退会状態 *}-->
		<!--{else}-->
			<li><a href="<!--{$smarty.const.TOP_URLPATH}-->" class="<!--{if $tpl_mypageno == 'index'}--> selected<!--{/if}-->">
				購入履歴一覧</a></li>
			<!--{if $smarty.const.OPTION_FAVOFITE_PRODUCT == 1}-->
				<li><a href="<!--{$smarty.const.TOP_URLPATH}-->" class="<!--{if $tpl_mypageno == 'favorite'}--> selected<!--{/if}-->">
					お気に入り一覧</a></li>
			<!--{/if}-->
			<li><a href="<!--{$smarty.const.TOP_URLPATH}-->" class="<!--{if $tpl_mypageno == 'change'}--> selected<!--{/if}-->">
				会員登録内容変更</a></li>
			<li><a href="<!--{$smarty.const.TOP_URLPATH}-->" class="<!--{if $tpl_mypageno == 'delivery'}--> selected<!--{/if}-->">
				お届け先追加・変更</a></li>
			<li><a href="<!--{$smarty.const.TOP_URLPATH}-->" class="<!--{if $tpl_mypageno == 'refusal'}--> selected<!--{/if}-->">
				退会手続き</a></li>
		<!--{/if}-->
	</ul>
	<!--{/strip}-->
<!--▲NAVI-->
