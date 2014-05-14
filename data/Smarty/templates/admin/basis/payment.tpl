<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="payment_id" value="<!--{$tpl_payment_id}-->" />
<div id="basis" class="contents-main">
    <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
    <div class="btn">
        <ul>
            <li><a class="btn-action" href="javascript:;" name="subm2" onclick="fnChangeAction('./payment_input.php'); fnModeSubmit('','',''); return false;">
                <span class="btn-next">支払方法を新規入力</span></a></li>
        </ul>
    </div>
    <!--{/if}-->
    <table class="list">
        <colgroup width="5%">
        <colgroup width="30%">
        <colgroup width="20%">
        <colgroup width="20%">
        <colgroup width="5%">
        <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
        <colgroup width="5%">
        <colgroup width="15%">
        <!--{/if}-->
        <tr>
            <th class="center">ID</th>
            <th>支払方法</th>
            <th>手数料（円）</th>
            <th>利用条件</th>
            <th>編集</th>
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <th>削除</th>
            <th>移動</th>
            <!--{/if}-->
        </tr>
        <!--{section name=cnt loop=$arrPaymentListFree}-->
        <tr>
            <td class="center"><!--{$arrPaymentListFree[cnt].payment_id|h}--></td>
            <td class="center"><!--{$arrPaymentListFree[cnt].payment_method|h}--></td>
            <!--{if $arrPaymentListFree[cnt].charge_flg == 2}-->
                <td class="center">-</td>
            <!--{else}-->
                <td class="right"><!--{$arrPaymentListFree[cnt].charge|number_format|h}--></td>
            <!--{/if}-->
            <td class="center">
                <!--{if $arrPaymentListFree[cnt].rule > 0}--><!--{$arrPaymentListFree[cnt].rule|number_format|h}--><!--{else}-->0<!--{/if}-->円
                <!--{if $arrPaymentListFree[cnt].upper_rule > 0}-->～<!--{$arrPaymentListFree[cnt].upper_rule|number_format|h}-->円<!--{elseif $arrPaymentListFree[cnt].upper_rule == "0"}--><!--{else}-->～無制限<!--{/if}--></td>
            <td class="center"><!--{if $arrPaymentListFree[cnt].fix != 1}--><a href="?" onclick="fnChangeAction('./payment_input.php'); fnModeSubmit('pre_edit', 'payment_id', <!--{$arrPaymentListFree[cnt].payment_id}-->); return false;">編集</a><!--{else}-->-<!--{/if}--></td>
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <td class="center"><!--{if $arrPaymentListFree[cnt].fix != 1}--><a href="?" onclick="fnModeSubmit('delete', 'payment_id', <!--{$arrPaymentListFree[cnt].payment_id}-->); return false;">削除</a><!--{else}-->-<!--{/if}--></td>
            <td class="center">
            <!--{if $smarty.section.cnt.iteration != 1}-->
            <a href="?" onclick="fnModeSubmit('up','payment_id', <!--{$arrPaymentListFree[cnt].payment_id}-->); return false;">上へ</a>
            <!--{/if}-->
            <!--{if $smarty.section.cnt.iteration != $smarty.section.cnt.last}-->
            <a href="?" onclick="fnModeSubmit('down','payment_id', <!--{$arrPaymentListFree[cnt].payment_id}-->); return false;">下へ</a>
            <!--{/if}-->
            <!--{/if}-->
            </td>
        </tr>
        <!--{/section}-->
    </table>
</div>
</form>
