<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->

<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();

function func_submit( id ){
    var fm = window.opener.document.form1;
    var no = escape('<!--{$smarty.get.no|h}-->');
    fm['recommend_id' + no].value = id;
    fm.mode.value = 'recommend_select';
    fm.anchor_key.value = 'recommend_no' + no;
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
<input name="anchor_key" type="hidden" value="" />
<input name="search_pageno" type="hidden" value="" />
<table>
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
        <td><input type="text" name="search_name" value="<!--{$arrForm.search_name|h}-->" size="35" class="box35" /></td>
    </tr>
</table>
<div class="btn-area">
    <a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'search', '', ''); return false;" name="subm"><span class="btn-next">検索を開始</span></a>
</div>

<!--▼検索結果表示-->
<!--{if $tpl_linemax}-->
    <p><!--{$tpl_linemax}-->件が該当しました。</p>
    <!--{* ▼ページナビ *}-->
    <ul class="pager" style="margin:10px 0;">
    <!--{$tpl_strnavi}-->
    </ul>
    <!--{* ▲ページナビ *}-->

    <!--{* ▼検索後表示部分 *}-->
    <table class="list">
        <tr>
            <th>商品画像</th>
            <th>商品コード</th>
            <th>商品名</th>
            <th>決定</th>
        </tr>
        <!--{section name=cnt loop=$arrProducts}-->
            <!--▼商品<!--{$smarty.section.cnt.iteration}-->-->
            <!--{assign var=status value="`$arrProducts[cnt].status`"}-->
            <tr style="background:<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->;">
                <td align="center">
                    <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrProducts[cnt].main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65" alt="<!--{$arrRecommend[$recommend_no].name|h}-->" />
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
                <td align="center"><a href="#" onclick="return func_submit(<!--{$arrProducts[cnt].product_id|h}-->)">決定</a></td>
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
