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

<h2 class="spNaked"><img src="<!--{$TPL_URLPATH}-->img/rohto/icon_login.gif" width="19" height="16"><!--{$tpl_title|h}--></h2>
	<p class="naked" style="margin-bottom:10px;">ここから先はﾛｸﾞｲﾝ､または会員登録が必要です｡</p>

		<p class="naked" style="margin-bottom:10px;">
		*過去にPCｻｲﾄから登録をされている場合､PCｻｲﾄで登録したﾒｰﾙｱﾄﾞﾚｽをご入力ください｡<br>
		*ご注文確認時に送られるﾒｰﾙ等は､登録されているﾒｰﾙｱﾄﾞﾚｽにご送付されますので､ご注意ください｡ﾒｰﾙｱﾄﾞﾚｽはﾛｸﾞｲﾝ後の｢ﾏｲﾍﾟｰｼﾞ｣より変更を承っています｡
		</p>


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
                                       <input type="submit" value="ログイン" name="log" id="log" class="btnOrange" />
        </div>

		<p style="display:block; margin:18px 0; text-align:right;"><a rel="external" href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->" class="btnGray">パスワードを忘れた方はこちら</a></p>






<div class="wrapPW20140317">
<table cellspacing="0" bgcolor="#a10000" width="100%">
<tr>
<td background="#a10000" bgcolor="#a10000"><font size="-1" color="#ffffff">【ロート通販からの重要なお知らせ】</font></td>
</tr>
</table>
  
<font size="-1" color="#a10000">ロート通販オンラインショップにご登録済みで、2014年3月17日（月）以降に初めてご利用のお客さまへ</font>
<br>
<font size="-1">2014年3月17日（月）に、ロート通販オンラインショップのシステムはリニューアルいたしました。リニューアルに伴いまして、お客さまには大変お手数をおかけいたしますが、パスワードの再発行をお願いいたします。
<a href="<!--{$smarty.const.ROOT_URLPATH}-->renewal/">パスワードの再発行はこちらから手続きを開始</a>していただけます。</font>
<br>
<font size="-1" color="#a10000">●パスワードを再発行済のお客さまは、ログインボタンからログインしてください。<br>
●ロート通販オンラインショップでのお買い物が初めてのお客さまは「<a href="<!--{$smarty.const.ROOT_URLPATH}-->contents/welcome.php">はじめての方へ</a>」にお進みください。</font><br>
<br>
<table width="100%" cellspacing="0" bgcolor="#2490D7">
<tr>
<td bgcolor="#2490D7"><font size="-1" color="#ffffff">■パスワード再発行の流れ～パスワードの再発行がお済みでないお客さまへ～</font></td>
</tr>
</table>
<br>
<table cellspacing="0">
<tr>
<td style="width:35px;"><img src="../image/pw20140317/step1.gif" width="30" height="36" alt="1"/></td>
<td><font size="-1"><a href="<!--{$smarty.const.ROOT_URLPATH}-->renewal/">こちら</a>から再発行の画面に進んでください。</font></td>
</tr>
</table>
<div style="text-align:center;"><center><img src="../image/pw20140317/arrow_next.gif" width="50" height="20" alt=""/></center></div>
<table cellspacing="0">
<tr>
<td style="width:35px;"><img src="../image/pw20140317/step2.gif" width="30" height="36" alt="2"/></td>
<td><font size="-1">表示された画面の案内に沿って、ご登録済のメールアドレスおよびご登録済のお名前（漢字氏名）を入力して、「次へ」進んでください。</font></td>
</tr>
</table>
<div style="text-align:center;"><center><img src="../image/pw20140317/arrow_next.gif" width="50" height="20" alt=""/></center></div>
<table cellspacing="0">
<tr>
<td style="width:35px;"><img src="../image/pw20140317/step3.gif" width="30" height="36" alt="3"/></td>
<td><font size="-1">「パスワードの再発行が完了しました」というメッセージが画面に表示されるとともに、パスワード変更通知のメール（件名：【ロート製薬】 パスワードを変更いたしました。）をお届けします。その中に初回ログイン用のパスワードが書かれているのをご確認ください。万が一メールが届かない場合は<a href="<!--{$smarty.const.ROOT_URLPATH}-->contact/">こちらへお問い合わせ</a>ください。</font></td>
</tr>
</table>
<div style="text-align:center;"><center><img src="../image/pw20140317/arrow_next.gif" width="50" height="20" alt=""/></center></div>
<table cellspacing="0">
<tr>
<td style="width:35px;"><img src="../image/pw20140317/step4.gif" width="30" height="36" alt="4"/></td>
<td><font size="-1">パスワード変更通知のメール（件名：【ロート製薬】 パスワードを変更いたしました。）内に書かれていたパスワードを用いて、マイページにログインしてください。</font></td>
</tr>
</table>
<div style="text-align:center;"><center><img src="../image/pw20140317/arrow_next.gif" width="50" height="20" alt=""/></center></div>
<table cellspacing="0">
<tr>
<td style="width:35px;"><img src="../image/pw20140317/step5.gif" width="30" height="36" alt="5"/></td>
<td><font size="-1">マイページにログイン後、「メールアドレスとパスワードの変更」メニューより、パスワードの変更をおこなってください。<br>
<font color="#a10000">【重要】 安全のため、届いたパスワードのままにはせず、必ずご自身で変更してください。</font></font></td>
</tr>
</table>
<br>
<table width="100%" cellspacing="0" bgcolor="#2490D7">
<tr>
<td bgcolor="#2490D7"><font size="-1" color="#ffffff">■安全にご利用いただくためのお願い～パスワードの再発行がお済みのお客さまへ～</font></td>
</tr>
</table>
<font size="-1" color="#2490D7">安全のため、パスワード変更通知のメール（件名：【ロート製薬】 パスワードを変更いたしました。）で届いたパスワードのままにせず、お客さまご自身でパスワードを改めて設定いただきますようお願い申し上げます。パスワードの変更は、マイページ内の「メールアドレスとパスワードの変更」画面にておこなっていただけます（上記「STEP 5」）。</font><br>
<br>
<font size="-1">以上で、パスワードの再設定は完了です。ご協力ありがとうございました。引き続きロート通販オンラインショップで、お買い物をお楽しみください。</font>
</div><!--/wrapPW20140317-->






     </div><!--▲loginarea -->
    </form>
 <!--▲コンテンツここまで -->

