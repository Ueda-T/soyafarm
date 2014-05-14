<script type="text/javascript">
<!--
var flag = 0;

function setFlag(){
    flag = 1;
}
function checkFlagAndSubmit(){
    if ( flag == 1 ){
        if( confirm("内容が変更されています。続行すれば変更内容は破棄されます。宜しいでしょうか？") ){
            fnSetvalAndSubmit( 'form1', 'mode', 'id_set' );
        } else {
            return false;
        }
    } else {
        fnSetvalAndSubmit( 'form1', 'mode', 'id_set' );
    }
}

//-->
</script>


<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="regist" />
<div id="basis" class="contents-main">
    <table>
        <tr>
            <th>テンプレート<span class="attention"> *</span></th>
            <td>
            <!--{assign var=key value="template_id"}-->
            <!--{if $arrErr[$key]}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <!--{/if}-->
            <select name="template_id" onChange="return checkFlagAndSubmit();" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
            <option value="" selected="selected">選択してください</option>
            <!--{html_options options=$arrMailTEMPLATE selected=$arrForm[$key]}-->
            </select>
            </td>
        </tr>
        <tr>
            <th>メールタイトル<span class="attention"> *</span></th>
            <td>
            <!--{assign var=key value="subject"}-->
            <!--{if $arrErr[$key]}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <!--{/if}-->
            <input type="text" name="subject" value="<!--{$arrForm[$key]|h}-->" onChange="setFlag();" size="30" class="box30" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
            </td>
        </tr>
        <tr>
            <th>ヘッダー</th>
            <td>
            <!--{assign var=key value="header"}-->
            <!--{if $arrErr[$key]}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <!--{/if}-->
            <textarea name="header" cols="75" rows="12" class="area75" onChange="setFlag();" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"><!--{$arrForm[$key]|h}--></textarea><br />
            <span class="attention"> (上限<!--{$smarty.const.LTEXT_LEN}-->文字)
            </span>
            <div>
                <a class="btn-normal" href="javascript:;" onclick="fnCharCount('form1','header','cnt_header'); return false;"><span>文字数カウント</span></a>
                今までに入力したのは
                <input type="text" name="cnt_header" size="4" class="box4" readonly = true style="text-align:right" />
                文字です。
            </div>
            </td>
        </tr>
        <tr>
            <th colspan="2" align="center">動的データ挿入部分</th>
        </tr>
        <tr>
            <th>フッター</th>
            <td>
            <!--{assign var=key value="footer"}-->
            <!--{if $arrErr[$key]}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <!--{/if}-->
            <textarea name="footer" cols="75" rows="12" class="area75" onChange="setFlag();" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"><!--{$arrForm[$key]|h}--></textarea><br />
            <span class="attention"> (上限<!--{$smarty.const.LTEXT_LEN}-->文字)</span>
            <div>
                <a class="btn-normal" href="javascript:;" onclick="fnCharCount('form1','footer','cnt_footer'); return false;"><span>文字数カウント</span></a>
                今までに入力したのは
                <input type="text" name="cnt_footer" size="4" class="box4" readonly = true style="text-align:right" />
                文字です。
            </div>
            </td>
        </tr>
    </table>

    <!--{if ($tpl_update_auth == $smarty.const.UPDATE_AUTH_ON)}-->
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'regist', '', ''); return false;"><span class="btn-next">この内容で登録する</span></a></li>
        </ul>
    </div>
    <!--{/if}-->
</div>
</form>
