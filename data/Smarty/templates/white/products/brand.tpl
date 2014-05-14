<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/products.js"></script>
<script type="text/javascript">//<![CDATA[
function fnSetClassCategories(form, classcat_id2_selected) {
    var $form = $(form);
    var product_id = $form.find('input[name=product_id]').val();
    var $sele1 = $form.find('select[name=classcategory_id1]');
    var $sele2 = $form.find('select[name=classcategory_id2]');
    setClassCategories($form, product_id, $sele1, $sele2, classcat_id2_selected);
}
// カゴに入れる
function fnInCart(productForm) {
    var product_id = productForm["product_id"].value;
    fnChangeAction("?#product" + product_id);
    if (productForm["classcategory_id1"]) {
        fnSetVal("classcategory_id1", productForm["classcategory_id1"].value);
    }
    if (productForm["classcategory_id2"]) {
        fnSetVal("classcategory_id2", productForm["classcategory_id2"].value);
    }
    fnSetVal("quantity", productForm["quantity"].value);
    fnSetVal("product_id", productForm["product_id"].value);
    fnSetVal("product_class_id", productForm["product_class_id"].value);
    fnSubmit();
}
//]]>
</script>

<!--▼CONTENTS-->
<div id="undercolumn">
    <form name="form1" id="form1" method="get" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="<!--{$mode|h}-->" />
        <!--{* ▼検索条件 *}-->
        <input type="hidden" name="brand_id" value="<!--{$arrSearchData.brand_id|h}-->" />
        <!--{* ▲検索条件 *}-->
        <!--{* ▼注文関連 *}-->
        <input type="hidden" name="product_id" value="" />
        <input type="hidden" name="classcategory_id1" value="" />
        <input type="hidden" name="classcategory_id2" value="" />
        <input type="hidden" name="product_class_id" value="" />
        <input type="hidden" name="quantity" value="" />
        <!--{* ▲注文関連 *}-->
        <input type="hidden" name="rnd" value="<!--{$tpl_rnd|h}-->" />
    </form>
    <!--★パンくず★-->
	<p class="pankuzu">
		<!--{$TopicPath}-->
	</p>

	<div id="mainEvent">
    <!--★タイトル★-->
    <!--{assign var=stClass value="cateTitle"}-->
	<!--{if $arrBrand.pc_free_space3}-->
		<!--{$arrBrand.pc_free_space3}-->
	<!--{else}-->
	<h1 class="<!--{$stClass}-->"><!--{$arrBrand.brand_name|h}--></h1>
	<!--{/if}-->

	<!--{if $arrBrand.pc_comment}-->
	<!--{$arrBrand.pc_comment}-->
	<!--{/if}-->

	<!--{$arrBrand.pc_free_space4}-->

    <!--{foreach from=$arrChildBrand item=childBrand name=arrChildBrand}-->

	<!--{if $tpl_child_brand}-->
		<!--{if $childBrand.pc_free_space1}-->
			<!--{$childBrand.pc_free_space1}-->
		<!--{else}-->
			<h2><!--{$childBrand.brand_name|h}--></h2>
		<!--{/if}-->
	<!--{/if}-->
	<div class="goodsList clearfix">

    <!--{assign var=brand_id value=$childBrand.brand_id}-->
    <!--{foreach from=$arrBrandProduct[$brand_id] item=arrProduct name=arrProducts}-->
        <!--{assign var=id value=$arrProduct.product_id}-->
        <!--{assign var=arrErr value=$arrProduct.arrErr}-->
        <!--▼商品-->
        <div class="itemStP">
        <form name="product_form<!--{$id|h}-->" action="?" onsubmit="return false;">
        <input type="hidden" name="brand_id" value="<!--{$arrSearchData.brand_id|h}-->" />
        <input type="hidden" name="mode" value="<!--{$mode|h}-->" />
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="regular_flg" id="regular_flg" value="" />
			<table cellspacing="0">
				<tr>
					<td class="thum">
						<a name="product<!--{$id|h}-->"></a>

							<!--★画像★-->
							<a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrProduct.product_id|u}-->" class="over"><!--商品写真--><img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH|sfTrimURL}-->/<!--{$arrProduct.main_list_image|sfNoImageMainList|h}-->" alt="<!--{$arrProduct.name|h}-->" class="picture" /></a>
					</td>

					<td>
						<!--★コメント★-->
						<dl>
							<dd class="gaiyo"><!--{$arrProduct.main_list_comment}--></dd>
						<!--★商品名★-->
							<dt><a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrProduct.product_id|u}-->"><!--{if isset($arrProduct.disp_name)}--><!--{$arrProduct.disp_name|h}--><!--{else}--><!--{$arrProduct.name|h}--><!--{/if}--></a></dt>

