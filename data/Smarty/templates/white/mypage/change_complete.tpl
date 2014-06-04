<!--▼CONTENTS-->
<!--{include file="`$smarty.const.TEMPLATE_REALDIR`mypage/pankuzu.tpl"}-->

<div id="mainMyPageTop">
	<!--{if !$tpl_disable_logout}-->
	<form name="header_login_form" id="header_login_form" method="post" action="<!--{$smarty.const.ROOT_URLPATH}-->frontparts/login_check.php" onsubmit="return fnCheckLogin('header_login_form')">
		<input type="hidden" name="mode" value="login" />
		<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
		<input type="hidden" name="url" value="<!--{$smarty.server.REQUEST_URI|h}-->" />
		<p class="logout">
			<a href="javascript:void(0);" onclick="fnFormModeSubmit('header_login_form', 'logout', '', ''); return false;"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/logout.gif" alt="ログアウト" class="swp" /></a>
		</p>
	</form>
	<!--{/if}-->

	<div class="wrapCustomer">
		<div class="wrapResult">
			<div class="wrapResultEle">
				<h2 class="result">お客様情報の変更を受付ました。</h2>
				<ul class="note">
					<li>※既にいただいています（準備中も含む）ご注文の注文者ならびに配送先の情報は変更されませんので、変更が必要な場合は、<br>
					<a href="<!--{$TPL_URLPATH}-->contact/" class="link">お問い合わせフォーム</a>までご連絡ください。</li>
				</ul>

				<div class="finishedRegular">
					<h3>定期購入中のお客様</h3>
					<p class="naked">お届け先変更をされたお客様で、定期購入をお申し込み中のお客様は、お手数ですが、ご変更後は、<br>
					<a href="<!--{$TPL_URLPATH}-->contact/" class="link">お問い合わせフォーム</a>までご連絡ください。お電話・FAXでも承っております。
					</p>
					<p class="naked">
						TEL：フリーダイヤル0120-39-3009（受付時間9:00～19:00、日・祝休み）<br />
						FAX：フリーダイヤル0120-39-4090（24時間365日受付）
					</p>
				</div>
			</div><!--／wrapResultEle-->
		</div>

		<a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/">
			<img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_back.gif" alt="戻る" name="back" id="back" class="swp" />
		</a>

	<!--{*
	    <div id="mycontents_area">
	        <h3><!--{$tpl_subtitle|h}--></h3>

	        <div id="complete_area">
	            <div class="message">
	                会員登録内容の変更が完了いたしました。<br />
	            </div>
	            <p>今後ともご愛顧賜りますようよろしくお願い申し上げます。</p>
	        </div>
	    </div>
	*}-->
	</div>
</div>
<!--▲CONTENTS-->
