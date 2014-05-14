商品番号を入力し､｢買い物かごへ｣をｸﾘｯｸしてください｡<br>
<font color="#FF0000" size="-1">個数はｶｰﾄ内で指定､変更ができます｡</font><br>
<hr>

<form name="form1" id="form1" method="post" action="catalogue.php">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />

<!--{if $tpl_err_msg}-->
<font color="#FF0000" size="-1"><!--{$tpl_err_msg}--></font><br>
<!--{/if}-->

<!--{section name=i start=0 loop=5}-->
<!--{assign var=index value=`$smarty.section.i.index+1`}-->
<!--{assign var=key value="goods$index"}-->
<!--{$index}-->:
<input size="10" name="<!--{$key}-->" id="<!--{$key}-->" value="<!--{$arrForm[$key]|h}-->" type="text" maxlength="10" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
<br>
<!--{if $arrErr[$key]}-->
<font color="#FF0000" size="-1"><!--{$arrErr[$key]}--></font>
<!--{/if}-->

<!--{assign var=key value="how$index"}-->
<input type="radio" name="<!--{$key}-->" id="<!--{$key}-->" value="0" <!--{if $arrForm[$key] eq 0}--> checked="checked" <!--{/if}--> /><label>単回</label><input type="radio" name="<!--{$key}-->" id="<!--{$key}-->" value="1" <!--{if $arrForm[$key] eq 1}--> checked="checked" <!--{/if}--> /><label>定期</label>
<br>
<!--{if $arrErr[$key]}-->
<font color="#FF0000" size="-1"><!--{$arrErr[$key]}--></font>
<!--{/if}-->
<br>
<!--{/section}-->

<br>
<input type="submit" name="" value="買い物かごへ">

</form>
