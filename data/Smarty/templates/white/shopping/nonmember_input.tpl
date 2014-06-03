<!--{* -*- coding: utf-8-unix; -*- *}-->
<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *}-->
<script type="text/javascript">
$(document).ready(function() {
	$('#zip01, #zip02').autotab_magic().autotab_filter({
		format: 'numeric',
	});
	$('#tel, #fax').autotab_magic().autotab_filter({
		format: 'numeric',
	});
});
</script>
<script type="text/javascript">//<![CDATA[
	$(function(){
		fnChangeQuestionnaire();
	});
//]]>
</script>
<!--▼CONTENTS-->
<div id="undercolumn">
	<div id="undercolumn_customer">
		<h1><img src="<!--{$TPL_URLPATH}-->img/soyafarm/order_title_step1.gif" alt="購入手続き" /></h1>

		<div class="wrapCoan">
			<p>下記項目にご入力ください。「<span class="attention">※</span>」印は入力必須項目です。<br />
			下記の項目にご入力の上、次のステップに進んでください。</p>

		<h3 class="order"><img src="<!--{$TPL_URLPATH}-->img/rohto/method_midashi01.gif" width="820" height="35" alt="お客様情報"></h3>

		<form name="form1" id="form1" method="post" action="?">
		<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
		<input type="hidden" name="mode" value="nonmember_confirm" />
		<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />
		<table class="tblOrder">
			<tr>
				<th>お名前<img align="right" src="<!--{$TPL_URLPATH}-->img/rohto/check.gif" alt="必須" /></th>
				<td>
					<!--{assign var=key1 value="order_name"}-->
					<span class="attention"><!--{$arrErr[$key1]}--></span>
					<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="16" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: auto;" class="box240" />
				</td>
			</tr>
			<tr>
				<th>お名前(フリガナ)<img align="right" src="<!--{$TPL_URLPATH}-->img/rohto/check.gif" alt="必須" /></th>
				<td>
					<!--{assign var=key1 value="order_kana"}-->
					<span class="attention"><!--{$arrErr[$key1]}--></span>
					<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="15" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: auto;" class="box240" />
				</td>
			</tr>
			<tr>
				<th>郵便番号<img align="right" src="<!--{$TPL_URLPATH}-->img/rohto/check.gif" alt="必須" /></th>
				<td>
					<!--{assign var=key1 value="order_zip01"}-->
					<!--{assign var=key2 value="order_zip02"}-->
					<span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
					<p class="top">〒&nbsp;<input type="text" name="<!--{$key1}-->" id="zip01" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" size="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;-&nbsp;	 <input type="text" name="<!--{$key2}-->" id="zip02" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" size="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />　
						<a href="http://search.post.japanpost.jp/zipcode/" target="_blank"><span class="mini">郵便番号検索</span></a></p>

					<p class="zipimg"><a href="<!--{$smarty.const.ROOT_URLPATH}-->address/<!--{$smarty.const.DIR_INDEX_PATH}-->" onclick="fnCallAddress('<!--{$smarty.const.INPUT_ZIP_URLPATH}-->', 'order_zip01', 'order_zip02', 'order_pref', 'order_addr01'); return false;" target="_blank"><img src="<!--{$TPL_URLPATH}-->img/button/btn_address_input.jpg" alt="住所自動入力" /></a>
						<span class="mini">&nbsp;郵便番号を入力後、クリックしてください。</span></p>
				</td>
			</tr>
			<tr>
				<th>住所<img align="right" src="<!--{$TPL_URLPATH}-->img/rohto/check.gif" alt="必須" /></th>
				<td>
					<table class="tblOrderInnr">
						<tr>
							<th>都道府県</th>	
							<td>
								<!--{assign var=key value="order_pref"}-->
								<span class="attention"><!--{$arrErr.order_pref}--><!--{$arrErr.order_addr01}--><!--{$arrErr.order_addr02}--></span>
								<select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
									<option value="">都道府県を選択</option>
									<!--{html_options options=$arrPref selected=$arrForm[$key].value}-->
								</select>
							</td>
						</tr>
						<tr>
							<th>市区町村</th>
							<td>
								<p>
									<!--{assign var=key value="order_addr01"}-->
									<span class="clr01">▼</span>住所は2つに分けてご記入ください。マンション名は必ず記入してください。
									<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="16" style="<!--{$arrErr[$key]|sfGetErrorColor}-->; ime-mode: auto;" size="45" /><br />
									<span class="example"><!--{$smarty.const.SAMPLE_ADDRESS1}--></span></p>
							</td>
						</tr>
						<tr>
							<th class="btm">番地・建物名<br />部屋番号</th>
							<td class="btm">
								<p>
									<!--{assign var=key value="order_addr02"}-->
									<span class="clr01">▼</span>ご登録名と表札のお名前が異なる場合は、"○○様方"と追記してください。
									<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="20" style="<!--{$arrErr[$key]|sfGetErrorColor}-->; ime-mode: auto;" size="45" /><br />
									<span class="example"><!--{$smarty.const.SAMPLE_ADDRESS2}--></span></p>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<th>電話番号<img align="right" src="<!--{$TPL_URLPATH}-->img/rohto/check.gif" alt="必須" /></th>
				<td>
					<!--{assign var=key1 value="order_tel"}-->
					<span class="attention"><!--{$arrErr[$key1]}--></span>
					<input type="text" name="<!--{$key1}-->" id="tel" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length*3}-->" size="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box180" />
				</td>
			</tr>
			<tr>
				<th>FAX</th>
				<td>
					<!--{assign var=key1 value="order_fax"}-->
					<span class="attention"><!--{$arrErr[$key1]}--></span>
					<input type="text" name="<!--{$key1}-->" id="fax" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length*3}-->" size="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box180" />
				</td>
			</tr>
			<tr>
				<th>メールアドレス<img align="right" src="<!--{$TPL_URLPATH}-->img/rohto/check.gif" alt="必須" /></th>
				<td>
					<!--{assign var=key value="order_email"}-->
					<span class="attention"><!--{$arrErr[$key]}--></span>
					<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->; ime-mode: disabled;" class="box380 top" /><br />
					<!--{assign var=key value="order_email02"}-->
					<span class="attention"><!--{$arrErr[$key]}--></span>
					<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->; ime-mode: disabled;" class="box380" /><br />
					<p class="mini"><span class="attention">確認のため2度入力してください。</span></p>
				</td>
			</tr>
			<tr>
				<th>性別</th>
				<td>
					<!--{assign var=key value="order_sex"}-->
					<!--{if $arrErr[$key]}-->
						<div class="attention"><!--{$arrErr[$key]}--></div>
					<!--{/if}-->
					<span style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
						<!--{html_radios name="$key" options=$arrSex selected=$arrForm[$key].value style="$err" label_ids=true}-->
					</span>
				</td>
			</tr>
			<tr>
				<th>生年月日</th>
				<td>
					<!--{assign var=errBirth value="`$arrErr.year``$arrErr.month``$arrErr.day`"}-->
					<span class="attention"><!--{$errBirth}--></span>
					<select name="year" style="<!--{$errBirth|sfGetErrorColor}-->">
						<!--{html_options options=$arrYear selected=$arrForm.year.value|default:''}-->
					</select>年
					<select name="month" style="<!--{$errBirth|sfGetErrorColor}-->">
						<!--{html_options options=$arrMonth selected=$arrForm.month.value|default:''}-->
					</select>月
					<select name="day" style="<!--{$errBirth|sfGetErrorColor}-->">
						<!--{html_options options=$arrDay selected=$arrForm.day.value|default:''}-->
					</select>日
				</td>
			</tr>
			<tr>
				<th>メールマガジン送付について<img align="right" src="<!--{$TPL_URLPATH}-->img/rohto/check.gif" alt="必須" /></th>
				<td>
					<!--{assign var=key value="mail_flag"}-->
					<!--{if $arrErr[$key]}-->
						<div class="attention"><!--{$arrErr[$key]}--></div>
					<!--{/if}-->
					<span style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
						<input type="radio" name="<!--{$key}-->" value="1" id="yes" <!--{if $arrForm[$key].value eq '1'}--> checked="checked" <!--{/if}--> /><label for="html">受け取る</label><br />
						<input type="radio" name="<!--{$key}-->" value="0" id="no" <!--{if $arrForm[$key].value eq '0'}--> checked="checked" <!--{/if}--> /><label for="no">受け取らない</label>
					</span>
				</td>
			</tr>
			<tr>
				<th>ＤＭ送付について<img align="right" src="<!--{$TPL_URLPATH}-->img/rohto/check.gif" alt="必須" /></th>
				<td>
					<!--{assign var=key value="dm_flg"}-->
					<!--{if $arrErr[$key]}-->
						<div class="attention"><!--{$arrErr[$key]}--></div>
					<!--{/if}-->
					<span style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
						<input type="radio" name="<!--{$key}-->" value="1" id="on" <!--{if $arrForm[$key].value eq '1'}--> checked="checked" <!--{/if}--> /><label for="on">受け取る</label><br />
						<input type="radio" name="<!--{$key}-->" value="0" id="no" <!--{if $arrForm[$key].value eq '0'}--> checked="checked" <!--{/if}--> /><label for="no">受け取らない</label><br />
					</span>
				</td>
			</tr>
			<tr>
				<th>アンケートについて<img align="right" src="<!--{$TPL_URLPATH}-->img/rohto/check.gif" alt="必須" /></th>
				<td>
					<!--{assign var=key value="questionnaire"}-->
					当サイトをどこで知りましたか？
					<br />
					<!--{if $arrErr[$key]}-->
						<div class="attention"><!--{$arrErr[$key]}--></div>
					<!--{/if}-->
					<select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" onChange="fnChangeQuestionnaire(); return false;">
						<option value="" selected="selected">選択してください</option>
						<!--{html_options options=$arrQuestionnaire selected=$arrForm[$key].value}-->
					</select>
					<br />
					<div id="questionnaire_other_text"></div>
					<!--{assign var=key value="questionnaire_other"}-->
					<!--{if $arrErr[$key]}-->
						<div class="attention"><!--{$arrErr[$key]}--></div>
					<!--{/if}-->
					<textarea name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" cols="70" rows="8" class="txtarea" wrap="soft"><!--{$arrForm[$key].value|h}--></textarea>
				</td>
			</tr>
		</table>

		<h3 class="order"><img src="<!--{$TPL_URLPATH}-->img/rohto/method_midashi03.gif" width="820" height="35" alt="お客様情報"></h3>

		<table class="tblOrder">
			<tr>
				<th colspan="2">
				<!--{assign var=key value="deliv_check"}-->
				<input type="checkbox" name="<!--{$key}-->" value="1" onclick="fnCheckInputDeliv();" <!--{$arrForm[$key].value|sfGetChecked:1}--> id="deliv_label" />
				<label for="deliv_label">お届け先を指定　※上記に入力された住所と同一の場合は省略可能です。</label>
				</th>
			</tr>
			<tr>
				<th>お名前<img align="right" src="<!--{$TPL_URLPATH}-->img/rohto/check.gif" alt="必須" /></th>
				<td>
					<!--{assign var=key1 value="shipping_name"}-->
					<span class="attention"><!--{$arrErr[$key1]}--></span>
					<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="16" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: auto;" class="box240" />
				</td>
			</tr>
			<tr>
				<th>お名前(フリガナ)<img align="right" src="<!--{$TPL_URLPATH}-->img/rohto/check.gif" alt="必須" /></th>
				<td>
					<!--{assign var=key1 value="shipping_kana"}-->
					<span class="attention"><!--{$arrErr[$key1]}--></span>
					<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="15" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: auto;" class="box240" />
				</td>
			</tr>
			<tr>
				<th>郵便番号<img align="right" src="<!--{$TPL_URLPATH}-->img/rohto/check.gif" alt="必須" /></th>
				<td>
				<!--{assign var=key1 value="shipping_zip01"}-->
				<!--{assign var=key2 value="shipping_zip02"}-->
					<span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
					<p class="top">〒&nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;"	 class="box60" />&nbsp;-&nbsp;	  <input type="text"	name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />　
						<a href="http://search.post.japanpost.jp/zipcode/" target="_blank"><span class="mini">郵便番号検索</span></a></p>

					<p class="zipimg"><a href="<!--{$smarty.const.ROOT_URLPATH}-->address/<!--{$smarty.const.DIR_INDEX_PATH}-->" onclick="fnCallAddress('<!--{$smarty.const.INPUT_ZIP_URLPATH}-->', 'shipping_zip01', 'shipping_zip02', 'shipping_pref', 'shipping_addr01'); return false;" target="_blank"><img src="<!--{$TPL_URLPATH}-->img/button/btn_address_input.jpg" alt="住所自動入力" /></a>
						<span class="mini">&nbsp;郵便番号を入力後、クリックしてください。</span></p>
				</td>
			</tr>
			<tr>
				<th>住所<img align="right" src="<!--{$TPL_URLPATH}-->img/rohto/check.gif" alt="必須" /></th>
				<td>
					<!--{assign var=key value="shipping_pref"}-->
					<span class="attention"><!--{$arrErr.shipping_pref}--><!--{$arrErr.shipping_addr01}--><!--{$arrErr.shipping_addr02}--></span>
					<select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
						<option value="">都道府県を選択</option>
						<!--{html_options options=$arrPref selected=$arrForm[$key].value}-->
					</select>
					<p>
						<!--{assign var=key value="shipping_addr01"}-->
						<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="16" style="<!--{$arrErr[$key]|sfGetErrorColor}-->; ime-mode: auto;" class="box380" /><br />
						<span class="example"><!--{$smarty.const.SAMPLE_ADDRESS1}--></span></p>
					<p>
						<!--{assign var=key value="shipping_addr02"}-->
						<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="20" style="<!--{$arrErr[$key]|sfGetErrorColor}-->; ime-mode: auto;" class="box380" /><br />
						<span class="example"><!--{$smarty.const.SAMPLE_ADDRESS2}--></span></p>
					<p class="mini"><span class="attention">住所は2つに分けてご記入ください。マンション名は必ず記入してください。</span></p>

				</td>
			</tr>
			<tr>
				<th>電話番号<img align="right" src="<!--{$TPL_URLPATH}-->img/rohto/check.gif" alt="必須" /></th>
				<td>
					<!--{assign var=key1 value="shipping_tel"}-->
					<span class="attention"><!--{$arrErr[$key1]}--></span>
					<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length*3}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box180" />
				</td>
			</tr>
		</table>
		<div class="btn_area">
			<ul>
				<li>
					<a href="javascript:void(0);" onclick="document.form1.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/button/btn_singular.jpg" alt="上記のお届け先に送る" name="singular" id="singular" /></a>
				</li>
			</ul>
		</div>
		</form>
		</div>
	</div>
</div>
<!--▲CONTENTS-->
