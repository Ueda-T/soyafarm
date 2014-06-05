<!--▼CONTENTS-->
<!--{*
<h2 class="bsc">購入手順について</h2>
<p class="naked"><!--{$arrOrder.law_term02}--></p>
*}-->

<div class="wrapForm mt20">
        <table summary="特定商取引に関する法律に基づく表記" cellspacing="0" class="bsc">
            <colgroup width="20%"></colgroup>
            <colgroup width="80%"></colgroup>
            <tr>
                <th>販売会社名</th>
                <td><!--{$arrOrder.law_company|h}--></td>
            </tr>
            <tr>
                <th>運営責任者</th>
                <td><!--{$arrOrder.law_manager|h}--></td>
            </tr>
            <tr>
                <th>所在地</th>
                <td><!--{*〒<!--{$arrOrder.law_zip01|h}-->-<!--{$arrOrder.law_zip02|h}-->*}-->
                <!--{$arrPref[$arrOrder.law_pref]|h}--><!--{$arrOrder.law_addr01|h}--><!--{$arrOrder.law_addr02|h}--></td>
            </tr>
            <tr>
                <th nowrap>電話番号</th>
                <td><!--{$arrOrder.law_tel01|h}-->-<!--{$arrOrder.law_tel02|h}-->-<!--{$arrOrder.law_tel03|h}--></td>
            </tr>
<!--{if $arrOrder.law_fax01|h}-->
            <tr>
                <th>FAX番号</th>
                <td><!--{$arrOrder.law_fax01|h}-->-<!--{$arrOrder.law_fax02|h}-->-<!--{$arrOrder.law_fax03|h}--></td>
            </tr>
<!--{/if}-->
<!--{*
            <tr>
                <th>メールアドレス</th>
                <td><a href="mailto:<!--{$arrOrder.law_email|escape:'hex'}-->"><!--{$arrOrder.law_email|escape:'hexentity'}--></a></td>
            </tr>
            <tr>
                <th>URL</th>
                <td><a href="<!--{$arrOrder.law_url|h}-->"><!--{$arrOrder.law_url|h}--></a></td>
            </tr>
*}-->
            <tr>
                <th>販売価格</th>
                <td>各商品ごとに掲載</td>
            </tr>
            <tr>
                <th>代金の支払方法</th>
                <td><!--{$arrOrder.law_term03}--></td>
            </tr>
            <tr>
                <th>代金の支払時期</th>
                <td><!--{$arrOrder.law_term04}--></td>
            </tr>
            <tr>
                <th>送料</th>
                <td><!--{$arrOrder.law_term01}--></td>
            </tr>
            <tr>
                <th>商品の引渡時期</th>
                <td><!--{$arrOrder.law_term05}--></td>
            </tr>
            <tr>
                <th>返品・交換・返金</th>
                <td><!--{$arrOrder.law_term06}--></td>
            </tr>
        </table>
</div>
<!--▲CONTENTS-->
