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
<!--{include file=$smarty.const.MDL_SBIVT3G_TPL_PATH|cat:"sphone/css/sbivt3g.css.tpl"}-->
<!--{include file=$smarty.const.MDL_SBIVT3G_TPL_PATH|cat:"sphone/js/sbivt3g.js.tpl"}-->

<div id="under02column">
  <div id="under02column_shopping" class="sbivt3g">
    <h2 class="spNaked"><img width="23" height="16" src="<!--{$TPL_URLPATH}-->img/rohto/icon_cart.gif">クレジットカード情報入力</h2>


      <p class="naked">
        ご利用になられますクレジットカード情報を下記フォームに入力してください。<br />
        決済ボタンを押していただきますと、決済を開始いたします。<br />
        <span class="attention">※ブラウザの戻るボタンはご利用になれませんのでご注意ください。</span>
        <!--{if $tpl_canReTrade == true}--><br />
        これまでのご購入にご利用いただいたカード情報を使って決済することもできます。<!--{/if}-->
      </p>

    <!--{if $tpl_canReTrade == true}-->
    <a id="reTradeTop" name="reTradeTop"></a>
    <form name="frmSbiReTrade" id="frmSbiReTrade" method="post" action="<!--{$smarty.server.PHP_SELF|h}-->">
      <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
      <input type="hidden" name="mode" value="retrade" />

      <!--{if is_array($arrReTradeRes) == true}-->
      <div class="attention">
      <!--{if $arrReTradeRes.isOK == false}-->
        [<!--{$arrReTradeRes.vResultCode}-->]<!--{$arrReTradeRes.mErrMsg}-->
      <!--{/if}-->
      </div>
      <!--{/if}-->
      <!--{assign var=key value="cardId"}-->
      <!--{assign var=rows value=$arrReTradeCard|@count}-->

        <div class="bdrGray">
          <h3>クレジットカード番号/有効期限(月/年)</h3>
          <div class="bgYellow">
        <!--{foreach name="retd" from="$arrReTradeCard" key="cardId" item="rec"}-->
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
              /
              <!--{$rec.cardExpire}-->
            </label>
            <br />
        <!--{/foreach}-->
          </div>
        </div>

        <div class="bdrGray">
          <h3>お支払い方法</h3>
          <div class="bgYellow">
            <!--{assign var=key value="reTradePaymentType"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <select name="<!--{$key}-->" id="<!--{$key}-->"
              style="<!--{$arrErr[$key]|sfGetErrorColor}-->" >
              <option value="">--選択してください--</option>
              <!--{* 一括払いを選択状態にする *}-->
              <!--{html_options options=$arrPaymentType
                selected=$smarty.const.MDL_SBIVT3G_PTYPE_BULK}-->
            </select>
          </div>
        </div>
        <!--{if count($arrPaymentCount) > 0}-->
        <div class="bdrGray">
          <h3>お支払い回数</h3>
            <p class="attention">※<span class="mini">分割払い選択時は必須</span></p>
          <div class="bgYellow">
            <!--{assign var=key value="reTradePaymentCount"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <select name="<!--{$key}-->" id="<!--{$key}-->"
              style="<!--{$arrErr[$key]|sfGetErrorColor}-->" >
              <option value="">--選択してください--</option>
              <!--{html_options options=$arrPaymentCount
                selected=$arrForm[$key].value|h}-->
            </select>
          </div>
        </div>
        <!--{else}-->
        <input type="hidden" name="<!--{$key2}-->" value="" />
        <!--{/if}-->

<!--{* 2011/11/26 再取引時は不要
        <!--{if $objSetting->get('C_securityFlg') == true}-->
        <div class="bdrGray">
          <h3>セキュリティコード</h3>
          <div class="bgYellow">
            <!--{assign var=key value="reTradeSecurityCode"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <div style="width:40%;">
              <input type="text" name="<!--{$key}-->" id="<!--{$key}-->"
                class="box120" maxlength="<!--{$arrForm[$key].length}-->"
                value="<!--{$arrForm[$key].value|h}-->"
                style="width:100%; ime-mode:disabled; <!--{$arrErr[$key]|sfGetErrorColor}-->"/>
            </div>
          </div>
          <p class="naked">カードの裏面3桁または表面4桁に記載されたコードを入力して下さい</p>
  
        </div>
        <!--{/if}-->
*}-->

      <div class="btn">
        <p style="margin:10px 0;"><input data-role="none" type="button" id="btnReTrade" class="btnOrange" value="選択したカードで決済" /></p>
        <p style="margin:10px 0;"><input data-role="none" type="button" id="btnReTradeBack" class="btnGray02" value="戻る" /></p>
      </div>
    </form>

    <h2 class="spNaked"><img width="23" height="16" src="<!--{$TPL_URLPATH}-->img/rohto/icon_cart.gif">クレジットカード決済入力</h2>
    <!--{/if}-->

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

      <div class="bdrGray">
          <h3>クレジットカード番号</h3>
          <div class="bgYellow">
            <!--{assign var=key value="cardNo"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <div style="width:90%">
            <input type="text" name="<!--{$key}-->"  id="<!--{$key}-->" 
              class="box240" maxlength="<!--{$arrForm[$key].length}-->"
              value="<!--{$arrForm[$key].value|h}-->"
              style="ime-mode:disabled; <!--{$arrErr[$key]|sfGetErrorColor}-->"/>
            </div>
          </div>
          <p class="naked" style="margin:10px 0 0;">半角数字入力(ハイフン有無は問いません)</p>
      </div>

        <div class="bdrGray">
          <h3>クレジットカード 有効期限(月／年)</h3>
          <div class="bgYellow clearfix">
            <!--{assign var=key1 value="expiryMon"}-->
            <!--{assign var=key2 value="expiryYear"}-->
            <span class="attention"><!--{$arrErr[$key1]}--></span>
            <span class="attention"><!--{$arrErr[$key2]}--></span>
            <div style="width:40%;margin-right:15px;float:left;">
            <label for="<!--{$key1}-->" style="display:none">Month</label>
            <input type="text" name="<!--{$key1}-->" id="<!--{$key1}-->"
              maxlength="<!--{$arrForm[$key1].length}-->"
              value="<!--{$arrForm[$key1].value|h}-->"
              style="width:100%; ime-mode:disabled; <!--{$arrErr[$key1]|sfGetErrorColor}-->"/>
            </div>
            <div style="width:40%;float:left;">
            <label for="<!--{$key2}-->" style="display:none">Year</label>
            <input type="text" name="<!--{$key2}-->" id="<!--{$key2}-->"
              maxlength="<!--{$arrForm[$key2].length}-->"
              value="<!--{$arrForm[$key2].value|h}-->"
              style="width:100%; ime-mode:disabled; <!--{$arrErr[$key2]|sfGetErrorColor}-->"/>
            </div>
          </div>
        </div>

        <div class="bdrGray">
          <h3>クレジットカード名義</h3>
          <div class="bgYellow clearfix">
            <!--{assign var=key1 value="firstName"}-->
            <!--{assign var=key2 value="lastName"}-->
            <span class="attention"><!--{$arrErr[$key1]}--></span>
            <span class="attention"><!--{$arrErr[$key2]}--></span>
            <div style="width:40%;margin-right:15px;float:left;">
            <label for="<!--{$key1}-->" style="display:none">名</label>
            <input type="text" name="<!--{$key1}-->" id="<!--{$key1}-->"
              placeholder="名"
              maxlength="<!--{$arrForm[$key1].length}-->"
              value="<!--{$arrForm[$key1].value|h}-->"
              style="width:100%; ime-mode:disabled; <!--{$arrErr[$key1]|sfGetErrorColor}-->"/>
            </div>
            <div style="width:40%;float:left;">
            <label for="<!--{$key2}-->" style="display:none">姓</label>
            <input type="text" name="<!--{$key2}-->" id="<!--{$key2}-->"
              placeholder="姓"
              maxlength="<!--{$arrForm[$key2].length}-->"
              value="<!--{$arrForm[$key2].value|h}-->"
              style="width:100%; ime-mode:disabled; <!--{$arrErr[$key2]|sfGetErrorColor}-->"/>
            </div>
          </div>
          <p style="clear:both;margin:10px 0 0;" class="naked">半角英字入力(大文字小文字は問いません)</p>
        </div>

        <div class="bdrGray">
          <h3>お支払い方法</h3>
          <div class="bgYellow">
            <!--{assign var=key value="paymentType"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <select name="<!--{$key}-->" id="<!--{$key}-->"
              style="<!--{$arrErr[$key]|sfGetErrorColor}-->" >
              <option value="">--選択してください--</option>
              <!--{* 一括払いを選択状態にする *}-->
              <!--{html_options options=$arrPaymentType
                selected=$smarty.const.MDL_SBIVT3G_PTYPE_BULK}-->
            </select>
          </div>
        </div>
        <!--{if count($arrPaymentCount) > 0}-->
        <div class="bdrGray">
          <h3>お支払い回数</h3>
            <p class="attention">※<span class="mini">分割払い選択時は必須</span></p>
          <div class="bgYellow">
            <!--{assign var=key value="paymentCount"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <select name="<!--{$key}-->" id="<!--{$key}-->"
              style="<!--{$arrErr[$key]|sfGetErrorColor}-->" >
              <option value="">--選択してください--</option>
              <!--{html_options options=$arrPaymentCount
                selected=$arrForm[$key].value|h}-->
            </select>
          </div>
        </div>
        <!--{else}-->
        <input type="hidden" name="paymentCount" value="" />
        <!--{/if}-->

        <!--{if $objSetting->get('C_securityFlg') == true}-->
        <div class="bdrGray">
          <h3>セキュリティコード</h3>
          <div class="bgYellow">
            <!--{assign var=key value="securityCode"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <div style="width:40%">
              <input type="text" name="<!--{$key}-->" id="<!--{$key}-->"
                maxlength="<!--{$arrForm[$key].length}-->"
                value="<!--{$arrForm[$key].value|h}-->"
                style="width:100%; ime-mode:disabled; <!--{$arrErr[$key]|sfGetErrorColor}-->"/>
            </div>
          </div>
            <p class="naked" style="margin:10px 0 0;">カードの裏面3桁または表面4桁に記載されたコードを入力して下さい</p>
        </div>
        <!--{/if}-->
      </table>
      <div class="btn">
        <p style="margin:10 0px;"><input data-role="none" type="button" id="btnExec" class="btnOrange" value="入力したカードで決済" /></p>
        <p style="margin:10 0px;"><input data-role="none" type="button" id="btnBack" class="btnGray02" value="戻る" /></p>
      </div>
    </form>
  </div>
</div>
<!--▲CONTENTS-->
