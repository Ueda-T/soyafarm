<!--▼CONTENTS-->
<h1><!--{$tpl_title|h}--></h1>
<h2 class="bsc">商品購入について</h2>
<p class="naked">お申し込みになる前に必ず、<a href="<!--{$smarty.const.ROOT_URLPATH}-->contents/guide.php" class="icon1">ショッピングガイド</a>の記載をお読みください。<br>
ご注文画面にお進みになられた時点で、本契約の諸条件を承諾していただいたものとみなさせていただきます。</p>

<h2 class="bsc">購入手順について</h2>
<p class="naked"><!--{$arrOrder.law_term02}--></p>

<h2 class="bsc">販売店舗の名称等について</h2>

        <table summary="特定商取引に関する法律に基づく表記" cellspacing="0" class="bsc">
            <colgroup width="20%"></colgroup>
            <colgroup width="80%"></colgroup>
            <tr>
                <th>販売価格</th>
                <td>各商品ごとに掲載</td>
            </tr>
            <tr>
                <th>送料</th>
                <td><!--{$arrOrder.law_term01}--></td>
            </tr>
            <tr>
                <th>代金の支払時期</th>
                <td><!--{$arrOrder.law_term04}--></td>
            </tr>
            <tr>
                <th>商品の引渡時期</th>
                <td><!--{$arrOrder.law_term05}--></td>
            </tr>
            <tr>
                <th>代金の支払方法</th>
                <td><!--{$arrOrder.law_term03}--></td>
            </tr>
            <tr>
                <th>返品・交換・返金</th>
                <td><!--{$arrOrder.law_term06}--></td>
            </tr>
            <tr>
                <th>販売業者</th>
                <td><!--{$arrOrder.law_company|h}--></td>
            </tr>
            <tr>
                <th>販売事業者　住所</th>
                <td><!--{*〒<!--{$arrOrder.law_zip01|h}-->-<!--{$arrOrder.law_zip02|h}-->*}-->
                <!--{$arrPref[$arrOrder.law_pref]|h}--><!--{$arrOrder.law_addr01|h}--><!--{$arrOrder.law_addr02|h}--></td>
            </tr>
            <tr>
                <th nowrap>販売事業者　電話番号</th>
                <td><!--{$arrOrder.law_tel01|h}-->-<!--{$arrOrder.law_tel02|h}-->-<!--{$arrOrder.law_tel03|h}--></td>
            </tr>
<!--{if $arrOrder.law_fax01|h}-->
            <tr>
                <th>販売事業者　FAX番号</th>
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
                <th>運営責任者</th>
                <td><!--{$arrOrder.law_manager|h}--></td>
            </tr>
        </table>
<!--▲CONTENTS-->
