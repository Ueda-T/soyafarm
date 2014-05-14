<!--{assign var=top value="`$smarty.const.ROOT_URLPATH`index.php"}-->
<!--{if $smarty.server.PHP_SELF == $top}-->
<div style="background-color:#eaeaea">
<table width="100%" border="0" cellpadding="1" cellspacing="0" bgcolor="#eaeaea" bordercolor="#eaeaea">
<tr>
<td align="center"><font size="-1">
ﾛｰﾄ製薬株式会社通販事業部<br>
<font color="red">[emoji:122]</font><a href="tel:0120880610">0120-880-610</a><br>
9:00～21:00<br>
(年末年始を除く)<br>
おかけ間違えのないようご注意ください。
</font></td>
</tr>
</table>
</div>
<!--{else}-->
<!--{include file="`$smarty.const.MOBILE_TEMPLATE_REALDIR`frontparts/bloc/information_subpage.tpl"}-->
<!--{/if}-->

<div style="background-color:#cccccc">
<!--{if $smarty.server.PHP_SELF == $top}-->
<table width="100%" border="0" cellpadding="1" cellspacing="0" bgcolor="#cccccc" bordercolor="#cccccc">
<tr>
<td align="center"><font size="-1">
	<!--{0|numeric_emoji}--><a href="#top" accesskey="0"><font color="#003b9b">HOME</font></a>
</font></td>
</tr>
</table>
<!--{else}-->
<table width="100%" border="0" cellpadding="1" cellspacing="0" bgcolor="#cccccc" bordercolor="#cccccc">
<tr>
<td align="center">
<font size="-1"><!--{0|numeric_emoji}--><a href="<!--{$smarty.const.ROOT_URLPATH}-->" accesskey="0"><font color="#003b9b">HOME</font></a><!--{2|numeric_emoji}--><a href="#top" accesskey="2"><font color="#003b9b">上</font></a><!--{8|numeric_emoji}--><a href="#footer" accesskey="8"><font color="#003b9b">下</font></a>
</font></td>
</tr>
</table>
<!--{/if}-->

<div align="center" id="footer">
<img src="<!--{$TPL_URLPATH}-->img/rohto/footer.gif" alt="copyright(c) Rohto Pharmaceutical Co., Ltd. all rights reserved." width="100%">
</div>
</div>
</div>

<!--{*
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<td align="center" bgcolor="#666666"><font color="#ffffff" size="-2">Copyright &copy;
<!--{if $smarty.const.RELEASE_YEAR !=  $smarty.now|date_format:"%Y"}-->
    <!--{$smarty.const.RELEASE_YEAR}-->-
<!--{/if}-->
<!--{$smarty.now|date_format:"%Y"}--> <!--{$arrSiteInfo.shop_name_eng|default:$arrSiteInfo.shop_name|h}--> All rights reserved.</font></td>
</tr>
</table>
*}-->
