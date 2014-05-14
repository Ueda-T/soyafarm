<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->
<table class="form">
    <tr>
        <td><!--{$tpl_subject|h}--></td>
    </tr>
    <tr>
        <td><!--{$tpl_body|h|nl2br}--></td>
    </tr>
</table>

<div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="window.close(); return false;"><span class="btn-next">ウインドウを閉じる</span></a></li>
        </ul>
    </div>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
