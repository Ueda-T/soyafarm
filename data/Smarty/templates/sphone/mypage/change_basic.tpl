<!--▼CONTENTS-->
<!--{* -*- coding: utf-8-unix; -*- *}-->
<script type="text/javascript">
var get_zip_addr = "<!--{$smarty.const.INPUT_ZIP_URLPATH}-->";
</script>
<script src="<!--{$TPL_URLPATH}-->js/pschecker.js" type="text/javascript"></script>
<script src="<!--{$smarty.const.ROOT_URLPATH}-->js/mypage_basic.js" type="text/javascript"></script>

<div id="mainMyPage">
	<h2 class="spNaked"><!--{$tpl_title}--></h2>

	<!--{if $tpl_navi != ""}-->
		<!--{include file=$tpl_navi}-->
	<!--{else}-->
		<!--{include file=`$smarty.const.TEMPLATE_REALDIR`mypage/navi.tpl}-->
	<!--{/if}-->

	<p class="naked mb10">メールアドレスとパスワードの変更を行います。<br /><img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_hisu.gif" alt="必須"> 印の箇所は、必ず入力してください。</p>

    <div class="wrapForm">
        <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="confirm" />
        <input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id|h}-->" />
        <table summary="メールアドレスとパスワード変更" class="tblOrder delivname" style="width:100%;">
            <tr>
                <th>メールアドレス <img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_hisu.gif" alt="必須" /></th>
            </tr>
            <tr>
                <td>
                    <!--{assign var=key1 value="email"}-->
                    <!--{assign var=key2 value="email02"}-->
                    <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
                    <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
                    <!--{/if}-->
                    <input type="text" name="<!--{$key1}-->" id="email" value="<!--{$arrForm[$key1]|h}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box300 top" /><span id="email-navi">形式に誤りがあります</span><br />
                    <span class="attention mini">確認のため、もう一度ご入力ください。</span><br />
                    <input type="text" name="<!--{$key2}-->" id="email02" value="<!--{$arrForm[$key2]|h}-->" style="<!--{$arrErr[$key1]|cat:$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;" class="box300" /><span id="email02-navi">形式に誤りがあります</span><br />
                </td>
            </tr>

            <tr id="passwordSection">
                <th>パスワード <img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_hisu.gif" alt="必須" /></th>
            </tr>
            <tr id="passwordSection">
                <td>
                    <!--{if $arrErr.password || $arrErr.password02}-->
                        <div class="attention"><!--{$arrErr.password}--><!--{$arrErr.password02}--></div>
                    <!--{/if}-->
                    <div class="password-container">
                        <p><span class="attention mini">半角<!--{$smarty.const.PASSWORD_MIN_LEN}-->～<!--{$smarty.const.PASSWORD_MAX_LEN}-->文字でお願いします。</span></p>
                        <input class="strong-password" type="password" name="password" value="<!--{$arrForm.password|h}-->" maxlength="<!--{$smarty.const.PASSWORD_MAX_LEN}-->" style="<!--{$arrErr.password|sfGetErrorColor}-->" class="box120" />
                        <p><span class="attention mini">確認のため、もう一度入力ください。</span></p>
                        <input class="strong-password" type="password" name="password02" value="<!--{$arrForm.password02|h}-->" maxlength="<!--{$smarty.const.PASSWORD_MAX_LEN}-->" style="<!--{$arrErr.password|cat:$arrErr.password02|sfGetErrorColor}-->" class="box120" />
                    </div>
                    <div class="chkIndicatorBox ml0">
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

        <p style="margin:10px auto;">
            <a href="javascript:void(0);" onclick="document.form1.submit();return false;" class="btnBlue">確認</a>
        </p>
        </form>
    </div>
</div>
<!--▲CONTENTS-->
