<!--▼CONTENTS-->
<section id="mypagecolumn">
	<h2 class="spNaked"><!--{$tpl_title}--></h2>
        <form name="form1" method="post" action="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/mypage/refusal.php">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="refusal_transactionid" value="<!--{$refusal_transactionid}-->" />
            <input type="hidden" name="mode" value="complete" />

     <h3 class="title_mypage"><!--{$tpl_subtitle|h}--></h3>

     <!--★インフォメーション★-->
        <div class="refusetxt">
           <p>登録削除手続きを実行してもよろしいでしょうか？</p>
                                            <ul class="btn_refuse">
              <li><a class="btn" href="./refusal.php" rel="external">いいえ、登録削除しません</a></li>
                       <li><input class="btn data-role-none" type="submit" value="はい、登録削除します" name="refuse_do" id="refuse_do" /></li>
           </ul>
        </div>
         </form>
</section>
<!--{*
<!--▼検索バー -->
<section id="search_area">
<form method="get" action="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="search" name="name" id="search" value="" placeholder="キーワードを入力" class="searchbox" >
</form>
</section>
<!--▲検索バー -->
*}-->
<!--▲CONTENTS-->
