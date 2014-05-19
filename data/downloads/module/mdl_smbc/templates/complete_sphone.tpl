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

<!--▼CONTENTS-->
<div id="under02column">
  <div id="under02column_shopping">

    <h2 class="title"><!--{$tpl_title|h}--></h2>

    <!-- ▼その他決済情報を表示する場合は表示 -->
    <!--{if $arrOther.title.value}-->
    <p><em>■<!--{$arrOther.title.name}-->情報</em><br />
        <!--{foreach key=key item=item from=$arrOther}-->
        <!--{if $key != "title"}-->
          <!--{if $item.name != ""}-->
            <!--{$item.name}-->：
          <!--{/if}-->
            <!--{$item.value|nl2br}--><br />
        <!--{/if}-->
        <!--{/foreach}-->
    </p><br />
     <!--{/if}-->
     <!-- ▲コンビに決済の場合には表示 -->

    <div id="completetext">
      <em><!--{$arrInfo.shop_name|h}-->の商品をご購入いただき、ありがとうございました。</em>
      <br /><br />
      <p>ただいま、ご注文の確認メールをお送りさせていただきました。<br />
        万一、ご確認メールが届かない場合は、トラブルの可能性もありますので大変お手数ではございますがもう一度お問い合わせいただくか、お電話にてお問い合わせくださいませ。<br /><br />
        今後ともご愛顧賜りますようよろしくお願い申し上げます。</p>

        <!--{if $smarty.session.credit_regist }-->
        <div style="margin-top:25px;">
            <p>「クレジットカード情報を登録する」ボタンをクリックすると、今回ご利用のクレジットカード情報の登録ができ、次回以降はクレジットカード情報を入力せずにご利用頂くことが出来ます。<br /><br />
            登録を希望される場合は「クレジットカード情報を登録する」ボタンをクリックして下さい。<br /><br />
            なお、既にクレジットカード情報を登録している場合は、今回ご利用の情報に更新されます。</p>
        </div>

        <form name="form1" id="form1" method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="mode" value="send" />

            <div class="tblareabtn">
              <p><input type="submit" value="クレジットカード情報を登録する" class="btn ui-link" width="130" height="30" alt="クレジットカード情報を登録する" name="send" /></p>
            </div>
        </form>
        <!--{/if}-->

      <p><!--{$arrInfo.shop_name|h}--><br />
        TEL：<!--{$arrInfo.tel01}-->-<!--{$arrInfo.tel02}-->-<!--{$arrInfo.tel03}--> <!--{if $arrInfo.business_hour != ""}-->（受付時間/<!--{$arrInfo.business_hour}-->）<!--{/if}--><br />
        E-mail：<a href="mailto:<!--{$arrInfo.email02|escape:'hex'}-->"><!--{$arrInfo.email02|escape:'hexentity'}--></a></p>
    </div>

    <div class="tblareabtn"><a class="btn_back ui-link" href="<!--{$smarty.const.TOP_URLPATH}-->">トップページへ</a>
    </div>
  </div>
</div>
<!--▲CONTENTS-->
