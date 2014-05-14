<!--{if $smarty.const.KAMI_GA_TRACKING != ""}-->
<!--▼Google Analytics-->
<script type="text/javascript">//<![CDATA[

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '<!--{$smarty.const.KAMI_GA_TRACKING}-->']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
//]]>
</script>
<!--▲Google Analytics-->
<!--{/if}-->

<!--{if $smarty.const.KAMI_GA_TRACKING != "" && count($arrCompleteOrder) > 0}-->
<!--▽ Google Analytics Ecommerce Tracking-->
<script type="text/javascript">

  var _gaq = _gaq || [];

  _gaq.push(['_setAccount', '<!--{$smarty.const.KAMI_GA_TRACKING}-->']);
  _gaq.push(['_trackPageview']);
  _gaq.push(['_addTrans',
    '<!--{$arrCompleteOrder[0].order_id}-->',        // order ID - required
    '<!--{$arrSiteInfo.shop_name|h}-->',             // affiliation or store name
    '<!--{$arrCompleteOrder[0].total}-->',        // total - required
    '<!--{$arrCompleteOrder[0].tax}-->',          // tax
    '<!--{$arrCompleteOrder[0].deliv_fee}-->',    // shipping
    '<!--{$arrCompleteOrder[0].order_addr01}-->', // city
    '<!--{$arrCompleteOrder[0].pref_name}-->',    // state or province
    ''                                    // country
  ]);

  // 受注明細
  <!--{section name=cnt loop=$arrCompleteOrder}-->
    _gaq.push(['_addItem',
      '<!--{$arrCompleteOrder[cnt].order_id}-->',     // order ID - required
      '<!--{$arrCompleteOrder[cnt].product_code}-->', // SKU/code - required
      '<!--{$arrCompleteOrder[cnt].product_name}-->', // product name
      '',                                     // category or variation
      '<!--{$arrCompleteOrder[cnt].price}-->',        // unit price - required
      '<!--{$arrCompleteOrder[cnt].quantity}-->'      // quantity - required
    ]);
  <!--{/section}-->

  _gaq.push(['_trackTrans']); //submits transaction to the Analytics servers

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<!--△ Google Analytics Ecommerce Tracking-->
<!--{/if}-->
