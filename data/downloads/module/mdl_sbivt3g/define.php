<?php
/**
 * define.php - 当該モジュールでの定数を定義
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: define.php 236 2014-02-04 05:02:23Z kaji $
 * @link        http://www.veritrans.co.jp/3gps
*/

// ディレクトリセパレータのエイリアス
if (defined('DS') == false) {
    define('DS', DIRECTORY_SEPARATOR);
}
// 改行コードのエイリアス
if (defined('LF') == false) {
    define('LF', PHP_EOL);
}

/** モジュールバージョン */
define('MDL_SBIVT3G_MODULE_VERSION', 'rohto-1.0.0');

/** モジュールコード */
define('MDL_SBIVT3G_MODULE_CODE', 'mdl_sbivt3g');

/*
 * ファイルパス
 */
/** 当該モジュールベースパス */
define('MDL_SBIVT3G_BASE', MODULE_REALDIR . MDL_SBIVT3G_MODULE_CODE .DS);

/** 呼び出し(htmlディレクトリ相当)パス */
define('MDL_SBIVT3G_HTML_PATH', MDL_SBIVT3G_BASE . 'html' .DS);
/** クラスパス */
define('MDL_SBIVT3G_CLASS_PATH', MDL_SBIVT3G_BASE . 'class' .DS);
/** ページクラスパス */
define('MDL_SBIVT3G_PAGE_PATH', MDL_SBIVT3G_CLASS_PATH . 'pages' .DS);
/** ヘルパークラスパス */
define('MDL_SBIVT3G_HELPER_PATH', MDL_SBIVT3G_CLASS_PATH . 'helper' .DS);
/** 共通処理クラスパス */
define('MDL_SBIVT3G_UTILS_PATH', MDL_SBIVT3G_CLASS_PATH . 'utils' .DS);
/** インターフェースクラスパス */
define('MDL_SBIVT3G_IF_PATH', MDL_SBIVT3G_CLASS_PATH . 'if' .DS);
/** Smartyテンプレートパス */
define('MDL_SBIVT3G_TPL_PATH', MDL_SBIVT3G_BASE . 'templates' .DS);
/** 説明テキストパス */
define('MDL_SBIVT3G_DOC_PATH', MDL_SBIVT3G_BASE . 'doc' .DS);
/** 上書きファイル保存パス */
define('MDL_SBIVT3G_INSTALL_FILE_PATH', MDL_SBIVT3G_BASE . 'install' .DS);
/** バックアップファイル保存パス */
define('MDL_SBIVT3G_BACKUP_RESERVE_PATH',
    MODULE_REALDIR . MDL_SBIVT3G_MODULE_CODE . '_bk' . DS);


/** tgMdkライブラリ配置パス */
define('MDL_SBIVT3G_TGMDK_INSTALLED_PATH', MDL_SBIVT3G_BASE .'tgMdkPHP'.DS);
/** tgMdkライブラリベースパス */
define('MDL_SBIVT3G_TGMDK_BASE', MDL_SBIVT3G_TGMDK_INSTALLED_PATH. 'tgMdk' .DS);
/** tgMdkライブラリJavaScriptパス */
define('MDL_SBIVT3G_TGMDK_JS_PATH', MDL_SBIVT3G_TGMDK_INSTALLED_PATH. 'js' .DS);
/** tgMdkライブラリtrAd用JavaScriptファイルパス */
define('MDL_SBIVT3G_TRAD_JS_FILE', 'tradv2.js');
define('MDL_SBIVT3G_TRAD_JS_PATH', MDL_SBIVT3G_TGMDK_JS_PATH. MDL_SBIVT3G_TRAD_JS_FILE);
/** tgMdkライブラリプロパティファイルパス */
define('MDL_SBIVT3G_PROPERTIES_PATH',
    MDL_SBIVT3G_TGMDK_BASE . '3GPSMDK.properties');
/** tgMdkライブラリlog4phpプロパティファイルパス */
define('MDL_SBIVT3G_LOG_PROPERTIES_PATH',
    MDL_SBIVT3G_TGMDK_BASE . 'log4php.properties');

/** tgMdkライブラリプロパティ雛形格納パス */
define('MDL_SBIVT3G_EXTRA', MDL_SBIVT3G_BASE . 'mdkextra' .DS);

/** log4phpライブラリ配置パス */
define('LOG4PHP_ROOT_DIR', MDL_SBIVT3G_TGMDK_BASE . 'Lib' . DS . 'log4php');
define('LOG4PHP_APPS_DIR', LOG4PHP_ROOT_DIR . DS . 'appenders');
define('LOG4PHP_LAYS_DIR', LOG4PHP_ROOT_DIR . DS . 'layouts');
/** log4phpライブラリパスをinclude_pathに設定する */
set_include_path(LOG4PHP_LAYS_DIR . PATH_SEPARATOR . get_include_path());
set_include_path(LOG4PHP_APPS_DIR . PATH_SEPARATOR . get_include_path());
set_include_path(LOG4PHP_ROOT_DIR . PATH_SEPARATOR . get_include_path());

