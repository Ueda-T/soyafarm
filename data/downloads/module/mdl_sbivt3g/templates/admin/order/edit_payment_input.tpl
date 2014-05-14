<!--{*
 * edit_payment_input.tpl - 受注管理編集 決済情報入力インクルード
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: admin_config.tpl
 * @link        http://www.veritrans.co.jp/3gps
 *}-->

<!--{if $objSbivt->isDstCredit()}-->

<span class="attention"><!--{$arrErr.cardInput}--></span>
<!--{if $objSbivt->canDownPriceCapture()}-->
<!--{assign var=key value="doDownPriceCapture"}-->
<span class="attention"><!--{$arrErr[$key]}--></span>
<label for="<!--{$key}-->1" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
  <input type="radio"
    id="<!--{$key}-->1" name="<!--{$key}-->" value="1"
    <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}-->/>
    減額処理を売上請求で行う
</label><br />
<label for="<!--{$key}-->0" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
  <input type="radio"
    id="<!--{$key}-->0" name="<!--{$key}-->" value="0"
    <!--{if $arrForm[$key].value == "0"}-->checked="checked"<!--{/if}-->/>
    減額処理を再取引で行う
</label>
<input type="hidden" name="canDownPriceCapture" value="1" />

<!--{elseif $objSbivt->canDownPriceCancel()}-->
<!--{assign var=key value="doDownPriceCancel"}-->
<span class="attention"><!--{$arrErr[$key]}--></span>
<label for="<!--{$key}-->1" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
  <input type="radio"
    id="<!--{$key}-->1" name="<!--{$key}-->" value="1"
    <!--{if $arrForm[$key].value == "1"}-->checked="checked"<!--{/if}-->/>
    減額処理を部分取消で行う
</label><br />
<label for="<!--{$key}-->0" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
  <input type="radio"
    id="<!--{$key}-->0" name="<!--{$key}-->" value="0"
    <!--{if $arrForm[$key].value == "0"}-->checked="checked"<!--{/if}-->/>
    減額処理を再取引で行う
</label>
<input type="hidden" name="canDownPriceCancel" value="1" />
<!--{/if}-->

<table style="background-color: #eee; margin: 5px 0 0;">
  <tr>
    <td rowspan="2"><strong>[カード番号入力決済時]</strong></th>
    <td>
      <!--{assign var=key value="newCardNo"}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      カード番号
      <input type="text" name="<!--{$key}-->"  id="<!--{$key}-->" 
        class="box20" maxlength="<!--{$arrForm[$key].length}-->"
        value="<!--{$arrForm[$key].value|h}-->"
        style="ime-mode:disabled; <!--{$arrErr[$key]|sfGetErrorColor}-->"/>
    </td>
  </tr>
  <tr>
    <td>
      <!--{assign var=key1 value="newExpiryMon"}-->
      <!--{assign var=key2 value="newExpiryYear"}-->
      <span class="attention"><!--{$arrErr[$key1]}--></span>
      <span class="attention"><!--{$arrErr[$key2]}--></span>
      有効期限(mm/yy)
      <input type="text" name="<!--{$key1}-->" id="<!--{$key1}-->"
        class="box3" maxlength="<!--{$arrForm[$key1].length}-->"
        value="<!--{$arrForm[$key1].value|h}-->"
        style="ime-mode:disabled; <!--{$arrErr[$key1]|sfGetErrorColor}-->"/>
      ／
      <input type="text" name="<!--{$key2}-->" id="<!--{$key2}-->"
        class="box3" maxlength="<!--{$arrForm[$key2].length}-->"
        value="<!--{$arrForm[$key2].value|h}-->"
        style="ime-mode:disabled; <!--{$arrErr[$key2]|sfGetErrorColor}-->"/>
    </td>
  </tr>

  <!--{if count($objSbivt->arrReTradeOId) > 0}-->
  <tr>
    <td><strong>[再取引決済時]</strong></td>
    <td>
      <!--{assign var=key value="newReTradeOId"}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      再取引用注文番号
      <select name="<!--{$key}-->" id="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->" >
        <option value="">--選択してください--</option>
        <!--{html_options options=$objSbivt->arrReTradeOId selected=$arrForm[$key].value}-->
      </select>
    </td>
  </tr>
  <!--{/if}-->

  <tr>
    <td rowspan="2"><strong>[カード番号入力・再取引決済共通]</strong></td>
    <td>
      <!--{assign var=key1 value="newPaymentType"}-->
      <!--{assign var=key2 value="newPaymentCount"}-->
      <span class="attention"><!--{$arrErr[$key1]}--></span>
      <span class="attention"><!--{$arrErr[$key2]}--></span>
      お支払い方法
      <select name="<!--{$key1}-->" id="<!--{$key1}-->"
        style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" >
        <option value=""> </option>
        <!--{html_options options=$objSbivt->arrPaymentType
          selected=$arrForm[$key1].value|h}-->
      </select>
      お支払い回数
      <select name="<!--{$key2}-->" id="<!--{$key2}-->"
        style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" >
        <option value=""> </option>
        <!--{html_options options=$objSbivt->arrPaymentCount
          selected=$arrForm[$key2].value|h}-->
      </select>
    </td>
  </tr>
  <tr>
    <td>
      <!--{assign var=key value="newCaptureFlg"}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      売上フラグ
      <select name="<!--{$key}-->" id="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->" >
        <!--{html_options options=$objSbivt->arrCardCaptures
          selected=$arrForm[$key].value|h}-->
      </select>
    </td>
  </tr>
</table>
<input type="hidden" name="cardInput" value="1" />

<!--{elseif $objSbivt->isDstCvs() }-->
<span class="attention"><!--{$arrErr.cvsInput}--></span>
<table style="background-color: #eee; margin: 5px 0 0;">
  <tr>
    <td>
      <strong>[コンビニ決済]</strong>
    </td>
    <td>
      <!--{assign var=key value="newConveni"}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      コンビニ店舗
      <select name="<!--{$key}-->" id="<!--{$key}-->"
        style="<!--{$arrErr[$key]|sfGetErrorColor}-->" >
        <option value="">--選択してください--</option>
        <!--{html_options options=$objSbivt->arrCvsShop selected=$arrForm[$key].value}-->
      </select>
    </td>
  </tr>
</table>
<input type="hidden" name="cvsInput" value="1" />
<!--{else}-->
なし
<!--{/if}-->

