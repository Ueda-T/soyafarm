<table id="<!--{$arrForm.dialogId}-->ResultCount">
<tr>
    <td id="resultCount">検索結果件数: <!--{$resultCount|default:0}-->件</td>
</tr>
</table>

<div id="<!--{$arrForm.dialogId}-->ListDiv">
    <table id="<!--{$arrForm.dialogId}-->List" summary="商品検索結果">
    <tr>
		<th id="<!--{$arrForm.dialogId}-->ListCheck">選択</th>
		<th id="<!--{$arrForm.dialogId}-->ListProductCode">商品コード</th>
		<th id="<!--{$arrForm.dialogId}-->ListProductName">商品名</th>
    </tr>
    <!--{section name=cnt loop=$arrResultList}-->
    <tr class="result">
        <td class="center"><input type="<!--{if $isSingleSelect == "0"}-->checkbox<!--{else}-->radio<!--{/if}-->" name="<!--{$arrForm.dialogId}-->_selectedProduct<!--{if $isSingleSelect == "0"}--><!--{$smarty.section.cnt.index}--><!--{/if}-->" value="<!--{$arrResultList[cnt].product_code|h}-->|<!--{$arrResultList[cnt].product_name|h}-->　<!--{$arrResultList[cnt].product_class_name|h}-->" /></td>
        <td><!--{$arrResultList[cnt].product_code|h}--></td>
        <td><!--{$arrResultList[cnt].product_name|h}-->　<!--{$arrResultList[cnt].product_class_name|h}--></td>
    </tr>
    <!--{/section}-->
    </table>
</div>
