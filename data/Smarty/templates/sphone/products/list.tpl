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

	<h2 class="spNaked"><!--{$tpl_subtitle|h}--></h2>

<!--{*
    <p class="intro clear"><span class="attention"><span id="productscount"><!--{$tpl_linemax}--></span>件</span>の商品がございます。</p>

<!--▼ページナビ(本文)-->
<section class="pagenumberarea clearfix">
 <ul>
    <!--{if $orderby != 'price'}-->
        <li><a href="javascript:fnChangeOrderby('price');" rel="external">価格順</a></li>
    <!--{else}-->
        <li class="on_number">価格順</li>
    <!--{/if}-->
    <!--{if $orderby != "date"}-->
        <li><a href="javascript:fnChangeOrderby('date');" rel="external">新着順</a></li>
    <!--{else}-->
        <li class="on_number">新着順</li>
    <!--{/if}-->
 </ul>
</section>
<!--▲ページナビ(本文)-->
*}-->

<div id="GoodsList">
	<table class="goodsList">
<!--{foreach from=$arrProducts item=arrProduct name=arrProducts}-->
    <!--{assign var=id value=$arrProduct.product_id}-->
    <!--{assign var=arrErr value=$arrProduct.arrErr}-->
    <!--▼商品-->
	<tr>
		<td class="goodsInfo" colspan="2">
		<form name="product_form<!--{$id|h}-->" action="?" onsubmit="return false;">
		<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
		<input type="hidden" name="product_id" value="<!--{$id|h}-->" />
		<input type="hidden" name="product_class_id" id="product_class_id<!--{$id|h}-->" value="<!--{$tpl_product_class_id[$id]}-->" />
			<div class="linkbox clearfix">
				<dl>
					<!--★商品名★-->
					<dt><!--{if isset($arrProduct.disp_name)}--><!--{$arrProduct.disp_name|h}--><!--{else}--><!--{$arrProduct.name|h}--><!--{/if}--><img src="<!--{$TPL_URLPATH}-->img/rohto/icon_link08.png" alt="" width="11" height="11"></dt>
					<!--★画像★-->
					<dd class="img"><img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH|sfTrimURL}-->/<!--{$arrProduct.main_list_image|sfNoImageMainList|h}-->" alt="<!--{$arrProduct.name|h}-->" class="picture" width="65" /></dd>
					<!--★商品コメント★-->
					<dd class="text"><!--{$arrProduct.main_list_comment}--></dd>
					<!--★商品価格★-->
					<dd class="price">
						<!--{* ★価格★ *}-->
						<strong><!--{$arrProduct.price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}--></strong>円(税込)<br />
						(税抜<strong><!--{$arrProduct.price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}--></strong>円)
						
						
					</dd>
				</dl>
				<p class="fullstory"><a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrProduct.product_id|u}-->" name="product<!--{$arrProduct.product_id}-->"><!--{if isset($arrProduct.disp_name)}--><!--{$arrProduct.disp_name|h}--><!--{else}--><!--{$arrProduct.name|h}--><!--{/if}-->の詳細を見る</a></p>

			</div>
			</form>
		</td>
	</tr>
	<tr>
		<td class="bg0 alignC">
        <!--{if $tpl_stock_find[$id]}-->
        <!--{* ▼在庫あり *}-->

            <!--{if strlen($arrProduct.sale_end_date) == 0 || $arrProduct.sale_end_date >= $smarty.now|date_format:"%Y-%m-%d"}-->
            <!--{* ▼販売期間中 *}-->
			<ul class="cartBtn clearfix">
	            <!--★カートに入れる★-->
	            <!--{if $arrProduct.teiki_flg == $smarty.const.REGULAR_PURCHASE_FLG_ON }-->
	            <!--★定期購入★-->
	            <li class="btnTeiki" onclick="fnInCart(document.product_form<!--{$id|h}-->, '1'); return false;">
	                <img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_teiki.png" alt="定期購入する" width="18" height="16">
	                定期購入する
	            </li>
	            <!--{* 定期フラグを保持 *}-->
	            <!--{foreach from=$tpl_teiki_flg key=key item=teiki_flg }-->
	            <input type="hidden" id="teiki_flg_<!--{$id}-->_<!--{$key}-->" value="<!--{$teiki_flg}-->" />
	            <!--{/foreach}-->
				<!--{/if}-->
	            <li class="btnCart" onclick="fnInCart(document.product_form<!--{$id|h}-->, ''); return false;">
	                <img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_cart.png" alt="カートに入れる" width="18" height="16">
	                <!--{if $arrProduct.sale_start_date && $arrProduct.sale_start_date > $smarty.now|date_format:"%Y-%m-%d 00:00:00"}-->
	                    予約する
	                <!--{else if strlen($arrProduct.sale_end_date) == 0 || $arrProduct.sale_end_date >= $smarty.now|date_format:"%Y-%m-%d"}-->
	                    カゴに入れる
	                <!--{/if}-->
	            </li>
			</ul>
            <!--{* ▲販売期間中 *}-->
            <!--{else}-->
            <!--{* ▼販売終了 *}-->
            <p class="goodsText" colspan="2" style="white-space:normal;">終了しました。</p>
            <!--{* ▲販売終了 *}-->
            <!--{/if}-->

        <!--{* ▲在庫あり *}-->
        <!--{else}-->
        <!--{* ▼在庫無し *}-->

            <!--{foreach from=$tpl_stock_status_name key=key item=stock_status }-->
            <div class="linkbox"><!--{$stock_status}--></div>
            <!--{/foreach}-->
        <!--{* ▲在庫無し *}-->
		<!--{/if}-->
		</td>
	</tr>
    <!--▲商品-->
    
<!--{foreachelse}-->
	<tr>
		<td class="none">&nbsp;</td>
		<td>
			<!--{include file="frontparts/search_zero.tpl"}-->
		</td>
		<td class="none">&nbsp;</td>
	</tr>
<!--{/foreach}-->
</table>

<!--{* ▼ #343 ○件中 N～Y件表示 *}-->
<div class="NaviPage">
	<table cellspacing="0" cellpadding="0">
		<tr>
			<td><!--{$tpl_from_no}-->～<!--{$tpl_to_no}-->件/全<!--{$tpl_linemax}-->件</td>
		</tr>
	</table>
</div>
<!--{* ▲ #343 ○件中 N～Y件表示 *}-->

<div class="pagenumber_area">
	<ul class="navi"><!--{$tpl_strnavi}--></ul>
</div>

</div>
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
