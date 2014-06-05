<?php
/** フロント表示関連 */
define('SAMPLE_NAME', "例：大豆 太郎");
define('SAMPLE_KANA', "例：ﾀﾞｲｽﾞ ﾀﾛｳ");
define('SAMPLE_TEL', "例：0120-39-3009");
define('SAMPLE_ZIP', "例：542-0086");
define('SAMPLE_ADDRESS1', "例：大阪市中央区西心斎橋");
define('SAMPLE_ADDRESS2', "例：2-1-5　日本生命御堂筋八幡町ビル");
/** ユーザファイル保存先 */
define('USER_DIR', "user_data/");
/** ユーザファイル保存先 */
define('USER_REALDIR', HTML_REALDIR . USER_DIR);
/** ユーザインクルードファイル保存先 */
define('USER_INC_REALDIR', USER_REALDIR . "include/");
/** 郵便番号専用DB */
define('ZIP_DSN', DEFAULT_DSN);
/** ユーザー作成ページ等 */
define('USER_URL', HTTP_URL . USER_DIR);
/** 認証方式 */
define('AUTH_TYPE', "HMAC");
/** テンプレートファイル保存先 */
define('USER_TEMPLATE_DIR', "templates/");
/** テンプレートファイル保存先 */
define('USER_PACKAGE_DIR', "packages/");
/** テンプレートファイル保存先 */
define('USER_TEMPLATE_REALDIR', USER_REALDIR . USER_PACKAGE_DIR);
/** テンプレートファイル一時保存先 */
define('TEMPLATE_TEMP_REALDIR', HTML_REALDIR . "upload/temp_template/");
/** ユーザー作成画面のデフォルトPHPファイル */
define('USER_DEF_PHP_REALFILE', USER_REALDIR . "__default.php");
/** その他画面のデフォルトページレイアウト */
define('DEF_LAYOUT', "products/list.php");
/** ダウンロードモジュール保存ディレクトリ */
define('MODULE_DIR', "downloads/module/");
/** ダウンロードモジュール保存ディレクトリ */
define('MODULE_REALDIR', DATA_REALDIR . MODULE_DIR);
/** DBセッションの有効期限(秒) */
define('MAX_LIFETIME', 7200);
/** マスターデータキャッシュディレクトリ */
define('MASTER_DATA_REALDIR', DATA_REALDIR . "cache/");
/** アップデート管理用ファイル格納場所 */
define('UPDATE_HTTP', "http://sv01.ec-cube.net/info/index.php");
/** アップデート管理用CSV1行辺りの最大文字数 */
define('UPDATE_CSV_LINE_MAX', 4096);
/** アップデート管理用CSVカラム数 */
define('UPDATE_CSV_COL_MAX', 13);
/** モジュール管理用CSVカラム数 */
define('MODULE_CSV_COL_MAX', 16);
/** 商品購入完了 */
define('AFF_SHOPPING_COMPLETE', 1);
/** ユーザ登録完了 */
define('AFF_ENTRY_COMPLETE', 2);
/** 文字コード */
define('CHAR_CODE', "UTF-8");
/** ロケール設定 */
define('LOCALE', "ja_JP.UTF-8");
/** 決済モジュール付与文言 */
define('ECCUBE_PAYMENT', "EC-CUBE");
/** PEAR::DBのデバッグモード */
define('PEAR_DB_DEBUG', 9);
/** PEAR::DBの持続的接続オプション */
define('PEAR_DB_PERSISTENT', false);
/** バッチを実行する最短の間隔(秒) */
define('LOAD_BATCH_PASS', 3600);
/** 締め日の指定(末日の場合は、31を指定してください。) */
define('CLOSE_DAY', 31);
/** 一般サイトエラー */
define('FAVORITE_ERROR', 13);
/** グラフ格納ディレクトリ */
define('GRAPH_REALDIR', HTML_REALDIR . "upload/graph_image/");
/** グラフURL */
define('GRAPH_URLPATH', ROOT_URLPATH . "upload/graph_image/");
/** 円グラフ最大表示数 */
define('GRAPH_PIE_MAX', 10);
/** グラフのラベルの文字数 */
define('GRAPH_LABEL_MAX', 40);
/** 何歳まで集計の対象とするか */
define('BAT_ORDER_AGE', 70);
/** 商品集計で何位まで表示するか */
define('PRODUCTS_TOTAL_MAX', 15);
/** 1:公開 2:非公開 */
define('DEFAULT_PRODUCT_DISP', 2);
/** 送料無料購入数量 (0の場合は、いくつ買っても無料にならない) */
define('DELIV_FREE_AMOUNT', 0);
/** 配送料の設定画面表示(有効:1 無効:0) */
define('INPUT_DELIV_FEE', 1);
/** 商品ごとの送料設定(有効:1 無効:0) */
define('OPTION_PRODUCT_DELIV_FEE', 0);
/** 配送業者ごとの配送料を加算する(有効:1 無効:0) */
define('OPTION_DELIV_FEE', 1);
/** おすすめ商品登録(有効:1 無効:0) */
define('OPTION_RECOMMEND', 1);
/** 商品規格登録(有効:1 無効:0) */
define('OPTION_CLASS_REGIST', 1);
/** 会員登録変更(マイページ)パスワード用 */
define('DEFAULT_PASSWORD', "******");
/** 別のお届け先最大登録数 */
define('DELIV_ADDR_MAX', 20);
/** 管理画面ステータス一覧表示件数 */
define('ORDER_STATUS_MAX', 50);
/** フロントレビュー書き込み最大数 */
define('REVIEW_REGIST_MAX', 5);
/** デバッグモード(true：sfPrintRやDBのエラーメッセージ、ログレベルがDebugのログを出力する、false：出力しない) */
define('DEBUG_MODE', false);
/** 管理ユーザID(メンテナンス用表示されない。) */
define('ADMIN_ID', "1");
/** 会員登録時に仮会員確認メールを送信するか (true:仮会員、false:本会員) */
define('CUSTOMER_CONFIRM_MAIL', false);
/** メールの本文,mimeヘッダの変換先エンコーディング設定(mbstring表記) */
define('MAIL_ENCODING', "ISO-2022-JP-MS");
/** メイルマガジンバッチモード(true:バッチで送信する ※要cron設定、false:リアルタイムで送信する) */
define('MELMAGA_BATCH_MODE', false);
/** ログイン画面フレーム */
define('LOGIN_FRAME', "login_frame.tpl");
/** 管理画面フレーム */
define('MAIN_FRAME', "main_frame.tpl");
/** 一般サイト画面フレーム */
define('SITE_FRAME', "site_frame.tpl");
/** 認証文字列 */
define('CERT_STRING', "7WDhcBTF");
/** 生年月日登録開始年 */
define('BIRTH_YEAR', 1901);
/** 本システムの稼働開始年 */
define('RELEASE_YEAR', 2013);
/** クレジットカードの期限＋何年 */
define('CREDIT_ADD_YEAR', 10);
/** 親カテゴリのカテゴリIDの最大数 (これ以下は親カテゴリとする。) */
define('PARENT_CAT_MAX', 12);
/** GET値変更などのいたずらを防ぐため最大数制限を設ける。 */
define('NUMBER_MAX', 1000000000);
/** ポイントの計算ルール(1:四捨五入、2:切り捨て、3:切り上げ) */
define('POINT_RULE', 2);
/** 1ポイント当たりの値段(円) */
define('POINT_VALUE', 1);
/** 管理モード 1:有効　0:無効(納品時) */
define('ADMIN_MODE', 0);
/** 売上集計バッチモード(true:バッチで集計する ※要cron設定、false:リアルタイムで集計する) */
define('DAILY_BATCH_MODE', false);
/** ログファイル最大数(ログテーション) */
define('MAX_LOG_QUANTITY', 5);
/** 1つのログファイルに保存する最大容量(byte) */
define('MAX_LOG_SIZE', "1073741824");
/** トランザクションID の名前 */
define('TRANSACTION_ID_NAME', "transactionid");
/** パスワード忘れの確認メールを送付するか否か。(0:送信しない、1:送信する) */
define('FORGOT_MAIL', 0);
/** 登録できるサブ商品の数 */
define('HTML_TEMPLATE_SUB_MAX', 12);
/** 文字数が多すぎるときに強制改行するサイズ(半角) */
define('LINE_LIMIT_SIZE', 60);
/** 誕生日月ポイント */
define('BIRTH_MONTH_POINT', 0);
/** 拡大画像横 */
define('LARGE_IMAGE_WIDTH', 500);
/** 拡大画像縦 */
define('LARGE_IMAGE_HEIGHT', 500);
/** 案内画像横 */
define('GUIDE_IMAGE_WIDTH', 780);
/** 案内画像縦 */
define('GUIDE_IMAGE_HEIGHT', 780);
/** 一覧画像横 */
define('SMALL_IMAGE_WIDTH', 130);
/** 一覧画像縦 */
define('SMALL_IMAGE_HEIGHT', 130);
/** 通常画像横 */
define('NORMAL_IMAGE_WIDTH', 300);
/** 通常画像縦 */
define('NORMAL_IMAGE_HEIGHT', 300);
/** 通常サブ画像横 */
define('NORMAL_SUBIMAGE_WIDTH', 200);
/** 通常サブ画像縦 */
define('NORMAL_SUBIMAGE_HEIGHT', 200);
/** 拡大サブ画像横 */
define('LARGE_SUBIMAGE_WIDTH', 500);
/** 拡大サブ画像縦 */
define('LARGE_SUBIMAGE_HEIGHT', 500);
/** 一覧表示画像横 */
define('DISP_IMAGE_WIDTH', 65);
/** 一覧表示画像縦 */
define('DISP_IMAGE_HEIGHT', 65);
/** その他の画像1 */
define('OTHER_IMAGE1_WIDTH', 500);
/** その他の画像1 */
define('OTHER_IMAGE1_HEIGHT', 500);
/** HTMLメールテンプレートメール担当画像横 */
define('HTMLMAIL_IMAGE_WIDTH', 110);
/** HTMLメールテンプレートメール担当画像縦 */
define('HTMLMAIL_IMAGE_HEIGHT', 120);
/** 画像サイズ制限(KB) */
define('IMAGE_SIZE', 1000);
/** CSVサイズ制限(KB) */
define('CSV_SIZE', 32768);
/** CSVアップロード1行あたりの最大文字数 */
define('CSV_LINE_MAX', 10240);
/** PDFサイズ制限(KB):商品詳細ファイル等 */
define('PDF_SIZE', 5000);
/** ファイル管理画面アップ制限(KB) */
define('FILE_SIZE', 32768);
/** アップできるテンプレートファイル制限(KB) */
define('TEMPLATE_SIZE', 10000);
/** カテゴリの最大階層 */
define('LEVEL_MAX', 5);
/** 最大カテゴリ登録数 */
define('CATEGORY_MAX', 1000);
/** 管理機能タイトル */
define('ADMIN_TITLE', "管理機能");
/** 編集時強調表示色 */
define('SELECT_RGB', "#ffffdf");
/** 入力項目無効時の表示色 */
define('DISABLED_RGB', "#C9C9C9");
/** エラー時表示色 */
define('ERR_COLOR', "#ffe8e8");
/** 親カテゴリ表示文字 */
define('CATEGORY_HEAD', ">");
define('BRAND_HEAD', ">");
/** 生年月日初期選択年 */
define('START_BIRTH_YEAR', 1970);
/** 価格名称 */
define('NORMAL_PRICE_TITLE', "税込価格");
/** 価格名称 */
define('SALE_PRICE_TITLE', "税抜価格");
/** ログファイル */
define('LOG_REALFILE', DATA_REALDIR . "logs/site.log");
/** 会員ログイン ログファイル */
define('CUSTOMER_LOG_REALFILE', DATA_REALDIR . "logs/customer.log");
/** 画像一時保存 */
define('IMAGE_TEMP_REALDIR', HTML_REALDIR . "upload/temp_image/");
/** 画像保存先 */
define('IMAGE_SAVE_REALDIR', HTML_REALDIR . "upload/save_image/");
/** 画像一時保存URL */
define('IMAGE_TEMP_URLPATH', ROOT_URLPATH . "upload/temp_image/");
/** 画像保存先URL */
define('IMAGE_SAVE_URLPATH', ROOT_URLPATH . "upload/save_image/");
/** RSS用画像一時保存URL */
define('IMAGE_TEMP_RSS_URL', HTTP_URL . "upload/temp_image/");
/** RSS用画像保存先URL */
define('IMAGE_SAVE_RSS_URL', HTTP_URL . "upload/save_image/");
/** エンコードCSVの一時保存先 */
define('CSV_TEMP_REALDIR', DATA_REALDIR . "upload/csv/");
define('CSV_SAVE_REALDIR', DATA_REALDIR . "upload/save_csv/");
/** 画像がない場合に表示 */
define('NO_IMAGE_REALDIR', USER_TEMPLATE_REALDIR . "img/picture/img_blank.gif");
/** システム管理トップ */
define('ADMIN_SYSTEM_URLPATH', ROOT_URLPATH . ADMIN_DIR . "system/" . DIR_INDEX_PATH);
/** 規格登録 */
define('ADMIN_CLASS_REGIST_URLPATH', ROOT_URLPATH . ADMIN_DIR . "products/class.php");
/** 郵便番号入力 */
define('INPUT_ZIP_URLPATH', ROOT_URLPATH . "ajax/input_zip.php");
/** 商品名入力 */
define('INPUT_PRODUCT_NAME_URLPATH', ROOT_URLPATH . "ajax/input_product.php");
/** 商品番号入力 */
define('INPUT_PRODUCT_URLPATH', ROOT_URLPATH . "ajax/getProductNameByCode.php");
/** カテゴリコード入力 */
define('INPUT_CATEGORY_URLPATH', ROOT_URLPATH . "ajax/input_category.php");
/** ブランドコード入力 */
define('INPUT_BRAND_URLPATH', ROOT_URLPATH . "ajax/input_brand.php");
/** 配送業者登録 */
define('ADMIN_DELIVERY_URLPATH', ROOT_URLPATH . ADMIN_DIR . "basis/delivery.php");
/** 支払い方法登録 */
define('ADMIN_PAYMENT_URLPATH', ROOT_URLPATH . ADMIN_DIR . "basis/payment.php");
/** ホーム */
define('ADMIN_HOME_URLPATH', ROOT_URLPATH . ADMIN_DIR . "home.php");
/** ログインページ */
define('ADMIN_LOGIN_URLPATH', ROOT_URLPATH . ADMIN_DIR . DIR_INDEX_PATH);
/** 商品検索ページ */
define('ADMIN_PRODUCTS_URLPATH', ROOT_URLPATH . ADMIN_DIR . "products/" . DIR_INDEX_PATH);
/** 注文編集ページ */
define('ADMIN_ORDER_EDIT_URLPATH', ROOT_URLPATH . ADMIN_DIR . "order/edit.php");
define('ADMIN_ORDER_VIEW_URLPATH', ROOT_URLPATH . ADMIN_DIR . "order/view.php");
/** 注文編集ページ */
define('ADMIN_ORDER_URLPATH', ROOT_URLPATH . ADMIN_DIR . "order/" . DIR_INDEX_PATH);
/** 注文編集ページ */
define('ADMIN_ORDER_MAIL_URLPATH', ROOT_URLPATH . ADMIN_DIR . "order/mail.php");
/** ログアウトページ */
define('ADMIN_LOGOUT_URLPATH', ROOT_URLPATH . ADMIN_DIR . "logout.php");
/** アクセス成功 */
define('SUCCESS', 0);
/** メンバー管理ページ表示行数 */
define('MEMBER_PMAX', 10);
/** 検索ページ表示行数 */
define('SEARCH_PMAX', 10);
/** ページ番号の最大表示数量 */
define('NAVI_PMAX', 4);
/** 商品サブ情報最大数 */
define('PRODUCTSUB_MAX', 5);
/** お届け時間の最大表示数 */
define('DELIVTIME_MAX', 16);
/** 配送料金の最大表示数 */
define('DELIVFEE_MAX', 47);
/** 短い項目の文字数 (名前など) */
define('STEXT_LEN', 50);
define('SMTEXT_LEN', 100);
/** 長い項目の文字数 (住所など) */
define('MTEXT_LEN', 200);
define('ADDRESS_LEN', 40);
/** 長中文の文字数 (問い合わせなど) */
define('MLTEXT_LEN', 1000);
/** 長文の文字数 */
define('LTEXT_LEN', 3000);
/** 超長文の文字数 (メルマガなど) */
define('LLTEXT_LEN', 99999);
/** URLの文字長 */
define('URL_LEN', 1024);
/** 管理画面用：ID・パスワードの文字数制限 */
define('ID_MAX_LEN', STEXT_LEN);
/** 管理画面用：ID・パスワードの文字数制限 */
define('ID_MIN_LEN', 4);
/** 商品コード長 */
define('PRODUCT_CODE_LEN', 10);
/** 金額桁数 */
define('PRICE_LEN', 8);
/** 率桁数 */
define('PERCENTAGE_LEN', 3);
/** 在庫数、販売制限数 */
define('AMOUNT_LEN', 6);
/** 配送形態算出係数 */
define('DELIV_JUDGMENT_LEN', 4);
/** 配送形態算出係数デフォルト値 */
define('DELIV_JUDGMENT_DEFAULT_VALUE', 1.00);
/** 郵便番号 */
define('ZIP_LEN', 7);
/** 郵便番号1 */
define('ZIP01_LEN', 3);
/** 郵便番号2 */
define('ZIP02_LEN', 4);
/** 電話番号各項目制限 */
define('TEL_ITEM_LEN', 6);
/** 電話番号総数 */
define('TEL_LEN', 12);
/** フロント画面用：パスワードの最小文字数 */
define('PASSWORD_MIN_LEN', 4);
/** フロント画面用：パスワードの最大文字数 */
define('PASSWORD_MAX_LEN', 16);
/** カテゴリコードの長さ定義 */
define('CATEGORY_CODE_LEN', 20);
/** ブランドコードの長さ定義 */
define('BRAND_CODE_LEN', 20);
/** 検査数値用桁数(INT) */
define('INT_LEN', 9);
/** クレジットカードの文字数 */
define('CREDIT_NO_LEN', 4);
/** キャンペーンコードコードの文字数 */
define('CAMPAIGN_CODE_LEN', 4);
/** 検索カテゴリ最大表示文字数(byte) */
define('SEARCH_CATEGORY_LEN', 18);
/** ファイル名表示文字数 */
define('FILE_NAME_LEN', 10);
/** 容量文字数 */
define('CAPACITY_LEN', 80);
/** 販売名文字数 */
define('SALES_NAME_LEN', 100);
/** 表示用商品名文字数 */
define('DISP_NAME_LEN', 100);
/** 日付時刻文字数 **/
define('DATE_TIME_LEN', 19);
/** 作成者ID文字数 **/
define('CREATOR_ID_LEN', 4);
/** 商品名バイト数 **/
define('PRODUCT_NAME_BYTE_LEN', 40);

