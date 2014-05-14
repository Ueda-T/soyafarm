<h2 class="spNaked"><!--{$tpl_title}--></h2>
<table width="100%" cellspacing="0" cellpadding="1" border="0">
<!--{section name=data loop=$arrNews max=10}-->
<!--{assign var="date_array" value="-"|explode:$arrNews[data].news_date_disp}-->
<tr>
<td bgcolor="#dfedf5" bordercolor="#dfedf5">
<h2><font size="-1">
<!--{$date_array[0]}-->/<!--{$date_array[1]}-->/<!--{$date_array[2]}-->
<strong><!--{$arrNews[data].news_title|h|nl2br}--></strong>
</font></h2>
</td>
</tr>
<td bgcolor="#ffffff" bordercolor="#ffffff"><font size="-1">
<!--{$arrNews[data].news_comment|h|nl2br}-->
<!--{if $arrNews[data].news_url}-->
<br>
<a href="<!--{$arrNews[data].news_url}-->"<!--{if $arrNews[data].link_method eq "2"}--> target="_blank"<!--{/if}-->>詳しくはこちらをご覧ください。</a>
<!--{/if}-->
</td>
</tr>
<tr>
<td bgcolor="#ffffff" bordercolor="#ffffff"><font size="-1" style="line-height:20%;">&nbsp;</font></td>
</tr>
<!--{/section}-->
</table>

<hr color="#dfedf5">

<font size="-1">
直前の画面に戻る場合は､携帯電話の｢戻る｣ﾎﾞﾀﾝを押すか､数字の0ｷｰを押してﾄｯﾌﾟﾍﾟｰｼﾞにお戻りください｡
</font>
<div align="right"><font size="-1">
<a href="#top"><font color="#003b9b">ﾍﾟｰｼﾞTOP▲</font></a>
</font></div>
<br>
