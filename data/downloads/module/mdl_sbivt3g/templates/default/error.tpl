<!--{*
 * error.tpl - 共通エラー画面
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: error.tpl 15 2011-07-28 03:41:11Z hira $
 * @link        http://www.veritrans.co.jp/3gps
 *}-->
<!--▼CONTENTS-->
<!--{include file=$smarty.const.MDL_SBIVT3G_TPL_PATH|cat:"default/css/sbivt3g.css.tpl"}-->
<!--{include file=$smarty.const.MDL_SBIVT3G_TPL_PATH|cat:"default/js/sbivt3g.js.tpl"}-->

<div id="under02column">
  <div id="under02column_shopping" class="sbivt3g">
    <h2 class="title">お支払い方法：<!--{$arrOrder.payment_method}--></h2>

    <form name="frmSbi" id="frmSbi" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
      <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
      <input type="hidden" name="mode" value="back" />
      <!--{if is_array($arrRes) == true}-->
      <div class="attention">
      <!--{if $arrRes.isOK == false}-->
        [<!--{$arrRes.vResultCode}-->]<!--{$arrRes.mErrMsg}-->
      <!--{/if}-->
      </div>
      <!--{/if}-->
      <div align="center">
        <input type="button" id="btnBack" class="btnNormal" value="戻　る" />
      </div>
    </form>
  </div>
</div>
<!--▲CONTENTS-->