/** 基幹連携 */

/** 顧客CD文字数 */
define('INOS_CUSTOMER_CD_LEN', 11);
/** カナ氏名文字数 */
define('INOS_KANA_LEN', 15);
/** 漢字氏名文字数 */
define('INOS_NAME_LEN', 32);
/** 電話番号文字数 */
define('INOS_TEL_LEN', 13);
/** 郵便番号文字数 */
define('INOS_ZIP_LEN', 8);
/** カナ住所文字数 */
define('INOS_ADDR_KANA_LEN', 40);
/** 住所文字数 */
define('INOS_ADDR_LEN', 40);
/** 日付文字数 */
define('INOS_DATE_LEN', 10);
/** 削除フラグ桁数 */
define('INOS_DEL_FLG_LEN', 1);
/** 出荷場所CD桁数 */
define('INOS_SHIPMENT_CD_LEN', 2);
/** 宅配便CD(配送業者ID)桁数 */
define('INOS_DELIV_ID_LEN', 2);
/** 箱サイズ文字数 */
define('INOS_BOX_SIZE_LEN', 3);
/** 送り状枚数桁数 */
define('INOS_INVOICE_NUM_LEN', 2);
/** 配達時間CD(配送時間ID)桁数 */
define('INOS_TIME_ID_LEN', 2);
/** 明細書同梱区分桁数 */
define('INOS_INCLUDE_KBN_LEN', 1);
/** 支払方法CD桁数 */
define('INOS_PAYMENT_ID_LEN', 1);
/** 税込送料桁数 */
define('INOS_DELIV_FEE_LEN', 11);
/** 受注NO文字数 */
define('INOS_ORDER_ID_LEN', 10);
/** 商品CD(商品ID)文字数 */
define('INOS_PRODUCT_CODE_LEN', 10);
/** 商品名称文字数 */
define('INOS_PRODUCT_NAME_LEN', 40);
/** 商品略称文字数 */
define('INOS_PRODUCT_SNAME_LEN', 20);
/** 商品数量 桁数 */
define('INOS_QUANTITY_LEN', 11);
/** 商品価格 桁数 */
define('INOS_PRICE_LEN', 11);
/** 配送区分 桁数 */
define('INOS_DELIV_KBN_LEN', 1);
/** コースCD桁数 */
define('INOS_COURSE_CD_LEN', 3);

