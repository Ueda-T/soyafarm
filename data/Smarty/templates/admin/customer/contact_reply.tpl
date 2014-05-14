<script type="text/javascript">
function confirm_send() {
    res = confirm('この内容で送信しますか？');
    if(res) {
        document.form1.mode.value = 'send';
        document.form1.submit();
    }
    return false;
}
</script>

<div id="" class="contents-main">
<h2>返信内容</h2>
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
  <input type="hidden" name="mode" value="">
  <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
  <input type="hidden" name="contact_id" value="<!--{$contact_id}-->">        
  <table class="form">
    <tr>
      <th>テンプレート</th>
      <td>
        <select name="template_id" onChange="return fnSetvalAndSubmit('form1', 'mode', 'template');">
        <!--{foreach from=$arrMailTemplate key="key" item="value"}-->
          <option value="<!--{$key}-->" <!--{if $key==$mailTemplateId}-->selected<!--{/if}-->><!--{$value|escape}-->
        <!--{/foreach}-->
      </td>
    </tr>
    <tr>
      <th>宛先</th>
      <td><!--{$contact_data[0].email|escape}--></td>
    </tr>
    <tr>
      <th>タイトル</th>
      <td><input type="text" name="title" value="<!--{$arrForm.title|escape}-->" size="<!--{$smarty.const.STEXT_LEN}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->"><!--{$arrErr.title}--></td>
    </tr>
    <tr>
      <th>本文</th>
      <td><!--{$arrErr.content}--><textarea name="content" cols="72" rows="10"><!--{$arrForm.content|escape}--></textarea></td>
    </tr>
    <tr>
      <th>お名前</th>
      <td><!--{$contact_data[0].name01|escape}--><!--{$contact_data[0].name02|escape}--> 様</td>
    </tr>
    <tr>
      <th>お問い合わせ内容</th>
      <td><!--{$contact_data[0].contents|escape|nl2br}--></td>
    </tr>
    <tr>
      <th colspan="2" align="center"><input type="button" name="send" value="送信する" onclick="return confirm_send();"></th>
    </tr>
  </table>
</form>
<!--{if $replyCount > 0}-->
  <h2>送信履歴</h2>
    <!--{foreach from=$arrReply key="key" item="value"}-->
  <table class="list">
      <tr>
        <th>送信日時</th>
        <td><!--{$value.create_date|sfDispDBDate}--></td>
      </tr>
      <tr>
        <th>タイトル</th>
        <td><!--{$value.title|escape}--></td>
      </tr>
      <tr>
        <th>本文</th>
        <td><!--{$value.content|escape|nl2br}--></td>
      </tr>
  </table>
    <!--{/foreach}-->
<!--{/if}-->
</div>
