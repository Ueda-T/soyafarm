<script type="text/javascript">
var get_zip_addr = "<!--{$smarty.const.INPUT_ZIP_URLPATH}-->";
</script>
<script src="<!--{$TPL_URLPATH}-->js/pschecker.js" type="text/javascript"></script>
<script src="<!--{$TPL_URLPATH}-->js/jquery.autoKana.js" type="text/javascript"></script>
<script src="<!--{$smarty.const.ROOT_URLPATH}-->js/mypage_basic.js" type="text/javascript"></script>
<!--▼CONTENTS-->
<!--{include file="`$smarty.const.TEMPLATE_REALDIR`mypage/pankuzu.tpl"}-->


<!--{if $tpl_navi != ""}-->
	<!--{include file=$tpl_navi}-->
<!--{else}-->
	<!--{include file=`$smarty.const.TEMPLATE_REALDIR`mypage/navi.tpl}-->
<!--{/if}-->

<div id="mainMyPage">
	<h1><img src="<!--{$TPL_URLPATH}-->img/soyafarm/mypage_title_sub1.gif" alt="ご登録内容の変更" /></h1>



	<p class="intro">ご登録内容の変更を行います。<img src="<!--{$TPL_URLPATH}-->img/soyafarm/check02.gif" alt="必須">印の箇所は、必ず入力してください。</p>

    <div class="wrapForm">
        <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="confirm" />
        <input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id|h}-->" />
        <input type="hidden" name="birth" value="<!--{$arrForm.birth|h}-->" />

        <table summary="会員登録内容変更 " class="delivname">
		<colgroup width="30%"></colgroup>
		<colgroup width="70%"></colgroup>
		<tr>
			<th><img src="<!--{$TPL_URLPATH}-->img/soyafarm/spacer.gif" alt="" width="31" height="13" />顧客番号</th>
			<td>
			  <!--{assign var=key value="customer_id"}-->
			  <!--{$arrForm[$key]|h}-->
			</td>
		</tr>
		<tr>
			<th><img src="<!--{$TPL_URLPATH}-->img/soyafarm/check02.gif" alt="必須" />お名前</th>
			<td>
				<!--{assign var=key1 value="`$prefix`name"}-->
				<!--{if $arrErr[$key1]}-->
					<div class="attention"><!--{$arrErr[$key1]}--></div>
				<!--{/if}-->
				<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="16" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: auto;" class="box240" id="userName" />&nbsp;
			</td>
		</tr>
		<tr>
			<th><img src="<!--{$TPL_URLPATH}-->img/soyafarm/check02.gif" alt="必須" />お名前(フリガナ)</th>
			<td>
				<!--{assign var=key1 value="`$prefix`kana"}-->
				<!--{if $arrErr[$key1]}-->
					<div class="attention"><!--{$arrErr[$key1]}--></div>
				<!--{/if}-->
				<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="15" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: auto;" class="box240" id="userFurigana" />&nbsp;
			</td>
		</tr>
		<tr>
			<th><img src="<!--{$TPL_URLPATH}-->img/soyafarm/check02.gif" alt="必須" />電話番号</th>
			<td>
				<!--{assign var=key1 value="`$prefix`tel"}-->
				<!--{if $arrErr[$key1]}-->
					<div class="attention"><!--{$arrErr[$key1]}--></div>
				<!--{/if}-->
				<input type="text" name="<!--{$key1}-->" id="tel" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN*3}-->" size="<!--{$smarty.const.TEL_ITEM_LEN*3}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box180" />
			</td>
		</tr>
		<tr>
			<th><img src="<!--{$TPL_URLPATH}-->img/soyafarm/check02.gif" alt="必須" />郵便番号</th>
			<td>
				<!--{assign var=key value="zip"}-->
				<!--{assign var=key3 value="`$prefix`pref"}-->
				<!--{assign var=key4 value="`$prefix`addr01"}-->
				<!--{assign var=key5 value="`$prefix`addr02"}-->
				<!--{assign var=key6 value="`$prefix`house_no"}-->

				<!--{if $arrErr[$key]}-->
					<div class="attention"><!--{$arrErr[$key]}--></div>
				<!--{/if}-->

				<p class="top">〒&nbsp;<input type="text" name="<!--{$key}-->" id="<!--{$key}-->" value="<!--{$arrForm[$key]|h}-->" maxlength="<!--{math equation="a+b" a=$smarty.const.ZIP_LEN b=1}-->" size="<!--{$smarty.const.ZIP_LEN}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;<img src="<!--{$TPL_URLPATH}-->img/soyafarm/zip.gif" alt="住所自動入力" id="easy" /></p>
				郵便番号をご入力後、ボタンを押してください。ご住所が自動で入力されます。<br />
				[<a href="http://search.post.japanpost.jp/zipcode/" target="_blank"><span class="mini">郵便番号検索ページヘ</span></a>]
			</td>
		</tr>

		<tr>
			<th><img src="<!--{$TPL_URLPATH}-->img/soyafarm/check02.gif" alt="必須" />住所</th>
			<td>
				<!--{if $arrErr[$key3] || $arrErr[$key4] || $arrErr[$key5]}-->
					<div class="attention"><!--{$arrErr[$key3]}--><!--{$arrErr[$key4]}--><!--{$arrErr[$key5]}--></div>
				<!--{/if}-->
				<select name="<!--{$key3}-->" id="pref" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->">
						<option value="" selected="selected">都道府県を選択</option>
						<!--{html_options options=$arrPref selected=$arrForm[$key3]}-->
				</select><br />
				<span id="addr1-navi">制限文字数を超えています</span>
				<p class="top"><div id="addr1-div"><input type="text" name="<!--{$key4}-->" id="addr1" value="<!--{$arrForm[$key4]|h}-->" maxlength="40" class="box300" style="<!--{$arrErr[$key4]|sfGetErrorColor}-->; ime-mode: auto;" /></div><br />
					<!--{$smarty.const.SAMPLE_ADDRESS1}--></p>
				<span id="addr2-navi">制限文字数を超えています</span>
				<p class="top"><input type="text" name="<!--{$key5}-->" id="addr2" value="<!--{$arrForm[$key5]|h}-->" maxlength="40" class="box300" style="<!--{$arrErr[$key5]|sfGetErrorColor}-->; ime-mode: auto;" /><label><input type="checkbox" name="house_no" id="house_no" <!--{if $arrForm[$key6]}-->checked="checked"<!--{/if}-->/>番地なし</label><br />
					<!--{$smarty.const.SAMPLE_ADDRESS2}--></p>
				<p class="mini"><span class="attention">住所は2つに分けてご記入ください。マンション名は必ず記入してください。</span></p>
			</td>
		</tr>
			<input type="hidden" name="email" value="<!--{$arrForm.email}-->" />
			<input type="hidden" name="email02" value="<!--{$arrForm.email02}-->" />
			<tr>
				<th><img src="<!--{$TPL_URLPATH}-->img/soyafarm/spacer.gif" alt="" width="31" height="13" />メールアドレス</th>
				<td>
				<!--{$arrForm.email}--><p class="mini"><span class="attention">※メールアドレスとパスワードの変更は<a href="change_basic.php">こちら</a>から</span></p>
				</td>
			</tr>
			<tr>
				<th><img src="<!--{$TPL_URLPATH}-->img/soyafarm/check02.gif" alt="必須" />性別</th>
				<td>
					<!--{assign var=key1 value="`$prefix`sex"}-->
					<!--{if $arrErr[$key1]}-->
						<div class="attention"><!--{$arrErr[$key1]}--></div>
					<!--{/if}-->

					<span style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
						<input type="radio" id="man" name="<!--{$key1}-->" value="1" <!--{if $arrForm[$key1] eq 1}--> checked="checked" <!--{/if}--> /><label for="man">男性</label><input type="radio" id="woman" name="<!--{$key1}-->" value="2" <!--{if $arrForm[$key1] eq 2}--> checked="checked" <!--{/if}--> /><label for="woman">女性</label>
					</span>
				</td>
			</tr>
			<tr>
				<th><img src="<!--{$TPL_URLPATH}-->img/soyafarm/check02.gif" alt="必須" />生年月日</th>
				<td>
					<!--{assign var=errBirth value="`$arrErr.year``$arrErr.month``$arrErr.day`"}-->
					<!--{if $errBirth}-->
						<div class="attention"><!--{$errBirth}--></div>
					<!--{/if}-->
					<!--{if $arrForm.birth && $arrForm.year && $arrForm.month && $arrForm.day}-->
						<!--{$arrForm.year}-->年<!--{$arrForm.month}-->月<!--{$arrForm.day}-->日
						<input type="hidden" name="year" value="<!--{$arrForm.year}-->" />
						<input type="hidden" name="month" value="<!--{$arrForm.month}-->" />
						<input type="hidden" name="day" value="<!--{$arrForm.day}-->" />
				
					<!--{else}-->
					<select name="year" style="<!--{$errBirth|sfGetErrorColor}-->">
						<!--{html_options options=$arrYear selected=$arrForm.year|default:''}-->
					</select>年
					<select name="month" style="<!--{$errBirth|sfGetErrorColor}-->">
						<!--{html_options options=$arrMonth selected=$arrForm.month|default:''}-->
					</select>月
					<select name="day" style="<!--{$errBirth|sfGetErrorColor}-->">
						<!--{html_options options=$arrDay selected=$arrForm.day|default:''}-->
					</select>日
					<!--{/if}-->
				</td>
			</tr>
				<input type="hidden" name="password" value="<!--{$arrForm.password}-->" />
				<input type="hidden" name="password02" value="<!--{$arrForm.password02}-->" />
				<tr>
					<th><img src="<!--{$TPL_URLPATH}-->img/soyafarm/check02.gif" alt="必須" />メールのご案内</th>
					<td>
						<!--{if $arrErr.mailmaga_flg}-->
							<div class="attention"><!--{$arrErr.mailmaga_flg}--></div>
						<!--{/if}-->
						<span style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->">
							<input type="radio" name="mailmaga_flg" value="1" id="mailmaga_flg_1" <!--{if $arrForm.mailmaga_flg eq '1'}--> checked="checked" <!--{/if}--> /><label for="mailmaga_flg_1">受け取る</label><input type="radio" name="mailmaga_flg" value="0" id="mailmaga_flg_0" <!--{if $arrForm.mailmaga_flg eq '0'}--> checked="checked" <!--{/if}--> /><label for="mailmaga_flg_0">受け取らない</label><div id="notReceive" style="display: none;" ><img src="<!--{$TPL_URLPATH}-->img/soyafarm/bnr_ml_merit.gif" alt="受け取りませんか"></a></div>
						</span>
					</td>
				</tr>
        </table>

		<div class="wrapResult">
			<div class="wrapResultEle">
				<div class="finishedRegular">
				<h3 class="result">ご注文後のお客様</h3>
				<ul class="note">
					<li>※既にいただいていますご注文の注文者ならびに配送先の情報は変更されませんので、変更が必要な場合は、<br>
					<a href="<!--{$TPL_URLPATH}-->contact/" class="link">お問い合わせフォーム</a>までご連絡ください。お電話・FAXでも承っております。</li>
				</ul>
				</div>

				<div class="finishedRegular mt20">
					<h3>定期購入中のお客様</h3>
					<p class="naked">お届け先変更をされたお客様で、定期購入をお申し込み中のお客様は、お手数ですが、ご変更後は、<br>
					<a href="<!--{$TPL_URLPATH}-->contact/" class="link">お問い合わせフォーム</a>までご連絡ください。お電話・FAXでも承っております。
					</p>
					<p class="naked">
						TEL：フリーダイヤル0120-39-3009（受付時間9:00～19:00、日・祝休み）<br />
						FAX：フリーダイヤル0120-39-4090（24時間365日受付）
					</p>
				</div>
			</div><!--／wrapResultEle-->
		</div>

        <p class="btn">
            <a href="javascript:void(0);" onclick="document.form1.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_kakunin02.gif" alt="確認" name="refusal" id="refusal" class="swp" /></a>
        </p>
        </form>
    </div>
</div>
<!--▲CONTENTS-->
