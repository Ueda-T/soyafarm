<!--{* -*- coding: utf-8-unix; -*- *}-->
<form name="form1" id="form1" method="post" action="?" enctype="multipart/form-data">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<!--{foreach key=key item=item from=$arrSearchHidden}-->
    <!--{if is_array($item)}-->
        <!--{foreach item=c_item from=$item}-->
        <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
        <!--{/foreach}-->
    <!--{else}-->
        <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
    <!--{/if}-->
<!--{/foreach}-->
<input type="hidden" name="mode" value="" />
<input type="hidden" name="promotion_cd" value="<!--{$arrForm.promotion_cd|h}-->" />
<!--{foreach key=key item=item from=$arrForm.arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
<!--{/foreach}-->
<div id="products" class="contents-main">
    <h2>プロモーションマスタ詳細</h2>
    <table class="form">
        <tr>
            <th>プロモーションコード</th>
            <td><!--{$arrForm.promotion_cd|h}--></td>
        </tr>
        <tr>
            <th>プロモーション名</th>
            <td><!--{$arrForm.promotion_name|h}--></td>
        </tr>
        <tr>
            <th>プロモーション区分</th>
            <td><!--{$arrPromotionKbn[$arrForm.promotion_kbn]|h}--></td>
        </tr>
        <tr>
            <th>有効区分</th>
            <td><!--{$arrValidKbn[$arrForm.valid_kbn]|h}--></td>
        </tr>
        <tr>
            <th>有効期間</th>
            <td>
				<!--{if $arrForm.valid_from == '' && $arrForm.valid_to == ''}-->
				    &nbsp;
				<!--{else}-->
				    <!--{$arrForm.valid_from|h}--> 〜 <!--{$arrForm.valid_to|h}-->
				<!--{/if}-->
			</td>
        </tr>
        <tr>
            <th>イベント</th>
            <td>
				<select name="promotion_media" size="5" style="width: 100%;">
                <!--{html_options options=$arrMedia}-->
				</select>
			</td>
        </tr>
        <tr>
            <th>受注区分</th>
            <td>
				<select name="promotion_order_kbn" size="5" style="width: 100%;">
                <!--{html_options options=$arrOrderKbn}-->
				</select>
			</td>
        </tr>
        <tr>
            <th>購入商品</th>
            <td>
				<select name="promotion_order_product" size="5" style="width: 100%;">
                <!--{html_options options=$arrOrderProduct}-->
				</select>
			</td>
        </tr>
        <tr>
            <th>購入数量</th>
            <td>
				<!--{if $arrForm.quantity_from == '' && $arrForm.quantity_to == ''}-->
				    &nbsp;
				<!--{else}-->
				    <!--{$arrForm.quantity_from|h}--> 〜 <!--{$arrForm.quantity_to|h}-->
				<!--{/if}-->
			</td>
        </tr>
        <tr>
            <th>数量集計区分</th>
            <td><!--{$arrQuantityKbn[$arrForm.quantity_kbn]|h}--></td>
        </tr>
        <tr>
            <th>購入金額（税込）</th>
            <td>
				<!--{if $arrForm.amount_from == '' && $arrForm.amount_to == ''}-->
				    &nbsp;
				<!--{else}-->
				    <!--{$arrForm.amount_from|h}--> 〜 <!--{$arrForm.amount_to|h}-->
				<!--{/if}-->
			</td>
        </tr>
        <tr>
            <th>コース区分</th>
            <td><!--{$arrCourseKbn[$arrForm.course_kbn]|h}--></td>
        </tr>
        <tr>
            <th>購入回数</th>
            <td>
				<!--{if $arrForm.count_from == '' && $arrForm.count_to == ''}-->
				    &nbsp;
				<!--{else}-->
				    <!--{$arrForm.count_from|h}--> 〜 <!--{$arrForm.count_to|h}-->
				<!--{/if}-->
			</td>
        </tr>
        <tr>
            <th>値引商品</th>
            <td>
				<select name="promotion_discount_product" size="5" style="width: 100%;">
                <!--{html_options options=$arrDiscountProduct}-->
				</select>
			</td>
        </tr>
        <tr>
            <th>送料区分</th>
            <td><!--{$arrDelivFeeKbn[$arrForm.deliv_fee_kbn]|h}--></td>
        </tr>
        <tr>
            <th>同梱商品</th>
            <td>
				<select name="promotion_include_product" size="5" style="width: 100%;">
                <!--{html_options options=$arrIncludeProduct}-->
				</select>
			</td>
        </tr>
        <tr>
            <th>適用回数</th>
            <td>
				<!--{if $arrForm.use_count == ''}-->
				    &nbsp;
				<!--{else}-->
				    <!--{$arrForm.use_count|h}--> 回
				<!--{/if}-->
			</td>
        </tr>
    </table>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_PRODUCTS_URLPATH}-->promotion_search.php'); fnModeSubmit('search', '', ''); return false;"><span class="btn-prev">検索画面に戻る</span></a></li>
        </ul>
    </div>
</div>
</form>
