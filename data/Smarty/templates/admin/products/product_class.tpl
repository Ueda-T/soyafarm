<script type="text/javascript">//<![CDATA[
$(function() {
    // 無制限チェックボックスの初期化
    $('input[id^=chk_stock_unlimited_]').each(function() {
        var index = $(this).attr('id').replace(/^chk_stock_unlimited_/ig, '');
        var checked = $(this).attr('checked');

        if (checked) {
            $('#stock_' + index)
                .attr('readonly', true)
                .css('background-color', '<!--{$smarty.const.DISABLED_RGB}-->');
        }
    });

    // 無制限チェックボックス
    $('input[id^=chk_stock_unlimited_]').change(function() {
        var index = $(this).attr('id').replace(/^chk_stock_unlimited_/ig, '');
        var checked = $('#chk_stock_unlimited_' + index + ":checked").val();
        if (checked) {
            $('#stock_' + index)
                .attr('readonly', true)
                .val('')
                .css('background-color', '<!--{$smarty.const.DISABLED_RGB}-->');
        } else {
            $('#stock_' + index)
                .attr('readonly', false)
                .css('background-color', '');
        }
    });

    // 1行目をコピーボタン
    $('#copy_from_first').click(function() {

        var check = $('#check_0:checked').val();
        $('input[id^=check_]').val([check]);

        var product_code = $('#product_code_0').val();
        $('input[id^=product_code_]').val(product_code);

        var stock = $('#stock_0').val();
        $('input[id^=stock_][class=box6]').val(stock);

        var stock_unlimited = $("#chk_stock_unlimited_0:checked").val();
        $('input[id^=chk_stock_unlimited_]').each(function() {
            var checked = stock_unlimited;
            var index = $(this).attr('id').replace(/^chk_stock_unlimited_/ig, '');
            if (checked) {
                $('#stock_' + index)
                    .attr('readonly', true)
                    .css('background-color', '<!--{$smarty.const.DISABLED_RGB}-->');
                $('#chk_stock_unlimited_' + index)
                    .prop('checked', true);
            } else {
                $('#stock_' + index)
                    .attr('readonly', false)
                    .css('background-color', '');
                $('#chk_stock_unlimited_' + index)
                    .prop('checked', false);
            }
        });

        var price01 = $('#price01_0').val();
        $('input[id^=price01_]').val(price01);

        var price02 = $('#price02_0').val();
        $('input[id^=price02_]').val(price02);

        var product_type_id_value = '';
        $('input[id^=product_type_id_0_]').each(function() {
            if ($(this).attr('checked')) {
                product_type_id_value = $(this).val();
            }
        });

        $('input[id^=product_type_id_]').each(function() {
            if ($(this).val() == product_type_id_value) {
                $(this).attr('checked', true);
            }
        });

        var down_filename = $('#down_filename_0').val();
        $('input[id^=down_filename_]').val(down_filename);
    });
});
//]]>
</script>
<div id="products" class="contents-main">
<h2>商品規格登録</h2>
<form name="form1" id="form1" method="post" action="" enctype="multipart/form-data">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<!--{foreach key=key item=item from=$arrSearchHidden}-->
    <!--{if is_array($item)}-->
        <!--{foreach item=c_item from=$item}-->
        <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
        <!--{/foreach}-->
    <!--{else}-->
        <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
    <!--{/if}-->
<!--{/foreach}-->
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="product_id" value="<!--{$arrForm.product_id.value|h}-->" />
<input type="hidden" name="upload_index" value="">
<input type="hidden" name="total" value="<!--{$arrForm.total.value|h}-->" />

    <table>
        <tr>
            <th>商品名</th>
            <td><!--{$arrForm.product_name.value|h}--></td>
        </tr>
        <tr>
            <th>規格<!--{* span class="attention">*</span *}--></th>
            <td>
                <!--{assign var=key value="class_id1"}-->
                <!--{if $arrErr[$key]}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{/if}-->

                <select name="<!--{$key}-->">
                    <option value="">選択してください</option>
                    <!--{html_options options=$arrClass selected=$arrForm[$key].value}-->
                </select>
            </td>
        </tr>
