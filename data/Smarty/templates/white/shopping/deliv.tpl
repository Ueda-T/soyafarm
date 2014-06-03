<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *}-->
<!--▼CONTENTS-->
<div id="undercolumn">
	<div id="undercolumn_shopping">
		<h1><img src="<!--{$TPL_URLPATH}-->img/soyafarm/order_title_step1.gif" width="960" height="70" alt="購入手続き：ご注文情報入力"></h1>

		<div class="wrapCoan">
			<p>下記一覧よりお届け先住所を選択して、「選択したお届け先に送る」ボタンをクリックしてください。
			<!--{if $tpl_addrmax < $smarty.const.DELIV_ADDR_MAX}-->
				<br />一覧にご希望の住所が無い場合は、「新しいお届け先を追加する」より追加登録してください。
			<!--{/if}--></p>
			<p class="mini attention">※最大<!--{$smarty.const.DELIV_ADDR_MAX|h}-->件まで登録できます。</p>

			<!--{if $tpl_addrmax < $smarty.const.DELIV_ADDR_MAX}-->
			<p class="addbtn">
				<a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php" onclick="win02('<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php?page=<!--{$smarty.server.PHP_SELF|h}-->','new_deiv','730','680'); return false;" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_add_address_on.jpg','addition');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_add_address.jpg','addition');"><img src="<!--{$TPL_URLPATH}-->img/button/btn_add_address.jpg" alt="新しいお届け先を追加する" name="addition" id="addition" /></a>
			</p>
			<!--{/if}-->

			<h3 class="order"><img src="<!--{$TPL_URLPATH}-->img/rohto/method_midashi03.gif" width="820" height="35" alt="お届け先" /></h3>

			<form name="form1" id="form1" method="post" action="?">
			<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
			<input type="hidden" name="mode" value="customer_addr" />
			<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />
			<input type="hidden" name="other_deliv_id" value="" />
			<!--{if $arrErr.deli != ""}-->
				<p class="attention"><!--{$arrErr.deli}--></p>
			<!--{/if}-->
			<table summary="お届け先の指定" class="tblOrder">
				<colgroup width="10%"></colgroup>
				<colgroup width="20%"></colgroup>
				<colgroup width="50%"></colgroup>
				<colgroup width="10%"></colgroup>
				<colgroup width="10%"></colgroup>
				<tr>
					<th class="alignC">選択</th>
					<th class="alignC">住所種類</th>
					<th class="alignC">お届け先</th>
					<th class="alignC">変更</th>
					<th class="alignC">削除</th>
				</tr>
				<!--{section name=cnt loop=$arrAddr}-->
				<tr>
					<td class="alignC">
						<!--{if $smarty.section.cnt.first}-->
							<input type="radio" name="deliv_check" id="chk_id_<!--{$smarty.section.cnt.iteration}-->" value="-1" <!--{if $arrForm.deliv_check.value == "" || $arrForm.deliv_check.value == -1}--> checked="checked"<!--{/if}--> />
						<!--{else}-->
							<input type="radio" name="deliv_check" id="chk_id_<!--{$smarty.section.cnt.iteration}-->" value="<!--{$arrAddr[cnt].other_deliv_id}-->"<!--{if $arrForm.deliv_check.value == $arrAddr[cnt].other_deliv_id}--> checked="checked"<!--{/if}--> />
						<!--{/if}-->
					</td>
					<td class="alignC">
						<label for="chk_id_<!--{$smarty.section.cnt.iteration}-->">
							<!--{if $smarty.section.cnt.first}-->
								会員登録住所
							<!--{else}-->
								追加登録住所
							<!--{/if}-->
						</label>
					</td>
					<td>
						<!--{assign var=key value=$arrAddr[cnt].pref}-->
						<!--{$arrPref[$key]}--><!--{$arrAddr[cnt].addr01|h}--><!--{$arrAddr[cnt].addr02|h}--><br />
						<!--{$arrAddr[cnt].name|h}-->
					</td>
					<td class="alignC">
						<!--{if !$smarty.section.cnt.first}-->
							<a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php" onclick="win02('<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php?page=<!--{$smarty.server.PHP_SELF|h}-->&amp;other_deliv_id=<!--{$arrAddr[cnt].other_deliv_id}-->','new_deiv','730','680'); return false;">変更</a>
							<!--{else}-->
								-
							<!--{/if}-->
					</td>
					<td class="alignC">
						<!--{if !$smarty.section.cnt.first}-->
							<a href="?" onclick="fnModeSubmit('delete', 'other_deliv_id', '<!--{$arrAddr[cnt].other_deliv_id}-->'); return false">削除</a>
							<!--{else}-->
								-
							<!--{/if}-->
					</td>
				</tr>
				<!--{/section}-->
			</table>

			<div class="orderBtn">
					<p class="left">
						<span class="f-right">
						<a href="javascript:void(0)" onclick="document.form1.submit();return false;"><img src="<!--{$TPL_URLPATH}-->img/rohto/btn_next.gif" alt="次へ" name="send_button" id="send_button" class="swp" /></a>
						</span>
						<a href="<!--{$smarty.const.CART_URLPATH}-->" onmouseover="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/rohto/btn_back_ov.gif',back03)" onmouseout="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/soyafarm/btn_back.gif',back03)">
						<img src="<!--{$TPL_URLPATH}-->img/soyafarm/btn_back.gif" alt="戻る" border="0" name="back03" id="back03" /></a>
					</p>
			</div>
			</form>
		</div>
	</div>
</div>
<!--▲CONTENTS-->
