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
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/facebox.js"></script>
<link rel="stylesheet" type="text/css" href="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/facebox.css" media="screen" />
<script type="text/javascript">//<![CDATA[
var send = true;

function fnCheckSubmit() {
    if(send) {
        send = false;
        return true;
    } else {
        alert("只今、処理中です。しばらくお待ち下さい。");
        return false;
    }
}

$(document).ready(function() {
    $('a.expansion').facebox({
        loadingImage : '<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/loading.gif',
        closeImage   : '<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/closelabel.png'
    });
});
//]]></script>

<!--CONTENTS-->
<div id="undercolumn">
    <div id="undercolumn_shopping">
        <p class="flow_area"><img src="<!--{$TPL_URLPATH}-->img/picture/img_flow_03.jpg" alt="購入手続きの流れ" /></p>
        <h2 class="title">SMBC決済 クレジット</h2>

        <!--{if $arrErr.rescd ne '' && $arrErr.rescd ne $smarty.const.MDL_SMBC_RES_OK && $arrErr.rescd ne $smarty.const.MDL_SMBC_RES_SECURE}-->
            <p class="attention">エラーが発生しました。以下の内容をご確認ください。</p>
            <p class="attention"><!--{$arrErr.rescd|escape}-->:<!--{$arrErr.res|escape}--></p>
        <!--{/if}-->

        <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="send" />
        <input type="hidden" name="bill_no" value="<!--{$bill_no}-->" />

        <table summary="決済情報の入力">
            <colgroup width="25%"></colgroup>
            <colgroup width="75%"></colgroup>
            <tr>
                <th scope="row">カード番号<span class="attention">※</span></th>
                <td>
                    <!--{assign var=key1 value="card_no1"}-->
                    <!--{assign var=key2 value="card_no2"}-->
                    <!--{assign var=key3 value="card_no3"}-->
                    <!--{assign var=key4 value="card_no4"}-->
                    <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--><!--{$arrErr[$key3]}--><!--{$arrErr[$key4]}--></span>
                    <input type="text"
                         name="<!--{$key1}-->"
                         value="<!--{$arrForm[$key1]|h}-->"
                         maxlength="4"
                         class="box60"
                         style="ime-mode: disabled; <!--{$arrErr[$key1]|sfGetErrorColor}-->"/>
                    &nbsp;-&nbsp;
                    <input type="text"
                         name="<!--{$key2}-->"
                         value="<!--{$arrForm[$key2]|h}-->"
                         maxlength="4"
                         class="box60"
                         style="ime-mode: disabled; <!--{$arrErr[$key2]|sfGetErrorColor}-->"/>
                    &nbsp;-&nbsp;
                    <input type="text"
                         name="<!--{$key3}-->"
                         value="<!--{$arrForm[$key3]|h}-->"
                         maxlength="4"
                         class="box60"
                         style="ime-mode: disabled; <!--{$arrErr[$key3]|sfGetErrorColor}-->"/>
                    &nbsp;-&nbsp;
                    <input type="text"
                         name="<!--{$key4}-->"
                         value="<!--{$arrForm[$key4]|h}-->"
                         maxlength="4"
                         class="box60"
                         style="ime-mode: disabled; <!--{$arrErr[$key4]|sfGetErrorColor}-->"/>
                    <p class="mini">※半角数字で入力してください。<br/>例）9999-9999-9999-9999</p>
                </td>
            </tr>
            <tr>
                <th scope="row">カード有効期限<span class="attention">※</span></th>
                <td>
                    <!--{assign var=key1 value="card_month"}-->
                    <!--{assign var=key2 value="card_year"}-->
                    <span class="attention"><!--{$arrErr[$key1]}--></span>
                    <span class="attention"><!--{$arrErr[$key2]}--></span>
                    <select name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" >
                    <option value="">--</option>
                    <!--{html_options options=$arrMonth selected=$arrForm[$key1]|h}-->
                    </select>　月　/
                    <select name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" >
                    <option value="">--</option>
                    <!--{html_options options=$arrYear selected=$arrForm[$key2]|h}-->
                    </select>　年<br/>
                    <p class="mini">例）[カード表記]07/10 → [実際の入力] 07月/10年</p>
                </td>
            </tr>
            <!--{if $arrParam.card_info_keep == 1}-->
            <tr>
                <td colspan="2">登録したカードでお支払い&nbsp;<span class="attention">※登録したカードでお支払いする場合はチェックを付けてください。</span></td>
            </tr>
            <tr>
                <th scope="row">登録カード情報</th>
                <td>
                    <label><input type="checkbox" name="use_regist_card" value="1" <!--{if $arrForm.use_regist_card.value eq 1}-->checked<!--{/if}--> /><img src="<!--{$smarty.const.ROOT_URLPATH}-->smbc/<!--{$arrParam.regist_card_brand}-->" height="32" style="margin:0 8px;"><!--{$arrParam.regist_card_num}--></label><br />
                </td>
            </tr>
            <!--{/if}-->
            <!--{if $arrParam.security_code_flg == 1}-->
            <tr>
                <th scope="row">セキュリティコード<span class="attention">※</span></th>
                <td>
                    <!--{assign var=key1 value="security_code"}-->
                    <span class="attention"><!--{$arrErr[$key1]}--></span>
                    <input type="text"
                           name="security_code"
                           value="<!--{$arrForm[$key1]|h}-->"
                           maxlength="4"
                           class="box60"
                           style="ime-mode: disabled; <!--{$arrErr[$key1]|sfGetErrorColor}-->"/><br />
                    <p class="mini">※半角数字で入力してください。<br/>例）999</p>
                </td>
            </tr>
            <!--{else}-->
            <input type="hidden" name="security_code" value="0" />
            <!--{/if}-->
            <tr>
                <th scope="row">お支払い区分<span class="attention">※</span></th>
                <td>
                    <!--{assign var=key1 value="paymethod"}-->
                    <span class="attention"><!--{$arrErr[$key1]}--></span>
                    <select name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" >
                    <!--{html_options options=$arrPayMethod selected=$arrForm[$key1]|escape}-->
                    </select>
                    <p class="mini">※お支払い回数についてはご契約されておりますクレジットカード会社によってご指定のお支払い回数がご利用できない場合がございます。</p>
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td>以上の内容で間違いなければ、下記「次へ」ボタンをクリックしてください。<br />
                    <em>※画面が切り替るまで少々時間がかかる場合がございますが、そのままお待ちください。</em>
                </td>
            </tr>
        </table>

        <div class="btn_area">
            <ul>
                <li>
                    <a href="#" onclick="fnModeSubmit('return','',''); return false;" onmouseover="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_back_on.jpg',back03)" onmouseout="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_back.jpg',back03)"><img src="<!--{$TPL_URLPATH}-->img/button/btn_back.jpg" alt="戻る" border="0" name="back03" id="back03" /></a>
                </li>
                <li>
                    <a href="#" onclick="fnCheckSubmit(); fnModeSubmit('send', '', ''); return false;" onmouseover="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_next_on.jpg',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/button/btn_next.jpg',this)"><img src="<!--{$TPL_URLPATH}-->img/button/btn_next.jpg" alt="次へ" border="0" name="next" id="next" /></a>
                </li>
            </ul>
        </div>
        </form>
    </div>
</div>
<!--▲CONTENTS-->
