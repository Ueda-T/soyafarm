<form name="form1" method="post" action="?">
<input type="hidden" name="mode" value="confirm" />
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<font size="-1" color="#FF0000">*は必須項目です。</font><br>
<br>

<font size="-1">●漢字氏名<font color="#FF0000"> *</font></font><br>
<!--{assign var=key1 value="`$prefix`name01"}-->
<!--{assign var=key2 value="`$prefix`name02"}-->
<!--{if $arrErr[$key1]}--><font size="-1" color="#FF0000"><!--{$arrErr[$key1]}--></font><!--{/if}-->
<!--{if $arrErr[$key2]}--><font size="-1" color="#FF0000"><!--{$arrErr[$key2]}--></font><!--{/if}-->

<font size="-1">姓</font><input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="7" size="14" id="userNameSei">
<font size="-1">名</font><input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" maxlength="7" size="14" id="userNameMei">&nbsp;
<font size="-1" color="#666666"><!--{$smarty.const.SAMPLE_NAME}--></font>
<br>
<br>

<font size="-1">●カタカナ氏名<font color="#FF0000"> *</font></font><br>
<!--{assign var=key1 value="`$prefix`kana01"}-->
<!--{assign var=key2 value="`$prefix`kana02"}-->
<!--{if $arrErr[$key1]}--><font size="-1" color="#FF0000"><!--{$arrErr[$key1]}--></font><!--{/if}-->
<!--{if $arrErr[$key2]}--><font size="-1" color="#FF0000"><!--{$arrErr[$key2]}--></font><!--{/if}-->

<font size="-1">ｾｲ</font><input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="7" size="14" id="userFuriganaSei">
<font size="-1">ﾒｲ</font><input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" maxlength="7" size="14" id="userFuriganaMei">&nbsp;
<font size="-1" color="#666666"><!--{$smarty.const.SAMPLE_KANA}--></font>
<br>
<br>

<font size="-1">●電話番号<font color="#FF0000"> *</font></font><br>
<!--{assign var=key1 value="`$prefix`tel"}-->
<!--{if $arrErr[$key1]}-->
<font size="-1" color="#FF0000"><!--{$arrErr[$key1]}--></font>
<!--{/if}-->
<input type="text" name="<!--{$key1}-->" id="tel" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN*3}-->" size="<!--{$smarty.const.TEL_ITEM_LEN*3}-->">
<font size="-1" color="#666666"><!--{$smarty.const.SAMPLE_TEL}--></font>
<br>
<br>

<font size="-1">●郵便番号<font color="#FF0000"> *</font></font><br>
<!--{assign var=key value="zip"}-->
<!--{assign var=key3 value="`$prefix`pref"}-->
<!--{assign var=key4 value="`$prefix`addr01"}-->
<!--{assign var=key5 value="`$prefix`addr02"}-->
<!--{assign var=key6 value="`$prefix`house_no"}-->
<!--{if $arrErr[$key]}-->
<font size="-1" color="#FF0000"><!--{$arrErr[$key]}--></font>
<!--{/if}-->
〒<input type="text" name="<!--{$key}-->" id="<!--{$key}-->" value="<!--{$arrForm[$key]|h}-->" maxlength="<!--{math equation="a+b" a=$smarty.const.ZIP_LEN b=1}-->" size="<!--{$smarty.const.ZIP_LEN+2}-->">
<br>
<br>

<font size="-1">●都道府県<font color="#FF0000"> *</font></font><br>
<!--{if $arrErr[$key3]}-->
<font size="-1" color="#FF0000"><!--{$arrErr[$key3]}--></font>
<!--{/if}-->
<select name="<!--{$key3}-->" id="pref">
<option value="" selected="selected">都道府県を選択</option>
<!--{html_options options=$arrPref selected=$arrForm[$key3]}-->
</select>
<br>
<br>

<font size="-1">●市区町村<font color="#FF0000"> *</font></font><br>
<!--{if $arrErr[$key4]}-->
<font size="-1" color="#FF0000"><!--{$arrErr[$key4]}--></font>
<!--{/if}-->
<input type="text" name="<!--{$key4}-->" id="addr1" value="<!--{$arrForm[$key4]|h}-->" maxlength="40">
<font size="-1" color="#666666"><!--{$smarty.const.SAMPLE_ADDRESS1}--></font>
<br>
<br>

<font size="-1">●番地・ビル名<font color="#FF0000"> *</font><br>
<!--{if $arrErr[$key5]}-->
<font size="-1" color="#FF0000"><!--{$arrErr[$key5]}--></font>
<!--{/if}-->
番地が必要のないご住所の場合、「番地なし」にチェックを付けてください。</font><br>
<input type="text" name="<!--{$key5}-->" id="addr2" value="<!--{$arrForm[$key5]|h}-->" maxlength="40"><input type="checkbox" name="house_no" <!--{if $arrForm[$key6]}-->checked="checked"<!--{/if}-->><font size="-1">番地なし</font><br>
<font size="-1" color="#666666"><!--{$smarty.const.SAMPLE_ADDRESS2}--></font>
<br>
<br>

