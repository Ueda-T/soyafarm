<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=shift_jis">
<meta name="copyright" content="Rohto Pharmaceutical Co., Ltd.">
<title><!--{if $tpl_subtitle|strlen >= 1}--><!--{$tpl_subtitle|h}-->|<!--{elseif $tpl_title|strlen >= 1}--><!--{$tpl_title|h}-->|<!--{/if}-->ロート通販モバイル</title>
<!--{* ▼Head COLUMN*}-->
<!--{if $arrPageLayout.HeadNavi|@count > 0}-->
<!--{* ▼上ナビ *}-->
<!--{foreach key=HeadNaviKey item=HeadNaviItem from=$arrPageLayout.HeadNavi}-->
<!--{* ▼<!--{$HeadNaviItem.bloc_name}--> ここから*}-->
<!--{if $HeadNaviItem.php_path != ""}-->
<!--{include_php file=$HeadNaviItem.php_path items=$HeadNaviItem}-->
<!--{else}-->
<!--{include file=$HeadNaviItem.tpl_path items=$HeadNaviItem}-->
<!--{/if}-->
<!--{* ▲<!--{$HeadNaviItem.bloc_name}--> ここまで*}-->
<!--{/foreach}-->
<!--{* ▲上ナビ *}-->
<!--{/if}-->
<!--{* ▲Head COLUMN*}-->
<!--{$tpl_arrCategory.metatag}-->
<!--{$arrBrand.metatag}-->
<!--{if $tpl_metatag}-->
<!--{$tpl_metatag}-->
<!--{else}-->
<!--{if $arrPageLayout.author|strlen >= 1}-->
<meta name="author" content="<!--{$arrPageLayout.author|h}-->" />
<!--{/if}-->
<!--{if $arrPageLayout.description|strlen >= 1}-->
<meta name="description" content="<!--{$arrPageLayout.description|h}-->" />
<!--{/if}-->
<!--{if $arrPageLayout.keyword|strlen >= 1}-->
<meta name="keywords" content="<!--{$arrPageLayout.keyword|h}-->" />
<!--{/if}-->
<!--{/if}-->
</head>
<!-- ▼ ＢＯＤＹ部 スタート -->
<!--{include file='./site_main.tpl'}-->
<!-- ▲ ＢＯＤＹ部 エンド -->
</html>
