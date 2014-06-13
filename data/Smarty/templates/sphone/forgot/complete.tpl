<!--{*<!--{include file="`$smarty.const.SMARTPHONE_TEMPLATE_REALDIR`popup_header.tpl" subtitle="パスワードを忘れた方(完了ページ)"}-->*}-->

  <section id="windowcolumn">
   <h2 class="spNaked">パスワードを忘れた方</h2>
   <div class="intro">
     <p class="naked">パスワードの発行が完了いたしました。<br />ご入力いただいたメールアドレスに新しいパスワードをお送りしておりますので、ご確認くださいませ。<br />
     ※下記パスワードは、マイページの「メールアドレスとパスワードの変更」よりご変更いただけます。</p>
     </div>
<!--{*
    <form action="?" method="post" name="form1">
         <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />

     <div class="window_area clearfix">
       <!--{if $smarty.const.FORGOT_MAIL != 1}-->
          <input id="completebox" type="text" value="<!--{$arrForm.new_password}-->" readonly="readonly" />
       <!--{else}-->
          <p  class="attention">ご登録メールアドレスに送付致しました。</p>
       <!--{/if}-->
         <hr />
     </div>
*}-->
     <p style="margin:10px;">
       <a rel="external" href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/mypage/login.php" class="btnBlue">ログイン画面へ</a>
     </p>
     </form>    
  </section>

<!--{*<!--{include file="`$smarty.const.SMARTPHONE_TEMPLATE_REALDIR`popup_footer.tpl"}-->*}-->
