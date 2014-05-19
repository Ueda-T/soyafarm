<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
<style type="text/css">
<!--
.btn-alert {
    background: url("/smbc/alert.jpg") no-repeat scroll left center transparent;
    display: inline-block;
    font-size: 114%;
    font-weight: bold;
    padding: 0 0 0 20px;
}
-->
</style>
<script type="text/javascript">
<!--
function fnBankAccountUpload() {
    <!--{if $change_flg}-->
    if(!window.confirm("割当情報は上書きされます。\n本当にアップロードしますか？")){
        return;
    }
    <!--{/if}-->
    fnFormModeSubmit('form1', 'upload', '', '');
}
function fnBankAccountDelete() {
    if(!window.confirm("本当に全件削除を実行しますか？")){
        return;
    }
    fnFormModeSubmit('form1', 'del', '', '');
}
//-->
</script>
<form name="form1" method="post" action="?" enctype="multipart/form-data">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="download_csv" value="" />

<div id="system" class="contents-main">
    <h2>顧客固定割当情報アップロード</h2>
    <table class="form">
        <tr>
            <th>顧客固定割当情報ファイル<span class="attention"> *</span></th>
            <td>
                <!--{assign var=key value="bankaccount_file"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="file" name="<!--{ $key }-->" class="box45" size="43"  style="<!--{$arrErr[$key]|sfGetErrorColor}--> <!--{if $arrErr[$key]}--> background-color:<!--{$smarty.const.ERR_COLOR|h}--><!--{/if}-->">
                <a class="btn-action" href="javascript:;" onclick="fnBankAccountUpload(); return false;"><span class="btn-next">アップロード</span></a>
            </td>
        </tr>
    </table>

    <h2>口座情報一覧</h2>
    <!--{if $all_bankaccount > 0}-->
        <span class="attention"><!--{$arrErr.bankaccount_error}--></span>
        <table class="system-bankaccount" width="900">
            <tr>
                <th>空き口座数／総口座数</th>
                <td><!--{$bankaccount}-->／<!--{$all_bankaccount}--></td>
            </tr>
            <!--{if $change_flg}-->
            <tr>
                <th>決済ステーション登録状態</th>
                <td>
                    <span class="attention"> *</span>割当情報が変更されました。顧客固定割当情報ファイルを決済ステーションに反映してください。<br />
                    <!--{section name=cnt start=0 loop=$change_flg}-->
                    <a href="javascript:;" onclick="fnModeSubmit('download','download_csv','<!--{$smarty.section.cnt.iteration}-->'); return false;">ダウンロード<!--{$smarty.section.cnt.iteration}--></a>&nbsp;&nbsp;
                    <!--{/section}-->
                </td>
            </tr>
            <!--{/if}-->
        </table>

        <div class="btn">
            <div class="btn-area">
                <ul>
                    <li><a class="btn-action" href="javascript:;" onclick="fnBankAccountDelete();return false;"><span class="btn-alert">口座全件登録削除</span></a></li>
                </ul>
            </div>
        </div>
    <!--{else}-->
        <span>登録されている口座情報はありません。</span>
    <!--{/if}-->
</div>
</form>
