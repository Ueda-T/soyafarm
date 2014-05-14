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
//]]></script>

<!--▼CONTENTS-->

<div id="undercolumn">
    <div id="undercolumn_cart">
<!--{*        <h2 class="title"><!--{$tpl_title|h}--></h2>*}-->

<!--{*
    <!--{if $smarty.const.USE_POINT !== false || count($arrProductsClass) > 0}-->
        <!--★ポイント案内★-->
        <!--{if $smarty.const.USE_POINT !== false}-->
            <div class="point_announce">
                <!--{if $tpl_login}-->
                    <span class="user_name"><!--{$tpl_name|h}--> 様</span>の、現在の所持ポイントは「<span class="point"><!--{$tpl_user_point|number_format|default:0}--> pt</span><!--{if $tpl_user_point_valid_date neq ""}-->(<!--{$tpl_user_point_valid_date|date_format:"%Y/%m/%d"}-->まで有効)<!--{/if}-->」、<br>お誕生日ポイントは「<span class="point"><!--{$tpl_user_birth_point|number_format|default:0}--> pt</span><!--{if $tpl_user_birth_point_valid_date neq ""}-->(<!--{$tpl_user_birth_point_valid_date|date_format:"%Y/%m/%d"}-->まで有効)<!--{/if}-->」です。<br />
                <!--{else}-->
                    <p>ロート製薬 オンラインショップからのご注文の場合は、WEBで会員登録いただいた後、ポイントサービス付与の対象となります。</p>
                <!--{/if}-->
            </div>
        <!--{/if}-->
    <!--{/if}-->
*}-->

    <p class="totalmoney_area">
        <!--{* カゴの中に商品がある場合にのみ表示 *}-->
        <!--{if count($cartKeys) > 1}-->
            <span class="attentionSt"><!--{foreach from=$cartKeys item=key name=cartKey}--><!--{$arrProductType[$key]}--><!--{if !$smarty.foreach.cartKey.last}-->、<!--{/if}--><!--{/foreach}-->は同時購入できません。<br />
                        お手数ですが、個別に購入手続きをお願い致します。
            </span>
        <!--{/if}-->

        <!--{if strlen($tpl_error) != 0}-->
            <p class="attention"><!--{$tpl_error|h}--></p>
        <!--{/if}-->

    </p>

    <!--{assign var=sub_total value=0}-->
    <!--{if count($cartItems) > 0}-->
    <!--{foreach from=$cartKeys item=key}-->
    <div class="cartList">
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
				<a href="javascript:void(0);" onclick="document.form<!--{$key}-->.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/rohto/cart_next.gif" alt="ご注文主様・お届け情報の入力" name="confirm" class="swp" /></a>
			</p>
			<!--{/if}-->

            <!--{assign var=tankai_flg value=0}-->
            <!--{assign var=teiki_flg value=0}-->
            <!--{foreach from=$cartItems[$key] item=item}-->
            <!--{* ▼商品情報 *}-->
            <input type="hidden" name="cart_no<!--{$item.cart_no}-->" value="<!--{$item.cart_no}-->" />
            <!--{if $item.regular_flg != $smarty.const.REGULAR_PURCHASE_FLG_ON}-->
                <!--{if $tankai_flg == 0}-->
            <h1 class="midashi01"><img src="<!--{$TPL_URLPATH}-->img/rohto/method_midashi_tankai.gif" alt="単回購入（今回のみお届け）" /></h1>
            <table class="cartListTankaiTbl" summary="単回商品">
                <tr>
                    <th class="alignC" colspan="2">商品名</th>
                    <th class="num">数量<!--{*<br /><span>変更する場合は数字を入力してください</span>*}--></th>
                    <th class="price" nowrap>小計<span class="dyn">(税込)</span></th>
                    <th class="deleteBtn">取消</th>
                </tr>
                <!--{assign var=tankai_flg value=1}-->
                <!--{/if}-->
                <tr style="<!--{if $item.error}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->">

                    <td class="alignC">
                        <!--{*<a class="expansion" target="_blank"
                        <!--{if $item.productsClass.main_image|strlen >= 1}--> href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.main_image|sfNoImageMainList|h}-->"
                        <!--{/if}-->
                        >*}-->
                        <a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$item.productsClass.product_id}-->">
                        <img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.main_list_image|sfNoImageMainList|h}-->" alt="<!--{$item.productsClass.name|h}-->" width="65" />
                        <!--{*<img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$item.productsClass.main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65" alt="<!--{$item.productsClass.name|h}-->" width="65" />*}-->
                        </a>
                    </td>
                    <td><!--{* 商品名 *}-->
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
                        <br />
                        価格：<!--{$item.price|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
                    </td>
                    <td class="alignC"><!--{$item.quantity}-->
                        <ul id="quantity_level">
                            <li><a href="?" onclick="fnFormModeSubmit('form<!--{$key}-->','down','cart_no','<!--{$item.cart_no}-->'); return false"><img src="<!--{$TPL_URLPATH}-->img/button/btn_minus.jpg" width="16" height="16" alt="-" /></a></li>
                            <li><a href="?" onclick="fnFormModeSubmit('form<!--{$key}-->','up','cart_no','<!--{$item.cart_no}-->'); return false"><img src="<!--{$TPL_URLPATH}-->img/button/btn_plus.jpg" width="16" height="16" alt="＋" /></a></li>
                        </ul>
                    </td>
                    <!--{assign var=sub_total value=$sub_total+$item.total_inctax}-->
                    <td class="alignR"><!--{$item.total_inctax|number_format}-->円</td>
                    <td class="alignC"><a href="?" onclick="fnFormModeSubmit('form<!--{$key}-->', 'delete', 'cart_no', '<!--{$item.cart_no}-->'); return false;"><img src="<!--{$TPL_URLPATH}-->img/rohto/delete.gif" alt="取消" /></a>
                    </td>
                </tr>

                <!--{if $item.productsClass.guide_image|strlen >= 1}-->
                <tr>
                    <td colspan="6" class="promImg"><img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.guide_image|sfNoImageMainList|h}-->" width="710" /></td>
                </tr>
                <!--{/if}-->

            <!--{else}-->
            <!--{* ▼定期商品 *}-->
                <!--{if $teiki_flg == 0}-->
                    <!--{if $tankai_flg == 1}-->
                        <!--{assign var=tankai_flg value=2}-->
            </table>
                    <!--{/if}-->
            <h1 class="midashi01"><img src="<!--{$TPL_URLPATH}-->img/rohto/method_midashi_teiki.gif" alt="定期購入" /></h1>
            <table class="cartListTeikiTbl" summary="定期商品">
                <tr>
                    <th class="alignC" colspan="2">商品名</th>
                    <th class="alignC" >お届け間隔</th>
                    <th class="num">数量<!--{*<br /><span>変更する場合は数字を入力してください</span>*}--></th>
                    <th class="price" nowrap>小計<span class="dyn">(税込)</span></th>
                    <th class="deleteBtn">取消</th>
                </tr>
                    <!--{assign var=teiki_flg value=1}-->
                <!--{/if}-->
                <tr style="<!--{if $item.error}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->">

                    <td class="alignC">
                        <!--{*<a class="expansion" target="_blank"
                        <!--{if $item.productsClass.main_image|strlen >= 1}--> href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.main_image|sfNoImageMainList|h}-->"
                        <!--{/if}-->
                        >*}-->
                        <a href="<!--{$smarty.const.P_DETAIL_URLPATH}--><!--{$item.productsClass.product_id}-->">
                        <img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.main_list_image|sfNoImageMainList|h}-->" alt="<!--{$item.productsClass.name|h}-->" width="65" />
                        <!--{*<img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$item.productsClass.main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65" alt="<!--{$item.productsClass.name|h}-->" width="65" />*}-->
                        </a>
                    </td>
                    <td><!--{* 商品名 *}-->
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
                        <br />
                        価格：<!--{$item.price|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
                    </td>
                    <td>
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
                    </td>
                    <td class="alignC"><!--{$item.quantity}-->
                        <ul id="quantity_level">
                            <li><a href="?" onclick="fnFormModeSubmit('form<!--{$key}-->','down','cart_no','<!--{$item.cart_no}-->'); return false"><img src="<!--{$TPL_URLPATH}-->img/button/btn_minus.jpg" width="16" height="16" alt="-" /></a></li>
                            <li><a href="?" onclick="fnFormModeSubmit('form<!--{$key}-->','up','cart_no','<!--{$item.cart_no}-->'); return false"><img src="<!--{$TPL_URLPATH}-->img/button/btn_plus.jpg" width="16" height="16" alt="＋" /></a></li>
                        </ul>
                    </td>
                    <!--{assign var=sub_total value=$sub_total+$item.total_inctax}-->
                    <td class="alignR"><!--{$item.total_inctax|number_format}-->円</td>
                    <td class="alignC"><a href="?" onclick="fnFormModeSubmit('form<!--{$key}-->', 'delete', 'cart_no', '<!--{$item.cart_no}-->'); return false;"><img src="<!--{$TPL_URLPATH}-->img/rohto/delete.gif" alt="取消" /></a>
                    </td>
                </tr>

                <!--{if $item.productsClass.guide_image_teiki|strlen >= 1}-->
                <tr>
                    <td colspan="6" class="promImg"><img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.guide_image_teiki|sfNoImageMainList|h}-->" width="710" /></td>
                </tr>
                <!--{/if}-->
            <!--{* ▲定期商品 *}-->
            <!--{/if}-->
            <!--{/foreach}-->
            <!--{if $teiki_flg == 1 || $tankai_flg == 1}-->
            </table>
            <!--{/if}-->

            <!--{* ▼定期専用のメッセージ表示 *}-->
            <!--{if $tpl_regular_purchase_flg === true}-->
            <div class="teikiAttentionBox clearfix">
                <div class="left">
                    <img src="<!--{$TPL_URLPATH}-->img/rohto/tit_teiki_attention.gif" />
                </div>
                <div class="right">
                    <p>
                        お届け間隔は、「日ごと」「ヶ月ごと」の指定をしていただけます。（お届け間隔は最大3ヶ月または90日まで）<br />
                        お届け間隔で「1ヶ月ごと」「2ヶ月ごと」「3ヶ月ごと」のいずれかを選択した場合のみ、お届け曜日のご指定が可能です。<br />
                        定期購入は、原則として最低3回以上の継続をお願いします。
                    </p>
                </div>
            </div>
            <!--{/if}-->
            <!--{* ▲定期専用のメッセージ表示 *}-->

            <!--{* ▲商品情報 *}-->
<div class="cartTotalBox">
	<div class="inner">
		<p class="subtotal">
			小計
			<strong><!--{$sub_total|number_format}-->円
			＋</strong>
			
			送料
			<strong><!--{$arrData[$key].deliv_fee|number_format}-->円</strong>
		</p>
		<ul class="ptInput">
			<li>
			ご使用ポイント
			<!--{assign var=key1 value="use_point"}-->
			<input type="text" size="10" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->"/>
			pt / <!--{$tpl_user_point|number_format}-->pt
			</li>

      <!--{if $tpl_customer_kbn != $smarty.const.CUSTOMER_KBN_EMPLOYEE}-->
			<li class="last">
			キャンペーンコード
			<input type="text" size="15" name="campaign_code" value="<!--{$tpl_campaign_code}-->" maxlength="<!--{$smarty.const.CAMPAIGN_CODE_LEN}-->" />
			</li>
      <!--{/if}-->
		</ul>
	</div>
	<div class="campaignCode">
		<div class="inner">
			<p class="codeApp"><!--{if $tpl_input_campaign_ok_flg}-->キャンペーンコードが適用されました<!--{/if}--></p>
			<p>
        <!--{if $tpl_customer_kbn != $smarty.const.CUSTOMER_KBN_EMPLOYEE}-->
				<a href="?" onclick="fnFormModeSubmit('form<!--{$key}-->','re_calc','cart_no','<!--{$item.cart_no}-->'); return false">
				<img src="<!--{$TPL_URLPATH}-->img/rohto/recalculate.gif" alt="再計算する" width="97" /></a>
			  <!--{/if}-->
				お支払いの合計
				<span class="price"><strong><!--{$arrData[$key].total|number_format}-->円</strong>
				(税込)</span>
			</p>
		</div>
	</div>
</div>

                <!--{if strlen($tpl_error) == 0}-->
				<p class="cartBtn">
					<input type="hidden" name="cartKey" value="<!--{$key}-->" />
					<a href="javascript:void(0);" onclick="document.form<!--{$key}-->.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/rohto/cart_next.gif" alt="ご注文主様・お届け情報の入力" name="confirm" class="swp" /></a>
				</p>
				<!--{/if}-->

        </form>
        </div>
    <!--{/foreach}-->
    <!--{else}-->
        <p class="cartNoData">現在、お買い物カゴには商品が入っておりません。</p>
    <!--{/if}-->
    </div>
</div>
<!--▲CONTENTS-->

<!--{$tpl_clickAnalyzer}-->
