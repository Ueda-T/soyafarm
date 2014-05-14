<!--{* -*- coding: utf-8-unix; -*- *}-->
<div id="products" class="contents-main">
<form name="search_form" id="search_form" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="search" />
    <h2>検索条件設定</h2>

    <!--検索条件設定テーブルここから-->
    <table>
        <tr>
            <th>商品ID</th>
            <td>
                <!--{assign var=key value="search_product_id"}-->
                <!--{if $arrErr[$key]}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{/if}-->
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30"/>
            </td>
            <th>種別</th>
            <td>
                <!--{assign var=key value="search_status"}-->
                <span class="attention"><!--{$arrErr[$key]|h}--></span>
                <!--{html_checkboxes name="$key" options=$arrDISP selected=$arrForm[$key].value}-->
            </td>
        </tr>
        <tr>
            <th>商品コード</th>
            <td>
                <!--{assign var=key value="search_product_code"}-->
                <!--{if $arrErr[$key]}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{/if}-->
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
            <th>商品名</th>
            <td>
                <!--{assign var=key value="search_name"}-->
                <!--{if $arrErr[$key]}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{/if}-->
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" />
            </td>
        </tr>
        <tr>
            <th>カテゴリ</th>
            <td colspan="3">
                <!--{assign var=key value="search_category_id"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                <option value="">選択してください</option>
                <!--{html_options options=$arrCatList selected=$arrForm[$key].value}-->
                </select>
            </td>
        </tr>
        <tr>
            <th>ブランド</th>
            <td colspan="3">
                <!--{assign var=key value="search_brand_id"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                <option value="">選択してください</option>
                <!--{html_options options=$arrBrandList selected=$arrForm[$key].value}-->
                </select>
            </td>
        </tr>
    </table>
    <div class="btn">
        <p class="page_rows">検索結果表示件数
        <!--{assign var=key value="search_page_max"}-->
        <!--{if $arrErr[$key]}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
        <!--{/if}-->
        <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
            <!--{html_options options=$arrPageMax selected=$arrForm.search_page_max.value}-->
        </select> 件</p>

        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('search_form', 'search', '', ''); return false;"><span class="btn-next">この条件で検索する</span></a></li>
            </ul>
        </div>

    </div>
    <!--検索条件設定テーブルここまで-->
</form>  


<!--{if count($arrErr) == 0 and ($smarty.post.mode == 'search' or $smarty.post.mode == 'delete')}-->

<!--★★検索結果一覧★★-->
<form name="form1" id="form1" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="search" />
    <input type="hidden" name="product_id" value="" />
    <input type="hidden" name="category_id" value="" />
    <!--{foreach key=key item=item from=$arrHidden}-->
        <!--{if is_array($item)}-->
            <!--{foreach item=c_item from=$item}-->
            <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
            <!--{/foreach}-->
        <!--{else}-->
            <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
        <!--{/if}-->
    <!--{/foreach}-->
    <h2>検索結果一覧</h2>
    <div class="btn">
        <span class="attention"><!--検索結果数--><!--{$tpl_linemax}-->件</span>&nbsp;が該当しました。
        <!--検索結果-->
        <!--{if $smarty.const.ADMIN_MODE == '1'}-->
            <a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('delete_all','',''); return false;">検索結果をすべて削除</a>
        <!--{/if}-->
        <!--{if ($tpl_csv_download_auth == $smarty.const.CSV_DOWNLOAD_AUTH_ON)}-->
        <a class="btn-tool" href="javascript:;" onclick="fnModeSubmit('csv','',''); return false;">CSVダウンロード</a>
        <!--{/if}-->
        <!--{if ($tpl_csv_download_auth == $smarty.const.CSV_DOWNLOAD_AUTH_ON) || ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
        <a class="btn-tool" href="javascript:;" onclick="fnModeSubmit('csv_production','',''); return false;">製作会社向けCSVダウンロード</a>
        <!--{/if}-->
    </div>
    <!--{if count($arrProducts) > 0}-->

        <!--{include file=$tpl_pager}-->

        <!--検索結果表示テーブル-->
        <table class="list" id="products-search-result">
            <colgroup width="6%">
            <colgroup width="8%">
            <colgroup width="34%">
            <colgroup width="20%">
            <colgroup width="20%">
            <colgroup width="6%">
            <colgroup width="6%">
            <tr>
                <th>商品<br />ID</th>
                <th>商品<br />コード</th>
                <th>商品名</th>
                <th>カテゴリ</th>
                <th>ブランド</th>
                <th>編集</th>
                <!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
                <th>規格</th>
                <!--{/if}-->
            </tr>

            <!--{section name=cnt loop=$arrProducts}-->
                <!--▼商品<!--{$smarty.section.cnt.iteration}-->-->
                <!--{assign var=status value="`$arrProducts[cnt].status`"}-->
                <tr style="background:<!--{$arrPRODUCTSTATUS_COLOR[$status]}-->;">
                    <td class="id"><!--{$arrProducts[cnt].product_id}--></td>
                    <td><!--{$arrProducts[cnt].product_code_min|h}-->
                        <!--{if $arrProducts[cnt].product_code_min != $arrProducts[cnt].product_code_max}-->
                            <br />～ <!--{$arrProducts[cnt].product_code_max|h}-->
                        <!--{/if}-->            </td>
                    <td><!--{$arrProducts[cnt].name|h}--></td>
                    <!--{* カテゴリ *}-->
                    <td>
                        <!--{foreach from=$arrProducts[cnt].categories item=category_id name=categories}-->
                            <!--{$arrLastCatList[$category_id]|sfTrim}-->
                            <!--{if !$smarty.foreach.categories.last}-->
						<br />
                            <!--{/if}-->
                        <!--{/foreach}-->
                    </td>
                    <!--{* ブランド *}-->
                    <td><!--{$arrProducts[cnt].brand_name|h}--></td>
                    <td class="menu"><span class="icon_edit"><a href="<!--{$smarty.const.ROOT_URLPATH}-->" onclick="fnChangeAction('./product.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts[cnt].product_id}-->); return false;" >編集</a></span></td>
                    <!--{if $smarty.const.OPTION_CLASS_REGIST == 1}-->
                    <td class="menu"><span class="icon_class"><a href="<!--{$smarty.const.ROOT_URLPATH}-->" onclick="fnChangeAction('./product_class.php'); fnModeSubmit('pre_edit', 'product_id', <!--{$arrProducts[cnt].product_id}-->); return false;" >規格</a></span></td>
                    <!--{/if}-->
                </tr>
                <!--▲商品<!--{$smarty.section.cnt.iteration}-->-->
            <!--{/section}-->
        </table>
        <input type="hidden" name="item_cnt" value="<!--{$arrProducts|@count}-->" />
        <!--検索結果表示テーブル-->
    <!--{/if}-->

</form>

<!--★★検索結果一覧★★-->        
<!--{/if}-->
</div>
