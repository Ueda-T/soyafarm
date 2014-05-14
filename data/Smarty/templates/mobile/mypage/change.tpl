<form name="form1" method="post" action="?">
    <input type="hidden" name="mode" value="confirm" />
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="birth" value="<!--{$arrForm.birth|h}-->" />


	顧客番号<br>
    <input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id}-->" />
    <!--{$arrForm.customer_id|h}--><br>
    <hr>

	<font color="#FF0000">【必須】</font>お名前<br>
	<font color="#FF0000"><!--{$arrErr.name}--></font>
	<input type="text" name="name" value="<!--{$arrForm.name|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="1"><br>
    <hr>

	<font color="#FF0000">【必須】</font>フリガナ<br>
	<font color="#FF0000"><!--{$arrErr.kana}--></font>
	<input type="text" name="kana" value="<!--{$arrForm.kana|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="2"><br>
    <hr>

	<font color="#FF0000">【必須】</font>電話番号<br>
    <!--{assign var=key1 value="`$prefix`tel"}-->
    <!--{if $arrErr[$key1]}-->
	<font color="#FF0000"><!--{$arrErr[$key1]}--></font>
    <!--{/if}-->
	<!--{assign var="size" value="`$smarty.const.TEL_ITEM_LEN+2`"}-->
    <input type="text" name="<!--{$key1}-->" id="tel" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN*3}-->" size="<!--{$smarty.const.TEL_ITEM_LEN*3}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box180" /><br>
    <hr>

	<!--{assign var=key1 value="zip"}-->
	<font color="#FF0000">【必須】</font>郵便番号<br>
	<font color="#FF0000"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></font>
	<input type="text" name="zip" value="<!--{if $arrForm.zip == ""}--><!--{$arrOtherDeliv.zip|h}--><!--{else}--><!--{$arrForm.zip|h}--><!--{/if}-->" maxlength="8" istyle="4">
    <br>
    <hr>

	<font color="#FF0000">【必須】</font>都道府県<br>
	<font color="#FF0000"><!--{$arrErr.pref}--></font>
	<select name="pref">
		<option value="">都道府県を選択</option>
		<!--{html_options options=$arrPref selected=$arrForm.pref}-->
	</select><br>
    <hr>

	<font color="#FF0000">【必須】</font>市区町村<br>
	<font color="#FF0000"><!--{$arrErr.addr01}--></font>
	<input type="text" name="addr01" value="<!--{$arrForm.addr01|h}-->" istyle="1"><br>
    <hr>

	<font color="#FF0000">【必須】</font>番地･ﾋﾞﾙ名<br>
	<font color="#FF0000"><!--{$arrErr.addr02}--></font>
	<input type="text" name="addr02" value="<!--{$arrForm.addr02|h}-->" istyle="1"><br>
        <label><input type="checkbox" name="house_no" <!--{if $arrForm.house_no}-->checked="checked"<!--{/if}-->/>番地なし</label><br>
    <hr>

	ﾒｰﾙｱﾄﾞﾚｽ<br>
	<font color="#FF0000"><!--{$arrErr.email}--></font>
	<input type="hidden" name="email" value="<!--{$arrForm.email|h}-->" istyle="3">
	<!--{$arrForm.email|h}--><br>
    <a href="change_basic.php">ﾒｰﾙｱﾄﾞﾚｽ,ﾊﾟｽﾜｰﾄﾞの変更</a><br>
    <hr>

	<font color="#FF0000">【必須】</font>性別<br>
	<font color="#FF0000"><!--{$arrErr.sex}--></font>
	<input type="radio" name="sex" value="1" <!--{if $arrForm.sex eq 1}-->checked<!--{/if}--> />男性&nbsp;<input type="radio" name="sex" value="2" <!--{if $arrForm.sex eq 2}-->checked<!--{/if}--> />女性<br>
    <hr>

	生年月日<br>
	<font color="#FF0000"><!--{$arrErr.year}--><!--{$arrErr.month}--><!--{$arrErr.day}--></font>
    <!--{if $arrForm.birth && $arrForm.year && $arrForm.month && $arrForm.day}-->
        <!--{$arrForm.year}-->年<!--{$arrForm.month}-->月<!--{$arrForm.day}-->日
        <input type="hidden" name="year" value="<!--{$arrForm.year}-->" />
        <input type="hidden" name="month" value="<!--{$arrForm.month}-->" />
        <input type="hidden" name="day" value="<!--{$arrForm.day}-->" />
    <!--{else}-->
        <input type="text" name="year" value="<!--{$arrForm.year|h}-->" size="4" maxlength="4" istyle="4">年
        <select name="month">
            <!--{html_options options=$arrMonth selected=$arrForm.month}-->
        </select>月
        <select name="day">
            <!--{html_options options=$arrDay selected=$arrForm.day}-->
        </select>日<br>
    <!--{/if}-->
    <hr>

	<font color="#FF0000">【必須】</font>ﾒｰﾙのご案内<br>
	<input type="hidden" name="mailmaga_flg" value="3">
    <!--{if $arrErr.mailmaga_flg}-->
	<font color="#FF0000"><!--{$arrErr.mailmaga_flg}--></font>
    <!--{/if}-->
    <input type="radio" name="mailmaga_flg" value="1" id="mailmaga_flg_1" <!--{if $arrForm.mailmaga_flg eq '1'}--> checked="checked" <!--{/if}--> />受け取る<input type="radio" name="mailmaga_flg" value="0" id="mailmaga_flg_0" <!--{if $arrForm.mailmaga_flg eq '0'}--> checked="checked" <!--{/if}--> />受け取らない
	
	<br>

	<center><input type="submit" name="submit" value="次へ"></center>

	<!--{foreach from=$list_data key=key item=item}-->
		<input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->">
	<!--{/foreach}-->
    <input type="hidden" name="password" value="<!--{$arrForm.password}-->" />

</form>
