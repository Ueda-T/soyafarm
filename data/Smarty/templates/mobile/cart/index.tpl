
<!--{if !$tpl_login}-->
<form name="member_form" id="member_form" method="post" action="../frontparts/login_check.php">
	<input type="hidden" name="mode" value="login" >
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="url" value="<!--{$smarty.server.REQUEST_URI|h}-->" />    
<table width="100%" border="0" cellpadding="1" cellspacing="0" bgcolor="#dfedf5" bordercolor="#dfedf5">
<tr>
<th colspan="2" align="left"><h2><font size="-1">ﾛｸﾞｲﾝ情報の入力</font></h2></th>
</tr>
</table>
<!--{if !$tpl_valid_phone_id}-->
<font size="-1">
	[emoji:110]メールアドレス<br>
    <!--{assign var=key value="login_email"}-->
	<font color="#FF0000"><!--{$arrErr[$key]}--></font>
    <input type="text" name="<!--{$key}-->" value="<!--{$tpl_login_email|h}-->" maxlength="<!--{$arrForm[$key].length}-->" size="40" istyle="3"><br>
<!--{else}-->
	<input type="hidden" name="login_email" value="dummy">
<!--{/if}-->
	[emoji:116]パスワード<br>
    <!--{assign var=key value="login_pass"}-->
    <font color="#FF0000"><!--{$arrErr[$key]}--></font>
    <input type="password" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" size="40" istyle="3"><br>
<div align="center">
	<center><input type="submit" value="ログイン" name="log"></center><br>
	<a href="<!--{$smarty.const.HTTPS_URL}-->forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->">パスワードを忘れた方はこちら</a><br>
</div>
</font>
</form>
<!--{else}-->
<strong><!--{$tpl_name|h}--> 様</strong>
<!--{if $smarty.const.USE_POINT !== false && $tpl_user_point}-->
<!--{$tpl_user_point|number_format|default:0}-->ポイント
<!--{/if}-->
<br>
<font color="maroon">[emoji:e9]</font><a href="<!--{$smarty.const.ROOT_URLPATH}-->frontparts/login_check.php?mode=logout">ﾛｸﾞｱｳﾄ</a><br>
<!--{/if}-->


<!--{* カゴの中に商品がある場合にのみ表示 *}-->
<!--{if count($cartKeys) > 1}-->
<font color="#FF0000"><!--{foreach from=$cartKeys item=key name=cartKey}--><!--{$arrProductType[$key]}--><!--{if !$smarty.foreach.cartKey.last}-->、<!--{/if}--><!--{/foreach}-->
は同時購入できません。お手数ですが、個別に購入手続きをお願い致します。<br></font>
<br>
<!--{/if}-->

<!--{if strlen($tpl_error) != 0}-->
<font color="#FF0000"><!--{$tpl_error|h}--></font><br>
<!--{/if}-->

<!--{assign var=sub_total value=0}-->
<!--{if count($cartItems) > 0}-->
<!--{foreach from=$cartKeys item=key}-->
<form name="form<!--{$key}-->" id="form<!--{$key}-->" method="post" action="?">
  <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
	<input type="hidden" name="cart_no" value="">
	<input type="hidden" name="cartKey" value="<!--{$key}-->">
  <input type="hidden" name="category_id" value="<!--{$tpl_category_id|h}-->" />
	<!--ご注文内容ここから-->

  <!--{* 社員の購入可能数のメッセージ *}-->
  <!--{if $tpl_employee_order_msg}-->
	  <p><!--{$tpl_employee_order_msg|h}--></p>
	<!--{/if}-->

  <!--{* カート内エラーを表示 *}-->
  <!--{if strlen($tpl_message) != 0}-->
      <font color="#FF0000"><!--{$tpl_message|h|nl2br}--></font>
  <!--{/if}-->

  <!--{* 同時購入エラーメッセージ *}-->
  <!--{if $tpl_payment_total_err}-->
	  <font color="#FF0000"><!--{$tpl_payment_total_err|h}--></font>
  <!--{/if}-->

  <!--{* 合計金額10万円以上はエラーメッセージ *}-->
  <!--{if $tpl_dropshipment_err}-->
	  <font color="#FF0000"><!--{$tpl_dropshipment_err|h}--></font>
  <!--{/if}-->

  <!--{* 社員専用エラーメッセージ *}-->
  <!--{foreach from=$arrEmployeeErr item=employee_err}-->
  <!--{if $employee_err|strlen > 0}-->
	  <font color="#FF0000"><!--{$employee_err|h}--></font>
	<!--{/if}-->
  <!--{/foreach}-->
