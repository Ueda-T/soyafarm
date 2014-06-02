<!--{if count($arrNews) > 0}-->
<div class="box">
	<h2><img src="<!--{$TPL_URLPATH}-->img/soyafarm/h1_news.gif" alt="What's new" width="700" height="30"></h2>
	<ul id="news">
	<!--{section name=data loop=$arrNews max=3}-->
	<!--{assign var="date_array" value="-"|explode:$arrNews[data].news_date_disp}-->
	<li>
	<h2><!--{$date_array[0]}-->年<!--{$date_array[1]}-->月<!--{$date_array[2]}-->日</h2>
	<h3 class="mb10">
		<!--{if $arrNews[data].news_url}-->
		<a href="<!--{$arrNews[data].news_url}-->"<!--{if $arrNews[data].link_method eq "2"}--> target="_blank"<!--{/if}-->><!--{$arrNews[data].news_title|h|nl2br}--></a>
		<!--{else}-->
		<!--{$arrNews[data].news_title|h|nl2br}-->
		<!--{/if}-->
	</h3>
	<p><!--{$arrNews[data].news_comment|h|nl2br}--></p>
	</li>
	<!--{/section}-->
	</ul>
	<p class="order"></p>
<!--{*
	<a href="<!--{$smarty.const.ROOT_URLPATH}-->contents/topiclist.php"><img src="<!--{$TPL_URLPATH}-->img/rohto/home_news_list.gif" alt="お知らせ一覧を見る" width="210" height="12" class="swp"></a>
*}-->
</div>
<!--{/if}-->