<!--▼CONTENTS-->
<script type="text/javascript">//<![CDATA[
    $(function() {
        if ($('input[name=deliv_id]:checked').val()
            || $('#deliv_id').val()) {
            showForm(true);
        } else {
            showForm(false);
        }
        $('input[id^=deliv_]').click(function() {
            showForm(true);
            var data = {};
            data.mode = 'select_deliv';
            data.deliv_id = $(this).val();
            data['<!--{$smarty.const.TRANSACTION_ID_NAME}-->'] = '<!--{$transactionid}-->';
            $.ajax({
                type : 'POST',
                url : location.pathname,
                data: data,
                cache : false,
                dataType : 'json',
                error : remoteException,
                success : function(data, dataType) {
                    if (data.error) {
                        remoteException();
                    } else {
                        // 支払い方法の行を生成
                        var payment_tbody = $('#payment tbody');
                        payment_tbody.empty();
                        for (var i in data.arrPayment) {
                            // ラジオボタン 
                            <!--{* IE7未満対応のため name と id をベタ書きする *}-->
                            var radio = $('<input type="radio" name="payment_id" id="pay_' + i + '" />')
                                .val(data.arrPayment[i].payment_id);
                            // ラベル
                            var label = $('<label />')
                                .attr('for', 'pay_' + i)
                                .text(data.arrPayment[i].payment_method);
                            // 行
                            var tr = $('<tr />')
                                .append($('<td />')
                                    .addClass('alignC')
                                    .append(radio))
                                .append($('<td />').append(label));

                            // 支払方法の画像が登録されている場合は表示
                            if (data.img_show) {
                                var payment_image = data.arrPayment[i].payment_image;
                                $('th#payment_method').attr('colspan', 3);
                                if (payment_image) {
                                    var img = $('<img />').attr('src', '<!--{$smarty.const.IMAGE_SAVE_URLPATH}-->' + payment_image);
                                    tr.append($('<td />').append(img));
                                } else {
                                    tr.append($('<td />'));
                                }
                            } else {
                                $('th#payment_method').attr('colspan', 2);
                            }

                            tr.appendTo(payment_tbody);
                        }
                        // お届け時間を生成
                        var deliv_time_id_select = $('select[id^=deliv_time_id]');
                        deliv_time_id_select.empty();
                        deliv_time_id_select.append($('<option />').text('指定なし').val(''));
                        for (var i in data.arrDelivTime) {
                            var option = $('<option />')
                                .val(i)
                                .text(data.arrDelivTime[i])
                                .appendTo(deliv_time_id_select);
                        }

                        //#43対応
                        // 画面の高さを再計算する
                        heightLine();
                    }
                }
            });
        });

        /**
         * 通信エラー表示.
         */
        function remoteException(XMLHttpRequest, textStatus, errorThrown) {
            alert('通信中にエラーが発生しました。カート画面に移動します。');
            location.href = '<!--{$smarty.const.CART_URLPATH}-->';
        }

        /**
         * 配送方法の選択状態により表示を切り替える
         */
        function showForm(show) {
            if (show) {
                $('#payment, div.delivdate, .select-msg').show();
                $('.non-select-msg').hide();
            } else {
                $('#payment, div.delivdate, .select-msg').hide();
                $('.non-select-msg').show();
            }
        }

        // 支払方法選択時のアクション
        $( 'input[name="payment_id"]:radio' ).change( function() {
            if ($( this ).val() == '<!--{$smarty.const.PAYMENT_ID_DAIBIKI}-->') {
                // 「代金引換」を選択時は、「配達時のご要望」を空にする
                $('#box_flg0').val('');

                // 請求書(明細書)の送付を別送不可にする。
                $('.inc_kbn_<!--{$smarty.const.INCLUDE_KBN_BESSOU}-->').css('display','none');
                // 同梱にチェックを入れる。
                $('#radio_inc_kbn_<!--{$smarty.const.INCLUDE_KBN_DOUKON}-->').attr('checked',true);
            }
            if ($( this ).val() != '<!--{$smarty.const.PAYMENT_ID_DAIBIKI}-->') {
                // 「請求書(明細書)の送付」を別送可能にする。
                $('.inc_kbn_<!--{$smarty.const.INCLUDE_KBN_BESSOU}-->').css('display','');
            }
        });

    });
				// 請求書送付の背景色変更
				function haikeiC(objId,color){
					document.getElementById(objId).style.backgroundColor=color;
				}


