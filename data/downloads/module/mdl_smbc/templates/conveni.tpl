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
<!--▼CONTENTS-->
<div id="undercolumn">
    <div id="undercolumn_shopping">
        <p class="flow_area"><img src="<!--{$TPL_URLPATH}-->img/picture/img_flow_03.jpg" alt="購入手続きの流れ" /></p>
        <h2 class="title">SMBC決済 コンビニ（番号方式）</h2>

        <!--{if $arrErr.rescd ne '' && $arrErr.rescd ne $smarty.const.MDL_SMBC_RES_OK && $arrErr.rescd ne $smarty.const.MDL_SMBC_RES_SECURE}-->
            <p class="attention">エラーが発生しました。以下の内容をご確認ください。</p>
            <p class="attention"><!--{$arrErr.rescd|escape}-->:<!--{$arrErr.res|escape}--></p>
        <!--{/if}-->

        <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="send" />

        <table summary="決済情報の入力">
            <colgroup width="25%"></colgroup>
            <colgroup width="75%"></colgroup>
            <tr>
                <th scope="row">コンビニの選択<span class="attention">※</span></th>
                <td>
                    <!--{assign var=key value="conveni"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <!--{html_radios name="$key" options=$arrCONVENI separator="<br />" selected=$arrForm[$key].value}-->
                    <p class="mini">※ お支払いいただくコンビニを選択してください。</p>
                    <a href="http://kb.smbc-fs.co.jp/oshiharai/payment-station/" target="_blank">コンビニエンスストアでのお支払方法について</a>
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
