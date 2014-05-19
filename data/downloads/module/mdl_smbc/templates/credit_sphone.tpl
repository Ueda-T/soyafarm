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
            <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />
            <input type="hidden" name="bill_no" value="<!--{$bill_no}-->" />

            <table summary="決済情報の入力" class="entryform">
              <tr>
                <th>カード番号<span class="attention">※</span></th>
                <td>
                <!--{assign var=key1 value="card_no1"}-->
                <!--{assign var=key2 value="card_no2"}-->
                <!--{assign var=key3 value="card_no3"}-->
                <!--{assign var=key4 value="card_no4"}-->
                <!--{if $arrErr[$key1] != "" || $arrErr[$key2] != "" || $arrErr[$key3] != "" || $arrErr[$key4] != ""}-->
                <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--><!--{$arrErr[$key3]}--><!--{$arrErr[$key4]}--></div>
                <!--{/if}-->
                    <input type="text"
                         name="<!--{$key1}-->"
                         value="<!--{$arrForm[$key1]|h}-->"
                         maxlength="4"
                         size="4"
                         style="ime-mode: disabled; <!--{$arrErr[$key1]|sfGetErrorColor}-->"/>
                    &nbsp;-&nbsp;
                    <input type="text"
                         name="<!--{$key2}-->"
                         value="<!--{$arrForm[$key2]|h}-->"
                         maxlength="4"
                         size="4"
                         style="ime-mode: disabled; <!--{$arrErr[$key2]|sfGetErrorColor}-->"/>
                    &nbsp;-&nbsp;
                    <input type="text"
                         name="<!--{$key3}-->"
                         value="<!--{$arrForm[$key3]|h}-->"
                         maxlength="4"
                         size="4"
                         style="ime-mode: disabled; <!--{$arrErr[$key3]|sfGetErrorColor}-->"/>
                    &nbsp;-&nbsp;
                    <input type="text"
                         name="<!--{$key4}-->"
                         value="<!--{$arrForm[$key4]|h}-->"
                         maxlength="4"
                         size="4"
                         style="ime-mode: disabled; <!--{$arrErr[$key4]|sfGetErrorColor}-->"/>
                <p class="mini">※半角数字で入力してください。<br />例）9999-9999-9999-9999</p>
                </td>
              </tr>
              <tr>
                <th>カード有効期限<span class="attention">※</span></th>
                <td>
                <!--{assign var=key1 value="card_month"}-->
                <!--{if $arrErr[$key1] != ""}-->
                <div class="attention"><!--{$arrErr[$key1]}--></div>
                <!--{/if}-->
                <!--{assign var=key2 value="card_year"}-->
                <!--{if $arrErr[$key2] != ""}-->
                <div class="attention"><!--{$arrErr[$key2]}--></div>
                <!--{/if}-->
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
                <td colspan="2">登録したカードでお支払い&nbsp;<em>登録したカードでお支払いする場合はチェックを付けてください。</em></td>
              </tr>
              <tr>
                <th>登録カード情報</th>
                <td>
                  <label><input type="checkbox" name="use_regist_card" value="1" <!--{if $arrForm.use_regist_card.value eq 1}-->checked<!--{/if}--> /><img src="<!--{$smarty.const.ROOT_URLPATH}-->smbc/<!--{$arrParam.regist_card_brand}-->" height="32" style="margin:0 8px;"><!--{$arrParam.regist_card_num}--></label><br />
                </td>
              </tr>
              <!--{/if}-->
              <!--{if $arrParam.security_code_flg == 1}-->
              <tr>
                <th>セキュリティコード<span class="attention">※</span></th>
                <td>
                    <!--{assign var=key1 value="security_code"}-->
                    <div class="attention"><!--{$arrErr[$key1]}--></div>
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
                <th>お支払い区分<span class="attention">※</span></th>
                <td>
                    <!--{assign var=key1 value="paymethod"}-->
                    <div class="attention"><!--{$arrErr[$key1]}--></div>
                    <select name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" >
                    <!--{html_options options=$arrPayMethod selected=$arrForm[$key1]|escape}-->
                    </select>
                    <p class="mini">※お支払い回数についてはご契約されておりますクレジットカード会社によってご指定のお支払い回数がご利用できない場合がございます。</p>
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
