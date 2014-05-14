<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="class_id" value="<!--{$tpl_class_id|h}-->" />
<div id="products" class="contents-main">

    <table>
        <tr>
            <th>規格名<span class="attention"> *</span></th>
            <td>
                <!--{if $arrErr.name}-->
                    <span class="attention"><!--{$arrErr.name}--></span>
                <!--{/if}-->
                <input type="text" name="name" value="<!--{$arrForm.name|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="" size="30" class="box30" />
                <span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span>
            </td>
        </tr>
    </table>
    <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'edit', '', ''); return false;"><span class="btn-next">この内容で登録する</span></a></li>
        </ul>
    </div>
    <!--{/if}-->

    <table class="list">
        <colgroup width="45%">
        <colgroup width="15%">
        <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
        <colgroup width="10%">
        <colgroup width="10%">
        <colgroup width="15%">
        <!--{/if}-->
        <tr>
            <th>規格名 (登録数)</th>
            <th>分類登録</th>
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <th class="edit">編集</th>
            <th class="delete">削除</th>
            <th>移動</th>
            <!--{/if}-->
        </tr>
        <!--{section name=cnt loop=$arrClass}-->
            <tr style="background:<!--{if $tpl_class_id != $arrClass[cnt].class_id}-->#ffffff<!--{else}--><!--{$smarty.const.SELECT_RGB}--><!--{/if}-->;">
                <!--{assign var=class_id value=$arrClass[cnt].class_id}-->
                <td><!--{* 規格名 *}--><!--{$arrClass[cnt].name|h}--> (<!--{$arrClassCatCount[$class_id]|default:0}-->)</td>
                <td align="center"><a href="<!--{$smarty.const.ROOT_URLPATH}-->" onclick="fnClassCatPage(<!--{$arrClass[cnt].class_id}-->); return false;">分類登録</a></td>
                  <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
                  <td align="center">
                      <!--{if $tpl_class_id != $arrClass[cnt].class_id}-->
                          <a href="?" onclick="fnModeSubmit('pre_edit', 'class_id', <!--{$arrClass[cnt].class_id}-->); return false;">編集</a>
                      <!--{else}-->
                          編集中
                      <!--{/if}-->
                  </td>
                  <td align="center">
                      <!--{if $arrClassCatCount[$class_id] > 0}-->
                          -
                      <!--{else}-->
                          <a href="?" onclick="fnModeSubmit('delete', 'class_id', <!--{$arrClass[cnt].class_id}-->); return false;">削除</a>
                      <!--{/if}-->
                  </td>
                  <td align="center">
                      <!--{if $smarty.section.cnt.iteration != 1}-->
                          <a href="?" onclick="fnModeSubmit('up', 'class_id', <!--{$arrClass[cnt].class_id}-->); return false;">上へ</a>
                      <!--{/if}-->
                      <!--{if $smarty.section.cnt.iteration != $smarty.section.cnt.last}-->
                          <a href="?" onclick="fnModeSubmit('down', 'class_id', <!--{$arrClass[cnt].class_id}-->); return false;">下へ</a>
                      <!--{/if}-->
                  </td>
                  <!--{/if}-->
            </tr>
        <!--{/section}-->
    </table>

</div>
</form>
