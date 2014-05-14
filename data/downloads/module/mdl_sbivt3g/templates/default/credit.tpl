<!--{*
 * credit.tpl - クレジットカード決済入力画面
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: credit.tpl 236 2014-02-04 05:02:23Z kaji $
 * @link        http://www.veritrans.co.jp/3gps
 *}-->
<!--▼CONTENTS-->
<!--{include file=$smarty.const.MDL_SBIVT3G_TPL_PATH|cat:"default/css/sbivt3g.css.tpl"}-->
<!--{include file=$smarty.const.MDL_SBIVT3G_TPL_PATH|cat:"default/js/sbivt3g.js.tpl"}-->

<h1><img src="<!--{$TPL_URLPATH}-->img/rohto/order_title_step3.gif" width="960" height="70" alt="購入手続き：クレジットカード決済" /></h1>

<div class="wrapCoan">

  <div class="alert2"><h3>お支払い手続きはまだ完了していません。</h3></div>

    <p>ご注文ありがとうございます。<br>
    続いて、お支払いクレジットカード情報を入力してください。「決済ボタン」をクリックされると、決済を開始いたします。</p>
    <p style="margin:15px 0;"><span class="red">※ブラウザの「戻る」ボタンで戻られますと、ご注文が重複する場合がございます。ご利用にならないでください。</span></p>

    <div class="wrapCoanEle">


    <!--{if $tpl_canReTrade == true}-->
    <h2><img src="<!--{$TPL_URLPATH}-->img/rohto/method_midashi13.gif" alt="かんたん決済（前回ご利用カードでのお支払い）" /></h2>
    <form name="frmSbiReTrade" id="frmSbiReTrade" method="post" action="<!--{$smarty.server.PHP_SELF|h}-->">
      <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
      <input type="hidden" name="mode" value="retrade" />
      <p style="margin:15px 0;">
        これまでのご購入にご利用いただいたカード情報を使って決済することもできます。
      </p>
      <!--{if is_array($arrReTradeRes) == true}-->
      <div class="attention">
      <!--{if $arrReTradeRes.isOK == false}-->
        [<!--{$arrReTradeRes.vResultCode}-->]<!--{$arrReTradeRes.mErrMsg}-->
      <!--{/if}-->
      </div>
      <!--{/if}-->
      <!--{assign var=key value="cardId"}-->
      <!--{assign var=rows value=$arrReTradeCard|@count}-->
      <table summary="クレジットカード選択" class="tblOrder">
        <tr>
          <th rowspan="<!--{$rows+1}-->">
            <span><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" width="31" height="13" class="type" />クレジットカード番号</span>
          </th>
          <th><span>カード番号</span></th>
          <th><span>有効期限(月／年)</span></th>
        </tr>
        <!--{foreach name="retd" from="$arrReTradeCard" key="cardId" item="rec"}-->
        <tr>
          <td>
            <!--{if $smarty.foreach.retd.first }-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <!--{/if}-->
            <label for="<!--{$key}-->_<!--{$cardId}-->"
                style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
              <input type="radio" name="<!--{$key}-->"
                id="<!--{$key}-->_<!--{$cardId}-->"
                value="<!--{$cardId}-->"
                <!--{if $arrForm[$key].value == $cardId}-->checked="checked"<!--{/if}-->
                />
              <!--{$rec.cardNumber}-->
            </label>
          </td>
          <td>
            <!--{$rec.cardExpire}-->
          </td>
        </tr>
        <!--{/foreach}-->
        <tr>
          <th>
            <span><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" width="31" height="13" class="type" />お支払い方法</span>
          </th>
          <td colspan="2">
            <!--{assign var=key1 value="reTradePaymentType"}-->
            <!--{assign var=key2 value="reTradePaymentCount"}-->
            <span class="attention"><!--{$arrErr[$key1]}--></span>
            <span class="attention"><!--{$arrErr[$key2]}--></span>
            <select name="<!--{$key1}-->" id="<!--{$key1}-->"
              style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" >
              <option value="">--選択してください--</option>
              <!--{* 一括払いを選択状態にする *}-->
              <!--{html_options options=$arrPaymentType
                selected=$smarty.const.MDL_SBIVT3G_PTYPE_BULK}-->
            </select>
            <!--{if count($arrPaymentCount) > 0}-->
            お支払い回数
            <select name="<!--{$key2}-->" id="<!--{$key2}-->"
              style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" >
              <option value="">--選択してください--</option>
              <!--{html_options options=$arrPaymentCount
                selected=$arrForm[$key2].value|h}-->
            </select>
            <p class="mini">お支払い方法で分割払い選択時は必ずお支払い回数を選択してください</p>
            <!--{else}-->
            <input type="hidden" name="paymentCount" value="" />
            <!--{/if}-->
          </td>
        </tr>
