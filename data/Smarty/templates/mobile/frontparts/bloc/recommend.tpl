<h2><img src="<!--{$TPL_URLPATH}-->img/rohto/bar_recommend.gif" alt="おすすめ商品 ﾛｰﾄ通販" width="100%"></h2>

<div align="center"><a href="<!--{$smarty.const.ROOT_URLPATH}-->products/brand.php?brand_code=1chouko">
<img src="<!--{$TPL_URLPATH}-->img/rohto/bnr_1chouko.gif" alt="1兆個のﾁｶﾗ" width="95%"><br>
<font size="-1">みなさまの健やかな毎日に。</font></a></div>
<br>

<div align="center"><a href="<!--{$smarty.const.ROOT_URLPATH}-->products/senobic.php">
<img src="<!--{$TPL_URLPATH}-->img/rohto/bnr_senobic_strawberry02.gif" alt="ｾﾉﾋﾞｯｸいちごﾐﾙｸ味が復活！" width="95%"><br>
<font size="-1">ｾﾉﾋﾞｯｸいちごﾐﾙｸ味が復活！</font></a></div>
<br>

<div align="center"><a href="<!--{$smarty.const.ROOT_URLPATH}-->products/brand.php?brand_code=koujihada">
<img src="<!--{$TPL_URLPATH}-->img/rohto/bnr_koujihada.gif" alt="｢糀(こうじ)｣のﾌﾟﾛと｢ｽｷﾝｹｱ｣のﾌﾟﾛが作った自然派高保湿ｸﾘｰﾑ"  width="95%"><br>
<font size="-1">｢糀(こうじ)｣のﾌﾟﾛと｢ｽｷﾝｹｱ｣のﾌﾟﾛが作った自然派高保湿ｸﾘｰﾑ</font></a></div>
<br>

<div align="center"><a href="<!--{$smarty.const.ROOT_URLPATH}-->products/brand.php?brand_code=seijyu">
<img src="<!--{$TPL_URLPATH}-->img/rohto/bnr_seiju2.gif" alt="聖樹のチカラ" width="95%"><br>
<font size="-1">話題の成分、レスベラトロール類（ポリフェノールの一種）含有</font></a></div>
<br>

<div align="center"><a href="<!--{$smarty.const.ROOT_URLPATH}-->shunkoku-shunsai.php">
<img src="<!--{$TPL_URLPATH}-->img/rohto/bnr_shun02.gif" alt="身体においしい商品、お届けします。旬穀旬菜"  width="95%"><br>
<font size="-1">身体においしい商品、お届けします。</font></a></div>
<br>


<!--{*
<!--{if count($arrBestProducts) > 0}-->

<!--{foreach from=$arrBestProducts item=arrProduct name=best_products}-->

<!-- ▼おすすめ商品コメント ここから -->
<a href="<!--{$smarty.const.MOBILE_P_DETAIL_URLPATH}--><!--{$arrProduct.product_id|u}-->">
<!--{$arrProduct.comment|h|nl2br}--></a>
<!-- ▲おすすめ商品コメント ここまで -->

<!--{if !$smarty.foreach.best_products.last}--><br><!--{/if}-->
<!--{/foreach}-->

<br>
<br>
<hr>

<!--{/if}-->
*}-->