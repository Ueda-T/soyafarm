<!--{$name}--> 様

ソヤファームクラブオンラインショップをご利用いただき、
誠にありがとうございます。
下記の内容で定期購入の変更を受け付けいたしました。

************************************************
◆変更前

<!--{foreach from=$arrBeforeRegular item=item name=beforeRegular}-->
商品名： <!--{$item.product_name}-->
数量　： <!--{$item.quantity}-->
<!--{*単価　： <!--{$item.price|number_format}-->円*}-->

<!--{if $smarty.foreach.beforeRegular.index == 0}-->
<!--{assign var=next_arrival_date value=$item.next_arrival_date}-->
<!--{assign var=after_next_arrival_date value=$item.after_next_arrival_date}-->
<!--{assign var=todoke_kankaku value=$item.todoke_kankaku}-->
<!--{/if}-->
<!--{/foreach}-->

次回お届け日　：<!--{$next_arrival_date|date_format:"%Y年%m月%d日"}-->
次々回お届け日：<!--{$after_next_arrival_date|date_format:"%Y年%m月%d日"}-->
お届け間隔　　：<!--{$todoke_kankaku}-->


************************************************
◆変更後

<!--{foreach from=$arrDetail item=item}-->
商品名： <!--{$item.productsClass.name}-->
数量　： <!--{$item.quantity}-->
<!--{*単価　： <!--{$item.price|number_format}-->円*}-->

<!--{/foreach}-->

次回お届け日　：<!--{$arrAfterRegular.next_arrival_date|date_format:"%Y年%m月%d日"}-->
次々回お届け日：<!--{$arrAfterRegular.after_next_arrival_date|date_format:"%Y年%m月%d日"}-->
お届け間隔　　：<!--{$arrAfterRegular.todoke_kankaku}-->


　　　　　　　　　　　■　■　■　■　■　■

このメールは、システムから自動的に配信しております。
このメールは送信専用です。
ご連絡をいただく場合には、必ずお問い合わせフォームまたは
下記フリーダイヤルからお願いいたします。

　　　　　　　　　　　■　■　■　■　■　■

--------------------------------------------------------------
<!--{$smarty.const.MAIL_COMMON_SIGNATURE}-->
--------------------------------------------------------------
