<script type="text/javascript">//<![CDATA[
var send = true;

function fnCheckSubmit() {
    if(send) {
        send = false;
        document.form1.submit();
        return true;
    } else {
        alert("只今、処理中です。しばらくお待ち下さい。");
        return false;
    }
}
//]]></script>

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
*}-->

        <p class="nakedC" style="margin-bottom:20px;">下記入力内容で送信してもよろしいでしょうか？<br />
            よろしければ、一番下の「送信」ボタンをクリックしてください。</p>

        <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="complete" />
        <!--{foreach key=key item=item from=$arrForm}-->
            <!--{if $key ne 'mode'}-->
                <input type="hidden" name="<!--{$key}-->" value="<!--{$item.value|h}-->" />
            <!--{/if}-->
        <!--{/foreach}-->

        <!--▼お問い合わせ内容確認-->
            <div class="wrapForm">

            <table summary="お問い合わせ">
            <colgroup width="30%"></colgroup>
            <colgroup width="70%"></colgroup>
                <tr>
                    <th>件名</th>
                    <td><!--{$arrSubject[$arrForm.subject.value]}--></td>
                <tr>
                    <th>お名前</th>
                    <td><!--{$arrForm.name.value|h}--></td>
                </tr>
                <tr>
                    <th>メールアドレス</th>
                    <td><!--{$arrForm.email.value|escape:'hexentity'}--></td>
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
                    <th>お問い合わせ日</th>
                    <td><!--{$arrForm.now.value|h}--></td>
                </tr>
                <tr>
                    <th>内容</th>
                    <td><!--{$arrForm.contents.value|h|nl2br}--></td>
                </tr>
            </table>

            <p class="btn">
                    <a href="javascript:void(0);" onclick="fnModeSubmit('return', '', ''); return false;"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_back.gif" alt="戻る" name="back02" id="back02" class="swp" /></a>
                    <a href="javascript:void(0);" onclick="return fnCheckSubmit();"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_send.gif" alt="送信" name="send" id="send" class="swp" /></a>
            </p>
            </div><!--／wrapForm-->

        <!--▲お問い合わせ内容確認-->
	<input type="hidden" name="token" value="<!--{$token}-->" />
        </form>
    </div>
</div>
<!--▲CONTENTS-->
