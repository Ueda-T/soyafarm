<link rel="stylesheet" href="<!--{$TPL_URLPATH}-->css/mypage_index.css" type="text/css" media="all" />
<script type="text/javascript">//<![CDATA[
// 定期情報の変更ボタンアクション
function fnChangeRegular(regular_id, line_no, objForm) {

    // 定期受注ID
    objForm.regular_id.value = regular_id;
    // 行NO
    objForm.line_no.value = line_no;

    objForm.action = './regular_detail.php';
    objForm.submit();
}
</script>

<!--▼CONTENTS-->
<!--{include file="`$smarty.const.TEMPLATE_REALDIR`mypage/pankuzu.tpl"}-->

<!--{if $tpl_navi != ""}-->
	<!--{include file=$tpl_navi}-->
<!--{else}-->
	<!--{include file=`$smarty.const.TEMPLATE_REALDIR`mypage/navi.tpl}-->
<!--{/if}-->

<div id="mainMyPage">
	<h1><img src="<!--{$TPL_URLPATH}-->img/soyafarm/mypage_title_sub4.gif" alt="定期購入一覧" /></h1>

	<p>このページでは、「次回お届け日時の確認」のみ行なっていただけます。<br />
	なお、お届け先および定期購入商品・数量のご変更、お支払い方法のご変更につきましては、<br />
	<a href="<!--{$TPL_URLPATH}-->contact/" class="link">お問い合わせフォーム</a>までご連絡ください。お電話・FAXでも承っております。
	</p>
	<p class="naked">
		TEL：フリーダイヤル0120-39-3009（受付時間9:00～19:00、日・祝休み）<br />
		FAX：フリーダイヤル0120-39-4090（24時間365日受付）
	</p>

	<form name="form1" id="form1" method="post" action="?">
	<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
	<input type="hidden" name="regular_id" value="" />
	<input type="hidden" name="line_no" value="" />
	<input type="hidden" name="pageno" value="<!--{$objNavi->nowpage}-->" />

	<!--{if $objNavi->all_row > 0}-->
		<!--{if $objNavi->strnavi}-->
		<div class="pagenumber_area regularPage">
			<div class="navi">
				<ul class="navi">
				<!--▼ページナビ-->
				<!--{$objNavi->strnavi}-->
				<!--▲ページナビ-->
				</ul>
			</div>
		</div>
		<!--{/if}-->

		<!--{* ▼定期購入一覧 *}-->
		<table summary="定期購入一覧" cellspacing="0" class="cart">
			<tr>
				<th class="rg_item-name"><p>商品名</p></th>
				<th class="item-quantity"><p>数量</p></th>
				<th nowrap class="deliv_interval"><p>お届け間隔</p></th>
				<th class="date"><p>次回お届け日</p></th>
				<th class="date"><p>次々回お届け日</p></th>
				<th class="btnKakunin"><p>詳細確認</p></th>
			</tr>
			<!--{foreach from=$arrRegularDetail item=regularDetail }-->
			<tr>
				<td class="alignL"><!--{$regularDetail.product_name|h}--></td><!--{* 商品名 *}-->
				<td class="alignC"><!--{$regularDetail.quantity|h}--></td><!--{* 数量 *}-->
				<td class="alignC"><!--{* お届け間隔 *}-->

				<!--{if $regularDetail.course_cd >= $smarty.const.COURSE_CD_DAY_MIN}-->
					<!--{$regularDetail.course_cd|h}-->日ごと
				<!--{else}-->
					<!--{$regularDetail.course_cd|h}-->ヶ月ごと
				<!--{/if}-->

				</td>
				<td class="alignC" nowrap><!--{$regularDetail.next_arrival_date|date_format:"%Y年%m月%d日"|h}--></td><!--{* 次回お届け日 *}-->
				<td class="alignC" nowrap><!--{$regularDetail.after_next_arrival_date|date_format:"%Y年%m月%d日"|h}--></td><!--{* 次々回お届け日 *}-->
				<td class="alignC">
                                        <!--{* 確認・変更 *}-->
					<!--{** 次回お届け日が未定は変更不可 **}-->
					<!--{if $regularDetail.next_arrival_date == ""}-->
					次回お届け日が未定のため<br />確認できません。
					<!--{** 次回お届け日の1週間以内は変更不可 **}-->
<!--{*
					<!--{elseif !$regularDetail.disp_flg}-->
					只今、出荷準備中のため<br />確認できません。
*}-->
					<!--{** 「6：休止中」は変更不可 **}-->
<!--{*
					<!--{elseif $regularDetail.status == $smarty.const.REGULAR_ORDER_STATUS_PAUSE}-->
					休止中のため<br />確認できません。
*}-->
					<!--{else}-->
					<ul class="regularChangeBtnList">
						<li>
							<input type="image" id="cart<!--{$id}-->_teiki" onclick="fnChangeRegular('<!--{$regularDetail.regular_id}-->', '<!--{$regularDetail.line_no}-->', this.form); return false;" src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_regular_change.gif" alt="お届けスケジュール確認" align="absmiddle" class="swp" />
						</li>
					</ul>
					<!--{/if}-->
				</td>
			</tr>
			<!--{/foreach}-->
		</table>
		<!--{* ▲定期購入一覧 *}-->
<!--{else}-->
	<p class="naked mt30 alignC">定期購入情報はありません。</p>
<!--{/if}-->
</form>
</div><!--/#mainMyPage-->