//]]>
</script>
<div id="undercolumn">
    <div id="undercolumn_shopping">
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
						<td>
							<!--{$item.productsClass.name|h}-->
						</td>
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
						<td>
							<!--{$item.product_name|h}-->
						</td>
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

        <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="confirm" />
        <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />

<div class="wrapCoan">
		<!--{if $tpl_login}-->
        <div class="wrapCoanEle">
			<h3 class="order">お客様情報</h3>
			<!--{*
            <h4 class="order"><img src="<!--{$TPL_URLPATH}-->img/rohto/check.gif" alt="必須" />お支払方法をご選択ください。</h4>
            <p class="non-select-msg">まずはじめに、配送方法を選択ください。</p>
            <!--{assign var=key value="payment_id"}-->
            <!--{if $arrErr[$key] != ""}-->
            <p class="attention"><!--{$arrErr[$key]}--></p>
            <!--{/if}-->
			*}-->
            <table summary="お客様情報" id="customer" class="tblOrder">
                <colgroup width="10%"></colgroup>
                <colgroup width="90%"></colgroup>
                <tbody>
                    <tr>
                        <th><span>漢字氏名</span></th>
                        <td class="alignC"><!--{$tpl_arrCustomer.name|h}--> 様</td>
                    </tr>
                    <tr>
                        <th><span>電話番号</span></th>
                        <td class="alignC"><!--{$tpl_arrCustomer.tel|h}--></td>
                    </tr>
                    <tr>
                        <th><span>住所</span></th>
                        <td class="alignC">〒<!--{$tpl_arrCustomer.zip|h}--> <!--{$arrPref[$tpl_arrCustomer.pref]|h}--><!--{$tpl_arrCustomer.addr01|h}--><!--{$tpl_arrCustomer.addr02|h}--></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="wrapCoanEle">
			<h3 class="order">お届け先</h3>

			<input type="hidden" name="other_deliv_id" value="" />
			<!--{if $arrErr.deli != ""}-->
				<p class="attention"><!--{$arrErr.deli}--></p>
			<!--{/if}-->
			<table summary="お届け先の指定" class="tblOrder">
				<colgroup width="5%"></colgroup>
				<colgroup width="15%"></colgroup>
				<colgroup width="15%"></colgroup>
				<colgroup width="50%"></colgroup>
				<colgroup width="15%"></colgroup>
				<!--{section name=cnt loop=$arrAddr}-->
				<tr>
					<td class="alignC">
						<!--{if $smarty.section.cnt.first}-->
							<input type="radio" name="deliv_check" id="chk_id_<!--{$smarty.section.cnt.iteration}-->" value="-1" <!--{if $arrForm.deliv_check.value == "" || $arrForm.deliv_check.value == -1}--> checked="checked"<!--{/if}--> />
						<!--{else}-->
							<input type="radio" name="deliv_check" id="chk_id_<!--{$smarty.section.cnt.iteration}-->" value="<!--{$arrAddr[cnt].other_deliv_id}-->"<!--{if $arrForm.deliv_check.value == $arrAddr[cnt].other_deliv_id}--> checked="checked"<!--{/if}--> />
						<!--{/if}-->
					</td>
					<td class="alignC">
						<label for="chk_id_<!--{$smarty.section.cnt.iteration}-->">
						<!--{$arrAddr[cnt].name|h}-->
						</label>
					</td>
					<td class="alignC">
						<label for="chk_id_<!--{$smarty.section.cnt.iteration}-->">
						〒<!--{$arrAddr[cnt].zip|h}-->
						</label>
					</td>
					<td>
						<!--{assign var=key value=$arrAddr[cnt].pref}-->
						<!--{$arrPref[$key]}--><!--{$arrAddr[cnt].addr01|h}--><!--{$arrAddr[cnt].addr02|h}-->
					</td>
					<td class="alignC">
						<!--{if !$smarty.section.cnt.first}-->
							<a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php" onclick="win02('<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php?page=<!--{$smarty.server.PHP_SELF|h}-->&amp;other_deliv_id=<!--{$arrAddr[cnt].other_deliv_id}-->','new_deiv','730','680'); return false;">配送先情報修正</a>
							<!--{else}-->
								<a href="<!--{$smarty.const.MYPAGE_CHANGE_URLPATH}-->">登録情報修正</a>
							<!--{/if}-->
					</td>
				</tr>
				<!--{/section}-->
			</table>
			<!--{if $tpl_addrmax < $smarty.const.DELIV_ADDR_MAX}-->
			<p class="addbtn">
				<a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php" onclick="win02('<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php?page=<!--{$smarty.server.PHP_SELF|h}-->','new_deiv','730','680'); return false;" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_add_address_on.jpg','addition');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_add_address.jpg','addition');"><img src="<!--{$TPL_URLPATH}-->img/button/btn_add_address.jpg" alt="新しいお届け先を追加する" name="addition" id="addition" /></a>
			</p>
			<!--{/if}-->

        </div>
		<!--{/if}-->

        <h3 class="order">お届け方法</h3>
        <!--{assign var=key value="deliv_id"}-->
        <!--{if $is_single_deliv}-->
            <input type="hidden" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" id="deliv_id" />
        <!--{else}-->
		<div class="wrapCoanEle">
            <h4 class="order"><img src="<!--{$TPL_URLPATH}-->img/rohto/check.gif" alt="必須" />配送方法をご選択ください。</h4>

            <!--{if $arrErr[$key] != ""}-->
            <p class="attention"><!--{$arrErr[$key]}--></p>
            <!--{/if}-->
            <table class="tblOrder" summary="配送方法選択">
                <colgroup width="10%"></colgroup>
                <colgroup width="90%"></colgroup>
                <tr>
                    <th><span>選択</span></th>
                    <th><span>配送方法</span></th>
                </tr>
                <!--{section name=cnt loop=$arrDeliv}-->
                <tr>
                    <td class="alignC"><input type="radio" id="deliv_<!--{$smarty.section.cnt.iteration}-->" name="<!--{$key}-->" value="<!--{$arrDeliv[cnt].deliv_id}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" <!--{$arrDeliv[cnt].deliv_id|sfGetChecked:$arrForm[$key].value}--> />
                    </td>
                    <td>
                        <label for="deliv_<!--{$smarty.section.cnt.iteration}-->"><!--{$arrDeliv[cnt].name|h}--><!--{if $arrDeliv[cnt].remark != ""}--><p><!--{$arrDeliv[cnt].remark|h|nl2br}--></p><!--{/if}--></label>
                    </td>
                </tr>
                <!--{/section}-->
            </table>
        </div>
        <!--{/if}-->

        <!--{if $cartKey != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
        <div class="wrapCoanEle">
        <!--{if $mail_deliv_flg === false}--><!--{* ▼メール便判定 *}-->
            <h4 class="order"><img src="<!--{$TPL_URLPATH}-->img/rohto/option.gif" alt="任意" />お届け希望日時、その他ご要望をお選びください。</h4>
            <p class="non-select-msg">まずはじめに、配送方法を選択ください。</p>
            <!--{foreach item=shippingItem name=shippingItem from=$arrShipping}-->
            <!--{assign var=index value=$shippingItem.shipping_id}-->
            <div class="delivdate top">
				<table class="tblOrder">
					<!--{if $is_multiple}-->
					<tr>
						<td colspan="2">
							<span class="st">▼<!--{$shippingItem.shipping_name}-->
							<!--{$arrPref[$shippingItem.shipping_pref]}--><!--{$shippingItem.shipping_addr01}--><!--{$shippingItem.shipping_addr02}--></span><br/>
						</td>
					</tr>
					<!--{/if}-->
					<tr>
						<th><span>時間帯指定</span></th>
						<td>
							<!--★お届け時間★-->
							<!--{assign var=key value="deliv_time_id`$index`"}-->
							<span class="attention"><!--{$arrErr[$key]}--></span>
							<select name="<!--{$key}-->" id="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
								<option value="" selected="">指定なし</option>
								<!--{assign var=shipping_time_value value=$arrForm[$key].value|default:$shippingItem.time_id}-->
								<!--{html_options options=$arrDelivTime selected=$shipping_time_value}-->
							</select>
						</td>
					</tr>
					<!--★お届け日★-->
					<!--{assign var=key value="deliv_date`$index`"}-->
					<tr>
						<th><span>お届け日</span></th>
						<td>
							<span class="attention"><!--{$arrErr[$key]}--></span>
							<!--{if !$arrDelivDate}-->
								ご指定頂けません。
							<!--{else}-->
								<select name="<!--{$key}-->" id="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
									<!--{if !$tpl_regular_flg}-->
									<option value="" selected="">指定なし</option>
									<!--{/if}-->
									<!--{assign var=shipping_date_value value=$arrForm[$key].value|default:$shippingItem.shipping_date}-->
									<!--{html_options options=$arrDelivDate selected=$shipping_date_value}-->
								</select>&nbsp;
							<!--{/if}-->
						</td>
					</tr>
                    <tr>
						<th><span>配達時のご要望</span></th>
						<td>
							<!--{assign var=key value="box_flg`$index`"}-->
                            <!--{if $tpl_is_cool === false }-->
							<select name="<!--{$key}-->" id="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
								<option value="" selected=""></option>
								<!--{assign var=box_flg value=$arrForm[$key].value|default:$shippingItem.box_flg}-->
								<!--{html_options options=$arrBoxFlg selected=$box_flg}-->
							</select>
                            <!--{else}-->
								冷蔵・冷凍便のため、ご指定頂けません。
                            <!--{/if}-->
                            <br />
							<span class="attention"><!--{$arrErr[$key]}--></span><br />
                            <span class="attention">※冷蔵・冷凍便を含む場合は、宅配BOXへのお届けは出来ません。</span>
						</td>
                    </tr>
                    <tr>
						<th><span>配送の種類</span></th>
                        <td>宅配便</td>

                    </tr>
				</table>
            </div>
            <!--{/foreach}-->
        </div>
        <!--{else}-->
            <p>配送の種類 メール便　　※郵便受けへのお届けにつき、配達日時のご指定は承れません。</p>
        <!--{/if}--><!--{* ▲メール便判定 *}-->

        <!--{/if}-->

        <div class="wrapCoanEle">
			<h3 class="order">お支払方法</h3>
            <h4 class="order"><img src="<!--{$TPL_URLPATH}-->img/rohto/check.gif" alt="必須" />お支払方法をご選択ください。</h4>
            <p class="non-select-msg">まずはじめに、配送方法を選択ください。</p>
            <!--{assign var=key value="payment_id"}-->
            <!--{if $arrErr[$key] != ""}-->
            <p class="attention"><!--{$arrErr[$key]}--></p>
            <!--{/if}-->
            <table summary="お支払方法選択" id="payment" class="tblOrder">
                <colgroup width="10%"></colgroup>
                <colgroup width="90%"></colgroup>
                <thead>
                    <tr>
                        <th><span>選択</span></th>
                        <th<!--{if $img_show}--> colspan="2"<!--{/if}--> id="payment_method"><span>お支払方法</span></th>
                    </tr>
                </thead>
                <tbody>
                    <!--{section name=cnt loop=$arrPayment}-->
                        <tr>
                        <td class="alignC"><input type="radio" id="pay_<!--{$smarty.section.cnt.iteration}-->" name="<!--{$key}-->"  value="<!--{$arrPayment[cnt].payment_id}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" <!--{$arrPayment[cnt].payment_id|sfGetChecked:$arrForm[$key].value}--> /></td>
                        <td>
                            <label for="pay_<!--{$smarty.section.cnt.iteration}-->"><!--{$arrPayment[cnt].payment_method|h}--><!--{if $arrPayment[cnt].note != ""}--><!--{/if}--></label>
                        </td>
                        <!--{if $img_show}-->
                            <td>
                                <!--{if $arrPayment[cnt].payment_image != ""}-->
                                    <img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrPayment[cnt].payment_image}-->" />
                                <!--{/if}-->
                            </td>
                        <!--{/if}-->
                        </tr>
                    <!--{/section}-->
                </tbody>
            </table>

            <h4 class="order"><img src="<!--{$TPL_URLPATH}-->img/rohto/check.gif" alt="必須" />請求書(明細書)のご送付方法をご指定ください。</h4>
            <!--{assign var=key value="include_kbn"}-->
            <!--{if $arrErr[$key] != ""}-->
            <p class="attention"><!--{$arrErr[$key]}--></p>
            <!--{/if}-->

            <table summary="請求書(明細書)の送付" id="enquete" class="tblOrder">
				<tr>
					<th class="btm"><span>請求書(明細書)の送付</span></th>
					<td>
						<!--{foreach from=$arrIncludeKbn item=str_include_kbn key=idx}-->
						    <label for="bill_spec<!--{$idx}-->">
						    <input type="radio" id="bill_spec<!--{$idx}-->" name="<!--{$key}-->"  value="<!--{$idx}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" <!--{$idx|sfGetChecked:$arrForm[$key].value}--> onclick="haikeiC('seikyuTyp<!--{$idx+1}-->','#fcefeb'),haikeiC('seikyuTyp<!--{if $idx == 0}-->2<!--{else}-->1<!--{/if}-->','#ffffff')" /><span><!--{$str_include_kbn}--></span>
						    </label>
						<!--{/foreach}-->
					</td>
				</tr>
			</table>
        </div>

        <!--{if $tpl_campaign_code|default:''|strlen == 0 && $customer_kbn != $smarty.const.CUSTOMER_KBN_EMPLOYEE}-->
        <div class="wrapCoanEle">
			    <h3 class="order">アンケート</h3>
            <h4 class="order"><img src="<!--{$TPL_URLPATH}-->img/rohto/check.gif" alt="必須" />アンケートにご協力ください。</h4>
            <!--{assign var=key value="event_code"}-->
            <!--{if $arrErr[$key] != ""}-->
            <p class="attention"><!--{$arrErr[$key]}--></p>
            <!--{/if}-->
            <table summary="アンケート選択" id="enquete" class="tblOrder">
                <colgroup width="100%"></colgroup>
                <thead>
                    <tr>
                        <th><span>今回お買い求めいただいたきっかけをお聞かせください。</span></th>
                    </tr>
                </thead>
                <tbody>
                        <tr>
                        <td class="alignC">
                        <!--{foreach from=$arrPlanningData item=arrRecord key=idx}-->
                        <input type="radio" id="event_code_<!--{$idx}-->" name="<!--{$key}-->"  value="<!--{$arrRecord.media_code}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" <!--{$arrRecord.media_code|sfGetChecked:$arrForm[$key].value}--> /><label for="event_code_<!--{$idx}-->"><!--{$arrRecord.planning_name|h}--></label>
                        <!--{/foreach}-->
                        </td>
                        </tr>
                </tbody>
            </table>
        </div>
        <!--{/if}-->

		<div class="wrapCoan">
			<div class="orderBtn">
				<p class="left">
					<span class="f-right" style="width:600px;float:right;text-align:right;">
					<a href="javascript:void(0);" onclick="document.form1.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_kakunin.gif" alt="ご注文情報の確認ページへ進む" name="next" id="next" class="swp" /></a>
					</span>
				<a href="<!--{$tpl_back_url|h}-->">
				<img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_back.gif" alt="戻る" border="0" name="back03" id="back03" /></a>
				</p>
			</div>
		</div>

</div>

        </form>
    </div>
</div>
<!--▲CONTENTS-->
<!--{$tpl_clickAnalyzer}-->
