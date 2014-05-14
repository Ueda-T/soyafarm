<!--{* -*- coding: utf-8-unix; -*- *}-->
<script type="text/javascript">
<!--

$(function() {
    // タブ表示（tabs）
    $("#ui-tab").tabs();
    $("#ui-tab .ui-tabs-nav").removeClass('ui-corner-all');
});

// -->
</script>

<form name="form1" id="form1" method="post" action="?" enctype="multipart/form-data">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="complete" />
<!--{foreach key=key item=item from=$arrSearchHidden}-->
    <!--{if is_array($item)}-->
        <!--{foreach item=c_item from=$item}-->
            <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
        <!--{/foreach}-->
    <!--{else}-->
        <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
    <!--{/if}-->
<!--{/foreach}-->
<!--{foreach key=key item=item from=$arrForm}-->
    <!--{if $key == 'pc_product_status' ||
            $key == 'sp_product_status' ||
            $key == 'mb_product_status'}-->
        <!--{foreach item=statusVal from=$item}-->
            <input type="hidden" name="<!--{$key}-->[]" value="<!--{$statusVal|h}-->" />
        <!--{/foreach}-->
    <!--{else}-->
        <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
    <!--{/if}-->
<!--{/foreach}-->
<div id="products" class="contents-main">
    <div class="btn-area-head">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnModeSubmit('confirm_return','',''); return false;"><span class="btn-prev">前のページに戻る</span></a></li>
        </ul>
    </div>

    <table>
        <tr>
            <th>商品名</th>
            <td><!--{$arrForm.name|h}--></td>
        </tr>
        <tr>
            <th>表示用商品名</th>
            <td><!--{$arrForm.disp_name|h}--></td>
        </tr>

        <!--{if $arrForm.has_product_class != true}-->
        <tr>
            <th>商品コード</th>
            <td><!--{$arrForm.product_code|h}--></td>
        </tr>
		<!--{/if}-->

        <tr>
            <th>商品カテゴリ</th>
            <td>
                <!--{section name=cnt loop=$arrForm.arrCategoryId}-->
                    <!--{assign var=key value=$arrForm.arrCategoryId[cnt]}-->
                    <!--{$arrCatList[$key]|sfTrim}--><br />
                <!--{/section}-->
            </td>
        </tr>
        <tr>
            <th>公開・非公開</th>
            <td>
                <!--{$arrDISP[$arrForm.status]}-->
            </td>
        </tr>
        <tr>
            <th>掲載開始日</th>
            <td>
                <!--{$arrForm.disp_start_date|h}-->
            </td>
        </tr>
        <tr>
            <th>販売期間</th>
            <td><!--{if $arrForm.sale_start_date != ""}-->
                    <!--{$arrForm.sale_start_date|h}--> ～
                    <!--{$arrForm.sale_end_date|h}-->
                <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>ブランド</th>
            <td><!--{$arrForm.brand_code|h}--> <!--{$arrForm.brand_name|h}-->
            </td>
        </tr>
        <tr>
            <th>販売名</th>
            <td><!--{$arrForm.sales_name|h}--></td>
        </tr>
        <tr>
            <th>産直区分</th>
            <td><!--{$arrSANTYOKU[$arrForm.drop_shipment]|h}--></td>
        </tr>
        <tr>
            <th>配送区分1</th>
            <td><!--{$arrHAISOKBN_1[$arrForm.deliv_kbn1]|h}--></td>
        </tr>
        <tr>
            <th>配送区分2</th>
            <td><!--{$arrHAISOKBN_2[$arrForm.deliv_kbn2]|h}--></td>
        </tr>
        <tr>
            <th>メール便業者</th>
            <td><!--{$arrMAILDELIV[$arrForm.mail_deliv_id]|h}--></td>
        </tr>
        <tr>
            <th>成分表示</th>
            <td><!--{$arrCOMPONENT_FLG[$arrForm.component_flg]|h}--></td>
        </tr>
        <tr>
            <th>検索除外</th>
            <td><!--{$arrNOT_SEARCH_FLG[$arrForm.not_search_flg]|h}--></td>
        </tr>
        <tr>
            <th>定期購入</th>
            <td>
				<!--{$arrTEIKI_FLG[$arrForm.teiki_flg]|h}-->
				<!--{if $arrForm.course_cd != ""}-->
				（<!--{$arrCourseCd[$arrForm.course_cd]|h}--> <!--{$arrTodokeKbn[$arrForm.todoke_kbn]|h}-->）
				<!--{/if}-->
			</td>
        </tr>
        <tr>
            <th>サンプル区分</th>
            <td><!--{$arrSAMPLE_FLG[$arrForm.sample_flg]|h}--></td>
        </tr>
        <tr>
            <th>プレゼント区分</th>
            <td><!--{$arrPRESENT_FLG[$arrForm.present_flg]|h}--></td>
        </tr>
        <tr>
            <th>販売対象フラグ</th>
            <td><!--{$arrSELL_FLG[$arrForm.sell_flg]|h}--></td>
        </tr>
        <tr>
            <th>社員購入グループ</th>
            <td><!--{$arrEMPLOYEE_CD_NAME[$arrForm.employee_sale_cd]|h}--></td>
        </tr>

        <!--{if $arrForm.has_product_class != true}-->
        <tr>
            <th><!--{$smarty.const.NORMAL_PRICE_TITLE}-->(税込)</th>
			<td><!--{if strlen($arrForm.price01) >= 1}--><!--{$arrForm.price01|h}--> 円<!--{/if}--></td>
        </tr>
		<tr>
			<th><!--{$smarty.const.SALE_PRICE_TITLE}-->(税込)</th>
			<td><!--{if strlen($arrForm.price02) >= 1}--><!--{$arrForm.price02|h}--> 円<!--{/if}--></td>
		</tr>
		<tr>
			<th>在庫数</th>
			<td>
				<!--{if $arrForm.stock_unlimited == 1}-->
				無制限
				<!--{else}-->
				<!--{$arrForm.stock|h}-->
				<!--{/if}-->
			</td>
		</tr>
        <!--{/if}-->

		<tr>
			<th>完売時の表示文言</th>
			<td><!--{$arrForm.stock_status_name|h}--></td>
		</tr>
        <tr>
            <th>購入制限</th>
            <td>
                <!--{$arrForm.sale_minimum_number|h}--> ～
				<!--{if $arrForm.sale_limit == "0"}-->
				無制限
				<!--{else}-->
                <!--{$arrForm.sale_limit|h}-->
				<!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>メール便計算個数</th>
            <td><!--{$arrForm.deliv_judgment|h}--></td>
        </tr>
        <tr>
            <th>発送日目安</th>
            <td><!--{$arrDELIVERYDATE[$arrForm.deliv_date_id]|h}--></td>
        </tr>
        <tr>
            <th>容量</th>
            <td><!--{$arrForm.capacity|h}--></td>
        </tr>
        <tr>
            <th>検索ワード</th>
            <td><!--{$arrForm.comment3|h}--></td>
        </tr>
        <tr>
            <th>METAタグ</th>
            <td><!--{$arrForm.metatag|h}--></td>
        </tr>
        <tr>
            <th>一覧コメント</th>
            <td><!--{$arrForm.main_list_comment}--></td>
        </tr>
        <tr>
            <th>一覧画像</th>
            <td>
                <!--{assign var=key value="main_list_image"}-->
                <!--{if $arrForm.arrFile[$key].filepath != ""}-->
				<img src="<!--{$arrForm.arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" /><br />
                <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>詳細コメント</th>
            <td><!--{$arrForm.main_comment}--></td>
        </tr>
        <tr>
            <th>詳細画像</th>
            <td>
                <!--{assign var=key value="main_image"}-->
                <!--{if $arrForm.arrFile[$key].filepath != ""}-->
				<img src="<!--{$arrForm.arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" /><br />
                <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>詳細拡大画像</th>
            <td>
                <!--{assign var=key value="main_large_image"}-->
                <!--{if $arrForm.arrFile[$key].filepath != ""}-->
				<img src="<!--{$arrForm.arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" /><br />
                <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>カート案内画像</th>
            <td>
                <!--{assign var=key value="guide_image"}-->
                <!--{if $arrForm.arrFile[$key].filepath != ""}-->
				<img width="100%" src="<!--{$arrForm.arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" /><br />
                <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>カート案内画像（定期）</th>
            <td>
                <!--{assign var=key value="guide_image_teiki"}-->
                <!--{if $arrForm.arrFile[$key].filepath != ""}-->
				<img width="100%" src="<!--{$arrForm.arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" /><br />
                <!--{/if}-->
            </td>
        </tr>
	</table>

	<div id="ui-tab" style="margin-bottom: 20px;">
	    <ul>
			<li><a href="#pc-tab"><span>PCサイト用</span></a></li>
			<li><a href="#sp-tab"><span>スマホサイト用</span></a></li>
			<li><a href="#mb-tab"><span>モバイルサイト用</span></a></li>
		</ul>

		<div id="pc-tab">
		    <table>
			<tr>
				<th>アイコン</th>
				<td>
					<!--{foreach from=$arrForm.pc_product_status item=status}-->
					    <!--{if $status != ""}-->
					<img src="<!--{$TPL_URLPATH_DEFAULT}--><!--{$arrSTATUS_IMAGE[$status]}-->">
                        <!--{/if}-->
					<!--{/foreach}-->
				</td>
			</tr>
			<tr>
				<th>コメント１</th>
				<td><!--{$arrForm.pc_comment1}--></td>
			</tr>
			<tr>
				<th>コメント２</th>
				<td><!--{$arrForm.pc_comment2}--></td>
			</tr>
			<tr>
				<th>コメント３</th>
				<td><!--{$arrForm.pc_comment3}--></td>
			</tr>
			<tr>
				<th>コメント４</th>
				<td><!--{$arrForm.pc_comment4}--></td>
			</tr>
			<tr>
				<th>カートボタン表示４</th>
				<td><!--{$arrCART_BTN_FLG[$arrForm.pc_button4]|h}--></td>
			</tr>
			<tr>
				<th>コメント５</th>
				<td><!--{$arrForm.pc_comment5}--></td>
			</tr>
			<tr>
				<th>カートボタン表示５</th>
				<td><!--{$arrCART_BTN_FLG[$arrForm.pc_button5]|h}--></td>
			</tr>
		    </table>
		</div>

		<div id="sp-tab">
		    <table>
			<tr>
				<th>アイコン</th>
				<td>
					<!--{foreach from=$arrForm.sp_product_status item=status}-->
					    <!--{if $status != ""}-->
					<img src="<!--{$TPL_URLPATH_DEFAULT}--><!--{$arrSTATUS_IMAGE[$status]}-->">
                        <!--{/if}-->
					<!--{/foreach}-->
				</td>
			</tr>
			<tr>
				<th>コメント１</th>
				<td><!--{$arrForm.sp_comment1}--></td>
			</tr>
			<tr>
				<th>コメント２</th>
				<td><!--{$arrForm.sp_comment2}--></td>
			</tr>
			<tr>
				<th>コメント３</th>
				<td><!--{$arrForm.sp_comment3}--></td>
			</tr>
			<tr>
				<th>コメント４</th>
				<td><!--{$arrForm.sp_comment4}--></td>
			</tr>
			<tr>
				<th>カートボタン表示４</th>
				<td><!--{$arrCART_BTN_FLG[$arrForm.sp_button4]|h}--></td>
			</tr>
			<tr>
				<th>コメント５</th>
				<td><!--{$arrForm.sp_comment5}--></td>
			</tr>
			<tr>
				<th>カートボタン表示５</th>
				<td><!--{$arrCART_BTN_FLG[$arrForm.sp_button5]|h}--></td>
			</tr>
		    </table>
		</div>

		<div id="mb-tab">
		    <table>
			<tr>
				<th>アイコン</th>
				<td>
					<!--{foreach from=$arrForm.mb_product_status item=status}-->
					    <!--{if $status != ""}-->
					<img src="<!--{$TPL_URLPATH_DEFAULT}--><!--{$arrSTATUS_IMAGE[$status]}-->">
                        <!--{/if}-->
					<!--{/foreach}-->
				</td>
			</tr>
			<tr>
				<th>コメント１</th>
				<td><!--{$arrForm.mb_comment1}--></td>
			</tr>
			<tr>
				<th>コメント２</th>
				<td><!--{$arrForm.mb_comment2}--></td>
			</tr>
			<tr>
				<th>コメント３</th>
				<td><!--{$arrForm.mb_comment3}--></td>
			</tr>
			<tr>
				<th>コメント４</th>
				<td><!--{$arrForm.mb_comment4}--></td>
			</tr>
			<tr>
				<th>コメント５</th>
				<td><!--{$arrForm.mb_comment5}--></td>
			</tr>
		    </table>
		</div>
	</div>

    <!--{* オペビルダー用 *}-->
	<!--{if "sfViewAdminOpe"|function_exists === TRUE}-->
        <!--{include file=`$smarty.const.MODULE_REALDIR`mdl_opebuilder/admin_ope_view.tpl}-->
    <!--{/if}-->

    <!--{if $smarty.const.OPTION_RECOMMEND == 1}-->
	<table>
        <!--▼関連商品-->
        <!--{section name=cnt loop=$smarty.const.RECOMMEND_PRODUCT_MAX}-->
            <!--{assign var=recommend_no value="`$smarty.section.cnt.iteration`"}-->
    <tr>
		<th>関連商品(<!--{$smarty.section.cnt.iteration}-->)<br />
			<!--{if $arrRecommend[$recommend_no].product_id|strlen >= 1}-->
			<img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrRecommend[$recommend_no].main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65" alt="<!--{$arrRecommend[$recommend_no].name|h}-->">
			<!--{/if}-->
		</th>
		<td>
			<!--{if $arrRecommend[$recommend_no].product_id|strlen >= 1}-->
			商品コード:<!--{$arrRecommend[$recommend_no].product_code_min}--><br />
			商品名:<!--{$arrRecommend[$recommend_no].name|h}--><br />
			コメント:<br />
			<!--{$arrRecommend[$recommend_no].comment|nl2br_html}-->
			<!--{/if}-->
		</td>
	</tr>
	    <!--{/section}-->
	    <!--▲関連商品-->
    </table>
	<!--{/if}-->

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnModeSubmit('confirm_return','',''); return false;"><span class="btn-prev">前のページに戻る</span></a></li>
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <li><a class="btn-action" href="javascript:;" onclick="document.form1.submit(); return false;"><span class="btn-next">この内容で登録する</span></a></li>
            <!--{/if}-->
        </ul>
    </div>
</div>
</form>
