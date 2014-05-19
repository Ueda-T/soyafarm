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
<!--▼CONTENTS-->
<div id="undercolumn">
    <div id="undercolumn_shopping">
        <p class="flow_area">
            <img src="<!--{$TPL_URLPATH}-->img/picture/img_flow_04.jpg" alt="購入手続きの流れ" />
        </p>
        <h2 class="title"><!--{$tpl_title|h}--></h2>

        <div id="complete_area">
            <!--{if $arrErr.rescd ne '' && $arrErr.rescd ne $smarty.const.MDL_SMBC_RES_OK && $arrErr.rescd ne $smarty.const.MDL_SMBC_RES_SECURE}-->
            <p class="message">エラーが発生しました。以下の内容をご確認ください。</p>
            <p class="attention"><!--{$arrErr.rescd|escape}-->:<!--{$arrErr.res|escape}--></p>
            <!--{else}-->
            <p class="message">クレジットカード情報を登録しました。</p>
            <p>次回以降はクレジットカード情報を入力せずにご利用頂くことが出来ます。<br />今後ともご愛顧賜りますようよろしくお願い申し上げます。</p>
            <!--{/if}-->
            <div class="shop_information">
                <p class="name"><!--{$arrInfo.shop_name|h}--></p>
                <p>TEL：<!--{$arrInfo.tel01}-->-<!--{$arrInfo.tel02}-->-<!--{$arrInfo.tel03}--> <!--{if $arrInfo.business_hour != ""}-->（受付時間/<!--{$arrInfo.business_hour}-->）<!--{/if}--><br />
                E-mail：<a href="mailto:<!--{$arrInfo.email02|escape:'hex'}-->"><!--{$arrInfo.email02|escape:'hexentity'}--></a>
                </p>
            </div>
        </div>

        <div class="btn_area">
            <ul>
                <li>
                    <a href="<!--{$smarty.const.TOP_URLPATH}-->" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_toppage_on.jpg','b_toppage');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_toppage.jpg','b_toppage');">
                        <img src="<!--{$TPL_URLPATH}-->img/button/btn_toppage.jpg" alt="トップページへ" border="0" name="b_toppage" /></a>
                </li>
            </ul>
        </div>

    </div>
</div>
<!--▲CONTENTS-->
