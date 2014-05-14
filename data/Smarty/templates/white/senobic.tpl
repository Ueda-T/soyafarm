<!--{if count($arrProducts) > 0 }-->
<style type="text/css">
<!--
#goodsDetail .cart {
margin:20px 0 0;
}
#goodsDetail div.comment3 .senobic  h2{
margin:30px 0 20px 0;
}
.senobic p,
.senobic table.eiyou td dd{
	font-size:0.85em;
	line-height:150%;
}
.senobic table.eiyou{
	width:660px;
	margin:0 auto;
}
.senobic h3{
	width:660px;
	margin:30px auto 0;
}
.senobic table.eiyou th{
	width:172px;
	vertical-align:middle;
	padding:30px 0 0 0;
}
.senobic table.eiyou td{
	padding:30px 0 0 18px;
}
.senobic table.eiyou td dt{
	font-size:1em;
	font-weight:bold;
	padding:3px 0 5px;
}
.senobic .voice{
	width:660px;
	margin:0 auto;
}
.senobic .voice img{
	display:block;
}
-->
</style>

<script type="text/javascript">
<!--
// 複数の数量選択プルダウンを選択時に同期する
function setQuantity(obj) {
	var setIndex = obj.selectedIndex;
	var idName = obj.id;
	var setId = idName.substr(0, 10);
	document.getElementById(setId).selectedIndex = setIndex;
	document.getElementById(setId + "_2").selectedIndex = setIndex;
	return true;
}
-->
</script>

	<form name="form1" action="?" method="post" onsubmit="return false;">
	<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
	<input type="hidden" name="regular_flg" value="" />

	<!--{* エラーメッセージ *}-->
	<!--{if $tpl_err_msg}-->
		<p class="error"><!--{$tpl_err_msg|h}--></p>
	<!--{/if}-->

	<h1><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/title.gif" width="700" height="245" alt="成長期応援飲料セノビック"/></h1>
<a name="matome" id="matome"></a>

	<div class="display" style="margin:25px 0 0 0;">
	<h2 style="margin:30px 0;"><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/display_title.gif" width="700" height="70" alt="セノビックは、1袋からご購入いただけるようになりました！"/></h2>

<!--{*▼商品情報*}-->
<!--{foreach from=$arrProducts item=i name=arrProducts}-->
<!--{if $i.teiki_flg != 0}-->
	<!--{assign var=id value=$i.product_id}-->
	<!--{assign var=arrErr value=$i.arrErr}-->
	<!--{assign var=index value=`$smarty.foreach.arrProducts.index+1`}-->
	<!--{php}-->
		// dlタグのstyle定義(デザイン用)
		$this -> _tpl_vars['arrStyle'] = array(
			'1'=>'width:130px; float:left; margin:0 15px 0 0;',
			'2'=>'width:130px; float:left; margin:0 15px 0 0;',
			'3'=>'width:120px; float:left; margin:0 25px 0 0;',
			'4'=>'width:120px; float:left; margin:0 25px 0 0;',
			'5'=>'width:120px; float:left; margin:0;'
		);
	<!--{/php}-->
	<input type="hidden" name="product_class_id_<!--{$index}-->" value="<!--{$tpl_product_class_id[$id]}-->" />

	<dl style="<!--{$arrStyle[$smarty.foreach.arrProducts.iteration]}-->">
		<dt>
			<a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$i.product_id|u}-->">
				<img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/display_<!--{$i.product_code_min|u}-->.gif" height="290" alt="<!--{$i.name|h}-->" class="swp"/>
			</a>
		</dt>
		<dd style="background:url(<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/ptn_01.gif); text-align:center; padding:10px 0; margin:10px 0; width:120px;">
			<!--{assign var=class_id value=$tpl_product_class_id[$id]}-->
			<select name="quantity_<!--{$index}-->" id="quantity_<!--{$index}-->" onchange="setQuantity(this);" style="text-align:center;<!--{$arrErr.quantity|sfGetErrorColor}-->">
				<!--{html_options options=$tpl_arrQuantity[$class_id] }-->
			</select>
		</dd>
	</dl>
<!--{/if}-->
<!--{/foreach}-->
<!--{*▲商品情報*}-->

<!--{* エラーメッセージ *}-->
<!--{if $tpl_err_msg}-->
	<br style="clear:both;" />
	<p class="error"><!--{$tpl_err_msg|h}--></p>
