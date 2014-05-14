/*************************************************************************
 * 非公開関数
 *************************************************************************/

/**
 * ダイアログIDと戻りの関数オブジェクトを紐付けるハッシュ
 */
var dialogIdToFunc = new Object();

/**
 * 各ダイアログの共通パラメータ
 */
var defaultParams = {
    modal: true,
    draggable: true,
    resizable: false,
    bgiframe: true,
    autoOpen: false,
    modal: true,
    position: "center",
};

/**
 * ダイアログ内の入力データをまとめてパラメータオブジェクトを作成する
 *
 * @param prefix ダイアログ内のinput要素のname属性プレフィックス
 * @param selector jQueryの要素セレクタ
 * @return パラメタータオブジェクト
 */
function _createData(prefix, elements) {
    var data = new Object();
    var regex = new RegExp();

    regex.compile(prefix + "_([^_]+)");

    $(elements).each(function() {
        match = this.name.match(regex);
        data[match[1]] = $(this).val();
    });

    return data;
}

/*************************************************************************
 * 公開関数
 *************************************************************************/

/**
 * ダイアログオープン
 *
 * @param dialogParams
 * @param dialogId
 * @param endFunc
 * @return
 */
function openDialog(dialogId, dialogParams, endFunc) {
    // 二重起動を防止（モーダルの場合は意味がないが念のため）
    if ($("#" + dialogId).length > 0) {
        isOpen = $("#" + dialogId).dialog("isOpen");
        if (isOpen) {
            return;
        }
    }

    data = new Object();
    data["mode"] = "open";
    data["dialogId"] = dialogId;

    if (dialogParams.isSingleSelect !== undefined) {
        data["isSingleSelect"] = dialogParams.isSingleSelect;
    }

    // ダイアログHTMLをロードする
    $.ajax({
        type: "POST",
        async: false,
        url: dialogParams.url,
        data: data,
        success: function(data) {
            // ダイアログ画面を挿入する
            $("#container").after(data);

            // ダイアログとして表示する
            $("#" + dialogId).dialog(dialogParams);
            $("#" + dialogId).dialog({
                close: function(event) {
                    $("#" + dialogId).remove();
                    $(".ui-dialog").remove();
                },
            });

            $(".ui-dialog").css("position", "fixed");
            $("#" + dialogId).dialog({"position": "center"});
            $("#" + dialogId).dialog("open");

            // 選択時実行関数設定
            if (endFunc) {
                dialogIdToFunc[dialogId] = endFunc;
            }

            // スクロールする検索結果の存在チェック
            if ($("#" + dialogId + "List").size() == 0) {
                return false;
            }

			// スクロールバー付きのテーブルを構築する
            try {
                new superTable(dialogId + "List", {
                    cssSkin: "sSky",
                    fixedCols: 0,
                    headerRows: 0,
                });
            } catch (e) {
            }
        },
        error: function(xmlHttpRequest, textStatus, errorThrown) {
            ;
        }
    });

    // 自動検索ONの場合は初期表示で検索を実行
    if (dialogParams.autoSearch !== undefined) {
        _search(dialogParams.url, dialogId, dialogParams.isSingleSelect, dialogParams);
    }

    return false;
}

/**
 * 検索処理実行
 * @param dialogId
 * @return
 */
function _search(dialogUrl, dialogId, isSingleSelect, dialogParams) {
    $("#" + dialogId + "_errors").empty();

    // 条件テキスト入力値、セレクトの選択値をかき集め、オブジェクトを
	// 作成する
    var data = _createData
        (dialogId, $("#" + dialogId).find
		 ("input[type='text'], input[type='hidden'], select"));
    data["mode"] = "search";
    data["dialogId"] = dialogId;

    // 任意で追加したパラメータをオブジェクトへセットする
    if (dialogParams !== undefined) {
        for (var key in dialogParams) {
            data[key] = dialogParams[key]; 
        }
    }

    if (isSingleSelect !== undefined) {
        data["isSingleSelect"] = isSingleSelect;
    }

    $.ajax({
        type: "POST",
        async: false,
        url: dialogUrl,
        data: data,
        success: function(data) {
            // 検索結果テーブルを差し替える
            $("#" + dialogId + "ListContainer").empty();
            $("#" + dialogId + "ListContainer").html(data);

            // スクロールバー付きテーブルの再設定
            try {
                new superTable(dialogId + "List", {
                    cssSkin: "sSky",
                    fixedCols: 0,
                    headerRows: 0,
                });
            } catch (e) {
            }
        },
        error: function(xmlHttpRequest, textStatus, errorThrown) {
            $("#" + dialogId + "_errors").empty();
            $("#" + dialogId + "_errors").append(xmlHttpRequest.responseText);
        }
    });

    return false;
}

/**
 * 検索結果の選択処理
 * @return
 */
function _select(dialogUrl, dialogId, radioOrCheckId) {
    // 選択された項目からオブジェクトを作成する
    var data = _createData
        (dialogId, $("#" + dialogId).find("input[name^='" +
		 dialogId + "_" + radioOrCheckId + "']:checked"));

    var func = dialogIdToFunc[dialogId];
    if (func != null && func instanceof Function) {
        // 呼び出し
        func(dialogId, data);
    }
}
