<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/products.js"></script>
<script type="text/javascript">//<![CDATA[
function fnSetClassCategories(form, classcat_id2_selected) {
    var $form = $(form);
    var product_id = $form.find('input[name=product_id]').val();
    //var $sele1 = $form.find('select[name=classcategory_id1]');
    //var $sele2 = $form.find('select[name=classcategory_id2]');
    //setClassCategories($form, product_id, $sele1, $sele2, classcat_id2_selected);
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
function fnInCart(productForm, regular_flg) {
    var product_id = productForm["product_id"].value;
    fnChangeAction("?#product" + product_id);
    if (productForm["classcategory_id1"]) {
        fnSetVal("classcategory_id1", productForm["classcategory_id1"].value);
    }
    if (productForm["classcategory_id2"]) {
        fnSetVal("classcategory_id2", productForm["classcategory_id2"].value);
    }
    //fnSetVal("quantity", productForm["quantity"].value);
    fnSetVal("product_id", productForm["product_id"].value);
    fnSetVal("product_class_id", productForm["product_class_id"].value);
    fnSetVal("regular_flg", regular_flg);
    fnSubmit();
}
//]]>
</script>

<!--▼CONTENTS-->
<div id="pankuzu">
	<!--{$TopicPath}-->
</div>
<section id="product_list">
    <form name="form1" id="form1" method="get" action="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="<!--{$mode|h}-->" />
        <input type="hidden" name="category_id" value="<!--{$arrSearchData.category_id|h}-->" />
        <input type="hidden" name="brand_id" value="<!--{$arrSearchData.brand_id|h}-->" />
        <input type="hidden" name="maker_id" value="<!--{$arrSearchData.maker_id|h}-->" />
        <input type="hidden" name="name" value="<!--{$arrSearchData.name|h}-->" />
        <input type="hidden" name="orderby" value="<!--{$orderby|h}-->" />
        <input type="hidden" name="disp_number" value="<!--{$disp_number|h}-->" />
        <input type="hidden" name="pageno" value="<!--{$tpl_pageno|h}-->" />
        <input type="hidden" name="product_id" value="" />
        <input type="hidden" name="classcategory_id1" value="" />
        <input type="hidden" name="classcategory_id2" value="" />
        <input type="hidden" name="product_class_id" value="" />
        <input type="hidden" name="quantity" value="1" />
        <input type="hidden" name="rnd" value="<!--{$tpl_rnd|h}-->" />
        <input type="hidden" name="regular_flg" id="regular_flg" value="" />
    </form>

	<!--{assign var=stClass value="spNaked"}-->
	<!--{if $arrBrand.sp_free_space3}-->
		<!--{$arrBrand.sp_free_space3}-->
	<!--{else}-->
	<h2 class="<!--{$stClass}-->"><!--{$arrBrand.brand_name|h}--></h2>
	<!--{/if}-->

	<!--{if $arrBrand.sp_comment}-->
	<!--{$arrBrand.sp_comment}-->
	<!--{/if}-->

	<!--{$arrBrand.sp_free_space4}-->

	<!--{foreach from=$arrChildBrand item=childBrand name=arrChildBrand}-->

	    <!--{if $tpl_child_brand}-->
		    <!--{if $childBrand.sp_free_space1}-->
			    <!--{$childBrand.sp_free_space1}-->
		    <!--{else}-->

<table width="100%" cellspacing="0" cellpadding="1" bordercolor="#dfedf5" border="0" bgcolor="#dfedf5" class="seriesTitle"><tr><th align="left"><h3><font size="-1"><span><!--{$childBrand.brand_name|h}--></span></font></h3></th></tr></table>
		    <!--{/if}-->
	    <!--{/if}-->

	<!--{assign var=brand_id value=$childBrand.brand_id}-->
	<!--{foreach from=$arrBrandProduct[$brand_id] item=arrProduct name=arrProducts}-->

<div id="GoodsList">
	<table class="goodsList">
    <!--{assign var=id value=$arrProduct.product_id}-->
    <!--{assign var=arrErr value=$arrProduct.arrErr}-->
    <!--▼商品-->
	<tr>
		<td class="none">&nbsp;</td>
		<td class="goodsInfo">
		<form name="product_form<!--{$id|h}-->" action="?" onsubmit="return false;">
		<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
		<input type="hidden" name="product_id" value="<!--{$id|h}-->" />
		<input type="hidden" name="product_class_id" id="product_class_id<!--{$id|h}-->" value="<!--{$tpl_product_class_id[$id]}-->" />
			<div class="linkbox clearfix">
				<dl>
					<!--★商品名★-->
					<dt><!--{if isset($arrProduct.disp_name)}--><!--{$arrProduct.disp_name|h}--><!--{else}--><!--{$arrProduct.name|h}--><!--{/if}--></dt>
					<!--★画像★-->
					<dd class="img"><img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrProduct.main_list_image|sfNoImageMainList|h}-->&amp;width=80&amp;height=80"  alt="<!--{$arrProduct.name|h}-->" width="50" /></dd>
					<!--★商品コメント★-->
					<dd class="text"><!--{$arrProduct.main_list_comment}--></dd>
					<!--★商品価格★-->
					<dd class="price">
						<!--{* ★価格★ *}-->
						<!--{if strlen($tpl_customer_kbn) == null || $tpl_customer_kbn == $smarty.const.CUSTOMER_KBN_NORMAL}-->
							￥<!--{if $arrProduct.price01_min == $arrProduct.price01_max}--><!--{$arrProduct.price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
							<!--{else}--><!--{$arrProduct.price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～<!--{$arrProduct.price01_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
							<!--{/if}-->(税込)
							<!--{* ★社員価格★ *}-->
							<!--{elseif $tpl_customer_kbn == $smarty.const.CUSTOMER_KBN_EMPLOYEE }-->
								￥<!--{if $arrProduct.price02_min == $arrProduct.price02_max}--><!--{$arrProduct.price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
							<!--{else}--><!--{$arrProduct.price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～<!--{$arrProduct.price02_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
							<!--{/if}-->(税込)
						<!--{/if}-->
					</dd>
				</dl>
				<p class="fullstory"><a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrProduct.product_id|u}-->" name="product<!--{$arrProduct.product_id}-->"><!--{if isset($arrProduct.disp_name)}--><!--{$arrProduct.disp_name|h}--><!--{else}--><!--{$arrProduct.name|h}--><!--{/if}-->の詳細を見る</a></p>

			</div>
			</form>
		</td>

        <!--{if $tpl_stock_find[$id]}-->
        <!--{* ▼在庫あり *}-->

            <!--{if strlen($arrProduct.sale_end_date) == 0 || $arrProduct.sale_end_date >= $smarty.now|date_format:"%Y-%m-%d"}-->
            <!--{* ▼販売期間中 *}-->

            <!--★定期購入★-->
            <!--{if $tpl_customer_kbn != $smarty.const.CUSTOMER_KBN_EMPLOYEE && $arrProduct.teiki_flg != 0}-->
            <td class="goodsCart" style="border:1px solid #41A61E;" onclick="fnInCart(document.product_form<!--{$id|h}-->, '1'); return false;">
                <div class="linkbox" style="color:#41A61E;">
                    <img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_teiki.png" alt="カートに入れる" width="18" height="16"><br>
                    定期購入<br>する<br><br>
                </div>
                <!--{* 定期フラグを保持 *}-->
                <!--{foreach from=$tpl_teiki_flg key=key item=teiki_flg }-->
                <input type="hidden" id="teiki_flg_<!--{$id}-->_<!--{$key}-->" value="<!--{$teiki_flg}-->" />
                <!--{/foreach}-->
            </td>
            <!--{/if}-->

            <!--★カートに入れる★-->
            <td class="goodsCart" onclick="fnInCart(document.product_form<!--{$id|h}-->, ''); return false;"<!--{if $tpl_customer_kbn == $smarty.const.CUSTOMER_KBN_EMPLOYEE || $arrProduct.teiki_flg == 0}--> colspan="2"<!--{/if}-->>
                <div class="linkbox">
                    <img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_cart.png" alt="カートに入れる" width="18" height="16"><br>
                    <!--{if $arrProduct.sale_start_date && $arrProduct.sale_start_date > $smarty.now|date_format:"%Y-%m-%d 00:00:00"}-->
                        予約する
                    <!--{else if strlen($arrProduct.sale_end_date) == 0 || $arrProduct.sale_end_date >= $smarty.now|date_format:"%Y-%m-%d"}-->
                        カートに<br>入れる
                    <!--{/if}-->
                </div>
            </td>

            <td class="none">&nbsp;</td>

            <!--{* ▲販売期間中 *}-->
            <!--{else}-->
            <!--{* ▼販売終了 *}-->
            <td class="goodsText" colspan="2">終了しました。</td>
            <!--{* ▲販売終了 *}-->
            <!--{/if}-->

        <!--{* ▲在庫あり *}-->
        <!--{else}-->
        <!--{* ▼在庫無し *}-->

            <td class="goodsText" colspan="2">
                <!--{foreach from=$tpl_stock_status_name key=key item=stock_status }-->
                <div class="linkbox"><!--{$stock_status}--></div>
                <!--{/foreach}-->
            </td>
        <!--{* ▲在庫無し *}-->
		<!--{/if}-->
	</tr>
    <!--▲商品-->
</table>
</div>
    <!--{/foreach}-->
<!--{$childBrand.sp_free_space2}-->
<!--{/foreach}-->
<!--{$arrBrand.sp_free_space5}-->

</section>
<!--▲CONTENTS-->

<script>
var pageNo = 2;
var url = "<!--{$smarty.const.P_DETAIL_URLPATH}-->";
var imagePath = "<!--{$smarty.const.IMAGE_SAVE_URLPATH|sfTrimURL}-->/";
var statusImagePath = "<!--{$TPL_URLPATH}-->";

function getProducts(limit) {
    $.mobile.pageLoading();
    var i = limit;
    //送信データを準備
    var postData = {};
    $('#form1').find(':input').each(function(){  
        postData[$(this).attr('name')] = $(this).val();  
    });
    postData["mode"] = "json";
    postData["pageno"] = pageNo;

    $.ajax({
        type: "POST",
           data: postData,
           cache: false,
           dataType: "json",
           error: function(XMLHttpRequest, textStatus, errorThrown){
            alert(textStatus);
            $.mobile.pageLoading(true);
           },
           success: function(result){
           
            var productStatus = result.productStatus;
             for (var j = 0; j < i; j++) {
                 if (result[j] != null) {
                     var product = result[j];
                     var productHtml = "";
                    var maxCnt = $(".list_area").length - 1;
                    var productEl = $(".list_area").get(maxCnt);
                    productEl = $(productEl).clone(true).insertAfter(productEl);
                    maxCnt++;
                    
                     //商品写真をセット
                     $($(".list_area .listphoto img").get(maxCnt)).attr({
                                                                 src: imagePath + product.main_list_image, 
                                                                 alt: product.name
                                                             });

                     //ステータスをセット
                     var statusAreaEl = $($(".list_area div.statusArea").get(maxCnt));
                    //ステータスの削除
                    statusAreaEl.empty();

                     if (productStatus[product.product_id] != null) {
                         var statusEl = '<ul class="status_icon">';
                         var statusCnt = productStatus[product.product_id].length;
                         for (var k = 0; k < statusCnt; k++) {
                             var status = productStatus[product.product_id][k];
                             var statusImgEl = '<li>' + status.status_name + '</li>' + "\n";
                             statusEl += statusImgEl;
                         }
                         statusEl += "</ul>";
                         statusAreaEl.append(statusEl);
                     }
                     
                     //商品名をセット
                     $($(".list_area a.productName").get(maxCnt)).text(product.name);
                     $($(".list_area a.productName").get(maxCnt)).attr("href", url + product.product_id);
                     
                     //販売価格をセット
                     var price = $($(".list_area span.price").get(maxCnt));
                    //販売価格をクリア
                     price.empty();
                     var priceVale = "";
                    //販売価格が範囲か判定
                     if (product.price02_min == product.price02_max) {
                         priceVale = product.price02_min_tax_format + '円';
                     } else {
                         priceVale = product.price02_min_tax_format + '～' + product.price02_max_tax_format + '円';
                     }
                     price.append(priceVale);
                     
                     //コメントをセット
                     $($(".list_area .listcomment").get(maxCnt)).text(product.main_list_comment);

                 }
             }
             pageNo++;

             //すべての商品を表示したか判定
             if (parseInt($("#productscount").text()) <= $(".list_area").length) {
                 $("#btn_more_product").hide();
             }
             $.mobile.pageLoading(true);
           }
});
}
</script>
