<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_header.tpl" subtitle="新しいお届け先の追加・変更"}-->

<script type="text/javascript">
var get_zip_addr = "<!--{$smarty.const.INPUT_ZIP_URLPATH}-->";
</script>
<script src="<!--{$TPL_URLPATH}-->js/pschecker.js" type="text/javascript"></script>
<script src="<!--{$TPL_URLPATH}-->js/jquery.autoKana.js" type="text/javascript"></script>
<script src="<!--{$smarty.const.ROOT_URLPATH}-->js/mypage_basic.js" type="text/javascript"></script>
<div id="window_area">
	    <h1><!--{$tpl_title|h}--></h1>
	    <p class="naked">下記項目にご入力ください。
	    入力後、一番下の「登録する」ボタンをクリックしてください。</p>

	<div class="wrapForm">
	    <form name="form1" id="form1" method="post" action="?">
	        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
	        <input type="hidden" name="mode" value="edit" />
	        <input type="hidden" name="other_deliv_id" value="<!--{$smarty.session.other_deliv_id}-->" />
	        <input type="hidden" name="ParentPage" value="<!--{$ParentPage}-->" />

	        <table summary="お届け先登録">
				<colgroup width="30%"></colgroup>
				<colgroup width="70%"></colgroup>
				<tr>
					<th><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" />お名前</th>
					<td>
						<!--{assign var=key1 value="`$prefix`name"}-->
						<!--{if $arrErr[$key1]}-->
							<div class="attention"><!--{$arrErr[$key1]}--></div>
						<!--{/if}-->
						<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="16" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: auto;" class="box240" id="userName" />&nbsp;
					</td>
				</tr>
				<tr>
					<th><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" />お名前(フリガナ)</th>
					<td>
						<!--{assign var=key1 value="`$prefix`kana"}-->
						<!--{if $arrErr[$key1]}-->
							<div class="attention"><!--{$arrErr[$key1]}--></div>
						<!--{/if}-->
						<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="15" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: auto;" class="box240" id="userFurigana" />&nbsp;
					</td>
				</tr>
				<tr>
					<th><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" />郵便番号</th>
					<td>
						<!--{assign var=key value="zip"}-->
						<!--{assign var=key3 value="`$prefix`pref"}-->
						<!--{assign var=key4 value="`$prefix`addr01"}-->
						<!--{assign var=key5 value="`$prefix`addr02"}-->

						<!--{if $arrErr[$key]}-->
							<div class="attention"><!--{$arrErr[$key]}--></div>
						<!--{/if}-->

						<p class="top">〒&nbsp;<input type="text" name="<!--{$key}-->" id="<!--{$key}-->" value="<!--{$arrForm[$key]|h}-->" maxlength="<!--{math equation="a+b" a=$smarty.const.ZIP_LEN b=1}-->" size="<!--{$smarty.const.ZIP_LEN}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;<img src="<!--{$TPL_URLPATH}-->img/rohto/zip.gif" alt="住所自動入力" id="easy" /></p>
						郵便番号をご入力後、ボタンを押してください。ご住所が自動で入力されます。<br />
						[<a href="http://search.post.japanpost.jp/zipcode/" target="_blank"><span class="mini">郵便番号検索ページヘ</span></a>]
					</td>
				</tr>

				<tr>
					<th><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" />住所</th>
					<td>
						<!--{if $arrErr[$key3] || $arrErr[$key4] || $arrErr[$key5]}-->
							<div class="attention"><!--{$arrErr[$key3]}--><!--{$arrErr[$key4]}--><!--{$arrErr[$key5]}--></div>
						<!--{/if}-->
						<select name="<!--{$key3}-->" id="pref" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->">
								<option value="" selected="selected">都道府県を選択</option>
								<!--{html_options options=$arrPref selected=$arrForm[$key3]}-->
						</select><br />
						<span id="addr1-navi" style="display: none;">制限文字数を超えています</span>
						<p class="top"><div id="addr1-div"><input type="text" name="<!--{$key4}-->" id="addr1" value="<!--{$arrForm[$key4]|h}-->" maxlength="40" class="box300" style="<!--{$arrErr[$key4]|sfGetErrorColor}-->; ime-mode: auto;" /></div><br />
							<!--{$smarty.const.SAMPLE_ADDRESS1}--></p>
						<span id="addr2-navi" style="display: none;">制限文字数を超えています</span>
						<p class="top"><input type="text" name="<!--{$key5}-->" id="addr2" value="<!--{$arrForm[$key5]|h}-->" maxlength="40" class="box300" style="<!--{$arrErr[$key5]|sfGetErrorColor}-->; ime-mode: auto;" /><label><input type="checkbox" name="house_no" id="house_no" />番地なし</label><br />
							<!--{$smarty.const.SAMPLE_ADDRESS2}--></p>
						<p class="mini"><span class="attention">住所は2つに分けてご記入ください。マンション名は必ず記入してください。</span></p>
					</td>
				</tr>
				<tr>
					<th><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" />電話番号</th>
					<td>
						<!--{assign var=key1 value="`$prefix`tel"}-->
						<!--{if $arrErr[$key1]}-->
							<div class="attention"><!--{$arrErr[$key1]}--></div>
						<!--{/if}-->
						<input type="text" name="<!--{$key1}-->" id="tel" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN*3}-->" size="<!--{$smarty.const.TEL_ITEM_LEN*3}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box180" />
					</td>
				</tr>
	        </table>

			<p style="text-align:center;margin:10px auto;">
				<a href="javascript:void(0);" onclick="form1.submit();">
					<img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_register.gif" alt="登録" class="swp" />
				</a>
			</p>
<!--{*
	        <div class="btn_area">
	            <ul>
	                <li><a href="javascript:void(0);" onclick="document.form1.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/button/btn_add_address_complete.jpg" alt="登録する" name="register" id="register" /></a></li>
	            </ul>
	        </div>
*}-->
	    </form>
	</div>
</div>

<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_footer.tpl"}-->
