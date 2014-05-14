<!--{if count($arrNews) > 0}-->
<h2><a name="1" id="1"><img src="<!--{$TPL_URLPATH}-->img/rohto/bar_news.gif" alt="お知らせ ﾛｰﾄ通販" width="100%"></a></h2>
<table width="100%" border="0" cellpadding="1" cellspacing="0">
<!--{section name=data loop=$arrNews max=3}-->
<!--{assign var="date_array" value="-"|explode:$arrNews[data].news_date_disp}-->
<tr>
<!--{if $smarty.section.data.iteration%2 == 1}-->
<td bgcolor="#ffffff" bordercolor="#ffffff">
<!--{else}-->
<td bgcolor="#eaeaea" bordercolor="#eaeaea">
<!--{/if}-->
<font size="-1">
<!--{$arrNews[data].news_date_disp|date_format:"%Y/%m/%d"}--><br>
<!--{$arrNews[data].news_title|h}-->
</font></td>
</tr>
<!--{/section}-->
</table>

<div align="right"><font size="-1">
<a href="<!--{$smarty.const.ROOT_URLPATH}-->contents/topiclist.php">もっと見る≫</a><br>
</font></div>

<div align="right"><font size="-1">
<a href="#top"><font color="#003b9b">ﾍﾟｰｼﾞTOP▲</font></a>
</font></div>
<!--{/if}-->