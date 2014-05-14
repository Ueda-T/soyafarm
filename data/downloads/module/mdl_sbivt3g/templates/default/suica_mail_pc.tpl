<!--{*
 * suica_mail_pc.tpl - Suica Internet Service決済(メール型)入力画面
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: suica_mail_pc.tpl 15 2011-07-28 03:41:11Z hira $
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
      <input type="hidden" name="mode" value="back" />
      <p>お支払いにご利用になるPCのメールアドレスを入力して下さい。</p>
      <!--{if is_array($arrRes) == true}-->
      <div class="attention">
      <!--{if $arrRes.isOK == false}-->
        [<!--{$arrRes.vResultCode}-->]<!--{$arrRes.mErrMsg}-->
      <!--{/if}-->
      </div>
      <!--{/if}-->
      <table summary="Suica Internet Service 決済入力">
        <tr>
          <th>
            PCメールアドレス
            <span class="attention">※</span>
          </th>
          <td>
            <span class="attention"><!--{$arrErr.mailAddr}--></span>
            <input type="text" name="mailAddr" id="mailAddr" 
              class="box240"maxlength="<!--{$arrForm.mailAddr.length}-->"
              value="<!--{$arrForm.mailAddr.value|h}-->"
              style="ime-mode:disabled; <!--{$arrErr.mailAddr|sfGetErrorColor}-->"/>
          </td>
        </tr>
      </table>
      <p>入力いただいたメールアドレスへお支払い方法についてお知らせするメールをお送りいたします。</p>
      <p class="attention">「入力したアドレスへ送信」のボタンを押す前にお使いのPCメーラーのメールフィルタ設定をご確認下さい。<br/>設定によってはお支払い方法についてのメールが受信できない場合があります。</p>
      <div align="center">
        <input type="button" id="btnBack" class="btnNormal" value="戻　る" />
        <input type="button" id="btnExec" class="btnNormal" value="入力したアドレスへ送信" />
      </div>
    </form>
  </div>
</div>
<!--▲CONTENTS-->