/*
 * インクルード
 */
// tgMdkライブラリ
require_once MDL_SBIVT3G_TGMDK_BASE . '3GPSMDK.php';

// 共通クラス
require_once MDL_SBIVT3G_CLASS_PATH . 'LC_SBIVT3G_CheckError.php';
require_once MDL_SBIVT3G_CLASS_PATH . 'LC_SBIVT3G_FormParam.php';
require_once MDL_SBIVT3G_HELPER_PATH . 'SC_Helper_SBIVT3G_Setting.php';
require_once MDL_SBIVT3G_HELPER_PATH . 'SC_Helper_SBIVT3G_Install.php';
require_once MDL_SBIVT3G_HELPER_PATH . 'SC_Helper_SBIVT3G_PaymentRule.php';
require_once MDL_SBIVT3G_HELPER_PATH . 'SC_Helper_SBIVT3G_Admin.php';
require_once MDL_SBIVT3G_UTILS_PATH  . 'GC_Utils_SBIVT3G.php';
require_once MDL_SBIVT3G_IF_PATH . 'SC_If_SBIVT3G_CompleteResource.php';
require_once MDL_SBIVT3G_IF_PATH . 'SC_If_SBIVT3G_OrderDataMainte.php';


// URLにサブフォルダが混在する場合に備えて
$_url = parse_url(HTTPS_URL);
/** 外部からのHTTPS接続URL */
define('MDL_SBIVT3G_HTTPS_URL' , $_url['scheme'] .'://'. $_url['host']);

/*
 * 設定値の定義
 */
/** 実行タイムアウト設定 */
define('MDL_SBIVT3G_STANDARD_EXECUTE_LIMIT', 125);

/** 画面表示時のレイアウトカラム数 */
define('MDL_SBIVT3G_COLUMN_NUM', '1');
/** 画面表示時のヘッダ表示有無 1:表示 2:非表示 */
define('MDL_SBIVT3G_HEADER_CHK', '1');
/** 画面表示時のフッタ表示有無 1:表示 2:非表示 */
define('MDL_SBIVT3G_FOOTER_CHK', '1');

/** 銀行決済(ネットバンク)の金融機関選択 true:モジュールで選択 false:しない */
define('MDL_SBIVT3G_NETBANK_IS_SELECT', false);


/*
 * 入力関連定義
 */
/** クレジットカード カード番号最大入力文字数 */
define('MDL_SBIVT3G_CARD_NO_MAXLEN', 19);
/** クレジットカード カード番号最小入力文字数 */
define('MDL_SBIVT3G_CARD_NO_MINLEN', 14);
/** クレジットカード カード名義(名)最大入力文字数 */
define('MDL_SBIVT3G_CARD_FIRST_NAME_MAXLEN', 20);
/** クレジットカード カード名義(氏)最大入力文字数 */
define('MDL_SBIVT3G_CARD_LAST_NAME_MAXLEN', 20);
/** クレジットカード セキュリティコード最大入力文字数 */
define('MDL_SBIVT3G_SECURITY_CODE_MAXLEN', 4);
/** クレジットカード セキュリティコード最小入力文字数 */
define('MDL_SBIVT3G_SECURITY_CODE_MINLEN', 3);

/** コンビニ・キャリア 選択サービスオプションの最大文字数 */
define('MDL_SBIVT3G_SERVICE_OPTION_MAXLEN', 15);
/** 銀行 選択金融機関コードの最大文字数 */
define('MDL_SBIVT3G_PAY_CSV_MAXLEN', 4);
/** 電子マネー他 メールアドレスの最大文字数 */
define('MDL_SBIVT3G_MAIL_LEFT_MAXLEN', 255);
define('MDL_SBIVT3G_MAIL_DOMAIN_MAXLEN', 100);
define('MDL_SBIVT3G_MAIL_ADDR_MAXLEN', 255);

/*
 * モジュール内部での決済ID
 */
