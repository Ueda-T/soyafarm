<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->

<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();

function func_submit( id ){
    // 脆弱性パッチ対応
    //var fm = window.opener.document.form<!--{$smarty.get.rank}-->;
    var fm = window.opener.document.form<!--{$rank|h}-->;
    fm.product_id.value = id;
    fm.mode.value = 'set_item';
    fm.rank.value = '<!--{$rank|h}-->';
    //fm.rank.value = '<!--{$smarty.get.rank}-->';
    fm.submit();
    window.close();
    return false;
}
//-->
</script>

<!--▼検索フォーム-->
<form name="form1" id="form1" method="post" action="#">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input name="mode" type="hidden" value="search" />
<input name="search_pageno" type="hidden" value="" />
    <table class="form">
        <colgroup width="20%">
        <colgroup width="80%">
        <tr>
            <th>カテゴリ</th>
            <td>
                <select name="search_category_id">
                    <option value="" selected="selected">選択してください</option>
                    <!--{html_options options=$arrCatList selected=$arrForm.search_category_id}-->
                </select>
            </td>
        </tr>
        <tr>
            <th>商品名</th>
            <td><input type="text" name="search_name" value="<!--{$arrForm.search_name}-->" size="35" class="box35" /></td>
        </tr>
    </table>
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'search', '', ''); return false;"><span class="btn-next">検索を開始</span></a></li>
        </ul>
    </div>
    <!--{* ▼検索結果表示 *}-->
    <!--{if $tpl_linemax}-->
    <p><!--{$tpl_linemax}-->件が該当しました。</p>
    <ul class="pager" style="margin:10px 0;">
    <!--{$tpl_strnavi}-->
    </ul>


    <table class="list">
        <colgroup width="15%">
        <colgroup width="13%">
        <colgroup width="60%">
        <colgroup width="13%">
        <tr>
            <th>商品画像</th>
            <th>商品コード</th>
            <th>商品名</th>
            <th>決定</th>
        </tr>
        <!--{section name=cnt loop=$arrProducts}-->
        <!--▼商品<!--{$smarty.section.cnt.iteration}-->-->
        <tr>
            <td class="center">
                <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrProducts[cnt].main_list_image|sfNoImageMainList|h}-->&width=65&height=65" alt="" />
            </td>
            <td>
                <!--{assign var=codemin value=`$arrProducts[cnt].product_code_min`}-->
                <!--{assign var=codemax value=`$arrProducts[cnt].product_code_max`}-->
                <!--{* 商品コード *}-->
                <!--{if $codemin != $codemax}-->
                    <!--{$codemin|h}-->～<!--{$codemax|h}-->
                <!--{else}-->
                    <!--{$codemin|h}-->
                <!--{/if}-->
            </td>
            <td><!--{$arrProducts[cnt].name|h}--></td>
            <td class="center"><a href="" onClick="return func_submit(<!--{$arrProducts[cnt].product_id}-->)">決定</a></td>
        </tr>
        <!--▲商品<!--{$smarty.section.cnt.iteration}-->-->
        <!--{sectionelse}-->
        <tr>
            <td colspan="4">商品が登録されていません</td>
        </tr>    
        <!--{/section}-->
    </table>
    <!--{/if}-->
    <!--{* ▲検索結果表示 *}-->

</form>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
