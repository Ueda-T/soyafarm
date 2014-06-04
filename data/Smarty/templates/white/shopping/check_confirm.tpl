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
<div id="undercolumn">
    <div id="undercolumn_shopping">
		<h1><img src="<!--{$TPL_URLPATH}-->img/soyafarm/order_title_step3.gif" alt="購入手続き：クレジットカード決済"></h1>

		<div class="attentionBox">
			<p>お支払い手続きはまだ完了していません。</p>
		</div>

		<p class="intro">ご注文ありがとうございます。<br />
		前回購入時に使用したクレジットカードで決済する場合は、「前回使用したカードで決済」ボタンをクリックしてください。<br />
			別のクレジットカードで決済する場合は、「クレジットカードを変更する」ボタンをクリックしてください。
		</p>
		<div class="wrapResult mt10">
			<div class="wrapResultEle">
				<h3 class="attention">定期購入中のお客様</h3>
				<p>
				クレジットカードを変更すると、お申し込み中の定期購入に使用されているクレジットカードも変更されます。
			</p>
			</div>
		</div>

        <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="confirm" />
        <input type="hidden" name="change_mode" value="no_change" />
        <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />

		<div class="wrapCoan">
			<div class="orderBtn">
				<p class="left clearfix">
					<span class="f-right" style="width:600px;float:right;text-align:right;">
					<a href="javascript:void(0);" onclick="return fnCheckSubmit('no_change');"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_nochange.gif" alt="変更しない" name="next-top" id="next-top" class="swp" /></a><br />
					<a href="javascript:void(0);" onclick="return fnCheckSubmit('change');"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_change.gif" alt="変更する" name="next-top" id="next-top" class="swp" style="margin-top:15px;" /></a>
					</span>
						<a href="./confirm.php"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_back.gif" alt="戻る" border="0" name="back04-top" id="back04-top" /></a>
				</p>
			</div>
		</div>

        </form>
    </div>
</div>
<!--▲CONTENTS-->
