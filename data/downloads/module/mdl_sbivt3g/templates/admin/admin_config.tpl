<!--{*
 * admin_config.tpl - 店舗別情報設定画面
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: admin_config.tpl 197 2013-08-13 01:25:36Z kaji $
 * @link        http://www.veritrans.co.jp/3gps
 *}-->
<!--▼CONTENTS-->

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->
<style type="text/css">
  .info { padding: 0 4px; }
</style>
<script type="text/javascript">//<![CDATA[
self.resizeTo(700, 800);
self.focus();
//]]>
</script>

<h2><!--{$tpl_subtitle}--></h2>
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|escape}-->">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid|escape}-->" />
<input type="hidden" name="mode" value="exec" />
<p class="remark">
ベリトランス3G MDK決済モジュールをご利用頂く為には、
別途ベリトランス株式会社様とのご契約後に発行されるマーチャントCCIDを
設定する必要があります。<br/><br/>
</p>

<!--{if $arrErr.err != ""}-->
<div style="border:solid 1px #CCCCCC;background-color:#FFEEEE;margin:5px 0;padding:10px;" class="attention">
<strong>ファイルエラーが発生しました</strong><br/> 
<!--{$arrErr.err|nl2br}-->
</div>
<!--{/if}-->

<table class="form">
  <colgroup width="30%"></colgroup>
  <colgroup width="70%"></colgroup>

  <tr>
    <th colspan="2">▼全決済共通設定</th>
  </tr>
  <tr>
    <th>マーチャントCCID<span class="attention">※</span></th>
    <td>
      <!--{assign var=key value=merchantCcId}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <input type="text"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        class="box40"
        value="<!--{$arrForm[$key].value|h}-->"
        maxlength="<!--{$arrForm[$key].length}-->" />
    </td>
  </tr>
  <tr>
    <th>マーチャント認証鍵<span class="attention">※</span></th>
    <td>
      <!--{assign var=key value=merchantPass}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <input type="password"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        class="box40"
        value="<!--{$arrForm[$key].value|h}-->"
        maxlength="<!--{$arrForm[$key].length}-->" />
    </td>
  </tr>

  <tr>
    <th>ダミーモード</th>
    <td>
      <!--{assign var=key value=dummyModeFlg}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <label for="<!--{$key}-->">
        <input id="<!--{$key}-->"
          type="checkbox"
          name="<!--{$key}-->"
          style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
          value="1"
          <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}-->/>
          ダミーモードで稼働する
      </label>
    </td>
  </tr>

  <tr>
    <th>取引IDプレフィックス</th>
    <td>
      <!--{assign var=key value=dummyModePrefix}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <label for="<!--{$key}-->">
        <input type="text" name="<!--{$key}-->" class="box30"
          style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
          value="<!--{$arrForm[$key].value|h}-->"
          maxlength="<!--{$arrForm[$key].length}-->" />
      </label><br/>
      ※半角英数字、"-"(ハイフン)、"_"(アンダースコア)が設定可能
    </td>
  </tr>
</table>


