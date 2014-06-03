<script type="text/javascript">
var get_zip_addr = "<!--{$smarty.const.INPUT_ZIP_URLPATH}-->";
</script>
<script src="<!--{$TPL_URLPATH}-->js/pschecker.js" type="text/javascript"></script>
<script src="<!--{$TPL_URLPATH}-->js/jquery.autoKana.js" type="text/javascript"></script>
<script src="<!--{$smarty.const.ROOT_URLPATH}-->js/mypage_basic.js" type="text/javascript"></script>
<!--▼CONTENTS-->
<div id="undercolumn">
	<div id="undercolumn_entry">
		<h1><img src="<!--{$TPL_URLPATH}-->img/soyafarm/order_title_step1.gif" alt="購入手続き" /></h1>

		<h3 class="cartListTitle"><img src="<!--{$TPL_URLPATH}-->img/rohto/method_cart_title.gif" width="820" height="31" alt="ご注文商品" /></h3>

		<div class="cartList">
			<table class="cartListTankaiTbl" summary="ご注文商品">
				<tr>
					<th class="alignC">商品名</th>
					<th class="num alignC" nowrap>数量</th>
					<th class="linern alignC">お届け間隔</th>
				</tr>
				<tbody>
				<!--{foreach from=$arrCartItems item=item}-->
					<tr>
						<td><!--{$item.productsClass.name|h}--></td>
						<td class="alignC"><!--{$item.quantity|number_format}--></td>
						<td>
						<!--{if $item.regular_flg == 1}-->
							<!--{$arrCourseCd[$item.course_cd]}--><!--{$arrTodokeKbn[$item.todoke_cycle]}-->
							<!--{if $item.todoke_week_no}-->
								<br />お届け曜日：<!--{$arrTodokeWeekNo[$item.todoke_week_no]}--><!--{$arrTodokeWeek[$item.todoke_week]}-->
							<!--{/if}-->
						<!--{/if}-->
						&nbsp;</td>
					</tr>
				<!--{/foreach}-->
				<!--{* 同梱品情報 *}-->
				<!--{if $tpl_include_product_flg}-->
				<!--{foreach from=$arrIncludeProduct item=item}-->
					<tr>
						<td><!--{$item.product_name|h}--></td>
						<td class="alignC"><!--{$item.quantity|number_format}--></td>
						<td>プレゼント商品</td>
					</tr>
				<!--{/foreach}-->
				<!--{/if}-->
				</tbody>
				<tfoot>
					<!--{*
					<tr class="total3">
						<td colspan="3">数量を変更する場合は、下の「戻る」ボタンで前のページに戻り、必要な数を入力し、「注文数量を決定する」ボタンを押してください。</td>
					</tr>
					*}-->
				</tfoot>
			</table>
		</div>

		<div class="wrapCoan">

			<table cellspacing="0" class="hanrei">
				<tr>
					<td><img src="<!--{$TPL_URLPATH}-->img/soyafarm/check.gif" alt="必須" /> 必須入力</td>
					<td><img src="<!--{$TPL_URLPATH}-->img/soyafarm/option.gif" alt="任意" /> 任意入力</td>
				</tr>
			</table>

			<!--{if $smarty.const.CUSTOMER_CONFIRM_MAIL}-->
			<p>ご登録されますと、まずは仮会員となります。<br />入力されたメールアドレスに、ご連絡が届きますので、本会員になった上でお買い物をお楽しみください。</p>
			<!--{/if}-->
			<form name="form1" id="form1" method="post" action="?">
				<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
				<input type="hidden" name="mode" value="confirm" />
				<div class="wrapCoanEle">
					<h3 class="order">お客様情報</h3>
					<h4 class="order"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/check.gif" alt="必須" />氏名をご入力ください。</h4>
					<table summary="ご氏名" class="tblOrder">
						<tr>
							<th><span>漢字氏名</span></th>
							<td>
								<!--{assign var=key1 value="`$prefix`name01"}-->
								<!--{assign var=key2 value="`$prefix`name02"}-->
								姓<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="7" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: auto;" size="14" id="userNameSei" />&nbsp;
								名<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" maxlength="7" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: auto;" size="14" id="userNameMei" />&nbsp;
								<span class="example"><!--{$smarty.const.SAMPLE_NAME}--></span>
								<!--{if $arrErr[$key1]}-->
									<p class="attention"><!--{$arrErr[$key1]}--></p>
								<!--{/if}-->
								<!--{if $arrErr[$key2]}-->
									<p class="attention"><!--{$arrErr[$key2]}--></p>
								<!--{/if}-->
							</td>
						</tr>
						<tr>
							<th><span>ｶﾀｶﾅ氏名</span></th>
							<td>
								<!--{assign var=key1 value="`$prefix`kana01"}-->
								<!--{assign var=key2 value="`$prefix`kana02"}-->
								ｾｲ<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="7" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: auto;" size="14" id="userFuriganaSei" />&nbsp;
								ﾒｲ<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" maxlength="7" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: auto;" size="14" id="userFuriganaMei" />&nbsp;
								<span class="example"><!--{$smarty.const.SAMPLE_KANA}--></span>
								<!--{if $arrErr[$key1]}--><p class="attention"><!--{$arrErr[$key1]}--></p><!--{/if}-->
								<!--{if $arrErr[$key2]}--><p class="attention"><!--{$arrErr[$key2]}--></p><!--{/if}-->
							</td>
						</tr>
					</table>

					<h4 class="order"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/check.gif" width="40" height="20" alt="必須">電話番号をご入力ください。※携帯電話番号でもご登録いただけます。</h4>

					<table summary="電話番号" class="tblOrder">
					<tr>
						<th><span>電話番号</span></th>
						<td>
							<!--{assign var=key1 value="`$prefix`tel"}-->
							<input type="text" name="<!--{$key1}-->" id="tel" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN*3}-->" size="<!--{$smarty.const.TEL_ITEM_LEN*3}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box180" />
								<span class="example"><!--{$smarty.const.SAMPLE_TEL}--></span>
							<!--{if $arrErr[$key1]}-->
								<p class="attention"><!--{$arrErr[$key1]}--></p>
							<!--{/if}-->
						</td>
					</tr>
					</table>

					<h4 class="order"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/check.gif" width="40" height="20" alt="必須">ご自宅の住所を建物名・部屋番号までご入力ください。</h4>

					<table summary="住所" class="tblOrder" cellspacing="0">
					<tr>
						<th><span>住所</span></th>
						<td>
							<table cellspacing="0" class="tblOrderInnr" cellspacing="0" style="border-collapse: separate;">
								<tr>
									<td colspan="2" class="top">
