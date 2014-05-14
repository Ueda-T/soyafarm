<?php

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * メールマガジンの予約配信用ページクラス.
 *
 * @package Page
 * @author IQUEVE CO.,LTD.
 * @version $Id: LC_Page_Admin_Mail_Followmail.php 122 2014-01-23 07:04:36Z nagata $
 */
class LC_Page_Admin_Mail_Followmail extends LC_Page_Admin_Ex {

	var $objMail;

    // フォローメール送信の基準となる日付
    // ※バッチ起動時(followmail.sh)に引数指定がなければ
    // 当日日付がセットされる
    var $date;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
    	 // SC_SendMailの拡張
	    if(file_exists(MODULE_PATH . "mdl_speedmail/SC_SpeedMail.php")) {
	        require_once(MODULE_PATH . "mdl_speedmail/SC_SpeedMail.php");
	        // SpeedMail対応
	        $this->objMail = new SC_SpeedMail();
	    } else {
	        $this->objMail = new SC_SendMail_Ex();
	    }

        // SSL強制時のリダイレクトを防止する
        //parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objQuery =& SC_Query_Ex::getSingletonInstance();

        // フォローメールマスタ情報を取得
        $arrFollowMailMst = $this->getFollowMail($objQuery);

        foreach($arrFollowMailMst as $arrFollowMail) { 

            // 送信対象の受注情報を取得
            $arrOrderInfo = $this->getOrderInfo
                                ($objQuery, $arrFollowMail);

            // 取得できなければ処理中止
            if (count($arrOrderInfo) == 0 ||
                $arrOrderInfo == null) {
                continue;
            }
            // 送信IDと初回フラグを取得する
            list($sendId, $firstFlag) 
                = $this->getSendIdAndFirstFlag
                      ($objQuery, $arrFollowMail["follow_id"]);

            // 開始時刻
            $startDateTime = date("Y-m-d H:i:s");

            foreach($arrOrderInfo as $arrOrder) {

                // フォローメール送信
                $res = SC_Helper_Mail_Ex::sfSendFollowMail
                           ($arrFollowMail, $arrOrder);

                // 送信顧客テーブルへ登録
                $this->registFollowMailCustomer
                    ($objQuery, $arrOrder, $sendId, $res, $firstFlag);
            }
            // 終了時刻
            $endDateTime = date("Y-m-d H:i:s");

            // 送信履歴テーブルへ登録
            $this->registFollowMailHistory($objQuery,
                                           $arrFollowMail,
                                           $sendId,
                                           $startDateTime,
                                           $endDateTime,
                                           $firstFlag
                                          );
        }
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * フォローメールマスタ情報を取得する.
     * @param  object $objQuery
     * @return array  $arrResult
     */
    function getFollowMail(&$objQuery) {

        $sql =<<<EOF

    SELECT *
      FROM dtb_follow_mail FM
INNER JOIN dtb_follow_mail_products FMP
        ON FM.follow_id = FMP.follow_id
     WHERE FM.del_flg  = 0
       AND FM.status   = 1
       AND FMP.del_flg = 0

EOF;
        $arrRecord = $objQuery->getAll($sql);

        // フォローメールIDを抽出
        foreach ($arrRecord as $arrVal) {
            $arrFollowId[] = $arrVal["follow_id"];
        }
        $arrFollowId = array_unique($arrFollowId);
        sort($arrFollowId);

        foreach ($arrFollowId as $idx => $followId) {
            foreach ($arrRecord as $arrVal) {
                if ($followId != $arrVal["follow_id"]) {
                    continue;
                }
                // フォローメールID
                $arrResult[$idx]["follow_id"]
                   = $arrVal["follow_id"];
                // フォローメールCD
                $arrResult[$idx]["follow_code"]
                   = $arrVal["follow_code"];
                // フォローメール名
                $arrResult[$idx]["follow_name"]
                   = $arrVal["follow_name"];
                // 経過日数 
                $arrResult[$idx]["send_term"]
                   = $arrVal["send_term"];
                // 件名 
                $arrResult[$idx]["subject"]
                   = $arrVal["subject"];
                // 本文 
                $arrResult[$idx]["mail_body"]
                   = $arrVal["mail_body"];
                // 作成者
                $arrResult[$idx]["creator_id"]
                   = $arrVal["creator_id"];
                // 商品コード
                $arrResult[$idx]["product_code"][]
                   = $arrVal["product_code"];
            }
        }
        return $arrResult;
    }

