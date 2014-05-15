<!--{* -*- coding: utf-8-unix; -*- *}-->
<script type="text/javascript">
<!--
    function fnReturn() {
        document.search_form.action = './<!--{$smarty.const.DIR_INDEX_PATH}-->';
        document.search_form.submit();
        return false;
    }

    function fnOrderidSubmit(order_id, order_id_value) {
        if(order_id != "" && order_id_value != "") {
            document.form2[order_id].value = order_id_value;
        }
        document.form2.action = '../order/edit.php';
        document.form2.submit();
    }
//-->
$(document).ready(function(){
    $.spin.imageBasePath = '<!--{$TPL_URLPATH}-->img/spin1/';
    $('#spin1').spin({
        min: 0,
	timeInterval: 150
    });
    $('#spin2').spin({
        min: 0,
	timeInterval: 150
    });
});
</script>

<form name="search_form" method="post" action="">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="search" />

    <!--{foreach from=$arrSearchData key="key" item="item"}-->
        <!--{if $key ne "customer_id" && $key ne "mode" && $key ne "edit_customer_id" && $key ne $smarty.const.TRANSACTION_ID_NAME}-->
            <!--{if is_array($item)}-->
                <!--{foreach item=c_item from=$item}-->
                    <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
                <!--{/foreach}-->
            <!--{else}-->
                <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
            <!--{/if}-->
        <!--{/if}-->
    <!--{/foreach}-->
</form>

<form name="form1" id="form1" method="post" action="?" autocomplete="off">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="confirm" />
    <input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id|h}-->" />

    <!-- 検索条件の保持 -->
    <!--{foreach from=$arrSearchData key="key" item="item"}-->
        <!--{if $key ne "customer_id" && $key ne "mode" && $key ne "edit_customer_id" && $key ne $smarty.const.TRANSACTION_ID_NAME && $key ne "search_page_max"}-->
            <!--{if is_array($item)}-->
                <!--{foreach item=c_item from=$item}-->
                    <input type="hidden" name="search_data[<!--{$key|h}-->][]" value="<!--{$c_item|h}-->" />
                <!--{/foreach}-->
            <!--{else}-->
                <input type="hidden" name="search_data[<!--{$key|h}-->]" value="<!--{$item|h}-->" />
            <!--{/if}-->
        <!--{/if}-->
    <!--{/foreach}-->

    <div id="customer" class="contents-main">
    <!--{if $arrForm.del_flg eq 1}--><span class="attention">※この顧客は退会されています</span><!--{/if}-->
        <table class="form">
            <!--{if $arrForm.customer_id}-->
            <tr>
                <th>顧客ID</th>
                <td><!--{$arrForm.customer_id|h}--></td>
            </tr>
            <!--{/if}-->
            <tr>
                <th>顧客名</th>
                <td>
                    <span class="attention"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></span>
                    <!--{$arrForm.name|h}-->
                    <input type="hidden" name="name" value="<!--{$arrForm.name|h}-->" />
                </td>
            </tr>
            <tr>
                <th>顧客名(カナ)</th>
                <td>
                    <span class="attention"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></span>
                    <!--{$arrForm.kana|h}-->
                    <input type="hidden" name="kana" value="<!--{$arrForm.kana|h}-->" />
                </td>
            </tr>
            <tr>
                <th>住所</th>
                <td>
                    〒 <!--{$arrForm.zip|h}--><br />
                    <input type="hidden" name="zip" value="<!--{$arrForm.zip|h}-->" />
                    <!--{$arrPref[$arrForm.pref]|h}-->
                    <input type="hidden" name="pref" value="<!--{$arrForm.pref}-->" /><br />
                    <!--{$arrForm.addr01|h}-->
                    <input type="hidden" name="addr01" value="<!--{$arrForm.addr01|h}-->" /><br />
                    <!--{$arrForm.addr02|h}-->
                    <input type="hidden" name="addr02" value="<!--{$arrForm.addr02|h}-->" /><br />
                </td>
            </tr>
            <tr>
                <th>メールアドレス</th>
                <td>
                    <span class="attention"><!--{$arrErr.email}--></span>
                    <!--{$arrForm.email|h}-->
                    <input type="hidden" name="email" value="<!--{$arrForm.email|h}-->" />
                </td>
            </tr>
            <tr>
                <th>TEL</th>
                <td>
                    <span class="attention"><!--{$arrErr.tel}--></span>
                    <!--{$arrForm.tel|h}-->
                    <input type="hidden" name="tel" value="<!--{$arrForm.tel|h}-->" />
                </td>
            </tr>
            <tr>
                <th>性別</th>
                <td>
                    <span class="attention"><!--{$arrErr.sex}--></span>
                    <!--{$arrSex[$arrForm.sex]|h}-->
                    <input type="hidden" name="sex" value="<!--{$arrForm.sex}-->" />
                </td>
            </tr>
            <tr>
                <th>生年月日</th>
                <td>
                    <span class="attention"><!--{$arrErr.year}--></span>
                    <!--{if strlen($arrForm.year) > 0 && strlen($arrForm.month) > 0 && strlen($arrForm.day) > 0}--><!--{$arrForm.year|h}-->年<!--{$arrForm.month|h}-->月<!--{$arrForm.day|h}-->日<!--{else}-->未登録<!--{/if}-->
                    <input type="hidden" name="year" value="<!--{$arrForm.year|h}-->" />
                    <input type="hidden" name="month" value="<!--{$arrForm.month|h}-->" />
                    <input type="hidden" name="day" value="<!--{$arrForm.day|h}-->" />
                </td>
            </tr>
            <tr>
                <th>パスワード</th>
                <td>
