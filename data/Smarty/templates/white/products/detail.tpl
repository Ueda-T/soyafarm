<link rel="stylesheet" href="<!--{$TPL_URLPATH}-->css/products_detail.css" type="text/css" media="all" />
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/products.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/facebox.js"></script>
<link rel="stylesheet" type="text/css" href="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/facebox.css" media="screen" />
<script type="text/javascript">//<![CDATA[
// 規格2に選択肢を割り当てる。
function SB_open(jan){
var url = "http://www.rohto.co.jp/prod/seib.cgi?kw="+jan;
sbWin=window.open(url,'sbwin','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=500,height=450');
self.name = "sb_Win";
sbWin.focus();
}
function fnSetClassCategories(form, classcat_id2_selected) {
	var $form = $(form);
	var product_id = $form.find('input[name=product_id]').val();
	var $sele1 = $form.find('select[name=classcategory_id1]');
	var $sele2 = $form.find('select[name=classcategory_id2]');
	setClassCategories($form, product_id, $sele1, $sele2, classcat_id2_selected);
}
$(document).ready(function() {
	$('a.expansion').facebox({
		loadingImage : '<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/loading.gif',
		closeImage	 : '<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/closelabel.png'
	});
});
//]]>
</script>

<!--★パンくず★-->
<p class="pankuzu">
	<!--{$TopicPath}-->
</p>

<!--▼CONTENTS-->
	<!--{$arrProduct.pc_comment1}-->
	<form name="form1" id="form1" method="post" action="?">
	<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
	<div id="product">
		<div id="detail">
			<div class="photo">
				<!--{assign var=key value="main_image"}-->
				<!--★画像★-->
<!--{*
				<!--{if $arrProduct.main_large_image|strlen >= 1}-->
					<a
						href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrProduct.main_large_image|h}-->"
						class="expansion"
						target="_blank"
					>
				<!--{/if}-->
*}-->
					<img src="<!--{$arrFile[$key].filepath|h}-->" width="<!--{$arrFile[$key].width}-->" height="<!--{$arrFile[$key].height}-->" alt="<!--{$arrProduct.name|h}-->" class="goodsPht" />
<!--{*
				<!--{if $arrProduct.main_large_image|strlen >= 1}-->
					</a>
				<!--{/if}-->
*}-->
			</div>
<!--{*
			<!--{if $arrProduct.main_large_image|strlen >= 1}-->
				<span class="mini">
						<!--★拡大する★-->
						<a
							href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrProduct.main_large_image|h}-->"
							class="expansion"
							target="_blank"
						>
							画像を拡大する</a>
				</span>
			<!--{/if}-->
*}-->

<!--{*
			<!--▼商品ステータス-->
			<!--{assign var=ps value=$productStatus[$tpl_product_id]}-->
			<!--{if count($ps) > 0}-->
				<p>
					<!--{foreach from=$ps item=status}-->
						<img src="<!--{$TPL_URLPATH}--><!--{$arrSTATUS[$status].image|h}-->" alt="<!--{$arrSTATUS[$status].name|h}-->" id="icon<!--{$status}-->" class="goodsIcon" />
					<!--{/foreach}-->
				</p>
			<!--{/if}-->
			<!--▲商品ステータス-->
*}-->

			<!--★商品名★-->
			<h2><!--{$arrProduct.main_list_comment}--></h2>
			<p class="introGC2"></p>

			<!--★詳細コメント★-->
			<p><!--{$arrProduct.main_comment}--></p>

			<p><!--{$arrProduct.pc_comment3}--></p>

