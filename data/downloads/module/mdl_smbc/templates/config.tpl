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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA    02111-1307, USA.
 */
*}-->
<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->
<script type="text/javascript">
<!--
jQuery(function(){
    toggleBankBox();
});
function toggleCreditBox() {
    var isDisabled = document.form1.credit.checked;
    if (document.form1.credit.checked) {
        document.form1.elements['pay_once'].disabled = false;
        document.form1.elements['pay_twice'].disabled = false;
        document.form1.elements['pay_monthly03'].disabled = false;
        document.form1.elements['pay_monthly05'].disabled = false;
        document.form1.elements['pay_monthly06'].disabled = false;
        document.form1.elements['pay_monthly10'].disabled = false;
        document.form1.elements['pay_monthly12'].disabled = false;
        document.form1.elements['pay_monthly15'].disabled = false;
        document.form1.elements['pay_monthly18'].disabled = false;
        document.form1.elements['pay_monthly20'].disabled = false;
        document.form1.elements['pay_monthly24'].disabled = false;
        document.form1.elements['pay_revolving'].disabled = false;
        document.form1.elements['pay_bonus'].disabled = false;
        document.form1.elements['security_code_flg'][0].disabled = false;
        document.form1.elements['security_code_flg'][1].disabled = false;
        document.form1.elements['card_info_keep'][0].disabled = false;
        document.form1.elements['card_info_keep'][1].disabled = false;
        if (document.form1.elements['card_info_keep'][0].checked) {
            document.form1.elements['card_info_pwd'].disabled = false;
        }
    } else {
        document.form1.elements['pay_once'].disabled = true;
        document.form1.elements['pay_twice'].disabled = true;
        document.form1.elements['pay_monthly03'].disabled = true;
        document.form1.elements['pay_monthly05'].disabled = true;
        document.form1.elements['pay_monthly06'].disabled = true;
        document.form1.elements['pay_monthly10'].disabled = true;
        document.form1.elements['pay_monthly12'].disabled = true;
        document.form1.elements['pay_monthly15'].disabled = true;
        document.form1.elements['pay_monthly18'].disabled = true;
        document.form1.elements['pay_monthly20'].disabled = true;
        document.form1.elements['pay_monthly24'].disabled = true;
        document.form1.elements['pay_revolving'].disabled = true;
        document.form1.elements['pay_bonus'].disabled = true;
        document.form1.elements['security_code_flg'][0].disabled = true;
        document.form1.elements['security_code_flg'][1].disabled = true;
        document.form1.elements['card_info_keep'][0].disabled = true;
        document.form1.elements['card_info_keep'][1].disabled = true;
        if (document.form1.elements['card_info_keep'][0].checked) {
            document.form1.elements['card_info_pwd'].disabled = true;
        }
    }
}
function toggleBankBox() {
    var isDisabled = document.form1.bank_transfer.checked;
    if (document.form1.bank_transfer.checked) {
        document.form1.elements['over_deposit'].disabled = false;
        document.form1.elements['short_deposit'].disabled = false;
        document.form1.elements['request_deposit'].disabled = false;
    } else {
        document.form1.elements['over_deposit'].disabled = true;
        document.form1.elements['short_deposit'].disabled = true;
        document.form1.elements['request_deposit'].disabled = true;
    }
}
function disableKeepPwd() {
    document.form1.elements['card_info_pwd'].disabled = false;
}
function enableKeepPwd() {
    document.form1.elements['card_info_pwd'].disabled = true;
}
function toggleConveniBox() {
    var isDisabled = document.form1.conveni_number.checked;
    if (document.form1.conveni_number.checked) {
        document.form1.elements['seven_eleven'].disabled = false;
        document.form1.elements['lawson'].disabled = false;
        document.form1.elements['seicomart'].disabled = false;
        document.form1.elements['familymart'].disabled = false;
        document.form1.elements['circlek_sunkus'].disabled = false;
    } else {
        document.form1.elements['seven_eleven'].disabled = true;
        document.form1.elements['lawson'].disabled = true;
        document.form1.elements['seicomart'].disabled = true;
        document.form1.elements['familymart'].disabled = true;
        document.form1.elements['circlek_sunkus'].disabled = true;
    }
}
function toggleCreditRegularBox() {
    var isDisabled = document.form1.credit_regular.checked;
    if (document.form1.credit_regular.checked) {
        document.form1.elements['regular_shop_cd'].disabled = false;
        document.form1.elements['regular_syuno_co_cd'].disabled = false;
        document.form1.elements['regular_shop_pwd'].disabled = false;
        document.form1.elements['regular_deal_pwd'].disabled = false;
    } else {
        document.form1.elements['regular_shop_cd'].disabled = true;
        document.form1.elements['regular_syuno_co_cd'].disabled = true;
        document.form1.elements['regular_shop_pwd'].disabled = true;
        document.form1.elements['regular_deal_pwd'].disabled = true;
    }
}
function togglePaymentSlip() {
    if (document.form1.payment_slip.checked) {
        document.form1.elements['payment_slip_issue'][0].disabled = false;
        document.form1.elements['payment_slip_issue'][1].disabled = false;
        if (document.form1.elements['payment_slip_issue'][1].checked) {
            document.form1.elements['payment_slip_destination'][0].disabled = false;
            document.form1.elements['payment_slip_destination'][1].disabled = false;
        }
    } else {
        document.form1.elements['payment_slip_issue'][0].disabled = true;
        document.form1.elements['payment_slip_issue'][1].disabled = true;
        if (document.form1.elements['payment_slip_issue'][1].checked) {
            document.form1.elements['payment_slip_destination'][0].disabled = true;
            document.form1.elements['payment_slip_destination'][1].disabled = true;
        }
    }
}
function disableDestination() {
    for (var i = 0; i < document.form1.elements['payment_slip_destination'].length; i++) {
        document.form1.elements['payment_slip_destination'][i].disabled = true;
    }
}
function enableDestination() {
    for (var i = 0; i < document.form1.elements['payment_slip_destination'].length; i++) {
        document.form1.elements['payment_slip_destination'][i].disabled = false;
    }
}
//-->
</script>
<!--▼検索フォーム-->
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|h}-->">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input name="mode" type="hidden" value="register">
<input name="search_pageno" type="hidden" value="">
<input name="customer_id" type="hidden" value="">
<h2>SMBCファイナンスサービス決済モジュール</h2>

