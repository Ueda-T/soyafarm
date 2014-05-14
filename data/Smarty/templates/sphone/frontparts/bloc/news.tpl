<!--{if count($arrNews) > 0}-->
<!--{* ▼新着情報 *}-->
<h2 class="spInx"><img src="<!--{$TPL_URLPATH}-->img/rohto/icon_news.gif" width="29" height="18">お知らせ</h2>
<div class="inxNews">
	<table width="100%" cellspacing="0" cellpadding="1" border="0">
		<!--{section name=data loop=$arrNews max=3}-->
		<tr>
			<!--{if $smarty.section.data.iteration%2 == 1}-->
			<td bgcolor="#ffffff" bordercolor="#ffffff">
			<!--{else}-->
			<td bgcolor="#eaeaea" bordercolor="#eaeaea">
			<!--{/if}-->
				<font size="-1">
					<!--{$arrNews[data].news_date_disp|date_format:"%Y/%m/%d"}--></span><br />
					<!--{$arrNews[data].news_title|h}-->
				</font>
			</td>
		</tr>
		<!--{/section}-->
	</table>
	<p class="btn"><a class="btnGray" href="<!--{$smarty.const.ROOT_URLPATH}-->contents/topiclist.php">お知らせ一覧を見る</a></p>
</div>
<!--{* ▲新着情報 *}-->
<!--{/if}-->
