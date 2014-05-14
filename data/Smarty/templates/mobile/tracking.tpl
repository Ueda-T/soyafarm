<!--{if $smarty.const.KAMI_GA_MOBILE_TRACKING != ""}-->
<!--▼Google Analytics-->
<!--{php}-->
  // Copyright 2009 Google Inc. All Rights Reserved.

  function googleAnalyticsGetImageUrl() {
    $GA_ACCOUNT = KAMI_GA_MOBILE_TRACKING;
    $GA_PIXEL = ROOT_URLPATH . "ga.php";

    $url = "";
    $url .= $GA_PIXEL . "?";
    $url .= "utmac=" . $GA_ACCOUNT;
    $url .= "&utmn=" . rand(0, 0x7fffffff);
    $referer = $_SERVER["HTTP_REFERER"];
    $query = $_SERVER["QUERY_STRING"];
    $path = $_SERVER["REQUEST_URI"];
    if (empty($referer)) {
      $referer = "-";
    }
    $url .= "&utmr=" . urlencode($referer);
    if (!empty($path)) {
      $url .= "&utmp=" . urlencode($path);
    }
    $url .= "&guid=ON";
    return str_replace("&", "&amp;", $url);
  }

  $googleAnalyticsImageUrl = googleAnalyticsGetImageUrl();
  echo '<img width="1" height="1" src="'.$googleAnalyticsImageUrl.'">';
<!--{/php}-->

<!--▲Google Analytics-->
<!--{/if}-->

<!--{if $smarty.const.KAMI_GA_MOBILE_TRACKING != "" && count($arrCompleteOrder) > 0}-->
<!--▽ Google Analytics Ecommerce Tracking-->
<!--{php}-->
  function googleAnalyticsGetTransUrl($orderId, $affiliation, $total, $tax, $shipping, $city, $state, $country) {
    $GA_ACCOUNT = KAMI_GA_MOBILE_TRACKING;
    $GA_PIXEL = ROOT_URLPATH . "ga.php";

    $url = "";
    $url .= $GA_PIXEL . "?";
    $url .= "utmac=" . $GA_ACCOUNT;
    $url .= "&utmn=" . rand(0, 0x7fffffff);
    $url .= "&utmt=tran";
    $url .= "&utmtid=" . urlencode($orderId);
    $url .= "&utmtst=" . urlencode($affiliation);
    $url .= "&utmtto=" . urlencode($total);
    $url .= "&utmttx=" . urlencode($tax);
    $url .= "&utmtsp=" . urlencode($shipping);
    $url .= "&utmtci=" . urlencode($city);
    $url .= "&utmtrg=" . urlencode($state);
    $url .= "&utmtco=" . urlencode($country);
    $url .= "&guid=ON";
    return str_replace("&", "&", $url);
  }

  function googleAnalyticsGetItemUrl($orderId, $sku, $name, $category, $price, $quantity) {
    $GA_ACCOUNT = KAMI_GA_MOBILE_TRACKING;
    $GA_PIXEL = ROOT_URLPATH . "ga.php";

    $url = "";
    $url .= $GA_PIXEL . "?";
    $url .= "utmac=" . $GA_ACCOUNT;
    $url .= "&utmn=" . rand(0, 0x7fffffff);
    $url .= "&utmt=item";
    $url .= "&utmtid=" . urlencode($orderId);
    $url .= "&utmipc=" . urlencode($sku);
    $url .= "&utmipn=" . urlencode($name);
    $url .= "&utmiva=" . urlencode($category);
    $url .= "&utmipr=" . urlencode($price);
    $url .= "&utmiqt=" . urlencode($quantity);
    $url .= "&guid=ON";
    return str_replace("&", "&", $url);
  }

  $order = $this->_tpl_vars['arrCompleteOrder'][0];
  $shop  = $this->_tpl_vars['arrSiteInfo']['shop_name'];
  $googleAnalyticsTransUrl = googleAnalyticsGetTransUrl(
    $order['order_id'],     // order ID - required
    $shop_name,             // affiliation or store name
    $order['total'],        // total - required
    $order['tax'],          // tax
    $order['deliv_fee'],    // shipping
    $order['order_addr01'], // city
    $order['pref_name'],    // state or province
    ''                      // country
  );
  echo '<img src="' . $googleAnalyticsTransUrl . '" />';

  foreach ($this->_tpl_vars['arrCompleteOrder'] as $detail) {
    $googleAnalyticsItemUrl = googleAnalyticsGetItemUrl(
      $detail['order_id'],     // order ID - required
      $detail['product_code'], // SKU/code - required
      $detail['product_name'], // product name
      '',                      // category or variation
      $detail['price'],        // unit price - required
      $detail['quantity']      // quantity - required
    );
    echo '<img src="' . $googleAnalyticsItemUrl . '"/>';
  }
<!--{/php}-->

<!--△ Google Analytics Ecommerce Tracking-->
<!--{/if}-->