<!--{*クレジットカード決済*}-->
<table class="form" id="C">
  <colgroup width="45%"></colgroup>
  <colgroup width="55%"></colgroup>
  <tr>
    <th colspan="2">▼<!--{$smarty.const.PAYMENT_NAME_CREDIT}-->設定</th>
  </tr>
  <tr>
    <th><!--{$smarty.const.PAYMENT_NAME_CREDIT}-->の利用</th>
    <td>
    <!--{assign var=key value="C_validFlg"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <label for="<!--{$key}-->">
    <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}-->
        /> 利用する</label>
    </td>
  </tr>
  <tr>
    <th>売上フラグ</th>
    <td>
      <!--{assign var=key value="C_captureFlg"}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
        <option value="" selected="">選択してください</option>
        <!--{html_options options=$arrCardCaptures selected=$arrForm[$key].value}-->
      </select>
    </td>
  </tr>
  <tr>
    <th>支払方法・回数の指定</th>
    <td>
      <div style="width:96%;height:15px;padding:5px;background-color:#EEEEEE">
        <strong>ご契約カードブランド</strong>
        <span style="font-size:80%;">ご契約のブランドを選択して下さい</span>
      </div>
      <!--{assign var=key value="C_cardType"}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <!--{foreach name="brand" from=$arrCredit item=val key=code}-->
      <label for="<!--{$key}-->_<!--{$code}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
        <input id="<!--{$key}-->_<!--{$code}-->" type="checkbox"
          name="<!--{$code}-->" value="1"
          <!--{if $arrForm[$code].value == "1"}-->checked="checked"<!--{/if}-->/>
          <!--{$val}-->
      </label>
      <br/>
      <!--{/foreach}-->

      <div style="width:96%;height:15px;padding:5px;background-color:#EEEEEE">
        <strong>支払回数</strong>
        <span style="font-size:80%;">必要な回数を選択して下さい</span>
      </div>
      <!--{assign var=key value="C_settingCount"}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <!--{foreach name="pCount" from=$arrSettingCount item=val key=code}-->
      <label for="<!--{$key}-->_<!--{$code}-->"
        style="display:block;width:49%;float:left;<!--{$arrErr[$key]|sfGetErrorColor}-->">
        <input id="<!--{$key}-->_<!--{$code}-->" type="checkbox"
          name="<!--{$key}-->[<!--{$code}-->]" value="1"
          <!--{if $arrForm[$key].value[$code]}-->checked="checked"<!--{/if}-->
          />
          <!--{$val}-->
      </label>
      <!--{/foreach}-->
      <br clear="both" />
      ※一括払いは必ずご利用いただけます。<br/>
      ※収納代行契約は、分割払い（3回～24回）、リボルビング払いをご利用いただけます。<br/>
    </td>
  </tr>
  
  
  <tr>
    <th>セキュリティーコード認証</th>
    <td>
      <!--{assign var=key value="C_securityFlg"}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <label for="<!--{$key}-->">
      <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}-->/>
        利用する</label>
    </td>
  </tr>
  <tr>
    <th>本人認証(3Dセキュア)</th>
    <td>
      <!--{assign var=key value="C_mpiFlg"}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <label for="<!--{$key}-->">
      <input type="checkbox"
        name="<!--{$key}-->"
        id="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1" <!--{if $arrForm[$key].value}--> checked <!--{/if}-->
        /> 利用する</label>
    </td>
  </tr>
  <tr>
    <th>本人認証タイプ</th>
    <td>
      <!--{assign var=key value="C_mpiOption"}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <select name="<!--{$key}-->" id="<!--{$key1}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
        <option value="" selected="selected">選択してください</option>
        <!--{html_options options=$arrMpiOption selected=$arrForm[$key].value}-->
      </select> 
    </td>
  </tr>
  <tr>
    <th>再取引機能</th>
    <td>
      <!--{assign var=key value="C_reTradeFlg"}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <label for="<!--{$key}-->">
      <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}-->/>
        利用する</label>
    </td>
           
</table>