<!--{/if}-->

<!--{*▼カートボタン*}-->
<dl style="clear:both; position:relative; ">
	<dt style="padding:20px 0 0 0;"><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/display_matome.gif" width="700" height="375" alt="セノビックは、おまとめ買いがお得！"/></dt>
	<!--{*社員の場合、定期購入ボタンが出ないためカートボタンを中央寄せにする*}-->
	<!--{if $tpl_customer_kbn != $smarty.const.CUSTOMER_KBN_EMPLOYEE}-->
	<dd style="position:absolute; left:100px; top:315px;">
	<!--{else}-->
	<dd style="position:absolute; left:245px; top:315px;">
	<!--{/if}-->
		<a href="javascript:void(0);" onclick="fnSetVal('regular_flg', '0'); document.form1.submit();return false;" name="cart"><img src="<!--{$TPL_URLPATH}-->img/rohto/cart.gif" width="210" height="35" alt="カートに入れる" class="swp"/></a>
	</dd>
	<!--{*社員は定期購入不可*}-->
	<!--{if $tpl_customer_kbn != $smarty.const.CUSTOMER_KBN_EMPLOYEE}-->
	<dd style="position:absolute; right:100px; top:315px;">
		<a href="javascript:void(0);" onclick="fnSetVal('regular_flg', '1'); document.form1.submit(); return false;"><img src="<!--{$TPL_URLPATH}-->img/rohto/teiki.gif" width="210" height="35" alt="定期購入する" class="swp"/></a>
	</dd>
	<!--{/if}-->
</dl>
<!--{*▲カートボタン*}-->

<h3 style="color:#207ec6; font-size:0.9em; margin:30px 0 15px 0;">人気商品につき、ご購入袋数制限のお願い</h3>
<p class="naked">月あたりの購入上限は、20袋までとさせていただいております。購入数量が上限を超えている場合には、ご連絡させていただきます場合がございますので、あしからず了承ください。</p>

<p class="naked">*：セノビック売上 2013年4月～3月（ロート調べ）</p>

<a name="teiki" id="teiki"></a>
<p style="margin:35px 0 0 0;"><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/teiki_kakaku.gif" width="700" height="820" alt=""/></p>
<p style="margin:20px 0 0 0;"><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/furufuru.gif" width="700" height="195" alt="ふるふるシェイカー"/></p>

<h3 style="color:#207ec6; font-size:0.9em; margin:30px 0 15px 0;">定期購入についての注意事項</h3>
<p class="naked">定期購入のお得が適用されるのは、お届け間隔が同じで、同じ日のお届けになるようにおまとめ購入いただいた場合となります。
（お届け間隔は、それぞれの商品ごとに、お買い物カゴに入れた後で設定いただきます）</p>

</div><!--/display-->

<a name="himitsu" id="himitsu"></a>
<h2 style="margin:40px 0 0 0;"><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/himitsu_title.gif" width="700" height="190" alt="大人気セノビックの秘密"/></h2>

<div id="goodsDetail">
<div class="comment3">
<div class="senobic">
<h2><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/supplement/senobic/senobic_title05.gif" alt="セノビックはカルシウム・ビタミンD・鉄の栄養機能食品！" width="700" height="34"></h2>
<p style="width:660px; margin:0 auto 20px;"><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/common_eiyou.gif" alt="" width="660" height="250"></p>

<h3><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/common_eiyou-midashi01.gif" alt="セノビックに含まれる、基本栄養素" width="660" height="55">

