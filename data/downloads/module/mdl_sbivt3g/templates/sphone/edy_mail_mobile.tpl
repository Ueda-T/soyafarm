<!--{*
 * edy_mail_mobile.tpl - モバイルEdy決済(メール型)入力画面
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: edy_mail_mobile.tpl 61 2011-08-23 11:20:05Z hira $
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
      <p>お支払いにご利用になる携帯電話のメールアドレスを入力して下さい。</p>
      <!--{if is_array($arrRes) == true}-->
      <div class="attention">
      <!--{if $arrRes.isOK == false}-->
        [<!--{$arrRes.vResultCode}-->]<!--{$arrRes.mErrMsg}-->
      <!--{/if}-->
      </div>
      <!--{/if}-->
      <table summary="モバイルEdy決済入力">
        <tr>
          <th>
            携帯メールアドレス
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
      <p class="attention">「入力したアドレスへ送信」のボタンを押す前にお使いの携帯電話のメールフィルタ設定をご確認下さい。<br/>設定によってはお支払い方法についてのメールが受信できない場合があります。</p>
      <div class="tblareabtn">
        <p><input data-role="none" type="button" id="btnExec" class="spbtn spbtn-shopping" value="入力したアドレスへ送信" /></p>
        <p><input data-role="none" type="button" id="btnBack" class="spbtn spbtn-medeum" value="戻る" /></p>
      </div>
    </form>
  </div>
</div>
<!--▲CONTENTS-->
