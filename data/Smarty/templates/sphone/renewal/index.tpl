<h2 class="spNaked">パスワードの再発行</h2>
<p class="naked">いつもソヤファームクラブオンラインショップをご利用いただきまして、誠にありがとうございます。<br>
2014年7月1日のシステムリニューアルに伴い、以前ご利用のお客さまには、初回ログイン時にパスワードの再設定を行っていただく必要がございます。<br>
大変お手数ですが、ご登録時のメールアドレスとご登録されたお名前を入力して「次へ」ボタンをクリックしてください。</p>
<div class="alert2">
<p class="nakedRed" style="padding:10px; font-weight:bold;">初回ログイン用のパスワードをメールにて発行いたします。</p>
</div><!--/alert2-->

<!--{if $arrErr.name || $arrErr.email}-->
<p class="attention">ご入力内容にエラーがあります。内容とエラーメッセージをご確認いただき、再度ご入力ください。</p>
<!--{/if}-->

    <form action="?" method="post" name="form1">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="mail_check" />

    <div class="bgGray">
		<table cellspacing="0">
                        <!--{if $errmsg}-->
			<tr>
			<td colspan="2">
	                <span class="error" style="background: #ff0000; color:#ffffff;">
                        <!--{$errmsg}-->
			</span>
			</td>
			</tr>
                        <!--{/if}-->

			<tr>
				<th>ご登録済みのメールアドレス</th>
			</tr>
			<tr>
				<td>
					<div>
	                    <input type="text" name="email" value="<!--{$arrForm.email|default:$tpl_login_email|h}-->" class="box300" style="<!--{$arrErr.email|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;&nbsp;<span style="color:#ea673b;">半角英数</span>
	                </div>
				</td>
			</tr>
			<tr>
				<th>ご登録済みのお名前(漢字氏名)</th>
			</tr>
			<tr>
				<td>
					<div>
	                    <input type="text" class="box240" name="name" value="<!--{$arrForm.name|default:''|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN*2}-->" style="<!--{$arrErr.name|sfGetErrorColor}-->; ime-mode: auto;" />&nbsp;&nbsp;<span style="color:#ea673b;">例：曽谷 丸男</span>
	                </div>
				</td>
			</tr>
		</table>
    </div>
	<p class="btn" style="margin:10px 0;"><a href="javascript:void(0);" onclick="document.form1.submit();return false;" class="btnOrange">次へ</a></p>
    </form>