<!--{*
			<!--★メール便案内★-->
			<!--{if $arrProduct.deliv_judgment < $smarty.const.DELIV_JUDGMENT_DEFAULT_VALUE}-->
			<div>
				<img src="<!--{$TPL_URLPATH}-->img/soyafarm/regular_info.gif" alt="この商品単体でのご注文は、メール便（送料無料）でのお届けとなります。代引き・お届け日はご指定いただけません。メール便のため、お届けにお時間をいただく場合がございます。" width="360" height="95" style="display:block; margin: 0 0 20px 0;" />
			</div>
			<!--{/if}-->
*}-->

		</div><!-- /#detail -->

		<div id="productsFormBox">
			<div id="recommend">
				<h3><img src="<!--{$TPL_URLPATH}-->img/soyafarm/ttl_recommend2.gif" alt="こんな方におすすめ" width="293" height="33"></h3>
				<!--{$arrProduct.pc_comment2}-->
			</div>

			<div id="buy">
				<h3><img src="<!--{$TPL_URLPATH}-->img/soyafarm/ttl_buy2.gif" alt="ご購入はこちら" /></h3>
				<ul>
					<li class="price">
						<!--★価格★-->
						<!--{if $arrProduct.price01_max > 0}-->
						税込<span><!--{$arrProduct.price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}--></span>円
						（税抜<span class="price2"><!--{$arrProduct.price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}--></span>円）
						<!--{/if}-->
					</li>
				</ul>

				<p>
					<!--{* ▼容量 *}-->
					<!--{if $arrProduct.capacity|strlen >= 1}-->
					<!--{$arrProduct.capacity|h}-->
					<!--{/if}-->
					<!--{* ▲容量 *}-->
				</p>

				<!--{if $tpl_stock_find}-->
				<div class="clearfix mb10">
					<div class="left">
				<!--{/if}-->

