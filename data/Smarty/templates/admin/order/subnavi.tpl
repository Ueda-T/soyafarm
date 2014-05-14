<div id="side_navi">
<ul class="level1">
    <li id="navi-order-index" class="<!--{if $tpl_mainno == 'order' && $tpl_subno == 'index'}-->on<!--{/if}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>受注照会</span></a></li>
    <li id="navi-order-regular" class="<!--{if $tpl_mainno == 'order' && $tpl_subno == 'regular_search'}-->on<!--{/if}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/regular_search.php"><span>定期照会</span></a></li>
    <li id="navi-order-followMail" class="<!--{if $tpl_subno == 'follow_mail_search'}-->on<!--{/if}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/followMail_search.php"><span>フォローメールマスタ照会</span></a></li>
    <li id="navi-order-followMail" class="<!--{if $tpl_subno == 'followMail_history'}-->on<!--{/if}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/followMail_history.php"><span>フォローメール履歴照会</span></a></li>
<!--
    <li id="navi-order-add" class="<!--{if $tpl_mainno == 'order' && $tpl_subno == 'add'}-->on<!--{/if}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/edit.php?mode=add"><span>新規受注登録</span></a></li>
    <li id="navi-order-status"
        class="<!--{if $tpl_mainno == 'order' && $tpl_subno == 'status'}-->on<!--{/if}-->"
    ><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/status.php"><span>ステータス管理</span></a></li>
-->
<!--▼ Veritrans 3G Module to sbivt3g_status.php -->
<!--
    <li id="navi-sbivt3g-order-status"
        class="<!--{if $tpl_mainno == 'order' && $tpl_subno == 'sbivt3g_status'}-->on<!--{/if}-->"
    ><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/sbivt3g_status.php"><span>3G専用ステータス管理</span></a></li>
-->
<!--▲ Veritrans 3G Module to sbivt3g_status.php -->

</ul>

<!--{if $tpl_csv_download_auth == $smarty.const.CSV_DOWNLOAD_AUTH_ON}-->
<ul class="level1" style="margin-top:14px;">
    <li style="padding:5px 4px;font-weight:bold;font-size:110%;">定期情報</li>
    <li<!--{if $tpl_subno == 'export_mix_regular'}--> class="on"<!--{/if}--> id="export-mix-regular"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/expMixRegular.php"><span>新旧商品混在定期情報エクスポート</span></a></li>
</ul>
<!--{/if}-->

<!--{if $tpl_inos_auth == $smarty.const.INOS_AUTH_ON}-->
<ul class="level1" style="margin-top:14px;">
    <li style="padding:5px 4px;font-weight:bold;font-size:110%;">INOSシステム連携</li>
    <!--{*<li<!--{if $tpl_subno == 'inos_export_order'}--> class="on"<!--{/if}--> id="navi-inos-export-order"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/inos_export_order.php"><span>受注エクスポート</span></a></li>*}-->
    <li<!--{if $tpl_subno == 'inos_import_order'}--> class="on"<!--{/if}--> id="navi-inos-import-order"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/inos_import_order.php"><span>受注インポート</span></a></li>
    <!--{*<li<!--{if $tpl_subno == 'inos_export_regular'}--> class="on"<!--{/if}--> id="navi-inos-export-regular"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/inos_export_regular.php"><span>定期エクスポート</span></a></li>*}-->
    <li<!--{if $tpl_subno == 'inos_import_teiki'}--> class="on"<!--{/if}--> id="navi-inos-import-teiki"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/inos_import_teiki.php"><span>定期インポート</span></a></li>
    <li<!--{if $tpl_subno == 'inos_export_order_regular'}--> class="on"<!--{/if}--> id="navi-inos-export-order-regular"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/inos_export_order_regular.php"><span>受注・定期エクスポート</span></a></li>
</ul>
<!--{/if}-->
</div
