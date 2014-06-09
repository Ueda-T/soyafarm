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
<h2 class="spNaked">お買い物カゴ<span>4 / 4</span></h2>
		<div class="estimate">ご注文はまだ完了していません。</div>
		<p class="naked">
		ご注文ありがとうございます。<br />
		前回購入時に使用したクレジットカードで決済する場合は、「前回使用したカードで決済」ボタンをクリックしてください。<br />
			別のクレジットカードで決済する場合は、「クレジットカードを変更する」ボタンをクリックしてください。
		</p>
		<p class="naked attention" style="margin-top:10px;">
		クレジットカードを変更すると、お申し込み中の定期購入に使用されているクレジットカードも変更されます。
		</p>

        <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="confirm" />
        <input type="hidden" name="change_mode" value="no_change" />
        <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />

		<p style="margin:10px auto;">
				<a href="javascript:void(0);" onclick="return fnCheckSubmit('no_change');"class="btnOrange" style="width:auto;text-decoration:none;">前回使用したカードで決済</a>
		</p>
		<p style="margin:10px auto;">
				<a href="javascript:void(0);" onclick="return fnCheckSubmit('change');" class="btnOrange" style="width:auto;text-decoration:none;">クレジットカードを変更する</a>
		</p>
		<p style="margin:10px auto 20px auto;">
			<a href="./confirm.php" class="btnGray02">戻る</a>
		</p>

        </form>
</section>
<!--▲CONTENTS-->
