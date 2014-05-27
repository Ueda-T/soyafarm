<script>
  dataLayer = [];
</script>
<script type="text/javascript">
//<![EDATA[
    var items = "<!--{$tpl_order}-->";
    items = eval("("+items+")");

    var cartProducts = [];

    for (var i in items) {
        cartProducts.push({
            "id": transactionId,
            "sku": items[i]['product_id'], // 商品番号
            "name": items[i]['product_name'], // 商品名
            "category": "", // 商品カテゴリ
            "price": items[i]['price'], // 単価
            "quantity": items[i]['quantity'] // 数量
        });
    }

    dataLayer.push({
        "transactionId": items[0]['order_id'],
        "transactionTotal": items[0]['total'], // 合計
        "transactionTax": items[0]['tax'], // 税金
        "transactionShipping": items[0]['deliv_fee'], // 送料
        "transactionProducts": cartProducts, // 商品情報
        "event": "trackTrans" // イベント名
    });
//]]>
</script>