<table class="form" id="V">
  <colgroup width="45%"></colgroup>
  <colgroup width="55%"></colgroup>
  <tr>
    <th colspan="2">▼<!--{$smarty.const.PAYMENT_NAME_CONVENI}-->設定</th>
  </tr>
  
  <tr>
    <th>01：セブンイレブンの利用</th>
    <td>
    <!--{assign var=key value="V_sejFlg"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <label for="<!--{$key}-->">
    <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}-->
        /> 利用する
    </label>
    </td>
  </tr>
  <tr>
    <th>02：ローソン・ミニストップ・セイコーマートの利用</th>
    <td>
    <!--{assign var=key value="V_lawsonFlg"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <label for="<!--{$key}-->">
    <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}-->
        /> 利用する
        <br/>※05：をご利用の場合は、選択しないでください。
    </label>
    </td>
  </tr>
  <tr>
    <th>03：ファミリーマートの利用</th>
    <td>
    <!--{assign var=key value="V_famimaFlg"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <label for="<!--{$key}-->">
    <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}-->
        /> 利用する
        <br/>※05：をご利用の場合は、選択しないでください。
    </label>
    </td>
  </tr>
  <tr>
    <th>04：サークルKサンクス・デイリーヤマザキの利用</th>
    <td>
    <!--{assign var=key value="V_otherFlg"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <label for="<!--{$key}-->">
    <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1" <!--{if $arrForm[$key].value}--> checked <!--{/if}-->
        /> 利用する</label>
    </td>
  </tr>
  <tr>
    <th>05：ローソン・ファミリーマート・ミニストップ・セイコーマートの利用</th>
    <td>
    <!--{assign var=key value="V_econFlg"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <label for="<!--{$key}-->">
    <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}-->
        /> 利用する
        <br/>※2013年9月以降にご契約の場合はこちらを選択してください。
    </label>
    </td>
  </tr>
  <tr>
    <th>決済期限日数</th>
    <td>
      <!--{assign var=key value=V_limitDays}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <input type="text"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        class="box6"
        value="<!--{$arrForm[$key].value}-->"
        maxlength="<!--{$arrForm[$key].length}-->">
    </td>
  </tr>
  <tr>
    <th>店舗名</th>
    <td>
      <!--{assign var=key value=V_shopName}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <input type="text"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        class="box40"
        value="<!--{$arrForm[$key].value|h}-->"
        maxlength="<!--{$arrForm[$key].length}-->">
      <br/>※セブンイレブンでは無効
    </td>
  </tr>
  <tr>
    <th>備考</th>
    <td>
      <!--{assign var=key value=V_note}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <input type="text"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        class="box40"
        value="<!--{$arrForm[$key].value|h}-->"
        maxlength="<!--{$arrForm[$key].length}-->">
      <br/>※「セブンイレブン」、「ローソン・ミニストップ・セイコーマート」、「ローソン・ファミリーマート・ミニストップ・セイコーマート」では無効
    </td>
  </tr>
</table>


<table class="form" id="EE">
  <colgroup width="45%"></colgroup>
  <colgroup width="55%"></colgroup>
  <tr>
    <th colspan="2">▼電子マネー(Edy)決済設定</th>
  </tr>
  <tr>
    <th><!--{*モバイルEdy*}--><!--{$smarty.const.PAYMENT_NAME_EDY_MOBILE}-->有効フラグ</th>
    <td>
    <!--{assign var=key value="EE_mobFlg"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <label for="<!--{$key}-->">
    <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}-->/> 有効にする</label>
    </td>
  </tr>

  <tr>
    <th><!--{*サイバーEdy*}--><!--{$smarty.const.PAYMENT_NAME_EDY_CYBER}-->有効フラグ</th>
    <td>
    <!--{assign var=key value="EE_pcFlg"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <label for="<!--{$key}-->">
    <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}-->/> 有効にする</label>
    </td>
  </tr>
  <tr>
    <th>決済期限日数
    <span class="attention"></span></th>
    <td>
      <!--{assign var=key value=EE_limitDays}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <input type="text"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        class="box6"
        value="<!--{$arrForm[$key].value|h}-->"
        maxlength="<!--{$arrForm[$key].length}-->"/>
      
     <br />※1～90日の範囲で設定可能
     <br />※<!--{*サイバーEdy*}--><!--{$smarty.const.PAYMENT_NAME_EDY_CYBER}-->では10日間固定
    </td>
  </tr>
  <tr>
    <th>店舗名</th>
    <td>
      <!--{assign var=key value=EE_shopName}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <input type="text"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        class="box40"
        value="<!--{$arrForm[$key].value|h}-->"
        maxlength="<!--{$arrForm[$key].length}-->" />
      <div>※<!--{*サイバーEdy*}--><!--{$smarty.const.PAYMENT_NAME_EDY_CYBER}-->では無効</div>
    </td>
  </tr>
  <tr>
    <th>依頼メールBCC要否</th>
    <td>
      <!--{assign var=key value="EE_bccMailFlg"}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <label for="<!--{$key}-->">
      <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}-->/> 送信する</label>

      <br />※<!--{*サイバーEdy*}--><!--{$smarty.const.PAYMENT_NAME_EDY_CYBER}-->では無効
    </td>
  </tr>
  <tr>
    <th>依頼メールBCCアドレス</th>
    <td>
      <!--{assign var=key value=EE_bccMailAddr}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <input type="text"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        class="box30"
        value="<!--{$arrForm[$key].value|h}-->"
        maxlength="<!--{$arrForm[$key].length}-->">
      <br/>※<!--{*サイバーEdy*}--><!--{$smarty.const.PAYMENT_NAME_EDY_CYBER}-->では無効
    </td>
  </tr>
  <tr>
    <th>依頼メール付加情報</th>
    <td>
      <!--{assign var=key value=EE_reqMailInfo}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <textarea rows="3" name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        class="box40"><!--{$arrForm[$key].value|h}--></textarea>
      <br/>※<!--{*サイバーEdy*}--><!--{$smarty.const.PAYMENT_NAME_EDY_CYBER}-->では無効
    </td>
  </tr>
  <tr>
    <th>完了メール付加情報</th>
    <td>
      <!--{assign var=key value=EE_cmpMailInfo}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <textarea rows="3" name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        class="box40"><!--{$arrForm[$key].value|h}--></textarea>
      <br/>※<!--{*サイバーEdy*}--><!--{$smarty.const.PAYMENT_NAME_EDY_CYBER}-->では無効
    </td>
  </tr>

