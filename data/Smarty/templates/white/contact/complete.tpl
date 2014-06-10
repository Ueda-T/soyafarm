<div id="undercolumn">
    <div id="undercolumn_contact">

<!--{*
		<!--{if $smarty.session.customer}-->
		<p class="pankuzu">
			<a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/">マイページ</a>
			&nbsp;&gt;&nbsp;
			お問い合わせ
		</p>
		<!--{/if}-->
*}-->

<!--{*
            <div class="wrapForm">
            <table summary="お問い合わせ" cellspacing="0">
            <colgroup width="30%"></colgroup>
            <colgroup width="70%"></colgroup>
                <tr>
                    <th>件名</th>
                    <td><!--{$arrSubject[$arrForm.subject.value]}--></td>
                </tr>
                <tr>
                    <th>お名前</th>
                    <td><!--{$arrForm.name.value|h}--></td>
                </tr>
                <tr>
                    <th>メールアドレス</th>
                    <td><a href="mailto:<!--{$arrForm.email.value|escape:'hex'}-->"><!--{$arrForm.email.value|escape:'hexentity'}--></a></td>
                </tr>
                <tr>
                    <th>電話番号</th>
                    <td>
                        <!--{if strlen($arrForm.tel.value) > 0 }-->
                            <!--{$arrForm.tel.value|h}-->
                        <!--{/if}-->
                    </td>
                </tr>
                <tr>
                    <th>お問合せ日</th>
                    <td><!--{$arrForm.now.value|h}--></td>
                </tr>
                <tr>
                    <th>内容</th>
                    <td><!--{$arrForm.contents.value|h|nl2br}--></td>
                </tr>
            </table>
            </div><!--／wrapForm-->
*}-->

		<div class="wrapContactEle"><h2 class="bscW" style="margin:20px auto;text-align:center;">お問い合わせを受け付けました。</h2>
		<p class="naked">
		お問い合わせ内容により、お電話にてご返答する場合がございます。あらかじめご了承ください。</p>
		<p class="naked">1週間経過いたしましても弊社より回答がない場合、お問い合わせが完了していない場合がございますので、<br>
		お手数ではございますが、再度、お問い合わせくださいますよう、よろしくお願いいたします。</p>

        <p class="btn">
            <a href="<!--{$smarty.const.TOP_URLPATH}-->"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_toppage.gif" alt="トップページへ" border="0" name="b_toppage" id="b_toppage" class="swp" /></a>
        </p>
		</div><!--／wrapContentEle-->

<!--{*
        <div class="wrapContact">
            <p class="nakedC">お問い合わせ内容の送信が完了いたしました。</p>
            <p class="nakedC">
                万一、ご回答メールが届かない場合は、トラブルの可能性もありますので<br />大変お手数ではございますがもう一度お問い合わせいただくか、お電話にてお問い合わせください。<br />
                今後ともご愛顧賜りますようよろしくお願い申し上げます。
            </p>
            <div class="shop_information">
            <p class="name"><!--{$arrSiteInfo.company_name|h}--><br />
            <p>TEL：<!--{$arrSiteInfo.tel01}-->-<!--{$arrSiteInfo.tel02}-->-<!--{$arrSiteInfo.tel03}-->
                <!--{if $arrSiteInfo.business_hour != ""}-->
                (受付時間/<!--{$arrSiteInfo.business_hour}-->)
                <!--{/if}--><br />
                E-mail：<a href="mailto:<!--{$arrSiteInfo.email02|escape:'hex'}-->"><!--{$arrSiteInfo.email02|escape:'hexentity'}--></a></p>
            </p>
            </div>
        </div>
*}-->
    </div>
</div>
