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
<form name="form1" id="form1" method="POST" action="?" >
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="status" value="<!--{if $arrForm.status == ""}-->1<!--{else}--><!--{$arrForm.status}--><!--{/if}-->" />
<input type="hidden" name="search_pageno" value="<!--{$tpl_pageno}-->" >
<input type="hidden" name="order_id" value="" />
<div id="order" class="contents-main">
    <h2>抽出条件</h2>
        <div class="btn">
        <!--{foreach key=key item=item from=$arrORDERSTATUS}-->
            <a
                class="btn-normal"
                style="padding-right: 1em;"
                <!--{if $key != $SelectedStatus}-->
                    href="javascript:;"
                    onclick="document.form1.search_pageno.value='1'; fnModeSubmit('search','status','<!--{$key}-->' ); return false;"
                <!--{/if}-->
            ><!--{$item}--></a>
        <!--{/foreach}-->
        </div>
    <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
    <h2>ステータス変更</h2>
    <!--{/if}-->

    <!--{* 登録テーブルここから *}-->
    <!--{if $tpl_linemax > 0}-->
        <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
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
        <span class="attention">※ <!--{$arrORDERSTATUS[$smarty.const.ORDER_CANCEL]}-->もしくは、削除に変更時には、在庫数を手動で戻してください。</span><br />
        <!--{/if}-->

        <p class="remark">
            <!--{$tpl_linemax}-->件が該当しました。
            <!--{$tpl_strnavi}-->
        </p>

        <table class="list center">
            <colgroup width="5%">
            <colgroup width="12%">
            <colgroup width="9%">
            <colgroup width="10%">
            <colgroup width="20%">
            <colgroup width="10%">
            <colgroup width="10%">
            <!--{* 2011.05.09 非表示 お届日追加
            <colgroup width="12%">
            <colgroup width="12%">
            *}-->
            <tr>
                <th><label for="move_check">選択</label> <input type="checkbox" name="move_check" id="move_check" onclick="fnAllCheck('#move_check', 'input[name^=move]')" /></th>
                <th>対応状況</th>
                <th>注文番号</th>
                <th>受注日</th>
                <th>お名前</th>
                <th>支払方法</th>
                <th>購入金額（円）</th>
                <th>お届日</th>
                <!--{* 2011.05.09 非表示 お届日追加
                <th>入金日</th>
                <th>発送日</th>
                *}-->
            </tr>
            <!--{section name=cnt loop=$arrStatus}-->
            <!--{assign var=status value="`$arrStatus[cnt].status`"}-->
            <!--{assign var=num value=$arrShippingDate[cnt]|@count}-->
            <tr style="background:<!--{$arrORDERSTATUS_COLOR[$status]}-->;">
                <td rowspan="<!--{$num}-->"><input type="checkbox" name="move[]" value="<!--{$arrStatus[cnt].order_id}-->" ></td>
                <td rowspan="<!--{$num}-->"><!--{$arrORDERSTATUS[$status]}--></td>
                <td rowspan="<!--{$num}-->"><a href="#" onclick="fnOpenWindow('./disp.php?order_id=<!--{$arrStatus[cnt].order_id}-->','order_disp','800','900'); return false;" ><!--{$arrStatus[cnt].order_id}--></a></td>
                <td rowspan="<!--{$num}-->"><!--{$arrStatus[cnt].create_date|sfDispDBDate:false}--></td>
                <td rowspan="<!--{$num}-->"><!--{$arrStatus[cnt].order_name|h}--></td>
                <!--{assign var=payment_id value=`$arrStatus[cnt].payment_id`}-->
                <td rowspan="<!--{$num}-->"><!--{$arrPayment[$payment_id]|h}--></td>
                <td rowspan="<!--{$num}-->" class="right"><!--{$arrStatus[cnt].total|number_format}--></td>
                <!--{foreach key=key1 item=item1 from=$arrShippingDate[cnt] }-->
                <!--{if key1 == 0}-->
                  <td style="background:<!--{$arrORDERSTATUS_COLOR[$status]}-->;">
                    <!--{if $item1 == NULL}-->
                      <p class="center">指定なし</p><br />
                    <!--{else}-->
                      <p class="center"><!--{$item1|date_format:"%Y/%m/%d"}--></p>
                    <!--{/if}-->
                  </td>
            </tr>
                <!--{else}-->
                <!--{* 2011.05.09 お届日追加 *}-->
                <tr style="background:<!--{$arrORDERSTATUS_COLOR[$status]}-->;">
                  <td>
                    <!--{if $item1 == NULL}-->
                      <p class="center">指定なし</p><br />
                    <!--{else}-->
                      <p class="center"><!--{$item1|date_format:"%Y/%m/%d"}--></p>
                    <!--{/if}-->
                  </td>
                </tr>
                <!--{/if}-->
                <!--{/foreach}-->

                
                <!--{* 2011.05.09 非表示
                <td><!--{if $arrStatus[cnt].payment_date != ""}--><!--{$arrStatus[cnt].payment_date|sfDispDBDate:false}--><!--{else}-->未入金<!--{/if}--></td>

                <td><!--{if $arrStatus[cnt].status eq 5}--><!--{$arrStatus[cnt].commit_date|sfDispDBDate:false}--><!--{else}-->未発送<!--{/if}--></td>
            *}-->
            <!--{/section}-->
        </table>
        <input type="hidden" name="move[]" value="" />

        <p><!--{$tpl_strnavi}--></p>

    <!--{elseif $arrStatus != "" & $tpl_linemax == 0}-->
        <div class="message">
            該当するデータはありません。
        </div>
    <!--{/if}-->

    <!--{* 登録テーブルここまで *}-->
</div>
</form>


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
//-->
</script>
