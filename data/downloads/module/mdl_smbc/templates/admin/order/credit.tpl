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
    function fnUncheckCheckToggle() {
        document.form1["checktoggle"].checked = false;
    }
    function fnCreditSubmit(order_id, payment_total) {
        if(!window.confirm("????????????????????????????????????\n????????????????????????")){
            return;
        }
        document.form1['mode'].value = "send";
        document.form1['order_id'].value = order_id;
        document.form1['payment_total'].value = payment_total;

        document.form1.submit();
        return false;
    }
    function fnCreditBatchSubmit() {
        var checkflag = 0;
        var fm = document.form1;
        var max = fm["batch_order_id[]"].length;

        if(max) {
            var i;
            for (i=0;i<max;i++){
                if(fm["batch_order_id[]"][i].checked == true){
                    checkflag = 1;
                }
            }
        } else if(fm["batch_order_id[]"].checked == true) {
            checkflag = 1;
        }

        if(checkflag == 0 && <!--{$arrForm.hidden_batch_order_id|@count}--> == 0){
            alert('??????????????????????????????????????????????????????');
            return false;
        }
        if(!window.confirm("????????????????????????????????????\n????????????????????????")){
            return;
        }
        document.form1['mode'].value = "batch";
        document.form1.submit();

        Box.gray();
        return false;
    }
    function fnCreditCancelSubmit() {
        var checkflag = 0;
        var fm = document.form1;
        var max = fm["cancel_order_id[]"].length;

        if(max) {
            var i;
            for (i=0;i<max;i++){
                if(fm["cancel_order_id[]"][i].checked == true){
                    checkflag = 1;
                }
            }
        } else if(fm["cancel_order_id[]"].checked == true) {
            checkflag = 1;
        }

        if(checkflag == 0 && <!--{$arrForm.hidden_cancel_order_id|@count}--> == 0){
            alert('??????????????????????????????????????????????????????');
            return false;
        }
        if(!window.confirm("????????????????????????????????????\n????????????????????????\n\n??????????????????????????????????????????????????????????????????")){
            return;
        }
        document.form1['mode'].value = "cancel";
        document.form1.submit();

        Box.gray();
        return false;
    }

