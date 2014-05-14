<!--{* -*- coding: utf-8-unix; -*- *}-->
<div id="side_navi">
<ul class="level1">
<li<!--{if $tpl_subno == 'index'}--> class="on"<!--{/if}--> id="navi-basis-index"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->basis/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>SHOPマスター</span></a></li>
<li<!--{if $tpl_subno == 'tradelaw'}--> class="on"<!--{/if}--> id="navi-basis-tradelaw"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->basis/tradelaw.php"><span>特定商取引法</span></a></li>
<li<!--{if $tpl_subno == 'delivery'}--> class="on"<!--{/if}--> id="navi-basis-delivery"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->basis/delivery.php"><span>配送方法設定</span></a></li>
<li<!--{if $tpl_subno == 'payment'}--> class="on"<!--{/if}--> id="navi-basis-payment"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->basis/payment.php"><span>支払方法設定</span></a></li>
<!--{if $USE_POINT}--><li<!--{if $tpl_subno == 'point'}--> class="on"<!--{/if}--> id="navi-basis-point"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->basis/point.php"><span>ポイント設定</span></a></li><!--{/if}-->
<li<!--{if $tpl_subno == 'mail'}--> class="on"<!--{/if}--> id="navi-basis-mail"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->basis/mail.php"><span>メール設定</span></a></li>
<li<!--{if $tpl_subno == 'seo'}--> class="on"<!--{/if}--> id="navi-basis-seo"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->basis/seo.php"><span>SEO管理</span></a></li>
<li<!--{if $tpl_subno == 'kiyaku'}--> class="on"<!--{/if}--> id="navi-basis-kiyaku"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->basis/kiyaku.php"><span>会員規約設定</span></a></li>
<!--{*
<li<!--{if $tpl_subno == 'zip_install'}--> class="on"<!--{/if}--> id="navi-basis-zip"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->basis/zip_install.php"><span>郵便番号DB登録</span></a></li>
*}-->

<!--{*
<li<!--{if $tpl_subno == 'holiday'}--> class="on"<!--{/if}--> id="navi-basis-holiday"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->basis/holiday.php"><span>定休日管理</span></a></li>
*}-->

<!--{* メニュー移動 (コンテンツ管理 から) *}-->
<li<!--{if $tpl_subno == 'new'}--> class="on"<!--{/if}--> id="navi-contents-index"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->contents/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>新着情報管理</span></a></li>
<!--{* メニュー移動 (システム設定 から) *}-->
<li<!--{if $tpl_subno == 'member'}--> class="on"<!--{/if}--> id="navi-system-index"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->system/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>メンバー管理</span></a></li>
<li<!--{if $tpl_subno == 'zip_install'}--> class="on"<!--{/if}--> id="navi-system-index"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->basis/zip_install.php"><span>郵便番号辞書</span></a></li>

</ul>
</div>
