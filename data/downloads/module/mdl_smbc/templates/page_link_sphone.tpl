<!--{printXMLDeclaration}--><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<!--{$smarty.const.CHAR_CODE}-->" />

<meta name="viewport" content="width=320,maximum-scale=1.0,user-scalable=no">
<meta name="format-detection" content="telephone=no">
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<!--{* 共通CSS *}-->
<link rel="stylesheet" media="only screen" href="<!--{$TPL_URLPATH}-->css/import.css" type="text/css" />

<!--{if $tpl_page_category == "abouts"}-->
<!--{if ($smarty.server.HTTPS != "") && ($smarty.server.HTTPS != "off")}-->
<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>
<!--{else}-->
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<!--{/if}-->
<!--{/if}-->
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/site.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery-1.4.2.min.js"></script>
<script src="<!--{$TPL_URLPATH}-->js/jquery.biggerlink.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function(){
        $('#recomendarea div.recomendleft, #recomendarea div.recomendright, #undercolumn div.listrightblock, #whoboughtarea div.whoboughtleft, #whoboughtarea div.whoboughtright').biggerlink();
    });
</script>

<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/barbutton.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/category.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/news.js"></script>

<title><!--{$arrSiteInfo.shop_name|h}--><!--{if $tpl_subtitle|strlen >= 1}--> / <!--{$tpl_subtitle|h}--><!--{elseif $tpl_title|strlen >= 1}--> / <!--{$tpl_title|h}--><!--{/if}--></title>
<!--{if $arrPageLayout.author|strlen >= 1}-->
    <meta name="author" content="<!--{$arrPageLayout.author|h}-->" />
<!--{/if}-->
<!--{if $arrPageLayout.description|strlen >= 1}-->
    <meta name="description" content="<!--{$arrPageLayout.description|h}-->" />
<!--{/if}-->
<!--{if $arrPageLayout.keyword|strlen >= 1}-->
    <meta name="keywords" content="<!--{$arrPageLayout.keyword|h}-->" />
<!--{/if}-->
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
</head>

<!-- ▼BODY部 スタート -->
<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *}-->
<body onload="org=document.charset; document.charset='Shift_JIS'; document.form1.submit(); document.charset=org;">
<style type="text/css">
/* スマートフォン用グラデーションボタン
----------------------------------------------- */
/*.spbtn {
display: block;
margin: 0.5em auto 0.3em;
padding: 0.4em 0;
font: bold large helvetica;
text-shadow: 0px -1px 1px rgba(0,0,0,0.5);
vertical-align: middle;
text-align:center;
text-decoration: none;
color: #ffffff;
background-color: #666666;
background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(rgba(255,255,255,0.25)), to(rgba(0,0,0,0.2)), color-stop(0.5, rgba(255,255,255,0.05)), color-stop(0.51, rgba(0,0,0,0.05)));
border: solid 2px #666666;
-webkit-border-radius: 8px;
-webkit-box-shadow: 0px 0px 3px rgba(0,0,0,0.7);
}*/
.spbtn:before {
content: "";
display: block;
height: 0;
clear: both;
visibility: hidden;
}
/*a.spbtn {
width: 79%;
max-width: 236px;
}*/
input.spbtn {
width: 100%;
}

/* ボタン中（お問い合わせ、友達に教える）
----------------------------------------------- */
.spbtn-medeum {
    background-color: #aaaaaa;
    border: 1px solid #666666;
    color: #FFFFFF;
    display: block;
    font: bold 100% helvetica;
    margin: 0 auto;
    padding: 7px;
    text-shadow: 0 -1px 1px rgba(0, 0, 0, 0.5);
    background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(rgba(255,255,255,0.25)), to(rgba(0,0,0,0.2)), color-stop(0.5, rgba(255,255,255,0.05)), color-stop(0.51, rgba(0,0,0,0.05)));
    -webkit-border-radius: 6px;
    -webkit-box-shadow: 0px 0px 3px rgba(0,0,0,0.7);
/*text-shadow: 0px -1px 1px rgba(0,0,0,0.5);
-webkit-border-radius: 6px;
-webkit-box-shadow: 0px 0px 3px rgba(0,0,0,0.7);*/
}
a.spbtn-medeum {
width: 95%;
text-decoration:none;
text-align:center;
}
input.spbtn-medeum {
width: 100%;
}

