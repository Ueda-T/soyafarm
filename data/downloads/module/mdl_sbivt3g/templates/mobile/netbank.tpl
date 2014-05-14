<!--{*
 * netbank.tpl - 銀行決済(ネットバンキング)入力画面
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: netbank.tpl 14 2011-07-27 11:24:43Z hira $
 * @link        http://www.veritrans.co.jp/3gps
 *}-->
<!--▼CONTENTS-->

<form method="post" action="<!--{$smarty.server.PHP_SELF|h}-->">
  <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
  <input type="hidden" name="mode" value="exec" />
  <p>
    ご利用の金融機関を選択して下さい。<br />
    「指定の金融機関で決済」をクリックすると、注文完了画面に遷移して各金融機関の入金画面へ遷移するボタンが表示されます。
  </p>
  <!--{if is_array($arrRes) == true}-->
  <font color="#FF0000">
  <!--{if $arrRes.isOK == false}-->
    [<!--{$arrRes.vResultCode}-->]<!--{$arrRes.mErrMsg}-->
  <!--{/if}-->
  </font><br />
  <!--{/if}-->

  ■ 金融機関<font color="#FF0000">※</font><br />
  <font color="#FF0000"><!--{$arrErr.payCsv}--></font>
  <select name="payCsv">
    <option value="">--選択してください--</option>
    <!--{html_options options=$arrBanks selected=$arrForm.payCsv}-->
  </select><br />
  <br />

  <div align="center"><input type="submit" value="選択した金融機関で決済" /></div>
</form>

<form method="post" action="<!--{$smarty.server.PHP_SELF|h}-->">
  <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
  <input type="hidden" name="mode" value="back" />
  <div align="center"><input type="submit" value="戻る" /></div>
</form>
<!--▲CONTENTS-->
