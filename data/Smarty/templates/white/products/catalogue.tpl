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
		        var result = eval('(' + r + ')');
		        arr = result.split("|");
			$("#" + e.data.id).text(arr[0]);
			if (arr[1] == 0) {
                            $("#" + e.data.relative).hide(0);
			} else {
                            $("#" + e.data.relative).show(0);
                        }
		    },
		    error: function(XMLHttpRequest, textStatus, errorThrown) {
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
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<table width="700" cellspacing="0">
  <tr>
    <td width="350">
      <table cellspacing="0" class="bsc" style="width:100%">
        <tr>
          <th>&nbsp;</th>
          <th class="center">商品番号</th>
        </tr>
        <!--{section name=i start=0 loop=5}-->
        <tr class="add">
          <!--{assign var=index value=`$smarty.section.i.index+1`}-->
          <th><!--{$index}--></th>
          <td>
            <!--{assign var=key value="goods$index"}-->
            <!--{if $arrErr[$key]}-->
            <div class="attention"><!--{$arrErr[$key]}--></div>
            <!--{/if}-->
            <input size="10" name="<!--{$key}-->" id="<!--{$key}-->" value="<!--{$arrForm[$key]|h}-->" type="text" maxlength="10" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
            <!--{assign var=key value="goodsname$index"}-->
            <span id="<!--{$key}-->"></span><br />

            <!--{assign var=key value="how$index"}-->
            <div id="how-div<!--{$index}-->">
            <input type="radio" name="<!--{$key}-->" id="<!--{$key}-->" value="0" <!--{if $arrForm[$key] eq 0}--> checked="checked" <!--{/if}--> /><label>単回</label><input type="radio" name="<!--{$key}-->" id="<!--{$key}-->" value="1" <!--{if $arrForm[$key] eq 1}--> checked="checked" <!--{/if}--> /><label>定期</label></div>
          </td>
        </tr>
        <!--{/section}-->
      </table>
    </td>
  </tr>
</table>
<p class="nakedC" style="margin:30px 0;"><a href="javascript:void(0);" onclick="document.form1.submit();return false;" name="cart"><img src="<!--{$TPL_URLPATH}-->img/rohto/cart.gif" alt="カートに入れる" class="swp" /></a></p>
</form>
<!--{$tpl_clickAnalyzer}-->
