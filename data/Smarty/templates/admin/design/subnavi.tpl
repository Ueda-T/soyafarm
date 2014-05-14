<script type="text/javascript">
<!--
function selectDevice() {
    obj = document.form_device_type.select_device_type;
    index = obj.selectedIndex;
    location.href='<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/<!--{$smarty.const.DIR_INDEX_PATH}-->?device_type_id=' + obj.options[index].value;
    return false;
}
// -->
</script>

<div id="side_navi">

<!-- 端末種別選択 -->
<form name="form_device_type">
<div class="device_type">
<select name="select_device_type" onChange="selectDevice();">
<option value="<!--{$smarty.const.DEVICE_TYPE_PC}-->"
    <!--{if $device_type_id == $smarty.const.DEVICE_TYPE_PC}-->
    selected="selected" <!--{/if}--> >PC</option>
<option value="<!--{$smarty.const.DEVICE_TYPE_MOBILE}-->"
    <!--{if $device_type_id == $smarty.const.DEVICE_TYPE_MOBILE}-->
    selected="selected" <!--{/if}--> >モバイル</option>
<option value="<!--{$smarty.const.DEVICE_TYPE_SMARTPHONE}-->"
    <!--{if $device_type_id == $smarty.const.DEVICE_TYPE_SMARTPHONE}-->
    selected="selected" <!--{/if}--> >スマートフォン</option>
</select>
</div>
</form>

<ul class="level1">
            <li<!--{if $tpl_subno == 'layout'}--> class="on"<!--{/if}--> id="navi-design-layout-<!--{$device_type_id}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/<!--{$smarty.const.DIR_INDEX_PATH}-->?device_type_id=<!--{$device_type_id}-->"><span>レイアウト</span></a></li>
            <li<!--{if $tpl_subno == 'main_edit'}--> class="on"<!--{/if}--> id="navi-design-main-<!--{$device_type_id}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/main_edit.php?device_type_id=<!--{$device_type_id}-->"><span>ページ詳細設定</span></a></li>
            <li<!--{if $tpl_subno == 'bloc'}--> class="on"<!--{/if}--> id="navi-design-bloc-<!--{$device_type_id}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/bloc.php?device_type_id=<!--{$device_type_id}-->"><span>ブロック設定</span></a></li>
            <li<!--{if $tpl_subno == 'header'}--> class="on"<!--{/if}--> id="navi-design-header-<!--{$device_type_id}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/header.php?device_type_id=<!--{$device_type_id}-->"><span>ヘッダー/フッター</span></a></li>
            <li<!--{if $tpl_subno == 'css'}--> class="on"<!--{/if}--> id="navi-design-css-<!--{$device_type_id}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/css.php?device_type_id=<!--{$device_type_id}-->"><span>CSS設定</span></a></li>
<!--{*
            <li<!--{if $tpl_subno == 'template'}--> class="on"<!--{/if}--> id="navi-design-template-<!--{$device_type_id}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/template.php?device_type_id=<!--{$device_type_id}-->"><span>テンプレート設定</span></a></li>
            <li<!--{if $tpl_subno == 'up_down'}--> class="on"<!--{/if}--> id="navi-design-add-<!--{$device_type_id}-->"><a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/up_down.php?device_type_id=<!--{$device_type_id}-->"><span>テンプレート追加</span></a></li>
*}-->
</ul>
</div>
