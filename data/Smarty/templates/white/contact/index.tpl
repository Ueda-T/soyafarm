<!--▼CONTENTS-->
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

        <form name="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="confirm" />

        <!--▼お問い合わせフォーム-->
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
*}-->
            <p class="nakedC">下記の項目を入力の上、送信してください。</p>

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

        <input type="hidden" name="now" value="<!--{$smarty.now|date_format:'%Y/%m/%d %H:%M:%S'}-->" />
        <!--▲お問い合わせフォーム-->
        </form>
    </div>
</div>
<!--▲CONTENTS-->
<!--{$tpl_clickAnalyzer}-->
<script src="<!--{$smarty.const.ROOT_URLPATH}-->js/contact.js" type="text/javascript"></script>