/** 顧客取込 メールアドレス文字数 */
define('INOS_CUSTOMER_EMAIL_LEN', 50);
/** 顧客取込 性別桁数 */
define('INOS_CUSTOMER_SEX_LEN', 1);
/** 顧客取込 顧客区分桁数 */
define('INOS_CUSTOMER_CUSTOMER_KBN_LEN', 1);
/** 顧客取込 DM区分桁数 */
define('INOS_CUSTOMER_DM_FLG_LEN', 1);
/** 顧客取込 電話区分桁数 */
define('INOS_CUSTOMER_TEL_FLG_LEN', 1);
/** 顧客取込 メール送信区分桁数 */
define('INOS_CUSTOMER_MAILMAGA_FLG_LEN', 1);
/** 顧客取込 個人情報区分桁数 */
define('INOS_CUSTOMER_PRIVACY_KBN_LEN', 1);
/** 顧客取込 償却顧客区分桁数 */
define('INOS_CUSTOMER_KASHIDAORE_KBN_LEN', 1);
/** 顧客取込 使用可能ポイント桁数 */
define('INOS_CUSTOMER_POINT_LEN', 5);
/** 顧客取込 クレジット会員ID文字数 */
define('INOS_CUSTOMER_TORIHIKI_ID_LEN', 50);
/** 顧客取込 WEB顧客CD桁数 */
define('INOS_CUSTOMER_CUSTOMER_ID_LEN', 10);
/** 顧客取込 顧客形態CD桁数 */
define('INOS_CUSTOMER_CUSTOMER_TYPE_CD_LEN', 2);

