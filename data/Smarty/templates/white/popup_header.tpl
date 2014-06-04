<!--{printXMLDeclaration}--><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<!--{$smarty.const.CHAR_CODE}-->" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<link rel="stylesheet" href="<!--{$TPL_URLPATH}-->css/import.css" type="text/css" media="all" />
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/site.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/jquery.autotab-1.1b.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/jquery-spin.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/jquery_topcatch.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/jquery.colorbox.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/common.js"></script>
<title><!--{$arrSiteInfo.shop_name}-->/<!--{$subtitle|h}--></title>
<script type="text/javascript">
window.onload = function() {
    rOver();
    //displayLocalNavi();
}
</script>
<script type="text/javascript">//<![CDATA[
    <!--{$tpl_javascript}-->
    $(function(){
        <!--{$tpl_onload}-->
    });
//]]>
</script>
</head>

<body class="popupPage">
<noscript>
    <p><em>JavaScriptを有効にしてご利用下さい.</em></p>
</noscript>

<a name="top" id="top"></a>

<!--▼CONTENTS-->
<!--{if !$disable_wincol}--><div id="windowcolumn"><!--{/if}-->