.spbtn-medeum02 {
    background-color: #aaaaaa;
    border: 1px solid #666666;
    color: #FFFFFF;
    display: block;
    font: bold 100% helvetica;
    margin: 0 auto;
    padding: 7px;
    text-shadow: 0 -1px 1px rgba(0, 0, 0, 0.5);
    text-align:center;
        -webkit-border-radius: 6px;
/*text-shadow: 0px -1px 1px rgba(0,0,0,0.5);
-webkit-border-radius: 6px;
-webkit-box-shadow: 0px 0px 3px rgba(0,0,0,0.7);*/
}

/* ショッピング関連ボタン
----------------------------------------------- */
.spbtn-shopping {
    background-color: #6dc935;
    border: 1px solid #666666;
    color: #FFFFFF;
    display: block;
    font: bold 120% helvetica;
    margin: 0 auto;
    padding: 7px;
    text-shadow: 0 -1px 1px rgba(0, 0, 0, 0.5);
    background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(rgba(255,255,255,0.25)), to(rgba(0,0,0,0.2)), color-stop(0.5, rgba(255,255,255,0.05)), color-stop(0.51, rgba(0,0,0,0.05)));
    -webkit-border-radius: 6px;
    -webkit-box-shadow: 0px 0px 3px rgba(0,0,0,0.7);

/*display: block;
margin: 0.5em auto 0.3em;
font: bold 100% helvetica;
border: solid 1px #666666;
text-shadow: 0px -1px 1px rgba(0,0,0,0.5);
color: #ffffff;
background-color: #fc4743;
background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(rgba(255,255,255,0.25)), to(rgba(0,0,0,0.2)), color-stop(0.5, rgba(255,255,255,0.05)), color-stop(0.51, rgba(0,0,0,0.05)));
-webkit-border-radius: 6px;
-webkit-box-shadow: 0px 0px 3px rgba(0,0,0,0.7);*/
}
a.spbtn-shopping {
width: 95%;
text-decoration:none;
margin:0;
text-align:center;
}
input.spbtn-shopping {
width: 100%;
margin:0;
}

p.addbtn {
margin-bottom:20px;
}

/* footer
----------------------------------------------- */
#pagetop {
width: 100%;
margin: 10px 0 0;
text-align: right;
}
#pagetop a{
color:#4D576B;
font: normal 100% helvetica;
text-shadow: 0px 1px 1px #ffffff;
text-decoration: underline;
}
div#footer {
    margin: 0;
    padding: 20px 0 0;
    text-align: center;
}

/* footer information
----------------------------------------------- */
#footer-menu {
text-align: left;
font: bold 100% helvetica;
}
#footer-menu li{
background: #ffffff url(../img/common/chevron.png) no-repeat right center;
}
#footer-navi {
float:left;
list-style:none outside none;
margin: 0 auto 4px;
padding: 0;
text-align:center;
width: 100%;
border: none;
background-color: transparent;
}
#footer-navi > li{
display: inline;
color:#4D576B;
font: normal 70% helvetica;
background-color: transparent;
/*text-shadow: 0px 1px 1px #ffffff;*/
border: none;
/*border-right: 1px dotted #999999;*/
/*-webkit-border-radius: 0;*/
line-height: 1.8em;
margin: 0;
padding: 0;
}
#footer-navi > li:last-child{
border-right: none;
}
#footer-navi > li > a {
margin: 0;
padding: 0;
display: inline;
color:#4D576B;
text-decoration:underline;
}
#copyright {
margin: 7px 0px;
color: #4D576B;
font: normal 70% helvetica;
/*text-shadow: 0px 1px 1px #ffffff;*/
text-align: center;
}
#copyright a{
color: #4D576B;
text-decoration: under-line;
}

/* テーブル(リスト)
----------------------------------------------- */
ul {
background: #fff;
border: 1px solid #B4B4B4;
font: bold 100% 'Helvetica-Bold';
padding: 0;
margin: 10px 0;
text-align: left;
-webkit-border-radius: 8px;
}
ul li {
color: #999999;
border-top: 1px solid #B4B4B4;
list-style-type: none;
padding: 4px 6px;
}