    /**
     * メール送信対象の受注情報を取得する.
     * @param  object $objQuery
     * @param  array  $arrFollowMail
     * @return array  $arrResult or null
     */
    function getOrderInfo(&$objQuery, $arrFollowMail) {

        // 商品コード
        $product_code 
            = "'". implode("','",$arrFollowMail["product_code"]). "'";
        // コースCD：単回購入
        $COURSE_CD_NOT_REGULAR = COURSE_CD_NOT_REGULAR;
        // 定期受注：解約
        $REGULAR_ORDER_STATUS_CANCEL = REGULAR_ORDER_STATUS_CANCEL;
        // フォローメール送信結果：送信済み
        $SEND_RESULT_COMPLETE = SEND_RESULT_COMPLETE;

        $sql =<<<EOF

         SELECT *
           FROM dtb_order O
     INNER JOIN dtb_order_detail OD
             ON O.order_id = OD.order_id
          WHERE DATE_FORMAT(O.commit_date, '%Y-%m-%d') = 
                    DATE_FORMAT(('{$this->date}' - 
                        INTERVAL {$arrFollowMail["send_term"]} DAY), '%Y-%m-%d')
            AND O.payment_total > O.return_amount
            AND OD.product_code IN ({$product_code})
            AND OD.course_cd = {$COURSE_CD_NOT_REGULAR}
 AND NOT EXISTS (SELECT 'X'
                   FROM dtb_regular_order RO
             INNER JOIN dtb_regular_order_detail ROD
                     ON RO.regular_id = ROD.regular_id
                  WHERE RO.customer_id = O.customer_id
                    AND ROD.product_id = OD.product_id
                    AND ROD.status != {$REGULAR_ORDER_STATUS_CANCEL})
 AND NOT EXISTS (SELECT 'X'
                   FROM dtb_follow_mail_history FMH
             INNER JOIN dtb_follow_mail_customer FMC
                     ON FMH.send_id = FMC.send_id
                  WHERE FMH.follow_id = {$arrFollowMail["follow_id"]}
                    AND DATE_FORMAT(FMH.start_date, '%Y-%m-%d')
                            = DATE_FORMAT('{$this->date}', '%Y-%m-%d')
                    AND FMC.email = O.order_email
                    AND FMC.send_flg = {$SEND_RESULT_COMPLETE})
EOF;
        $arrRecord = $objQuery->getAll($sql);

        // 取得できなければ処理中止
        if (count($arrRecord) == 0) {
            return;
        }

        // 受注IDを抽出
        foreach ($arrRecord as $key => $arrVal) {
            $arrOrderId[] = $arrVal["order_id"];
        }
        $arrOrderId = array_unique($arrOrderId);
        sort($arrOrderId);

        foreach ($arrOrderId as $idx => $orderId) {
            foreach ($arrRecord as $key => $arrVal) {
                if ($orderId != $arrVal["order_id"]) {
                    continue;
                }
                // 受注ID
                $arrResult[$idx]["order_id"]
                   = $arrVal["order_id"];
                // 顧客ID
                $arrResult[$idx]["customer_id"]
                   = $arrVal["customer_id"];
                // メールアドレス
                $arrResult[$idx]["order_email"]
                   = $arrVal["order_email"];
                // 氏名
                $arrResult[$idx]["order_name"]
                   = $arrVal["order_name"];
            }
        }

        return $arrResult;
    }

