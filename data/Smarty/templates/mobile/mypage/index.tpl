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

<br>

<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#b9d0dc">
<tr bgcolor="#dfedf5">
<th colspan="2" align="left"><h2><font size="-1">
ﾏｲﾍﾟｰｼﾞ&nbsp;ﾒﾆｭｰ
</font></h2></th>
</tr>
<tr bgcolor="#ffffff">
<td colspan="2"><font size="-1">
<font color="red">[emoji:69]</font><a href="<!--{$smarty.const.CART_URLPATH}-->">お買い物ｶｺﾞを見る</a>
</font></td>
</tr>
<tr bgcolor="#ffffff">
<td colspan="2"><font size="-1">
[emoji:176]<a href="history_list.php">購入履歴</a><br>
└<a href="regular.php">定期購入履歴</a><br>
<font color="#666666">現在のご注文完了された商品の配送手続き状況及び､過去一年間の注文内容を確認できます｡</font>
</font></td>
<tr bgcolor="#ffffff">
<td colspan="2"><font size="-1">
[emoji:176]<a href="reorder.php">過去注文からのご購入</a><br>
<font color="#666666">過去にご注文された商品を再注文できます｡</font>
</font></td>
</tr>
<tr bgcolor="#ffffff">
<td colspan="2"><font size="-1">
&#xE6B1;<a href="change.php">ご登録内容の変更</a><br>
<font color="#666666">お客様情報を変更できます｡</font>
</font></td>
</tr>
<tr bgcolor="#ffffff">
<td colspan="2"><font size="-1">
<font color="orange">[emoji:70]</font><a href="delivery_addr.php">配送先新規登録</a><br>
<font color="#666666">配送先の情報をｱﾄﾞﾚｽ帳に登録できます｡</font><br>

&nbsp;&nbsp;･<a href="delivery.php">配送先の追加・変更</a><br>
</font></td>
</tr>
<tr bgcolor="#ffffff">
<td colspan="2"><font size="-1">
<font color="red">[emoji:116]</font><a href="change_basic.php">ﾒｰﾙｱﾄﾞﾚｽとﾊﾟｽﾜｰﾄﾞの変更</a><br>
<font color="#666666">現在使用しているﾒｰﾙｱﾄﾞﾚｽとﾊﾟｽﾜｰﾄﾞの変更ができます｡</font>
</font></td>
</tr>
<tr bgcolor="#ffffff">
<td colspan="2"><font size="-1">
<font color="red">[emoji:138]</font><a href="refusal.php">登録削除</a><br>
<font color="#666666">ご登録内容､注文履歴を削除します｡※ｽﾃｰｼﾞ情報も削除します｡</font>
</font></td>
</tr>
<tr bgcolor="#ffffff">
<td colspan="2"><font size="-1">
<font color="maroon">[emoji:e9]</font><a href="<!--{$smarty.const.ROOT_URLPATH}-->frontparts/login_check.php?mode=logout">ﾛｸﾞｱｳﾄ</a><br>
<font color="#666666">｢ﾏｲﾍﾟｰｼﾞﾒﾆｭｰ｣の使用を終了します｡</font>
</font></td>
</tr>
</table>
<br>

<font size="-1">
｢旬穀旬菜｣と｢ﾛｰﾄ通販｣は同じお買物ｶｺﾞでお買物ができます｡<br>
</font>

<div align="center">
<a href="<!--{$smarty.const.ROOT_URLPATH}-->shunkoku-shunsai.php"><img src="<!--{$TPL_URLPATH}-->img/rohto/bnr_shun.gif" alt="旬穀旬菜" width="95%"></a><br>
<a href="<!--{$smarty.const.ROOT_URLPATH}-->"><img src="<!--{$TPL_URLPATH}-->img/rohto/bnr_rohto.gif" alt="ﾛｰﾄ通販" width="95%"></a><br>
</div>
<br>

<!--{*
■購入履歴一覧<br>
<!--{if $objNavi->all_row > 0}-->
    <!--{$objNavi->all_row}-->件の購入履歴があります。<br>
    <br>
    <!--{section name=cnt loop=$arrOrder}-->
    <hr>
        ▽購入日時<br>
        <!--{$arrOrder[cnt].create_date|sfDispDBDate}--><br>
        ▽注文番号<br>
        <!--{$arrOrder[cnt].order_id}--><br>
        <!--{assign var=payment_id value="`$arrOrder[cnt].payment_id`"}-->
        ▽お支払い方法<br>
        <!--{$arrPayment[$payment_id]|h}--><br>
        ▽合計金額<br>
        <font color="#ff0000"><!--{$arrOrder[cnt].payment_total|number_format}-->円</font><br>
        <div align="right"><a href="./history.php?order_id=<!--{$arrOrder[cnt].order_id}-->">→詳細を見る</a></div><br>
    <!--{/section}-->
    <hr>
<!--{else}-->
    購入履歴はありません。<br>
<!--{/if}-->

<!--{if $objNavi->strnavi != ""}-->
<!--{$objNavi->strnavi}-->
<br>
<!--{/if}-->
*}-->