ul#paymentP, ul#mypageT {
background: #fff;
border: 1px solid #B4B4B4;
font:normal 100% 'Helvetica';
padding: 0;
margin: 10px 0;
text-align: left;
-webkit-border-radius: 8px;
}

ul#paymentP li, ul#mypageT li {
color: #000000;
list-style-type: none;
padding: 4px 6px;
}

li:first-child {
border-top: 0;
-webkit-border-top-left-radius: 8px;
-webkit-border-top-right-radius: 8px;
}
li:last-child {
-webkit-border-bottom-left-radius: 8px;
-webkit-border-bottom-right-radius: 8px;
}

/* table角丸(共通)
----------------------------------------------- */
table {
width: 100%;
max-width: 300px;
margin: 10px 0 20px;
text-align: center;
vertical-align: middle;
border-collapse: collapse;
border-spacing: 0;
-webkit-border-radius: 8px;
-webkit-box-shadow: 0 0 4px #000000;
}
table tr {
border-bottom: solid 1px #cccccc;
-webkit-border-radius: 8px;
}
table > tr:last-child,
table > tbody > tr:last-child {
border-bottom: none;
}
table th,
table td {
margin: 0;
max-width: 250px;
height: 2em;
background-color: #ffffff;
border: none;
-webkit-border-radius: 0;
font-weight:normal;
}
table th {
background-color: #f0f0f0;
}
table td {
background-color: #ffffff;
}
table tr:first-of-type th:first-child,
table tr:first-of-type td:first-child {
-webkit-border-top-left-radius: inherit;
}
table tr:first-of-type th:last-child,
table tr:first-of-type td:last-child {
-webkit-border-top-right-radius: inherit;
}
table tr:last-of-type th:first-child,
table tr:last-of-type td:first-child {
-webkit-border-bottom-left-radius: inherit;
}
table tr:last-of-type th:last-child,
table tr:last-of-type td:last-child {
-webkit-border-bottom-right-radius: inherit;
}
table > thead tr:last-of-type th:first-child,
table > thead tr:last-of-type td:first-child,
table > thead tr:last-of-type th:last-child,
table > thead tr:last-of-type td:last-child {
-webkit-border-bottom-left-radius: 0;
-webkit-border-bottom-right-radius: 0;
}
table > thead + tbody > tr:first-of-type th:first-child,
table > thead + tbody > tr:first-of-type td:first-child,
table > thead + tbody > tr:first-of-type th:last-child,
table > thead + tbody > tr:first-of-type td:last-child {
-webkit-border-top-left-radius: 0;
-webkit-border-top-right-radius: 0;
}

/* テーブル（div）共通？
----------------------------------------------- */
#block {
width: 100%;
background-color: #ffffff;
-webkit-border-radius: 8px;
-webkit-box-shadow: 0px 0px 4px #000000;
margin: 10px 0;
}
.box-wrap {
display:inline-block;
width: 100%;
border-top: 1px solid #cccccc;
}
.box-wrap:first-child {
border-top: none;
-webkit-border-top-left-radius: 8px;
-webkit-border-top-right-radius: 8px;
}
.box-wrap:last-child {
-webkit-border-bottom-left-radius: 8px;
-webkit-border-bottom-right-radius: 8px;
}
.box-photo {
float:left;
display:block;
margin-left: 10px;
margin-right: 10px;
position: relative;
}
.box-data {
display:block;
margin: 0px 8px;
text-align: left;
}
.box-data h3 {
margin: 0px;
white-space: nowrap;
overflow: hidden;
text-overflow: ellipsis;
-webkit-text-overflow: ellipsis;
}
.box-data h3 a {
font-size: 100%;
text-decoration: none;
}
.description, .box-comment {
font: bold 100% Osaka;
line-height: 1.5em;
white-space: nowrap;
overflow: hidden;
text-overflow: ellipsis;
-webkit-text-overflow: ellipsis;
}
.box-price {
color: #993333;
font: bold 100% Osaka;
line-height: 1.5em;
}
</style>


<!--{$GLOBAL_ERR}-->
<noscript>
  <p>JavaScript を有効にしてご利用下さい.</p>
</noscript>

<a name="top" id="top"></a>

