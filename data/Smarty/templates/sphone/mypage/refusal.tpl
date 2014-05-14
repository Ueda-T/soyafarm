<!--▼CONTENTS-->
<section id="mypagecolumn">
	<h2 class="spNaked"><!--{$tpl_title}--></h2>

      <h3 class="title_mypage"><!--{$tpl_subtitle|h}--></h3>
          <form name="form1" method="post" action="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/mypage/refusal.php">
              <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
              <input type="hidden" name="mode" value="confirm" />

      <!--★インフォメーション★-->
      <div class="refusetxt">
          <p>会員を登録削除された場合には、現在保存されている購入履歴や、お届け先などの情報は、すべて削除されますがよろしいでしょうか？</p>
         <div class="btn_area">
              <p><input class="btn data-role-none" type="submit" value="登録削除手続き手続きへ進む" name="refusal" id="refusal" /></p>
         </div>
      </div>
          </form>
</section>
<!--▲CONTENTS-->
