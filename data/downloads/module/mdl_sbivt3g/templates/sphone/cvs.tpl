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
<!--{include file=$smarty.const.MDL_SBIVT3G_TPL_PATH|cat:"sphone/css/sbivt3g.css.tpl"}-->
<!--{include file=$smarty.const.MDL_SBIVT3G_TPL_PATH|cat:"sphone/js/sbivt3g.js.tpl"}-->

<div id="under02column">
  <div id="under02column_shopping" class="sbivt3g">
    <h2 class="title"><!--{$arrOrder.payment_method}--></h2>

    <form name="frmSbi" id="frmSbi" method="post" action="<!--{$smarty.server.PHP_SELF|h}-->">
      <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
      <input type="hidden" name="mode" value="exec" />
      <p>
        ご利用になるコンビニエンスストアを選択して下さい。<br />
        注文後<!--{$objSetting->get('V_limitDays')}-->日以内にお支払いいただけない場合、ご注文を取消とさせていただくことがありますのでご了承下さい。
      </p>
      <!--{if is_array($arrRes) == true}-->
      <div class="attention">
      <!--{if $arrRes.isOK == false}-->
        [<!--{$arrRes.vResultCode}-->]<!--{$arrRes.mErrMsg}-->
      <!--{/if}-->
      </div>
      <!--{/if}-->
      <span class="attention"><!--{$arrErr.serviceOptionType}--></span>
      <table summary="コンビニ店舗選択">
        <!--{foreach from=$arrCvsShop key=key item=shopName}-->
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
              <!--{$shopName|h}-->
            </label>
          </td>
        </tr>
        <!--{/foreach}-->
      </table>
      <p>お支払いのため以下の情報がご利用になるストアへ送信されます。</p>
      <table summary="注文者情報">
        <tr>
          <th>お客様の氏名</th>
          <td><!--{$arrOrder.order_kana01}--> <!--{$arrOrder.order_kana02}--></td>
        </tr>
        <tr>
          <th>お客様の電話番号</th>
          <td><!--{$arrOrder.order_tel01}-->-<!--{$arrOrder.order_tel02}-->-<!--{$arrOrder.order_tel03}--></td>
        </tr>
      </table>
      <div class="tblareabtn">
        <p><input data-role="none" type="button" id="btnExec" class="spbtn spbtn-shopping" value="選択したコンビニで決済" /></p>
        <p><input data-role="none" type="button" id="btnBack" class="spbtn spbtn-medeum" value="戻る" /></p>
      </div>
    </form>
  </div>
</div>
<!--▲CONTENTS-->
