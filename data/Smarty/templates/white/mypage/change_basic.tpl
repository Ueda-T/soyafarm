<!--{* -*- coding: utf-8-unix; -*- *}-->
<script type="text/javascript">
var get_zip_addr = "<!--{$smarty.const.INPUT_ZIP_URLPATH}-->";
</script>
<script src="<!--{$TPL_URLPATH}-->js/pschecker.js" type="text/javascript"></script>
<script src="<!--{$smarty.const.ROOT_URLPATH}-->js/mypage_basic.js" type="text/javascript"></script>
<!--▼CONTENTS-->
<!--{include file="`$smarty.const.TEMPLATE_REALDIR`mypage/pankuzu.tpl"}-->

<div id="mainMyPage">
	<h1><img src="<!--{$TPL_URLPATH}-->img/rohto/mypage_title_sub2.gif" width="700" height="70" alt="ご登録内容の変更" /></h1>

	<!--{if !$tpl_disable_logout}-->
	<form name="header_login_form" id="header_login_form" method="post" action="<!--{$smarty.const.ROOT_URLPATH}-->frontparts/login_check.php" onsubmit="return fnCheckLogin('header_login_form')">
		<input type="hidden" name="mode" value="login" />
		<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
		<input type="hidden" name="url" value="<!--{$smarty.server.REQUEST_URI|h}-->" />
		<p class="logout">
			<a href="javascript:void(0);" onclick="fnFormModeSubmit('header_login_form', 'logout', '', ''); return false;"><img src="<!--{$TPL_URLPATH}-->img/rohto/logout.gif" alt="ログアウト" class="swp" /></a>
		</p>
	</form>
	<!--{/if}-->

	<p class="intro">メールアドレスとパスワードの変更を行います。</p>

    <div class="wrapForm">
        <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="confirm" />
        <input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id|h}-->" />
        <table summary="会員登録内容変更 " class="delivname">
            <tr>
                <th>メールアドレス</th>
                <td colspan="2">
                    <!--{assign var=key1 value="email"}-->
                    <!--{assign var=key2 value="email02"}-->
                    <!--{if $arrErr[$key1]}-->
                    <div class="attention"><!--{$arrErr[$key1]}--></div>
                    <!--{/if}-->
                    <input type="text" name="<!--{$key1}-->" id="email" value="<!--{$arrForm[$key1]|h}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box300 top" /><span id="email-navi">形式に誤りがあります</span><br />
                    <span class="attention mini">確認のため、もう一度ご入力ください。</span><br />
                    <!--{if $arrErr[$key2]}-->
                    <div class="attention"><!--{$arrErr[$key2]}--></div>
                    <!--{/if}-->
                    <input type="text" name="<!--{$key2}-->" id="email02" value="<!--{$arrForm[$key2]|h}-->" style="<!--{$arrErr[$key1]|cat:$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;" class="box300" /><span id="email02-navi">形式に誤りがあります</span><br />
                </td>
            </tr>

            <tr id="passwordSection">
                <th>新しいパスワード</th>
                <td>
                    <div class="password-container">
                        <p><span class="attention mini">半角<!--{$smarty.const.PASSWORD_MIN_LEN}-->～<!--{$smarty.const.PASSWORD_MAX_LEN}-->文字でお願いします。</span></p>
					    <!--{if $arrErr.password}-->
					    <div class="attention"><!--{$arrErr.password}--></div>
					    <!--{/if}-->
                        <input class="strong-password" type="password" name="password" value="<!--{$arrForm.password|h}-->" maxlength="<!--{$smarty.const.PASSWORD_MAX_LEN}-->" style="<!--{$arrErr.password|sfGetErrorColor}-->" class="box120" />
                        <p><span class="attention mini">確認のため、もう一度入力ください。</span></p>
					    <!--{if $arrErr.password02}-->
					    <div class="attention"><!--{$arrErr.password02}--></div>
					    <!--{/if}-->
                        <input class="strong-password" type="password" name="password02" value="<!--{$arrForm.password02|h}-->" maxlength="<!--{$smarty.const.PASSWORD_MAX_LEN}-->" style="<!--{$arrErr.password|cat:$arrErr.password02|sfGetErrorColor}-->" class="box120" />
                    </div>
                </td>
                <td>
                    <div class="chkIndicatorBox">
                        <p class="tit">パスワードの安全性</p>
                        <div class="password-container">
                            <div class="strength-indicator clearfix">
                                <div class="labelBox">強度：</div>
                                <div class="meter"></div>
                            </div>
                        </div>
                    </div>
                </td>
        </tr>
        </table>
        <p class="btn">
            <a href="javascript:void(0);" onclick="document.form1.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/rohto/kakunin02.gif" alt="確認" name="refusal" id="refusal" class="swp" /></a>
        </p>
        </form>
    </div>
</div>
<!--▲CONTENTS-->