<br>
<div align="center"><font size="-1">
<input type="submit" name="confirm" value=" 次へ(購入手続きへ)≫ "><br>
</font></div>
<font size="-1">
お買い物を続ける場合は､携帯電話の｢戻る｣ﾎﾞﾀﾝを押すか､数字の0ｷｰを押してﾄｯﾌﾟﾍﾟｰｼﾞにお戻りください｡<br><br>
</font>

<table width="100%" border="0" cellpadding="1" cellspacing="0" bgcolor="#ffce00" bordercolor="#ffce00">
<tr>
<th colspan="2" align="center"><h2><font size="-1">
商品合計(税込):<font color="#cc0000"><!--{$tpl_total_inctax[$key]|number_format}-->円</font>
</font></h2></th>
</tr>
</table>

    <!--{if count($cartKeys) > 1}-->
    <hr>
    ■<!--{$arrProductType[$key]}-->
    <hr>
    <!--{/if}-->


    <!--{foreach from=$cartItems[$key] item=item name=item}-->
<!--{if $item.regular_flg != $smarty.const.REGULAR_PURCHASE_FLG_ON}-->

<font color="#0066FF">▼単回購入</font><br>
<!--{if $smarty.foreach.item.iteration %2 == 0}-->
<table width="100%" border="0" cellpadding="1" cellspacing="0" bgcolor="#eaeaea" bordercolor="#eaeaea">
<!--{else}-->
<table width="100%" border="0" cellpadding="1" cellspacing="0" bgcolor="#ffffff" bordercolor="#ffffff">
<!--{/if}-->
<tr valign="top">
<td width="33%"><img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.main_image|sfNoImageMainList|h}-->" width="60"></td>
<td><font size="-1">
		◎<!--{* 商品名 *}--><a href="<!--{$smarty.const.MOBILE_P_DETAIL_URLPATH}--><!--{$item.productsClass.product_id|u}-->"><!--{$item.productsClass.name|h}--></a><br>
        <!--{* 規格名1 *}--><!--{if $item.productsClass.classcategory_name1 != ""}--><!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--><br><!--{/if}-->
        <!--{* 規格名2 *}--><!--{if $item.productsClass.classcategory_name2 != ""}--><!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}--><br><!--{/if}-->
</td>
</tr>
<tr>
<td width="33%"><font size="-1">単価(税込)</font></td>
<td><font size="-1">
<!--{* 販売価格 *}-->
<!--{$item.price|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
</td>
</tr>
<td width="33%"><!--{* 数量 *}--><font size="-1">数量</font></td>
<td><font size="-1"><!--{$item.quantity}-->
		<a href="?mode=up&amp;cart_no=<!--{$item.cart_no}-->&amp;cartKey=<!--{$key}-->">＋</a>
		<a href="?mode=down&amp;cart_no=<!--{$item.cart_no}-->&amp;cartKey=<!--{$key}-->">－</a>
		<a href="?mode=delete&amp;cart_no=<!--{$item.cart_no}-->&amp;cartKey=<!--{$key}-->">削除</a>
</td>
</tr>
<tr>

<!--{assign var=sub_total value=$sub_total+$item.total_inctax}-->
<td width="33%"><!--{* 合計 *}--><font size="-1">小計(税込)</font></td>
<td><font color="#ff6600" size="-1"><!--{$item.total_inctax|number_format}-->円<br>
</font>
</td>
</tr>
</table>
<!--{else}-->
<br>
<br>
<font color="#0066FF">▼定期購入</font><br>
<!--{* 定期商品 *}-->
<!--{if $smarty.foreach.item.iteration %2 == 0}-->
<table width="100%" border="0" cellpadding="1" cellspacing="0" bgcolor="#eaeaea" bordercolor="#eaeaea">
<!--{else}-->
<table width="100%" border="0" cellpadding="1" cellspacing="0" bgcolor="#ffffff" bordercolor="#ffffff">
<!--{/if}-->
<tr valign="top">
<td width="33%"><img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.main_image|sfNoImageMainList|h}-->" width="60"></td>
<td><font size="-1">
		◎<!--{* 商品名 *}--><a href="<!--{$smarty.const.MOBILE_P_DETAIL_URLPATH}--><!--{$item.productsClass.product_id|u}-->"><!--{$item.productsClass.name|h}--></a><br>
        <!--{* 規格名1 *}--><!--{if $item.productsClass.classcategory_name1 != ""}--><!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--><br><!--{/if}-->
        <!--{* 規格名2 *}--><!--{if $item.productsClass.classcategory_name2 != ""}--><!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}--><br><!--{/if}-->
