<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
*}-->
<form name="form1" id="form1" method="post" action="">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<div id="system" class="contents-main">
    <div class="pager">
        <ul>
            <!--▼ページ送り-->
            <!--{$tpl_strnavi}-->
            <!--▲ページ送り-->
        </ul>
    </div>

    <!--▼メンバー一覧ここから-->
    <table class="list">
        <colgroup width="15%">
        <colgroup width="20%">
        <colgroup width="20%">
        <colgroup width="10%">
        <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
        <colgroup width="5%">
        <colgroup width="5%">
        <colgroup width="20%">
        <div class="btn">
            <a class="btn-action" href="javascript:;" onclick="win02('./input.php','input','620','450'); return false;"><span class="btn-next">メンバーを新規入力</span></a>
        </div>
        <!--{/if}-->
        <tr>
            <th>権限</th>
            <th>名前</th>
            <th>所属</th>
            <th>稼働</th>
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <th>編集</th>
            <th>削除</th>
            <th>移動</th>
            <!--{/if}-->
        </tr>
        <!--{section name=data loop=$list_data}--><!--▼メンバー<!--{$smarty.section.data.iteration}-->-->
        <tr>
            <!--{assign var="auth" value=$list_data[data].authority}--><td><!--{$arrAUTHORITY[$auth]|h}--></td>
            <td><!--{$list_data[data].name|h}--></td>
            <td><!--{$list_data[data].department|h}--></td>
            <!--{assign var="work" value=$list_data[data].work}--><td><!--{$arrWORK[$work]|h}--></td>
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <td align="center"><a href="#" onClick="win02('./input.php?id=<!--{$list_data[data].member_id}-->&amp;pageno=<!--{$tpl_disppage}-->','member_edit','620','450'); return false;">編集</a></td>
            <td align="center"><!--{if $workmax > 1}--><a href="#" onClick="fnDeleteMember(<!--{$list_data[data].member_id}-->,<!--{$tpl_disppage}-->); return false;">削除</a><!--{else}-->-<!--{/if}--></td>
            <td align="center">
            <!--{$tpl_nomove}-->
            <!--{if !($smarty.section.data.first && $tpl_disppage eq 1)}--><a href="./rank.php?id=<!--{$list_data[data].member_id}-->&amp;move=up&amp;pageno=<!--{$tpl_disppage}-->">上へ</a><!--{/if}-->
            <!--{if !($smarty.section.data.last && $tpl_disppage eq $tpl_pagemax)}--><a href="./rank.php?id=<!--{$list_data[data].member_id}-->&amp;move=down&amp;pageno=<!--{$tpl_disppage}-->">下へ</a><!--{/if}-->
            </td>
            <!--{/if}-->
        </tr>
        <!--▲メンバー<!--{$smarty.section.data.iteration}-->-->
        <!--{/section}-->
    </table>

    <div class="pager">
        <ul>
            <!--▼ページ送り-->
            <!--{$tpl_strnavi}-->
            <!--▲ページ送り-->
        </ul>
    </div>
</div>
</form>
