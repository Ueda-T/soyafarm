<!--{*
 * edit_payment.tpl - 受注管理編集 決済情報インクルード
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: admin_config.tpl
 * @link        http://www.veritrans.co.jp/3gps
 *}-->
        <!--▼ Veritrans 3G Module payment -->
        <!--▼ Veritrans 3G Module payment_input -->
        <!--{if ($objSbivt->isChangePayment() || $objSbivt->isChangeTotal())
        && $objSbivt->isDstSbiPayment() && $arrErr[$key] == ''}-->
        <tr>
            <th>お支払方法 変更時入力</th>
            <td>
        <!--{include file=$smarty.const.MDL_SBIVT3G_TPL_PATH|cat:"admin/order/edit_payment_input.tpl"}-->
            </td>
        </tr>
        <!--{/if}-->
        <!--▲ Veritrans 3G Module payment_input -->
        <!--{assign var=key value="memo01"}-->
        <tr>
            <th>
              3G決済ステータス変更
              <!--{if $smarty.const.MDL_SBIVT3G_STATUS_REFRESH_ENABLED}-->
              <!--{if $objSbivt->isSrcSbiPayment()}-->
              <a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('sbivt3gRefresh','',''); return false;">最新の状態を取得</a>
              <!--{/if}-->
              <!--{/if}-->
            </th>
            <td>
              <!--{if $objSbivt->isSrcSbiPayment()}-->

                <span class="attention"><!--{$arrErr[$key]}--></span>

                <!--{assign var="src_payment_id" value=$objSbivt->getSrcPaymentId()}-->
                <!--{$arrPayment[$src_payment_id]}--> /
                <!--{$objSbivt->getSrcPayStatusName()}-->
                <!--{if $objSbivt->arrEnableStatus|@count > 0}-->
                →
                <select name="<!--{$key}-->"
                  style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                  <option value="">変更しない</option>
                    <!--{html_options options=$objSbivt->arrEnableStatus
                      selected=$arrForm[$key].value}-->
                </select>
                <!--{/if}-->

              <!--{else}-->
                未設定
              <!--{/if}-->
            </td>
        </tr>
        <!--{assign var=key value="memo03"}-->
        <!--{if $arrForm[$key].value != ''}-->
        <tr>
            <th>3G決済変更ログ</th>
            <td>
              <!--{$arrForm[$key].value|nl2br}-->
            </td>
        </tr>
        <!--{/if}-->
        <!--{assign var=key value="memo08"}-->
        <!--{if $arrForm[$key].value != ''}-->
        <tr>
            <th>返金用URL</th>
            <td>
              <!--{$arrForm[$key].value|h}-->
            </td>
        </tr>
        <!--{/if}-->
        <!--▲ Veritrans 3G Module payment -->
