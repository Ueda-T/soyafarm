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
<div id="undercolumn">
    <div id="undercolumn_shopping">
		<h1><img src="<!--{$TPL_URLPATH}-->img/soyafarm/order_title_step2.gif" alt="購入手続き：ご注文情報確認"></h1>

		<p class="intro">ご注文はまだ完了していません。<br>
			以下のご注文内容をご確認の上、画面下の「<!--{if $use_module}-->次へ<!--{else}-->注文する<!--{/if}-->」ボタンをクリックしてください。
		</p>

        <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="<!--{if $chk_torihiki_id}-->check_confirm<!--{else}-->confirm<!--{/if}-->" />
        <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />

		<div class="wrapCoan">
			<div class="orderBtn">
				<p class="left">
					<span class="f-right" style="width:600px;float:right;text-align:right;">
				<!--{if $use_module}-->
					<a href="javascript:void(0);" onclick="return fnCheckSubmit();"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_next.gif" alt="次へ" name="next" id="next" class="swp" /></a>
				<!--{else}-->
					<a href="javascript:void(0);" onclick="return fnCheckSubmit();"><img src="<!--{$TPL_URLPATH}-->img/rohto/btn_buy.gif" alt="注文する"  name="next" id="next" class="swp" /></a>
				<!--{/if}-->
					</span>
				<a href="./payment.php"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_back.gif" alt="戻る" name="back<!--{$key}-->" /></a>
				</p>
			</div>
		</div>

		<h3 class="cartListTitle"><img src="<!--{$TPL_URLPATH}-->img/rohto/method_cart_title.gif" width="820" height="31" alt="ご注文商品"></h3>

        <table summary="ご注文内容確認" class="cartListTankaiTbl">
            <colgroup width="10%"></colgroup>
            <colgroup width="40%"></colgroup>
            <colgroup width="20%"></colgroup>
            <colgroup width="10%"></colgroup>
            <colgroup width="20%"></colgroup>
            <tr>
                <th scope="col">商品写真</th>
                <th scope="col">商品名</th>
                <th scope="col">単価</th>
                <th scope="col">数量</th>
                <th scope="col">小計</th>
            </tr>
            <!--{foreach from=$arrCartItems item=item}-->
                <tr>
                    <td class="alignC">
                        <a
                            <!--{if $item.productsClass.main_image|strlen >= 1}--> href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$item.productsClass.main_image|sfNoImageMainList|h}-->" class="expansion" target="_blank"
                            <!--{/if}-->
                        >
                            <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$item.productsClass.main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65" alt="<!--{$item.productsClass.name|h}-->" /></a>
                    </td>
                    <td>
                        <ul>
                            <li><!--{$item.productsClass.name|h}--></li>
                            <!--{if $item.productsClass.classcategory_name1 != ""}-->
                            <li><!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--></li>
                            <!--{/if}-->
                            <!--{if $item.productsClass.classcategory_name2 != ""}-->
                            <li><!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}--></li>
                            <!--{/if}-->
                        </ul>
                    </td>
                    <td class="alignR">
                        <!--{$item.price|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
                    </td>
                    <td class="alignR"><!--{$item.quantity|number_format}--></td>
                    <td class="alignR"><!--{$item.total_inctax|number_format}-->円</td>
                </tr>
            <!--{/foreach}-->
            <!--{* 同梱品情報 *}-->
            <!--{if $tpl_include_product_flg}-->
            <!--{foreach from=$arrIncludeProduct item=item}-->
                <tr>
                    <td class="alignC">
                            <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$item.main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65" alt="<!--{$item.product_name|h}-->" /></a>
                    </td>
                    <td>
                        <ul>
                            <li>プレゼント商品</li>
                            <li><!--{$item.product_name|h}--></li>
                        </ul>
                    </td>
                    <td class="alignR">&nbsp;</td>
                    <td class="alignR"><!--{$item.quantity|number_format}--></td>
                    <td class="alignR">&nbsp;</td>
                </tr>
            <!--{/foreach}-->
            <!--{/if}-->
            <tr>
                <th colspan="4" class="alignR" scope="row">小計</th>
                <td class="alignR"><!--{$tpl_total_inctax[$cartKey]|number_format}-->円</td>
            </tr>
            <!--{if $smarty.const.USE_POINT !== false}-->
                <tr>
                    <th colspan="4" class="alignR" scope="row">値引き（ポイントご使用時）</th>
                    <td class="alignR">
                        <!--{assign var=discount value=`$arrForm.use_point*$smarty.const.POINT_VALUE`}-->
                        -<!--{$discount|number_format|default:0}-->円</td>
                </tr>
            <!--{/if}-->
            <tr>
                <th colspan="4" class="alignR" scope="row">送料</th>
                <td class="alignR"><!--{$arrForm.deliv_fee|number_format}-->円</td>
            </tr>
			<!--{* 使用していない為、コメントアウト
            <tr>
                <th colspan="4" class="alignR" scope="row">手数料</th>
                <td class="alignR"><!--{$arrForm.charge|number_format}-->円</td>
            </tr>
			*}-->
            <tr>
                <th colspan="4" class="alignR" scope="row">合計</th>
                <td class="alignR"><span class="price"><!--{$arrForm.payment_total|number_format}-->円</span></td>
            </tr>
        </table>

<div class="wrapCoan">

        <!--お届け先ここから-->
        <!--{* 販売方法判定（ダウンロード販売のみの場合はお届け先を表示しない） *}-->
        <!--{if $cartKey != $smarty.const.PRODUCT_TYPE_DOWNLOAD}-->
        <!--{foreach item=shippingItem from=$arrShipping name=shippingItem}-->
        <h3 class="order">お届け先<!--{if $is_multiple}--><br /><!--{$smarty.foreach.shippingItem.iteration}--><!--{/if}--></h3>
        <!--{if $is_multiple}-->
            <table summary="ご注文内容確認" class="tblOrder">
                <tr>
                    <th scope="col">商品写真</th>
                    <th scope="col">商品名</th>
                    <th scope="col">単価</th>
                    <th scope="col">数量</th>
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
                        <td><!--{* 商品名 *}--><!--{$item.productsClass.name|h}--><br />
                            <!--{if $item.productsClass.classcategory_name1 != ""}-->
                                <!--{$item.productsClass.class_name1}-->：<!--{$item.productsClass.classcategory_name1}--><br />
                            <!--{/if}-->
                            <!--{if $item.productsClass.classcategory_name2 != ""}-->
                                <!--{$item.productsClass.class_name2}-->：<!--{$item.productsClass.classcategory_name2}-->
                            <!--{/if}-->
                        </td>
                        <td class="alignR">
                            <!--{$item.price|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
                        </td>
                        <td class="alignC" id="quantity"><!--{$item.quantity}--></td>
                        <!--{* XXX 購入小計と誤差が出るためコメントアウト
                        <td class="alignR"><!--{$item.total_inctax|number_format}-->円</td>
                        *}-->
                    </tr>
                <!--{/foreach}-->
            </table>
        <!--{/if}-->

        <table summary="お届け先確認" class="tblOrder">
            <tbody>
                <tr>
                    <th scope="row"><span>お届け先：漢字氏名</span></th>
                    <td><!--{$shippingItem.shipping_name|h}-->
                </tr>
                <tr>
                    <th scope="row"><span>お届け先：ｶﾀｶﾅ氏名</span></th>
                    <td><!--{$shippingItem.shipping_kana|h}-->
                </tr>
                <tr>
                    <th scope="row"><span>お届け先：電話番号</span></th>
                    <td><!--{$shippingItem.shipping_tel}-->
                </tr>
                <tr>
                    <th scope="row"><span>お届け先：住所</span></th>
                    <td>〒<!--{$shippingItem.shipping_zip|h}-->
                    <!--{$arrPref[$shippingItem.shipping_pref]}--><!--{$shippingItem.shipping_addr01|h}--><!--{$shippingItem.shipping_addr02|h}--></td>
                </tr>
            </tbody>
        </table>
        <!--{/foreach}-->
        <!--{/if}-->
        <!--お届け先ここまで-->

		<h3 class="order">お届け方法</h3>
        <table summary="配送方法" class="tblOrder">
            <tbody>
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

		<h3 class="order">お支払方法</h3>
        <table summary="お支払方法" class="tblOrder">
            <tr>
                <th scope="row"><span>お支払方法</span></th>
                <td><!--{$arrForm.payment_method|h}--></td>
            </tr>
            <tr>
                <th scope="row"><span>請求書送付方法</span></th>
                <td><!--{$arrIncludeKbn[$arrForm.include_kbn]|h}--></td>
            </tr>
        </table>
<!--{*
		<h3 class="order">その他お問い合わせ</h3>
        <table summary="お支払方法" class="tblOrder">
            <tr>
                <th scope="row">その他お問い合わせ</th>
                <td><!--{$arrForm.message|h|nl2br}--></td>
            </tr>
        </table>
*}-->

</div>

		<div class="wrapCoan">
			<div class="orderBtn">
				<p class="left">
					<span class="f-right" style="width:600px;float:right;text-align:right;">
				<!--{if $use_module}-->
					<a href="javascript:void(0);" onclick="return fnCheckSubmit();"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_next.gif" alt="次へ" name="next" id="next" class="swp" /></a>
				<!--{else}-->
					<a href="javascript:void(0);" onclick="return fnCheckSubmit();"><img src="<!--{$TPL_URLPATH}-->img/rohto/btn_buy.gif" alt="注文する"  name="next" id="next" class="swp" /></a>
				<!--{/if}-->
					</span>
				<a href="./payment.php"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_back.gif" alt="戻る" name="back<!--{$key}-->" /></a>
				</p>
			</div>
		</div>

        </form>
    </div>
</div>
<!--▲CONTENTS-->
