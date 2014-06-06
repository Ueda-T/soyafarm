<?php

/*
// TOPページ
$CLICK_ANALYZER_TOP_TAG =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20110921113710'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20110921113710' width='0' height='0' /></noscript>
EOF;
define("CLICK_ANALYZER_TOP", $CLICK_ANALYZER_TOP_TAG);
// 固定ページ用クリックアナライザ
$CLICK_ANALYZER_STATIC = array();

// ROS_ブランド一覧
// http://www.shop.rohto.co.jp/shop/contents/004.aspx
$CLICK_ANALYZER_STATIC["brand"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20110921114808'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20110921114808' width='0' height='0' /></noscript>
EOF;

// 共通_はじめての方へ
// http://www.shop.rohto.co.jp/shop/contents/welcome.aspx
$CLICK_ANALYZER_STATIC["welcome"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20110929005433'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20110929005433' width='0' height='0' /></noscript>
EOF;

// 共通_ショッピングガイド
// http://www.shop.rohto.co.jp/shop/contents/guide.aspx
$CLICK_ANALYZER_STATIC["guide"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20110929005453'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20110929005453' width='0' height='0' /></noscript>
EOF;

// 共通_ショップ特典
// http://www.shop.rohto.co.jp/shop/contents/merit.aspx
$CLICK_ANALYZER_STATIC["merit"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20110929005527'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20110929005527' width='0' height='0' /></noscript>
EOF;

// 共通_FAQ
// http://www.shop.rohto.co.jp/shop/contents/faq.aspx
$CLICK_ANALYZER_STATIC["faq"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20110929005540'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20110929005540' width='0' height='0' /></noscript>
EOF;

// 共通_お問い合わせ
// https://www.shop.rohto.co.jp/shop/contact/contact.aspx
$CLICK_ANALYZER_STATIC["contact"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20110929005606'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20110929005606' width='0' height='0' /></noscript>
EOF;

// 共通_マイページ
// https://www.shop.rohto.co.jp/shop/customer/menu.aspx
$CLICK_ANALYZER_STATIC["mypage"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20110929005622'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20110929005622' width='0' height='0' /></noscript>
EOF;

// 共通_クイック注文
// http://www.shop.rohto.co.jp/shop/goods/cform.aspx
$CLICK_ANALYZER_STATIC["catalogue"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20110929005636'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20110929005636' width='0' height='0' /></noscript>
EOF;

// 共通_買い物カゴ
// http://www.shop.rohto.co.jp/shop/cart/cart.aspx
$CLICK_ANALYZER_STATIC["cart"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20110929005657'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20110929005657' width='0' height='0' /></noscript>
EOF;

// 共通_利用規約・新規会員登録
// https://www.shop.rohto.co.jp/shop/order/method.aspx
$CLICK_ANALYZER_STATIC["entry"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20110929005731'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20110929005731' width='0' height='0' /></noscript>
EOF;

// 共通_ご注文方法の指定
// https://www.shop.rohto.co.jp/shop/order/method.aspx
$CLICK_ANALYZER_STATIC["payment"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20110929005731'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20110929005731' width='0' height='0' /></noscript>
EOF;

// 商品検索
// http://www.shop.rohto.co.jp/shop/goods/search.aspx
$CLICK_ANALYZER_STATIC["search"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20111213140818'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20111213140818' width='0' height='0' /></noscript>
EOF;

// ご注文完了
// https://www.shop.rohto.co.jp/shop/order/order.aspx
$CLICK_ANALYZER_STATIC["complete"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20130212183032'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20130212183032' width='0' height='0' /></noscript>
EOF;

// お肌の乾燥対策
// http://www.shop.rohto.co.jp/shop/contents/onayami01.aspx
$CLICK_ANALYZER_STATIC["onayami01"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20111219104807'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20111219104807' width='0' height='0' /></noscript>
EOF;

// ニキビのできにくいお肌になりたい
// http://www.shop.rohto.co.jp/shop/contents/onayami02.aspx
$CLICK_ANALYZER_STATIC["onayami02"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20111219105359'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20111219105359' width='0' height='0' /></noscript>
EOF;

// はじめよう！すっきり快腸生活
// http://www.shop.rohto.co.jp/shop/contents/onayami03.aspx
$CLICK_ANALYZER_STATIC["onayami03"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20111219105429'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20111219105429' width='0' height='0' /></noscript>
EOF;

// 睡眠でお悩みの方
// http://www.shop.rohto.co.jp/shop/contents/sleep.aspx
$CLICK_ANALYZER_STATIC["sleep"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20111219105500'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20111219105500' width='0' height='0' /></noscript>
EOF;

// スキンケア110番
// http://www.shop.rohto.co.jp/shop/contents/colum01.aspx
$CLICK_ANALYZER_STATIC["colum01"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20111219105524'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20111219105524' width='0' height='0' /></noscript>
EOF;

// 秋から冬へのうるおい肌づくり
// http://www.shop.rohto.co.jp/shop/contents/colum02.aspx
$CLICK_ANALYZER_STATIC["colum02"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20111219105548'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20111219105548' width='0' height='0' /></noscript>
EOF;

// ROS_アロマ（カテゴリトップ）
// http://www.shop.rohto.co.jp/shop/category/category.aspx?category=10
$CLICK_ANALYZER_STATIC["aroma"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20110921114218'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20110921114218' width='0' height='0' /></noscript>
EOF;

// 旬穀_旬穀旬菜のお約束
// http://www.shop.rohto.co.jp/shop/event/event.aspx?event=about
$CLICK_ANALYZER_STATIC["about"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20110921120312'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20110921120312' width='0' height='0' /></noscript>
EOF;

// 旬穀_旬コ_夏を元気に乗り切る
// http://www.shop.rohto.co.jp/shop/event/event.aspx?event=column03
$CLICK_ANALYZER_STATIC["column03"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20110921120925'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20110921120925' width='0' height='0' /></noscript>
EOF;

// 旬穀_旬コ_冬のあったか食品
// http://www.shop.rohto.co.jp/shop/event/event.aspx?event=column04
$CLICK_ANALYZER_STATIC["column04"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20110921120957'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20110921120957' width='0' height='0' /></noscript>
EOF;

// 旬穀_お歳暮特集2011
// http://www.shop.rohto.co.jp/shop/event/event.aspx?event=oseibo
$CLICK_ANALYZER_STATIC["oseibo"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20111112021450'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20111112021450' width='0' height='0' /></noscript>
EOF;

// 旬穀新商品shun10
// http://www.shop.rohto.co.jp/shop/event/event.aspx?event=shun10
$CLICK_ANALYZER_STATIC["shun10"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20111116232825'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20111116232825' width='0' height='0' /></noscript>
EOF;

// セノビック大好き写真＆メッセージ募集
// http://www.shop.rohto.co.jp/shop/event/event.aspx?event=snbpht
$CLICK_ANALYZER_STATIC["snbpht"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20111208213234'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20111208213234' width='0' height='0' /></noscript>
EOF;

// お買い得キャンペーン2011冬
// http://www.shop.rohto.co.jp/shop/event/event.aspx?event=win2011
$CLICK_ANALYZER_STATIC["win2011"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20111208213325'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20111208213325' width='0' height='0' /></noscript>
EOF;

// 旬_年末年始に忙しいママの味方
// http://www.shop.rohto.co.jp/shop/event/event.aspx?event=shuwin
$CLICK_ANALYZER_STATIC["shuwin"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20111208213431'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20111208213431' width='0' height='0' /></noscript>
EOF;

// イベント＆特集一覧
// http://www.shop.rohto.co.jp/shop/event/event.aspx?event=evtnav
$CLICK_ANALYZER_STATIC["evtnav"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20111213131651'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20111213131651' width='0' height='0' /></noscript>
EOF;

// 新商品特集
// http://www.shop.rohto.co.jp/shop/event/event.aspx?event=new
$CLICK_ANALYZER_STATIC["new"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20111014204144'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20111014204144' width='0' height='0' /></noscript>
EOF;

// セノビックおまとめ購入ページ
$CLICK_ANALYZER_STATIC["senobic"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20110921115043'></script><noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20110921115043' width='0' height='0' /></noscript>
EOF;


// カテゴリ用クリックアナライザ
$CLICK_ANALYZER_CATEGORY = array();
// ROS_美容・スキンケア（カテゴリトップ）
// http://www.shop.rohto.co.jp/shop/category/category.aspx?category=20
$CLICK_ANALYZER_CATEGORY["4"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20110921113856'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20110921113856' width='0' height='0' /></noscript>
EOF;

// ROS_サプリメント（カテゴリトップ）
// http://www.shop.rohto.co.jp/shop/category/category.aspx?category=30
$CLICK_ANALYZER_CATEGORY["3"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20110921114126'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20110921114126' width='0' height='0' /></noscript>
EOF;

// アクネロジー<HTMLメール>
// http://www.shop.rohto.co.jp/acnelogy/mail/spot.html
$CLICK_ANALYZER_CATEGORY["697"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20120105100634'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20120105100634' width='0' height='0' /></noscript>
EOF;

// ブランド用クリックアナライザ
$CLICK_ANALYZER_BRAND = array();
// ROS_コンタクトケア（カテゴリトップ）
// http://www.shop.rohto.co.jp/shop/category/category.aspx?category=40
$CLICK_ANALYZER_BRAND["31"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20110921114239'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20110921114239' width='0' height='0' /></noscript>
EOF;

// ROS_セノビック一覧
// http://www.shop.rohto.co.jp/shop/event/event.aspx?event=senobic
$CLICK_ANALYZER_BRAND["1"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20110921115043'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20110921115043' width='0' height='0' /></noscript>
EOF;

// 旬穀_トップページ（HOME）
// http://www.shop.rohto.co.jp/shop/shun/index.html
$CLICK_ANALYZER_BRAND["35"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20110921115358'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20110921115358' width='0' height='0' /></noscript>
EOF;

// ROS_オバジ
// http://www.shop.rohto.co.jp/shop/event/event.aspx?event=10000029
$CLICK_ANALYZER_BRAND["6"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20110929005139'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20110929005139' width='0' height='0' /></noscript>
EOF;

// ROS_50の恵み
// http://www.shop.rohto.co.jp/shop/event/event.aspx?event=50megumi
$CLICK_ANALYZER_BRAND["30"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20110929005248'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20110929005248' width='0' height='0' /></noscript>
EOF;

// ディーナ一覧
// http://www.shop.rohto.co.jp/shop/event/event.aspx?event=10000047
$CLICK_ANALYZER_BRAND["22"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20120524013401'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20120524013401' width='0' height='0' /></noscript>
EOF;

// インソール
// http://www.shop.rohto.co.jp/shop/event/event.aspx?event=insole
$CLICK_ANALYZER_BRAND["60"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20111116231906'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20111116231906' width='0' height='0' /></noscript>
EOF;


// 商品詳細用クリックアナライザ
$CLICK_ANALYZER_PRODUCTS = array();
// ROS_セノビック3種セット(各2袋)
// http://www.shop.rohto.co.jp/shop/goods/goods.aspx?goods=13323W
$CLICK_ANALYZER_PRODUCTS["112"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20110921115122'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20110921115122' width='0' height='0' /></noscript>
EOF;

// 水素水商品詳細30752Y
// http://www.shop.rohto.co.jp/shop/goods/goods.aspx?goods=30752Y
$CLICK_ANALYZER_PRODUCTS["77"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20111116232632'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20111116232632' width='0' height='0' /></noscript>
EOF;

// 旬穀オイルオイスター
// http://www.shop.rohto.co.jp/shop/goods/goods.aspx?goods=306695
$CLICK_ANALYZER_PRODUCTS["118"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20111116233130'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20111116233130' width='0' height='0' /></noscript>
EOF;

// ケール青汁 せんいのチカラ
// http://www.shop.rohto.co.jp/shop/goods/goods.aspx?goods=304004
$CLICK_ANALYZER_PRODUCTS["104"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20111208213829'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20111208213829' width='0' height='0' /></noscript>
EOF;

// 古町糀製造所 糀肌くりーむ
// http://www.shop.rohto.co.jp/shop/goods/goods.aspx?goods=126729
$CLICK_ANALYZER_PRODUCTS["127"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20120906070442'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20120906070442' width='0' height='0' /></noscript>
EOF;

// 商品詳細）クリアビジョンＥＸ
// http://www.shop.rohto.co.jp/shop/goods/goods.aspx?goods=304455
$CLICK_ANALYZER_PRODUCTS["171"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20121004153945'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20121004153945' width='0' height='0' /></noscript>
EOF;

// 商品詳細）ミガック
// http://www.shop.rohto.co.jp/shop/goods/goods.aspx?goods=134441
$CLICK_ANALYZER_PRODUCTS["122"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20140128113909'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20140128113909' width='0' height='0' /></noscript>
EOF;

// 商品詳細）1兆個のチカラ（60粒）
// http://www.shop.rohto.co.jp/shop/goods/goods.aspx?goods=132591
$CLICK_ANALYZER_PRODUCTS["103"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20140128113935'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20140128113935' width='0' height='0' /></noscript>
EOF;

// 商品詳細）1兆個のチカラ（20粒）
// http://www.shop.rohto.co.jp/shop/goods/goods.aspx?goods=134397
$CLICK_ANALYZER_PRODUCTS["83"] =<<<EOF
<script type='text/javascript' charset='utf-8' src='//clickanalyzer.jp/ClickIndex.js' id='contents/20140128113950'></script>
<noscript><img src='//clickanalyzer.jp/ClickIndex.php?mode=noscript&id=contents&pg=20140128113950' width='0' height='0' /></noscript>
EOF;

 */
?>