<table class="form" id="ES">
  <colgroup width="45%"></colgroup>
  <colgroup width="55%"></colgroup>
  <tr>
    <th colspan="2">▼電子マネー決済(Suica)設定</th>
  </tr>
  <tr>
    <th><!--{*モバイルSuica決済(メール型)y*}--><!--{$smarty.const.PAYMENT_NAME_SUICA_MOBILE_MAIL}-->有効フラグ</th>
    <td>
    <!--{assign var=key value="ES_mobMailFlg"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <label for="<!--{$key}-->">
    <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}--> /> 有効にする</label>
    </td>
  </tr>
  <tr>
    <th><!--{*モバイルSuica決済*}--><!--{$smarty.const.PAYMENT_NAME_SUICA_MOBILE_APP}-->有効フラグ</th>
    <td>
    <!--{assign var=key value="ES_mobAppFlg"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <label for="<!--{$key}-->">
    <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}--> /> 有効にする</label>
    </td>
  </tr>
  <tr>
    <th><!--{*Suicaインターネットサービス決済(メール型)*}--><!--{$smarty.const.PAYMENT_NAME_SUICA_PC_MAIL}-->有効フラグ</th>
    <td>
    <!--{assign var=key value="ES_pcMailFlg"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <label for="<!--{$key}-->">
    <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}--> /> 有効にする</label>
    </td>
  </tr>
  <tr>
    <th><!--{*Suicaインターネットサービス決済(アプリ型)*}--><!--{$smarty.const.PAYMENT_NAME_SUICA_PC_APP}-->有効フラグ</th>
    <td>
    <!--{assign var=key value="ES_pcAppFlg"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <label for="<!--{$key}-->">
    <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}--> /> 有効にする</label>
    </td>
  </tr>
  
  <tr>
    <th>決済期限日数</th>
    <td>
      <!--{assign var=key value=ES_limitDays}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <input type="text"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        class="box6"
        value="<!--{$arrForm[$key].value|h}-->"
        maxlength="<!--{$arrForm[$key].length}-->">
      ※1～365日の範囲で設定可能
    </td>
  </tr>

  <tr>
    <th>表示商品・サービス名</th>
    <td>
      <!--{assign var=key value=ES_shopName}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <input type="text"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        class="box40"
        value="<!--{$arrForm[$key].value|h}-->"
        maxlength="<!--{$arrForm[$key].length}-->">
    </td>
  </tr>

  <tr>
    <th>依頼・返金メールBCC要否</th>
    <td>
    <!--{assign var=key value="ES_bccMailFlg"}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
    <label for="<!--{$key}-->">
      <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value}-->checked="checked"<!--{/if}--> /> 送信する</label>
      <br />※メールお届け型以外では無効
    </td>
  </tr>
  <tr>
    <th>依頼・返金メールBCCアドレス</th>
    <td>
      <!--{assign var=key value=ES_bccMailAddr}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <input type="text"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        class="box40"
        value="<!--{$arrForm[$key].value|h}-->"
        maxlength="<!--{$arrForm[$key].length}-->">
      <br />※メールお届け型以外では無効
    </td>
  </tr>
  <tr>
    <th>依頼メール付加情報</th>
    <td>
      <!--{assign var=key value=ES_reqMailInfo}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <textarea rows="3" name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        class="box40"><!--{$arrForm[$key].value|h}--></textarea>
      <br />※メールお届け型以外では無効
    </td>
  </tr>
  <tr>
    <th>完了メール付加情報</th>
    <td>
      <!--{assign var=key value=ES_cmpMailInfo}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <textarea rows="3" name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        class="box40"><!--{$arrForm[$key].value|h}--></textarea>
      <br />※メールお届け型以外では無効
    </td>
  </tr>
  <tr>
    <th>内容確認付加情報</th>
    <td>
      <!--{assign var=key value=ES_cnfDispInfo}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <textarea rows="3" name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        class="box40"><!--{$arrForm[$key].value|h}--></textarea>
    </td>
  </tr>
  <tr>
    <th>完了画面付加情報</th>
    <td>
      <!--{assign var=key value=ES_cmpDispInfo}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <textarea rows="3" name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        class="box40"><!--{$arrForm[$key].value|h}--></textarea>
    </td>
  </tr>
