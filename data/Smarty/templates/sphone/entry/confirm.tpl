<script type="text/javascript">//<![CDATA[
var send = true;

function fnCheckSubmit() {
    if(send) {
        send = false;
        document.form1.submit();
        return true;
    } else {
        alert("只今、処理中です。しばらくお待ち下さい。");
        return false;
    }
}

//]]></script>
<!--▼CONTENTS-->
<div id="undercolumn">
    <div id="undercolumn_entry">
		<h2 class="spNaked">購入手続き</h2>
		<div class="wrapCoan">
        <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="complete">
        <!--{foreach from=$arrForm key=key item=item}-->
            <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
        <!--{/foreach}-->

		<p style="margin:10px 0;">【お客様情報】</p>
        <table summary="入力内容確認" class="tblOrder">
            <tr>
                <th><span>漢字氏名</span></th>
            </tr>
            <tr>
                <td>
                    <!--{$arrForm.name|h}-->
                </td>
            </tr>
            <tr>
                <th><span>ｶﾀｶﾅ氏名</span></th>
            </tr>
            <tr>
                <td>
                    <!--{$arrForm.kana|h}-->
                </td>
            </tr>
            <tr>
                <th><span>電話番号</span></th>
            </tr>
            <tr>
                <td>
                    <!--{$arrForm.tel|h}-->
                </td>
            </tr>
            <tr>
                <th><span>住所</span></th>
            </tr>
            <tr>
                <td>
                    〒<!--{$arrForm.zip|h}-->
                    <!--{$arrPref[$arrForm.pref]|h}--><!--{$arrForm.addr01|h}--><!--{$arrForm.addr02|h}-->
                </td>
            </tr>
<!--{*
            <tr>
                <th><span><img src="<!--{$TPL_URLPATH}-->img/rohto/spacer.gif" alt="" width="31" height="13" />FAX</span></th>
            </tr>
            <tr>
                <td>
                    <!--{if strlen($arrForm.fax) > 0}-->
                        <!--{$arrForm.fax|h}-->
                    <!--{else}-->
                        未登録
                    <!--{/if}-->
                </td>
            </tr>
 *}-->
            <tr>
                <th><span>メールアドレス</span></th>
            </tr>
            <tr>
                <td>
                    <a href="mailto:<!--{$arrForm.email|escape:'hex'}-->"><!--{$arrForm.email|escape:'hexentity'}--></a>
                </td>
            </tr>
            <tr>
                <th><span>パスワード</span></th>
            </tr>
            <tr>
                <td><!--{$passlen}--></td>
            </tr>
            <tr>
                <th><span>性別</span></th>
            </tr>
            <tr>
                <td>
                    <!--{if $arrForm.sex eq 1}-->
                    男性
                    <!--{elseif $arrForm.sex eq 2}-->
                    女性
                    <!--{else}-->
                    未登録
                    <!--{/if}-->
                </td>
            </tr>
            <tr>
                <th><span>生年月日</span></th>
            </tr>
            <tr>
                <td>
                    <!--{if strlen($arrForm.year) > 0 && strlen($arrForm.month) > 0 && strlen($arrForm.day) > 0}-->
                        <!--{$arrForm.year|h}-->年<!--{$arrForm.month|h}-->月<!--{$arrForm.day|h}-->日
                    <!--{else}-->
                    未登録
                    <!--{/if}-->
                </td>
            </tr>
<!--{*
            <tr>
                <th><span>パスワードを忘れた時のヒント</span></th>
            </tr>
            <tr>
                <td>
                    質問：<!--{$arrReminder[$arrForm.reminder]|h}--><br />
                    答え：<!--{$arrForm.reminder_answer|h}-->
                </td>
            </tr>
 *}-->
<!--{*
            <tr>
                <th><span>メールマガジン送付について</span></th>
            </tr>
            <tr>
                <td>
                    <!--{if $arrForm.mailmaga_flg eq '0'}-->
                    受け取らない
                    <!--{else}-->
                    受け取る
                    <!--{/if}-->
                </td>
            </tr>
            <tr>
                <th><span>ＤＭ送付について</span></th>
            </tr>
            <tr>
                <td>
                    <!--{if $arrForm.dm_flg eq '0'}-->
                    受け取らない
                    <!--{else}-->
                    受け取る
                    <!--{/if}-->
                </td>
            </tr>
            <tr>
                <th><span>アンケートついて</span></th>
            </tr>
            <tr>
                <td>
                    <!--{if $arrForm.questionnaire eq ''}-->
                    未登録
                    <!--{else}-->
                    <!--{$arrQuestionnaire[$arrForm.questionnaire]|h}-->
                    <!--{if $arrForm.questionnaire_other neq ''}-->
                    <br />
                    <!--{$arrForm.questionnaire_other|h}-->
                    <!--{/if}-->
                    <!--{/if}-->
                </td>
            </tr>
 *}-->
        </table>

<!--{*お届け先指定がある場合表示*}-->
<!--{if $arrForm.other_addr_flg}-->
		<p style="margin:10px 0;">【お届け先】</p>

        <table summary="お届け先入力内容確認" class="tblOrder">
            <tr>
                <th><span>お届け先：漢字氏名</span></th>
            </tr>
            <tr>
                <td>
                    <!--{$arrForm.shipping_name|h}-->
                </td>
            </tr>
            <tr>
                <th><span>お届け先：ｶﾀｶﾅ氏名</span></th>
            </tr>
            <tr>
                <td>
                    <!--{$arrForm.shipping_kana|h}-->
                </td>
            </tr>
            <tr>
                <th><span>お届け先：電話番号</span></th>
            </tr>
            <tr>
                <td>
                    <!--{$arrForm.shipping_tel|h}-->
                </td>
            </tr>
            <tr>
                <th><span>お届け先：住所</span></th>
            </tr>
            <tr>
                <td>
                    〒<!--{$arrForm.shipping_zip|h}-->
                    <!--{$arrPref[$arrForm.shipping_pref]|h}--><!--{$arrForm.shipping_addr01|h}--><!--{$arrForm.shipping_addr02|h}-->
                </td>
            </tr>
        </table>
<!--{/if}-->
		</div>

				<div class="btn">
					<p style="margin:10px;"><a href="javascript:void(0);" onclick="fnCheckSubmit();return false;" class="btnOrange">お届け情報を入力する</a></p>
					<p style="margin:10px;"><a href="?" onclick="fnModeSubmit('return', '', ''); return false;" class="btnGray02">戻る</a></p>
				</div>

        </form>
    </div>
</div>
<!--▲CONTENTS-->
