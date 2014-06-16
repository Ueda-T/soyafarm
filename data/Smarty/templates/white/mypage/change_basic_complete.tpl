<!--▼CONTENTS-->
<!--{include file="`$smarty.const.TEMPLATE_REALDIR`mypage/pankuzu.tpl"}-->

<!--{if $tpl_navi != ""}-->
	<!--{include file=$tpl_navi}-->
<!--{else}-->
	<!--{include file=`$smarty.const.TEMPLATE_REALDIR`mypage/navi.tpl}-->
<!--{/if}-->

<div id="mainMyPageTop">


	<div class="wrapCustomer">
		<div class="wrapResult">
			<div class="wrapResultEle innr">
				<h2 class="result none">お客様情報の変更を受付ました。</h2>
			</div><!--／wrapResultEle-->
		</div>

		<a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/">
			<img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_back.gif" alt="戻る" name="back" id="back" class="swp" />
		</a>
	</div>
</div>
<!--▲CONTENTS-->
