<?php
/**
 * GC_Utils_SBIVT3G.php - GC_Utils_SBIVT3G クラスを定義
 *
 * LICENSE: （ライセンスに関する情報）
 *
 * @category    Veritrans
 * @package     Mdl_SBIVT3G
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version     $Id: GC_Utils_SBIVT3G.php 193 2013-07-31 01:24:57Z kaji $
 * @link        http://www.veritrans.co.jp/3gps
*/

/**
 * 3Gモジュール共通処理クラス
 *
 * @category    Veritrans
 * @package     Lib
 * @copyright   2011 SBI VeriTrans Co., Ltd.
 * @license     http://www.veritrans.co.jp/3gpslicense   Veritrans License
 * @version    Release: @package_version@
 * @link        http://www.veritrans.co.jp/3gps
 * @access  public
 * @author  K.Hiranuma
 */
class GC_Utils_SBIVT3G {

    /**
     * バージョン比較
     *
     * @access public
     * @param string $target 比較対象バージョン
     * @return integer 0:同じ 0<:対象の方が新しい 0>:対象の方が古い
     */
    function compareVersion($target) {
        // 現状
        $arr = explode('.', ECCUBE_VERSION);
        $major  = isset($arr[0])? intval($arr[0]) : 0;
        $middle = isset($arr[1])? intval($arr[1]) : 0;
        $minor  = isset($arr[2])? intval($arr[2]) : 0;

        // 対象
        $arrTarget = explode('.', $target);
        $target_major  = isset($arrTarget[0])? intval($arrTarget[0]) : 0;
        $target_middle = isset($arrTarget[1])? intval($arrTarget[1]) : 0;
        $target_minor  = isset($arrTarget[2])? intval($arrTarget[2]) : 0;

        if ($major != $target_major) {
            return $major - $target_major;
        } else if ($middle != $target_middle) {
            return $middle - $target_middle;
        } else {
            return $minor - $target_minor;
        }
    }

    /**
     * 3G MDK仕様の受注番号を返す
     *
     * @access public
     * @param integer $orderId 受注番号
     * @return string ゼロパディングされた受注番号
     */
    function getMdkOrderId($orderId)
    {
        $objSetting = SC_Helper_SBIVT3G_Setting::getSingletonInstance();
        $prefix = $objSetting->arrSettings['dummyModePrefix'];

        return sprintf("%s%011d", $prefix, $orderId);
    }

    /**
     * 改行コードを除去
     *
     * @access public
     * @param  string $string 対象
     * @return string 改行を除いた文字列
     */
    function removeLf($string) {
        $array = array("\r", "\n");
        return str_replace($array, '', $string);
    }

    /**
     * trAdをPCブラウザ表示
     * 2012/07/24 バージョンアップに伴い変更
     *
     * @access public
     * @param  string $vResultCode 結果詳細コード
     * @param  string $tradUrl trAd-URL
     * @return string HTML(JavaScript)コード
     */
    function setShowAd($vResultCode, $tradUrl) {
        // trAdライブラリパス
        $path = ROOT_URLPATH . MDL_SBIVT3G_TRAD_JS_SET_PATH;
        $html = <<<EOD
<script type="text/javascript" src="$path"></script>
<script type="text/javascript">showAdv('$vResultCode', '$tradUrl');</script>
EOD;
        return GC_Utils_SBIVT3G::removeLf($html);
    }

    /**
     * trAdを携帯ブラウザ表示
     *
     * @access public
     * @param  string $vResultCode 結果詳細コード
     * @param  string $tradUrl trAd-URL
     * @return string HTMLコード
     * @see tgSample-php-130.tar.gz -> tgSample-PHP/web/trad/trad.php
     */
    function setShowAdForMobile($adurl) {
        if (empty($adurl)) {
            return;
        }
        $adurl .= time();

        $bannerurl = GC_Utils_SBIVT3G::file_get_contents($adurl);

        $bannerurl = strtolower(strtr(urldecode($bannerurl), '"', "'"));
        $startindex = strpos($bannerurl, "<a");
        $endindex = strpos($bannerurl, "<img") - $startindex;

        $atag = substr($bannerurl, $startindex, $endindex);

        $imgtag = strstr($bannerurl, "<img");
        $imgtag = substr($imgtag, 0, strpos($imgtag, "</a>"));

        $target = "";
        if (strpos($imgtag, "https:")) {
            $target = "https:";
        } else {
            $target = "http:";
        }
        $imgsrc = substr($imgtag, strpos($imgtag, $target));
        $imgsrc = substr($imgsrc, 0, strpos($imgsrc, ".gif") + 4);

        //return "{$atag}<img src=\"../trad/adimage.php?url={$imgsrc}\" border=0></a>";
        return "<div align=\"center\">$atag<img src=\"$imgsrc\"/></a></div>";
    }