/** 定期取込 コース受注NO桁数 */
define('INOS_REGULAR_BASE_NO_LEN', 10);
/** 定期取込 状況フラグ桁数 */
define('INOS_REGULAR_STATUS_LEN', 1);
/** 定期取込 指定条件(備考)文字数 */
define('INOS_REGULAR_REMARKS_LEN', 60);
/** 定期取込 購入済み回数 桁数 */
define('INOS_REGULAR_BUY_NUM_LEN', 3);

/** 定期詳細取込 行NO 桁数 */
define('INOS_REGULAR_DETAIL_LINE_NO_LEN', 2);
/** 定期詳細取込 届け日指定区分桁数 */
define('INOS_REGULAR_DETAIL_TODOKE_KBN_LEN', 1);
/** 定期詳細取込 お届け日桁数 */
define('INOS_REGULAR_DETAIL_TODOKE_DAY_LEN', 2);
/** 定期詳細取込 曜日指定桁数 */
define('INOS_REGULAR_DETAIL_TODOKE_WEEK_LEN', 1);
/** 定期詳細取込 キャンセル理由CD桁数 */
define('INOS_REGULAR_DETAIL_CANCEL_REASON_CD_LEN', 2);
/** 定期詳細取込 値引率桁数 */
define('INOS_CUT_RATE_LEN', 3);

/** 商品取込 一般価格 */
define('INOS_PRODUCTS_PRICE_NORMAL_LEN', 7);
/** 商品取込 社員価格 */
define('INOS_PRODUCTS_PRICE_EMPLOYEE_LEN', 7);
/** 商品取込 消費税端数処理区分 */
define('INOS_PRODUCTS_TAX_FRACTION_KBN_LEN', 1);
/** 商品取込 単位 */
define('INOS_PRODUCTS_UNIT_LEN', 1);
/** 商品取込 送料負担区分 */
define('INOS_PRODUCTS_DELIV_CHARGE_KBN_LEN', 1);
/** 商品取込 販売対象区分 */
define('INOS_PRODUCTS_SELL_FLG_LEN', 1);
/** 商品取込 サンプル区分 */
define('INOS_PRODUCTS_SAMPLE_FLG_LEN', 1);
/** 商品取込 プレゼント区分 */
define('INOS_PRODUCTS_PRESENT_FLG_LEN', 1);
/** 商品取込 ポイント対象区分 */
define('INOS_PRODUCTS_TERGET_POINT_FLG_LEN', 1);
/** 商品取込 配送形態算出係数 */
define('INOS_PRODUCTS_DELIV_JUDGMENT_LEN', 4);
/** 商品取込 メール便業者区分 */
define('INOS_PRODUCTS_MAIL_DELIV_KBN_LEN', 1);

