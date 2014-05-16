<!--{if $arrForm.customer_id}-->
<tr>
    <th>振込先銀行口座の固定割当</th>
    <td>
        <span class="attention"><!--{$arrError.bankaccount}--></span>
        <!--{if $bankaccount}-->
        <a class="btn-normal" href="javascript:;" onclick="return fnModeSubmit('send_data_smbc_bank_remove','',''); return false;">口座固定解除</a>
        <!--{$bankaccount}-->
        <!--{else}-->
        <a class="btn-normal" href="javascript:;" onclick="return fnModeSubmit('send_data_smbc_bank_assign','',''); return false;">口座固定割当</a>
        <!--{/if}-->
    </td>
</tr>
<!--{/if}-->
