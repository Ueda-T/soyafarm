<body>
<!--{include file="tagmgr.tpl"}-->
<!--{* A8タグ表示用 *}-->
<!--{if "sfPrintA8Tag"|function_exists === TRUE}-->
    <!--{include file=`$smarty.const.MODULE_REALDIR`mdl_a8/print_a8_tag.tpl}-->
<!--{/if}-->
<!--{$GLOBAL_ERR}-->
<noscript>
    <p>JavaScript を有効にしてご利用下さい.</p>
</noscript>

<div id="wrapper">
<div class="frame_outer">
    <a name="top" id="top"></a>

    <!--{* ▼HeaderHeaderTop COLUMN*}-->
    <!--{if $arrPageLayout.HeaderTopNavi|@count > 0}-->
        <div id="headertopcolumn">
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
            <div id="wrapContent" class="topCol">
				<div id="wrapContentEle">
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
            </div>
        <!--{/if}-->
        <!--{* ▲TOP COLUMN*}-->

    <!--{* ▼CONTENTS *}-->
    <!--{assign var=top value="`$smarty.const.ROOT_URLPATH`index.php"}-->
    <!--{if $smarty.server.PHP_SELF == $top}-->
    <div class="homeMain">
	<!--{else}-->
    <div id="wrapContent">
    	<div id="wrapContentEle">
	<!--{/if}-->
        <!--{* ▼LEFT COLUMN *}-->
        <!--{if $arrPageLayout.LeftNavi|@count > 0}-->
            <div id="leftcolumn" class="side_column heightLine">
                <!--{* ▼左ナビ *}-->
                <!--{foreach key=LeftNaviKey item=LeftNaviItem from=$arrPageLayout.LeftNavi}-->
                    <!-- ▼<!--{$LeftNaviItem.bloc_name}--> -->
                    <!--{if $LeftNaviItem.php_path != ""}-->
                        <!--{include_php file=$LeftNaviItem.php_path items=$LeftNaviItem}-->
                    <!--{else}-->
                        <!--{include file=$LeftNaviItem.tpl_path items=$LeftNaviItem}-->
                    <!--{/if}-->
                    <!-- ▲<!--{$LeftNaviItem.bloc_name}--> -->
                <!--{/foreach}-->
                <!--{* ▲左ナビ *}-->
            </div>
        <!--{/if}-->
        <!--{* ▲LEFT COLUMN *}-->

        <!--{* ▼CENTER COLUMN *}-->
        <div 
            <!--{if $tpl_column_num == 3}-->
                id="three_maincolumn"
            <!--{elseif $tpl_column_num == 2}-->
                <!--{if $arrPageLayout.LeftNavi|@count == 0}-->
                    <!--{if $smarty.server.PHP_SELF == $top}-->
                    class="homeMainEle"
                    <!--{else}-->
                    id="main"
                    <!--{/if}-->
                <!--{else}-->
                    id="two_maincolumn_right"
                <!--{/if}-->
            <!--{elseif $tpl_column_num == 1}-->
                id="one_maincolumn"
            <!--{/if}-->
            <!--{*class="main_column"*}-->
        >
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
            <!--{include file=$tpl_mainpage}-->
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

        <!--{* ▼RIGHT COLUMN *}-->
        <!--{if $arrPageLayout.RightNavi|@count > 0}-->
		<!--{if $smarty.server.PHP_SELF == $top}-->
			<div class="homeSide">
		<!--{else}-->
            <div id="sub" class="side_column heightLine">
		<!--{/if}-->
                <!--{* ▼右ナビ *}-->
                <!--{foreach key=RightNaviKey item=RightNaviItem from=$arrPageLayout.RightNavi}-->
                    <!-- ▼<!--{$RightNaviItem.bloc_name}--> -->
                    <!--{if $RightNaviItem.php_path != ""}-->
                        <!--{include_php file=$RightNaviItem.php_path items=$RightNaviItem}-->
                    <!--{else}-->
                        <!--{include file=$RightNaviItem.tpl_path items=$RightNaviItem}-->
                    <!--{/if}-->
                    <!-- ▲<!--{$RightNaviItem.bloc_name}--> -->
                <!--{/foreach}-->
                <!--{* ▲右ナビ *}-->
            </div>
        <!--{/if}-->
        <!--{* ▲RIGHT COLUMN *}-->

        <!--{* ▼BOTTOM COLUMN*}-->
        <!--{if $arrPageLayout.BottomNavi|@count > 0}-->
            <div id="bottomcolumn">
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

        <!--{if $smarty.server.PHP_SELF != $top}-->
        </div>
        <!--{/if}-->
	</div>
    <!--{* ▲CONTENTS *}-->
</div><!--/#wrapper-->

    <!--{* ▼FOOTER *}-->
    <!--{if $arrPageLayout.footer_chk != 2}-->
        <!--{include file=$footer_tpl}-->
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
</div>

<!--{include file="tracking.tpl"}-->

<!--{* ▼UNITAG *}-->
<!--{if $tpl_tag_unitag}--><!--{$tpl_tag_unitag|smarty:nodefaults}--><!--{/if}-->
<!--{* ▲UNITAG *}-->

<!--{* ▼CLICK ANALYZER *}-->
<!--{if $smarty.server.PHP_SELF == $top}-->
<!--{$smarty.const.CLICK_ANALYZER_TOP}-->
<!--{/if}-->
<!--{* ▲CLICK ANALYZER *}-->

<!--{* ▼BLADE TAG *}-->
<!--{if $tpl_tag_blade}--><!--{$tpl_tag_blade|smarty:nodefaults}--><!--{/if}-->
<!--{* ▲BLADE TAG *}-->

<!--{* ▼MarketOne TAG *}-->
<!--{if $tpl_tag_market_one}--><!--{$tpl_tag_market_one|smarty:nodefaults}--><!--{/if}-->
<!--{* ▲MarketOne TAG *}-->

</body>
