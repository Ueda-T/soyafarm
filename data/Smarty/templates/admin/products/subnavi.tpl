<div id="side_navi">
<ul class="level1">
<li<!--{if $tpl_subno == 'index'}--> class="on"<!--{/if}--> id="navi-products-index"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->products/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>商品照会</span></a></li>

<!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
<!--{*
<li<!--{if $tpl_subno == 'upload_csv'}--> class="on"<!--{/if}--> id="navi-products-uploadcsv"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->products/upload_csv.php"><span>商品マスタインポート(更新)</span></a></li>
*}-->
<li<!--{if $tpl_subno == 'upload_design_csv'}--> class="on"<!--{/if}--> id="navi-products-uploaddesigncsv"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->products/upload_design_csv.php"><span>商品マスタインポート(更新)</span></a></li>
<!--{/if}-->

<li<!--{if $tpl_subno == 'planning_search'}--> class="on"<!--{/if}--> id="navi-products-planning"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->products/planning_search.php"><span>企画照会</span></a></li>
<li<!--{if $tpl_subno == 'brand_search'}--> class="on"<!--{/if}--> id="navi-products-brand"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->products/brand_search.php"><span>ブランド照会</span></a></li>
<li<!--{if $tpl_subno == 'category_search'}--> class="on"<!--{/if}--> id="navi-products-category"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->products/category_search.php"><span>カテゴリ照会</span></a></li>
<!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
<li<!--{if $tpl_subno == 'class'}--> class="on"<!--{/if}--> id="navi-products-class"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->products/class.php"><span>規格管理</span></a></li>
<!--{/if}-->
<li<!--{if $tpl_subno == 'product_rank'}--> class="on"<!--{/if}--> id="navi-products-rank"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->products/product_rank.php"><span>商品並び替え</span></a></li>
<li<!--{if $tpl_subno == 'promotion_search'}--> class="on"<!--{/if}--> id="navi-products-promotion-search"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->products/promotion_search.php"><span>プロモーション照会</span></a></li>
<li<!--{if $tpl_subno == 'media_search'}--> class="on"<!--{/if}--> id="navi-products-media-search"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->products/media_search.php"><span>広告媒体照会</span></a></li>
</ul>
<!--{*
<!--{if $tpl_inos_auth == $smarty.const.INOS_AUTH_ON}-->
<ul class="level1" style="margin-top:14px;">
<li style="padding:5px 4px;font-weight:bold;font-size:110%;">INOSシステム連携</li>
<li<!--{if $tpl_subno == 'products_import'}--> class="on"<!--{/if}--> id="navi-products-products-import"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->products/products_import.php"><span>商品マスタインポート</span></a></li>
<li<!--{if $tpl_subno == 'promotion_import'}--> class="on"<!--{/if}--> id="navi-products-promotion-import"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->products/promotion_import.php"><span>プロモーションマスタインポート</span></a></li>
</ul>
<!--{/if}-->
*}-->
</div>