<font size="-1">●メールアドレス<font color="#FF0000"> *</font><br>
<!--{assign var=key1 value="`$prefix`email"}-->
<!--{assign var=key2 value="`$prefix`email02"}-->
<!--{if $arrErr[$key1] || $arrErr[$key2]}-->
<font size="-1" color="#FF0000"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></font>
<!--{/if}-->
<input type="text" name="<!--{$key1}-->" id="email" value="<!--{$arrForm[$key1]|h}-->"><br>
<font size="-1">メールアドレスを確認のため、再度ご入力ください。</font><br>
<input type="text" name="<!--{$key2}-->" id="email02" value="<!--{$arrForm[$key2]|h}-->">
<br>
<br>

<font size="-1">●パスワード:半角<!--{$smarty.const.PASSWORD_MIN_LEN}-->文字以上<!--{$smarty.const.PASSWORD_MAX_LEN}-->文字以内<font color="#FF0000"> *</font></font><br>
<!--{if $arrErr.password || $arrErr.password02}-->
<font size="-1" color="#FF0000"><!--{$arrErr.password}--><!--{$arrErr.password02}--></font>
<!--{/if}-->
<input class="strong-password" type="password" name="password" value="<!--{$arrForm.password|h}-->" maxlength="<!--{$smarty.const.PASSWORD_MAX_LEN}-->"><br>
<font size="-1">パスワードを確認のため、再度ご入力ください。</font><br>
<input class="strong-password" type="password" name="password02" value="<!--{$arrForm.password02|h}-->" maxlength="<!--{$smarty.const.PASSWORD_MAX_LEN}-->">
<br>
<br>

<font size="-1">●性別<font color="#FF0000"> *</font></font><br>
<!--{assign var=key1 value="`$prefix`sex"}-->
<!--{if $arrErr[$key1]}-->
<font size="-1" color="#FF0000"><!--{$arrErr[$key1]}--></font>
<!--{/if}-->

<input type="radio" id="man" name="<!--{$key1}-->" value="1" <!--{if $arrForm[$key1] eq 1}-->checked="checked"<!--{/if}--> /><font size="-1">男性</font>
<input type="radio" id="woman" name="<!--{$key1}-->" value="2" <!--{if $arrForm[$key1] eq 2}-->checked="checked"<!--{/if}--> /><font size="-1">女性</font>
<br>
<br>

<font size="-1">●生年月日</font><br>
<!--{assign var=errBirth value="`$arrErr.year``$arrErr.month``$arrErr.day`"}-->
<!--{if $errBirth}-->
<font size="-1" color="#FF0000"><!--{$errBirth}--></font>
<!--{/if}-->
<select name="year" style="<!--{$errBirth|sfGetErrorColor}-->">
<!--{html_options options=$arrYear selected=$arrForm.year|default:''}-->
</select>年
<select name="month" style="<!--{$errBirth|sfGetErrorColor}-->">
<!--{html_options options=$arrMonth selected=$arrForm.month|default:''}-->
</select>月
<select name="day" style="<!--{$errBirth|sfGetErrorColor}-->">
<!--{html_options options=$arrDay selected=$arrForm.day|default:''}-->
</select>日<br>
<font size="-1">16歳未満のお客様は、必ず保護者様の同意の下にご注文いただくようお願いいたします。</font>
<br>
<br>

<table width="100%" cellspacing="0" cellpadding="1" bordercolor="#dfedf5" border="0" bgcolor="#dfedf5">
<tr>
<th align="left" colspan="2"><h2><font size="-1">お届け先</font></h2></th>
</tr>
</table>
<!--{assign var=key value="other_addr_flg"}-->
<label><input type="checkbox" name="<!--{$key}-->" id="<!--{$key}-->" value="1" <!--{if $arrForm[$key]}-->checked="checked"<!--{/if}--> />上記以外のご住所へ配送</label><br>
※ここにチェックを入れて、下記にお届け先をご入力ください。<br>
<br>

<!--{assign var=prefix value="shipping_"}-->
<font size="-1">●お届け先：漢字氏名<font color="#FF0000"> *</font></font><br>
<!--{assign var=key1 value="`$prefix`name01"}-->
<!--{assign var=key2 value="`$prefix`name02"}-->
<!--{if $arrErr[$key1]}-->
<font size="-1" color="#FF0000"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></font>
<!--{/if}-->
姓<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="7" size="14" id="userNameSeiShip">&nbsp;
名<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" maxlength="7" size="14" id="userNameMeiShip">&nbsp;
<font size="-1" color="#666666"><!--{$smarty.const.SAMPLE_NAME}--></font>
<br>
<br>

<font size="-1">●お届け先：カタカナ氏名<font color="#FF0000"> *</font></font><br>
<!--{assign var=key1 value="`$prefix`kana01"}-->
<!--{assign var=key2 value="`$prefix`kana02"}-->
<!--{if $arrErr[$key1]}-->
<font size="-1" color="#FF0000"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></font>
<!--{/if}-->
ｾｲ<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="7" size="14" id="userFuriganaSeiShip">&nbsp;
ﾒｲ<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" maxlength="7" size="14" id="userFuriganaMeiShip">&nbsp;
<font size="-1" color="#666666"><!--{$smarty.const.SAMPLE_KANA}--></font>
<br>
<br>

