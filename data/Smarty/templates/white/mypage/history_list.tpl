<!--▼CONTENTS-->
<!--{include file="`$smarty.const.TEMPLATE_REALDIR`mypage/pankuzu.tpl"}-->

<div id="mainMyPage">
	<h1><img src="<!--{$TPL_URLPATH}-->img/soyafarm/mypage_title_sub3.gif"  alt="ご注文履歴" /></h1>

	<!--{if !$tpl_disable_logout}-->
	<form name="header_login_form" id="header_login_form" method="post" action="<!--{$smarty.const.ROOT_URLPATH}-->frontparts/login_check.php" onsubmit="return fnCheckLogin('header_login_form')">
		<input type="hidden" name="mode" value="login" />
		<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
		<input type="hidden" name="url" value="<!--{$smarty.server.REQUEST_URI|h}-->" />
		<p class="logout">
			<a href="javascript:void(0);" onclick="fnFormModeSubmit('header_login_form', 'logout', '', ''); return false;"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/logout.gif" alt="ログアウト" class="swp" /></a>
		</p>
	</form>
	<!--{/if}-->

	<ul class="styleDisc" style="margin:20px 0;">
		<li>「注文番号」をクリックすると、そのご注文の詳細を確認することができます。</li>
		<li>「商品名」の<input type="checkbox" name="" value=""> にチェックを付けて「チェックを付けた商品を購入する」ボタンを押すと、同じ商品を購入することができます。</li>
	</ul>

		<div class="cartList">
			<form name="form1" method="post" action="?">
        		<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    			<input type="hidden" name="order_id" value="" />
    			<input type="hidden" name="post_flg" value="1" />
    			<input type="hidden" name="product_cnt" value="<!--{$arrOrderMs|@count}-->" />
    			<input type="hidden" name="pageno" value="<!--{$objNavi->nowpage}-->" />
    			<!--{if $objNavi->all_row > 0}-->
    				<!--{*<p><span class="attention"><!--{$objNavi->all_row}-->件</span>の購入履歴があります。</p>*}-->

    				<div class="pagenumber_area">
    				  <ul class="navi">
    					<!--▼ページナビ-->
    					<!--{$objNavi->strnavi}-->
    					<!--▲ページナビ-->
    				  </ul>
    				</div>

    				<table summary="購入履歴">
    					<tr>
    						<th class="alignC">注文日<br />注文番号</th>
    						<th class="alignC" nowrap>お支払い<br />金額合計</th>
<!--{*
    						<th class="alignC">お支払い方法</th>
*}-->
    						<th class="alignC">処理状況</th>
    						<th class="alignC">商品名</th>
    					</tr>
                        <!--{assign var=product_cnt value=0}-->
    					<!--{section name=cnt loop=$arrOrder}-->
    						<tr>
    							<td class="alignC" nowrap><!--{$arrOrder[cnt].create_date|date_format:"%Y年%m月%d日"}--><br />
    							<a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/history.php?order_id=<!--{$arrOrder[cnt].order_id}-->"><!--{$arrOrder[cnt].order_id}--></a></td>
    							<td class="alignR" nowrap><!--{$arrOrder[cnt].payment_total|number_format}-->円</td>
    							<!--{assign var=payment_id value="`$arrOrder[cnt].payment_id`"}-->
<!--{*
    							<td class="alignC"><!--{$arrPayment[$payment_id]|h}--></td>
*}-->
    							<td>
<!--{assign var=i value=$arrOrder[cnt].status}-->
<!--{$arrOrderStatus[$i].name}-->
</td>
    							<td>
                                    <!--{foreach item=arrOrderMsItem from=$arrOrderMs}-->
                                        <!--{if $arrOrder[cnt].order_id == $arrOrderMsItem.order_id }-->
                                            <!--{ * 販売可能商品のみリンク・チェックボックス表示 * }-->
                                            <!--{if $arrOrderMsItem.product_valid_flg == 1 }-->
                                                <input type="checkbox" name="chk_product_<!--{$product_cnt|h}-->" value="<!--{$arrOrderMsItem.product_class_id|h}-->" >
                                                <a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrOrderMsItem.product_id|u}-->"><!--{$arrOrderMsItem.product_name|h}--> <!--{if $arrOrderMsItem.classcategory_name1 != ""}--><!--{$arrOrderMsItem.classcategory_name1}--><!--{/if}--> <!--{if $arrOrderMsItem.classcategory_name2 != ""}--><!--{$arrOrderMsItem.classcategory_name2}--><!--{/if}--></a><br>
                                                <input type="hidden" name="course_cd_<!--{$product_cnt|h}-->" value="<!--{$arrOrderMsItem.course_cd|h}-->" >
                                            <!--{else}-->
                                                &nbsp;　　<!--{$arrOrderMsItem.product_name|h}--> <!--{if $arrOrderMsItem.classcategory_name1 != ""}--><!--{$arrOrderMsItem.classcategory_name1}--><!--{/if}--> <!--{if $arrOrderMsItem.classcategory_name2 != ""}--><!--{$arrOrderMsItem.classcategory_name2}--><!--{/if}--><br>
                                            <!--{/if}-->
                                            <!--{assign var=product_cnt value=$product_cnt+1}-->
                                        <!--{/if}-->
                                    <!--{/foreach}-->
                                </td>
    						</tr>
    					<!--{/section}-->
    				</table>
                    <p class="nakedC alignC" style="margin:30px auto;"><a href="javascript:void(0);" onclick="document.form1.submit();return false;" name="cart"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_cart.gif" alt="カートに入れる" class="swp" /></a></p>
    			<!--{else}-->
    				<p>購入履歴はありません。</p>
    			<!--{/if}-->
			</form>
		</div>
</div>
