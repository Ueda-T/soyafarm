<!--{assign var=top value="`$smarty.const.ROOT_URLPATH`index.php"}-->
<!--{if $smarty.server.PHP_SELF != $top}-->
<!--{include file="`$smarty.const.SMARTPHONE_TEMPLATE_REALDIR`frontparts/bloc/main_navi.tpl"}-->
<!--{/if}-->
<div id="spFooter" style="margin:0;<!--{if $smarty.server.PHP_SELF == $top}-->border-top:3px solid #A9CF39;<!--{/if}-->">
	<table cellpadding="0" cellspacing="0" class="ftSubbtn">
		<tr>
			<td><a href="<!--{$smarty.const.ROOT_URLPATH}-->" accesskey="0" class="conL">HOME</a></td>
			<td><a href="<!--{$smarty.const.ROOT_URLPATH}-->info/" class="conR">利用規約</a></td>
		</tr>
		<tr>
			<td><a href="<!--{$smarty.const.ROOT_URLPATH}-->order/" class="conL">特定商取引法に基づく表示</a></td>
			<td><a href="<!--{$smarty.const.ROOT_URLPATH}-->guide/privacy.php" class="conR">ﾌﾟﾗｲﾊﾞｼｰﾎﾟﾘｼｰ</a></td>
		</tr>
		<tr>
			<td><a href="<!--{$smarty.const.ROOT_URLPATH}-->faq/" class="conL">よくあるご質問</a></td>
			<td><a href="<!--{$smarty.const.ROOT_URLPATH}-->contact/" class="conR">お問い合わせ</a></td>
		</tr>
	</table>

	<div class="copy">
		copyright &copy; SOYAFARM. All rights reserved.
	</div><!--//copy-->
</div>