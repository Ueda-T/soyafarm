/**
 * エンターキーでの入力欄移動
 * 拒否属性：disabled, readonly, display=none
 **/
function move_focus_to_next_tabindex(evt) {
    if ($(evt.target).filter("[readonly!='true'][disabled!='true'][class!='c_disable']").size() == 0) {
	// 無効な項目をフォーカス中の場合
	evt.preventDefault();
	return false;
    }

    var elements = $(":input:visible[tabindex]" + "[type!='hidden'][type!='file'][type!='reset'][type!='submit'][type!='button'][type!='image']" + "[readonly!='true'][disabled!='true'][class!='c_disable']");

    elements.sort(
	function(a, b) {
	    return parseInt(a.getAttribute("tabindex")) - parseInt(b.getAttribute("tabindex"));
	}
    );

    var index = elements.index(evt.target);
    if (index != -1 && elements.get( index + 1 ) ) {
	try {
	    elements.get( index + 1 ).focus();
	} catch(e) {
	    ;
	}
    } else {
	evt.target.blur();
    }

    // ここでよばないと最後のオブジェクトでエンターキーが有効になってしまう。
    evt.preventDefault();

    return false;
}

/**
 * エンターキーでの入力欄移動設定
 **/
$(function() {
    var target = $(":input:visible[tabindex][type!='hidden'][type!='file'][type!='reset'][type!='submit'][type!='button'][type!='image'][type!='textarea']");

    if (target.size() > 0) {
	target.bind('keypress', 'return', move_focus_to_next_tabindex);
    }
});
