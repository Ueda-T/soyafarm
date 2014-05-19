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
<style type="text/css">.
@charset "utf-8";
/*-----------------------------------------------
ボタン（進む系ボタン:グリーン）
----------------------------------------------- */
a.btn,a.btn:link,a.btn:visited,a.btn:hover{
color:#FFF;
font-size: 16px;
font-weight:bold;
text-decoration: none;
text-align:center;
padding: 10px;
text-shadow: 0 -1px 1px rgba(0,0,0,1);
border: 1px solid #A9ABAD;
border-radius: 5px;
-moz-border-radius: 5px;
-webkit-border-radius: 5px;
display:block;
background:#B4DF34;
background: -moz-linear-gradient(center top, #B4DF34 0%,#669222 100%);
background: -webkit-gradient(linear, left top, left bottom, color-stop(0, #B4DF34),color-stop(1, #669222));
cursor:pointer;
-webkit-transition:opacity 0.5s ease-in;
-moz-transition:opacity 0.5s ease-in;
}
input[type="submit"].btn{
width:100%;
color:#FFF;
font-size: 16px;
font-weight:bold;
text-decoration: none;
text-align:center;
padding: 10px;
text-shadow: 0 -1px 1px rgba(0,0,0,1);
border: 1px solid #A9ABAD;
border-radius: 5px;
-moz-border-radius: 5px;
-webkit-border-radius: 5px;
display:block;
background:#B4DF34;
background: -moz-linear-gradient(center top, #B4DF34 0%,#669222 100%);
background: -webkit-gradient(linear, left top, left bottom, color-stop(0, #B4DF34),color-stop(1, #669222));
cursor:pointer;
-webkit-transition:opacity 0.5s ease-in;
-moz-transition:opacity 0.5s ease-in;
}
/*-----------------------------------------------
ボタン（戻る系ボタン:グレー）
----------------------------------------------- */
a.btn_back,a.btn_back:link,a.btn_back:visited,a.btn_back:hover {
color:#FFF;
font-size: 16px;
font-weight:bold;
text-decoration: none;
text-align:center;
text-shadow: 0 -1px 1px rgba(0,0,0,1);
border: 1px solid #A9ABAD;
border-radius: 5px;
-moz-border-radius: 5px;
-webkit-border-radius: 5px;
display:block;
padding:5px 0;
background:#999999;
background: -moz-linear-gradient(center top, #C5C5C5 0%,#999999 100%);
background: -webkit-gradient(linear, left top, left bottom, color-stop(0, #C5C5C5),color-stop(1, #999999));
}
input[type="submit"].btn_back{
color:#FFF;
font-size: 16px;
font-weight:bold;
text-decoration: none;
text-align:center;
text-shadow: 0 -1px 1px rgba(0,0,0,1);
border: 1px solid #A9ABAD;
border-radius: 5px;
-moz-border-radius: 5px;
-webkit-border-radius: 5px;
display:block;
padding:5px 0;
background:#999999;
background: -moz-linear-gradient(center top, #C5C5C5 0%,#999999 100%);
background: -webkit-gradient(linear, left top, left bottom, color-stop(0, #C5C5C5),color-stop(1, #999999));
}
</style>
<script type="text/javascript">//<![CDATA[
    $(function() {
        /**
         * 通信エラー表示.
         */
        function remoteException(XMLHttpRequest, textStatus, errorThrown) {
            alert('通信中にエラーが発生しました。確認画面に移動します。');
            location.href = '<!--{$smarty.const.SHOPPING_CONFIRM_URLPATH}-->';
        }
    });
//]]>
</script>
<div id="under02column">
    <div id="under02column_shopping">
        <h2 class="title"><!--{$tpl_title|h}--></h2><br />

        <!--{if $arrErr.rescd ne '' && $arrErr.rescd ne $smarty.const.MDL_SMBC_RES_OK && $arrErr.rescd ne $smarty.const.MDL_SMBC_RES_SECURE}-->
          <p class="attention">エラーが発生しました。以下の内容をご確認ください。<br><!--{$arrErr.rescd|escape}-->:<!--{$arrErr.res|escape}--></p>
        <!--{/if}-->

        <form name="form1" id="form1" method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="mode" value="send" />

            <table summary="決済情報の入力" class="entryform">
              <tr>
                <th>コンビニの選択<span class="attention">※</span></th>
                <td align="left">
                <!--{assign var=key value="conveni"}-->
                <div class="attention"><!--{$arrErr[$key]}--></div>
                <!--{html_radios name="$key" options=$arrCONVENI separator="<br />" selected=$arrForm[$key].value}-->
                <p class="mini">※ お支払いいただくコンビニを選択してください。</p>
                <a href="http://kb.smbc-fs.co.jp/oshiharai/payment-station/" target="_blank">コンビニエンスストアでのお支払方法について</a>
                </td>
              </tr>
            </table>

            <div class="tblareabtn">
              <p><input type="submit" value="次へ" class="btn ui-link" width="130" height="30" alt="次へ" name="send" /></p>
              <br />
              <p><a href="<!--{$smarty.const.SHOPPING_CONFIRM_URLPATH}-->" class="btn_back ui-link">戻る</a></p>
            </div>
        </form>
    </div>
</div>
<!--▲CONTENTS-->
