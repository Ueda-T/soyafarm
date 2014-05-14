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

<!--{if $tpl_canReTrade == true}-->
<a name="reTradeTop"></a>
ご利用になられますｸﾚｼﾞｯﾄｶｰﾄﾞ情報を下記ﾌｫｰﾑに入力してください｡<br>
決済ﾎﾞﾀﾝを押していただきますと､決済を開始いたします｡<br>
<font color="red">※ﾌﾞﾗｳｻﾞの戻るﾎﾞﾀﾝはご利用になれませんのでご注意ください｡</font><br>

<form method="post" action="<!--{$smarty.server.PHP_SELF|h|cat:"#reTradeTop"}-->">
  <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
  <input type="hidden" name="mode" value="retrade" />
  <p>
    これまでのご購入にご利用いただいたカード情報を使って決済することもできます。
  </p>

  <!--{if is_array($arrReTradeRes) == true}-->
  <font color="#FF0000">
  <!--{if $arrReTradeRes.isOK == false}-->
    [<!--{$arrReTradeRes.vResultCode}-->]<br />
    <!--{$arrReTradeRes.mErrMsg}-->
  <!--{/if}-->
  </font><br />
  <!--{/if}-->
  ■ クレジットカード番号 月／年<font color="#FF0000">※</font><br />
  <!--{assign var=key value="cardId"}-->
  <font color="#FF0000"><!--{$arrErr[$key]}--></font>
  <table>
    <!--{foreach name="retd" from="$arrReTradeCard" key="cardId" item="rec"}-->
    <tr>
      <td>
        <input type="radio" name="<!--{$key}-->" value="<!--{$cardId}-->"
          <!--{if $arrForm[$key].value == $cardId}-->checked="checked"<!--{/if}-->
          />
        <!--{$rec.cardNumber}-->
        <!--{$rec.cardExpire}-->
      </td>
    </tr>
    <!--{/foreach}-->
  </table>

  ■ お支払い方法<font color="#FF0000">※</font><br />
  <!--{assign var=key value="reTradePaymentType"}-->
  <font color="#FF0000"><!--{$arrErr[$key]}--></font>
  <select name="<!--{$key}-->">
    <option value="">--選択してください--</option>
      <!--{* 一括払いを選択状態にする *}-->
      <!--{html_options options=$arrPaymentType
      selected=$smarty.const.MDL_SBIVT3G_PTYPE_BULK}-->
    </select><br />
  <br />

<!--{if count($arrPaymentCount) > 0}-->
  ■ お支払い回数<font color="#FF0000">※</font><br />
  <!--{assign var=key value="reTradePaymentCount"}-->
  <font color="#FF0000"><!--{$arrErr[$key]}--></font>
  <select name="<!--{$key}-->" id="<!--{$key}-->"
    style="<!--{$arrErr[$key]|sfGetErrorColor}-->" >
    <option value="">--選択してください--</option>
    <!--{html_options options=$arrPaymentCount
      selected=$arrForm[$key].value|h}-->
  </select><br />
  ※分割払い選択時は必須<br />
  <br />
<!--{else}-->
  <input type="hidden" name="paymentCount" value="" />
<!--{/if}-->
  <div align="center"><input type="submit" value="選択したカードで決済" /></div>
</form>

<form method="post" action="<!--{$smarty.server.PHP_SELF|h}-->">
  <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
  <input type="hidden" name="mode" value="back" />
  <div align="center"><input type="submit" value="戻る" /></div>
</form>


<hr size="1">
クレジットカード番号入力
<hr size="1">
<!--{/if}-->