<font size="-1">●お届け先：電話番号<font color="#FF0000"> *</font></font><br>
<!--{assign var=key1 value="`$prefix`tel"}-->
<!--{if $arrErr[$key1]}-->
<font size="-1" color="#FF0000"><!--{$arrErr[$key1]}--></font>
<!--{/if}-->
<input type="text" name="<!--{$key1}-->" id="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN*3}-->" size="<!--{$smarty.const.TEL_ITEM_LEN*3}-->">
<font size="-1" color="#666666"><!--{$smarty.const.SAMPLE_TEL}--></font>
<br>
<br>

<font size="-1">●郵便番号<font color="#FF0000"> *</font></font><br>
<!--{assign var=key value="`$prefix`zip"}-->
<!--{assign var=key3 value="`$prefix`pref"}-->
<!--{assign var=key4 value="`$prefix`addr01"}-->
<!--{assign var=key5 value="`$prefix`addr02"}-->
<!--{assign var=key6 value="`$prefix`house_no"}-->
<!--{if $arrErr[$key]}-->
<font size="-1" color="#FF0000"><!--{$arrErr[$key]}--></font>
<!--{/if}-->
〒&nbsp;<input type="text" name="<!--{$key}-->" id="<!--{$key}-->" value="<!--{$arrForm[$key]|h}-->" maxlength="<!--{math equation="a+b" a=$smarty.const.ZIP_LEN b=1}-->" size="<!--{$smarty.const.ZIP_LEN}-->">
<font size="-1" color="#666666"><!--{$smarty.const.SAMPLE_ZIP}--></font>
<br>
<br>

<font size="-1">●都道府県<font color="#FF0000"> *</font></font><br>
<!--{if $arrErr[$key3]}-->
<font size="-1" color="#FF0000"><!--{$arrErr[$key3]}--></font>
<!--{/if}-->
<select name="<!--{$key3}-->" id="<!--{$key3}-->>
<option value="" selected="selected">都道府県を選択</option>
<!--{html_options options=$arrPref selected=$arrForm[$key3]}-->
</select>
<br>
<br>

<font size="-1">●市区町村<font color="#FF0000"> *</font></font><br>
<!--{if $arrErr[$key4]}-->
<font size="-1" color="#FF0000"><!--{$arrErr[$key4]}--></font>
<!--{/if}-->
<input type="text" name="<!--{$key4}-->" id="<!--{$key4}-->" value="<!--{$arrForm[$key4]|h}-->" maxlength="40">
<font size="-1" color="#666666"><!--{$smarty.const.SAMPLE_ADDRESS1}--></font>
<br>
<br>

<font size="-1">●番地・ビル名<font color="#FF0000"> *</font></font><br>
番地が必要のないご住所の場合、「番地なし」にチェックを付けてください。<br>
<!--{if $arrErr[$key5]}-->
<font size="-1" color="#FF0000"><!--{$arrErr[$key5]}--></font>
<!--{/if}-->
<input type="text" name="<!--{$key5}-->" id="<!--{$key5}-->" value="<!--{$arrForm[$key5]|h}-->" maxlength="40"><label><input type="checkbox" name="shipping_house_no" id="shipping_house_no" <!--{if $arrForm[$key6]}-->checked="checked"<!--{/if}-->><font size="-1">番地なし</font></label><br>
<font size="-1" color="#666666"><!--{$smarty.const.SAMPLE_ADDRESS2}--></font>
<br>
<br>

<!--{assign var=key value="agree"}-->
<input type="hidden" name="<!--{$key}-->" id="<!--{$key}-->" value="1" />
<!--{*
<table width="100%" cellspacing="0" cellpadding="1" bordercolor="#dfedf5" border="0" bgcolor="#dfedf5">
<tr>
<th align="left" colspan="2"><h2><font size="-1">ご利用規約</font></h2></th>
</tr>
</table>
<font size="-1">
会員登録をされる前に、<a href="<!--{$smarty.const.ROOT_URLPATH}-->entry/kiyaku.php" target="_blank">ご利用規約</a>をよくお読みください。<br>
規約には、本サービスを使用するに当たってのあなたの権利と義務が規定されております。<br>
<!--{assign var=key value="agree"}-->
<!--{if $arrErr[$key]}-->
<font size="-1" color="#FF0000">※ 利用規約へのご同意が必要です。</font><br>
<!--{/if}-->
<input type="checkbox" name="<!--{$key}-->" id="<!--{$key}-->" value="1" <!--{if $arrForm[$key]}-->checked="checked"<!--{/if}--> />規約に同意する
</font><br>
<br>
*}-->
<center><input type="submit" name="submit" value="次へ"></center>

<!--{foreach from=$list_data key=key item=item}-->
<input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->">
<!--{/foreach}-->
</form>