</h3>
<table cellspacing="0" class="eiyou">
<tr>
<th><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/common_eiyou-title01.gif" alt="カルシウム" width="172" height="42"></th>
<td>
<dl><dt style="color:#259bed;">骨や歯の形成に必要な栄養素です</dt>
<dd>骨や歯の形成に欠かせないカルシウムは、成長期のお子様から健康を気遣う高齢者の方まで不可欠な栄養素です。<br>
セノビックならコップ2杯※1で1日あたりの栄養素等表示基準値の86%※2を摂取できます。</dd>
</dl></td>
</tr>
<tr>
<th><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/common_eiyou-title02.gif" alt="ビタミンD" width="172" height="42"></th>
<td><dl>
<dt style="color:#f67b31;">カルシウムの吸収を促進します</dt>
<dd>カルシウムには、なかなか体内に吸収されにくいという弱点があります。<br>
そこで、セノビックはカルシウムの吸収を促進するビタミンDを配合。骨の形成を助けます。</dd>
</dl></td>
</tr>
<tr>
<th><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/common_eiyou-title03.gif" alt="鉄" width="172" height="42"></th>
<td><dl>
<dt style="color:#c786c9;">赤血球をつくるのに必要な栄養素</dt>
<dd>鉄分は全身に酸素を運ぶ赤血球のもと。不足すると貧血の原因になるとも言われています。セノビックならコップ2杯※1で1日あたりの栄養素等表示基準値の81%※2を摂取できます。</dd>
</dl></td>
</tr>
</table>

<h3><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/common_eiyou-midashi02.gif" alt="さらに、成長に大切な栄養素も" width="660" height="55"></h3>

<table cellspacing="0" class="eiyou">
<tr>
<th><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/common_eiyou-title04.gif" alt="ボーンペップ" width="172" height="42"></th>
<td><dl>
<dt style="color:#82c639;">成長に大切な栄養素を含んだ卵から生まれた卵黄ペプチド</dt>
<dd>たんぱく質をはじめとする栄養素が豊富に含まれ、栄養バランスに優れた卵に着目。成長に大切な栄養素を含んだ卵から生まれた卵黄ペプチドを、コップ2杯※1でたっぷり100mgも摂取できます。</dd>
</dl></td>
</tr>
<tr>
<th><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/common_eiyou-title05.gif" alt="乳清プロテイン" width="172" height="42"></th>
<td><dl>
<dt style="color:#f3ce00;">筋肉などの身体を構成するのに必要な栄養素です</dt>
<dd>ミルク由来のプロテイン（乳清プロテイン）は牛乳から得られたたんぱく質です。筋肉などの身体を構成するのに必要な栄養素です。コップ2杯※1で4000mg（プロテインinバナナ味）あるいは1000mg（ポタージュ味）が摂取できます。</dd>
</dl></td>
</tr>
<tr>
<th><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/common_eiyou-title06.gif" alt="オリゴミル（ミルクペプチド）" width="172" height="42"></th>
<td><dl>
<dt style="color:#59c5c6;">ミルクの栄養素を凝縮した栄養成分</dt>
<dd>牛乳由来のたんぱく質を分解して得られるペプチド※混合物です。（ミルクココア味で摂取できます）<br>
※アミノ酸が複数（2～20個）つながった物質のことです。 乳幼児が母乳を飲んで、栄養を取っていることをヒントに、「牛乳」に着目しました。</dd>
</dl></td>
</tr>
</table>
<p>&nbsp;</p>
<h2><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/supplement/senobic/senobic_title04.gif" alt="セノビック ここがポイント！" width="700" height="45"></h2>
<div style="margin: 0 20px;">
<h3><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/supplement/senobic/senobic_point01.jpg" alt="コップ2杯(※1)で86％(※2)のカルシウムを摂取！" width="580" height="55"></h3>
<p style="margin-bottom:3px;">骨や歯の形成に欠かせないカルシウムは、成長期のお子様から健康を気遣う高齢者の方まで不可欠な栄養素。
セノビックならコップ2杯※1で1日あたりの栄養素等表示基準値の86％※2を摂取できます。</p>
 
<h3><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/supplement/senobic/senobic_point02.jpg" alt="カルシウムの吸収を促進するビタミンDを配合！" width="580" height="55"></h3>
<p style="margin-bottom:3px;">カルシウムには、なかなか体内に吸収されにくいという弱点があります。そこで、セノビックはカルシウムの吸収を
促進するビタミンDを配合。骨の形成を助けます。</p>
<h3><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/supplement/senobic/senobic_point03.jpg" alt="さらに、鉄分はコップ2杯※1で81％※2をカバー" width="385" height="55"></h3>
<p style="margin-bottom:3px;">鉄分は全身に酸素を運ぶ赤血球のもと。<br>
不足すると貧血の原因になるとも言われています。<br>
セノビックならコップ2杯※1で1日あたりの栄養素等表示基準値の81％※2を摂取できます。</p>
<h3><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/supplement/senobic/senobic_point04.jpg" alt="成長に大切な栄養素を含んだ卵から生まれた卵黄ペプチド配合" width="452" height="55"></h3>
<p style="margin-bottom:5px;">たんぱく質をはじめとする栄養素が豊富に含まれ、栄養バランスに優れた卵に着目。
成長に大切な栄養素を含んだ卵から生まれた卵黄ペプチドを、コップ2杯でたっぷり100mgも摂取できます。</p>
<p class="box small">※1  牛乳150mL＋セノビック約10gを1杯として換算。<br>
※2 （■牛乳＊＋■セノビック）コップ2杯で1日あたりの栄養素等表示基準値に占める割合を示しています。<br>
＊牛乳の栄養素は「日本食品標準成分表2010」から換算しています</p>
</div>
 
