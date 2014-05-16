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
ご注文、有り難うございました。<br>
商品到着をお楽しみにお待ちくださいませ。<br><br>
どうぞ、今後とも、<!--{$arrInfo.shop_name|h}-->をよろしくお願いします。

<!--{if $arrOther.title.value }-->
<!-- ▼その他の決済情報 -->
<br>
<br>
■<!--{$arrOther.title.name}-->情報<br>
<!--{foreach key=key item=item from=$arrOther}-->
<!--{if $key != "title"}-->
<!--{if $item.name != ""}--><!--{$item.name}-->：<!--{/if}--><!--{$item.value|nl2br}--><br>
<!--{/if}-->
<!--{/foreach}-->
<!-- ▲その他の決済情報 -->
<!--{/if}-->
<!--{if $smarty.session.credit_regist }-->
<br>
<br>
<br>
「クレジットカード情報を登録する」ボタンをクリックすると、今回ご利用のクレジットカード情報の登録ができ、次回以降はクレジットカード情報を入力せずにご利用頂くことが出来ます。<br><br>
登録を希望される場合は「クレジットカード情報を登録する」ボタンをクリックして下さい。<br><br>
なお、既にクレジットカード情報を登録している場合は、今回ご利用の情報に更新されます。<br><br>
<br>
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="send" />
    <p align="center"><input type="submit" name="keep" id="keep" value="クレジットカード情報を登録する"></p>
</form>
<!--{/if}-->
