<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/facebox.js"></script>
<link rel="stylesheet" type="text/css" href="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/facebox.css" media="screen" />
<script type="text/javascript">//<![CDATA[
var send = true;

function fnCheckSubmit() {
    if(send) {
        send = false;
        document.form1.submit();
        return true;
    } else {
        alert("只今、処理中です。しばらくお待ち下さい。");
        return false;
    }
}

$(document).ready(function() {
    $('a.expansion').facebox({
        loadingImage : '<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/loading.gif',
        closeImage   : '<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.facebox/closelabel.png'
    });
});
//]]></script>

<!--CONTENTS-->
<section id="undercolumn">
<h2 class="spNaked"><img src="<!--{$TPL_URLPATH}-->img/rohto/icon_cart.gif" width="23" height="16">お買い物カゴ<span>4 / 4</span></h2>
		<div class="estimate">ご注文はまだ完了していません。</div>
		<p class="naked">
			以下のご注文内容をご確認の上、画面下の「<!--{if $use_module}-->次へ<!--{else}-->注文する<!--{/if}-->」ボタンをクリックしてください。
		</p>

        <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="<!--{if $chk_torihiki_id}-->check_confirm<!--{else}-->confirm<!--{/if}-->" />
        <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />

		<div class="bdrGray">

		<h3 style="margin:0 0 7px;">ご購入商品</h3>

