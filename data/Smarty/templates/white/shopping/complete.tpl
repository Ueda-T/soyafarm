<!--▼CONTENTS-->
<!--トラッキングタグ-->
<img src="https://monipla.jp/mp_track/16669225344c80a495d01bd/eff_convert.php?e=16669225344c80a495d01bd&on=&pc=&qt=&am=" alt="" border="0" style="border:0px;" width="1" height="1" />

<div id="undercolumn">
	<div id="undercolumn_shopping">
		<h1><img src="<!--{$TPL_URLPATH}-->img/rohto/order_title_step4.gif" width="960" height="70" alt="購入手続き：完了"></h1>

		<div style="margin:20px 100px;">

		<p class="wrapInfo" style="padding:30px 0;"><span style="font-size:200%; color:#4c85e2;">ご注文ありがとうございました。</span></p>
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

<div style="width:760px; margin:20px auto 30px;">
<dl>
<dt><a href="<!--{$smarty.const.ROOT_URLPATH}-->products/brand.php?brand_code=1chouko"><img src="<!--{$TPL_URLPATH}-->img/rohto/order_thanks_1chouko.gif" alt="乳酸菌サプリメント「1兆個のチカラ」" width="760" height="135"></a></dt>
</dl>
</div>

<div style="width:760px; margin:30px auto; background:url(<!--{$TPL_URLPATH}-->img/rohto/back_ptn9.gif);">
<img src="<!--{$TPL_URLPATH}-->img/rohto/bookmark_title.gif" width="760" height="49" alt="いつもロート通販オンラインショップをご利用いただき、ありがとうございます。">
<p style="margin:15px 0; text-align:center; font-size:0.875em; line-height:150%;">より快適にロート通販をご利用いただくために
<br>
ロート通販オンラインショップを「お気に入り」に追加することをおすすめいたします。</p>
<a href="javascript:window.external.AddFavorite
('<!--{$smarty.const.HTTP_URL}-->', 'ロート製薬公式【ロート通販オンラインショップ】')
" style="width:410px; margin:0 auto; display:block;"><img src="<!--{$TPL_URLPATH}-->img/rohto/order_bookmark.gif" alt="ロート通販オンラインショップをお気に入りに登録する" width="410" height="35" class="swp"></a>
<p style="font-size:0.7em; line-height:120%; color:#737373; text-align:center; margin:20px 0 0; padding:0 0 20px; background:url(<!--{$TPL_URLPATH}-->img/rohto/bookmark_ft.gif) bottom no-repeat;">上記のボタンはInternet Explorerにのみ対応しております。ご利用の環境によっては機能しない場合もございます。<br>
機能しない場合はブラウザに付属しているお気に入り（ブックマーク）機能からご登録ください。</p>
</div>



<p class="nakedC" style="margin-top:30px;"><a href="<!--{$smarty.const.ROOT_URLPATH}-->" class="link">ロート通販トップへ</a></p>


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

			<div class="btn_area">
				<ul>
					<li>
						<a href="<!--{$smarty.const.ROOT_URLPATH}-->">
							<img src="<!--{$TPL_URLPATH}-->img/rohto/btn_toppage.gif" alt="トップページへ" border="0" name="b_toppage" class="swp" /></a>
					</li>
				</ul>
			</div>
		</div>

*}-->

	</div>
</div>
<!--▲CONTENTS-->
<!--{$tpl_clickAnalyzer}-->