<!--{assign var=key value="zip"}-->
<!--{assign var=key3 value="`$prefix`pref"}-->
<!--{assign var=key4 value="`$prefix`addr01"}-->
<!--{assign var=key5 value="`$prefix`addr02"}-->
<!--{assign var=key6 value="`$prefix`house_no"}-->
<p>〒&nbsp;<input type="text" name="<!--{$key}-->" id="<!--{$key}-->" value="<!--{$arrForm[$key]|h}-->" maxlength="<!--{math equation="a+b" a=$smarty.const.ZIP_LEN b=1}-->" size="<!--{$smarty.const.ZIP_LEN}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;<img src="<!--{$TPL_URLPATH}-->img/rohto/zip.gif" alt="住所自動入力" id="easy" />
								<span class="example"><!--{$smarty.const.SAMPLE_ZIP}--></span></p>
										郵便番号をご入力後、ボタンを押してください。ご住所が自動で入力されます。<br />
										[<a href="http://search.post.japanpost.jp/zipcode/" target="_blank"><span class="mini">郵便番号検索ページヘ</span></a>]
										<!--{if $arrErr[$key]}-->
											<p class="attention"><!--{$arrErr[$key]}--></p>
										<!--{/if}-->
									</td>
								</tr>
								<tr>
									<th>都道府県</th>
									<td>
										<select name="<!--{$key3}-->" id="pref" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->">
												<option value="" selected="selected">都道府県を選択</option>
												<!--{html_options options=$arrPref selected=$arrForm[$key3]}-->
										</select>
											</p>
										<!--{if $arrErr[$key3]}-->
											<div class="attention"><!--{$arrErr[$key3]}--></div>
										<!--{/if}-->
									</td>
								</tr>
								<tr>
									<th>市区町村</th>
									<td>
										<span id="addr1-navi">制限文字数を超えています</span>
										<p class="top"><div id="addr1-div"><input type="text" name="<!--{$key4}-->" id="addr1" value="<!--{$arrForm[$key4]|h}-->" maxlength="40" class="box300" style="<!--{$arrErr[$key4]|sfGetErrorColor}-->; ime-mode: auto;" /></div>
										<span class="example"><!--{$smarty.const.SAMPLE_ADDRESS1}--></span></p>
										<!--{if $arrErr[$key4]}-->
											<div class="attention"><!--{$arrErr[$key4]}--></div>
										<!--{/if}-->
									</td>
								</tr>
								<tr>
									<th class="btm">番地・ビル名</th>
									<td class="btm">
										<span class="clr01">▼</span>
										番地が必要のないご住所の場合、「番地なし」にチェックを付けてください。<br />
										<span id="addr2-navi">制限文字数を超えています</span>
										<p class="top"><input type="text" name="<!--{$key5}-->" id="addr2" value="<!--{$arrForm[$key5]|h}-->" maxlength="40" class="box300" style="<!--{$arrErr[$key5]|sfGetErrorColor}-->; ime-mode: auto;" /><label><input type="checkbox" name="house_no" id="house_no" <!--{if $arrForm[$key6]}-->checked="checked"<!--{/if}-->/><span class="dyn">番地なし</span></label><br />
											<span class="example"><!--{$smarty.const.SAMPLE_ADDRESS2}--></span></p>
										<!--{if $arrErr[$key5]}-->
											<div class="attention"><!--{$arrErr[$key5]}--></div>
										<!--{/if}-->
									</td>
								</tr>
							</table>
						</td>
					</tr>

					<!--{*
						<tr>
							<th><span><img src="<!--{$TPL_URLPATH}-->img/rohto/spacer.gif" alt="" width="31" height="13" />FAX</span></th>
							<td>
								<!--{assign var=key1 value="`$prefix`fax"}-->
								<!--{if $arrErr[$key1]}-->
									<div class="attention"><!--{$arrErr[$key1]}--></div>
								<!--{/if}-->
								<input type="text" name="<!--{$key1}-->" id="fax" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN*3}-->" size="<!--{$smarty.const.TEL_ITEM_LEN*3}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box180" />
							</td>
						</tr>
					*}-->
					</table>

					<h4 class="order"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/check.gif" width="40" height="20" alt="必須">メールアドレスをご入力ください。</h4>

					<table summary="メールアドレス" class="tblOrder">
						<tr>
							<th><span>メールアドレス</span></th>
							<td>
								<!--{assign var=key1 value="`$prefix`email"}-->
								<!--{assign var=key2 value="`$prefix`email02"}-->
								<!--{if $arrErr[$key1] || $arrErr[$key2]}-->
									<div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
								<!--{/if}-->
								<input type="text" name="<!--{$key1}-->" id="email" value="<!--{$arrForm[$key1]|h}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box300 top" />
								<span class="example">半角英数</span>
								<span id="email-navi">形式に誤りがあります</span><br />
								<span class="attention mini">確認のため、もう一度ご入力ください。</span><br />
								<input type="text" name="<!--{$key2}-->" id="email02" value="<!--{$arrForm[$key2]|h}-->" style="<!--{$arrErr[$key1]|cat:$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;" class="box300" /><span id="email02-navi">形式に誤りがあります</span><br />
							</td>
						</tr>
					</table>

					<h4 class="order"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/check.gif" width="40" height="20" alt="必須">パスワードをご入力ください。マイページにアクセスする際に必要になります。</h4>

					<table summary="パスワード" class="tblOrder">
							<tr>
								<th><span>マイページパスワード</span></th>
								<td id="passwordSection">
									<table cellspacing="0" class="tblOrderInnr">
											<th>入力</th>
											<td>
												<div class="password-container">
													<input class="strong-password" type="password" name="password" value="<!--{$arrForm.password|h}-->" maxlength="<!--{$smarty.const.PASSWORD_MAX_LEN}-->" style="<!--{$arrErr.password|sfGetErrorColor}-->" class="box120" />
													<span class="example">半角<!--{$smarty.const.PASSWORD_MIN_LEN}-->～<!--{$smarty.const.PASSWORD_MAX_LEN}-->文字</span>
												</div>
											</td>
											<td class="btm" rowspan="2">
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
										<tr>
											<th class="btm">確認用</th>
											<td class="btm">
												<input class="strong-password" type="password" name="password02" value="<!--{$arrForm.password02|h}-->" maxlength="<!--{$smarty.const.PASSWORD_MAX_LEN}-->" style="<!--{$arrErr.password|cat:$arrErr.password02|sfGetErrorColor}-->" class="box120" /></td>
										</tr>
									</table>
									<!--{if $arrErr.password || $arrErr.password02}-->
										<div class="attention"><!--{$arrErr.password}--><!--{$arrErr.password02}--></div>
									<!--{/if}-->
								</td>
							</tr>
					</table>

					<h4 class="order"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/check.gif" alt="必須" />性別をご指定ください。</h4>

					<table summary="性別" class="tblOrder">
						<tr>
							<th><span>性別</span></th>
							<td>
								<!--{assign var=key1 value="`$prefix`sex"}-->
								<!--{if $arrErr[$key1]}-->
									<div class="attention"><!--{$arrErr[$key1]}--></div>
								<!--{/if}-->

								<span style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
									<input type="radio" id="man" name="<!--{$key1}-->" value="1" <!--{if $arrForm[$key1] eq 1}-->checked="checked"<!--{/if}--> /><label for="man">男性</label><input type="radio" id="woman" name="<!--{$key1}-->" value="2" <!--{if $arrForm[$key1] eq 2}-->checked="checked"<!--{/if}--> /><label for="woman">女性</label>
								</span>
							</td>
						</tr>
					</table>

					<h4 class="order"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/check.gif" alt="必須" />生年月日をご入力ください。</h4>

					<table summary="生年月日" class="tblOrder">
						<tr>
							<th><span>生年月日</span></th>
							<td>
								<!--{assign var=errBirth value="`$arrErr.year``$arrErr.month``$arrErr.day`"}-->
								<select name="year" style="<!--{$errBirth|sfGetErrorColor}-->">
									<!--{html_options options=$arrYear selected=$arrForm.year|default:''}-->
								</select>年
								<select name="month" style="<!--{$errBirth|sfGetErrorColor}-->">
									<!--{html_options options=$arrMonth selected=$arrForm.month|default:''}-->
								</select>月
								<select name="day" style="<!--{$errBirth|sfGetErrorColor}-->">
									<!--{html_options options=$arrDay selected=$arrForm.day|default:''}-->
								</select>日
								<!--{if $errBirth}-->
									<p class="attention"><!--{$errBirth}--></p>
								<!--{/if}-->
							</td>
						</tr>
					<!--{*
							<tr>
								<th><span>パスワードを忘れた時のヒント</span></th>
								<td>
									<!--{if $arrErr.reminder || $arrErr.reminder_answer}-->
										<div class="attention"><!--{$arrErr.reminder}--><!--{$arrErr.reminder_answer}--></div>
									<!--{/if}-->
									質問：
									<select name="reminder" style="<!--{$arrErr.reminder|sfGetErrorColor}-->">
										<option value="" selected="selected">選択してください</option>
										<!--{html_options options=$arrReminder selected=$arrForm.reminder}-->
									</select>
									<br />
									答え：<input type="text" name="reminder_answer" value="<!--{$arrForm.reminder_answer|h}-->" style="<!--{$arrErr.reminder_answer|sfGetErrorColor}-->; ime-mode: auto;" class="box260" />
								</td>
							</tr>
					*}-->
					<!--{*
							<tr>
								<th><span>メールマガジン送付について</span></th>
								<td>
									<!--{if $arrErr.mailmaga_flg}-->
										<div class="attention"><!--{$arrErr.mailmaga_flg}--></div>
									<!--{/if}-->
									<p><strong>お得な特別セール</strong>や<strong>健康に関する情報</strong>をお届けするお知らせメールを受け取りますか？</p>
									<span style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->">
										<input type="radio" name="mailmaga_flg" value="1" id="mailmaga_flg_1" <!--{if $arrForm.mailmaga_flg eq '1'}--> checked="checked" <!--{/if}--> /><label for="mailmaga_flg_1">受け取る</label><input type="radio" name="mailmaga_flg" value="0" id="mailmaga_flg_0" <!--{if $arrForm.mailmaga_flg eq '0'}--> checked="checked" <!--{/if}--> /><label for="mailmaga_flg_0">受け取らない</label><div id="notReceive" style="display: none;" ><img width="495" height="97" src="<!--{$TPL_URLPATH}-->img/rohto/mail_uketoru_up_s.gif" alt="受け取りませんか"></a></div>
									</span>
								</td>
							</tr>
							<tr>
								<th><span>ＤＭ送付について</span></th>
								<td>
									<!--{if $arrErr.dm_flg}-->
										<div class="attention"><!--{$arrErr.dm_flg}--></div>
									<!--{/if}-->
									<span style="<!--{$arrErr.dm_flg|sfGetErrorColor}-->">
										<input type="radio" name="dm_flg" value="1" id="dm_flg_1" <!--{if $arrForm.dm_flg eq '1'}--> checked="checked" <!--{/if}--> /><label for="dm_flg_1">受け取る</label><input type="radio" name="dm_flg" value="0" id="dm_flg_0" <!--{if $arrForm.dm_flg eq '0'}--> checked="checked" <!--{/if}--> /><label for="dm_flg_0">受け取らない</label><br />
									</span>
								</td>
							</tr>
							<tr>
								<th><span>アンケートについて</span></th>
								<td>
									当サイトをどこで知りましたか？
									<br />
									<!--{if $arrErr.questionnaire}-->
										<div class="attention"><!--{$arrErr.questionnaire}--></div>
									<!--{/if}-->
									<select name="questionnaire" id="questionary" style="<!--{$arrErr.questionnaire|sfGetErrorColor}-->" >
										<option value="" selected="selected">選択してください</option>
										<!--{html_options options=$arrQuestionnaire selected=$arrForm.questionnaire}-->
									</select>
									<br />
									<div id="questionnaire_other_text"></div>
									<!--{if $arrErr.questionnaire_other}-->
										<div class="attention"><!--{$arrErr.questionnaire_other}--></div>
									<!--{/if}-->
									<p id="otherMessage"></p>
									<textarea name="questionnaire_other" id="other" style="<!--{$arrErr.questionnaire_other|sfGetErrorColor}-->" cols="50" rows="8" class="txtarea" wrap="soft"><!--{$arrForm.questionnaire_other|h}--></textarea>
								</td>
							</tr>
					*}-->
					</table>

				<h3 class="order">お届け先</h3>

					<table cellspacing="0" class="tblOrder">
						<tr>
							<th class="dyn">
								<!--{assign var=key value="other_addr_flg"}-->
								<label><input type="checkbox" name="<!--{$key}-->" id="<!--{$key}-->" value="1" <!--{if $arrForm[$key]}-->checked="checked"<!--{/if}--> />上記以外のご住所へ配送</label>
								&nbsp;&nbsp;&nbsp;&nbsp;<span class="example">※ここにチェックを入れていただきますと、お届け先入力フォームが出ます。</span>
							</th>
						</tr>
					</table>

				<div id="other_addr_box" style="display:<!--{if $arrForm[$key]}-->block;<!--{else}-->none;<!--{/if}-->">
					<h4 class="order"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/check.gif" alt="必須" />お届け先の氏名をご入力ください。</h4>

					<table summary="お届け先：ご氏名" cellspacing="0" class="tblOrder">
					<!--{assign var=prefix value="shipping_"}-->
					<tr>
						<th><span>お届け先：漢字氏名</span></th>
						<td>
							<!--{assign var=key1 value="`$prefix`name01"}-->
							<!--{assign var=key2 value="`$prefix`name02"}-->
							姓<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="7" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: auto;" size="14" id="userNameSeiShip" />&nbsp;
							名<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" maxlength="7" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: auto;" size="14" id="userNameMeiShip" />&nbsp;
								<span class="example"><!--{$smarty.const.SAMPLE_NAME}--></span>
								<!--{if $arrErr[$key1]}-->
									<p class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></p>
								<!--{/if}-->
						</td>
					</tr>
					<tr>
						<th><span>お届け先：カタカナ氏名</span></th>
						<td>
							<!--{assign var=key1 value="`$prefix`kana01"}-->
							<!--{assign var=key2 value="`$prefix`kana02"}-->
							ｾｲ<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="7" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: auto;" size="14" id="userFuriganaSeiShip" />&nbsp;
							ﾒｲ<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" maxlength="7" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: auto;" size="14" id="userFuriganaMeiShip" />&nbsp;
								<span class="example"><!--{$smarty.const.SAMPLE_KANA}--></span>
								<!--{if $arrErr[$key1]}-->
									<p class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></p>
								<!--{/if}-->
						</td>
					</tr>
					</table>

					<h4 class="order"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/check.gif" alt="必須" />お届け先電話番号をご入力ください。※携帯電話番号でもご登録いただけます。</h4>

					<table summary="お届け先：電話番号" cellspacing="0" class="tblOrder">
					<tr>
						<th><span>お届け先：電話番号</span></th>
						<td>
							<!--{assign var=key1 value="`$prefix`tel"}-->
							<input type="text" name="<!--{$key1}-->" id="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN*3}-->" size="<!--{$smarty.const.TEL_ITEM_LEN*3}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box180" />
								<span class="example"><!--{$smarty.const.SAMPLE_TEL}--></span>
							<!--{if $arrErr[$key1]}-->
								<p class="attention"><!--{$arrErr[$key1]}--></p>
							<!--{/if}-->
						</td>
					</tr>
					</table>

					<h4 class="order"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/check.gif" alt="必須" />お届け先住所を建物名・部屋番号までご入力ください。</h4>

					<table summary="お届け先：住所" cellspacing="0" class="tblOrder">

					<tr>
						<th><span>お届け先：住所</span></th>
						<td>
							<table cellspacing="0" class="tblOrderInnr" cellspacing="0" style="border-collapse: separate;">
								<tr>
									<td colspan="2" class="top">
