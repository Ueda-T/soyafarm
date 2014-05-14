<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="holiday_id" value="<!--{$tpl_holiday_id}-->" />
<div id="basis" class="contents-main">

    <table class="form">
        <tr>
            <th>タイトル<span class="attention"> *</span></th>
            <td>
                <!--{if $arrErr.title}--><span class="attention"><!--{$arrErr.title}--></span><!--{/if}-->
                <input type="text" name="title" value="<!--{$arrForm.title|h}-->" maxlength="<!--{$smarty.const.SMTEXT_LEN}-->" style="" size="60" class="box60"/>
                <span class="attention"> (上限<!--{$smarty.const.SMTEXT_LEN}-->文字)</span>
            </td>
        </tr>
        <tr>
            <th>日付<span class="attention"> *</span></th>
            <td>
                <!--{if $arrErr.date || $arrErr.month || $arrErr.day}-->
                <span class="attention"><!--{$arrErr.date}--></span>
                <span class="attention"><!--{$arrErr.month}--></span>
                <span class="attention"><!--{$arrErr.day}--></span>
                <!--{/if}-->
                <select name="month" style="<!--{$arrErr.month|sfGetErrorColor}-->">
                    <option value="">--</option>
                    <!--{html_options options=$arrMonth selected=$arrForm.month}-->
                </select>月
                <select name="day" style="<!--{$arrErr.day|sfGetErrorColor}-->">
                    <option value="">--</option>
                    <!--{html_options options=$arrDay selected=$arrForm.day}-->
                </select>日
                <br />
                <span class="attention">振替休日は自動設定されないので、振替え先の日付を設定してください。</span>
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
        <colgroup width="50%">
        <colgroup width="15%">
        <colgroup width="5%">
        <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
        <colgroup width="5%">
        <colgroup width="15%">
        <!--{/if}-->
        <tr>
            <th>タイトル</th>
            <th>日付</th>
            <th class="edit">編集</th>
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <th class="delete">削除</th>
            <th>移動</th>
            <!--{/if}-->
        </tr>
        <!--{section name=cnt loop=$arrHoliday}-->
        <tr style="background:<!--{if $tpl_holiday_id != $arrHoliday[cnt].holiday_id}-->#ffffff<!--{else}--><!--{$smarty.const.SELECT_RGB}--><!--{/if}-->;">
            <!--{assign var=holiday_id value=$arrHoliday[cnt].holiday_id}-->
            <td><!--{$arrHoliday[cnt].title|h}--></td>
            <td><!--{$arrHoliday[cnt].month|h}-->月<!--{$arrHoliday[cnt].day|h}-->日</td>
            <td class="center">
                <!--{if $tpl_holiday_id != $arrHoliday[cnt].holiday_id}-->
                <a href="?" onclick="fnModeSubmit('pre_edit', 'holiday_id', <!--{$arrHoliday[cnt].holiday_id}-->); return false;">編集</a>
                <!--{else}-->
                編集中
                <!--{/if}-->
            </td>
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <td class="center">
                <!--{if $arrClassCatCount[$class_id] > 0}-->
                -
                <!--{else}-->
                <a href="?" onclick="fnModeSubmit('delete', 'holiday_id', <!--{$arrHoliday[cnt].holiday_id}-->); return false;">削除</a>
                <!--{/if}-->
            </td>
            <td class="center">
                <!--{if $smarty.section.cnt.iteration != 1}-->
                <a href="?" onclick="fnModeSubmit('up', 'holiday_id', <!--{$arrHoliday[cnt].holiday_id}-->); return false;" />上へ</a>
                <!--{/if}-->
                <!--{if $smarty.section.cnt.iteration != $smarty.section.cnt.last}-->
                <a href="?" onclick="fnModeSubmit('down', 'holiday_id', <!--{$arrHoliday[cnt].holiday_id}-->); return false;" />下へ</a>
                <!--{/if}-->
            </td>
            <!--{/if}-->
        </tr>
        <!--{/section}-->
    </table>

</div>
</form>
