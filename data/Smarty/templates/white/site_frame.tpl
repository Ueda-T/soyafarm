<!--{printXMLDeclaration}--><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<!--{$smarty.const.CHAR_CODE}-->" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta name="copyright" content="Rohto Pharmaceutical Co., Ltd.">
<title><!--{if $tpl_subtitle|strlen >= 1}--><!--{$tpl_subtitle|h}--><!--{elseif $tpl_title|strlen >= 1}--><!--{$tpl_title|h}--><!--{/if}-->【<!--{$arrSiteInfo.shop_name|h}-->】</title>
<link rel="stylesheet" href="<!--{$TPL_URLPATH}-->css/import.css" type="text/css" media="all" />
<link rel="stylesheet" type="text/css" href="<!--{$TPL_URLPATH}-->css/fontsize-m.css" media="screen,tv,projection,print" />
<link rel="alternate stylesheet" type="text/css" href="<!--{$TPL_URLPATH}-->css/fontsize-s.css" media="screen,tv,projection,print" title="s" />
<link rel="alternate stylesheet" type="text/css" href="<!--{$TPL_URLPATH}-->css/fontsize-l.css" media="screen,tv,projection,print" title="l" />
<link rel="alternate" type="application/rss+xml" title="RSS" href="<!--{$smarty.const.HTTP_URL}-->rss/<!--{$smarty.const.DIR_INDEX_PATH}-->" />
<!--{if $tpl_mainno}-->
<link rel="stylesheet" href="<!--{$TPL_URLPATH}-->css/mypage.css" type="text/css" media="all" />
<!--{/if}-->
<!--{*  ダイアログ用 *}-->
<link rel="stylesheet" href="<!--{$smarty.const.ROOT_URLPATH}-->css/superTables.css" type="text/css" media="all" />

<!--{if $tpl_page_category == "abouts"}-->
<!--{if ($smarty.server.HTTPS != "") && ($smarty.server.HTTPS != "off")}-->
<script type="text/javascript" src="https://maps-api-ssl.google.com/maps/api/js?sensor=false"></script>
<!--{else}-->
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<!--{/if}-->
<!--{/if}-->
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/site.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/jquery.autotab-1.1b.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/jquery-spin.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/jquery_topcatch.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/jquery.colorbox.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/rollover.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/scroll.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/font_switch.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/onload.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/AC_RunActiveContent.js" ></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/common.js"></script>
<!--{assign var=top value="`$smarty.const.ROOT_URLPATH`index.php"}-->
<!--{if $smarty.server.PHP_SELF == $top}-->
<link rel="stylesheet" href="<!--{$TPL_URLPATH}-->css/index.css" type="text/css" media="all" />
<link rel="stylesheet" href="<!--{$TPL_URLPATH}-->css/main_img.css" type="text/css" media="all" />
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/main_img.js"></script>
<!--{/if}-->

<!--{*  ダイアログ用 *}-->
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/dialogs.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/superTables.js"></script>

<script type="text/javascript">
window.onload = function() {
    rOver();
    //displayLocalNavi();
}
</script>
<!--{assign var=top value="`$smarty.const.ROOT_URLPATH`index.php"}-->
<!--{if $smarty.server.PHP_SELF == $top}-->
<script type="text/javascript">
$(document).ready(function(){
    $('a#mainVisualPromiseButton').colorbox({opacity:0.6});
});
</script>
<!--{/if}-->

<!--{*if $tpl_javascript*}-->
<script type="text/javascript">//<![CDATA[
<!--{$tpl_javascript}-->
$(function(){
    <!--{$tpl_onload}-->
});
//]]>
</script>
<!--{*/if*}-->
<!--{* ▼Head COLUMN*}-->
<!--{if $arrPageLayout.HeadNavi|@count > 0}-->
    <!--{* ▼上ナビ *}-->
    <!--{foreach key=HeadNaviKey item=HeadNaviItem from=$arrPageLayout.HeadNavi}-->
        <!--{* ▼<!--{$HeadNaviItem.bloc_name}--> ここから*}-->
        <!--{if $HeadNaviItem.php_path != ""}-->
            <!--{include_php file=$HeadNaviItem.php_path}-->
        <!--{else}-->
            <!--{include file=$HeadNaviItem.tpl_path}-->
        <!--{/if}-->
        <!--{* ▲<!--{$HeadNaviItem.bloc_name}--> ここまで*}-->
    <!--{/foreach}-->
    <!--{* ▲上ナビ *}-->
<!--{/if}-->
<!--{* ▲Head COLUMN*}-->
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/heightLine.js"></script>
<!--{$tpl_arrCategory.metatag}-->
<!--{$arrBrand.metatag}-->
<!--{if $tpl_metatag}-->
<!--{$tpl_metatag}-->
<!--{else}-->
<!--{if $arrPageLayout.description|strlen >= 1}--><meta name="description" content="<!--{$arrPageLayout.description|h}-->" /><!--{/if}-->
<!--{if $arrPageLayout.keyword|strlen >= 1}--><meta name="keywords" content="<!--{$arrPageLayout.keyword|h}-->" /><!--{/if}-->
<!--{if $arrPageLayout.author|strlen >= 1}--><meta name="author" content="<!--{$arrPageLayout.author|h}-->" /><!--{/if}-->
<!--{/if}-->
</head>

<!-- ▼BODY部 スタート -->
<!--{include file='./site_main.tpl'}-->
<!-- ▲BODY部 エンド -->

</html>