/** モジュール内部での決済ID カード */
define('MDL_SBIVT3G_INNER_ID_CREDIT',           MDL_SBIVT3G_MODULE_CODE.'01');
/** モジュール内部での決済ID コンビニ */
define('MDL_SBIVT3G_INNER_ID_CVS',              MDL_SBIVT3G_MODULE_CODE.'02');
/** モジュール内部での決済ID Pay-easy ATM */
define('MDL_SBIVT3G_INNER_ID_PAYEASY_ATM',      MDL_SBIVT3G_MODULE_CODE.'03');
/** モジュール内部での決済ID Pay-easy NET */
define('MDL_SBIVT3G_INNER_ID_PAYEASY_NET',      MDL_SBIVT3G_MODULE_CODE.'04');
/** モジュール内部での決済ID モバイルEdy */
define('MDL_SBIVT3G_INNER_ID_EDY_MOBILE_MAIL',  MDL_SBIVT3G_MODULE_CODE.'05');
/** モジュール内部での決済ID サイバーEdy */
define('MDL_SBIVT3G_INNER_ID_EDY_PC_APP',       MDL_SBIVT3G_MODULE_CODE.'06');
/** モジュール内部での決済ID モバイルSuica(mail) */
define('MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_MAIL',MDL_SBIVT3G_MODULE_CODE.'07');
/** モジュール内部での決済ID モバイルSuica(app) */
define('MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_APP', MDL_SBIVT3G_MODULE_CODE.'08');
/** モジュール内部での決済ID Suica IS(mail) */                           
define('MDL_SBIVT3G_INNER_ID_SUICA_PC_MAIL',    MDL_SBIVT3G_MODULE_CODE.'09');
/** モジュール内部での決済ID Suica IS(APP) */                            
define('MDL_SBIVT3G_INNER_ID_SUICA_PC_APP',     MDL_SBIVT3G_MODULE_CODE.'10');
/** モジュール内部での決済ID WAON モバイル */   
define('MDL_SBIVT3G_INNER_ID_WAON_MOBILE_APP',  MDL_SBIVT3G_MODULE_CODE.'11');
/** モジュール内部での決済ID WAON PC */                                  
define('MDL_SBIVT3G_INNER_ID_WAON_PC_APP',      MDL_SBIVT3G_MODULE_CODE.'12');
/** モジュール内部での決済ID 銀聯ネット */                               
define('MDL_SBIVT3G_INNER_ID_CUP',              MDL_SBIVT3G_MODULE_CODE.'13');
/** モジュール内部での決済ID PayPal */                                   
define('MDL_SBIVT3G_INNER_ID_PAYPAL',           MDL_SBIVT3G_MODULE_CODE.'14');
/** モジュール内部での決済ID キャリア */                                   
define('MDL_SBIVT3G_INNER_ID_CARRIER',          MDL_SBIVT3G_MODULE_CODE.'15');
                                                                         
/*
 * モジュール内部での決済ステータス
 */
/** 決済ステータス 与信 (カード,PayPal,キャリア) */
define('MDL_SBIVT3G_STATUS_AUTH',    '1');
/** 決済ステータス 売上 (カード,PayPal,キャリア) */
define('MDL_SBIVT3G_STATUS_CAPTURE', '2');
/** 決済ステータス 取消
    (カード,コンビニ,電子マネー(Suica),銀聯ネット,PayPal,キャリア) */
define('MDL_SBIVT3G_STATUS_CANCEL',  '3');
/** 決済ステータス 申込 (コンビニ,銀行,電子マネー,銀聯ネット) */
define('MDL_SBIVT3G_STATUS_REQUEST', '4');
/** 決済ステータス 入金 (コンビニ,銀行,電子マネー,銀聯ネット) */
define('MDL_SBIVT3G_STATUS_DEPOSIT', '5');
/** 決済ステータス 返金 (電子マネー,銀聯ネット,PayPal) */
define('MDL_SBIVT3G_STATUS_REFUND',  '6');
/** 決済ステータス 期限切れ  */
define('MDL_SBIVT3G_STATUS_EXPIRED', '7');
/*
 * モジュール内部での決済ステータス文字列
 */
/** 決済ステータス 与信 */
define('MDL_SBIVT3G_STATUS_STRING_AUTH',    '与信');
/** 決済ステータス 売上 */
define('MDL_SBIVT3G_STATUS_STRING_CAPTURE', '売上');
/** 決済ステータス 取消 */
define('MDL_SBIVT3G_STATUS_STRING_CANCEL',  '取消');
/** 決済ステータス 申込 */
define('MDL_SBIVT3G_STATUS_STRING_REQUEST', '申込');
/** 決済ステータス 入金 */
define('MDL_SBIVT3G_STATUS_STRING_DEPOSIT', '入金');
/** 決済ステータス 返金 */
define('MDL_SBIVT3G_STATUS_STRING_REFUND',  '返金');
/** 決済ステータス 期限切れ  */
define('MDL_SBIVT3G_STATUS_STRING_EXPIRED', '期限切れ');

/*
 * リクエスト・レスポンス仕様の定数
 */
/** 共通 結果コード:正常 */
define('MLD_SBIVT3G_MSTATUS_OK', 'success');
/** 共通 結果コード:異常 */
define('MLD_SBIVT3G_MSTATUS_NG', 'failure');
/** 共通 結果コード:保留 */
define('MLD_SBIVT3G_MSTATUS_PENDING', 'pending');

