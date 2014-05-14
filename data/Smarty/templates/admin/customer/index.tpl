<!--{* -*- coding: utf-8-unix; -*- *}-->
<script type="text/javascript">
<!--

    function fnDelete(customer_id) {
        if (confirm('この顧客情報を削除しても宜しいですか？')) {
            document.form1.mode.value = "delete"
            document.form1['edit_customer_id'].value = customer_id;
            document.form1.submit();
            return false;
        }
    }

    function fnEdit(customer_id) {
        document.form1.action = './edit.php';
        document.form1.mode.value = "edit_search"
        document.form1['edit_customer_id'].value = customer_id;
        document.form1.search_pageno.value = 1;
        document.form1.submit();
        return false;
    }

    function fnReSendMail(customer_id) {
        if (confirm('仮登録メールを再送しても宜しいですか？')) {
            document.form1.mode.value = "resend_mail"
            document.form1['edit_customer_id'].value = customer_id;
            document.form1.submit();
            return false;
        }
    }
//-->
</script>

<div id="customer" class="contents-main">
<form name="search_form" id="search_form" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="search" />

    <h2>検索条件設定</h2>

    <!--検索条件設定テーブルここから-->
    <table class="form">
        <!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`/adminparts/form_customer_search.tpl"}-->
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
<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'delete' or $smarty.post.mode == 'resend_mail')}-->

<!--★★検索結果一覧★★-->
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="search" />
<input type="hidden" name="edit_customer_id" value="" />
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
        <!--検索結果-->
    </div>
    <!--{if count($arrData) > 0}-->

    <!--{include file=$tpl_pager}-->
<!--{*
    <div class="pagenumber_area">
    <ul class="navi"><!--{$objNavi->strnavi}--></ul>
    </div>
*}-->

    <!--検索結果表示テーブル-->
    <table class="list" id="customer-search-result">
        <colgroup width="10%">
        <colgroup width="15%">
        <colgroup width="5%">
        <colgroup width="25%">
        <colgroup width="15%">
        <colgroup width="7%">
        <tr>
            <th>顧客ID</th>
            <th rowspan="2">お名前/(フリガナ)</th>
            <th rowspan="2">性別</th>
            <th>TEL</th>
            <th rowspan="2">登録日</th>
            <th rowspan="2">詳細</th>
        </tr>
        <tr>
            <th>都道府県</th>
            <th>メールアドレス</th>
        </tr>
        <!--{foreach from=$arrData item=row}-->
            <tr <!--{if $row.del_flg eq 1}-->class="withdrawal"<!--{/if}-->>
                <td><!--{$row.customer_id|h}--></td>
                <td rowspan="2"><!--{$row.name|h}--><br />(<!--{$row.kana|h}-->)</td>
                <td class="center" rowspan="2"><!--{$arrSex[$row.sex]|h}--></td>
                <td><!--{$row.tel|h}--></td>
                <td rowspan="2"><!--{$row.create_date|h}--></td>
                <td class="center" rowspan="2"><span class="icon_edit"><a href="#" onclick="return fnEdit('<!--{$row.customer_id|h}-->');">詳細</a></span></td>
<!--{*
                <td class="center" rowspan="2"><span class="icon_delete"><a href="#" onclick="return fnDelete('<!--{$row.customer_id|h}-->');">削除</a></span></td>
*}-->
            </tr>
            <tr <!--{if $row.del_flg eq 1}-->class="withdrawal"<!--{/if}-->>
                <td><!--{assign var=pref value=$row.pref}--><!--{$arrPref[$pref]}--></td>
                <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
                  <td><!--{mailto address=$row.email encode="javascript"}--></td>
                <!--{else}-->
                  <td><!--{$row.email}--></td>
                <!--{/if}-->
            </tr>
        <!--{/foreach}-->
    </table>
    <!--検索結果表示テーブル-->

    <!--{/if}-->
</form>
<!--★★検索結果一覧★★-->

<!--{/if}-->
</div>
