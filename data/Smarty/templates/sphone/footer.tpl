<!--{assign var=top value="`$smarty.const.ROOT_URLPATH`index.php"}-->
<!--{if $smarty.server.PHP_SELF != $top}-->
<!--{include file="`$smarty.const.SMARTPHONE_TEMPLATE_REALDIR`frontparts/bloc/main_navi.tpl"}-->
<!--{/if}-->
<div id="spFooter" style="margin:0;">
	<table cellpadding="0" cellspacing="0" class="ftSubbtn">
		<tr>
			<td><a href="<!--{$smarty.const.ROOT_URLPATH}-->" accesskey="0" class="conL">HOME</a></td>
			<td><a href="<!--{$smarty.const.ROOT_URLPATH}-->contents/law.php" class="conR">利用規約</a></td>
		</tr>
		<tr>
			<td><a href="<!--{$smarty.const.ROOT_URLPATH}-->guide/privacy.php" class="conL">ﾌﾟﾗｲﾊﾞｼｰﾎﾟﾘｼｰ</a></td>
			<td><a href="<!--{$smarty.const.ROOT_URLPATH}-->order/" class="conR">特定商取引法に基づく表示</a></td>
		</tr>
		<tr>
			<td><a href="<!--{$smarty.const.ROOT_URLPATH}-->contents/faq.php" class="conL">FAQ(よくある質問)</a></td>
			<td><a href="<!--{$smarty.const.ROOT_URLPATH}-->contact/" class="conR">お問い合わせ</a></td>
		</tr>
	</table>

	<div class="rohto">ﾛｰﾄ製薬株式会社通販事業部<br>
		<img src="<!--{$TPL_URLPATH}-->img/rohto/icon_tel.gif" alt="ﾌﾘｰﾀﾞｲﾔﾙ" width="24" height="14"><span style="font-size:1.333em; font-weight:bold;"><a href="tel:0120-880-610" style="color:#005aac;">0120-880-610</a></span><br>
		9:00～21:00(年末年始を除く)<br>
		<img src="<!--{$TPL_URLPATH}-->img/rohto/icon_caution.gif" alt="注意" width="13" height="9">おかけ間違えのないようご注意ください。
	</div><!--//rohto-->

	<div class="copy">
		copyright (c) Rohto Pharmaceutical Co., Ltd. all rights reserved.
	</div><!--//copy-->
</div>