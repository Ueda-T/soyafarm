<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/facebox.js"></script>
<link rel="stylesheet" type="text/css" href="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/facebox.css" media="screen" />
<script type="text/javascript">//<![CDATA[
$(document).ready(function() {
    $('a.expansion').facebox({
        loadingImage : '<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/loading.gif',
        closeImage   : '<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/closelabel.png'
    });

    // お届け曜日の表示制御
    function selectTodokeKbn(index) {

        var todoke_cycle = 'select[name="todoke_cycle' + index + '"]';
        // 「日ごと(todoke_cycle = 1)」を選択した場合はお届け曜日を非表示
        if ($(todoke_cycle).val() ==
            '<!--{$smarty.const.TODOKE_CYCLE_DAY}-->') {
 
            $("#todoke_week" + index + '_select').css("display", "none");
            $("#todoke_week_no" + index).val("");
            $("#todoke_week" + index).val("");

        } else {
            $("#todoke_week" + index + '_select').css("display", "");
        }
    }
    var type_all_exp = 'select[name^="todoke_cycle"]';
    $(type_all_exp).each(function(){
        var index = $(this).attr('name').replace('todoke_cycle', '');
        selectTodokeKbn(index);
        $(this).change(function(){selectTodokeKbn(index);});
    });

    // 社員の場合
    if ('<!--{$tpl_customer_kbn}-->' == '<!--{$smarty.const.CUSTOMER_KBN_EMPLOYEE}-->') {
        // キャンペーン項目が非表示になるのでCSS調整
        $('.cartTotalBox .ptInput li').css("border-right", "1px solid #fff");
        $('.cartTotalBox .ptInput li').css("display", "inline");
        $('.cartTotalBox .ptInput li').css("padding", "0 1em");
    }
});
    // AJAXログイン
    function ajaxLogin() {
        var postData = new Object;
        postData['<!--{$smarty.const.TRANSACTION_ID_NAME}-->'] = "<!--{$transactionid}-->";
        postData['mode'] = 'login';
        postData['login_email'] = $('input[type=email]').val();
        postData['login_pass'] = $('input[type=password]').val();
        postData['url'] = $('input[name=url]').val();
    
        $.ajax({
            type: "POST",
            url: "<!--{$smarty.const.HTTPS_URL}-->frontparts/login_check.php",
            data: postData,
            cache: false,
            dataType: "json",
            error: function(XMLHttpRequest, textStatus, errorThrown){
                alert(textStatus);
            },
            success: function(result){
                if (result.success) {
                    location.href = result.success;
                } else {
                    alert(result.login_error);
                }
            }
        });
    }
