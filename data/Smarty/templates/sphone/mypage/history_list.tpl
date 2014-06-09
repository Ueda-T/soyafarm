<!--▼CONTENTS-->
<section id="mypagecolumn">

	<h2 class="spNaked"><!--{$tpl_title}-->/<!--{$tpl_subtitle|h}--></h2>

   <form name="form1" id="form1" method="post" action="?">
      <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
      <input type="hidden" name="order_id" value="" />
      <input type="hidden" name="pageno" value="<!--{$objNavi->nowpage}-->" />

        <!--{if $objNavi->all_row > 0}-->
        <div class="pagenumber_area regularPage">
            <ul class="navi">
            <!--▼ページナビ-->
            <!--{$objNavi->strnavi}-->
            <!--▲ページナビ-->
            </ul>
        </div>

        <!--★インフォメーション★-->
        <div class="information">
           <p><span class="attention"><span id="historycount"><!--{$objNavi->all_row}--></span>件</span>のご注文履歴があります。</p>
        </div>

        <div class="form_area">

        <!--▼フォームボックスここから -->
        <div class="formBox">
           <!--{section name=cnt loop=$arrOrder max=$dispNumber}-->
             <!--▼商品 -->
             <div class="arrowBox">
              <p>
                 <em>注文番号：</em><span class="order_id"><!--{$arrOrder[cnt].order_id}--><!--{assign var=payment_id value="`$arrOrder[cnt].payment_id`"}--></span><br />
                 <em>購入日時：</em><span class="create_date"><!--{$arrOrder[cnt].create_date|sfDispDBDate}--></span><br />
<!--{*
                 <em>お支払い方法：</em><span class="payment_id"><!--{$arrPayment[$payment_id]|h}--></span><br />
*}-->
                 <em>合計金額：</em><span class="payment_total"><!--{$arrOrder[cnt].payment_total|number_format}--></span>円
              </p>
              <a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/history.php?order_id=<!--{$arrOrder[cnt].order_id}-->" rel="external"></a>
             </div>
             <!--▲商品 -->
           <!--{/section}-->
       </div><!--▲formBox -->
       </div><!--▲form_area-->

        <div class="pagenumber_area regularPage">
            <ul class="navi">
            <!--▼ページナビ-->
            <!--{$objNavi->strnavi}-->
            <!--▲ページナビ-->
            </ul>
        </div>

       <!--{else}-->
    <div class="form_area">
       <div class="information">
           <p>ご注文履歴はありません。</p>
       </div>
    </div><!--▲form_area-->
        <!--{/if}-->
   </form>

</section>
