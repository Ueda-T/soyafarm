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
		<h1><img src="<!--{$TPL_URLPATH}-->img/rohto/order_title_step2.gif" width="960" height="70" alt="購入手続き：ご注文情報確認"></h1>

		<p class="intro">現在、定期購入で使用されている<br />
			クレジットカードを使用される場合は、「変更しない」ボタンをクリックしてください。<br />ご確認の上、画面下の「<!--{if $use_module}-->次へ<!--{else}-->注文する<!--{/if}-->」ボタンをクリックしてください。
			クレジットカードを変更される場合は、「変更する」ボタンをクリックしてください。<br />
			※現在、定期購入で使用されているクレジットカードも今回変更されるクレジットカードに変更となります。
		</p>

        <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="confirm" />
        <input type="hidden" name="change_mode" value="no_change" />
        <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />

		<div class="wrapCoan">
			<div class="orderBtn">
				<p class="modoru">
						<a href="./confirm.php" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/rohto/btn_back_ov.gif', 'back04-top')" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/rohto/btn_back.gif', 'back04-top')"><img src="<!--{$TPL_URLPATH}-->img/rohto/btn_back.gif" alt="戻る" border="0" name="back04-top" id="back04-top" /></a>
				</p>
					<a href="javascript:void(0);" onclick="return fnCheckSubmit('no_change');"><img src="<!--{$TPL_URLPATH}-->img/rohto/btn_next.gif" alt="変更しない" name="next-top" id="next-top" class="swp" /></a>
					<a href="javascript:void(0);" onclick="return fnCheckSubmit('change');"><img src="<!--{$TPL_URLPATH}-->img/rohto/btn_buy.gif" alt="変更する" name="next-top" id="next-top" class="swp" /></a>
			</div>
		</div>

        </form>
    </div>
</div>
<!--▲CONTENTS-->
