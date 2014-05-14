<section id="windowcolumn">
    <h2 class="spNaked">パスワードを忘れた方</h2>
    <form action="?" method="post" name="form1">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="mail_check" />
       <div class="intro">
         <p class="naked">ご登録時のメールアドレスと、ご登録されたお名前を入力して「次へ」ボタンをクリックしてください。</p>
        <p class="naked attention">【重要】新しくパスワードを発行いたしますので、お忘れになったパスワードはご利用できなくなります。</p>
          </div>
     <div class="window_area clearfix">
        <p>メールアドレス<br />
          <span class="attention"><!--{$arrErr.email}--></span>
          <span class="attention"><!--{$errmsg}--></span>
          <input type="email" name="email" 
            value="<!--{$arrForm.email|default:''|h}-->" 
             style="<!--{$arrErr.email|sfGetErrorColor}-->; ime-mode: disabled;" 
              maxlength="200" class="boxLong data-role-none" placeholder="メールアドレス" /></p>
        <p>お名前<br />
          <span class="attention"><!--{$arrErr.name}--></span>
          <input type="text" name="name" 
            value="<!--{$arrForm.name|default:''|h}-->" 
             maxlength="<!--{$smarty.const.STEXT_LEN}-->" 
              style="<!--{$arrErr.name|sfGetErrorColor}-->; ime-mode: active;" 
                 class="boxHarf text data-role-none" placeholder="お名前"/>&nbsp;&nbsp;

   </div>

        <p class="btn"><input class="btnOrange" type="submit" value="次へ" /></p>
     </form>
</section>

<!--{*<!--{include file="`$smarty.const.SMARTPHONE_TEMPLATE_REALDIR`popup_footer.tpl"}-->*}-->
