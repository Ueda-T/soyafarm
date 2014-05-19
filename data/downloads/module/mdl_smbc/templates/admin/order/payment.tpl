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
<link rel="stylesheet" href="<!--{$TPL_URLPATH}-->css/thickbox.css" type="text/css" media="screen" />
<script type="text/javascript">
    <!--
    function fnSelectCheckSubmit(){

        var selectflag = 0;
        var fm = document.form1;

        if(fm.change_status.options[document.form1.change_status.selectedIndex].value == ""){
        selectflag = 1;
        }

        if(selectflag == 1){
            alert('セレクトボックスが選択されていません');
            return false;
        }
        var i;
        var checkflag = 0;
        var max = fm["move[]"].length;

        if(max) {
            for (i=0;i<max;i++){
                if(fm["move[]"][i].checked == true){
                    checkflag = 1;
                }
            }
        } else {
            if(fm["move[]"].checked == true) {
                checkflag = 1;
            }
        }

        if(checkflag == 0){
            alert('チェックボックスが選択されていません');
            return false;
        }

        if(selectflag == 0 && checkflag == 1){
        document.form1.mode.value = 'update';
        document.form1.submit();
        }
    }
    function fnCheckAll() {
        var fm = document.form1;
        var max = fm["batch_order_id[]"].length;
        var i;
        if(max != undefined){
            for (i=0;i<max;i++){
                if(document.form1["batch_order_id[]"][i].disabled == false){
                    document.form1["batch_order_id[]"][i].checked = true;
                }
            }
        }else if(document.form1["batch_order_id[]"].disabled == false){
            document.form1["batch_order_id[]"].checked = true;
        }
    }
    function fnUncheckAll() {
        var fm = document.form1;
        var max = fm["batch_order_id[]"].length;
        var i;
        if(max != undefined){
            for (i=0;i<max;i++){
                document.form1["batch_order_id[]"][i].checked = false;
            }
        }else{
            document.form1["batch_order_id[]"].checked = false;
        }
    }
    function fnCheckToggle() {
        var fm = document.form1;
        var max = fm["batch_order_id[]"].length;
        var i;

        if(fm["checktoggle"].checked == false){
            //uncheck
            if(max != undefined){
                for (i=0;i<max;i++){
                    document.form1["batch_order_id[]"][i].checked = false;
                }
            }else{
                document.form1["batch_order_id[]"].checked = false;
            }
        }else{
            //check
            if(max != undefined){
                for (i=0;i<max;i++){
                    if(document.form1["batch_order_id[]"][i].disabled == false){
                        document.form1["batch_order_id[]"][i].checked = true;
                    }
                }
            }else if(document.form1["batch_order_id[]"].disabled == false){
                document.form1["batch_order_id[]"].checked = true;
            }
        }
    }
