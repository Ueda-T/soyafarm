<!--{*
 * edit_payment.tpl - 受注管理表示 決済情報インクルード
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
            <!--▼ SBI Veritrans 3G Module payment -->
            <!--{if $arrForm.memo01.value != ''}-->
            <tr>
                <th>3G決済ステータス変更</th>
                <td>
                  <!--{$objSbivt->arrPayStatus[$arrForm.memo01.value]|h}-->
                </td>
            </tr>
            <!--{/if}-->
            <!--{if $objSbivt->arrSrcOrder.memo03 != ''}-->
            <tr>
                <th>3G決済変更ログ</th>
                <td>
                  <!--{$objSbivt->arrSrcOrder.memo03|nl2br}-->
                </td>
            </tr>
            <!--{/if}-->
            <!--{if $objSbivt->arrSrcOrder.memo08 != ''}-->
            <tr>
                <th>返金用URL</th>
                <td>
                  <!--{$objSbivt->arrSrcOrder.memo08|h}-->
                </td>
            </tr>
            <!--{/if}-->
            <!--▲ SBI Veritrans 3G Module payment -->
