<script src="<!--{$smarty.const.ROOT_URLPATH}-->js/products.js"></script>
<script src="<!--{$TPL_URLPATH}-->js/jquery.facebox/facebox.js"></script>
<script>//<![CDATA[
// 規格2に選択肢を割り当てる。
function fnSetClassCategories(form, classcat_id2_selected) {
    var $form = $(form);
    var product_id = $form.find('input[name=product_id]').val();
    var $sele1 = $form.find('select[name=classcategory_id1]');
    var $sele2 = $form.find('select[name=classcategory_id2]');
    setClassCategories($form, product_id, $sele1, $sele2, classcat_id2_selected);
}
$(function(){
    $('#detailphotoblock ul li').flickSlide({target:'#detailphotoblock>ul', duration:5000, parentArea:'#detailphotoblock', height: 200});
    $('#whobought_area ul li').flickSlide({target:'#whobought_area>ul', duration:5000, parentArea:'#whobought_area', height: 80});

    //お勧め商品のリンクを張り直し(フリックスライドによるエレメント生成後)
    $('#whobought_area li').biggerlink();
    //商品画像の拡大
    $('a.expansion').facebox({
        loadingImage : '<!--{$TPL_URLPATH}-->js/jquery.facebox/loading.gif',
        closeImage   : '<!--{$TPL_URLPATH}-->js/jquery.facebox/closelabel.png'
    });
});
//サブエリアの表示/非表示
var speed = 500;
var stateSub = 0;
function fnSubToggle(areaEl, imgEl) {
    areaEl.slideToggle(speed);
    if (stateSub == 0) {
        $(imgEl).attr("src", "<!--{$TPL_URLPATH}-->img/common/btn_plus.png");
        stateSub = 1;
    } else {
        $(imgEl).attr("src", "<!--{$TPL_URLPATH}-->img/common/btn_minus.png");
        stateSub = 0
    }
}
//お勧めエリアの表示/非表示
var statewhobought = 0;
function fnWhoboughtToggle(areaEl, imgEl) {
    areaEl.slideToggle(speed);
    if (statewhobought == 0) {
        $(imgEl).attr("src", "<!--{$TPL_URLPATH}-->img/common/btn_plus.png");
        statewhobought = 1;
    } else {
        $(imgEl).attr("src", "<!--{$TPL_URLPATH}-->img/common/btn_minus.png");
        statewhobought = 0
    }
}

// 定期カート挿入イベント
function fnAddProduct(regular_flg, objForm) {
    if (regular_flg != "") {
        objForm.regular_flg.value = regular_flg;
    } else {
        objForm.regular_flg.value = '0';
    }
    objForm.submit();
}
//]]>
</script>

<!--▼CONTENTS-->
<div id="pankuzu">
	<!--{$TopicPath}-->
</div>

