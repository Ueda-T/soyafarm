<h2 class="spNaked"><!--{$tpl_title}--></h2>
<table width="100%" cellspacing="0" cellpadding="1" bordercolor="#E3FF99" border="0" bgcolor="#E3FF99">
<tr>
<th align="left"><h2><font size="-1">販売会社名</font></h2></th>
</tr>
</table>
<font size="-1">
<!--{$arrOrder.law_company|h}--><br><br>
</font>


<table width="100%" cellspacing="0" cellpadding="1" bordercolor="#E3FF99" border="0" bgcolor="#E3FF99">
<tr>
<th align="left"><h2><font size="-1">運営責任者</font></h2></th>
</tr>
</table>
<font size="-1">
<!--{$arrOrder.law_manager|h}--><br><br>
</font>

<table width="100%" cellspacing="0" cellpadding="1" bordercolor="#E3FF99" border="0" bgcolor="#E3FF99">
<tr>
<th align="left"><h2><font size="-1">所在地</font></h2></th>
</tr>
</table>
<font size="-1">
<!--{*〒<!--{$arrOrder.law_zip01|h}-->-<!--{$arrOrder.law_zip02|h}--><br>*}-->
<!--{$arrPref[$arrOrder.law_pref]|h}--><!--{$arrOrder.law_addr01|h}--><!--{$arrOrder.law_addr02|h}--><br><br>
</font>

<table width="100%" cellspacing="0" cellpadding="1" bordercolor="#E3FF99" border="0" bgcolor="#E3FF99">
<tr>
<th align="left"><h2><font size="-1">販売事業者　電話番号</font></h2></th>
</tr>
</table>
<font size="-1">
<!--{$arrOrder.law_tel01|h}-->-<!--{$arrOrder.law_tel02|h}-->-<!--{$arrOrder.law_tel03|h}--><br><br>
</font>

<!--{if $arrOrder.law_fax01|h}-->
<table width="100%" cellspacing="0" cellpadding="1" bordercolor="#E3FF99" border="0" bgcolor="#E3FF99">
<tr>
<th align="left"><h2><font size="-1">販売事業者　FAX番号</font></h2></th>
</tr>
</table>
<font size="-1">
<!--{$arrOrder.law_fax01|h}-->-<!--{$arrOrder.law_fax02|h}-->-<!--{$arrOrder.law_fax03|h}--><br><br>
</font>
<!--{/if}-->

<!--{*
<table width="100%" cellspacing="0" cellpadding="1" bordercolor="#E3FF99" border="0" bgcolor="#E3FF99">
<tr>
<th align="left"><h2><font size="-1">メールアドレス</font></h2></th>
</tr>
</table>
<font size="-1">
<a href="mailto:<!--{$arrOrder.law_email|escape:'hex'}-->"><!--{$arrOrder.law_email|escape:'hexentity'}--></a><br><br>
</font>

<table width="100%" cellspacing="0" cellpadding="1" bordercolor="#E3FF99" border="0" bgcolor="#E3FF99">
<tr>
<th align="left"><h2><font size="-1">URL</font></h2></th>
</tr>
</table>
<font size="-1">
<a href="<!--{$arrOrder.law_url|h}-->"><!--{$arrOrder.law_url|h}--></a><br><br>
</font>
*}-->


<table width="100%" cellspacing="0" cellpadding="1" bordercolor="#E3FF99" border="0" bgcolor="#E3FF99">
<tr>
<th align="left"><h2><font size="-1">販売価格</font></h2></th>
</tr>
</table>
<font size="-1">
各商品ごとに掲載<br><br>
</font>

<table width="100%" cellspacing="0" cellpadding="1" bordercolor="#E3FF99" border="0" bgcolor="#E3FF99">
<tr>
<th align="left"><h2><font size="-1">代金の支払方法</font></h2></th>
</tr>
</table>
<font size="-1">
<!--{$arrOrder.law_term03}--><br><br>
</font>

<table width="100%" cellspacing="0" cellpadding="1" bordercolor="#E3FF99" border="0" bgcolor="#E3FF99">
<tr>
<th align="left"><h2><font size="-1">代金の支払時期</font></h2></th>
</tr>
</table>
<font size="-1">
<!--{$arrOrder.law_term04}--><br><br>
</font>

<table width="100%" cellspacing="0" cellpadding="1" bordercolor="#E3FF99" border="0" bgcolor="#E3FF99">
<tr>
<th align="left"><h2><font size="-1">送料</font></h2></th>
</tr>
</table>
<font size="-1">
<!--{$arrOrder.law_term01}--><br><br>
</font>

<table width="100%" cellspacing="0" cellpadding="1" bordercolor="#E3FF99" border="0" bgcolor="#E3FF99">
<tr>
<th align="left"><h2><font size="-1">商品の引渡時期</font></h2></th>
</tr>
</table>
<font size="-1">
<!--{$arrOrder.law_term05}--><br><br>
</font>

<table width="100%" cellspacing="0" cellpadding="1" bordercolor="#E3FF99" border="0" bgcolor="#E3FF99">
<tr>
<th align="left"><h2><font size="-1">返品・交換・返金</font></h2></th>
</tr>
</table>
<font size="-1">
<!--{$arrOrder.law_term06}--><br><br>
</font>
