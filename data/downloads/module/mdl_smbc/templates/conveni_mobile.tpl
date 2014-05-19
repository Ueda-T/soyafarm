<!--{*
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
 *}-->
<!--{if $arrErr.rescd ne '' && $arrErr.rescd ne $smarty.const.MDL_SMBC_RES_OK && $arrErr.rescd ne $smarty.const.MDL_SMBC_RES_SECURE}-->
<font color="#FF0000">
エラーが発生しました。以下の内容をご確認ください。<br>
<!--{$arrErr.rescd|escape}-->:<!--{$arrErr.res|escape}--></font>
<br>
<!--{/if}-->

<form method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="send">
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">

■コンビニの選択<br>
<!--{assign var=key value="conveni"}-->
<!--{if $arrErr[$key] != "" }-->
<font color="#FF0000"><!--{$arrErr[$key]}--></font>
<!--{/if}-->
<!--{html_radios name="$key" options=$arrCONVENI separator="<br>" selected=$arrForm[$key].value}-->
<br>
<a href="http://kb.smbc-fs.co.jp/oshiharai/payment-station/" target="_blank">コンビニエンスストアでのお支払方法について</a><br>

<center><input type="submit" name="send" value="注文"></center>
</form>
<form method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="return">
<center><input type="submit" name="return" value="戻る"></center>
</form>
