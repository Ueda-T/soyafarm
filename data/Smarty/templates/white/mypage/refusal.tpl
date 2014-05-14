<!--▼CONTENTS-->
<!--{include file="`$smarty.const.TEMPLATE_REALDIR`mypage/pankuzu.tpl"}-->

<div id="mainMyPage">
	<h1><img src="<!--{$TPL_URLPATH}-->img/rohto/mypage_title_sub6.gif" width="700" height="70" alt="登録削除"></h1>

    <div id="mycontents_area">
        <form name="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="refusal_transactionid" value="<!--{$refusal_transactionid}-->" />
        <input type="hidden" name="mode" value="confirm" />
		<div class="withdrawal">
			<img src="<!--{$TPL_URLPATH}-->img/rohto/mypage_sub6_note.gif" alt="登録の削除処理を行う前に必ずご確認ください" width="700" height="26">
			<dl class="withdrawal">
				<dd>
					<ul class="note">
						<li>※登録削除処理を行いますと、オンラインショップのご利用が行っていただけなくなります。</li>
						<!--{*<li>※ポイント加算率の低い初期ステージ（ブロンズステージ）からの再スタートとなります。</li>*}-->
					</ul>
				</dd>

				<dt>定期購入中のお客様へ</dt>
				<dd>
					<ul class="note">
						<li>※定期購入中に登録削除処理をされましても、出荷手配が完了いたしております商品は発送となります。</li>
						<li>※登録削除をご希望の場合、お手数ではございますが、
						フリーダイヤル0120-252-610（受付時間9:00～21:00）<br>
						または、<a href="<!--{$smarty.const.ROOT_URLPATH}-->contact/" class="link">お問い合わせフォーム</a>よりご連絡をお願いいたします。</li>
					</ul>
				</dd>
			</dl>
		</div>

				<p class="nakedC">
					<a href="javascript:void(0);" onclick="document.form1.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/rohto/withdrawal.gif" alt="登録を削除する" name="refusal" id="refusal" class="swp" /></a>
				</p>
        </form>
    </div>
</div>
<!--▲CONTENTS-->
