<!--{printXMLDeclaration}--><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<!--{$smarty.const.CHAR_CODE}-->" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<meta name="robots" content="noindex,nofollow" />
<link rel="stylesheet" href="<!--{$TPL_URLPATH}-->css/admin_contents.css" type="text/css" media="all" />
<link rel="stylesheet" href="<!--{$TPL_URLPATH}-->css/admin_file_manager.css" type="text/css" media="all" />
<link rel="stylesheet" href="<!--{$TPL_URLPATH}-->css/jquery/jquery-ui-1.10.3.custom.css" type="text/css" media="all" />
<link rel="stylesheet" href="<!--{$smarty.const.ROOT_URLPATH}-->css/superTables.css" type="text/css" media="all" />
<!--{if $tpl_mainno eq "basis" && $tpl_subno eq "index"}-->
<!--{if ($smarty.server.HTTPS != "") && ($smarty.server.HTTPS != "off")}-->
<script type="text/javascript" src="https://maps-api-ssl.google.com/maps/api/js?sensor=false"></script>
<!--{else}-->
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<!--{/if}-->
<script type="text/javascript">//<![CDATA[
    var map = new google.maps.Map(document.getElementById("maps"), {"zoom":"3"});
//]]>
</script>
<!--{/if}-->
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/site.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/jquery/jquery-1.9.1.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/admin.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/file_manager.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/jquery/jquery-spin.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->user_data/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/jquery/jquery-ui-1.10.3.custom.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/jquery/jquery.ui.datepicker-ja.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/dialogs.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/superTables.js"></script>
<title><!--{$smarty.const.ADMIN_TITLE}--></title>
<script type="text/javascript">//<![CDATA[
    <!--{$tpl_javascript}-->
    $(function(){
        <!--{$tpl_onload}-->
    });
//]]>
</script>

<style TYPE="text/css">
<!--
p.faq {
    float: right;
    padding-right: 10px;
}
-->
</style>
</head>

<body class="<!--{if strlen($tpl_authority) >= 1}-->authority_<!--{$tpl_authority}--><!--{/if}-->">
<!--{$GLOBAL_ERR}-->
<noscript>
    <p>JavaScript を有効にしてご利用下さい.</p>
</noscript>

<div id="container">
<a name="top"></a>

<!--{if $smarty.const.ADMIN_MODE == '1'}-->
<div id="admin-mode-on">ADMIN_MODE ON</div>
<!--{/if}-->

<!--{* ▼HEADER *}-->
<div id="header">
    <div id="header-contents">
        <div id="logo"><a href="<!--{$smarty.const.ADMIN_HOME_URLPATH}-->"><img src="<!--{$TPL_URLPATH}-->img/header/logo.gif" width="120" alt="通販の神様 管理画面" /></a></div>
        <div id="site-check">
            <p>
            	<img src="<!--{$TPL_URLPATH}-->img/header/icon_user.gif" /><span>ログイン中&nbsp;:&nbsp;<!--{* ログイン名 *}--><!--{$smarty.session.login_name|h}--></span>&nbsp;様&nbsp;
            	<a href="<!--{$smarty.const.HTTP_URL}--><!--{$smarty.const.DIR_INDEX_PATH}-->"<!--{* class="btn-tool-format"*}--> target="_blank"><img src="<!--{$TPL_URLPATH}-->img/header/icon_shop.gif" /><span>ショップ表示</span></a>
            	<a href="<!--{$smarty.const.ADMIN_LOGOUT_URLPATH}-->"<!--{* class="btn-tool-format"*}-->><img src="<!--{$TPL_URLPATH}-->img/header/icon_logout.gif" />ログアウト</a>
            </p>
        </div>
    </div>
</div>
<!--{* ▲HEADER *}-->

<!--{* ▼NAVI *}-->
<div id="navi-wrap">
    <ul id="navi" class="clearfix">
        <!--{if $tpl_critical_menu == $smarty.const.CRITICAL_MENU_ON}-->
        <li id="navi-order" class="<!--{if $tpl_mainno eq "order"}-->on<!--{/if}-->">
            <a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>受注関連</span></a>
        </li>
        <li id="navi-customer" class="<!--{if $tpl_mainno eq "customer"}-->on<!--{/if}-->">
            <a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->customer/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>顧客関連</span></a>
        </li>
        <!--{/if}-->
        <li id="navi-products" class="<!--{if $tpl_mainno eq "products"}-->on<!--{/if}-->">
            <a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->products/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>商品関連</span></a>
        </li>
        <li id="navi-design" class="<!--{if $tpl_mainno eq "design"}-->on<!--{/if}-->">
            <a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>デザイン関連</span></a>
        </li>
        <!--{if $tpl_critical_menu == $smarty.const.CRITICAL_MENU_ON}-->
        <li id="navi-basis" class="<!--{if $tpl_mainno eq "basis"}-->on<!--{/if}-->">
            <a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->basis/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span class="level1">その他</span></a>
        </li>
        <!--{/if}-->
        <!--{* メニュー移動
        <li id="navi-total" class="<!--{if $tpl_mainno eq "total"}-->on<!--{/if}-->">
            <a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->total/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>売上集計</span></a>
        </li>
        <li id="navi-mail" class="<!--{if $tpl_mainno eq "mail"}-->on<!--{/if}-->">
            <a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->mail/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>メルマガ管理</span></a>
        </li>
        <li id="navi-contents" class="<!--{if $tpl_mainno eq "contents"}-->on<!--{/if}-->">
            <a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->contents/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>コンテンツ管理</span></a>
        </li>
        <li id="navi-system" class="<!--{if $tpl_mainno eq "system"}-->on<!--{/if}-->">
            <a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->system/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>システム設定</span></a>
        </li>
        <li id="navi-ownersstore" class="<!--{if $tpl_mainno eq "ownersstore"}-->on<!--{/if}-->">
            <a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->ownersstore/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>オーナーズストア</span></a>
        </li>
        *}-->
    </ul>
</div>
<!--{* ▲NAVI *}-->

<!--{* ▼CONTENTS *}-->
<div id="contents" class="clearfix">

    <!-- サイドナビ -->
    <!--{if $tpl_subnavi != ''}-->
    <!--{include file=$tpl_subnavi}-->
    <!--{elseif $objDisplay->view->_smarty->template_exists("$tpl_mainno/subnavi.tpl")}-->
    <!--{include file="$tpl_mainno/subnavi.tpl"}-->
    <!--{/if}-->

    <!-- メイン -->
    <!--{include file=$tpl_mainpage}-->

</div>
<!--{* ▲CONTENTS *}-->

<!--{* ▼FOOTER *}-->
<div id="footer">
	<div id="topagetop">
	    <p class="sites"><a href="#top"><img src="<!--{$TPL_URLPATH}-->img/common/btn_pagetop.gif" alt="トップページへ" /></a></p>
	</div>

    <div id="footer-contents">
        <div id="copyright">Copyright &copy; 2013 IQUEVE CO.,LTD. All Rights Reserved.</div>
    </div>
</div>
<!--{* ▲FOOTER *}-->

</div>
</body>
</html>
