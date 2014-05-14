<!--{* -*- coding: utf-8-unix; -*- *}-->
<script type="text/javascript">
<!--
    function fnSelectCheckSubmit(action){

        var fm = document.form1;

        if (!fm["pdf_order_id[]"]) {
            return false;
        }

        var checkflag = false;
        var max = fm["pdf_order_id[]"].length;

        if (max) {
            for (var i=0; i<max; i++) {
                if(fm["pdf_order_id[]"][i].checked == true){
                    checkflag = true;
                }
            }
        } else {
            if(fm["pdf_order_id[]"].checked == true) {
                checkflag = true;
            }
        }

        if(!checkflag){
            alert('チェックボックスが選択されていません');
            return false;
        }

        fnOpenPdfSettingPage(action);
    }

    function fnOpenPdfSettingPage(action){
        var fm = document.form1;
        win02("about:blank", "pdf_input", "620","650");

        // 退避
        tmpTarget = fm.target;
        tmpMode = fm.mode.value;
        tmpAction = fm.action;

        fm.target = "pdf_input";
        fm.mode.value = 'pdf';
        fm.action = action;
        fm.submit();
        WIN.focus();

        // 復元
        fm.target = tmpTarget;
        fm.mode.value = tmpMode;
        fm.action = tmpAction;
    }
