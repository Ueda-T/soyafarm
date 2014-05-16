<?php
$require_conf_php_dir = realpath(dirname( __FILE__));
require_once($require_conf_php_dir. '/core.php');

define('MDL_SMBC_PATH', MODULE_REALDIR . 'mdl_smbc/');
define('MDL_SMBC_PLUGIN_PATH', MDL_SMBC_PATH . 'plugin/');
define('MDL_SMBC_CLASS_PATH', MDL_SMBC_PATH . 'class/');
define('MDL_SMBC_TEMPLATE_PATH', MDL_SMBC_PATH . 'templates/');
define('SMBC_LOG_REALFILE', DATA_REALDIR . 'logs/mdl_smbc.log');

/* オーナーズストア設定画面 */
/* 契約コード桁数 */
define('MDL_SMBC_SHOP_CD_LEN', 7);
/* 収納企業コード桁数 */
define('MDL_SMBC_SYUNO_CO_CD_LEN', 8);
/* ショップパスワード桁数 */
define('MDL_SMBC_SHOP_PWD_LEN', 20);
/* クレジットカード情報お預かり機能用認証パスワード桁数 */
define('MDL_SMBC_CARD_INFO_PWD_LEN', 20);
/* 郵送先 */
// ユーザ様
define('MDL_SMBC_PAYMENT_SLIP_ISSUE_USER', 1);
// SMBCファイナンスサービス
define('MDL_SMBC_PAYMENT_SLIP_ISSUE_SMBC', 2);

/* 決済手段区分 */
// クレジット
define('MDL_SMBC_CREDIT_BILL_METHOD', '05');
// コンビニ番号方式
define('MDL_SMBC_CONVENI_NUMBER_BILL_METHOD', '03');
// 払込票（コンビニ、ゆうちょ等）
define('MDL_SMBC_PAYMENT_SLIP_BILL_METHOD', '20');
// 銀行振込
define('MDL_SMBC_BANK_TRANSFER_BILL_METHOD', '06');
// ペイジー
define('MDL_SMBC_PAYEASY_BILL_METHOD', '07');
// 電子マネー
define('MDL_SMBC_ELECTRONIC_MONEY_BILL_METHOD', '09');
// ネットバンク
define('MDL_SMBC_NETBUNK_BILL_METHOD', '10');

/* 決済種類コード */
// クレジットカード
define('MDL_SMBC_CREDIT_KESSAI_ID', '0501');
// コンビニ（番号方式）:セブンイレブン
define('MDL_SMBC_CONVENI_SEVENELEVEN_KESSAI_ID', '0301');
// コンビニ（番号方式）:ローソン
define('MDL_SMBC_CONVENI_LAWSON_KESSAI_ID', '0302');
// コンビニ（番号方式）:セイコーマート
define('MDL_SMBC_CONVENI_SEICOMART_KESSAI_ID', '0303');
// コンビニ（番号方式）:ファミリーマート
define('MDL_SMBC_CONVENI_FAMILYMART_KESSAI_ID', '0304');
// コンビニ（番号方式）:サークルK・サンクス
define('MDL_SMBC_CONVENI_CIRCLEKSUNKUS_KESSAI_ID', '0305');
// 払込票（コンビニ・ゆうちょ等）
define('MDL_SMBC_PAYMENT_SLIP_KESSAI_ID', '2001');
// 銀行振込
define('MDL_SMBC_BANK_TRANSFER_KESSAI_ID', '0601');
// ネットバンク
define('MDL_SMBC_NETBUNK_KESSAI_ID', '1001');

// 決済種類コードをキーとしたコンビニ名
$arrCONVENI = array(
    MDL_SMBC_CONVENI_SEVENELEVEN_KESSAI_ID => 'セブン-イレブン',
    MDL_SMBC_CONVENI_LAWSON_KESSAI_ID => 'ローソン',
    MDL_SMBC_CONVENI_SEICOMART_KESSAI_ID => 'セイコーマート',
    MDL_SMBC_CONVENI_FAMILYMART_KESSAI_ID => 'ファミリーマート',
    MDL_SMBC_CONVENI_CIRCLEKSUNKUS_KESSAI_ID => 'サークルＫ・サンクス'
);

