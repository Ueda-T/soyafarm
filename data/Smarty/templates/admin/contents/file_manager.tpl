<script type="text/javascript">//<![CDATA[
    $(function() {
        var bread_crumbs = <!--{$tpl_now_dir}-->;
        var file_path = '<!--{$tpl_file_path}-->';
        var $delimiter = '<span>&nbsp;&gt;&nbsp;</span>';
        var $node = $('h2');
        var total = bread_crumbs.length;
        for (var i in bread_crumbs) {
            file_path += bread_crumbs[i] + '/';
            $('<a href="javascript:;" onclick="fnFolderOpen(\'' + file_path + '\'); return false;" />')
                .text(bread_crumbs[i])
                .appendTo($node);
            if (i < total - 1) $node.append($delimiter);
        }
    });

var IMG_FOLDER_CLOSE   = "<!--{$TPL_URLPATH}-->img/contents/folder_close.gif";  // フォルダクローズ時画像
var IMG_FOLDER_OPEN    = "<!--{$TPL_URLPATH}-->img/contents/folder_open.gif";   // フォルダオープン時画像
var IMG_PLUS           = "<!--{$TPL_URLPATH}-->img/contents/plus.gif";          // プラスライン
var IMG_MINUS          = "<!--{$TPL_URLPATH}-->img/contents/minus.gif";         // マイナスライン
var IMG_NORMAL         = "<!--{$TPL_URLPATH}-->img/contents/space.gif";         // スペース
//]]>
</script>
<form name="form1" method="post" action="?"  enctype="multipart/form-data">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="now_file" value="<!--{$tpl_now_dir|h}-->" />
<input type="hidden" name="now_dir" value="<!--{$tpl_now_file|h}-->" />
<input type="hidden" name="tree_select_file" value="" />
<input type="hidden" name="tree_status" value="" />
<input type="hidden" name="select_file" value="" />
<div id="admin-contents" class="contents-main">
    <div id="contents-filemanager-tree">
        <div id="tree"></div>
    </div>
    <div id="contents-filemanager-right">
        <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
        <table class="now_dir">
            <tr>
                <th>ファイルのアップロード</th>
                <td>
                    <!--{if $arrErr.upload_file}--><span class="attention"><!--{$arrErr.upload_file}--></span><!--{/if}-->
                    <input type="file" name="upload_file" size="40" <!--{if $arrErr.upload_file}-->style="background-color:<!--{$smarty.const.ERR_COLOR|h}-->"<!--{/if}-->><a class="btn-normal" href="javascript:;" onclick="setTreeStatus('tree_status');fnModeSubmit('upload','',''); return false;">アップロード</a>
                </td>
            </tr>
            <tr>
                <th>フォルダ作成</th>
                <td>
                    <!--{if $arrErr.create_file}--><span class="attention"><!--{$arrErr.create_file}--></span><!--{/if}-->
                    <input type="text" name="create_file" value="" style="width:336px;<!--{if $arrErr.create_file}--> background-color:<!--{$smarty.const.ERR_COLOR|h}--><!--{/if}-->"><a class="btn-normal" href="javascript:;" onclick="setTreeStatus('tree_status');fnModeSubmit('create','',''); return false;">作成</a>
                </td>
            </tr>
        </table>
        <!--{/if}-->
        <h2><!--{* jQuery で挿入される *}--></h2>
        <table class="list">
            <tr>
                <th>ファイル名</th>
                <th>サイズ</th>
                <th>更新日付</th>
                <th class="edit">表示</th>
                <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
                <th>ダウンロード</th>
                <th class="delete">削除</th>
                <!--{/if}-->
            </tr>
            <!--{if !$tpl_is_top_dir}-->
                <tr id="parent_dir" onclick="fnSetFormVal('form1', 'select_file', '<!--{$tpl_parent_dir|h}-->');fnSelectFile('parent_dir', '#808080');" onDblClick="setTreeStatus('tree_status');fnDbClick(arrTree, '<!--{$tpl_parent_dir|h}-->', true, '<!--{$tpl_now_dir|h}-->', true)" style="">
                    <td>
                        <img src="<!--{$TPL_URLPATH}-->img/contents/folder_parent.gif" alt="フォルダ">&nbsp;..
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <!--{/if}-->
                </tr>
            <!--{/if}-->
            <!--{section name=cnt loop=$arrFileList}-->
                <!--{assign var="id" value="select_file`$smarty.section.cnt.index`"}-->
                <tr id="<!--{$id}-->" style="">
                    <td class="file-name" onDblClick="setTreeStatus('tree_status');fnDbClick(arrTree, '<!--{$arrFileList[cnt].file_path|h}-->', <!--{if $arrFileList[cnt].is_dir|h}-->true<!--{else}-->false<!--{/if}-->, '<!--{$tpl_now_dir|h}-->', false)">
                        <!--{if $arrFileList[cnt].is_dir}-->
                            <img src="<!--{$TPL_URLPATH}-->img/contents/folder_close.gif" alt="フォルダ">
                        <!--{else}-->
                            <img src="<!--{$TPL_URLPATH}-->img/contents/file.gif">
                        <!--{/if}-->
                        <!--{$arrFileList[cnt].file_name|h}-->
                    </td>
                    <td class="right">
                        <!--{$arrFileList[cnt].file_size|number_format}-->
                    </td>
                    <td class="center">
                        <!--{$arrFileList[cnt].file_time|h}-->
                    </td>
                    <!--{if $arrFileList[cnt].is_dir}-->
                        <td class="center">
                            <a href="javascript:;" onclick="fnSetFormVal('form1', 'tree_select_file', '<!--{$arrFileList[cnt].file_path}-->');fnSelectFile('<!--{$id}-->', '#808080');fnModeSubmit('move','',''); return false;">表示</a>
                        </td>
                    <!--{else}-->
                        <td class="center">
                            <a href="javascript:;" onclick="fnSetFormVal('form1', 'select_file', '<!--{$arrFileList[cnt].file_path|h}-->');fnSelectFile('<!--{$id}-->', '#808080');fnModeSubmit('view','',''); return false;">表示</a>
                        </td>
                    <!--{/if}-->
                    <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
                    <!--{if $arrFileList[cnt].is_dir}-->
                        <!--{* ディレクトリはダウンロード不可 *}-->
                        <td class="center">-</td>
                    <!--{else}-->
                        <td class="center">
                            <a href="javascript:;" onclick="fnSetFormVal('form1', 'select_file', '<!--{$arrFileList[cnt].file_path|h}-->');fnSelectFile('<!--{$id}-->', '#808080');setTreeStatus('tree_status');fnModeSubmit('download','',''); return false;">ダウンロード</a>
                        </td>
                    <!--{/if}-->
                    <td class="center">
                        <a href="javascript:;" onclick="fnSetFormVal('form1', 'select_file', '<!--{$arrFileList[cnt].file_path|h}-->');fnSelectFile('<!--{$id}-->', '#808080');setTreeStatus('tree_status');fnModeSubmit('delete','',''); return false;">削除</a>
                    </td>
                    <!--{/if}-->
                </tr>
            <!--{/section}-->
        </table>
    </div>
</div>
</form>
