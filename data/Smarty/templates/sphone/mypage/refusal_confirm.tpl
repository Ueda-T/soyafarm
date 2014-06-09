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
			<p><input class="btn_back data-role-none" type="submit" value="登録削除" name="refuse_do" id="refuse_do" /></p>
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
