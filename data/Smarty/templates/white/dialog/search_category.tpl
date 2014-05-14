<script type="text/javascript">
<!--
    /**
     * 初期化ボタン押下時
     */
    function doClear() {
        $('#<!--{$arrForm.dialogId}-->_categoryCode').val('');
        $('#<!--{$arrForm.dialogId}-->_categoryName').val('');

        $('#<!--{$arrForm.dialogId}-->_errors').empty();
        $('#resultCount').html('検索結果件数: 0件');
        $('#<!--{$arrForm.dialogId}-->ListContainer table tr.result').remove();

        return false;
    }

    /**
     * 検索ボタン押下時
     */
    function doSearch() {
        url = "<!--{$smarty.const.ROOT_URLPATH}-->dialog/search_category.php";

        _search(url, '<!--{$arrForm.dialogId}-->');

        return false;
    }

    /**
     * 選択ボタン押下時
     */
    function doSelect() {
        url = "<!--{$smarty.const.ROOT_URLPATH}-->dialog/search_category.php";

        _select(url, '<!--{$arrForm.dialogId}-->', 'selectedCategoryCode');

        return doClose();
    }

    /**
     * 閉じるボタン押下時
     */
    function doClose() {
        $('#<!--{$arrForm.dialogId}-->').dialog('close');

        return false;
    }
// -->
</script>

<style TYPE="text/css">
<!--
table#<!--{$arrForm.dialogId}-->ResultCount {
    width: 710px;
    margin-bottom: 0px;
}

table#<!--{$arrForm.dialogId}-->ResultCount td#resultCount {
    text-align: left;
    border: none;
}

div#<!--{$arrForm.dialogId}-->ListDiv {
    padding: 0px;
    border: none;
    width: 710px;
    height: 254px;
    overflow: hidden;
}

table#<!--{$arrForm.dialogId}-->List {
    width: 692px;
    margin-top: 0px;
}

table#<!--{$arrForm.dialogId}-->List th {
    text-align: center;
    font-weight: bold;
}

th#<!--{$arrForm.dialogId}-->ListCheck {
    width: 5px;
}

th#<!--{$arrForm.dialogId}-->ListCategoryCode {
    width: 50px;
}
-->
</style>

<div id="<!--{$arrForm.dialogId}-->" title="カテゴリ検索">
	<span id="<!--{$arrForm.dialogId}-->_errors" style="color: red">
	</span>
    <form>
        <table class="forms" summary="カテゴリ検索条件" style="width: 710px" >
			<colgroup>
				<col span="1" style="width: 20%">
				<col span="1" style="width: 80%">
			</colgroup>
			<tr>
				<th>カテゴリコード</th>
				<td><input id="<!--{$arrForm.dialogId}-->_categoryCode" name="<!--{$arrForm.dialogId}-->_categoryCode" type="text" value="" class="box10" maxlength="10" /></td>
			</tr>
			<tr>
				<th>カテゴリ名</th>
				<td><input id="<!--{$arrForm.dialogId}-->_categoryName" name="<!--{$arrForm.dialogId}-->_categoryName" type="text" value="" class="box40" maxlength="40" /></td>
			</tr>
		</table>

		<div style="width: 710px; text-align: right">
			<a class="btn-normal box6 center" href="javascript:;" onclick="doClear();"><span>初期化</span></a>
			<a class="btn-normal box6 center" href="javascript:;" onclick="doSearch();"><span>検索</span></a>
		</div>
	</form>

	<span id="<!--{$arrForm.dialogId}-->ListContainer">
        <table id="<!--{$arrForm.dialogId}-->ResultCount">
            <tr>
                <td id="resultCount">検索結果件数: 0件</td>
            </tr>
        </table>

		<div id="<!--{$arrForm.dialogId}-->ListDiv">
			<table id="<!--{$arrForm.dialogId}-->List" summary="カテゴリ検索結果">
			<tr>
				<th id="<!--{$arrForm.dialogId}-->ListCheck">選択</th>
				<th id="<!--{$arrForm.dialogId}-->ListCategoryCode">カテゴリコード</th>
				<th id="<!--{$arrForm.dialogId}-->ListCategoryName">カテゴリ名</th>
			</tr>
			</table>
		</div>
	</span>

	<br>

    <div style="width: 710px; text-align: right">
		<a href="javascript:;" id="<!--{$arrForm.dialogId}-->_selectButton" class="btn-normal box6 center" onclick="doSelect();"><span>選択</span></a>
		<a href="javascript:;" class="btn-normal box6 center" onclick="doClose();"><span>閉じる</span></a>
	</div>
</div>
