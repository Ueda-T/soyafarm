<link rel="stylesheet" href="<!--{$TPL_URLPATH}-->jquery.multiselect2side/css/jquery.multiselect2side.css" type="text/css" media="screen" />
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->jquery.multiselect2side/js/jquery.multiselect2side.js" ></script>
<script type="text/javascript">
<!--
$().ready(function() {
    $('#output_list').multiselect2side({
        selectedPosition: 'right',
        moveOptions: true,
        labelsx: 'CSV出力しない項目',
        labeldx: 'CSV出力する項目',
        labelTop: '一番上',
        labelBottom: '一番下',
        labelUp: '一つ上',
        labelDown: '一つ下',
        labelSort: '項目順序'
    });
    // multiselect2side の初期選択を解除
    $('.ms2side__div select').val(null);
    // [Sort] ボタンは混乱防止のため非表示
    // FIXME 選択・非選択のボタンと比べて、位置ズレしている
    $('.ms2side__div .SelSort').hide();
});

function lfFormModeDefautSetSubmit(form, mode) {
    if (!window.confirm('初期設定で登録しても宜しいですか')) {
        return;
    }
    return fnSetFormSubmit(form, 'mode', mode);
}
//-->
</script>
<div class="contents-main">
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="confirm" />
<input type="hidden" name="tpl_subno_csv" value="<!--{$tpl_subno_csv|h}-->" />
<div id="admin-contents" class="contents-main">
    <!--{if $tpl_is_update}-->
    <span class="attention">※ 正常に更新されました。</span>
    <!--{/if}-->
    <span class="attention"><!--{$arrErr.tpl_subno_csv}--></span>
    <div class="ms2side__area" style="padding-left: 10px;">
        <span class="attention"><!--{$arrErr.output_list}--></span>
        <select multiple name="output_list[]" style="<!--{$arrErr.output_list|sfGetErrorColor}-->;" id="output_list" size="20">
            <!--{html_options options=$arrOptions selected=$arrSelected}-->
        </select>
    </div>

    <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'confirm', '', ''); return false;"><span class="btn-next">この内容で登録する</span></a></li>
            <li><a class="btn-action" href="javascript:;" onclick="lfFormModeDefautSetSubmit('form1', 'defaultset', '', ''); return false;"><span class="btn-next">初期設定に戻して登録</span></a></li>
        </ul>
    </div>
    <!--{/if}-->

</div>
</form>
</div>
