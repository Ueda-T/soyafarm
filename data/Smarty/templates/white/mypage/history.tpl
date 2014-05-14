<!--▼CONTENTS-->
<p class="pankuzu">
	<a href="<!--{$smarty.const.ROOT_URLPATH}-->"><!--{$smarty.const.TPL_PC_HOME_NAME}--></a>
	&nbsp;&gt;&nbsp;
	<a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/"><!--{$tpl_title}--></a>
	&nbsp;&gt;&nbsp;
	<a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/history_list.php">ご注文履歴</a>
	&nbsp;&gt;&nbsp;
	<!--{$tpl_subtitle}-->
</p>

<div id="mainMyPage">
	<h1><img src="<!--{$TPL_URLPATH}-->img/rohto/mypage_title_sub3detail.gif" width="700" height="70" alt="ご注文内容詳細"></h1>

	<!--{if !$tpl_disable_logout}-->
	<form name="header_login_form" id="header_login_form" method="post" action="<!--{$smarty.const.ROOT_URLPATH}-->frontparts/login_check.php" onsubmit="return fnCheckLogin('header_login_form')">
		<input type="hidden" name="mode" value="login" />
		<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
		<input type="hidden" name="url" value="<!--{$smarty.server.REQUEST_URI|h}-->" />
		<p class="logout">
			<a href="javascript:void(0);" onclick="fnFormModeSubmit('header_login_form', 'logout', '', ''); return false;"><img src="<!--{$TPL_URLPATH}-->img/rohto/logout.gif" alt="ログアウト" class="swp" /></a>
		</p>
	</form>
	<!--{/if}-->
	<p class="intro">ご注文内容の詳細をご確認ください。</p>


    <div class="wrapForm">
    <table summary="お届け先" class="delivname typ2">
                <colgroup width="20%"></colgroup>
                <colgroup width="30%"></colgroup>
                <colgroup width="20%"></colgroup>
                <colgroup width="30%"></colgroup>
                <tr>
                    <th class="alignL">オーダー番号</th>
                    <td><!--{$tpl_arrOrderData.order_id}--></td>
                    <th class="alignL">ご注文日</th>
                    <td><!--{$tpl_arrOrderData.create_date|date_format:"%Y年%m月%d日"|h}--></td>
                </tr>
                <tr>
                    <th class="alignL">処理状況</th>
                    <td colspan="3">
<!--{assign var=i value=$tpl_arrOrderData.status}-->
<!--{if $arrOrderStatus[$i].image_l}--><img src="<!--{$TPL_URLPATH}-->img/rohto/<!--{$arrOrderStatus[$i].image_l}-->" /><!--{else}--><!--{$arrOrderStatus[$i].name}--><!--{/if}-->
</td>
                </tr>
                <tr>
                    <th>送り主</th>
                    <td>
                        〒<!--{$tpl_arrOrderData.order_zip|h}--><br />
                        <!--{$arrPref[$tpl_arrOrderData.order_pref]}--><!--{$tpl_arrOrderData.order_addr01}--><br />
                        <!--{$tpl_arrOrderData.order_addr02}--><br /><br />
                        <!--{$tpl_arrOrderData.order_name}-->&nbsp;様<br />
                        TEL:<!--{$tpl_arrOrderData.order_tel}-->
                    </td>
                    <th>ご購入金額</th>
                    <td>
                        商品金額(税込)：<!--{$tpl_arrOrderData.subtotal|number_format}-->円<br />
                        送料(税込)：<!--{assign var=key value="deliv_fee"}--><!--{$tpl_arrOrderData[$key]|number_format|h}-->円<br />
                        使用ポイント：<!--{assign var=key value="use_point"}--><!--{$tpl_arrOrderData[$key]|number_format|h}-->pt<br />
                        合計金額(税込)：<!--{$tpl_arrOrderData.payment_total|number_format}-->円
                    </td>
                </tr>
                <tr>
                    <th>お支払方法</th>
                    <td><!--{$arrPayment[$tpl_arrOrderData.payment_id]|h}--></td>
                    <th>出荷日</th>
                    <td><!--{$tpl_arrOrderData.commit_date|date_format:"%Y年%m月%d日"|h}--></td>
                </tr>
                <tr>
                    <th>配送方法</th>
                    <td><!--{$arrDelivBox[$tpl_arrOrderData.deliv_box_id]|h}--></td>
                    <th>請求書送付方法</th>
                    <td><!--{$arrIncludeKbn[$tpl_arrOrderData.include_kbn]|h}--></td>
                </tr>
            </table>

        <div class="mycondition_area clearfix">
            <form action="order.php" name="reorder_form" id="reorder_form" method="post">
                <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
                <!--{*
                <p class="btn">
                    <input type="hidden" name="order_id" value="<!--{$tpl_arrOrderData.order_id|h}-->">
                    <a href="javascript:void(0);" onclick="document.reorder_form.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/button/btn_order_re.jpg" alt="この購入内容で再注文する" name="submit" value="この購入内容で再注文する" /></a>
                </p>
                *}-->
            </form>
        </div>

