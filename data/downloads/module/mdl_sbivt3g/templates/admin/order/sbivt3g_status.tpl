<!--{*
 * sbivt3g_status.tpl - Veritrans 3G 専用ステータス管理
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: admin_config.tpl
 * @link        http://www.veritrans.co.jp/3gps
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
          <label for="payment">支払方法
            <select id="payment" name="payment" onchange="document.form1.search_pageno.value='1'; fnModeSubmit('search','','' ); return false;">
            <option value="">選択してください</option>
            <!--{html_options options=$arrPayment selected=$arrForm.payment}-->
            </select>
          </label>
          <label for="sbivt_status">3G決済ステータス
            <select id="sbivt_status" name="sbivt_status" onchange="document.form1.search_pageno.value='1'; fnModeSubmit('search','','' ); return false;">
            <option value="">選択してください</option>
            <!--{html_options options=$arrSbivtStatus selected=$arrForm.sbivt_status}-->
            </select>
          </label>
        </div>
    <h2>3G決済ステータス変更</h2>
        <!--{if $tpl_log|nl2br != ''}-->
        <div style="margin:10px; padding:5px; background-color:#EEEEEE;">
          <div><strong>結果ログ</strong></div>
          <!--{$tpl_log|nl2br}-->
        </div>
        <!--{/if}-->
    <!--{* 登録テーブルここから *}-->
    <!--{if $tpl_linemax > 0}-->
        <div class="btn">
          <!--{if $arrForm.sbivt_status != ""}-->
          <label for="change_sbivt_status">変更先 3G決済ステータス
            <select id="change_sbivt_status" name="change_sbivt_status" style="<!--{$Errormes|sfGetErrorColor}-->">
                <option value="">選択してください</option>
                <!--{html_options options=$arrSbivtEnableStatus selected=$arrForm.change_sbivt_status}-->
            </select>
          </label>
          <a class="btn-normal" href="javascript:;" onclick="fnSelectCheckSubmit(); return false;"><span>ステータス変更</span></a>
          <!--{else}-->
          抽出条件の「3G決済ステータス」を指定して下さい。
          <!--{/if}-->
        </div>
        <p class="remark">
            <!--{$tpl_linemax}-->件が該当しました。
            <!--{$tpl_strnavi}-->
        </p>

        <table class="list center">
            <colgroup width="5%">
            <colgroup width="7%">
            <colgroup width="7%">
            <colgroup width="9%">
            <colgroup width="9%">
            <colgroup width="19%">
            <colgroup width="10%">
            <colgroup width="10%">
            <colgroup width="12%">
            <colgroup width="12%">
            <tr>
                <th><label for="move_check">選択</label> <input type="checkbox" name="move_check" id="move_check" onclick="fnAllCheck(this, 'input[name=move[]]')" /></th>
                <th>3G決済<br/>ステータス</th>
                <th>対応状況</th>
                <th>注文番号</th>
                <th>受注日</th>
                <th>顧客名</th>
                <th>支払方法</th>
                <th>購入金額（円）</th>
                <th>入金日</th>
                <th>発送日</th>
            </tr>
            <!--{section name=cnt loop=$arrStatus}-->
            <!--{assign var=status value="`$arrStatus[cnt].status`"}-->
            <!--{assign var=sbivt_status value="`$arrStatus[cnt].memo01`"}-->
            <tr style="background:<!--{$arrORDERSTATUS_COLOR[$status]}-->;">
                <td><input type="checkbox" name="move[]" value="<!--{$arrStatus[cnt].order_id}-->" /></td>
                <td><!--{$arrSbivtStatus[$sbivt_status]|h|default:"－"}--></td>
                <td><!--{$arrORDERSTATUS[$status]}--></td>
                <td><a href="#" onclick="fnOpenWindow('<!--{$tpl_dispUri}-->?order_id=<!--{$arrStatus[cnt].order_id}-->','order_disp','800','900'); return false;" ><!--{$arrStatus[cnt].order_id}--></a></td>
                <td><!--{$arrStatus[cnt].create_date|sfDispDBDate:false}--></td>
                <td><!--{$arrStatus[cnt].order_name01|h}--><!--{$arrStatus[cnt].order_name02|h}--></td>
                <!--{assign var=payment_id value=`$arrStatus[cnt].payment_id`}-->
                <td><!--{$arrPayment[$payment_id]|h}--></td>
                <td class="right"><!--{$arrStatus[cnt].total|number_format}--></td>
                <td><!--{if $arrStatus[cnt].payment_date != ""}--><!--{$arrStatus[cnt].payment_date|sfDispDBDate:false}--><!--{else}-->未入金<!--{/if}--></td>
                <td><!--{if $arrStatus[cnt].status eq 5}--><!--{$arrStatus[cnt].commit_date|sfDispDBDate:false}--><!--{else}-->未発送<!--{/if}--></td>
            </tr>
            <!--{/section}-->
        </table>

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

    if(fm.change_sbivt_status.options[document.form1.change_sbivt_status.selectedIndex].value == ""){
    selectflag = 1;
    }

    if(selectflag == 1){
        alert('セレクトボックスが選択されていません');
        return false;
    }
    var i;
    var checkflag = 0;
    var max = fm["move[]"].length;
    var checkCnt = 0;

    if(max) {
        for (i=0;i<max;i++){
            if(fm["move[]"][i].checked == true){
                checkflag = 1;
                checkCnt++;
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
    var limitCnt = <!--{$smarty.const.MDL_SBIVT3G_MODIFY_STATUS_LIMIT}-->;
    if (checkCnt > limitCnt) {
        alert('更新は一度に' + limitCnt + '件まででお願いします');
        return false;
    }

    if(selectflag == 0 && checkflag == 1){
    document.form1.mode.value = 'update';
    document.form1.submit();
    }
}
//-->
</script>
