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
定期購入一覧</font></h2></th>
</tr>
</table>
<font size="-1">
<!--{if $objNavi->strnavi != ""}-->
<!--{$objNavi->strnavi}-->
<hr>
<!--{/if}-->

<!--{foreach from=$arrRegularDetail item=regularDetail }-->
【商品名】<br>
<!--{$regularDetail.product_name|h}--><br>
【数量】<br>
<!--{$regularDetail.quantity|h}-->個<br>
【お届け間隔】<br>
<!--{if $regularDetail.course_cd >= $smarty.const.COURSE_CD_DAY_MIN}-->
<!--{$regularDetail.course_cd|h}-->日ごと<br>
<!--{else}-->
<!--{$regularDetail.course_cd|h}-->ヶ月ごと<br>
<!--{/if}-->
【次回お届け日】<br>
<!--{$regularDetail.next_arrival_date|date_format:"%Y年%m月%d日"|h}--><br>
【次々回次回お届け日】<br>
<!--{$regularDetail.after_next_arrival_date|date_format:"%Y年%m月%d日"|h}--><br>

<!--{** 次回お届け日が未定は変更不可 **}-->
<!--{if $regularDetail.next_arrival_date == ""}-->
次回お届け日が未定のため<br />変更できません。

<!--{** 次回お届け日の1週間以内は変更不可 **}-->
<!--{elseif !$regularDetail.disp_flg}-->
只今、出荷準備中のため<br />変更できません。

<!--{** 「6：休止中」は変更不可 **}-->
<!--{elseif $regularDetail.status == $smarty.const.REGULAR_ORDER_STATUS_PAUSE}-->
休止中のため<br />変更できません。

<!--{else}-->
<a href="./regular_change.php?regular_id=<!--{$regularDetail.regular_id|h}-->&line_no=<!--{$regularDetail.line_no|h}-->">お届けスケジュールの変更 &gt;&gt;</a>

<!--{/if}-->
<hr>
<!--{/foreach}-->

<!--{if $objNavi->strnavi != ""}-->
<br>
<!--{$objNavi->strnavi}-->
<!--{/if}-->
<br><br>
</font>


