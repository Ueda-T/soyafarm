<!--▼CONTENTS-->
<!--{include file="`$smarty.const.TEMPLATE_REALDIR`mypage/pankuzu.tpl"}-->

<div id="mainMyPage">
	<h1><img src="<!--{$TPL_URLPATH}-->img/rohto/mypage_title_sub5.gif" width="700" height="70" alt="配送先の登録・修正" /></h1>

	<!--{if !$tpl_disable_logout}-->
	<form name="header_login_form" id="header_login_form" method="post" action="<!--{$smarty.const.ROOT_URLPATH}-->frontparts/login_check.php" onsubmit="return fnCheckLogin('header_login_form')">
		<input type="hidden" name="mode" value="login" />
		<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
		<input type="hidden" name="url" value="<!--{$smarty.server.REQUEST_URI|h}-->" />
		<p class="logout">
			<a href="javascript:void(0);" onclick="fnFormModeSubmit('header_login_form', 'logout', '', ''); return false;"><img src="<!--{$TPL_URLPATH}-->img/soyafarm/logout.gif" alt="ログアウト" class="swp" /></a>
		</p>
	</form>
	<!--{/if}-->

    <div id="mycontents_area">
        <p class="naked">登録住所以外への住所へ送付される場合等にご利用いただくことができます。<br />
        ※最大<span class="attention"><!--{$smarty.const.DELIV_ADDR_MAX|h}-->件</span>まで登録できます。</p>

        <!--{if $tpl_linemax < $smarty.const.DELIV_ADDR_MAX}-->
            <!--{* 退会時非表示 *}-->
            <!--{if $tpl_login}-->
                <p class="add_address">
                    <a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php" onclick="win03('./delivery_addr.php','delivadd','730','680'); return false;" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_add_address_on.jpg','newadress');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_add_address.jpg','newadress');" target="_blank"><img src="<!--{$TPL_URLPATH}-->img/button/btn_add_address.jpg" alt="新しいお届け先を追加" border="0" name="newadress" /></a>
                </p>
            <!--{/if}-->
        <!--{/if}-->

        <!--{if $tpl_linemax > 0}-->
        <form name="form1" method="post" action="?" >
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="mode" value="" />
            <input type="hidden" name="other_deliv_id" value="" />
            <input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->" />

			<div class="cartList">
            <table summary="お届け先">
            <colgroup width="5%"></colgroup>
            <colgroup width="25%"></colgroup>
            <colgroup width="50%"></colgroup>
            <colgroup width="10%"></colgroup>
            <colgroup width="10%"></colgroup>
                <tr>
                    <th colspan="5">お届け先</th>
                </tr>
                <!--{section name=cnt loop=$arrOtherDeliv}-->
                    <!--{assign var=OtherPref value="`$arrOtherDeliv[cnt].pref`"}-->
                    <tr>
                        <td class="alignC"><!--{$smarty.section.cnt.iteration}--></td>
                        <td><label for="add<!--{$smarty.section.cnt.iteration}-->">お届け先住所</label></td>
                        <td>
                            〒<!--{$arrOtherDeliv[cnt].zip}--><br />
                            <!--{$arrPref[$OtherPref]|h}--><!--{$arrOtherDeliv[cnt].addr01|h}--><!--{$arrOtherDeliv[cnt].addr02|h}--><br />
                            <!--{$arrOtherDeliv[cnt].name|h}-->
                        </td>
                        <td class="alignC">
                            <a href="./delivery_addr.php" onclick="win02('./delivery_addr.php?other_deliv_id=<!--{$arrOtherDeliv[cnt].other_deliv_id}-->','deliv_disp','730','680'); return false;">変更</a>
                        </td>
                        <td class="alignC">
                            <a href="javascript:void(0);" onclick="fnModeSubmit('delete','other_deliv_id','<!--{$arrOtherDeliv[cnt].other_deliv_id}-->'); return false;">削除</a>
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