    /**
     * trAdをPCブラウザ表示(MPI用)
     * 2012/07/24 バージョンアップに伴い変更
     *
     * @access public
     * @param  string $tradUrl trAd-URL
     * @return string HTML(JavaScript)コード
     */
    function setShowAdForMpi($tradUrl) {
        // trAdライブラリパス
        $path = ROOT_URLPATH . MDL_SBIVT3G_TRAD_JS_SET_PATH;

        $html = <<<EOD
<script type="text/javascript" src="$path"></script>
<script type="text/javascript">$js showAdv4Mpi('$tradUrl');</script>
EOD;
        return GC_Utils_SBIVT3G::removeLf($html);
    }

    /**
     * trAdをスマートフォンブラウザ表示
     * 2012/07/24 バージョンアップに伴い追加
     *
     * @access public
     * @param  string $vResultCode 結果詳細コード
     * @param  string $tradUrl trAd-URL
     * @return string HTML(JavaScript)コード
     */
    function setShowAdForSP($vResultCode, $tradUrl) {
        // trAdライブラリパス
        $path = ROOT_URLPATH . MDL_SBIVT3G_TRAD_JS_SET_PATH;
        $html = <<<EOD
<script type="text/javascript" src="$path"></script>
<script type="text/javascript">$js showAdv4SP('$vResultCode', '$tradUrl');</script>
EOD;
        return GC_Utils_SBIVT3G::removeLf($html);
    }

    /**
     * file_get_contents()の代替処理
     *
     * @access pubulic
     * @param string $filepath
     * @return string
     */
    function file_get_contents($filepath) {
        if (function_exists('file_get_contents') == true) {
            $content = file_get_contents($filepath);
        } else {
            $fp = fopen($filepath, 'r');
            $content = fread($fp, filesize($filepath));
            fclose($fp); 
        }
        return $content;
    }

    /**
     * システム時刻に引数日数を加算した日付を指定したフォーマットで返す
     *
     * @access pubulic
     * @param integer $days 日数
     * @param string  $format 日付フォーマット
     * @return string
     */
    function getAddDateFormat($days = 0, $format = 'Y/m/d') {
        return date($format, strtotime(sprintf('+ %d day', $days)));
    }

    /**
     * UserAgentからPaSoRi対応のブラウザかを判定する
     *
     * @access pubulic
     * @return boolean
     */
    function isValidBrowserForPasori() {
        $ptn = '|compatible; MSIE (\d+\.\d+); |';
        if (preg_match($ptn, $_SERVER['HTTP_USER_AGENT'], $m) == false) {
            return false;
        }

        if ((int)$m[1] < 7) {
            return false;
        }

        return true;
    }

    /**
     * UserAgentから電子マネーアプリ対応のスマートフォンブラウザかを判定する
     *
     * @access pubulic
     * @return boolean
     */
    function isValidSphoneForEMoney() {
        /*
        // 現在はAndroidのみを対象にする
        $ptn = '|Android( \d+(\.\d+)+)?(-update\d+)?;?|';
        if (preg_match($ptn, $_SERVER['HTTP_USER_AGENT'], $m) == false) {
            return false;
        }
        return true;
         */
        // 対応しない
        return false;
    }

    /**
     * 再取引データの取得
     *
     * @access protected
     * @param  $customerId 顧客ID
     * @return array 再取引カード情報
     */
    function getReTradeCard($customerId) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sql = <<<EOD
SELECT
  order_id,
  memo04,
  memo06,
  create_date
FROM dtb_order
WHERE del_flg <> 1
AND   customer_id <> 0
AND   customer_id = ?
AND   status NOT IN ( ?, ? )
AND   create_date > ?
AND   payment_id IN (
  SELECT payment_id FROM dtb_payment
  WHERE del_flg <> 1
  AND memo01 = ?
  ORDER BY update_date DESC
)
AND   ( memo06 IS NOT NULL AND memo06 <> '')
ORDER BY order_id DESC
LIMIT ?
EOD;
        $limitDate = date('Y-m-d 00:00:00',
            strtotime(sprintf('- %d month', MDL_SBIVT3G_RETRADE_VALID_MONTH)));
        $arrParam = array(
            $customerId,
            ORDER_CANCEL,
            ORDER_PENDING,
            $limitDate,
            MDL_SBIVT3G_INNER_ID_CREDIT,
            MDL_SBIVT3G_RETRADE_TARGET_LIMIT,
        );
        $arrRows = $objQuery->getAll($sql, $arrParam);

