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
		<h1><img src="<!--{$TPL_URLPATH}-->img/soyafarm/order_title_step1.gif" alt="購入手続き" /></h1>
		<div class="wrapCoan">
		<h3 class="order">お客様情報</h3>
        <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="complete">
        <!--{foreach from=$arrForm key=key item=item}-->
            <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
        <!--{/foreach}-->

        <table summary="入力内容確認" class="tblOrder">
            <colgroup width="30%"></colgroup>
            <colgroup width="70%"></colgroup>
            <tr>
                <th><span>漢字氏名</span></th>
                <td>
                    <!--{$arrForm.name|h}-->
                </td>
            </tr>
            <tr>
                <th><span>ｶﾀｶﾅ氏名</span></th>
                <td>
                    <!--{$arrForm.kana|h}-->
                </td>
            </tr>
            <tr>
                <th><span>電話番号</span></th>
                <td>
                    <!--{$arrForm.tel|h}-->
                </td>
            </tr>
            <tr>
                <th><span>住所</span></th>
                <td>
                    〒<!--{$arrForm.zip|h}-->
                    <!--{$arrPref[$arrForm.pref]|h}--><!--{$arrForm.addr01|h}--><!--{$arrForm.addr02|h}-->
                </td>
            </tr>
<!--{*
            <tr>
                <th><span><img src="<!--{$TPL_URLPATH}-->img/soyafarm/spacer.gif" alt="" width="31" height="13" />FAX</span></th>
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
                <td>
                    <a href="mailto:<!--{$arrForm.email|escape:'hex'}-->"><!--{$arrForm.email|escape:'hexentity'}--></a>
                </td>
            </tr>
            <tr>
                <th><span>パスワード
                </span></th>
                <td><!--{$passlen}--></td>
            </tr>
            <tr>
                <th><span>性別</span></th>
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
                <td>
                    質問：<!--{$arrReminder[$arrForm.reminder]|h}--><br />
                    答え：<!--{$arrForm.reminder_answer|h}-->
                </td>
            </tr>
 *}-->
<!--{*
            <tr>
                <th><span>メールマガジン送付について</span></th>
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
		<h3 class="order">お届け先</h3>

        <table summary="お届け先入力内容確認" class="tblOrder">
            <tr>
                <th><span>お届け先：漢字氏名</span></th>
                <td>
                    <!--{$arrForm.shipping_name|h}-->
                </td>
            </tr>
            <tr>
                <th><span>お届け先：ｶﾀｶﾅ氏名</span></th>
                <td>
                    <!--{$arrForm.shipping_kana|h}-->
                </td>
            </tr>
            <tr>
                <th><span>お届け先：電話番号</span></th>
                <td>
                    <!--{$arrForm.shipping_tel|h}-->
                </td>
            </tr>
            <tr>
                <th><span>お届け先：住所</span></th>
                <td>
                    〒<!--{$arrForm.shipping_zip|h}-->
                    <!--{$arrPref[$arrForm.shipping_pref]|h}--><!--{$arrForm.shipping_addr01|h}--><!--{$arrForm.shipping_addr02|h}-->
                </td>
            </tr>
        </table>
<!--{/if}-->
		</div>

		<div class="wrapCoan">
			<div class="orderBtn">
				<p class="left">
					<span class="f-right" style="width:600px;float:right;text-align:right;">
					<a href="javascript:void(0);" onclick="fnCheckSubmit();return false;"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_cart_next.gif" alt="お届け情報を入力する" border="0" name="send" id="send" class="swp" /></a>
					</span>
				<a href="?" onclick="fnModeSubmit('return', '', ''); return false;"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_back.gif" alt="戻る" border="0" name="back" id="back" class="swp" /></a>
				</p>
			</div>
		</div>

        </form>
    </div>
</div>
<!--▲CONTENTS-->