/** クッキー保持期限(日) */
define('COOKIE_EXPIRE', 365);
/** カテゴリ区切り文字 */
define('SEPA_CATNAVI', " > ");
/** カテゴリ区切り文字 */
define('SEPA_CATLIST', " | ");
/** 会員情報入力 */
define('SHOPPING_URL', HTTPS_URL . "shopping/" . DIR_INDEX_PATH);
/** 会員登録ページTOP */
define('ENTRY_URL', HTTPS_URL . "entry/" . DIR_INDEX_PATH);
/** 会員規約ページ */
define('KIYAKU_URL', HTTPS_URL . "entry/kiyaku.php");
/** サイトトップ */
define('TOP_URLPATH', ROOT_URLPATH . DIR_INDEX_PATH);
/** カートトップ */
define('CART_URLPATH', ROOT_URLPATH . "cart/" . DIR_INDEX_PATH);
define('NOTFOUND_URLPATH', ROOT_URLPATH . "404/" . DIR_INDEX_PATH);
/** お届け先設定 */
define('DELIV_URLPATH', ROOT_URLPATH . "shopping/deliv.php");
/** 複数お届け先設定 */
define('MULTIPLE_URLPATH', ROOT_URLPATH . "shopping/multiple.php");
/** Myページトップ */
define('URL_MYPAGE_TOP', HTTPS_URL . "mypage/login.php");
/** 購入確認ページ */
define('SHOPPING_CONFIRM_URLPATH', ROOT_URLPATH . "shopping/confirm.php");
/** お支払い方法選択ページ */
define('SHOPPING_PAYMENT_URLPATH', ROOT_URLPATH . "shopping/payment.php");
/** 購入完了画面 */
define('SHOPPING_COMPLETE_URLPATH', ROOT_URLPATH . "shopping/complete.php");
/** モジュール追加用画面 */
define('SHOPPING_MODULE_URLPATH', ROOT_URLPATH . "shopping/load_payment_module.php");
/** 商品詳細(HTML出力) */
define('P_DETAIL_URLPATH', ROOT_URLPATH . "products/detail.php?product_id=");
/** マイページお届け先URL */
define('MYPAGE_DELIVADDR_URLPATH', ROOT_URLPATH . "mypage/delivery.php");
/** マイページ登録内容変更URL */
define('MYPAGE_CHANGE_URLPATH', ROOT_URLPATH . "mypage/change.php");
/** マイページ注文履歴URL */
define('MYPAGE_HISTORY_URLPATH', ROOT_URLPATH . "mypage/history_list.php");
/** メールアドレス種別 */
define('MAIL_TYPE_PC', 1);
/** メールアドレス種別 */
define('MAIL_TYPE_MOBILE', 2);
/** 新着情報管理画面 開始年(西暦) */
define('ADMIN_NEWS_STARTYEAR', 2005);
/** 会員登録 */
define('ENTRY_CUSTOMER_TEMP_SUBJECT', "会員仮登録が完了いたしました。");
/** 会員登録 */
define('ENTRY_CUSTOMER_REGIST_SUBJECT', "本会員登録が完了いたしました。");
/** 再入会制限時間 (単位: 時間) */
define('ENTRY_LIMIT_HOUR', 1);
/** 関連商品表示数 */
define('RECOMMEND_PRODUCT_MAX', 6);
/** おすすめ商品表示数 */
define('RECOMMEND_NUM', 8);
/** お届け可能日以降のプルダウン表示最大日数 */
define('DELIV_DATE_END_MAX', 21);
/** 購入時強制会員登録(1:有効　0:無効) */
define('PURCHASE_CUSTOMER_REGIST', 0);
/** 支払期限 */
define('CV_PAYMENT_LIMIT', 14);
/** 商品レビューでURL書き込みを許可するか否か */
define('REVIEW_ALLOW_URL', 0);
/** Pear::Mail バックエンド:mail|smtp|sendmail */
define('MAIL_BACKEND', "smtp");
/** SMTPサーバー */
define('SMTP_HOST', "127.0.0.1");
/** SMTPポート */
define('SMTP_PORT', "25");
/** アップデート時にサイト情報を送出するか */
define('UPDATE_SEND_SITE_INFO', false);
/** ポイントを利用するか(true:利用する、false:利用しない) (false は一部対応) */
define('USE_POINT', false);
/** 在庫無し商品の非表示(true:非表示、false:表示) */
define('NOSTOCK_HIDDEN', false);
/** モバイルサイトを利用するか(true:利用する、false:利用しない) (false は一部対応) */
define('USE_MOBILE', true);
/** デフォルトテンプレート名(PC) */
define('DEFAULT_TEMPLATE_NAME', "white");
/** デフォルトテンプレート名(モバイル) */
define('MOBILE_DEFAULT_TEMPLATE_NAME', "mobile");
/** デフォルトテンプレート名(スマートフォン) */
define('SMARTPHONE_DEFAULT_TEMPLATE_NAME', "sphone");
/** テンプレート名 */
define('TEMPLATE_NAME', "white");
/** モバイルテンプレート名 */
define('MOBILE_TEMPLATE_NAME', "mobile");
/** スマートフォンテンプレート名 */
define('SMARTPHONE_TEMPLATE_NAME', "sphone");
/** SMARTYテンプレート */
define('SMARTY_TEMPLATES_REALDIR',  DATA_REALDIR . "Smarty/templates/");
/** SMARTYテンプレート(PC) */
define('TEMPLATE_REALDIR', SMARTY_TEMPLATES_REALDIR . TEMPLATE_NAME . "/");
/** SMARTYテンプレート(管理機能) */
define('TEMPLATE_ADMIN_REALDIR', SMARTY_TEMPLATES_REALDIR . "admin/");
/** SMARTYコンパイル */
define('COMPILE_REALDIR', DATA_REALDIR . "Smarty/templates_c/" . TEMPLATE_NAME . "/");
/** SMARTYコンパイル(管理機能) */
define('COMPILE_ADMIN_REALDIR', DATA_REALDIR . "Smarty/templates_c/admin/");
/** ブロックファイル保存先 */
define('BLOC_DIR', "frontparts/bloc/");
/** SMARTYテンプレート(mobile) */
define('MOBILE_TEMPLATE_REALDIR', SMARTY_TEMPLATES_REALDIR . MOBILE_TEMPLATE_NAME . "/");
/** SMARTYコンパイル(mobile) */
define('MOBILE_COMPILE_REALDIR', DATA_REALDIR . "Smarty/templates_c/" . MOBILE_TEMPLATE_NAME . "/");
/** SMARTYテンプレート(smart phone) */
define('SMARTPHONE_TEMPLATE_REALDIR', SMARTY_TEMPLATES_REALDIR . SMARTPHONE_TEMPLATE_NAME . "/");
/** SMARTYコンパイル(smartphone) */
define('SMARTPHONE_COMPILE_REALDIR', DATA_REALDIR . "Smarty/templates_c/" . SMARTPHONE_TEMPLATE_NAME . "/");
/** SMARTYタグテンプレート(PC) */
define('TAG_TEMPLATE_REALDIR', TEMPLATE_REALDIR . "tag_templates/");
/** SMARTYタグテンプレート(スマートフォン) */
define('SMARTPHONE_TAG_TEMPLATE_REALDIR', SMARTPHONE_TEMPLATE_REALDIR . "tag_templates/");
/** EメールアドレスチェックをRFC準拠にするか(true:準拠する、false:準拠しない) */
define('RFC_COMPLIANT_EMAIL_CHECK', false);
/** モバイルサイトのセッションの存続時間 (秒) */
define('MOBILE_SESSION_LIFETIME', 1800);
/** 携帯電話向け変換画像保存ディレクトリ */
define('MOBILE_IMAGE_REALDIR', HTML_REALDIR . "upload/mobile_image/");
/** 携帯電話向け変換画像保存ディレクトリ */
define('MOBILE_IMAGE_URLPATH', ROOT_URLPATH . "upload/mobile_image/");
/** モバイルURL */
define('MOBILE_TOP_URLPATH', ROOT_URLPATH . DIR_INDEX_PATH);
/** カートトップ */
define('MOBILE_CART_URLPATH', ROOT_URLPATH . "cart/" . DIR_INDEX_PATH);
/** 会員情報入力 */
define('MOBILE_SHOPPING_URL', HTTPS_URL . "shopping/" . DIR_INDEX_PATH);
/** 購入確認ページ */
define('MOBILE_SHOPPING_CONFIRM_URLPATH', ROOT_URLPATH . "shopping/confirm.php");
/** お支払い方法選択ページ */
define('MOBILE_SHOPPING_PAYMENT_URLPATH', ROOT_URLPATH . "shopping/payment.php");
/** 商品詳細(HTML出力) */
define('MOBILE_P_DETAIL_URLPATH', ROOT_URLPATH . "products/detail.php?product_id=");
/** 購入完了画面 */
define('MOBILE_SHOPPING_COMPLETE_URLPATH', ROOT_URLPATH . "shopping/complete.php");
/** モジュール追加用画面 */
define('MOBILE_SHOPPING_MODULE_URLPATH', ROOT_URLPATH . "shopping/load_payment_module.php");
/** セッション維持方法：useCookie|useRequest */
define('SESSION_KEEP_METHOD', "useCookie");
/** セッションの存続時間 (秒) */
define('SESSION_LIFETIME', 1800);
/** オーナーズストアURL */
define('OSTORE_URL', "http://store.ec-cube.net/");
/** オーナーズストアURL */
define('OSTORE_SSLURL', "https://store.ec-cube.net/");
/** オーナーズストアログパス */
define('OSTORE_LOG_REALFILE', DATA_REALDIR . "logs/ownersstore.log");
/** オーナーズストア通信ステータス */
define('OSTORE_STATUS_ERROR', "ERROR");
/** オーナーズストア通信ステータス */
define('OSTORE_STATUS_SUCCESS', "SUCCESS");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_UNKNOWN', "1000");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_INVALID_PARAM', "1001");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_NO_CUSTOMER', "1002");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_WRONG_URL_PASS', "1003");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_NO_PRODUCTS', "1004");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_NO_DL_DATA', "1005");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_DL_DATA_OPEN', "1006");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_DLLOG_AUTH', "1007");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_C_ADMIN_AUTH', "2001");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_C_HTTP_REQ', "2002");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_C_HTTP_RESP', "2003");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_C_FAILED_JSON_PARSE', "2004");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_C_NO_KEY', "2005");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_C_INVALID_ACCESS', "2006");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_C_INVALID_PARAM', "2007");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_C_AUTOUP_DISABLE', "2008");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_C_PERMISSION', "2009");
/** オーナーズストア通信エラーコード */
define('OSTORE_E_C_BATCH_ERR', "2010");
/** お気に入り商品登録(有効:1 無効:0) */
define('OPTION_FAVOFITE_PRODUCT', 1);
/** 画像リネーム設定 (商品画像のみ) (true:リネームする、false:リネームしない) */
define('IMAGE_RENAME', true);
/** プラグインディレクトリ */
define('PLUGIN_DIR', "plugins/");
/** プラグイン保存先 */
define('PLUGIN_REALDIR', USER_REALDIR . PLUGIN_DIR);
/** プラグイン URL */
define('PLUGIN_URL', USER_URL . PLUGIN_DIR);
/** 日数桁数 */
define('DOWNLOAD_DAYS_LEN', 3);
/** ダウンロードファイル登録可能拡張子(カンマ区切り)" */
define('DOWNLOAD_EXTENSION', "zip,lzh,jpg,jpeg,gif,png,mp3,pdf,csv");
/** ダウンロード販売ファイル用サイズ制限(KB) */
define('DOWN_SIZE', 50000);
/** 1:実商品 2:ダウンロード */
define('DEFAULT_PRODUCT_DOWN', 1);
/** ダウンロードファイル一時保存 */
define('DOWN_TEMP_REALDIR', DATA_REALDIR . "download/temp/");
/** ダウンロードファイル保存先 */
define('DOWN_SAVE_REALDIR', DATA_REALDIR . "download/save/");
/** ダウンロードファイル存在エラー */
define('DOWNFILE_NOT_FOUND', 22);
/** ダウンロード販売機能用オンライン決済payment_id(カンマ区切り) */
define('ONLINE_PAYMENT', "1");
/** ダウンロード販売機能 ダウンロードファイル読み込みバイト(KB) */
define('DOWNLOAD_BLOCK', 1024);
/** 新規注文 */
define('ORDER_NEW', 1);
/** 入金待ち */
define('ORDER_PAY_WAIT', 8);
/** 入金済み */
define('ORDER_PRE_END', 3);
/** キャンセル */
define('ORDER_CANCEL', 6);
/** 取り寄せ中 */
define('ORDER_BACK_ORDER', 4);
/** 発送済み */
define('ORDER_DELIV', 2);
/** 決済処理中 */
define('ORDER_PENDING', 7);
/** 通常商品 */
define('PRODUCT_TYPE_NORMAL', 1);
/** ダウンロード商品 */
define('PRODUCT_TYPE_DOWNLOAD', 2);
/** SQLログを取得するフラグ(1:表示, 0:非表示) */
define('SQL_QUERY_LOG_MODE', 1);
/** SQLログを取得する時間設定(設定値以上かかった場合に取得) */
define('SQL_QUERY_LOG_MIN_EXEC_TIME', 2);
/** ページ表示時間のログを取得するフラグ(1:表示, 0:非表示) */
define('PAGE_DISPLAY_TIME_LOG_MODE', 1);
/** ページ表示時間のログを取得する時間設定(設定値以上かかった場合に取得) */
define('PAGE_DISPLAY_TIME_LOG_MIN_EXEC_TIME', 2);
/** 端末種別: モバイル */
define('DEVICE_TYPE_MOBILE', 1);
/** 端末種別: スマートフォン */
define('DEVICE_TYPE_SMARTPHONE', 2);
/** 端末種別: PC */
define('DEVICE_TYPE_PC', 10);
/** 端末種別: 基幹 */
define('DEVICE_TYPE_KIKAN', 20);
/** 端末種別: 管理画面 */
define('DEVICE_TYPE_ADMIN', 99);
/** 顧客区分: 一般 */
define('CUSTOMER_KBN_NORMAL', 0);
/** 顧客区分: 社員 */
define('CUSTOMER_KBN_EMPLOYEE', 1);
/** 顧客区分: 公用 */
define('CUSTOMER_KBN_OFFICIAL', 2);
/** 配置ID: 未使用 */
define('TARGET_ID_UNUSED', 0);
/** 配置ID: LeftNavi */
define('TARGET_ID_LEFT', 1);
/** 配置ID: MainHead */
define('TARGET_ID_MAIN_HEAD', 2);
/** 配置ID: RightNavi */
define('TARGET_ID_RIGHT', 3);
/** 配置ID: MainFoot */
define('TARGET_ID_MAIN_FOOT', 4);
/** 配置ID: TopNavi */
define('TARGET_ID_TOP', 5);
/** 配置ID: BottomNavi */
define('TARGET_ID_BOTTOM', 6);
/** 配置ID: HeadNavi */
define('TARGET_ID_HEAD', 7);
/** 配置ID: HeadTopNavi */
define('TARGET_ID_HEAD_TOP', 8);
/** 配置ID: FooterBottomNavi */
define('TARGET_ID_FOOTER_BOTTOM', 9);
/** 配置ID: HeaderInternalNavi */
define('TARGET_ID_HEADER_INTERNAL', 10);
/** CSV入出力列設定有効無効フラグ: 有効 */
define('CSV_COLUMN_STATUS_FLG_ENABLE', 1);
/** CSV入出力列設定有効無効フラグ: 無効 */
define('CSV_COLUMN_STATUS_FLG_DISABLE', 2);
/** CSV入出力列設定読み書きフラグ: 読み書き可能 */
define('CSV_COLUMN_RW_FLG_READ_WRITE', 1);
/** CSV入出力列設定読み書きフラグ: 読み込みのみ可能 */
define('CSV_COLUMN_RW_FLG_READ_ONLY', 2);
/** CSV入出力列設定読み書きフラグ: キー列 */
define('CSV_COLUMN_RW_FLG_KEY_FIELD', 3);
/** 無制限フラグ： 無制限 */
define('UNLIMITED_FLG_UNLIMITED', "1");
/** 無制限フラグ： 制限有り */
define('UNLIMITED_FLG_LIMITED', "0");
/**  EC-CUBE更新情報取得 (true:取得する false:取得しない) */
define('ECCUBE_INFO', false);
/** 外部サイトHTTP取得タイムアウト時間(秒) */
define('HTTP_REQUEST_TIMEOUT', "5");
/** プラグインの状態：アップロード済み */
define('PLUGIN_STATUS_UPLOADED', "1");
/** プラグインの状態：インストール済み */
define('PLUGIN_STATUS_INSTALLED', "2");
/** プラグイン有効/無効：有効 */
define('PLUGIN_ENABLE_TRUE', "1");
/** プラグイン有効/無効：無効 */
define('PLUGIN_ENABLE_FALSE', "2");
/** 郵便番号CSVのZIPアーカイブファイルの取得元 */
define('ZIP_DOWNLOAD_URL', "http://www.post.japanpost.jp/zipcode/dl/kogaki/zip/ken_all.zip");
/** 初回購入除外支払方法 */
define('INIT_EXCLUSION_PAYMENT_ID', "4");
/** マダムアルバURL */
define('MADAMEALBA_URL', "http://www.madamealba.com");
/** アクアミスティークURL */
define('STELLA_MISTIQ_URL', "http://stella-mistiq.com");
/** ヘッダー・フッター表示用デフォルトブランド区分 */
define('DEFAULT_BRAND_KBN', "");
/** マダムアルバブランド区分 */
define('MADAMEALBA_BRAND_KBN', "0");
/** アクアミスティークブランド区分 */
define('STELLA_MISTIQ_BRAND_KBN', "1");
/** 割引期間 */
define('DISCOUNT_END_DATE', "20130831");
/** 設定金額以上なら割引 */
define('DISCOUNT_PRICE_OVER1', 10500);
define('DISCOUNT_PRICE_OVER2', 8400);
define('DISCOUNT_PRICE_OVER3', 5250);
/** 割引率(%) */
define('DISCOUNT_PRICE_RATE1', 20);
define('DISCOUNT_PRICE_RATE2', 15);
define('DISCOUNT_PRICE_RATE3', 10);
/** 割引対象外商品 */
//define('DISCOUNT_NO_PRODUCT_CLASS_ID', "58,73,74,75,47,44,43,56,104,55,54,53,52,46,45,94");
define('DISCOUNT_NO_PRODUCT_CLASS_ID', "58,73,74,75,47,44,43,56,104,55,54,53,52,46,45,94,100,102,71,70,91,96,88,95,68,60,83,85,81,82,78,67,65,66,62,63,59,57,50,51");
/** 送料デフォルト計算 */
define('DEFALUT_DELIV_AMOUNT', 300);
/** セット商品（プレゼント）(購入商品:プレゼント商品,プレゼント商品;)*/
/** 最後に「;」を付加しない **/
//define('PRESENT_PRODUCT_CLASS_ID', "");
define('PRESENT_PRODUCT_CLASS_ID', "96:105;91:105;95:105;88:105;70:105;71:105");