</table>

<!--{* 2011/10/04 凍結
<table class="form" id="EW">
  <colgroup width="45%"></colgroup>
  <colgroup width="55%"></colgroup>
  <tr>
    <th colspan="2">▼電子マネー(Waon)決済設定</th>
  </tr>
  <tr>
    <th><!--{$smarty.const.PAYMENT_NAME_WAON_MOBILE}-->有効フラグ</th>
    <td>
    <!--{assign var=key value="EW_mobFlg"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <label for="<!--{$key}-->">
    <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}--> /> 有効にする</label>
    </td>
  </tr>
  <tr>
    <th><!--{$smarty.const.PAYMENT_NAME_WAON_PC}-->有効フラグ</th>
    <td>
    <!--{assign var=key value="EW_pcFlg"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <label for="<!--{$key}-->">
    <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}--> /> 有効にする</label>
    </td>
  </tr>
  <tr>
    <th>決済期限日数</th>
    <td>
      <!--{assign var=key value=EW_limitDays}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <input type="text"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        class="box6"
        value="<!--{$arrForm[$key].value|h}-->"
        maxlength="<!--{$arrForm[$key].length}-->">
      <br />※1～365日の範囲で設定可能
    </td>
  </tr>
  <tr>
    <th>返金期限日数</th>
    <td>
      <!--{assign var=key value=EW_limitCancel}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <input type="text"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        class="box6"
        value="<!--{$arrForm[$key].value|h}-->"
        maxlength="<!--{$arrForm[$key].length}-->">
      <br />※1～365日の範囲で設定可能
    </td>
  </tr>
</table>
*}-->
<input type="hidden" name="EW_mobFlg" value="0" />
<input type="hidden" name="EW_pcFlg" value="0" />
  
