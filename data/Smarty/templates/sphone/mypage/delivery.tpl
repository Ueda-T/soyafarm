<!--▼CONTENTS-->
<div id="mainMyPage">
	<h2 class="spNaked"><!--{$tpl_title}--></h2>

	<!--{if $tpl_navi != ""}-->
		<!--{include file=$tpl_navi}-->
	<!--{else}-->
		<!--{include file=`$smarty.const.TEMPLATE_REALDIR`mypage/navi.tpl}-->
	<!--{/if}-->

    <div id="mycontents_area">
        <p class="naked">登録住所以外への住所へ送付される場合等にご利用いただくことができます。<br />
        ※最大<span class="attention"><!--{$smarty.const.DELIV_ADDR_MAX|h}-->件</span>まで登録できます。</p>

        <!--{if $tpl_linemax < $smarty.const.DELIV_ADDR_MAX}-->
            <!--{* 退会時非表示 *}-->
            <!--{if $tpl_login}-->
                <p class="add_address" style="margin:1em 10px;text-align:right;">
                    <a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php" onclick="win03('./delivery_addr.php','delivadd','730','680'); return false;" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_add_address_on.jpg','newadress');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_add_address.jpg','newadress');" target="_blank" class="btnGray">新しいお届け先を追加</a>
                </p>
            <!--{/if}-->
        <!--{/if}-->

        <!--{if $tpl_linemax > 0}-->
        <form name="form1" method="post" action="?" >
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="mode" value="" />
            <input type="hidden" name="other_deliv_id" value="" />
            <input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->" />

			<div class="bgYellow" style="margin-bottom:1em;">
            <table summary="お届け先">
                <!--{section name=cnt loop=$arrOtherDeliv}-->
                    <!--{assign var=OtherPref value="`$arrOtherDeliv[cnt].pref`"}-->
                    <tr>
                        <td class="alignC"><!--{$smarty.section.cnt.iteration}--></td>
<!--{*
                        <td><label for="add<!--{$smarty.section.cnt.iteration}-->">お届け先住所</label></td>
*}-->
                        <td>
                            〒<!--{$arrOtherDeliv[cnt].zip}--><br />
                            <!--{$arrPref[$OtherPref]|h}--><!--{$arrOtherDeliv[cnt].addr01|h}--><!--{$arrOtherDeliv[cnt].addr02|h}--><br />
                            <!--{$arrOtherDeliv[cnt].name|h}-->
                        </td>
					</tr>
					<tr>
                        <td colspan="2" height="30" align="right">
                            <a href="./delivery_addr.php" onclick="win02('./delivery_addr.php?other_deliv_id=<!--{$arrOtherDeliv[cnt].other_deliv_id}-->','deliv_disp','730','680'); return false;" class="btnGray03">変更</a>
                            <a href="javascript:void(0);" onclick="fnModeSubmit('delete','other_deliv_id','<!--{$arrOtherDeliv[cnt].other_deliv_id}-->'); return false;" class="btnGray03">削除</a>
                        </td>
                    </tr>
                <!--{/section}-->
            </table>
			</div>
        </form>
        <!--{else}-->
        <p class="delivempty"><strong>新しいお届け先はありません。</strong></p>
        <!--{/if}-->
    </div>
</div>
<!--▲CONTENTS-->
