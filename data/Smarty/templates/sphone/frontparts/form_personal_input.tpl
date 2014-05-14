 <dt><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" />お名前&nbsp;</dt>
  <dd>
  <!--{assign var=key1 value="`$prefix`name"}-->
    <!--{if $arrErr[$key1]}-->
    <div class="attention"><!--{$arrErr[$key1]}--></div>
    <!--{/if}-->
    <input type="text" id="userName" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" class="boxLong text data-role-none" placeholder="お名前" />
  </dd>

 <dt><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" />お名前(フリガナ)&nbsp;</dt>
  <dd>
  <!--{assign var=key1 value="`$prefix`kana"}-->
    <!--{if $arrErr[$key1]}-->
    <div class="attention"><!--{$arrErr[$key1]}--></div>
    <!--{/if}-->
    <input type="text"id="userFurigana" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" class="boxLong text data-role-none" placeholder="お名前(フリガナ)"/>
  </dd>

 <dt><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" />郵便番号&nbsp;</dt>
   <dd>
  <!--{assign var=key value="`$prefix`zip"}-->
     <!--{assign var=key3 value="`$prefix`pref"}-->
     <!--{assign var=key4 value="`$prefix`addr01"}-->
     <!--{assign var=key5 value="`$prefix`addr02"}-->
     <!--{if $arrErr[$key]}-->
     <div class="attention"><!--{$arrErr[$key]}--></div>
     <!--{/if}-->

     <p class="top">〒&nbsp;<input type="text" name="<!--{$key}-->" id="<!--{$key}-->" value="<!--{$arrForm[$key]|h}-->" maxlength="<!--{math equation="a+b" a=$smarty.const.ZIP_LEN b=1}-->" size="<!--{$smarty.const.ZIP_LEN+2}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;<img src="<!--{$TPL_URLPATH}-->img/rohto/zip.gif" alt="住所自動入力" id="easy" /></p>
        <p>郵便番号をご入力後、ボタンを押してください。ご住所が自動で入力されます。<br />
        [<a href="http://search.post.japanpost.jp/zipcode/" target="_blank"><span class="mini">郵便番号検索ページヘ</span></a>]</p>
   </dd>

 <dt><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" />住所&nbsp;</dt>
   <dd>
  <!--{if $arrErr[$key3] || $arrErr[$key4] || $arrErr[$key5]}-->
     <div class="attention"><!--{$arrErr[$key3]}--><!--{$arrErr[$key4]}--><!--{$arrErr[$key5]}--></div>
     <!--{/if}-->
        <select name="<!--{$key3}-->" id="pref" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->">
                <option value="" selected="selected">都道府県を選択</option>
                <!--{html_options options=$arrPref selected=$arrForm[$key3]}-->
        </select><br />
        <p class="top"><div id="addr1-div"><input type="text" name="<!--{$key4}-->" id="addr1" value="<!--{$arrForm[$key4]|h}-->" maxlength="40" style="<!--{$arrErr[$key4]|sfGetErrorColor}-->; ime-mode: active;" />
        <span id="addr1-navi">制限文字数を超えています</span></div>
            <span class="example"><!--{$smarty.const.SAMPLE_ADDRESS1}--></span></p>
        <p class="top"><input type="text" name="<!--{$key5}-->" id="addr2" value="<!--{$arrForm[$key5]|h}-->" maxlength="40" style="<!--{$arrErr[$key5]|sfGetErrorColor}-->; ime-mode: active;" /><label><input type="checkbox" name="house_no" id="house_no" />番地なし</label><br />
        <span id="addr2-navi">制限文字数を超えています</span></p>
            <span class="example"><!--{$smarty.const.SAMPLE_ADDRESS2}--></span>
        <p>番地が必要のないご住所の場合、「番地なし」にチェックを付けてください。</p>
   </dd>
  
  <dt><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" />電話番号&nbsp;</dt>
   <dd>
    <!--{assign var=key1 value="`$prefix`tel"}-->
     <!--{if $arrErr[$key1]}-->
     <div class="attention"><!--{$arrErr[$key1]}--></div>
     <!--{/if}-->
     <input type="text" id="tel" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN*3}-->" class="boxLong text data-role-none" />
   </dd>
 <!--{if $flgFields > 1}-->