//]]>
</script>
<section>
<!--▼CONTENTS-->
<section id="undercolumn">
<h2 class="spNaked"><img src="<!--{$TPL_URLPATH}-->img/rohto/icon_cart.gif" width="23" height="16">お買い物カゴ</h2>

    <!--{* カゴの中に商品がある場合にのみ表示 *}-->
    <!--{if count($cartKeys) > 1}-->
        <span class="attentionSt"><!--{foreach from=$cartKeys item=key name=cartKey}--><!--{$arrProductType[$key]}--><!--{if !$smarty.foreach.cartKey.last}-->、<!--{/if}--><!--{/foreach}-->は同時購入できません。<br />
                    お手数ですが、個別に購入手続きをお願い致します。
        </span>
    <!--{/if}-->

    <!--{if strlen($tpl_error) != 0}-->
        <p class="attention"><!--{$tpl_error|h}--></p>
    <!--{/if}-->

    <!--▼ログインフォーム-->

    <!--{* ▼ログイン後 *}-->
    <!--{if $tpl_login}-->
    
      <!--▼logout form-->
      &nbsp;<strong><!--{$tpl_name|h}--> 様</strong>
    <!--{if $smarty.const.USE_POINT !== false && $tpl_user_point}-->
      <strong><!--{$tpl_user_point|number_format}-->pt</strong>
    <!--{/if}-->
      <!--{if !$tpl_disable_logout}-->
      <form name="header_login_form" id="header_login_form" method="post" action="<!--{$smarty.const.ROOT_URLPATH}-->frontparts/login_check.php" onsubmit="return fnCheckLogin('header_login_form')">
        <input type="hidden" name="mode" value="login" />
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="url" value="<!--{$smarty.server.REQUEST_URI|h}-->" />
        <p style="text-align:right;margin-bottom:10px;">
          <a href="javascript:void(0);" onclick="fnFormModeSubmit('header_login_form', 'logout', '', ''); return false;" class="btnGray">ログアウト</a>
        </p>
      </form>
      <!--{/if}-->
      <!--▲logout form-->
    
    <!--{* ▼ログイン前 *}-->
    <!--{else}-->
      <!--▼login form-->
      	<p class="naked" style="margin-bottom:10px;">ここから先はﾛｸﾞｲﾝ､または会員登録が必要です｡</p>
      
           <form name="login_mypage" id="login_mypage" method="post" action="javascript:;" onsubmit="return ajaxLogin();">
              <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
              <input type="hidden" name="mode" value="login" />
              <input type="hidden" name="url" value="<!--{$smarty.server.REQUEST_URI|h}-->" />
      <div>
      
          <div>
                <!--{assign var=key value="login_email"}-->
                     <span class="attention"><!--{$arrErr[$key]}--></span>
                        <input type="email" name="<!--{$key}-->" value="<!--{$tpl_login_email|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="width:100%; <!--{$arrErr[$key]|sfGetErrorColor}-->" class="mailtextBox data-role-none" placeholder="メールアドレス" />
      
                <!--{assign var=key value="login_pass"}-->
                     <span class="attention"><!--{$arrErr[$key]}--></span>
                        <input type="password" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" style="width:100%; <!--{$arrErr[$key]|sfGetErrorColor}-->" class="passtextBox data-role-none" placeholder="パスワード" />
      
      </div><!--▲loginBox -->
      
      <p style="display:block; margin:18px 0; text-align:right;"><a rel="external" href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->" class="btnGray">パスワードを忘れた方はこちら</a></p>
      
              <div style="margin:10px 0;">
                                             <input type="submit" value="ログイン" name="log" id="log" class="btnOrange" />
              </div>
           </div><!--▲loginarea -->
          </form>
      <!--▲login form-->
    <!--{/if}-->
    
    <hr />
    <!--▲ログインフォーム-->

    <!--{assign var=sub_total value=0}-->
    <!--{if count($cartItems) > 0}-->
    <!--{foreach from=$cartKeys item=key}-->
        <form name="form<!--{$key}-->" id="form<!--{$key}-->" method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="mode" value="confirm" />
            <input type="hidden" name="cart_no" value="" />
            <input type="hidden" name="cartKey" value="<!--{$key}-->" />
            <input type="hidden" name="category_id" value="<!--{$tpl_category_id|h}-->" />

            <!--{* 社員の購入可能数のメッセージ *}-->
            <!--{if $tpl_employee_order_msg}-->
	            <p><!--{$tpl_employee_order_msg|h}--></p>
	          <!--{/if}-->

            <!--{* カート内エラーを表示 *}-->
            <!--{if strlen($tpl_message) != 0}-->
                <p class="error"><!--{$tpl_message|h|nl2br}--></p>
            <!--{/if}-->

            <!--{* 同時購入エラーメッセージ *}-->
            <!--{if $tpl_payment_total_err}-->
	            <p class="error"><!--{$tpl_payment_total_err|h}--></p>
	        <!--{/if}-->

            <!--{* 合計金額10万円以上はエラーメッセージ *}-->
            <!--{if $tpl_dropshipment_err}-->
	            <p class="error"><!--{$tpl_dropshipment_err|h}--></p>
	        <!--{/if}-->

            <!--{* 社員専用エラーメッセージ *}-->
            <!--{foreach from=$arrEmployeeErr item=employee_err}-->
            <!--{if $employee_err|strlen > 0}-->
	            <p class="error"><!--{$employee_err|h}--></p>
	          <!--{/if}-->
            <!--{/foreach}-->

			<!--{if $arrErr.use_point}-->
			<p class="error"><!--{$arrErr.use_point}--></p>
			<!--{/if}-->

			<!--{if $tpl_order_promotion_err}-->
				<p class="error">「キ ャ ン ペ ー ン コ ー ド」に入力いただいた内容が正しくありません。</p>
			<!--{/if}-->

			<!--{if strlen($tpl_error) == 0}-->
			<p class="cartBtn">
				<input type="hidden" name="cartKey" value="<!--{$key}-->" />
				<input type="button" onclick="document.form<!--{$key}-->.submit();return false;" class="btnOrange" value="次へ(購入手続きへ)" /></a>
			</p>
			<!--{/if}-->

            <!--{assign var=tankai_flg value=0}-->
            <!--{assign var=teiki_flg value=0}-->
            <!--{foreach from=$cartItems[$key] item=item}-->
            <!--{* ▼商品情報 *}-->
            <input type="hidden" name="cart_no<!--{$item.cart_no}-->" value="<!--{$item.cart_no}-->" />
            <!--{if $item.regular_flg != $smarty.const.REGULAR_PURCHASE_FLG_ON}-->
                <!--{if $tankai_flg == 0}-->
            <h2 style="background-color:#C7B068;color:#FFF;border-radius:4px 4px 0 0;margin-top:10px;padding:13px 10px;font-weight:bold;font-size:1em;">単回購入（今回のみお届け）</h2>
			<table cellpadding="0" cellspacing="0" class="cartGoods" style="margin-top:0;">
                <!--{assign var=tankai_flg value=1}-->
                <!--{/if}-->
                <tr style="<!--{if $item.error}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->">
					<td rowspan="3" width="120">
						<img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.main_list_image|sfNoImageMainList|h}-->" alt="<!--{$item.productsClass.name|h}-->" width="65" />
						<!--{*<img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$item.productsClass.main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65" alt="<!--{$item.productsClass.name|h}-->" width="65" />*}-->
					</td>
					<td style="padding:10px 9px 0 0; width:100%;">
						<!--{* 商品名 *}-->
						<a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$item.productsClass.product_id}-->"><!--{$item.productsClass.name|h}--></a><br />
						<!--{if $item.productsClass.product_code_min == $item.productsClass.product_code_max}-->
						    <!--{$item.productsClass.product_code_min|h}-->
						<!--{else}-->
						    <!--{$item.productsClass.product_code_min|h}-->～<!--{$item.productsClass.product_code_max|h}-->
						<!--{/if}-->

						<!--{if $item.productsClass.classcategory_name1 != ""}-->
						    <!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--><br />
						<!--{/if}-->
						<!--{if $item.productsClass.classcategory_name2 != ""}-->
						    <!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}-->
						<!--{/if}-->
					</td>
				</tr>
				<tr>
					<td style="padding:15px 9px 0 0; text-align:right;">
						<!--{$item.price|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
                    </td>
				</tr>
				<tr>
					<td style="padding:5px 9px 0 0; text-align:right;vertical-align:middle;">
						<table style="width:auto;margin:0 0 0 auto;">
							<tr>
								<td style="vertical-align:middle;">
									数量:<!--{$item.quantity}-->
								</td>
								<td style="vertical-align:middle;"><a href="?" onclick="fnFormModeSubmit('form<!--{$key}-->','down','cart_no','<!--{$item.cart_no}-->'); return false"><img src="<!--{$TPL_URLPATH}-->img/button/btn_minus.jpg" width="20" height="20" alt="-" style="margin:0 1.5em;" /></a>
								</td>
								<td style="vertical-align:middle;padding-right:1em;">
									<a href="?" onclick="fnFormModeSubmit('form<!--{$key}-->','up','cart_no','<!--{$item.cart_no}-->'); return false"><img src="<!--{$TPL_URLPATH}-->img/button/btn_plus.jpg" width="20" height="20" alt="＋" style="margin:0 1.5em 0 0;" /></a>
								</td>
								<td style="vertical-align:middle;">
			                        <a href="?" onclick="fnFormModeSubmit('form<!--{$key}-->', 'delete', 'cart_no', '<!--{$item.cart_no}-->'); return false;" class="btnGray02">削除</a>
			                    </td>
		                    </tr>
	                    </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="border-bottom: dotted 1px #7f7f7f; text-align:right;">
						<!--{assign var=sub_total value=$sub_total+$item.total_inctax}-->
						<div class="money"><!--{$item.total_inctax|number_format}-->円</div>
                    </td>
                </tr>

<!--{*
                <!--{if $item.productsClass.guide_image|strlen >= 1}-->
                <tr>
                    <td colspan="2" class="promImg"><img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.guide_image|sfNoImageMainList|h}-->" width="100%" /></td>
                </tr>
                <!--{/if}-->
*}-->

            <!--{else}-->
            <!--{* ▼定期商品 *}-->
                <!--{if $teiki_flg == 0}-->
                    <!--{if $tankai_flg == 1}-->
                        <!--{assign var=tankai_flg value=2}-->
            </table>
                    <!--{/if}-->
            <h2 style="background-color:#88C442;color:#FFF;border-radius:4px 4px 0 0;margin-top:10px;padding:13px 10px;font-weight:bold;font-size:1em;">定期購入</h2>
			<table cellpadding="0" cellspacing="0" class="cartGoods" style="margin-top:0;">
                        <!--{assign var=key1 value="course_cd`$item.cart_no`"}-->
                        <!--{assign var=key2 value="todoke_cycle`$item.cart_no`"}-->
                        <!--{assign var=key3 value="todoke_week_no`$item.cart_no`"}-->
                        <!--{assign var=key4 value="todoke_week`$item.cart_no`"}-->
                        <!--{assign var=key5 value="todoke_kbn`$item.cart_no`"}-->
			<span class="attention"><!--{$arrErr[$key1]}--></span>
			<span class="attention"><!--{$arrErr[$key2]}--></span>
                        <div id="course_cd">お届け間隔
                        <input type="hidden" name="<!--{$key5}-->" id="<!--{$key5}-->" value="<!--{$arrForm[$key5]}-->" />
                            <select name="<!--{$key1}-->" id="<!--{$key1}-->" onchange="fnFormModeSubmit('form<!--{$key}-->','set_regular','cart_no','<!--{$item.cart_no}-->'); return false" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
                            <!--{html_options options=$arrCourseCd selected=$arrForm[$key1]|default:''}-->
                            </select>
                            <select name="<!--{$key2}-->" id="<!--{$key2}-->" onchange="fnFormModeSubmit('form<!--{$key}-->','set_regular','cart_no','<!--{$item.cart_no}-->'); return false" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->">
                            <!--{html_options options=$arrTodokeKbn selected=$arrForm[$key2]|default:''}-->
                            </select>
                        </div>

                     <!--{assign var=teiki_flg value=1}-->
                <!--{/if}-->
                <tr style="<!--{if $item.error}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->">
					<td rowspan="4" width="120">
                        <img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.main_list_image|sfNoImageMainList|h}-->" alt="<!--{$item.productsClass.name|h}-->" width="65" />
                        <!--{*<img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$item.productsClass.main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65" alt="<!--{$item.productsClass.name|h}-->" width="65" />*}-->
                    </td>
					<td style="padding:10px 9px 0 0; width:100%;">
						<!--{* 商品名 *}-->
                        <a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$item.productsClass.product_id}-->"><!--{$item.productsClass.name|h}--></a><br />
                        <!--{if $item.productsClass.product_code_min == $item.productsClass.product_code_max}-->
                            <!--{$item.productsClass.product_code_min|h}-->
                        <!--{else}-->
                            <!--{$item.productsClass.product_code_min|h}-->～<!--{$item.productsClass.product_code_max|h}-->
                        <!--{/if}-->

                        <!--{if $item.productsClass.classcategory_name1 != ""}-->
                            <!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--><br />
                        <!--{/if}-->
                        <!--{if $item.productsClass.classcategory_name2 != ""}-->
                            <!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}-->
                        <!--{/if}-->
					</td>
				</tr>
				<tr>
					<td style="padding:15px 9px 0 0; text-align:right;">
                        価格：<!--{$item.price|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
                    </td>
				</tr>
				<tr>
				    <td>
				<!--{*
					<td style="padding:5px 9px 0 0; text-align:left;">
                        <!--{assign var=key1 value="course_cd`$item.cart_no`"}-->
                        <!--{assign var=key2 value="todoke_cycle`$item.cart_no`"}-->
                        <!--{assign var=key3 value="todoke_week_no`$item.cart_no`"}-->
                        <!--{assign var=key4 value="todoke_week`$item.cart_no`"}-->
                        <!--{assign var=key5 value="todoke_kbn`$item.cart_no`"}-->
					    <span class="attention"><!--{$arrErr[$key1]}--></span>
					    <span class="attention"><!--{$arrErr[$key2]}--></span>
                        <div id="course_cd">お届け間隔
                        <input type="hidden" name="<!--{$key5}-->" id="<!--{$key5}-->" value="<!--{$arrForm[$key5]}-->" />
                            <select name="<!--{$key1}-->" id="<!--{$key1}-->" onchange="fnFormModeSubmit('form<!--{$key}-->','set_regular','cart_no','<!--{$item.cart_no}-->'); return false" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
                            <!--{html_options options=$arrCourseCd selected=$arrForm[$key1]|default:''}-->
                            </select>
                            <select name="<!--{$key2}-->" id="<!--{$key2}-->" onchange="fnFormModeSubmit('form<!--{$key}-->','set_regular','cart_no','<!--{$item.cart_no}-->'); return false" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->">
                            <!--{html_options options=$arrTodokeKbn selected=$arrForm[$key2]|default:''}-->
                            </select>
                        </div>
					    <span class="attention"><!--{$arrErr[$key3]}--></span>
					    <span class="attention"><!--{$arrErr[$key4]}--></span>
                        <div id="<!--{$key4}-->_select">お届け曜日
                            <select name="<!--{$key3}-->" id="<!--{$key3}-->" onchange="fnFormModeSubmit('form<!--{$key}-->','set_regular','cart_no','<!--{$item.cart_no}-->'); return false" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->">
                            <option></option>
                            <!--{html_options options=$arrTodokeWeekNo selected=$arrForm[$key3]|default:''}-->
                            </select>
                            <select name="<!--{$key4}-->" id="<!--{$key4}-->" onchange="fnFormModeSubmit('form<!--{$key}-->','set_regular','cart_no','<!--{$item.cart_no}-->'); return false" style="<!--{$arrErr[$key4]|sfGetErrorColor}-->">
                            <option></option>
                            <!--{html_options options=$arrTodokeWeek selected=$arrForm[$key4]|default:''}-->
                            </select>
                        </div>
				*}-->
                    </td>
				</tr>
				<tr>
					<td style="padding:5px 9px 0 0; text-align:right;">
                    <!--{$item.quantity}--><a href="?" onclick="fnFormModeSubmit('form<!--{$key}-->','down','cart_no','<!--{$item.cart_no}-->'); return false"><img src="<!--{$TPL_URLPATH}-->img/button/btn_minus.jpg" width="16" height="16" alt="-" /></a>
                    <a href="?" onclick="fnFormModeSubmit('form<!--{$key}-->','up','cart_no','<!--{$item.cart_no}-->'); return false"><img src="<!--{$TPL_URLPATH}-->img/button/btn_plus.jpg" width="16" height="16" alt="＋" /></a>
                    <!--{assign var=sub_total value=$sub_total+$item.total_inctax}-->
                    <a href="?" onclick="fnFormModeSubmit('form<!--{$key}-->', 'delete', 'cart_no', '<!--{$item.cart_no}-->'); return false;" class="btnGray02">削除</a>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="border-bottom: dotted 1px #7f7f7f; text-align:right;">
                    <div class="money"><!--{$item.total_inctax|number_format}-->円</div>
                    </td>
                </tr>

<!--{*
                <!--{if $item.productsClass.guide_image_teiki|strlen >= 1}-->
                <tr>
                    <td colspan="2" class="promImg"><img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.guide_image_teiki|sfNoImageMainList|h}-->" width="710" /></td>
                </tr>
                <!--{/if}-->
*}-->

            <!--{* ▲定期商品 *}-->
            <!--{/if}-->
            <!--{/foreach}-->

            <!--{if $teiki_flg == 1 || $tankai_flg == 1}-->
            </table>
            <!--{/if}-->

            <!--{* ▼定期専用のメッセージ表示 *}-->
            <!--{if $tpl_regular_purchase_flg === true}-->
            <h3 class="subtitle">定期購入に関する注意事項</h3>
            <div class="cart" style="margin:20px 0 0">
                    <p>
                        お届け間隔は、「日ごと」「ヶ月ごと」の指定をしていただけます。（お届け間隔は最大3ヶ月または90日まで）<br />
                        お届け間隔で「1ヶ月ごと」「2ヶ月ごと」「3ヶ月ごと」のいずれかを選択した場合のみ、お届け曜日のご指定が可能です。<br />
                        定期購入は、原則として最低3回以上の継続をお願いします。
                    </p>
            </div>
            <!--{/if}-->
            <!--{* ▲定期専用のメッセージ表示 *}-->

            <!--{* ▲商品情報 *}-->
			<table cellpadding="0" cellspacing="0" class="cartGoods02">
				<tr>
					<td colspan="2" style="background-color:#FFF;padding:15px 9px;color:#464646; font-weight:bold; vertical-align:middle; text-align:center;">
						小計
						<span style="font-size:1.125em;color:#ff8f00;font-weight:bold;"><!--{$sub_total|number_format}-->円</span>
						＋
						送料
						<span style="font-size:1.125em;color:#ff8f00;font-weight:bold;"><!--{$arrData[$key].deliv_fee|number_format}-->円</span>
					</td>
				</tr>
				<tr>
					<td style="background-color:#FFF; padding:0 0 0 9px; font-size:0.75em; color:#464646; font-weight:bold; vertical-align:middle;">
