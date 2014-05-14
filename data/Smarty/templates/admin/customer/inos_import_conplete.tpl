<div id="products" class="contents-main">
    <div class="message">
        <span>顧客情報インポートを実行しました。</span>
    </div>
    <!--{if $arrRowErr}-->
        <table class="form">
            <tr>
                <td>
                    <!--{foreach item=err from=$arrRowErr}-->
                        <span class="attention"><!--{$err|h}--></span><br />
                    <!--{/foreach}-->
                </td>
            </tr>
        </table>
    <!--{/if}-->
    <!--{if $arrRowResult}-->
        <table class="form">
            <tr>
                <td>
                    <!--{foreach item=result from=$arrRowResult}-->
                    <span><!--{$result}--><br/></span>
                    <!--{/foreach}-->
                </td>
            </tr>
        </table>
    <!--{/if}-->
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="./inos_import.php"><span class="btn-prev">戻る</span></a></li>
        </ul>
    </div>
</div>