<dt><img src="<!--{$TPL_URLPATH}-->img/rohto/spacer.gif" alt="" width="31" height="13" />FAX</dt>
   <dd>
   <!--{assign var=key1 value="`$prefix`fax"}-->
     <!--{if $arrErr[$key1]}-->
     <div class="attention"><!--{$arrErr[$key1]}--></div>
     <!--{/if}-->
       <input type="tel" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" maxlength="<!--{$smarty.const.TEL_LEN}-->" class="boxLong text data-role-none" />
   </dd>

  <dt><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" />メールアドレス&nbsp;</dt>
   <dd>
  <!--{assign var=key1 value="`$prefix`email"}-->
     <!--{assign var=key2 value="`$prefix`email02"}-->
     <!--{if $arrErr[$key1] || $arrErr[$key2]}-->
     <div class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></div>
     <!--{/if}-->
       <input type="email" name="<!--{$key1}-->" value="<!--{$arrForm[$key1]|h}-->" class="boxLong text top data-role-none" />
       <input type="email" name="<!--{$key2}-->" value="<!--{$arrForm[$key2]|h}-->" class="boxLong text data-role-none" placeholder="確認のため2回入力してください" />
    </dd>

  <dt><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" />性別&nbsp;</dt>
   <dd>
   <!--{assign var=key1 value="`$prefix`sex"}-->
     <!--{if $arrErr[$key1]}-->
     <div class="attention"><!--{$arrErr[$key1]}--></div>
     <!--{/if}-->
     <span style="<!--{$arrErr[$key1]|sfGetErrorColor}-->">
      <p><input type="radio" id="man" name="<!--{$key1}-->" value="1" <!--{if $arrForm[$key1] eq 1}--> checked="checked" <!--{/if}--> class="data-role-none" /><label for="man">男性</label>&nbsp;&nbsp;
    <input type="radio" id="woman" name="<!--{$key1}-->" value="2" <!--{if $arrForm[$key1] eq 2}--> checked="checked" <!--{/if}--> class="data-role-none" /><label for="woman">女性</label></p>
   </dd>
     </span>
 
  <dt><img src="<!--{$TPL_URLPATH}-->img/rohto/spacer.gif" alt="" width="31" height="13" />生年月日</dt>
   <dd>
   <!--{assign var=errBirth value="`$arrErr.year``$arrErr.month``$arrErr.day`"}-->
     <!--{if $errBirth}-->
     <div class="attention"><!--{$errBirth}--></div>
     <!--{/if}-->
     <select name="year" style="<!--{$errBirth|sfGetErrorColor}-->" class="boxShort data-role-none">
   <!--{html_options options=$arrYear selected=$arrForm.year|default:''}-->
     </select><span class="selectdate">年</span>
     <select name="month" style="<!--{$errBirth|sfGetErrorColor}-->" class="boxShort data-role-none">
       <!--{html_options options=$arrMonth selected=$arrForm.month|default:''}-->
     </select><span class="selectdate">月</span>
     <select name="day" style="<!--{$errBirth|sfGetErrorColor}-->" class="boxShort data-role-none">
       <!--{html_options options=$arrDay selected=$arrForm.day|default:''}-->
     </select><span class="selectdate">日</span>
   </dd>
<!--{if $flgFields > 2}-->
  
