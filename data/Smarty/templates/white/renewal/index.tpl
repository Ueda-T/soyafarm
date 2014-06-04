<h1 class="bscW">パスワードの再発行</h1>
<div class="wrapCustomer">
<p class="naked">いつも、ロート通販をご利用いただきまして、誠にありがとうございます。<br>
2014年3月17日のシステムリニューアルに伴い、以前ご利用のお客さまには、初回ログイン時にパスワードの再設定を行っていただく必要がございます。<br>
大変お手数ですが、ご登録時のメールアドレスとご登録されたお名前を入力して「次へ」ボタンをクリックしてください。</p>
<div class="alert2">
<p class="nakedRed" style="padding:10px 0; font-weight:bold;">初回ログイン用のパスワードをメールにて発行いたします。</p>
</div><!--/alert2-->

<!--{if $arrErr.name || $arrErr.email}-->
<p class="error">ご入力内容にエラーがあります。内容とエラーメッセージをご確認いただき、再度ご入力ください。</p>
<!--{/if}-->

    <form action="?" method="post" name="form1">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="mail_check" />

    <div class="wrapForm">
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
				<td>
					<div>
	                    <input type="text" name="email" value="<!--{$arrForm.email|default:$tpl_login_email|h}-->" class="box300" style="<!--{$arrErr.email|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;&nbsp;<span style="color:#ea673b;">半角英数</span>
	                </div>
				</td>
			</tr>
			<tr>
				<th>ご登録済みのお名前(漢字氏名)</th>
				<td>
					<div>
	                    <input type="text" class="box240" name="name" value="<!--{$arrForm.name|default:''|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN*2}-->" style="<!--{$arrErr.name|sfGetErrorColor}-->; ime-mode: auto;" />&nbsp;&nbsp;<span style="color:#ea673b;">例：呂登 太郎</span>
	                </div>
				</td>
			</tr>
		</table>
	    <p class="btn"><input type="image" src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_next.gif" alt="次へ" name="next" id="next" class="swp" /></p>
    </div>
    </form>
</div><!--/wrapCustomer-->