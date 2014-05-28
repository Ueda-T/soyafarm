<!--▼CONTENTS-->
<div id="undercolumn">
    <div id="undercolumn_contact">
		<h1><img src="<!--{$TPL_URLPATH}-->img/rohto/contact_title.gif" alt="お問い合わせ"></h1>

		<!--{if $smarty.session.customer}-->
		<p class="pankuzu">
			<a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/">マイページ</a>
			&nbsp;&gt;&nbsp;
			お問い合わせ
		</p>
		<!--{/if}-->

        <form name="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="confirm" />

        <!--▼お問い合わせフォーム-->

        <!--{* 顧客名を表示 *}-->
        <!--{if $smarty.session.customer}-->
        <div class="wrapCustomer">
            <div class="myPagePersonal">
                <div class="myPageNamae">
                    <table cellspacing="0" class="lay1">
                        <tr>
                            <td>
                                <dl class="stage">
                                    <dt><strong><!--{$tpl_customer_name|h}--></strong>&nbsp;様</dt>
                                </dl>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <!--{/if}-->

        <div class="wrapContact">
            <div class="bf">
                <img src="<!--{$TPL_URLPATH}-->img/rohto/contact_before.gif" alt="お問い合わせの前にご確認ください" width="431" height="37" class="p01">
                <img src="<!--{$TPL_URLPATH}-->img/rohto/contact_text.gif" alt="よくある質問を掲載しています。まずこちらをご覧ください。" width="431" height="15" class="p02">
            <a href="<!--{$smarty.const.ROOT_URLPATH}-->contents/faq.php" class="p03"><img src="<!--{$TPL_URLPATH}-->img/rohto/contact_btn_faq.gif" alt="FAQ（よくある質問）" width="310" height="50" class="swp"></a>

            <div class="pickup">
                <table cellpadding="0" cellspacing="5">
                    <tr>
                        <th><img src="<!--{$TPL_URLPATH}-->img/rohto/contact_pictip_title.gif" width="165" height="18" alt="特に多く寄せられるご質問"></th>
<td><a href="<!--{$smarty.const.ROOT_URLPATH}-->contents/faq02.php#faq204" class="icon1">定期購入の変更をしたい。</a></td>
<td class="right"><a href="<!--{$smarty.const.ROOT_URLPATH}-->contents/faq05.php#faq705" class="icon1">メールマガジンの配信変更をしたい。</a></td>
</tr>
<tr>
<th>&nbsp;</th>
<td colspan="2" style="border:0; padding-top:5px;"><a href="<!--{$smarty.const.ROOT_URLPATH}-->contents/faq05.php#faq702" class="icon1">ログインができない場合はどうすればよいですか？ </a></td>

                    </tr>
                </table>
            </div><!--/pickup-->
        </div><!--／bf-->

            <div class="btn">
                <span><a href="http://www.rohto.co.jp/support/" target="_blank"><img src="<!--{$TPL_URLPATH}-->img/rohto/contact_btn1.gif" alt="製品に関するご質問・販売店等に関するお問い合わせはこちら" width="265" height="55" class="swp"></a></span>

                <span><a href="https://www.rclub2.rohto.co.jp/club/mail/" target="_blank"><img src="<!--{$TPL_URLPATH}-->img/rohto/contact_btn2.gif" alt="ロートくらぶに関するお問い合わせはこちら" width="265" height="55" class="swp"></a></span>
            </div><!--／btn-->

            <p class="nakedC">ご注文に関するお問い合わせ等については、下記のフォームよりお問い合わせください。</p>

            <div class="wrapForm">

            <table summary="お問い合わせ">
                <tr>
                    <th><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" />件名</th>
                    <td>
                        <span class="attention"><!--{$arrErr.subject}--></span>
                        <select name="subject" id="subject" style="<!--{$arrErr.subject|sfGetErrorColor}-->">
                            <option value="" selected="selected">↓お選びください</option>
                            <!--{html_options options=$arrSubject selected=$arrForm.subject}-->
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" />お名前</th>
                    <td>
                        <span class="attention"><!--{$arrErr.name}--></span>
                        <input type="text" class="box150" name="name" value="<!--{$arrForm.name.value|h|default:$arrData.name|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name|sfGetErrorColor}-->; ime-mode: auto;" /><span>&nbsp;（全角で入力してください）</span>
                    </td>
                </tr>
                <tr>
                    <th><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" />メールアドレス</th>
                    <td>
                        <span class="attention"><!--{$arrErr.email}--><!--{$arrErr.email02}--></span>
                        <input type="text" class="box380 top" name="email" value="<!--{$arrForm.email.value|h|default:$arrData.email|h}-->" style="<!--{$arrErr.email|sfGetErrorColor}-->; ime-mode: disabled;" /><br />
                        <!--{* ログインしていれば入力済みにする *}-->
                        <!--{if $smarty.server.REQUEST_METHOD != 'POST' && $smarty.session.customer}-->
                        <!--{assign var=email02 value=$arrData.email}-->
                        <!--{/if}-->
                    </td>
                </tr>
                <tr>
                    <th><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" />電話番号</th>
                    <td>
                        <span class="attention"><!--{$arrErr.tel}--></span>
                        <input type="text" class="box180" name="tel" value="<!--{$arrForm.tel.value|h|default:$arrData.tel|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN*3}-->" style="<!--{$arrErr.tel|sfGetErrorColor}-->; ime-mode: disabled;" /><span>&nbsp;例：06-6575-1231</span>
                    </td>
                </tr>
                <tr>
                    <th><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" />内容</th>
                    <td>
                        <span class="attention"><!--{$arrErr.contents}--></span>
                        <textarea name="contents" id="contents" class="box380" cols="60" rows="20" style="<!--{$arrErr.contents.value|h|sfGetErrorColor}-->; ime-mode: auto;"><!--{$arrForm.contents.value|h}--></textarea><br />
                        <span class="mini">(500文字以内でお願いします。)</span>
                    </td>
                </tr>
            </table>
            <p class="btn">
                    <a href="javascript:void(0);" onclick="document.form1.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/rohto/kakunin02.gif" alt="確認ページへ" name="confirm" class="swp" /></a>
            </p>
            </div><!--／wrapForm-->

            <ul class="kome">
                <li>ご記入漏れがある場合、お問い合わせにお答え出来ない場合がございます。</li>
                <li>メールアドレスは、くれぐれもお間違いのないようにご入力ください。</li>
                <li>平日の17時以降、土・日祝日のお問い合わせにつきましては、返信までにお時間をいただくことがございます。</li>
                <li>お問い合わせ内容により、お電話にてご返答する場合がございます。あらかじめご了承ください。</li>
                <li>商品およびロート通販の内容以外のお問い合わせ（ex.ビジネスのご提案）についてはご容赦くださいますようお願いいたします。</li>
            </ul>

        </div><!--／wrapContact-->
        <input type="hidden" name="now" value="<!--{$smarty.now|date_format:'%Y/%m/%d %H:%M:%S'}-->" />
        <!--▲お問い合わせフォーム-->
        </form>
    </div>
</div>
<!--▲CONTENTS-->
<!--{$tpl_clickAnalyzer}-->
<script src="<!--{$smarty.const.ROOT_URLPATH}-->js/contact.js" type="text/javascript"></script>
