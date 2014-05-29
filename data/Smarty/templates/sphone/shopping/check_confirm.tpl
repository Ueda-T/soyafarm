<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/facebox.js"></script>
<link rel="stylesheet" type="text/css" href="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/facebox.css" media="screen" />
<script type="text/javascript">//<![CDATA[
var send = true;

function fnCheckSubmit(chg) {
    if(send) {
        send = false;
        document.form1.change_mode.value = chg;
        document.form1.submit();
        return true;
    } else {
        alert("只今、処理中です。しばらくお待ち下さい。");
        return false;
    }
}

$(document).ready(function() {
    $('a.expansion').facebox({
        loadingImage : '<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/loading.gif',
        closeImage   : '<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/closelabel.png'
    });
});
//]]></script>

<!--CONTENTS-->
<section id="undercolumn">
<h2 class="spNaked"><img src="<!--{$TPL_URLPATH}-->img/rohto/icon_cart.gif" width="23" height="16">お買い物カゴ<span>4 / 4</span></h2>
		<div class="estimate">ご注文はまだ完了していません。</div>
		<p class="naked">
		現在、定期購入で使用されている<br />
		クレジットカードを使用される場合は、「変更しない」ボタン<br />
		クレジットカードを変更される場合は、「変更する」ボタン<br />
		を押してください。
		現在、定期購入で使用されているクレジットカードも今回変更されるクレジットカードに変更となります。
		</p>

        <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="confirm" />
        <input type="hidden" name="change_mode" value="no_change" />
        <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />

		<p style="margin:10px auto;">
				<a href="javascript:void(0);" onclick="return fnCheckSubmit('no_change');"class="btnOrange" style="width:auto;text-decoration:none;">変更しない</a>
				<a href="javascript:void(0);" onclick="return fnCheckSubmit('change');" class="btnOrange" style="width:auto;text-decoration:none;">変更する</a>
		</p>
		<p style="margin:10px auto 20px auto;">
			<a href="./confirm.php" class="btnGray02">戻る</a>
		</p>

        </form>
</section>
<!--▲CONTENTS-->