<h2><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/supplement/senobic/senobic_title02.gif" alt="「セノビック」のおいしい召し上がり方" width="700" height="34"></h2>
 
<img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/common_how.gif" alt="冷たい牛乳に溶かすだけ" width="660" height="185" class="f-left" style="padding:0 20px 20px;">
<p style="margin:0 20px; ">小スプーン山盛り2杯（約10g）に少量の牛乳（ホットの場合は温かい牛乳）約30mLを加え、よくかき混ぜた後、さらに牛乳（ホットの場合は温かい牛乳）120mLを注いで再度よくかき混ぜてください。<br>
●1日コップ2杯を目安にお飲みください。<br>
●コップの底に顆粒が沈みやすいので、よくかきまぜてください。 </p>
 
<h2><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/supplement/senobic/senobic_title03.gif" alt="ママの声　/　ユーザーボイス" width="700" height="64"></h2>
<table cellpadding="0" cellspacing="0" style="margin:0 auto;">
<tr>
<td><p style="padding:0 20px 0 0;"><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/supplement/senobic/senobic_voice01.gif" alt="とってもおいしい。子ども達も、普段はなかなか飲まない牛乳も、これだと喜んで飲んでくれると思います。" width="279" height="122"></p></td>
<td><p><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/supplement/senobic/senobic_voice03.gif" alt="大人の感覚では、少し甘めかな、という感じ。子どもはこれくらいの方がちょうど好きだと思う。" width="279" height="122"></p></td>
</tr>
<tr>
<td><p style="padding:10px 20px 0 0;"><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/supplement/senobic/senobic_voice02.gif" alt="朝に限らず、ジュースを飲ませるなら、代わりにこれを飲ませたいです。" width="279" height="99"></p></td>
<td><p style="padding:10px 0 0;"><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/supplement/senobic/senobic_voice04.gif" alt="凍らせてアイスにしたり、いろんなアレンジで楽しみながら続けています！" width="279" height="99"></p></td>
</tr>
</table>

</div><!--/senobic-->

</div><!--/comment3-->

</div><!--/goodsDetail-->



<div class="display" style="margin:50px 0 0 0;">
<!--{*▼商品情報*}-->
<!--{foreach from=$arrProducts item=i name=arrProducts}-->
<!--{if $i.teiki_flg != 0}-->
	<!--{assign var=id value=$i.product_id}-->
	<!--{assign var=arrErr value=$i.arrErr}-->
	<!--{assign var=index value=`$smarty.foreach.arrProducts.index+1`}-->

	<dl style="<!--{$arrStyle[$smarty.foreach.arrProducts.iteration]}-->">
		<dt>
			<a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$i.product_id|u}-->">
				<img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/display_<!--{$i.product_code_min|u}-->.gif" height="290" alt="<!--{$i.name|h}-->" class="swp"/>
			</a>
		</dt>
		<dd style="background:url(<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/ptn_01.gif); text-align:center; padding:10px 0; margin:10px 0; width:120px;">
			<!--{assign var=class_id value=$tpl_product_class_id[$id]}-->
			<select name="quantity_<!--{$index}-->_2" id="quantity_<!--{$index}-->_2" onchange="setQuantity(this);" style="text-align:center;<!--{$arrErr.quantity|sfGetErrorColor}-->">
				<!--{html_options options=$tpl_arrQuantity[$class_id] }-->
			</select>
		</dd>
	</dl>
<!--{/if}-->
<!--{/foreach}-->
<!--{*▲商品情報*}-->

