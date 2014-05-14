<div id="side_navi">
<ul class="level1">
<li<!--{if $tpl_subno == 'index'}--> class="on"<!--{/if}--> id="navi-system-index"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->system/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>メンバー管理</span></a></li>
<li<!--{if $tpl_subno == 'bkup'}--> class="on"<!--{/if}--> id="navi-system-bkup"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->system/bkup.php"><span>バックアップ管理</span></a></li>
<li<!--{if $tpl_subno == 'parameter'}--> class="on"<!--{/if}--> id="navi-system-parameter"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->system/parameter.php"><span>パラメーター設定</span></a></li>
<li<!--{if $tpl_subno == 'masterdata'}--> class="on"<!--{/if}--> id="navi-system-masterdata"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->system/masterdata.php"><span>マスターデータ管理</span></a></li>
<li<!--{if $tpl_subno == 'adminarea'}--> class="on"<!--{/if}--> id="navi-system-adminarea"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->system/adminarea.php"><span>管理画面設定</span></a></li>
<li<!--{if $tpl_subno == 'system'}--> class="on"<!--{/if}--> id="navi-system-system"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->system/system.php"><span>システム情報</span></a></li>
<li<!--{if $tpl_subno == 'plugin'}--> class="on"<!--{/if}--> id="navi-system-plugin"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->system/plugin.php"><span>プラグイン管理</span></a></li>
<li<!--{if $tpl_mainno == 'system' && $tpl_subno == 'log'}--> class="on"<!--{/if}--> id="navi-system-log"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->system/log.php"><span><!--{*EC-CUBE*}-->ログ表示</span></a></li>
<li<!--{if $tpl_mainno == 'system' && $tpl_subno == 'editdb'}--> class="on"<!--{/if}--> id="navi-system-editdb"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->system/editdb.php"><span>データベース管理</span></a></li>
</ul>
</div>