<!--{assign var=key value="`$prefix`zip"}-->
<!--{assign var=key3 value="`$prefix`pref"}-->
<!--{assign var=key4 value="`$prefix`addr01"}-->
<!--{assign var=key5 value="`$prefix`addr02"}-->
<!--{assign var=key6 value="`$prefix`house_no"}-->
							<p class="top">〒&nbsp;<input type="text" name="<!--{$key}-->" id="<!--{$key}-->" value="<!--{$arrForm[$key]|h}-->" maxlength="<!--{math equation="a+b" a=$smarty.const.ZIP_LEN b=1}-->" size="<!--{$smarty.const.ZIP_LEN}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;<img src="<!--{$TPL_URLPATH}-->img/rohto/zip.gif" alt="住所自動入力" id="shipping_easy" /></p>
								<span class="example"><!--{$smarty.const.SAMPLE_ZIP}--></span></span></p>
							郵便番号をご入力後、ボタンを押してください。ご住所が自動で入力されます。<br />
							[<a href="http://search.post.japanpost.jp/zipcode/" target="_blank"><span class="mini">郵便番号検索ページヘ</span></a>]
							<!--{if $arrErr[$key]}-->
								<div class="attention"><!--{$arrErr[$key]}--></div>
							<!--{/if}-->
									</td>
								</tr>
								<tr>
									<th>都道府県</th>
									<td>
										<select name="<!--{$key3}-->" id="<!--{$key3}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->">
											<option value="" selected="selected">都道府県を選択</option>
											<!--{html_options options=$arrPref selected=$arrForm[$key3]}-->
										</select>
										<!--{if $arrErr[$key3]}-->
											<div class="attention"><!--{$arrErr[$key3]}--></div>
										<!--{/if}-->
									</td>
								</tr>
								<tr>
									<th>市区町村</th>
									<td>
										<span id="shipping_addr1-navi">制限文字数を超えています</span>
										<p class="top"><div id="shipping-addr1-div"><input type="text" name="<!--{$key4}-->" id="<!--{$key4}-->" value="<!--{$arrForm[$key4]|h}-->" maxlength="40" class="box300" style="<!--{$arrErr[$key4]|sfGetErrorColor}-->; ime-mode: auto;" /></div>
										<span class="example"><!--{$smarty.const.SAMPLE_ADDRESS1}--></span></p></p>
										<!--{if $arrErr[$key4]}-->
											<div class="attention"><!--{$arrErr[$key4]}--></div>
										<!--{/if}-->
									</td>
								</tr>
								<tr>
									<th class="btm">番地・ビル名</th>
									<td class="btm">
										<span class="clr01">▼</span>
										番地が必要のないご住所の場合、「番地なし」にチェックを付けてください。<br />
										<span id="shipping_addr2-navi">制限文字数を超えています</span>
										<p class="top"><input type="text" name="<!--{$key5}-->" id="<!--{$key5}-->" value="<!--{$arrForm[$key5]|h}-->" maxlength="40" class="box300" style="<!--{$arrErr[$key5]|sfGetErrorColor}-->; ime-mode: auto;" /><label><input type="checkbox" name="shipping_house_no" id="shipping_house_no" <!--{if $arrForm[$key6]}-->checked="checked"<!--{/if}-->/><span class="dyn">番地なし</span></label><br />
											<span class="example"><!--{$smarty.const.SAMPLE_ADDRESS2}--></span></p>
										<!--{if $arrErr[$key5]}-->
											<div class="attention"><!--{$arrErr[$key5]}--></div>
										<!--{/if}-->
									</td>
								</tr>
							</table>
						</td>
					</tr>
					</table>
				</div>
				</div>


				<h3 class="order" style="margin-bottom:0;"><!--{$arrSiteInfo.shop_name}-->オンラインショップご利用規約</h3>
				<div class="wrapKiyaku">
					<div class="wrapCoanEle" style="padding-top:15px;">
						<span class="order"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/check.gif" alt="必須"></span>
						<!--{assign var=key value="agree"}-->
						<!--{if $arrErr[$key]}-->
							<p class="attention">利用規約へのご同意が必要です。</p>
						<!--{/if}-->
						<p>【重要】 会員登録をされる前に、下記ご利用規約をよくお読みください。</p>
						<p>規約には、本サービスを使用するに当たってのあなたの権利と義務が規定されております。
						<textarea name="textfield" readonly="readonly" style="width:860px;height:250px;margin-top:10px;"><!--{$tpl_kiyaku_text|h}--></textarea>
					</div>
					<div class="wrapCoanAgre">
						<label><input type="checkbox" name="<!--{$key}-->" id="<!--{$key}-->" value="1" <!--{if $arrForm[$key]}-->checked="checked"<!--{/if}--> />規約に同意する</label>
					</div>
				</div>

				<div class="orderBtn">
					<p class="left">
						<span class="f-right" style="width:600px;float:right;text-align:right;">
							<a href="javascript:void(0);" onclick="document.form1.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_kakunin_c.gif" alt="確認ページへ" name="confirm" id="confirm" class="swp" /></a>
						</span>
						<a href="<!--{$smarty.const.CART_URLPATH}-->"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_back.gif" alt="戻る" class="swp"></a>
					</p>
				</div>
			</form>
		</div>
	</div>
</div>
<!--▲CONTENTS-->
<!--{$tpl_clickAnalyzer}-->