<dt><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" />希望するパスワード&nbsp;</dt>
   <dd>
  <!--{if $arrErr.password || $arrErr.password02}-->
     <div class="attention"><!--{$arrErr.password}--><!--{$arrErr.password02}--></div>
     <!--{/if}-->
     <input type="password" name="password" value="<!--{$arrForm.password|h}-->" maxlength="<!--{$smarty.const.PASSWORD_MAX_LEN}-->" style="<!--{$arrErr.password|sfGetErrorColor}-->" class="boxLong text top data-role-none" />
     <input type="password" name="password02" value="<!--{$arrForm.password02|h}-->" maxlength="<!--{$smarty.const.PASSWORD_MAX_LEN}-->" style="<!--{$arrErr.password|cat:$arrErr.password02|sfGetErrorColor}-->" class="boxLong text data-role-none" placeholder="確認のため2回入力してください" />
     <p class="attention mini">半角<!--{$smarty.const.PASSWORD_MIN_LEN}-->～<!--{$smarty.const.PASSWORD_MAX_LEN}-->文字</p>
   </dd>

  <dt><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" />パスワードを忘れた時のヒント&nbsp;</dt>
   <dd>
   <!--{if $arrErr.reminder || $arrErr.reminder_answer}-->
     <div class="attention"><!--{$arrErr.reminder}--><!--{$arrErr.reminder_answer}--></div>
     <!--{/if}-->
     <select name="reminder" style="<!--{$arrErr.reminder|sfGetErrorColor}-->" class="boxLong top data-role-none">
      <option value="" selected="selected">質問を選択してください</option>
      <!--{html_options options=$arrReminder selected=$arrForm.reminder}-->
     </select>

      <input type="text" name="reminder_answer" value="<!--{$arrForm.reminder_answer|h}-->" class="boxLong text data-role-none" placeholder="質問の答えを入力してください" />
   </dd>

   <dt><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" />メールマガジン&nbsp;</dt>
    <dd>
   <ul>
   <!--{if $arrErr.mailmaga_flg}-->
       <div class="attention"><!--{$arrErr.mailmaga_flg}--></div>
       <!--{/if}-->
       <span style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->">
       <li><input type="radio" name="mailmaga_flg" value="1" id="mailmaga_flg_1" <!--{if $arrForm.mailmaga_flg eq '1'}--> checked="checked" <!--{/if}--> class="data-role-none" /><label for="mailmaga_flg_1">受け取る</label></li>
       <li><input type="radio" name="mailmaga_flg" value="0" id="mailmaga_flg_0" <!--{if $arrForm.mailmaga_flg eq '0'}--> checked="checked" <!--{/if}--> class="data-role-none" /><label for="mailmaga_flg_0">受け取らない</label></li>
</span>
     </ul>
    </dd>

   <dt><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" />DM送付について&nbsp;</dt>
   <dd>
   <ul>
       <!--{if $arrErr.dm_flg}-->
       <div class="attention"><!--{$arrErr.dm_flg}--></div>
       <!--{/if}-->
       <span style="<!--{$arrErr.dm_flg|sfGetErrorColor}-->">
          <li><input type="radio" name="dm_flg" value="1" id="dm_flg_1" <!--{if $arrForm.dm_flg eq '1'}--> checked="checked" <!--{/if}--> class="data-role-none" /><label for="dm_flg_1">受け取る</label></li>
          <li><input type="radio" name="dm_flg" value="0" id="dm_flg_0" <!--{if $arrForm.dm_flg eq '0'}--> checked="checked" <!--{/if}--> class="data-role-none" /><label for="dm_flg_0">受け取らない</label></li>
       </span>
   </ul>
   </dd>

   <dt><img src="<!--{$TPL_URLPATH}-->img/rohto/check02.gif" alt="必須" />アンケートについて&nbsp;</dt>
   <dd>
   <ul>
                当サイトをどこで知りましたか？
                <br />
                <!--{if $arrErr.questionnaire}-->
                    <div class="attention"><!--{$arrErr.questionnaire}--></div>
                <!--{/if}-->
                <select name="questionnaire" style="<!--{$arrErr.questionnaire|sfGetErrorColor}-->" onChange="fnChangeQuestionnaire(); return false;" class="boxLong top data-role-none">
                    <option value="" selected="selected">選択してください</option>
                    <!--{html_options options=$arrQuestionnaire selected=$arrForm.questionnaire}-->
                </select>
                <br />
                <div id="questionnaire_other_text"></div>
                <!--{if $arrErr.questionnaire_other}-->
                    <div class="attention"><!--{$arrErr.questionnaire_other}--></div>
                <!--{/if}-->
                <textarea name="questionnaire_other" style="<!--{$arrErr.questionnaire_other|sfGetErrorColor}-->" cols="70" rows="8" class="boxLong text data-role-none txtarea" wrap="soft"><!--{$arrForm.questionnaire_other|h}--></textarea>
   </ul>
   </dd>
<!--{/if}-->
<!--{/if}-->
