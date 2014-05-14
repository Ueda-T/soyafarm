<section id="mypagecolumn">
	<h2 class="spNaked"><!--{$tpl_title}--></h2>

  <h3 class="title_mypage">定期購入一覧</h3>

   <form name="form1" id="form1" method="post" action="?">
      <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
      <input type="hidden" name="regular_id" value="" />
	  <input type="hidden" name="line_no" value="" />
      <input type="hidden" name="pageno" value="<!--{$objNavi->nowpage}-->" />

        <div class="cartList">
        <!--{if $objNavi->all_row > 0}-->
            <div class="pagenumber_area regularPage">
                <ul class="navi">
                <!--▼ページナビ-->
                <!--{$objNavi->strnavi}-->
                <!--▲ページナビ-->
                </ul>
            </div>

        <div class="form_area">
        <!--▼フォームボックスここから -->
        <div class="formBox">

           <!--{section name=cnt loop=$arrRegularDetail max=$dispNumber}-->
             <!--▼商品 -->
             <div class="arrowBox">
              <p>
                 <em>商品名：</em><span class="product_name"><!--{$arrRegularDetail[cnt].product_name}--></span><br />
                 <em>数量：</em><span class="quantity"><!--{$arrRegularDetail[cnt].quantity}--></span><br />
                 <em>お届け間隔：</em>
                    <span class="todoke_kankaku">
                    <!--{if $arrRegularDetail[cnt].course_cd >= $smarty.const.COURSE_CD_DAY_MIN}-->
                        <!--{$arrRegularDetail[cnt].course_cd|h}-->日ごと
                    <!--{else}-->
                        <!--{$arrRegularDetail[cnt].course_cd|h}-->ヶ月ごと
                    <!--{/if}-->
                    </span>
                    <br />
                 <em>　次回お届け日：</em><!--{$arrRegularDetail[cnt].next_arrival_date|date_format:"%Y年%m月%d日"|h}--></span><br />
                 <em>次々回お届け日：</em><!--{$arrRegularDetail[cnt].after_next_arrival_date|date_format:"%Y年%m月%d日"|h}--><br /><br />
                    <span class="regular_change">
                    <!--{** 次回お届け日が未定は変更不可 **}-->
					<!--{if $arrRegularDetail[cnt].next_arrival_date == ""}-->
					次回お届け日が未定のため<br />変更できません。

					<!--{** 次回お届け日の1週間以内は変更不可 **}-->
					<!--{elseif !$arrRegularDetail[cnt].disp_flg}-->
					只今、出荷準備中のため<br />変更できません。

					<!--{** 「6：休止中」は変更不可 **}-->
					<!--{elseif $arrRegularDetail[cnt].status == $smarty.const.REGULAR_ORDER_STATUS_PAUSE}-->
					休止中のため<br />変更できません。

					<!--{else}-->
                             <a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/regular_change.php?regular_id=<!--{$arrRegularDetail[cnt].regular_id}-->&line_no=<!--{$arrRegularDetail[cnt].line_no}-->">お届けスケジュールの変更</a>
					<!--{/if}-->
                    </span>
              </p>
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
           <p>定期購入情報はありません。</p>
       </div>
    </div><!--▲form_area-->

<!--{/if}-->
                </form>

</section>
