<div class="bloc_outer">
    <div id="guide_area" class="bloc_body">
        <!--{strip}-->
        <ul class="button_like">
        <li>
            <a href="<!--{$smarty.const.ROOT_URLPATH}-->abouts/<!--{$smarty.const.DIR_INDEX_PATH}-->" class="<!--{if $tpl_page_category == "abouts"}--> selected<!--{/if}-->"
            >当サイトについて</a></li>
        <li>
            <a href="<!--{$smarty.const.HTTPS_URL}-->contact/<!--{$smarty.const.DIR_INDEX_PATH}-->" class="<!--{if $tpl_page_category == "contact"}--> selected<!--{/if}-->"
            >お問い合わせ</a></li>
        <li>
            <a href="<!--{$smarty.const.ROOT_URLPATH}-->order/<!--{$smarty.const.DIR_INDEX_PATH}-->" class="<!--{if $tpl_page_category == "order"}--> selected<!--{/if}-->"
            >特定商取引に関する表記</a></li>
        <li>
            <a href="<!--{$smarty.const.ROOT_URLPATH}-->guide/privacy.php" class="<!--{if $tpl_page_category == "order"}--> selected<!--{/if}-->"
            >プライバシーポリシー</a></li>
        <li>
            <a href="<!--{$smarty.const.ROOT_URLPATH}-->faq/index.php" class="<!--{if $tpl_page_category == "faq"}--> selected<!--{/if}-->"
            >よくあるご質問</a></li>
        </ul>
        <!--{/strip}-->
        <div style="height: 0px; overflow: hidden;"></div><!--{* IE6ハック(背景乱れ防止) *}-->
    </div>
</div>
