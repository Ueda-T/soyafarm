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
(function($, window, document, undefined) {
// ロード中画像の先読み
var loading_img = new Image();
loading_img.src = '<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.USER_DIR}-->packages/default/img/ajax/loading.gif';

var Box = function() {};
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
    },
    hide: function() {
        $('#TB_load').hide();
        $('#TB_overlay').fadeOut('normal', function() {
            $('#TB_load').remove();
            $('#TB_overlay').remove();
            $('#TB_window').remove();
        });
    }
};
    var btn = function() {};
    btn.prototype = {
        executeSettled: function() {
            window.Box.gray();
            $.ajax({
                type: 'POST',
                cache: false,
                url: "<!--{$smarty.server.REQUEST_URI|h}-->",
                data: {
                    mode: 'execute_settled',
                    <!--{$smarty.const.TRANSACTION_ID_NAME}-->: '<!--{$transactionid}-->'
                },
                dataType: 'json',
                complete: window.btn.doComplete,
                error: window.btn.handleError,
                success: function(data, textStatus, jqXHR) {
                    if (data.header.rescd != '<!--{$smarty.const.MDL_SMBC_RES_OK}-->'){
                        // エラーが発生した場合
                        window.alert("請求確定でエラーが発生しました。\n\n"
                                     + '[' + data.header.rescd + ']' + data.header.res);
                        return false;
                    }
                    window.alert("請求確定が完了しました。");
                    return false;
                }
            });
            return false;
        },
        executeAuthorization: function() {
            window.Box.gray();
            $.ajax({
                type: 'POST',
                cache: false,
                url: "<!--{$smarty.server.REQUEST_URI|h}-->",
                data: {
                    mode: 'execute_authorization',
                    <!--{$smarty.const.TRANSACTION_ID_NAME}-->: '<!--{$transactionid}-->'
                },
                dataType: 'json',
                complete: window.btn.doComplete,
                error: window.btn.handleError,
                success: function(data, textStatus, jqXHR) {
                    if (data.header.rescd != '<!--{$smarty.const.MDL_SMBC_RES_OK}-->'){
                        // エラーが発生した場合
                        window.alert("与信結果取得でエラーが発生しました。\n\n"
                                     + '[' + data.header.rescd + ']' + data.header.res);
                        return false;
                    }
                    window.alert("与信結果取得が完了しました。");
                    return false;
                }
            });
            return false;
        },
        executeDelete: function(shoporder_no, bill_no, order_id) {
            window.Box.gray();
            $.ajax({
                type: 'POST',
                cache: false,
                url: "<!--{$smarty.server.REQUEST_URI|h}-->",
                data: {
                    mode: 'execute_delete',
                    <!--{$smarty.const.TRANSACTION_ID_NAME}-->: '<!--{$transactionid}-->',
                    shoporder_no: shoporder_no,
                    bill_no: bill_no,
                    order_id: order_id
                },
                dataType: 'json',
                complete: window.btn.doComplete,
                error: window.btn.handleError,
                success: function(data, textStatus, jqXHR) {
                    window.alert('[' + data.header.rescd + ']' + data.header.res);
                    window.fnFormModeSubmit('search_form', 'search', '', '');
                    return false;
                }
            });
            return false;
        },
        doUpdateInterval: function(order_id) {
            window.Box.gray();
            var regular_interval_from_year = $('select[name=regular_interval_from_year_' + order_id + ']').val()
            , regular_interval_from_month = $('select[name=regular_interval_from_month_' + order_id + ']').val()
            , regular_interval_to_year = $('select[name=regular_interval_to_year_' + order_id + ']').val()
            , regular_interval_to_month = $('select[name=regular_interval_to_month_' + order_id + ']').val();

            $.ajax({
                type: 'POST',
                cache: false,
                url: "<!--{$smarty.server.REQUEST_URI|h}-->",
                data: {
                    mode: 'update_interval',
                    <!--{$smarty.const.TRANSACTION_ID_NAME}-->: '<!--{$transactionid}-->',
                    order_id: order_id,
                    regular_interval_from_year: regular_interval_from_year,
                    regular_interval_from_month: regular_interval_from_month,
                    regular_interval_to_year: regular_interval_to_year,
                    regular_interval_to_month: regular_interval_to_month
                },
                dataType: 'json',
                complete: window.btn.doComplete,
                error: window.btn.handleError,
                success: function(data, textStatus, jqXHR) {
                    window.alert('[' + data.header.rescd + ']' + data.header.res);
                    if (data.header.rescd == '<!--{$smarty.const.MDL_SMBC_RES_OK}-->'){
                        window.fnFormModeSubmit('search_form', 'search', '', '');
                    }
                    return false;
                }
            });
            return false;
        },
        handleError: function(XMLHttpRequest, textStatus, errorThrown) {
            window.alert("エラーが発生しました\n" + textStatus);
        },
        doComplete: function() {
            window.Box.hide();
        }
    };
    window.btn = new btn();
    window.Box = new Box();
})(jQuery, this, this.document);
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
            <th>定期注文状況</th>
            <td>
                <!--{assign var=key value="search_regular_status"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                <option value="">選択してください</option>
                <!--{html_options options=$arrSmbcRegularStatus selected=$arrForm[$key].value}-->
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
    </table>

    <div class="btn">
        <p class="page_rows">検索結果表示件数
        <!--{assign var=key value="search_page_max"}-->
        <span class="attention"><!--{$arrErr[$key]}--></span>
        <select name="<!--{$arrForm[$key].keyname}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
        <!--{html_options options=$arrPageMax selected=$arrForm[$key].value}-->
        </select> 件</p>

      <p class="attention">※ 継続課金は後払いのため、利用期間の翌月に請求確定を行います。</p>
      <p class="attention">※ 休止期間を指定する場合、請求確定に対して休止する月を指定するので実際の利用期間とは1か月の差異があります。</p>
      <table class="list">
        <tr>
          <th>対象期間</th><th>請求確定締め日</th><th>休止に設定する日</th>
        </tr>
        <tr>
          <td class="center">【前年】12月1日〜12月末日</td><td class="center">1月13日</td><td class="center">1月</td>
        </tr>
        <tr>
          <td class="center">1月1日〜1月末日</td><td class="center">2月13日</td><td class="center">2月</td>
        </tr>
        <tr>
          <td class="center">2月1日〜2月末日</td><td class="center">3月13日</td><td class="center">3月</td>
        </tr>
        <tr>
          <td class="center">3月1日〜3月末日</td><td class="center">4月13日</td><td class="center">4月</td>
        </tr>
        <tr>
          <td class="center">4月1日〜4月末日</td><td class="center">5月13日</td><td class="center">5月</td>
        </tr>
        <tr>
          <td class="center">5月1日〜5月末日</td><td class="center">6月13日</td><td class="center">6月</td>
        </tr>
        <tr>
          <td class="center">6月1日〜6月末日</td><td class="center">7月13日</td><td class="center">7月</td>
        </tr>
        <tr>
          <td class="center">7月1日〜7月末日</td><td class="center">8月13日</td><td class="center">8月</td>
        </tr>
        <tr>
          <td class="center">8月1日〜8月末日</td><td class="center">9月13日</td><td class="center">9月</td>
        </tr>
        <tr>
          <td class="center">9月1日〜9月末日</td><td class="center">10月13日</td><td class="center">10月</td>
        </tr>
        <tr>
          <td class="center">10月1日〜10月末日</td><td class="center">11月13日</td><td class="center">11月</td>
        </tr>
        <tr>
          <td class="center">11月1日〜11月末日</td><td class="center">12月13日</td><td class="center">12月</td>
        </tr>
        <tr>
          <td class="center">12月1日〜12月末日</td><td class="center">【翌年】1月13日</td><td class="center">1月</td>
        </tr>
      </table>


        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('search_form', 'search', '', ''); return false;"><span class="btn-next">この条件で検索する</span></a></li>
            </ul>
        </div>
    </div>
    <!--検索条件設定テーブルここまで-->
</form>


<!--{if $arrError.rescd ne '' && $arrError.rescd ne $smarty.const.MDL_SMBC_RES_OK && $arrError.rescd ne $smarty.const.MDL_SMBC_RES_SECURE}-->
<table border="0" cellspacing="20" cellpadding="0" summary=" " align="left">
    <tr>
        <td>
            <span class="attention">エラーが発生しました。以下の内容をご確認ください。</span><br />
            <span class="attention">注文番号：<!--{ $arrError.order_id }--></span><br />
            <span class="attention"><!--{$arrError.rescd|escape}-->:<!--{$arrError.res|escape}--></span><br />
        </td>
    </tr>
</table>
<!--{/if}-->
<!--{if $errMsg ne ''}-->
<table border="0" cellspacing="0" cellpadding="0" summary=" " align="left">
    <tr>
        <td>
            <span class="attention">エラーが発生しました。以下の内容をご確認ください。</span><br />
            <span class="attention"><!--{$errMsg|escape}--></span><br />
        </td>
    </tr>
</table>
<!--{/if}-->

<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'send' or $smarty.post.mode == 'batch' or $smarty.post.mode == 'cancel' )}-->

