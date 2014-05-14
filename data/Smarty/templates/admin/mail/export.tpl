<div id="mail" class="contents-main">
  <form name="search_form" id="search_form" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="search" />
    <h2>メルマガ用データ出力</h2>
    <table>
      <tr>
        <th>出力情報</th>
        <td>
          1項目:顧客番号<br />
          2項目:氏名<br />
          3項目:メールアドレス<br />
          4項目:性別<br />
          5項目:都道府県<br />
          6項目:登録日<br />
          7項目:生年月日<br />
          8項目:顧客区分<br />
        </td>
      </tr>
    </table>

    <!--{if ($tpl_csv_download_auth == $smarty.const.CSV_DOWNLOAD_AUTH_ON)}-->
    <div class="btn">
      <div class="btn-area">
        <ul>
          <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('search_form', 'search', '', ''); return false;"><span class="btn-next">データ出力する</span></a></li>
        </ul>
      </div>
    </div>
    <!--{/if}-->
  </form>
</div>