<!--{if $arrForm.del_flg eq 1}--><span class="attention">退会者のため変更できません</span><!--{else}-->
                    <span class="attention"><!--{$arrErr.password}--></span>
                    <input type="password" name="password" value="<!--{$arrForm.password|h}-->" size="30" class="box30" <!--{if $arrErr.password != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /><br />半角<!--{$smarty.const.PASSWORD_MIN_LEN}-->～<!--{$smarty.const.PASSWORD_MAX_LEN}-->文字<!--{/if}-->
                </td>
            </tr>
            <tr>
                <th>メールマガジン</th>
                <td>
                    <span class="attention"><!--{$arrErr.mailmaga_flg}--></span>
                    <!--{$arrMailMagazineType[$arrForm.mailmaga_flg]|h}-->
                    <input type="hidden" name="mailmaga_flg" value="<!--{$arrForm.mailmaga_flg|h}-->" />
                </td>
            </tr>

            <!--{if $smarty.const.USE_POINT !== false}-->
            <tr>
              <th>ポイント</th>
              <td>
                  <span class="attention"><!--{$arrErr.point}--></span>
                  <!--{$arrForm.point|h}-->
                  <input type="hidden" name="point" value="<!--{$arrForm.point|h}-->" /> pt
              </td>
            </tr>
            <!--{/if}-->

            <tr>
                <th>最終ログイン日時</th>
                <td>
                    <!--{$arrForm.lastlogin_date|h}-->
                    <input type="hidden" name="lastlogin_date" value="<!--{$arrForm.lastlogin_date|h}-->" />
                </td>
            </tr>
            <tr>
                <th>登録日時</th>
                <td>
                    <!--{$arrForm.create_date|h}-->
                    <input type="hidden" name="create_date" value="<!--{$arrForm.create_date|h}-->" />
                </td>
            </tr>
            <tr>
                <th>最終更新日時</th>
                <td>
                    <!--{$arrForm.update_date|h}-->
                    <input type="hidden" name="update_date" value="<!--{$arrForm.update_date|h}-->" />
                </td>
            </tr>
            <tr>
                <th>基幹顧客番号</th>
                <td>
                    <!--{$arrForm.customer_cd|h}-->
                    <input type="hidden" name="customer_cd" value="<!--{$arrForm.customer_cd|h}-->" />
                </td>
            </tr>
            <tr>
                <th>顧客区分</th>
                <td>
                    <!--{$arrCustomerKbn[$arrForm.customer_kbn]|h}-->
                    <input type="hidden" name="customer_kbn" value="<!--{$arrForm.customer_kbn|h}-->" />
                </td>
            </tr>
            <tr>
                <th>償却顧客区分</th>
                <td>
                    <!--{$arrKashidaoreKbn[$arrForm.kashidaore_kbn]|h}-->
                    <input type="hidden" name="kashidaore_kbn" value="<!--{$arrForm.kashidaore_kbn|h}-->" />
                </td>
            </tr>
            <tr>
                <th>顧客形態</th>
                <td>
                    <!--{$arrCustomerTypeCd[$arrForm.customer_type_cd]|h}-->
                    <input type="hidden" name="customer_type_cd" value="<!--{$arrForm.customer_type_cd|h}-->" />
                </td>
            </tr>
            <input type="hidden" name="dm_flg" value="<!--{$arrForm.dm_flg|h}-->" />
            <input type="hidden" name="agree" value="1" />
        </table>

        <p class="page_rows">検索結果表示件数
        <!--{assign var=key value="search_page_max"}-->
        <span class="attention"><!--{$arrErr[$key]}--></span>
        <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" onchange="fnNaviSearchPage(1, 'return'); return false;">
        <!--{html_options options=$arrPageMax selected=$arrForm[$key]}-->
        </select> 件</p>

        <div class="btn-area">
            <ul>
                <!--{if count($arrSearchData) > 0}-->
                <li><a class="btn-action" href="javascript:;" onclick="return fnReturn();"><span class="btn-prev">検索画面に戻る</span></a></li>
                <!--{/if}-->
                <!--{if $arrForm.del_flg ne 1}-->
                <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
                <li><a class="btn-action" href="javascript:;" onclick="fnSetFormSubmit('form1', 'mode', 'confirm'); return false;"><span class="btn-next">確認ページへ</span></a></li>
                <!--{/if}--><!--{/if}-->
            </ul>
        </div>

        <input type="hidden" name="order_id" value="" />
        <input type="hidden" name="search_pageno" value="<!--{$tpl_pageno}-->">
        <input type="hidden" name="edit_customer_id" value="<!--{$edit_customer_id}-->" >

        <!--{if $arrForm.customer_id != ""}-->
        <h2>購入履歴一覧</h2>
        <!--{if $tpl_linemax > 0}-->
        <p><span class="attention"><!--購入履歴一覧--><!--{$tpl_linemax}-->件</span>&nbsp;が該当しました。</p>

        <!--{include file=$tpl_pager}-->

            <!--{* 購入履歴一覧表示テーブル *}-->
            <table class="list">
                <tr>
                    <th>日付</th>
                    <th>注文番号</th>
                    <th>購入金額</th>
                    <th>発送日</th>
                    <th>支払方法</th>
                </tr>
                <!--{section name=cnt loop=$arrPurchaseHistory}-->
                    <tr>
                        <td><!--{$arrPurchaseHistory[cnt].create_date|sfDispDBDate}--></td>
                        <td class="center"><a href="#" onclick="fnOpenWindow('../order/disp.php?order_id=<!--{$arrPurchaseHistory[cnt].order_id}-->','order_disp','800','900'); return false;" ><!--{$arrPurchaseHistory[cnt].order_id}--></a></td>
                        <td class="center"><!--{$arrPurchaseHistory[cnt].payment_total|number_format}-->円</td>
                        <td class="center"><!--{if $arrPurchaseHistory[cnt].commit_date}--><!--{$arrPurchaseHistory[cnt].commit_date|sfDispDBDate}--><!--{else}-->未発送<!--{/if}--></td>
                        <!--{assign var=payment_id value="`$arrPurchaseHistory[cnt].payment_id`"}-->
                        <td class="center"><!--{$arrPayment[$payment_id]|h}--></td>
                    </tr>
                <!--{/section}-->
            </table>
            <!--{* 購入履歴一覧表示テーブル *}-->
        <!--{else}-->
            <div class="message">購入履歴はありません。</div>
        <!--{/if}-->
        <!--{/if}-->

    </div>
</form>
