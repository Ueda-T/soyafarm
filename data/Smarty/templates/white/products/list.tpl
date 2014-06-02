<link rel="stylesheet" href="<!--{$TPL_URLPATH}-->css/products.css" type="text/css" media="all" />
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/products.js"></script>
<script type="text/javascript">//<![CDATA[
function fnSetClassCategories(form, classcat_id2_selected) {
    var $form = $(form);
    var product_id = $form.find('input[name=product_id]').val();
    var $sele1 = $form.find('select[name=classcategory_id1]');
    var $sele2 = $form.find('select[name=classcategory_id2]');
    setClassCategories($form, product_id, $sele1, $sele2, classcat_id2_selected);
}
// 並び順を変更
function fnChangeOrderby(orderby) {
    fnSetVal('orderby', orderby);
    fnSetVal('pageno', 1);
    fnSubmit();
}
// 表示件数を変更
function fnChangeDispNumber(dispNumber) {
    fnSetVal('disp_number', dispNumber);
    fnSetVal('pageno', 1);
    fnSubmit();
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
        <input type="hidden" name="category_id" value="<!--{$arrSearchData.category_id|h}-->" />
        <input type="hidden" name="brand_id" value="<!--{$arrSearchData.brand_id|h}-->" />
        <input type="hidden" name="maker_id" value="<!--{$arrSearchData.maker_id|h}-->" />
        <input type="hidden" name="name" value="<!--{$arrSearchData.name|h}-->" />
        <!--{* ▲検索条件 *}-->
        <!--{* ▼ページナビ関連 *}-->
        <input type="hidden" name="orderby" value="<!--{$orderby|h}-->" />
        <input type="hidden" name="disp_number" value="<!--{$disp_number|h}-->" />
        <input type="hidden" name="pageno" value="<!--{$tpl_pageno|h}-->" />
        <!--{* ▲ページナビ関連 *}-->
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

<h1><img src="<!--{$TPL_URLPATH}-->img/soyafarm/h1_lineup.gif" alt="商品ラインナップ"></h1>

    <!--★タイトル★-->
    <!--{assign var=stClass value="cateTitle"}-->
	<!--{if $tpl_arrCategory.image}-->
    <img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH|sfTrimURL}-->/<!--{$tpl_arrCategory.image|h}-->" style="display:block;margin-bottom:30px;">
    <!--{assign var=stClass value="category_title"}-->
	<!--{/if}-->

	<h1 class="<!--{$stClass}-->"><!--{$tpl_subtitle|h}--></h1>

    <!--▼検索条件-->
    <!--{if $tpl_subtitle == "検索結果"}-->
        <ul class="pagecond_area">
            <li><strong>商品カテゴリ：</strong><!--{$arrSearch.category|h}--></li>
            <!--{if $arrSearch.brand|strlen >= 1}--><li><strong>ブランド：</strong><!--{$arrSearch.brand|h}--></li><!--{/if}-->
            <!--{if $arrSearch.maker|strlen >= 1}--><li><strong>メーカー：</strong><!--{$arrSearch.maker|h}--></li><!--{/if}-->
            <li><strong>商品名：</strong><!--{$arrSearch.name|h}--></li>
        </ul>
    <!--{/if}-->
    <!--▲検索条件-->

    <!--▼ページナビ(本文)-->
    <!--{capture name=page_navi_body}-->
        <div class="pagenumber_area clearfix">
            <div class="change">
                <!--{if $orderby != 'price'}-->
                    <a href="javascript:fnChangeOrderby('price');">価格順</a>
                <!--{else}-->
                    <strong>価格順</strong>
                <!--{/if}-->&nbsp;
                <!--{if $orderby != "date"}-->
                        <a href="javascript:fnChangeOrderby('date');">新着順</a>
                <!--{else}-->
                    <strong>新着順</strong>
                <!--{/if}-->
                表示件数
                <select name="disp_number" onchange="javascript:fnChangeDispNumber(this.value);">
                    <!--{foreach from=$arrPRODUCTLISTMAX item="dispnum" key="num"}-->
                        <!--{if $num == $disp_number}-->
                            <option value="<!--{$num}-->" selected="selected" ><!--{$dispnum}--></option>
                        <!--{else}-->
                            <option value="<!--{$num}-->" ><!--{$dispnum}--></option>
                        <!--{/if}-->
                    <!--{/foreach}-->
                </select>
            </div>

			<table>
				<tr>
					<td style="padding:1px 10px 0 0;">
						<!--{* ▼ #343 ○件中 N～Y件表示 *}-->
						<div>
							<!--{$tpl_linemax}-->件中&nbsp;<!--{$tpl_from_no}-->～<!--{$tpl_to_no}-->件表示
						</div>
						<!--{* ▲ #343 ○件中 N～Y件表示 *}-->
					</td>
					<td>
						<ul class="navi"><!--{$tpl_strnavi}--></ul>
					</td>
				</tr>
			</table>
        </div>
    <!--{/capture}-->
    <!--▲ページナビ(本文)-->
	<p class="introCategory"></p>

<!--{if $arrProducts}-->
	<div class="goodsList clearfix">
<!--{/if}-->

<ul id="lineup">
    <!--{foreach from=$arrProducts item=arrProduct name=arrProducts}-->

<!--{*
        <!--{if $smarty.foreach.arrProducts.first}-->
            <!--▼件数-->
            <div>
                <span class="attention"><!--{$tpl_linemax}-->件</span>の商品がございます。
            </div>
            <!--▲件数-->

            <!--▼ページナビ(上部)-->
            <form name="page_navi_top" id="page_navi_top" action="?">
                <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
                <!--{if $tpl_linemax > 0}--><!--{$smarty.capture.page_navi_body|smarty:nodefaults}--><!--{/if}-->
            </form>
            <!--▲ページナビ(上部)-->
        <!--{/if}-->
*}-->

        <!--{assign var=id value=$arrProduct.product_id}-->
        <!--{assign var=arrErr value=$arrProduct.arrErr}-->
        <li>
        <!--▼商品-->
        <form name="product_form<!--{$id|h}-->" action="?" onsubmit="return false;">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="regular_flg" id="regular_flg" value="" />

						<!--★商品名★-->
							<h2><!--{if isset($arrProduct.disp_name)}--><!--{$arrProduct.disp_name|h}--><!--{else}--><!--{$arrProduct.name|h}--><!--{/if}--></h2>

					<div class="photo">
						<a name="product<!--{$id|h}-->"></a>

							<!--★画像★-->
							<a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrProduct.product_id|u}-->" class="over"><!--商品写真--><img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH|sfTrimURL}-->/<!--{$arrProduct.main_list_image|sfNoImageMainList|h}-->" alt="<!--{$arrProduct.name|h}-->" class="picture" /></a>
					</div>

					<div class="detail">
						<!--★コメント★-->
						<p><!--{$arrProduct.main_list_comment}--></p>

						<p class="shosai">
							<a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrProduct.product_id|u}-->"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_products_detail.gif" alt="商品の詳細はこちら" border="0" class="btn"></a>
						</p>

					<div class="tocart">
						<h3><img src="<!--{$TPL_URLPATH}-->img/soyafarm/ttl_buy.gif" alt="ご購入はこちら" width="411" height="26"></h3>
						<div class="pinkBox">
							<!--★価格★-->
							<ul>
								<li class="price">税込<span><!--{$arrProduct.price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}--></span>円<br />
								（税抜<span class="price2"><!--{$arrProduct.price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}--></span>円）
								</li>
								<!--{* ▼容量 *}-->
								<!--{if $arrProduct.capacity|strlen >= 1}-->
								<li><!--{$arrProduct.capacity|h}--></li>
								<!--{/if}-->
								<!--{* ▲容量 *}-->
							</ul>

                <!--▼買い物かご-->
                <input type="hidden" name="product_id" value="<!--{$id|h}-->" />
                <input type="hidden" name="product_class_id" id="product_class_id<!--{$id|h}-->" value="<!--{$tpl_product_class_id[$id]}-->" />

                    <!--{if $tpl_stock_find[$id]}-->
                        <!--{if $tpl_classcat_find1[$id]}-->
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
                        <!--{/if}-->
                        
                                <!--{if $arrErr.quantity != ""}-->
                                    <br /><span class="attention"><!--{$arrErr.quantity}--></span>
                                <!--{/if}-->

                                <!--{if strlen($arrProduct.sale_end_date) == 0 || $arrProduct.sale_end_date >= $smarty.now|date_format:"%Y-%m-%d"}-->
                                <div id="cartbtn_default">
                                <!--★カゴに入れる★-->
                                    <!--{ * 数量はプルダウンで選択できるように* }-->
                                    <!--{assign var=class_id value=$tpl_product_class_id[$id]}-->
                                    <select name="quantity" style="<!--{$arrErr.quantity|sfGetErrorColor}-->">
                                        <!--{html_options options=$tpl_arrQuantity[$class_id] }-->
                                    </select>

                                    <div class="cartBtnList">
                                        <!--★社員は定期購入不可★-->
                                        <!--{if $arrProduct.teiki_flg != 0}-->
                                        <p id="cartbtn_teiki_<!--{$id}-->" class="teikiBtn">
                                        <a href="javascript:void(0);" onclick="fnAddProduct('1', document.product_form<!--{$id|h}-->); return false;"><img src="<!--{$TPL_URLPATH}-->img/rohto/teiki_s.gif" alt="定期購入する" align="absmiddle" class="swp" /></a>
                                        </p>
                                        <!--{/if}-->
                                        <p><a href="javascript:void(0);" onclick="fnInCart(document.product_form<!--{$id|h}-->); return false;"><img src="<!--{$TPL_URLPATH}-->img/rohto/cart_s.gif" alt="カートに入れる" align="absmiddle" class="swp" /></a></p>
                                </div>
                                <!--{/if}-->

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
				</div><!--/.pinkBox-->
			</div><!--/.toCart-->
				</div>
        </form>
        <!--▲商品-->
        </li>
    <!--{foreachelse}-->
        <!--{include file="frontparts/search_zero.tpl"}-->


		<form name="search_form2" id="search_form2" method="get" action="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php">
			<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
			<input type="hidden" name="mode" value="search" />

			<div class="wrapKensaku">
				<div class="wrapKensakuEle">
					<table cellspacing="0">
						<tr class="top">
							<th>キーワード</th>
							<td>
								<input type="text" name="name" class="search" maxlength="50" value="<!--{$smarty.get.name|h}-->" />
							</td>
						</tr>
						<tr>
							<th>カテゴリ</th>
							<td>
								<select name="category_id" class="tree">
									<option label="カテゴリ指定なし" value="">カテゴリ指定なし</option>
									<!--{html_options options=$arrCatList}-->
								</select>
							</td>
						</tr>
						<!--{if $arrBrandList}-->
						<tr>
							<th>ブランド</th>
							<td>
									<select name="brand_id" class="brand">
										<option label="ブランド指定なし" value="">ブランド指定なし</option>
										<!--{html_options options=$arrBrandList}-->
									</select>
							</td>
						</tr>
						<!--{/if}-->
						<!--{if $arrMakerList}-->
						<tr>
							<th>メーカー</th>
							<td>
									<select name="maker_id" class="maker">
										<option label="メーカー指定なし" value="">メーカー指定なし</option>
										<!--{html_options options=$arrMakerList}-->
									</select>
							</td>
						</tr>
						<!--{/if}-->
					</table>
				</div><!--／wrapKensakuEle-->
				<p class="btn"><a href="javascript:void(0);" onclick="document.search_form2.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/rohto/search.gif" class="swp" /></a></p>
			</div><!--／wrapKensaku-->
		</form>

    <!--{/foreach}-->
    </ul>

<!--{if $arrProducts}-->
</div>
<!--{/if}-->


    <form name="page_navi_bottom" id="page_navi_bottom" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <!--{if $tpl_linemax > 0}--><!--{$smarty.capture.page_navi_body|smarty:nodefaults}--><!--{/if}-->
    </form>
</div>
<!--▲CONTENTS-->
<!--{$tpl_clickAnalyzer}-->
