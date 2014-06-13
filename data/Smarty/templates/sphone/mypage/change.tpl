<!--▼CONTENTS-->
<!--{* -*- coding: utf-8-unix; -*- *}-->
<script type="text/javascript">
var get_zip_addr = "<!--{$smarty.const.INPUT_ZIP_URLPATH}-->";
</script>
<script src="<!--{$TPL_URLPATH}-->js/pschecker.js" type="text/javascript"></script>
<script src="<!--{$TPL_URLPATH}-->js/hanzenkaku.min.js" type="text/javascript"></script>
<script src="<!--{$TPL_URLPATH}-->js/jquery.autoKana.js" type="text/javascript"></script>
<script src="<!--{$smarty.const.ROOT_URLPATH}-->js/mypage_basic.js" type="text/javascript"></script>

<div id="mainMyPage">
	<h2 class="spNaked"><!--{$tpl_title}--></h2>

	<!--{if $tpl_navi != ""}-->
		<!--{include file=$tpl_navi}-->
	<!--{else}-->
		<!--{include file=`$smarty.const.TEMPLATE_REALDIR`mypage/navi.tpl}-->
	<!--{/if}-->

    <div class="wrapForm">
        <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="confirm" />
        <input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id|h}-->" />
        <input type="hidden" name="birth" value="<!--{$arrForm.birth|h}-->" />

        <table summary="会員登録内容変更" class="tblOrder delivname" style="width:100%;">
<tr>
    <th>顧客番号</th>
</tr>
<tr>
	<td>
      <!--{assign var=key value="customer_id"}-->
      <!--{$arrForm[$key]|h}-->
    </td>
</tr>
<tr>
    <th>お名前 <img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_hisu.gif" alt="必須" /></th>
</tr>
<tr>
    <td>
        <!--{assign var=key1 value="`$prefix`name"}-->
        <!--{if $arrErr[$key1]}-->
            <div class="attention"><!--{$arrErr[$key1]}--></div>
        <!--{/if}-->
        <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="16" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: active;" class="box240" id="userName" />&nbsp;
    </td>
</tr>
<tr>
    <th>お名前(フリガナ) <img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_hisu.gif" alt="必須" /></th>
</tr>
<tr>
    <td>
        <!--{assign var=key1 value="`$prefix`kana"}-->
        <!--{if $arrErr[$key1]}-->
            <div class="attention"><!--{$arrErr[$key1]}--></div>
        <!--{/if}-->
        <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="15" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: active;" class="box240" id="userFurigana" />&nbsp;
    </td>
</tr>
<tr>
    <th>電話番号 <img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_hisu.gif" alt="必須" /></th>
</tr>
<tr>
    <td>
        <!--{assign var=key1 value="`$prefix`tel"}-->
        <!--{if $arrErr[$key1]}-->
            <div class="attention"><!--{$arrErr[$key1]}--></div>
        <!--{/if}-->
        <input type="text" name="<!--{$key1}-->" id="tel" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN*3}-->" size="<!--{$smarty.const.TEL_ITEM_LEN*3}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box180" />
    </td>
</tr>
<tr>
    <th>郵便番号 <img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_hisu.gif" alt="必須" /></th>
</tr>
<tr>
    <td>
        <!--{assign var=key value="zip"}-->
        <!--{assign var=key3 value="`$prefix`pref"}-->
        <!--{assign var=key4 value="`$prefix`addr01"}-->
        <!--{assign var=key5 value="`$prefix`addr02"}-->

        <!--{if $arrErr[$key]}-->
            <div class="attention"><!--{$arrErr[$key]}--></div>
        <!--{/if}-->

        <p class="top">〒&nbsp;<input type="text" name="<!--{$key}-->" id="<!--{$key}-->" value="<!--{$arrForm[$key]|h}-->" maxlength="<!--{math equation="a+b" a=$smarty.const.ZIP_LEN b=1}-->" size="<!--{$smarty.const.ZIP_LEN+2}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;<img src="<!--{$TPL_URLPATH}-->img/soyafarm/zip.gif" alt="住所自動入力" id="easy" /></p>
        <p>郵便番号をご入力後、ボタンを押してください。ご住所が自動で入力されます。<br />
        [<a href="http://search.post.japanpost.jp/zipcode/" target="_blank"><span class="mini">郵便番号検索ページヘ</span></a>]</p>
    </td>
