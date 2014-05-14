<?php
/*
 * This file is part of EC-CUBE PAYMENT MODULE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.net/product/payment/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
require_once '../require.php';
require_once MODULE_REALDIR . 'mdl_pg_mulpay/inc/include.php';
require_once MDL_PG_MULPAY_CLASS_REALDIR . 'LC_Mdl_PG_MULPAY.php';

$mdl_pg_mulpay_receive_lockdir = '';

function is_not_interesting_notify($pay_type, $status) {
    // ステータスが未決済(3D登録済)では、特に何も処理しない。
    return $pay_type == MDL_PG_MULPAY_CREDIT_PAY_TYPE && $status == 'AUTHENTICATED';
}

function exit_process($ret, $objPG) {
    $lockdir = $GLOBALS['mdl_pg_mulpay_receive_lockdir'];
    if ($lockdir != '') {
        if (!rmdir($lockdir)) {
            $objPG->printLog("lock release failed: $lockdir");
        }
    }

    $objPG->printLog("end of processing:" . $ret);
    echo $ret;
    exit();
}

function send_warning_mail($order_id, $pay_name, $objPG) {
    $arrInfo = SC_Helper_DB_Ex::sfGetBasisData();

    // メール本文の取得
    $body = "受注情報に存在しないオーダーIDの決済結果を受信しました。";
    $body .= "\n\n";
    $body .= "オーダーID： " . $order_id . "\n";
    $body .= "利用金額： " . $_POST['Amount'] . "円\n";
    $body .= "決済方法： " . $pay_name . "\n";
    $body .= "受付日時： " . $_POST['ReceiptDate'] . "\n";
    $body .= "処理日付： " . $_POST['TranDate'];
    $body .= "\n\n";
    $body .= "ご確認をお願い致します。";
    $body .= "\n\n";
    $body .= "GMO-PGから結果通知プログラムURLに結果を返却した際、EC-CUBE側\n";
    $body .= "（dtb_order）に該当データが存在しないため「不一致」となり、\n";
    $body .= "本メールが送信されています。\n";
    $body .= "まずは、EC-CUBE管理画面とPGマルチペイメントサービスのショップ\n";
    $body .= "管理画面とで決済データをご確認いただき、決済結果に相違がないこと\n";
    $body .= "をご確認ください。\n";

    // メール送信処理
    $objSendMail = new SC_SendMail_Ex();
    $from = $arrInfo['email03'];
    $error = $arrInfo['email04'];
    $to = $arrInfo['email01'];
    $to_name =  $arrInfo['shop_name'];

    $tosubject = "PGマルチペイメントサービス決済モジュール 不一致データ検出";
    $objSendMail->setItem('', $tosubject, $body, $from, "PGマルチペイメントサービス決済モジュール", $from, $error, $error);
    $objSendMail->setTo($to, $to_name);
    $objSendMail->sendMail();    // メール送信

    $objPG->printLog("send mail");
}

function want_warning_mail($pay_type, $status) {
    $err_code = trim($_POST['ErrCode']);
    $err_info = trim($_POST['ErrInfo']);

    // ErrCode, ErrInfoが設定されていない場合のみ、不一致メールを送信する。
    return (empty($err_code) && empty($err_info))
        && !($pay_type == MDL_PG_MULPAY_PAYPAL_PAY_TYPE && ($status == 'PAYFAIL' || $status == 'REQSUCCESS'));
}

function process_notify($arrPayType) {
    $objPG =& LC_Mdl_PG_MULPAY::getInstance();
    $objPG->printLog("start of processing:" . print_r($_POST, true));

    $shop_id = $objPG->getUserSettings('shop_id');
    $shop_pass = $objPG->getUserSettings('shop_pass');
    
    if (strcmp(trim($_POST['ShopID']), $shop_id) != 0
        || !isset($_POST['OrderID'])
        || !isset($_POST['PayType'])
        || !isset($_POST['Status'])
        || !isset($_POST['AccessID']))
    {
        $objPG->printLog("invalid parameter");
        exit_process(1, $objPG);
    }

    $order_id = trim($_POST['OrderID']);
    $access_id = trim($_POST['AccessID']);
    $status = trim($_POST['Status']);
    $pay_type = trim($_POST['PayType']);
    $tran_id = trim($_POST['TranID']);

    // オーダーIDの形式をチェックし、整数でない場合は処理対象から除外して、
    // 応答"0"を返す。
    if (! is_numeric($order_id)) {
        $objPG->printLog("order_id '".$order_id."' is not numeric.");
        exit_process(0, $objPG);
    } else if (! is_int($order_id + 0)) {
        $objPG->printLog("order_id '".$order_id."' is not integer.");
        exit_process(0, $objPG);
    }

    // スリープ
    if (strcmp($status, 'REQSUCCESS') == 0 || strcmp($status, 'AUTH') == 0 || strcmp($status, 'CHECK') == 0|| strcmp($status, 'CAPTURE') == 0) {
        $objPG->printLog("waits for " . MDL_PG_MULPAY_RECEIVE_WAIT_TIME . " second");
        sleep(MDL_PG_MULPAY_RECEIVE_WAIT_TIME);
    }

    // 興味のない通知は処理しない
    if (is_not_interesting_notify($pay_type, $status)) {
        exit_process(0, $objPG);
    }

    $objQuery =& SC_Query_Ex::getSingletonInstance();
    if ($objQuery->isError()) {
        exit_process(1, $objPG); // 処理エラー
    }

    // ロックディレクトリを作成
    // クレジット決済のみTranIDを考慮する。
    $lockdir = realpath(dirname(__FILE__))."/${order_id}_${access_id}";
    if ($pay_type == MDL_PG_MULPAY_CREDIT_PAY_TYPE && !empty($tran_id)) {
        if (!mkdir($lockdir)) {
            // ロック取得失敗: 再取得を試みる
            $objPG->printLog("order-id: $order_id locking attempt failed. TranID:$tran_id");
            $try_max = 5;
            $try = 1;
            do {
                $objPG->printLog("order-id: $order_id sleeping. try:".$try." TranID:$tran_id");
                sleep(2);
            } while (mkdir($lockdir) || $try_max < ++$try);

            if ($try_max < $try) {
                $objPG->printLog("order-id: $order_id locking failed. TranID:$tran_id");
                exit_process(1, $objPG); // ロック取得エラー
            }
        }

        // ロック取得成功
        $GLOBALS['mdl_pg_mulpay_receive_lockdir'] = $lockdir;
        $objPG->printLog("order-id: $order_id lock acquired. TranID:$tran_id");
    }

    // 受注情報を取得
    $arrOrder = $objQuery->getRow('*', 'dtb_order', 'order_id = ? AND memo03 = ? AND del_flg = 0',
                                  array($order_id, $access_id));
    if (empty($arrOrder)) {
        $objPG->printLog("order-id not found: " . $order_id);

        if (want_warning_mail($pay_type, $status)) {
            send_warning_mail($order_id, $arrPayType[$pay_type], $objPG);
        }
        exit_process(0, $objPG); // 受注情報が存在せず処理できないので、GMOPGへは正常を返す。
    }

    $arrUpdates = array();

    // TranIDを比較し、処理済みのIDより若いIDのメッセージは処理しない
    // TranIDが存在しないメッセージは対象外
/*
   if ($pay_type == MDL_PG_MULPAY_CREDIT_PAY_TYPE &&
        !empty($tran_id) && strcmp($tran_id, $arrOrder['memo05']) < 0) {
        $objPG->printLog("order-id: $order_id, $tran_id < ".$arrOrder['memo05']);
        exit_process(0, $objPG);
    }
*/
    if ($pay_type == MDL_PG_MULPAY_CREDIT_PAY_TYPE) {
        $arrUpdates['memo05'] = $tran_id;
    }

    // EC-CUBE対応状況を"入金済み"に変更
    // 1. カード以外の支払いかつステータスがPAYSUCCESS
    // 2. PayPal支払いかつステータスがCAPTURE
    if (($pay_type != MDL_PG_MULPAY_CREDIT_PAY_TYPE && $status == 'PAYSUCCESS')
        || ($pay_type == MDL_PG_MULPAY_PAYPAL_PAY_TYPE && $status == 'CAPTURE'))
    {
        $arrUpdates["status"] = ORDER_PRE_END;

        if (isset($_POST['FinishDate']) && !empty($_POST['FinishDate'])) {
            $paymentDate = $_POST['FinishDate'];
        } else if (isset($_POST['TranDate']) && !empty($_POST['TranDate'])) {
            $paymentDate = $_POST['TranDate'];
        } else {
            $paymentDate = date('YmdHis');
        }
        $arrUpdates['payment_date'] = LC_Mdl_PG_MULPAY::formatISO8601($paymentDate);
    }

    // iD決済を正常に完了したので「新規受付」
    if ($pay_type == MDL_PG_MULPAY_NETID_PAY_TYPE && $status == 'AUTH') {
        $arrUpdates["status"] = ORDER_NEW;
    }

    // "キャンセル"
    if (($pay_type == MDL_PG_MULPAY_PAYPAL_PAY_TYPE || $pay_type == MDL_PG_MULPAY_NETID_PAY_TYPE)
        && $status == 'CANCEL') {
        $arrUpdates["status"] = ORDER_CANCEL;
    }

    // GMO現状態ををmemo04に記録する。
    $arrUpdates["memo04"] = $status;

    // 金額変更時に合計, お支払い合計を更新する。
    if ($pay_type == MDL_PG_MULPAY_CREDIT_PAY_TYPE || $pay_type == MDL_PG_MULPAY_NETID_PAY_TYPE || $pay_type == MDL_PG_MULPAY_AU_PAY_TYPE) {
        $arrUpdates["payment_total"] = (int)trim($_POST['Amount']) + (int)trim($_POST['Tax']);
    }

    // 入金日を設定する。
    // - クレジット決済かつステータスがSALESかCAPTUREの場合
    // - iD決済かつステータスがSALESかCAPTUREの場合
    // - au決済かつステータスがSALESかCAPTUREの場合
    // - ドコモケータイ払いかつステータスがSALESかCAPTUREの場合
    // 他の決済に関しては、ORDER_PRE_ENDを設定しているところで設定する
    if (($status == 'SALES' || $status == 'CAPTURE') &&
        ($pay_type == MDL_PG_MULPAY_CREDIT_PAY_TYPE || $pay_type == MDL_PG_MULPAY_NETID_PAY_TYPE || $pay_type == MDL_PG_MULPAY_AU_PAY_TYPE || $pay_type = MDL_PG_MULPAY_DOCOMO_PAY_TYPE))
    {
        $tranDate = isset($_POST['TranDate']) ? $_POST['TranDate'] : date('YmdHis');
        $arrUpdates['payment_date'] = LC_Mdl_PG_MULPAY::formatISO8601($tranDate);
    }

    // auかんたん決済 支払い方法を反映する。
    if ($pay_type == MDL_PG_MULPAY_AU_PAY_TYPE && !empty($_POST['AuPayMethod'])) {
        $arrUpdates["memo05"] = $_POST['AuPayMethod'];
    }

    if (count($arrUpdates) > 0) {
        $arrUpdates["update_date"] = "NOW()";
        $objQuery->update("dtb_order", $arrUpdates,
                          "order_id = ? AND memo03 = ? AND del_flg = 0", array($order_id, $access_id));
        $objPG->printLog("update dtb_order: $order_id " . print_r($arrUpdates, true));
    }

    exit_process(0, $objPG);
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $objPG =& LC_Mdl_PG_MULPAY::getInstance();
    $objPG->printLog("GET Request" . print_r($_GET, true));
	header("HTTP/1.1 400 Bad Request");
    exit();
}

// 決済結果通知受信処理開始
process_notify($arrPayType);

?>
