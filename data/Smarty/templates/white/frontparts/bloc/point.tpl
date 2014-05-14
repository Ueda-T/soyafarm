<!--{if $tpl_login && $smarty.const.USE_POINT !== false && $CustomerPoint}-->
<div class="rottaInfo">
	<h2><img src="<!--{$TPL_URLPATH}-->img/rohto/sub_rotta_title.gif" alt="ポイント（通販ポイント）残高" width="210" height="40"></h2>
	<div class="rottaInfoEle">
		<dl>
			<dt>現在の保有ポイント</dt>
			<dd><span><!--{$CustomerPoint|number_format|default:"0"|h}--></span></dd>
		</dl>

		<dl>
			<dt><span><!--{$CustomerPointValidDate|date_format:"%Y年%m月%d日"}--></span>までに使用期限切れ<span class="dyn">※</span>になるポイント</dt>
			<dd class="dyn"><span><!--{$CustomerPoint|number_format|default:"0"|h}--></span></dd>
		</dl>
		<p>※期限までにご利用のない場合、ポイントは消滅します。</p>

	</div><!--rottaInfoEle-->
</div><!--rottaInfo-->
<!--{/if}-->