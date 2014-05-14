<div class="contents-main">
<form name="index_form" method="post" action="?"> 
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="confirm" />
    <div class="btn">
        <a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('index_form', 'confirm', '', '');"><span class="btn-next">変更する</span></a>
    </div>
    <table class="list">
        <colgroup width="5%">
        <colgroup width="5%">
        <colgroup width="28%">
        <colgroup width="25%">
        <colgroup width="43%">
        <tr>
            <th colspan="2">インデックス</th>
            <th rowspan="2">テーブル名</th>
            <th rowspan="2">カラム名</th>
            <th rowspan="2">説明</th>
        </tr>
        <tr>
            <th>ON</th>
            <th>OFF</th>
        </tr>

        <!--{section name=cnt loop=$arrForm}-->
            <tr>
                <td class="center"><input type="radio" name="indexflag_new[<!--{$smarty.section.cnt.iteration}-->]" value="1" <!--{if $arrForm[cnt].indexflag == "1"}-->checked<!--{/if}--> /></td>
                <td class="center"><input type="radio" name="indexflag_new[<!--{$smarty.section.cnt.iteration}-->]" value="" <!--{if $arrForm[cnt].indexflag != "1"}-->checked<!--{/if}--> /></td>
                <th class="column"><!--{$arrForm[cnt].table_name}--></th>
                <th class="column"><!--{$arrForm[cnt].column_name}--></th>
                <td><!--{$arrForm[cnt].recommend_comment}--></td>
            </tr>
            <input type="hidden" name="table_name[<!--{$smarty.section.cnt.iteration}-->]" value="<!--{$arrForm[cnt].table_name}-->" />
            <input type="hidden" name="column_name[<!--{$smarty.section.cnt.iteration}-->]" value="<!--{$arrForm[cnt].column_name}-->" />
            <input type="hidden" name="indexflag[<!--{$smarty.section.cnt.iteration}-->]" value="<!--{$arrForm[cnt].indexflag}-->" />
        <!--{/section}-->
    </table>

    <a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('index_form', 'confirm', '', ''); return false;"><span class="btn-next">変更する</span></a>
</form>
</div>