/** クレジット 支払方法 一括 */
define('MDL_SBIVT3G_PTYPE_BULK',  '10');
/** クレジット 支払方法 分割 */
define('MDL_SBIVT3G_PTYPE_SPLIT', '61');
/** クレジット 支払方法 リボ */
define('MDL_SBIVT3G_PTYPE_REVO',  '80');
/** クレジット 支払方法 ボーナス一括 */
define('MDL_SBIVT3G_PTYPE_BONUS_BULK', '21');
/** クレジット 支払回数 コードプレフィックス */
define('MDL_SBIVT3G_PCOUNT_PREFIX', 'C');
/** クレジット 支払回数パターン(カンマ区切り) */
define('MDL_SBIVT3G_PCOUNT_PATTERN', '2,3,5,6,10,12,15,18,20,24');
/** クレジット 支払方法文字列 一括 */
define('MDL_SBIVT3G_PTYPE_STRING_BULK',  '一括払い');
/** クレジット 支払方法文字列 分割 */
define('MDL_SBIVT3G_PTYPE_STRING_SPLIT', '分割払い');
/** クレジット 支払方法文字列 リボ */
define('MDL_SBIVT3G_PTYPE_STRING_REVO',  'リボルビング払い');
/** クレジット 支払方法文字列 ボーナス一括 */
define('MDL_SBIVT3G_PTYPE_STRING_BONUS_BULK', 'ボーナス一括払い');
/** クレジット 設定用支払方法・回数　2回払い */
define('MDL_SBIVT3G_SETTING_PTYPE_SECOND','1');
/** クレジット 設定用支払方法・回数　分割払い */
define('MDL_SBIVT3G_SETTING_PTYPE_SPLIT', '2');
/** クレジット 設定用支払方法・回数　リボ払い */
define('MDL_SBIVT3G_SETTING_PTYPE_REVO',  '3');
/** クレジット 設定用支払方法・回数　ボーナス一括払い */
define('MDL_SBIVT3G_SETTING_PTYPE_BONUS', '4');
/** クレジット 設定用支払方法・回数文字列　2回払い */
define('MDL_SBIVT3G_SETTING_PTYPE_STRING_SECOND', '2回払い');
/** クレジット 設定用支払方法・回数文字列　分割払い */
define('MDL_SBIVT3G_SETTING_PTYPE_STRING_SPLIT', '分割払い(3～24回)');
/** クレジット 設定用支払方法・回数文字列　リボ払い */
define('MDL_SBIVT3G_SETTING_PTYPE_STRING_REVO',  'リボルビング払い');
/** クレジット 設定用支払方法・回数文字列　ボーナス一括払い */
define('MDL_SBIVT3G_SETTING_PTYPE_STRING_BONUS_BULK', 'ボーナス一括払い');
/** クレジット 再取引対象レコード有効月数 */
define('MDL_SBIVT3G_RETRADE_VALID_MONTH',  '13');
/** クレジット 再取引対象レコード最大表示数 */
define('MDL_SBIVT3G_RETRADE_TARGET_LIMIT',  '10');
/** クレジット 保留時のリトライ回数 */
define('MLD_SBIVT3G_CARD_RETRY_LIMIT', '3');
/** クレジット 保留時のリトライの待ち秒数 */
define('MLD_SBIVT3G_CARD_RETRY_WAIT', '3');

/** MPIホスティング 本人認証タイプ 内部コード 完全認証 */
define('MDL_SBIVT3G_MPI_ID_COMPLETE',  '1');
/** MPIホスティング 本人認証タイプ 内部コード 通常認証 カード会社リスク */
define('MDL_SBIVT3G_MPI_ID_COMPANY',   '2');
/** MPIホスティング 本人認証タイプ 内部コード 通常認証 加盟店リスク */
define('MDL_SBIVT3G_MPI_ID_MERCHANT',  '3');
/** MPIホスティング 本人認証タイプ APIコード 完全認証 */
define('MDL_SBIVT3G_MPI_CODE_COMPLETE',  'mpi-complete');
/** MPIホスティング 本人認証タイプ APIコード 通常認証 カード会社リスク */
define('MDL_SBIVT3G_MPI_CODE_COMPANY',   'mpi-company');
/** MPIホスティング 本人認証タイプ APIコード 通常認証 加盟店リスク */
define('MDL_SBIVT3G_MPI_CODE_MERCHANT',  'mpi-merchant');
/** MPIホスティング リダイレクト用決済結果戻りURL */
define('MDL_SBIVT3G_MPI_RETURN_URL', MDL_SBIVT3G_HTTPS_URL
    . SHOPPING_MODULE_URLPATH . '?mode=comp');