    /**
     * フォローメール送信顧客テーブルに登録する.
     * @param  object  $objQuery 
     * @param  array   $arrOrder  受注情報
     * @param  integer $sendId    送信ID
     * @param  boolean $res       送信結果   true:成功 false:失敗
     * @param  boolean $firstFlag 初回フラグ true:初回 false:再送
     * @return void
     */
    function registFollowMailCustomer
    (&$objQuery, $arrOrder, $sendId, $res, $firstFlag) {

        // 送信結果
        if ($res) {
            $sqlval["send_flg"] = SEND_RESULT_COMPLETE;
        } else {
            $sqlval["send_flg"] = SEND_RESULT_ERROR;
        }
        // 再送時
        if (!$firstFlag) {
            // WHERE句作成
            $where = 'send_id = ? AND customer_id = ? ';
            // 更新
            $objQuery->update
                ("dtb_follow_mail_customer", $sqlval,
                    $where, array($sendId, $arrOrder["customer_id"]));
            // 再送時はここまで
            return;
        }

        // 送信ID
        $sqlval["send_id"] = $sendId;
        // 顧客ID
        $sqlval["customer_id"] = $arrOrder["customer_id"];
        // 受注ID
        $sqlval["order_id"] = $arrOrder["order_id"];
        // メールアドレス
        $sqlval["email"] = $arrOrder["order_email"];
        // 氏名
        $sqlval["name"] = $arrOrder["order_name"];
        // 登録
        $objQuery->insert
            ("dtb_follow_mail_customer", $sqlval);
    }

    /**
     * フォローメール送信履歴テーブルに登録する.
     * @param  object  $objQuery 
     * @param  array   $arrFollowMail フォローメール情報
     * @param  integer $sendId        送信ID
     * @param  string  $startDateTime 開始時刻
     * @param  string  $endDateTime   終了時刻
     * @param  boolean $firstFlag     初回フラグ
     * @return void
     */
    function registFollowMailHistory
    (&$objQuery, $arrFollowMail, $sendId,
      $startDateTime, $endDateTime, $firstFlag) {

        // 送信総数をカウント
        $where = "send_id = ? ";
        $sendCount = $objQuery->count
            ("dtb_follow_mail_customer", $where, array($sendId));

        // 送信完了数をカウント
        $where .= " AND send_flg = ? ";
        $compCount = $objQuery->count
            ("dtb_follow_mail_customer", $where,
                array($sendId, SEND_RESULT_COMPLETE));

        // 送信総数
        $sqlval["send_count"] = $sendCount;
        // 送信完了数
        $sqlval["complete_count"] = $compCount;

        // 再送時
        if (!$firstFlag) {
            // WHERE句作成
            $where = 'send_id = ? ';
            // 更新
            $objQuery->update
                ("dtb_follow_mail_history", $sqlval,
                    $where, array($sendId));
            // 再送時はここまで
            return;
        }
        // 送信ID
        $sqlval["send_id"] = $sendId;
        // フォローメールID
        $sqlval["follow_id"] = $arrFollowMail["follow_id"];
        // 件名
        $sqlval["subject"] = $arrFollowMail["subject"];
        // 本文
        $sqlval["body"] = $arrFollowMail["mail_body"];
        // 開始時刻
        $sqlval["start_date"] = $startDateTime;
        // 終了時刻
        $sqlval["end_date"] = $endDateTime;
        // 作成者
        $sqlval["creator_id"] = $arrFollowMail["creator_id"];

        // 登録
        $objQuery->insert
            ("dtb_follow_mail_history", $sqlval);
    }

    /**
     * 送信IDと初回フラグを取得する.
     * @param  object  $objQuery 
     * @param  integer $followId フォローメールID
     * @return array  ($sendId, $firstFlag) 送信ID, 初回フラグ
     */
    function getSendIdAndFirstFlag(&$objQuery, $followId) {

        // 初回フラグ定義
        $firstFlag = false;

        // WHERE句作成
        $where = "DATE_FORMAT(start_date, '%Y-%m-%d') = "
               . "DATE_FORMAT('". $this->date. "', '%Y-%m-%d') "
               . "AND follow_id = ? ";

        // 送信IDを取得
        $sendId = $objQuery->get
            ("send_id", "dtb_follow_mail_history", $where, $followId);

        // 送信IDが取得できない場合
        if (strlen($sendId) == 0 || $sendId == null) {
            // 送信IDを採番
            $sendId = $objQuery->nextVal
                ('dtb_follow_mail_history_send_id');

            // 初回フラグをたてる
            $firstFlag = true;
        }
        return array($sendId, $firstFlag);
    }
}
?>
