<!--{*
 * suica_mail_mobile.tpl - モバイルSuica決済(メール型)入力画面
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: suica_mail_mobile.tpl 14 2011-07-27 11:24:43Z hira $
 * @link        http://www.veritrans.co.jp/3gps
 *}-->
<!--▼CONTENTS-->

<form method="post" action="<!--{$smarty.server.PHP_SELF|h}-->">
  <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
  <input type="hidden" name="mode" value="exec" />
  <p>お支払いにご利用になる携帯電話のメールアドレスを入力して下さい。</p>
  <!--{if is_array($arrRes) == true}-->
  <font color="#FF0000">
  <!--{if $arrRes.isOK == false}-->
    [<!--{$arrRes.vResultCode}-->]<!--{$arrRes.mErrMsg}-->
  <!--{/if}-->
  </font><br />
  <!--{/if}-->
  ■ 携帯メールアドレス<font color="#FF0000">※</font><br />
  <font color="#FF0000"><!--{$arrErr.mailAddr}--></font>
  <input type="text" name="mailAddr"
    maxlength="<!--{$arrForm.mailAddr.length}-->"
    value="<!--{$arrForm.mailAddr.value|h}-->" /><br />

  <p>
    入力いただいたメールアドレスへお支払い方法についてお知らせするメールをお送りいたします。<br />
  <font color="#FF0000">「入力したアドレスへ送信」のボタンを押す前にお使いの携帯電話のメールフィルタ設定をご確認下さい。<br/>設定によってはお支払い方法についてのメールが受信できない場合があります。</font>
  </p>

  <div align="center"><input type="submit" value="入力したアドレスへ送信" /></div>
</form>

<form method="post" action="<!--{$smarty.server.PHP_SELF|h}-->">
  <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
  <input type="hidden" name="mode" value="back" />
  <div align="center"><input type="submit" value="戻る" /></div>
</form>
<!--▲CONTENTS-->
