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
    function fnCardCheck(customer_id) {
        document.form1['mode'].value = "check";
        document.form1['customer_id'].value = customer_id;
        document.form1.submit();
        return false;
    }
    function fnCardDelete(customer_id) {
        if(!window.confirm("削除処理を行います。\nよろしいですか？")){
            return;
        }
        document.form1['mode'].value = "delete";
        document.form1['customer_id'].value = customer_id;
        document.form1.submit();
        return false;
    }
//-->
</script>
<div id="order" class="contents-main">
<form name="search_form" id="search_form" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="search" />
<!--{foreach key=key item=item from=$arrCard}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/foreach}-->
    <h2>検索条件設定</h2>
    <!--{* 検索条件設定テーブルここから *}-->
        <table>
          <tr>
            <th>顧客ID</th>
            <td>
            <!--{assign var=key value="search_customer_id"}-->
            <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><br /><!--{/if}-->
            <input type="text" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" value="<!--{$arrForm[$key].value|h}-->" size="30" class="box30" <!--{if $arrErr[$key]}--><!--{sfSetErrorStyle}--><!--{/if}--> /></td>
            <th>都道府県</th>
            <td>
                <!--{assign var=key value="search_pref"}-->
                <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><br /><!--{/if}-->
                <select class="top" name="<!--{$key}-->">
                    <option value="" selected="selected" <!--{if $arrErr[$key]}--><!--{sfSetErrorStyle}--><!--{/if}-->>都道府県を選択</option>
                    <!--{html_options options=$arrPref selected=$arrForm[$key].value}-->
                </select>
            </td>
        </tr>
        <tr>
            <th>顧客名</th>
            <td>
                    <!--{assign var=key value="search_order_name"}-->
                    <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><br /><!--{/if}-->
                    <input type="text" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" value="<!--{$arrForm[$key].value|h}-->" size="30" class="box30" <!--{if $arrErr[$key]}--><!--{sfSetErrorStyle}--><!--{/if}--> />
            </td>
            <th>顧客名(カナ)</th>
            <td>
                <!--{assign var=key value="search_order_kana"}-->
                <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><br /><!--{/if}-->
                <input type="text" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" value="<!--{$arrForm[$key].value|h}-->" size="30" class="box30" <!--{if $arrErr[$key]}--><!--{sfSetErrorStyle}--><!--{/if}--> />
            </td>
        </tr>
        <tr>
            <th>メールアドレス</th>
            <td colspan="3">
            <!--{assign var=key value="search_email"}-->
            <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
            <input type="text" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" value="<!--{$arrForm[$key].value|h}-->" size="60" class="box60" <!--{if $arrErr[$key]}--><!--{sfSetErrorStyle}--><!--{/if}-->/>
            </td>
        </tr>
        <tr>
            <th>電話番号</th>
            <td colspan="3">
                <!--{assign var=key value="search_tel"}-->
                <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><br /><!--{/if}-->
                <input type="text" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" value="<!--{$arrForm[$key].value|h}-->" size="60" class="box60" /></td>
        </tr>
        <tr>
            <th>登録・更新日</th>
            <td colspan="3">
            <!--{assign var=errkey1 value="search_syear"}-->
            <!--{assign var=errkey2 value="search_eyear"}-->
                <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><span class="attention"><!--{$arrErr[$errkey1]}--><!--{$arrErr[$errkey2]}--></span><br /><!--{/if}-->
                <!--{assign var=key value="search_syear"}-->
                <select name="<!--{$key}-->" <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
                    <option value="" selected="selected">----</option>
                    <!--{html_options options=$arrYear selected=$arrForm[$key].value}-->
                </select>年
                <!--{assign var=key value="search_smonth"}-->
                <select name="<!--{$key}-->" <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
                    <option value="" selected="selected">--</option>
                    <!--{html_options options=$arrMonth selected=$arrForm[$key].value}-->
                </select>月
                <!--{assign var=key value="search_sday"}-->
                <select name="<!--{$key}-->" <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
                    <option value="" selected="selected">--</option>
                    <!--{html_options options=$arrDay selected=$arrForm[$key].value}-->
                </select>日～
                <!--{assign var=key value="search_eyear"}-->
                <select name="<!--{$key}-->" <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
                    <option value="" selected="selected">----</option>
                    <!--{html_options options=$arrYear selected=$arrForm[$key].value}-->
                </select>年
                <!--{assign var=key value="search_emonth"}-->
                <select name="<!--{$key}-->" <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
                    <option value="" selected="selected">--</option>
                    <!--{html_options options=$arrMonth selected=$arrForm[$key].value}-->
                </select>月
                <!--{assign var=key value="search_eday"}-->
                <select name="<!--{$key}-->" <!--{if $arrErr[$errkey1] || $arrErr[$errkey2]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
                    <option value="" selected="selected">--</option>
                    <!--{html_options options=$arrDay selected=$arrForm[$key].value}-->
                </select>日
            </td>
        </tr>
    </table>
    <div class="btn">
        <p class="page_rows">検索結果表示件数
        <select name="search_page_max">
            <!--{html_options options=$arrPageMax selected=$arrForm.search_page_max}-->
        </select> 件</p>
        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('search_form', 'search', '', ''); return false;"><span class="btn-next">この条件で検索する</span></a></li>
            </ul>
        </div>
    </div>
</form>

<!--{if $arrError.rescd ne '' && $arrError.rescd ne $smarty.const.MDL_SMBC_RES_OK && $arrError.rescd ne $smarty.const.MDL_SMBC_RES_SECURE}-->
<table border="0" cellspacing="20" cellpadding="0" summary=" " align="left">
    <tr>
        <td>
            <span class="red12">エラーが発生しました。以下の内容をご確認ください。</span><br />
            <span class="red12">顧客コード：<!--{ $arrError.customer_id }--></span><br />
            <span class="red12"><!--{$arrError.rescd|escape}-->:<!--{$arrError.res|escape}--></span><br />
        </td>
    </tr>
</table>
<!--{/if}-->
<!--{if $errMsg ne ''}-->
<table border="0" cellspacing="0" cellpadding="0" summary=" " align="left">
    <tr>
        <td>
            <span class="red12">エラーが発生しました。以下の内容をご確認ください。</span><br />
            <span class="red12"><!--{$errMsg|escape}--></span><br />
        </td>
    </tr>
</table>
<!--{/if}-->

<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'check' or $smarty.post.mode == 'delete') }-->