<!--{*
        <!-- 使用ポイントここから -->
        <!--{if $smarty.const.USE_POINT !== false}-->
            <table summary="使用ポイント">
                <colgroup width="30%"></colgroup>
                <colgroup width="70%"></colgroup>
                <tr>
                    <th class="alignL">ご使用ポイント</th>
                    <td><!--{assign var=key value="use_point1"}--><!--{$tpl_arrOrderData[$key]|number_format|default:0}--> pt</td>
                </tr>
                <tr>
                    <th class="alignL">ご使用お誕生日ポイント</th>
                    <td><!--{assign var=key value="use_point2"}--><!--{$tpl_arrOrderData[$key]|number_format|default:0}--> pt</td>
                </tr>
                <tr>
                    <th class="alignL">今回加算されるポイント</th>
                    <td><!--{$tpl_arrOrderData.add_point|number_format|default:0}--> pt</td>
                </tr>
            </table>
        <!--{/if}-->
        <!-- 使用ポイントここまで -->
*}-->

        <!--{foreach item=shippingItem name=shippingItem from=$arrShipping}-->
            <h2 class="bsc">お届け先<!--{if $isMultiple}--><!--{$smarty.foreach.shippingItem.iteration}--><!--{/if}--></h2>

	    <div class="wrapForm">
            <table summary="お届け先" class="delivname typ2">
                    <colgroup width="12%"></colgroup>
                    <colgroup width="15%"></colgroup>
                    <colgroup width="73%"></colgroup>
                    <tr>
                        <th class="alignL" colspan="2">お届け先ご住所</th>
                        <td>〒<!--{$shippingItem.shipping_zip}-->&nbsp;<!--{$arrPref[$shippingItem.shipping_pref]}--><!--{$shippingItem.shipping_addr01|h}--><!--{$shippingItem.shipping_addr02|h}--></td>
                    </tr>
                    <tr>
                        <th class="alignL" colspan="2">お届け先氏名</th>
                        <td><!--{$shippingItem.shipping_name|h}-->&nbsp;様</td>
                    </tr>
                    <tr>
                        <th class="alignL" colspan="2">お届け先電話番号</th>
                        <td><!--{$shippingItem.shipping_tel}--></td>
                    </tr>
                    <tr>
                        <th rowspan="3">お届け方法</th>
                        <th>お届け希望日</th>
                        <td><!--{$shippingItem.shipping_date|default:'指定なし'|h}--></td>
                    </tr>
                    <tr>
                        <th>時間指定</th>
                        <td><!--{$shippingItem.shipping_time|default:'指定なし'|h}--></td>
                    </tr>
                    <tr>
                        <th>配達時のご要望</th>
                        <td><!--{$tpl_arrOrderData.note|default:'指定なし'|h}--></td>
                    </tr>
                    <tr>
                        <th colspan="2">伝票番号</th>
                        <td><!--{$shippingItem.shipping_num|default:''|h}--></td>
                    </tr>
                    <tr>
                        <th colspan="2">荷物問合せURL</th>
                        <td><a href="<!--{$shippingItem.confirm_url|default:''|h}-->" target="_blank"><!--{$shippingItem.confirm_url|default:''|h}--></a></td>
                    </tr>
                </tbody>
            </table>
		</div>
        <!--{/foreach}-->

        <h2 class="bsc">ご注文商品</h2>
		<div class="cartList">
        <table summary="購入商品詳細">
            <colgroup width="50%"></colgroup>
            <colgroup width="10%"></colgroup>
            <colgroup width="20%"></colgroup>
            <colgroup width="30%"></colgroup>
            <tr>
                <th class="alignC">商品名</th>
                <th class="alignC">数量</th>
                <th class="alignC">金額(税込)</th>
                <th class="alignC">お届け間隔</th>
            </tr>
            <!--{foreach from=$tpl_arrOrderDetail item=orderDetail}-->
                <tr>
                    <td>
                        <!--{if $orderDetail.product_valid_flg == 1}-->
                        <a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$orderDetail.product_id|u}-->"><!--{$orderDetail.product_name|h}-->
                        <!--{else}-->
                        <!--{$orderDetail.product_name|h}-->
                        <!--{/if}-->

                        <!--{if $orderDetail.classcategory_name1 != ""}--><br />
                            <!--{$orderDetail.classcategory_name1}-->
                        <!--{/if}-->
                        <!--{if $orderDetail.classcategory_name2 != ""}-->
                            <!--{$orderDetail.classcategory_name2}-->
                        <!--{/if}-->
                        <!--{if $orderDetail.product_valid_flg == 1}-->
                        </a>
                        <!--{/if}-->
                    </td>
                    <!--{assign var=quantity value=`$orderDetail.quantity`}-->
                    <!--{assign var=price value=`$orderDetail.price`}-->
                    <td class="alignR"><!--{$quantity|h}--></td>
                    <td class="alignR"><!--{$price|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format|h}-->円</td>
                    <td class="alignC">
                        <!--{assign var=key1 value=`$orderDetail.course_cd`}-->
                        <!--{if $key1 > 0 && $key1 <= 3}-->
                            <!--{$key1|h}-->ヶ月ごと
                        <!--{elseif $key1 >= 20 && $key1 <= 90}-->
                            <!--{$key1|h}-->日ごと
                        <!--{/if}-->

                    </td>
                </tr>
            <!--{/foreach}-->
        </table>
        </div>