<!--★★検索結果一覧★★-->
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="search" />
<!--{foreach key=key item=item from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/foreach}-->
    <h2>検索結果一覧</h2>
        <div class="btn">
        <span class="attention"><!--検索結果数--><!--{$tpl_linemax}-->件</span>&nbsp;が該当しました。
        <!--{*<a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('csv','',''); return false;">CSV ダウンロード</a>*}-->
        <a class="btn-normal" href="javascript:;" onclick="btn.executeSettled(); return false;">全件請求確定実行</a>
        <a class="btn-normal" href="javascript:;" onclick="btn.executeAuthorization(); return false;">全件与信結果取得</a>
   </div>
    <!--{if count($arrCreditData) > 0}-->

    <!--{include file=$tpl_pager}-->
    <p>※ 請求確定を実行した当日は、請求取消や金額変更は行えません。<p>
    <p>※ 削除は、即時請求が不能になります。解約のお申し込みを受けてもすぐ削除せず、ご利用分の請求確定が終わってから削除してください。</p>
    <!--{* 検索結果表示テーブル *}-->
    <table class="list">
        <col width="13%" />
        <col width="15%" />
        <col width="23%" />
        <col width="7%" />
        <col width="10%" />
        <col width="5%" />
        <tr>
            <th>受注番号</th>
            <th>注文日</th>
            <th rowspan="2">商品名/規格1/規格2</th>
            <th rowspan="2">購入回数</th>
            <th>お名前</th>
            <th rowspan="2">削除</th>
        </tr>
        <tr>
            <th>管理番号</th>
            <th>休止期間</th>
            <th>定期注文状況</th>
        </tr>
        <!--{foreach from=$arrCreditData item=array name=regular_order}-->
            <!--{assign var=status value="`$array.status`"}-->
        <tr>
<!--
            <td class="center" rowspan="2"><a href ="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnOpenWindow('./credit_edit.php?order_id=<!--{$array.order_id}-->&type=regular','order_disp','800','900'); return false;" ><!--{$array.shoporder_no|h}--></td>
-->
            <td class="center"><!--{$array.order_id}--></td>
            <td class="center"><!--{$array.create_date|sfDispDBDate:true}--></td>
            <td rowspan="2"><!--{$array.product_name}--></td>
            <td rowspan="2" class="center"><!--{$array.purchased|h}--></td>
            <td class="center"><!--{$array.name|escape}--></td>
            <td rowspan="2" class="center"><a href="javascript:;" onclick="btn.executeDelete('<!--{$array.shoporder_no|h}-->', '<!--{$array.bill_no|h}-->', '<!--{$array.order_id|h}-->'); return false;">削除</a></td>
        </tr>
        <tr>
	    <td  class="center"><!--{$array.shoporder_no|h}--></td>
            <td>
                <select name="regular_interval_from_year_<!--{$array.order_id|h}-->" style="<!--{$arrErr.search_eupdateyear|sfGetErrorColor}-->">
                    <option value="">----</option>
                    <!--{html_options options=$arrIntervalYear selected=$array.regular_interval_from|substr:0:4}-->
                </select>年
                <select name="regular_interval_from_month_<!--{$array.order_id|h}-->" style="<!--{$arrErr.search_eupdateyear|sfGetErrorColor}-->">
                    <option value="">--</option>
                    <!--{html_options options=$arrMonth selected=$array.regular_interval_from|substr:4:2}-->
                </select>月<br />〜<br />
                <select name="regular_interval_to_year_<!--{$array.order_id|h}-->" style="<!--{$arrErr.search_eupdateyear|sfGetErrorColor}-->">
                    <option value="">----</option>
                    <!--{html_options options=$arrIntervalYear selected=$array.regular_interval_to|substr:0:4}-->
                </select>年
                <select name="regular_interval_to_month_<!--{$array.order_id|h}-->" style="<!--{$arrErr.search_eupdateyear|sfGetErrorColor}-->">
                    <option value="">--</option>
                    <!--{html_options options=$arrMonth selected=$array.regular_interval_to|substr:4:2}-->
                </select>月
                <a href="javascript:;" onclick="btn.doUpdateInterval('<!--{$array.order_id|h}-->'); return false;">変更</a>
            </td>

            <td class="center">
              <!--{$arrSmbcRegularStatus[$array.regular_status]|h}-->
              <!--{if $array.rescd|@strlen > 0 && $array.rescd != $smarty.const.MDL_SMBC_RES_OK}-->
                <br /><span class="attention">[<!--{$array.rescd|h}-->]<!--{$array.res|h}--></span>
              <!--{/if}-->
            </td>
        </tr>
        <!--{/foreach}-->
    </table>
    <!--{* 検索結果表示テーブル *}-->
    <!--{/if}-->
</form>
<!--{/if}-->
</div>