<section id="GoodsDetail">
	<form name="form1" id="form1" method="post" action="<!--{$smarty.const.ROOT_URLPATH}-->products/detail.php" style="margin:0;padding:0;">

	<div class="goodsIntro">

		<!--{* ▼コメント１ *}-->
		<!--{if $arrProduct.sp_comment1}-->
		<div><!--{$arrProduct.sp_comment1}--></div>
		<!--{/if}-->
		<!--{* ▲コメント１ *}-->

		<!--★タイトル★-->
		<h1><!--{if isset($arrProduct.disp_name)}--><!--{$arrProduct.disp_name|h}--><!--{else}--><!--{$arrProduct.name|h}--><!--{/if}--></h1>

		<!--★詳細メインコメント★-->
		<p class="comment"><!--{$arrProduct.main_list_comment}--></p>

		<!--★画像★-->
		<div id="detailphotoblock_" class="mainImageInit goodsImg">
		    <ul>
		        <!--{assign var=key value="main_image"}-->
		        <li id="mainImage0">

		        <!--{* 画像の縦横倍率を算出 *}-->
		        <!--{assign var=detail_image_size value=200}-->
		        <!--{assign var=main_image_factor value=`$arrFile[$key].width/$detail_image_size`}-->
		        <!--{if $arrProduct.main_large_image|strlen >= 1}-->
		            <a rel="external" class="expansion" href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrProduct.main_large_image|h}-->" target="_blank">
		                <img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrProduct.main_image|h}-->" alt="<!--{$arrProduct.name|h}-->" width="<!--{$arrFile.main_image.width/$main_image_factor}-->" height="<!--{$arrFile.main_image.height/$main_image_factor}-->" /></a>
		        <!--{else}-->
		            <img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrProduct.main_image|h}-->" alt="<!--{$arrProduct.name|h}-->" width="<!--{$arrFile.main_image.width/$main_image_factor}-->" height="<!--{$arrFile.main_image.height/$main_image_factor}-->" />
		        <!--{/if}-->
		        </li>
		        <!--★サブ画像★-->
		        <!--{section name=cnt loop=$smarty.const.PRODUCTSUB_MAX}-->
		        <!--{assign var=sub_image_factor value=`$arrFile[$key].width/$detail_image_size`}-->
		        <!--{assign var=key value="sub_image`$smarty.section.cnt.index+1`"}-->
		        <!--{assign var=lkey value="sub_large_image`$smarty.section.cnt.index+1`"}-->
		        <!--{if $arrFile[$key].filepath != ""}-->
		            <li id="mainImage<!--{$smarty.section.cnt.index+1}-->">
		            <!--{if $arrProduct[$lkey] != ""}-->
		              <a rel="external" class="expansion" href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrProduct[$lkey]|h}-->" target="_blank">
		              <img src="<!--{$arrFile[$key].filepath|h}-->" alt="<!--{$arrProduct.name|h}-->" width="<!--{$arrFile[$key].width/$sub_image_factor}-->" height="<!--{$arrFile[$key].height/$sub_image_factor}-->" /></a>
		            <!--{else}-->
		              <img src="<!--{$arrFile[$key].filepath|h}-->" alt="<!--{$arrProduct.name|h}-->" width="<!--{$arrFile[$key].width/$sub_image_factor}-->" height="<!--{$arrFile[$key].height/$sub_image_factor}-->" />
		            <!--{/if}-->
		            </li>
		        <!--{/if}-->
		        <!--{/section}-->
		    </ul>
		</div>
    <!--▼商品ステータス-->
    <!--{assign var=ps value=$productStatus[$tpl_product_id]}-->
    <!--{if count($ps) > 0}-->

	<div align="center">
        <p class="goodsIcon">
        <!--{foreach from=$ps item=status}-->
            <img src="<!--{$TPL_URLPATH}--><!--{$arrSTATUS[$status].image|h}-->" alt="<!--{$arrSTATUS[$status].name|h}-->" id="icon<!--{$status}-->" />
        <!--{/foreach}-->
        </p>

		<a href="<!--{$smarty.const.ROOT_URLPATH}-->contents/icon.php"><font size="-2" color="#666666">表示アイコンについて</font></a>
	</div>
	<br>
    <!--{/if}-->
    <!--▲商品ステータス-->

