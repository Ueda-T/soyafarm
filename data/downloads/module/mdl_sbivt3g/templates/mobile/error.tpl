<!--{*
 * error.tpl - 共通エラー画面
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: error.tpl 14 2011-07-27 11:24:43Z hira $
 * @link        http://www.veritrans.co.jp/3gps
 *}-->
<!--▼CONTENTS-->

<form method="post" action="<!--{$smarty.server.PHP_SELF|h}-->">
  <h2>決済エラーが発生しました</h2>
  <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
  <input type="hidden" name="mode" value="back" />
  <!--{if is_array($arrRes) == true}-->
  <font color="#FF0000">
  <!--{if $arrRes.isOK == false}-->
    [<!--{$arrRes.vResultCode}-->]<!--{$arrRes.mErrMsg}-->
  <!--{/if}-->
  </font><br />
  <!--{/if}-->

  <div align="center"><input type="submit" value="戻る" /></div>
</form>
<!--▲CONTENTS-->