//-->
$(document).ready(function(){
    $.spin.imageBasePath = '<!--{$TPL_URLPATH}-->img/spin1/';
    $('#spin1').spin({
        min: 0,
        interval: 100,
	timeInterval: 150
    });
    $('#spin2').spin({
        min: 0,
        interval: 100,
	timeInterval: 150
    });
});
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
            <td>
                <!--{assign var=key1 value="search_order_id1"}-->
                <!--{assign var=key2 value="search_order_id2"}-->
                <span class="attention"><!--{$arrErr[$key1]}--></span>
                <span class="attention"><!--{$arrErr[$key2]}--></span>
                <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" />
                ～ 
                <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" />
            </td>
            <th>対応状況</th>
            <td>
                <!--{assign var=key value="search_order_status"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                <option value="">選択してください</option>
                <!--{html_options options=$arrOrderStatus selected=$arrForm[$key].value}-->
                </select>
            </td>
        </tr>

        <tr>
            <th>顧客ID</th>
            <td>
            <!--{assign var=key value="search_customer_id"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
            <th>顧客コード(基幹)</th>
            <td>
            <!--{assign var=key value="search_customer_cd"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
        </tr>

        <tr>
            <th>お名前</th>
            <td>
            <!--{assign var=key value="search_order_name"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
            <th>お名前(フリガナ)</th>
            <td>
            <!--{assign var=key value="search_order_kana"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
        </tr>
        <tr>
            <th>メールアドレス</th>
            <td>
                <!--{assign var=key value="search_order_email"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
            <th>TEL</th>
            <td>
                <!--{assign var=key value="search_order_tel"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
        </tr>

        <!--{* ▼2011.04.26 検索条件から生年月日と性別を除外 *}-->
        <!--{if 0}-->
        <tr>
            <th>生年月日</th>
            <td colspan="3">
                <span class="attention"><!--{$arrErr.search_sbirthyear}--></span>
                <span class="attention"><!--{$arrErr.search_ebirthyear}--></span>
                <select name="search_sbirthyear" style="<!--{$arrErr.search_sbirthyear|sfGetErrorColor}-->">
                <option value="">----</option>
                <!--{html_options options=$arrBirthYear selected=$arrForm.search_sbirthyear.value}-->
                </select>年
                <select name="search_sbirthmonth" style="<!--{$arrErr.search_sbirthyear|sfGetErrorColor}-->">
                <option value="">--</option>
                <!--{html_options options=$arrMonth selected=$arrForm.search_sbirthmonth.value}-->
                </select>月
                <select name="search_sbirthday" style="<!--{$arrErr.search_sbirthyear|sfGetErrorColor}-->">
                <option value="">--</option>
                <!--{html_options options=$arrDay selected=$arrForm.search_sbirthday.value}-->
                </select>日～
                <select name="search_ebirthyear" style="<!--{$arrErr.search_ebirthyear|sfGetErrorColor}-->">
                <option value="">----</option>
                <!--{html_options options=$arrBirthYear selected=$arrForm.search_ebirthyear.value}-->
                </select>年
                <select name="search_ebirthmonth" style="<!--{$arrErr.search_ebirthyear|sfGetErrorColor}-->">
                <option value="">--</option>
                <!--{html_options options=$arrMonth selected=$arrForm.search_ebirthmonth.value}-->
                </select>月
                <select name="search_ebirthday" style="<!--{$arrErr.search_ebirthyear|sfGetErrorColor}-->">
                <option value="">--</option>
                <!--{html_options options=$arrDay selected=$arrForm.search_ebirthday.value}-->
                </select>日
            </td>
        </tr>
        <tr>
            <th>性別</th>
            <td colspan="3">
            <!--{assign var=key value="search_order_sex"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <!--{html_checkboxes name="$key" options=$arrSex selected=$arrForm[$key].value}-->
            </td>
        </tr>
        <!--{/if}-->
        <!--{* ▲2011.04.26 検索条件から生年月日と性別を除外 *}-->

        <tr>
            <th>支払方法</th>
            <td colspan="3">
            <!--{assign var=key value="search_payment_id"}-->
            <span class="attention"><!--{$arrErr[$key]|h}--></span>
            <!--{html_checkboxes name="$key" options=$arrPayments selected=$arrForm[$key].value}-->
            </td>
        </tr>
        
        <!--{* ▼2011.05.09 検索条件にお届け日指定を追加 *}-->
        <tr>
          <th>お届日指定</th>
          <td colspan="3">
            <span class="attention"><!--{$arrErr.search_sdelivyear}--></span>
            <span class="attention"><!--{$arrErr.search_edelivyear}--></span>
            <select name="search_sdelivyear"    style="<!--{$arrErr.search_sdelivyear|sfGetErrorColor}-->">
            <option value="">----</option>
              <!--{html_options options=$arrDelivYear selected=$arrForm.search_sdelivyear.value}-->
            </select>年
            <select name="search_sdelivmonth" style="<!--{$arrErr.search_sdelivyear|sfGetErrorColor}-->">
              <option value="">--</option>
              <!--{html_options options=$arrMonth selected=$arrForm.search_sdelivmonth.value}-->
            </select>月
            <select name="search_sdelivday" style="<!--{$arrErr.search_sdelivyear|sfGetErrorColor}-->">
              <option value="">--</option>
              <!--{html_options options=$arrDay selected=$arrForm.search_sdelivday.value}-->
            </select>日～
            <select name="search_edelivyear" style="<!--{$arrErr.search_edelivyear|sfGetErrorColor}-->">
              <option value="">----</option>
              <!--{html_options options=$arrDelivYear selected=$arrForm.search_edelivyear.value}-->
            </select>年
            <select name="search_edelivmonth" style="<!--{$arrErr.search_edelivyear|sfGetErrorColor}-->">
              <option value="">--</option>
              <!--{html_options options=$arrMonth selected=$arrForm.search_edelivmonth.value}-->
            </select>月
            <select name="search_edelivday" style="<!--{$arrErr.search_edelivyear|sfGetErrorColor}-->">
              <option value="">--</option>
              <!--{html_options options=$arrDay selected=$arrForm.search_edelivday.value}-->
            </select>日
          </td>
        </tr>
        <!--{* ▲2011.05.09 検索条件にお届け日指定を追加 *}-->

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
            <th>購入金額</th>
            <td>
                <!--{assign var=key1 value="search_total1"}-->
                <!--{assign var=key2 value="search_total2"}-->
                <span class="attention"><!--{$arrErr[$key1]}--></span>
                <span class="attention"><!--{$arrErr[$key2]}--></span>
                <input type="text" id="spin1" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" />
                円 ～ 
                <input type="text" id="spin2" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" />
                円
            </td>
            <th>キャンペーンコード</th>
            <td>
                <!--{assign var=key value="search_campaign_cd"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
	    </td>
        </tr>

        <tr>
            <th>商品コード</th>
            <td>
            <!--{assign var=key value="search_product_cd"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
            <th>商品名</th>
            <td>
                <!--{assign var=key value="search_product_name"}-->
                <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="6" class="box30" />
            </td>
        </tr>
        <tr>
            <th>基幹連携</th>
            <td colspan="3">
            <!--{assign var=key value="search_kikan_flg"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <!--{html_checkboxes name="$key" options=$arrKikanFlg selected=$arrForm[$key].value}-->
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
        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('search_form', 'search', '', ''); return false;"><span class="btn-next">この条件で検索する</span></a></li>
            </ul>
        </div>
    </div>
    <!--検索条件設定テーブルここまで-->
</form>

<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'delete')}-->

<!--★★検索結果一覧★★-->
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="search" />
<input type="hidden" name="order_id" value="" />
<!--{foreach key=key item=item from=$arrHidden}-->
    <!--{if is_array($item)}-->
        <!--{foreach item=c_item from=$item}-->
        <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
        <!--{/foreach}-->
    <!--{else}-->
        <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
    <!--{/if}-->
<!--{/foreach}-->
    <h2>検索結果一覧</h2>
        <div class="btn">
        <span class="attention"><!--検索結果数--><!--{$tpl_linemax}-->件</span>&nbsp;が該当しました。
        <!--{if $smarty.const.ADMIN_MODE == '1'}-->
        <a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('delete_all','',''); return false;"><span>検索結果をすべて削除</span></a>
        <!--{/if}-->
        <!--{if ($tpl_csv_download_auth == $smarty.const.CSV_DOWNLOAD_AUTH_ON)}-->
        <a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('csv','',''); return false;">CSV ダウンロード</a>
        <!--{/if}-->
		<!--{* 2013.06.21 modified by iqueve.
        <a class="btn-normal" href="javascript:;" onclick="fnSelectCheckSubmit('pdf.php'); return false;"><span>PDF一括出力</span></a>
        *}-->
    </div>
    <!--{if count($arrResults) > 0}-->

    <!--{include file=$tpl_pager}-->

    <!--{* 検索結果表示テーブル *}-->
        <table class="list">
        <colgroup width="10%">
        <colgroup width="10%">
        <colgroup width="15%">
        <colgroup width="10%">
        <colgroup width="10%">
        <colgroup width="10%">
        <colgroup width="10%">
        <colgroup width="10%">
		<!--{* 2013.06.21 modified by iqueve.
        <colgroup width="10%">
		*}-->
<!--{*
        <colgroup width="5%">
        <colgroup width="5%">
        <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
        <colgroup width="5%">
        <!--{/if}-->
*}-->
        <!--{* ペイジェントモジュール連携用 *}-->
        <!--{assign var=path value=`$smarty.const.MODULE_REALDIR`mdl_paygent/paygent_order_index.tpl}-->
        <!--{if file_exists($path)}-->
            <!--{include file=$path}-->
        <!--{else}-->
       
        <tr>
            <th rowspan="2">受注日</th>
            <th rowspan="2">注文番号</th>
            <th rowspan="2">顧客名</th>
            <th rowspan="2">状況</th>
            <th rowspan="2">支払方法</th>
            <th rowspan="2">購入金額(円)</th>
            <th style="font-size:85%">全商品発送日</th>
			<!--{* 2013.06.21 modified by iqueve.
            <th rowspan="2"><label for="pdf_check">帳票</label> <input type="checkbox" name="pdf_check" id="pdf_check" onclick="fnAllCheck('#pdf_check', 'input[name^=pdf_order_id]')" /></th>
			*}-->
            <th rowspan="2">詳細</th>
<!--{*
            <th rowspan="2">対応状況</th>
            <th rowspan="2">メール</th>
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <th rowspan="2">削除</th>
            <!--{/if}-->
*}-->
        </tr>
        <tr>
          <th style="font-size:85%">お届け日指定</th>
        </tr>


        <!--{section name=cnt loop=$arrResults}-->
        <!--{assign var=status value="`$arrResults[cnt].status`"}-->
        <!--{math equation="x + y" x=$arrShippingDate[cnt]|@count y=1 assign=num}-->
        <tr style="background:<!--{$arrOrderStatusColor[$status]}-->;">
            <td rowspan="<!--{$num}-->" class="center" nowrap><!--{$arrResults[cnt].create_date|sfDispDBDate}--></td>
            <td rowspan="<!--{$num}-->" class="center"><!--{$arrResults[cnt].order_id}--></td>
            <td rowspan="<!--{$num}-->" ><a href="../customer/edit.php?mode=edit_search&edit_customer_id=<!--{$arrResults[cnt].customer_id}-->"><span class="icon_edit"><!--{$arrResults[cnt].order_name|h}--></span></a></td>
            <!--{assign var=val value=$arrResults[cnt].status}-->
            <td rowspan="<!--{$num}-->" class="center"><!--{$arrORDERSTATUS[$val]}--></td>

            <!--{assign var=payment_id value="`$arrResults[cnt].payment_id`"}-->
            <td rowspan="<!--{$num}-->" class="center"><!--{$arrPayments[$payment_id]}--></td>
            <td rowspan="<!--{$num}-->" class="right"><!--{$arrResults[cnt].total|number_format}--></td>
            <td class="center"><!--{$arrResults[cnt].commit_date|sfDispDBDate|date_format:"%Y/%m/%d"|default:"未発送"}--></td>
			<!--{* 2013.06.21 modified by iqueve.
            <td rowspan="<!--{$num}-->" class="center">
                <input type="checkbox" name="pdf_order_id[]" value="<!--{$arrResults[cnt].order_id}-->" id="pdf_order_id_<!--{$arrResults[cnt].order_id}-->"/><label for="pdf_order_id_<!--{$arrResults[cnt].order_id}-->">一括出力</label><br>
                <a href="./" onClick="win02('pdf.php?order_id=<!--{$arrResults[cnt].order_id}-->','pdf_input','620','650'); return false;"><span class="icon_class">個別出力</span></a>
            </td>
			*}-->

			<!--{*
            <td rowspan="<!--{$num}-->" class="center"><a href="?" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_ORDER_EDIT_URLPATH}-->'); fnModeSubmit('pre_edit', 'order_id', '<!--{$arrResults[cnt].order_id}-->'); return false;"><span class="icon_edit">編集</span></a></td>
			*}-->
            <td rowspan="<!--{$num}-->" class="center"><a href="?" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_ORDER_VIEW_URLPATH}-->'); fnModeSubmit('pre_edit', 'order_id', '<!--{$arrResults[cnt].order_id}-->'); return false;"><span class="icon_edit">詳細</span></a></td>

<!--{*
            <td rowspan="<!--{$num}-->" class="center"><!--{$arrORDERSTATUS[$status]}--></td>
            <td rowspan="<!--{$num}-->" class="center">
                <!--{if $arrResults[cnt].order_email|strlen >= 1}-->
                    <a href="?" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_ORDER_MAIL_URLPATH}-->'); fnModeSubmit('pre_edit', 'order_id', '<!--{$arrResults[cnt].order_id}-->'); return false;"><span class="icon_mail">通知</span></a>
                <!--{/if}-->
            </td>
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <td rowspan="<!--{$num}-->" class="center"><a href="?" onclick="fnModeSubmit('delete_order', 'order_id', <!--{$arrResults[cnt].order_id}-->); return false;"><span class="icon_delete">削除</span></a></td>
            <!--{/if}-->
*}-->
          </tr>
          <!--{*2011.04.27 お届け日指定 *}-->
          <!--{foreach key=key1 item=item1 from=$arrShippingDate[cnt] }-->
          <tr style="background:<!--{$arrOrderStatusColor[$status]}-->;">
            <!--{if $item1 == NULL}-->
              <td class="center">指定なし</td>
            <!--{else}-->
              <td class="center"><!--{$item1|date_format:"%Y/%m/%d"}--></td>
            <!--{/if}-->
          </tr>
          <!--{/foreach}-->
            <!--{*2011.04.27 お届け日指定 *}-->
        <!--{/section}-->
        <!--{/if}-->
    </table>
    <!--{* 検索結果表示テーブル *}-->
    <!--{/if}-->
</form>
<!--{/if}-->
</div>