(function() {
// ??????????????????????????????
var loading_img = new Image();
loading_img.src = '<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.USER_DIR}-->packages/default/img/ajax/loading.gif';

var Box = function() {}
Box.prototype = {
    // detect Mac and Firefox use.
    detectMacFF: function() {
        var ua = navigator.userAgent.toLowerCase();
        if (ua.indexOf('mac') != -1 && ua.indexOf('firefox') != -1) {
            return true;
        }
    },
    show_loading: function() {
        //if IE 6
        if (typeof document.body.style.maxHeight === "undefined") {
            $("body","html").css({height: "100%", width: "100%"});
            $("html").css("overflow","hidden");
            //iframe to hide select elements in ie6
            if (document.getElementById("TB_HideSelect") === null) {
                $("body").append("<iframe id='TB_HideSelect'></iframe><div id='TB_overlay'></div><div id='TB_window'></div>");
            }
        //all others
        } else {
            if(document.getElementById("TB_overlay") === null){
                $("body").append("<div id='TB_overlay'></div><div id='TB_window'></div>");
            }
        }

        if(this.detectMacFF()){
            //use png overlay so hide flash
            $("#TB_overlay").addClass("TB_overlayMacFFBGHack");
        } else {
            //use background and opacity
            $("#TB_overlay").addClass("TB_overlayBG");
        }

        //add and show loader to the page
        $("body").append(
              "<div id='TB_load'>"
            + "  <p style='color:#ffffff'>\u30B5\u30FC\u30D0\u3068\u901A\u4FE1\u4E2D\u3067\u3059</p>"
            + "  <img src='" + loading_img.src + "' />"
            + "</div>"
        );
        $('#TB_load').show();
    },
    gray: function() {
        this.show_loading();
    }
}
window.Box = new Box();
})();
//-->
</script>
<div id="order" class="contents-main">
<form name="search_form" id="search_form" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="search" />
    <h2>??????????????????</h2>
    <!--{* ?????????????????????????????????????????? *}-->
    <table>
        <tr>
            <th>????????????</th>
            <td colspan="3">
                <!--{assign var=key1 value="search_order_id1"}-->
                <!--{assign var=key2 value="search_order_id2"}-->
                <span class="attention"><!--{$arrErr[$key1]}--></span>
                <span class="attention"><!--{$arrErr[$key2]}--></span>
                <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" />
                ???
                <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" />
            </td>
        </tr>
        <tr>
            <th>????????????</th>
            <td>
                <!--{assign var=key value="search_order_status"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                <option value="">????????????????????????</option>
                <!--{html_options options=$arrORDERSTATUS selected=$arrForm[$key].value}-->
                </select>
            </td>
            <th>?????????????????????</th>
            <td>
                <!--{assign var=key value="search_credit_status"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                <option value="">????????????????????????</option>
                <!--{html_options options=$arrCREDITSTATUS selected=$arrForm[$key].value}-->
                </select>
            </td>
        </tr>
        <tr>
            <th>?????????</th>
            <td>
            <!--{assign var=key value="search_order_name"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
            <th>?????????(??????)</th>
            <td>
            <!--{assign var=key value="search_order_kana"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
        </tr>
        <tr>
            <th>?????????</th>
            <td colspan="3">
                <!--{if $arrErr.search_sorderyear}--><span class="attention"><!--{$arrErr.search_sorderyear}--></span><!--{/if}-->
                <!--{if $arrErr.search_eorderyear}--><span class="attention"><!--{$arrErr.search_eorderyear}--></span><!--{/if}-->
                <select name="search_sorderyear" style="<!--{$arrErr.search_sorderyear|sfGetErrorColor}-->">
                <option value="">----</option>
                <!--{html_options options=$arrRegistYear selected=$arrForm.search_sorderyear.value}-->
                </select>???
                <select name="search_sordermonth" style="<!--{$arrErr.search_sorderyear|sfGetErrorColor}-->">
                <option value="">--</option>
                <!--{html_options options=$arrMonth selected=$arrForm.search_sordermonth.value}-->
                </select>???
                <select name="search_sorderday" style="<!--{$arrErr.search_sorderyear|sfGetErrorColor}-->">
                <option value="">--</option>
                <!--{html_options options=$arrDay selected=$arrForm.search_sorderday.value}-->
                </select>??????
                <select name="search_eorderyear" style="<!--{$arrErr.search_eorderyear|sfGetErrorColor}-->">
                <option value="">----</option>
                <!--{html_options options=$arrRegistYear selected=$arrForm.search_eorderyear.value}-->
                </select>???
                <select name="search_eordermonth" style="<!--{$arrErr.search_eorderyear|sfGetErrorColor}-->">
                <option value="">--</option>
                <!--{html_options options=$arrMonth selected=$arrForm.search_eordermonth.value}-->
                </select>???
                <select name="search_eorderday" style="<!--{$arrErr.search_eorderyear|sfGetErrorColor}-->">
                <option value="">--</option>
                <!--{html_options options=$arrDay selected=$arrForm.search_eorderday.value}-->
                </select>???
            </td>
        </tr>
        <tr>
            <th>?????????</th>
            <td colspan="3">
                <!--{if $arrErr.search_supdateyear}--><span class="attention"><!--{$arrErr.search_supdateyear}--></span><!--{/if}-->
                <!--{if $arrErr.search_eupdateyear}--><span class="attention"><!--{$arrErr.search_eupdateyear}--></span><!--{/if}-->
                <select name="search_supdateyear" style="<!--{$arrErr.search_supdateyear|sfGetErrorColor}-->">
                    <option value="">----</option>
                    <!--{html_options options=$arrRegistYear selected=$arrForm.search_supdateyear.value}-->
                </select>???
                <select name="search_supdatemonth" style="<!--{$arrErr.search_supdateyear|sfGetErrorColor}-->">
                    <option value="">--</option>
                    <!--{html_options options=$arrMonth selected=$arrForm.search_supdatemonth.value}-->
                </select>???
                <select name="search_supdateday" style="<!--{$arrErr.search_supdateyear|sfGetErrorColor}-->">
                    <option value="">--</option>
                    <!--{html_options options=$arrDay selected=$arrForm.search_supdateday.value}-->
                </select>??????
                <select name="search_eupdateyear" style="<!--{$arrErr.search_eupdateyear|sfGetErrorColor}-->">
                    <option value="">----</option>
                    <!--{html_options options=$arrRegistYear selected=$arrForm.search_eupdateyear.value}-->
                </select>???
                <select name="search_eupdatemonth" style="<!--{$arrErr.search_eupdateyear|sfGetErrorColor}-->">
                    <option value="">--</option>
                    <!--{html_options options=$arrMonth selected=$arrForm.search_eupdatemonth.value}-->
                </select>???
                <select name="search_eupdateday" style="<!--{$arrErr.search_eupdateyear|sfGetErrorColor}-->">
                    <option value="">--</option>
                    <!--{html_options options=$arrDay selected=$arrForm.search_eupdateday.value}-->
                </select>???
            </td>
        </tr>
    </table>

    <div class="btn">
        <p class="page_rows">????????????????????????
        <!--{assign var=key value="search_page_max"}-->
        <span class="attention"><!--{$arrErr[$key]}--></span>
        <select name="<!--{$arrForm[$key].keyname}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
        <!--{html_options options=$arrPageMax selected=$arrForm[$key].value}-->
        </select> ???</p>
        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('search_form', 'search', '', ''); return false;"><span class="btn-next">???????????????????????????</span></a></li>
            </ul>
        </div>
    </div>
    <!--??????????????????????????????????????????-->
</form>

<!--{if $arrError.rescd ne '' && $arrError.rescd ne $smarty.const.MDL_SMBC_RES_OK && $arrError.rescd ne $smarty.const.MDL_SMBC_RES_SECURE}-->
<table border="0" cellspacing="20" cellpadding="0" summary=" " align="left">
    <tr>
        <td>
            <span class="attention">???????????????????????????????????????????????????????????????????????????</span><br />
            <span class="attention">???????????????<!--{ $arrError.order_id }--></span><br />
            <span class="attention"><!--{$arrError.rescd|escape}-->:<!--{$arrError.res|escape}--></span><br />
        </td>
    </tr>
</table>
<!--{/if}-->
<!--{if $errMsg ne ''}-->
<table border="0" cellspacing="0" cellpadding="0" summary=" " align="left">
    <tr>
        <td>
            <span class="attention">???????????????????????????????????????????????????????????????????????????</span><br />
            <span class="attention"><!--{$errMsg|escape}--></span><br />
        </td>
    </tr>
</table>
<!--{/if}-->

<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'send' or $smarty.post.mode == 'batch' or $smarty.post.mode == 'cancel' )}-->

<!--??????????????????????????????-->
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
    <h2>??????????????????</h2>
        <div class="btn">
        <span class="attention"><!--???????????????--><!--{$tpl_linemax}-->???</span>&nbsp;????????????????????????
   </div>
    <!--{if count($arrCreditData) > 0}-->

    <!--{include file=$tpl_pager}-->
    <p align="right">??? ???????????????????????????????????????????????????????????????????????????????????????<p>
    <!--{* ?????????????????????????????? *}-->
    <table class="list">
        <colgroup width="8%">
        <colgroup width="12%">
        <colgroup width="12%">
        <colgroup width="15%">
        <colgroup width="10%">
        <colgroup width="10%">
        <colgroup width="12%">
        <colgroup width="8%">
        <colgroup width="8%">
        <tr>
            <th>????????????</th>
            <th>????????????</th>
            <th>????????????</th>
            <th>?????????</th>
            <th>????????????</th>
            <th>????????????</th>
            <th>?????????????????????</th>
            <th nowrap>
                <input type="button" name="regist" value="????????????" onclick="fnCreditBatchSubmit();return false;"><br />
                <input type="checkbox" name="checktoggle" value="????????????" onclick="fnCheckToggle();">????????????
            </th>
            <th><input type="button" name="cancel" value="????????????" onclick="fnCreditCancelSubmit();return false;"></th>
        </tr>
        <!--{foreach from=$arrCreditData item=array}-->
            <!--{assign var=status value="`$array.status`"}-->
        <tr>
            <td class="center"><a href ="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnOpenWindow('./credit_edit.php?order_id=<!--{$array.order_id}-->','order_disp','800','900'); return false;" ><!--{$array.order_id}--></td>
            <td class="center"><!--{$array.create_date|sfDispDBDate:true}--></td>
            <td class="center"><!--{$array.update_date|sfDispDBDate:true}--></td>
            <td class="center"><!--{$array.order_name01|escape}--><!--{$array.order_name02|escape}--></td>
            <td class="center"><!--{$array.payment_total|number_format}--></td>
            <td class="center"><!--{$arrORDERSTATUS[$status]}--></td>
            <td class="center"><!--{ if $array.credit_status eq $smarty.const.MDL_SMBC_CREDIT_STATUS_YOSHIN }-->?????????<!--{ elseif $array.credit_status eq $smarty.const.MDL_SMBC_CREDIT_STATUS_KAKUTEI }-->????????????<!--{ else }-->????????????<!--{ /if }--></td>
            <td class="center">
            <!--{if $array.credit_status eq $smarty.const.MDL_SMBC_CREDIT_STATUS_YOSHIN}-->
                <input type="checkbox" name="batch_order_id[]" value="<!--{$array.order_id}-->" onclick="fnUncheckCheckToggle();">
            <!--{else}-->
                <input type="checkbox" name="batch_order_id[]" value="" disabled >
            <!--{/if}-->
            </td>
            <td class="center">
            <!--{if $array.credit_status eq $smarty.const.MDL_SMBC_CREDIT_STATUS_CANCEL}-->
                <input type="checkbox" name="cancel_order_id[]" value="" disabled >
            <!--{else}-->
                <input type="checkbox" name="cancel_order_id[]" value="<!--{$array.order_id}-->">
                <input type="hidden" name="batch_payment_total[<!--{$array.order_id}-->]" value="<!--{$array.payment_total}-->">
            <!--{/if}-->
            </td>
        </tr>
        <!--{/foreach}-->
    </table>
    <!--{* ?????????????????????????????? *}-->
    <!--{/if}-->
</form>
<!--{/if}-->
</div>
