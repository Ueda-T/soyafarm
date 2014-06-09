<header class="clearfix">
<h1><a rel="external" href="<!--{$smarty.const.HTTP_URL}-->"><img src="<!--{$TPL_URLPATH}-->img/common/img_logo.gif" width="40%;" /></a></h1>
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