/** コンビニ サービスオプションタイプ セブンイレブン */
define('MDL_SBIVT3G_CVS_TYPE_SEVEN',  'sej');
/** コンビニ サービスオプションタイプ ローソン・ミニストップ・セイコーマート */
define('MDL_SBIVT3G_CVS_TYPE_LAWSON', 'lawson');
/** コンビニ サービスオプションタイプ ファミリーマート */
define('MDL_SBIVT3G_CVS_TYPE_FM',     'famima');
/** コンビニ サービスオプションタイプ
    ローソン・ファミリーマート・ミニストップ・セイコーマート */
define('MDL_SBIVT3G_CVS_TYPE_ECON', 'econ');
/** コンビニ サービスオプションタイプ その他 */
define('MDL_SBIVT3G_CVS_TYPE_OTHER',  'other');

/** コンビニ・ATM 説明テキストファイル名フォーマット */
define('MDL_SBIVT3G_EXPLAIN_FILE_NAME_FMT', 'explain_%s.txt');

/** 銀行決済 サービスオプションタイプ ATM */
define('MDL_SBIVT3G_BANK_TYPE_ATM',    'atm');
/** 銀行決済 サービスオプションタイプ ネット-PC */
define('MDL_SBIVT3G_BANK_TYPE_NET_PC', 'netbank-pc');
/** 銀行決済 サービスオプションタイプ ネット-DoCoMo */
define('MDL_SBIVT3G_BANK_TYPE_NET_DC', 'netbank-docomo');
/** 銀行決済 サービスオプションタイプ ネット-au */
define('MDL_SBIVT3G_BANK_TYPE_NET_AU', 'netbank-au');
/** 銀行決済 サービスオプションタイプ ネット-ソフトバンク */
define('MDL_SBIVT3G_BANK_TYPE_NET_SB', 'netbank-softbank');

/** 電子マネー決済 サービスオプションタイプ モバイルEdy */
define('MDL_SBIVT3G_EM_TYPE_MOBILE_EDY', 'edy-mobile');
/** 電子マネー決済 サービスオプションタイプ サイバーEdy */
define('MDL_SBIVT3G_EM_TYPE_CYBER_EDY', 'edy-pc');
/** 電子マネー決済 サービスオプションタイプ モバイルSuica(メール型) */
define('MDL_SBIVT3G_EM_TYPE_MOBILE_SUICA_MAIL', 'suica-mobile-mail');
/** 電子マネー決済 サービスオプションタイプ モバイルSuica(アプリ起動型) */
define('MDL_SBIVT3G_EM_TYPE_MOBILE_SUICA_APP', 'suica-mobile-app');
/** 電子マネー決済 サービスオプションタイプ SIS(メール型) */
define('MDL_SBIVT3G_EM_TYPE_PC_SUICA_MAIL', 'suica-pc-mail');
/** 電子マネー決済 サービスオプションタイプ SIS(アプリ起動型) */
define('MDL_SBIVT3G_EM_TYPE_PC_SUICA_APP', 'suica-pc-app');
/** 電子マネー決済 サービスオプションタイプ モバイルWaon(アプリ起動型) */
define('MDL_SBIVT3G_EM_TYPE_MOBILE_WAON_APP', 'waon-mobile');
/** 電子マネー決済 サービスオプションタイプ PC-Waon(アプリ起動型) */
define('MDL_SBIVT3G_EM_TYPE_PC_WAON_APP', 'waon-pc');

/** 銀聯ネット決済 リダイレクト用サービスタイプ */
define('MDL_SBIVT3G_CUP_SERVICE_TYPE', 'cup');
/** 銀聯ネット決済 リダイレクト用サービスコマンド */
define('MDL_SBIVT3G_CUP_SERVICE_COMMAND', 'Authorize');
/** 銀聯ネット決済 リダイレクト用処理方式目 */
define('MDL_SBIVT3G_CUP_PROC_METHOD', 'authorize');
/** 銀聯ネット決済 リダイレクト用決済結果戻りURL */
define('MDL_SBIVT3G_CUP_TERM_URL', MDL_SBIVT3G_HTTPS_URL
    . SHOPPING_MODULE_URLPATH . '?mode=exec');

/** PayPal決済 リダイレクト用決済結果戻りURL */
define('MDL_SBIVT3G_PAYPAL_RETURN_URL', MDL_SBIVT3G_HTTPS_URL
    . SHOPPING_MODULE_URLPATH . '?mode=exec');
/** PayPal決済 リダイレクト用キャンセルURL */
define('MDL_SBIVT3G_PAYPAL_CANCEL_URL', MDL_SBIVT3G_HTTPS_URL
    . SHOPPING_MODULE_URLPATH . '?mode=back');

