<!--▼CONTENTS-->
<!--{include file="`$smarty.const.TEMPLATE_REALDIR`mypage/pankuzu.tpl"}-->

<div id="mainMyPage">
	<h1><img src="<!--{$TPL_URLPATH}-->img/rohto/mypage_title_sub6.gif" width="700" height="70" alt="登録削除" /></h1>

    <form name="form1" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="refusal_transactionid" value="<!--{$refusal_transactionid}-->" />
    <input type="hidden" name="mode" value="complete" />

    <p class="nakedC">登録削除手続きを実行してもよろしいでしょうか？</p>

	<div class="formWithdrawal">
	    <p class="nakedC">
	             <a href="javascript:void(0);" onclick="document.form1.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/rohto/withdrawal.gif" alt="はい、登録削除します" name="refuse_do" id="refuse_do" class="swp" /></a>
	    </p>
	</div>

    </form>
</div>
<!--▲CONTENTS-->
