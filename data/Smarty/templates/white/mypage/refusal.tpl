<!--▼CONTENTS-->
<!--{include file="`$smarty.const.TEMPLATE_REALDIR`mypage/pankuzu.tpl"}-->

<!--{if $tpl_navi != ""}-->
	<!--{include file=$tpl_navi}-->
<!--{else}-->
	<!--{include file=`$smarty.const.TEMPLATE_REALDIR`mypage/navi.tpl}-->
<!--{/if}-->

<div id="mainMyPage">
	<h1><img src="<!--{$TPL_URLPATH}-->img/soyafarm/mypage_title_sub6.gif"  alt="登録削除"></h1>

    <div id="mycontents_area">
        <form name="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="refusal_transactionid" value="<!--{$refusal_transactionid}-->" />
        <input type="hidden" name="mode" value="confirm" />
		<div class="withdrawal">
			<dl class="withdrawal">
				<dd>
					<ul class="note">
						<li>※登録削除処理を行いますと、オンラインショップのご利用が行っていただけなくなります。</li>
					</ul>
				</dd>

				<dt>定期購入中のお客様へ</dt>
				<dd>
					<ul class="note">
						<li>※定期購入中に登録削除処理をされましても、出荷手配が完了いたしております商品は発送となります。</li>
						<li>※登録削除をご希望の場合、お手数ではございますが、
						フリーダイヤル0120-39-3009（受付時間9:00～19:00、日・祝休み）<br>
						または、<a href="<!--{$smarty.const.ROOT_URLPATH}-->contact/" class="link">お問い合わせフォーム</a>よりご連絡をお願いいたします。</li>
					</ul>
				</dd>
			</dl>
		</div>

				<p class="btn">
					<a href="javascript:void(0);" onclick="document.form1.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_refusal.gif" alt="登録を削除する" name="refusal" id="refusal" class="swp" /></a>
				</p>
        </form>
    </div>
</div>
<!--▲CONTENTS-->