/** キャリア決済 サービスオプションタイプ ドコモケータイ払い */
define('MDL_SBIVT3G_CARRIER_TYPE_DOCOMO',  'docomo');
/** キャリア決済 サービスオプションタイプ au かんたん決済 */
define('MDL_SBIVT3G_CARRIER_TYPE_AU', 'au');
/** キャリア決済 サービスオプションタイプ ソフトバンクまとめて支払い（B） */
define('MDL_SBIVT3G_CARRIER_TYPE_SB_KTAI', 'sb_ktai');
/** キャリア決済 サービスオプションタイプ ソフトバンクまとめて支払い（A） */
define('MDL_SBIVT3G_CARRIER_TYPE_SB_MATOMETE', 'sb_matomete');
/** キャリア決済 サービスオプションタイプ S!まとめて支払い */
define('MDL_SBIVT3G_CARRIER_TYPE_S_BIKKURI', 's_bikkuri');
/** キャリア決済 端末種別 PC */
define('MDL_SBIVT3G_CARRIER_TERMINAL_PC',       '0');
/** キャリア決済 端末種別 スマートフォン */
define('MDL_SBIVT3G_CARRIER_TERMINAL_SMAHO',    '1');
/** キャリア決済 端末種別 フィーチャーフォン */
define('MDL_SBIVT3G_CARRIER_TERMINAL_KTAI',     '2');
/** キャリア決済 商品タイプ デジタルコンテンツ */
define('MDL_SBIVT3G_CARRIER_ITEMTYPE_DIGITAL',  '0');
/** キャリア決済 商品タイプ 物販 */
define('MDL_SBIVT3G_CARRIER_ITEMTYPE_BUPPAN',   '1');
/** キャリア決済 商品タイプ 役務 */
define('MDL_SBIVT3G_CARRIER_ITEMTYPE_EKIMU',    '2');
/** キャリア決済 都度/継続区分 都度決済 */
define('MDL_SBIVT3G_CARRIER_ACCTYPE_TSUDO',     '0');
/** キャリア決済 都度/継続区分 継続課金 */
define('MDL_SBIVT3G_CARRIER_ACCTYPE_KEIZOKU',   '1');
/** キャリア決済 与信同時売上フラグ 与信のみ */
define('MDL_SBIVT3G_CARRIER_WC_AUTHORIZE',      '0');
/** キャリア決済 与信同時売上フラグ 与信同時売上 */
define('MDL_SBIVT3G_CARRIER_WC_CAPTURE',        '1');
/** キャリア決済 本人認証(3Dセキュア) 無し */
define('MDL_SBIVT3G_CARRIER_D3FLAG_NONE',       '0');
/** キャリア決済 本人認証(3Dセキュア) バイパス */
define('MDL_SBIVT3G_CARRIER_D3FLAG_BYPASS',     '1');
/** キャリア決済 本人認証(3Dセキュア) 有り */
define('MDL_SBIVT3G_CARRIER_D3FLAG_HAS',        '2');
/** キャリア決済 リダイレクト用 決済完了時URL */
define('MDL_SBIVT3G_CARRIER_SUCCESS_URL', MDL_SBIVT3G_HTTPS_URL
    . SHOPPING_MODULE_URLPATH . '?mode=success');
/** キャリア決済 リダイレクト用 決済キャンセル時URL */
define('MDL_SBIVT3G_CARRIER_CANCEL_URL', MDL_SBIVT3G_HTTPS_URL
    . SHOPPING_MODULE_URLPATH . '?mode=cancel');
/** キャリア決済 リダイレクト用 決済エラー時URL */
define('MDL_SBIVT3G_CARRIER_ERROR_URL', MDL_SBIVT3G_HTTPS_URL
    . SHOPPING_MODULE_URLPATH . '?mode=error');

/** 入金通知プログラム用ディレクトリ */
define('MDL_SBIVT3G_RECEIVE_DIR', 'sbivt3g/');
/** 入金通知プログラムURI */
define('MDL_SBIVT3G_RECEIVE_URLPATH', MDL_SBIVT3G_RECEIVE_DIR . 'res.php');
/** 入金通知プログラムURL */
define('MDL_SBIVT3G_RECEIVE_URL', HTTPS_URL . MDL_SBIVT3G_RECEIVE_URLPATH);

/** tgMdkライブラリtrAd用JavaScriptの配置パス */
define('MDL_SBIVT3G_TRAD_JS_SET_PATH', MDL_SBIVT3G_RECEIVE_DIR . MDL_SBIVT3G_TRAD_JS_FILE);

/*
 * エラーメッセージ
 */
/** PaSoRi非対応のブラウザでのPCアプリ起動型電子マネー決済時 */
define('MDL_SBIVT3G_EM_UNVALID_BROWSER_PASORI_MSG', 'ご利用の環境はこの決済方法に対応しておりません。Internet Explorer のバージョン7以上をご準備下さい。');
/** 非モバイルブラウザでのMobileアプリ起動型電子マネー決済時 */
define('MDL_SBIVT3G_EM_UNVALID_BROWSER_MOBILE_MSG', 'ご利用の環境はこの決済方法に対応しておりません。電子マネーアプリをインストールすることのできるモバイル端末でご利用下さい。');
/** 銀聯ネット非対応端末での選択時 */
define('MDL_SBIVT3G_CUP_UNSUPPORT_MSG', 'ご利用の環境はこの決済方法に対応しておりません。');
/** PayPal非対応端末での選択時 */
define('MDL_SBIVT3G_PAYPAL_UNSUPPORT_MSG', 'ご利用の環境はこの決済方法に対応しておりません。');