/** 支払方法ID(クレジットカード) */
define('PAYMENT_ID_CREDIT', 2);
/** 支払方法ID(代金引換) */
define('PAYMENT_ID_DAIBIKI', 1);
/** 支払方法ID(振込) */
define('PAYMENT_ID_FURIKOMI', 4);
/** 配送業者ID(ヤマト運輸) */
define('DELIV_ID_YAMATO', 1);
/** 配送業者ID(佐川急便) */
define('DELIV_ID_SAGAWA', 2);
/** 配送業者ID(ヤマト運輸メール便) */
define('DELIV_ID_YAMATO_MAIL', 3);
/** 産直品フラグ(紀泉) */
define('DROP_SHIPMENT_FLG_OFF', '1');
/** 産直品フラグ(産直) */
define('DROP_SHIPMENT_FLG_ON', '2');
/** 配送区分(通常) */
define('DELIV_KBN_NORMAL', '0');
/** 配送区分(ワレモノ) */
define('DELIV_KBN_BREAKABLES', '1');
/** 配送区分(なまもの) */
define('DELIV_KBN_PERISHABLES', '2');
/** 冷凍冷蔵区分(通常) */
define('COOL_KBN_NORMAL', '0');
/** 冷凍冷蔵区分(冷蔵) */
define('COOL_KBN_REIZOU', '1');
/** 冷凍冷蔵区分(冷凍) */
define('COOL_KBN_REITOU', '2');
/** 定期購入フラグ */
define('REGULAR_PURCHASE_FLG_OFF', '0');
define('REGULAR_PURCHASE_FLG_ON',  '1');
/** ユーザ権限 更新 */
define('UPDATE_AUTH_OFF', 0);
define('UPDATE_AUTH_ON',  1);
/** ユーザ権限 CSVダウンロード */
define('CSV_DOWNLOAD_AUTH_OFF', 0);
define('CSV_DOWNLOAD_AUTH_ON',  1);
/** ユーザ権限 基幹連携 */
define('INOS_AUTH_OFF', 0);
define('INOS_AUTH_ON',  1);
/** ユーザ権限 顧客・受注メニュー */
define('CRITICAL_MENU_OFF', 0);
define('CRITICAL_MENU_ON',  1);
/** データ種別 基幹連携 */
define('INOS_DATA_TYPE_SEND_CUSTOMER',   1); // 1：顧客送信
define('INOS_DATA_TYPE_SEND_ORDER',      2); // 2：受注送信
define('INOS_DATA_TYPE_SEND_REGULAR',    3); // 3：定期送信
define('INOS_DATA_TYPE_RECV_CUSTOMER',   4); // 4：顧客取込
define('INOS_DATA_TYPE_RECV_ORDER',      5); // 5：受注取込
define('INOS_DATA_TYPE_REVC_REGULAR',    6); // 6：定期取込
define('INOS_DATA_TYPE_REVC_PRODUCT',    7); // 7：商品取込
define('INOS_DATA_TYPE_REVC_PROMOTION',  8); // 8：プロモーション取込
/** エラーフラグ 基幹連携 */
define('INOS_ERROR_FLG_EXIST_NORMAL', 0); // 0:正常
define('INOS_ERROR_FLG_EXIST_ERROR',  1); // 1:エラーあり
/** 送信フラグ 基幹連携 */
define('INOS_SEND_FLG_OFF', 0); // 0：未送信
define('INOS_SEND_FLG_ON',  1); // 1：送信済
/** 削除フラグ 基幹連携 */
define('INOS_DEL_FLG_OFF', 0);
define('INOS_DEL_FLG_ON',  1);
/** 顧客取込 個人情報区分 基幹連携 */
define('INOS_CUSTOMER_PRIVACY_KBN_NORMAL', 0); // 希望しない
define('INOS_CUSTOMER_PRIVACY_KBN_DELETE', 1); // 削除希望
/** 顧客取込 償却顧客区分 基幹連携 */
define('INOS_CUSTOMER_KASHIDAORE_KBN_NORMAL',     0); // 通常顧客
define('INOS_CUSTOMER_KASHIDAORE_KBN_KASHIDAORE', 1); // 貸倒顧客
/** 宅配便CD 基幹連携 */
define('INOS_DELIV_ID_YAMATO', 0); // ヤマト
define('INOS_DELIV_ID_SAGAWA', 1); // 佐川
/** 定期受注状況(0:受注中、1:購入中、6:休止、7:保留、9:解約)  */
define('REGULAR_ORDER_STATUS_ORDER',    0);
define('REGULAR_ORDER_STATUS_PURCHASE', 1);
define('REGULAR_ORDER_STATUS_PAUSE',    6);
define('REGULAR_ORDER_STATUS_HOLD',     7);
define('REGULAR_ORDER_STATUS_CANCEL',   9);
/** お届け間隔 */
define('TODOKE_CYCLE_DAY',    1); // 日ごと
define('TODOKE_CYCLE_MONTH',  2); // ヶ月ごと
/** お届け日指定区分 */
define('TODOKE_KBN_DAY',  1); // 届け日指定
define('TODOKE_KBN_WEEK', 2); // 曜日指定
/** コースCD 単回購入 */
define('COURSE_CD_NOT_REGULAR', 0);
/** コースCD [日ごと]の上限、下限 */
define('COURSE_CD_DAY_MIN',   10);
define('COURSE_CD_DAY_MAX',   90);
/** コースCD [月ごと]の上限、下限 */
define('COURSE_CD_MONTH_MIN',   1);
define('COURSE_CD_MONTH_MAX',   3);
/** 送信済みフラグ(0:送信済、1:未送信) */
define('SEND_FLG_UNSENT', 0);
define('SEND_FLG_SENT',   1);
/** 販売対象フラグ(0:対象外、1:販売対象) */
define('SELL_FLG_OFF', 0);
define('SELL_FLG_ON',  1);
/** フォローメール送信結果 */
define('SEND_RESULT_COMPLETE', 1); //成功
define('SEND_RESULT_ERROR',    2); //エラー
/** 明細書(請求書)同梱区分 */
define('INCLUDE_KBN_BESSOU', 0); // 別送
define('INCLUDE_KBN_DOUKON', 1); // 同梱
/** 箱ID */
define('DELIV_BOX_ID_TAKUHAI', '0'); // 宅配便
define('DELIV_BOX_ID_MAIL',    '1'); // メール便 
/** 企画種別 */
define('PLANNING_TYPE_CAMPAIGN', 1); // キャンペーン
define('PLANNING_TYPE_ENQUETE',  2); // アンケート用
/** プロモーション数量区分 */
define('PROMOTION_QUANTITY_KBN_DETAIL', '1'); // 明細
define('PROMOTION_QUANTITY_KBN_ALL',    '2'); // 全明細
/** プロモーション適用年数 */
define('PROMOTION_USE_YEAR', '3'); // 適用期間３年前から現在
/** プロモーションコース区分 */
define('PROMOTION_COURSE_KBN_REGULAR', '1'); // 定期
define('PROMOTION_COURSE_KBN_ALL', '9'); // 全体
/** プロモーション受注区分 */
define('PROMOTION_ORDER_KBN_WEB', '3'); // WEB
/** プロモーション区分 */
define('PROMOTION_KBN_DISCOUNT', '1'); // 値引き
define('PROMOTION_KBN_SEND',     '2'); // 送料
define('PROMOTION_KBN_INCLUDE',  '3'); // 同梱品
/** プロモーション有効区分 */
define('PROMOTION_VALID_KBN_OFF', '0'); // 無効
define('PROMOTION_VALID_KBN_ON',  '1'); // 有効
/** プロモーション送料区分 */
define('PROMOTION_DELIV_FEE_KBN_FREE',    '0'); // 無料
define('PROMOTION_DELIV_FEE_KBN_NO_FREE', '1'); // 有料
/** 購入できる合計金額の上限 */
define('PAYMENT_TOTAL_LIMIT', 100000);
/** 初回購入時の振込可能な金額閾値 */
define('UPPER_RULE_FURIKOMI_FIRST_TIME', 4999);
/** メールテンプレートID */
define('MAIL_TEMPLATE_ID_ORDER_COMP',         1);
define('MAIL_TEMPLATE_ID_ORDER_COMP_MOBILE',  2);
define('MAIL_TEMPLATE_ID_ORDER_CANCEL',       3);
define('MAIL_TEMPLATE_ID_PAYMENT_CONF',       4);
define('MAIL_TEMPLATE_ID_CONTACT_COMP',       5);
define('MAIL_TEMPLATE_ID_SHIPPING_COMP',      6);
define('MAIL_TEMPLATE_ID_SHIPPING_MAIL_COMP', 7);
/** カートボタン表示フラグ */
define('BUTTON_DISP_FLG_ON', '1'); // 表示する
/** 会員ステータス  */
define('CUSTOMER_STATUS_NOT_MEMBER', '1'); // 仮会員
define('CUSTOMER_STATUS_MEMBER',     '2'); // 本会員
/** お届け希望時間指定(指定なし:0)  */
define('DELIV_TIME_ID_NONE', '0');

