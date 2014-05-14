<div id="home">

    <!--{* メインエリア *}-->
    <div id="home-main">
        <form name="form1" method="post" action="#">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />

        <!--{* ショップ情報ここから *}-->
        <h2>ショップ情報</h2>
        <table summary="ショップ情報" class="shop-info">
            <tr>
                <th>現在の顧客数</td>
                <td><!--{$customer_cnt|default:"0"|number_format}-->名</td>
            </tr>
            <tr>
                <th>昨日の売上高</td>
                <td><!--{$order_yesterday_amount|default:"0"|number_format}-->円</td>
            </tr>
            <tr>
                <th>昨日の売上件数</td>
                <td><!--{$order_yesterday_cnt|default:"0"|number_format}-->件</td>
            </tr>
            <tr>
                <th><span>今月の売上高</span><span>(昨日まで) </span></td>
                <td><!--{$order_month_amount|default:"0"|number_format}-->円</td>
            </tr>
            <tr>
                <th><span>今月の売上件数 </span><span>(昨日まで) </span></td>
                <td><!--{$order_month_cnt|default:"0"|number_format}-->件</td>
            </tr>
        </table>
        <!--{* ショップの状況ここまで *}-->

        <!--{* 新規受注ここから *}-->
        <h2>新規受注</h2>
        <table summary="新規受付一覧" id="home-order">
            <tr>
                <th class="center">受注日</th>
                <th class="center">お名前</th>
                <th class="center">購入商品</th>
                <th class="center">支払方法</th>
                <th class="center">購入金額(円)</th>
            </tr>
            <!--{section name=i loop=$arrNewOrder}-->
            <tr>
                <td><!--{$arrNewOrder[i].create_date}--></td>
                <td><!--{$arrNewOrder[i].order_name|h}--></td>
                <td><!--{$arrNewOrder[i].product_name|h}--></td>
                <td><!--{$arrNewOrder[i].payment_method|h}--></td>
                <td class="right"><!--{$arrNewOrder[i].total|number_format}-->円</td>
            </tr>
            <!--{/section}-->
        </table>
        <!--{* 新規受付一覧ここまで *}-->

        </form>
    </div>
    <!--{* メインエリア *}-->

</div>
<!--{* ▲CONTENTS *}-->
