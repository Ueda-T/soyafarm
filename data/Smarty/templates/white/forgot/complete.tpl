<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_header.tpl" subtitle="パスワードを忘れた方(完了ページ)"}-->

<div id="window_area">
    <h1>パスワードを忘れた方</h1>
    <p class="naked">パスワードの発行が完了いたしました。<br />
    ご入力いただいたメールアドレスに新しいパスワードをお送りしておりますので、ご確認くださいませ。<br />
        ※下記パスワードは、マイページの「メールアドレスとパスワードの変更」よりご変更いただけます。</p>
<!--{*
    <form action="?" method="post" name="form1">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <div id="forgot">
            <!--{if $smarty.const.FORGOT_MAIL != 1}-->
                    <p><span class="attention"><!--{$arrForm.new_password}--></span></p>
            <!--{else}-->
            <p><span class="attention">ご登録メールアドレスに送付致しました。</span></p>
            <!--{/if}-->
        </div>
        <div class="btn_area">
            <ul>
                <li><a href="javascript:window.close()" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_close_on.jpg','close');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_close.jpg','close');"><img src="<!--{$TPL_URLPATH}-->img/button/btn_close.jpg" alt="閉じる" name="close" id="close" /></a></li>
            </ul>
        </div>
    </form>
*}-->
</div>

<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_footer.tpl"}-->
