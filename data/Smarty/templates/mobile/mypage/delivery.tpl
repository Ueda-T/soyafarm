<!--▼CONTENTS-->
<font size="-1">登録住所以外への住所へ送付される場合等にご利用いただくことができます。<br>
※最大<font color="#FF0000"><!--{$smarty.const.DELIV_ADDR_MAX|h}-->件</font>まで登録できます。</font><br>
<br>

<!--{if $tpl_linemax < $smarty.const.DELIV_ADDR_MAX}-->
<!--{* 退会時非表示 *}-->
<!--{if $tpl_login}-->
<font size="-1"><a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php">新しいお届け先を追加</a></font>
<br>
<!--{/if}-->
<!--{/if}-->
<hr>
<!--{if $tpl_linemax > 0}-->
<form name="form1" method="post" action="?" >
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="confirm">
<input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->">

<!--{section name=cnt loop=$arrOtherDeliv}-->
<input type="hidden" name="other_deliv_id" value="<!--{$arrOtherDeliv[cnt].other_deliv_id}-->">
<!--{assign var=OtherPref value="`$arrOtherDeliv[cnt].pref`"}-->
<!--{$smarty.section.cnt.iteration}-->:
<label for="add<!--{$smarty.section.cnt.iteration}-->">お届け先住所</label><br>
〒<!--{$arrOtherDeliv[cnt].zip}--><br>
<!--{$arrPref[$OtherPref]|h}--><!--{$arrOtherDeliv[cnt].addr01|h}--><!--{$arrOtherDeliv[cnt].addr02|h}--><br>
<!--{$arrOtherDeliv[cnt].name|h}--><br>

<a href="./delivery_addr.php?other_deliv_id=<!--{$arrOtherDeliv[cnt].other_deliv_id}-->">変更</a>
<input type="submit" name="delete_<!--{$arrOtherDeliv[cnt].other_deliv_id}-->" value="削除">
<hr>
<!--{/section}-->
</form>
<!--{else}-->
<font size="-1">新しいお届け先はありません。</font><br>
<br>
<!--{/if}-->
<!--▲CONTENTS-->
