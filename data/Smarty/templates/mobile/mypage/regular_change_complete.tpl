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
<table width="100%" bordercolor="#dfedf5" border="0" bgcolor="#dfedf5" cellspacing="0" cellpadding="1">
<tr>
<th align="left"><h2><font size="-1">
定期購入変更手続き</font></h2></th>
</tr>
</table>
<font size="-1">
<br>
定期購入情報を変更しました。
<br><br>
<a href="./regular.php">&lt;&lt;一覧へ戻る</a>
<br><br>
</font>
