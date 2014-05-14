<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_header.tpl" subtitle="お客様の声書き込み（完了ページ）"}-->

<div id="window_area">
    <h2 class="title">お客様の声書き込み</h2>
    <div id="completebox">
        <p class="message">登録が完了しました。ご利用ありがとうございました。</p>
        <p>弊社にて登録内容を確認後、ホームページに反映させていただきます。<br />
            今しばらくお待ちくださいませ。</p>
    </div>
    <div class="btn_area">
        <ul>
            <li><a href="javascript:window.close()" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_close_on.jpg','b_close');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_close.jpg','b_close');">
                <img src="<!--{$TPL_URLPATH}-->img/button/btn_close.jpg" alt="閉じる" border="0" name="b_close" /></a></li>
        </ul>
    </div>
</div>

<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_footer.tpl"}-->
