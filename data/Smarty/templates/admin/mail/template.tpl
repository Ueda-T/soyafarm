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
<form name="form1" id="form1" method="post" action="?">
<div id="mail" class="contents-main">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
<div class="btn">
    <a class="btn-action" href="./template_input.php"><span class="btn-next">テンプレートを新規入力</span></a>
</div>
<!--{/if}-->
<!--{if count($arrTemplates) > 0}-->
    <table class="list center">
    <colgroup width="15%">
    <colgroup width="35%">
    <colgroup width="20%">
    <colgroup width="10%">
    <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
    <colgroup width="10%">
    <!--{/if}-->
    <colgroup width="10%">
        <tr>
            <th>作成日</th>
            <th>subject</th>
            <th>メール形式</th>
            <th>編集</th>
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <th>削除</th>
            <!--{/if}-->
            <th>プレビュー</th>
        </tr>
        <!--{section name=data loop=$arrTemplates}-->
        <tr>
            <td><!--{$arrTemplates[data].create_date|date_format:'%Y/%m/%d'|h}--></td>
            <td class="left"><!--{$arrTemplates[data].subject|h}--></td>
            <!--{assign var=type value=$arrTemplates[data].mail_method|h}-->
            <td><!--{$arrMagazineType[$type]}--></td>
            <td><a href="./template_input.php?mode=edit&amp;template_id=<!--{$arrTemplates[data].template_id}-->">編集</a></td>
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <td><a href="#" onclick="fnDelete('?mode=delete&amp;id=<!--{$arrTemplates[data].template_id}-->'); return false;">削除</a></td>
            <!--{/if}-->
            <td><a href="#" onclick="win03('./preview.php?mode=template&amp;template_id=<!--{$arrTemplates[data].template_id}-->','preview','650','700'); return false;" target="_blank">プレビュー</a></td>
        </tr>
        <!--{/section}-->
    </table>
<!--{/if}-->
</div>
</form>