<table class="form" id="B">
  <colgroup width="45%"></colgroup>
  <colgroup width="55%"></colgroup>
  <tr>
    <th colspan="2">▼銀行・郵貯(Pay-easy)決済設定</th>
  </tr>
  <tr>
    <th><!--{$smarty.const.PAYMENT_NAME_ATM}-->有効フラグ</th>
    <td>
    <!--{assign var=key value="B_atmFlg"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <label for="<!--{$key}-->">
    <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}--> /> 有効にする</label>
    </td>
  </tr>
  <tr>
    <th><!--{$smarty.const.PAYMENT_NAME_NETBANK}-->有効フラグ</th>
    <td>
    <!--{assign var=key value="B_netFlg"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <label for="<!--{$key}-->">
    <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}--> /> 有効にする</label>
    </td>
  </tr>
  <tr>
    <th>決済期限日数</th>
    <td>
      <!--{assign var=key value=B_limitDays}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <input type="text"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        class="box6"
        value="<!--{$arrForm[$key].value|h}-->"
        maxlength="<!--{$arrForm[$key].length}-->">
      <br />※1～60日の範囲で設定可能
    </td>
  </tr>
  <tr>
    <th>請求内容</th>
    <td>
      <!--{assign var=key value=B_note}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <input type="text"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        class="box40"
        value="<!--{$arrForm[$key].value|h}-->"
        maxlength="<!--{$arrForm[$key].length}-->">
    </td>
  </tr>
  <tr>
    <th>請求内容カナ</th>
    <td>
      <!--{assign var=key value=B_noteKana}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <input type="text"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        class="box40"
        value="<!--{$arrForm[$key].value|h}-->"
        maxlength="<!--{$arrForm[$key].length}-->">
    </td>
  </tr>
</table>

<table class="form" id="P">
  <colgroup width="45%"></colgroup>
  <colgroup width="55%"></colgroup>
  <tr>
    <th colspan="2">▼<!--{*PayPal決済*}--><!--{$smarty.const.PAYMENT_NAME_PAYPAL}-->設定</th>
  </tr>
  <tr>
    <th>有効フラグ</th>
    <td>
    <!--{assign var=key value="P_validFlg"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <label for="<!--{$key}-->">
    <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}--> /> 有効にする</label>
    </td>
  </tr>
  <tr>
    <th>売上フラグ</th>
    <td>
      <!--{assign var=key value="P_captureFlg"}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
        <option value="" selected="">選択してください</option>
        <!--{html_options options=$arrPaypalCaptures selected=$arrForm[$key].value}-->
      </select>
    </td>
  </tr>
  <tr>
    <th>オーダー説明</th>
    <td>
      <!--{assign var=key value=P_note}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <textarea rows="3" name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        class="box40"><!--{$arrForm[$key].value|h}--></textarea>
    </td>
  </tr>
</table>

