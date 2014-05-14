<font size="-1">ご注文ありがとうございました。<br>
<a href="<!--{$smarty.const.HTTPS_URL}-->mypage/history_list.php?<!--{$smarty.const.SID}-->">マイページ内のご注文履歴</a>にて、ご注文内容の詳細、お届け状況をご確認いただけます。<br>
<br>
オーダー番号：<!--{$tpl_order_id|h}--><br>
</font>
<!--{*
<!--{if $arrOther.title.value}-->
<!-- ▼その他の決済情報 -->
<br>
<br>
■<!--{$arrOther.title.name}-->情報<br>
<!--{foreach key=key item=item from=$arrOther}-->
<!--{if $key != "title"}-->
<!--{if $item.name != ""}--><!--{$item.name}-->：<!--{/if}--><!--{$item.value|nl2br}--><br>
<!--{/if}-->
<!--{/foreach}-->
<br>
<!-- ▲その他の決済情報 -->
<!--{/if}-->
*}-->