<!--{* 2011/11/26 再取引時は不要
        <!--{if $objSetting->get('C_securityFlg') == true}-->
        <tr>
          <th>
            <span><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" width="31" height="13" class="type" />セキュリティコード</span>
          </th>
          <td colspan="2">
              <!--{assign var=key value="reTradeSecurityCode"}-->
              <span class="attention"><!--{$arrErr[$key]}--></span>
              <input type="text" name="<!--{$key}-->" id="<!--{$key}-->"
                class="box60" maxlength="<!--{$arrForm[$key].length}-->"
                value="<!--{$arrForm[$key].value|h}-->"
                style="ime-mode:disabled; <!--{$arrErr[$key]|sfGetErrorColor}-->"/>
              <p class="mini">カードの裏面3桁または表面4桁に記載されたコードを入力して下さい</p>
          </td>
        </tr>
        <!--{/if}-->
*}-->
      </table>
      <div class="orderBtn" style="padding-bottom:30px;">
        <p class="left">
          <span class="f-right"><img src="<!--{$TPL_URLPATH}-->img/rohto/credit_regi.gif" id="btnReTrade" class="swp" alt="選択したカードで決済" style="cursor:pointer;" /></span>
        <img src="<!--{$TPL_URLPATH}-->img/rohto/btn_back.gif" id="btnReTradeBack" class="swp" alt="戻る" style="cursor:pointer;" />
        </p>
      </div>
    </form>
    <!--{/if}-->

    <h2 style="margin-bottom:15px;"><img src="<!--{$TPL_URLPATH}-->img/rohto/method_midashi14.gif" alt="クレジットカード決済入力" /></h2>

    <form name="frmSbi" id="frmSbi" method="post" action="<!--{$smarty.server.PHP_SELF|h}-->">
      <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
      <input type="hidden" name="mode" value="exec" />
      <!--{if is_array($arrRes) == true}-->
      <div class="attention">
      <!--{if $arrRes.isOK == false}-->
        [<!--{$arrRes.vResultCode}-->]<!--{$arrRes.mErrMsg}-->
      <!--{/if}-->
      </div>
      <!--{/if}-->
      <table summary="クレジットカード決済入力" class="tblOrder">
        <tr>
          <th>
            <span><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" width="31" height="13" class="type" />クレジットカード番号</span>
          </th>
          <td>
            <!--{assign var=key value="cardNo"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$key}-->"  id="<!--{$key}-->" 
              class="box240" maxlength="<!--{$arrForm[$key].length}-->"
              value="<!--{$arrForm[$key].value|h}-->"
              style="ime-mode:disabled; <!--{$arrErr[$key]|sfGetErrorColor}-->"/>
            <p class="mini">半角数字入力(ハイフン有無は問いません)</p>
          </td>
        </tr>
        <tr>
          <th nowrap>
            <span><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" width="31" height="13" class="type" />クレジットカード有効期限(月／年)</span>
          </th>
          <td>
            <!--{assign var=key1 value="expiryMon"}-->
            <!--{assign var=key2 value="expiryYear"}-->
            <span class="attention"><!--{$arrErr[$key1]}--></span>
            <span class="attention"><!--{$arrErr[$key2]}--></span>
            <input type="text" name="<!--{$key1}-->" id="<!--{$key1}-->"
              class="box60" maxlength="<!--{$arrForm[$key1].length}-->"
              value="<!--{$arrForm[$key1].value|h}-->"
              style="ime-mode:disabled; <!--{$arrErr[$key1]|sfGetErrorColor}-->"/>
            ／
            <input type="text" name="<!--{$key2}-->" id="<!--{$key2}-->"
              class="box60" maxlength="<!--{$arrForm[$key2].length}-->"
              value="<!--{$arrForm[$key2].value|h}-->"
              style="ime-mode:disabled; <!--{$arrErr[$key2]|sfGetErrorColor}-->"/>
          </td>
        </tr>
        <tr>
          <th>
            <span><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" width="31" height="13" class="type" />クレジットカード名義</span>
          </th>
          <td>
            <!--{assign var=key1 value="firstName"}-->
            <!--{assign var=key2 value="lastName"}-->
            <span class="attention"><!--{$arrErr[$key1]}--></span>
            <span class="attention"><!--{$arrErr[$key2]}--></span>
            <label for="<!--{$key1}-->">名</label>
            <input type="text" name="<!--{$key1}-->" id="<!--{$key1}-->"
              class="box140" maxlength="<!--{$arrForm[$key1].length}-->"
              value="<!--{$arrForm[$key1].value|h}-->"
              style="ime-mode:disabled; <!--{$arrErr[$key1]|sfGetErrorColor}-->"/>
            <label for="<!--{$key2}-->">姓</label>
            <input type="text" name="<!--{$key2}-->" id="<!--{$key2}-->"
              class="box140" maxlength="<!--{$arrForm[$key2].length}-->"
              value="<!--{$arrForm[$key2].value|h}-->"
              style="ime-mode:disabled; <!--{$arrErr[$key2]|sfGetErrorColor}-->"/>
            <p class="mini">半角英字入力(大文字小文字は問いません)</p>
          </td>
        </tr>
        <tr>
          <th>
            <span><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" width="31" height="13" class="type" />お支払い方法</span>
          </th>
          <td>
            <!--{assign var=key1 value="paymentType"}-->
            <!--{assign var=key2 value="paymentCount"}-->
            <span class="attention"><!--{$arrErr[$key1]}--></span>
            <span class="attention"><!--{$arrErr[$key2]}--></span>
            <select name="<!--{$key1}-->" id="<!--{$key1}-->"
              style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" >
              <option value="">--選択してください--</option>
              <!--{* 一括払いを選択状態にする *}-->
              <!--{html_options options=$arrPaymentType
                selected=$smarty.const.MDL_SBIVT3G_PTYPE_BULK}-->
            </select>
            <!--{if count($arrPaymentCount) > 0}-->
            お支払い回数
            <select name="<!--{$key2}-->" id="<!--{$key2}-->"
              style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" >
              <option value="">--選択してください--</option>
              <!--{html_options options=$arrPaymentCount
                selected=$arrForm[$key2].value|h}-->
            </select>
            <p class="mini">お支払い方法で分割払い選択時は必ずお支払い回数を選択してください</p>
            <!--{else}-->
            <input type="hidden" name="paymentCount" value="" />
            <!--{/if}-->
          </td>
        </tr>

        <!--{if $objSetting->get('C_securityFlg') == true}-->
        <tr>
          <th>
            <span><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" width="31" height="13" class="type" />セキュリティコード</span>
          </th>
          <td>
              <!--{assign var=key value="securityCode"}-->
              <span class="attention"><!--{$arrErr[$key]}--></span>
              <input type="text" name="<!--{$key}-->" id="<!--{$key}-->"
                class="box60" maxlength="<!--{$arrForm[$key].length}-->"
                value="<!--{$arrForm[$key].value|h}-->"
                style="ime-mode:disabled; <!--{$arrErr[$key]|sfGetErrorColor}-->"/>
              <p class="mini">カードの裏面3桁または表面4桁に記載されたコードを入力して下さい</p>
          </td>
        </tr>
        <!--{/if}-->
      </table>

      <div class="orderBtn">
        <p class="left">
          <span class="f-right">
            <img src="<!--{$TPL_URLPATH}-->img/rohto/credit_regi.gif" id="btnExec" class="swp" value="入力したカードで決済" style="cursor:pointer;" /></span>
            <img src="<!--{$TPL_URLPATH}-->img/rohto/btn_back.gif" id="btnBack" class="swp" alt="戻る" style="cursor:pointer;" /></a>
        </p>
      </div>
    </form>
  </div>


<table cellspacing="0" style="margin:20px 0 0 0;">
<tr>
<td style="vertical-align:middle;"><img src="<!--{$TPL_URLPATH}-->img/rohto/credit.gif" alt="VISA、JCB、アメックス、ダイナーズカード、マスターカード" style="vertical-align:middle;">&nbsp;&nbsp;お支払いは一回払いのみとなります。</td>
</tr>
<tr>
<td style="font-size:130%;"><ul class="kome">
<li>ご購入情報はSSL暗号化通信により保護されます。また、ご注文後に当システムにカード情報が残ることはありません。</li>
</ul></td>
</tr>
</table>

</div>
<!--▲CONTENTS-->
