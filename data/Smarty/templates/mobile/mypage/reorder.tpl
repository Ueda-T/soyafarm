<font size="-1">
<!--{$CustomerName|h}--> 様<br><br>
お客様番号:<!--{$CustomerId}--><br>
</font>
<!--★現在のポイント★-->
<!--{if $smarty.const.USE_POINT !== false && $CustomerPoint}-->
<table border="0" cellpadding="2" cellspacing="0" width="100%">
<tr>
<td bgcolor="#ff8a00"><font color="#ffffff" size="-1">
現在の通販ポイント残高:&nbsp;<!--{$CustomerPoint|number_format|default:"0"|h}-->&nbsp;ポイント
</td>
</tr>
<!--{if $CustomerPointValidDate neq ""}-->
<tr>
<td bgcolor="#ffe9cf">
<font color="#666666" size="-2">
<!--{$CustomerPointValidDate|date_format:"%Y年%m月%d日"}-->で<!--{$CustomerPoint|number_format|default:"0"|h}-->ポイントが消滅します。<br>
※期限までにご利用がない場合、ポイントは消滅します。</font>
</td>
</tr>
<!--{/if}-->
</table>
<!--{/if}-->

<br><br>

■チェックをつけて「買い物かごへ」ボタンをクリックすると、再注文できます。<br>

<form name="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="post_flg" value="1" />
<input type="hidden" name="product_cnt" value="<!--{$arrHistory|@count}-->" />
<input type="hidden" name="pageno" value="<!--{$objNavi->nowpage}-->" />
<!--{if $objNavi->all_row > 0}-->

<!--▼ページナビ-->
<!--{$objNavi->strnavi}-->
<!--▲ページナビ-->

<!--{assign var=product_cnt value=0}-->
<!--{section name=cnt loop=$arrHistory}-->
    <hr>

        <!--{if $arrHistory[cnt].product_valid_flg == '1'}-->
        <input type="checkbox" name="chk_product_<!--{$product_cnt|h}-->" value="<!--{$arrHistory[cnt].product_class_id|h}-->" >
        <!--{/if}-->
        <!--{$arrHistory[cnt].product_name}--> <!--{$arrHistory[cnt].classcategory_name1}--> <!--{$arrHistory[cnt].classcategory_name2}--><br>
        <!--{assign var=product_cnt value=$product_cnt+1}-->
    <!--{/section}-->
    <hr>
<!--{else}-->
    購入履歴はありません。<br>
<!--{/if}-->


<center><input type="submit" name="select" id="cart" value="カートに入れる"></center>

</form>
