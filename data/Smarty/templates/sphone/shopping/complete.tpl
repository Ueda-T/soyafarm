<!--▼CONTENTS-->
<!--トラッキングタグ-->
<img src="https://monipla.jp/mp_track/16669225344c80a495d01bd/eff_convert.php?e=16669225344c80a495d01bd&on=&pc=&qt=&am=" alt="" border="0" style="border:0px;" width="1" height="1" />

<section id="undercolumn">
   <h2 class="spNaked"><!--{$tpl_title|h}--></h2>
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
*}-->

<div style="padding:0 10px;">
<div class="bdrGray">

		<p class="naked" style="margin-bottom:15px;">ご注文ありがとうございました。<br />
		<a href="<!--{$smarty.const.HTTPS_URL}-->mypage/history_list.php">マイページ内のご注文履歴</a>にて、ご注文内容の詳細、お届け状況をご確認いただけます。</p>

<table class="bgYellow">
<tr>
<th>オーダー番号</th>
<td><!--{$tpl_order_id|h}--></td>
</tr>
</table>

</div>
</div>

<!--{*
		<div class="shop_information">
			<p class="naked"><!--{$arrInfo.shop_name|h}--></p>
			<p class="naked">TEL：<!--{$arrInfo.tel01}-->-<!--{$arrInfo.tel02}-->-<!--{$arrInfo.tel03}--> <!--{if $arrInfo.business_hour != ""}-->（受付時間/<!--{$arrInfo.business_hour}-->）<!--{/if}--><br />
			E-mail：<a href="mailto:<!--{$arrInfo.email02|escape:'hex'}-->"><!--{$arrInfo.email02|escape:'hexentity'}--></a>
			</p>
		</div>
*}-->

    <div class="btn_area">
       <a href="<!--{$smarty.const.TOP_URLPATH}-->" class="btn_toppage btn_sub" rel="external">トップページへ</a>
    </div>
</section>
<!--▲CONTENTS-->
