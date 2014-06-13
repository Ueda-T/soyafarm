<!--▼CONTENTS-->
<!--{* -*- coding: utf-8-unix; -*- *}-->
<script type="text/javascript">
var get_zip_addr = "<!--{$smarty.const.INPUT_ZIP_URLPATH}-->";
</script>
<script src="<!--{$TPL_URLPATH}-->js/pschecker.js" type="text/javascript"></script>
<script src="<!--{$smarty.const.ROOT_URLPATH}-->js/mypage_basic.js" type="text/javascript"></script>

<section id="mainMyPage">
	<h2 class="spNaked">ご登録内容の変更(確認ページ)</h2>

	<p class="naked">以下のご登録内容をご確認いただき、よろしければ「登録」ボタンを押してください。</p>

    <div class="wrapForm">
        <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
		<input type="hidden" name="mode" value="complete" />
        <input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id|h}-->" />

		<!--{foreach from=$arrForm key=key item=item}-->
			<!--{if $key ne "mode" && $key ne "subm"}-->
			<input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
			<!--{/if}-->
		<!--{/foreach}-->

        <table summary="会員登録内容変更" class="tblOrder delivname" style="width:100%;">
            <tr>
                <th>メールアドレス</th>
            </tr>
            <tr>
                <td><!--{$arrForm.email|escape:'hexentity'}--></td>
            </tr>
            <tr>
                <th>パスワード</th>
            </tr>
            <tr>
				<td><!--{$passlen}--></td>
            </tr>
        </table>

		<p class="btn" style="margin-top:1em;">
			<a href="javascript:void(0);" onclick="document.form1.submit();return false;" class="btnBlue">登録</a>
		</p>
		<p style="margin:1em 0;">
			<a href="javascript:void(0);" onclick="fnModeSubmit('return', '', ''); return false;" class="btnGray03">戻る</a>
		</p>
        </form>
    </div>
</section>
<!--▲CONTENTS-->