define('CAMPAIGN_PARAM_STR', 'campaign');

// 受注プロモーション最大処理件数 ※受注情報毎
define('ORDER_PROMOTION_MAX_COUNT', 50);
// 配送案内メール最大処理件数 ※配送情報毎
define('SHIPPING_MAIL_MAX_COUNT', 100);

// パンくずHOME表記
define('TPL_PC_HOME_NAME', 'ソヤファームクラブ HOME');

// メールタイトル
define('MAIL_TITLE_SHOP_NAME', 'ソヤファームクラブ');

$mailSignature =<<<EOF
ソヤファームクラブ　http://www.soyafarm.com/
フリーダイヤル:0120-39-3009　受付時間:9時～19時(日・祝休み)
▼お問い合わせフォームはこちら
https://www.soyafarm.com/shop/contact/
EOF;

// メール署名
define('MAIL_COMMON_SIGNATURE', $mailSignature);

// 定期編集用SESSION　KEY情報
define('CART_NORMAL_KEY', 'cart');
// 定期編集用SESSION　KEY情報
define('CART_REGULAR_KEY', 'regular_cart');

// 定期次回お届け日有効未来ヶ月
define('REGULAR_FUTURE_MONTH', 3);

// エラーメール送信先
define('DB_ERROR_MAIL_TO', "soyafarm@iqueve.co.jp");

// 定期1ヶ月指定商品(「,」区切りにて指定)
define('REGULAR_ONE_MONTH_PRODUCTS', "000018");

// 定期お届け日指定(「,」区切りにて指定)
define('REGULAR_DELIV_PATTERN', "1,5,10,15,20,25");

// 顧客マスタデフォルト割引コード
define('DEFAULT_CUSTOMER_TYPE_CD', "1");

?>