<!--{*
        <tr>
            <th>規格2</th>
            <td>
                <!--{assign var=key value="class_id2"}-->
                <!--{if $arrErr[$key]}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{/if}-->
                <select name="<!--{$key}-->">
                    <option value="">選択してください</option>
                    <!--{html_options options=$arrClass selected=$arrForm[$key].value}-->
                </select>
            </td>
        </tr>
*}-->
    </table>
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('<!--{$smarty.const.ADMIN_PRODUCTS_URLPATH}-->'); fnModeSubmit('search','',''); return false;" ><span class="btn-prev">検索結果へ戻る</span></a></li>
            <li><a class="btn-action" href="javascript:;" onclick="fnModeSubmit('disp','',''); return false;"><span class="btn-next">表示する</span></a></li>
        <!--{if $arrForm.total.value > 0}-->
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <li><a class="btn-action" href="javascript:;" onclick="fnModeSubmit('delete','',''); return false;"><span class="btn-next">削除する</span></a></li>
            <!--{/if}-->
        <!--{/if}-->
        </ul>
    </div>

    <!--{if $arrForm.total.value > 0}-->

    <!--{foreach item=item name=i from=$arrClassCat}-->
        <!--{if $smarty.foreach.i.first}-->
            <!--{assign var=cnt value=$smarty.foreach.i.total}-->
        <!--{/if}-->
    <!--{/foreach}-->

    <div class="list-info clearfix">
        <div class="btn"><a class="btn-normal" href="javascript:;" id="copy_from_first"><span>一行目のデータをコピーする</span></a></div>
    </div>

    <!--{if $arrErr.check_empty}-->
        <span class="attention"><!--{$arrErr.check_empty}--></span>
    <!--{/if}-->

    <table class="list">
        <colgroup width="5%">
        <colgroup width="10%">
        <colgroup width="9%">
        <colgroup width="20%">
        <colgroup width="10%">
        <colgroup width="10%">
        <tr>
            <th><input type="checkbox" onclick="fnAllCheck('#allCheck', 'input[name^=check]')" id="allCheck" /> <label for="allCheck"><br>登録</label></th>
            <th>規格<!--{* br>(<!--{$arrClass[$class_id1]|default:"未選択"|h}-->) *}--></th>
            <th>商品コード</th>
            <th>在庫数<!--{* span class="attention">*</span *}--></th>
            <th><!--{$smarty.const.NORMAL_PRICE_TITLE}-->(税込)<!--{* span class="attention">*</span *}--></th>
            <th><!--{$smarty.const.SALE_PRICE_TITLE}-->(税込)<!--{* span class="attention">*</span *}--></th>
        </tr>
        <!--{section name=cnt loop=$arrForm.total.value}-->
            <!--{assign var=index value=$smarty.section.cnt.index}-->

            <tr>
                <td class="center">
                    <!--{assign var=key value="classcategory_id1"}-->
                    <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                    <!--{assign var=key value="classcategory_id2"}-->
                    <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                    <!--{assign var=key value="product_class_id"}-->
                    <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                    <!--{assign var=key value="check"}-->
                    <!--{if $arrErr[$key][$index]}-->
                        <span class="attention"><!--{$arrErr[$key][$index]}--></span>
                    <!--{/if}-->
                    <input type="checkbox" name="<!--{$key}-->[<!--{$index}-->]" value="1" <!--{if $arrForm[$key].value[$index] == 1}-->checked="checked"<!--{/if}--> id="<!--{$key}-->_<!--{$index}-->" />
                </td>
                <td class="center">
                    <!--{assign var=key value="classcategory_name1"}-->
                    <!--{if $arrErr[$key][$index]}-->
                        <span class="attention"><!--{$arrErr[$key][$index]}--></span>
                    <!--{/if}-->
                    <!--{$arrForm[$key].value[$index]|h}-->
                    <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                </td>
                <!--{* td class="center">
                    <!--{assign var=key value="classcategory_name2"}-->
                    <!--{if $arrErr[$key][$index]}-->
                        <span class="attention"><!--{$arrErr[$key][$index]}--></span>
                    <!--{/if}-->
                    <!--{$arrForm[$key].value[$index]|h}-->
                    <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                </td *}-->
                <td class="center">
                    <!--{assign var=key value="product_code"}-->
                    <!--{if $arrErr[$key][$index]}-->
                        <span class="attention"><!--{$arrErr[$key][$index]}--></span>
                    <!--{/if}-->
                    <input type="text" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" size="14" class="box7" maxlength="<!--{$arrForm[$key].length}-->" <!--{if $arrErr[$key][$index] != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> id="<!--{$key}-->_<!--{$index}-->" />
                </td>
                <td class="center">
                    <!--{assign var=key value="stock"}-->
                    <!--{if $arrErr[$key][$index]}-->
                        <span class="attention"><!--{$arrErr[$key][$index]}--></span>
                    <!--{/if}-->
                     <input type="text" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" size=14" class="box7" maxlength="<!--{$arrForm[$key].length}-->" <!--{if $arrErr[$key][$index] != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> id="<!--{$key}-->_<!--{$index}-->" />
                    <!--{assign var=key value="stock_unlimited"}-->
                    <!--{if $arrErr[$key][$index]}-->
                        <span class="attention"><!--{$arrErr[$key][$index]}--></span>
                    <!--{/if}-->
                    <input type="checkbox" name="<!--{$key}-->[<!--{$index}-->]" value="1" <!--{if $arrForm[$key].value[$index] == "1"}-->checked="checked"<!--{/if}--> id="chk_<!--{$key}-->_<!--{$index}-->" /><label for="chk_<!--{$key}-->_<!--{$index}-->">無制限</label>
                </td>
                <td class="center">
                    <!--{assign var=key value="price01"}-->
                    <!--{if $arrErr[$key][$index]}-->
                        <span class="attention"><!--{$arrErr[$key][$index]}--></span>
                    <!--{/if}-->
                    <input type="text" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" size="14" class="box7" maxlength="<!--{$arrForm[$key].length}-->" <!--{if $arrErr[$key][$index] != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> id="<!--{$key}-->_<!--{$index}-->" />円
                </td>
                <td class="center">
                    <!--{assign var=key value="price02"}-->
                    <!--{if $arrErr[$key][$index]}-->
                        <span class="attention"><!--{$arrErr[$key][$index]}--></span>
                    <!--{/if}-->
                    <input type="text" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" size="16" class="box7" maxlength="<!--{$arrForm[$key].length}-->" <!--{if $arrErr[$key][$index] != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> id="<!--{$key}-->_<!--{$index}-->" />円
                </td>
                <!--{* 商品種別は常に「通常商品」*}-->
                <!--{assign var=key value="product_type_id"}-->
                <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$smarty.const.PRODUCT_TYPE_NORMAL}-->" />
            </tr>
        <!--{/section}-->
    </table>

    <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'edit', '', ''); return false;"><span class="btn-next">確認ページへ</span></a></li>
        </ul>
    </div>
    <!--{/if}-->

    <!--{/if}-->

</form>
</div>
