<form name="form1" id="form1" method="post" action="">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="edit" />
<!--{assign var=key value="deliv_id"}-->
<input type="hidden" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" />
<div id="basis" class="contents-main">
    <h2>配送方法登録</h2>

    <table>
        <tr>
            <th>配送業者名<span class="attention"> *</span></td>
            <td colspan="3">
            <!--{assign var=key value="name"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="60" class="box60" /></td>
        </tr>
        <tr>
            <th>名称<span class="attention"> *</span></td>
            <td colspan="3">
            <!--{assign var=key value="service_name"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="60" class="box60" /></td>
        </tr>
        <tr>
            <th>説明</td>
            <td colspan="3">
            <!--{assign var=key value="remark"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <textarea name="<!--{$arrForm[$key].keyname}-->" cols="60" rows="8" class="area60" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"><!--{$arrForm[$key].value|h}--></textarea></td>
        </tr>
        <tr>
            <th>伝票No.URL</td>
            <td colspan="3">
            <!--{assign var=key value="confirm_url"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="60" class="box60" /></td>
        </tr>
        <!--{section name=cnt loop=$smarty.const.DELIVTIME_MAX}-->
        <!--{assign var=type value="`$smarty.section.cnt.index%2`"}-->
        <!--{assign var=keyno value="`$smarty.section.cnt.iteration`"}-->
        <!--{assign var=key value="deliv_time`$smarty.section.cnt.iteration`"}-->
        <!--{assign var=key_next value="deliv_time`$smarty.section.cnt.iteration+1`"}-->
        <!--{if $type == 0}-->
            <!--{if $arrErr[$key] != "" || $arrErr[$key_next] != ""}-->
            <tr>
                <td colspan="4"><span class="attention"><!--{$arrErr[$key]}--><!--{$arrErr[$key_next]}--></span></td>
            </tr>        
            <!--{/if}-->
            <tr>
            <th>お届け時間<!--{$keyno}--></td>
            <!--{if $smarty.section.cnt.last}-->
            <!--{assign var=colspan value="3"}-->
            <!--{else}-->
            <!--{assign var=colspan value="1"}-->
            <!--{/if}-->
            <td colspan="<!--{$colspan}-->">
            <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="20" class="box20" /></td>
        <!--{else}-->
            <th>お届け時間<!--{$keyno}--></td>
            <td><input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" size="20" class="box20" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /> </td>
            </tr>
        <!--{/if}-->
        <!--{/section}-->

    </table>

    <h2>取扱商品種別</h2>
    <!--{assign var=key value="product_type_id"}-->
    <table>
        <tr>
            <th>商品種別</th>
            <td><span class="attention"><!--{$arrErr[$key]}--></span><!--{html_radios name=$key options=$arrProductType selected=$arrForm[$key].value separator='&nbsp;&nbsp;'}--></td>
        </tr>
    </table>

    <h2>取扱支払方法</h2>
    <!--{assign var=key value="payment_ids"}-->
    <table>
        <tr>
            <th>支払方法</th>
            <td>
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{html_checkboxes name=$key options=$arrPayments selected=$arrForm[$key].value separator='&nbsp;&nbsp;'}-->
            </td>
        </tr>
    </table>

    <!--{if $smarty.const.INPUT_DELIV_FEE}-->
    <h2>配達日数登録</h2>

<!--{* #409 配送料の設定を廃止
    <div class="btn">※全国一律送料 <input type="text" name="fee_all" class="box10" /> 円に設定する　<a class="btn-normal" href="javascript:;" onclick="fnSetDelivFee(<!--{$smarty.const.DELIVFEE_MAX}-->); return false;"><span>反映</span></a></div>
*}-->

    <div class="btn">※全国一律配達日数 <input type="text" name="days_all" class="box10" /> 日に設定する　<a class="btn-normal" href="javascript:;" onclick="fnSetDelivDays(<!--{$smarty.const.DELIVFEE_MAX}-->); return false;"><span>反映</span></a></div>
    <table>
        <!--{section name=cnt loop=$smarty.const.DELIVFEE_MAX}-->
        <!--{assign var=type value="`$smarty.section.cnt.index%2`"}-->
        <!--{assign var=keyno value="`$smarty.section.cnt.iteration`"}-->
        <!--{assign var=key value="fee`$smarty.section.cnt.iteration`"}-->
        <!--{assign var=key_next value="fee`$smarty.section.cnt.iteration+1`"}-->
        <!--{assign var=key2 value="days`$smarty.section.cnt.iteration`"}-->
        <!--{assign var=key2_next value="days`$smarty.section.cnt.iteration+1`"}-->

        <!--{if $type == 0}-->
            <!--{if $arrErr[$key] != "" || $arrErr[$key_next] != "" || $arrErr[$key2] != "" || $arrErr[$key2_next] != ""}-->
            <tr>
                <td colspan="4"><span class="attention"><!--{$arrErr[$key]}--><!--{$arrErr[$key_next]}--><!--{$arrErr[$key2]}--><!--{$arrErr[$key2_next]}--></span></td>
            </tr>        
            <!--{/if}-->

            <tr>
            <th><!--{$arrPref[$keyno]}--></td>
            <!--{if $smarty.section.cnt.last}-->
            <!--{assign var=colspan value="3"}-->    
            <!--{else}-->
            <!--{assign var=colspan value="1"}-->
            <!--{/if}-->
            <td width="247" colspan="<!--{$colspan}-->">

<!--{* #409 配送料の設定を廃止
                <input type="text" name="<!--{$arrForm[$key].keyname}-->"
                    value="<!--{$arrForm[$key].value|h}-->"
                    maxlength="<!--{$arrForm[$key].length}-->"
                    size="6" class="box6"
                    style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /> 円
*}-->
                <input type="text" name="<!--{$arrForm[$key2].keyname}-->"
                    value="<!--{$arrForm[$key2].value|h}-->"
                    maxlength="<!--{$arrForm[$key2].length}-->"
                    size="6" class="box6"
                    style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" /> 日
            </td>
        <!--{else}-->
            <th><!--{$arrPref[$keyno]}--></td>
            <td width="248">

<!--{* #409 配送料の設定を廃止
                <input type="text" name="<!--{$arrForm[$key].keyname}-->"
                    value="<!--{$arrForm[$key].value|h}-->"
                    maxlength="<!--{$arrForm[$key].length}-->"
                    size="6"
                    class="box6"
                    style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /> 円
*}-->
                <input type="text" name="<!--{$arrForm[$key2].keyname}-->"
                    value="<!--{$arrForm[$key2].value|h}-->"
                    maxlength="<!--{$arrForm[$key2].length}-->"
                    size="6"
                    class="box6"
                    style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" /> 日
            </td>
            </tr>
        <!--{/if}-->
        <!--{/section}-->
    </table>
    <!--{/if}-->

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="location.href='./delivery.php';"><span class="btn-prev">前のページに戻る</span></a></li>
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'edit', '', ''); return false;"><span class="btn-next">この内容で登録する</span></a></li>
            <!--{/if}-->
        </ul>
    </div>
</div>
</form>
