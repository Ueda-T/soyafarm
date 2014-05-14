<!--{assign var=top value="`$smarty.const.ROOT_URLPATH`index.php"}-->
<div id="spHeader">
<a rel="external" href="<!--{$smarty.const.HTTP_URL}-->"><img src="<!--{$TPL_URLPATH}-->img/rohto/head_logo.gif" alt="ROHTO ONLINE SHOP" width="320" height="51"></a>
</div><!--//spHeader-->
<!--{if !$tpl_mypageno && $smarty.server.PHP_SELF == $top}-->
<p class="copy">美と健康を応援するﾛｰﾄ製薬の通販ｻｲﾄ</p>
<!--{/if}-->
<!-- //header -->

<header class="global_header clearfix">
<h1><a rel="external" href="<!--{$smarty.const.HTTP_URL}-->"></a></h1>
<div class="header_utility">
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
</div>
</header>
