<div class="bloc_outer">
    <div id="calender_area">
    <h2><img src="<!--{$TPL_URLPATH}-->img/title/tit_bloc_calender.gif" alt="カレンダー" /></h2>
        <div class="bloc_body">
            <!--{section name=num loop=$arrCalendar}-->
                <!--{assign var=arrCal value=`$arrCalendar[num]`}-->
                <!--{section name=cnt loop=$arrCal}-->
                    <!--{if $smarty.section.cnt.first}-->
                        <table>
                            <caption class="month"><!--{$arrCal[cnt].year}-->年<!--{$arrCal[cnt].month}-->月の定休日</caption>
                            <thead><tr><th>日</th><th>月</th><th>火</th><th>水</th><th>木</th><th>金</th><th>土</th></tr></thead>
                    <!--{/if}-->
                    <!--{if $arrCal[cnt].first}-->
                        <tr>
                        <!--{/if}-->
                        <!--{if !$arrCal[cnt].in_month}-->
                            <td></td>
                        <!--{elseif $arrCal[cnt].holiday}-->
                            <td class="off"><!--{$arrCal[cnt].day}--></td>
                        <!--{else}-->
                            <td><!--{$arrCal[cnt].day}--></td>
                        <!--{/if}-->
                        <!--{if $arrCal[cnt].last}-->
                            </tr>
                    <!--{/if}-->
                <!--{/section}-->
                <!--{if $smarty.section.cnt.last}-->
                    </table>
                <!--{/if}-->
            <!--{/section}-->
            <p class="information">※赤字は休業日です</p>
        </div>

    </div>
</div>