<!--{*
			<table cellspacing="0" class="propaty">
				<!-- ▼メーカー -->
				<!--{if $arrProduct.maker_name|strlen >= 1}-->
				<tr>
					<th>メーカー</th>
					<td><!--{$arrProduct.maker_name|h}--></td>
				</tr>
				<!--{/if}-->
				<!-- ▲メーカー -->

				<!--▼メーカーURL-->
				<!--{if $arrProduct.comment1|strlen >= 1}-->
				<tr>
					<th>メーカーURL</th>
					<td><a href="<!--{$arrProduct.comment1|h}-->"><!--{$arrProduct.comment1|h}--></a></td>
				</tr>
				<!--{/if}-->
				<!--▼メーカーURL-->

				<!--★商品コード★-->
				<tr>
					<th>商品番号</th>
					<td>
						<span id="product_code_default">
							<!--{if $arrProduct.product_code_min == $arrProduct.product_code_max}-->
								<!--{$arrProduct.product_code_min|h}-->
							<!--{else}-->
								<!--{$arrProduct.product_code_min|h}-->～<!--{$arrProduct.product_code_max|h}-->
							<!--{/if}-->
						</span><span id="product_code_dynamic"></span>
					</td>
				</tr>

				<!-- ▼ブランド名 -->
				<!--{if $arrProduct.brand_name|strlen >= 1}-->
				<tr>
					<th>ブランド名</th>
					<td><!--{$arrProduct.brand_name|h}--></td>
				</tr>
				<!--{/if}-->
				<!-- ▲ブランド名 -->

				<!-- ▼販売名 -->
				<!--{if $arrProduct.sales_name|strlen >= 1}-->
				<tr>
					<th>販売名</th>
					<td><!--{$arrProduct.sales_name|h}--></td>
				</tr>
				<!--{/if}-->
				<!-- ▲販売名 -->

				<!--{if $tpl_stock_find && $tpl_classcat_find1}-->
				<tr>
				<!--▼規格1-->
					<th><!--{$tpl_class_name1|h}--></th>
					<td>
						<select name="classcategory_id1" style="<!--{$arrErr.classcategory_id1|sfGetErrorColor}-->">
						<!--{html_options options=$arrClassCat1 selected=$arrForm.classcategory_id1.value}-->
						</select>
						<!--{if $arrErr.classcategory_id1 != ""}-->
						<br /><span class="attention">※ <!--{$tpl_class_name1}-->を入力して下さい。</span>
						<!--{/if}-->
					</td>
				</tr>
				<!--▲規格1-->
				<!--{if $tpl_classcat_find2}-->
				<!--▼規格2-->
				<tr>
					<th><!--{$tpl_class_name2|h}--></th>
					<td>
						<select name="classcategory_id2" style="<!--{$arrErr.classcategory_id2|sfGetErrorColor}-->">
						</select>
						<!--{if $arrErr.classcategory_id2 != ""}-->
						<br /><span class="attention">※ <!--{$tpl_class_name2}-->を入力して下さい。</span>
						<!--{/if}-->
					</td>
					<!--▲規格2-->
				</tr>
				<!--{/if}-->
				<!--{/if}-->
			</table>
*}-->

			<!--▼買い物かご-->

			<input type="hidden" name="mode" value="cart" />
			<input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->" />
			<input type="hidden" name="product_class_id" value="<!--{$tpl_product_class_id}-->" id="product_class_id" />
			<input type="hidden" name="favorite_product_id" value="" />
			<input type="hidden" name="regular_flg" id="regular_flg" value="" />

			<!--{if $tpl_stock_find}-->
					<!--{if $arrProduct.sale_start_date && $arrProduct.sale_start_date > $smarty.now|date_format:"%Y-%m-%d 00:00:00"}-->
						<!--★数量★-->
						<!--{ * 数量はプルダウンで選択できるように* }-->
						数量
						<select name="quantity" style="<!--{$arrErr.quantity|sfGetErrorColor}-->">
							<!--{html_options options=$tpl_arrQuantity[$tpl_product_class_id] }-->
						</select>
						</div>
						<div class="right">
						<!--★カゴに入れる★-->
						<a href="javascript:void(0);" onclick="document.form1.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_yoyaku.gif" alt="予約する" name="cart" id="cart" class="swp" /></a>
						</div>
					</div>
					<!--{else}-->
						<!--★販売終了日過ぎていない場合★-->
						<!--{if strlen($arrProduct.sale_end_date) == 0 || $arrProduct.sale_end_date >= $smarty.now|date_format:"%Y-%m-%d"}-->
						  <!--★数量★-->
						  <!--{ * 数量はプルダウンで選択できるように* }-->
						  数量
						  <select name="quantity" style="<!--{$arrErr.quantity|sfGetErrorColor}-->">
							  <!--{html_options options=$tpl_arrQuantity[$tpl_product_class_id] }-->
						  </select>
						</div>
						<div class="right">
						  <!--{* 定期可の商品のみボタンを表示 *}-->
						  <!--{if $arrProduct.teiki_flg == $smarty.const.REGULAR_PURCHASE_FLG_ON}-->
							  <a href="javascript:void(0);" onclick="fnAddProduct('1', document.form1); return false;"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_teiki.gif" alt="定期購入する" name="cart" id="cart" class="swp" style="margin-bottom:10px;" /></a><br />
						  <!--{/if}-->
						  <!--★カゴに入れる★-->
						  <a href="javascript:void(0);" onclick="document.form1.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_cart.gif" alt="カートに入れる" name="cart" id="cart" class="swp" /></a>
						</div>
					</div>
						<!--★販売終了日過ぎた場合★-->
						<!--{else}-->
							終了しました
						<!--{/if}-->
					<!--{/if}-->

					<!--{if $arrErr.quantity != ""}-->
						<br /><span class="attention"><!--{$arrErr.quantity}--></span>
					<!--{/if}-->
					<div class="attention" id="cartbtn_dynamic"></div>

					<!--{* 在庫切れ時の表示切り替え *}-->
					<!--{foreach from=$tpl_stock_status_name key=key item=stock_status }-->
					<div class="cart" id="cartbtn_stock_status<!--{$key}-->"><!--{$stock_status}--></div>
					<!--{/foreach}-->

					<!--{* 定期フラグを保持 *}-->
					<!--{foreach from=$tpl_teiki_flg key=key item=teiki_flg }-->
					<input type="hidden" id="teiki_flg_<!--{$tpl_product_id}-->_<!--{$key}-->" value="<!--{$teiki_flg}-->" />
					<!--{/foreach}-->

			<!--{else}-->
				<!--{foreach from=$tpl_stock_status_name key=key item=stock_status }-->
				<div class="cart" id="cartbtn_stock_status<!--{$key}-->">
					<p class="alignC"><!--{$stock_status}--></p>
				</div>
				<!--{/foreach}-->
			<!--{/if}-->
			<!--▲買い物かご-->
			</div>
		</div>