ご使用ポイント</td>
<td style="background-color:#FFF; padding:15px 9px 15px 0; font-size:0.75em; color:#464646; font-weight:bold; text-align:right">
						<!--{assign var=key1 value="use_point"}-->
						<input type="text" size="5" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->"/>
						pt / <!--{$tpl_user_point|number_format}-->pt
					</td>
				</tr>

<!--{if $tpl_customer_kbn != $smarty.const.CUSTOMER_KBN_EMPLOYEE}-->
				<tr>
					<td style="background-color:#FFF; padding:0 0 0 9px; font-size:0.75em; color:#464646; font-weight:bold; vertical-align:middle;">
キャンペーンコード</td>
<td style="background-color:#FFF; padding:15px 9px 15px 0; font-size:1.125em; color:#ff8f00; font-weight:bold; text-align:right">
		<input type="text" size="15" name="campaign_code" value="<!--{$tpl_campaign_code}-->" maxlength="<!--{$smarty.const.CAMPAIGN_CODE_LEN}-->" />
					</td>
				</tr>
<!--{/if}-->
			</table>

			<!--{if $tpl_customer_kbn != $smarty.const.CUSTOMER_KBN_EMPLOYEE}-->
			<p><a href="?" onclick="fnFormModeSubmit('form<!--{$key}-->','re_calc','cart_no','<!--{$item.cart_no}-->'); return false" class="btn">再計算する</a></p>
			<!--{/if}-->

