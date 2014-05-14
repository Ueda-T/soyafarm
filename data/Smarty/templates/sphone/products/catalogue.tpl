<!--{* -*- coding: utf-8-unix; -*- *}-->
<script type="text/javascript">
    $(function() {
        var _lookup = function(e) {
	    var s = $(this).val();
	    if (s) {
	        $.ajax({
                    url: "<!--{$smarty.const.INPUT_PRODUCT_URLPATH}-->",
                    data: {
		        productCode:s,
		    },
		    success: function(r) {
		        var result = eval(r);
		        arr = result.split("|");
			$("#" + e.data.id).text(arr[0]);
			if (arr[1] == 0) {
                            $("#" + e.data.relative).hide(0);
			} else {
                            $("#" + e.data.relative).show(0);
                        }
		    },
		    error: function(data) {
		        ;
		    },
	        });
            } else {
	    	$("#" + e.data.id).text("");
                $("#" + e.data.relative).show(0);
            }
	};
	$("#goods1").on("blur", {id: "goodsname1", relative: "how-div1"}, _lookup);
	$("#goods2").on("blur", {id: "goodsname2", relative: "how-div2"}, _lookup);
	$("#goods3").on("blur", {id: "goodsname3", relative: "how-div3"}, _lookup);
	$("#goods4").on("blur", {id: "goodsname4", relative: "how-div4"}, _lookup);
	$("#goods5").on("blur", {id: "goodsname5", relative: "how-div5"}, _lookup);
    });
</script>
<!--▼CONTENTS-->
<section id="product_list">
	<h2 class="spNaked"><!--{$tpl_subtitle|h}--></h2>
<p class="intro">商品番号を入力して「買物カゴに入れる」ボタンを押してください。<br>個数は「買物カゴ」（次の画面）で指定出来ます。</p>
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />

<table cellspacing="0" class="bsc" style="width:100%">
<!--{section name=i start=0 loop=5}-->
<tr class="add">
  <!--{assign var=index value=`$smarty.section.i.index+1`}-->
  <th width="10"><!--{$index}--></th>
  <td width="100">
    <!--{assign var=key value="goods$index"}-->
    <!--{if $arrErr[$key]}-->
    <div class="attention"><!--{$arrErr[$key]}--></div>
    <!--{/if}-->
    <input size="10" name="<!--{$key}-->" id="<!--{$key}-->" value="<!--{$arrForm[$key]|h}-->" type="text" maxlength="10" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
  </td>
  <td>
    <!--{assign var=key value="goodsname$index"}-->
    <span id="<!--{$key}-->"></span><br />
  </td>
</tr>
<tr>
  <td colspan="2>
    <!--{assign var=key value="how$index"}-->
    <div id="how-div<!--{$index}-->">
    <input type="radio" name="<!--{$key}-->" id="<!--{$key}-->" value="0" <!--{if $arrForm[$key] eq 0}--> checked="checked" <!--{/if}--> /><label>単回</label><input type="radio" name="<!--{$key}-->" id="<!--{$key}-->" value="1" <!--{if $arrForm[$key] eq 1}--> checked="checked" <!--{/if}--> /><label>定期</label></div>
  </td>
  <td>&nbsp;</td>
</tr>
<!--{/section}-->
</table>

<p class="nakedC" style="margin-top:2em;"><a href="javascript:void(0);" onclick="document.form1.submit();return false;" name="cart" class="btnOrange">買い物かごへ</a></p>
</form>