<!--{if $tpl_login}-->
<div id="subCustomer">
	<div class="wrapPersonal">
		<dl class="stage">
			<dt><strong><!--{$tpl_name}--> </strong>&nbsp;様</dt>
		</dl>
	</div><!--／wrapPersonal-->

	<!--{if $smarty.const.USE_POINT !== false && $CustomerPoint}-->
	<div class="rottaInfo">
		<h2><img src="ポイント（通販ポイント）残高</h2>
		<div class="rottaInfoEle">
			<dl>
				<dt>現在の保有ポイント</dt>
				<dd><span><!--{$CustomerPoint|number_format|default:"0"|h}--></span></dd>
			</dl>

			<dl>
				<dt><span><!--{$CustomerPointValidDate|date_format:"%Y年%m月%d日"}--></span>までに使用期限切れ<span class="dyn">※</span>になるポイント</dt>
				<dd class="dyn"><span><!--{$CustomerPoint|number_format|default:"0"|h}--></span></dd>
			</dl>
			<p>※期限までにご利用のない場合、ポイントは消滅します。</p>

		</div><!--rottaInfoEle-->
	</div><!--rottaInfo-->
	<!--{/if}-->

	<dl class="myPageMenu">
		<dt class="top"><a href="<!--{$smarty.const.URL_MYPAGE_TOP}-->">マイページメニュートップ</a></dt>
		<dt>
		<!--{if $tpl_mypageno == 'change'}-->
			<span class="koko">ご登録内容の変更</span>
		<!--{else}-->
			<a href="change.php">ご登録内容の変更</a>
		<!--{/if}-->
		</dt>

		<dt>
		<!--{if $tpl_mypageno == 'change_basic'}-->
			<span class="koko">メールアドレス及びパスワードの変更</span>
		<!--{else}-->
			<a href="change_basic.php">メールアドレス及びパスワードの変更</a>
		<!--{/if}-->
		</dt>

		<dt>
		<!--{if $tpl_mypageno == 'history_list'}-->
			<span class="koko">ご注文履歴</span>
		<!--{else}-->
			<a href="history_list.php">ご注文履歴</a>
		<!--{/if}-->
		</dt>

<!--{*
		<dt>
		<!--{if $tpl_mypageno == 'regular'}-->
			<span class="koko">定期購入一覧</span>
		<!--{else}-->
			<a href="regular.php">定期購入一覧</a>
		<!--{/if}-->
		</dt>
*}-->

		<dt>
		<!--{if $tpl_mypageno == 'delivery'}-->
			<span class="koko">配送先の登録・修正</span>
		<!--{else}-->
			<a href="delivery.php">配送先の登録・修正</a>
		<!--{/if}-->
		</dt>
	</dl>
	<!--{if $tpl_mypageno != 'refusal'}-->
	<p class="sakujyo"><a href="refusal.php">登録削除</a></p>
	<!--{/if}-->
<!--{/if}-->
</div>