<!--{* エラーメッセージ *}-->
<!--{if $tpl_err_msg}-->
	<br style="clear:both;" />
	<p class="error"><!--{$tpl_err_msg|h}--></p>
<!--{/if}-->

<!--{*▼カートボタン*}-->
<dl style="clear:both; position:relative; ">
	<dt style="padding:20px 0 0 0;"><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/display_matome.gif" width="700" height="375" alt="セノビックは、おまとめ買いがお得！"/></dt>
	<!--{*社員の場合、定期購入ボタンが出ないためカートボタンを中央寄せにする*}-->
	<!--{if $tpl_customer_kbn != $smarty.const.CUSTOMER_KBN_EMPLOYEE}-->
	<dd style="position:absolute; left:100px; top:315px;">
	<!--{else}-->
	<dd style="position:absolute; left:245px; top:315px;">
	<!--{/if}-->
		<a href="javascript:void(0);" onclick="fnSetVal('regular_flg', '0'); document.form1.submit();return false;" name="cart"><img src="<!--{$TPL_URLPATH}-->img/rohto/cart.gif" width="210" height="35" alt="カートに入れる" class="swp"/></a>
	</dd>
	<!--{*社員は定期購入不可*}-->
	<!--{if $tpl_customer_kbn != $smarty.const.CUSTOMER_KBN_EMPLOYEE}-->
	<dd style="position:absolute; right:100px; top:315px;">
		<a href="javascript:void(0);" onclick="fnSetVal('regular_flg', '1'); document.form1.submit(); return false;"><img src="<!--{$TPL_URLPATH}-->img/rohto/teiki.gif" width="210" height="35" alt="定期購入する" class="swp"/></a>
	</dd>
	<!--{/if}-->
</dl>
<!--{*▲カートボタン*}-->

<h3 style="color:#207ec6; font-size:0.95em; margin:30px 0 15px 0;">人気商品につき、ご購入袋数制限のお願い</h3>
<p class="naked">月あたりの購入上限は、20袋までとさせていただいております。購入数量が上限を超えている場合には、ご連絡させていただきます場合がございますので、あしからず了承ください。</p>

<p class="naked">*：セノビック売上 2013年4月～3月（ロート調べ）</p>

<p style="margin:35px 0 0 0;"><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/teiki_kakaku.gif" width="700" height="820" alt=""/></p>
<p style="margin:20px 0 0 0;"><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/furufuru.gif" width="700" height="195" alt="ふるふるシェイカー"/></p>

<h3 style="color:#207ec6; font-size:0.95em; margin:30px 0 15px 0;">定期購入についての注意事項</h3>
<p class="naked">定期購入のお得が適用されるのは、お届け間隔が同じで、同じ日のお届けになるようにおまとめ購入いただいた場合となります。
（お届け間隔は、それぞれの商品ごとに、お買い物カゴに入れた後で設定いただきます）</p>

</div><!--/display-->

<input type="hidden" name="product_index" value="<!--{$index}-->" />

</form>

<p style="text-align:center; margin:30px auto;">
<a href="/shop/event/snbpht.php"><img src="<!--{$smarty.const.ROOT_URLPATH}-->image/senobic/banner.jpg" alt="セノビック大好き写真＆メッセージ募集！" width="580" height="100"></a>
</p>

<!--{else}-->
<p class="intro">該当商品がありません。</p>
<!--{/if}-->

<!-- Google Code for &#12475;&#12494;&#12499;&#12483;&#12463; Remarketing List --><script type="text/javascript">/* <![CDATA[ */var google_conversion_id = 1002217752;var google_conversion_language = "en";var google_conversion_format = "3";var google_conversion_color = "666666";var google_conversion_label = "pjhpCLDdsAIQmMLy3QM";var google_conversion_value = 0;/* ]]> */</script><script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js"></script><noscript><div style="display:inline;"><img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/1002217752/?label=pjhpCLDdsAIQmMLy3QM&amp;guid=ON&amp;script=0"/></div></noscript><img height="1" width="1" src="http://view.atdmt.com/action/jp_ROHTO_senobic_020113"/>
<!--{$tpl_clickAnalyzer}-->
<script type="text/javascript"><!--var blade_co_account_id='3010';var blade_group_id='';--></script><script src="http://d-cache.microad.jp/js/bl_track.js"></script>
