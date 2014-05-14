<!--{*
 * netbank.tpl - 銀行決済(ネットバンキング)入力画面
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: netbank.tpl 15 2011-07-28 03:41:11Z hira $
 * @link        http://www.veritrans.co.jp/3gps
 *}-->
<!--▼CONTENTS-->
<!--{include file=$smarty.const.MDL_SBIVT3G_TPL_PATH|cat:"default/css/sbivt3g.css.tpl"}-->
<!--{include file=$smarty.const.MDL_SBIVT3G_TPL_PATH|cat:"default/js/sbivt3g.js.tpl"}-->

<div id="under02column">
  <div id="under02column_shopping" class="sbivt3g">
    <h2 class="title">お支払い方法：<!--{$arrOrder.payment_method}--></h2>

    <form name="frmSbi" id="frmSbi" method="post" action="<!--{$smarty.server.PHP_SELF|h}-->">
      <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
      <input type="hidden" name="mode" value="exec" />
      <p>
        ご利用の金融機関を選択して下さい。<br />
        「指定の金融機関で決済」をクリックすると、各金融機関の入金画面へ遷移します。
      </p>
      <!--{if is_array($arrRes) == true}-->
      <div class="attention">
      <!--{if $arrRes.isOK == false}-->
        [<!--{$arrRes.vResultCode}-->]<!--{$arrRes.mErrMsg}-->
      <!--{/if}-->
      </div>
      <!--{/if}-->
      <table summary="金融機関選択">
        <tr>
          <th>金融機関<span class="attention">※</span></th>
          <td>
            <span class="attention"><!--{$arrErr.payCsv}--></span>
            <select name="payCsv" id="payCsv"
              style="<!--{$arrErr.payCsv|sfGetErrorColor}-->">
              <option value="">--選択してください--</option>
              <!--{html_options options=$arrBanks selected=$arrForm.payCsv}-->
            </select>
          </td>
        </tr>
      </table>
      <div align="center">
        <input type="button" id="btnBack" class="btnNormal" value="戻　る" />
        <input type="button" id="btnExec" class="btnNormal" value="選択した金融機関で決済" />
      </div>
    </form>
  </div>
</div>
<!--▲CONTENTS-->
