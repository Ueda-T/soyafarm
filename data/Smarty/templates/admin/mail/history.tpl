<div id="mail" class="contents-main">
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="search_pageno" value="" />
<input type="hidden" name="mode" value="" />
<!--{if count($arrDataList) > 0}-->
    <!--{include file=$tpl_pager}-->
    <table class="list center">
        <tr>
            <th>配信開始時刻</th>
            <th rowspan="2">タイトル</th>
            <th rowspan="2">表示<br />確認</th>
            <th rowspan="2">配信<br />条件</th>
            <th rowspan="2">配信<br />総数</th>
            <th rowspan="2">配信<br />済数</th>
            <th rowspan="2">配信<br />失敗数</th>
            <th rowspan="2">未配信数</th>
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <th rowspan="2">再試行</th>
            <th rowspan="2" class="delete" >削除</th>
            <!--{/if}-->
        </tr>
        <tr>
            <th>配信終了時刻</th>
        </tr>
        <!--{section name=cnt loop=$arrDataList}-->
        <tr>
            <td><!--{$arrDataList[cnt].start_date|sfDispDBDate|h}--></td>
            <td rowspan="2" class="left"><!--{$arrDataList[cnt].subject|h}--></td>
            <td rowspan="2"><a href="javascript:;" onclick="win03('./preview.php?mode=history&amp;send_id=<!--{$arrDataList[cnt].send_id|h}-->', 'confirm', '720', '600'); return false;">確認</a></td>
            <td rowspan="2"><a href="javascript:;" onclick="win03('./<!--{$smarty.const.DIR_INDEX_PATH}-->?mode=query&amp;send_id=<!--{$arrDataList[cnt].send_id|h}-->','query','615','800'); return false;">確認</a></td>
            <td rowspan="2"><!--{$arrDataList[cnt].count_all|h}--></td>
            <td rowspan="2"><!--{$arrDataList[cnt].count_sent|h}--></td>
            <td rowspan="2" style="<!--{if $arrDataList[cnt].count_error >= 1}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->">
                <!--{$arrDataList[cnt].count_error|h}-->
            </td>
            <td rowspan="2" style="<!--{if $arrDataList[cnt].count_unsent >= 1}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->">
                <!--{$arrDataList[cnt].count_unsent|h}-->
            </td>
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <td rowspan="2">
                <!--{if $arrDataList[cnt].count_error >= 1 || $arrDataList[cnt].count_unsent >= 1}-->
                    <a href="index.php?mode=retry&amp;send_id=<!--{$arrDataList[cnt].send_id|h}-->" onclick="return window.confirm('未配信と配信失敗となった宛先に再送信を試みますか?');">実行</a>
                <!--{/if}-->
            </td>
            <td rowspan="2"><a href="?mode=delete&amp;send_id=<!--{$arrDataList[cnt].send_id|h}-->" onclick="return window.confirm('配信履歴を削除しても宜しいでしょうか');">削除</a></td>
            <!--{/if}-->

        </tr>
        <tr>
            <td><!--{$arrDataList[cnt].end_date|sfDispDBDate|h}--></td>
        </tr>
        <!--{/section}-->
    </table>
<!--{else}-->
    <div id="complete">
        <div class="complete-top"></div>
        <div class="contents">
            <div class="message">
                配信履歴はありません
            </div>
        </div>
        <div class="btn-area-top"></div>
        <div class="btn-area" style="padding: 0; margin: 0;">
            <ul>
                <li><a class="btn-action" href="./<!--{$smarty.const.DIR_INDEX_PATH}-->"><span class="btn-prev">配信内容設定へ戻る</span></a></li>
            </ul>
        </div>
        <div class="btn-area-bottom"></div>
<!--{/if}-->
</form>
</div>
