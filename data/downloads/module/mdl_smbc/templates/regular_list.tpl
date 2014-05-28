<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
*}-->
<script type="text/javascript">
$(function() {
    doCancel = function(shoporder_no, order_id) {
        $.ajax({
            type: 'POST',
            cache: false,
            url: "<!--{$smarty.server.REQUEST_URI|h}-->",
            data: {
                mode: 'cancel',
                    <!--{$smarty.const.TRANSACTION_ID_NAME}-->: '<!--{$transactionid}-->',
                    shoporder_no: shoporder_no,
                    order_id: order_id
                },
            dataType: 'json',
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                window.alert("エラーが発生しました\n" + textStatus);
            },
            success: function(data, textStatus, jqXHR) {
                window.alert(data.header.res);
                window.location.href = '<!--{$smarty.server.REQUEST_URI|h}-->';
                return false;
            }
        });
        return false;
    };
});
</script>
<div id="mypagecolumn">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <!--{if $tpl_navi != ""}-->
        <!--{include file=$tpl_navi}-->
    <!--{else}-->
        <!--{include file=`$smarty.const.TEMPLATE_REALDIR`mypage/navi.tpl}-->
    <!--{/if}-->
    <div id="mycontents_area">
        <form name="form1" id="form1" method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="order_id" value="" />
            <input type="hidden" name="pageno" value="<!--{$objNavi->nowpage}-->" />
            <input type="hidden" name="mode" value="regular_order" />
            <h3><!--{$tpl_subtitle|h}--></h3>

            <!--{if $objNavi->all_row > 0}-->

                <p><span class="attention"><!--{$objNavi->all_row}-->件</span>の購入履歴があります。</p>
                <div class="pagenumber_area">
                    <!--▼ページナビ-->
                    <!--{$objNavi->strnavi}-->
                    <!--▲ページナビ-->
                </div>

                <table summary="購入履歴">
                    <tr>
                        <th class="alignC">購入日時</th>

                        <th class="alignC" rowspan="2" >商品名</th>
                        <th class="alignC">合計金額</th>
                        <th class="alignC" rowspan="2">再申込み</th>

                    </tr>
                    <tr>
                      <th class="alignC">注文番号</th>
                        <!--{if $smarty.const.MYPAGE_ORDER_STATUS_DISP_FLAG }-->
                          <th class="alignC">ご注文状況</th>
                        <!--{/if}-->
<!--
			   <th class="alignC">キャンセル</th>
-->
                    </tr>
                    <!--{section name=cnt loop=$arrOrder}-->
                        <tr>
                            <td class="alignC"><!--{$arrOrder[cnt].create_date|sfDispDBDate}--></td>

                            <!--{assign var=payment_id value="`$arrOrder[cnt].payment_id`"}-->
                            <td rowspan="2">
                                <!--{foreach item=orderDetail from=$arrOrder[cnt].products}-->
                                <!--{$orderDetail.product_name|h}-->
                                    <!--{if $orderDetail.classcategory_name1 != ""}-->
                                        / <!--{$orderDetail.classcategory_name1|h}-->
                                    <!--{/if}-->
                                    <!--{if $orderDetail.classcategory_name2 != ""}-->
                                        / <!--{$orderDetail.classcategory_name2|h}-->
                                    <!--{/if}-->
                                    <br />
                                <!--{/foreach}-->

                            </td>
                            <td class="alignR"><!--{$arrOrder[cnt].payment_total|number_format}-->円</td>
                            <!--{assign var=order_status_id value="`$arrOrder[cnt].status`"}-->
                            <!--{if $order_status_id == $smarty.const.ORDER_CANCEL }-->
                            <td class="alignC"  rowspan="2"><a href="#" onclick="fnModeSubmit('regular_order', 'order_id', '<!--{$arrOrder[cnt].order_id|h}-->'); return false;">再申込み</a></td>
                            <!--{else}-->
                            <td class="alignC"  rowspan="2">-</td>
                            <!--{/if}-->
                        </tr>
                        <tr>
                            <td class="alignC"><!--{$arrOrder[cnt].shoporder_no|h}--></td>
                            <!--{if $smarty.const.MYPAGE_ORDER_STATUS_DISP_FLAG }-->
                                <!--{assign var=order_status_id value="`$arrOrder[cnt].status`"}-->
                                <!--{if $order_status_id != $smarty.const.ORDER_PENDING }-->
                                <td class="alignC"><!--{$arrCustomerOrderStatus[$order_status_id]|h}--></td>
                                <!--{else}-->
                                <td class="alignC attention"><!--{$arrCustomerOrderStatus[$order_status_id]|h}--></td>
                                <!--{/if}-->
                            <!--{/if}-->
<!--
                            <!--{if $order_status_id != $smarty.const.ORDER_CANCEL }-->
                            <td class="alignC"><a href="javascript:;" onclick="doCancel('<!--{$arrOrder[cnt].shoporder_no|h}-->', '<!--{$arrOrder[cnt].order_id|h}-->'); return false;">キャンセル</a></td>
                            <!--{else}-->
                            <td class="alignC">-</td>
                            <!--{/if}-->
-->
                        </tr>
                    <!--{/section}-->
                </table>

            <!--{else}-->
                <p>購入履歴はありません。</p>
            <!--{/if}-->
        </form>
    </div>
</div>
