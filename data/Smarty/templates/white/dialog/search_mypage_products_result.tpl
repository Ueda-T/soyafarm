<table id="<!--{$arrForm.dialogId}-->ResultCount">
    <tr>
        <td id="resultCount">商品をお選びください。</td>
    </tr>
</table>

<div id="<!--{$arrForm.dialogId}-->ListDiv">
    <table id="<!--{$arrForm.dialogId}-->List" summary="商品検索結果">
    <tr>
		<th id="<!--{$arrForm.dialogId}-->ListProductImg">画像</th>
		<th id="<!--{$arrForm.dialogId}-->ListProductName">商品名</th>
		<th id="<!--{$arrForm.dialogId}-->ListCheck">操作</th>
    </tr>
    <!--{section name=cnt loop=$arrResultList}-->
    <tr class="result">
        <td><img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrResultList[cnt].main_list_image|h}-->" width="60" ></td>
        <td id="<!--{$arrForm.dialogId}-->ListProductName"><!--{$arrResultList[cnt].product_name|h}-->　<!--{$arrResultList[cnt].product_class_name|h}--></td>
        <td class="center" id="<!--{$arrForm.dialogId}-->ListCheck">
            <input type='button' onclick="doSelect('<!--{$arrResultList[cnt].product_id|h}-->|<!--{$arrResultList[cnt].product_class_id|h}-->|<!--{$arrResultList[cnt].product_name|h}-->　<!--{$arrResultList[cnt].product_class_name|h}-->|<!--{$arrResultList[cnt].price|h}-->|<!--{$arrResultList[cnt].sale_limit|h}-->|<!--{$arrResultList[cnt].sale_minimum_number|h}-->');" value="選択" />
        </td>
    </tr>
    <!--{/section}-->
    </table>
</div>