        // 連想配列にする
        $arrList = array();
        foreach ($arrRows as $row) {
            // 格納
            $arrList[$row['memo04']] = array(
                'orderId' => $row['order_id'],
                'maskedNo' => preg_replace('/\*+/', '***', $row['memo06']),
                'orderDate' => substr($row['create_date'], 0, 10)
            );
        }
        return $arrList;
    }

    /**
     * 内部決済ID取得
     *
     * @access public
     * @param string $paymentId   支払方法ID
     * @return boolean 
     */
    function getInnerPayment($paymentId) {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $sql = 'SELECT memo01 FROM dtb_payment WHERE payment_id = ?';
        $innerPayment = $objQuery->getOne($sql, array($paymentId));
        return $innerPayment;
    }

    /**
     * サービス名を取得
     *
     * @access public
     * @param string $innerPayment 内部決済ID
     * @return mixed サービス名
     */
    function getServiceName($innerPayment = '') {
        $arrNames = array(
            MDL_SBIVT3G_INNER_ID_CREDIT           => PAYMENT_NAME_CREDIT,
            MDL_SBIVT3G_INNER_ID_CVS              => PAYMENT_NAME_CONVENI,
            MDL_SBIVT3G_INNER_ID_PAYEASY_ATM      => PAYMENT_NAME_ATM,
            MDL_SBIVT3G_INNER_ID_PAYEASY_NET      => PAYMENT_NAME_NETBANK,
            MDL_SBIVT3G_INNER_ID_EDY_MOBILE_MAIL  => PAYMENT_NAME_EDY_MOBILE,
            MDL_SBIVT3G_INNER_ID_EDY_PC_APP       => PAYMENT_NAME_EDY_CYBER,
            MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_MAIL
                => PAYMENT_NAME_SUICA_MOBILE_MAIL,
            MDL_SBIVT3G_INNER_ID_SUICA_MOBILE_APP
                => PAYMENT_NAME_SUICA_MOBILE_APP,
            MDL_SBIVT3G_INNER_ID_SUICA_PC_MAIL
                => PAYMENT_NAME_SUICA_PC_MAIL,
            MDL_SBIVT3G_INNER_ID_SUICA_PC_APP     => PAYMENT_NAME_SUICA_PC_APP,
            MDL_SBIVT3G_INNER_ID_WAON_MOBILE_APP  => PAYMENT_NAME_WAON_MOBILE,
            MDL_SBIVT3G_INNER_ID_WAON_PC_APP      => PAYMENT_NAME_WAON_PC,
            MDL_SBIVT3G_INNER_ID_CUP              => PAYMENT_NAME_UNIONPAY,
            MDL_SBIVT3G_INNER_ID_PAYPAL           => PAYMENT_NAME_PAYPAL,
            MDL_SBIVT3G_INNER_ID_CARRIER          => PAYMENT_NAME_CARRIER,
        );
        if(strcmp($innerPayment, '') == 0) {
            return $arrNames;
        } else if (isset($arrNames[$innerPayment]) == true) {
            return $arrNames[$innerPayment];
        }
        return '';
    }

    /**
     * 受注レコード記載用ログ情報作成
     *
     * @access protected
     * @param string $innerPayment 内部決済ID
     * @param array  $arrRes       レスポンス配列
     * @param string $other        追記項目
     * @return string 結果レコード
     */
    function putPaymentLogString($innerPayment, $arrRes, $other = '') {
        $format = '%02s/%02d/%02d %02d:%02d:%02d 決済用注文ID[%s][%s]→[%s] %s';

        $serviceName = GC_Utils_SBIVT3G::getServiceName($innerPayment);
        if (strcmp($serviceName, '') == 0) {
            $serviceName = $innerPayment;
        }

        $arrStatus = SC_Helper_SBIVT3G_Setting::getPayStatus();
        if (isset($arrStatus[$arrRes['payStatus']]) == true) {
            $payStatus = $arrStatus[$arrRes['payStatus']];
        } else {
            $payStatus = '不明';
        }

        $log = sprintf($format,
            date('y'), date('m'), date('d'),
            date('H'), date('i'), date('s'),
            $arrRes['orderId'],
            $serviceName,
            $payStatus,
            $other
        );
        return $log . LF;
    }

    /**
     * コンビニ決済受付番号から店舗別の項目を生成
     *
     * @access protected
     * @param string $optionType サービスオプションタイプ
     * @param string $receiptNo  受付番号
     * @param string $telNo      電話番号(ローソン・セイコーマート用)
     * @param booelan $isArray  true:返値を文字列 false:配列
     * @return mixed 結果
     */
    function translateRecpNo($optionType, $receiptNo, $telNo, $isArray = false){

        $arrReturn = array();

        switch ($optionType) {
            // セブンイレブン
        case MDL_SBIVT3G_CVS_TYPE_SEVEN:
            $arrReturn['払込票番号'] = $receiptNo;
            break;

            // ローソン・ミニストップ・セイコーマート
        case MDL_SBIVT3G_CVS_TYPE_LAWSON:
            // ローソン・ファミリーマート・ミニストップ・セイコーマート
        case MDL_SBIVT3G_CVS_TYPE_ECON:
            $arrReturn['受付番号'] = $receiptNo;
            $arrReturn['お客様番号(お申込み時電話番号)'] = $telNo;
            break;

            // ファミリーマート
        case MDL_SBIVT3G_CVS_TYPE_FM:
            $arrReturn['お支払い受付番号'] = $receiptNo;
            break;

            // サークルKサンクス・デイリーヤマザキ
        case MDL_SBIVT3G_CVS_TYPE_OTHER:
            $arrReturn['オンライン決済番号'] = $receiptNo;
            break;

        default :
            break;
        }

        if ($isArray == false) {
            $return = '';
            foreach ($arrReturn as $k => $v) {
                $return .= sprintf(' %s[%s]', $k, $v);
            }
            return $return;
        }
        return $arrReturn;
    }

    /**
     * コンビニ決済、ATM決済の説明を取得
     *
     * @access protected
     * @param string $optionType サービスオプションタイプ
     * @param string $receiptNo  受付番号
     * @param string $telNo      電話番号(ローソン・セイコーマート用)
     * @param booelan $isString  true:返値を文字列 false:配列
     * @return mixed 結果
     */
    function getExplain($optionType) {

        $rtnExplain = '';

        // サービスオプションタイプからファイル名生成
        $path = MDL_SBIVT3G_DOC_PATH;
        $path .= sprintf(MDL_SBIVT3G_EXPLAIN_FILE_NAME_FMT, $optionType);

        // あれば取得
        if (file_exists($path) == true) {
            $rtnExplain =GC_Utils_SBIVT3G::file_get_contents($path); 
        }

        return $rtnExplain;
    }

    /**
     * DB更新時の"Now()"をバージョンごとに読み替える
     *
     * @access protected
     * @return string
     */
    function getNowExpression() {

        // 2.11.3までは"Now()"
        $expression =  'Now()';
        if (self::compareVersion('2.11.4') >= 0) {
            // 2.11.4からは"CURRENT_TIMESTAMP"を使う
            $expression = 'CURRENT_TIMESTAMP';
        }
        return $expression;
    }

    /**
     * エラーハンドラの復元
     * tgMDKで無駄にrestore_error_handler()がコールされているので
     * EC-CUBEが設定したエラー・ハンドラが消失するため
     *
     * @access protected
     * @return string
     */
    function restoreErrorHandler() {
        if (class_exists('SC_Helper_HandleError') == true) {
            // 2.12.0から導入のHandleErrorへルパー
            set_error_handler(
                array('SC_Helper_HandleError', 'handle_warning'),
                E_USER_ERROR | E_WARNING | E_USER_WARNING
                | E_CORE_WARNING | E_COMPILE_WARNING
            );
        } else if (function_exists('handle_error') == true) {
            // 2.11.Xで利用されていたエラーハンドラ
            set_error_handler('handle_error');
        }
    }

    /**
     * クレジットカードの年月の論理チェック
     *
     * @access public
     * @param  string $mon  月(2桁表記)
     * @param  string $year 年(2桁表記)
     * @return boolean false:入力異常
     */
    function isValidExpiry($mon, $year) {

        // 有効期限入力を文字列化
        $exp = sprintf('20%02d-%02d-01', $year, $mon);

        // 前月までの入力は有効
        $prev = strtotime(sprintf('%d-%02d-01', date('Y'), date('m') - 1));
        $valid = date('Y-m-d', $prev);

        // 判定
        if ($exp < $valid) {
            return false;
        }
        return true;
    }

    /**
     * キャリア決済の決済処理時エラーメッセージを取得する
     *
     * @access public
     * @param  string $vResultCode 詳細結果コード
     * @return エラーメッセージ
     */
    function getCarrierErrorMessage($vResultCode)
    {
        $message = 'アプリケーションエラーが発生しました。';

        switch ($vResultCode) {
        case 'WG02':
            $message = 'キャリア側でエラーが発生しました。';
            break;
        case 'WG03':
            $message = '既にご購入済みのサービスです。';
            break;
        case 'WG04':
            $message = 'オーソリ限度額オーバーです。';
            break;
        case 'WG06':
            $message = '時間外のリクエストです。';
            break;
        case 'WG07':
            $message = 'キャリア決済をご利用いただけません。';
            break;
        case 'WG08':
            $message  = 'お客さまの OpenID が変わった可能性があります。';
            $message .= 'OpenID なしで再試行してください。';
            break;
        case 'WGU1':
            $message = '決済がキャンセルされました。';
            break;
        case 'WGU2':
            $message = '決済中にエラーが発生しました。';
            break;
        case 'WGU3':
            $message = '決済後にエラーが発生しました。';
            break;
        default:
            break;
        }

        return $message;
    }
}
?>
