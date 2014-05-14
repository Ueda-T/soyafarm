<!--▼HEADER-->
<!--{assign var=top value="`$smarty.const.ROOT_URLPATH`index.php"}-->
<!--{if $smarty.server.PHP_SELF == $top}-->
<div style="background-color:#dfedf5">
<marquee bgcolor="#003b9b" loop="infinite">
<font size="-1" color="#ffffff">美と健康を応援するﾛｰﾄ製薬の通販ｻｲﾄ</font>
</marquee>
<table width="100%" border="0" cellpadding="1" cellspacing="0" bgcolor="#dfedf5" bordercolor="#dfedf5">
<tr>
<td align="center">
<font size="-2">
<font color="green">[emoji:e59]</font><a href="<!--{$smarty.const.ROOT_URLPATH}-->contents/welcome.php">初めての方へ</a>|<font color="maroon">[emoji:e9]</font><a href="<!--{$smarty.const.URL_MYPAGE_TOP}-->?<!--{$smarty.const.SID}-->">ﾏｲﾍﾟｰｼﾞ</a>
</font>
</td>
</tr>
</table>
</div>
<!--{else}-->
<div align="center">
<img src="<!--{$TPL_URLPATH}-->img/rohto/header.gif" alt="ﾛｰﾄ通販ﾓﾊﾞｲﾙ" width="100%">
</div>
<!--{/if}-->

<!--{* ▼HeaderInternal COLUMN*}-->
<!--{if $arrPageLayout.HeaderInternalNavi|@count > 0}-->
    <!--{* ▼上ナビ *}-->
    <!--{foreach key=HeaderInternalNaviKey item=HeaderInternalNaviItem from=$arrPageLayout.HeaderInternalNavi}-->
        <!-- ▼<!--{$HeaderInternalNaviItem.bloc_name}--> -->
        <!--{if $HeaderInternalNaviItem.php_path != ""}-->
            <!--{include_php file=$HeaderInternalNaviItem.php_path items=$HeaderInternalNaviItem}-->
        <!--{else}-->
            <!--{include file=$HeaderInternalNaviItem.tpl_path items=$HeaderInternalNaviItem}-->
        <!--{/if}-->
        <!-- ▲<!--{$HeaderInternalNaviItem.bloc_name}--> -->
    <!--{/foreach}-->
    <!--{* ▲上ナビ *}-->
<!--{/if}-->
<!--{* ▲HeaderInternal COLUMN*}-->

<!--{* ▼タイトル *}-->
<!--{if !$tpl_notitle}-->
<!--{if $tpl_title != "" || $tpl_subtitle != ""}-->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<th bgcolor="#003b9b" align="left"><h2><font size="-1" color="#ffffff"><!--{if $tpl_subtitle != ""}--><!--{$tpl_subtitle|h}--><!--{else}--><!--{$tpl_title|h}--><!--{/if}--></font></h2></th>
</tr>
</table>
<!--{/if}-->
<!--{/if}-->
<!--{* ▲タイトル *}-->
<!--▲HEADER-->
