<html lang="ja">
<head>
<meta charset="UTF-8"> 
<meta http-equiv="Content-Type" content="text/html; charset=<!--{$smarty.const.CHAR_CODE}-->" />

<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=0" />
<meta name="format-detection" content="telephone=no">
<!--{* 共通CSS *}-->
<link rel="stylesheet" media="only screen" href="<!--{$TPL_URLPATH}-->css/import.css" />

<!--{if $tpl_page_category == "abouts"}-->
<!--{if ($smarty.server.HTTPS != "") && ($smarty.server.HTTPS != "off")}-->
<script src="https://maps-api-ssl.google.com/maps/api/js?sensor=false"></script>
<!--{else}-->
<script src="http://maps.google.com/maps/api/js?sensor=false"></script>
<!--{/if}-->
<!--{/if}-->
<script src="<!--{$smarty.const.ROOT_URLPATH}-->js/navi.js"></script>
<script src="<!--{$smarty.const.ROOT_URLPATH}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/site.js"></script>
<script src="<!--{$TPL_URLPATH}-->js/jquery-1.7.2.min.js"></script>
<script src="<!--{$TPL_URLPATH}-->js/jquery.biggerlink.js"></script>
<script src="<!--{$TPL_URLPATH}-->js/smoothscroll.js" type="text/javascript"></script>
<script src="<!--{$TPL_URLPATH}-->js/linkbox.js" type="text/javascript"></script>

<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/jquery.imagesloaded.min.js" charset="utf8"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/roat.sp.mainvisual.js" charset="utf8"></script>

<script>//<![CDATA[
    $(function(){
        $('.header_navi li,.recomendblock, .list_area, .newslist li, .bubbleBox, .arrowBox, .category_body, .navBox li,#mypagecolumn .cartitemBox').biggerlink();
    });
//]]>
</script>
<script src="<!--{$TPL_URLPATH}-->js/btn.js"></script>
<script src="<!--{$TPL_URLPATH}-->js/barbutton.js"></script>
<script src="<!--{$TPL_URLPATH}-->js/category.js"></script>
<script src="<!--{$TPL_URLPATH}-->js/news.js"></script>

<!--{* スマートフォンカスタマイズ用CSS *}-->
<!--{*
<link rel="stylesheet" media="only screen" href="<!--{$TPL_URLPATH}-->css/jquery.mobile-1.0a3.css" />
*}-->
<link rel="stylesheet" media="screen" href="<!--{$TPL_URLPATH}-->js/jquery.facebox/facebox.css" />

<!--{* スマートフォンカスタマイズ用JS *}-->
<script src="<!--{$TPL_URLPATH}-->js/config.js"></script>
<!--{*
<script src="<!--{$TPL_URLPATH}-->js/jquery.mobile-1.0a3.min.js"></script>
*}-->
<script src="<!--{$TPL_URLPATH}-->js/jquery.autoResizeTextAreaQ-0.1.js"></script>
<script src="<!--{$TPL_URLPATH}-->js/jquery.flickslide.js"></script>
<script src="<!--{$TPL_URLPATH}-->js/favorite.js"></script>

<title><!--{if $tpl_subtitle|strlen >= 1}--><!--{$tpl_subtitle|h}--><!--{elseif $tpl_title|strlen >= 1}--><!--{$tpl_title|h}--><!--{/if}-->【ソヤファームクラブ】</title>
<!--{* iPhone用アイコン画像 *}-->
<link rel="apple-touch-icon" href="<!--{$TPL_URLPATH}-->img/common/apple-touch-icon.png" />

<script type="text/javascript">//<![CDATA[
    <!--{$tpl_javascript}-->
    $(function(){
    <!--{$tpl_onload}-->
});
//]]>
</script>



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

<!-- ▼BODY部 スタート -->
<!--{include file='./site_main.tpl'}-->
<!-- ▲BODY部 エンド -->

</html>
