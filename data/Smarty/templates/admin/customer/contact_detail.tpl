<div id="" class="contents-main">

<h2>問い合わせ詳細</h2>
<form name="form2" id="form2" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
    <!--modeとトランザクションIDと会員番号をhidden要素としてPOSTする-->
    <input type="hidden" name="mode" value="confirm">
    <input type="hidden" name="contact_id" value="<!--{$list_data.contact_id|escape}-->">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />

    <table class="form">
        <tr>
            <th>送信日時</th>
            <td colspan="3"><!--{$list_data.create_date}--></td>
        </tr>
        <tr>
            <th>対応状況</th>
            <td align = "center">
                <select name="status" id="status">
                    <option value="1" <!--{if $list_data.status eq 1}-->selected="selected"<!--{/if}--> >既読　</option>
                    <option value="2" <!--{if $list_data.status eq 2}-->selected="selected"<!--{/if}--> >対応中</option>
                    <option value="3" <!--{if $list_data.status eq 3}-->selected="selected"<!--{/if}--> >対応済</option>
                </select>
                <input type="submit" alt="変更する" name="subm" value="変更する">
            </td>
            <th>顧客ID</th>
            <td>
                <!--会員の問い合わせなら会員番号を表示する-->
                <!--{if $list_data.customer_id  != NULL && $list_data.customer_id != "" }-->
                    <!--{$list_data.customer_id}-->
                <!--{else}-->
                    非会員
                <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>お名前</th>
            <td width="40%"><!--{$list_data.name01}--><!--{$list_data.name02}--></td>
            <th>電話番号</th>
            <td width="20%"><!--{$list_data.tel01}-->-<!--{$list_data.tel02}-->-<!--{$list_data.tel03}--></td>
        </tr>
        <tr>
            <th>ご住所</th>
            <td colspan = "3">〒<!--{$list_data.zip01}-->-<!--{$list_data.zip02}--><!--{$arrPref[$list_data.pref]}-->&nbsp;<!--{$list_data.addr01}--></td>
        </tr>
        <tr>
            <th>メールアドレス</th>
            <td colspan = "3"><!--{$list_data.email}--></td>
        </tr>
        <tr>
            <th colspan = "4">お問い合わせ内容</th>
        </tr>
        <tr>
            <td colspan = "4"><!--{$list_data.contents|nl2br}--></td>
        </tr>
        <tr>
            <td colspan = "4" align = "center">
                <!--▼検索結果へ戻る-->
                <a class="btn-action" href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->customer/contact.php"><span class="btn-prev">検索画面に戻る</span></a>
                <!--▲検索結果へ戻る-->
                <!--▼返信する-->
                <a class="btn-action" href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->customer/contact_reply.php?contact_id=<!--{$list_data.contact_id }-->"><span class="btn-prev">返信する</span></a>
                <!--▲返信する-->
            </td>
        </tr>
    </table>
</form>


<h2>問い合わせ履歴一覧</h2>
<p><span class="attention"><!--検索結果数--><!--{$tpl_linemax}-->件</span>&nbsp;が該当しました。</p>
<!--{if $tpl_linemax > 0}-->
    <form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->?contact_id=<!--{$list_data.contact_id}-->">
        <!--modeと会員番号とトランザクションIDと指定したページNoをhidden要素としてPOSTする-->
        <input type="hidden" name="mode" value="">
        <input type="hidden" name="contact_id" value="<!--{$list_data.contact_id}-->">
        <input type="hidden" name="search_pageno" value="<!--{$tpl_pageno}-->">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <!--{include file=$tpl_pager}-->
    </form>
    <table class="form">
        <tr>
            <th>日付</th>
            <th>問合せ番号</th>
            <th>内容</th>
            <th>対応状況</th>
        </tr>
        <!--{section name=cnt loop=$arrContactHistory}-->
            <tr>
                <td><!--{$arrContactHistory[cnt].create_date|sfDispDBDate}--></td>
                <td><a href="contact_detail.php?contact_id=<!--{$arrContactHistory[cnt].contact_id}-->"><!--{$arrContactHistory[cnt].contact_id}--></a></td>
                <td><!--{$arrContactHistory[cnt].contents|truncate:60}--></td>
                <td>
                    <!--{if $arrContactHistory[cnt].status eq 1}-->
                        既読
                    <!--{elseif $arrContactHistory[cnt].status eq 2}-->
                        対応中
                    <!--{elseif $arrContactHistory[cnt].status eq 3}-->
                        対応済
                    <!--{else}-->
                        未読
                    <!--{ /if }-->
                </td>
            </tr>
        <!--{/section}-->
    </table>
<!--{else}-->
    <p>問合せ履歴はありません。</p>
<!--{/if}-->

</div>
