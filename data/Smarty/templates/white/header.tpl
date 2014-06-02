<!--{* -*- coding: utf-8-unix; -*- *}-->
<!--▼HEADER-->
<div id="header" class="clearfix">

	<h1 id="logo_hd"><a href="<!--{$smarty.const.ROOT_URLPATH}-->"><img src="<!--{$TPL_URLPATH}-->img/common/img_logo.gif" alt="SOYAFARM CLUB　自然の恵み、大豆をもっと暮らしの中に" width="169" height="58"></a></h1>
	<p id="freecall"><img src="<!--{$TPL_URLPATH}-->img/common/img_tel.gif" alt="お電話でのご注文はこちら　【通話料無料】0120-39-3009" width="298" height="43"></p>
		<dl id="fontsize">
		<dt><img src="<!--{$TPL_URLPATH}-->img/common/txt_fontsize.gif" alt="文字サイズ" width="67" height="13" /></dt>
		<dd class="fs-s"><a href="javascript:void(0);" onclick="setActiveStyleSheet('s'); return false;" onkeypress="setActiveStyleSheet('s'); return false;"><img src="<!--{$TPL_URLPATH}-->img/common/bt_font-s.gif" alt="文字サイズ：小" width="28" height="28" class="btn" /></a></dd>
		<dd class="fs-s_ac"><a href="javascript:void(0);" onclick="setActiveStyleSheet('s'); return false;" onkeypress="setActiveStyleSheet('s'); return false;"><img src="<!--{$TPL_URLPATH}-->img/common/bt_font-s-on.gif" alt="文字サイズ：小" width="28" height="28" /></a></dd>
		<dd class="fs-m"><a href="javascript:void(0);" onclick="setActiveStyleSheet('m'); return false;" onkeypress="setActiveStyleSheet('m'); return false;"><img src="<!--{$TPL_URLPATH}-->img/common/bt_font-m.gif" alt="文字サイズ：中" width="28" height="28" class="btn" /></a></dd>
		<dd class="fs-m_ac"><a href="javascript:void(0);" onclick="setActiveStyleSheet('m'); return false;" onkeypress="setActiveStyleSheet('m'); return false;"><img src="<!--{$TPL_URLPATH}-->img/common/bt_font-m-on.gif" alt="文字サイズ：中" width="28" height="28" /></a></dd>
		<dd class="fs-l"><a href="javascript:void(0);" onclick="setActiveStyleSheet('l'); return false;" onkeypress="setActiveStyleSheet('l'); return false;"><img src="<!--{$TPL_URLPATH}-->img/common/bt_font-l.gif" alt="文字サイズ：大" width="28" height="28" class="btn" /></a></dd>
		<dd class="fs-l_ac"><a href="javascript:void(0);" onclick="setActiveStyleSheet('l'); return false;" onkeypress="setActiveStyleSheet('l'); return false;"><img src="<!--{$TPL_URLPATH}-->img/common/bt_font-l-on.gif" alt="文字サイズ：大" width="28" height="28" /></a></dd>
		</dl>
<!--{*
		<div class="gNavS">
			<div class="sagasu">
				<form name="search_form" id="search_form" method="get" action="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php">
					<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
					<input type="hidden" name="mode" value="search" />
					<select name="category_id" class="tree">
						<option label="カテゴリ指定なし" value="">カテゴリ指定なし</option>
						<!--{html_options options=$arrCatList}-->
					</select>
				   <!--{if $arrBrandList}-->
						<select name="brand_id" class="brand">
							<option label="ブランド指定なし" value="">ブランド指定なし</option>
							<!--{html_options options=$arrBrandList}-->
						</select>
					<!--{/if}-->
				   <!--{if $arrMakerList}-->
						<select name="maker_id" class="maker">
							<option label="メーカー指定なし" value="">メーカー指定なし</option>
							<!--{html_options options=$arrMakerList}-->
						</select>

					<!--{/if}-->
					<input type="text" name="name" class="search" maxlength="50" value="<!--{$smarty.get.name|h}-->" />
					<a href="javascript:void(0);" onclick="document.search_form.submit();return false;" class="submitBtn"><img src="<!--{$TPL_URLPATH}-->img/rohto/gnav_btn_kensaku.gif" class="swp" /></a>
				</form>
			</div>
		</div>

		<dl class="gNav">
			<dt><a href="<!--{$smarty.const.ROOT_URLPATH}-->event/"><img src="<!--{$TPL_URLPATH}-->img/rohto/gnav_event.gif" alt="イベント＆特集" width="162" height="49" class="swp"></a></dt>
			<dt class="gNavCartBtn"><a href="<!--{$smarty.const.CART_URLPATH}-->"><img src="<!--{$TPL_URLPATH}-->img/rohto/gnav_cart.gif" alt="お買い物カゴを見る" width="154" height="49" class="swp">
<!--{if $arrCartList.0.TotalQuantity}-->
<span class="cartCnt"><!--{$arrCartList.0.TotalQuantity|number_format|default:0}--></span>
<!--{/if}--></a></dt>
		</dl>

		<div class="gNavKW">
			<dl>
				<dt><img src="<!--{$TPL_URLPATH}-->img/rohto/gnav_hot.gif" alt="ホットワード" width="63" height="10"></dt>
				<dd><a href="<!--{$smarty.const.ROOT_URLPATH}-->products/senobic.php">セノビック</a></dd>
				<dd><a href="<!--{$smarty.const.ROOT_URLPATH}-->products/brand.php?brand_code=koujihada">糀</a></dd>
				<dd><a href="<!--{$smarty.const.ROOT_URLPATH}-->products/brand.php?brand_code=1chouko">乳酸菌</a></dd>
				<dd><a href="<!--{$smarty.const.ROOT_URLPATH}-->products/brand.php?brand_code=1chouko">1兆個</a></dd>
				<dd><a href="<!--{$smarty.const.ROOT_URLPATH}-->category/beauty.php">美容 スキンケア</a></dd>
			</dl>
		</div><!--／gNavKW-->

*}-->

</div>

<!--{* ▼HeaderInternal COLUMN*}-->
<!--{if $arrPageLayout.HeaderInternalNavi|@count > 0}-->
<div id="header_wrap">
	<div id="header" class="clearfix">
		<div id="header_utility">
			<div id="headerInternalColumn">
				<!--{* ▼上ナビ *}-->
				<!--{foreach key=HeaderInternalNaviKey item=HeaderInternalNaviItem from=$arrPageLayout.HeaderInternalNavi}-->
					<!-- ▼<!--{$HeaderInternalNaviItem.bloc_name}--> -->
					<!--{if $HeaderInternalNaviItem.php_path != ""}-->
						<!--{include_php file=$HeaderInternalNaviItem.php_path items=$HeaderInternalNaviItem}-->
					<!--{else}-->
						<!--{include file=$HeaderInternalNaviItem.tpl_path items=$HeaderInternalNaviItem}-->
					<!--{/if}-->
					<!-- ▲<!--{$HeaderInternalNaviItem.bloc_name}--> -->
				<!--{/foreach}-->
				<!--{* ▲上ナビ *}-->
			</div>
		</div>
	</div>
</div>
<!--{/if}-->
<!--{* ▲HeaderInternal COLUMN*}-->
<!--▲HEADER-->

