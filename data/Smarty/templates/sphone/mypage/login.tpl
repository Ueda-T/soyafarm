<!--▼CONTENTS-->
<script>
  function ajaxLogin() {
      var postData = new Object;
      postData['<!--{$smarty.const.TRANSACTION_ID_NAME}-->'] = "<!--{$transactionid}-->";
      postData['mode'] = 'login';
      postData['login_email'] = $('input[type=email]').val();
      postData['login_pass'] = $('input[type=password]').val();
      postData['url'] = $('input[name=url]').val();

      $.ajax({
          type: "POST",
          url: "<!--{$smarty.const.HTTPS_URL}-->frontparts/login_check.php",
          data: postData,
          cache: false,
          dataType: "json",
          error: function(XMLHttpRequest, textStatus, errorThrown){
              alert(textStatus);
          },
          success: function(result){
              if (result.success) {
                  location.href = result.success;
              } else {
                  alert(result.login_error);
              }
          }
      });
  }
</script>
<section>

<h2 class="spNaked"><!--{$tpl_title|h}--></h2>
	<p class="naked" style="margin-bottom:10px;">ここから先はﾛｸﾞｲﾝ､または会員登録が必要です｡</p>

<div class="attentionBox">
	<h3 class="tit">重要なお知らせ</h3>
	<p>
		2014年7月1日のシステムリニューアルに伴い、以前ご利用のお客様には初回ログイン時にパスワードの再発行が必要になります。<br />
		お手数をおかけいたしますが、下記のリンクからパスワード再発行手続きを行ってください。
	</p>
	<p class="alignC">
		<a href="<!--{$smarty.const.HTTPS_URL}-->renewal/" class="btn imp">
			パスワードを再発行する
		</a>
	</p>
</div>

     <form name="login_mypage" id="login_mypage" method="post" action="javascript:;" onsubmit="return ajaxLogin();">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="login" />
        <input type="hidden" name="url" value="<!--{$smarty.server.REQUEST_URI|h}-->" />

<div>

    <div>
          <!--{assign var=key value="login_email"}-->
               <span class="attention"><!--{$arrErr[$key]}--></span>
                  <input type="email" name="<!--{$key}-->" value="<!--{$tpl_login_email|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="width:100%; <!--{$arrErr[$key]|sfGetErrorColor}-->" class="mailtextBox data-role-none" placeholder="メールアドレス" />

          <!--{assign var=key value="login_pass"}-->
               <span class="attention"><!--{$arrErr[$key]}--></span>
                  <input type="password" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" style="width:100%; <!--{$arrErr[$key]|sfGetErrorColor}-->" class="passtextBox data-role-none" placeholder="パスワード" />

</div><!--▲loginBox -->
        <div style="margin:10px 0;">
                                       <input type="submit" value="ログイン" name="log" id="log" class="btnBlue" />
        </div>

		<p style="display:block; margin:18px 0; text-align:right;"><a rel="external" href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->" class="btnGray">パスワードを忘れた方はこちら</a></p>

     </div><!--▲loginarea -->
    </form>
 <!--▲コンテンツここまで -->

