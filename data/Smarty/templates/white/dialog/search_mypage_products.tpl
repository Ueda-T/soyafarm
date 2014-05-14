<script type="text/javascript">
<!--

    /**
     * 検索ボタン押下時
     */
    function doSearch(isSingleSelect) {
        url = "<!--{$smarty.const.ROOT_URLPATH}-->dialog/search_mypage_products.php";

        _search(url, '<!--{$arrForm.dialogId}-->', isSingleSelect);

        return false;
    }

    /**
     * 選択ボタン押下時
     */
    function doSelect(productInfo) {
        url = "<!--{$smarty.const.ROOT_URLPATH}-->dialog/search_mypage_products.php";

        lfSelect('<!--{$arrForm.dialogId}-->', productInfo);

        return doClose();
    }

    /**
     * 閉じるボタン押下時
     */
    function doClose() {
        $('#<!--{$arrForm.dialogId}-->').dialog('close');

        return false;
    }

/**
 * 検索結果の選択処理
 * @return
 */
function lfSelect(dialogId, productInfo) {
    // 選択された項目からオブジェクトを作成する
     data['product'] = productInfo;

    var func = dialogIdToFunc[dialogId];
    if (func != null && func instanceof Function) {
        // 呼び出し
        func(dialogId, data);
    }
}

// -->
</script>

<style TYPE="text/css">
<!--
table#<!--{$arrForm.dialogId}-->ResultCount {
    width: 560px;
    margin-bottom: 0px;
}

table#<!--{$arrForm.dialogId}-->ResultCount td#resultCount {
    text-align: left;
    border: none;
}

div#<!--{$arrForm.dialogId}-->ListDiv {
    padding: 0px;
    border: none;
    width: 560px;
    height: 380px;
    overflow: hidden;
}

table#<!--{$arrForm.dialogId}-->List {
    width: 542px;
    margin-top: 0px;
}

table#<!--{$arrForm.dialogId}-->List th {
    text-align: center;
    background: #fff8e0;
    border-top: 2px solid #c7b068;
    border-right: 1px solid #c7b068;
}

th#<!--{$arrForm.dialogId}-->ListCheck {
    width: 5px;
    border-right: 1px solid #c7b068;
}

th#<!--{$arrForm.dialogId}-->ListProductImg {
    width: 50px;
    border-left: 1px solid #c7b068;
}

td#<!--{$arrForm.dialogId}-->ListProductName {
	vertical-align: middle;
}
td#<!--{$arrForm.dialogId}-->ListCheck {
	vertical-align: middle;
}
-->
</style>

<div id="<!--{$arrForm.dialogId}-->" title="商品選択">
	<span id="<!--{$arrForm.dialogId}-->_errors" style="color: red">
	</span>
	<span id="<!--{$arrForm.dialogId}-->ListContainer">
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
			</table>
		</div>
	</span>

	<br>

    <div class="ui-dialog-buttonset" style="text-align:right;">
        <button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false" onclick="doClose();"><span class="ui-button-text">閉じる</span></button>
    </div>
</div>
