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
<style type="text/css">
div#completetext {
    width: 670px;
    margin: 15px auto 0 auto;
    padding: 15px;
    border: 5px solid #ccc;
}
</style>

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
//]]>
</script>
<div id="undercolumn">
    <div id="undercolumn_shopping">
        <p class="flow_area">
            <img src="<!--{$TPL_URLPATH}-->img/picture/img_flow_04.jpg" alt="購入手続きの流れ" />
        </p>
        <h2 class="title"><!--{$tpl_title|h}--></h2>

        <div id="complete_area">
            <p class="message"><!--{$arrInfo.shop_name|h}-->の商品をご購入いただき、ありがとうございました。</p>

            <!-- ▼その他決済情報を表示する場合は表示 -->
            <!--{if $arrOther.title.value}-->
                <p><span class="attention">■<!--{$arrOther.title.name}-->情報</span><br />
                    <!--{foreach key=key item=item from=$arrOther}-->
                        <!--{if $key != "title"}-->
                            <!--{if $item.name != ""}-->
                                <!--{$item.name}-->：
                            <!--{/if}-->
                                <!--{$item.value|nl2br}--><br />
                        <!--{/if}-->
                    <!--{/foreach}-->
                </p>
            <!--{/if}-->
            <!-- ▲コンビニ決済の場合には表示 -->

            <p>ただいま、ご注文の確認メールをお送りさせていただきました。<br />
                万一、ご確認メールが届かない場合は、トラブルの可能性もありますので大変お手数ではございますがもう一度お問い合わせいただくか、お電話にてお問い合わせくださいませ。<br />
                今後ともご愛顧賜りますようよろしくお願い申し上げます。</p>

            <!--{if $smarty.session.credit_regist }-->
            <div id="completetext">
                <div style="margin-top:25px;">
                    <p>「クレジットカード情報を登録する」ボタンをクリックすると、今回ご利用のクレジットカード情報の登録ができ、次回以降はクレジットカード情報を入力せずにご利用頂くことが出来ます。<br />
                    登録を希望される場合は「クレジットカード情報を登録する」ボタンをクリックして下さい。<br />
                    なお、既にクレジットカード情報を登録している場合は、今回ご利用の情報に更新されます。</p>
                </div>
                <div class="btn_area">
                    <form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
                        <input type="hidden" name="mode" value="send" />
                        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
                        <ul>
                            <li>
                                <a href="#" onclick="fnCheckSubmit(); fnModeSubmit('send', '', ''); return false;" onmouseover="chgImg('<!--{$smarty.const.ROOT_URLPATH}-->smbc/b_card_on.jpg','keep');" onmouseout="chgImg('<!--{$smarty.const.ROOT_URLPATH}-->smbc/b_card.jpg','keep');"><img src="<!--{$smarty.const.ROOT_URLPATH}-->smbc/b_card.jpg" alt="クレジットカード情報を登録する" border="0" name="keep" /></a>
                            </li>
                        </ul>
                    </form>
                </div>
            </div>
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