</div><!-- /#product -->

	</form>

	<!--詳細ここまで-->

	<!--{* ▼コメント４ *}-->
	<div>
	<!--{$arrProduct.pc_comment4}-->
	</div>
	<!--{* ▲コメント４ *}-->

	<!--{* ▼カートボタン表示４ *}-->
	<!--{if $arrProduct.pc_button4 == BUTTON_DISP_FLG_ON}-->
	<div class="cart<!--{if !$tpl_stock_find}--> noStockBox<!--{/if}-->">
		<div class="cartInnr">
			<!--{if $tpl_stock_find}-->
			<h3><strong><!--{if isset($arrProduct.disp_name)}--><!--{$arrProduct.disp_name|h}--><!--{else}--><!--{$arrProduct.name|h}--><!--{/if}--></strong>のご購入はこちら</h3>
			<!--{/if}-->
			<form name="form2" id="form2" method="post" action="?">
			<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
				<!--▼買い物かご-->
				<input type="hidden" name="mode" value="cart" />
				<input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->" />
				<input type="hidden" name="product_class_id" value="<!--{$tpl_product_class_id}-->" id="product_class_id" />
				<input type="hidden" name="favorite_product_id" value="" />
				<input type="hidden" name="regular_flg" id="regular_flg" value="" />
				<!--{if $tpl_stock_find}-->
					<!--{if $tpl_classcat_find1}-->
					<table cellspacing="0" class="propaty">
						<tr>
						<!--▼規格1-->
							<th><!--{$tpl_class_name1|h}--></th>
							<td>
								<select name="classcategory_id1" style="<!--{$arrErr.classcategory_id1|sfGetErrorColor}-->">
								<!--{html_options options=$arrClassCat1 selected=$arrForm.classcategory_id1.value}-->
								</select>
								<!--{if $arrErr.classcategory_id1 != ""}-->
								<br /><span class="attention">※ <!--{$tpl_class_name1}-->を入力して下さい。</span>
								<!--{/if}-->
							</td>
						</tr>
						<!--▲規格1-->
						<!--{if $tpl_classcat_find2}-->
						<!--▼規格2-->
						<tr>
							<th><!--{$tpl_class_name2|h}--></th>
							<td>
								<select name="classcategory_id2" style="<!--{$arrErr.classcategory_id2|sfGetErrorColor}-->">
								</select>
								<!--{if $arrErr.classcategory_id2 != ""}-->
								<br /><span class="attention">※ <!--{$tpl_class_name2}-->を入力して下さい。</span>
								<!--{/if}-->
							</td>
						</tr>
						<!--▲規格2-->
						<!--{/if}-->
					</table>
					<!--{/if}-->

					<table style="margin:0 auto;">
						<tr>
							<td class="price">
								<!--★価格★-->
								<!--{if $arrProduct.price01_max > 0}-->
								税込<span><!--{$arrProduct.price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}--></span>円
								（税抜<span class="price2"><!--{$arrProduct.price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}--></span>円）
								<!--{/if}-->
							</td>
					<!--{if $arrProduct.sale_start_date && $arrProduct.sale_start_date > $smarty.now|date_format:"%Y-%m-%d 00:00:00"}-->
							<td style="padding-right:20px;" rowspan="2">
								<!--★数量★-->
								<!--{ * 数量はプルダウンで選択できるように* }-->
								<select name="quantity" style="<!--{$arrErr.quantity|sfGetErrorColor}-->">
									<!--{html_options options=$tpl_arrQuantity[$tpl_product_class_id] }-->
								</select>
							</td>
							<td rowspan="2">
								<!--★カゴに入れる★-->
								<a href="javascript:void(0);" onclick="document.form2.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_yoyaku.gif" alt="予約する" name="cart" id="cart" class="swp" /></a>
							</td>
					<!--{else}-->
					<!--★販売終了日過ぎていない場合★-->
						<!--{if strlen($arrProduct.sale_end_date) == 0 || $arrProduct.sale_end_date >= $smarty.now|date_format:"%Y-%m-%d"}-->
							<td style="padding-right:20px;" rowspan="2">
							  <!--★数量★-->
							  <!--{ * 数量はプルダウンで選択できるように* }-->
							  <select name="quantity" style="<!--{$arrErr.quantity|sfGetErrorColor}-->">
								  <!--{html_options options=$tpl_arrQuantity[$tpl_product_class_id] }-->
							  </select>
							</td>
							<td rowspan="2">
							<!--{* 定期可の商品のみボタンを表示 *}-->
							<!--{if $arrProduct.teiki_flg == $smarty.const.REGULAR_PURCHASE_FLG_ON}-->
								<a href="javascript:void(0);" onclick="fnAddProduct('1', document.form2); return false;"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_teiki.gif" alt="定期購入する" name="cart" id="cart" class="swp" /></a>
								<!--{/if}-->
								<!--★カゴに入れる★-->
								<a href="javascript:void(0);" onclick="fnAddProduct('0', document.form2); return false;"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_cart.gif" alt="カートに入れる" name="cart" id="cart" class="swp" /></a>
							</td>
						<!--★販売終了日過ぎた場合★-->
						<!--{else}-->
							終了しました
						<!--{/if}-->
					<!--{/if}-->
					</tr>
					<tr>
						<td>
							<!--{* ▼容量 *}-->
							<!--{if $arrProduct.capacity|strlen >= 1}-->
							<!--{$arrProduct.capacity|h}-->
							<!--{/if}-->
							<!--{* ▲容量 *}-->
						</td>
					</tr>
				</table>



					<!--{if $arrErr.quantity != ""}-->
						<br /><span class="attention"><!--{$arrErr.quantity}--></span>
					<!--{/if}-->
					<div class="attention" id="cartbtn_dynamic"></div>

					<!--{* 在庫切れ時の表示切り替え *}-->
					<!--{foreach from=$tpl_stock_status_name key=key item=stock_status }-->
					<div id="cartbtn_stock_status<!--{$key}-->"><!--{$stock_status}--></div>
					<!--{/foreach}-->

					<!--{* 定期フラグを保持 *}-->
					<!--{foreach from=$tpl_teiki_flg key=key item=teiki_flg }-->
					<input type="hidden" id="teiki_flg_<!--{$tpl_product_id}-->_<!--{$key}-->" value="<!--{$teiki_flg}-->" />
					<!--{/foreach}-->

				<!--{else}-->

					<!--{** 在庫切れ表示 **}-->
					<!--{foreach from=$tpl_stock_status_name key=key item=stock_status }-->
					<div id="cartbtn_stock_status<!--{$key}-->"><!--{$stock_status}--></div>
					<!--{/foreach}-->

				<!--{/if}-->
				<!--▲買い物かご-->
			</form>
		</div><!-- /.cartInnr -->
	</div><!-- /.cart -->
	<!--{/if}-->
	<!--{* ▲カートボタン表示４ *}-->

	<!--{* ▼コメント５ *}-->
	<div>
	<!--{$arrProduct.pc_comment5}-->
	</div>
	<!--{* ▲コメント５ *}-->

	<!--{* ▼カートボタン表示５ *}-->
	<!--{if $arrProduct.pc_button5 == BUTTON_DISP_FLG_ON}-->
	<div class="cart<!--{if !$tpl_stock_find}--> noStockBox<!--{/if}-->">
		<div class="cartInnr">
			<!--{if $tpl_stock_find}-->
			<h3><strong><!--{if isset($arrProduct.disp_name)}--><!--{$arrProduct.disp_name|h}--><!--{else}--><!--{$arrProduct.name|h}--><!--{/if}--></strong>のご購入はこちら</h3>
			<!--{/if}-->
			<form name="form3" id="form3" method="post" action="?">
			<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
					<!--▼買い物かご-->
					<input type="hidden" name="mode" value="cart" />
					<input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->" />
					<input type="hidden" name="product_class_id" value="<!--{$tpl_product_class_id}-->" id="product_class_id" />
					<input type="hidden" name="favorite_product_id" value="" />
					<input type="hidden" name="regular_flg" id="regular_flg" value="" />
					<!--{if $tpl_stock_find}-->
						<!--{if $tpl_classcat_find1}-->
						<table cellspacing="0" class="propaty">
							<tr>
							<!--▼規格1-->
								<th><!--{$tpl_class_name1|h}--></th>
								<td>
									<select name="classcategory_id1" style="<!--{$arrErr.classcategory_id1|sfGetErrorColor}-->">
									<!--{html_options options=$arrClassCat1 selected=$arrForm.classcategory_id1.value}-->
									</select>
									<!--{if $arrErr.classcategory_id1 != ""}-->
									<br /><span class="attention">※ <!--{$tpl_class_name1}-->を入力して下さい。</span>
									<!--{/if}-->
								</td>
							</tr>
							<!--▲規格1-->
							<!--{if $tpl_classcat_find2}-->
							<!--▼規格2-->
							<tr>
								<th><!--{$tpl_class_name2|h}--></th>
								<td>
									<select name="classcategory_id2" style="<!--{$arrErr.classcategory_id2|sfGetErrorColor}-->">
									</select>
									<!--{if $arrErr.classcategory_id2 != ""}-->
									<br /><span class="attention">※ <!--{$tpl_class_name2}-->を入力して下さい。</span>
									<!--{/if}-->
								</td>
							</tr>
							<!--▲規格2-->
							<!--{/if}-->
						<!--{/if}-->
						</table>

					<table style="margin:0 auto;">
						<tr>
							<td class="price">
								<!--★価格★-->
								<!--{if $arrProduct.price01_max > 0}-->
								税込<span><!--{$arrProduct.price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}--></span>円
								（税抜<span class="price2"><!--{$arrProduct.price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}--></span>円）
								<!--{/if}-->
							</td>
						<!--{if $arrProduct.sale_start_date && $arrProduct.sale_start_date > $smarty.now|date_format:"%Y-%m-%d 00:00:00"}-->
							<td style="padding-right:20px;" rowspan="2">
								<!--★数量★-->
								<!--{ * 数量はプルダウンで選択できるように* }-->
								<select name="quantity" style="<!--{$arrErr.quantity|sfGetErrorColor}-->">
									<!--{html_options options=$tpl_arrQuantity[$tpl_product_class_id] }-->
								</select>
							</td>
							<td rowspan="2">
								<!--★カゴに入れる★-->
								<a href="javascript:void(0);" onclick="document.form3.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_yoyaku.gif" alt="予約する" name="cart" id="cart" class="swp" /></a>
							</td>
						<!--{else}-->
							<!--★販売終了日過ぎていない場合★-->
							<!--{if strlen($arrProduct.sale_end_date) == 0 || $arrProduct.sale_end_date >= $smarty.now|date_format:"%Y-%m-%d"}-->
							<td style="padding-right:20px;" rowspan="2">
							  <!--★数量★-->
							  <!--{ * 数量はプルダウンで選択できるように* }-->
							  <select name="quantity" style="<!--{$arrErr.quantity|sfGetErrorColor}-->">
								  <!--{html_options options=$tpl_arrQuantity[$tpl_product_class_id] }-->
							  </select>
							</td>
							<td rowspan="2">
							  <!--{* 定期可の商品のみボタンを表示 *}-->
							  <!--{if $arrProduct.teiki_flg == $smarty.const.REGULAR_PURCHASE_FLG_ON}-->
								  <a href="javascript:void(0);" onclick="fnAddProduct('1', document.form3); return false;"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_teiki.gif" alt="定期購入する" name="cart" id="cart" class="swp" /></a>
							  <!--{/if}-->
							  <!--★カゴに入れる★-->
							  <a href="javascript:void(0);" onclick="fnAddProduct('0', document.form3); return false;"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_cart.gif" alt="カートに入れる" name="cart" id="cart" class="swp" /></a>

							</td>
							<!--★販売終了日過ぎた場合★-->
							<!--{else}-->
								終了しました
							<!--{/if}-->
						<!--{/if}-->
						</tr>
						<tr>
							<td>
								<!--{* ▼容量 *}-->
								<!--{if $arrProduct.capacity|strlen >= 1}-->
								<!--{$arrProduct.capacity|h}-->
								<!--{/if}-->
								<!--{* ▲容量 *}-->
							</td>
						</tr>
					</table>

						<!--{if $arrErr.quantity != ""}-->
							<br /><span class="attention"><!--{$arrErr.quantity}--></span>
						<!--{/if}-->
						<div class="attention" id="cartbtn_dynamic"></div>

						<!--{* 在庫切れ時の表示切り替え *}-->
						<!--{foreach from=$tpl_stock_status_name key=key item=stock_status }-->
						<div id="cartbtn_stock_status<!--{$key}-->"><!--{$stock_status}--></div>
						<!--{/foreach}-->

						<!--{* 定期フラグを保持 *}-->
						<!--{foreach from=$tpl_teiki_flg key=key item=teiki_flg }-->
						<input type="hidden" id="teiki_flg_<!--{$tpl_product_id}-->_<!--{$key}-->" value="<!--{$teiki_flg}-->" />
						<!--{/foreach}-->

					<!--{else}-->

						<!--{** 在庫切れ表示 **}-->
						<!--{foreach from=$tpl_stock_status_name key=key item=stock_status }-->
						<div id="cartbtn_stock_status<!--{$key}-->"><!--{$stock_status}--></div>
						<!--{/foreach}-->

					<!--{/if}-->
					<!--▲買い物かご-->
			</form>
		</div><!-- /.cartInnr -->
	</div><!-- /.cart -->
	<!--{/if}-->
	<!--{* ▲カートボタン表示５ *}-->

	<!--▼サブコメント-->
	<!--{section name=cnt loop=$smarty.const.PRODUCTSUB_MAX}-->
		<!--{assign var=key value="sub_title`$smarty.section.cnt.index+1`"}-->
		<!--{if $arrProduct[$key] != ""}-->
			<div class="sub_area clearfix">
				<h3><!--★サブタイトル★--><!--{$arrProduct[$key]|h}--></h3>
				<!--{assign var=ckey value="sub_comment`$smarty.section.cnt.index+1`"}-->
				<!--▼サブ画像-->
				<!--{assign var=key value="sub_image`$smarty.section.cnt.index+1`"}-->
				<!--{assign var=lkey value="sub_large_image`$smarty.section.cnt.index+1`"}-->
				<!--{if $arrProduct[$key]|strlen >= 1}-->
					<div class="subtext"><!--★サブテキスト★--><!--{$arrProduct[$ckey]|nl2br_html}--></div>
					<div class="subphotoimg">
						<!--{if $arrProduct[$lkey]|strlen >= 1}-->
							<a href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrProduct[$lkey]|h}-->" class="expansion" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_expansion_on.gif', 'expansion_<!--{$lkey|h}-->');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_expansion.gif', 'expansion_<!--{$lkey|h}-->');" target="_blank" >
						<!--{/if}-->
						<img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrProduct.name|h}-->" width="<!--{$arrFile[$key].width}-->" height="<!--{$arrFile[$key].height}-->" />
						<!--{if $arrProduct[$lkey]|strlen >= 1}--></a>
							<span class="mini">
								<a href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrProduct[$lkey]|h}-->" class="expansion" target="_blank">
									画像を拡大する</a>
							</span>
						<!--{/if}-->
					</div><!-- /.subphotoimg -->
				<!--{else}-->
					<p class="subtext"><!--★サブテキスト★--><!--{$arrProduct[$ckey]|nl2br_html}--></p>
				<!--{/if}-->
				<!--▲サブ画像-->
			</div><!-- /.sub_area -->
		<!--{/if}-->
	<!--{/section}-->
	<!--▲サブコメント-->