</td>
</tr>
<tr>
<td width="33%"><font size="-1">単価(税込)</font></td>
<td><font size="-1">
<!--{* 販売価格 *}-->
<!--{$item.price|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
</td>
</tr>
<td width="33%"><!--{* 数量 *}--><font size="-1">数量</font></td>
<td><font size="-1"><!--{$item.quantity}-->
		<a href="?mode=up&amp;cart_no=<!--{$item.cart_no}-->&amp;cartKey=<!--{$key}-->">＋</a>
		<a href="?mode=down&amp;cart_no=<!--{$item.cart_no}-->&amp;cartKey=<!--{$key}-->">－</a>
		<a href="?mode=delete&amp;cart_no=<!--{$item.cart_no}-->&amp;cartKey=<!--{$key}-->">削除</a>
</td>
</tr>
<tr>
<td width="33%"><!--{* お届け間隔 *}--><font size="-1">お届け間隔</font></td>
<td>

    <!--{assign var=key1 value="course_cd`$item.cart_no`"}-->
    <!--{assign var=key2 value="todoke_cycle`$item.cart_no`"}-->
    <!--{assign var=key3 value="todoke_week_no`$item.cart_no`"}-->
    <!--{assign var=key4 value="todoke_week`$item.cart_no`"}-->
    <!--{assign var=key5 value="todoke_kbn`$item.cart_no`"}-->
    <input type="hidden" name="<!--{$key5}-->" id="<!--{$key5}-->" value="<!--{$arrForm[$key5]}-->" />
    <font color="#FF0000" size="-1"><!--{$arrErr[$key1]}--></font>
    <font color="#FF0000" size="-1"><!--{$arrErr[$key2]}--></font>
    <select name="<!--{$key1}-->" id="<!--{$key1}-->">
        <!--{html_options options=$arrCourseCd selected=$arrForm[$key1]|default:''}-->
    </select>
    <select name="<!--{$key2}-->" id="<!--{$key2}-->">
        <!--{html_options options=$arrTodokeKbn selected=$arrForm[$key2]|default:''}-->
    </select>
</td>
</tr>
<td width="33%"><!--{* お届け曜日指定 *}--><font size="-1">お届け曜日指定</font></td>
<td>
    <font color="#FF0000" size="-1"><!--{$arrErr[$key3]}--></font>
    <font color="#FF0000" size="-1"><!--{$arrErr[$key4]}--></font>
    <select name="<!--{$key3}-->" id="<!--{$key3}-->">
    <option></option>
    <!--{html_options options=$arrTodokeWeekNo selected=$arrForm[$key3]|default:''}-->
    </select>
    <select name="<!--{$key4}-->" id="<!--{$key4}-->">
    <option></option>
    <!--{html_options options=$arrTodokeWeek selected=$arrForm[$key4]|default:''}-->
    </select>
<tr>

<!--{assign var=sub_total value=$sub_total+$item.total_inctax}-->
<td width="33%"><!--{* 合計 *}--><font size="-1">小計(税込)</font></td>
<td><font color="#ff6600" size="-1"><!--{$item.total_inctax|number_format}-->円<br>
</font>
</td>
</tr>
</table>
<!--{/if}-->
	<!--{/foreach}-->

<!--{*
	<font color="#FF0000">
	商品合計:<!--{$tpl_total_inctax[$key]|number_format}-->円<br>
	合計:<!--{$arrData[$key].total-$arrData[$key].deliv_fee|number_format}-->円<br>
	</font>
    <br>
    <!--{if $key != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
    <!--{if $arrInfo.free_rule > 0}-->
    <!--{if !$arrData[$key].is_deliv_free}-->
        あと「<font color="#FF0000"><!--{$tpl_deliv_free[$key]|number_format}-->円</font>」で<font color="#FF0000">送料無料</font>です！！<br>
    <!--{else}-->
        現在、「<font color="#FF0000">送料無料</font>」です！！<br>
    <!--{/if}-->
    <br>
    <!--{/if}-->
    <!--{/if}-->

	<!--{if $smarty.const.USE_POINT !== false}-->
    <!--{if $arrData[$key].birth_point > 0}-->
		お誕生月ﾎﾟｲﾝﾄ<br>
		<!--{$arrData[$key].birth_point|number_format}-->pt<br>
	<!--{/if}-->
    今回加算ﾎﾟｲﾝﾄ<br>
    <!--{$arrData[$key].add_point|number_format}-->pt<br>
    <br>
	<!--{/if}-->
*}-->

<table width="100%" border="0" cellpadding="1" cellspacing="0">
<tr>
<td>
<!--{assign var=key1 value="use_point"}-->
<font color="#FF0000" size="-1"><!--{$arrErr[$key1]}--></font>
ご使用ポイント</td>
<td>
<input type="text" size="5" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->"/>pt / <!--{$tpl_user_point|number_format}-->pt
</td>
</tr>
<!--{if $tpl_customer_kbn != $smarty.const.CUSTOMER_KBN_EMPLOYEE}-->
<tr>
<td>
<!--{if $tpl_order_promotion_err}-->
<font color="#FF0000" size="-1">「キャンペーンコード」に<br>入力いただいた<br>内容が正しく<br>ありません。</font>
<!--{/if}-->
キャンペーンコード
</td>
<td>
<input type="text" size="15" name="campaign_code" value="<!--{$tpl_campaign_code}-->" maxlength="<!--{$smarty.const.CAMPAIGN_CODE_LEN}-->" />
</td>
</tr>
<tr>
<td>
</td>
<td>
<input type="submit" name="re_calc" value="再計算"><br>
</td>
</tr>
<!--{/if}-->
<tr>
<td>
</td>
</tr>
</table>

<table width="100%" border="0" cellpadding="1" cellspacing="0" bgcolor="#ffce00" bordercolor="#ffce00">
<tr>
<td colspan="2" align="center"><font size="-1">
商品合計(税込)<font color="#cc0000"><!--{$sub_total|number_format}-->円</font>
＋
送料
<font color="#cc0000"><!--{$arrData[$key].deliv_fee|number_format}-->円</font>
<!--{if $tpl_input_campaign_ok_flg}--><br>キャンペーンコードが適用されました<!--{/if}-->
<br>お支払いの合計(税込):<font color="#cc0000"><!--{$arrData[$key].total|number_format}-->円</font><br>

<input type="submit" name="confirm" value=" 次へ(購入手続きへ)≫ "><br>
<br>
</font></td>
</tr>
</table>

</form>

<!--{*
<br>
<!--{if $tpl_prev_url != ""}-->
    <a href="<!--{$tpl_prev_url|h}-->">[emoji:69]お買物を続ける</a><br>
    <br>
<!--{/if}-->
*}-->
<!--{/foreach}-->
<!--{else}-->
	<font color="#0066FF">現在､ｶｰﾄには商品が入っておりません｡</font><br>
    <br>
<!--{/if}-->

<hr color="#b83314">
<font size="-1">
｢<a href="<!--{$smarty.const.ROOT_URLPATH}-->shunkoku-shunsai.php">旬穀旬菜</a>｣と｢<a href="<!--{$smarty.const.ROOT_URLPATH}-->">ﾛｰﾄ通販</a>｣は同じお買物カゴでお買物ができます｡<br>
<br>
</font>

<div align="right"><font size="-1">
<a href="#top"><font color="#003b9b">ﾍﾟｰｼﾞTOP▲</font></a>
</font></div>

<hr color="#dfedf5">
<font size="-1">
直前の画面に戻る場合は､携帯電話の｢戻る｣ﾎﾞﾀﾝを押すか､数字の0ｷｰを押してﾄｯﾌﾟﾍﾟｰｼﾞにお戻りください｡
</font>

<div align="right"><font size="-1">
<a href="#top"><font color="#003b9b">ﾍﾟｰｼﾞTOP▲</font></a>
</font></div>
<br>

<!--{*
<!--{if $smarty.const.USE_POINT !== false && $tpl_user_point}-->
<hr>
<!--{if $tpl_login}-->
<!--{$tpl_name|h}--> 様の、現在の所持ポイントは「<font color="#FF0000"><!--{$tpl_user_point|number_format|default:0}--> pt</font>」です。<br>
<!--{else}-->
ポイント制度をご利用になられる場合は、会員登録後ログインしてくださいますようお願い致します。
<!--{/if}-->
ポイントは商品購入時に1ptを<!--{$smarty.const.POINT_VALUE}-->円として使用することができます。
<!--{/if}-->
*}-->