<!--{* 商品ステータスはブランドで非表示の為コメントアウト
						<!--▼商品ステータス-->
						<!--{if count($productStatus[$id]) > 0}-->
							<dd>
							    <ul class="status_icon clearfix">
							        <!--{foreach from=$productStatus[$id] item=status}--> 
							            <li>
							                <img src="<!--{$TPL_URLPATH}--><!--{$arrSTATUS_IMAGE[$status]}-->" alt="<!--{$arrSTATUS[$status]}-->" />
							            </li>
							        <!--{/foreach}-->
							    </ul>
						    </dd>
						<!--{/if}-->
						<!--▲商品ステータス-->
*}-->
							<!--★価格★-->
						<!--{if strlen($tpl_customer_kbn) == null || $tpl_customer_kbn == $smarty.const.CUSTOMER_KBN_NORMAL}-->
							<dd class="price">￥
								<!--{if $arrProduct.price01_min == $arrProduct.price01_max}-->
									<!--{$arrProduct.price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
								<!--{else}-->
									<!--{$arrProduct.price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～<!--{$arrProduct.price01_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
								<!--{/if}-->
							<span>(税込)</span>
							</dd>
							<!--★社員価格★-->
						<!--{elseif $tpl_customer_kbn == $smarty.const.CUSTOMER_KBN_EMPLOYEE }-->
							<dd class="price">￥
								<!--{if $arrProduct.price02_min == $arrProduct.price02_max}-->
									<!--{$arrProduct.price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
								<!--{else}-->
									<!--{$arrProduct.price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～<!--{$arrProduct.price02_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
								<!--{/if}-->
							<span>(税込)</span>
							</dd>
						<!--{/if}-->
						</dl>

                <!--▼買い物かご-->
                <input type="hidden" name="product_id" value="<!--{$id|h}-->" />
                <input type="hidden" name="product_class_id" id="product_class_id<!--{$id|h}-->" value="<!--{$tpl_product_class_id[$id]}-->" />

                    <!--{if $tpl_stock_find[$id]}-->
                        <!--{if $tpl_classcat_find1[$id]}-->
                            <div class="classlist">
                                <dl class="size01 clearfix">
                                        <!--▼規格1-->
                                        <dt><!--{$tpl_class_name1[$id]|h}-->：</dt>
                                        <dd>
                                            <select name="classcategory_id1" style="<!--{$arrErr.classcategory_id1|sfGetErrorColor}-->">
                                                <!--{html_options options=$arrClassCat1[$id] selected=$arrProduct.classcategory_id1}-->
                                            </select>
                                            <!--{if $arrErr.classcategory_id1 != ""}-->
                                                <p class="attention">※ <!--{$tpl_class_name1[$id]}-->を入力して下さい。</p>
                                            <!--{/if}-->
                                        </dd>
                                        <!--▲規格1-->
                                </dl>
                                <!--{if $tpl_classcat_find2[$id]}-->
                                    <dl class="size02 clearfix">
                                        <!--▼規格2-->
                                        <dt><!--{$tpl_class_name2[$id]|h}-->：</dt>
                                        <dd>
                                            <select name="classcategory_id2" style="<!--{$arrErr.classcategory_id2|sfGetErrorColor}-->">
                                            </select>
                                            <!--{if $arrErr.classcategory_id2 != ""}-->
                                                <p class="attention">※ <!--{$tpl_class_name2[$id]}-->を入力して下さい。</p>
                                            <!--{/if}-->
                                        </dd>
                                        <!--▲規格2-->
                                    </dl>
                                <!--{/if}-->
                            </div>
                        <!--{/if}-->
                            <div class="quantity">
                                <!--{if $arrErr.quantity != ""}-->
                                    <br /><span class="attention"><!--{$arrErr.quantity}--></span>
                                <!--{/if}-->

                                <!--{if $arrProduct.sale_start_date && $arrProduct.sale_start_date > $smarty.now|date_format:"%Y-%m-%d 00:00:00"}-->
                                    <!--{ * 数量はプルダウンで選択できるように* }-->
                                    <!--
                                    <input type="text" name="quantity" size="3" value="<!--{$arrProduct.quantity|default:1|h}-->" maxlength="<!--{$smarty.const.INT_LEN}-->" style="<!--{$arrErr.quantity|sfGetErrorColor}-->" />
                                    -->
                                    <!--{assign var=class_id value=$tpl_product_class_id[$id]}-->
                                    <select name="quantity" style="<!--{$arrErr.quantity|sfGetErrorColor}-->">
                                        <!--{html_options options=$tpl_arrQuantity[$class_id] }-->
                                    </select>

                                    <a href="javascript:void(0);" onclick="fnInCart(document.product_form<!--{$id|h}-->); return false;"><img src="<!--{$TPL_URLPATH}-->img/rohto/reserve_s.jpg" alt="予約する" align="absmiddle" class="swp" /></a>
                                <!--{else}-->
                                    <!--{if strlen($arrProduct.sale_end_date) == 0 || $arrProduct.sale_end_date >= $smarty.now|date_format:"%Y-%m-%d"}-->
                                <div id="cartbtn_default">
                                <!--★カゴに入れる★-->
                                    <!--{assign var=class_id value=$tpl_product_class_id[$id]}-->
                                    <select name="quantity" style="<!--{$arrErr.quantity|sfGetErrorColor}-->">
                                        <!--{html_options options=$tpl_arrQuantity[$class_id] }-->
                                    </select>

                                    <div class="cartBtnList">
	                                    <!--★社員は定期購入不可★-->
	                                    <!--{if $tpl_customer_kbn != $smarty.const.CUSTOMER_KBN_EMPLOYEE && $arrProduct.teiki_flg != 0}-->
	                                    <p id="cartbtn_teiki_<!--{$id}-->" class="teikiBtn">
	                                    <a href="javascript:void(0);" onclick="fnAddProduct('1', document.product_form<!--{$id|h}-->); return false;"><img src="<!--{$TPL_URLPATH}-->img/rohto/teiki_s.gif" alt="定期購入する" align="absmiddle" class="swp" /></a>
	                                    </p>
                                        <!--{/if}-->
	                                    <a href="javascript:void(0);" onclick="fnInCart(document.product_form<!--{$id|h}-->); return false;"><img src="<!--{$TPL_URLPATH}-->img/rohto/cart_s.gif" alt="カートに入れる" align="absmiddle" class="swp" /></a>
                                    </div>
                                    <!--{/if}-->
                                <!--{/if}-->
                                </div>

                                <!--{* 在庫切れ時の表示切り替え *}-->
                                <!--{foreach from=$tpl_stock_status_name key=key item=stock_status }-->
                                <div class="attention" id="cartbtn_stock_status<!--{$key}-->"><!--{$stock_status}--></div>
                                <!--{/foreach}-->

                                <!--{* 定期フラグを保持 *}-->
                                <!--{foreach from=$tpl_teiki_flg key=key item=teiki_flg }-->
                                <input type="hidden" id="teiki_flg_<!--{$id}-->_<!--{$key}-->" value="<!--{$teiki_flg}-->" />
                                <!--{/foreach}-->
                                </div>
                    <!--{else}-->
                        <!--{foreach from=$tpl_stock_status_name key=key item=stock_status }-->
                        <div class="attention" id="cartbtn_stock_status<!--{$key}-->"><!--{$stock_status}--></div>
                        <!--{/foreach}-->
                    <!--{/if}-->
                <!--▲買い物かご-->

				</td>
			</tr>
		</table>
        </form>
	</div>

	<!--{if $smarty.foreach.arrProducts.iteration %2 === 0 || $smarty.foreach.arrProducts.last}-->
		<div class="btm"></div>
	<!--{/if}-->

        <!--▲商品-->

    <!--{/foreach}-->
</div>

<!--{$childBrand.pc_free_space2}-->
<!--{/foreach}-->
<!--{$arrBrand.pc_free_space5}-->
</div>
</div>
<!--▲CONTENTS-->
<!--{$tpl_clickAnalyzer}-->
