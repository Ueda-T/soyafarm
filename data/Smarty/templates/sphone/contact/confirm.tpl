<script type="text/javascript">//<![CDATA[
var send = true;

function fnCheckSubmit() {
    if(send) {
        send = false;
        document.form1.submit();
        return true;
    } else {
        alert("只今、処理中です。しばらくお待ち下さい。");
        return false;
    }
}
//]]></script>
<!--▼CONTENTS-->
<section id="undercolumn">
       <!--☆お問い合わせ内容確認 -->
       <h2 class="spNaked"><!--{$tpl_title|h}--></h2>

          <form name="form1" id="form1" method="post" action="?">
              <input type="hidden" name="token" value="<!--{$token}-->" />
              <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
              <input type="hidden" name="mode" value="complete" />
              <!--{foreach key=key item=item from=$arrForm}-->
              <!--{if $key ne 'mode'}-->
              <input type="hidden" name="<!--{$key}-->" value="<!--{$item.value|h}-->" />
              <!--{/if}-->
              <!--{/foreach}-->

       <dl class="form_entry">
          <dt>件名</dt>
          <dd><!--{$arrSubject[$arrForm.subject.value]}--></dd>

          <dt>お名前</dt>
           <dd><!--{$arrForm.name.value|h}--></dd>

          <dt>メールアドレス</dt>
           <dd><!--{$arrForm.email.value|escape:'hexentity'}--></a></dd>

          <dt>電話番号</dt>
            <dd><!--{if strlen($arrForm.tel.value) > 0}-->
                <!--{$arrForm.tel.value|h}-->
                <!--{/if}-->
            </dd>

          <dt>お問い合わせ日</dt>
           <dd><!--{$arrForm.now.value|h}--></a></dd>

          <dt>お問い合わせ内容<br /></dt>
           <dd><!--{$arrForm.contents.value|h|nl2br}--></dd>
       </dl>

       <div class="btn_area">
           <ul class="btn_btm">
             <li><input type="button" value="送信" class="btnBlue" name="send" id="send" onclick="fnCheckSubmit();" /></li>
             <li><a class="btnGray02" href="javascript:fnModeSubmit('return', '', '');">戻る</a></li>
           </ul>
       </div> 
       </form>
</section>
<!--▲CONTENTS -->