<div>
        <table summary="ご注文内容確認" class="bgYellow">
            <!--{foreach from=$arrCartItems item=item}-->
                <tr>
                    <td colspan="2">
                        <ul>
                            <li><strong><!--{$item.productsClass.name|h}--></strong></li>
                            <!--{if $item.productsClass.classcategory_name1 != ""}-->
                            <li><!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--></li>
                            <!--{/if}-->
                            <!--{if $item.productsClass.classcategory_name2 != ""}-->
                            <li><!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}--></li>
                            <!--{/if}-->
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        数量:<!--{$item.quantity|number_format}-->　<!--{$item.price|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円</td>
                </tr>
            <!--{/foreach}-->
            <!--{* 同梱品情報 *}-->
            <!--{if $tpl_include_product_flg}-->
            <!--{foreach from=$arrIncludeProduct item=item}-->
                <tr>
                    <td colspan="2">
                        <ul>
                            <li>プレゼント商品</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <ul>
                            <li><strong><!--{$item.product_name|h}--></strong></li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        数量:<!--{$item.quantity|number_format}--></td>
                </tr>
            <!--{/foreach}-->
            <!--{/if}-->
            <tr>
                <th style="text-align:right;">小計</th>
                <td style="text-align:right;"><!--{$tpl_total_inctax[$cartKey]|number_format}-->円</td>
            </tr>
            <!--{if $smarty.const.USE_POINT !== false}-->
                <tr>
                    <th style="text-align:right;">ポイント値引き</th>
                    <td style="text-align:right;">
                        <!--{assign var=discount value=`$arrForm.use_point*$smarty.const.POINT_VALUE`}-->
                        -<!--{$discount|number_format|default:0}-->円</td>
                </tr>
            <!--{/if}-->
            <tr>
                <th style="text-align:right;">送料</th>
                <td style="text-align:right;"><!--{$arrForm.deliv_fee|number_format}-->円</td>
            </tr>
			<!--{* 使用していない為、コメントアウト
            <tr>
                <th colspan="3" class="alignR" scope="row">手数料</th>
                <td style="text-align:right;"><!--{$arrForm.charge|number_format}-->円</td>
            </tr>
			*}-->
            <tr>
                <th style="text-align:right;">合計</th>
                <td style="text-align:right;"><span class="price"><!--{$arrForm.payment_total|number_format}-->円</span></td>
            </tr>
        </table>
</div>

		</div>
		<div class="bdrGray">

        <!--お届け先ここから-->
        <!--{* 販売方法判定（ダウンロード販売のみの場合はお届け先を表示しない） *}-->
        <!--{if $cartKey != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
        <!--{foreach item=shippingItem from=$arrShipping name=shippingItem}-->
        <h3 style="margin:20px 0 7px;">お届け先<!--{if $is_multiple}--><br /><!--{$smarty.foreach.shippingItem.iteration}--><!--{/if}--></h3>
        <!--{if $is_multiple}-->
            <table summary="ご注文内容確認" class="bgYellow">
                <tr>
                    <th scope="col">商品写真</th>
                    <!--{* XXX 購入小計と誤差が出るためコメントアウト
                    <th scope="col">小計</th>
                    *}-->
                </tr>
                <!--{foreach item=item from=$shippingItem.shipment_item}-->
                    <tr>
                        <td class="alignC">
                            <a
                                <!--{if $item.productsClass.main_image|strlen >= 1}--> href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.main_image|sfNoImageMainList|h}-->" class="expansion" target="_blank"
                                <!--{/if}-->
                            >
                                <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$item.productsClass.main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65" alt="<!--{$item.productsClass.name|h}-->" /></a>
                        </td>
                    </tr>
                    <tr>
                    <th scope="col">単価</th>
                    </tr>
                    <tr>
                        <td><!--{* 商品名 *}--><strong><!--{$item.productsClass.name|h}--></strong><br />
                            <!--{if $item.productsClass.classcategory_name1 != ""}-->
                                <!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--><br />
                            <!--{/if}-->
                            <!--{if $item.productsClass.classcategory_name2 != ""}-->
                                <!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}-->
                            <!--{/if}-->
                        </td>
                    </tr>
                    <tr>
                    <th scope="col">数量</th>
                    </tr>
                    <tr>
                        <td class="alignR">
                            <!--{$item.price|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
                        </td>
                    </tr>
                    <tr>
                        <td class="alignC" id="quantity"><!--{$item.quantity}--></td>
                        <!--{* XXX 購入小計と誤差が出るためコメントアウト
                        <td class="alignR"><!--{$item.total_inctax|number_format}-->円</td>
                        *}-->
                    </tr>
                <!--{/foreach}-->
            </table>
        <!--{/if}-->

		<div>
        <table summary="お届け先確認" class="bgYellow">
            <tbody>
                <tr>
                    <th scope="row"><span>お届け先：漢字氏名</span></th>
                </tr>
                <tr>
                    <td><!--{$shippingItem.shipping_name|h}--> 様</td>
                </tr>
                <tr>
                    <th scope="row"><span>お届け先：ｶﾀｶﾅ氏名</span></th>
                </tr>
                <tr>
                    <td><!--{$shippingItem.shipping_kana|h}--></td>
                </tr>
                <tr>
                    <th scope="row"><span>お届け先：電話番号</span></th>
                </tr>
                <tr>
                    <td><!--{$shippingItem.shipping_tel}--></td>
                </tr>
                <tr>
                    <th scope="row"><span>お届け先：住所</span></th>
                </tr>
                <tr>
                    <td>〒<!--{$shippingItem.shipping_zip|h}--><br />
                    <!--{$arrPref[$shippingItem.shipping_pref]}--><!--{$shippingItem.shipping_addr01|h}--><!--{$shippingItem.shipping_addr02|h}--></td>
                </tr>
            </tbody>
        </table>
		</div>
        <!--{/foreach}-->
        <!--{/if}-->
        <!--お届け先ここまで-->

		</div>
		<div class="bdrGray">

		<h3 style="margin:20px 0 7px;">配送情報</h3>

		<div>
        <table summary="配送方法" class="bgYellow">
            <tr>
                <th scope="row"><span>配送方法</span></th>
                <td><!--{if $mail_deliv_flg}-->メール便<!--{else}-->宅配便<!--{/if}--></td>
            </tr>
            <!--{if $cartKey != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
            <tr>
                <th scope="row"><span>お届け日</span></th>
                <td><!--{$shippingItem.shipping_date|default:"指定なし"|h}--></td>
            </tr>
            <tr>
                <th scope="row"><span>お届け時間</span></th>
                <td><!--{$shippingItem.shipping_time|default:"指定なし"|h}--></td>
            </tr>
            <tr>
                <th scope="row"><span>配達時のご要望</span></th>
                <td><!--{$arrBoxFlg[$shippingItem.box_flg]|default:"指定なし"|h}--></td>
            </tr>
            <!--{/if}-->
        </table>
        </div>

		</div>
		<div class="bdrGray">
		<h3 style="margin:20px 0 7px;">お支払方法</h3>

		<div>
        <table summary="お支払方法" class="bgYellow">
            <tr>
                <th scope="row"><span>お支払方法</span></th>
            </tr>
            <tr>
                <td><!--{$arrForm.payment_method|h}--></td>
            </tr>
            <tr>
                <th scope="row"><span>請求書送付方法</span></th>
            </tr>
            <tr>
                <td><!--{$arrIncludeKbn[$arrForm.include_kbn]|h}--></td>
            </tr>
        </table>
		</div>

		</div>

<!--{*
		<h3 class="order"><img src="<!--{$TPL_URLPATH}-->img/rohto/method_midashi11.gif" width="820" height="35" alt="その他お問い合わせ" /></h3>
        <table summary="お支払方法" class="bgYellow">
            <tr>
                <th scope="row">その他お問い合わせ</th>
                <td><!--{$arrForm.message|h|nl2br}--></td>
            </tr>
        </table>
*}-->

		<p style="margin:10px auto;">
			<!--{if $use_module}-->
				<a href="javascript:void(0);" onclick="return fnCheckSubmit();"class="btnOrange" style="width:auto;text-decoration:none;">次へ</a>
			<!--{else}-->
				<a href="javascript:void(0);" onclick="return fnCheckSubmit();" class="btnOrange" style="width:auto;text-decoration:none;">注文する</a>
			<!--{/if}-->
		</p>
		<p style="margin:10px auto 20px auto;">
			<a href="./payment.php" class="btnGray02">戻る</a>
		</p>

        </form>
</section>
<!--▲CONTENTS-->