<!--{*
	<!--この商品に対するお客様の声-->
	<div id="customervoice_area">
		<h2><img src="<!--{$TPL_URLPATH}-->img/title/tit_product_voice.gif" alt="この商品に対するお客様の声" /></h2>

		<div class="review_bloc clearfix">
			<p>この商品に対するご感想をぜひお寄せください。</p>
			<div class="review_btn">
				<!--{if count($arrReview) < $smarty.const.REVIEW_REGIST_MAX}-->
					<!--★新規コメントを書き込む★-->
					<a href="./review.php"
						onclick="win02('./review.php?product_id=<!--{$arrProduct.product_id}-->','review','600','640'); return false;"
						onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_comment_on.jpg','review');"
						onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_comment.jpg','review');" target="_blank">
						<img src="<!--{$TPL_URLPATH}-->img/button/btn_comment.jpg" alt="新規コメントを書き込む" name="review" id="review" /></a>
				<!--{/if}-->
			</div><!-- /.review_btn -->
		</div><!-- /.review_bloc -->

		<!--{if count($arrReview) > 0}-->
			<ul>
				<!--{section name=cnt loop=$arrReview}-->
					<li>
						<p class="voicetitle"><!--{$arrReview[cnt].title|h}--></p>
						<p class="voicedate"><!--{$arrReview[cnt].create_date|sfDispDBDate:false}-->　投稿者：<!--{if $arrReview[cnt].reviewer_url}--><a href="<!--{$arrReview[cnt].reviewer_url}-->" target="_blank"><!--{$arrReview[cnt].reviewer_name|h}--></a><!--{else}--><!--{$arrReview[cnt].reviewer_name|h}--><!--{/if}-->　おすすめレベル：<span class="recommend_level"><!--{assign var=level value=$arrReview[cnt].recommend_level}--><!--{$arrRECOMMEND[$level]|h}--></span></p>
						<p class="voicecomment"><!--{$arrReview[cnt].comment|h|nl2br}--></p>
					</li>
				<!--{/section}-->
			</ul>
		<!--{/if}-->
	</div><!-- /#customervoice_area -->
	<!--お客様の声ここまで-->
*}-->

	<!--▼関連商品-->
	<!--{if $arrRecommend}-->
			<h2><img src="<!--{$TPL_URLPATH}-->img/soyafarm/tit_recommend.gif" alt="その他のオススメ商品" /></h2>

			<div class="itemGStT">
			<!--{section name=cnt loop=$arrRecommend}-->
				<!--{if $arrRecommend[cnt]}-->
					<dl>
						<dd class="thum">
							<a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrRecommend[cnt].product_id|u}-->">
								<img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH|sfTrimURL}-->/<!--{$arrRecommend[cnt].main_list_image|sfNoImageMainList|h}-->" alt="<!--{$arrRecommend[cnt].name|h}-->" class="picture" /></a>
						</dd>
						<dt>
							<h3><a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrRecommend[cnt].product_id|u}-->"><!--{$arrRecommend[cnt].name|h}--></a></h3>

							<!--{if $tpl_customer_kbn != $smarty.const.CUSTOMER_KBN_EMPLOYEE}-->
							<!--{* ▼通常価格 *}-->
							<!--{assign var=price01_min value=`$arrRecommend[cnt].price01_min`}-->
							<!--{assign var=price01_max value=`$arrRecommend[cnt].price01_max`}-->
							<dd class="price"><span>
								<!--{if $price01_min == $price01_max}-->
									<!--{$price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
								<!--{else}-->
									<!--{$price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～<!--{$price01_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
								<!--{/if}-->円<span class="small">(税込)</span></span>
							</dd>
							<!--{* ▲通常価格 *}-->

							<!--{else}-->

							<!--{* ▼社員価格 *}-->
							<!--{assign var=price02_min value=`$arrRecommend[cnt].price02_min`}-->
							<!--{assign var=price02_max value=`$arrRecommend[cnt].price02_max`}-->
							<dd class="price"><span>
								<!--{if $price02_min == $price02_max}-->
									<!--{$price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
								<!--{else}-->
									<!--{$price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～<!--{$price02_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
								<!--{/if}-->円<span class="small">(税込)</span></span>
							</dd>
							
							<!--{* ▲社員価格 *}-->

							<!--{/if}-->
							<p class="mini"><!--{$arrRecommend[cnt].comment|nl2br}--></p>
						</dt>
					</dl>
				<!--{/if}-->
		<!--{/section}-->
		</div><!-- /.itemGStT -->
	<!--{/if}-->
	<!--▲関連商品-->
<!--▲CONTENTS-->
<!--{$tpl_clickAnalyzer}-->