/** 決済モジュール名 */
define('MDL_SBIVT3G_MODULE_NAME' ,'ベリトランス3G MDK決済モジュール');

/** 決済方法名 */
/** クレジットカード決済 */
define('PAYMENT_NAME_CREDIT',           'クレジットカード決済');
/* コンビニ決済 */
define('PAYMENT_NAME_CONVENI',          'コンビニ決済');
/* モバイルEdy */
define('PAYMENT_NAME_EDY_MOBILE',       'モバイルEdy決済');
/* サイバーEdy */
define('PAYMENT_NAME_EDY_CYBER',        'サイバーEdy決済');
/* モバイルSuica決済(メール型) */
define('PAYMENT_NAME_SUICA_MOBILE_MAIL','モバイルSuica決済(メールお届け型)');
/* モバイルSuica決済(アプリ型) */
define('PAYMENT_NAME_SUICA_MOBILE_APP', 'モバイルSuica決済');
/* Suicaインターネットサービス決済(メール型) */
define('PAYMENT_NAME_SUICA_PC_MAIL',    'Suicaインターネットサービス決済(メールお届け型)');
/** Suicaインターネットサービス決済(アプリ型) */
define('PAYMENT_NAME_SUICA_PC_APP',     'Suicaインターネットサービス決済');
/** Waon決済(モバイル版) */
define('PAYMENT_NAME_WAON_MOBILE',      'モバイルWaon決済');
/** Waon決済(PC版) */
define('PAYMENT_NAME_WAON_PC',          'Waon決済');
/** Pay-easy決済(ATM) */
define('PAYMENT_NAME_ATM',              '銀行・郵貯 ATM決済');
/** Pay-easy決済(ネットバンキング) */
define('PAYMENT_NAME_NETBANK',          '銀行・郵貯 ネットバンキング決済');
/** 銀聯ネット決済 */
define('PAYMENT_NAME_UNIONPAY',         '銀聯ネット決済');
/** PayPal決済 */
define('PAYMENT_NAME_PAYPAL',           'PayPal決済');
/** キャリア決済 */
define('PAYMENT_NAME_CARRIER',          'キャリア決済');
/** キャリア決済 ドコモ */
define('PAYMENT_NAME_CARRIER_DOCOMO',   'ドコモケータイ払い');
/** キャリア決済 au */
define('PAYMENT_NAME_CARRIER_AU',       'au かんたん決済');
/** キャリア決済 ソフトバンク */
define('PAYMENT_NAME_CARRIER_SB_KTAI',  'ソフトバンクまとめて支払い（B）');
/** キャリア決済 ソフトバンク */
define('PAYMENT_NAME_CARRIER_SB_MATOMETE', 'ソフトバンクまとめて支払い（A）');
/** キャリア決済 ソフトバンク */
define('PAYMENT_NAME_CARRIER_S_BIKKURI', 'S!まとめて支払い');

/*
 * 入力パラメータ桁数
 *
 */
/** マーチャントCCID */
define('MERCHANT_CCID_LEN','24');
/** マーチャントパスワード */
define('MERCHANT_PASS_LEN','64');
/** ダミーモード用取引IDプレフィックス */
define('DUMMY_MODE_PREFIX_LEN', 90);
/** コンビニ決済 店舗名 */
define('V_SHOPNAME_LEN','32');
/** コンビニ決済 備考 */
define('V_NOTE_LEN','32');
/** 電子マネー決済(Edy) 店舗名 */
define('EE_SHOPNAME_LEN','48');
/** 電子マネー決済(Edy) 依頼・返金メールBCCアドレス */
define('EE_BCC_MAIL_ADDR_LEN','256');
/** 電子マネー決済(Edy) 依頼メール付加情報 */
define('EE_REQ_MAIL_INFO_LEN','256');
/** 電子マネー決済(Edy) 完了メール付加情報 */
define('EE_CMP_MAIL_INFO_LEN','300');
/** 電子マネー決済(Suica) 表示商品・サービス名 */
define('ES_SHOPNAME_LEN','40');
/** 電子マネー決済(Suica) 依頼・返金メールBCCアドレス */ 
define('ES_BCC_MAIL_ADDR_LEN','256');
/** 電子マネー決済(Suica) 依頼メール付加情報 */
define('ES_REQ_MAIL_INFO_LEN','256');
/** 電子マネー決済(Suica) 完了メール付加情報 */
define('ES_CMP_MAIL_INFO_LEN','300');
/** 電子マネー決済(Suica) 内容確認付加情報 */
define('ES_CNF_DISP_INFO_LEN','256');
/** 電子マネー決済(Suica) 完了画面付加情報 */
define('ES_CMP_DISP_INFO_LEN','300');
/** 銀行決済 請求内容 */
define('B_NOTE_LEN','24');
/** 銀行決済 請求内容カナ */
define('B_NOTE_KANA_LEN','48');
/** 銀聯ネット決済 店舗名 */
define('G_SHOPNAME_LEN','50');
/** 銀聯ネット決済 備考ラベル */
define('G_NOTE_LABEL_LEN','50');
/** 銀聯ネット決済 備考 */
define('G_NOTE_LEN','50');
/** PayPal決済 備考 */
define('P_NOTE_LEN','127');
/** キャリア決済 商品情報 */
define('CA_ITEM_INFO_LEN','40');

