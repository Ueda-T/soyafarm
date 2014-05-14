<!--{*
 * cvs.tpl - コンビニ決済入力画面
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: cvs.tpl 61 2011-08-23 11:20:05Z hira $
 * @link        http://www.veritrans.co.jp/3gps
 *}-->
<!--▼CONTENTS-->
<form method="post" action="<!--{$smarty.server.PHP_SELF|h}-->">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="exec" />
  <p>
    ご利用になるコンビニエンスストアを選択して下さい。<br />
    注文後<!--{$objSetting->get('V_limitDays')}-->日以内にお支払いいただけない場合、ご注文を取消とさせていただくことがありますのでご了承下さい。
  </p>
  <!--{if is_array($arrRes) == true}-->
  <font color="#FF0000">
  <!--{if $arrRes.isOK == false}-->
    [<!--{$arrRes.vResultCode}-->]<!--{$arrRes.mErrMsg}-->
  <!--{/if}-->
  </font><br />
  <!--{/if}-->

  ■ コンビニエンスストア<font color="#FF0000">※</font><br />
  <font color="#FF0000"><!--{$arrErr.serviceOptionType}--></font>
  <!--{foreach from=$arrCvsShop key=key item=shopName}-->
  <input type="radio" name="serviceOptionType" value="<!--{$key}-->"
    <!--{if $arrForm.serviceOptionType.value == $key}-->
    checked="checked"<!--{/if}--> />
    <!--{$shopName|h}--><br />
  <!--{/foreach}-->

  <p>お支払いのため以下の情報がご利用になるストアへ送信されます。</p>
  ■ お客様の氏名<br />
  <!--{$arrOrder.order_kana01}--> <!--{$arrOrder.order_kana02}--><br/>
  ■ お客様の電話番号<br />
  <!--{$arrOrder.order_tel01}-->-<!--{$arrOrder.order_tel02}-->-<!--{$arrOrder.order_tel03}--><br />
  <br />

  <div align="center"><input type="submit" value="選択したコンビニで決済" /></div>
</form>

<form method="post" action="<!--{$smarty.server.PHP_SELF|h}-->">
  <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
  <input type="hidden" name="mode" value="back" />
  <div align="center"><input type="submit" value="戻る" /></div>
</form>
<!--▲CONTENTS-->