<!--{*
        <!--★通常価格★-->
        <!--{if $arrProduct.price01_max > 0}-->
        <p class="normal_price">
        <span class="mini"><!--{$smarty.const.NORMAL_PRICE_TITLE}-->(税込)：</span>
        <span id="price01_default">
               <!--{if $arrProduct.price01_min == $arrProduct.price01_max}-->
               <!--{$arrProduct.price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
                 <!--{else}-->
                   <!--{$arrProduct.price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～<!--{$arrProduct.price01_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
                    <!--{/if}--></span>
        <span id="price01_dynamic"></span>円</p>
        <!--{/if}-->

        <!--★販売価格★-->
        <p class="sale_price"><span class="mini"><!--{$smarty.const.SALE_PRICE_TITLE}-->(税込)：</span>
        <span class="price"><span id="price02_default">
                <!--{if $arrProduct.price02_min == $arrProduct.price02_max}-->
                 <!--{$arrProduct.price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
                   <!--{else}-->
                     <!--{$arrProduct.price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～<!--{$arrProduct.price02_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
                       <!--{/if}-->
        </span><span id="price02_dynamic"></span>円</span></p>

        <!--★ポイント★-->
        <!--{if $smarty.const.USE_POINT !== false}-->
            <p class="sale_price"><span class="mini">ポイント：</span><span id="point_default">
                   <!--{if $arrProduct.price02_min == $arrProduct.price02_max}-->
                     <!--{$arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id|number_format}-->
                       <!--{else}-->
                        <!--{if $arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id == $arrProduct.price02_max|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id}-->
                          <!--{$arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id|number_format}-->
                            <!--{else}-->
                              <!--{$arrProduct.price02_min|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id|number_format}-->～<!--{$arrProduct.price02_max|sfPrePoint:$arrProduct.point_rate:$smarty.const.POINT_RULE:$arrProduct.product_id|number_format}-->
                                <!--{/if}-->
                                  <!--{/if}-->
            </span><span id="point_dynamic"></span>Pt</p>
        <!--{/if}-->
*}-->

        <!--{* ▼コメント２ *}-->
        <!--{if $arrProduct.sp_comment2}-->
        <div class="recommendCmt">
			<h2>こんな方におすすめ</h2>
			<!--{$arrProduct.sp_comment2}-->
        </div>
        <!--{/if}-->
        <!--{* ▲コメント２ *}-->

        <!--▼メーカーURL-->
        <!--{if $arrProduct.comment1|strlen >= 1}-->
        <p class="sale_price"><span class="mini">メーカーURL：</span><span>
            <a rel="external" href="<!--{$arrProduct.comment1|h}-->" target="_blank">
                <!--{$arrProduct.comment1|h}--></a>
        </span></p>
        <!--{/if}-->
        <!--▲メーカーURL-->

        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="cart" />
        <input type="hidden" name="product_id" value="<!--{$tpl_product_id}-->" />
        <input type="hidden" name="product_class_id" value="<!--{$tpl_product_class_id}-->" id="product_class_id" />
        <input type="hidden" name="favorite_product_id" value="" />
        <input type="hidden" name="regular_flg" id="regular_flg" value="" />
        
        <!--▼買い物かご-->
        <!--{if $tpl_stock_find}-->
    
            <!--{if $tpl_classcat_find1}-->
                <div class="cart_area">
                     <dl>
                        <!--▼規格1-->
                        <dt><!--{$tpl_class_name1|h}--></dt>
                        <dd>
                        <select name="classcategory_id1"
                            style="<!--{$arrErr.classcategory_id1|sfGetErrorColor}-->" 
                            class="data-role-none">
                            <!--{html_options options=$arrClassCat1 selected=$arrForm.classcategory_id1.value}-->
                        </select>
                        <!--{if $arrErr.classcategory_id1 != ""}-->
                            <br /><span class="attention">※ <!--{$tpl_class_name1}-->を入力して下さい。</span>
                        <!--{/if}-->
                        </dd>
                        <!--▲規格1-->
            <!--{/if}-->

            <!--{if $tpl_classcat_find2}-->
                        <!--▼規格2-->
                        <dt><!--{$tpl_class_name2|h}--></dt>
                        <dd>
                        <select name="classcategory_id2"
                            style="<!--{$arrErr.classcategory_id2|sfGetErrorColor}-->"
                            class="data-role-none">
                        </select>
                        <!--{if $arrErr.classcategory_id2 != ""}-->
                            <br /><span class="attention">※ <!--{$tpl_class_name2}-->を入力して下さい。</span>
                        <!--{/if}-->
                        </dd>
                        <!--▲規格2-->
                     </dl>
                  </div>
            <!--{/if}-->

            <div class="goodsCart mb20">
		        <!--★通常価格★-->
		          <!--{if $arrProduct.price01_max > 0}-->
		          <p class="price">
		          <span>税込</span><strong><!--{$arrProduct.price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}--></strong><span>円</span><br />
		          <span>（税抜<strong><!--{$arrProduct.price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}--></strong>円）</span>
		          </p>
		          <!--{/if}-->

				<!--{*★数量★*}-->
				<table border="0" cellpadding="0" cellspacing="0" style="margin:0 auto;width:90%;">
					<tr>
                        <!--{* #346 販売終了後は表示しない *}-->
                        <!--{if strlen($arrProduct.sale_end_date) == 0 || $arrProduct.sale_end_date >= $smarty.now|date_format:"%Y-%m-%d"}-->
						<td width="60">
                        <select name="quantity" style="<!--{$arrErr.quantity|sfGetErrorColor}-->">
                            <!--{html_options options=$tpl_arrQuantity[$tpl_product_class_id] }-->
                        </select>
	                        <!--{if $arrErr.quantity != ""}-->
	                            <br /><span class="attention"><!--{$arrErr.quantity}--></span>
	                        <!--{/if}-->
						</td>
                        <!--{/if}-->

        <!--{else}-->
            <div class="goodsCart mb20">
        <!--{/if}-->

			        <!--★カートに入れる★-->
			        <!--{if $tpl_stock_find}-->

                        <!--{if $arrProduct.sale_start_date && $arrProduct.sale_start_date > $smarty.now|date_format:"%Y-%m-%d 00:00:00"}-->
			        	<td style="padding:0 0 0 5px;">
                            <ul class="cartBtn">
                            <!--{* 予約ボタン *}-->
                                <li class="btnCart">
				                <a rel="external" href="javascript:void(document.form1.submit());"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_cart.png" alt="予約する" width="18" height="16">
				                予約する</a>
                                </li>
                            </ul>
				            <div class="attention" id="cartbtn_dynamic"></div>
			            </td>

                        <!--{else}-->
                            <!--{* 販売終了日過ぎていない場合 *}-->
                            <!--{if strlen($arrProduct.sale_end_date) == 0 || $arrProduct.sale_end_date >= $smarty.now|date_format:"%Y-%m-%d"}-->
                            <td style="padding:0 0 0 5px;">
                            	<ul class="cartBtn">
                                <!--{if $arrProduct.teiki_flg == $smarty.const.REGULAR_PURCHASE_FLG_ON }-->
                                <li class="btnTeiki">
                                    <a rel="external" href="javascript:void(fnAddProduct('1', document.form1));"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_teiki.png" alt="定期購入する" width="18" height="16">
                                    定期購入する</a>
                                </li>
                                <div class="attention" id="cartbtn_dynamic"></div>
                                <!--{/if}-->

                                <li class="btnCart">
                                    <a rel="external" href="javascript:void(fnAddProduct('0', document.form1));"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_cart.png" alt="カゴに入れる" width="18" height="16">
                                    カゴに入れる</a>
                                </li>
                                </ul>
                                <div class="attention" id="cartbtn_dynamic"></div>

                            </td>
                            <!--{* 販売終了日過ぎた場合 *}-->
                            <!--{else}-->
                            終了しました
                            <!--{/if}-->
                        <!--{/if}-->

                        <!--{* 在庫切れ時の表示切り替え *}-->
                        <!--{foreach from=$tpl_stock_status_name key=key item=stock_status }-->
                        <div class="cart" id="cartbtn_stock_status<!--{$key}-->"><!--{$stock_status}--></div>
                        <!--{/foreach}-->

                        <!--{* 定期フラグを保持 *}-->
                        <!--{foreach from=$tpl_teiki_flg key=key item=teiki_flg }-->
                        <input type="hidden" id="teiki_flg_<!--{$tpl_product_id}-->_<!--{$key}-->" value="<!--{$teiki_flg}-->" />
                        <!--{/foreach}-->

			        </tr>
			        <!--{else}-->
			        	<td>
                            <!--{foreach from=$tpl_stock_status_name key=key item=stock_status }-->
                            <div class="cart" id="cartbtn_stock_status<!--{$key}-->"><!--{$stock_status}--></div>
                            <!--{/foreach}-->
			            </td>
			        </tr>
			        <!--{/if}-->
				</table>
        </div>
        <!--▲買い物かご-->

<!--{*
        <!--{if $tpl_login}-->
            <!--{if !$is_favorite}-->
                <div class="btn_favorite">
                    <p><a rel="external" href="javascript:void(0);" onclick="fnAddFavoriteSphone(<!--{$arrProduct.product_id|h}-->); return false;" class="btn_sub">お気に入りに追加</a></p>
                </div>
            <!--{else}-->
                <div class="btn_favorite">
                    <p>お気に入り登録済み</p>
                </div>
            <!--{/if}-->
        <!--{/if}-->
*}-->

		<div>
			<p class="naked mb20"><!--{$arrProduct.main_comment}--></p>
		</div>

        <!--{* ▼コメント３ *}-->
        <!--{if $arrProduct.sp_comment3}-->
        <div><!--{$arrProduct.sp_comment3}--></div>
        <!--{/if}-->
        <!--{* ▲コメント３ *}-->

        <!--{* ▼コメント４ *}-->
        <!--{if $arrProduct.sp_comment4}-->
        <div><!--{$arrProduct.sp_comment4}--></div>
        <!--{/if}-->
        <!--{* ▲コメント４ *}-->

        <!--{* ▼カートボタン表示４ *}-->
        <!--{if $arrProduct.sp_button4 == BUTTON_DISP_FLG_ON}-->
        <!--▼買い物かご-->
        <!--{if $tpl_stock_find}-->
    
            <!--{if $tpl_classcat_find1}-->
                <div class="cart_area">
                     <dl>
                        <!--▼規格1-->
                        <dt><!--{$tpl_class_name1|h}--></dt>
                        <dd>
                        <select name="classcategory_id1"
                            style="<!--{$arrErr.classcategory_id1|sfGetErrorColor}-->" 
                            class="data-role-none">
                            <!--{html_options options=$arrClassCat1 selected=$arrForm.classcategory_id1.value}-->
                        </select>
                        <!--{if $arrErr.classcategory_id1 != ""}-->
                            <br /><span class="attention">※ <!--{$tpl_class_name1}-->を入力して下さい。</span>
                        <!--{/if}-->
                        </dd>
                        <!--▲規格1-->
            <!--{/if}-->

            <!--{if $tpl_classcat_find2}-->
                        <!--▼規格2-->
                        <dt><!--{$tpl_class_name2|h}--></dt>
                        <dd>
                        <select name="classcategory_id2"
                            style="<!--{$arrErr.classcategory_id2|sfGetErrorColor}-->"
                            class="data-role-none">
                        </select>
                        <!--{if $arrErr.classcategory_id2 != ""}-->
                            <br /><span class="attention">※ <!--{$tpl_class_name2}-->を入力して下さい。</span>
                        <!--{/if}-->
                        </dd>
                        <!--▲規格2-->
                     </dl>
                  </div>
            <!--{/if}-->
                
            <div class="goodsCart mb20">
				<!--{*★数量★*}-->
				<table border="0" cellpadding="0" cellspacing="0" style="margin:0 auto;width:90%;">
					<tr>
						<td width="60">
<!--{*
<input type="number" name="quantity" value="<!--{$arrForm.quantity.value|default:1|h}-->" max="<!--{$smarty.const.INT_LEN}-->" style="<!--{$arrErr.quantity|sfGetErrorColor}-->" data-role="none" />
*}-->
                        <select name="quantity" style="<!--{$arrErr.quantity|sfGetErrorColor}-->">
                            <!--{html_options options=$tpl_arrQuantity[$tpl_product_class_id] }-->
                        </select>
	                        <!--{if $arrErr.quantity != ""}-->
	                            <br /><span class="attention"><!--{$arrErr.quantity}--></span>
	                        <!--{/if}-->
						</td>
        <!--{else}-->
            <div class="goodsCart">
        <!--{/if}-->

			        <!--★カートに入れる★-->
			        <!--{if $tpl_stock_find}-->

                        <!--{if $arrProduct.sale_start_date && $arrProduct.sale_start_date > $smarty.now|date_format:"%Y-%m-%d 00:00:00"}-->
			        	<td style="padding:0 0 0 5px;">
			        		<ul class="cartBtn">
                            <!--{* 予約ボタン *}-->
				            <li class="btnCart">
				                <a rel="external" href="javascript:void(document.form1.submit());"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_cart.png" alt="予約する" width="18" height="16">
				                予約する</a>
				            </li>
				            </ul>
				            <div class="attention" id="cartbtn_dynamic"></div>
			            </td>

                        <!--{else}-->
                            <!--{* 販売終了日過ぎていない場合 *}-->
                            <!--{if strlen($arrProduct.sale_end_date) == 0 || $arrProduct.sale_end_date >= $smarty.now|date_format:"%Y-%m-%d"}-->
                            <td style="padding:0 0 0 5px;">
			        		<ul class="cartBtn">
                                <!--{if $arrProduct.teiki_flg == $smarty.const.REGULAR_PURCHASE_FLG_ON }-->
                                <li class="btnTeiki">
                                    <a rel="external" href="javascript:void(fnAddProduct('1', document.form1));"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_teiki.png" alt="定期購入する" width="18" height="16">
                                    定期購入する</a>
                                <div class="attention" id="cartbtn_dynamic"></div>
                                </li>
                                <!--{/if}-->
                                <li class="btnCart">
                                    <a rel="external" href="javascript:void(fnAddProduct('0', document.form1));"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_cart.png" alt="カゴに入れる" width="18" height="16">
                                    カゴに入れる</a>
                                <div class="attention" id="cartbtn_dynamic"></div>
                                </li>
                            </ul>
                            </td>
                            <!--{* 販売終了日過ぎた場合 *}-->
                            <!--{else}-->
                            終了しました
                            <!--{/if}-->
                        <!--{/if}-->

                        <!--{* 在庫切れ時の表示切り替え *}-->
                        <!--{foreach from=$tpl_stock_status_name key=key item=stock_status }-->
                        <div class="cart" id="cartbtn_stock_status<!--{$key}-->"><!--{$stock_status}--></div>
                        <!--{/foreach}-->

                        <!--{* 定期フラグを保持 *}-->
                        <!--{foreach from=$tpl_teiki_flg key=key item=teiki_flg }-->
                        <input type="hidden" id="teiki_flg_<!--{$tpl_product_id}-->_<!--{$key}-->" value="<!--{$teiki_flg}-->" />
                        <!--{/foreach}-->

			        </tr>
			        <!--{else}-->
			        	<td>
                            <!--{foreach from=$tpl_stock_status_name key=key item=stock_status }-->
                            <div class="cart" id="cartbtn_stock_status<!--{$key}-->"><!--{$stock_status}--></div>
                            <!--{/foreach}-->
			            </td>
			        </tr>
			        <!--{/if}-->
				</table>
        </div>
        <!--▲買い物かご-->
        <!--{/if}-->
        <!--{* ▲カートボタン表示４ *}-->

</div>

		<h2 class="title">商品詳細</h2>

		<table cellspacing="0" cellpadding="0" class="detail">
			<tr>
				<th>商品名</th>
				<td><!--{if isset($arrProduct.disp_name)}--><!--{$arrProduct.disp_name|h}--><!--{else}--><!--{$arrProduct.name|h}--><!--{/if}--></td>
			</tr>
<!--{*
			<tr>
				<th>商品番号</th>
				<td><!--{if $arrProduct.product_code_min == $arrProduct.product_code_max}-->
		            <!--{$arrProduct.product_code_min|h}-->
		        <!--{else}-->
		            <!--{$arrProduct.product_code_min|h}-->～<!--{$arrProduct.product_code_max|h}-->
		        <!--{/if}--></td>
			</tr>
*}-->
			<!--{* ▼ブランド名 *}-->
<!--{*
			<!--{if $arrProduct.brand_name|strlen >= 1}-->
			<tr>
				<th>ブランド名</th>
				<td><!--{$arrProduct.brand_name|h}--></td>
			</tr>
			<!--{/if}-->
*}-->
			<!--{* ▲ブランド名 *}-->
			<!--{* ▼容量 *}-->
			<!--{if $arrProduct.capacity|strlen >= 1}-->
			<tr>
				<th>容量</th>
				<td><!--{$arrProduct.capacity|h}--></td>
			</tr>
			<!--{/if}-->
			<!--{* ▲容量 *}-->
<!--{*
			<tr>
				<th>価格(税込)</th>
				<td>￥
        <!--{if $tpl_customer_kbn != $smarty.const.CUSTOMER_KBN_EMPLOYEE}-->
        <!--{if $arrProduct.price01_min == $arrProduct.price01_max}--><!--{$arrProduct.price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}--><!--{else}--><!--{$arrProduct.price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～\<!--{$arrProduct.price01_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}--><!--{/if}-->
        <!--{else}-->
        <!--{if $arrProduct.price02_min == $arrProduct.price02_max}--><!--{$arrProduct.price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}--><!--{else}--><!--{$arrProduct.price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～\<!--{$arrProduct.price02_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}--><!--{/if}-->
        <!--{/if}-->
      </td>
			</tr>
*}-->
		</table>

<!--詳細ここまで-->

<!--▼サブエリアここから-->
<!--{if $arrProduct.sub_title1 != ""}-->
  <div class="title_box_sub clearfix">
    <h2>商品情報</h2>
     <!--{assign var=ckey value="sub_comment`$smarty.section.cnt.index+1`"}-->
      <span class="b_expand"><img src="<!--{$TPL_URLPATH}-->img/common/btn_minus.png" onclick="fnSubToggle($('#sub_area'), this);" alt=""></span>
       </div>
    <div id="sub_area">
        <!--{section name=cnt loop=$smarty.const.PRODUCTSUB_MAX}-->
            <!--{assign var=key value="sub_title`$smarty.section.cnt.index+1`"}-->
            <!--{if $arrProduct[$key] != ""}-->
            <!--▼サブ情報-->
           <div class="subarea clearfix">
            <!--★サブタイトル★-->
            <h3><!--{$arrProduct[$key]|h}--></h3>

            <!--★サブ画像★-->
            <!--{assign var=sub_image_size value=80}-->
            <!--{assign var=key value="sub_image`$smarty.section.cnt.index+1`"}-->
            <!--{assign var=lkey value="sub_large_image`$smarty.section.cnt.index+1`"}-->
            <!--{assign var=ckey value="sub_comment`$smarty.section.cnt.index+1`"}-->
            <!--{assign var=sub_image_factor value=`$arrFile[$key].width/$sub_image_size`}-->
            <!--{if $arrProduct[$key]|strlen >= 1}-->
                <p class="subphotoimg">
                <!--{if $arrProduct[$lkey]|strlen >= 1}-->
                    <a rel="external" class="expansion" href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrProduct[$lkey]|h}-->" target="_blank">
                    <img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrProduct.name|h}-->" width="<!--{$arrFile[$key].width/$sub_image_factor}-->" height="<!--{$arrFile[$key].height/$sub_image_factor}-->" />
                    </a>
                <!--{else}-->
                    <img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrProduct.name|h}-->" width="<!--{$arrFile[$key].width/$sub_image_factor}-->" height="<!--{$arrFile[$key].height/$sub_image_factor}-->" />
                <!--{/if}-->
                </p>
            <!--{/if}-->
            <!--★サブテキスト★-->
            <p class="subtext"><!--★サブテキスト★--><!--{$arrProduct[$ckey]|nl2br_html}--></p>
            </div>
            <!--{/if}-->
        <!--{/section}-->
    </div>
<!--{/if}-->
<!--サブエリアここまで-->


<!--▼この商品の関連商品-->
<!--{if $arrRecommend}-->
<div class="title_box_sub clearfix">
<h2 class="title">この商品の関連商品</h2>

<div id="GoodsList">
    <table class="goodsList">
    <!--{section name=cnt loop=$arrRecommend}-->
        <!--{if $arrRecommend[cnt].product_id}-->
        <tr>
          <td class="none">&nbsp;</td>
          <td id="mainImage1<!--{$smarty.section.cnt.index}-->" class="goodsInfo">
          <div class="linkbox clearfix">
          <dl>
          <dt><!--{$arrRecommend[cnt].name|h}--></a></dt>
          <dd class="img"><img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH|sfTrimURL}-->/<!--{$arrRecommend[cnt].main_list_image|sfNoImageMainList|h}-->" alt="<!--{$arrRecommend[cnt].name|h}-->" width="65" /></dd>
          <dd class="text"><!--{$arrRecommend[cnt].main_list_comment}--></dd>
          <dd class="price">
          <!--{if $tpl_customer_kbn != $smarty.const.CUSTOMER_KBN_EMPLOYEE}-->
              <!--{* ▼通常価格 *}-->         
              <!--{assign var=price01_min value=`$arrRecommend[cnt].price01_min`}-->
              <!--{assign var=price01_max value=`$arrRecommend[cnt].price01_max`}-->
              <p class="normal_price"><span class="price">
              <!--{if $price01_min == $price01_max}-->
                <!--{$price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
              <!--{else}-->
                <!--{$price01_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～<!--{$price01_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
              <!--{/if}--> 
              円</span></p>
              <!--{* ▲通常価格 *}-->         

          <!--{else}-->
              <!--{* ▼社員価格 *}-->         
              <!--{assign var=price02_min value=`$arrRecommend[cnt].price02_min`}-->
              <!--{assign var=price02_max value=`$arrRecommend[cnt].price02_max`}-->
              <h3><a rel="external" href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrRecommend[cnt].product_id|u}-->"><!--{$arrRecommend[cnt].name|h}--></a></h3>
              <p class="sale_price"><span class="price">
              <!--{if $price02_min == $price02_max}-->
                <!--{$price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
              <!--{else}-->
                <!--{$price02_min|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->～<!--{$price02_max|sfCalcIncTax:$arrSiteInfo.tax:$arrSiteInfo.tax_rule|number_format}-->
              <!--{/if}--> 
              円</span></p>
              <!--{* ▲社員価格 *}-->
          <!--{/if}-->
          </dd>
          </dl>
          <p class="fullstory"><a rel="external" href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$arrRecommend[cnt].product_id|u}-->"><!--{$arrRecommend[cnt].name|h}-->の詳細を見る</a></p>
          </div>
          </td>
          <td class="none">&nbsp;</td>
          </tr>
        <!--{/if}-->
    <!--{/section}-->
    </table>
</div>
<!--{/if}-->

<!--▲この商品の関連商品-->

        <!--{* ▼コメント５ *}-->
        <!--{if $arrProduct.sp_comment5}-->
        <div>
        <!--{$arrProduct.sp_comment5}-->
        </div>
        <!--{/if}-->
        <!--{* ▲コメント５ *}-->

        <!--{* ▼カートボタン表示５ *}-->
        <!--{if $arrProduct.sp_button5 == BUTTON_DISP_FLG_ON}-->
        <!--▼買い物かご-->
        <!--{if $tpl_stock_find}-->

			<div class="goodsBtm">
            <!--{if $tpl_classcat_find1}-->
                <div class="cart_area">
                     <dl>
                        <!--▼規格1-->
                        <dt><!--{$tpl_class_name1|h}--></dt>
                        <dd>
                        <select name="classcategory_id1"
                            style="<!--{$arrErr.classcategory_id1|sfGetErrorColor}-->" 
                            class="data-role-none">
                            <!--{html_options options=$arrClassCat1 selected=$arrForm.classcategory_id1.value}-->
                        </select>
                        <!--{if $arrErr.classcategory_id1 != ""}-->
                            <br /><span class="attention">※ <!--{$tpl_class_name1}-->を入力して下さい。</span>
                        <!--{/if}-->
                        </dd>
                        <!--▲規格1-->
            <!--{/if}-->

            <!--{if $tpl_classcat_find2}-->
                        <!--▼規格2-->
                        <dt><!--{$tpl_class_name2|h}--></dt>
                        <dd>
                        <select name="classcategory_id2"
                            style="<!--{$arrErr.classcategory_id2|sfGetErrorColor}-->"
                            class="data-role-none">
                        </select>
                        <!--{if $arrErr.classcategory_id2 != ""}-->
                            <br /><span class="attention">※ <!--{$tpl_class_name2}-->を入力して下さい。</span>
                        <!--{/if}-->
                        </dd>
                        <!--▲規格2-->
                     </dl>
                  </div>
            <!--{/if}-->
                
            <div class="goodsCart">

				<!--{*★数量★*}-->
				<table border="0" cellpadding="0" cellspacing="0" style="margin:0 auto;width:90%;">
					<tr>
						<td width="60">
<!--{*
<input type="number" name="quantity" value="<!--{$arrForm.quantity.value|default:1|h}-->" max="<!--{$smarty.const.INT_LEN}-->" style="<!--{$arrErr.quantity|sfGetErrorColor}-->" data-role="none" />
*}-->
                        <select name="quantity" style="<!--{$arrErr.quantity|sfGetErrorColor}-->">
                            <!--{html_options options=$tpl_arrQuantity[$tpl_product_class_id] }-->
                        </select>
	                        <!--{if $arrErr.quantity != ""}-->
	                            <br /><span class="attention"><!--{$arrErr.quantity}--></span>
	                        <!--{/if}-->
						</td>
        <!--{else}-->
            <div class="goodsCart">
        <!--{/if}-->

			        <!--★カートに入れる★-->
			        <!--{if $tpl_stock_find}-->

                        <!--{if $arrProduct.sale_start_date && $arrProduct.sale_start_date > $smarty.now|date_format:"%Y-%m-%d 00:00:00"}-->
			        	<td style="padding:0 0 0 5px;">
                            <ul class="cartBtn">
                            <!--{* 予約ボタン *}-->
                                <li class="btnCart">
				                <a rel="external" href="javascript:void(document.form1.submit());"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_cart.png" alt="予約する" width="18" height="16">
				                予約する</a>
                                </li>
                            </ul>
				            <div class="attention" id="cartbtn_dynamic"></div>
			            </td>

                        <!--{else}-->
                            <!--{* 販売終了日過ぎていない場合 *}-->
                            <!--{if strlen($arrProduct.sale_end_date) == 0 || $arrProduct.sale_end_date >= $smarty.now|date_format:"%Y-%m-%d"}-->
                            <td style="padding:0 0 0 5px;">
                            <ul class="cartBtn">
                                <!--{* 社員は定期購入不可 *}-->
                                <!--{if $arrProduct.teiki_flg == $smarty.const.REGULAR_PURCHASE_FLG_ON }-->
                                <li class="btnTeiki">
                                    <a rel="external" href="javascript:void(fnAddProduct('1', document.form1));"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_teiki.png" alt="定期購入する" width="18" height="16">
                                    定期購入する</a>
                                </li>
                                <div class="attention" id="cartbtn_dynamic"></div>
                                <!--{/if}-->
                                <li class="btnCart">
                                    <a rel="external" href="javascript:void(fnAddProduct('0', document.form1));"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_cart.png" alt="カゴに入れる" width="18" height="16">
                                    カゴに入れる</a>
                                </li>
                                </ul>
                                <div class="attention" id="cartbtn_dynamic"></div>

                            </td>
                            <!--{* 販売終了日過ぎた場合 *}-->
                            <!--{else}-->
                            終了しました
                            <!--{/if}-->
                        <!--{/if}-->

                        <!--{* 在庫切れ時の表示切り替え *}-->
                        <!--{foreach from=$tpl_stock_status_name key=key item=stock_status }-->
                        <div class="cart" id="cartbtn_stock_status<!--{$key}-->"><!--{$stock_status}--></div>
                        <!--{/foreach}-->

                        <!--{* 定期フラグを保持 *}-->
                        <!--{foreach from=$tpl_teiki_flg key=key item=teiki_flg }-->
                        <input type="hidden" id="teiki_flg_<!--{$tpl_product_id}-->_<!--{$key}-->" value="<!--{$teiki_flg}-->" />
                        <!--{/foreach}-->

			        </tr>
			        <!--{else}-->
			        	<td>
                            <!--{foreach from=$tpl_stock_status_name key=key item=stock_status }-->
                            <div class="cart" id="cartbtn_stock_status<!--{$key}-->"><!--{$stock_status}--></div>
                            <!--{/foreach}-->
			            </td>
			        </tr>
			        <!--{/if}-->
				</table>
        <!--▲買い物かご-->
        		</div>
        <!--{/if}-->
        <!--{* ▲カートボタン表示５ *}-->

<!--{*
<p class="mail"><a href="mailto:@?subject=<!--{if isset($arrProduct.disp_name)}--><!--{$arrProduct.disp_name|h}--><!--{else}--><!--{$arrProduct.name|h}--><!--{/if}-->&amp;body=<!--{if isset($arrProduct.disp_name)}--><!--{$arrProduct.disp_name|h}--><!--{else}--><!--{$arrProduct.name|h}--><!--{/if}-->%0d%0a<!--{$smarty.const.HTTP_URL}-->detail.php?product_id=<!--{$tpl_product_id}-->">この商品を友達に教える</a>
</p>
*}-->

<!--{*
<div class="goodsBtm">
	<!--★関連カテゴリ★-->
	<p class="list">
	<!--{section name=r loop=$arrRelativeCat}-->
	    <!--{section name=s loop=$arrRelativeCat[r]}-->
	    <a rel="external" href="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php?category_id=<!--{$arrRelativeCat[r][s].category_id}-->"><!--{$arrRelativeCat[r][s].category_name}--> カテゴリ一覧へ</a>
	    <!--{if !$smarty.section.s.last}--><!--{$smarty.const.SEPA_CATNAVI}--><!--{/if}-->
	    <!--{/section}--><br />
	<!--{/section}-->
	</p>
</div>
*}-->

<!--{*
<div class="btn_area">
<p><a href="javascript:history.back();" class="btn_more" data-rel="back">商品一覧に戻る</a></p>
</div>
*}-->
    </form>
</section>
<!--▲CONTENTS-->
