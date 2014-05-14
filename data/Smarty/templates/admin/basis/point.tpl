<!--{* -*- coding: utf-8-unix; -*- *}-->
<script type="text/javascript">
$(document).ready(function(){
    $.spin.imageBasePath = '<!--{$TPL_URLPATH}-->img/spin1/';
    $('#spin1').spin({
        min: 0,
	timeInterval: 150,
    });
    $('#spin2').spin({
        min: 0,
	timeInterval: 150,
    });
});
</script>
<form name="point_form" id="point_form" method="post" action="">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="<!--{$tpl_mode}-->" />
<div id="basis" class="contents-main">
    <table>
        <tr>
            <th>ポイント付与率（初期値）<span class="attention"> *</span></th>
            <td>
                <!--{assign var=key value="point_rate"}-->
                <!--{if $arrErr[$key]}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{/if}-->
                <input type="text" id="spin1" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="6" class="box6" />
                ％　小数点以下切り捨て</td>
        </tr>
        <tr>
            <th>会員登録時付与ポイント<span class="attention"> *</span></th>
            <td>
                <!--{assign var=key value="welcome_point"}-->
                <!--{if $arrErr[$key]}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{/if}-->
                <input type="text" id="spin2" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="6" class="box6" />
            pt</td>
        </tr>
    </table>

    <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('point_form', '<!--{$tpl_mode}-->', '', ''); return false;"><span class="btn-next">この内容で登録する</span></a></li>
        </ul>
    </div>
    <!--{/if}-->
</div>
</form>
