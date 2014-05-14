<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="kiyaku_id" value="<!--{$tpl_kiyaku_id}-->" />
<div id="basis" class="contents-main">
    <table class="form">
        <tr>
            <th>規約タイトル<span class="attention"> *</span></th>
            <td>
                <span class="attention"><!--{$arrErr.kiyaku_title}--></span>
                <span class="attention"><!--{$arrErr.name}--></span>
                <input type="text" name="kiyaku_title" value="<!--{$arrForm.kiyaku_title|h}-->" maxlength="<!--{$smarty.const.SMTEXT_LEN}-->" style="<!--{if $arrErr.kiyaku_title != "" || $arrErr.name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="60" class="box60"/>
                <span class="attention"> (上限<!--{$smarty.const.SMTEXT_LEN}-->文字)</span>
            </td>
        </tr>
        <tr>
            <th>規約内容<span class="attention"> *</span></th>
            <td>
            <span class="attention"><!--{$arrErr.kiyaku_text}--></span>
            <textarea name="kiyaku_text" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" cols="60" rows="8" class="area60" style="<!--{if $arrErr.kiyaku_text != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" ><!--{$arrForm.kiyaku_text|h}--></textarea>
            <span class="attention"> (上限<!--{$smarty.const.MLTEXT_LEN}-->文字)</span>
            </td>
        </tr>
    </table>
    <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'edit', '', ''); return false;"><span class="btn-next">この内容で登録する</span></a></li>
        </ul>
    </div>
    <!--{/if}-->

    <table class="list">
        <colgroup width="65%">
        <colgroup width="10%">
        <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
        <colgroup width="10%">
        <colgroup width="15%">
        <!--{/if}-->
        <tr>
            <th>規約タイトル</th>
            <th>編集</th>
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <th>削除</th>
            <th>移動</th>
            <!--{/if}-->
        </tr>
        <!--{section name=cnt loop=$arrKiyaku}-->
            <tr style="background:<!--{if $tpl_kiyaku_id != $arrKiyaku[cnt].kiyaku_id}-->#ffffff<!--{else}--><!--{$smarty.const.SELECT_RGB}--><!--{/if}-->;">
            <!--{assign var=kiyaku_id value=$arrKiyaku[cnt].kiyaku_id}-->
                <td><!--{* 規格名 *}--><!--{$arrKiyaku[cnt].kiyaku_title|h}--></td>
                <td align="center">
                    <!--{if $tpl_kiyaku_id != $arrKiyaku[cnt].kiyaku_id}-->
                    <a href="?" onclick="fnModeSubmit('pre_edit', 'kiyaku_id', <!--{$arrKiyaku[cnt].kiyaku_id}-->); return false;">編集</a>
                    <!--{else}-->
                    編集中
                    <!--{/if}-->
                </td>
                <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
                <td align="center">
                    <!--{if $arrClassCatCount[$class_id] > 0}-->
                    -
                    <!--{else}-->
                    <a href="?" onclick="fnModeSubmit('delete', 'kiyaku_id', <!--{$arrKiyaku[cnt].kiyaku_id}-->); return false;">削除</a>
                    <!--{/if}-->
                </td>
                <td align="center">
                    <!--{if $smarty.section.cnt.iteration != 1}-->
                    <a href="?" onclick="fnModeSubmit('up', 'kiyaku_id', <!--{$arrKiyaku[cnt].kiyaku_id}-->); return false;">上へ</a>
                    <!--{/if}-->
                    <!--{if $smarty.section.cnt.iteration != $smarty.section.cnt.last}-->
                    <a href="?" onclick="fnModeSubmit('down', 'kiyaku_id', <!--{$arrKiyaku[cnt].kiyaku_id}-->); return false;">下へ</a>
                    <!--{/if}-->
                </td>
                <!--{/if}-->
            </tr>
        <!--{/section}-->
    </table>

</div>
</form>
