<!--▼CONTENTS-->
<!--{include file="`$smarty.const.TEMPLATE_REALDIR`mypage/pankuzu.tpl"}-->

<div class="wrapCustomer">
        <div class="wrapResult">
            <div class="wrapResultEle">
				<h2 class="result">登録削除手続きが完了いたしました。</h2>

				<ul class="note">
					<li>
						マイページをご利用いただき誠にありがとうございました。<br />
						またのご利用を心よりお待ち申し上げます。
					</li>
				</ul>

				<div class="finishedRegular">
					<h3>定期購入中のお客様へ</h3>
					<p class="naked">※定期購入中に登録削除処理をされましても、出荷手配が完了いたしております商品は発送となります。<br />
					※登録削除をご希望の場合、お手数ではございますが、
					フリーダイヤル0120-252-610（受付時間9:00～21:00）<br />
					または、<a href="<!--{$smarty.const.ROOT_URLPATH}-->contact/" class="link">お問い合わせフォーム</a>よりご連絡をお願いいたします。
					</p>
				</div>

            </div>
        </div>

<!--{*
            <div class="shop_information">
                <p class="name"><!--{$arrSiteInfo.company_name|h}--></p>
                <p>TEL：<!--{$arrSiteInfo.tel01}-->-<!--{$arrSiteInfo.tel02}-->-<!--{$arrSiteInfo.tel03}--> <!--{if $arrSiteInfo.business_hour != ""}-->（受付時間/<!--{$arrSiteInfo.business_hour}-->）<!--{/if}--><br />
                E-mail：<a href="mailto:<!--{$arrSiteInfo.email02|escape:'hex'}-->"><!--{$arrSiteInfo.email02|escape:'hexentity'}--></a></p>
            </div>
*}-->

</div>
<!--▲CONTENTS-->