/* バージョン */
// データ連携：PC
define('MDL_SMBC_DATA_LINK_PC_VERSION', '210');
// データ連携：モバイル・クレジット
define('MDL_SMBC_DATA_LINK_MOBILE_CREDIT_VERSION', '220');
// データ連携：モバイル・クレジット
define('MDL_SMBC_DATA_LINK_MOBILE_CONVENI_VERSION', '210');
// データ連携：クレジット 3Dセキュア
define('MDL_SMBC_DATA_LINK_CREDIT_SECURE_VERSION', '211');
// データ連携：クレジットカード番号お預かりサービス
define('MDL_SMBC_DATA_LINK_CARD_INFO_KEEP_VERSION', '214');
// 画面連携：PC
define('MDL_SMBC_PAGE_LINK_PC_VERSION', '110');
// 画面連携：モバイル
define('MDL_SMBC_PAGE_LINK_MOBILE_VERSION', '120');
// クレジット 請求確定連携
define('MDL_SMBC_KAKUTEI_LINK_CREDIT_VERSION', '212');
// クレジット 与信全額取消連携
define('MDL_SMBC_YOSHIN_ALL_CANCEL_LINK_CREDIT_VERSION', '215');
// クレジット 与信一部取消連携
define('MDL_SMBC_YOSHIN_PART_CANCEL_LINK_CREDIT_VERSION', '216');
// クレジット 売上全額取消連携
define('MDL_SMBC_SALES_ALL_CANCEL_LINK_CREDIT_VERSION', '217');
// クレジット 売上一部取消連携
define('MDL_SMBC_SALES_PART_CANCEL_LINK_CREDIT_VERSION', '218');

/* 請求金額上限 */
// クレジット
define('MDL_SMBC_CREDIT_UPPER_RULE_MAX', 9999999);
// コンビニ番号方式
define('MDL_SMBC_CONVENI_NUMBER_UPPER_RULE_MAX', 300000);
// 払込票（コンビニ、ゆうちょ等）
define('MDL_SMBC_PAYMENT_SLIP_UPPER_RULE_MAX', 99999999);
// 銀行振込
define('MDL_SMBC_BANK_TRANSFER_UPPER_RULE_MAX', 99999999);
// ペイジー
define('MDL_SMBC_PAYEASY_UPPER_RULE_MAX', 99999999);
// 電子マネー
define('MDL_SMBC_ELECTRONIC_MONEY_UPPER_RULE_MAX', 50000);
// ネットバンク
define('MDL_SMBC_NETBUNK_UPPER_RULE_MAX', 99999999);

/* 支払方法名 */
// クレジット
define('MDL_SMBC_CREDIT_PAY_TYPE', 'クレジットカード決済');
// コンビニ番号方式
define('MDL_SMBC_CONVENI_NUMBER_PAY_TYPE', 'コンビニエンスストア（受付番号）決済');
// 払込票（コンビニ、ゆうちょ等）
define('MDL_SMBC_PAYMENT_SLIP_PAY_TYPE', '払込票利用の決済');
// 銀行振込
define('MDL_SMBC_BANK_TRANSFER_PAY_TYPE', '銀行振込決済');
// ペイジー
define('MDL_SMBC_PAYEASY_PAY_TYPE', 'ペイジー決済');
// 電子マネー
define('MDL_SMBC_ELECTRONIC_MONEY_PAY_TYPE', '電子マネー決済');
// ネットバンク
define('MDL_SMBC_NETBUNK_PAY_TYPE', 'ネットバンク決済');

/* クレジット決済 */
// クレジット有効年の加算値
define('MDL_SMBC_CREDIT_ADD_YEAR', '9');

/* 結果コード */
// 与信OK
define('MDL_SMBC_RES_OK', '000000');
// 3Dセキュア
define('MDL_SMBC_RES_SECURE', '000100');
// 加盟店顧客IDが登録されていません
define('MDL_SMBC_RES_IF_ERROR_KMT_KOK_ID', '943115');
// 過入金
define('MDL_SMBC_RES_OVER', '943009');
// 一部入金
define('MDL_SMBC_RES_SHORT', '943008');
// 依頼入金
define('MDL_SMBC_RES_REQUEST', '943167');
// 依頼入金（重複）
define('MDL_SMBC_RES_DUPLICATION', '943168');

// 決済ステーションへの送信データの文字コード
define('MDL_SMBC_TO_ENCODE', "SJIS-win");

/* クレジット請求確定処理フラグ */
// 与信
define('MDL_SMBC_CREDIT_STATUS_YOSHIN', 1);
// 請求確定済み
define('MDL_SMBC_CREDIT_STATUS_KAKUTEI', 2);
// 請求取消済み
define('MDL_SMBC_CREDIT_STATUS_CANCEL', 3);

/* 入金ステータス */
// 与信
define('MDL_SMBC_PAYMENT_STATUS_OK', 1);
// 請求確定済み
define('MDL_SMBC_PAYMENT_STATUS_OVER', 2);
// 請求取消済み
define('MDL_SMBC_PAYMENT_STATUS_SHORT', 3);
?>
