	<!--{strip}-->
	<!--▼現在のポイント-->
	<!--{if $point_disp !== false}-->
		<p>
			<!--{$CustomerName|h}--></strong>&nbsp;様<br />
			お客様番号：<!--{$CustomerId}-->
		</p>

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
	<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#b9d0dc">
		<tr bgcolor="#dfedf5">
			<th align="left"><h2><font size="3">マイページメニュー</font></h2></th>
		</tr>
	</table>

	<ul class="myPageMenu">
		<!--{* 会員状態 *}-->
		<!--{if $tpl_login}-->
			<li><a href="<!--{$smarty.const.CART_URLPATH}-->">お買い物カゴを見る</a></li>


			<li><a href="history_list.php" class="<!--{if $tpl_mypageno == 'index'}--> selected<!--{/if}-->">購入履歴
			<span class="dsc">現在のご注文完了された商品の配送手続き状況及び、注文内容を確認できます。</span></a></li>

			<li><a href="regular.php" class="<!--{if $tpl_mypageno == 'regular'}--> selected<!--{/if}-->">定期購入履歴</a></li>
			<li><a href="change.php" class="<!--{if $tpl_mypageno == 'change'}--> selected<!--{/if}-->">ご登録内容の変更
			<span class="dsc">お客様情報を変更できます。</span></a></li>
			<li><a href="delivery.php" class="<!--{if $tpl_mypageno == 'delivery'}--> selected<!--{/if}-->">配送先新規登録
			<span class="dsc">配送先の情報を登録できます。</span></a></li>

			<li><a href="change_basic.php" class="<!--{if $tpl_mypageno == 'change_basic'}--> selected<!--{/if}-->">メールアドレスとパスワードの変更
			<span class="dsc">現在使用しているメールアドレスとパスワードの変更ができます。</span></a></li>

			<li><a href="refusal.php" width="240" height="115" class="swp">登録削除
			<span class="dsc">ご登録内容､注文履歴を削除します。</span></a></li>

		<!--{else}-->
		<!--{/if}-->
	</ul>
	<!--{/strip}-->
<!--▲NAVI-->
