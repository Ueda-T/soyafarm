<!--{* -*- coding: utf-8-unix; -*- *}-->
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" href="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery.fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="<!--{$tpl_mode}-->" />

<div id="faq" class="contents-main">

    <h2>～よくあるご質問～</h2>
    <table summary="受注関連についてのご質問">
      <th>受注関連について</th>
      <tr>
        <td>
          <div>Q1.受注データを編集したいのですが？<br />
          A1.下記手順により受注編集画面を開き、編集することが出来ます。<br />　「受注関連」クリック → 検索条件を設定し、受注データを検索 → 該当データの「編集」リンクをクリック<br />
          </div>
          <br />
          <div>Q2.受注データを一括登録出来ますか？<br />
          A2.申し訳有りませんが、現時点では対応しておりません。<br />
          </div>
          <br />
          <div>Q3.管理画面から新規受注登録を行いました。送料が正しく計算されていないようですが？<br />
          A3.送料は自動計算されません。お手数ですが、ご自身で入力していただくようお願い致します。
          </div>
          <br />
        </td>
      </tr>
    </table>

    <table summary="顧客関連についてのご質問">
      <th>顧客関連について</th>
      <tr>
      <td>
        <div>Q1.顧客情報を一度にまとめて登録する方法はありますか？<br />
      A1.申し訳有りませんが、現時点では対応しておりません。<br />
        </div>
        <br />
        <div>Q2.誤って、顧客情報を削除してしまいました。元に戻せますか？<br />
        A2.画面上の操作でデータを元に戻すことは出来ません。データベースから復元することは可能です。<br />
        </div>
        <br />
      </td>
      </tr>
    </table>
    
    <table summary="商品関連についてのご質問">
      <th>商品関連について</th>
      <tr>
        <td>
          <div>Q1.CSVファイルでの商品登録がうまくいきません<br />
          A1.商品データCSVファイルに正しいデータが挿入されていないとエラーとなります。また、入力必須項目などもございますので、今一度データの内容をご確認ください。<br />
          </div>
          <br />
          <div>Q2.商品在庫の編集がしたいので、商品編集画面を開くと「在庫数」の項目が無いので編集出来ません<br />
          A2.商品に規格を設定していると、商品編集画面より在庫数を編集することが出来ません。<br />商品ごとの規格編集画面より、在庫数を設定してください。<br />
          </div>
        </td>
      </tr>
    </table>
    
    <table summary="デザイン関連についてのご質問">
      <th>デザイン関連について</th>
      <tr>
        <td>
          <div>Q1.ページ詳細編集画面よりページの編集を行い登録をクリックしたところ、書き込み失敗のエラーが発生してしまいました。どうすれば登録できますか？<br />
      A1.テンプレートファイルに書き込み権限が無いと登録に失敗してしまいます。ファイルに書き込み権限があるかご確認ください。<br />
          </div>
          <br />
          <div>Q2.デザインテンプレートを変更したいのですが？<br />
          A2.「デザイン関連」→「テンプレート追加」よりあらかじめテンプレートを追加しておく必要がございます。追加後、「デザイン関連」→「テンプレート設定」より変更可能となります。
          </div>
        </td>
      </tr>
    </table>
    
    <table summary="その他のご質問">
      <th>その他</th>
      <tr>
       <td>
         <div>Q1.システム管理用のアカウントを増やしたいのですが？<br />
         A1.「その他」→「メンバー管理」から新規登録が可能です。尚、パスワードをお忘れになると管理画面にログイン出来なくなりますので、取り扱いには十分ご注意ください。
         </div>
         <br />
         <div>Q2.支払方法を新規登録しましたが、お買い物ページで追加した支払方法が表示されません。
         <br />A2.「その他」→「配送方法設定」より該当の配送業者の「編集」リンクをクリックしていただき、「配送方法登録」画面を開きます。そちらより、「取扱支払方法」を設定すると、お買いものページで選択出来るようになります。
         </div>
         <br />
         <div>Q3.携帯電話で管理画面を操作出来ますか？<br />
         A3.申し訳有りませんが、現時点で携帯電話での操作には対応しておりません。<br />
         </div>
         <br />
       </td>
      </tr>
    </table>
</div>
</form>
