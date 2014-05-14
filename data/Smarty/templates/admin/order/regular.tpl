<!--{* -*- coding: utf-8-unix; -*- *}-->
<form name="form1" id="form1" method="post" action="?" enctype="multipart/form-data">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<!--{foreach key=key item=item from=$arrSearchHidden}-->
    <!--{if is_array($item)}-->
        <!--{foreach item=c_item from=$item}-->
        <input type="hidden" name="<!--{$key|h}-->[]" value="<!--{$c_item|h}-->" />
        <!--{/foreach}-->
    <!--{else}-->
        <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
    <!--{/if}-->
<!--{/foreach}-->
<input type="hidden" name="mode" value="" />
<input type="hidden" name="promotion_cd" value="<!--{$arrForm.promotion_cd|h}-->" />
<!--{foreach key=key item=item from=$arrForm.arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
<!--{/foreach}-->
<div id="products" class="contents-main">
    <h2>定期購入情報</h2>
    <table class="form">
        <tr>
            <th>定期番号</th>
            <td><!--{$arrForm.regular_id|h}--></td>
        </tr>
        <tr>
            <th>申込日</th>
            <td><!--{$arrForm.order_date|date_format:"%Y年%m月%d日"|h}--></td>
        </tr>
        <tr>
            <th>基幹定期番号</th>
            <td><!--{$arrForm.regular_base_no|h}--></td>
        </tr>
        <tr>
            <th>支払方法</th>
            <td><!--{$arrPayment[$arrForm.payment_id]}--></td>
        </tr>
        <tr>
            <th>時間帯指定</th>
            <td><!--{$arrDelivTime[$arrForm.time_id]}--></td>
        </tr>
        <tr>
            <th>請求書送付方法</th>
            <td><!--{$arrIncludeKbn[$arrForm.include_kbn]}--></td>
        </tr>
    </table>

    <h2>お客様情報</h2>
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
            <th>基幹顧客番号</th>
            <td><!--{$arrForm.customer_cd|h}--></td>
        </tr>
    </table>

    <h2>定期購入スケジュール</h2>
    <table class="form">
        <tr>
            <th>商品コード</th>
            <td><!--{$arrForm.product_code|h}--></td>
        </tr>
        <tr>
            <th>商品名</th>
            <td><!--{$arrForm.product_name|h}--></td>
        </tr>
        <tr>
            <th>数量</th>
            <td><!--{$arrForm.quantity|h}--></td>
        </tr>
        <tr>
            <th>お届け間隔</th>
            <td><!--{$arrForm.course_cd|h}--><!--{* ○ヶ月ごと *}-->

                <!--{* 第X Y曜日 *}-->
                <!--{if $arrForm.todoke_week != "" && $arrForm.todoke_week2 != ""}-->
                    &nbsp;<!--{$arrTodokeWeekNo[$arrForm.todoke_week]}-->
                    <!--{$arrTodokeWeek[$arrForm.todoke_week2]}-->曜日
                <!--{/if}-->
            </td>
        </tr>
        <tr>
            <th>次回お届け日</th>
            <td><!--{$arrForm.next_arrival_date|date_format:"%Y年%m月%d日"|h}--></td>
        </tr>
        <tr>
            <th>次々回お届け日</th>
            <td><!--{$arrForm.after_next_arrival_date|date_format:"%Y年%m月%d日"|h}--></td>
        </tr>
        <tr>
            <th>終了日</th>
            <td><!--{$arrForm.cancel_date|date_format:"%Y年%m月%d日"|h}--></td>
        </tr>
        <tr>
            <th>状態</th>
            <td><!--{$arrRegularOrderStatus[$arrForm.status]}--></td>
        </tr>
    </table>

    <h2>お届け先情報</h2>
    <table class="form">
        <tr>
            <th>お名前</th>
            <td><!--{$arrForm.order_name|h}--></td>
        </tr>
        <tr>
            <th>お名前(カナ)</th>
            <td><!--{$arrForm.order_kana|h}--></td>
        </tr>
        <tr>
            <th>TEL</th>
            <td><!--{$arrForm.order_tel|h}--></td>
        </tr>
        <tr>
            <th>住所</th>
            <td>〒<!--{$arrForm.order_zip}--><br />
                <!--{$arrPref[$arrForm.order_pref]}--><!--{$arrForm.order_addr01|h}--><!--{$arrForm.order_addr02|h}-->
            </td>
        </tr>
    </table>

    <!--▼メール送信履歴-->
    <h2>メール送信履歴</h2>
    <table class="list">
        <tr>
            <th>処理日</th>
            <th>件名</th>
        </tr>
        <!--{section name=i loop=$arrMailHistory}-->
        <tr class="center">
            <td><!--{$arrMailHistory[i].send_date|sfDispDBDate|h}--></td>
            <!--{assign var=key value="`$arrMailHistory[i].template_id`"}-->
            <td><a href="?" onclick="win02('./regularMailView.php?send_id=<!--{$arrMailHistory[i].send_id}-->','mail_view','650','800'); return false;"><!--{$arrMailHistory[i].subject|h}--></a></td>
        </tr>
        <!--{/section}-->
    </table>
    <!--▲メール送信履歴-->

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnChangeAction('./regular_search.php'); fnModeSubmit('search', '', ''); return false;"><span class="btn-prev">検索画面に戻る</span></a></li>
        </ul>
    </div>
</div>
</form>
