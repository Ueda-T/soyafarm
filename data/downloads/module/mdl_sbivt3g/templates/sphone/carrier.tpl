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
<!--{include file=$smarty.const.MDL_SBIVT3G_TPL_PATH|cat:"sphone/css/sbivt3g.css.tpl"}-->
<!--{include file=$smarty.const.MDL_SBIVT3G_TPL_PATH|cat:"sphone/js/sbivt3g.js.tpl"}-->

<div id="under02column">
  <div id="under02column_shopping" class="sbivt3g">
    <h2 class="title"><!--{$arrOrder.payment_method}--></h2>

    <form name="frmSbi" id="frmSbi" method="post" action="<!--{$smarty.server.PHP_SELF|h}-->">
      <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
      <input type="hidden" name="mode" value="exec" />
      <p>
        ご利用になるお支払い方法を選択して下さい。
      </p>
      <!--{if is_array($arrRes) == true}-->
      <div class="attention">
      <!--{if $arrRes.isOK == false}-->
        [<!--{$arrRes.vResultCode}-->]<!--{$arrRes.mErrMsg}-->
      <!--{/if}-->
      </div>
      <!--{/if}-->
      <span class="attention"><!--{$arrErr.serviceOptionType}--></span>
      <table summary="キャリア選択">
        <!--{foreach from=$arrCarrierServices key=key item=carrierName}-->
        <tr>
          <td class="selectArea">
            <input type="radio" name="serviceOptionType"
              id="serviceOptionType_<!--{$key}-->" value="<!--{$key}-->"
              <!--{if $arrForm.serviceOptionType.value == $key}-->
              checked="checked"<!--{/if}--> />
          </td>
          <td>
            <label for="serviceOptionType_<!--{$key}-->"
              style="<!--{$arrErr.serviceOptionType|sfGetErrorColor}-->">
              <!--{$carrierName|h}-->
            </label>
          </td>
        </tr>
        <!--{/foreach}-->
      </table>
      <div class="tblareabtn">
        <p><input data-role="none" type="button" id="btnExec" class="spbtn spbtn-shopping" value="選択したキャリアで決済" /></p>
        <p><input data-role="none" type="button" id="btnBack" class="spbtn spbtn-medeum" value="戻る" /></p>
      </div>
    </form>
  </div>
</div>
<!--▲CONTENTS-->
