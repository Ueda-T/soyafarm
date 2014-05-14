<table width="100%" border="0" cellpadding="1" cellspacing="0" class="list">
<!--{foreach from=$arrChildren key=i item=arrChild name=arrChild}-->
<!--{if $arrChild.has_children}-->
<!--{assign var=path value="`$smarty.const.ROOT_URLPATH`products/category_list.php"}-->
<!--{else}-->
<!--{assign var=path value="`$smarty.const.ROOT_URLPATH`products/list.php"}-->
<!--{/if}-->
<!--{if $smarty.foreach.arrChild.iteration%2 == 0}-->
<tr bgcolor="#eaeaea">
<!--{else}-->
<tr bgcolor="#ffffff">
<!--{/if}-->
<td><font size="-1">&gt;<a href="<!--{$path}-->?category_id=<!--{$arrChild.category_id}-->"><!--{$arrChild.category_name|h}--></a></font></td>
</tr>
<!--{/foreach}-->
</table>
<br>

<div align="right"><font size="-1">
<a href="#top"><font color="#003b9b">▲ページTOP</font></a>
</font></div>
<br>
