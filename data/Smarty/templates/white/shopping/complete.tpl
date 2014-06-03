<!--▼CONTENTS-->
<div id="undercolumn">
	<div id="undercolumn_shopping">
		<h1><img src="<!--{$TPL_URLPATH}-->img/soyafarm/order_title_step4.gif" alt="購入手続き：完了"></h1>

		<div style="margin:20px 100px;">

		<p class="wrapInfo" style="padding:30px 0;"><span style="font-size:200%; color:#F63;">ご注文ありがとうございました。</span></p>
		<p class="naked"><a href="<!--{$smarty.const.HTTPS_URL}-->mypage/history_list.php" class="link">マイページ内のご注文履歴</a>にて、ご注文内容の詳細、お届け状況をご確認いただけます。</p>
		</div>

<div class="wrapForm" style="margin:20px 100px;">
<table cellspacing="0" class="typ2">
<tr>
<th>オーダー番号</th>
<td><!--{$tpl_order_id|h}--></td>
</tr>
</table>
</div><!--／wrapForm-->

<!--{*
			<!-- ▼その他決済情報を表示する場合は表示 -->
			<!--{if $arrOther.title.value}-->
				<p><span class="attention">■<!--{$arrOther.title.name}-->情報</span><br />
					<!--{foreach key=key item=item from=$arrOther}-->
						<!--{if $key != "title"}-->
							<!--{if $item.name != ""}-->
								<!--{$item.name}-->：
							<!--{/if}-->
								<!--{$item.value|nl2br}--><br />
						<!--{/if}-->
					<!--{/foreach}-->
				</p>
			<!--{/if}-->
			<!-- ▲コンビに決済の場合には表示 -->

			<div id="complete_area">
				<p class="message"><!--{$arrInfo.shop_name|h}-->の商品をご購入いただき、ありがとうございました。</p>
				<p>ただいま、ご注文の確認メールをお送りさせていただきました。<br />
					万一、ご確認メールが届かない場合は、トラブルの可能性もありますので大変お手数ではございますがもう一度お問い合わせいただくか、お電話にてお問い合わせくださいませ。<br />
					今後ともご愛顧賜りますようよろしくお願い申し上げます。</p>


				<div class="shop_information">
					<p class="name"><!--{$arrInfo.shop_name|h}--></p>
					<p>TEL：<!--{$arrInfo.tel01}-->-<!--{$arrInfo.tel02}-->-<!--{$arrInfo.tel03}--> <!--{if $arrInfo.business_hour != ""}-->（受付時間/<!--{$arrInfo.business_hour}-->）<!--{/if}--><br />
					E-mail：<a href="mailto:<!--{$arrInfo.email02|escape:'hex'}-->"><!--{$arrInfo.email02|escape:'hexentity'}--></a>
					</p>
				</div>
			</div>
*}-->

			<div class="btn_area">
				<ul>
					<li>
						<a href="<!--{$smarty.const.ROOT_URLPATH}-->">
							<img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_toppage.gif" alt="トップページへ" border="0" name="b_toppage" class="swp" /></a>
					</li>
				</ul>
			</div>
		</div>


	</div>
</div>
<!--▲CONTENTS-->
<!--{$tpl_clickAnalyzer}-->