<!--{* ▼HeaderHeaderTop COLUMN*}-->
    <!--{if $arrPageLayout.HeaderTopNavi|@count > 0}-->
        <div>
            <!--{* ▼上ナビ *}-->
            <!--{foreach key=HeaderTopNaviKey item=HeaderTopNaviItem from=$arrPageLayout.HeaderTopNavi}-->
                <!-- ▼<!--{$HeaderTopNaviItem.bloc_name}--> -->
                <!--{if $HeaderTopNaviItem.php_path != ""}-->
                    <!--{include_php file=$HeaderTopNaviItem.php_path items=$HeaderTopNaviItem}-->
                <!--{else}-->
                    <!--{include file=$HeaderTopNaviItem.tpl_path items=$HeaderTopNaviItem}-->
                <!--{/if}-->
                <!-- ▲<!--{$HeaderTopNaviItem.bloc_name}--> -->
            <!--{/foreach}-->
            <!--{* ▲上ナビ *}-->
        </div>
    <!--{/if}-->
<!--{* ▲HeaderHeaderTop COLUMN*}-->
<!--{* ▼HEADER *}-->
<!--{if $arrPageLayout.header_chk != 2}-->
        <!--{include file= $header_tpl}-->
<!--{/if}-->
<!--{* ▲HEADER *}-->


<!--{* ▼TOP COLUMN*}-->
<!--{if $arrPageLayout.TopNavi|@count > 0}-->
            <div>
                <!--{* ▼上ナビ *}-->
                <!--{foreach key=TopNaviKey item=TopNaviItem from=$arrPageLayout.TopNavi}-->
                    <!-- ▼<!--{$TopNaviItem.bloc_name}--> -->
                    <!--{if $TopNaviItem.php_path != ""}-->
                        <!--{include_php file=$TopNaviItem.php_path items=$TopNaviItem}-->
                    <!--{else}-->
                        <!--{include file=$TopNaviItem.tpl_path items=$TopNaviItem}-->
                    <!--{/if}-->
                    <!-- ▲<!--{$TopNaviItem.bloc_name}--> -->
                <!--{/foreach}-->
                <!--{* ▲上ナビ *}-->
            </div>
<!--{/if}-->
<!--{* ▲TOP COLUMN*}-->

<!--{* ▼CENTER COLUMN *}-->
        <div>
            <!--{* ▼メイン上部 *}-->
            <!--{if $arrPageLayout.MainHead|@count > 0}-->
                <!--{foreach key=MainHeadKey item=MainHeadItem from=$arrPageLayout.MainHead}-->
                    <!-- ▼<!--{$MainHeadItem.bloc_name}--> -->
                    <!--{if $MainHeadItem.php_path != ""}-->
                        <!--{include_php file=$MainHeadItem.php_path items=$MainHeadItem}-->
                    <!--{else}-->
                        <!--{include file=$MainHeadItem.tpl_path items=$MainHeadItem}-->
                    <!--{/if}-->
                    <!-- ▲<!--{$MainHeadItem.bloc_name}--> -->
                <!--{/foreach}-->
            <!--{/if}-->
            <!--{* ▲メイン上部 *}-->

            <!--{* ▼メイン *}-->
                <form name="form1" method="POST" action="<!--{$server_url}-->" accept-charset="Shift_JIS">
                <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
                <!--{foreach from=$arrParam key=key item=val}-->
                <input type="hidden" name="<!--{$key}-->" value="<!--{$val}-->">
                <!--{/foreach}-->
                しばらくお待ちください。
                </form>
            <!--{* ▲メイン *}-->

            <!--{* ▼メイン下部 *}-->
            <!--{if $arrPageLayout.MainFoot|@count > 0}-->
                <!--{foreach key=MainFootKey item=MainFootItem from=$arrPageLayout.MainFoot}-->
                    <!-- ▼<!--{$MainFootItem.bloc_name}--> -->
                    <!--{if $MainFootItem.php_path != ""}-->
                        <!--{include_php file=$MainFootItem.php_path items=$MainFootItem}-->
                    <!--{else}-->
                        <!--{include file=$MainFootItem.tpl_path items=$MainFootItem}-->
                    <!--{/if}-->
                    <!-- ▲<!--{$MainFootItem.bloc_name}--> -->
                <!--{/foreach}-->
            <!--{/if}-->
            <!--{* ▲メイン下部 *}-->
        </div>