<!--{*
        <h2 class="bsc">メール配信履歴一覧</h2>
        <table>
            <tr>
                <th class="alignC">処理日</th>
                <th class="alignC">通知メール</th>
                <th class="alignC">件名</th>
            </tr>
            <!--{section name=cnt loop=$tpl_arrMailHistory}-->
            <tr class="center">
                <td class="alignC"><!--{$tpl_arrMailHistory[cnt].send_date|sfDispDBDate|h}--></td>
                <!--{assign var=key value="`$tpl_arrMailHistory[cnt].template_id`"}-->
                <td class="alignC"><!--{$arrMAILTEMPLATE[$key]|h}--></td>
                <td><a href="#" onclick="win02('./mail_view.php?send_id=<!--{$tpl_arrMailHistory[cnt].send_id}-->','mail_view','650','800'); return false;"><!--{$tpl_arrMailHistory[cnt].subject|h}--></a></td>
            </tr>
            <!--{/section}-->
        </table>
*}-->
        <!-- お問い合わせ -->
        <p class="btn">
            <a href="<!--{$smarty.const.ROOT_URLPATH}-->contact/"><img src="<!--{$TPL_URLPATH}-->img/rohto/contact_pastorder.gif" alt="このご注文へのお問い合わせ" class="swp" /></a>
        </p>

<!--{*
        <div class="btn_area">
            <ul>
                <li>
                    <a href="./<!--{$smarty.const.DIR_INDEX_PATH}-->history_list.php" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_back_on.jpg','change');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_back.jpg','change');"><img src="<!--{$TPL_URLPATH}-->img/button/btn_back.jpg" alt="戻る" name="change" id="change" /></a>
                </li>
            </ul>
        </div>
*}-->

    </div>
</div>
<!--▲CONTENTS-->
