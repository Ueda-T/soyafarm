<script type="text/javascript">//<![CDATA[
// 定期情報の変更ボタンアクション
function fnChangeRegular(regular_id, line_no, objForm) {

    // 定期受注ID
    objForm.regular_id.value = regular_id;
    // 行NO
    objForm.line_no.value = line_no;

    objForm.action = './regular_change.php';
    objForm.submit();
}
</script>

<!--▼CONTENTS-->
<!--{include file="`$smarty.const.TEMPLATE_REALDIR`mypage/pankuzu.tpl"}-->

<div id="mainMyPage">
	<h1><img src="<!--{$TPL_URLPATH}-->img/rohto/mypage_title_sub4.gif" alt="定期購入一覧" /></h1>

	<p class="intro">
		このページから、「お届けスケジュールのご確認」「定期購入内容のご変更」が行っていただけます。<br />
		お申込番号ごとにある「お届けスケジュールのご確認＆定期内容のご変更」ボタンをクリックして進んでください。
	</p>

	<form name="form1" id="form1" method="post" action="?">
	<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
	<input type="hidden" name="regular_id" value="" />
	<input type="hidden" name="line_no" value="" />
	<input type="hidden" name="pageno" value="<!--{$objNavi->nowpage}-->" />

	<!--{if $objNavi->all_row > 0}-->
	<div class="cartList">
		<div class="pagenumber_area regularPage">
			<div class="navi">
				<ul class="navi">
				<!--▼ページナビ-->
				<!--{$objNavi->strnavi}-->
				<!--▲ページナビ-->
				</ul>
			</div>
		</div>

		<!--{* ▼定期購入一覧 *}-->
		<table summary="定期購入一覧" cellspacing="0">
			<tr>
				<th>商品名</th>
				<th>数量</th>
				<th nowrap>お届け間隔</th>
				<th>次回お届け日</th>
				<th>次々回お届け日</th>
				<th class="linern">確認・変更</th>
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
				<td class="alignC"><!--{* 確認・変更 *}-->

					<!--{** 次回お届け日が未定は変更不可 **}-->
					<!--{if $regularDetail.next_arrival_date == ""}-->
					次回お届け日が未定のため<br />変更できません。

					<!--{** 次回お届け日の1週間以内は変更不可 **}-->
					<!--{elseif !$regularDetail.disp_flg}-->
					只今、出荷準備中のため<br />変更できません。

					<!--{** 「6：休止中」は変更不可 **}-->
					<!--{elseif $regularDetail.status == $smarty.const.REGULAR_ORDER_STATUS_PAUSE}-->
					休止中のため<br />変更できません。

					<!--{else}-->
					<ul class="regularChangeBtnList">
						<li>
							<input type="image" id="cart<!--{$id}-->_teiki" onclick="fnChangeRegular('<!--{$regularDetail.regular_id}-->', '<!--{$regularDetail.line_no}-->', this.form); return false;" src="<!--{$TPL_URLPATH}-->img/rohto/regular_change_btn01.gif" alt="お届けスケジュールのご確認＆定期内容のご変更" align="absmiddle" class="swp" />
						</li>
					</ul>
					<!--{/if}-->
				</td>
			</tr>
			<!--{/foreach}-->
		</table>
		<!--{* ▲定期購入一覧 *}-->
	</div>

	<h2 class="bscW">定期購入専用ダイヤルのご案内</h2>
	<p class="naked">下の表の【1】～【3】は、このページの「お届けスケジュールのご確認＆定期内容のご変更」ボタンからご変更いただくことができません。専用フリーダイヤルをご利用ください。</p>

	<table cellspacing="0" class="regularTel">
		<tr>
			<th rowspan="2"><img src="<!--{$TPL_URLPATH}-->img/rohto/regular_icon01.gif" alt="" width="45" height="41" /></th>
			<th>【1】</th>
			<th>お届け先情報の変更</th>
			<td rowspan="2" class="dyn"><span>専用フリーダイヤル</span>
			<p>0120-<span class="att">252</span>-610</p></td>
			<td rowspan="2">9:00～21:00<br>
			（年末年始を除く）</td>
		</tr>
		<tr>
			<th>【2】</th>
			<th>請求書の送付先の変更</th>
		</tr>
		<tr class="kugiri">
			<th><img src="<!--{$TPL_URLPATH}-->img/rohto/regular_icon02.gif" alt="" width="45" height="36" /></th>
			<th>【3】</th>
			<th>1つの「お申込番号」内の<br>全ての定期購入の終了</th>
			<td class="dyn"><span>専用フリーダイヤル</span>
			<p>0120-<span class="att">880</span>-610</p></td>
			<td>9:00～21:00<br>
			（年末年始を除く）</td>
		</tr>
	</table>

<!--{else}-->
	<p>定期購入情報はありません。</p>
<!--{/if}-->
</form>
</div><!--/#mainMyPage-->
