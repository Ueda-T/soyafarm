<table id="<!--{$arrForm.dialogId}-->ResultCount">
<tr>
    <td id="resultCount">検索結果件数: <!--{$resultCount|default:0}-->件</td>
</tr>
</table>

<div id="<!--{$arrForm.dialogId}-->ListDiv">
    <table id="<!--{$arrForm.dialogId}-->List" summary="ブランド検索結果">
    <tr>
		<th id="<!--{$arrForm.dialogId}-->ListCheck">選択</th>
		<th id="<!--{$arrForm.dialogId}-->ListBrandCode">ブランドコード</th>
		<th id="<!--{$arrForm.dialogId}-->ListBrandName">ブランド名</th>
    </tr>
    <!--{section name=cnt loop=$arrResultList}-->
    <tr class="result">
        <td class="center"><input type="radio" name="<!--{$arrForm.dialogId}-->_selectedBrandCode" value="<!--{$arrResultList[cnt].brand_code|h}-->" /></td>
        <td><!--{$arrResultList[cnt].brand_code|h}--></td>
        <td><!--{$arrResultList[cnt].brand_name|h}--></td>
    </tr>
    <!--{/section}-->
    </table>
</div>
