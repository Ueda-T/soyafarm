<link rel="stylesheet" href="<!--{$TPL_URLPATH}-->css/mypage_index.css" type="text/css" media="all" />

<!--▼CONTENTS-->
<!--{include file="`$smarty.const.TEMPLATE_REALDIR`mypage/pankuzu.tpl"}-->

<div id="mainMyPageTop">
	<!--{if $tpl_navi != ""}-->
		<!--{include file=$tpl_navi}-->
	<!--{else}-->
		<!--{include file=`$smarty.const.TEMPLATE_REALDIR`mypage/navi.tpl}-->
	<!--{/if}-->

	<h1><img src="<!--{$TPL_URLPATH}-->img/soyafarm/mypage_title_sub3.gif"  alt="購入履歴" /></h1>



	<ul class="styleDisc" style="margin:20px 0;">
		<li>「注文番号」をクリックすると、そのご注文の詳細を確認することができます。</li>
		<li>「商品名」の<input type="checkbox" name="" value=""> にチェックを付けて「チェックを付けた商品を購入する」ボタンを押すと、同じ商品を購入することができます。</li>
	</ul>

			<form name="form1" method="post" action="?">
        		<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    			<input type="hidden" name="order_id" value="" />
    			<input type="hidden" name="post_flg" value="1" />
    			<input type="hidden" name="product_cnt" value="<!--{$arrOrderMs|@count}-->" />
    			<input type="hidden" name="pageno" value="<!--{$objNavi->nowpage}-->" />
    			<!--{if $objNavi->all_row > 0}-->
    				<!--{*<p><span class="attention"><!--{$objNavi->all_row}-->件</span>の購入履歴があります。</p>*}-->

    				<!--{if $objNavi->strnavi}-->
    				<div class="pagenumber_area">
    				  <ul class="navi">
    					<!--▼ページナビ-->
    					<!--{$objNavi->strnavi}-->
    					<!--▲ページナビ-->
    				  </ul>
    				</div>
					<!--{/if}-->

    				<table summary="購入履歴" class="cart">
    					<tr>
    						<th><p>注文日・注文番号</p></th>
<!--{*
    						<th><p>処理状況</p></th>
*}-->
    						<th class="item-name"><p>商品名</p></th>
    						<th class="total-cost"><p>お支払い金額合計</p></th>
    					</tr>
                        <!--{assign var=product_cnt value=0}-->
    					<!--{section name=cnt loop=$arrOrder}-->
    						<tr>
    							<td class="alignC" nowrap><!--{$arrOrder[cnt].create_date|date_format:"%Y年%m月%d日"}--><br />
    							<a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/history.php?order_id=<!--{$arrOrder[cnt].order_id}-->"><!--{$arrOrder[cnt].order_id}--></a></td>
    							<!--{assign var=payment_id value="`$arrOrder[cnt].payment_id`"}-->
<!--{*
    							<td class="alignC"><!--{$arrPayment[$payment_id]|h}--></td>
    							<td>
<!--{assign var=i value=$arrOrder[cnt].status}-->
<!--{$arrOrderStatus[$i].name}-->
</td>
*}-->
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
    							<td nowrap class="alignC"><strong>￥<!--{$arrOrder[cnt].payment_total|number_format}--></strong></td>
    						</tr>
    					<!--{/section}-->
    				</table>
                    <p class="nakedC alignC" style="margin:30px auto;"><a href="javascript:void(0);" onclick="document.form1.submit();return false;" name="cart"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_cart.gif" alt="カートに入れる" class="swp" /></a></p>
    			<!--{else}-->
    				<p>購入履歴はありません。</p>
    			<!--{/if}-->
			</form>

<!--{*
	<div class="wrapCustomer">
		<div class="myPagePersonal">
			<div class="wrapSA">
				<p>▼ご登録住所以外への住所へ送付される場合等にご利用いただくことができます。<br />
				※最大<span class="attention"><!--{$smarty.const.DELIV_ADDR_MAX|h}-->件</span>までご登録いただけます。</p>

				<!--{if $tpl_linemax < $smarty.const.DELIV_ADDR_MAX}-->
					<!--{if $tpl_login}-->
						<p class="add_address">
							<a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php" onclick="win03('./delivery_addr.php','delivadd','730','680'); return false;" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_add_address_on.jpg','newadress');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_add_address.jpg','newadress');" target="_blank"><img src="<!--{$TPL_URLPATH}-->img/button/btn_add_address.jpg" alt="新しいお届け先を追加" border="0" name="newadress" /></a>
						</p>
					<!--{/if}-->
				<!--{/if}-->

				<!--{if $tpl_linemax > 0}-->
				<form name="form1" method="post" action="?" >
					<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
					<input type="hidden" name="mode" value="" />
					<input type="hidden" name="other_deliv_id" value="" />
					<input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->" />

					<div class="cartList">
					<table summary="お届け先">
					<colgroup width="5%"></colgroup>
					<colgroup width="75%"></colgroup>
					<colgroup width="10%"></colgroup>
					<colgroup width="10%"></colgroup>
						<th colspan="4">配送先住所</th>
						<!--{section name=cnt loop=$arrOtherDeliv}-->
							<!--{assign var=OtherPref value="`$arrOtherDeliv[cnt].pref`"}-->
							<tr>
								<td class="alignC"><!--{$smarty.section.cnt.iteration}--></td>
								<td>
									〒<!--{$arrOtherDeliv[cnt].zip}--><br />

									<!--{$arrPref[$OtherPref]|h}--><!--{$arrOtherDeliv[cnt].addr01|h}--><!--{$arrOtherDeliv[cnt].addr02|h}--><br />
									<!--{$arrOtherDeliv[cnt].name|h}-->
								</td>
								<td class="alignC">
									<a href="./delivery_addr.php" onclick="win02('./delivery_addr.php?other_deliv_id=<!--{$arrOtherDeliv[cnt].other_deliv_id}-->','deliv_disp','730','680'); return false;">変更</a>
								</td>
								<td class="alignC">
									<a href="#" onclick="fnModeSubmit('delete','other_deliv_id','<!--{$arrOtherDeliv[cnt].other_deliv_id}-->'); return false;">削除</a>
								</td>
							</tr>
						<!--{/section}-->
					</table>
					</div>
				</form>
				<!--{else}-->
				<p class="naked" style="color:#999999;">---ご登録済みの配送先はありません。---</p>
				<!--{/if}-->
			</div>
			<p class="alignR">
				<a href="refusal.php"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_refusal.gif" alt="登録削除" class="swp"></a>
			</p>
		</div>
	</div>
*}-->
</div>
<!--{$tpl_clickAnalyzer}-->
