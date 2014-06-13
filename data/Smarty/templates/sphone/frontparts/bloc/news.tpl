<!--{if count($arrNews) > 0}-->
<!--{* ▼新着情報 *}-->
<h2 class="spInx">お知らせ</h2>
<div class="inxNews">
	<table width="100%" cellspacing="0" cellpadding="1" border="0">
		<!--{section name=data loop=$arrNews max=3}-->
		<tr>
			<td bgcolor="#ffffff" bordercolor="#ffffff" style="border-bottom:1px dotted #CCC;">
				<font size="-1">
					<!--{$arrNews[data].news_date_disp|date_format:"%Y/%m/%d"}--></span><br />
					<!--{$arrNews[data].news_title|h}-->
				</font>
			</td>
		</tr>
		<!--{/section}-->
	</table>
	<p class="btn mr10"><a class="btnGray" href="<!--{$smarty.const.ROOT_URLPATH}-->contents/topiclist.php">お知らせ一覧を見る</a></p>
</div>
<!--{* ▲新着情報 *}-->
<!--{/if}-->
