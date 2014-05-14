<!--{* -*- coding: utf-8-unix; -*- *}-->
<script type="text/javascript">
<!--

function func_return(){
    document.form1.mode.value = "return";
    document.form1.submit();
}

//-->
</script>


<form name="form1" id="form1" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="complete" />

    <!--{foreach from=$arrForm key=key item=item}-->
        <!--{if $key ne "mode" && $key ne "subm" && $key ne $smarty.const.TRANSACTION_ID_NAME}-->
            <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
        <!--{/if}-->
    <!--{/foreach}-->

    <!-- 検索条件の保持 -->
    <!--{foreach from=$arrSearchData key="key" item="item"}-->
        <!--{if $key ne "customer_id" && $key ne "mode" && $key ne "edit_customer_id" && $key ne $smarty.const.TRANSACTION_ID_NAME}-->
            <!--{if is_array($item)}-->
                <!--{foreach item=c_item from=$item}-->
                    <input type="hidden" name="search_data[<!--{$key|h}-->][]" value="<!--{$c_item|h}-->" />
                <!--{/foreach}-->
            <!--{else}-->
                <input type="hidden" name="search_data[<!--{$key|h}-->]" value="<!--{$item|h}-->" />
            <!--{/if}-->
        <!--{/if}-->
    <!--{/foreach}-->

    <div id="customer" class="contents-main">
        <table class="form">
            <tr>
                <th>顧客ID</th>
                <td><!--{$arrForm.customer_id|h}--></td>
            </tr>
            <tr>
                <th>顧客名</th>
                <td><!--{$arrForm.name|h}--></td>
            </tr>
            <tr>
                <th>顧客名(カナ)</th>
                <td><!--{$arrForm.kana|h}--></td>
            </tr>
            <tr>
                <th>住所</td>
                <td>
					〒 <!--{$arrForm.zip|h}--><br />
					<!--{$arrPref[$arrForm.pref]|h}--><br />
					<!--{$arrForm.addr01|h}--><br />
					<!--{$arrForm.addr02|h}-->
				</td>
            </tr>
            <tr>
                <th>メールアドレス</th>
                <td><!--{$arrForm.email|h}--></td>
            </tr>
            <tr>
                <th>TEL</th>
                <td><!--{$arrForm.tel|h}--></td>
            </tr>
            <tr>
                <th>性別</th>
                <td><!--{if strlen($arrSex[$arrForm.sex]) == 0}-->未登録<!--{else}--><!--{$arrSex[$arrForm.sex]|h}--><!--{/if}--></td>
            </tr>
            <tr>
                <th>生年月日</th>
                <td><!--{if strlen($arrForm.year) > 0 && strlen($arrForm.month) > 0 && strlen($arrForm.day) > 0}--><!--{$arrForm.year|h}-->年<!--{$arrForm.month|h}-->月<!--{$arrForm.day|h}-->日<!--{else}-->未登録<!--{/if}--></td>
            </tr>
            <tr>
                <th>パスワード</th>
                <td><!--{$smarty.const.DEFAULT_PASSWORD}--></td>
            </tr>
            <tr>
                <th>メールマガジン</th>
                <td><!--{$arrMailMagazineType[$arrForm.mailmaga_flg]|h}--></td>
            </tr>
            <!--{if $smarty.const.USE_POINT !== false}-->
            <tr>
                <th>ポイント</td>
                <td><!--{$arrForm.point|default:"0"|h}--> pt</td>
            </tr>
            <!--{/if}-->

            <tr>
                <th>最終ログイン日時</th>
                <td><!--{$arrForm.lastlogin_date|h}--></td>
            </tr>
            <tr>
                <th>登録日時</th>
                <td><!--{$arrForm.create_date|h}--></td>
            </tr>
            <tr>
                <th>最終更新日時</th>
                <td><!--{$arrForm.update_date|h}--></td>
            </tr>
            <tr>
                <th>基幹顧客番号</th>
                <td><!--{$arrForm.customer_cd|h}--></td>
            </tr>
            <tr>
                <th>顧客区分</th>
                <td><!--{$arrCustomerKbn[$arrForm.customer_kbn]|h}--></td>
            </tr>
            <tr>
                <th>償却顧客区分</th>
                <td><!--{$arrKashidaoreKbn[$arrForm.kashidaore_kbn]|h}--></td>
            </tr>
        </table>
        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="func_return(); return false;"><span class="btn-prev">編集画面に戻る</span></a></li>
                <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
                <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'complete', '', ''); return false;"><span class="btn-next">この内容で登録する</span></a></li>
                <!--{/if}-->
            </ul>
        </div>
    </div>
</form>
