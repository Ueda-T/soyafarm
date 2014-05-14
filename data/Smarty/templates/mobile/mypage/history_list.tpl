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

■購入履歴一覧<br>
<!--{if $objNavi->all_row > 0}-->
    <!--{$objNavi->all_row}-->件の購入履歴があります。<br>
    <br>
    <!--{section name=cnt loop=$arrOrder}-->
    <hr>
        ▽注文日<br>
        <!--{$arrOrder[cnt].create_date|date_format:"%Y年%m月%d日"}--><br>
        ▽オーダー番号<br>
        <a href="./history.php?order_id=<!--{$arrOrder[cnt].order_id}-->"><!--{$arrOrder[cnt].order_id}--></a><br>
        ▽ご購入金額<br>
        <font color="#ff0000"><!--{$arrOrder[cnt].payment_total|number_format}-->円</font><br>
        <!--{assign var=payment_id value="`$arrOrder[cnt].payment_id`"}-->
        ▽お支払い方法<br>
        <!--{$arrPayment[$payment_id]|h}--><br><br>
    <!--{/section}-->
    <hr>
<!--{else}-->
    購入履歴はありません。<br>
<!--{/if}-->

<!--{if $objNavi->strnavi != ""}-->
<!--{$objNavi->strnavi}-->
<br>
<!--{/if}-->