//-->
</script>
<div id="order" class="contents-main">
<form name="search_form" id="search_form" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="search" />
    <h2>検索条件設定</h2>
    <!--{* 検索条件設定テーブルここから *}-->
    <table>
        <tr>
            <th>注文番号</th>
            <td colspan="3">
                <!--{assign var=key1 value="search_order_id1"}-->
                <!--{assign var=key2 value="search_order_id2"}-->
                <span class="attention"><!--{$arrErr[$key1]}--></span>
                <span class="attention"><!--{$arrErr[$key2]}--></span>
                <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" />
                ～
                <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" />
            </td>
        </tr>
        <tr>
            <th>対応状況</th>
            <td>
                <!--{assign var=key value="search_order_status"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                <option value="">選択してください</option>
                <!--{html_options options=$arrORDERSTATUS selected=$arrForm[$key].value}-->
                </select>
            </td>
            <th>入金ステータス</th>
            <td>
                <!--{assign var=key value="search_payment_status"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                <option value="">選択してください</option>
                <!--{html_options options=$arrPAYMENTSTATUS selected=$arrForm[$key].value}-->
                </select>
            </td>
        </tr>
        <tr>
            <th>顧客名</th>
            <td>
            <!--{assign var=key value="search_order_name"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
            <th>顧客名(カナ)</th>
            <td>
            <!--{assign var=key value="search_order_kana"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
        </tr>
        <tr>
            <th>受注日</th>
            <td colspan="3">
                <!--{if $arrErr.search_sorderyear}--><span class="attention"><!--{$arrErr.search_sorderyear}--></span><!--{/if}-->
                <!--{if $arrErr.search_eorderyear}--><span class="attention"><!--{$arrErr.search_eorderyear}--></span><!--{/if}-->
                <select name="search_sorderyear" style="<!--{$arrErr.search_sorderyear|sfGetErrorColor}-->">
                <option value="">----</option>
                <!--{html_options options=$arrRegistYear selected=$arrForm.search_sorderyear.value}-->
                </select>年
                <select name="search_sordermonth" style="<!--{$arrErr.search_sorderyear|sfGetErrorColor}-->">
                <option value="">--</option>
                <!--{html_options options=$arrMonth selected=$arrForm.search_sordermonth.value}-->
                </select>月
                <select name="search_sorderday" style="<!--{$arrErr.search_sorderyear|sfGetErrorColor}-->">
                <option value="">--</option>
                <!--{html_options options=$arrDay selected=$arrForm.search_sorderday.value}-->
                </select>日～
                <select name="search_eorderyear" style="<!--{$arrErr.search_eorderyear|sfGetErrorColor}-->">
                <option value="">----</option>
                <!--{html_options options=$arrRegistYear selected=$arrForm.search_eorderyear.value}-->
                </select>年
                <select name="search_eordermonth" style="<!--{$arrErr.search_eorderyear|sfGetErrorColor}-->">
                <option value="">--</option>
                <!--{html_options options=$arrMonth selected=$arrForm.search_eordermonth.value}-->
                </select>月
                <select name="search_eorderday" style="<!--{$arrErr.search_eorderyear|sfGetErrorColor}-->">
                <option value="">--</option>
                <!--{html_options options=$arrDay selected=$arrForm.search_eorderday.value}-->
                </select>日
            </td>
        </tr>
        <tr>
            <th>更新日</th>
            <td colspan="3">
                <!--{if $arrErr.search_supdateyear}--><span class="attention"><!--{$arrErr.search_supdateyear}--></span><!--{/if}-->
                <!--{if $arrErr.search_eupdateyear}--><span class="attention"><!--{$arrErr.search_eupdateyear}--></span><!--{/if}-->
                <select name="search_supdateyear" style="<!--{$arrErr.search_supdateyear|sfGetErrorColor}-->">
                    <option value="">----</option>
                    <!--{html_options options=$arrRegistYear selected=$arrForm.search_supdateyear.value}-->
                </select>年
                <select name="search_supdatemonth" style="<!--{$arrErr.search_supdateyear|sfGetErrorColor}-->">
                    <option value="">--</option>
                    <!--{html_options options=$arrMonth selected=$arrForm.search_supdatemonth.value}-->
                </select>月
                <select name="search_supdateday" style="<!--{$arrErr.search_supdateyear|sfGetErrorColor}-->">
                    <option value="">--</option>
                    <!--{html_options options=$arrDay selected=$arrForm.search_supdateday.value}-->
                </select>日～
                <select name="search_eupdateyear" style="<!--{$arrErr.search_eupdateyear|sfGetErrorColor}-->">
                    <option value="">----</option>
                    <!--{html_options options=$arrRegistYear selected=$arrForm.search_eupdateyear.value}-->
                </select>年
                <select name="search_eupdatemonth" style="<!--{$arrErr.search_eupdateyear|sfGetErrorColor}-->">
                    <option value="">--</option>
                    <!--{html_options options=$arrMonth selected=$arrForm.search_eupdatemonth.value}-->
                </select>月
                <select name="search_eupdateday" style="<!--{$arrErr.search_eupdateyear|sfGetErrorColor}-->">
                    <option value="">--</option>
                    <!--{html_options options=$arrDay selected=$arrForm.search_eupdateday.value}-->
                </select>日
            </td>
        </tr>
        <tr>
            <th>注文情報表示</th>
            <td colspan="3"><input type="checkbox" name="not_payment" value="1">入金情報のない注文情報も表示する</td>
        </tr>
    </table>

    <div class="btn">
        <p class="page_rows">検索結果表示件数
        <!--{assign var=key value="search_page_max"}-->
        <span class="attention"><!--{$arrErr[$key]}--></span>
        <select name="<!--{$arrForm[$key].keyname}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
        <!--{html_options options=$arrPageMax selected=$arrForm[$key].value}-->
        </select> 件</p>
        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('search_form', 'search', '', ''); return false;"><span class="btn-next">この条件で検索する</span></a></li>
            </ul>
        </div>
    </div>
    <!--検索条件設定テーブルここまで-->
</form>

<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'send' or $smarty.post.mode == 'batch' or $smarty.post.mode == 'cancel' )}-->

<!--★★検索結果一覧★★-->
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="search" />
<input type="hidden" name="order_id" value="" />
<input type="hidden" name="payment_total" value="">
<!--{foreach key=key item=item from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/foreach}-->
<!--{foreach key=key item=item from=$arrForm.hidden_batch_order_id}-->
<input type="hidden" name="hidden_batch_order_id[<!--{$key}-->]" value="<!--{$item}-->">
<!--{/foreach}-->
<!--{foreach key=key item=item from=$arrForm.hidden_cancel_order_id}-->
<input type="hidden" name="hidden_cancel_order_id[<!--{$key}-->]" value="<!--{$item}-->">
<!--{/foreach}-->
    <h2>ステータス変更</h2>
    <div class="btn">
        <select name="change_status">
            <option value="" selected="selected" style="<!--{$Errormes|sfGetErrorColor}-->" >選択してください</option>
            <!--{foreach key=key item=item from=$arrORDERSTATUS}-->
            <!--{if $key ne $SelectedStatus}-->
            <option value="<!--{$key}-->" ><!--{$item}--></option>
            <!--{/if}-->
            <!--{/foreach}-->
            <option value="delete">削除</option>
        </select>
        <a class="btn-normal" href="javascript:;" onclick="fnSelectCheckSubmit(); return false;"><span>移動</span></a>
    </div>

    <h2>検索結果一覧</h2>
        <div class="btn">
        <span class="attention"><!--検索結果数--><!--{$tpl_linemax}-->件</span>&nbsp;が該当しました。
   </div>
    <!--{if count($arrPaymentData) > 0}-->

    <!--{include file=$tpl_pager}-->
    <!--{* 検索結果表示テーブル *}-->
    <table class="list">
        <colgroup width="5%">
        <colgroup width="7%">
        <colgroup width="9%">
        <colgroup width="9%">
        <colgroup width="15%">
        <colgroup width="10%">
        <colgroup width="10%">
        <colgroup width="12%">
        <colgroup width="12%">
        <colgroup width="12%">
        <colgroup width="12%">
        <tr>
            <th><input type="checkbox" name="move_check" id="move_check" onclick="fnAllCheck(this, 'input[name=move[]]')" /></th>
            <th>注文番号</th>
            <th>受注日時</th>
            <th>更新日時</th>
            <th>顧客名</th>
            <th>購入金額</th>
            <th>対応状況</th>
            <th>入金ステータス</th>
            <th>入金日時</th>
            <th>振込人名</th>
            <th>入金金額</th>
        </tr>
        <!--{foreach from=$arrPaymentData item=array}-->
            <!--{assign var=status value="`$array.status`"}-->
        <tr>
            <td class="center"><input type="checkbox" name="move[]" value="<!--{$array.order_id}-->" ></td>
            <td class="center"><a href ="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnOpenWindow('./credit_edit.php?order_id=<!--{$array.order_id}-->','order_disp','800','900'); return false;" ><!--{$array.order_id}--></td>
            <td class="center"><!--{$array.create_date|sfDispDBDate:true}--></td>
            <td class="center"><!--{$array.update_date|sfDispDBDate:true}--></td>
            <td class="center"><!--{$array.order_name01|escape}--><!--{$array.order_name02|escape}--></td>
            <td class="center"><!--{$array.payment_total|number_format}--></td>
            <td class="center"><!--{$arrORDERSTATUS[$status]}--></td>
            <td class="center"><!--{ if $array.payment_status eq $smarty.const.MDL_SMBC_PAYMENT_STATUS_OK }-->入金済み<!--{ elseif $array.payment_status eq $smarty.const.MDL_SMBC_PAYMENT_STATUS_OVER }-->過入金<!--{ elseif $array.payment_status eq $smarty.const.MDL_SMBC_PAYMENT_STATUS_SHORT }-->一部入金<!--{ /if }--></td>
            <td class="center"><!--{$array.payment_date|sfDispDBDate:true}--></td>
            <td class="center"></td>
            <td class="center"><!--{$array.payment_amount|number_format}--></td>
        </tr>
        <!--{/foreach}-->
    </table>
    <input type="hidden" name="move[]" value="" />
    <!--{* 検索結果表示テーブル *}-->
    <!--{/if}-->
</form>
<!--{/if}-->
</div>
