<!--{if count($arrErr) == 0}-->

<div id="" class="contents-main">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
    <!--modeとトランザクションIDをhidden要素としてPOSTする-->
    <input type="hidden" name="mode" value="search">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="contact_id" value="">        
    <!--他にもPOSTする値があればhidden要素として繰り返す-->
    <!--{foreach key=key item=item from=$arrHidden}-->
        <input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
    <!--{/foreach}-->
    <h2>検索結果一覧</h2>
    <p><span class="attention"><!--検索結果数--><!--{$tpl_linemax}-->件</span>&nbsp;が該当しました。</p>
    <!--{include file=$tpl_pager}-->
    <table class="list">
	<!--デーブルの横幅の指定-->
        <colgroup width="10%">
        <colgroup width="10%">
        <colgroup width="25%">
        <colgroup width="20%">
        <colgroup width="15%">
        <colgroup width="15%">
        <colgroup width="5%">
        <tr>
            <th rowspan="2">状況</th>
            <th>名前</th>
            <th rowspan="2">内容</th>
            <th>メールアドレス</th>
            <th rowspan="2">受信日時</th>
            <th rowspan="2">住所</th>
            <th rowspan="2">操作</th>
        </tr>
        <tr>
            <th>会員ID</th>
            <th>TEL</th>
        </tr>
        <!--問い合わせ件数分だけ表示する-->
        <!--{section name=cnt loop=$arrResults}-->
            <tr>
                <td rowspan="2">
                    <a href   = "./contact_detail.php?contact_id=<!--{$arrResults[cnt].contact_id }-->">
                    <!--{if $arrResults[cnt].status == 0}-->
                        未読
                    <!--{elseif $arrResults[cnt].status == 1}-->
                        既読
                    <!--{elseif $arrResults[cnt].status == 2}-->
                        対応中
                    <!--{elseif $arrResults[cnt].status == 3}-->
                        対応済み
                    <!--{/if}-->
                    </a>
                </td>
                <td><!--{$arrResults[cnt].name01}--><!--{$arrResults[cnt].name02}--></td>
                <td rowspan="2"><!--{$arrResults[cnt].contents|truncate:60}--></td>
                <td><!--{$arrResults[cnt].email}--></td>
                <td rowspan="2"><!--{$arrResults[cnt].create_date}--></td>
		<!--{assign var=pref value=$arrResults[cnt].pref}-->
                <td rowspan="2">〒<!--{$arrResults[cnt].zip01}-->-<!--{$arrResults[cnt].zip02}--><br /><!--{$arrPref[$pref]}--><!--{$arrResults[cnt].addr01}--><!--{$arrResults[cnt].addr02}--></td>
		<td><a href = "./contact_reply.php?contact_id=<!--{$arrResults[cnt].contact_id }-->">返信</a></td>
            </tr>
            <tr>
                <td>
                    <!--会員の問い合わせなら会員番号を表示する-->
                    <!--{if $arrResults[cnt].customer_id  != NULL && $arrResults[cnt].customer_id != "" && $arrResults[cnt].customer_id != 0 }-->
                        <!--{$arrResults[cnt].customer_id}-->
                    <!--{else}-->
                        非会員
                    <!--{/if}-->
                </td>
                <td><!--{$arrResults[cnt].tel01}-->-<!--{$arrResults[cnt].tel02}-->-<!--{$arrResults[cnt].tel03}--></td>
                <td><a href="./contact.php?contact_id=<!--{$arrResults[cnt].contact_id}-->" onclick="fnModeSubmit('delete', 'contact_id', <!--{$arrResults[cnt].contact_id }-->);return false;"><span class="icon_delete">削除</span></a></td>
            </tr>
        <!--{/section}-->
    </table>
    <!--{include file=$tpl_pager}-->
</form>
<!--{/if}-->
</div>