<table class="form" id="CA">
  <colgroup width="45%"></colgroup>
  <colgroup width="55%"></colgroup>
  <tr>
    <th colspan="2">▼<!--{*キャリア決済*}--><!--{$smarty.const.PAYMENT_NAME_CARRIER}-->設定</th>
  </tr>
  <tr>
    <th><!--{$smarty.const.PAYMENT_NAME_CARRIER_DOCOMO}-->の利用</th>
    <td>
    <!--{assign var=key value="CA_docomoFlg"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <label for="<!--{$key}-->">
    <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}-->
        /> 利用する</label><br/>
    <div style="width:96%;height:15px;padding:5px;background-color:#EEEEEE">
      <strong>PCで<!--{$smarty.const.PAYMENT_NAME_CARRIER_DOCOMO}-->を許可</strong>
    </div>
    <!--{assign var=key value="CA_docomoPcFlg"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <label for="<!--{$key}-->">
    <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}-->
	/> 許可する</label>
    </td>
  </tr>
  <tr>
    <th><!--{$smarty.const.PAYMENT_NAME_CARRIER_AU}-->の利用</th>
    <td>
    <!--{assign var=key value="CA_auFlg"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <label for="<!--{$key}-->">
    <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}-->
        /> 利用する</label>
    </td>
  </tr>
  <tr>
    <th><!--{$smarty.const.PAYMENT_NAME_CARRIER_SB_KTAI}-->の利用</th>
    <td>
    <!--{assign var=key value="CA_sb_ktaiFlg"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <label for="<!--{$key}-->">
    <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}-->
        /> 利用する</label><br/>
    <div style="width:96%;height:15px;padding:5px;margin-bottom:3px;background-color:#EEEEEE">
      <strong>本人認証(3Dセキュア)</strong>
    </div>
    <!--{assign var=key value="CA_3DFlg"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
      <!--{html_options options=$arrCarrier3D selected=$arrForm[$key].value}-->
    </select>
    </td>
  </tr>
  <tr>
    <th><!--{$smarty.const.PAYMENT_NAME_CARRIER_SB_MATOMETE}-->の利用</th>
    <td>
    <!--{assign var=key value="CA_sb_matometeFlg"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <label for="<!--{$key}-->">
    <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}-->
        /> 利用する</label><br/>
    </td>
  </tr>
  <tr>
    <th><!--{$smarty.const.PAYMENT_NAME_CARRIER_S_BIKKURI}-->の利用</th>
    <td>
    <!--{assign var=key value="CA_s_bikkuriFlg"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <label for="<!--{$key}-->">
    <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}-->
        /> 利用する</label><br/>
    </td>
  </tr>
  <tr>
    <th>商品タイプ</th>
    <td>
      <!--{assign var=key value="CA_itemType"}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
        <option value="" selected="">選択してください</option>
        <!--{html_options options=$arrCarrierItemTypes selected=$arrForm[$key].value}-->
      </select>
	  <br/>※<!--{$smarty.const.PAYMENT_NAME_CARRIER_SB_MATOMETE}-->：デジタルコンテンツのみ可能
	  <br/>※<!--{$smarty.const.PAYMENT_NAME_CARRIER_S_BIKKURI}-->：役務不可
    </td>
  </tr>
  <tr>
    <th>売上フラグ</th>
    <td>
      <!--{assign var=key value="CA_captureFlg"}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
        <option value="" selected="">選択してください</option>
        <!--{html_options options=$arrCarrierCaptures selected=$arrForm[$key].value}-->
      </select>
	  <br/>※<!--{$smarty.const.PAYMENT_NAME_CARRIER_SB_MATOMETE}-->は「与信＋売上請求」のみ可能
    </td>
  </tr>
  <tr>
    <th>商品情報</th>
    <td>
      <!--{assign var=key value=CA_itemInfo}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <input type="text"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        class="box40"
        value="<!--{$arrForm[$key].value|h}-->"
        maxlength="<!--{$arrForm[$key].length}-->" />
      <br/>※<!--{$smarty.const.PAYMENT_NAME_CARRIER_DOCOMO}-->と<!--{$smarty.const.PAYMENT_NAME_CARRIER_SB_MATOMETE}-->で利用されます
      <br/>※機種依存文字を指定すると決済がエラーとなります
    </td>
  </tr>
</table>

<table>
  <tr>
    <th>▼上書きファイル一覧</th>
  </tr>
  <tr>
    <td style="width:100%">
     「この内容で登録する」ボタンをクリックすると、以下のモジュール稼働のために以下のファイルを自動で上書きします。<br/>
      以下のファイルにカスタマイズを行っている箇所が含まれている場合は消去されます。自動での上書きをスキップするには『ファイルを自動上書きしない(カスタマイズ利用者向け)』を選択して下さい。<br/>
      <span class="attention">※上書き後、以下のファイルを編集するとモジュールが正常に稼働しない可能性があります。</span><br/>
      <!--{assign var=key value="doNotOverride"}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <label for="<!--{$key}-->_0">
      <input type="radio" name="<!--{$key}-->" id="<!--{$key}-->_0"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->" value="0"
        <!--{if $arrForm[$key].value != "1"}--> checked="checked" <!--{/if}-->
        /> ファイルを自動上書きする(デフォルト)</label><br/>
      <label for="<!--{$key}-->_1">
      <input type="radio" name="<!--{$key}-->" id="<!--{$key}-->_1"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->" value="1"
        <!--{if $arrForm[$key].value == "1"}--> checked="checked" <!--{/if}-->
        /> ファイルを自動上書きしない(カスタマイズ利用者向け)</label>
    </td>
  </tr>
  <tr>
    <td>
      <ul style="list-style-type:disc">
        <!--{foreach from=$installFiles item=file}-->
        <li style="white-space:nowrap;list-style-position:inside;list-style-type:disc;margin-left:5px;text-indent:2px"><!--{$file.dst|wordwrap:75:'<br/>':true}--></li>
        <!--{/foreach}-->
      </ul>
    </td>
  </tr>
</table>

<table>
  <tr>
    <th>▼入金通知プログラム</th>
  </tr>
  <tr>
    <td style="width:100%">
      入金通知プログラムとは、購入者が注文完了後にコンビニ、電子マネー、銀行・郵貯などで入金操作を行ったという情報を3Gサーバーから自動で受信するプログラムのことです。<br/>
      MAP(ベリトランスのマーチャント管理ポータルサービス)で入金通知プログラムを有効にして以下のURLを設定して下さい。<br/>
      <span class="attention">※クレジットカード決済以外の決済は入金通知プログラムを有効にしないと返金処理などをEC-CUBEから行うことができません。</span>
    </td>
  </tr>
  <tr>
    <td>
      <ul>
        <li>お客様の入金通知URL</li>
        <li><strong><!--{$smarty.const.MDL_SBIVT3G_RECEIVE_URL}--></strong></li>
      </ul>
    </td>
  </tr>
</table>

<!--{if $smarty.const.MDL_SBIVT3G_AUTOMAIL_ENABLED == true}-->
<table>
  <colgroup width="45%"></colgroup>
  <colgroup width="55%"></colgroup>
  <tr>
    <th colspan="2" style="width:100%">▼お支払い期限前・期限切れメール配信プログラム</th>
  </tr>
  <tr>
    <td colspan="2" style="width:100%">
      お支払い期限前・期限切れメール配信プログラムとは、コンビニ決済、電子マネー決済、銀行・郵貯決済、銀聯ネット決済に設けられている支払期限の数日前、及び期限経過後に自動でメールを配信するプログラムのことです。<br/>
      配信プログラムを設定することで受注情報の期限数日前、及び期限が経過した受注に対してお客様への案内のメール配信を行うことができます。<br/>
    </td>
  </tr>
  <tr>
    <th>お支払い期限前メール自動配信</th>
    <td>
    <!--{assign var=key value="noticeMailFlg"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <label for="<!--{$key}-->">
    <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}--> checked="checked" <!--{/if}-->
        /> 利用する</label>
    </td>
  </tr>
  <tr>
    <th>お支払い期限切れメール自動配信</th>
    <td>
    <!--{assign var=key value="expireMailFlg"}-->
    <span class="attention"><!--{$arrErr[$key]}--></span>
    <label for="<!--{$key}-->">
    <input type="checkbox"
        id="<!--{$key}-->"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        value="1"
        <!--{if $arrForm[$key].value == "1"}--> checked="checked" <!--{/if}-->
           /> 利用する</label>
    </td>
  </tr>
  <tr>
    <th>お支払い期限前メールの配信タイミング</th>
    <td>
      <!--{assign var=key value="noticeDays"}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      支払期限の<input type="text"
        name="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->"
        class="box6"
        value="<!--{$arrForm[$key].value|h}-->"
        maxlength="<!--{$arrForm[$key].length}-->" />日前<br/>
    </td>
  </tr>
  <tr>
    <td colspan="2" style="width:100%">
      EC-CUBEをインストールしたサーバがLinuxサーバの場合はcronに、Windowsサーバであればタスクスケジューラに以下のPHPプログラムファイルを日次で起動するように設定して下さい。
    </td>
  </tr>
  <tr>
    <td colspan="2" style="width:100%">
      <ul>
        <li>配信プログラムのファイルパス</li>
        <li><strong><!--{$smarty.const.MDL_SBIVT3G_AUTO_MAIL_PROGRAM|realpath}--></strong></li>
      </ul>
    </td>
  </tr>
</table>
<!--{/if}-->

<div class="btn-area" id="btn-area">
  <ul>
    <li><a class="btn-action" href="javascript:;" onclick="fnModeSubmit('exec', '', ''); return false;"><span class="btn-next">この内容で登録する</span></a></li>
  </ul>
</div>
</form>
<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
