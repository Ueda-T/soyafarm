<div id="side_navi">
<ul class="level1">
<li<!--{if $tpl_subno == 'index'}--> class="on"<!--{/if}--> id="navi-customer-index"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->customer/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>顧客照会</span></a></li>
<!--{*
<li<!--{if $tpl_subno == 'customer'}--> class="on"<!--{/if}--> id="navi-customer-customer"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->customer/edit.php"><span>新規顧客登録</span></a></li>
*}-->
<!--{* ▽メニュー移動 (メルマガ管理 から) *}-->
<!--
<li<!--{if $tpl_subno == 'mail'}--> class="on"<!--{/if}--> id="navi-mail-index"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->mail/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>メルマガ配信</span></a></li>
<li<!--{if $tpl_subno == 'template'}--> class="on"<!--{/if}--> id="navi-mail-template"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->mail/template.php"><span>メルマガ設定</span></a></li>
<li<!--{if $tpl_subno == 'history'}--> class="on"<!--{/if}--> id="navi-mail-history"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->mail/history.php"><span>メルマガ配信履歴</span></a></li>
-->
<!--{if ($tpl_csv_download_auth == $smarty.const.CSV_DOWNLOAD_AUTH_ON)}-->
<li<!--{if $tpl_subno == 'export'}--> class="on"<!--{/if}--> id="navi-mail-export"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->mail/export.php"><span>メルマガ用データ出力</span></a></li>
<!--{/if}-->

<!--{*
<!--{if $tpl_inos_auth == $smarty.const.INOS_AUTH_ON}-->
<ul class="level1" style="margin-top:14px;">
<li style="padding:5px 4px;font-weight:bold;font-size:110%;">INOSシステム連携</li>
<li<!--{if $tpl_subno == 'customer_import'}--> class="on"<!--{/if}--> id="navi-customer-import"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->customer/customer_import.php"><span>顧客インポート</span></a></li>
<li<!--{if $tpl_subno == 'inos_export_customer'}--> class="on"<!--{/if}--> id="navi-inos-export-customer"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->customer/inos_export_customer.php"><span>顧客エクスポート</span></a></li>
</ul>
<!--{/if}-->
*}-->
</div>