<form method="post" action="<!--{$smarty.server.PHP_SELF|h}-->">
  <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
  <input type="hidden" name="mode" value="exec" />
    ご利用になるクレジットカードの番号等を入力して下さい。<br><br>
  <!--{if is_array($arrRes) == true}-->
  <font color="#FF0000">
  <!--{if $arrRes.isOK == false}-->
    [<!--{$arrRes.vResultCode}-->]<br /><!--{$arrRes.mErrMsg}-->
  <!--{/if}-->
  </font><br />
  <!--{/if}-->
  ■ クレジットカード番号<font color="#FF0000">※</font><br />
  <!--{assign var=key value="cardNo"}-->
  <font color="#FF0000"><!--{$arrErr[$key]}--></font>
  <input type="text" name="<!--{$key}-->" istyle="4"
    maxlength="<!--{$arrForm[$key].length}-->"
    value="<!--{$arrForm[$key].value|h}-->" /><br />
  ※半角数字入力(ハイフン有無は問いません) <br />
  <br />

  ■ 有効期限(月／年)<font color="#FF0000">※</font><br />
  <!--{assign var=key1 value="expiryMon"}-->
  <!--{assign var=key2 value="expiryYear"}-->
  <font color="#FF0000"><!--{$arrErr[$key1]}--></font>
  <font color="#FF0000"><!--{$arrErr[$key2]}--></font>
  <input type="text" name="<!--{$key1}-->" istyle="4"
    maxlength="<!--{$arrForm[$key1].length}-->"
    size="<!--{$arrForm[$key1].length}-->"
    value="<!--{$arrForm[$key1].value|h}-->" />
  ／
  <input type="text" name="<!--{$key2}-->"istyle="4" 
    maxlength="<!--{$arrForm[$key2].length}-->"
    size="<!--{$arrForm[$key2].length}-->"
    value="<!--{$arrForm[$key2].value|h}-->" /><br />
  <br />

  ■ クレジットカード名義<font color="#FF0000">※</font><br />
  <!--{assign var=key1 value="firstName"}-->
  <!--{assign var=key2 value="lastName"}-->
  <font color="#FF0000"><!--{$arrErr[$key1]}--></font>
  <font color="#FF0000"><!--{$arrErr[$key2]}--></font>
  名<input type="text" name="<!--{$key1}-->" istyle="3"
      maxlength="<!--{$arrForm[$key1].length}-->"
      size="<!--{$arrForm[$key1].length}-->"
      value="<!--{$arrForm[$key1].value|h}-->" />
  姓<input type="text" name="<!--{$key2}-->" istyle="3"
      maxlength="<!--{$arrForm[$key2].length}-->"
      size="<!--{$arrForm[$key2].length}-->"
      value="<!--{$arrForm[$key2].value|h}-->" /><br />
  半角英字入力(大文字小文字は問いません)<br />
  <br />

  ■ お支払い方法<font color="#FF0000">※</font><br />
  <!--{assign var=key value="paymentType"}-->
  <font color="#FF0000"><!--{$arrErr[$key]}--></font>
  <select name="<!--{$key}-->">
    <option value="">--選択してください--</option>
      <!--{* 一括払いを選択状態にする *}-->
      <!--{html_options options=$arrPaymentType
      selected=$smarty.const.MDL_SBIVT3G_PTYPE_BULK}-->
    </select><br />
  <br />

<!--{if count($arrPaymentCount) > 0}-->
  ■ お支払い回数<font color="#FF0000">※</font><br />
  <!--{assign var=key value="paymentCount"}-->
  <font color="#FF0000"><!--{$arrErr[$key]}--></font>
  <select name="<!--{$key}-->" id="<!--{$key}-->"
    style="<!--{$arrErr[$key]|sfGetErrorColor}-->" >
    <option value="">--選択してください--</option>
    <!--{html_options options=$arrPaymentCount
      selected=$arrForm[$key].value|h}-->
  </select><br />
  ※分割払い選択時は必須<br />
  <br />
<!--{else}-->
  <input type="hidden" name="paymentCount" value="" />
<!--{/if}-->

  <!--{if $objSetting->get('C_securityFlg') == true}-->
  ■ セキュリティコード<font color="#FF0000">※</font><br />
  <!--{assign var=key value="securityCode"}-->
  <font color="#FF0000"><!--{$arrErr[$key]}--></font>
  <input type="text" name="<!--{$key}-->" istyle="4"
    maxlength="<!--{$arrForm[$key].length}-->"
    value="<!--{$arrForm[$key].value|h}-->" /><br />
  ※カードの裏面3桁または表面4桁に記載されたコードを入力して下さい<br />
  <br />
  <!--{/if}-->

  <div align="center"><input type="submit" value="入力したカードで決済" /></div>
</form>

<form method="post" action="<!--{$smarty.server.PHP_SELF|h}-->">
  <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
  <input type="hidden" name="mode" value="back" />
  <div align="center"><input type="submit" value="戻る" /></div>
</form>

<!--▲CONTENTS-->
