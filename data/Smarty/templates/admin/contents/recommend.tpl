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
<script type="text/javascript">
<!--
function lfnCheckSubmit( fm ){

    var err = '';
    /*
    if ( ! fm["title"].value ){
        err += '見出しコメントを入力して下さい。';
    }
    */
    if ( ! fm["comment"].value ){
        if ( err ) err += '';
        err += 'コメントを入力して下さい。';
    }
    if ( err ){
        alert(err);
        return false;
    } else {
        if(window.confirm('内容を登録しても宜しいですか')){
                fm.submit();
                return true;
        }
    }
}

function lfnCheckSetItem( rank ){
    var flag = true;
    var checkRank = '<!--{$checkRank|h}-->';
    if ( checkRank ){
        if ( rank != checkRank ){
            if( ! window.confirm('さきほど選択した<!--{$checkRank|h}-->位の情報は破棄されます。宜しいでしょうか')){
                flag = false;
            }
        } 
    }

    if ( flag ){
        win03('./recommend_search.php?rank=' + rank,'search','615','600');
    }
}

//-->
</script>

<div id="admin-contents" class="contents-main">

        <!--{section name=cnt loop=$tpl_disp_max}-->

    <table class="list center" id="recommend-table">
        <colgroup width="13%">
        <colgroup width="73%">
        <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
        <colgroup width="7%">
        <colgroup width="7%">
        <!--{/if}-->
        <tr>
            <th>順位</th>
            <th>商品/コメント</th>
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <th>編集</th>
            <th>削除</th>
            <!--{/if}-->
        </tr>

        <tr>
            <td>おすすめ商品(<!--{$smarty.section.cnt.iteration}-->)</td>
                <!--{if $arrItems[$smarty.section.cnt.iteration].product_id}-->
                    <td>
                        <div id="table-wrap" class="clearfix">
                            <div class="table-img">
                                <!--{if $arrItems[$smarty.section.cnt.iteration].product_id}-->
                                    <img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrItems[$smarty.section.cnt.iteration].main_list_image|sfNoImageMainList|h}-->" alt="<!--{$arrItems[$smarty.section.cnt.iteration].name|h}-->" width="100" height="100" />
                                <!--{/if}-->
                            </div>
                            <div class="table-detail">
                                <div class="detail-name">商品名： <!--{$arrItems[$smarty.section.cnt.iteration].name|h}--></div>
                                    <div class="detail-form">
                                        <form name="form<!--{$smarty.section.cnt.iteration}-->" id="form<!--{$smarty.section.cnt.iteration}-->" method="post" action="?">
                                            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
                                            <input type="hidden" name="mode" value="regist" />
                                            <input type="hidden" name="product_id" value="<!--{$arrItems[$smarty.section.cnt.iteration].product_id|h}-->" />
                                            <input type="hidden" name="category_id" value="<!--{$category_id|h}-->" />
                                            <input type="hidden" name="rank" value="<!--{$arrItems[$smarty.section.cnt.iteration].rank|h}-->" />
                                            <span class="attention"><!--{$arrErr[$smarty.section.cnt.iteration].comment}--></span>
                                            <textarea class="top" name="comment" cols="45" rows="4" style="width: 586px; height: 80px; <!--{$arrErr[$smarty.section.cnt.iteration].comment|sfGetErrorColor}-->" <!--{$arrItems[$smarty.section.cnt.iteration].product_id|sfGetEnabled}-->><!--{$arrItems[$smarty.section.cnt.iteration].comment|h}--></textarea>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                <!--{else}-->
                    <td class="AlignLeft">
                        <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
                        <a class="btn-action-m" href="javascript:;" onclick="lfnCheckSetItem('<!--{$smarty.section.cnt.iteration}-->'); return false;" target="_blank"><span class="btn-next">商品を選択する</span></a>
                        <!--{/if}-->
                        <form name="form<!--{$smarty.section.cnt.iteration}-->" id="form<!--{$smarty.section.cnt.iteration}-->" method="post" action="?">
                            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
                            <input type="hidden" name="mode" value="regist" />
                            <input type="hidden" name="product_id" value="<!--{$arrItems[$smarty.section.cnt.iteration].product_id|h}-->" />
                            <input type="hidden" name="category_id" value="<!--{$category_id|h}-->" />
                            <input type="hidden" name="rank" value="<!--{$arrItems[$smarty.section.cnt.iteration].rank|h}-->" />
                        </form>
                    </td>
                <!--{/if}-->
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <td>
                <!--{if $arrItems[$smarty.section.cnt.iteration].product_id}-->
                    <a href="javascript:;" onclick="lfnCheckSetItem('<!--{$smarty.section.cnt.iteration}-->'); return false;" target="_blank">
                        編集</a>
                <!--{else}-->
                    - -
                <!--{/if}-->
            </td>
            <td>
                <!--{if $arrItems[$smarty.section.cnt.iteration].product_id}-->
                        <a href="javascript:;" onClick="return fnInsertValAndSubmit( document.form<!--{$smarty.section.cnt.iteration}-->, 'mode', 'delete', '削除します。宜しいですか' )">削除</a>
                <!--{else}-->
                    - -
                <!--{/if}-->
            </td>
            <!--{/if}-->
        </tr>

        <tr><td colspan="4" class="no-border-w" height="20"></td></tr>
        <!--{if $arrItems[$smarty.section.cnt.iteration].product_id}-->
        <tr><td colspan="4" class="no-border">
        <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
        <a class="btn-action" href="javascript:;" onclick="return lfnCheckSubmit(document.form<!--{$smarty.section.cnt.iteration}-->);"><span class="btn-next">この内容で登録する</span></a>
        <!--{/if}-->
        </td>
        </tr>
        <!--{/if}-->
    <!--▲おすすめ商品<!--{$smarty.section.cnt.iteration}-->-->
    <!--{/section}-->
    </table>
</div>