/** コンビニ決済 支払い期限日数 */
define('CONVENI_PAYMENT_TERM_DAY','60');
/** 電子マネー決済(Edy) 支払い期限日数 */
define('EDY_PAYMENT_TERM_DAY','90');
/** 電子マネー決済(Edy App) 支払い期限(固定) */
define('EDY_APP_PAYMENT_TERM_DAY','10');
/** 電子マネー決済(Suica) 決済 支払い期限日数 */
define('SUICA_PAYMENT_TERM_DAY','365');
/** 電子マネー決済(Waon) 決済 支払い期限日数 */
define('WAON_PAYMENT_TERM_DAY','365');
/** 銀行決済 支払い期限日数 */
define('BANK_PAYMENT_TERM_DAY','60');
/** 支払い期限デフォルト値 */
define('DEFAULT_PAYMENT_TERM_DAY', 10);
/** 電子マネー決済(Suica) 表示商品・サービス名 デフォルト */
define('DEFAULT_ES_SHOPNAME' ,'インターネットショッピング');
/**  利用条件(円以上) */ 
define('PAYMENT_RULE_CREDIT',  2);
define('PAYMENT_RULE_CONVENI', 1);
define('PAYMENT_RULE_PAYEASY', 1);
define('PAYMENT_RULE_EDY',     1);
define('PAYMENT_RULE_SUICA',   1);
define('PAYMENT_RULE_WAON',    1);
define('PAYMENT_RULE_CUP',     1);
define('PAYMENT_RULE_PAYPAL',  1);
define('PAYMENT_RULE_CARRIER', 1);
/**  利用条件(円以下) */
define('PAYMENT_UPPER_RULE_CREDIT',  NULL);
define('PAYMENT_UPPER_RULE_CONVENI', 299999);
define('PAYMENT_UPPER_RULE_PAYEASY', NULL);
define('PAYMENT_UPPER_RULE_EDY',     50000);
define('PAYMENT_UPPER_RULE_SUICA',   20000);
define('PAYMENT_UPPER_RULE_WAON',    20000);
define('PAYMENT_UPPER_RULE_CUP',     NULL);
define('PAYMENT_UPPER_RULE_PAYPAL',  NULL);
define('PAYMENT_UPPER_RULE_CARRIER', NULL);

/** 3G専用ステータス管理画面の一括更新数限度 */
define('MDL_SBIVT3G_MODIFY_STATUS_LIMIT', 50);

/** ステータス同期ボタンの有効化 true:ボタンを表示 false:非表示 */
define('MDL_SBIVT3G_STATUS_REFRESH_ENABLED', false);

/** 期限前・期限切れメール自動配信の有効化 true:設定を表示 false:非表示 */
define('MDL_SBIVT3G_AUTOMAIL_ENABLED', false);

/** 期限前・期限切れメール自動配信プログラムパス */
define('MDL_SBIVT3G_AUTO_MAIL_PROGRAM',
    MDL_SBIVT3G_BASE . 'batch/auto_mail.php');
/** 決済期限前メールタイトル(mtb_mail_template) */
define('MDL_SBIVT3G_MAIL_TPL_TITLE_PAY_NOTICE',
    '[SBIベリトランス]お支払い期限前告知メール');
/** 決済期限切れメールタイトル(mtb_mail_template) */
define('MDL_SBIVT3G_MAIL_TPL_TITLE_PAY_EXPIRE',
    '[SBIベリトランス]お支払い期限切れメール');

/** 決済期限前メールテンプレートファイルパス */
define('MDL_SBIVT3G_MAIL_TPL_FILE_PAY_NOTICE',
    MDL_SBIVT3G_TPL_PATH . 'mail_templates/expired_mail.tpl');
/** 決済期限切れメールテンプレートファイルパス */
define('MDL_SBIVT3G_MAIL_TPL_FILE_PAY_EXPIRE',
    MDL_SBIVT3G_MAIL_TPL_FILE_PAY_NOTICE);

?>
