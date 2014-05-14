<!--{* -*- coding: utf-8-unix; -*- *}-->
<!--{* ロゴ *}-->
<div id="login-logo">
<img src="<!--{$TPL_URLPATH}-->img/header/logo.gif" alt="管理画面ログイン" />
</div>
<!--{* ▼CONTENTS *}-->
<div id="login-wrap">
    <div id="login-form" class="clearfix">
        <div id="input-form">
            <form name="form1" id="form1" method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="mode" value="login" />
            <p>
                <label for="login_id">ID</label>
                <input type="text" name="login_id" size="20" class="box25" />
            </p>
            <p>
                <label for="password">パスワード</label>
                <input type="password" name="password" size="20" class="box25" />
            </p>
            <p><a class="btn-tool-format" href="javascript:;" onclick="document.form1.submit(); return false;"><span>ログイン</span></a></p>
            </form>
        </div>
    </div>

</div>
<div id="copyright">Copyright &copy; 2011 <a href="http://www.iqueve.co.jp">IQUEVE CO.,LTD.</a> All Rights Reserved.</div>
<!--{* ▲CONTENTS *}-->

<script type="text/javascript">
//<![CDATA[
document.form1.login_id.focus();
$(function() {
    $('<input type="submit" />')
        .css({'position' : 'absolute',
            'top' : '-1000px'})
        .appendTo('form');
});
//]]>
</script>
