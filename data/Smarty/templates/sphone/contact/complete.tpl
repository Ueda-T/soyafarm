<section id="undercolumn">
        <h2 class="spNaked"><!--{$tpl_title|h}--></h2>
          <div class="thankstext">
            お問い合わせをいただき、ありがとうございました。
          </div>
        <hr>
          <div id="completetext">
             <p>万一、ご回答メールが届かない場合は、トラブルの可能性もありますので、大変お手数ではございますがもう一度お問い合わせいただくか、お電話にてお問い合わせください。</p>
             <p>今後ともご愛顧賜りますようよろしくお願い申し上げます。</p>
          </div>
                                         
                                        <div class="btn_area">
              <a class="btn_s btn_sub" href="<!--{$smarty.const.HTTP_URL}-->">トップページへ</a>
          </div>
        <hr>
          <div class="shopInformation">
             <p><!--{$arrSiteInfo.company_name|h}--></p>
             <p>TEL：<!--{$arrSiteInfo.tel01}-->-<!--{$arrSiteInfo.tel02}-->-<!--{$arrSiteInfo.tel03}--><br />
                 E-mail：<a href="mailto:<!--{$arrSiteInfo.email02|escape:'hex'}-->"><!--{$arrSiteInfo.email02|escape:'hexentity'}--></a></p>
          </div>
</section>
<!--{*
<!--▼検索バー -->
<section id="search_area">
<form method="get" action="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="search" name="name" id="search" value="" placeholder="キーワードを入力" class="searchbox" >
</form>
</section>
*}-->
<!--▲検索バー -->
<!--▲CONTENTS -->
