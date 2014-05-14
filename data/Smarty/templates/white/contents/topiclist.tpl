<h1 class="page_title"><!--{$tpl_title}--></h1>

<div class="newsList">
<!--{section name=data loop=$arrNews}-->
	<!--{assign var="date_array" value="-"|explode:$arrNews[data].news_date_disp}-->
	<div class="news">
		<h2>
		<!--{if $arrNews[data].news_url}-->
			<a href="<!--{$arrNews[data].news_url}-->"<!--{if $arrNews[data].link_method eq "2"}--> target="_blank"<!--{/if}-->><strong><!--{$arrNews[data].news_title|h|nl2br}--></strong></a>
		<!--{else}-->
		<strong><!--{$arrNews[data].news_title|h|nl2br}--></strong>
		<!--{/if}-->
		</h2>

		<dl>
			<dd class="day"><!--{$date_array[0]}-->/<!--{$date_array[1]}-->/<!--{$date_array[2]}--></dd>
			<dd><!--{$arrNews[data].news_comment|h|nl2br}--></dd>
			<!--{if $arrNews[data].news_url}-->
<!--{*
			<dd><a href="<!--{$arrNews[data].news_url}-->"<!--{if $arrNews[data].link_method eq "2"}--> target="_blank"<!--{/if}-->>詳しくはこちらをご覧ください。</a></dd>
*}-->
			<!--{/if}-->
		</dl>
	</div>
<!--{/section}-->
</div>
