<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="deliv_id" value="" />
<div id="basis" class="contents-main">
    <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
    <div class="btn">
        <ul>
            <li><a class="btn-action" href="javascript:;" name="subm2" onclick="fnChangeAction('./delivery_input.php'); fnModeSubmit('pre_edit','',''); return false;">
                <span class="btn-next">配送方法<!--{if $smarty.const.INPUT_DELIV_FEE}-->・配達日数を新規入力<!--{/if}--></span></a></li>
        </ul>
    </div>
    <!--{/if}-->
    <table class="list">
        <colgroup width="35%">
        <colgroup width="30%">
        <colgroup width="10%">
        <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
        <colgroup width="10%">
        <colgroup width="15%">
        <!--{/if}-->
        <tr>
            <th>配送業者</th>
            <th>名称</th>
            <th>編集</th>
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <th>削除</th>
            <th>移動</th>
            <!--{/if}-->
        </tr>
        <!--{section name=cnt loop=$arrDelivList}-->
            <tr>
                <td><!--{$arrDelivList[cnt].name|h}--></td>
                <td><!--{$arrDelivList[cnt].service_name|h}--></td>
                <td align="center"><a href="?" onclick="fnChangeAction('./delivery_input.php'); fnModeSubmit('pre_edit', 'deliv_id', <!--{$arrDelivList[cnt].deliv_id}-->); return false;">
                    編集</a></td>
                <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
                <td align="center"><a href="?" onclick="fnModeSubmit('delete', 'deliv_id', <!--{$arrDelivList[cnt].deliv_id}-->); return false;">
                    削除</a></td>
                <td align="center">
                <!--{if $smarty.section.cnt.iteration != 1}-->
                <a href="?" onclick="fnModeSubmit('up','deliv_id', '<!--{$arrDelivList[cnt].deliv_id}-->'); return false;">上へ</a>
                <!--{/if}-->
                <!--{if $smarty.section.cnt.iteration != $smarty.section.cnt.last}-->
                <a href="?" onclick="fnModeSubmit('down','deliv_id', '<!--{$arrDelivList[cnt].deliv_id}-->'); return false;">下へ</a>
                <!--{/if}-->
                </td>
                <!--{/if}-->
            </tr>
        <!--{/section}-->
    </table>
</div>
</form>
