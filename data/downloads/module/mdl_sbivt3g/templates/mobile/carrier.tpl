<!--{*
 * carrier.tpl - キャリア決済入力画面
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: carrier.tpl 193 2013-07-31 01:24:57Z kaji $
 * @link        http://www.veritrans.co.jp/3gps
 *}-->
<!--▼CONTENTS-->
<form method="post" action="<!--{$smarty.server.PHP_SELF|h}-->">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="exec" />
  <p>
    ご利用になるお支払い方法を選択して下さい。
  </p>
  <!--{if is_array($arrRes) == true}-->
  <font color="#FF0000">
  <!--{if $arrRes.isOK == false}-->
    [<!--{$arrRes.vResultCode}-->]<!--{$arrRes.mErrMsg}-->
  <!--{/if}-->
  </font><br />
  <!--{/if}-->

  ■ キャリア<font color="#FF0000">※</font><br />
  <font color="#FF0000"><!--{$arrErr.serviceOptionType}--></font>
  <!--{foreach from=$arrCarrierServices key=key item=carrierName}-->
  <input type="radio" name="serviceOptionType" value="<!--{$key}-->"
    <!--{if $arrForm.serviceOptionType.value == $key}-->
    checked="checked"<!--{/if}--> />
    <!--{$carrierName|h}--><br />
  <!--{/foreach}-->

  <br />

  <div align="center"><input type="submit" value="選択したキャリアで決済" /></div>
</form>

<form method="post" action="<!--{$smarty.server.PHP_SELF|h}-->">
  <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
  <input type="hidden" name="mode" value="back" />
  <div align="center"><input type="submit" value="戻る" /></div>
</form>
<!--▲CONTENTS-->
