<!--{* -*- coding: utf-8-unix; -*- *}-->
<script type="text/javascript">
$(document).ready(function(){
    $.spin.imageBasePath = '<!--{$TPL_URLPATH}-->img/spin1/';
    $('#spin1').spin({
        min: 0,
	timeInterval: 150,
    });
});
</script>
<div class="contents-main">
<!--{if $tpl_mode|strlen == 0 || $arrErr|@count >= 1}-->
    <style type="text/css">

    </style>
    <form name="form1" id="form1" method="get" action="?" onsubmit="return false;">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="">
        <p>保存されている郵便番号CSVの更新日時: <!--{$tpl_csv_datetime|h}--></p>
        <p>郵便番号CSVには <!--{$tpl_line|h}--> 行のデータがあります。</p>
        <p>郵便番号DBには <!--{$tpl_count_mtb_zip|h}--> 行のデータがあります。</p>
        <!--{if $tpl_count_mtb_zip == 0}-->
            <p class="attention">登録を行なってください。</p>
        <!--{elseif $tpl_line <> $tpl_count_mtb_zip}-->
            <p class="attention">行数に差異があります。登録に異常がある恐れがあります。</p>
        <!--{/if}-->

        <div class="basis-zip-item info">
            <p>通常は、[自動登録] を利用してください。<br />
                ただし、タイムアウトしてしまう環境では、タイムアウトした後に [手動登録] を正常に完了するまで繰り返してください。</p>
        </div>

        <div class="basis-zip-item">
            <h2>自動登録</h2>
            <p>下の [削除] <!--{if !$tpl_skip_update_csv}-->[郵便番号CSV更新] <!--{/if}-->[DB手動登録] を順に実行します。ただし、タイムアウトした場合、DBは元の状態に戻ります。</p>
            <p><a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('auto', '', ''); return false;"><span class="btn-next">自動登録</span></a></p>
        </div>

        <div class="basis-zip-item">
            <h2>DB手動登録</h2>
            <p>指定した行数から郵便番号を登録します。タイムアウトした場合、直前まで登録されます。</p>
            <p><a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('manual', '', ''); return false;"><span class="btn-next">手動登録</span></a>　開始行: <input type="text" id="spin1" name="startRowNum" value="<!--{$arrForm.startRowNum|default:$tpl_count_mtb_zip+1|h}-->" size="8"><span class="attention"><!--{$arrErr.startRowNum}--></span></p>
        </div>

        <div class="basis-zip-item">
            <h2>郵便番号CSV更新</h2>
            <!--{if $tpl_skip_update_csv}-->
                ご利用頂けません。
                <!--{if $tpl_zip_download_url_empty}-->
                    <p class="attention">※ パラメーター ZIP_DOWNLOAD_URL が未設定です。</p>
                <!--{/if}-->
                <!--{if $tpl_zip_function_not_exists}-->
                    <p class="attention">※ PHP 拡張モジュール「zip」が無効です。</p>
                <!--{/if}-->
            <!--{else}-->
                <p>日本郵便の WEB サイトから、郵便番号CSVを取得します。</p>
                <p><a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('update_csv', '', ''); return false;"><span class="btn-next">更新</span></a><span class="attention"><!--{$arrErr.startRowNum}--></span></p>
            <!--{/if}-->
        </div>

        <div class="basis-zip-item end">
            <h2>削除</h2>
            <p>全ての郵便番号を削除します。再登録するまで、住所自動入力は機能しなくなります。</p>
            <p><a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('delete', '', ''); return false;"><span class="btn-next">削除</span></a></p>
        </div>
    </form>
<!--{else}-->
    <iframe src="?mode=<!--{$tpl_mode|h}-->&amp;exec=yes&amp;startRowNum=<!--{$arrForm.startRowNum|h}-->" name="progress" height="200" width="750" frameborder="0"></iframe>
<!--{/if}-->
</div>