<!--★★検索結果一覧★★-->
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="search" />
<input type="hidden" name="customer_id" value="" />
<!--{foreach key=key item=item from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/foreach}-->
<!--{foreach key=key item=item from=$arrCard}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/foreach}-->
    <h2>検索結果一覧</h2>
    <div class="btn">
        <span class="attention"><!--検索結果数--><!--{$tpl_linemax}-->件</span>&nbsp;が該当しました。
    </div>
    <!--{if count($search_data) > 0}-->

    <!--{include file=$tpl_pager}-->

    <!--検索結果表示テーブル-->
    <table class="list" id="customer-search-result">
        <colgroup width="10%">
        <colgroup width="30%">
        <colgroup width="30%">
        <colgroup width="15%">
        <colgroup width="10%">
        <tr>
            <th rowspan="2">顧客ID</th>
            <th>顧客名/(カナ)</th>
            <th>TEL</th>
            <th rowspan="2">クレジットカード番号</th>
            <th rowspan="2">確認／削除</th>
        </tr>
        <tr>
            <th>住所</th>
            <th>メールアドレス</th>
        </tr>
        <!--{section name=data loop=$search_data}-->
        <tr>
            <td rowspan="2"><!--{$search_data[data].customer_id|escape}--></td>
            <td><!--{$search_data[data].name01|escape}--> <!--{$search_data[data].name02|escape}-->(<!--{$search_data[data].kana01|escape}--> <!--{$search_data[data].kana02|escape}-->)</td>
            <td><!--{$search_data[data].tel01|escape}-->-<!--{$search_data[data].tel02|escape}-->-<!--{$search_data[data].tel03|escape}--></td>
            <!--{assign var=key value="card_"|cat:$search_data[data].customer_id}-->
            <!--{ if $arrCard[$key] == 1 }-->
            <td class="center" rowspan="2">登録なし</td>
            <td class="center" rowspan="2">-</td>
            <!--{elseif $arrCard[$key] == 2 }-->
            <td class="center" rowspan="2">登録あり</td>
            <td class="center" rowspan="2"><a href="#" onclick="fnCardDelete('<!--{$search_data[data].customer_id}-->');return false;">削除</a></td>
            <!--{else}-->
            <td rowspan="2" align="center">&nbsp;</td>
            <td rowspan="2" align="center"><a href="#" onclick="fnCardCheck('<!--{$search_data[data].customer_id}-->');return false;">確認</a></td>
            <!--{/if}-->
        </tr>
        <tr>
            <td><!--{assign var=key value=$search_data[data].pref}--><!--{$arrPref[$key]}--> <!--{$search_data[data].addr01|escape}--> <!--{$search_data[data].addr02|escape}--></td>
            <td><!--{mailto address=$search_data[data].email encode="javascript"}--></td>
        </tr>
        <!--{/section}-->
    </table>
    <!--検索結果表示テーブル-->

    <!--{/if}-->
</form>
<!--★★検索結果一覧★★-->
<!--{/if}-->
</div>
