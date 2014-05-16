<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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
<!--{if $arrErr.rescd ne '' && $arrErr.rescd ne $smarty.const.MDL_SMBC_RES_OK && $arrErr.rescd ne $smarty.const.MDL_SMBC_RES_SECURE}-->
<font color="red">
エラーが発生しました。以下の内容をご確認ください。<br>
<!--{$arrErr.rescd|escape}-->:<!--{$arrErr.res|escape}--></font>
<br>
<!--{/if}-->

<form method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="send">
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
<input type="hidden" name="bill_no" value="<!--{$bill_no}-->" />

■カード番号<br>
<!--{assign var=key1 value="card_no1"}-->
<!--{assign var=key2 value="card_no2"}-->
<!--{assign var=key3 value="card_no3"}-->
<!--{assign var=key4 value="card_no4"}-->
<!--{if $arrErr[$key1] != "" || $arrErr[$key2] != "" || $arrErr[$key3] != "" || $arrErr[$key4] != ""}-->
<font color="#FF0000"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--><!--{$arrErr[$key3]}--><!--{$arrErr[$key4]}--></font>
<!--{/if}-->
<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|escape}-->" maxlength="4" size="4" istyle="4">
-
<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|escape}-->" maxlength="4" size="4" istyle="4">
-
<input type="text" name="<!--{$key3}-->" value="<!--{$arrForm[$key3]|escape}-->" maxlength="4" size="4" istyle="4">
-
<input type="text" name="<!--{$key4}-->" value="<!--{$arrForm[$key4]|escape}-->" maxlength="4" size="4" istyle="4">
<br>
<br>
■有効期限<br>
<!--{assign var=key_month value="card_month"}-->
<!--{if $arrErr[$key_month] != ""}-->
<font color="#FF0000"><!--{$arrErr[$key_month]}--></font>
<!--{/if}-->
<!--{assign var=key_year value="card_year"}-->
<!--{if $arrErr[$key_year] != ""}-->
<font color="#FF0000"><!--{$arrErr[$key_year]}--></font>
<!--{/if}-->
<select name="<!--{$key_month}-->">
<option value="">--</option>
<!--{html_options options=$arrMonth selected=$arrForm[$key_month]}-->
</select>
月/
<select name="<!--{$key_year}-->">
<option value="">--</option>
<!--{html_options options=$arrYear selected=$arrForm[$key_year]}-->
</select>
年
<br>
<br>
<!--{if $arrParam.card_info_keep == 1}-->
■登録カード情報<br>
登録したカードでお支払いする場合はチェックを付けてください。<br>
<input type="checkbox" name="use_regist_card" value="1" <!--{if $arrForm.use_regist_card.value eq 1}-->checked<!--{/if}--> />
<!--{$arrParam.regist_card_num}--><br>
<br>
<!--{/if}-->
<!--{if $arrParam.security_code_flg == 1}-->
■セキュリティコード<br>
<!--{assign var=key1 value="security_code"}-->
<font color="#FF0000"><!--{$arrErr[$key1]}--></font>
<input type="text" name="security_code" value="<!--{$arrForm[$key1]|escape}-->" maxlength="4" size="4" istyle="4">
<br>
<br>
<!--{else}-->
<input type="hidden" name="security_code" value="0" />
<!--{/if}-->
■お支払い区分<br>
お支払い回数についてはご契約されておりますクレジットカード会社によってご指定のお支払い回数がご利用できない場合がございます。<br>
<!--{assign var=key1 value="paymethod"}-->
<font color="#FF0000"><!--{$arrErr[$key1]}--></font>
<select name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|escape}-->" >
<!--{html_options options=$arrPayMethod selected=$arrForm[$key1]|escape}-->
</select>
<br>
<br>
<center><input type="submit" name="send" value="注文"></center>
</form>

<form method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="return">
<center><input type="submit" name="return" value="戻る"></center>
</form>
