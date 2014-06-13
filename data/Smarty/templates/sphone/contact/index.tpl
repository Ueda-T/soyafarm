<!--▼CONTENTS-->
<script>
  $(function() {
      $('#contents')
          .css('font-size', '100%')
          .autoResizeTextAreaQ({
              'max_rows': 50,
              'extra_rows': 0
          });
  });
</script>
<section id="undercolumn">
    <h2 class="spNaked"><!--{$tpl_title|h}--></h2>
    <form name="form1" method="post" action="?">
      <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
       <input type="hidden" name="mode" value="confirm" />

        <dl class="form_entry">
         <dt>件名&nbsp;<img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_hisu.gif" alt="必須" /></dt>
         <dd><span class="attention"><!--{$arrErr.subject}--></span>
            <select name="subject" id="subject" style="<!--{$arrErr.subject|sfGetErrorColor}-->">
                <option value="" selected="selected">↓お選びください</option>
                <!--{html_options options=$arrSubject selected=$arrForm.subject}-->
            </select>
         </dd>
         <dt>お名前&nbsp;<img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_hisu.gif" alt="必須" /></dt>
           <dd><span class="attention"><!--{$arrErr.name}--></span>
             <input type="text" name="name" 
               value="<!--{$arrForm.name.value|default:$arrData.name|h}-->" 
                maxlength="<!--{$smarty.const.STEXT_LEN}-->" 
                 style="<!--{$arrErr.name|sfGetErrorColor}-->" class="boxHarf text data-role-none" placeholder="お名前"/>&nbsp;&nbsp;
              </dd>

             <dt>メールアドレス&nbsp;<img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_hisu.gif" alt="必須" /></dt>
              <dd><span class="attention"><!--{$arrErr.email}--><!--{$arrErr.email02}--></span>
                <input type="email" name="email" 
                 value="<!--{$arrForm.email.value|default:$arrData.email|h}-->" 
                  style="<!--{$arrErr.email|sfGetErrorColor}-->" 
                   maxlength="<!--{$smarty.const.MTEXT_LEN}-->" class="boxLong top text data-role-none" />
              </dd>

          <dt>電話番号&nbsp;<img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_hisu.gif" alt="必須" /></dt>
            <dd><span class="attention"><!--{$arrErr.tel}--></span>
                <input type="tel" name="tel" 
                  value="<!--{$arrForm.tel.value|default:$arrData.tel|h}-->" 
                   maxlength="<!--{$smarty.const.TEL_ITEM_LEN*3}-->" 
                    style="<!--{$arrErr.tel01|sfGetErrorColor}-->" 
                     class="boxShort text data-role-none" />
              </dd>


             <dt>内容&nbsp;<img src="<!--{$TPL_URLPATH}-->img/soyafarm/icon_hisu.gif" alt="必須" /></dt>
              <dd><span class="attention"><!--{$arrErr.contents}--></span>
                <textarea name="contents" id="contents" class="textarea data-role-none" rows="4" cols="30" style="<!--{$arrErr.contents|sfGetErrorColor}-->"><!--{$arrForm.contents.value|h}--></textarea>
              </dd>

         </dl>
         <input type="hidden" name="now" value="<!--{$smarty.now|date_format:'%Y/%m/%d %H:%M:%S'}-->" />
         <div class="btn_area">
             <input type="submit" value="確認ページへ" class="btnBlue" name="confirm" id="confirm" />
         </div>
     </form>
</section>
<!--▲CONTENTS-->
<script src="<!--{$smarty.const.ROOT_URLPATH}-->js/contact.js" type="text/javascript"></script>