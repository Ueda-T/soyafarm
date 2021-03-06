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
<form name="form1" id="form1" method="post" action="?" enctype="multipart/form-data"">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="csv_upload" />
<div id="products" class="contents-main">
    <div class="message">
        <span>CSV登録を実行しました。</span>
    </div>
    <!--{if $arrRowErr}-->
        <table class="form">
            <tr>
                <td>
                    <!--{foreach item=err from=$arrRowErr}-->
                        <span class="attention"><!--{$err}--></span>
                    <!--{/foreach}-->
                </td>
            </tr>
        </table>
    <!--{/if}-->
    <!--{if $arrRowResult}-->
        <table class="form">
            <tr>
                <td>
                    <!--{foreach item=result from=$arrRowResult}-->
                    <span><!--{$result}--><br/></span>
                    <!--{/foreach}-->
                    <!--{if $tpl_err_count > 0}-->
                    <span class="attention"><!--{$tpl_err_count}-->件のエラーデータがありました。<br/></span>
                    <!--{/if}-->
                </td>
            </tr>
        </table>
    <!--{/if}-->
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="./customer_import.php"><span class="btn-prev">戻る</span></a></li>
            <!--{if $tpl_err_count > 0}-->
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'errcsv_download', '', ''); return false;"><span>顧客エラーデータ出力</span></a></li>
            <!--{/if}-->
        </ul>
    </div>
</div>
</form>
