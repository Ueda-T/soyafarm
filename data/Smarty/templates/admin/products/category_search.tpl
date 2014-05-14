<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/breadcrumbs.js"></script>
<script type="text/javascript">//<![CDATA[
    $(function() {
        $('h2').breadcrumbs({
            'bread_crumbs': <!--{$tpl_bread_crumbs}-->
        });
    });
//]]>
</script>

<form name="form1" id="form1" method="post" action="?" enctype="multipart/form-data">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="parent_category_id" value="<!--{$arrForm.parent_category_id|h}-->">
<input type="hidden" name="category_id" value="<!--{$arrForm.category_id|h}-->">
<input type="hidden" name="keySet" value="">
<div id="products" class="contents-main">
    <!--{if ($tpl_csv_download_auth == $smarty.const.CSV_DOWNLOAD_AUTH_ON)}-->
    <div class="btn">
        <a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('csv','',''); return false;">CSV ダウンロード</a>
        <a class="btn-normal" href='../contents/csv.php?tpl_subno_csv=category'>CSV 出力項目設定</a>
    </div>
    <!--{/if}-->

    <!--{* ▼画面左 *}-->
    <div id="products-category-left">
        <a href="?"><img src="<!--{$TPL_URLPATH}-->img/contents/folder_close.gif" alt="フォルダ">&nbsp;ホーム</a><br />
        <!--{section name=cnt loop=$arrTree}-->
            <!--{assign var=level value="`$arrTree[cnt].level`}-->

            <!--{* 上の階層表示の時にdivを閉じる *}-->
            <!--{assign var=close_cnt value="`$before_level-$level+1`"}-->
            <!--{if $close_cnt > 0}-->
                <!--{section name=n loop=$close_cnt}--></div><!--{/section}-->
            <!--{/if}-->

            <!--{* スペース繰り返し *}-->
            <!--{section name=n loop=$level}-->　　<!--{/section}-->

            <!--{* カテゴリ名表示 *}-->
            <!--{assign var=disp_name value="`$arrTree[cnt].category_id`.`$arrTree[cnt].category_name`"}-->
            <!--{if $arrTree[cnt].level != $smarty.const.LEVEL_MAX}-->
                <a href="?" onclick="fnModeSubmit('tree', 'parent_category_id', <!--{$arrTree[cnt].category_id}-->); return false;">
                <!--{if $arrForm.parent_category_id == $arrTree[cnt].category_id}-->
                    <img src="<!--{$TPL_URLPATH}-->img/contents/folder_open.gif" alt="フォルダ">
                <!--{else}-->
                    <img src="<!--{$TPL_URLPATH}-->img/contents/folder_close.gif" alt="フォルダ">
                <!--{/if}-->
                <!--{$disp_name|sfCutString:10:false|h}--></a><br />
            <!--{else}-->
                <img src="<!--{$TPL_URLPATH}-->img/contents/folder_close.gif" alt="フォルダ">
                <!--{$disp_name|sfCutString:10:false|h}--></a><br />
            <!--{/if}-->

            <!--{if $arrTree[cnt].display == true}-->
                <div id="f<!--{$arrTree[cnt].category_id}-->">
            <!--{else}-->
                <div id="f<!--{$arrTree[cnt].category_id}-->" style="display:none">
            <!--{/if}-->

            <!--{if $smarty.section.cnt.last}-->
                <!--{section name=n loop=$level}--></div><!--{/section}-->
            <!--{/if}-->

            <!--{assign var=before_level value="`$arrTree[cnt].level`}-->
        <!--{/section}-->
    </div>
    <!--{* ▲画面左 *}-->

    <!--{* ▼画面右 *}-->
    <div id="products-category-right">


        <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
        <!--{*
        <div class="now_dir">
                <!--{if $arrErr.category_name}-->
                <span class="attention"><!--{$arrErr.category_name}--></span>
                <!--{/if}-->
                <input type="text" name="category_name" value="<!--{$arrForm.category_name|h}-->" size="30" class="box30" maxlength="<!--{$smarty.const.STEXT_LEN}-->" />
                <a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('edit','',''); return false;"><span class="btn-next">登録</span></a><span class="attention">&nbsp;（上限<!--{$smarty.const.STEXT_LEN}-->文字）</span>
        </div>
        *}-->
        <!-- div class="btn-area" -->
        <div class="now_dir">
            <ul>
                <li><a class="btn-action" href="javascript:;" name="subm2" onclick="fnChangeAction('./category.php'); fnModeSubmit('', '', ''); return false;"><span class="btn-next">カテゴリを新規入力</span></a></li>
            </ul>
        </div>
        <!--{/if}-->

        <h2><!--{* jQuery で挿入される *}--></h2>
        <!--{if $arrErr.category_name}-->
        <span class="attention100"><!--{$arrErr.category_name}--></span>
        <!--{/if}-->

        <!--{if count($arrList) > 0}-->
        <table class="list" id="categoryTable">
            <colgroup width="5%">
            <colgroup width="15%">
            <colgroup width="45%">
            <colgroup width="10%">
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <colgroup width="10%">
            <colgroup width="15%">
            <!--{/if}-->
            <tr class="nodrop nodrag">
                <th>ID</th>
                <th>カテゴリコード</th>
                <th>カテゴリ名</th>
                <th class="edit">編集</th>
                <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
                <th class="delete">削除</th>
                <th>移動</th>
                <!--{/if}-->
            </tr>

            <!--{section name=cnt loop=$arrList}-->
            <tr id="<!--{$arrList[cnt].category_id}-->" style="background:<!--{if $arrForm.category_id != $arrList[cnt].category_id}-->#ffffff<!--{else}--><!--{$smarty.const.SELECT_RGB}--><!--{/if}-->;" align="left">
                <td class="right"><!--{$arrList[cnt].category_id}--></td>
                <td class="left"><!--{$arrList[cnt].category_code|h}--></td>
                <td>
                <!--{if $arrList[cnt].level != $smarty.const.LEVEL_MAX}-->
                    <a href="?" onclick="fnModeSubmit('tree', 'parent_category_id', <!--{$arrList[cnt].category_id}-->); return false"><!--{$arrList[cnt].category_name|h}--></a>
                <!--{else}-->
                    <!--{$arrList[cnt].category_name|h}-->
                <!--{/if}-->
                </td>
                  <td class="center">
                      <!--{if $arrForm.category_id != $arrList[cnt].category_id}-->
                      <a href="?" onclick="fnChangeAction('./category.php'); fnModeSubmit('', 'category_id', <!--{$arrList[cnt].category_id}-->); return false;">編集</a>
                      <!--{else}-->
                      編集中
                      <!--{/if}-->
                  </td>
                  <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
                  <td class="center">
                      <a href="?" onclick="fnModeSubmit('delete', 'category_id', <!--{$arrList[cnt].category_id}-->); return false;">削除</a>
                  </td>
                  <td class="center">
                  <!--{* 移動 *}-->
                  <!--{if $smarty.section.cnt.iteration != 1}-->
                  <a href="?" onclick="fnModeSubmit('up','category_id', <!--{$arrList[cnt].category_id}-->); return false;">上へ</a>
                  <!--{/if}-->
                  <!--{if $smarty.section.cnt.iteration != $smarty.section.cnt.last}-->
                  <a href="?" onclick="fnModeSubmit('down','category_id', <!--{$arrList[cnt].category_id}-->); return false;">下へ</a>
                  <!--{/if}-->
                  </td>
                  <!--{/if}-->

            </tr>
            <!--{/section}-->
        </table>
        <!--{else}-->
        <p>この階層には、カテゴリが登録されていません。</p>
        <!--{/if}-->
    </div>
    <!--{* ▲画面右 *}-->

</div>
</form>
