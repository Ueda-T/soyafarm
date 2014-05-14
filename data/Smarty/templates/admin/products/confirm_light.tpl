<form name="form1" id="form1" method="post" action="?" enctype="multipart/form-data">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="complete" />
    <!--{foreach key=key item=item from=$arrForm}-->
        <!--{if $key == 'product_status'}-->
            <!--{foreach item=statusVal from=$item}-->
            <input type="hidden" name="<!--{$key}-->[]" value="<!--{$statusVal|h}-->" />
            <!--{/foreach}-->
        <!--{else}-->
            <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
        <!--{/if}-->
    <!--{/foreach}-->
    <div id="products" class="contents-main">

    <table>
        <tr>
            <th>商品名</th>
            <td>
            <!--{$arrForm.name|h}-->
            </td>
        </tr>
        <tr>
            <th>商品カテゴリ</th>
            <td>
            <!--{section name=cnt loop=$arrForm.arrCategoryId}-->
                <!--{assign var=key value=$arrForm.arrCategoryId[cnt]}-->
                <!--{$arrCatList[$key]|sfTrim}--><br />
            <!--{/section}-->
            </td>
        </tr>
        <tr>
            <th>公開・非公開</th>
            <td>
            <!--{$arrDISP[$arrForm.status]}-->
            </td>
        </tr>
        <tr>
            <th>商品ステータス</th>
            <td>
            <!--{foreach from=$arrForm.product_status item=status}-->
                <!--{if $status != ""}-->
                    <img src="<!--{$TPL_URLPATH_DEFAULT}--><!--{$arrSTATUS_IMAGE[$status]}-->">
                <!--{/if}-->
            <!--{/foreach}-->
            </td>
        </tr>
        <tr>
            <th>ブランド</th>
            <td>
                <!--{$arrBRAND[$arrForm.brand_kbn]}-->
            </td>
        </tr>

        <!--{if $arrForm.has_product_class != true}-->
            <tr>
                <th>商品コード</th>
                <td>
                <!--{$arrForm.product_code|h}-->
                </td>
            </tr>
            <tr>
                <th><!--{$smarty.const.NORMAL_PRICE_TITLE}--></th>
                <td>
                <!--{$arrForm.price01|h}-->
                円</td>
            </tr>
            <tr>
                <th><!--{$smarty.const.SALE_PRICE_TITLE}--></th>
                <td>
                <!--{$arrForm.price02|h}-->
                円</td>
            </tr>
            <tr>
                <th>在庫数</th>
                <td>
                <!--{if $arrForm.stock_unlimited == 1}-->
                    無制限
                <!--{else}-->
                    <!--{$arrForm.stock|h}-->
                <!--{/if}-->
                </td>
            </tr>
        <!--{/if}-->
        <!--{if $smarty.const.USE_POINT}-->
        <tr>
            <th>ポイント付与率</th>
            <td>
            <!--{$arrForm.point_rate|h}-->
            ％</td>
        </tr>
        <!--{/if}-->
        <tr>
            <th>発送日目安</th>
            <td>
            <!--{$arrDELIVERYDATE[$arrForm.deliv_date_id]|h}-->
            </td>
        </tr>
        <tr>
            <th>一覧-メインコメント</th>
            <td>
            <!--{$arrForm.main_list_comment}-->
            </td>
        </tr>
        <tr>
            <th>詳細-メインコメント</th>
            <td>
            <!--{$arrForm.main_comment}-->
            </td>
        </tr>
        <tr>
            <th>一覧-メイン画像</th>
            <td>
            <!--{assign var=key value="main_list_image"}-->
            <!--{if $arrForm.arrFile[$key].filepath != ""}-->
            <img src="<!--{$arrForm.arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" /><br />
            <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>詳細-メイン画像</th>
            <td>
            <!--{assign var=key value="main_image"}-->
            <!--{if $arrForm.arrFile[$key].filepath != ""}-->
            <img src="<!--{$arrForm.arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" /><br />
            <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>詳細-メイン拡大画像</th>
            <td>
            <!--{assign var=key value="main_large_image"}-->
            <!--{if $arrForm.arrFile[$key].filepath != ""}-->
            <img src="<!--{$arrForm.arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" /><br />
            <!--{/if}-->
            </td>
        </tr>
    </table>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnModeSubmit('confirm_return','',''); return false;"><span class="btn-prev">前のページに戻る</span></a></li>
            <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
            <li><a class="btn-action" href="javascript:;" onclick="document.form1.submit(); return false;"><span class="btn-next">この内容で登録する</span></a></li>
            <!--{/if}-->
        </ul>
    </div>
</div>
</form>