<!--{if $tpl_input_campaign_ok_flg}-->キャンペーンコードが適用されました<!--{/if}-->
			<table cellpadding="0" cellspacing="0" class="cartGoods02">
				<tr>
					<td style="padding:0 0 0 9px; font-size:0.75em; color:#464646; font-weight:bold; vertical-align:middle;">

						お支払いの合計
					</td>
					<td style="padding:15px 9px 15px 0; font-size:1.125em; color:#ff8f00; font-weight:bold; text-align:right">
						<!--{$arrData[$key].total|number_format}-->円
			(税込)</span>
					</td>
				</tr>
			</table>

            <!--{if strlen($tpl_error) == 0}-->
			<p class="cartBtn">
				<input type="hidden" name="cartKey" value="<!--{$key}-->" />
				<input type="button" onclick="document.form<!--{$key}-->.submit();return false;" class="btnOrange" value="次へ(購入手続きへ)" />
			</p>
			<!--{/if}-->

        </form>
    <!--{/foreach}-->
    <!--{else}-->
        <p class="empty"><span class="attention">※ 現在カート内に商品はございません。</span></p>
    <!--{/if}-->

<p style="text-align:center; margin:20px 0 60px;">
	<a class="btnGray02" href="<!--{$tpl_prev_url|h}-->">買い物を続ける</a>
</p>

</section>
<!--▲CONTENTS-->