SMBCファイナンスサービス決済モジュールをご利用頂く為には、ショップ様ご自身でSMBCファイナンスサービス株式会社とご契約頂く必要があります。<br/>
お申し込みにつきましては、下記のページよりお問い合わせ下さい。<br/>
<br/>
<a href="https://ssl.kb.smbc-fs.co.jp/contact_s/" target="_blank"> ＞＞ SMBCファイナンスサービスへのお問合せ</a><br/>
<br/>
《ご注意事項》<br/>
ご契約内容をご確認頂き、設定をお願い致します。<br/>
尚、ご契約内容や設定変更をご希望される場合についてはSMBCファイナンスサービス（株）の営業担当者宛ご連絡していただきますようお願い致します。<br/>
<br/>
<!--{if $arrErr.top}-->
    <!--{if $arrErr.top}--><span class="attention"><!--{$arrErr.top}--></span><!--{/if}-->
<!--{/if}-->
<table class="form">
    <colgroup width="28%">
    <colgroup width="72%">
    <tr class="n">
        <th class="colmun" colspan="2">▼管理設定</th>
    </tr>
    <tr class="n">
        <th class="colmun">接続先<span class="attention"> *</span></th>
        <td>
        <!--{assign var=key value="connect_url"}-->
        <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
        <label><input type="radio" name="connect_url" value="real" <!--{if $arrForm.connect_url.value eq "real"}-->checked<!--{/if}--> />本番用（実際に運用する場合に選択）</label><br />
        <label><input type="radio" name="connect_url" value="test" <!--{if $arrForm.connect_url.value eq "test"}-->checked<!--{/if}--> />試験用（動作を確認する場合に選択）</label><br />
        </td>
    </tr>
    <tr class="n">
        <th class="colmun" colspan="2">▼都度決済設定</th>
    </tr>
    <tr>
        <th class="colmun">契約コード<span class="attention"> *</span></th>
        <td>
            <!--{assign var=key value="shop_cd"}-->
            <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
            <input type="text" name="shop_cd" class="box30" size="30" value="<!--{$arrForm.shop_cd.value|h}-->" maxlength="<!--{$arrForm[$key].length|h}-->" <!--{if $arrErr[$key]}--><!--{sfSetErrorStyle}--><!--{/if}--> />
        </td>
    </tr>
    <tr>
        <th class="colmun">収納企業コード<span class="attention"> *</span></th>
        <td>
            <!--{assign var=key value="syuno_co_cd"}-->
            <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
            <input type="text" name="syuno_co_cd" class="box30" size="30" value="<!--{$arrForm.syuno_co_cd.value|h}-->" maxlength="<!--{$arrForm[$key].length|h}-->" <!--{if $arrErr[$key]}--><!--{sfSetErrorStyle}--><!--{/if}--> />
        </td>
    </tr>    
    <tr>
        <th class="colmun">ショップパスワード<span class="attention"> *</span></th>
        <td>
            <!--{assign var=key value="shop_pwd"}-->
            <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
            <input type="text" name="shop_pwd" class="box30" size="30" value="<!--{$arrForm.shop_pwd.value|h}-->" maxlength="<!--{$arrForm[$key].length|h}-->" <!--{if $arrErr[$key]}--><!--{sfSetErrorStyle}--><!--{/if}--> />
        </td>
    </tr>
    <tr class="n">
        <th class="colmun">利用決済<span class="attention"> *</span><br />（SMBCファイナンスサービスとご契約いただいている決済手段のみ選択して下さい）</th>
        <td>
        <!--{assign var=key value="pay_type"}-->
        <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
        <label><input type="checkbox" name="credit" value="1" onclick="toggleCreditBox();" <!--{if $arrForm.credit.value eq 1}-->checked<!--{/if}--> />クレジットカード決済</label><br />
        <label><input type="checkbox" name="conveni_number" value="1" onclick="toggleConveniBox();" <!--{if $arrForm.conveni_number.value eq 1}-->checked<!--{/if}--> />コンビニエンスストア（受付番号）決済</label><br />
        <label><input type="checkbox" name="bank_transfer" value="1" onclick="toggleBankBox();" <!--{if $arrForm.bank_transfer.value eq 1}-->checked<!--{/if}--> />銀行振込決済（払込票決済のみでご利用の場合は、チェック不要）</label><br />
        <label><input type="checkbox" name="pay_easy" value="1" <!--{if $arrForm.pay_easy.value eq 1}-->checked<!--{/if}--> />ペイジー決済（払込票決済のみでご利用の場合は、チェック不要）</label><br />
        <label><input type="checkbox" name="netbank" value="1" <!--{if $arrForm.netbank.value eq 1}-->checked<!--{/if}--> />ネットバンク決済</label><br />
        <label><input type="checkbox" name="electronic_money" value="1" <!--{if $arrForm.electronic_money.value eq 1}-->checked<!--{/if}--> />電子マネー決済</label><br />
        <label><input type="checkbox" name="payment_slip" value="1" onclick="togglePaymentSlip();" <!--{if $arrForm.payment_slip.value eq 1}-->checked<!--{/if}--> />払込票利用の決済（コンビニエンスストア（払込票）決済、ゆうちょ振替決済など）</label><br />
        <label><input type="checkbox" name="credit_regular" value="1" onclick="toggleCreditRegularBox();" <!--{if $arrForm.credit_regular.value eq 1}-->checked<!--{/if}--> />クレジットカード決済(継続課金)</label><br />
        </td>
    </tr>
    <tr class="n">
        <th class="colmun" colspan="2">▼クレジットカード決済 お支払い区分<br />（上記利用決済で「クレジットカード決済」を選択されている場合は必須）</th>
    </tr>
    <tr class="n">
        <th class="colmun">お支払い区分</th>
        <td>
        <!--{assign var=key value="paymethod"}-->
        <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
        <label><input type="checkbox" name="pay_once" value="1" <!--{if $arrForm.pay_once.value eq 1}-->checked<!--{/if}--> <!--{if $arrForm.credit.value ne 1}-->disabled<!--{/if}--> />１回払い</label><br />
        <label><input type="checkbox" name="pay_twice" value="1" <!--{if $arrForm.pay_twice.value eq 1}-->checked<!--{/if}--> <!--{if $arrForm.credit.value ne 1}-->disabled<!--{/if}--> />２回払い</label><br />
        <label><input type="checkbox" name="pay_monthly03" value="1" <!--{if $arrForm.pay_monthly03.value eq 1}-->checked<!--{/if}--> <!--{if $arrForm.credit.value ne 1}-->disabled<!--{/if}--> />分割払い（３回）</label><br />
        <label><input type="checkbox" name="pay_monthly05" value="1" <!--{if $arrForm.pay_monthly05.value eq 1}-->checked<!--{/if}--> <!--{if $arrForm.credit.value ne 1}-->disabled<!--{/if}--> />分割払い（５回）</label><br />
        <label><input type="checkbox" name="pay_monthly06" value="1" <!--{if $arrForm.pay_monthly06.value eq 1}-->checked<!--{/if}--> <!--{if $arrForm.credit.value ne 1}-->disabled<!--{/if}--> />分割払い（６回）</label><br />
        <label><input type="checkbox" name="pay_monthly10" value="1" <!--{if $arrForm.pay_monthly10.value eq 1}-->checked<!--{/if}--> <!--{if $arrForm.credit.value ne 1}-->disabled<!--{/if}--> />分割払い（１０回）</label><br />
        <label><input type="checkbox" name="pay_monthly12" value="1" <!--{if $arrForm.pay_monthly12.value eq 1}-->checked<!--{/if}--> <!--{if $arrForm.credit.value ne 1}-->disabled<!--{/if}--> />分割払い（１２回）</label><br />
        <label><input type="checkbox" name="pay_monthly15" value="1" <!--{if $arrForm.pay_monthly15.value eq 1}-->checked<!--{/if}--> <!--{if $arrForm.credit.value ne 1}-->disabled<!--{/if}--> />分割払い（１５回）</label><br />
        <label><input type="checkbox" name="pay_monthly18" value="1" <!--{if $arrForm.pay_monthly18.value eq 1}-->checked<!--{/if}--> <!--{if $arrForm.credit.value ne 1}-->disabled<!--{/if}--> />分割払い（１８回）</label><br />
        <label><input type="checkbox" name="pay_monthly20" value="1" <!--{if $arrForm.pay_monthly20.value eq 1}-->checked<!--{/if}--> <!--{if $arrForm.credit.value ne 1}-->disabled<!--{/if}--> />分割払い（２０回）</label><br />
        <label><input type="checkbox" name="pay_monthly24" value="1" <!--{if $arrForm.pay_monthly24.value eq 1}-->checked<!--{/if}--> <!--{if $arrForm.credit.value ne 1}-->disabled<!--{/if}--> />分割払い（２４回）</label><br />
        <label><input type="checkbox" name="pay_revolving" value="1" <!--{if $arrForm.pay_revolving.value eq 1}-->checked<!--{/if}--> <!--{if $arrForm.credit.value ne 1}-->disabled<!--{/if}--> />リボ払い</label><br />
        <label><input type="checkbox" name="pay_bonus" value="1" <!--{if $arrForm.pay_bonus.value eq 1}-->checked<!--{/if}--> <!--{if $arrForm.credit.value ne 1}-->disabled<!--{/if}--> />ボーナス一括払い</label><br />
        </td>
    </tr>
    <tr class="n">
        <th class="colmun" colspan="2">▼クレジットカード決済 セキュリティコード入力欄表示設定<br />（上記利用決済で「クレジットカード決済」を選択されている場合は必須）</th>
    </tr>
    <tr class="n">
        <th class="colmun">セキュリティコード</th>
        <td>
        <!--{assign var=key value="security_code_flg"}-->
        <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
        <label><input type="radio" name="security_code_flg" value="1" <!--{if $arrForm.security_code_flg.value eq 1}-->checked<!--{/if}--> <!--{if $arrForm.credit.value ne 1}-->disabled<!--{/if}--> />利用する</label><br />
        <label><input type="radio" name="security_code_flg" value="2" <!--{if $arrForm.security_code_flg.value eq 2}-->checked<!--{/if}--> <!--{if $arrForm.credit.value ne 1}-->disabled<!--{/if}--> />利用しない</label><br />
        </td>
    </tr>
    <tr class="n">
        <th class="colmun" colspan="2">▼クレジットカード情報お預かり機能表示設定<br />（上記利用決済で「クレジットカード決済」を選択されている場合は必須）</th>
    </tr>
    <tr class="n">
        <th class="colmun">クレジットカードお預かり機能</th>
        <td>
        <!--{assign var=key value="card_info_keep"}-->
        <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
        <label><input type="radio" name="card_info_keep" value="1" onclick="disableKeepPwd();" <!--{if $arrForm.card_info_keep.value eq 1}-->checked<!--{/if}--> <!--{if $arrForm.credit.value ne 1}-->disabled<!--{/if}-->  />利用する</label><br />
        <label><input type="radio" name="card_info_keep" value="2" onclick="enableKeepPwd();" <!--{if $arrForm.card_info_keep.value eq 2}-->checked<!--{/if}--> <!--{if $arrForm.credit.value ne 1}-->disabled<!--{/if}-->  />利用しない</label><br />
        </td>
    </tr>
    <tr>
        <th class="colmun">お預かり機能用パスワード<br />（「お預かり機能」で利用するを選択している場合は必須）</th>
        <td>
            <!--{assign var=key value="card_info_pwd"}-->
            <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
            <input type="text" name="card_info_pwd" class="box30" size="30" value="<!--{$arrForm.card_info_pwd.value|h}-->" maxlength="<!--{$smarty.const.MDL_SMBC_CARD_INFO_PWD_LEN}-->" <!--{if $arrErr[$key]}--><!--{sfSetErrorStyle}--><!--{/if}--> <!--{if $arrForm.card_info_keep.value ne 1}-->disabled<!--{/if}--> />
        </td>
    </tr>
    <tr class="n">
        <th class="colmun" colspan="2">▼銀行振込決済<br />（上記利用決済で「銀行振込決済」を選択されている場合は必須）</th>
    </tr>
    <tr>
        <th class="colmun">入金誤差範囲内の設定</th>
        <td>
            <font color="#ff0000">入金金額 ＞ 請求金額</font> <!--{html_options name=over_deposit options=$arrDeposit selected=$arrForm.over_deposit.value}--><br />
            入金金額 ＝ 請求金額 「入金済み」にする<br />
            <font color="#0000ff">入金金額 ＜ 請求金額</font> <!--{html_options name=short_deposit options=$arrDeposit selected=$arrForm.short_deposit.value|default:0}--><br />
        </td>
    </tr>
    <tr>
        <th class="colmun">入金誤差範囲外の設定</th>
        <td>
            SMBCファイナンスサービスからの入金照会<br />
            <!--{html_options name=request_deposit options=$arrDeposit selected=$arrForm.request_deposit.value|default:0}--><br />
        </td>
    </tr>
    <tr class="n">
        <th class="colmun" colspan="2">▼コンビニ決済（受付番号）設定<br />（上記利用決済で「コンビニエンスストア（受付番号）決済」を選択されている場合は必須）</th>
    </tr>
    <tr class="n">
        <th class="colmun">コンビニ選択</th>
        <td>
        <!--{assign var=key value="conveni"}-->
        <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
        <label><input type="checkbox" name="seven_eleven" value="1" <!--{if $arrForm.seven_eleven.value eq 1}-->checked<!--{/if}--> <!--{if $arrForm.conveni_number.value ne 1}-->disabled<!--{/if}--> />セブン-イレブン</label><br />
        <label><input type="checkbox" name="lawson" value="1" <!--{if $arrForm.lawson.value eq 1}-->checked<!--{/if}--> <!--{if $arrForm.conveni_number.value ne 1}-->disabled<!--{/if}--> />ローソン</label><br />
        <label><input type="checkbox" name="seicomart" value="1" <!--{if $arrForm.seicomart.value eq 1}-->checked<!--{/if}--> <!--{if $arrForm.conveni_number.value ne 1}-->disabled<!--{/if}--> />セイコーマート</label><br />
        <label><input type="checkbox" name="familymart" value="1" <!--{if $arrForm.familymart.value eq 1}-->checked<!--{/if}--> <!--{if $arrForm.conveni_number.value ne 1}-->disabled<!--{/if}--> />ファミリーマート</label><br />
        <label><input type="checkbox" name="circlek_sunkus" value="1" <!--{if $arrForm.circlek_sunkus.value eq 1}-->checked<!--{/if}--> <!--{if $arrForm.conveni_number.value ne 1}-->disabled<!--{/if}--> />サークルK・サンクス</label><br />
        </td>
    </tr>
    <tr class="n">
        <th class="colmun" colspan="2">▼払込票決済設定<br />（上記利用決済で「払込票利用の決済」を選択されている場合は必須）</th>
    </tr>
    <tr class="n">
        <th class="colmun">払込票の印刷</th>
        <td>
        <!--{assign var=key value="payment_slip_issue"}-->
        <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
        <label><input type="radio" name="payment_slip_issue" value="1" onclick="disableDestination();" <!--{if $arrForm.payment_slip_issue.value eq 1}-->checked<!--{/if}--> <!--{if $arrForm.payment_slip.value ne 1}-->disabled<!--{/if}--> />自社発行（ショップ様での印刷）</label><br />
        <label><input type="radio" name="payment_slip_issue" value="2" onclick="enableDestination();" <!--{if $arrForm.payment_slip_issue.value eq 2}-->checked<!--{/if}--> <!--{if $arrForm.payment_slip.value ne 1}-->disabled<!--{/if}--> />代行発行（SMBCファイナンスサービスでの印刷）</label><br />
        </td>
    </tr>
    <tr class="n">
        <th class="colmun">郵送先<br />（「払込票の印刷」で代行発行を選択している場合は必須）</th>
        <td>
        <!--{assign var=key value="payment_slip_destination"}-->
        <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
        <label><input type="radio" name="payment_slip_destination" value="1" <!--{if $arrForm.payment_slip_destination.value eq 1}-->checked<!--{/if}--> <!--{if $arrForm.payment_slip_issue.value ne 2}-->disabled<!--{/if}--> />ショップ様</label><br />
        <label><input type="radio" name="payment_slip_destination" value="2" <!--{if $arrForm.payment_slip_destination.value eq 2}-->checked<!--{/if}--> <!--{if $arrForm.payment_slip_issue.value ne 2}-->disabled<!--{/if}--> />注文者様</label><br />
        </td>
    </tr>
    <tr class="n">
        <th class="colmun" colspan="2">
          ▼クレジットカード 定期販売設定<br />(「クレジットカード(継続課金)」を選択している場合は必須)<br />
          定期販売の３Ｄセキュア認証・セキュリティコード認証の利用有無は
          決済ステーションへの契約にお申込頂いた内容でチェックされます。<br />
          変更をご希望の場合は、営業担当者へご連絡下さい。
        </th>
    </tr>
    <tr>
        <th class="colmun">契約コード</th>
        <td>
            <!--{assign var=key value="regular_shop_cd"}-->
            <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
            <input type="text" name="<!--{$key}-->" class="box30" size="30" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length|h}-->" <!--{if $arrErr[$key]}--><!--{sfSetErrorStyle}--><!--{/if}--> />
        </td>
    </tr>
    <tr>
        <th class="colmun">収納企業コード</th>
        <td>
            <!--{assign var=key value="regular_syuno_co_cd"}-->
            <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
            <input type="text" name="<!--{$key}-->" class="box30" size="30" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length|h}-->" <!--{if $arrErr[$key]}--><!--{sfSetErrorStyle}--><!--{/if}--> />
        </td>
    </tr>
    <tr>
        <th class="colmun">ショップパスワード</th>
        <td>
            <!--{assign var=key value="regular_shop_pwd"}-->
            <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
            <input type="text" name="<!--{$key}-->" class="box30" size="30" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length|h}-->" <!--{if $arrErr[$key]}--><!--{sfSetErrorStyle}--><!--{/if}--> />
        </td>
    </tr>
    <tr>
        <th class="colmun">取引検索用パスワード</th>
        <td>
            <!--{assign var=key value="regular_deal_pwd"}-->
            <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
            <input type="text" name="<!--{$key}-->" class="box30" size="30" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length|h}-->" <!--{if $arrErr[$key]}--><!--{sfSetErrorStyle}--><!--{/if}--> />
        </td>
    </tr>

</table>

<div class="btn-area">
    <ul>
        <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'register', '', ''); return false;" name="subm"><span class="btn-next">この内容で登録する</span></a></li>
    </ul>
</div>
</form>
<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