<!--{* ▲CENTER COLUMN *}-->

<!--{* ▼BOTTOM COLUMN*}-->
        <!--{if $arrPageLayout.BottomNavi|@count > 0}-->
            <div>
                <!--{* ▼下ナビ *}-->
                <!--{foreach key=BottomNaviKey item=BottomNaviItem from=$arrPageLayout.BottomNavi}-->
                    <!-- ▼<!--{$BottomNaviItem.bloc_name}--> -->
                    <!--{if $BottomNaviItem.php_path != ""}-->
                        <!--{include_php file=$BottomNaviItem.php_path items=$BottomNaviItem}-->
                    <!--{else}-->
                        <!--{include file=$BottomNaviItem.tpl_path items=$BottomNaviItem}-->
                    <!--{/if}-->
                    <!-- ▲<!--{$BottomNaviItem.bloc_name}--> -->
                <!--{/foreach}-->
                <!--{* ▲下ナビ *}-->
            </div>
        <!--{/if}-->
<!--{* ▲BOTTOM COLUMN*}-->


<!--{* ▼FOOTER *}-->
<!--{if $arrPageLayout.footer_chk != 2}-->
<!--▼ BEGIN FOOTER-->
<div id="footer">
<div id="footer-info">
<ul id="footer-menu">
<li><a href="<!--{$smarty.const.CART_URLPATH|h}-->">カゴの中を見る</a></li>
<li><a href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/mypage/login.php">MYページ</a></li>
<li><a href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/entry/kiyaku.php">新規会員登録</a></li>

<!--{php}-->
$tmp = $this->get_template_vars('tpl_mainpage');
if(preg_match("/index\.tpl$/", $tmp))
$this->assign('isTop', 1);
<!--{/php}-->

</ul>

<span class="footB">
<a href="<!--{$smarty.const.HTTP_URL}-->abouts/<!--{$smarty.const.DIR_INDEX_PATH|h}-->">当サイトについて</a>│
<a href="<!--{$smarty.const.HTTPS_URL}-->contact/<!--{$smarty.const.DIR_INDEX_PATH|h}-->">お問い合わせ</a><br>
<a href="<!--{$smarty.const.HTTP_URL}-->order/<!--{$smarty.const.DIR_INDEX_PATH|h}-->">特定商取引に関する表記</a>│
<a href="<!--{$smarty.const.HTTP_URL}-->guide/privacy.php">プライバシーポリシー</a>
</span>

<div id="copyright">(C) <!--{$arrSiteInfo.shop_name|h}-->.</div>

</div>
</div>
<!--▲ END FOOTER-->
<!--{/if}-->
<!--{* ▲FOOTER *}-->

 <!--{* ▼FooterBottom COLUMN*}-->
    <!--{if $arrPageLayout.FooterBottomNavi|@count > 0}-->
        <div id="footerbottomcolumn">
            <!--{* ▼上ナビ *}-->
            <!--{foreach key=FooterBottomNaviKey item=FooterBottomNaviItem from=$arrPageLayout.FooterBottomNavi}-->
                <!-- ▼<!--{$FooterBottomNaviItem.bloc_name}--> -->
                <!--{if $FooterBottomNaviItem.php_path != ""}-->
                    <!--{include_php file=$FooterBottomNaviItem.php_path items=$FooterBottomNaviItem}-->
                <!--{else}-->
                    <!--{include file=$FooterBottomNaviItem.tpl_path items=$FooterBottomNaviItem}-->
                <!--{/if}-->
                <!-- ▲<!--{$FooterBottomNaviItem.bloc_name}--> -->
            <!--{/foreach}-->
            <!--{* ▲上ナビ *}-->
        </div>
    <!--{/if}-->
<!--{* ▲FooterBottom COLUMN*}-->

</body>
<!--{assign var=index value="`$smarty.const.ROOT_URLPATH``$smarty.const.DIR_INDEX_FILE`"}-->
<!--{if $index != $smarty.server.PHP_SELF}-->
    <script type="text/javascript" language="JavaScript">
    //<![CDATA[
    setTopButton("<!--{$smarty.const.HTTPS_URL}-->");
    //]]>
    </script>
<!--{/if}-->
<!-- ▲BODY部 エンド -->

</html>
