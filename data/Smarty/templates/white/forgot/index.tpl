<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_header.tpl" subtitle="パスワードを忘れた方(入力ページ)"}-->

<div id="window_area">
    <h1>パスワードの再発行</h1>
    <p class="naked">ご登録時のメールアドレスと、ご登録されたお名前を入力して「次へ」ボタンをクリックしてください。<br />
    <span class="attention">※新しくパスワードを発行いたしますので、お忘れになったパスワードはご利用できなくなります。</span></p>

	<div class="wrapForm">
    <form action="?" method="post" name="form1">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="mail_check" />

	<table cellspacing="0">
		<tr>
			<th><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" />メールアドレス</th>
			<td>
				<p class="attention"><!--{$arrErr.email}--></p>
				<input type="text" name="email" value="<!--{$arrForm.email|default:$tpl_login_email|h}-->" class="box240" style="<!--{$arrErr.email|sfGetErrorColor}-->; ime-mode: disabled;" />
			</td>
		</tr>
		<tr>
			<th><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" />お名前</th>
			<td>
                <p class="attention">
                    <!--{$arrErr.name}-->
                    <!--{$errmsg}-->
                </p>
                    <input type="text" class="box240" name="name" value="<!--{$arrForm.name|default:''|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN*2}-->" style="<!--{$arrErr.name|sfGetErrorColor}-->; ime-mode: auto;" />
			</td>
		</tr>
	</table>

    <p class="btn">
        <a href="javascript:void(0);" onclick="document.form1.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_next.gif" alt="次へ" name="next" id="next" class="swp" /></a>
    </p>
    </form>
	</div>
</div>
<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_footer.tpl"}-->

