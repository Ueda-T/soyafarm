<!--{*
/*
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
 */
*}-->
<!--{php}-->
require_once(MODULE_REALDIR . 'mdl_smbc/inc/include.php');
require_once(MDL_SMBC_CLASS_PATH . 'SC_Mdl_SMBC.php');
$objSMBC =& SC_Mdl_SMBC::getInstance();
$arrSubData = $objSMBC->getSubData();
$this->assign('keizoku_flg', $arrSubData['keizoku_flg']);
<!--{/php}-->
<ul class="level1">
    <li id="navi-order-index" class="<!--{if $tpl_mainno == 'order' && $tpl_subno == 'index'}-->on<!--{/if}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>受注管理</span></a></li>
    <li id="navi-order-add" class="<!--{if $tpl_mainno == 'order' && $tpl_subno == 'add'}-->on<!--{/if}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/edit.php?mode=add"><span>新規受注入力</span></a></li>
    <li id="navi-order-status"
        class="<!--{if $tpl_mainno == 'order' && $tpl_subno == 'status'}-->on<!--{/if}-->"
    ><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/status.php"><span>ステータス管理</span></a></li>
    <li id="navi-order-credit" class="<!--{if $tpl_mainno == 'order' && $tpl_subno == 'credit'}-->on<!--{/if}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/credit.php"><span>クレジット請求管理</span></a></li>
    <li id="navi-order-delete" class="<!--{if $tpl_mainno == 'order' && $tpl_subno == 'delete'}-->on<!--{/if}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/delete.php"><span>カード情報削除管理</span></a></li>
    <li id="navi-order-payment" class="<!--{if $tpl_mainno == 'order' && $tpl_subno == 'payment'}-->on<!--{/if}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/payment.php"><span>入金管理</span></a></li>
    <!--{if $keizoku_flg == '1'}-->
    <li id="navi-order-payment" class="<!--{if $tpl_mainno == 'order' && $tpl_subno == 'regular'}-->on<!--{/if}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/regular.php"><span>定期購入課金管理</span></a></li>
    <li id="navi-order-payment" class="<!--{if $tpl_mainno == 'order' && $tpl_subno == 'upload_csv_regular'}-->on<!--{/if}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/upload_csv_regular.php"><span>定期受注CSVアップロード</span></a></li>
    <!--{/if}-->
</ul>
