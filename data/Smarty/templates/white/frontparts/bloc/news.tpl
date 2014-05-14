<!--{if count($arrNews) > 0}-->
<div id="topInfo">
	<h2 class="dyn"><img src="<!--{$TPL_URLPATH}-->img/rohto/home_news_title.gif" alt="ロート通販からのお知らせ" width="210" height="45"></h2>
	<!--{section name=data loop=$arrNews max=3}-->
	<!--{assign var="date_array" value="-"|explode:$arrNews[data].news_date_disp}-->
	<dl class="news">
		<dt><!--{$date_array[0]}-->/<!--{$date_array[1]}-->/<!--{$date_array[2]}--></dt>
		<dt>
			<!--{if $arrNews[data].news_url}-->
			<a href="<!--{$arrNews[data].news_url}-->"<!--{if $arrNews[data].link_method eq "2"}--> target="_blank"<!--{/if}-->><!--{$arrNews[data].news_title|h|nl2br}--></a>
			<!--{else}-->
			<strong class="red"><!--{$arrNews[data].news_title|h|nl2br}--></strong>
			<!--{/if}-->
		</dt>
		<dd><!--{$arrNews[data].news_comment|h|nl2br}--></dd>
	</dl>
	<!--{/section}-->
	<a href="<!--{$smarty.const.ROOT_URLPATH}-->contents/topiclist.php"><img src="<!--{$TPL_URLPATH}-->img/rohto/home_news_list.gif" alt="お知らせ一覧を見る" width="210" height="12" class="swp"></a>
</div><!--／topInfo-->
<!--{/if}-->
