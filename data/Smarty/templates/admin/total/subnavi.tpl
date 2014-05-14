<div id="side_navi">
<ul class="level1">
    <li id="navi-total-term"
        class="<!--{if ($tpl_mainno == 'total' && ($arrForm.page.value == 'term' || $arrForm.page.value == ''))}-->on<!--{/if}-->"
    ><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->total/<!--{$smarty.const.DIR_INDEX_PATH}-->?page=term"><span>期間別集計</span></a></li>
    <li id="navi-total-products"
        class="<!--{if ($tpl_mainno == 'total' && $arrForm.page.value == 'products')}-->on<!--{/if}-->"
    ><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->total/<!--{$smarty.const.DIR_INDEX_PATH}-->?page=products"><span>商品別集計</span></a></li>
    <li id="navi-total-age"
        class="<!--{if ($tpl_mainno == 'total' && $arrForm.page.value == 'age')}-->on<!--{/if}-->"
    ><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->total/<!--{$smarty.const.DIR_INDEX_PATH}-->?page=age"><span>年代別集計</span></a></li>
    <li id="navi-total-member"
        class="<!--{if ($tpl_mainno == 'total' && $arrForm.page.value == 'member')}-->on<!--{/if}-->"
    ><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->total/<!--{$smarty.const.DIR_INDEX_PATH}-->?page=member"><span>会員別集計</span></a></li>
</ul>
</div>
