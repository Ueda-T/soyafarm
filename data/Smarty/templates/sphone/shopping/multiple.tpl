<!--▼コンテンツここから -->
<script type="text/javascript">//<![CDATA[
function fnChangeSplitNum() {
    $('#form1').find('input[type="hidden"][name="mode"]').val('');
    $('#form1').submit();
    return true;
}
//]]>
</script>
<section id="undercolumn">

<h2 class="title"><!--{$tpl_title|h}--></h2>

<!--★インフォメーション★-->
<div class="information end">
 <p>各商品のお届け先を選択してください。</p>
 <p>※数量はカゴの中の数量と合わせてください。</p>
</div>

<!--★ボタン★-->
<!--{if $tpl_addrmax < $smarty.const.DELIV_ADDR_MAX}-->
<div class="btn_area_top">
  <a rel="external" href="javascript:void(0);" class="btn_sub addbtn" onclick="win02('<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php?page=<!--{$smarty.server.PHP_SELF|h}-->','new_deiv','600','640'); return false;">新しいお届け先を追加</a>
</div>
<!--{/if}-->

<!--▼フォームここから -->
<div class="form_area">
    <form name="form1" id="form1" method="post" action="<!--{$smarty.const.ROOT_URLPATH}-->shopping/multiple.php">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />
            <input type="hidden" name="line_of_num" value="<!--{$arrForm.line_of_num.value}-->" />
            <input type="hidden" name="mode" value="confirm" />

<select id="split_num" name="split_num" onChange="fnChangeSplitNum();">
    <option value="0">商品1個ごとに分割</option>
    <!--{foreach from=$arrSplitSel key="num" item="disp"}-->
    <option value="<!--{$num|h}-->" <!--{if $num == $arrForm.split_num.value}-->selected="selected"<!--{/if}-->><!--{$disp|h}-->分割</option>
    <!--{/foreach}-->
</select>

<!--{section name=line loop=$arrForm.line_of_num.value}-->
            <!--{assign var=index value=$smarty.section.line.index}-->
<!--{assign var=key value="quantity"}-->
<!--{if $arrErr[$key][$index] != ''}-->
    <span class="attention"><!--{$arrErr[$key][$index]}--></span>
<!--{/if}-->
<div class="formBox">
<!--▼商品 -->
<div class="delivitemBox">
<img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arrForm.main_list_image.value[$index]|sfNoImageMainList|h}-->&amp;width=80&amp;height=80" alt="&lt;!--{$arrForm.name[$index]|h}--&gt;" class="photoL" />
<div class="delivContents">

  <p><em><!--{$arrForm.name.value[$index]|h}--></em><br />
    <!--{if $arrForm.classcategory_name1.value[$index] != ""}-->
        <span class="mini"><!--{$arrForm.class_name1.value[$index]|h}-->：<!--{$arrForm.classcategory_name1.value[$index]|h}--></span><br />
    <!--{/if}-->
    <!--{if $arrForm.classcategory_name2.value[$index] != ""}-->
        <span class="mini"><!--{$arrForm.class_name2.value[$index]|h}-->：<!--{$arrForm.classcategory_name2.value[$index]|h}--></span><br />
    <!--{/if}-->
    <!--{$arrForm.price.value[$index]|sfCalcIncTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->円
    </p>
<ul>
<li class="result"><span class="mini">数量</li>
 <li>
      <input type="number" name="<!--{$key}-->[<!--{$index}-->]" class="cartin_quantity txt" value="<!--{$arrForm[$key].value[$index]|h}-->" max="9" style="" />
 </li>
  </ul>
</div>

</div>
 <!--▲商品 -->
<div class="btn_area_btm">

                        <input type="hidden" name="cart_no[<!--{$index}-->]" value="<!--{$index}-->" />
                          <!--{assign var=key value="product_class_id"}-->
                          <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                          <!--{assign var=key value="name"}-->
                          <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                          <!--{assign var=key value="class_name1"}-->
                          <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                          <!--{assign var=key value="class_name2"}-->
                          <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                          <!--{assign var=key value="classcategory_name1"}-->
                          <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                          <!--{assign var=key value="classcategory_name2"}-->
                          <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                          <!--{assign var=key value="main_image"}-->
                          <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                          <!--{assign var=key value="main_list_image"}-->
                          <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                          <!--{assign var=key value="price"}-->
                          <input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
                        <!--{assign var=key value="shipping"}-->
                        <select name="<!--{$key}-->[<!--{$index}-->]" class="boxLong data-role-none"><!--{html_options options=$addrs selected=$arrForm[$key].value[$index]}--></select>
                        <!--{if strlen($arrErr[$key][$index]) >= 1}-->
                        <div class="attention"><!--{$arrErr[$key][$index]}--></div>
                        <!--{/if}-->
</div>

</div><!--▲formBox -->
<!--{/section}-->

<ul class="btn_btm">
 <li><a rel="external" href="javascript:void(document.form1.submit());" class="btn">選択したお届け先に送る</a></li>
 <li><a rel="external" href="<!--{$smarty.const.CART_URLPATH}-->" class="btn_back">戻る</a></li>
</ul>

</form>

</div><!--▲form_area -->


</section>
<!--▲コンテンツここまで -->
