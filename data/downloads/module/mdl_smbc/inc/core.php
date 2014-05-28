<?php

/* 請求情報の送信先 */
// データ連携（テスト用）
define('MDL_SMBC_DATA_LINK_URL_TEST', 'https://www.paymentstation.jp/cooperationtest/sf/at/ksuketsukeinforeg/uketsukeInfoRegInit.do');
// データ連携（本番用）
define('MDL_SMBC_DATA_LINK_URL_REAL', 'https://www.paymentstation.jp/cooperation/sf/at/ksuketsukeinforeg/uketsukeInfoRegInit.do');

// 画面連携PC（テスト用）
define('MDL_SMBC_PAGE_LINK_PC_URL_TEST', 'https://www.paymentstation.jp/customertest/sf/at/kokksuketsukeinfo/begin.do');
// 画面連携PC（本番用）
define('MDL_SMBC_PAGE_LINK_PC_URL_REAL', 'https://www.paymentstation.jp/customer/sf/at/kokksuketsukeinfo/begin.do');

// 画面連携スマートフォン（テスト用）スマートフォンもPCと同じ画面で連携
define('MDL_SMBC_PAGE_LINK_SP_URL_TEST', 'https://www.paymentstation.jp/customertest/sf/at/kokksuketsukeinfo/begin.do');
// 画面連携スマートフォン（本番用）スマートフォンもPCと同じ画面で連携
define('MDL_SMBC_PAGE_LINK_SP_URL_REAL', 'https://www.paymentstation.jp/customer/sf/at/kokksuketsukeinfo/begin.do');

// 画面連携モバイル（テスト用）
define('MDL_SMBC_PAGE_LINK_MOBILE_URL_TEST', 'https://www.paymentstation.jp/mobiletest/PFG307105_Begin.do');
// 画面連携モバイル（本番用）
define('MDL_SMBC_PAGE_LINK_MOBILE_URL_REAL', 'https://www.paymentstation.jp/mobile/PFG307105_Begin.do');

// 画面連携継続課金PC（テスト用）
define('MDL_SMBC_REGULAR_PAGE_LINK_PC_URL_TEST', 'https://www.paymentstation.jp/customertest/sf/at/kokkzmoshikomi/begin.do');
// 画面連携継続課金PC（本番用）
define('MDL_SMBC_REGULAR_PAGE_LINK_PC_URL_REAL', 'https://www.paymentstation.jp/customer/sf/at/kokkzmoshikomi/begin.do');

// 画面連携継続課金MB（テスト用）
define('MDL_SMBC_REGULAR_PAGE_LINK_MOBILE_URL_TEST', 'https://www.paymentstation.jp/mobiletest/PFG307187_Begin.do');
// 画面連携継続課金MB（本番用）
define('MDL_SMBC_REGULAR_PAGE_LINK_MOBILE_URL_REAL', 'https://www.paymentstation.jp/mobile/PFG307187_Begin.do');

// クレジット請求確定連携（テスト用）
define('MDL_SMBC_CREDIT_KAKUTEI_LINK_URL_TEST', 'https://www.paymentstation.jp/cooperationtest/sf/cd/skuinfokt/skuinfoKakutei.do');
// クレジット請求確定連携（本番用）
define('MDL_SMBC_CREDIT_KAKUTEI_LINK_URL_REAL', 'https://www.paymentstation.jp/cooperation/sf/cd/skuinfokt/skuinfoKakutei.do');

// 3Dセキュア連携（テスト用？）
define('MDL_SMBC_SECURE_LINK_URL_TEST', 'https://www.paymentstation.jp/cooperationtest/sf/cd/creditinfo/threeDResultReg.do');
// 3Dセキュア連携（本番用）
define('MDL_SMBC_SECURE_LINK_URL_REAL', 'https://www.paymentstation.jp/cooperation/sf/cd/creditinfo/threeDResultReg.do');

// クレジットカード情報お預かり機能（テスト用）
define('MDL_SMBC_CREDIT_INFO_KEEP_LINK_URL_TEST', 'https://www.paymentstation.jp/cooperationtest/sf/at/idkanri/begin.do');
// クレジットカード情報お預かり機能（本番用）
define('MDL_SMBC_CREDIT_INFO_KEEP_LINK_URL_REAL', 'https://www.paymentstation.jp/cooperation/sf/at/idkanri/begin.do');
// 取引検索(テスト用)
define('MDL_SMBC_TRHKINFO_URL_TEST', 'https://www.paymentstation.jp/cooperationtest/sf/at/trhkinfosearch/begin.do');
// 取引検索(本番用)
define('MDL_SMBC_TRHKINFO_URL_REAL', 'https://www.paymentstation.jp/cooperation/sf/at/trhkinfosearch/begin.do');
?>