</tr>

<tr>
    <th>住所 <img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_hisu.gif" alt="必須" /></th>
</tr>
<tr>
    <td>
        <!--{if $arrErr[$key3] || $arrErr[$key4] || $arrErr[$key5]}-->
            <div class="attention"><!--{$arrErr[$key3]}--><!--{$arrErr[$key4]}--><!--{$arrErr[$key5]}--></div>
        <!--{/if}-->
        <select name="<!--{$key3}-->" id="pref" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->">
                <option value="" selected="selected">都道府県を選択</option>
                <!--{html_options options=$arrPref selected=$arrForm[$key3]}-->
        </select><br />
        <p class="top"><div id="addr1-div"><input type="text" name="<!--{$key4}-->" id="addr1" value="<!--{$arrForm[$key4]|h}-->" maxlength="40" style="<!--{$arrErr[$key4]|sfGetErrorColor}-->; ime-mode: active;" />
        <span id="addr1-navi">制限文字数を超えています</span></div>
            <span class="example"><!--{$smarty.const.SAMPLE_ADDRESS1}--></span></p>
        <p class="top"><input type="text" name="<!--{$key5}-->" id="addr2" value="<!--{$arrForm[$key5]|h}-->" maxlength="40" style="<!--{$arrErr[$key5]|sfGetErrorColor}-->; ime-mode: active;" /><label><input type="checkbox" name="house_no" id="house_no" />番地なし</label><br />
        <span id="addr2-navi">制限文字数を超えています</span></p>
            <span class="example"><!--{$smarty.const.SAMPLE_ADDRESS2}--></span>
        <p>番地が必要のないご住所の場合、「番地なし」にチェックを付けてください。</p>
    </td>
</tr>
    <input type="hidden" name="email" value="<!--{$arrForm.email}-->" />
    <input type="hidden" name="email02" value="<!--{$arrForm.email02}-->" />
    <tr>
        <th>メールアドレス</th>
</tr>
<tr>
        <td>
        <!--{$arrForm.email}-->
        <p class="mini"><span class="attention">※メールアドレスとパスワードの変更は<a href="change_basic.php">こちら</a>から</span></p>
        </td>
    </tr>
    <tr>
        <th>性別 <img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_hisu.gif" alt="必須" /></th>
</tr>
<tr>
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
        <th>生年月日 <img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_hisu.gif" alt="必須" /></th>
    </tr>
    <tr>
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
            <th>メールのご案内 <img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_hisu.gif" alt="必須" /></th>
</tr>
<tr>
            <td>
                <!--{if $arrErr.mailmaga_flg}-->
                    <div class="attention"><!--{$arrErr.mailmaga_flg}--></div>
                <!--{/if}-->
                <span style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->">
                    <input type="radio" name="mailmaga_flg" value="1" id="mailmaga_flg_1" <!--{if $arrForm.mailmaga_flg eq '1'}--> checked="checked" <!--{/if}--> /><label for="mailmaga_flg_1">受け取る</label><input type="radio" name="mailmaga_flg" value="0" id="mailmaga_flg_0" <!--{if $arrForm.mailmaga_flg eq '0'}--> checked="checked" <!--{/if}--> /><label for="mailmaga_flg_0">受け取らない</label><div id="notReceive" style="display: none;" ><img width="100%" src="<!--{$TPL_URLPATH}-->img/soyafarm/bnr_ml_merit.gif" alt="受け取りませんか"></a></div>
                </span>
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
