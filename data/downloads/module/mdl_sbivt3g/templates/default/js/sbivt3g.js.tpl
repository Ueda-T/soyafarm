<!--{*
 * sbivt3g.js - 決済画面共通JavaScriptテンプレート
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: sbivt3g.js.tpl 56 2011-08-09 13:09:04Z hira $
 * @link        http://www.veritrans.co.jp/3gps
*}-->
<script type="text/javascript">
if (jQuery) {

  var send = true;
  var message='この画面を離れるとご注文は取消となりますがよろしいですか？';

  function setSubmit(targetId, setMode) {
      $('#'+targetId).click(function(){
          if ($(this).parents('form').length > 0) {
              var form = $(this).parents('form').first();
              if (form.children('input[name=mode]').length > 0) {
                  form.children('input[name=mode]').first().val(setMode);
                  if(send) {
                      send = false;
                      form.submit();
                      return true;
                  } else {
                      alert("只今、処理中です。しばらくお待ち下さい。");
                      return false;
                  }
              }
          }
          return false;
      });
  }
  $(function(){
      setSubmit('btnBack', 'back');
      setSubmit('btnExec', 'exec');
      setSubmit('btnReTrade', 'retrade');
      setSubmit('btnReTradeBack', 'back');

      $('a').each(function(){
          var href = $(this).attr('href');
          if (!href || href.substr(0, 1) != '#') {
            $(this).click(function(){
                return window.confirm(message);
            });
          }
      });
      $('form').each(function(){
          var frmId = $(this).attr('id');
          if (!frmId || (frmId != 'frmSbi' && frmId != 'frmSbiCup'
                  && frmId != 'frmSbiReTrade')) {
              $(this).submit(function(){
                  return window.confirm(message);
              });
          }
      });
  });

}

/*
 * 上書き定義
 */
function fnFormModeSubmit(form, mode, keyname, keyid) {
    switch(mode) {
    case 'delete':
        if(!window.confirm('一度削除したデータは、元に戻せません。\n削除しても宜しいですか？')){
            return;
        }
        break;
    case 'confirm':
        if(!window.confirm('登録しても宜しいですか')){
            return;
        }
        break;
    case 'regist':
        if(!window.confirm('登録しても宜しいですか')){
            return;
        }
        break;
    default:
        break;
    }
    document.forms[form]['mode'].value = mode;
    if(keyname != "" && keyid != "") {
        document.forms[form][keyname].value = keyid;
    }
    if (!window.confirm(message)) {
        return;
    }
    document.forms[form].submit();
}
</script>

