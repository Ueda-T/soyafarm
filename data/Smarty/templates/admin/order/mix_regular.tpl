<div id="mail" class="contents-main">
  <form name="search_form" id="search_form" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="search" />
    <h2>新旧商品混在定期情報エクスポート</h2>
    <table>
      <tr>
        <th>出力情報</th>
        <td>
          1項目:顧客ID<br />
          2項目:顧客番号<br />
          3項目:顧客名
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
